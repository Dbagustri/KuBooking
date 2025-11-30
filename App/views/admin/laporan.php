<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan | Kubooking</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#f2f7fc] text-gray-800 flex">

    <!-- SIDEBAR -->
    <?php
    $sidebarPath = __DIR__ . '/../layout/sidebar.php';
    if (file_exists($sidebarPath)) {
        include $sidebarPath;
    }

    // DATA DARI CONTROLLER
    $range   = $range ?? 'month';

    $rooms      = $summary_rooms      ?? [];
    $roomsPage  = $rooms_page         ?? 1;
    $roomsTotal = $rooms_total_pages  ?? 1;

    // Jika nanti ada:
    $prodi      = $summary_prodi      ?? [];
    $prodiPage  = $prodi_page         ?? 1;
    $prodiTotal = $prodi_total_pages  ?? 1;

    $rating     = $summary_rating     ?? [];
    $ratingPage = $rating_page        ?? 1;
    $ratingTotal = $rating_total_pages ?? 1;
    ?>

    <!-- KONTEN -->
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

        <div class="px-8 pb-12 space-y-8">
            <h1 class="text-2xl font-bold text-[#1e3a5f]">Laporan</h1>

            <!-- FILTER RANGE -->
            <div class="flex space-x-2">
                <?php
                $ranges = [
                    'week'   => '1 Minggu',
                    'month'  => '1 Bulan',
                    '3month' => '3 Bulan',
                    '6month' => '6 Bulan',
                ];
                ?>

                <?php foreach ($ranges as $key => $label): ?>
                    <a href="index.php?controller=admin&action=laporan&range=<?= $key ?>"
                        class="px-4 py-2 rounded-lg text-sm font-medium border 
                               <?= $range === $key ? 'bg-[#1e3a5f] text-white border-[#1e3a5f]' : 'bg-white text-gray-700 border-gray-300' ?>
                               hover:bg-[#163052] hover:text-white transition">
                        <?= $label ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- ====================================================== -->
            <!-- 1. LAPORAN RUANGAN TERBANYAK -->
            <!-- ====================================================== -->

            <div class="bg-white shadow rounded-xl p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-[#1e3a5f]">
                        Ruangan Dipinjam Terbanyak
                    </h2>

                    <button class="px-4 py-1.5 bg-[#1e3a5f] text-white rounded-lg text-sm hover:bg-[#163052]">
                        Export
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                        <thead>
                            <tr class="bg-gray-200 text-gray-800 text-sm">
                                <th class="px-3 py-2">No</th>
                                <th class="px-3 py-2">Tanggal</th>
                                <th class="px-3 py-2">Ruangan Pertama</th>
                                <th class="px-3 py-2">Ruangan Kedua</th>
                                <th class="px-3 py-2">Ruangan Ketiga</th>
                                <th class="px-3 py-2">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($rooms)): ?>
                                <?php foreach ($rooms as $i => $row): ?>
                                    <tr class="<?= $i % 2 ? 'bg-gray-50' : 'bg-white' ?> border-b">
                                        <td class="px-3 py-2 text-center"><?= $i + 1 ?></td>
                                        <td class="px-3 py-2"><?= htmlspecialchars($row['tanggal'] ?? '-') ?></td>
                                        <td class="px-3 py-2 text-center"><?= $row['ruang_1'] ?? 0 ?></td>
                                        <td class="px-3 py-2 text-center"><?= $row['ruang_2'] ?? 0 ?></td>
                                        <td class="px-3 py-2 text-center"><?= $row['ruang_3'] ?? 0 ?></td>
                                        <td class="px-3 py-2 text-center"><?= $row['total'] ?? 0 ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="px-3 py-6 text-center text-gray-500">
                                        Tidak ada data.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION -->
                <?php if ($roomsTotal > 1): ?>
                    <div class="flex justify-center space-x-1 pt-2">
                        <?php if ($roomsPage > 1): ?>
                            <a href="index.php?controller=admin&action=laporan&range=<?= $range ?>&rooms_page=<?= $roomsPage - 1 ?>"
                                class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">&lt;</a>
                        <?php else: ?>
                            <span class="px-3 py-1 bg-gray-100 text-gray-400 rounded">&lt;</span>
                        <?php endif; ?>

                        <?php for ($p = 1; $p <= $roomsTotal; $p++): ?>
                            <a href="index.php?controller=admin&action=laporan&range=<?= $range ?>&rooms_page=<?= $p ?>"
                                class="px-3 py-1 rounded <?= $p == $roomsPage ? 'bg-[#1e3a5f] text-white' : 'bg-gray-200 hover:bg-gray-300' ?>">
                                <?= $p ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($roomsPage < $roomsTotal): ?>
                            <a href="index.php?controller=admin&action=laporan&range=<?= $range ?>&rooms_page=<?= $roomsPage + 1 ?>"
                                class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">&gt;</a>
                        <?php else: ?>
                            <span class="px-3 py-1 bg-gray-100 text-gray-400 rounded">&gt;</span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ====================================================== -->
            <!-- 2. LAPORAN PRODI / JURUSAN -->
            <!-- ====================================================== -->

            <div class="bg-white shadow rounded-xl p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-[#1e3a5f]">
                        Ruangan Dipinjam Berdasarkan Prodi
                    </h2>

                    <button class="px-4 py-1.5 bg-[#1e3a5f] text-white rounded-lg text-sm hover:bg-[#163052]">
                        Export
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                        <thead>
                            <tr class="bg-gray-200 text-gray-800 text-sm">
                                <th class="px-3 py-2">No</th>
                                <th class="px-3 py-2">Tanggal</th>
                                <th class="px-3 py-2">TI</th>
                                <th class="px-3 py-2">TS</th>
                                <th class="px-3 py-2">TIF</th>
                                <th class="px-3 py-2">TAV</th>
                                <th class="px-3 py-2">AK</th>
                                <th class="px-3 py-2">TE</th>
                                <th class="px-3 py-2">Total</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (!empty($prodi)): ?>
                                <?php foreach ($prodi as $i => $row): ?>
                                    <tr class="<?= $i % 2 ? 'bg-gray-50' : 'bg-white' ?> border-b">
                                        <td class="px-3 py-2 text-center"><?= $i + 1 ?></td>
                                        <td class="px-3 py-2"><?= htmlspecialchars($row['tanggal'] ?? '-') ?></td>
                                        <td class="px-3 py-2 text-center"><?= $row['TI'] ?? 0 ?></td>
                                        <td class="px-3 py-2 text-center"><?= $row['TS'] ?? 0 ?></td>
                                        <td class="px-3 py-2 text-center"><?= $row['TIF'] ?? 0 ?></td>
                                        <td class="px-3 py-2 text-center"><?= $row['TAV'] ?? 0 ?></td>
                                        <td class="px-3 py-2 text-center"><?= $row['AK'] ?? 0 ?></td>
                                        <td class="px-3 py-2 text-center"><?= $row['TE'] ?? 0 ?></td>
                                        <td class="px-3 py-2 text-center"><?= $row['total'] ?? 0 ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="px-3 py-6 text-center text-gray-500">
                                        Tidak ada data.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION -->
                <?php if ($prodiTotal > 1): ?>
                    <div class="flex justify-center space-x-1 pt-2">
                        <?php if ($prodiPage > 1): ?>
                            <a href="index.php?controller=admin&action=laporan&range=<?= $range ?>&prodi_page=<?= $prodiPage - 1 ?>"
                                class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">&lt;</a>
                        <?php else: ?>
                            <span class="px-3 py-1 bg-gray-100 text-gray-400 rounded">&lt;</span>
                        <?php endif; ?>

                        <?php for ($p = 1; $p <= $prodiTotal; $p++): ?>
                            <a href="index.php?controller=admin&action=laporan&range=<?= $range ?>&prodi_page=<?= $p ?>"
                                class="px-3 py-1 rounded <?= $p == $prodiPage ? 'bg-[#1e3a5f] text-white' : 'bg-gray-200 hover:bg-gray-300' ?>">
                                <?= $p ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($prodiPage < $prodiTotal): ?>
                            <a href="index.php?controller=admin&action=laporan&range=<?= $range ?>&prodi_page=<?= $prodiPage + 1 ?>"
                                class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">&gt;</a>
                        <?php else: ?>
                            <span class="px-3 py-1 bg-gray-100 text-gray-400 rounded">&gt;</span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ====================================================== -->
            <!-- 3. LAPORAN RATING -->
            <!-- ====================================================== -->

            <div class="bg-white shadow rounded-xl p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-[#1e3a5f]">
                        Rating Ruangan
                    </h2>

                    <button class="px-4 py-1.5 bg-[#1e3a5f] text-white rounded-lg text-sm hover:bg-[#163052]">
                        Export
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                        <thead>
                            <tr class="bg-gray-200 text-gray-800 text-sm">
                                <th class="px-3 py-2">No</th>
                                <th class="px-3 py-2">Tanggal</th>
                                <th class="px-3 py-2">Ruangan Pertama</th>
                                <th class="px-3 py-2">Ruangan Kedua</th>
                                <th class="px-3 py-2">Ruangan Ketiga</th>
                                <th class="px-3 py-2">Rata-rata</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($rating)): ?>
                                <?php foreach ($rating as $i => $row): ?>
                                    <tr class="<?= $i % 2 ? 'bg-gray-50' : 'bg-white' ?> border-b">
                                        <td class="px-3 py-2 text-center"><?= $i + 1 ?></td>
                                        <td class="px-3 py-2"><?= htmlspecialchars($row['tanggal'] ?? '-') ?></td>
                                        <td class="px-3 py-2 text-center"><?= $row['r1'] ?? 0 ?></td>
                                        <td class="px-3 py-2 text-center"><?= $row['r2'] ?? 0 ?></td>
                                        <td class="px-3 py-2 text-center><?= $row['r3'] ?? 0 ?></td>
                                        <td class=" px-3 py-2 text-center"><?= $row['avg'] ?? 0 ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="px-3 py-6 text-center text-gray-500">
                                        Tidak ada data.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION -->
                <?php if ($ratingTotal > 1): ?>
                    <div class="flex justify-center space-x-1 pt-2">
                        <?php if ($ratingPage > 1): ?>
                            <a href="index.php?controller=admin&action=laporan&range=<?= $range ?>&rating_page=<?= $ratingPage - 1 ?>"
                                class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">&lt;</a>
                        <?php else: ?>
                            <span class="px-3 py-1 bg-gray-100 text-gray-400 rounded">&lt;</span>
                        <?php endif; ?>

                        <?php for ($p = 1; $p <= $ratingTotal; $p++): ?>
                            <a href="index.php?controller=admin&action=laporan&range=<?= $range ?>&rating_page=<?= $p ?>"
                                class="px-3 py-1 rounded <?= $p == $ratingPage ? 'bg-[#1e3a5f] text-white' : 'bg-gray-200 hover:bg-gray-300' ?>">
                                <?= $p ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($ratingPage < $ratingTotal): ?>
                            <a href="index.php?controller=admin&action=laporan&range=<?= $range ?>&rating_page=<?= $ratingPage + 1 ?>"
                                class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">&gt;</a>
                        <?php else: ?>
                            <span class="px-3 py-1 bg-gray-100 text-gray-400 rounded">&gt;</span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>

</body>

</html>