<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\AdminAccount;

class SuperAdminController extends Controller
{
    /**
     * Halaman kelola admin
     * URL: index.php?controller=superAdmin&action=kelolaAdmin
     */
    public function kelolaAdmin()
    {
        Auth::requireRole(['super_admin']);

        // Ambil filter & search dari query string
        $statusFilter = $_GET['status'] ?? 'all';
        $search       = trim($_GET['q'] ?? '');
        $page         = isset($_GET['page']) ? (int)$_GET['page'] : 1;

        if (!in_array($statusFilter, ['all', 'aktif', 'nonaktif'], true)) {
            $statusFilter = 'all';
        }
        if ($page < 1) {
            $page = 1;
        }

        $perPage = 20;

        $model  = new AdminAccount();
        $result = $model->getAdminList($page, $perPage, $statusFilter, $search);

        $admins     = $result['data']        ?? [];
        $totalPages = $result['total_pages'] ?? 1;

        $this->view('admin/kelolaadmin', [
            'admins'       => $admins,
            'currentPage'  => $page,
            'totalPages'   => $totalPages,
            'statusFilter' => $statusFilter,
            'search'       => $search,
        ]);
    }

    /**
     * Halaman form tambah admin (GET) + proses simpan (POST)
     * URL:
     *   GET  index.php?controller=superAdmin&action=createAdmin
     *   POST index.php?controller=superAdmin&action=createAdmin
     */
    public function createAdmin()
    {
        Auth::requireRole(['super_admin']);

        // Kalau POST → proses simpan
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            return $this->handleStoreAdmin();
        }

