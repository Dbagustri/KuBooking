<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\NotificationService;
use App\Models\BookingAdmin;
use App\Models\BookingReschedule;
use App\Models\Room;
use App\Models\AccountSuspend;
use App\Models\Account;
use App\Models\BookingUser;


class AdminBookingController extends Controller
{
    /** @var BookingAdmin */
    private $bookingAdminModel;
    /** @var BookingReschedule */
    private $bookingRescheduleModel;
    /** @var Room */
    private $roomModel;
    /** @var AccountSuspend */
    private $accountSuspendModel;
    /** @var Account */
    private $accountModel;
    /** @var BookingUser */
    private $bookingUserModel;
    /** @var NotificationService */
    private $notif;

    public function __construct()
    {
        $this->bookingAdminModel      = new BookingAdmin();
        $this->bookingRescheduleModel = new BookingReschedule();
        $this->roomModel              = new Room();
        $this->accountSuspendModel    = new AccountSuspend();
        $this->accountModel           = new Account();
        $this->bookingUserModel       = new BookingUser();
        $this->notif = new NotificationService();
    }
    private function sendNotifToUserById(int $userId, string $jenis, array $data = []): bool
    {
        $user = $this->accountModel->findById($userId);

        if (!$user) {
            error_log("[NOTIF] User not found. userId={$userId}, jenis={$jenis}");
            return false;
        }

        $toEmail = (string)($user['email'] ?? '');
        $toName  = (string)($user['nama'] ?? 'User');

        if ($toEmail === '' || !filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            error_log("[NOTIF] Email invalid/empty. userId={$userId}, jenis={$jenis}, email={$toEmail}");
            return false;
        }

        // pastikan template punya {{nama}}
        $data = array_merge(['nama' => $toName], $data);

        $ok = $this->notif->sendByJenis($jenis, $toEmail, $toName, $data);

        if (!$ok) {
            error_log("[NOTIF] Send failed. userId={$userId}, jenis={$jenis}, email={$toEmail}");
        }

        return $ok;
    }



    public function manage()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $cancelled = $this->bookingAdminModel->autoCancelLateBookings();

        foreach ($cancelled as $b) {
            // kirim ke PJ kalau ada email
            if (!empty($b['id_pj']) && !empty($b['email'])) {
                $toEmail = (string)$b['email'];
                $toName  = (string)($b['nama'] ?? 'User');

                $start = !empty($b['start_time']) ? strtotime($b['start_time']) : null;
                $end   = !empty($b['end_time']) ? strtotime($b['end_time']) : null;

                $this->notif->sendByJenis('booking_auto_cancel', $toEmail, $toName, [
                    'nama'        => $toName,
                    'nama_ruangan' => $b['nama_ruangan'] ?? '',
                    'tanggal'     => $start ? date('Y-m-d', $start) : '',
                    'jam_mulai'   => $start ? date('H:i', $start) : '',
                    'jam_selesai' => $end ? date('H:i', $end) : '',
                    'kode_booking' => $b['booking_code'] ?? '',
                ]);
            }
        }

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;

        $limit  = 5;
        $offset = ($page - 1) * $limit;

        $status = $_GET['status'] ?? 'all';
        $q      = trim($_GET['q'] ?? '');
        $tipe   = $_GET['tipe'] ?? 'internal';

        $allowedStatus = [
            'all',
            'pending',
            'approved',
            'rejected',
            'ongoing',
            'selesai',
            'completed',
            'cancelled',
            'reschedule_pending',
            'reschedule_approved',
            'reschedule_rejected',
        ];
        if (!in_array($status, $allowedStatus, true)) $status = 'all';
        if (!in_array($tipe, ['all', 'internal', 'external'], true)) $tipe = 'internal';

        $totalRows  = $this->bookingAdminModel->countForAdmin($status, $q, $tipe);
        $totalPages = $totalRows > 0 ? (int)ceil($totalRows / $limit) : 1;

        if ($page > $totalPages) {
            $page = $totalPages;
            $offset = ($page - 1) * $limit;
        }

        $bookings = $this->bookingAdminModel->getForAdmin($limit, $offset, $status, $q, $tipe);

