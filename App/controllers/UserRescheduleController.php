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

        $idUser      = (int)$account['id_account'];
        $idBooking   = $_GET['id_booking'] ?? $_GET['id'] ?? null;
        $idReschedQ  = $_GET['id_reschedule'] ?? null;
        $idReschedQ  = ctype_digit((string)$idReschedQ) ? (int)$idReschedQ : null;

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

        // Cek status terakhir booking (boleh: pending / approved / reschedule_pending)
        $lastStatus = $bookingModel->getLastStatus((int)$idBooking);
        if (!in_array($lastStatus, ['pending', 'approved', 'reschedule_pending'], true)) {
            $this->redirectWithMessage(
                'index.php?controller=userBooking&action=riwayat',
                'Booking dengan status ini tidak dapat di-reschedule.',
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

        // default tanggal = tanggal booking lama
        $selectedDate = $_GET['tanggal'] ?? ($booking['tanggal'] ?? date('Y-m-d'));

        // disabled slot: abaikan booking ini sendiri
        $disabledSlots = $bookingModel->getDisabledSlotsForRoomDateExcept(
            (int)$booking['id_ruangan'],
            $selectedDate,
            (int)$idBooking
        );

        $reschedule = null;
        $members    = [];

        if ($idReschedQ) {
            // mode EDIT draft reschedule yang sudah ada
            $res = $rescheduleModel->findWithBooking($idReschedQ);

            if ($res && (int)$res['id_bookings'] === (int)$idBooking) {
                $reschedule  = $res;
                $selectedDate = $_GET['tanggal'] ?? ($res['new_tanggal'] ?? $selectedDate);
                $members     = $rescheduleModel->getMembers($idReschedQ);
            } else {
                // fallback: pakai anggota booking lama
                $members = $bookingModel->getMembers((int)$idBooking);
            }
        } else {
            // mode BUAT draft baru (belum ada Booking_reschedule)
            $members = $bookingModel->getMembers((int)$idBooking);
        }

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

        $idBooking = (int) ($this->input('id_booking') ?? 0);
        $idRuangan = (int) ($this->input('id_ruangan') ?? 0);

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

        // Kalau sudah ada reschedule_pending → jangan bikin request baru
        if ($lastStatus === 'reschedule_pending') {
            $this->redirectWithMessage(
                'index.php?controller=userBooking&action=riwayat',
                'Sudah ada permintaan reschedule yang masih menunggu persetujuan admin.',
                'error'
            );
            return;
        }

        // Cek status terakhir (pending / approved) DAN belum lewat waktunya
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
        $durasiBaru   = (int) $this->input('durasi_baru');
        $alasan       = $this->input('alasan');

        $today = date('Y-m-d');

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
                'error'         => 'Tanggal baru tidak valid.',
            ]);
            return;
        }

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
                'error'         => 'Jam mulai dan durasi baru wajib diisi (maks 3 jam).',
            ]);
            return;
        }

        $newStart = $tanggalBaru . ' ' . $jamMulaiBaru . ':00';
        $newEnd   = date('Y-m-d H:i:s', strtotime($newStart . " + {$durasiBaru} hour"));

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
                'error'         => 'Jadwal baru bentrok dengan peminjaman lain.',
            ]);
            return;
        }

        // Ambil anggota untuk dimasukkan ke draft reschedule
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
            // join_reschedule_until bisa dikirim di sini kalau mau custom,
            // tapi di model sudah ada default +10 menit
        ], $memberIds);

        $bookingModel->addStatus(
            $idBooking,
            'reschedule_pending',
            null,
            $idReschedule,
            $alasan
        );

        $this->redirectWithMessage(
            'index.php?controller=userReschedule&action=reschedule'
                . '&id_booking=' . $idBooking
                . '&id_reschedule=' . $idReschedule,
            'Permintaan reschedule berhasil dibuat. Anda masih dapat mengatur anggota jadwal baru sebelum admin menyetujui.',
            'success'
        );
    }

    public function removeRescheduleMember()
    {
        Auth::requireRole(['mahasiswa', 'dosen', 'tendik']);

        $account = $this->getCurrentAccount();
        if (!$account) {
            $this->redirect('index.php?controller=auth&action=login');
            return;
        }

        $idReschedule = (int) ($this->input('id_reschedule') ?? 0);
        $idUser       = (int) ($this->input('id_user') ?? 0);

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
        $bookingModel = new BookingUser();
        $lastStatus   = $bookingModel->getLastStatus((int)$res['id_bookings']);
        if ($lastStatus !== 'reschedule_pending') {
            $this->redirectWithMessage(
                'index.php?controller=userBooking&action=riwayat',
                'Anggota tidak dapat diubah karena status reschedule sudah diproses.',
                'error'
            );
            return;
        }

        if ((int)$res['id_user'] !== (int)$account['id_account']) {
            $this->redirectWithMessage(
                'index.php?controller=userBooking&action=riwayat',
                'Hanya PJ yang bisa mengubah anggota reschedule.',
                'error'
            );
            return;
        }

        if ($idUser === (int)$account['id_account']) {
            $this->redirectWithMessage(
                'index.php?controller=userReschedule&action=reschedule&id_booking=' . (int)$res['id_bookings'],
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
