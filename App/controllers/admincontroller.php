<?php

namespace App\Controllers;

use App\Core\NotificationService;
use App\Core\Controller;
use App\Core\Auth;
use App\Models\Registrasi;
use App\Models\Account;
use App\Models\BookingAdmin;
use App\Models\Room;
use App\Models\Laporan;

class AdminController extends Controller
{
    /** @var Registrasi */
    private $registrasiModel;
    /** @var Account */
    private $accountModel;
    /** @var BookingAdmin */
    private $bookingModel;
    /** @var Room */
    private $roomModel;
    /** @var NotificationService */
    private $notif;
    /** @var Laporan */
    private $laporanModel;

    private const PER_PAGE = 5;

    public function __construct()
    {
        $this->registrasiModel = new Registrasi();
        $this->accountModel    = new Account();
        $this->bookingModel    = new BookingAdmin();
        $this->roomModel       = new Room();
        $this->laporanModel    = new Laporan();
        $this->notif = new NotificationService();
    }
    private function sendNotif(string $jenis, string $email, string $nama, array $data = []): void
    {
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            error_log("[NOTIF] Invalid email: {$email}, jenis={$jenis}");
            return;
        }
        $data = array_merge(['nama' => $nama], $data);
        $this->notif->sendByJenis($jenis, $email, $nama, $data);
    }


    public function home()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $verifikasi_hari_ini    = $this->registrasiModel->countPendingToday();
        $booking_hari_ini       = $this->bookingModel->countToday();
        $ruang_kosong_hari_ini  = $this->roomModel->countActive();
        $user_aktif             = $this->accountModel->countActive();

        $activeTab = $_GET['tab'] ?? 'booking';

        // booking pending (pagination sendiri)
        $booking_page  = max(1, (int)($_GET['booking_page'] ?? 1));
        $bookingLimit  = self::PER_PAGE;
        $bookingResult = $this->bookingModel->getPendingWithPagination($booking_page, $bookingLimit);

        $booking_pending     = $bookingResult['list'] ?? [];
        $booking_total_pages = $bookingResult['total_pages'] ?? 1;

        // user pending (pagination sendiri)
        $user_page   = max(1, (int)($_GET['user_page'] ?? 1));
        $userLimit   = self::PER_PAGE;
        $userResult  = $this->registrasiModel->getPendingUsers($user_page, 'pending', '', $userLimit);

        $user_pending     = $userResult['list'] ?? [];
        $user_total_pages = $userResult['total_pages'] ?? 1;

        $this->view('admin/home', [
            'verifikasi_hari_ini'   => $verifikasi_hari_ini,
            'booking_hari_ini'      => $booking_hari_ini,
            'ruang_kosong_hari_ini' => $ruang_kosong_hari_ini,
            'user_aktif'            => $user_aktif,

            'booking_pending'       => $booking_pending,
            'booking_page'          => $booking_page,
            'booking_total_pages'   => $booking_total_pages,

            'user_pending'          => $user_pending,
            'user_page'             => $user_page,
            'user_total_pages'      => $user_total_pages,

            'activeTab'             => $activeTab,
        ]);
    }



    public function approveUser()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $id = $_POST['id_registrasi'] ?? $_GET['id'] ?? null;
        if (!$id || !ctype_digit((string)$id)) {
            $this->redirect('index.php?controller=admin&action=verifikasiUser');
            return;
        }
        $reg = $this->registrasiModel->findById($id);
        if (!$reg || $reg['status'] !== 'pending') {
            $this->redirect('index.php?controller=admin&action=verifikasiUser');
            return;
        }
        $role     = $reg['role_registrasi'];
        $academic = $this->deriveAcademicData($reg, $role);
        $this->accountModel->createFromRegistrasi([
            'id_registrasi'     => $reg['id_registrasi'],
            'nama'              => $reg['nama'],
            'email'             => $reg['email'],
            'jurusan'           => $reg['jurusan'] ?? null,
            'prodi'             => $reg['prodi'] ?? null,
            'nim_nip'           => $reg['nim_nip'],
            'unit_jurusan'      => $reg['unit_jurusan'] ?? null,

            'password'          => $reg['password'],
            'role'              => $role,
            'angkatan'          => $academic['angkatan'],
            'durasi_studi'      => $academic['durasi_studi'],
            'aktif_sampai'      => $academic['aktif_sampai'],
            'status_aktif'      => 'aktif',
            'screenshot_kubaca' => $reg['screenshot_kubaca'] ?? null,
        ]);
        $this->registrasiModel->updateStatus($id, 'approved');
        $this->sendNotif('user_approved', $reg['email'], $reg['nama'], [
            'email'   => $reg['email'],
            'nim_nip' => $reg['nim_nip'],
            'role'    => $role,
        ]);

        $this->redirect('index.php?controller=admin&action=verifikasiUser');
    }
    public function rejectUser()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $id = $_POST['id_registrasi'] ?? $_GET['id'] ?? null;
        if (!$id || !ctype_digit((string)$id)) {
            $this->redirect('index.php?controller=admin&action=verifikasiUser');
            return;
        }

        $reg = $this->registrasiModel->findById((int)$id);
        if ($reg && ($reg['status'] ?? '') === 'pending') {
            $alasan = trim($this->input('alasan') ?? ($_POST['alasan'] ?? ''));
            $this->registrasiModel->updateStatus($id, 'rejected');
            $this->sendNotif('user_rejected', (string)$reg['email'], (string)$reg['nama'], [
                'alasan' => $alasan !== '' ? $alasan : 'Tidak ada keterangan.',
            ]);
        } else {
            $this->registrasiModel->updateStatus($id, 'rejected');
        }

        $this->redirect('index.php?controller=admin&action=verifikasiUser');
    }

    private function deriveAcademicData(array $reg, string $role): array
    {
        $nowYear = (int) date('Y');

        if ($role === 'mahasiswa') {
            $nimDigits = preg_replace('/\D/', '', $reg['nim_nip']);
            $angkatan = $nowYear;
            $durasi   = 4;

            if (strlen($nimDigits) >= 4) {
                $angkatan2 = substr($nimDigits, 0, 2);
                if (ctype_digit($angkatan2)) {
                    $angkatan = 2000 + (int) $angkatan2;
                }

                $kodeDurasi = substr($nimDigits, 2, 2);
                $durasi     = $kodeDurasi === '07' ? 3 : 4;
            }

            $aktifSampaiYear = $angkatan + $durasi;
            $aktif_sampai    = $aktifSampaiYear . '-12-31';

            return [
                'angkatan'     => $angkatan,
                'durasi_studi' => $durasi,
                'aktif_sampai' => $aktif_sampai,
            ];
        }
        $angkatan        = $nowYear;
        $durasi          = 40;
        $aktifSampaiYear = $angkatan + $durasi;
        $aktif_sampai    = $aktifSampaiYear . '-12-31';

        return [
            'angkatan'     => $angkatan,
            'durasi_studi' => $durasi,
            'aktif_sampai' => $aktif_sampai,
        ];
    }

    public function verifikasiUser()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = self::PER_PAGE;

        $filter  = $_GET['filter'] ?? 'pending';
        $search  = trim($_GET['q'] ?? '');
        $data = $this->registrasiModel->getPendingUsers($page, $filter, $search, $perPage);
        $data['current_page'] = $page;
        $data['filter']       = $filter;
        $data['search']       = $search;

        $this->view('admin/verifikasiuser', $data);
    }



    public function ruangan()
    {
        Auth::requireRole(['admin', 'super_admin']);
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $perPage = self::PER_PAGE;
        $offset  = ($page - 1) * $perPage;
        $search = trim($_GET['q'] ?? '');
        $status = $_GET['status'] ?? 'all';
        $result    = $this->roomModel->getAdminList($perPage, $offset, $search, $status);
        $anyActive = $this->roomModel->anyActive();
        $this->view('admin/kelolaruangan', [
            'rooms'        => $result['data'],
            'current_page' => $page,
            'total_pages'  => $result['total_pages'],
            'search'       => $search,
            'status'       => $status,
            'anyActive'    => $anyActive,
        ]);
    }

    public function anggota()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $page   = max(1, (int)($_GET['page'] ?? 1));
        $perPage = self::PER_PAGE;
        $filter = $_GET['filter'] ?? 'all';
        $search = trim($_GET['q'] ?? '');
        $result = $this->accountModel->getAdminUserList($page, $perPage, $filter, $search);

        $this->view('admin/kelolaanggota', [
            'users'       => $result['data'],
            'currentPage' => $result['current_page'],
            'totalPages'  => $result['total_pages'],
            'filter'      => $result['filter'],
            'search'      => $result['search'],
        ]);
    }

    public function laporan()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $range = $_GET['range'] ?? 'month';
        $perPage = 5;

        $roomsPage   = max(1, (int)($_GET['rooms_page'] ?? 1));
        $prodiPage   = max(1, (int)($_GET['prodi_page'] ?? 1));
        $jurusanPage = max(1, (int)($_GET['jurusan_page'] ?? 1));
        $ratingPage  = max(1, (int)($_GET['rating_page'] ?? 1));

        $rooms   = $this->laporanModel->getRuanganPaged($range, $roomsPage, $perPage);
        $prodi   = $this->laporanModel->getProdiPaged($range, $prodiPage, $perPage);
        $jurusan = $this->laporanModel->getJurusanPaged($range, $jurusanPage, $perPage);
        $rating  = $this->laporanModel->getRatingPaged($range, $ratingPage, $perPage);

        $this->view('admin/laporan', [
            'range' => $range,

            'summary_rooms'   => $rooms['rows'],
            'rooms_page'      => $rooms['current_page'],
            'rooms_total_pages' => $rooms['total_pages'],

            'summary_prodi'   => $prodi['rows'],
            'prodi_page'      => $prodi['current_page'],
            'prodi_total_pages' => $prodi['total_pages'],

            'summary_jurusan' => $jurusan['rows'],
            'jurusan_page'    => $jurusan['current_page'],
            'jurusan_total_pages' => $jurusan['total_pages'],

            'summary_rating'  => $rating['rows'],
            'rating_page'     => $rating['current_page'],
            'rating_total_pages' => $rating['total_pages'],
        ]);
    }


    public function editUser()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $id = $_GET['id'] ?? null;
        if (!$id || !ctype_digit((string)$id)) {
            $this->redirectWithMessage(
                'index.php?controller=admin&action=anggota',
                'ID user tidak valid.',
                'error'
            );
            return;
        }

        $user = $this->accountModel->findById((int)$id);

        if (!$user) {
            $this->redirectWithMessage(
                'index.php?controller=admin&action=anggota',
                'User tidak ditemukan.',
                'error'
            );
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->view('admin/edituser', [
                'user' => $user,
            ]);
            return;
        }

        $nama   = trim($this->input('nama'));
        $email  = trim($this->input('email'));
        $status = $this->input('status_aktif') ?? $user['status_aktif'];
        $role   = $this->input('role') ?? $user['role'];
        $jurusan     = trim($this->input('jurusan') ?? '');
        $angkatanIn  = $this->input('angkatan');
        $aktifSampai = $this->input('aktif_sampai');
        $angkatan = null;
        if ($angkatanIn !== null && $angkatanIn !== '') {
            $angkatan = (int)$angkatanIn;
            if ($angkatan <= 0) {
                $angkatan = null;
            }
        }


        if ($nama === '' || $email === '') {
            $this->redirectWithMessage(
                'index.php?controller=admin&action=editUser&id=' . $id,
                'Nama dan email wajib diisi.',
                'error'
            );
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirectWithMessage(
                'index.php?controller=admin&action=editUser&id=' . $id,
                'Format email tidak valid.',
                'error'
            );
            return;
        }

        $this->accountModel->updateBasicProfile((int)$id, [
            'nama'         => $nama,
            'email'        => $email,
            'status_aktif' => $status,
            'role'         => $role,
            'jurusan'      => $jurusan !== '' ? $jurusan : null,
            'angkatan'     => $angkatan,
            'aktif_sampai' => $aktifSampai ?: null,
        ]);


        $this->redirectWithMessage(
            'index.php?controller=admin&action=anggota',
            'Data user berhasil diperbarui.',
            'success'
        );
    }

    public function setRoomStatus()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $idRuangan = $this->input('id_ruangan');
        $status    = $this->input('status');

        if (!$idRuangan || !ctype_digit((string)$idRuangan)) {
            $this->redirectWithMessage(
                'index.php?controller=admin&action=ruangan',
                'ID ruangan tidak valid.',
                'error'
            );
            return;
        }

        if (!in_array($status, ['aktif', 'nonaktif'], true)) {
            $this->redirectWithMessage(
                'index.php?controller=admin&action=ruangan',
                'Status ruangan tidak dikenal.',
                'error'
            );
            return;
        }

        $id = (int)$idRuangan;

        if ($status === 'nonaktif' && $this->roomModel->hasActiveBookings($id)) {
            $this->redirectWithMessage(
                'index.php?controller=admin&action=ruangan',
                'Tidak dapat menonaktifkan ruangan ini karena masih ada peminjaman aktif. Batalkan dulu booking terkait.',
                'error'
            );
            return;
        }

        $this->roomModel->updateStatus($id, $status);

        $this->redirectWithMessage(
            'index.php?controller=admin&action=ruangan',
            $status === 'aktif'
                ? 'Ruangan berhasil diaktifkan.'
                : 'Ruangan berhasil dinonaktifkan.',
            'success'
        );
    }

    public function deactivateAllRooms()
    {
        Auth::requireRole(['admin', 'super_admin']);

        if ($this->roomModel->hasAnyActiveBookings()) {
            $this->redirectWithMessage(
                'index.php?controller=admin&action=ruangan',
                'Masih terdapat peminjaman aktif. Tidak dapat menonaktifkan seluruh ruangan. Batalkan dulu booking yang berjalan/pending.',
                'error'
            );
            return;
        }

        $this->roomModel->updateAllStatus('nonaktif');

        $this->redirectWithMessage(
            'index.php?controller=admin&action=ruangan',
            'Seluruh ruangan berhasil dinonaktifkan.',
            'success'
        );
    }

    public function setUserStatus()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $idUser = (int)($this->input('id_user') ?? 0);
        $status = $this->input('status');

        if (!$idUser) {
            $this->redirectWithMessage(
                'index.php?controller=admin&action=anggota',
                'ID user tidak valid.',
                'error'
            );
            return;
        }

        if (!in_array($status, ['aktif', 'nonaktif'], true)) {
            $this->redirectWithMessage(
                'index.php?controller=admin&action=anggota',
                'Status tidak dikenal.',
                'error'
            );
            return;
        }

        $current = Auth::user();
        if (!empty($current['id_account']) && (int)$current['id_account'] === $idUser && $status === 'nonaktif') {
            $this->redirectWithMessage(
                'index.php?controller=admin&action=anggota',
                'Anda tidak dapat menonaktifkan akun Anda sendiri.',
                'error'
            );
            return;
        }

        $ok = $this->accountModel->updateStatusAktif($idUser, $status);

        if (!$ok) {
            $this->redirectWithMessage(
                'index.php?controller=admin&action=anggota',
                'Gagal mengubah status user.',
                'error'
            );
            return;
        }

        $this->redirectWithMessage(
            'index.php?controller=admin&action=anggota',
            $status === 'aktif'
                ? 'User berhasil diaktifkan.'
                : 'User berhasil dinonaktifkan.',
            'success'
        );
    }

    public function deleteUser()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $idUser = (int)($this->input('id_user') ?? 0);

        if (!$idUser) {
            $this->redirectWithMessage(
                'index.php?controller=admin&action=anggota',
                'ID user tidak valid.',
                'error'
            );
            return;
        }

        $current = Auth::user();
        if (!empty($current['id_account']) && (int)$current['id_account'] === $idUser) {
            $this->redirectWithMessage(
                'index.php?controller=admin&action=anggota',
                'Anda tidak dapat menghapus akun Anda sendiri.',
                'error'
            );
            return;
        }

        $ok = $this->accountModel->deleteById($idUser);

        if (!$ok) {
            $this->redirectWithMessage(
                'index.php?controller=admin&action=anggota',
                'Gagal menghapus user. Pastikan tidak ada relasi penting yang terganggu.',
                'error'
            );
            return;
        }

        $this->redirectWithMessage(
            'index.php?controller=admin&action=anggota',
            'User berhasil dihapus.',
            'success'
        );
    }

    public function toggleAllRooms()
    {
        Auth::requireRole(['admin', 'super_admin']);
        if ($this->roomModel->anyActive()) {
            $today = date('Y-m-d');
            $cancelled = $this->bookingModel->cancelBookingsByDate($today);
            $this->roomModel->setAllStatus('nonaktif');

            foreach ($cancelled as $b) {
                if (empty($b['email'])) continue;

                $toEmail = (string)$b['email'];
                $toName  = (string)($b['nama'] ?? 'User');

                $start = !empty($b['start_time']) ? strtotime($b['start_time']) : null;
                $end   = !empty($b['end_time']) ? strtotime($b['end_time']) : null;

                // ini pakai template yang paling dekat: room_disabled_cancel
                $this->sendNotif('room_disabled_cancel', $toEmail, $toName, [
                    'nama_ruangan' => $b['nama_ruangan'] ?? '',
                    'tanggal'      => $start ? date('Y-m-d', $start) : $today,
                    'jam_mulai'    => $start ? date('H:i', $start) : '',
                    'jam_selesai'  => $end ? date('H:i', $end) : '',
                    'kode_booking' => $b['booking_code'] ?? '',
                    'catatan_admin' => 'Dibatalkan karena penutupan perpustakaan.',
                ]);
            }

            $count = count($cancelled);


            $this->redirectWithMessage(
                'index.php?controller=admin&action=ruangan',
                "Semua ruangan dinonaktifkan. {$count} booking pada tanggal {$today} dibatalkan.",
                'success'
            );
            return;
        }
        $this->roomModel->setAllStatus('aktif');
        $this->redirectWithMessage(
            'index.php?controller=admin&action=ruangan',
            'Semua ruangan telah diaktifkan.',
            'success'
        );
    }

    public function editRoom()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $idParam = $_GET['id'] ?? null;
        if (!$idParam || !ctype_digit((string)$idParam)) {
            $this->redirectWithMessage(
                'index.php?controller=admin&action=ruangan',
                'ID ruangan tidak valid.',
                'error'
            );
            return;
        }

        $id   = (int)$idParam;
        $room = $this->roomModel->findById($id);

        if (!$room) {
            $this->redirectWithMessage(
                'index.php?controller=admin&action=ruangan',
                'Ruangan tidak ditemukan.',
                'error'
            );
            return;
        }

        $schedule           = $this->roomModel->getScheduleByRoom($id);
        $facilities         = $this->roomModel->getAllFacilities();
        $selectedFacilities = $this->roomModel->getFacilityIdsByRoom($id);

        $this->view('admin/editruangan', [
            'room'               => $room,
            'schedule'           => $schedule,
            'facilities'         => $facilities,
            'selectedFacilities' => $selectedFacilities,
        ]);
    }

    public function updateRoom()
    {
        Auth::requireRole(['admin', 'super_admin']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectWithMessage(
                'index.php?controller=admin&action=ruangan',
                'Metode request tidak valid.',
                'error'
            );
            return;
        }

        $idRuangan = (int)($this->input('id_ruangan') ?? 0);
        if ($idRuangan <= 0) {
            $this->redirectWithMessage(
                'index.php?controller=admin&action=ruangan',
                'ID ruangan tidak valid.',
                'error'
            );
            return;
        }

        $existingRoom = $this->roomModel->findById($idRuangan);
        if (!$existingRoom) {
            $this->redirectWithMessage(
                'index.php?controller=admin&action=ruangan',
                'Ruangan tidak ditemukan.',
                'error'
            );
            return;
        }

        $namaRuangan = trim($this->input('nama_ruangan'));
        $lokasi      = trim($this->input('lokasi'));
        $kategori    = trim($this->input('kategori'));
        $kapMin      = (int)$this->input('kapasitas_min');
        $kapMax      = (int)$this->input('kapasitas_max');
        $status      = $this->input('status_operasional') ?: 'nonaktif';

        $selectedFacilities = isset($_POST['fasilitas']) && is_array($_POST['fasilitas'])
            ? $_POST['fasilitas']
            : [];

        $roomData = [
            'id_ruangan'         => $idRuangan,
            'nama_ruangan'       => $namaRuangan,
            'lokasi'             => $lokasi,
            'kategori'           => $kategori,
            'kapasitas_min'      => $kapMin,
            'kapasitas_max'      => $kapMax,
            'status_operasional' => $status,
            'foto_ruangan'       => $existingRoom['foto_ruangan'] ?? null,
        ];

        $renderBack = function (string $message) use ($roomData, $selectedFacilities, $idRuangan) {
            $schedule            = $this->roomModel->getScheduleByRoom($idRuangan);
            $facilities          = $this->roomModel->getAllFacilities();
            $selectedFacilityIds = $selectedFacilities;

            $this->view('admin/editruangan', [
                'room'               => $roomData,
                'schedule'           => $schedule,
                'facilities'         => $facilities,
                'selectedFacilities' => $selectedFacilityIds,
                'error'              => $message,
            ]);
        };

        if ($namaRuangan === '' || $lokasi === '') {
            $renderBack('Nama ruangan dan lokasi wajib diisi.');
            return;
        }

        if ($kapMin <= 0 || $kapMax <= 0 || $kapMin > $kapMax) {
            $renderBack('Kapasitas minimum dan maksimum harus lebih dari 0 dan Kapasitas Min ≤ Kapasitas Max.');
            return;
        }
        $fotoPath = $existingRoom['foto_ruangan'] ?? null;

        if (!empty($_FILES['foto_ruangan']['name'])) {
            $file     = $_FILES['foto_ruangan'];
            $tmpName  = $file['tmp_name'];
            $fileName = $file['name'];

            if ($file['error'] === UPLOAD_ERR_OK && is_uploaded_file($tmpName)) {
                $ext     = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];

                if (!in_array($ext, $allowed, true)) {
                    $renderBack('Format gambar tidak didukung. Gunakan JPG, JPEG, PNG, atau WEBP.');
                    return;
                }

                $uploadDir = __DIR__ . '/../../public/uploads/rooms/';
                if (!is_dir($uploadDir)) {
                    @mkdir($uploadDir, 0777, true);
                }

                $newName  = 'room_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
                $fullPath = $uploadDir . $newName;

                if (!move_uploaded_file($tmpName, $fullPath)) {
                    $renderBack('Gagal mengupload foto ruangan.');
                    return;
                }

                $fotoPath = 'uploads/rooms/' . $newName;
            } else {
                $renderBack('Terjadi kesalahan saat mengupload file.');
                return;
            }
        }
        $updateData = [
            'nama_ruangan'       => $namaRuangan,
            'lokasi'             => $lokasi,
            'kategori'           => $kategori,
            'kapasitas_min'      => $kapMin,
            'kapasitas_max'      => $kapMax,
            'status_operasional' => $status,
            'foto_ruangan'       => $fotoPath,
        ];

        $ok = $this->roomModel->updateRoom($idRuangan, $updateData);
        if (!$ok) {
            $renderBack('Gagal memperbarui ruangan. Silakan coba lagi.');
            return;
        }
        $this->roomModel->syncFacilities($idRuangan, $selectedFacilities);

        $this->redirectWithMessage(
            'index.php?controller=admin&action=ruangan',
            'Data ruangan berhasil diperbarui.',
            'success'
        );
    }

    public function detailRuangan()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $id = $_GET['id'] ?? null;
        if (!$id || !ctype_digit((string)$id)) {
            $this->redirectWithMessage(
                'index.php?controller=admin&action=ruangan',
                'ID ruangan tidak valid.',
                'error'
            );
            return;
        }

        $room = $this->roomModel->findById((int)$id);
        if (!$room) {
            $this->redirectWithMessage(
                'index.php?controller=admin&action=ruangan',
                'Ruangan tidak ditemukan.',
                'error'
            );
            return;
        }

        $fasilitas         = $this->roomModel->getFasilitas((int)$id);
        $schedule          = $this->roomModel->getScheduleByRoom((int)$id);
        $hasActiveBookings = $this->roomModel->hasActiveBookings((int)$id);

        $this->view('admin/detailruangan', [
            'room'              => $room,
            'fasilitas'         => $fasilitas,
            'schedule'          => $schedule,
            'hasActiveBookings' => $hasActiveBookings,
        ]);
    }

    public function detailUser()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $id = $_GET['id'] ?? null;
        if (!$id || !ctype_digit((string)$id)) {
            $this->redirectWithMessage(
                'index.php?controller=admin&action=anggota',
                'ID user tidak valid.',
                'error'
            );
            return;
        }

        $user = $this->accountModel->findById((int)$id);

        if (!$user) {
            $this->redirectWithMessage(
                'index.php?controller=admin&action=anggota',
                'User tidak ditemukan.',
                'error'
            );
            return;
        }

        $this->view('admin/detailuser', [
            'user' => $user,
        ]);
    }

    public function detailRegistrasi()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $id = $_GET['id'] ?? null;
        if (!$id || !ctype_digit((string)$id)) {
            $this->redirectWithMessage(
                'index.php?controller=admin&action=verifikasiUser',
                'ID registrasi tidak valid.',
                'error'
            );
            return;
        }

        $reg = $this->registrasiModel->findById((int)$id);

        if (!$reg) {
            $this->redirectWithMessage(
                'index.php?controller=admin&action=verifikasiUser',
                'Data registrasi tidak ditemukan.',
                'error'
            );
            return;
        }

        $this->view('admin/detailregistrasi', [
            'reg' => $reg,
        ]);
    }

    public function tambahRuangan()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $facilities = $this->roomModel->getAllFacilities();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->view('admin/tambah-ruangan', [
                'room'               => [],
                'facilities'         => $facilities,
                'selectedFacilities' => [],
            ]);
            return;
        }

        $namaRuangan = trim($this->input('nama_ruangan'));
        $lokasi      = trim($this->input('lokasi'));
        $kategori    = trim($this->input('kategori'));
        $kapMin      = (int)$this->input('kapasitas_min');
        $kapMax      = (int)$this->input('kapasitas_max');
        $status      = $this->input('status_operasional') ?: 'aktif';

        $selectedFacilities = isset($_POST['fasilitas']) && is_array($_POST['fasilitas'])
            ? $_POST['fasilitas']
            : [];

        $roomData = [
            'nama_ruangan'       => $namaRuangan,
            'lokasi'             => $lokasi,
            'kategori'           => $kategori,
            'kapasitas_min'      => $kapMin,
            'kapasitas_max'      => $kapMax,
            'status_operasional' => $status,
            'fasilitas_ids'      => $selectedFacilities,
        ];

        $renderError = function (string $message) use ($roomData, $facilities, $selectedFacilities) {
            $this->view('admin/tambah-ruangan', [
                'room'               => $roomData,
                'facilities'         => $facilities,
                'selectedFacilities' => $selectedFacilities,
                'error'              => $message,
            ]);
        };

        if ($namaRuangan === '' || $lokasi === '') {
            $renderError('Nama ruangan dan lokasi wajib diisi.');
            return;
        }

        if ($kapMin <= 0 || $kapMax <= 0 || $kapMin > $kapMax) {
            $renderError('Kapasitas minimum dan maksimum harus lebih dari 0 dan Kapasitas Min ≤ Kapasitas Max.');
            return;
        }

        if ($this->roomModel->existsByName($namaRuangan)) {
            $renderError('Nama ruangan sudah digunakan. Silakan gunakan nama ruangan lain.');
            return;
        }

        $fotoPath = null;

        if (!empty($_FILES['foto_ruangan']['name'])) {
            $file     = $_FILES['foto_ruangan'];
            $tmpName  = $file['tmp_name'];
            $fileName = $file['name'];

            if ($file['error'] === UPLOAD_ERR_OK && is_uploaded_file($tmpName)) {
                $ext     = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];

                if (!in_array($ext, $allowed, true)) {
                    $renderError('Format gambar tidak didukung. Gunakan JPG, JPEG, PNG, atau WEBP.');
                    return;
                }

                $uploadDir = __DIR__ . '/../../public/uploads/rooms/';
                if (!is_dir($uploadDir)) {
                    @mkdir($uploadDir, 0777, true);
                }

                $newName  = 'room_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
                $fullPath = $uploadDir . $newName;

                if (!move_uploaded_file($tmpName, $fullPath)) {
                    $renderError('Gagal mengupload foto ruangan.');
                    return;
                }

                $fotoPath = 'uploads/rooms/' . $newName;
            } else {
                $renderError('Terjadi kesalahan saat mengupload file.');
                return;
            }
        }

        $dataInsert                 = $roomData;
        $dataInsert['foto_ruangan'] = $fotoPath;

        $roomId = $this->roomModel->createRoom($dataInsert);
        if ($roomId <= 0) {
            $renderError('Gagal menyimpan ruangan. Silakan coba lagi.');
            return;
        }

        if (!empty($selectedFacilities)) {
            $this->roomModel->syncFacilities($roomId, $selectedFacilities);
        }

        $this->redirectWithMessage(
            'index.php?controller=admin&action=ruangan',
            'Ruangan baru berhasil ditambahkan.',
            'success'
        );
    }

    public function deleteRuangan()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $idRuangan = (int)($this->input('id_ruangan') ?? $this->input('id') ?? 0);

        if ($idRuangan <= 0) {
            $this->redirectWithMessage(
                'index.php?controller=admin&action=ruangan',
                'ID ruangan tidak valid.',
                'error'
            );
            return;
        }

        if ($this->roomModel->hasActiveBookings($idRuangan)) {
            $this->redirectWithMessage(
                'index.php?controller=admin&action=ruangan',
                'Ruangan tidak dapat dihapus karena masih memiliki booking aktif. Ubah status menjadi nonaktif jika ingin menutup peminjaman.',
                'error'
            );
            return;
        }

        $ok = $this->roomModel->deleteRoom($idRuangan);

        if (!$ok) {
            $this->redirectWithMessage(
                'index.php?controller=admin&action=ruangan',
                'Ruangan tidak dapat dihapus karena masih memiliki data terkait (misalnya riwayat booking). Anda dapat menonaktifkan ruangan ini sebagai alternatif.',
                'error'
            );
            return;
        }

        $this->redirectWithMessage(
            'index.php?controller=admin&action=ruangan',
            'Ruangan berhasil dihapus.',
            'success'
        );
    }
    public function tambahAnggota()
    {
        Auth::requireRole(['admin', 'super_admin']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->view('admin/tambahanggota', [
                'error' => null,
                'old'   => [
                    'nama'          => '',
                    'email'         => '',
                    'nim_nip'       => '',
                    'role'          => 'dosen',
                    'jurusan'       => '',
                    'unit_jurusan'  => '',
                    'status_aktif'  => 'aktif',
                ],
            ]);
            return;
        }
        $nama         = trim($this->input('nama'));
        $email        = trim($this->input('email'));
        $nimNip       = trim($this->input('nim_nip'));
        $role         = $this->input('role');
        $jurusan      = trim($this->input('jurusan'));
        $unitJurusan  = trim($this->input('unit_jurusan'));
        $password     = (string)$this->input('password');
        $password2    = (string)$this->input('password2');
        $statusAktif  = $this->input('status_aktif') === 'nonaktif' ? 'nonaktif' : 'aktif';

        $old = [
            'nama'          => $nama,
            'email'         => $email,
            'nim_nip'       => $nimNip,
            'role'          => $role,
            'jurusan'       => $jurusan,
            'unit_jurusan'  => $unitJurusan,
            'status_aktif'  => $statusAktif,
        ];
        if ($nama === '' || $email === '' || $nimNip === '' || $role === '') {
            $this->view('admin/tambahanggota', [
                'error' => 'Nama, email, NIP/NIM, dan role wajib diisi.',
                'old'   => $old,
            ]);
            return;
        }

        if (!in_array($role, ['dosen', 'tendik'], true)) {
            $this->view('admin/tambahanggota', [
                'error' => 'Role tidak valid. Hanya dosen atau tendik.',
                'old'   => $old,
            ]);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->view('admin/tambahanggota', [
                'error' => 'Format email tidak valid.',
                'old'   => $old,
            ]);
            return;
        }
        if ($role === 'dosen' && $jurusan === '') {
            $this->view('admin/tambahanggota', [
                'error' => 'Jurusan wajib diisi untuk dosen.',
                'old'   => $old,
            ]);
            return;
        }

        if ($role === 'tendik' && $unitJurusan === '') {
            $this->view('admin/tambahanggota', [
                'error' => 'Unit / jurusan wajib diisi untuk tendik.',
                'old'   => $old,
            ]);
            return;
        }
        if ($password === '' || $password2 === '') {
            $this->view('admin/tambahanggota', [
                'error' => 'Password dan konfirmasi password wajib diisi.',
                'old'   => $old,
            ]);
            return;
        }

        if ($password !== $password2) {
            $this->view('admin/tambahanggota', [
                'error' => 'Konfirmasi password tidak sama.',
                'old'   => $old,
            ]);
            return;
        }

        if (
            strlen($password) < 8 ||
            !preg_match('/[A-Za-z]/', $password) ||
            !preg_match('/\d/', $password)
        ) {
            $this->view('admin/tambahanggota', [
                'error' => 'Password minimal 8 karakter dan harus mengandung huruf dan angka.',
                'old'   => $old,
            ]);
            return;
        }
        if ($this->accountModel->existsByEmail($email)) {
            $this->view('admin/tambahanggota', [
                'error' => 'Email sudah terdaftar pada akun lain.',
                'old'   => $old,
            ]);
            return;
        }

        if ($this->accountModel->existsByNimNip($nimNip)) {
            $this->view('admin/tambahanggota', [
                'error' => 'NIM/NIP sudah terdaftar pada akun lain.',
                'old'   => $old,
            ]);
            return;
        }
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $academic = $this->deriveAcademicData(
            ['nim_nip' => $nimNip],
            $role
        );
        $this->accountModel->createManual([
            'nama'             => $nama,
            'email'            => $email,
            'nim_nip'          => $nimNip,
            'jurusan'          => $role === 'dosen' ? $jurusan : null,
            'unit_jurusan'     => $role === 'tendik' ? $unitJurusan : null,
            'role'             => $role,
            'password'         => $hashedPassword,
            'angkatan'         => $academic['angkatan'],
            'durasi_studi'     => $academic['durasi_studi'],
            'aktif_sampai'     => $academic['aktif_sampai'],
            'status_aktif'     => $statusAktif,
        ]);

        $this->redirectWithMessage(
            'index.php?controller=admin&action=anggota',
            'Anggota baru berhasil ditambahkan.',
            'success'
        );
    }
}
