<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Account;
use App\Models\Registrasi;
use App\Models\Room;

class AuthController extends Controller
{
    public function landing()
    {
        if (Auth::check()) {
            $this->redirectBasedOnRole();
            return;
        }

        $roomModel = new Room();
        $rooms     = $roomModel->getFeaturedRooms(3);

        $this->view('auth/landing', [
            'rooms' => $rooms,
        ]);
    }


    public function login()
    {
        // Cek apakah user sudah login?
        if (Auth::check()) {
            // Jika sudah login, jangan kasih akses ke halaman login.
            $this->redirectBasedOnRole();
            return;
        }

        $this->view('auth/login');
    }

    public function loginProcess()
    {
        // Kalau sudah login dan sengaja hit URL ini, juga nggak perlu proses login lagi
        if (Auth::check()) {
            $this->redirectBasedOnRole();
            return;
        }

        $nim      = $this->input('npm'); // nim/nip
        $password = $this->input('password');

        if (!$nim || !$password) {
            $this->view('auth/login', ['error' => 'NIM/NIP dan password wajib diisi']);
            return;
        }

        $accountModel    = new Account();
        $registrasiModel = new Registrasi();

        // 1. Cek di tabel Account (akun yang sudah aktif/terverifikasi)
        $user = $accountModel->findByNimNip($nim);

        if ($user) {
            // Verifikasi password
            if (!password_verify($password, $user['password'])) {
                $this->view('auth/login', ['error' => 'Password salah']);
                return;
            }

            // TODO: Cek jika akun di-suspend (opsional)

            // Set Session
            Auth::loginFromAccount($user);

            // Redirect
            $this->redirectBasedOnRole();
            return;
        }

        // 2. Jika tidak ada di Account, cek di tabel Registrasi (Pending/Rejected/Baru)
        $reg = $registrasiModel->findByNimNip($nim);

        if (!$reg) {
            $this->view('auth/login', ['error' => 'NIM/NIP tidak ditemukan']);
            return;
        }

        if (!password_verify($password, $reg['password'])) {
            $this->view('auth/login', ['error' => 'Password salah']);
            return;
        }

        if ($reg['status'] === 'rejected') {
            $this->view('auth/login', ['error' => 'Registrasi Anda ditolak. Silakan hubungi admin.']);
            return;
        }

        // Login dari data registrasi (status pending / belum full verified)
        Auth::loginFromRegistrasi($reg);

        // User dari registrasi biasanya diarahkan ke dashboard user biasa
        $this->redirect('index.php?controller=userBooking&action=home');
    }

    public function register()
    {
        // User yang sudah login tidak boleh register lagi
        if (Auth::check()) {
            $this->redirectBasedOnRole();
            return;
        }

        $role = $_GET['role'] ?? 'mahasiswa';
        $this->view('auth/register', ['role' => $role]);
    }