        // Kalau GET → tampilkan form kosong
        $this->view('admin/tambahadmin', [
            'old'   => [],
            'error' => null,
        ]);
    }

    /**
     * Alias untuk kompatibilitas:
     * POST index.php?controller=superAdmin&action=storeAdmin
     * akan diproses sama seperti createAdmin (POST)
     */
    public function storeAdmin()
    {
        Auth::requireRole(['super_admin']);
        return $this->handleStoreAdmin();
    }

    /**
     * Logic utama simpan admin baru (dipakai oleh createAdmin/storeAdmin)
     */
    private function handleStoreAdmin()
    {
        $nama         = trim($this->input('nama'));
        $email        = trim($this->input('email'));
        $nim_nip      = trim($this->input('nim_nip'));
        $role         = $this->input('role') ?? 'admin';
        $status_aktif = $this->input('status_aktif') ?? 'aktif';
        $password     = $this->input('password');
        $password2    = $this->input('password2');

        $old = [
            'nama'         => $nama,
            'email'        => $email,
            'nim_nip'      => $nim_nip,
            'role'         => $role,
            'status_aktif' => $status_aktif,
        ];

        // Validasi basic
        if (!$nama || !$email || !$nim_nip || !$password || !$password2) {
            $this->view('admin/tambahadmin', [
                'old'   => $old,
                'error' => 'Nama, Email, NIM/NIP, dan kedua field password wajib diisi.',
            ]);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->view('admin/tambahadmin', [
                'old'   => $old,
                'error' => 'Format email tidak valid.',
            ]);
            return;
        }

        if ($password !== $password2) {
            $this->view('admin/tambahadmin', [
                'old'   => $old,
                'error' => 'Konfirmasi password tidak sama.',
            ]);
            return;
        }

        // Kebijakan password: minimal 8, ada huruf & angka
        if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d).{8,}$/', $password)) {
            $this->view('admin/tambahadmin', [
                'old'   => $old,
                'error' => 'Password minimal 8 karakter dan harus mengandung huruf dan angka.',
            ]);
            return;
        }

        if (!in_array($role, ['admin', 'super_admin'], true)) {
            $role = 'admin';
        }

        if (!in_array($status_aktif, ['aktif', 'nonaktif'], true)) {
            $status_aktif = 'aktif';
        }

        $model = new AdminAccount();

        // Cek duplikasi email / NIM/NIP di admin
        if ($model->existsByNimOrEmail($nim_nip, $email, null)) {
            $this->view('admin/tambahadmin', [
                'old'   => $old,
                'error' => 'NIM/NIP atau Email sudah terdaftar sebagai admin/super admin.',
            ]);
            return;
        }

        $hashed = password_hash($password, PASSWORD_BCRYPT);

        $model->createAdmin([
            'nama'         => $nama,
            'nim_nip'      => $nim_nip,
            'email'        => $email,
            'password'     => $hashed,
            'role'         => $role,
            'status_aktif' => $status_aktif,
        ]);

        $this->redirectWithMessage(
            'index.php?controller=superAdmin&action=kelolaAdmin',
            'Admin baru berhasil ditambahkan.'
        );
    }

    /**
     * Halaman edit admin (GET)
     * URL: index.php?controller=superAdmin&action=editAdmin&id=123
     */
    public function editAdmin()
    {
        Auth::requireRole(['super_admin']);

        $id = $_GET['id'] ?? null;
        if (!$id || !ctype_digit((string)$id)) {
            $this->redirectWithMessage(
                'index.php?controller=superAdmin&action=kelolaAdmin',
                'ID admin tidak valid.',
                'error'
            );
            return;
        }

        $model = new AdminAccount();
        $admin = $model->findAdminById((int)$id);

        if (!$admin) {
            $this->redirectWithMessage(
                'index.php?controller=superAdmin&action=kelolaAdmin',
                'Data admin tidak ditemukan.',
                'error'
            );
            return;
        }

        $this->view('admin/editadmin', [
            'admin' => $admin,
            'error' => null,
        ]);
    }

    /**
     * Proses update data admin (POST) + optional ubah password
     * URL: index.php?controller=superAdmin&action=updateAdmin
     */
    public function updateAdmin()
    {
        Auth::requireRole(['super_admin']);

        // hidden input di form: name="id_admin"
        $id = (int)($this->input('id_admin') ?? $this->input('id_account') ?? 0);

        $nama         = trim($this->input('nama'));
        $email        = trim($this->input('email'));
        $nim_nip      = trim($this->input('nim_nip'));
        $role         = $this->input('role') ?? 'admin';
        $status_aktif = $this->input('status_aktif') ?? 'aktif';

        $newPass  = $this->input('password');
        $newPass2 = $this->input('password2');

        if ($id <= 0) {
            $this->redirectWithMessage(
                'index.php?controller=superAdmin&action=kelolaAdmin',
                'ID admin tidak valid.',
                'error'
            );
            return;
        }

        $model = new AdminAccount();
        $admin = $model->findAdminById($id);

        if (!$admin) {
            $this->redirectWithMessage(
                'index.php?controller=superAdmin&action=kelolaAdmin',
                'Data admin tidak ditemukan.',
                'error'
            );
            return;
        }

        // Untuk re-render kalau ada error
        $admin['nama']         = $nama;
        $admin['email']        = $email;
        $admin['nim_nip']      = $nim_nip;
        $admin['role']         = $role;
        $admin['status_aktif'] = $status_aktif;

        // Validasi basic
        if (!$nama || !$email || !$nim_nip) {
            $this->view('admin/editadmin', [
                'admin' => $admin,
                'error' => 'Nama, Email, dan NIM/NIP wajib diisi.',
            ]);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->view('admin/editadmin', [
                'admin' => $admin,
                'error' => 'Format email tidak valid.',
            ]);
            return;
        }

        if (!in_array($role, ['admin', 'super_admin'], true)) {
            $role = 'admin';
        }

        if (!in_array($status_aktif, ['aktif', 'nonaktif'], true)) {
            $status_aktif = 'aktif';
        }

        // Cegah super admin mematikan dirinya sendiri
        $current = Auth::user();
        if (!empty($current['id_account']) && (int)$current['id_account'] === $id) {
            $role         = 'super_admin';
            $status_aktif = 'aktif';
            $admin['role']         = $role;
            $admin['status_aktif'] = $status_aktif;
        }

        // Cek duplikasi NIM/NIP atau email (kecuali dirinya sendiri)
        if ($model->existsByNimOrEmail($nim_nip, $email, $id)) {
            $this->view('admin/editadmin', [
                'admin' => $admin,
                'error' => 'NIM/NIP atau Email sudah dipakai admin/super admin lain.',
            ]);
            return;
        }

        // Update data utama
        $ok = $model->updateAdmin($id, [
            'nama'         => $nama,
            'nim_nip'      => $nim_nip,
            'email'        => $email,
            'role'         => $role,
            'status_aktif' => $status_aktif,
        ]);

        if (!$ok) {
            $this->view('admin/editadmin', [
                'admin' => $admin,
                'error' => 'Gagal menyimpan perubahan data admin.',
            ]);
            return;
        }

        // Optional: kalau password diisi, validasi & update
        if ($newPass || $newPass2) {
            if ($newPass !== $newPass2) {
                $this->view('admin/editadmin', [
                    'admin' => $admin,
                    'error' => 'Konfirmasi password baru tidak sama.',
                ]);
                return;
            }

            if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d).{8,}$/', $newPass)) {
                $this->view('admin/editadmin', [
                    'admin' => $admin,
                    'error' => 'Password baru minimal 8 karakter dan harus mengandung huruf dan angka.',
                ]);
                return;
            }

            $hashed = password_hash($newPass, PASSWORD_BCRYPT);
            $model->updateAdminPassword($id, $hashed);
        }

        $this->redirectWithMessage(
            'index.php?controller=superAdmin&action=kelolaAdmin',
            'Data admin berhasil diperbarui.',
            'success'
        );
    }

    /**
     * Set status aktif/nonaktif admin dari menu tiga titik di kelolaAdmin
     * URL: index.php?controller=superAdmin&action=setAdminStatus
     */
    public function setAdminStatus()
    {
        Auth::requireRole(['super_admin']);

        $id     = (int)($this->input('id_admin') ?? 0);
        $status = $this->input('status') ?? 'nonaktif';

        if ($id <= 0 || !in_array($status, ['aktif', 'nonaktif'], true)) {
            $this->redirectWithMessage(
                'index.php?controller=superAdmin&action=kelolaAdmin',
                'Data status admin tidak valid.',
                'error'
            );
            return;
        }

        $model = new AdminAccount();
        $admin = $model->findAdminById($id);

        if (!$admin) {
            $this->redirectWithMessage(
                'index.php?controller=superAdmin&action=kelolaAdmin',
                'Admin tidak ditemukan.',
                'error'
            );
            return;
        }

        // Cegah ganti status diri sendiri
        $current = Auth::user();
        if (!empty($current['id_account']) && (int)$current['id_account'] === $id) {
            $this->redirectWithMessage(
                'index.php?controller=superAdmin&action=kelolaAdmin',
                'Anda tidak dapat mengubah status akun Anda sendiri.',
                'error'
            );
            return;
        }

        $model->setAdminStatus($id, $status);

        $msg = $status === 'aktif'
            ? 'Akun admin berhasil diaktifkan.'
            : 'Akun admin berhasil dinonaktifkan.';

        $this->redirectWithMessage(
            'index.php?controller=superAdmin&action=kelolaAdmin',
            $msg,
            'success'
        );
    }

    /**
     * Hapus admin
     * URL: index.php?controller=superAdmin&action=deleteAdmin
     */
    public function deleteAdmin()
    {
        Auth::requireRole(['super_admin']);

        $id = (int)($this->input('id_admin') ?? 0);

        if ($id <= 0) {
            $this->redirectWithMessage(
                'index.php?controller=superAdmin&action=kelolaAdmin',
                'ID admin tidak valid.',
                'error'
            );
            return;
        }

        $model = new AdminAccount();
        $admin = $model->findAdminById($id);

        if (!$admin) {
            $this->redirectWithMessage(
                'index.php?controller=superAdmin&action=kelolaAdmin',
                'Data admin tidak ditemukan.',
                'error'
            );
            return;
        }

        // Cegah hapus diri sendiri
        $current = Auth::user();
        if (!empty($current['id_account']) && (int)$current['id_account'] === $id) {
            $this->redirectWithMessage(
                'index.php?controller=superAdmin&action=kelolaAdmin',
                'Anda tidak dapat menghapus akun Anda sendiri.',
                'error'
            );
            return;
        }

        $model->deleteAdmin($id);

        $this->redirectWithMessage(
            'index.php?controller=superAdmin&action=kelolaAdmin',
            'Admin berhasil dihapus.',
            'success'
        );
    }

    /**
     * Detail admin (card khusus admin)
     * URL: index.php?controller=superAdmin&action=detailAdmin&id=123
     */
    public function detailAdmin()
    {
        Auth::requireRole(['super_admin']);

        $id = $_GET['id'] ?? null;
        if (!$id || !ctype_digit((string)$id)) {
            $this->redirectWithMessage(
                'index.php?controller=superAdmin&action=kelolaAdmin',
                'ID admin tidak valid.',
                'error'
            );
            return;
        }

        $model = new AdminAccount();
        $admin = $model->findAdminById((int)$id);

        if (!$admin) {
            $this->redirectWithMessage(
                'index.php?controller=superAdmin&action=kelolaAdmin',
                'Admin tidak ditemukan.',
                'error'
            );
            return;
        }

        $this->view('admin/detailadmin', [
            'admin' => $admin,
        ]);
    }
}
