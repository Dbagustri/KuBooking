<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan | Kubooking</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#f2f7fc] text-gray-800 flex">

    <?php
    // ==========================
    // SIDEBAR
    // ==========================
    $sidebarPath = __DIR__ . '/../layout/sidebar.php';
    if (file_exists($sidebarPath)) {
        include $sidebarPath;
    }

    // ==========================
    // DATA DARI CONTROLLER
    // ==========================
    $range          = $range ?? 'month';

    $roomsRaw       = $summary_rooms   ?? [];
    $prodiRaw       = $summary_prodi   ?? [];
    $jurusanRaw     = $summary_jurusan ?? [];
    $ratingSummary  = $summary_rating  ?? [];

    /**
     * Helper pivot: 
     *  - $rows: array data mentah
     *  - $keyDate: nama kolom tanggal (string)
     *  - $keyCat: nama kolom kategori (ruangan/prodi/jurusan)
     *  - $keyTotal: nama kolom total
     * Return:
     *  - ['dates' => [...], 'categories' => [...], 'data' => [tanggal => [kategori => total]]]
     */
    function pivotByCategory(array $rows, string $keyDate, string $keyCat, string $keyTotal): array
    {
        $dates      = [];
        $categories = [];
        $data       = [];

        foreach ($rows as $row) {
            $tgl = $row[$keyDate] ?? null;
            $cat = $row[$keyCat] ?? null;
            $tot = (int)($row[$keyTotal] ?? 0);

            if (!$tgl || !$cat) {
                continue;
            }

            if (!in_array($tgl, $dates, true)) {
                $dates[] = $tgl;
            }
            if (!in_array($cat, $categories, true)) {
                $categories[] = $cat;
            }

            if (!isset($data[$tgl])) {
                $data[$tgl] = [];
            }
            $data[$tgl][$cat] = $tot;
        }

        sort($dates);
        sort($categories);

        return [
            'dates'      => $dates,
            'categories' => $categories,
            'data'       => $data,
        ];
    }

    // Pivot untuk masing-masing laporan
    $pivotRuangan = pivotByCategory($roomsRaw,   'tanggal', 'nama_ruangan', 'total');
    $pivotProdi   = pivotByCategory($prodiRaw,   'tanggal', 'prodi',        'total');
    $pivotJurusan = pivotByCategory($jurusanRaw, 'tanggal', 'jurusan',      'total');
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
            <div class="flex flex-wrap gap-2">
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
            <!-- 1. LAPORAN RUANGAN TERBANYAK (PIVOT) -->
            <!-- ====================================================== -->
            <div class="bg-white shadow rounded-xl p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-[#1e3a5f]">
                        Laporan Ruangan Terbanyak (per Tanggal)
                    </h2>

                    <button class="px-4 py-1.5 bg-[#1e3a5f] text-white rounded-lg text-sm hover:bg-[#163052]">
                        Export
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <?php if (!empty($pivotRuangan['dates']) && !empty($pivotRuangan['categories'])): ?>
                        <table class="min-w-full bg-white border border-gray-200 rounded-lg text-sm">
                            <thead>
                                <tr class="bg-gray-200 text-gray-800">
                                    <th class="px-3 py-2 text-left">Tanggal</th>
                                    <?php foreach ($pivotRuangan['categories'] as $roomName): ?>
                                        <th class="px-3 py-2 text-center">
                                            <?= htmlspecialchars($roomName) ?>
                                        </th>
                                    <?php endforeach; ?>
                                    <th class="px-3 py-2 text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pivotRuangan['dates'] as $tgl): ?>
                                    <?php
                                    $rowData = $pivotRuangan['data'][$tgl] ?? [];
                                    $sum     = 0;
                                    ?>
                                    <tr class="border-b <?= (strtotime($tgl) % 2) ? 'bg-gray-50' : 'bg-white' ?>">
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            <?= htmlspecialchars($tgl) ?>
                                        </td>

                                        <?php foreach ($pivotRuangan['categories'] as $roomName): ?>
                                            <?php
                                            $val = isset($rowData[$roomName]) ? (int)$rowData[$roomName] : 0;
                                            $sum += $val;
                                            ?>
                                            <td class="px-3 py-2 text-center">
                                                <?= $val ?>
                                            </td>
                                        <?php endforeach; ?>

                                        <td class="px-3 py-2 text-center font-semibold">
                                            <?= $sum ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-gray-500 text-sm text-center py-4">
                            Tidak ada data ruangan untuk range ini.
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ====================================================== -->
            <!-- 2. LAPORAN BERDASARKAN PRODI (PIVOT) -->
            <!-- ====================================================== -->
            <div class="bg-white shadow rounded-xl p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-[#1e3a5f]">
                        Laporan Berdasarkan Prodi (per Tanggal)
                    </h2>

                    <button class="px-4 py-1.5 bg-[#1e3a5f] text-white rounded-lg text-sm hover:bg-[#163052]">
                        Export
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <?php if (!empty($pivotProdi['dates']) && !empty($pivotProdi['categories'])): ?>
                        <table class="min-w-full bg-white border border-gray-200 rounded-lg text-sm">
                            <thead>
                                <tr class="bg-gray-200 text-gray-800">
                                    <th class="px-3 py-2 text-left">Tanggal</th>
                                    <?php foreach ($pivotProdi['categories'] as $prodi): ?>
                                        <th class="px-3 py-2 text-center">
                                            <?= htmlspecialchars($prodi) ?>
                                        </th>
                                    <?php endforeach; ?>
                                    <th class="px-3 py-2 text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pivotProdi['dates'] as $tgl): ?>
                                    <?php
                                    $rowData = $pivotProdi['data'][$tgl] ?? [];
                                    $sum     = 0;
                                    ?>
                                    <tr class="border-b <?= (strtotime($tgl) % 2) ? 'bg-gray-50' : 'bg-white' ?>">
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            <?= htmlspecialchars($tgl) ?>
                                        </td>

                                        <?php foreach ($pivotProdi['categories'] as $prodi): ?>
                                            <?php
                                            $val = isset($rowData[$prodi]) ? (int)$rowData[$prodi] : 0;
                                            $sum += $val;
                                            ?>
                                            <td class="px-3 py-2 text-center">
                                                <?= $val ?>
                                            </td>
                                        <?php endforeach; ?>

                                        <td class="px-3 py-2 text-center font-semibold">
                                            <?= $sum ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-gray-500 text-sm text-center py-4">
                            Tidak ada data prodi untuk range ini.
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ====================================================== -->
            <!-- 3. LAPORAN BERDASARKAN JURUSAN (PIVOT) -->
            <!-- ====================================================== -->
            <div class="bg-white shadow rounded-xl p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-[#1e3a5f]">
                        Laporan Berdasarkan Jurusan (per Tanggal)
                    </h2>

                    <button class="px-4 py-1.5 bg-[#1e3a5f] text-white rounded-lg text-sm hover:bg-[#163052]">
                        Export
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <?php if (!empty($pivotJurusan['dates']) && !empty($pivotJurusan['categories'])): ?>
                        <table class="min-w-full bg-white border border-gray-200 rounded-lg text-sm">
                            <thead>
                                <tr class="bg-gray-200 text-gray-800">
                                    <th class="px-3 py-2 text-left">Tanggal</th>
                                    <?php foreach ($pivotJurusan['categories'] as $jurusan): ?>
                                        <th class="px-3 py-2 text-center">
                                            <?= htmlspecialchars($jurusan) ?>
                                        </th>
                                    <?php endforeach; ?>
                                    <th class="px-3 py-2 text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pivotJurusan['dates'] as $tgl): ?>
                                    <?php
                                    $rowData = $pivotJurusan['data'][$tgl] ?? [];
                                    $sum     = 0;
                                    ?>
                                    <tr class="border-b <?= (strtotime($tgl) % 2) ? 'bg-gray-50' : 'bg-white' ?>">
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            <?= htmlspecialchars($tgl) ?>
                                        </td>

                                        <?php foreach ($pivotJurusan['categories'] as $jurusan): ?>
                                            <?php
                                            $val = isset($rowData[$jurusan]) ? (int)$rowData[$jurusan] : 0;
                                            $sum += $val;
                                            ?>
                                            <td class="px-3 py-2 text-center">
                                                <?= $val ?>
                                            </td>
                                        <?php endforeach; ?>

                                        <td class="px-3 py-2 text-center font-semibold">
                                            <?= $sum ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-gray-500 text-sm text-center py-4">
                            Tidak ada data jurusan untuk range ini.
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ====================================================== -->
            <!-- 4. LAPORAN RATING RUANGAN (NON-PIVOT) -->
            <!-- ====================================================== -->
            <div class="bg-white shadow rounded-xl p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-[#1e3a5f]">
                        Laporan Rating Ruangan (Rata-rata di Range Terpilih)
                    </h2>

                    <button class="px-4 py-1.5 bg-[#1e3a5f] text-white rounded-lg text-sm hover:bg-[#163052]">
                        Export
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <?php if (!empty($ratingSummary)): ?>
                        <table class="min-w-full bg-white border border-gray-200 rounded-lg text-sm">
                            <thead>
                                <tr class="bg-gray-200 text-gray-800">
                                    <th class="px-3 py-2 text-left">Ruangan</th>
                                    <th class="px-3 py-2 text-center">Rata-rata Rating</th>
                                    <th class="px-3 py-2 text-center">Jumlah Feedback</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ratingSummary as $row): ?>
                                    <tr class="border-b">
                                        <td class="px-3 py-2">
                                            <?= htmlspecialchars($row['nama_ruangan'] ?? '-') ?>
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            <?= number_format((float)($row['avg_rating_range'] ?? 0), 2) ?>
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            <?= (int)($row['total_feedback'] ?? 0) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-gray-500 text-sm text-center py-4">
                            Belum ada feedback untuk range ini.
                        </p>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

</body>

</html>