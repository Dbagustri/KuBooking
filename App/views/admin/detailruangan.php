<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Detail Ruangan | Kubooking</title>
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
        <?php
        $flashPath = __DIR__ . '/../layout/flash.php';
        if (file_exists($flashPath)) {
            include $flashPath;
        }
        ?>
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
        // Normalisasi data dari controller
        /** @var array $room */
        $room = $room ?? [];

        $idRuangan = (int)($room['id_ruangan'] ?? 0);
        $nama      = $room['nama_ruangan'] ?? '-';
        $kategori  = $room['kategori'] ?? '-';
        $lokasi    = $room['lokasi'] ?? '-';
        $kapMin    = $room['kapasitas_min'] ?? null;
        $kapMax    = $room['kapasitas_max'] ?? null;
        $status    = $room['status_operasional'] ?? 'nonaktif';
        $deskripsi = $room['deskripsi'] ?? ($room['keterangan'] ?? '');
        $foto      = $room['foto_ruangan'] ?? '';

        $statusLabel = $status === 'aktif' ? 'Aktif' : 'Nonaktif';
        $badgeClass  = $status === 'aktif'
            ? 'bg-green-100 text-green-800'
            : 'bg-gray-200 text-gray-700';

        /** @var array $fasilitas */
        $fasilitas = isset($fasilitas) && is_array($fasilitas) ? $fasilitas : [];

        /** @var array $schedule */
        $schedule = isset($schedule) && is_array($schedule) ? $schedule : [];

        // opsional: apakah masih punya booking aktif (kalau dikirim dari controller)
        $hasActiveBookings = isset($hasActiveBookings) ? (bool)$hasActiveBookings : false;

        // text konfirmasi toggle status
        $confirmTextStatus = $status === 'aktif'
            ? 'Yakin ingin menonaktifkan ruangan ini?'
            : 'Aktifkan kembali ruangan ini?';
        ?>

        <div class="px-8 pb-10 space-y-6">

            <!-- HEADER + BACK -->
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-[#1e3a5f]">Detail Ruangan</h1>

                <a href="index.php?controller=admin&action=ruangan"
                    class="px-4 py-2 rounded-lg bg-gray-300 hover:bg-gray-400 text-gray-800 text-sm font-semibold">
                    Kembali
                </a>
            </div>

            <!-- CARD INFO UTAMA -->
            <div class="bg-white shadow rounded-lg p-6 space-y-6">

                <!-- HEADER RUANGAN -->
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-[#1e3a5f] mb-1">
                            <?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8') ?>
                        </h2>
                        <div class="flex flex-wrap items-center gap-2 text-sm text-gray-600">
                            <span class="px-2 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold">
                                <?= htmlspecialchars($kategori, ENT_QUOTES, 'UTF-8') ?>
                            </span>

                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[11px] font-semibold <?= $badgeClass ?>">
                                <?= htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </div>

                        <?php if (!empty($lokasi) && $lokasi !== '-'): ?>
                            <p class="mt-3 text-sm text-gray-700">
                                <span class="font-semibold">Lokasi:</span>
                                <?= htmlspecialchars($lokasi, ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        <?php endif; ?>

                        <p class="mt-2 text-sm text-gray-700">
                            <span class="font-semibold">Kapasitas:</span>
                            <?php if ($kapMin !== null && $kapMax !== null): ?>
                                <?= (int)$kapMin ?> - <?= (int)$kapMax ?> orang
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </p>

                        <?php if (!empty(trim($deskripsi))): ?>
                            <div class="mt-3 text-sm text-gray-700">
                                <span class="font-semibold">Deskripsi:</span>
                                <p class="mt-1 whitespace-pre-line">
                                    <?= nl2br(htmlspecialchars(trim($deskripsi), ENT_QUOTES, 'UTF-8')) ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- FOTO PREVIEW -->
                    <?php if (!empty($foto)): ?>
                        <div class="w-full md:w-64 md:text-right">
                            <p class="text-xs text-gray-500 mb-1 md:text-right">Foto Ruangan</p>
                            <img src="<?= htmlspecialchars($foto, ENT_QUOTES, 'UTF-8') ?>"
                                alt="Foto ruangan"
                                class="w-full md:w-64 h-40 object-cover rounded-lg border shadow-sm">
                        </div>
                    <?php endif; ?>
                </div>

                <!-- FASILITAS -->
                <div class="pt-4 border-t">
                    <h3 class="text-lg font-semibold mb-3">Fasilitas</h3>

                    <?php if (!empty($fasilitas)): ?>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach ($fasilitas as $f): ?>
                                <?php
                                $namaF = $f['nama_fasilitas'] ?? '-';
                                $icon  = $f['icon'] ?? null;
                                ?>
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-gray-100 text-gray-800 text-xs font-medium">
                                    <?php if ($icon): ?>
                                        <span class="text-lg"><?= htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') ?></span>
                                    <?php endif; ?>
                                    <span><?= htmlspecialchars($namaF, ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-sm text-gray-500">
                            Belum ada fasilitas yang terdaftar untuk ruangan ini.
                        </p>
                    <?php endif; ?>
                </div>

                <!-- JADWAL RUANGAN -->
                <div class="pt-4 border-t">
                    <h3 class="text-lg font-semibold mb-3">Jadwal Operasional</h3>

                    <?php if (!empty($schedule)): ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm bg-white border rounded">
                                <thead>
                                    <tr class="bg-gray-100 text-left">
                                        <th class="px-3 py-2">Hari</th>
                                        <th class="px-3 py-2">Buka</th>
                                        <th class="px-3 py-2">Tutup</th>
                                        <th class="px-3 py-2">Istirahat Dari</th>
                                        <th class="px-3 py-2">Istirahat Sampai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($schedule as $row): ?>
                                        <tr class="border-t">
                                            <td class="px-3 py-2">
                                                <?= htmlspecialchars($row['hari'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                            </td>
                                            <td class="px-3 py-2">
                                                <?= htmlspecialchars($row['open_time'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                            </td>
                                            <td class="px-3 py-2">
                                                <?= htmlspecialchars($row['close_time'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                            </td>
                                            <td class="px-3 py-2">
                                                <?= htmlspecialchars($row['break_start'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                            </td>
                                            <td class="px-3 py-2">
                                                <?= htmlspecialchars($row['break_end'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-sm text-gray-500">
                            Jadwal operasional ruangan belum diatur.
                        </p>
                    <?php endif; ?>
                </div>

                <!-- INFO BOOKING AKTIF (OPSIONAL) -->
                <?php if ($hasActiveBookings): ?>
                    <div class="pt-4 border-t">
                        <div class="flex items-start gap-2 text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                            <span class="mt-0.5">⚠️</span>
                            <p>
                                Ruangan ini masih memiliki peminjaman aktif (hari ini atau ke depan).
                                Beberapa aksi seperti menonaktifkan ruangan mungkin dibatasi.
                            </p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- AKSI ADMIN -->
                <div class="pt-6 border-t flex flex-wrap gap-3">

                    <a href="index.php?controller=admin&action=editRoom&id=<?= $idRuangan ?>"
                        class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-semibold">
                        Edit Ruangan
                    </a>

                    <form action="index.php?controller=admin&action=setRoomStatus"
                        method="POST"
                        onsubmit="return confirm('<?= htmlspecialchars($confirmTextStatus, ENT_QUOTES, 'UTF-8') ?>');">
                        <input type="hidden" name="id_ruangan" value="<?= $idRuangan ?>">
                        <input type="hidden" name="status" value="<?= $status === 'aktif' ? 'nonaktif' : 'aktif' ?>">

                        <?php if ($status === 'aktif'): ?>
                            <button type="submit"
                                class="px-5 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-semibold">
                                Nonaktifkan Ruangan
                            </button>
                        <?php else: ?>
                            <button type="submit"
                                class="px-5 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-semibold">
                                Aktifkan Ruangan
                            </button>
                        <?php endif; ?>
                    </form>

                </div>

            </div>

        </div>
    </div>

</body>

</html>