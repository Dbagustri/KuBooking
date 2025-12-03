<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Registrasi;
use App\Models\Account;
use App\Models\BookingAdmin;
use App\Models\Room;
use App\Models\Laporan;

class AdminController extends Controller
{
    public function home()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $registrasiModel = new Registrasi();
        $accountModel    = new Account();
        $bookingModel    = new BookingAdmin();
        $roomModel       = new Room();

        // ==== Statistik kartu atas ====
        $verifikasi_hari_ini    = $registrasiModel->countPendingToday();
        $booking_hari_ini       = $bookingModel->countToday();
        $ruang_kosong_hari_ini  = $roomModel->countActive();
        $user_aktif             = $accountModel->countActive();

        // ==== Pagination & tab ====
        $activeTab = $_GET['tab'] ?? 'booking';

        // Booking pending (pagination)
        $booking_page = isset($_GET['booking_page']) ? (int)$_GET['booking_page'] : 1;
        if ($booking_page < 1) $booking_page = 1;

        $bookingLimit  = 10;
        $bookingResult = $bookingModel->getPendingWithPagination($booking_page, $bookingLimit);
        $booking_pending     = $bookingResult['list'];
        $booking_total_pages = $bookingResult['total_pages'];

        // User pending (pakai Registrasi::getPendingUsers)
        $user_page = isset($_GET['user_page']) ? (int)$_GET['user_page'] : 1;
        if ($user_page < 1) $user_page = 1;

        // filter = 'pending' supaya hanya status pending
        $userResult = $registrasiModel->getPendingUsers($user_page, 'pending', '');
        $user_pending     = $userResult['list'];
        $user_total_pages = $userResult['total_pages'];

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

        $registrasiModel = new Registrasi();
        $accountModel    = new Account();

        $reg = $registrasiModel->findById($id);
        if (!$reg || $reg['status'] !== 'pending') {
            $this->redirect('index.php?controller=admin&action=verifikasiUser');
            return;
        }

        $role     = $reg['role_registrasi'];
        $academic = $this->deriveAcademicData($reg, $role);

        $accountModel->createFromRegistrasi([
            'id_registrasi' => $reg['id_registrasi'],
            'nama'          => $reg['nama'],
            'email'         => $reg['email'],
            'jurusan'       => $reg['jurusan'] ?? null,
            'prodi'         => $reg['prodi'] ?? null,
            'nim_nip'       => $reg['nim_nip'],
            'unit_jurusan'  => $reg['unit_jurusan'] ?? null,
            'password'      => $reg['password'],
            'role'          => $role,
            'angkatan'      => $academic['angkatan'],
            'durasi_studi'  => $academic['durasi_studi'],
            'aktif_sampai'  => $academic['aktif_sampai'],
            'status_aktif'  => 'aktif',
            'screenshot_kubaca' => $reg['screenshot_kubaca'] ?? null,
        ]);

        $registrasiModel->updateStatus($id, 'approved');

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

