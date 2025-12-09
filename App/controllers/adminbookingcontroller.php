<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\BookingAdmin;
use App\Models\BookingReschedule;
use App\Models\Room;
use App\Models\AccountSuspend;
use App\Models\Account;
use App\Models\BookingUser;

class AdminBookingController extends Controller
{

    public function manage()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $bookingModel = new BookingAdmin();
        $bookingModel->autoCancelLateBookings();

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;

        $limit  = 5;
        $offset = ($page - 1) * $limit;
        $totalRows   = $bookingModel->countAllForAdmin();
        $totalPages  = $totalRows > 0 ? (int)ceil($totalRows / $limit) : 1;
        $bookings = $bookingModel->getAllForAdmin($limit, $offset);

        $this->view('admin/kelolabooking', [
            'bookings'    => $bookings,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
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

        $bookingModel = new BookingAdmin();
        $booking      = $bookingModel->findAdminDetail((int)$id);

        if (!$booking) {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=manage',
                'Data booking tidak ditemukan.',
                'error'
            );
        }

        $members = $bookingModel->getMembers((int)$id);

        $this->view('admin/booking-detail', [
            'booking' => $booking,
            'members' => $members,
        ]);
    }

    public function createInternal()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $roomModel      = new Room();
        $rooms          = $roomModel->getAllActive();
        $bookingModel   = new BookingAdmin();
        $accountModel   = new Account();
        $bookingUser    = new BookingUser();

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
        if (empty($members) || !is_array($members)) {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=createInternal',
                'Minimal 1 anggota harus ditambahkan.',
                'error'
            );
        }
        $room = $roomModel->findById((int)$idRuangan);
        if (!$room) {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=createInternal',
                'Ruangan tidak ditemukan.',
                'error'
            );
        }

        $kapasitasMin = (int)($room['kapasitas_min'] ?? 0);
        $kapasitasMax = (int)($room['kapasitas_max'] ?? 0);
        $validMembers = [];
        foreach ($members as $mid) {
            if (!ctype_digit((string)$mid)) {
                continue;
            }
            $mid = (int)$mid;
            $user = $accountModel->findById($mid);
            if (!$user) {
                return $this->redirectWithMessage(
                    'index.php?controller=adminBooking&action=createInternal',
                    'Salah satu anggota tidak ditemukan di database (ID: ' . $mid . ').',
                    'error'
                );
            }
            $active = $bookingUser->getActiveBookingForUser($mid);
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

        $pjIdUser = (int)($pjIdInput ?? 0);
        if (!$pjIdUser || !in_array($pjIdUser, $validMembers, true)) {
            $pjIdUser = $validMembers[0];
        }

        $data = [
            'id_ruangan'   => (int)$idRuangan,
            'tanggal'      => $tanggal,
            'jam_mulai'    => $jamMulai,
            'durasi'       => $durasi,
            'keperluan'    => $keperluan,
            'members'      => $validMembers,
            'pj_id_user'   => $pjIdUser,
        ];

        $idBooking = $bookingModel->createInternalBooking($data);

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

        $roomModel    = new Room();
        $rooms        = $roomModel->getAllActive();
        $bookingModel = new BookingAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idRuangan     = $this->input('id_ruangan');
            $tanggal       = $this->input('tanggal');
            $jamMulai      = $this->input('jam_mulai');
            $durasi        = (int) $this->input('durasi');
            $jumlahAnggota = (int) $this->input('jumlah_anggota');
            $keperluan     = $this->input('keperluan');
            $guestName  = $this->input('guest_name')  ?: $this->input('nama_peminjam');
            $guestEmail = $this->input('guest_email') ?: $this->input('email_peminjam');
            $guestPhone = $this->input('guest_phone') ?: $this->input('no_hp');
            $asalInstansi = $this->input('asal_instansi');

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

            $ruangan = $roomModel->findById((int)$idRuangan);
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
                'id_ruangan'      => (int) $idRuangan,
                'tanggal'         => $tanggal,
                'jam_mulai'       => substr($jamMulai, 0, 5),
                'durasi'          => $durasi,
                'jumlah_anggota'  => $jumlahAnggota,
                'keperluan'       => $keperluan,
                'guest_name'      => $guestName,
                'guest_email'     => $guestEmail,
                'guest_phone'     => $guestPhone,
                'asal_instansi'   => $asalInstansi,
                'surat_izin'      => $suratIzinPath,
            ];

            $idBooking = $bookingModel->createExternalBooking($data);

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
        $bookingModel = new BookingAdmin();
        $roomModel    = new Room();

        $booking = $bookingModel->findWithRoom((int)$id);
        if (!$booking) {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=manage',
                'Data booking tidak ditemukan.',
                'error'
            );
        }
        $members = $bookingModel->getMembers((int)$id);
        $rooms   = $roomModel->getAllActive();
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

        $tanggal = $tanggal ?: date('Y-m-d', strtotime($booking['tanggal']));
        $jamMulai = $jamMulai ?: date('H:i', strtotime($booking['start_time']));
        $durasi = $durasi > 0 ? $durasi : 1;
        if ($durasi > 3) {
            $durasi = 3;
        }

        $keperluan = $keperluan !== null ? $keperluan : ($booking['keperluan'] ?? '');
        $room = $roomModel->findById($idRuangan);
        if (!$room) {
            return $this->redirectWithMessage(
                "index.php?controller=adminBooking&action=edit&id={$id}",
                'Ruangan tidak ditemukan.',
                'error'
            );
        }
        $kapMin = (int)($room['kapasitas_min'] ?? 0);
        $kapMax = (int)($room['kapasitas_max'] ?? 0);
        $jumlahAnggota = count($memberIds);

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

        $ok = $bookingModel->updateAdminBooking($dataUpdate, $memberIds);

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

        $bookingModel = new BookingAdmin();
        $bookingModel->deleteBooking((int)$idBooking);

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

        $bookingModel = new BookingAdmin();
        $idBooking    = (int)$idBooking;
        $lastStatus = $bookingModel->getLastStatus($idBooking);

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
        $bookingModel->addStatus($idBooking, 'approved');

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

        $bookingModel = new BookingAdmin();
        $idBooking    = (int)$idBooking;

        $lastStatus = $bookingModel->getLastStatus($idBooking);

        if (in_array($lastStatus, ['rejected', 'cancelled', 'selesai', 'completed'], true)) {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=manage',
                'Booking ini sudah berstatus final, tidak dapat ditolak lagi.',
                'error'
            );
        }
        $bookingModel->addStatus($idBooking, 'rejected', $alasan ?: null);

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
        $bookingModel = new BookingAdmin();
        $lastStatus   = $bookingModel->getLastStatus($idBooking);

        if (!in_array($lastStatus, ['approved', 'reschedule_approved'], true)) {
            return $this->redirectWithMessage(
                $detailUrl,
                'Booking hanya dapat dimulai jika sudah disetujui (APPROVED).',
                'error'
            );
        }

        $booking = $bookingModel->findWithRoom($idBooking);
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

        $bookingModel->setCheckinTime($idBooking);

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

        $bookingModel = new BookingAdmin();
        $idBooking    = (int)$idBooking;
        $booking = $bookingModel->findWithRoom($idBooking);
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
        $bookingModel->addStatus($idBooking, 'selesai');
        if (!empty($booking['id_pj'])) {
            $accSuspend = new AccountSuspend();
            $accSuspend->resetCounter((int)$booking['id_pj']);
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

            $bookingModel = new BookingAdmin();
            $count        = $bookingModel->cancelBookingsByDate($tanggal);

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

        $bookingModel    = new BookingAdmin();
        $rescheduleModel = new BookingReschedule();

        $booking = $bookingModel->findAdminDetail($idBooking);
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

        $reschedule = $rescheduleModel->findLatestByBooking($idBooking);
        if (!$reschedule) {
            $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=detail&id=' . $idBooking,
                'Data reschedule tidak ditemukan.',
                'error'
            );
            return;
        }

        $idReschedule = (int)$reschedule['id_reschedule'];
        $newMembers   = $rescheduleModel->getMembers($idReschedule);

        $this->view('admin/reschedule_detail', [
            'booking'     => $booking,
            'reschedule'  => $reschedule,
            'newMembers'  => $newMembers,
        ]);
    }

    public function approveReschedule()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $idReschedule = (int)($this->input('id_reschedule') ?? 0);
        if (!$idReschedule) {
            $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=manage',
                'Data reschedule tidak valid.',
                'error'
            );
            return;
        }

        $bookingModel = new BookingAdmin();
        $result       = $bookingModel->approveReschedule($idReschedule);

        if (empty($result['success'])) {
            $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=manage',
                $result['message'] ?? 'Gagal menyetujui reschedule.',
                'error'
            );
            return;
        }
        $idBooking = $result['id_booking'] ?? null;
        $targetUrl = $idBooking
            ? 'index.php?controller=adminBooking&action=detail&id=' . (int)$idBooking
            : 'index.php?controller=adminBooking&action=manage';

        $this->redirectWithMessage(
            $targetUrl,
            'Reschedule berhasil disetujui. Jadwal booking utama telah diperbarui.',
            'success'
        );
    }

    public function rejectReschedule()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $idReschedule = (int)($this->input('id_reschedule') ?? 0);
        $alasan       = $this->input('alasan_reject') ?? null;

        if (!$idReschedule) {
            $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=manage',
                'Data reschedule tidak valid.',
                'error'
            );
            return;
        }

        $bookingModel = new BookingAdmin();
        $result       = $bookingModel->rejectReschedule($idReschedule, $alasan);

        if (empty($result['success'])) {
            $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=manage',
                $result['message'] ?? 'Gagal menolak reschedule.',
                'error'
            );
            return;
        }

        $idBooking = $result['id_booking'] ?? null;
        $targetUrl = $idBooking
            ? 'index.php?controller=adminBooking&action=detail&id=' . (int)$idBooking
            : 'index.php?controller=adminBooking&action=manage';

        $this->redirectWithMessage(
            $targetUrl,
            'Reschedule ditolak.',
            'success'
        );
    }
}
