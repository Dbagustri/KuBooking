<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Account;
use App\Models\BookingUser;
use App\Models\Room;
use App\Models\BookingReschedule;

class UserBookingController extends Controller
{
    private function getCurrentAccount(): ?array
    {
        $accountId = Auth::id(); // harusnya berisi id_account
        if (!$accountId) {
            return null;
        }

        $accountModel = new Account();
        $acc = $accountModel->findById($accountId);

        return $acc ?: null;
    }
    private function ensureCanBook(): ?array
    {
        $account = $this->getCurrentAccount();

        if (!$account || ($account['status_aktif'] ?? '') !== 'aktif') {
            $this->redirectWithMessage(
                'index.php?controller=userBooking&action=home',
                'Akun Anda belum aktif atau sedang disuspend. Anda belum dapat melakukan booking.',
                'error'
            );
            return null;
        }

        return $account;
    }

    public function profil()
    {
        Auth::requireRole(['mahasiswa', 'dosen', 'tendik']);

        $account = $this->getCurrentAccount();
        if (!$account) {
            $this->redirect('index.php?controller=auth&action=login');
            return;
        }

        $this->view('user/profil', [
            'currentUser' => $account,
        ]);
    }

    public function home()
    {
        Auth::requireRole(['mahasiswa', 'dosen', 'tendik']);

        $bookingModel = new BookingUser();
        $roomModel    = new Room();

        $account        = $this->getCurrentAccount();
        $booking_aktif  = null;
        $canBook        = false;
        $unratedBooking = null;

        if ($account) {
            $canBook       = ($account['status_aktif'] ?? '') === 'aktif';
            $booking_aktif = $bookingModel->getActiveBookingForUser((int)$account['id_account']);

            // Kalau status aktif dan tidak ada booking aktif, cek apakah ada booking selesai yang belum dirating
            if ($canBook && !$booking_aktif) {
                $unratedBooking = $bookingModel->getUnratedFinishedBookingForUser((int)$account['id_account']);
                if ($unratedBooking) {
                    // Lock booking baru sampai user memberi rating
                    $canBook = false;
                }
            }
        }

        $rooms     = $roomModel->getAllActive();
        $joinError = $_GET['join_error'] ?? null;

        $this->view('user/home', [
            'currentUser'    => $account,
            'booking_aktif'  => $booking_aktif,
            'rooms'          => $rooms,
            'join_error'     => $joinError,
            'canBook'        => $canBook,
            'unratedBooking' => $unratedBooking, // opsional, kalau mau ditampilkan di view
        ]);
    }

    public function riwayat()
    {
        Auth::requireRole(['mahasiswa', 'dosen', 'tendik']);

        $account = $this->getCurrentAccount();
        if (!$account) {
            $this->view('user/riwayat', [
                'history'    => [],
                'page'       => 1,
                'totalPages' => 1,
                'totalData'  => 0,
                'startData'  => 0,
                'endData'    => 0,
            ]);
            return;
        }

        $idUser = (int)$account['id_account'];

        $page    = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        $perPage = 10;
        $offset  = ($page - 1) * $perPage;

        $bookingModel = new BookingUser();

        $history    = $bookingModel->getHistoryByUser($idUser, $perPage, $offset);
        $totalData  = $bookingModel->countHistoryByUser($idUser);
        $totalPages = $totalData > 0 ? (int)ceil($totalData / $perPage) : 1;

        $startData = $totalData > 0 ? $offset + 1 : 0;
        $endData   = min($offset + $perPage, $totalData);

        $this->view('user/riwayat', [
            'history'    => $history,
            'page'       => $page,
            'totalPages' => $totalPages,
            'totalData'  => $totalData,
            'startData'  => $startData,
            'endData'    => $endData,
        ]);
    }

