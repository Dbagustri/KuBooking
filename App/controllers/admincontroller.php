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

        $user_pending           = $registrasiModel->getPending(20);
        $verifikasi_hari_ini    = $registrasiModel->countPendingToday();
        $booking_hari_ini       = $bookingModel->countToday();
        $ruang_kosong_hari_ini  = $roomModel->countActive();
        $user_aktif             = $accountModel->countActive();
        $booking_pending        = $bookingModel->getPendingForDashboard(10);

        $this->view('admin/home', [
            'user_pending'          => $user_pending,
            'booking_pending'       => $booking_pending,
            'verifikasi_hari_ini'   => $verifikasi_hari_ini,
            'booking_hari_ini'      => $booking_hari_ini,
            'ruang_kosong_hari_ini' => $ruang_kosong_hari_ini,
            'user_aktif'            => $user_aktif,
        ]);
    }

    public function approveUser()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $id = $_POST['id_registrasi'] ?? null;
        if (!$id) {
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

        // role langsung dari field role_registrasi (sudah jelas)
        $role = $reg['role_registrasi'];

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
        ]);

        $registrasiModel->updateStatus($id, 'approved');

        $this->redirect('index.php?controller=admin&action=verifikasiUser');
    }

    public function rejectUser()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $id = $_POST['id_registrasi'] ?? null;
        if (!$id) {
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
            $nimDigits = preg_replace('/\D/', '', $reg['nim_nip']); // ambil angka saja

            // default
            $angkatan = $nowYear;
            $durasi   = 4;

            if (strlen($nimDigits) >= 4) {
                $angkatan2 = substr($nimDigits, 0, 2); // "24"
                if (ctype_digit($angkatan2)) {
                    $angkatan = 2000 + (int) $angkatan2;
                }

                $kodeDurasi = substr($nimDigits, 2, 2); // "07"
                // mapping sementara, nanti bisa kamu ganti sendiri
                if ($kodeDurasi === '07') {
                    $durasi = 3;
                } else {
                    $durasi = 4; // default 4 tahun
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

        // Dosen & tendik: aktif lama (misal 40 tahun)
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

        $page   = $_GET['page'] ?? 1;
        $filter = $_GET['filter'] ?? '';
        $search = $_GET['q'] ?? '';

        $model = new Account();
        $data  = $model->getAdminUserList($page, $filter, $search);

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

            // 'summary_prodi'      => $prodi['data'],
            // 'prodi_page'         => $prodiPage,
            // 'prodi_total_pages'  => $prodi['total_pages'],

            // 'summary_rating'     => $rating['data'],
            // 'rating_page'        => $ratingPage,
            // 'rating_total_pages' => $rating['total_pages'],
        ]);
    }
}