    public function registerProcess()
    {
        // Kalau sudah login dan tetap akses URL ini, arahkan balik
        if (Auth::check()) {
            $this->redirectBasedOnRole();
            return;
        }

        $role        = $this->input('role') ?? 'mahasiswa';
        $nama        = $this->input('nama');
        $email       = $this->input('email');
        $nim_nip     = $this->input('npm');
        $password    = $this->input('password');
        $password2   = $this->input('password2');
        $jurusan     = $this->input('jurusan');
        $prodi       = $this->input('prodi');
        $unitJurusan = $this->input('unit_jurusan');

        // Validasi Input Kosong
        if (!$nama || !$email || !$nim_nip || !$password || !$password2) {
            $this->view('auth/register', [
                'error' => 'Field bertanda * wajib diisi',
                'role'  => $role,
            ]);
            return;
        }

        // Validasi Email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->view('auth/register', [
                'error' => 'Format email tidak valid',
                'role'  => $role,
            ]);
            return;
        }

        // Validasi Password Match
        // Validasi Password Match
        if ($password !== $password2) {
            $this->view('auth/register', [
                'error' => 'Konfirmasi password tidak sama',
                'role'  => $role,
            ]);
            return;
        }

        // Validasi kekuatan password: minimal 8 karakter, ada huruf & angka
        if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d).{8,}$/', $password)) {
            $this->view('auth/register', [
                'error' => 'Password minimal 8 karakter dan harus mengandung huruf dan angka.',
                'role'  => $role,
            ]);
            return;
        }


        $accountModel    = new Account();
        $registrasiModel = new Registrasi();

        // Cek Duplikasi Akun
        if (
            $accountModel->existsByNimOrEmail($nim_nip, $email) ||
            $registrasiModel->existsByNimOrEmail($nim_nip, $email)
        ) {

            $this->view('auth/register', [
                'error' => 'NIM/NIP atau Email sudah terdaftar',
                'role'  => $role,
            ]);
            return;
        }

        $screenshotPath = null;

        // Validasi Khusus Per Role & Upload File
        if ($role === 'mahasiswa') {
            if (!$jurusan || !$prodi) {
                $this->view('auth/register', [
                    'error' => 'Jurusan dan Prodi wajib diisi untuk Mahasiswa',
                    'role'  => $role,
                ]);
                return;
            }

            if (!isset($_FILES['screenshot']) || $_FILES['screenshot']['error'] !== UPLOAD_ERR_OK) {
                $this->view('auth/register', [
                    'error' => 'Screenshot Kubaca wajib diunggah untuk Mahasiswa',
                    'role'  => $role,
                ]);
                return;
            }

            $ext = strtolower(pathinfo($_FILES['screenshot']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
                $this->view('auth/register', [
                    'error' => 'Hanya file JPG/PNG yang diperbolehkan',
                    'role'  => $role,
                ]);
                return;
            }

            if ($_FILES['screenshot']['size'] > 2 * 1024 * 1024) { // 2MB
                $this->view('auth/register', [
                    'error' => 'Ukuran file maksimal 2MB',
                    'role'  => $role,
                ]);
                return;
            }

            $filename   = time() . '_' . preg_replace('/[^a-z0-9\.\-_]/i', '', $_FILES['screenshot']['name']);
            $uploadDir  = __DIR__ . '/../../public/uploads/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $targetPath = $uploadDir . $filename;

            if (!move_uploaded_file($_FILES['screenshot']['tmp_name'], $targetPath)) {
                $this->view('auth/register', [
                    'error' => 'Gagal mengunggah file',
                    'role'  => $role,
                ]);
                return;
            }

            $screenshotPath = 'uploads/' . $filename;
        } elseif ($role === 'dosen') {
            if (!$jurusan) {
                $this->view('auth/register', [
                    'error' => 'Jurusan wajib diisi untuk Dosen',
                    'role'  => $role,
                ]);
                return;
            }
            // Reset field yang tidak perlu
            $prodi          = null;
            $unitJurusan    = null;
            $screenshotPath = null;
        } elseif ($role === 'tendik') {
            if (!$unitJurusan) {
                $this->view('auth/register', [
                    'error' => 'Unit/Jurusan wajib diisi untuk Tendik',
                    'role'  => $role,
                ]);
                return;
            }
            // Reset field yang tidak perlu
            $jurusan        = null;
            $prodi          = null;
            $screenshotPath = null;
        }

        // Hashing Password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Simpan ke Database
        $newId = $registrasiModel->create([
            'nama'              => $nama,
            'email'             => $email,
            'nim_nip'           => $nim_nip,
            'jurusan'           => $jurusan,
            'prodi'             => $prodi,
            'unit_jurusan'      => $unitJurusan,
            'password'          => $hashedPassword,
            'screenshot_kubaca' => $screenshotPath,
            'status'            => 'pending',
            'role_registrasi'   => $role,
        ]);

        if ($newId) {
            // Redirect sukses dengan pesan
            $this->redirectWithMessage(
                'index.php?controller=auth&action=login',
                'Registrasi berhasil. Akun Anda menunggu verifikasi admin. Anda sudah bisa login, namun belum bisa melakukan booking.'
            );
        } else {
            $this->view('auth/register', [
                'error' => 'Gagal menyimpan data registrasi',
                'role'  => $role,
            ]);
        }
    }

    public function logout()
    {
        Auth::logout();
    }

    // Helper: Fungsi private untuk mengarahkan user sesuai role
    private function redirectBasedOnRole()
    {
        $user = Auth::user();
        $role = $user['role'] ?? 'mahasiswa';

        if (in_array($role, ['admin', 'super_admin'], true)) {
            $this->redirect('index.php?controller=admin&action=home');
        } else {
            $this->redirect('index.php?controller=userBooking&action=home');
        }
    }
    public function gantiPassword()
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            $this->redirect('index.php?controller=auth&action=login');
            return;
        }

        $user = Auth::user();
        $nim  = $user['nim_nip'] ?? null;

        if (!$nim) {
            $this->view('auth/gantipassword', [
                'error'   => 'Data akun di sesi tidak lengkap. Silakan login ulang.',
                'success' => null,
            ]);
            return;
        }

        // Kalau GET -> tampilkan form
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->view('auth/gantipassword', [
                'error'   => null,
                'success' => null,
            ]);
            return;
        }

        // Kalau POST -> proses ubah password
        $passwordLama  = $this->input('password_lama');
        $passwordBaru  = $this->input('password_baru');
        $passwordBaru2 = $this->input('password_baru2');

        if (!$passwordLama || !$passwordBaru || !$passwordBaru2) {
            $this->view('auth/gantipassword', [
                'error'   => 'Semua field wajib diisi.',
                'success' => null,
            ]);
            return;
        }

        if (strlen($passwordBaru) < 6) {
            $this->view('auth/gantipassword', [
                'error'   => 'Password baru minimal 6 karakter.',
                'success' => null,
            ]);
            return;
        }

        if ($passwordBaru !== $passwordBaru2) {
            $this->view('auth/gantipassword', [
                'error'   => 'Konfirmasi password baru tidak sama.',
                'success' => null,
            ]);
            return;
        }

        $accountModel    = new Account();
        $registrasiModel = new Registrasi();

        // Cari dulu di tabel Account, kalau gak ada baru di tabel Registrasi
        $record = $accountModel->findByNimNip($nim);
        $source = 'account';

        if (!$record) {
            $record = $registrasiModel->findByNimNip($nim);
            $source = 'registrasi';
        }

        if (!$record) {
            $this->view('auth/gantipassword', [
                'error'   => 'Akun tidak ditemukan di database. Silakan hubungi admin.',
                'success' => null,
            ]);
            return;
        }

        // Cek password lama
        if (!password_verify($passwordLama, $record['password'])) {
            $this->view('auth/gantipassword', [
                'error'   => 'Password lama yang Anda masukkan salah.',
                'success' => null,
            ]);
            return;
        }

        // Hash password baru
        $hashed = password_hash($passwordBaru, PASSWORD_BCRYPT);

        // Update ke tabel yang sesuai
        if ($source === 'account') {
            $ok = $accountModel->updatePasswordByNimNip($nim, $hashed);
        } else {
            $ok = $registrasiModel->updatePasswordByNimNip($nim, $hashed);
        }

        if (!$ok) {
            $this->view('auth/gantipassword', [
                'error'   => 'Gagal mengubah password. Silakan coba lagi.',
                'success' => null,
            ]);
            return;
        }

        // Kalau mau, bisa pakai flash message
        $this->redirectWithMessage(
            'index.php?controller=userBooking&action=home',
            'Password berhasil diubah.'
        );
    }
}
