<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil | Kubooking</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="bg-gray-100 min-h-screen font-sans">

    <?php
    // navbar utama user
    $navbarPath = __DIR__ . '/../layout/navbar.php';
    if (file_exists($navbarPath)) {
        include $navbarPath;
    }

    // data user dari controller
    $user = $currentUser ?? [];

    // Foto profil (opsional)
    $fotoProfil = !empty($user['foto'])
        ? $user['foto']
        : 'img/default-user.png';

    $role        = $user['role'] ?? 'mahasiswa';
    $statusAktif = $user['status_aktif'] ?? 'aktif';
    ?>
    <?php
    $flashPath = __DIR__ . '/../layout/flash.php';
    if (file_exists($flashPath)) {
        include $flashPath;
    }
    ?>
    <div class="max-w-6xl mx-auto mt-8 px-4 pb-10 space-y-6">

        <!-- Tombol kembali -->
        <div>
            <a href="index.php?controller=userBooking&action=home"
                class="inline-flex items-center text-gray-700 hover:text-gray-900">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- KIRI: Kartu profil + info -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-md p-6 space-y-6">

                <!-- Header profil -->
                <div class="flex items-center space-x-4">
                    <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-200 border border-gray-300 shadow">
                        <img
                            src="<?= htmlspecialchars($fotoProfil) ?>"
                            alt="Foto profil <?= htmlspecialchars($user['nama'] ?? 'Pengguna') ?>"
                            class="w-full h-full object-cover">
                    </div>

                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">
                            <?= htmlspecialchars($user['nama'] ?? 'Pengguna') ?>
                        </h2>
                        <p class="text-sm text-gray-600">
                            <?= htmlspecialchars($user['email'] ?? 'Email belum diisi') ?>
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            Terdaftar sebagai <?= htmlspecialchars(ucfirst($role)); ?>,
                            status akun:
                            <?= $statusAktif === 'aktif' ? 'Aktif' : 'Nonaktif'; ?>
                        </p>
                    </div>
                </div>

                <!-- Info detail (SEMUA DARI REGISTRASI) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">

                    <!-- Nama -->
                    <div class="space-y-1">
                        <label class="text-gray-600">Nama</label>
                        <input type="text" readonly
                            value="<?= htmlspecialchars($user['nama'] ?? '-') ?>"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-gray-50 focus:outline-none">
                    </div>

                    <!-- Email -->
                    <div class="space-y-1">
                        <label class="text-gray-600">Email</label>
                        <input type="text" readonly
                            value="<?= htmlspecialchars($user['email'] ?? '-') ?>"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-gray-50 focus:outline-none">
                    </div>

                    <!-- NIM / NIP -->
                    <div class="space-y-1">
                        <label class="text-gray-600">NIM / NIP</label>
                        <input type="text" readonly
                            value="<?= htmlspecialchars($user['nim_nip'] ?? '-') ?>"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-gray-50 focus:outline-none">
                    </div>

                    <!-- Jurusan -->
                    <div class="space-y-1">
                        <label class="text-gray-600">Jurusan</label>
                        <input type="text" readonly
                            value="<?= htmlspecialchars($user['jurusan'] ?? '-') ?>"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-gray-50 focus:outline-none">
                    </div>

                    <!-- Prodi (kalau mahasiswa) ATAU Unit (kalau tendik) -->
                    <?php if ($role === 'mahasiswa'): ?>
                        <div class="space-y-1">
                            <label class="text-gray-600">Program Studi</label>
                            <input type="text" readonly
                                value="<?= htmlspecialchars($user['prodi'] ?? '-') ?>"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-gray-50 focus:outline-none">
                        </div>
                    <?php elseif ($role === 'tendik'): ?>
                        <div class="space-y-1">
                            <label class="text-gray-600">Unit / Jurusan</label>
                            <input type="text" readonly
                                value="<?= htmlspecialchars($user['unit_jurusan'] ?? '-') ?>"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-gray-50 focus:outline-none">
                        </div>
                    <?php endif; ?>

                </div>
            </div>

            <!-- KANAN: Pengaturan akun -->
            <div class="space-y-4">

                <div class="bg-white rounded-2xl shadow-md p-6 space-y-3">
                    <h3 class="font-semibold text-gray-900 mb-1">Pengaturan Akun</h3>
                    <p class="text-xs text-gray-500 mb-3">
                        Atur informasi dasar akunmu.
                    </p>

                    <div class="space-y-2 text-sm">
                        <a href="index.php?controller=user&action=editProfil"
                            class="flex items-center justify-between px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 transition">
                            <span>Edit Profil</span>
                            <span>&gt;</span>
                        </a>

                        <a href="index.php?controller=userBooking&action=riwayat"
                            class="flex items-center justify-between px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 transition">
                            <span>Riwayat Peminjaman</span>
                            <span>&gt;</span>
                        </a>

                        <a href="index.php?controller=auth&action=gantiPassword"
                            class="flex items-center justify-between px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 transition">
                            <span>Ganti Password</span>
                            <span>&gt;</span>
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-md p-6 space-y-3">
                    <h3 class="font-semibold text-gray-900">Keluar Akun</h3>
                    <p class="text-xs text-gray-500">
                        Keluar dari akun Kubooking pada perangkat ini
                    </p>

                    <a href="index.php?controller=auth&action=logout"
                        class="block mt-3 text-center border border-red-400 text-red-500 rounded-full py-2 text-sm font-semibold hover:bg-red-50 transition">
                        Logout
                    </a>
                </div>

            </div>
        </div>

    </div>

    <?php
    $footerPath = __DIR__ . '/../layout/footer.php';
    if (file_exists($footerPath)) {
        require $footerPath;
    }
    ?>
</body>

</html>