    public function booking()
    {
        Auth::requireRole(['mahasiswa', 'dosen', 'tendik']);

        $idRuangan    = $_GET['id_ruangan'] ?? $_GET['id'] ?? null;
        $idBooking    = $_GET['id_booking'] ?? null;
        $selectedDate = $_GET['tanggal'] ?? date('Y-m-d');

        if (!$idRuangan || !ctype_digit((string)$idRuangan)) {
            $this->redirect('index.php?controller=userBooking&action=home');
            return;
        }

        $roomModel    = new Room();
        $bookingModel = new BookingUser();

        $room = $roomModel->findById((int)$idRuangan);
        if (!$room) {
            http_response_code(404);
            echo "Ruangan tidak ditemukan";
            return;
        }

        $booking       = null;
        $members       = [];
        $isExpired     = false;
        $disabledSlots = $bookingModel->getDisabledSlotsForRoomDate((int)$idRuangan, $selectedDate);

        if ($idBooking && ctype_digit((string)$idBooking)) {
            $booking = $bookingModel->findWithRoom((int)$idBooking);
            if ($booking) {
                $members = $bookingModel->getMembers((int)$idBooking);

                $now       = date('Y-m-d H:i:s');
                $isExpired = (
                    (int)$booking['submitted'] === 0 &&
                    !empty($booking['group_expire_at']) &&
                    $now > $booking['group_expire_at']
                );
            }
        }

        $this->view('user/booking', [
            'room'          => $room,
            'booking'       => $booking,
            'members'       => $members,
            'isExpired'     => $isExpired,
            'disabledSlots' => $disabledSlots,
        ]);
    }

    public function createGroup()
    {
        Auth::requireRole(['mahasiswa', 'dosen', 'tendik']);

        $user      = Auth::user();
        $idUser    = $user['id_account'] ?? null;
        $idRoom    = $this->input('id_ruangan');
        $tanggal   = $this->input('tanggal');
        $jamMulai  = $this->input('jam_mulai');
        $durasi    = (int)$this->input('durasi');
        $keperluan = $this->input('keperluan');

        if (!$idUser || !Auth::isActive()) {
            $this->redirectWithMessage(
                'index.php?controller=userBooking&action=home',
                'Akun Anda belum aktif atau sedang disuspend. Anda belum dapat melakukan booking.',
                'error'
            );
            return;
        }

        $bookingModel = new BookingUser();
        $roomModel    = new Room();

        $today = date('Y-m-d');

        // Validasi tanggal
        if (
            !$tanggal ||
            !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal) ||
            $tanggal < $today
        ) {
            $this->redirectWithMessage(
                'index.php?controller=userBooking&action=home',
                'Tanggal booking tidak valid.',
                'error'
            );
            return;
        }

        // Validasi durasi
        if ($durasi < 1 || $durasi > 3) {
            $this->redirectWithMessage(
                'index.php?controller=userBooking&action=home',
                'Durasi booking tidak valid (maksimal 3 jam).',
                'error'
            );
            return;
        }

        // Cek booking aktif
        $activeBooking = $bookingModel->getActiveBookingForUser((int)$idUser);
        if ($activeBooking) {
            $this->redirectWithMessage(
                'index.php?controller=userBooking&action=home',
                'Anda masih memiliki peminjaman aktif. Selesaikan peminjaman tersebut sebelum membuat booking baru.',
                'error'
            );
            return;
        }

        // ✅ Cek booking selesai yang belum dirating
        $unrated = $bookingModel->getUnratedFinishedBookingForUser((int)$idUser);
        if ($unrated) {
            $this->redirectWithMessage(
                'index.php?controller=userBooking&action=riwayat',
                'Anda memiliki peminjaman yang sudah selesai namun belum diberi rating. ' .
                    'Silakan beri rating terlebih dahulu sebelum membuat booking baru.',
                'error'
            );
            return;
        }

        $room = null;
        if ($idRoom && ctype_digit((string)$idRoom)) {
            $room = $roomModel->findById((int)$idRoom);
        }

        if (!$idRoom || !$room || !$tanggal || !$jamMulai || !$durasi) {
            $disabledSlots = $idRoom && ctype_digit((string)$idRoom)
                ? $bookingModel->getDisabledSlotsForRoomDate((int)$idRoom, $tanggal ?: $today)
                : [];

            $this->view('user/booking', [
                'room'          => $room,
                'booking'       => null,
                'members'       => [],
                'isExpired'     => false,
                'disabledSlots' => $disabledSlots,
                'error'         => 'Tanggal, jam mulai, dan durasi wajib dipilih.',
            ]);
            return;
        }

        $start = $tanggal . ' ' . $jamMulai . ':00';
        $end   = date('Y-m-d H:i:s', strtotime($start . " + {$durasi} hour"));

        // Cek bentrok dengan booking lain
        if ($bookingModel->isBentrok((int)$idRoom, $start, $end)) {
            $disabledSlots = $bookingModel->getDisabledSlotsForRoomDate((int)$idRoom, $tanggal);

            $this->view('user/booking', [
                'room'          => $room,
                'booking'       => null,
                'members'       => [],
                'isExpired'     => false,
                'disabledSlots' => $disabledSlots,
                'error'         => 'Jam yang dipilih sudah dibooking, silakan pilih jam lain.',
            ]);
            return;
        }

