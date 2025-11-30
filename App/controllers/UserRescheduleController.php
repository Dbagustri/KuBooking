<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\BookingUser;
use App\Models\BookingReschedule;
use App\Models\Account;

class UserRescheduleController extends Controller
{
    private function getCurrentAccount(): ?array
    {
        $accountId = Auth::id();
        if (!$accountId) {
            return null;
        }

        $accountModel = new Account();
        $acc = $accountModel->findById($accountId);

        return $acc ?: null;
    }

    public function reschedule()
    {
        Auth::requireRole(['mahasiswa', 'dosen', 'tendik']);

        $account = $this->getCurrentAccount();
        if (!$account) {
            $this->redirect('index.php?controller=auth&action=login');
            return;
        }

        $idUser     = (int)$account['id_account'];
        $idBooking  = $_GET['id_booking'] ?? $_GET['id'] ?? null;
        $idReschedQ = $_GET['id_reschedule'] ?? null;
        $idReschedQ = ctype_digit((string)$idReschedQ) ? (int)$idReschedQ : null;

        if (!$idBooking || !ctype_digit((string)$idBooking)) {
            $this->redirectWithMessage(
                'index.php?controller=userBooking&action=riwayat',
                'ID booking tidak valid.',
                'error'
            );
            return;
        }

        $bookingModel    = new BookingUser();
        $rescheduleModel = new BookingReschedule();

        $booking = $bookingModel->findWithRoom((int)$idBooking);
        if (!$booking) {
            $this->redirectWithMessage(
                'index.php?controller=userBooking&action=riwayat',
                'Data booking tidak ditemukan.',
                'error'
            );
            return;
        }

        // Hanya PJ yang boleh reschedule
        if ((int)$booking['id_pj'] !== $idUser) {
            $this->redirectWithMessage(
                'index.php?controller=userBooking&action=riwayat',
                'Hanya Penanggung Jawab yang dapat mengajukan reschedule.',
                'error'
            );
            return;
        }

        // Tidak boleh reschedule kalau sudah lewat / sedang berjalan
        $now = date('Y-m-d H:i:s');
        if ($booking['start_time'] <= $now) {
            $this->redirectWithMessage(
                'index.php?controller=userBooking&action=riwayat',
                'Booking yang sudah berjalan / lewat tidak dapat di-reschedule.',
                'error'
            );
            return;
        }

        $today        = date('Y-m-d');
        $selectedDate = $booking['tanggal'] ?? $today;

        $reschedule = null;
        $members    = [];

        // 1) Kalau URL bawa id_reschedule → pakai itu (kalau valid)
        if ($idReschedQ) {
            $res = $rescheduleModel->findWithBooking($idReschedQ);

            if ($res && (int)$res['id_bookings'] === (int)$idBooking) {
                $reschedule   = $res;
                $selectedDate = $_GET['tanggal'] ?? ($res['new_tanggal'] ?? $selectedDate);
                $members      = $rescheduleModel->getMembers($idReschedQ);
            } else {
                $members = $bookingModel->getMembers((int)$idBooking);
            }
        } else {
            // 2) Tidak ada id_reschedule di URL → ambil draft / terakhir dari model
            $latest = $rescheduleModel->findLatestByBooking((int)$idBooking);
            if ($latest) {
                $reschedule   = $latest;
                $idReschedQ   = (int)$latest['id_reschedule'];
                $selectedDate = $_GET['tanggal'] ?? ($latest['new_tanggal'] ?? $selectedDate);
                $members      = $rescheduleModel->getMembers($idReschedQ);
            } else {
                // 3) Belum pernah reschedule → pakai anggota booking lama
                $members = $bookingModel->getMembers((int)$idBooking);
            }
        }

        // Disabled slot: abaikan booking ini sendiri
        $disabledSlots = $bookingModel->getDisabledSlotsForRoomDateExcept(
            (int)$booking['id_ruangan'],
            $selectedDate,
            (int)$idBooking
        );

        $this->view('user/reschedule', [
            'booking'       => $booking,
            'members'       => $members,
            'disabledSlots' => $disabledSlots,
            'reschedule'    => $reschedule,
            'error'         => null,
        ]);
    }

