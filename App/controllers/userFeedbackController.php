<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Feedback;
use App\Models\BookingUser;
use App\Models\Room;

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
        $bookingModel = new BookingUser();
        $feedback = new Feedback();

        if ($feedback->hasRated($idBooking, $idUser)) {
            $this->redirectWithMessage(
                'index.php?controller=userBooking&action=riwayat',
                'Anda sudah mengisi rating.',
                'error'
            );
            return;
        }

        $feedback->submit($idBooking, $idUser, $rating, $komentar);

        $this->redirectWithMessage(
            'index.php?controller=userBooking&action=riwayat',
            'Terima kasih! Rating berhasil dikirim.',
            'success'
        );
    }

    public function adminIndex()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;

        $search       = $_GET['q']      ?? '';
        $roomFilter   = $_GET['room']   ?? 'all';
        $ratingFilter = $_GET['rating'] ?? 'all';
        $perPage      = 20;

        $feedbackModel = new Feedback();
        $roomModel     = new Room();

        // butuh method ini di Model Feedback (lihat poin 2)
        $result = $feedbackModel->getAdminList($page, $perPage, $search, $roomFilter, $ratingFilter);

        // buat dropdown ruangan (boleh pakai all active / semua ruangan)
        $rooms = $roomModel->getAllActive();

        $this->view('admin/feedback', [
            'feedbacks'     => $result['data'] ?? [],
            'current_page'  => $page,
            'total_pages'   => $result['total_pages'] ?? 1,
            'search'        => $search,
            'roomFilter'    => $roomFilter,
            'ratingFilter'  => $ratingFilter,
            'rooms'         => $rooms,
        ]);
    }
    public function delete()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $idFeedback = (int)($this->input('id_feedback') ?? 0);

        if ($idFeedback <= 0) {
            $this->redirectWithMessage(
                'index.php?controller=userFeedback&action=adminIndex',
                'ID feedback tidak valid.',
                'error'
            );
            return;
        }

        $feedbackModel = new Feedback();
        $ok = $feedbackModel->deleteById($idFeedback);

        if (!$ok) {
            $this->redirectWithMessage(
                'index.php?controller=userFeedback&action=adminIndex',
                'Gagal menghapus feedback. Silakan coba lagi.',
                'error'
            );
            return;
        }

        $this->redirectWithMessage(
            'index.php?controller=userFeedback&action=adminIndex',
            'Feedback berhasil dihapus.',
            'success'
        );
    }
}
