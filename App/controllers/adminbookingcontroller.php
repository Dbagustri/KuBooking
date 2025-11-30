<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\BookingAdmin;
use App\Models\Room;

class AdminBookingController extends Controller
{
    /**
     * List semua booking untuk admin.
     * View: app/views/admin/kelolabooking.php
     * 
     * Rute: index.php?controller=adminBooking&action=manage
     */
    public function manage()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $bookingModel = new BookingAdmin();

        $page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;

        $limit  = 50;
        $offset = ($page - 1) * $limit;

        // getAllForAdmin: semua booking (pending/approved/rejected/dll)
        $bookings = $bookingModel->getAllForAdmin($limit, $offset);

        $this->view('admin/kelolabooking', [
            'bookings'    => $bookings,
            'currentPage' => $page,
        ]);
    }

    /**
     * Detail booking (booking + room + pj + anggota).
     * View: app/views/admin/booking-detail.php
     * 
     * Rute: index.php?controller=adminBooking&action=detail&id=123
     */
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

        $this->view('admin/booking-detail', [
            'booking' => $booking,
        ]);
    }

    /**
     * CREATE INTERNAL booking
     * GET  → tampilkan form
     * POST → simpan ke DB lewat BookingAdmin::createInternalBooking()
     * 
     * Rute: index.php?controller=adminBooking&action=createInternal
     */
    public function createInternal()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $roomModel    = new Room();
        $rooms        = $roomModel->getAllActive();
        $bookingModel = new BookingAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idPj          = Auth::id(); // untuk sekarang PJ = admin yang login
            $idRuangan     = $this->input('id_ruangan');
            $tanggal       = $this->input('tanggal');
            $jamMulai      = $this->input('jam_mulai');
            $durasi        = (int)$this->input('durasi');
            $jumlahAnggota = (int)$this->input('jumlah_anggota');
            $keperluan     = $this->input('keperluan');

            // validasi sederhana
            if (!$idRuangan || !$tanggal || !$jamMulai || $durasi <= 0) {
                return $this->redirectWithMessage(
                    'index.php?controller=adminBooking&action=createInternal',
                    'Semua field wajib diisi dengan benar.',
                    'error'
                );
            }

            $data = [
                'id_pj'           => (int)$idPj,
                'id_ruangan'      => (int)$idRuangan,
                'tanggal'         => $tanggal,
                'jam_mulai'       => $jamMulai,
                'durasi'          => $durasi,
                'jumlah_anggota'  => $jumlahAnggota > 0 ? $jumlahAnggota : 1,
                'keperluan'       => $keperluan,
            ];

            $idBooking = $bookingModel->createInternalBooking($data);

            if (!$idBooking) {
                return $this->redirectWithMessage(
                    'index.php?controller=adminBooking&action=createInternal',
                    'Gagal membuat booking. Cek kembali jadwal (kemungkinan bentrok).',
                    'error'
                );
            }

            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=manage',
                'Booking internal berhasil dibuat.'
            );
        }

        // GET → tampilkan form
        $this->view('admin/booking-form-internal', [
            'rooms' => $rooms,
        ]);
    }

    /**
     * CREATE EKSTERNAL booking
     * GET  → tampilkan form
     * POST → simpan ke DB lewat BookingAdmin::createExternalBooking()
     * 
     * Rute: index.php?controller=adminBooking&action=createExternal
     */
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
            $durasi        = (int)$this->input('durasi');
            $jumlahAnggota = (int)$this->input('jumlah_anggota');
            $keperluan     = $this->input('keperluan');

            $guestName     = $this->input('guest_name');
            $guestEmail    = $this->input('guest_email');
            $guestPhone    = $this->input('guest_phone');
            $asalInstansi  = $this->input('asal_instansi');
            $suratIzin     = $this->input('surat_izin'); // path file / nama file

            if (!$idRuangan || !$tanggal || !$jamMulai || $durasi <= 0 || !$guestName) {
                return $this->redirectWithMessage(
                    'index.php?controller=adminBooking&action=createExternal',
                    'Field wajib (ruangan, tanggal, jam, durasi, nama peminjam) harus diisi.',
                    'error'
                );
            }

            $data = [
                'id_ruangan'      => (int)$idRuangan,
                'tanggal'         => $tanggal,
                'jam_mulai'       => $jamMulai,
                'durasi'          => $durasi,
                'jumlah_anggota'  => $jumlahAnggota > 0 ? $jumlahAnggota : 1,
                'keperluan'       => $keperluan,
                'guest_name'      => $guestName,
                'guest_email'     => $guestEmail,
                'guest_phone'     => $guestPhone,
                'asal_instansi'   => $asalInstansi,
                'surat_izin'      => $suratIzin,
            ];

            $idBooking = $bookingModel->createExternalBooking($data);

            if (!$idBooking) {
                return $this->redirectWithMessage(
                    'index.php?controller=adminBooking&action=createExternal',
                    'Gagal membuat booking eksternal. Cek kembali jadwal (kemungkinan bentrok).',
                    'error'
                );
            }

            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=manage',
                'Booking eksternal berhasil dibuat.'
            );
        }

        // GET → tampilkan form
        $this->view('admin/booking-form-external', [
            'rooms' => $rooms,
        ]);
    }

    /**
     * EDIT booking (internal / eksternal)
     * GET  → tampilkan form edit
     * POST → update data booking via BookingAdmin::updateAdminBooking()
     * 
     * Rute: index.php?controller=adminBooking&action=edit&id=123
     */
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

        $rooms = $roomModel->getAllActive();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idRuangan     = $this->input('id_ruangan');
            $tanggal       = $this->input('tanggal');
            $jamMulai      = $this->input('jam_mulai');
            $durasi        = (int)$this->input('durasi');
            $jumlahAnggota = (int)$this->input('jumlah_anggota');
            $keperluan     = $this->input('keperluan');

            $dataUpdate = [
                'id_booking'      => (int)$id,
                'id_ruangan'      => $idRuangan ?: $booking['id_ruangan'],
                'tanggal'         => $tanggal ?: $booking['tanggal'],
                'jam_mulai'       => $jamMulai ?: date('H:i', strtotime($booking['start_time'])),
                'durasi'          => $durasi > 0 ? $durasi : 1,
                'jumlah_anggota'  => $jumlahAnggota > 0 ? $jumlahAnggota : (int)$booking['jumlah_anggota'],
                'keperluan'       => $keperluan !== null ? $keperluan : $booking['keperluan'],
            ];

            // Kalau booking eksternal, ikut update data guest
            if ((int)$booking['is_external'] === 1) {
                $dataUpdate['guest_name']     = $this->input('guest_name')     ?: $booking['guest_name'];
                $dataUpdate['guest_email']    = $this->input('guest_email')    ?: $booking['guest_email'];
                $dataUpdate['guest_phone']    = $this->input('guest_phone')    ?: $booking['guest_phone'];
                $dataUpdate['asal_instansi']  = $this->input('asal_instansi')  ?: $booking['asal_instansi'];
                $dataUpdate['surat_izin']     = $this->input('surat_izin')     ?: $booking['surat_izin'];
            }

            $ok = $bookingModel->updateAdminBooking($dataUpdate);

            if (!$ok) {
                return $this->redirectWithMessage(
                    "index.php?controller=adminBooking&action=edit&id={$id}",
                    'Gagal mengupdate booking (kemungkinan jadwal bentrok).',
                    'error'
                );
            }

            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=manage',
                'Booking berhasil diperbarui.'
            );
        }

        // GET → tampilkan form edit
        $this->view('admin/booking-edit', [
            'booking' => $booking,
            'rooms'   => $rooms,
        ]);
    }

    /**
     * DELETE booking
     * Rute: POST index.php?controller=adminBooking&action=delete
     * Param: id_booking (POST)
     */
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

    /**
     * APPROVE booking (status → approved)
     * Rute: POST index.php?controller=adminBooking&action=approve
     */
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

        $bookingModel->addStatus((int)$idBooking, 'approved');

        return $this->redirectWithMessage(
            'index.php?controller=adminBooking&action=manage',
            'Booking berhasil disetujui.'
        );
    }

    /**
     * REJECT booking (status → rejected)
     * Rute: POST index.php?controller=adminBooking&action=reject
     */
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

        $bookingModel->addStatus((int)$idBooking, 'rejected', $alasan ?: null);

        return $this->redirectWithMessage(
            'index.php?controller=adminBooking&action=manage',
            'Booking berhasil ditolak.',
            'error'
        );
    }

    /**
     * MULAI booking (Hari H – checkin)
     * - Hanya bisa jika status terakhir 'approved'
     * - Set checkin_time = NOW()
     * - Auto-cancel 10 menit hanya berlaku untuk yang belum punya checkin_time
     * 
     * Rute: POST index.php?controller=adminBooking&action=start
     */
    public function start()
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

        // Status terakhir harus APPROVED
        $lastStatus = $bookingModel->getLastStatus($idBooking);
        if ($lastStatus !== 'approved') {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=manage',
                'Booking hanya dapat dimulai jika sudah disetujui (APPROVED).',
                'error'
            );
        }

        // Ambil booking untuk cek checkin_time & start_time
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
                'index.php?controller=adminBooking&action=manage',
                'Booking ini sudah pernah dimulai.',
                'error'
            );
        }

        // Optional: Jangan mulai sebelum waktu mulai
        $now        = date('Y-m-d H:i:s');
        $start_time = $booking['start_time'];

        if ($now < $start_time) {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=manage',
                'Tidak dapat mulai sebelum waktu peminjaman.',
                'error'
            );
        }

        // Set checkin_time lewat model
        $bookingModel->setCheckinTime($idBooking);

        return $this->redirectWithMessage(
            'index.php?controller=adminBooking&action=manage',
            'Booking telah dimulai. Kunci boleh diserahkan.'
        );
    }

    /**
     * Tandai booking SELESAI (status → selesai)
     * - Biasanya dipanggil setelah kunci dikembalikan
     * - Optional: hanya boleh selesai jika sudah pernah mulai (checkin_time tidak null)
     * 
     * Rute: POST index.php?controller=adminBooking&action=complete
     */
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

        // Ambil data booking
        $booking = $bookingModel->findWithRoom($idBooking);
        if (!$booking) {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=manage',
                'Data booking tidak ditemukan.',
                'error'
            );
        }

        // Optional: pastikan sudah pernah mulai
        if (empty($booking['checkin_time'])) {
            return $this->redirectWithMessage(
                'index.php?controller=adminBooking&action=manage',
                'Booking belum pernah dimulai, tidak dapat ditandai selesai.',
                'error'
            );
        }

        // Status terakhir boleh 'approved' atau tetap 'approved' + checkin_time ≠ NULL
        $bookingModel->addStatus($idBooking, 'selesai');

        return $this->redirectWithMessage(
            'index.php?controller=adminBooking&action=manage',
            'Booking telah ditandai selesai.'
        );
    }

    /**
     * Penutupan perpustakaan: batalkan semua booking di tanggal tertentu.
     * GET  → tampilkan form pilih tanggal
     * POST → jalankan cancel massal via BookingAdmin::cancelBookingsByDate()
     * 
     * Rute: index.php?controller=adminBooking&action=closeDate
     */
    public function closeDate()
    {
        Auth::requireRole(['admin', 'super_admin']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tanggal = $this->input('tanggal');

            // Validasi sederhana format YYYY-mm-dd
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

        // GET → tampilkan form pilih tanggal
        $this->view('admin/close-date', [
            // bisa kirim data tambahan kalau perlu
        ]);
    }
}
