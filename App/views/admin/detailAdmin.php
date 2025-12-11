<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Detail Admin | Kubooking</title>
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
        // FLASH MESSAGE
        $flashPath = __DIR__ . '/../layout/flash.php';
        if (file_exists($flashPath)) {
            include $flashPath;
        }

        // NAVBAR
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
        // ====== DATA ADMIN (dikirim dari SuperAdminController) ======
        $admin = $admin ?? [];

        $idAdmin      = isset($admin['id_account']) ? (int)$admin['id_account'] : 0;
        $nama         = htmlspecialchars($admin['nama'] ?? '-', ENT_QUOTES, 'UTF-8');
        $email        = htmlspecialchars($admin['email'] ?? '-', ENT_QUOTES, 'UTF-8');
        $nimNip       = htmlspecialchars($admin['nim_nip'] ?? '-', ENT_QUOTES, 'UTF-8');
        $role         = $admin['role'] ?? 'admin';
        $statusAktif  = $admin['status_aktif'] ?? 'nonaktif';
        $createdAt    = htmlspecialchars($admin['created_at'] ?? '-', ENT_QUOTES, 'UTF-8');
        $lastLogin    = htmlspecialchars($admin['last_login'] ?? '-', ENT_QUOTES, 'UTF-8');

        // Label & badge role
        switch ($role) {
            case 'super_admin':
                $roleLabel = 'Super Admin';
                $roleClass = 'bg-purple-100 text-purple-800';
                break;
            case 'admin':
            default:
                $roleLabel = 'Admin';
                $roleClass = 'bg-blue-100 text-blue-800';
                break;
        }

        // Status akun
        if ($statusAktif === 'aktif') {
            $statusLabel = 'Aktif';
            $statusClass = 'bg-green-100 text-green-800';
        } else {
            $statusLabel = 'Nonaktif';
            $statusClass = 'bg-gray-200 text-gray-700';
        }

        $targetStatus           = $statusAktif === 'aktif' ? 'nonaktif' : 'aktif';
        $escapedTargetStatus    = htmlspecialchars($targetStatus, ENT_QUOTES, 'UTF-8');
        $konfirmasiPesan        = $targetStatus === 'nonaktif'
            ? 'Nonaktifkan admin ini?'
            : 'Aktifkan kembali admin ini?';
        $escapedKonfirmasiPesan = htmlspecialchars($konfirmasiPesan, ENT_QUOTES, 'UTF-8');
        ?>

        <div class="px-4 sm:px-6 lg:px-10 pb-10 w-full max-w-5xl mx-auto space-y-6">

            <!-- HEADER -->
            <div class="pt-4 sm:pt-6">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-[#1e3a5f] tracking-tight">
                            Detail Admin
                        </h1>
                        <p class="text-sm text-gray-600 mt-1">
                            Lihat informasi lengkap akun admin Kubooking.
                        </p>
                    </div>

                    <a href="index.php?controller=superAdmin&action=kelolaAdmin"
                        class="inline-flex items-center gap-1 px-4 py-2 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs sm:text-sm font-medium shadow-sm">
                        ← Kembali
                    </a>
                </div>
            </div>

            <!-- KARTU UTAMA -->
            <div class="bg-white shadow-lg rounded-2xl border border-gray-100 p-6 sm:p-7 space-y-8">

                <!-- HEADER ADMIN -->
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6">

                    <!-- Identitas -->
                    <div class="flex gap-4">
                        <!-- Avatar inisial -->
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
                        </div>
                    </div>
                </div>

                <!-- GRID INFORMASI -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Informasi Akun -->
                    <div class="rounded-xl border border-gray-100 bg-slate-50/70 p-4 sm:p-5">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold text-[#1e3a5f] uppercase tracking-wide">
                                Informasi Akun
                            </h3>
                            <span class="text-[11px] text-gray-500">
                                Data identitas admin
                            </span>
                        </div>

                        <div class="space-y-2 text-sm">
                            <div>
                                <p class="text-gray-500 text-[11px] mb-0.5 uppercase tracking-wide">Nama Lengkap</p>
                                <p class="font-semibold text-gray-800">
                                    <?= $nama ?>
                                </p>
                            </div>

                            <div>
                                <p class="text-gray-500 text-[11px] mb-0.5 uppercase tracking-wide">NIM / NIP</p>
                                <p class="font-mono font-semibold text-gray-800">
                                    <?= $nimNip ?>
                                </p>
                            </div>

                            <div>
                                <p class="text-gray-500 text-[11px] mb-0.5 uppercase tracking-wide">Email</p>
                                <p class="font-semibold text-gray-800">
                                    <?= $email ?>
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

                        <div class="grid grid-cols-1 gap-3 text-sm">
                            <div>
                                <p class="text-gray-500 text-[11px] mb-0.5 uppercase tracking-wide">ID Akun</p>
                                <p class="font-mono font-semibold text-gray-800">
                                    <?= $idAdmin ?: '-' ?>
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
                <?php if ($idAdmin > 0): ?>
                    <div class="pt-4 border-t border-dashed border-gray-200 mt-2">
                        <div class="flex flex-wrap gap-3">
                            <!-- EDIT ADMIN -->
                            <a href="index.php?controller=superAdmin&action=editAdmin&id=<?= $idAdmin ?>"
                                class="inline-flex items-center justify-center px-5 py-2.5 rounded-full bg-blue-600 text-white text-xs sm:text-sm font-semibold shadow-sm hover:bg-blue-700">
                                Edit Admin
                            </a>

                            <!-- SET STATUS -->
                            <form action="index.php?controller=superAdmin&action=setAdminStatus"
                                method="POST"
                                onsubmit="return confirm('<?= $escapedKonfirmasiPesan ?>');">
                                <input type="hidden" name="id_admin" value="<?= $idAdmin ?>">
                                <input type="hidden" name="status" value="<?= $escapedTargetStatus ?>">

                                <?php if ($statusAktif === 'aktif'): ?>
                                    <button type="submit"
                                        class="inline-flex items-center justify-center px-5 py-2.5 rounded-full bg-red-600 text-white text-xs sm:text-sm font-semibold shadow-sm hover:bg-red-700">
                                        Nonaktifkan Admin
                                    </button>
                                <?php else: ?>
                                    <button type="submit"
                                        class="inline-flex items-center justify-center px-5 py-2.5 rounded-full bg-green-600 text-white text-xs sm:text-sm font-semibold shadow-sm hover:bg-green-700">
                                        Aktifkan Admin
                                    </button>
                                <?php endif; ?>
                            </form>

                            <!-- HAPUS ADMIN -->
                            <form action="index.php?controller=superAdmin&action=deleteAdmin"
                                method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus admin ini? Tindakan ini tidak dapat dibatalkan.');">
                                <input type="hidden" name="id_admin" value="<?= $idAdmin ?>">

                                <button type="submit"
                                    class="inline-flex items-center justify-center px-5 py-2.5 rounded-full bg-red-50 text-red-700 border border-red-200 text-xs sm:text-sm font-semibold hover:bg-red-100">
                                    Hapus Admin
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