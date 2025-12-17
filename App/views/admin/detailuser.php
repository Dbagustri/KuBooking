<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Detail User | Kubooking</title>
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

    <div class="flex-1 flex flex-col h-screen overflow-y-auto">
        <?php
        $flashPath = __DIR__ . '/../layout/flash.php';
        if (file_exists($flashPath)) {
            include $flashPath;
        }
        ?>

        <?php
        $navPath = __DIR__ . '/../layout/nav-admin.php';
        ?>
        <div class="m-4 mb-0">
            <?php
            if (file_exists($navPath)) {
                include_once $navPath;
            }
            ?>
        </div>

        <?php
        $user = $user ?? [];

        $idUser       = isset($user['id_account']) ? (int)$user['id_account'] : 0;
        $nama         = htmlspecialchars($user['nama'] ?? '-', ENT_QUOTES, 'UTF-8');
        $email        = htmlspecialchars($user['email'] ?? '-', ENT_QUOTES, 'UTF-8');
        $nimNip       = htmlspecialchars($user['nim_nip'] ?? '-', ENT_QUOTES, 'UTF-8');
        $role         = $user['role'] ?? 'mahasiswa';
        $statusAktif  = $user['status_aktif'] ?? 'nonaktif';
        $jurusan      = htmlspecialchars($user['jurusan'] ?? '', ENT_QUOTES, 'UTF-8');
        $prodi        = htmlspecialchars($user['prodi'] ?? '', ENT_QUOTES, 'UTF-8');
        $unitJurusan  = htmlspecialchars($user['unit_jurusan'] ?? '', ENT_QUOTES, 'UTF-8');
        $angkatan     = $user['angkatan'] ?? null;
        $durasiStudi  = $user['durasi_studi'] ?? null;
        $aktifSampai  = htmlspecialchars($user['aktif_sampai'] ?? '-', ENT_QUOTES, 'UTF-8');
        $createdAt    = htmlspecialchars($user['created_at'] ?? '-', ENT_QUOTES, 'UTF-8');
        $lastLogin    = htmlspecialchars($user['last_login'] ?? '-', ENT_QUOTES, 'UTF-8');
        $screenshot   = htmlspecialchars($user['screenshot_kubaca'] ?? '', ENT_QUOTES, 'UTF-8');

        switch ($role) {
            case 'admin':
                $roleLabel = 'Admin';
                $roleClass = 'bg-purple-100 text-purple-800';
                break;
            case 'super_admin':
                $roleLabel = 'Super Admin';
                $roleClass = 'bg-red-100 text-red-800';
                break;
            case 'dosen':
                $roleLabel = 'Dosen';
                $roleClass = 'bg-blue-100 text-blue-800';
                break;
            case 'tendik':
                $roleLabel = 'Tenaga Kependidikan';
                $roleClass = 'bg-amber-100 text-amber-800';
                break;
            case 'mahasiswa':
            default:
                $roleLabel = 'Mahasiswa';
                $roleClass = 'bg-emerald-100 text-emerald-800';
                break;
        }

        if ($statusAktif === 'aktif') {
            $statusLabel = 'Aktif';
            $statusClass = 'bg-green-100 text-green-800';
        } else {
            $statusLabel = 'Nonaktif';
            $statusClass = 'bg-gray-200 text-gray-700';
        }

        $targetStatus            = $statusAktif === 'aktif' ? 'nonaktif' : 'aktif';
        $escapedTargetStatus     = htmlspecialchars($targetStatus, ENT_QUOTES, 'UTF-8');
        $konfirmasiPesan         = $targetStatus === 'nonaktif' ? 'Nonaktifkan user ini?' : 'Aktifkan kembali user ini?';
        $escapedKonfirmasiPesan  = htmlspecialchars($konfirmasiPesan, ENT_QUOTES, 'UTF-8');
        ?>

        <div class="px-4 sm:px-6 lg:px-10 pb-10 w-full max-w-6xl mx-auto space-y-6">

            <!-- HEADER + BREADCRUMB -->
            <div class="pt-4 sm:pt-6">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-[#1e3a5f] tracking-tight">
                            Detail User
                        </h1>
                        <p class="text-sm text-gray-600 mt-1">
                            Lihat informasi lengkap akun anggota Kubooking.
                        </p>
                    </div>

                    <a href="index.php?controller=admin&action=anggota"
                        class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium 
                          border border-slate-300 text-slate-600 bg-white hover:bg-slate-50 shadow-sm">
                        Kembali
                    </a>
                </div>
            </div>

            <!-- MAIN CARD -->
            <div class="bg-white shadow-lg rounded-2xl border border-gray-100 p-6 sm:p-7 space-y-8">

                <!-- USER HEADER SECTION -->
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6">

                    <!-- Left: Identitas utama -->
                    <div class="flex gap-4">
                        <!-- Avatar bulat (inisial) -->
                        <div class="hidden sm:flex w-14 h-14 rounded-full bg-[#1e3a5f] text-white items-center justify-center text-xl font-semibold shadow-md">
                            <?php
                            $initial = mb_substr(strip_tags($nama), 0, 1, 'UTF-8');
                            echo htmlspecialchars(mb_strtoupper($initial, 'UTF-8'), ENT_QUOTES, 'UTF-8');
                            ?>
                        </div>

                        <div>
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#10223a]">
                                <?= $nama ?>
                            </h2>

                            <div class="flex flex-wrap items-center gap-2 mt-2 text-xs sm:text-[13px]">
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full font-semibold <?= $roleClass ?>">
                                    <?= htmlspecialchars($roleLabel, ENT_QUOTES, 'UTF-8') ?>
                                </span>

                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full font-semibold <?= $statusClass ?>">
                                    <span class="w-2 h-2 rounded-full <?= $statusAktif === 'aktif' ? 'bg-green-500' : 'bg-gray-400' ?>"></span>
                                    Status: <?= htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </div>

                            <div class="mt-4 space-y-1 text-sm text-gray-700">
                                <p>
                                    <span class="font-semibold text-gray-600">NIM / NIP:</span>
                                    <span class="ml-1 font-mono"><?= $nimNip ?></span>
                                </p>
                                <p>
                                    <span class="font-semibold text-gray-600">Email:</span>
                                    <span class="ml-1"><?= $email ?></span>
                                </p>
                            </div>

                            <?php if ($jurusan || $prodi || $unitJurusan): ?>
                                <div class="mt-3 text-sm text-gray-700 space-y-1">
                                    <?php if ($jurusan): ?>
                                        <p>
                                            <span class="font-semibold text-gray-600">Jurusan:</span>
                                            <span class="ml-1"><?= $jurusan ?></span>
                                        </p>
                                    <?php endif; ?>
                                    <?php if ($prodi): ?>
                                        <p>
                                            <span class="font-semibold text-gray-600">Program Studi:</span>
                                            <span class="ml-1"><?= $prodi ?></span>
                                        </p>
                                    <?php endif; ?>
                                    <?php if ($unitJurusan): ?>
                                        <p>
                                            <span class="font-semibold text-gray-600">Unit / Jurusan:</span>
                                            <span class="ml-1"><?= $unitJurusan ?></span>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Right: Screenshot Kubaca (kalau ada) -->
                    <?php if ($screenshot && $role === 'mahasiswa'): ?>
                        <div class="w-full md:w-64 md:text-right">
                            <p class="text-xs text-gray-500 mb-1 md:text-right">Screenshot Kubaca</p>
                            <a href="<?= $screenshot ?>" target="_blank" class="inline-block">
                                <img src="<?= $screenshot ?>"
                                    alt="Screenshot Kubaca"
                                    class="w-full md:w-64 h-40 object-cover rounded-xl border border-gray-200 shadow-sm hover:shadow-md hover:opacity-95 transition">
                            </a>
                            <p class="mt-1 text-[11px] text-gray-500 md:text-right">
                                Klik gambar untuk melihat ukuran penuh.
                            </p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- GRID INFORMASI DETAIL -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    <!-- Informasi Keanggotaan -->
                    <div class="rounded-xl border border-gray-100 bg-slate-50/70 p-4 sm:p-5">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold text-[#1e3a5f] uppercase tracking-wide">
                                Informasi Keanggotaan
                            </h3>
                            <span class="text-[11px] text-gray-500">
                                Data akademik / masa aktif
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500 text-[11px] mb-0.5 uppercase tracking-wide">Angkatan</p>
                                <p class="font-semibold text-gray-800">
                                    <?= $angkatan ? (int)$angkatan : '-' ?>
                                </p>
                            </div>

                            <div>
                                <p class="text-gray-500 text-[11px] mb-0.5 uppercase tracking-wide">Durasi Studi / Masa Aktif</p>
                                <p class="font-semibold text-gray-800">
                                    <?php if ($durasiStudi !== null): ?>
                                        <?= (int)$durasiStudi ?> tahun
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </p>
                            </div>

                            <div>
                                <p class="text-gray-500 text-[11px] mb-0.5 uppercase tracking-wide">Aktif Sampai</p>
                                <p class="font-semibold text-gray-800">
                                    <?= $aktifSampai ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Sistem -->
                    <div class="rounded-xl border border-gray-100 bg-slate-50/70 p-4 sm:p-5">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold text-[#1e3a5f] uppercase tracking-wide">
                                Informasi Sistem
                            </h3>
                            <span class="text-[11px] text-gray-500">
                                Metadata akun di Kubooking
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500 text-[11px] mb-0.5 uppercase tracking-wide">ID Akun</p>
                                <p class="font-mono font-semibold text-gray-800">
                                    <?= $idUser ?: '-' ?>
                                </p>
                            </div>

                            <div>
                                <p class="text-gray-500 text-[11px] mb-0.5 uppercase tracking-wide">Dibuat Pada</p>
                                <p class="font-semibold text-gray-800">
                                    <?= $createdAt ?>
                                </p>
                            </div>

                            <div>
                                <p class="text-gray-500 text-[11px] mb-0.5 uppercase tracking-wide">Login Terakhir</p>
                                <p class="font-semibold text-gray-800">
                                    <?= $lastLogin ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ACTION BUTTONS -->
                <?php if ($idUser > 0): ?>
                    <div class="pt-4 border-t border-dashed border-gray-200 mt-2">
                        <div class="flex flex-wrap gap-3">
                            <a href="index.php?controller=admin&action=editUser&id=<?= $idUser ?>"
                                class="inline-flex items-center justify-center px-5 py-2.5 rounded-full bg-blue-600 text-white text-xs sm:text-sm font-semibold shadow-sm hover:bg-blue-700">
                                Edit User
                            </a>

                            <form action="index.php?controller=admin&action=setUserStatus"
                                method="POST"
                                onsubmit="return confirm('<?= $escapedKonfirmasiPesan ?>');">
                                <input type="hidden" name="id_user" value="<?= $idUser ?>">
                                <input type="hidden" name="status" value="<?= $escapedTargetStatus ?>">

                                <?php if ($statusAktif === 'aktif'): ?>
                                    <button type="submit"
                                        class="inline-flex items-center justify-center px-5 py-2.5 rounded-full bg-red-600 text-white text-xs sm:text-sm font-semibold shadow-sm hover:bg-red-700">
                                        Nonaktifkan User
                                    </button>
                                <?php else: ?>
                                    <button type="submit"
                                        class="inline-flex items-center justify-center px-5 py-2.5 rounded-full bg-green-600 text-white text-xs sm:text-sm font-semibold shadow-sm hover:bg-green-700">
                                        Aktifkan User
                                    </button>
                                <?php endif; ?>
                            </form>

                            <form action="index.php?controller=admin&action=deleteUser"
                                method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus user ini? Tindakan ini tidak dapat dibatalkan.');">
                                <input type="hidden" name="id_user" value="<?= $idUser ?>">

                                <button type="submit"
                                    class="inline-flex items-center justify-center px-5 py-2.5 rounded-full bg-red-50 text-red-700 border border-red-200 text-xs sm:text-sm font-semibold hover:bg-red-100">
                                    Hapus User
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

        </div>
    </div>

</body>

</html>