    public function submitReschedule()
    {
        Auth::requireRole(['mahasiswa', 'dosen', 'tendik']);

        $account = $this->getCurrentAccount();
        if (!$account) {
            $this->redirect('index.php?controller=auth&action=login');
            return;
        }

        if (($account['status_aktif'] ?? '') !== 'aktif') {
            $this->redirectWithMessage(
                'index.php?controller=userBooking&action=riwayat',
                'Akun Anda sedang tidak aktif. Anda tidak dapat mengajukan reschedule.',
                'error'
            );
            return;
        }

        $idBooking = (int)($this->input('id_booking') ?? 0);
        $idRuangan = (int)($this->input('id_ruangan') ?? 0);

        if (!$idBooking || !$idRuangan) {
            $this->redirectWithMessage(
                'index.php?controller=userBooking&action=riwayat',
                'Data booking tidak lengkap.',
                'error'
            );
            return;
        }

        $bookingModel    = new BookingUser();
        $rescheduleModel = new BookingReschedule();

        $booking = $bookingModel->findWithRoom($idBooking);
        if (!$booking) {
            $this->redirectWithMessage(
                'index.php?controller=userBooking&action=riwayat',
                'Data booking tidak ditemukan.',
                'error'
            );
            return;
        }

        // Pastikan user adalah PJ
        if ((int)$booking['id_pj'] !== (int)$account['id_account']) {
            $this->redirectWithMessage(
                'index.php?controller=userBooking&action=riwayat',
                'Hanya Penanggung Jawab yang dapat mengajukan reschedule.',
                'error'
            );
            return;
        }

        $now        = date('Y-m-d H:i:s');
        $lastStatus = $bookingModel->getLastStatus($idBooking);

        // Hanya boleh reschedule dari status pending / approved
        if (!in_array($lastStatus, ['pending', 'approved'], true) || $booking['start_time'] <= $now) {
            $this->redirectWithMessage(
                'index.php?controller=userBooking&action=riwayat',
                'Booking yang sudah berjalan / dengan status ini tidak dapat di-reschedule.',
                'error'
            );
            return;
        }

        // Ambil input jadwal baru
        $tanggalBaru  = $this->input('tanggal_baru');
        $jamMulaiBaru = $this->input('jam_mulai_baru');
        $durasiBaru   = (int)$this->input('durasi_baru');
        $alasan       = $this->input('alasan');

        $today = date('Y-m-d');

        // Validasi tanggal
        if (
            !$tanggalBaru ||
            !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalBaru) ||
            $tanggalBaru < $today
        ) {
            $members       = $bookingModel->getMembers($idBooking);
            $disabledSlots = $bookingModel->getDisabledSlotsForRoomDateExcept(
                $idRuangan,
                $tanggalBaru ?: $booking['tanggal'],
                $idBooking
            );

            $this->view('user/reschedule', [
                'booking'       => $booking,
                'members'       => $members,
                'disabledSlots' => $disabledSlots,
                'reschedule'    => null,
                'error'         => 'Tanggal baru tidak valid.',
            ]);
            return;
        }

        // Validasi jam & durasi
        if ($durasiBaru < 1 || $durasiBaru > 3 || empty($jamMulaiBaru)) {
            $members       = $bookingModel->getMembers($idBooking);
            $disabledSlots = $bookingModel->getDisabledSlotsForRoomDateExcept(
                $idRuangan,
                $tanggalBaru,
                $idBooking
            );

            $this->view('user/reschedule', [
                'booking'       => $booking,
                'members'       => $members,
                'disabledSlots' => $disabledSlots,
                'reschedule'    => null,
                'error'         => 'Jam mulai dan durasi baru wajib diisi (maks 3 jam).',
            ]);
            return;
        }

        $newStart = $tanggalBaru . ' ' . $jamMulaiBaru . ':00';
        $newEnd   = date('Y-m-d H:i:s', strtotime($newStart . " + {$durasiBaru} hour"));

        // Kalau reschedule ke hari ini, jam mulai tidak boleh lewat dari sekarang
        if ($tanggalBaru === $today && $newStart <= $now) {
            $members       = $bookingModel->getMembers($idBooking);
            $disabledSlots = $bookingModel->getDisabledSlotsForRoomDateExcept(
                $idRuangan,
                $tanggalBaru,
                $idBooking
            );

            $this->view('user/reschedule', [
                'booking'       => $booking,
                'members'       => $members,
                'disabledSlots' => $disabledSlots,
                'reschedule'    => null,
                'error'         => 'Jam mulai baru sudah lewat dari waktu sekarang.',
            ]);
            return;
        }

