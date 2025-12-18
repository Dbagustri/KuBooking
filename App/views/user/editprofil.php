<?php
// $currentUser dikirim dari controller
$user        = $currentUser ?? [];
$role        = $user['role'] ?? 'mahasiswa';
$statusAktif = $user['status_aktif'] ?? 'aktif';

$fotoProfil = !empty($user['foto'])
    ? $user['foto']
    : 'img/default-user.png';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil | Kubooking</title>
    <link rel="stylesheet" href="/kubooking/public/src/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="bg-gray-100 min-h-screen font-sans">

    <?php
    $navbarPath = __DIR__ . '/../layout/navbar.php';
    if (file_exists($navbarPath)) {
        include $navbarPath;
    }
    ?>
    <?php
    $flashPath = __DIR__ . '/../layout/flash.php';
    if (file_exists($flashPath)) {
        include $flashPath;
    }
    ?>
    <div class="max-w-5xl mx-auto mt-8 px-4 pb-10">

        <!-- Back -->
        <a href="index.php?controller=user&action=profil"
            class="inline-flex items-center text-gray-700 hover:text-gray-900 mb-4">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali</span>
        </a>

        <div class="bg-white rounded-2xl shadow-md px-6 py-8 md:px-10">

            <!-- Foto + Nama -->
            <div class="flex flex-col items-center mb-8">
                <div class="w-28 h-28 rounded-full overflow-hidden bg-gray-200 border border-gray-300 shadow">
                    <img src="<?= htmlspecialchars($fotoProfil) ?>"
                        alt="Foto profil <?= htmlspecialchars($user['nama'] ?? 'Pengguna') ?>"
                        class="w-full h-full object-cover">
                </div>
                <h1 class="mt-4 text-xl font-semibold text-gray-900">
                    Edit Profil
                </h1>
            </div>

            <form
                action="index.php?controller=user&action=updateProfil"
                method="POST"
                class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 text-sm">

                <!-- Nama (boleh edit) -->
                <div class="space-y-1">
                    <label class="text-gray-700">Nama</label>
                    <input
                        type="text"
                        name="nama"
                        value="<?= htmlspecialchars($user['nama'] ?? '') ?>"
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <!-- Email (boleh edit) -->
                <div class="space-y-1">
                    <label class="text-gray-700">Email</label>
                    <input
                        type="email"
                        name="email"
                        value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <!-- NIM / NIP (TIDAK boleh edit) -->
                <div class="space-y-1">
                    <label class="text-gray-700">NIM / NIP</label>
                    <input
                        type="text"
                        value="<?= htmlspecialchars($user['nim_nip'] ?? '-') ?>"
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-gray-100 text-gray-500 cursor-not-allowed"
                        disabled>
                </div>

                <!-- Jurusan (dari register, tidak boleh edit) -->
                <div class="space-y-1">
                    <label class="text-gray-700">Jurusan</label>
                    <input
                        type="text"
                        value="<?= htmlspecialchars($user['jurusan'] ?? '-') ?>"
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-gray-100 text-gray-500 cursor-not-allowed"
                        disabled>
                </div>

                <!-- Prodi / Unit (dari register, tidak boleh edit) -->
                <?php if ($role === 'mahasiswa'): ?>
                    <div class="space-y-1">
                        <label class="text-gray-700">Program Studi</label>
                        <input
                            type="text"
                            value="<?= htmlspecialchars($user['prodi'] ?? '-') ?>"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-gray-100 text-gray-500 cursor-not-allowed"
                            disabled>
                    </div>
                <?php elseif ($role === 'tendik'): ?>
                    <div class="space-y-1">
                        <label class="text-gray-700">Unit / Jurusan</label>
                        <input
                            type="text"
                            value="<?= htmlspecialchars($user['unit_jurusan'] ?? '-') ?>"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-gray-100 text-gray-500 cursor-not-allowed"
                            disabled>
                    </div>
                <?php endif; ?>

                <!-- Status akun (hanya info) -->
                <div class="space-y-1">
                    <label class="text-gray-700">Status Akun</label>
                    <input
                        type="text"
                        value="<?= $statusAktif === 'aktif' ? 'Aktif' : 'Nonaktif' ?>"
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-gray-100 text-gray-500 cursor-not-allowed"
                        disabled>
                </div>

                <!-- Tombol submit (full width di bawah) -->
                <div class="md:col-span-2 mt-4">
                    <button
                        type="submit"
                        class="w-full bg-[#1e3a5f] text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>