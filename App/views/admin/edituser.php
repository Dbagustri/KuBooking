<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit User | Kubooking</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#edf3fb] text-gray-800 flex min-h-screen">

    <?php
    // SIDEBAR
    $sidebarPath = __DIR__ . '/../layout/sidebar.php';
    if (file_exists($sidebarPath)) {
        include_once $sidebarPath;
    }
    ?>

    <!-- KONTEN UTAMA -->
    <div class="flex-1 flex flex-col h-screen overflow-y-auto">
        <?php
        $flashPath = __DIR__ . '/../layout/flash.php';
        if (file_exists($flashPath)) {
            include $flashPath;
        }
        ?>

        <!-- NAVBAR -->
        <div class="m-4 mb-0">
            <?php
            $navPath = __DIR__ . '/../layout/nav-admin.php';
            if (file_exists($navPath)) {
                include_once $navPath;
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

        <div class="px-4 sm:px-6 lg:px-10 pb-10 w-full max-w-5xl mx-auto space-y-6">

            <!-- HEADER + BREADCRUMB -->
            <div class="pt-4 sm:pt-6">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <nav class="text-xs text-gray-500 mb-1">
                            <a href="index.php?controller=admin&action=home" class="hover:text-[#1e3a5f]">Dashboard</a>
                            <span class="mx-1">/</span>
                            <a href="index.php?controller=admin&action=anggota" class="hover:text-[#1e3a5f]">Kelola Anggota</a>
                            <span class="mx-1">/</span>
                            <span class="text-gray-700">Edit User</span>
                        </nav>
                        <h1 class="text-2xl sm:text-3xl font-bold text-[#1e3a5f] tracking-tight">
                            Edit User
                        </h1>
                        <p class="text-sm text-gray-600 mt-1">
                            Perbarui data akun anggota perpustakaan.
                        </p>
                    </div>

                    <a href="index.php?controller=admin&action=anggota"
                        class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium 
                          border border-slate-300 text-slate-600 bg-white hover:bg-slate-50 shadow-sm">
                        Kembali
                    </a>
                </div>
            </div>

            <!-- CARD FORM -->
            <div class="bg-white shadow-lg rounded-2xl border border-gray-100 p-6 sm:p-7 space-y-6">

                <!-- INFO SINGKAT USER -->
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">

                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                            Identitas Akun
                        </p>

                        <div class="space-y-1 text-sm text-gray-700">
                            <p>
                                <span class="font-semibold text-gray-600">NIM / NIP:</span>
                                <span class="ml-1 font-mono">
                                    <?= htmlspecialchars($nim_nip, ENT_QUOTES, 'UTF-8') ?: '-' ?>
                                </span>
                            </p>

                            <?php if ($angkatan): ?>
                                <p>
                                    <span class="font-semibold text-gray-600">Angkatan:</span>
                                    <span class="ml-1">
                                        <?= htmlspecialchars($angkatan, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </p>
                            <?php endif; ?>

                            <?php if ($aktifSampai): ?>
                                <p>
                                    <span class="font-semibold text-gray-600">Aktif sampai:</span>
                                    <span class="ml-1">
                                        <?= htmlspecialchars($aktifSampai, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="text-sm text-gray-700 md:text-right">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                            Informasi Akademik
                        </p>

                        <?php if ($jurusan): ?>
                            <p>
                                <span class="font-semibold text-gray-600">Jurusan:</span>
                                <span class="ml-1">
                                    <?= htmlspecialchars($jurusan, ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </p>
                        <?php endif; ?>
                        <?php if ($prodi): ?>
                            <p class="mt-1">
                                <span class="font-semibold text-gray-600">Prodi:</span>
                                <span class="ml-1">
                                    <?= htmlspecialchars($prodi, ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </p>
                        <?php endif; ?>
                        <?php if ($unitJurusan): ?>
                            <p class="mt-1">
                                <span class="font-semibold text-gray-600">Unit / Jurusan:</span>
                                <span class="ml-1">
                                    <?= htmlspecialchars($unitJurusan, ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </p>
                        <?php endif; ?>

                        <?php if (!$jurusan && !$prodi && !$unitJurusan): ?>
                            <p class="text-xs text-gray-500 mt-1">
                                Tidak ada data akademik yang tercatat.
                            </p>
                        <?php endif; ?>
                    </div>

                </div>

                <!-- FORM EDIT -->
                <form action="index.php?controller=admin&action=editUser&id=<?= $idAccount ?>"
                    method="POST"
                    class="space-y-6">

                    <!-- SECTION: DATA DASAR -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between gap-2">
                            <h2 class="text-sm font-semibold text-[#1e3a5f] uppercase tracking-wide">
                                Data Dasar
                            </h2>
                            <span class="text-[11px] text-gray-500">
                                Field bertanda <span class="text-red-500">*</span> wajib diisi
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="nama"
                                    value="<?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8') ?>"
                                    class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm px-3 py-2.5"
                                    required>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="email"
                                    name="email"
                                    value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
                                    class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm px-3 py-2.5"
                                    required>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION: DATA AKADEMIK (EDITABLE) -->
                    <div class="space-y-4 border-t border-dashed border-gray-200 pt-5">
                        <h2 class="text-sm font-semibold text-[#1e3a5f] uppercase tracking-wide">
                            Data Akademik
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide">
                                    Jurusan
                                </label>
                                <input
                                    type="text"
                                    name="jurusan"
                                    value="<?= htmlspecialchars($jurusan, ENT_QUOTES, 'UTF-8') ?>"
                                    placeholder="Contoh: TIK"
                                    class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm px-3 py-2.5">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide">
                                    Angkatan
                                </label>
                                <input
                                    type="number"
                                    name="angkatan"
                                    value="<?= htmlspecialchars($angkatan, ENT_QUOTES, 'UTF-8') ?>"
                                    min="2000"
                                    max="<?= date('Y') + 10 ?>"
                                    class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm px-3 py-2.5">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide">
                                    Aktif Sampai
                                </label>
                                <input
                                    type="date"
                                    name="aktif_sampai"
                                    value="<?= $aktifSampai ? htmlspecialchars(substr($aktifSampai, 0, 10), ENT_QUOTES, 'UTF-8') : '' ?>"
                                    class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm px-3 py-2.5">
                            </div>
                        </div>
                    </div>

                    <!-- SECTION: HAK AKSES & STATUS -->
                    <div class="space-y-4 border-t border-dashed border-gray-200 pt-5">
                        <h2 class="text-sm font-semibold text-[#1e3a5f] uppercase tracking-wide">
                            Hak Akses & Status Akun
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide">
                                    Role
                                </label>
                                <select
                                    name="role"
                                    class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm px-3 py-2.5 bg-white">
                                    <?php
                                    $roles = ['mahasiswa', 'dosen', 'tendik'];
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
                                <label class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide">
                                    Status Akun
                                </label>
                                <select
                                    name="status_aktif"
                                    class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm px-3 py-2.5 bg-white">
                                    <option value="aktif" <?= $statusAktif === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                    <option value="nonaktif" <?= $statusAktif === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs sm:text-sm text-gray-600">
                            <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                                <p class="font-semibold mb-1 text-gray-700">Catatan Role</p>
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Mengubah role dapat mengubah hak akses di Kubooking.</li>
                                    <li>Pastikan role sesuai dengan status kepegawaian / kemahasiswaan.</li>
                                </ul>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                                <p class="font-semibold mb-1 text-gray-700">Catatan Status Akun</p>
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Akun <span class="font-semibold">nonaktif</span> tidak dapat login maupun melakukan booking.</li>
                                    <li>Admin tidak dapat menonaktifkan atau menghapus akun dirinya sendiri (dicek di controller).</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- ACTION BUTTONS -->
                    <div class="pt-5 border-t border-gray-200 flex justify-end gap-3">
                        <a href="index.php?controller=admin&action=anggota"
                            class="px-4 py-2.5 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs sm:text-sm font-semibold shadow-sm">
                            Batal
                        </a>

                        <button type="submit"
                            class="px-5 py-2.5 rounded-full bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-semibold shadow-sm">
                            Simpan Perubahan
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>

</body>

</html>