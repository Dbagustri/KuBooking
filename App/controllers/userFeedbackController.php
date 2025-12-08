<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Feedback;
use App\Models\BookingUser;

class UserFeedbackController extends Controller
{
    public function submit()
    {
        $user = Auth::user();
        if (!$user) {
            $this->redirectWithMessage(
                'index.php?controller=auth&action=login',
                'Silakan login terlebih dahulu.',
                'error'
            );
            return;
        }

        $idUser     = (int)$user['id_account'];
        $idBooking  = (int)$this->input('id_booking');
        $rating     = (int)$this->input('rating');
        $komentar   = $this->input('komentar');

        if (!$idBooking || $rating < 1 || $rating > 5) {
            $this->redirectWithMessage(
                'index.php?controller=userBooking&action=riwayat',
                'Data feedback tidak valid.',
                'error'
            );
            return;
        }

        $bookingModel = new BookingUser();

        // Validasi: user harus anggota booking
        if (!$bookingModel->isMemberOf($idBooking, $idUser)) {
            $this->redirectWithMessage(
                'index.php?controller=userBooking&action=riwayat',
                'Anda tidak terdaftar sebagai anggota booking.',
                'error'
            );
            return;
        }

        // Cek apakah boleh memberikan rating
        if (!$bookingModel->canGiveRating($idBooking)) {
            $this->redirectWithMessage(
                'index.php?controller=userBooking&action=riwayat',
                'Booking belum selesai, tidak bisa memberi rating.',
                'error'
            );
            return;
        }

        $feedback = new Feedback();

        // Cegah double rating
        if ($feedback->hasRated($idBooking, $idUser)) {
            $this->redirectWithMessage(
                'index.php?controller=userBooking&action=riwayat',
                'Anda sudah mengisi rating.',
                'error'
            );
            return;
        }

        // Simpan
        $feedback->submit($idBooking, $idUser, $rating, $komentar);

        $this->redirectWithMessage(
            'index.php?controller=userBooking&action=riwayat',
            'Terima kasih! Rating berhasil dikirim.',
            'success'
        );
    }
}
