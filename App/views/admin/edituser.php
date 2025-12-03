<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit User | Kubooking</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#f2f7fc] text-gray-800 flex">

    <?php
    // SIDEBAR
    $sidebarPath = __DIR__ . '/../layout/sidebar.php';
    if (file_exists($sidebarPath)) {
        include $sidebarPath;
    }
    ?>

    <!-- KONTEN UTAMA -->
    <div class="flex-1 flex flex-col h-screen overflow-y-auto">

        <!-- NAVBAR -->
        <div class="m-4">
            <?php
            $navPath = __DIR__ . '/../layout/nav-admin.php';
            if (file_exists($navPath)) {
                include $navPath;
            }
            ?>
        </div>

        <?php
        // Normalisasi data user dari controller
        $user = $user ?? [];

        $idAccount   = (int)($user['id_account'] ?? 0);
        $nama        = $user['nama'] ?? '';
        $email       = $user['email'] ?? '';
        $nim_nip     = $user['nim_nip'] ?? '';
        $jurusan     = $user['jurusan'] ?? '';
        $prodi       = $user['prodi'] ?? '';
        $unitJurusan = $user['unit_jurusan'] ?? '';
        $role        = $user['role'] ?? 'mahasiswa';
        $statusAktif = $user['status_aktif'] ?? 'aktif';
        $angkatan    = $user['angkatan'] ?? '';
        $aktifSampai = $user['aktif_sampai'] ?? '';
        ?>

        <div class="px-8 pb-10 space-y-6">

            <!-- HEADER + BACK -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-[#1e3a5f]">Edit User</h1>
                    <p class="text-sm text-gray-600 mt-1">
                        Perbarui data akun anggota perpustakaan.
                    </p>
                </div>

                <a href="index.php?controller=admin&action=anggota"
                    class="px-4 py-2 rounded-lg bg-gray-300 hover:bg-gray-400 text-gray-800 text-sm font-semibold">
                    Kembali
                </a>
            </div>

            <!-- CARD FORM -->
            <div class="bg-white shadow rounded-lg p-6 space-y-6">

                <!-- INFO SINGKAT -->
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                    <div>
                        <p class="text-sm text-gray-600">
                            <span class="font-semibold">NIM/NIP:</span>
                            <?= htmlspecialchars($nim_nip, ENT_QUOTES, 'UTF-8') ?: '-' ?>
                        </p>
                        <?php if ($angkatan): ?>
                            <p class="text-sm text-gray-600 mt-1">
                                <span class="font-semibold">Angkatan:</span>
                                <?= htmlspecialchars($angkatan, ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        <?php endif; ?>
                        <?php if ($aktifSampai): ?>
                            <p class="text-sm text-gray-600 mt-1">
                                <span class="font-semibold">Aktif sampai:</span>
                                <?= htmlspecialchars($aktifSampai, ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <div class="text-sm text-gray-600 md:text-right">
                        <?php if ($jurusan): ?>
                            <p><span class="font-semibold">Jurusan:</span>
                                <?= htmlspecialchars($jurusan, ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        <?php endif; ?>
                        <?php if ($prodi): ?>
                            <p class="mt-1"><span class="font-semibold">Prodi:</span>
                                <?= htmlspecialchars($prodi, ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        <?php endif; ?>
                        <?php if ($unitJurusan): ?>
                            <p class="mt-1"><span class="font-semibold">Unit/Jurusan:</span>
                                <?= htmlspecialchars($unitJurusan, ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- FORM EDIT -->
                <form action="index.php?controller=admin&action=editUser&id=<?= $idAccount ?>"
                    method="POST"
                    class="space-y-5">

                    <!-- ROW 1: NAMA & EMAIL -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                name="nama"
                                value="<?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8') ?>"
                                class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm px-3 py-2"
                                required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email"
                                name="email"
                                value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
                                class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm px-3 py-2"
                                required>
                        </div>
                    </div>

                    <!-- ROW 2: ROLE & STATUS -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Role
                            </label>
                            <select name="role"
                                class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm px-3 py-2">
                                <?php
                                $roles = ['mahasiswa', 'dosen', 'tendik', 'admin', 'super_admin'];
                                foreach ($roles as $r):
                                    $selected = ($role === $r) ? 'selected' : '';
                                ?>
                                    <option value="<?= htmlspecialchars($r, ENT_QUOTES, 'UTF-8') ?>" <?= $selected ?>>
                                        <?= ucfirst(str_replace('_', ' ', $r)) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Status Akun
                            </label>
                            <select name="status_aktif"
                                class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm px-3 py-2">
                                <option value="aktif" <?= $statusAktif === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                <option value="nonaktif" <?= $statusAktif === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <!-- INFO TAMBAHAN (READONLY) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                        <div>
                            <p class="font-semibold mb-1">Informasi Akademik</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Perubahan role dapat mempengaruhi hak akses.</li>
                                <li>Pastikan email aktif dan dapat dihubungi.</li>
                            </ul>
                        </div>
                        <div>
                            <p class="font-semibold mb-1">Status Akun</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Akun nonaktif tidak dapat melakukan booking.</li>
                                <li>Admin tidak dapat menonaktifkan dirinya sendiri (sudah dicek di controller).</li>
                            </ul>
                        </div>
                    </div>

                    <!-- ACTION BUTTONS -->
                    <div class="pt-4 border-t flex justify-end gap-3">
                        <a href="index.php?controller=admin&action=anggota"
                            class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-semibold">
                            Batal
                        </a>

                        <button type="submit"
                            class="px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                            Simpan Perubahan
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>

</body>

</html>