        // cek bentrok dengan booking lain (kecuali dirinya sendiri)
        if ($bookingModel->isBentrokExcept($idRuangan, $newStart, $newEnd, $idBooking)) {
            $members       = $bookingModel->getMembers($idBooking);
            $disabledSlots = $bookingModel->getDisabledSlotsForRoomDateExcept(
                $idRuangan,
                $tanggalBaru,
                $idBooking
            );

            $this->view('user/reschedule', [
                'booking'       => $booking,
                'members'       => $members,
                'disabledSlots' => $disabledSlots,
                'reschedule'    => null,
                'error'         => 'Jadwal baru bentrok dengan peminjaman lain.',
            ]);
            return;
        }

        // Cek apakah sudah ada draft reschedule
        $existing = $rescheduleModel->findLatestByBooking($idBooking);

        if ($existing) {
            // UPDATE draft lama (jadwal & alasan) + refresh window join 10 menit
            $rescheduleModel->updateRequest((int)$existing['id_reschedule'], [
                'new_start_time'        => $newStart,
                'new_end_time'          => $newEnd,
                'new_tanggal'           => $tanggalBaru,
                'alasan'                => $alasan,
                'join_reschedule_until' => date('Y-m-d H:i:s', strtotime('+10 minutes')),
            ]);
            $idReschedule = (int)$existing['id_reschedule'];
        } else {
            // BUAT draft baru + copy anggota dari booking utama
            $members   = $bookingModel->getMembers($idBooking);
            $memberIds = array_map(fn($m) => (int)$m['id_user'], $members);

            $idReschedule = $rescheduleModel->createRequest([
                'id_bookings'    => $idBooking,
                'id_ruangan'     => $idRuangan,
                'id_user'        => (int)$account['id_account'],
                'new_start_time' => $newStart,
                'new_end_time'   => $newEnd,
                'new_tanggal'    => $tanggalBaru,
                'alasan'         => $alasan,
                // join_reschedule_until default +10 menit di model
            ], $memberIds);
        }

