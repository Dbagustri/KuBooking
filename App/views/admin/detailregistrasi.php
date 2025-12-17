<?php
// app/views/admin/detailregistrasi.php

/**
 * Asumsi: variabel $reg sudah di-set dari controller:
 * $reg = $registrasiModel->findById($id);
 */

$reg = $reg ?? [];

// Inisialisasi & sanitasi data
$idReg        = isset($reg['id_registrasi']) ? (int)$reg['id_registrasi'] : 0;
$nama         = htmlspecialchars($reg['nama'] ?? '-', ENT_QUOTES, 'UTF-8');
$email        = htmlspecialchars($reg['email'] ?? '-', ENT_QUOTES, 'UTF-8');
$nimNip       = htmlspecialchars($reg['nim_nip'] ?? '-', ENT_QUOTES, 'UTF-8');
$roleReg      = $reg['role_registrasi'] ?? 'mahasiswa';
$status       = $reg['status'] ?? 'pending';

$jurusan      = htmlspecialchars($reg['jurusan'] ?? '', ENT_QUOTES, 'UTF-8');
$prodi        = htmlspecialchars($reg['prodi'] ?? '', ENT_QUOTES, 'UTF-8');
$unitJurusan  = htmlspecialchars($reg['unit_jurusan'] ?? '', ENT_QUOTES, 'UTF-8');

$createdAt    = htmlspecialchars($reg['created_at'] ?? '-', ENT_QUOTES, 'UTF-8');
$screenshot   = htmlspecialchars($reg['screenshot_kubaca'] ?? '', ENT_QUOTES, 'UTF-8');

// Badge role registrasi
switch ($roleReg) {
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

// Badge status registrasi
if ($status === 'approved') {
    $statusLabel = 'Approved';
    $statusClass = 'bg-green-100 text-green-800';
} elseif ($status === 'rejected') {
    $statusLabel = 'Rejected';
    $statusClass = 'bg-red-100 text-red-800';
} else { // pending
    $statusLabel = 'Pending';
    $statusClass = 'bg-yellow-100 text-yellow-800';
}

// Flag final (tidak bisa di-approve/reject lagi)
$isFinal = in_array($status, ['approved', 'rejected'], true);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Detail Registrasi User | Kubooking</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#f2f7fc] text-gray-800 flex">

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
        <div class="m-4">
            <?php
            $navPath = __DIR__ . '/../layout/nav-admin.php';
            if (file_exists($navPath)) {
                include_once $navPath;
            }
            ?>
        </div>

        <div class="px-8 pb-10 space-y-6">

            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-[#1e3a5f]">
                    Detail Registrasi User
                </h1>

                <a href="index.php?controller=admin&action=verifikasiUser"
                    class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium 
                          border border-slate-300 text-slate-600 bg-white hover:bg-slate-50 shadow-sm">
                    Kembali
                </a>
            </div>

            <div class="bg-white shadow rounded-lg p-6 space-y-6">

                <!-- HEADER USER -->
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-[#1e3a5f]">
                            <?= $nama ?>
                        </h2>

                        <div class="flex flex-wrap items-center gap-2 mt-2 text-sm">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $roleClass ?>">
                                <?= htmlspecialchars($roleLabel, ENT_QUOTES, 'UTF-8') ?>
                            </span>

                            <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $statusClass ?>">
                                Status Registrasi: <?= htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </div>

                        <p class="mt-3 text-sm text-gray-700">
                            <span class="font-semibold">NIM/NIP:</span>
                            <?= $nimNip ?>
                        </p>

                        <p class="mt-1 text-sm text-gray-700">
                            <span class="font-semibold">Email:</span>
                            <?= $email ?>
                        </p>

                        <?php if ($jurusan || $prodi || $unitJurusan): ?>
                            <div class="mt-3 text-sm text-gray-700 space-y-1">
                                <?php if ($jurusan): ?>
                                    <p>
                                        <span class="font-semibold">Jurusan:</span>
                                        <?= $jurusan ?>
                                    </p>
                                <?php endif; ?>
                                <?php if ($prodi): ?>
                                    <p>
                                        <span class="font-semibold">Prodi:</span>
                                        <?= $prodi ?>
                                    </p>
                                <?php endif; ?>
                                <?php if ($unitJurusan): ?>
                                    <p>
                                        <span class="font-semibold">Unit/Jurusan (Tendik):</span>
                                        <?= $unitJurusan ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($screenshot): ?>
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

                <!-- INFO REGISTRASI -->
                <div class="pt-4 border-t">
                    <h3 class="text-lg font-semibold mb-3">Informasi Registrasi</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500 text-xs">ID Registrasi</p>
                            <p class="font-mono font-medium">
                                <?= $idReg ?: '-' ?>
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-500 text-xs">Tanggal Registrasi</p>
                            <p class="font-medium">
                                <?= $createdAt ?>
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-500 text-xs">Status Sekarang</p>
                            <p class="font-medium">
                                <?= htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- AKSI APPROVE / REJECT -->
                <div class="pt-6 border-t flex flex-wrap gap-3">

                    <!-- APPROVE -->
                    <form action="index.php?controller=admin&action=approveUser"
                        method="POST"
                        onsubmit="return confirm('Setujui registrasi user ini?');">
                        <input type="hidden" name="id_registrasi" value="<?= $idReg ?>">
                        <button type="submit"
                            <?= $isFinal ? 'disabled' : '' ?>
                            class="px-5 py-2 rounded-lg text-sm font-semibold
                                <?= $isFinal
                                    ? 'bg-green-100 text-green-300 cursor-not-allowed'
                                    : 'bg-green-600 text-white hover:bg-green-700' ?>">
                            Approve
                        </button>
                    </form>

                    <!-- REJECT -->
                    <form action="index.php?controller=admin&action=rejectUser"
                        method="POST"
                        onsubmit="return confirm('Tolak registrasi user ini?');">
                        <input type="hidden" name="id_registrasi" value="<?= $idReg ?>">
                        <button type="submit"
                            <?= $isFinal ? 'disabled' : '' ?>
                            class="px-5 py-2 rounded-lg text-sm font-semibold border
                                <?= $isFinal
                                    ? 'bg-red-50 text-red-300 border-red-100 cursor-not-allowed'
                                    : 'bg-red-50 text-red-700 border-red-200 hover:bg-red-100' ?>">
                            Reject
                        </button>
                    </form>

                    <!-- INFO KETIKA FINAL -->
                    <?php if ($isFinal): ?>
                        <p class="text-xs text-gray-500 mt-2">
                            Registrasi ini sudah berstatus <strong><?= htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8') ?></strong>,
                            tidak dapat diubah lagi dari sini.
                        </p>
                    <?php endif; ?>

                </div>

            </div>

        </div>
    </div>

</body>

</html>