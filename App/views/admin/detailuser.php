<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Detail User | Kubooking</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#f2f7fc] text-gray-800 flex">

    <?php
    // SIDEBAR
    $sidebarPath = __DIR__ . '/../layout/sidebar.php';
    if (file_exists($sidebarPath)) {
        // Perbaikan: Gunakan require_once atau include_once untuk memastikan file di-load sekali
        include_once $sidebarPath;
    }
    ?>

    <div class="flex-1 flex flex-col h-screen overflow-y-auto">

        <div class="m-4">
            <?php
            $navPath = __DIR__ . '/../layout/nav-admin.php';
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

        // Badge status
        if ($statusAktif === 'aktif') {
            $statusLabel = 'Aktif';
            $statusClass = 'bg-green-100 text-green-800';
        } else {
            $statusLabel = 'Nonaktif';
            $statusClass = 'bg-gray-200 text-gray-700';
        }
        $targetStatus = $statusAktif === 'aktif' ? 'nonaktif' : 'aktif';
        $escapedTargetStatus = htmlspecialchars($targetStatus, ENT_QUOTES, 'UTF-8');
        $konfirmasiPesan = $targetStatus === 'nonaktif' ? 'Nonaktifkan user ini?' : 'Aktifkan kembali user ini?';
        $escapedKonfirmasiPesan = htmlspecialchars($konfirmasiPesan, ENT_QUOTES, 'UTF-8');
        ?>

        <div class="px-8 pb-10 space-y-6">

            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-[#1e3a5f]">Detail User</h1>

                <a href="index.php?controller=admin&action=anggota"
                    class="px-4 py-2 rounded-lg bg-gray-300 hover:bg-gray-400 text-gray-800 text-sm font-semibold">
                    Kembali
                </a>
            </div>

            <div class="bg-white shadow rounded-lg p-6 space-y-6">

                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-[#1e3a5f]">
                            <?= $nama // Sudah di-escape di atas 
                            ?>
                        </h2>

                        <div class="flex flex-wrap items-center gap-2 mt-2 text-sm">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $roleClass ?>">
                                <?= htmlspecialchars($roleLabel, ENT_QUOTES, 'UTF-8') // Escape label role 
                                ?>
                            </span>

                            <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $statusClass ?>">
                                Status: <?= htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8') // Escape label status 
                                        ?>
                            </span>
                        </div>

                        <p class="mt-3 text-sm text-gray-700">
                            <span class="font-semibold">NIM/NIP:</span>
                            <?= $nimNip // Sudah di-escape di atas 
                            ?>
                        </p>

                        <p class="mt-1 text-sm text-gray-700">
                            <span class="font-semibold">Email:</span>
                            <?= $email // Sudah di-escape di atas 
                            ?>
                        </p>

                        <?php if ($jurusan || $prodi || $unitJurusan): ?>
                            <div class="mt-3 text-sm text-gray-700 space-y-1">
                                <?php if ($jurusan): ?>
                                    <p>
                                        <span class="font-semibold">Jurusan:</span>
                                        <?= $jurusan // Sudah di-escape di atas 
                                        ?>
                                    </p>
                                <?php endif; ?>
                                <?php if ($prodi): ?>
                                    <p>
                                        <span class="font-semibold">Prodi:</span>
                                        <?= $prodi // Sudah di-escape di atas 
                                        ?>
                                    </p>
                                <?php endif; ?>
                                <?php if ($unitJurusan): ?>
                                    <p>
                                        <span class="font-semibold">Unit/Jurusan:</span>
                                        <?= $unitJurusan // Sudah di-escape di atas 
                                        ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($screenshot && $role === 'mahasiswa'): ?>
                        <div class="w-full md:w-64 md:text-right">
                            <p class="text-xs text-gray-500 mb-1 md:text-right">Screenshot Kubaca</p>
                            <a href="<?= $screenshot ?>" target="_blank">
                                <img src="<?= $screenshot ?>"
                                    alt="Screenshot Kubaca"
                                    class="w-full md:w-64 h-40 object-cover rounded-lg border shadow-sm hover:opacity-90 transition">
                            </a>
                            <p class="mt-1 text-[11px] text-gray-500 md:text-right">
                                Klik gambar untuk memperbesar.
                            </p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="pt-4 border-t">
                    <h3 class="text-lg font-semibold mb-3">Informasi Keanggotaan</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500 text-xs">Angkatan</p>
                            <p class="font-medium">
                                <?= $angkatan ? (int)$angkatan : '-' ?>
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-500 text-xs">Durasi Studi / Masa Aktif</p>
                            <p class="font-medium">
                                <?php if ($durasiStudi !== null): ?>
                                    <?= (int)$durasiStudi ?> tahun
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-500 text-xs">Aktif Sampai</p>
                            <p class="font-medium">
                                <?= $aktifSampai // Sudah di-escape di atas 
                                ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t">
                    <h3 class="text-lg font-semibold mb-3">Informasi Sistem</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500 text-xs">ID Akun</p>
                            <p class="font-mono font-medium">
                                <?= $idUser ?: '-' ?>
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-500 text-xs">Dibuat Pada</p>
                            <p class="font-medium">
                                <?= $createdAt // Sudah di-escape di atas 
                                ?>
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-500 text-xs">Login Terakhir</p>
                            <p class="font-medium">
                                <?= $lastLogin // Sudah di-escape di atas 
                                ?>
                            </p>
                        </div>
                    </div>
                </div>

                <?php if ($idUser > 0): ?>
                    <div class="pt-6 border-t flex flex-wrap gap-3">

                        <a href="index.php?controller=admin&action=editUser&id=<?= $idUser ?>"
                            class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-semibold">
                            Edit User
                        </a>

                        <form action="index.php?controller=admin&action=setUserStatus"
                            method="POST"
                            onsubmit="return confirm('<?= $escapedKonfirmasiPesan
                                                        ?>');">
                            <input type="hidden" name="id_user" value="<?= $idUser ?>">
                            <input type="hidden" name="status" value="<?= $escapedTargetStatus ?>">

                            <?php if ($statusAktif === 'aktif'): ?>
                                <button type="submit"
                                    class="px-5 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-semibold">
                                    Nonaktifkan User
                                </button>
                            <?php else: ?>
                                <button type="submit"
                                    class="px-5 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-semibold">
                                    Aktifkan User
                                </button>
                            <?php endif; ?>
                        </form>

                        <form action="index.php?controller=admin&action=deleteUser"
                            method="POST"
                            onsubmit="return confirm('Yakin ingin menghapus user ini? Tindakan ini tidak dapat dibatalkan.');">
                            <input type="hidden" name="id_user" value="<?= $idUser ?>">

                            <button type="submit"
                                class="px-5 py-2 bg-red-50 text-red-700 border border-red-200 rounded-lg hover:bg-red-100 text-sm font-semibold">
                                Hapus User
                            </button>
                        </form>

                    </div>
                <?php endif; ?>

            </div>

        </div>
    </div>

</body>

</html>