        $kodeKelompok = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 6);
        $bookingCode  = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);

        $expireAt = date('Y-m-d H:i:s', strtotime('+5 minutes'));

        $idBooking = $bookingModel->createGroupBooking([
            'id_pj'           => (int)$idUser,
            'id_ruangan'      => (int)$idRoom,
            'booking_code'    => $bookingCode,
            'kode_kelompok'   => $kodeKelompok,
            'start_time'      => $start,
            'end_time'        => $end,
            'jumlah_anggota'  => 0,
            'tanggal'         => $tanggal,
            'keperluan'       => $keperluan,
            'group_expire_at' => $expireAt,
            'submitted'       => 0,
        ]);

        $bookingModel->addMember($idBooking, (int)$idUser);

        $this->redirect(
            "index.php?controller=userBooking&action=booking&id_ruangan={$idRoom}&id_booking={$idBooking}"
        );
    }


    public function groupDetail()
    {
        Auth::requireRole(['mahasiswa', 'dosen', 'tendik']);

        $idBooking = $_GET['id'] ?? null;
        if (!$idBooking || !ctype_digit((string)$idBooking)) {
            echo "Booking tidak ditemukan.";
            return;
        }

        $bookingModel = new BookingUser();
        $dataBooking  = $bookingModel->findWithRoom((int)$idBooking);
        if (!$dataBooking) {
            echo "Booking tidak ditemukan.";
            return;
        }

        $members = $bookingModel->getMembers((int)$idBooking);

        $now       = date('Y-m-d H:i:s');
        $isExpired = (
            (int)$dataBooking['submitted'] === 0 &&
            $dataBooking['group_expire_at'] !== null &&
            $now > $dataBooking['group_expire_at']
        );

        $this->view('user/group', [
            'booking'   => $dataBooking,
            'members'   => $members,
            'isExpired' => $isExpired,
        ]);
    }

    public function joinGroup()
    {
        Auth::requireRole(['mahasiswa', 'dosen', 'tendik']);

        $kode   = $this->input('kode_kelompok');
        $user   = Auth::user();
        $idUser = $user['id_account'] ?? null;

        if (!$kode) {
            $this->redirect(
                'index.php?controller=userBooking&action=home&join_error=' .
                    urlencode('Kode kelompok wajib diisi')
            );
            return;
        }

        if (!$idUser || !Auth::isActive()) {
            $this->redirectWithMessage(
                'index.php?controller=userBooking&action=home',
                'Akun Anda belum aktif atau sedang disuspend. Anda belum dapat melakukan booking.',
                'error'
            );
            return;
        }

        $bookingModel    = new BookingUser();
        $rescheduleModel = new BookingReschedule();

        // Cek apakah user sudah punya booking aktif
        $activeBooking = $bookingModel->getActiveBookingForUser((int)$idUser);
        if ($activeBooking) {
            $this->redirectWithMessage(
                'index.php?controller=userBooking&action=home',
                'Anda masih memiliki peminjaman aktif. Selesaikan peminjaman tersebut sebelum bergabung ke kelompok baru.',
                'error'
            );
            return;
        }

        // ✅ Cek booking selesai yang belum dirating
        $unrated = $bookingModel->getUnratedFinishedBookingForUser((int)$idUser);
        if ($unrated) {
            $this->redirectWithMessage(
                'index.php?controller=userBooking&action=riwayat',
                'Anda memiliki peminjaman yang sudah selesai namun belum diberi rating. ' .
                    'Silakan beri rating terlebih dahulu sebelum bergabung ke kelompok baru.',
                'error'
            );
            return;
        }

        $b = $bookingModel->findByKodeKelompok($kode);

        if (!$b) {
            $this->redirect(
                'index.php?controller=userBooking&action=home&join_error=' .
                    urlencode('Kode kelompok tidak ditemukan')
            );
            return;
        }

        $today = date('Y-m-d');
        if (!empty($b['tanggal']) && $b['tanggal'] < $today) {
            $this->redirect(
                'index.php?controller=userBooking&action=home&join_error=' .
                    urlencode('Kelompok ini sudah melewati tanggal peminjaman.')
            );
            return;
        }

        $idBooking  = (int)$b['id_bookings'];
        $lastStatus = $bookingModel->getLastStatus($idBooking);
        $now        = date('Y-m-d H:i:s');
        $activeRes  = $rescheduleModel->findActiveRescheduleForBooking($idBooking);

        if (!$activeRes && !in_array($lastStatus, ['reschedule_pending', 'reschedule_approved'], true)) {
            $draftRes = $rescheduleModel->findLatestByBooking($idBooking);
            if ($draftRes) {
                $activeRes = $draftRes;
            }
        }

        if ($activeRes && !in_array($lastStatus, ['reschedule_pending', 'reschedule_approved'], true)) {
            $idReschedule = (int)$activeRes['id_reschedule'];
            $resMembers   = $rescheduleModel->getMembers($idReschedule);
            $currentCount = count($resMembers);
            $capMax       = (int)($activeRes['kapasitas_max'] ?? 0);

            if ($capMax > 0 && $currentCount >= $capMax) {
                $this->redirect(
                    'index.php?controller=userBooking&action=home&join_error=' .
                        urlencode('Kelompok di jadwal baru sudah penuh')
                );
                return;
            }

            foreach ($resMembers as $m) {
                if ((int)$m['id_user'] === (int)$idUser) {
                    $this->redirect(
                        "index.php?controller=userReschedule&action=reschedule" .
                            "&id_booking={$idBooking}&id_reschedule={$idReschedule}"
                    );
                    return;
                }
            }

            $rescheduleModel->addMember($idReschedule, (int)$idUser);

            $this->redirect(
                "index.php?controller=userReschedule&action=reschedule" .
                    "&id_booking={$idBooking}&id_reschedule={$idReschedule}"
            );
            return;
        }

        if (in_array($lastStatus, ['reschedule_pending', 'reschedule_approved'], true)) {
            $this->redirect(
                'index.php?controller=userBooking&action=home&join_error=' .
                    urlencode('Kelompok di jadwal baru sudah tidak aktif (waktu join habis / sudah diajukan).')
            );
            return;
        }

        if ((int)$b['submitted'] === 1) {
            $this->redirect(
                'index.php?controller=userBooking&action=home&join_error=' .
                    urlencode('Kelompok sudah diajukan ke admin, tidak dapat menerima anggota baru.')
            );
            return;
        }

        if ($b['group_expire_at'] && $now > $b['group_expire_at']) {
            $this->redirect(
                'index.php?controller=userBooking&action=home&join_error=' .
                    urlencode('Kelompok sudah tidak aktif (waktu pembentukan kelompok habis).')
            );
            return;
        }

        $bookingDetail = $bookingModel->findWithRoom($idBooking);
        $capMax        = (int)$bookingDetail['kapasitas_max'];

        if ((int)$b['jumlah_anggota'] >= $capMax) {
            $this->redirect(
                'index.php?controller=userBooking&action=home&join_error=' .
                    urlencode('Kelompok sudah penuh')
            );
            return;
        }

        $members = $bookingModel->getMembers($idBooking);
        foreach ($members as $m) {
            if ((int)$m['id_user'] === (int)$idUser) {
                $this->redirect(
                    "index.php?controller=userBooking&action=booking" .
                        "&id_ruangan={$b['id_ruangan']}&id_booking={$idBooking}"
                );
                return;
            }
        }

        $bookingModel->addMember($idBooking, (int)$idUser);

        $this->redirect(
            "index.php?controller=userBooking&action=booking" .
                "&id_ruangan={$b['id_ruangan']}&id_booking={$idBooking}"
        );
    }



    public function kickMember()
    {
        Auth::requireRole(['mahasiswa', 'dosen', 'tendik']);

        $idBooking = $this->input('id_booking');
        $idUser    = $this->input('id_user');
        $currentId = Auth::id();

        if (
            !$idBooking || !ctype_digit((string)$idBooking) ||
            !$idUser || !ctype_digit((string)$idUser)
        ) {
            echo "Data tidak valid.";
            return;
        }

        $bookingModel = new BookingUser();
        $b = $bookingModel->findWithRoom((int)$idBooking);

        if (!$b || (int)$b['id_pj'] !== (int)$currentId) {
            echo "Tidak diizinkan.";
            return;
        }
        $now = date('Y-m-d H:i:s');

        if ((int)$b['submitted'] === 1 || ($b['group_expire_at'] && $now > $b['group_expire_at'])) {
            $this->redirectWithMessage(
                "index.php?controller=userBooking&action=booking&id_ruangan={$b['id_ruangan']}&id_booking={$idBooking}",
                'Kelompok sudah tidak aktif atau sudah diajukan, anggota tidak dapat diubah.',
                'error'
            );
            return;
        }

        if ((int)$idUser === (int)$b['id_pj']) {
            $this->redirect("index.php?controller=userBooking&action=booking&id_ruangan={$b['id_ruangan']}&id_booking={$idBooking}");
            return;
        }

        $bookingModel->removeMember((int)$idBooking, (int)$idUser);

        $this->redirect(
            "index.php?controller=userBooking&action=booking" .
                "&id_ruangan={$b['id_ruangan']}&id_booking={$idBooking}"
        );
    }

    public function submitBooking()
    {
        Auth::requireRole(['mahasiswa', 'dosen', 'tendik']);

        $idBooking = $this->input('id_booking');
        $currentId = Auth::id();

        if (!$idBooking || !ctype_digit((string)$idBooking)) {
            echo "ID booking tidak ditemukan.";
            return;
        }

        $bookingModel = new BookingUser();
        $b = $bookingModel->findWithRoom((int)$idBooking);

        if (!$b || (int)$b['id_pj'] !== (int)$currentId) {
            echo "Tidak diizinkan.";
            return;
        }

        $now = date('Y-m-d H:i:s');
        if ((int)$b['submitted'] === 1 || ($b['group_expire_at'] && $now > $b['group_expire_at'])) {
            echo "Kelompok sudah kadaluarsa atau sudah diajukan.";
            return;
        }

        $members     = $bookingModel->getMembers((int)$idBooking);
        $memberCount = count($members);
        if ($memberCount < (int)$b['kapasitas_min']) {
            echo "Anggota belum memenuhi kapasitas minimum.";
            return;
        }

        $bookingModel->markSubmitted((int)$idBooking);
        $bookingModel->addStatus((int)$idBooking, 'pending');

        $this->redirect("index.php?controller=userBooking&action=riwayat");
    }

    public function deleteGroup()
    {
        Auth::requireRole(['mahasiswa', 'dosen', 'tendik']);

        $idBooking = $this->input('id_booking');
        $currentId = Auth::id();

        if (!$idBooking || !ctype_digit((string)$idBooking)) {
            echo "ID booking tidak valid.";
            return;
        }

        $bookingModel = new BookingUser();
        $b = $bookingModel->findWithRoom((int)$idBooking);

        if (!$b || (int)$b['id_pj'] !== (int)$currentId) {
            echo "Tidak diizinkan.";
            return;
        }

        if ((int)$b['submitted'] === 1) {
            echo "Booking sudah diajukan, tidak dapat dihapus dari sini.";
            return;
        }

        $bookingModel->deleteBooking((int)$idBooking);

        $this->redirect("index.php?controller=userBooking&action=home");
    }

    public function cancel()
    {
        Auth::requireRole(['mahasiswa', 'dosen', 'tendik']);

        $user = Auth::user();
        if (!$user || empty($user['id_account'])) {
            $this->redirect('index.php?controller=auth&action=login');
            return;
        }

        $idUser    = (int)$user['id_account'];
        $idBooking = $_GET['id_booking'] ?? $_POST['id_booking'] ?? null;

        if (!$idBooking || !ctype_digit((string)$idBooking)) {
            $this->redirectWithMessage(
                'index.php?controller=userBooking&action=riwayat',
                'ID booking tidak valid.',
                'error'
            );
            return;
        }

        $bookingModel = new BookingUser();
        $result       = $bookingModel->cancelBooking((int)$idBooking, $idUser);

        if (empty($result['success'])) {
            $this->redirectWithMessage(
                'index.php?controller=userBooking&action=riwayat',
                $result['message'] ?? 'Gagal membatalkan booking.',
                'error'
            );
            return;
        }

        $this->redirectWithMessage(
            'index.php?controller=userBooking&action=riwayat',
            'Booking berhasil dibatalkan.',
            'success'
        );
    }

    public function editProfil()
    {
        Auth::requireRole(['mahasiswa', 'dosen', 'tendik']);

        $userSession = Auth::user();
        if (!$userSession || empty($userSession['id_account'])) {
            $this->redirect('index.php?controller=auth&action=login');
            return;
        }

        $accountModel = new Account();
        $currentUser  = $accountModel->findById((int)$userSession['id_account']);

        if (!$currentUser) {
            $this->redirectWithMessage(
                'index.php?controller=userBooking&action=home',
                'Data akun tidak ditemukan.',
                'error'
            );
            return;
        }

        $this->view('user/editprofil', [
            'currentUser' => $currentUser,
        ]);
    }
}