        $this->redirectWithMessage(
            'index.php?controller=userReschedule&action=reschedule'
                . '&id_booking=' . $idBooking
                . '&id_reschedule=' . $idReschedule,
            'Jadwal baru tersimpan sebagai draft. Silakan atur anggota jadwal baru lalu klik "Ajukan Reschedule" untuk mengirim ke admin.',
            'success'
        );
    }

    public function finalizeReschedule()
    {
        Auth::requireRole(['mahasiswa', 'dosen', 'tendik']);

        $account = $this->getCurrentAccount();
        if (!$account) {
            $this->redirect('index.php?controller=auth&action=login');
            return;
        }

        $idBooking    = (int)($this->input('id_booking') ?? 0);
        $idReschedule = (int)($this->input('id_reschedule') ?? 0);

        if (!$idBooking || !$idReschedule) {
            $this->redirectWithMessage(
                'index.php?controller=userBooking&action=riwayat',
                'Data reschedule tidak lengkap.',
                'error'
            );
            return;
        }

        $bookingModel    = new BookingUser();
        $rescheduleModel = new BookingReschedule();

        $booking    = $bookingModel->findWithRoom($idBooking);
        $reschedule = $rescheduleModel->findWithBooking($idReschedule);

        if (
            !$booking
            || !$reschedule
            || (int)$reschedule['id_bookings'] !== $idBooking
        ) {
            $this->redirectWithMessage(
                'index.php?controller=userBooking&action=riwayat',
                'Data booking / reschedule tidak ditemukan.',
                'error'
            );
            return;
        }

        // hanya PJ
        if ((int)$booking['id_pj'] !== (int)$account['id_account']) {
            $this->redirectWithMessage(
                'index.php>controller=userBooking&action=riwayat',
                'Hanya PJ yang dapat mengajukan reschedule.',
                'error'
            );
            return;
        }

        // Jangan ajukan lagi kalau status booking sudah reschedule_pending/approved
        $lastStatus = $bookingModel->getLastStatus($idBooking);
        if (in_array($lastStatus, ['reschedule_pending', 'reschedule_approved'], true)) {
            $this->redirectWithMessage(
                'index.php?controller=userBooking&action=riwayat',
                'Reschedule ini sudah diajukan sebelumnya.',
                'error'
            );
            return;
        }

        // Booking belum lewat
        $now = date('Y-m-d H:i:s');
        if ($booking['start_time'] <= $now) {
            $this->redirectWithMessage(
                'index.php?controller=userBooking&action=riwayat',
                'Booking yang sudah berjalan / lewat tidak dapat di-reschedule.',
                'error'
            );
            return;
        }

        // Cek anggota minimal di draft reschedule
        $members = $rescheduleModel->getMembers($idReschedule);
        $capMin  = (int)($booking['kapasitas_min'] ?? 0);

        if (count($members) < $capMin) {
            $this->redirectWithMessage(
                'index.php?controller=userReschedule&action=reschedule'
                    . '&id_booking=' . $idBooking
                    . '&id_reschedule=' . $idReschedule,
                'Anggota jadwal baru belum memenuhi kapasitas minimum.',
                'error'
            );
            return;
        }

        // LOCK: tandai dengan join_reschedule_until = NOW() (anggota tidak boleh diubah lagi)
        $rescheduleModel->markSubmitted($idReschedule);

        // Tambah status reschedule_pending di booking utama
        $bookingModel->addStatus(
            $idBooking,
            'reschedule_pending',
            null,
            $idReschedule,
            $reschedule['alasan'] ?? null
        );

        $this->redirectWithMessage(
            'index.php?controller=userBooking&action=riwayat',
            'Reschedule berhasil diajukan. Anggota jadwal baru sudah dikunci dan menunggu persetujuan admin.',
            'success'
        );
    }

    /**
     * PJ menghapus anggota dari draft reschedule.
     * Hanya boleh kalau window join (join_reschedule_until) masih aktif.
     */
    public function removeRescheduleMember()
    {
        Auth::requireRole(['mahasiswa', 'dosen', 'tendik']);

        $account = $this->getCurrentAccount();
        if (!$account) {
            $this->redirect('index.php?controller=auth&action=login');
            return;
        }

        $idReschedule = (int)($this->input('id_reschedule') ?? 0);
        $idUser       = (int)($this->input('id_user') ?? 0);

        if (!$idReschedule || !$idUser) {
            $this->redirectWithMessage(
                'index.php?controller=userBooking&action=riwayat',
                'Data reschedule tidak valid.',
                'error'
            );
            return;
        }

        $resModel = new BookingReschedule();
        $res      = $resModel->findWithBooking($idReschedule);

        if (!$res) {
            $this->redirectWithMessage(
                'index.php?controller=userBooking&action=riwayat',
                'Data reschedule tidak ditemukan.',
                'error'
            );
            return;
        }

        // Hanya PJ yang membuat reschedule yang boleh ubah anggota
        if ((int)$res['id_user'] !== (int)$account['id_account']) {
            $this->redirectWithMessage(
                'index.php?controller=userBooking&action=riwayat',
                'Hanya PJ yang bisa mengubah anggota reschedule.',
                'error'
            );
            return;
        }

        // Tidak boleh ubah anggota kalau reschedule sudah diajukan (dikunci)
        $now = date('Y-m-d H:i:s');
        if (!empty($res['join_reschedule_until']) && $res['join_reschedule_until'] <= $now) {
            $this->redirectWithMessage(
                'index.php?controller=userReschedule&action=reschedule'
                    . '&id_booking=' . (int)$res['id_bookings']
                    . '&id_reschedule=' . (int)$res['id_reschedule'],
                'Reschedule sudah diajukan. Anggota tidak dapat diubah lagi.',
                'error'
            );
            return;
        }

        // PJ tidak boleh menghapus dirinya sendiri
        if ($idUser === (int)$account['id_account']) {
            $this->redirectWithMessage(
                'index.php?controller=userReschedule&action=reschedule'
                    . '&id_booking=' . (int)$res['id_bookings']
                    . '&id_reschedule=' . (int)$res['id_reschedule'],
                'PJ tidak boleh menghapus dirinya sendiri.',
                'error'
            );
            return;
        }

        $resModel->removeMember($idReschedule, $idUser);

        $this->redirectWithMessage(
            'index.php?controller=userReschedule&action=reschedule'
                . '&id_booking=' . (int)$res['id_bookings']
                . '&id_reschedule=' . (int)$idReschedule,
            'Anggota berhasil dihapus dari jadwal baru.',
            'success'
        );
    }
}