        $registrasiModel = new Registrasi();
        $registrasiModel->updateStatus($id, 'rejected');

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
                if ($kodeDurasi === '07') {
                    $durasi = 3;
                } else {
                    $durasi = 4;
                }
            }

            $aktifSampaiYear = $angkatan + $durasi;
            $aktif_sampai    = $aktifSampaiYear . '-12-31';

            return [
                'angkatan'     => $angkatan,
                'durasi_studi' => $durasi,
                'aktif_sampai' => $aktif_sampai,
            ];
        }

        // Dosen & tendik
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

        $model = new Registrasi();

        $page   = $_GET['page']  ?? 1;
        $filter = $_GET['filter'] ?? '';
        $search = $_GET['q']      ?? '';

        $data = $model->getPendingUsers($page, $filter, $search);

        $this->view('admin/verifikasiuser', $data);
    }

    public function ruangan()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $roomModel = new Room();

        $page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) {
            $page = 1;
        }
        $search = $_GET['q'] ?? '';

        $perPage = 20;
        $offset  = ($page - 1) * $perPage;

        $result = $roomModel->getAdminList($perPage, $offset, $search);

        $this->view('admin/kelolaruangan', [
            'rooms'        => $result['data'],
            'current_page' => $page,
            'total_pages'  => $result['total_pages'],
            'search'       => $search,
        ]);
    }

    public function anggota()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;

        $filter = $_GET['filter'] ?? '';
        $search = $_GET['q'] ?? '';

        $model = new Account();
        $data  = $model->getAdminUserList($page, $filter, $search);
        // $data diharapkan berisi: users, current_page, total_pages, filter, search

        $this->view('admin/kelolaanggota', $data);
    }

    public function laporan()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $range      = $_GET['range'] ?? 'month';
        $roomsPage  = isset($_GET['rooms_page'])   ? (int)$_GET['rooms_page']   : 1;
        $prodiPage  = isset($_GET['prodi_page'])   ? (int)$_GET['prodi_page']   : 1;
        $ratingPage = isset($_GET['rating_page'])  ? (int)$_GET['rating_page']  : 1;

        $laporanModel = new Laporan();

        $rooms = $laporanModel->getSummaryRuangan($range, $roomsPage);
        // $prodi  = $laporanModel->getSummaryProdi($range, $prodiPage);
        // $rating = $laporanModel->getSummaryRating($range, $ratingPage);

        $this->view('admin/laporan', [
            'range'              => $range,
            'summary_rooms'      => $rooms['data'],
            'rooms_page'         => $roomsPage,
            'rooms_total_pages'  => $rooms['total_pages'],
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

        $accountModel = new Account();
        $user = $accountModel->findById((int)$id);

        if (!$user) {
            $this->redirectWithMessage(
                'index.php?controller=admin&action=anggota',
                'User tidak ditemukan.',
                'error'
            );
            return;
        }

        // GET → tampilkan form
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->view('admin/edituser', [
                'user' => $user,
            ]);
            return;
        }

        // POST → proses update
        $nama   = trim($this->input('nama'));
        $email  = trim($this->input('email'));
        $status = $this->input('status_aktif') ?? $user['status_aktif'];
        $role   = $this->input('role') ?? $user['role'];

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

        $accountModel->updateBasicProfile((int)$id, [
            'nama'         => $nama,
            'email'        => $email,
            'status_aktif' => $status,
            'role'         => $role,
        ]);

        $this->redirectWithMessage(
            'index.php?controller=admin&action=anggota',
            'Data user berhasil diperbarui.',
            'success'
        );
    }


    /**
     * Aktif / nonaktif satu ruangan
     */
    public function setRoomStatus()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $idRuangan = $this->input('id_ruangan');
        $status    = $this->input('status'); // 'aktif' atau 'nonaktif'

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

        $roomModel = new Room();
        $id        = (int)$idRuangan;

        // Kalau mau NONAKTIFKAN, cek dulu apakah masih ada booking aktif
        if ($status === 'nonaktif' && $roomModel->hasActiveBookings($id)) {
            $this->redirectWithMessage(
                'index.php?controller=admin&action=ruangan',
                'Tidak dapat menonaktifkan ruangan ini karena masih ada peminjaman aktif. Batalkan dulu booking terkait.',
                'error'
            );
            return;
        }

        $roomModel->updateStatus($id, $status);

        $this->redirectWithMessage(
            'index.php?controller=admin&action=ruangan',
            $status === 'aktif'
                ? 'Ruangan berhasil diaktifkan.'
                : 'Ruangan berhasil dinonaktifkan.',
            'success'
        );
    }

    /**
     * Nonaktifkan semua ruangan sekaligus
     */
    public function deactivateAllRooms()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $roomModel = new Room();

        // Cek apakah masih ada booking aktif di ruangan manapun
        if ($roomModel->hasAnyActiveBookings()) {
            $this->redirectWithMessage(
                'index.php?controller=admin&action=ruangan',
                'Masih terdapat peminjaman aktif. Tidak dapat menonaktifkan seluruh ruangan. Batalkan dulu booking yang berjalan/pending.',
                'error'
            );
            return;
        }

        $roomModel->updateAllStatus('nonaktif');

        $this->redirectWithMessage(
            'index.php?controller=admin&action=ruangan',
            'Seluruh ruangan berhasil dinonaktifkan.',
            'success'
        );
    }

    /**
     * Aktif/nonaktif user (dipanggil dari kelolaanggota)
     */
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

        // Jangan biarkan admin menonaktifkan dirinya sendiri
        $current = Auth::user();
        if (!empty($current['id_account']) && (int)$current['id_account'] === $idUser && $status === 'nonaktif') {
            $this->redirectWithMessage(
                'index.php?controller=admin&action=anggota',
                'Anda tidak dapat menonaktifkan akun Anda sendiri.',
                'error'
            );
            return;
        }

        $accountModel = new Account();
        $ok = $accountModel->updateStatusAktif($idUser, $status);

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
            $status === 'aktif' ? 'User berhasil diaktifkan.' : 'User berhasil dinonaktifkan.',
            'success'
        );
    }

    /**
     * Hapus user (dipanggil dari kelolaanggota)
     */
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

        $accountModel = new Account();
        $ok = $accountModel->deleteById($idUser);

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

        $roomModel = new Room();

        // kalau masih ada ruangan aktif → matikan semua
        if ($roomModel->anyActive()) {
            $roomModel->setAllStatus('nonaktif');
            $this->redirectWithMessage(
                'index.php?controller=admin&action=ruangan',
                'Semua ruangan telah dinonaktifkan.'
            );
        } else {
            // kalau tidak ada yang aktif → aktifkan semua
            $roomModel->setAllStatus('aktif');
            $this->redirectWithMessage(
                'index.php?controller=admin&action=ruangan',
                'Semua ruangan telah diaktifkan.'
            );
        }
    }
    public function editRoom()
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

        $roomModel = new Room();
        $room      = $roomModel->findById((int)$id);

        if (!$room) {
            $this->redirectWithMessage(
                'index.php?controller=admin&action=ruangan',
                'Ruangan tidak ditemukan.',
                'error'
            );
            return;
        }
        $schedule = $roomModel->getScheduleByRoom((int)$id);

        $this->view('admin/editruangan', [
            'room'     => $room,
            'schedule' => $schedule,
        ]);
    }


    public function updateRoom()
    {
        Auth::requireRole(['admin', 'super_admin']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?controller=admin&action=ruangan');
            return;
        }

        $idRuangan = (int)($this->input('id_ruangan') ?? 0);
        $nama      = trim($this->input('nama_ruangan'));
        $lokasi    = trim($this->input('lokasi'));
        $kategori  = trim($this->input('kategori'));
        $kapMin    = (int)$this->input('kapasitas_min');
        $kapMax    = (int)$this->input('kapasitas_max');
        $status    = $this->input('status_operasional') ?? 'aktif';

        if (!$idRuangan || $nama === '' || $kapMin <= 0 || $kapMax <= 0) {
            $this->redirectWithMessage(
                'index.php?controller=admin&action=editRoom&id=' . $idRuangan,
                'Nama ruangan dan kapasitas wajib diisi dengan benar.',
                'error'
            );
            return;
        }

        if (!in_array($status, ['aktif', 'nonaktif'], true)) {
            $status = 'aktif';
        }

        $roomModel = new Room();
        $room      = $roomModel->findById($idRuangan);
        if (!$room) {
            $this->redirectWithMessage(
                'index.php?controller=admin&action=ruangan',
                'Ruangan tidak ditemukan.',
                'error'
            );
            return;
        }
        $fotoPath = $room['foto_ruangan'] ?? null;
        if (!empty($_FILES['foto_ruangan']) && $_FILES['foto_ruangan']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['foto_ruangan']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                $filename  = time() . '_' . preg_replace('/[^a-z0-9\.\-_]/i', '', $_FILES['foto_ruangan']['name']);
                $uploadDir = __DIR__ . '/../../public/uploads/ruangan/';

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $targetPath = $uploadDir . $filename;
                if (move_uploaded_file($_FILES['foto_ruangan']['tmp_name'], $targetPath)) {
                    $fotoPath = 'uploads/ruangan/' . $filename;
                }
            }
        }

        $ok = $roomModel->updateRoom($idRuangan, [
            'nama_ruangan'      => $nama,
            'lokasi'            => $lokasi,
            'kategori'          => $kategori,
            'kapasitas_min'     => $kapMin,
            'kapasitas_max'     => $kapMax,
            'status_operasional' => $status,
            'foto_ruangan'      => $fotoPath,
        ]);

        if (!$ok) {
            $this->redirectWithMessage(
                'index.php?controller=admin&action=editRoom&id=' . $idRuangan,
                'Gagal mengupdate data ruangan.',
                'error'
            );
            return;
        }

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

        $roomModel = new Room();
        $room      = $roomModel->findById((int)$id);

        if (!$room) {
            $this->redirectWithMessage(
                'index.php?controller=admin&action=ruangan',
                'Ruangan tidak ditemukan.',
                'error'
            );
            return;
        }

        $fasilitas         = $roomModel->getFasilitas((int)$id);
        $schedule          = $roomModel->getScheduleByRoom((int)$id);
        $hasActiveBookings = $roomModel->hasActiveBookings((int)$id);

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

        $accountModel = new \App\Models\Account();
        $user         = $accountModel->findById((int)$id);

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
}
