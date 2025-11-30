<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Room;
use App\Models\BookingUser;
use App\Models\Account;

class UserController extends Controller
{
    /**
     * Home user → sekarang diarahkan ke UserBookingController::home
     */
    public function home()
    {
        $this->redirect('index.php?controller=userBooking&action=home');
    }

    /**
     * Detail ruangan
     */
    public function detailroom()
    {
        $id = $_GET['id'] ?? null;

        // Validasi ID
        if (!$id || !ctype_digit($id)) {
            return $this->redirect('index.php?controller=userBooking&action=home');
        }

        $roomModel = new Room();

        // Ambil data ruangan
        $room = $roomModel->findById($id);
        if (!$room) {
            return $this->redirectWithMessage(
                'index.php?controller=userBooking&action=home',
                'Ruangan tidak ditemukan.',
                'error'
            );
        }

        // Ambil fasilitas
        $fasilitas = $roomModel->getFasilitas($id);

        // Ambil booking hari ini (pakai start_time / end_time)
        $booked = $roomModel->getBookedSlotsToday($id);

        // Slot tetap (untuk tampilan saja)
        $slots = ['08.00–10.00', '10.00–12.00', '13.00–15.00'];

        // Default semua hijau
        $slotStatus = array_fill_keys($slots, 'green');

        // Ubah slot yang bentrok jadi merah
        foreach ($booked as $b) {
            $jam = date('H.i', strtotime($b['start_time'])) . "–" .
                date('H.i', strtotime($b['end_time']));
            if (isset($slotStatus[$jam])) {
                $slotStatus[$jam] = 'red';
            }
        }

        // Cek booking aktif user (disable tombol Pesan)
        $bookingModel = new BookingUser();
        $user         = Auth::user();
        $idUser       = $user['id_account'] ?? null;

        $booking_aktif = $idUser ? $bookingModel->getActiveBookingForUser($idUser) : null;

        $canBook        = Auth::isActive();
        $buttonDisabled = (!$canBook || !empty($booking_aktif));

        // Kirim data ke view
        $this->view('user/detailroom', [
            'room'           => $room,
            'fasilitas'      => $fasilitas,
            'slots'          => $slots,
            'slotStatus'     => $slotStatus,
            'buttonDisabled' => $buttonDisabled,
            'booking_aktif'  => $booking_aktif,
            'canBook'        => $canBook,
        ]);
    }


    public function profil()
    {
        // wajib login (mahasiswa/dosen/tendik)
        Auth::requireRole(['mahasiswa', 'dosen', 'tendik']);

        $session = Auth::user();
        if (!$session) {
            $this->redirect('index.php?controller=auth&action=login');
            return;
        }

        // kalau sudah punya id_account, ambil data lengkap dari tabel Account
        $currentUser = $session;
        if (!empty($session['id_account'])) {
            $accountModel = new Account();
            $row = $accountModel->findById($session['id_account']);
            if ($row) {
                $currentUser = $row;
            }
        }

        $this->view('user/profil', [
            'currentUser' => $currentUser,
        ]);
    }

    // ========= EDIT PROFIL (FORM) =========
    public function editProfil()
    {
        Auth::requireRole(['mahasiswa', 'dosen', 'tendik']);

        $session = Auth::user();
        if (!$session) {
            $this->redirect('index.php?controller=auth&action=login');
            return;
        }

        $currentUser = $session;
        if (!empty($session['id_account'])) {
            $accountModel = new Account();
            $row = $accountModel->findById($session['id_account']);
            if ($row) {
                $currentUser = $row;
            }
        }

        $this->view('user/editprofil', [
            'currentUser' => $currentUser,
        ]);
    }

    // ========= UPDATE PROFIL (PROSES POST) =========
    public function updateProfil()
    {
        Auth::requireRole(['mahasiswa', 'dosen', 'tendik']);

        $session = Auth::user();
        if (!$session || empty($session['id_account'])) {
            $this->redirect('index.php?controller=auth&action=login');
            return;
        }

        $idAccount = $session['id_account'];

        $nama  = trim($this->input('nama'));
        $email = trim($this->input('email'));

        // Validasi sederhana
        if ($nama === '' || $email === '') {
            $this->redirectWithMessage(
                'index.php?controller=user&action=editProfil',
                'Nama dan email wajib diisi.',
                'error'
            );
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirectWithMessage(
                'index.php?controller=user&action=editProfil',
                'Format email tidak valid.',
                'error'
            );
            return;
        }

        $accountModel = new Account();
        $accountModel->updateBasicProfile($idAccount, [
            'nama'  => $nama,
            'email' => $email,
        ]);

        // (opsional) refresh session user di sini kalau kamu punya helper-nya

        $this->redirectWithMessage(
            'index.php?controller=user&action=profil',
            'Profil berhasil diperbarui.'
        );
    }
}