        $this->view('admin/kelolabooking', [
            'bookings'     => $bookings,
            'currentPage'  => $page,
            'totalPages'   => $totalPages,
            'statusFilter' => $status,
            'search'       => $q,
            'typeFilter'   => $tipe,
        ]);
    }



    public function detail()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $id = $_GET['id'] ?? null;
        if (!$id || !ctype_digit((string)$id)) {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=manage',
                'ID booking tidak valid.',
                'error'
            );
        }

        $booking = $this->bookingAdminModel->findAdminDetail((int)$id);

        if (!$booking) {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=manage',
                'Data booking tidak ditemukan.',
                'error'
            );
        }

        $members = $this->bookingAdminModel->getMembers((int)$id);

        $this->view('admin/booking-detail', [
            'booking' => $booking,
            'members' => $members,
        ]);
    }

    public function createInternal()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $rooms = $this->roomModel->getAllActive();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->view('admin/booking-form-internal', [
                'rooms' => $rooms,
            ]);
            return;
        }

        $idRuangan  = $this->input('id_ruangan');
        $tanggal    = $this->input('tanggal');
        $jamMulai   = $this->input('jam_mulai');
        $durasi     = (int)$this->input('durasi');
        $keperluan  = $this->input('keperluan');
        $members    = $_POST['members'] ?? [];
        $pjIdInput  = $this->input('pj_id_user');
        // ❌ Blok Sabtu/Minggu
        if ($this->isWeekend($tanggal)) {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=createInternal',
                'Peminjaman tidak tersedia pada Sabtu/Minggu. Pilih hari kerja (Senin–Jumat).',
                'error'
            );
        }

        // ====== VALIDASI DASAR ======
        if (
            !$idRuangan ||
            !$tanggal ||
            !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal) ||
            !$jamMulai ||
            $durasi <= 0
        ) {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=createInternal',
                'Ruangan, tanggal, jam mulai, dan durasi wajib diisi dengan benar.',
                'error'
            );
        }

        // Durasi maksimal 3 jam seperti user biasa
        if ($durasi < 1 || $durasi > 3) {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=createInternal',
                'Durasi peminjaman harus antara 1 hingga 3 jam.',
                'error'
            );
        }

        // Minimal 1 anggota
        if (empty($members) || !is_array($members)) {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=createInternal',
                'Minimal 1 anggota harus ditambahkan.',
                'error'
            );
        }

        $room = $this->roomModel->findById((int)$idRuangan);
        if (!$room) {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=createInternal',
                'Ruangan tidak ditemukan.',
                'error'
            );
        }

        // ====== VALIDASI WAKTU (TIDAK BOLEH DI MASA LALU) ======
        $today = date('Y-m-d');
        $now   = date('Y-m-d H:i:s');

        if ($tanggal < $today) {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=createInternal',
                'Tanggal peminjaman tidak boleh di masa lalu.',
                'error'
            );
        }

        $start = $tanggal . ' ' . $jamMulai . ':00';
        $end   = date('Y-m-d H:i:s', strtotime($start . " + {$durasi} hour"));

        if ($tanggal === $today && $start <= $now) {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=createInternal',
                'Jam mulai sudah lewat dari waktu sekarang.',
                'error'
            );
        }

        // ====== CEK BENTROK DENGAN BOOKING LAIN ======
        if ($this->bookingUserModel->isBentrok((int)$idRuangan, $start, $end)) {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=createInternal',
                'Jadwal bentrok dengan peminjaman lain. Silakan pilih jam lain.',
                'error'
            );
        }

        // ====== VALIDASI ANGGOTA ======
        $kapasitasMin = (int)($room['kapasitas_min'] ?? 0);
        $kapasitasMax = (int)($room['kapasitas_max'] ?? 0);

        $validMembers = [];
        foreach ($members as $mid) {
            if (!ctype_digit((string)$mid)) {
                continue;
            }
            $mid = (int)$mid;

            $user = $this->accountModel->findById($mid);
            if (!$user) {
                return $this->redirectWithMessage(
                    'index.php?controller=adminBooking&action=createInternal',
                    'Salah satu anggota tidak ditemukan di database (ID: ' . $mid . ').',
                    'error'
                );
            }

            // Tidak boleh punya booking aktif
            $active = $this->bookingUserModel->getActiveBookingForUser($mid);
            if ($active) {
                return $this->redirectWithMessage(
                    'index.php?controller=adminBooking&action=createInternal',
                    'User ' . htmlspecialchars($user['nama']) . ' masih memiliki peminjaman aktif.',
                    'error'
                );
            }

            $validMembers[] = $mid;
        }

        if (empty($validMembers)) {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=createInternal',
                'Daftar anggota tidak valid.',
                'error'
            );
        }

        $jumlahAnggota = count($validMembers);

        if ($kapasitasMin > 0 && $jumlahAnggota < $kapasitasMin) {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=createInternal',
                'Jumlah anggota kurang dari kapasitas minimum ruangan (' . $kapasitasMin . ').',
                'error'
            );
        }

        if ($kapasitasMax > 0 && $jumlahAnggota > $kapasitasMax) {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=createInternal',
                'Jumlah anggota melebihi kapasitas maksimum ruangan (' . $kapasitasMax . ').',
                'error'
            );
        }

        // ====== TENTUKAN PJ (ANGGOTA PERTAMA DEFAULT) ======
        $pjIdUser = (int)($pjIdInput ?? 0);
        if (!$pjIdUser || !in_array($pjIdUser, $validMembers, true)) {
            $pjIdUser = $validMembers[0];
        }

        $data = [
            'id_ruangan' => (int)$idRuangan,
            'tanggal'    => $tanggal,
            'jam_mulai'  => $jamMulai,
            'durasi'     => $durasi,
            'keperluan'  => $keperluan,
            'members'    => $validMembers,
            'pj_id_user' => $pjIdUser,
        ];

        $idBooking = $this->bookingAdminModel->createInternalBooking($data);

        if (!$idBooking) {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=createInternal',
                'Gagal membuat booking internal. Cek kembali jadwal (kemungkinan bentrok).',
                'error'
            );
        }

        return $this->redirectWithMessage(
            'index.php?controller=adminBooking&action=manage',
            'Booking internal berhasil dibuat.'
        );
    }

    public function createExternal()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $rooms = $this->roomModel->getAllActive();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idRuangan     = $this->input('id_ruangan');
            $tanggal       = $this->input('tanggal');
            $jamMulai      = $this->input('jam_mulai');
            $durasi        = (int)$this->input('durasi');
            $jumlahAnggota = (int)$this->input('jumlah_anggota');
            $keperluan     = $this->input('keperluan');
            $guestName     = $this->input('guest_name')  ?: $this->input('nama_peminjam');
            $guestEmail    = $this->input('guest_email') ?: $this->input('email_peminjam');
            $guestPhone    = $this->input('guest_phone') ?: $this->input('no_hp');
            $asalInstansi  = $this->input('asal_instansi');

            if ($this->isWeekend($tanggal)) {
                return $this->redirectWithMessage(
                    'index.php?controller=adminBooking&action=createExternal',
                    $this->weekendMessage(),
                    'error'
                );
            }

            if (
                !$idRuangan ||
                !$tanggal ||
                !$jamMulai ||
                $durasi <= 0 ||
                !$guestName
            ) {
                return $this->redirectWithMessage(
                    'index.php?controller=adminBooking&action=createExternal',
                    'Field wajib (ruangan, tanggal, jam, durasi, nama peminjam) harus diisi.',
                    'error'
                );
            }

            if (!ctype_digit((string)$idRuangan)) {
                return $this->redirectWithMessage(
                    'index.php?controller=adminBooking&action=createExternal',
                    'ID ruangan tidak valid.',
                    'error'
                );
            }

            $ruangan = $this->roomModel->findById((int)$idRuangan);
            if (!$ruangan || ($ruangan['status_operasional'] ?? '') !== 'aktif') {
                return $this->redirectWithMessage(
                    'index.php?controller=adminBooking&action=createExternal',
                    'Ruangan tidak ditemukan atau tidak aktif.',
                    'error'
                );
            }

            $today = date('Y-m-d');
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal) || $tanggal < $today) {
                return $this->redirectWithMessage(
                    'index.php?controller=adminBooking&action=createExternal',
                    'Tanggal peminjaman tidak valid atau sudah lewat.',
                    'error'
                );
            }
            if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $jamMulai)) {
                return $this->redirectWithMessage(
                    'index.php?controller=adminBooking&action=createExternal',
                    'Format jam mulai tidak valid.',
                    'error'
                );
            }
            if ($durasi < 1 || $durasi > 3) {
                return $this->redirectWithMessage(
                    'index.php?controller=adminBooking&action=createExternal',
                    'Durasi peminjaman tidak valid (harus 1–3 jam).',
                    'error'
                );
            }

            $startDateTime = \DateTime::createFromFormat('Y-m-d H:i', $tanggal . ' ' . substr($jamMulai, 0, 5));
            if (!$startDateTime) {
                return $this->redirectWithMessage(
                    'index.php?controller=adminBooking&action=createExternal',
                    'Kombinasi tanggal & jam mulai tidak valid.',
                    'error'
                );
            }

            $endDateTime = clone $startDateTime;
            $endDateTime->modify('+' . $durasi . ' hour');

            $now = new \DateTime();
            if ($tanggal === $today && $startDateTime <= $now) {
                return $this->redirectWithMessage(
                    'index.php?controller=adminBooking&action=createExternal',
                    'Jam mulai sudah lewat dari waktu sekarang.',
                    'error'
                );
            }

            $kapMin = (int)($ruangan['kapasitas_min'] ?? 0);
            $kapMax = (int)($ruangan['kapasitas_max'] ?? 0);

            if ($jumlahAnggota <= 0) {
                $jumlahAnggota = 1;
            }

            if ($kapMin > 0 && $jumlahAnggota < $kapMin) {
                return $this->redirectWithMessage(
                    'index.php?controller=adminBooking&action=createExternal',
                    'Jumlah anggota kurang dari kapasitas minimum ruangan.',
                    'error'
                );
            }

            if ($kapMax > 0 && $jumlahAnggota > $kapMax) {
                return $this->redirectWithMessage(
                    'index.php?controller=adminBooking&action=createExternal',
                    'Jumlah anggota melebihi kapasitas maksimum ruangan.',
                    'error'
                );
            }

            // upload surat izin
            $suratIzinPath = null;
            if (!empty($_FILES['surat_izin']) && $_FILES['surat_izin']['error'] !== UPLOAD_ERR_NO_FILE) {
                if ($_FILES['surat_izin']['error'] !== UPLOAD_ERR_OK) {
                    return $this->redirectWithMessage(
                        'index.php?controller=adminBooking&action=createExternal',
                        'Gagal mengunggah surat izin.',
                        'error'
                    );
                }

                $ext     = strtolower(pathinfo($_FILES['surat_izin']['name'], PATHINFO_EXTENSION));
                $allowed = ['pdf', 'jpg', 'jpeg', 'png'];

                if (!in_array($ext, $allowed, true)) {
                    return $this->redirectWithMessage(
                        'index.php?controller=adminBooking&action=createExternal',
                        'Surat izin harus berupa PDF atau gambar (JPG/PNG).',
                        'error'
                    );
                }

                if ($_FILES['surat_izin']['size'] > 5 * 1024 * 1024) {
                    return $this->redirectWithMessage(
                        'index.php?controller=adminBooking&action=createExternal',
                        'Ukuran file surat izin maksimal 5MB.',
                        'error'
                    );
                }

                $uploadDir = __DIR__ . '/../../public/uploads/surat_izin/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $safeName = preg_replace('/[^a-z0-9\.\-_]/i', '', $_FILES['surat_izin']['name']);
                $filename = time() . '_' . $safeName;
                $target   = $uploadDir . $filename;

                if (!move_uploaded_file($_FILES['surat_izin']['tmp_name'], $target)) {
                    return $this->redirectWithMessage(
                        'index.php?controller=adminBooking&action=createExternal',
                        'Gagal menyimpan file surat izin di server.',
                        'error'
                    );
                }

                $suratIzinPath = 'uploads/surat_izin/' . $filename;
            }

            $data = [
                'id_ruangan'     => (int)$idRuangan,
                'tanggal'        => $tanggal,
                'jam_mulai'      => substr($jamMulai, 0, 5),
                'durasi'         => $durasi,
                'jumlah_anggota' => $jumlahAnggota,
                'keperluan'      => $keperluan,
                'guest_name'     => $guestName,
                'guest_email'    => $guestEmail,
                'guest_phone'    => $guestPhone,
                'asal_instansi'  => $asalInstansi,
                'surat_izin'     => $suratIzinPath,
            ];

            $idBooking = $this->bookingAdminModel->createExternalBooking($data);

            if (!$idBooking) {
                return $this->redirectWithMessage(
                    'index.php?controller=adminBooking&action=createExternal',
                    'Gagal membuat booking eksternal. Cek kembali jadwal (kemungkinan bentrok) atau kapasitas.',
                    'error'
                );
            }

            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=manage',
                'Booking eksternal berhasil dibuat.'
            );
        }

        $this->view('admin/booking-eksternal', [
            'rooms' => $rooms,
        ]);
    }

    public function edit()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $id = $_GET['id'] ?? null;
        if (!$id || !ctype_digit((string)$id)) {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=manage',
                'ID booking tidak valid.',
                'error'
            );
        }

        $booking = $this->bookingAdminModel->findWithRoom((int)$id);
        if (!$booking) {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=manage',
                'Data booking tidak ditemukan.',
                'error'
            );
        }

        $members = $this->bookingAdminModel->getMembers((int)$id);
        $rooms   = $this->roomModel->getAllActive();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->view('admin/booking-edit', [
                'booking' => $booking,
                'rooms'   => $rooms,
                'members' => $members,
            ]);
            return;
        }

        $idRuangan = $this->input('id_ruangan');
        $tanggal   = $this->input('tanggal');
        $jamMulai  = $this->input('jam_mulai');
        $durasi    = (int)$this->input('durasi');
        $keperluan = $this->input('keperluan');

        $memberIds = array_map('intval', $_POST['members'] ?? []);
        $memberIds = array_values(array_unique(array_filter($memberIds)));

        if (empty($memberIds)) {
            return $this->redirectWithMessage(
                "index.php?controller=adminBooking&action=edit&id={$id}",
                'Minimal 1 anggota harus ada pada booking.',
                'error'
            );
        }

        $idRuangan = $idRuangan && ctype_digit((string)$idRuangan)
            ? (int)$idRuangan
            : (int)$booking['id_ruangan'];

        $tanggal  = $tanggal ?: date('Y-m-d', strtotime($booking['tanggal']));
        $jamMulai = $jamMulai ?: date('H:i', strtotime($booking['start_time']));
        $durasi   = $durasi > 0 ? $durasi : 1;
        if ($durasi > 3) {
            $durasi = 3;
        }
        if ($this->isWeekend($tanggal)) {
            return $this->redirectWithMessage(
                "index.php?controller=adminBooking&action=edit&id={$id}",
                $this->weekendMessage(),
                'error'
            );
        }

        $keperluan = $keperluan !== null ? $keperluan : ($booking['keperluan'] ?? '');

        $room = $this->roomModel->findById($idRuangan);
        if (!$room) {
            return $this->redirectWithMessage(
                "index.php?controller=adminBooking&action=edit&id={$id}",
                'Ruangan tidak ditemukan.',
                'error'
            );
        }

        $kapMin         = (int)($room['kapasitas_min'] ?? 0);
        $kapMax         = (int)($room['kapasitas_max'] ?? 0);
        $jumlahAnggota  = count($memberIds);

        if ($kapMin > 0 && $jumlahAnggota < $kapMin) {
            return $this->redirectWithMessage(
                "index.php?controller=adminBooking&action=edit&id={$id}",
                'Jumlah anggota kurang dari kapasitas minimum ruangan (' . $kapMin . ').',
                'error'
            );
        }
        if ($kapMax > 0 && $jumlahAnggota > $kapMax) {
            return $this->redirectWithMessage(
                "index.php?controller=adminBooking&action=edit&id={$id}",
                'Jumlah anggota melebihi kapasitas maksimum ruangan (' . $kapMax . ').',
                'error'
            );
        }

        $dataUpdate = [
            'id_booking' => (int)$id,
            'id_ruangan' => $idRuangan,
            'tanggal'    => $tanggal,
            'jam_mulai'  => $jamMulai,
            'durasi'     => $durasi,
            'keperluan'  => $keperluan,
        ];

        $ok = $this->bookingAdminModel->updateAdminBooking($dataUpdate, $memberIds);

        if (!$ok) {
            return $this->redirectWithMessage(
                "index.php?controller=adminBooking&action=edit&id={$id}",
                'Gagal mengupdate booking (jadwal bentrok atau data tidak valid).',
                'error'
            );
        }

        return $this->redirectWithMessage(
            'index.php?controller=adminBooking&action=manage',
            'Booking berhasil diperbarui.'
        );
    }

    public function delete()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $idBooking = $_POST['id_booking'] ?? null;

        if (!$idBooking || !ctype_digit((string)$idBooking)) {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=manage',
                'ID booking tidak valid.',
                'error'
            );
        }

        $this->bookingAdminModel->deleteBooking((int)$idBooking);

        return $this->redirectWithMessage(
            'index.php?controller=adminBooking&action=manage',
            'Booking berhasil dihapus.'
        );
    }

    public function approve()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $idBooking = $_POST['id_booking'] ?? null;

        if (!$idBooking || !ctype_digit((string)$idBooking)) {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=manage',
                'ID booking tidak ditemukan.',
                'error'
            );
        }

        $idBooking  = (int)$idBooking;
        $lastStatus = $this->bookingAdminModel->getLastStatus($idBooking);

        if (in_array($lastStatus, ['approved', 'reschedule_approved', 'cancelled', 'rejected', 'selesai', 'completed'], true)) {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=manage',
                'Booking ini sudah tidak bisa disetujui lagi.',
                'error'
            );
        }
        if ($lastStatus === 'reschedule_pending') {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=manage',
                'Booking ini sedang menunggu reschedule. Gunakan menu "Proses Reschedule".',
                'error'
            );
        }

        $this->bookingAdminModel->addStatus($idBooking, 'approved');
        // Ambil data booking untuk isi template
        $booking = $this->bookingAdminModel->findWithRoom($idBooking);
        if ($booking && !empty($booking['id_pj'])) {
            $start = isset($booking['start_time']) ? strtotime($booking['start_time']) : null;
            $end   = isset($booking['end_time']) ? strtotime($booking['end_time']) : null;

            $this->sendNotifToUserById((int)$booking['id_pj'], 'booking_approved', [
                'nama_ruangan'  => $booking['nama_ruangan'] ?? '',
                'tanggal'       => $start ? date('Y-m-d', $start) : ($booking['tanggal'] ?? ''),
                'jam_mulai'     => $start ? date('H:i', $start) : '',
                'jam_selesai'   => $end ? date('H:i', $end) : '',
                'kode_booking'  => $booking['booking_code'] ?? '',
            ]);
        }

        return $this->redirectWithMessage(
            'index.php?controller=adminBooking&action=manage',
            'Booking berhasil disetujui.'
        );
    }

    public function reject()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $idBooking = $_POST['id_booking'] ?? null;
        $alasan    = $_POST['alasan'] ?? '';

        if (!$idBooking || !ctype_digit((string)$idBooking)) {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=manage',
                'ID booking tidak ditemukan.',
                'error'
            );
        }

        $idBooking  = (int)$idBooking;
        $lastStatus = $this->bookingAdminModel->getLastStatus($idBooking);

        if (in_array($lastStatus, ['rejected', 'cancelled', 'selesai', 'completed'], true)) {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=manage',
                'Booking ini sudah berstatus final, tidak dapat ditolak lagi.',
                'error'
            );
        }

        $this->bookingAdminModel->addStatus($idBooking, 'rejected', $alasan ?: null);
        $booking = $this->bookingAdminModel->findWithRoom($idBooking);
        if ($booking && !empty($booking['id_pj'])) {
            $start = isset($booking['start_time']) ? strtotime($booking['start_time']) : null;
            $end   = isset($booking['end_time']) ? strtotime($booking['end_time']) : null;

            $this->sendNotifToUserById((int)$booking['id_pj'], 'booking_rejected', [
                'nama_ruangan' => $booking['nama_ruangan'] ?? '',
                'tanggal'      => $start ? date('Y-m-d', $start) : ($booking['tanggal'] ?? ''),
                'jam_mulai'    => $start ? date('H:i', $start) : '',
                'jam_selesai'  => $end ? date('H:i', $end) : '',
                'kode_booking' => $booking['booking_code'] ?? '',
                'alasan'       => $alasan ?: 'Tidak ada keterangan.',
            ]);
        }

        return $this->redirectWithMessage(
            'index.php?controller=adminBooking&action=manage',
            'Booking berhasil ditolak.',
            'success'
        );
    }

    public function start()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $idBookingRaw = $_POST['id_booking'] ?? null;

        if (!$idBookingRaw || !ctype_digit((string)$idBookingRaw)) {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=manage',
                'ID booking tidak ditemukan.',
                'error'
            );
        }

        $idBooking = (int)$idBookingRaw;
        $detailUrl = 'index.php?controller=adminBooking&action=detail&id=' . $idBooking;

        $lastStatus = $this->bookingAdminModel->getLastStatus($idBooking);

        if (!in_array($lastStatus, ['approved', 'reschedule_approved'], true)) {
            return $this->redirectWithMessage(
                $detailUrl,
                'Booking hanya dapat dimulai jika sudah disetujui (APPROVED).',
                'error'
            );
        }

        $booking = $this->bookingAdminModel->findWithRoom($idBooking);
        if (!$booking) {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=manage',
                'Data booking tidak ditemukan.',
                'error'
            );
        }

        if (!empty($booking['checkin_time'])) {
            return $this->redirectWithMessage(
                $detailUrl,
                'Booking ini sudah pernah dimulai.',
                'error'
            );
        }

        $now        = date('Y-m-d H:i:s');
        $start_time = $booking['start_time'];

        if ($now < $start_time) {
            return $this->redirectWithMessage(
                $detailUrl,
                'Tidak dapat mulai sebelum waktu peminjaman.',
                'error'
            );
        }

        $this->bookingAdminModel->setCheckinTime($idBooking);

        return $this->redirectWithMessage(
            $detailUrl,
            'Booking telah dimulai. Kunci boleh diserahkan.'
        );
    }

    public function complete()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $idBooking = $_POST['id_booking'] ?? null;

        if (!$idBooking || !ctype_digit((string)$idBooking)) {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=manage',
                'ID booking tidak ditemukan.',
                'error'
            );
        }

        $idBooking = (int)$idBooking;
        $booking   = $this->bookingAdminModel->findWithRoom($idBooking);

        if (!$booking) {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=manage',
                'Data booking tidak ditemukan.',
                'error'
            );
        }

        if (empty($booking['checkin_time'])) {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=manage',
                'Booking belum pernah dimulai, tidak dapat ditandai selesai.',
                'error'
            );
        }

        $this->bookingAdminModel->addStatus($idBooking, 'selesai');

        if (!empty($booking['id_pj'])) {
            $this->accountSuspendModel->resetCounter((int)$booking['id_pj']);
        }

        return $this->redirectWithMessage(
            'index.php?controller=adminBooking&action=manage',
            'Booking telah ditandai selesai.'
        );
    }

    public function closeDate()
    {
        Auth::requireRole(['admin', 'super_admin']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tanggal = $this->input('tanggal');

            if (!$tanggal || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
                return $this->redirectWithMessage(
                    'index.php?controller=adminBooking&action=closeDate',
                    'Format tanggal tidak valid.',
                    'error'
                );
            }

            $count = $this->bookingAdminModel->cancelBookingsByDate($tanggal);

            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=manage',
                "Penutupan perpustakaan berhasil. {$count} booking pada tanggal {$tanggal} dibatalkan."
            );
        }

        $this->view('admin/close-date', []);
    }

    public function processReschedule()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $idBooking = $_GET['id_booking'] ?? null;
        if (!$idBooking || !ctype_digit((string)$idBooking)) {
            $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=manage',
                'ID booking tidak valid.',
                'error'
            );
            return;
        }
        $idBooking = (int)$idBooking;

        $booking = $this->bookingAdminModel->findAdminDetail($idBooking);
        if (!$booking) {
            $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=manage',
                'Data booking tidak ditemukan.',
                'error'
            );
            return;
        }

        if (($booking['last_status'] ?? $booking['status'] ?? '') !== 'reschedule_pending') {
            $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=detail&id=' . $idBooking,
                'Booking ini tidak memiliki reschedule yang sedang menunggu persetujuan.',
                'error'
            );
            return;
        }

        $reschedule = $this->bookingRescheduleModel->findLatestByBooking($idBooking);
        if (!$reschedule) {
            $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=detail&id=' . $idBooking,
                'Data reschedule tidak ditemukan.',
                'error'
            );
            return;
        }

        $idReschedule = (int)$reschedule['id_reschedule'];
        $newMembers   = $this->bookingRescheduleModel->getMembers($idReschedule);

        $this->view('admin/reschedule_detail', [
            'booking'    => $booking,
            'reschedule' => $reschedule,
            'newMembers' => $newMembers,
        ]);
    }

    public function approveReschedule()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $idReschedule = (int)($this->input('id_reschedule') ?? 0);
        if ($idReschedule <= 0) {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=manage',
                'Data reschedule tidak valid.',
                'error'
            );
        }

        $result = $this->bookingAdminModel->approveReschedule($idReschedule);

        if (empty($result['success'])) {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=manage',
                $result['message'] ?? 'Gagal menyetujui reschedule.',
                'error'
            );
        }

        $idBooking = (int)($result['id_booking'] ?? $result['id_bookings'] ?? $result['booking_id'] ?? 0);

        if ($idBooking > 0) {
            $booking = $this->bookingAdminModel->findWithRoom($idBooking);

            if (!$booking) {
                error_log("[NOTIF] reschedule_approved: booking not found. idBooking={$idBooking}");
            } else {
                // fallback kalau key beda
                $idPj = (int)($booking['id_pj'] ?? $booking['pj_id_user'] ?? $booking['id_user_pj'] ?? 0);

                if ($idPj <= 0) {
                    error_log("[NOTIF] reschedule_approved: id_pj empty. idBooking={$idBooking}. keys=" . implode(',', array_keys($booking)));
                } else {
                    $start = !empty($booking['start_time']) ? strtotime($booking['start_time']) : null;
                    $end   = !empty($booking['end_time']) ? strtotime($booking['end_time']) : null;

                    $sent = $this->sendNotifToUserById($idPj, 'reschedule_approved', [
                        'nama_ruangan'     => $booking['nama_ruangan'] ?? '',
                        'tanggal_baru'     => $start ? date('Y-m-d', $start) : ($booking['tanggal'] ?? ''),
                        'jam_mulai_baru'   => $start ? date('H:i', $start) : '',
                        'jam_selesai_baru' => $end ? date('H:i', $end) : '',
                        'kode_booking'     => $booking['booking_code'] ?? '',
                    ]);

                    if ($sent === false) {
                        error_log("[NOTIF] reschedule_approved: send failed. idBooking={$idBooking}, id_pj={$idPj}");
                    }
                }
            }
        } else {
            error_log("[NOTIF] reschedule_approved: idBooking missing from result keys=" . implode(',', array_keys((array)$result)));
        }


        $targetUrl = $idBooking > 0
            ? 'index.php?controller=adminBooking&action=detail&id=' . $idBooking
            : 'index.php?controller=adminBooking&action=manage';

        return $this->redirectWithMessage(
            $targetUrl,
            'Reschedule berhasil disetujui. Jadwal booking utama telah diperbarui.',
            'success'
        );
    }

    public function rejectReschedule()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $idReschedule = (int)($this->input('id_reschedule') ?? 0);
        $alasan       = trim((string)($this->input('alasan_reject') ?? ''));

        if ($idReschedule <= 0) {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=manage',
                'Data reschedule tidak valid.',
                'error'
            );
        }

        $result = $this->bookingAdminModel->rejectReschedule($idReschedule, $alasan ?: null);

        if (empty($result['success'])) {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=manage',
                $result['message'] ?? 'Gagal menolak reschedule.',
                'error'
            );
        }

        $idBooking = (int)($result['id_booking'] ?? 0);

        // ===== NOTIF (tidak mengganggu flow utama) =====
        if ($idBooking > 0) {
            $booking = $this->bookingAdminModel->findWithRoom($idBooking);

            if (!$booking) {
                error_log("[NOTIF] reschedule_rejected: booking not found. idBooking={$idBooking}");
            } else {
                $idPj = (int)($booking['id_pj'] ?? 0);
                if ($idPj <= 0) {
                    error_log("[NOTIF] reschedule_rejected: id_pj empty. idBooking={$idBooking}");
                } else {
                    $start = !empty($booking['start_time']) ? strtotime($booking['start_time']) : null;
                    $end   = !empty($booking['end_time']) ? strtotime($booking['end_time']) : null;

                    $sent = $this->sendNotifToUserById($idPj, 'reschedule_rejected', [
                        'nama_ruangan'     => $booking['nama_ruangan'] ?? '',
                        'tanggal_lama'     => $start ? date('Y-m-d', $start) : '',
                        'jam_mulai_lama'   => $start ? date('H:i', $start) : '',
                        'jam_selesai_lama' => $end ? date('H:i', $end) : '',
                        'kode_booking'     => $booking['booking_code'] ?? '',
                        'alasan'           => $alasan !== '' ? $alasan : 'Tidak ada keterangan.',
                    ]);

                    if ($sent === false) {
                        error_log("[NOTIF] reschedule_rejected: send failed. idBooking={$idBooking}, id_pj={$idPj}");
                    }
                }
            }
        }

        $targetUrl = $idBooking > 0
            ? 'index.php?controller=adminBooking&action=detail&id=' . $idBooking
            : 'index.php?controller=adminBooking&action=manage';

        return $this->redirectWithMessage(
            $targetUrl,
            'Reschedule ditolak.',
            'success'
        );
    }
}
