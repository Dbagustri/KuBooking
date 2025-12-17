<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Ruangan | Kubooking</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#f2f7fc] text-gray-800 flex">
    <?php
    $sidebarPath = __DIR__ . '/../layout/sidebar.php';
    if (file_exists($sidebarPath)) include $sidebarPath;
    ?>

    <div class="flex-1 flex flex-col h-screen overflow-y-auto">
        <?php
        $flashPath = __DIR__ . '/../layout/flash.php';
        if (file_exists($flashPath)) include $flashPath;
        ?>
        <div class="m-4">
            <?php
            $navPath = __DIR__ . '/../layout/nav-admin.php';
            if (file_exists($navPath)) include $navPath;
            ?>
        </div>

        <?php
        // Data dari controller (fallback aman)
        $rooms        = $rooms        ?? [];
        $search       = $search       ?? '';
        $statusFilter = $status       ?? 'all';
        $currentPage  = $current_page ?? 1;
        $totalPages   = $total_pages  ?? 1;
        $anyActive    = $anyActive    ?? false;

        $btnLabel    = $anyActive ? 'Nonaktifkan Semua (OFF)' : 'Aktifkan Semua (ON)';
        $btnColor    = $anyActive ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700';
        $confirmText = $anyActive
            ? 'Yakin ingin menonaktifkan seluruh ruangan? Jika masih ada peminjaman aktif, ruangan tidak bisa dinonaktifkan.'
            : 'Aktifkan semua ruangan?';
        ?>

        <div class="px-8 pb-10 space-y-6">

            <!-- HEADER + ACTIONS -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-[#1e3a5f]">Kelola Ruangan</h1>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 sm:items-center">
                    <a href="index.php?controller=admin&action=tambahRuangan"
                        class="inline-flex items-center justify-center px-4 py-2 rounded-full text-sm font-semibold text-white bg-[#1e3a5f] hover:bg-[#163052] shadow">
                        Tambah Ruangan
                    </a>

                    <form action="index.php?controller=admin&action=toggleAllRooms"
                        method="POST"
                        onsubmit="return confirm('<?= htmlspecialchars($confirmText, ENT_QUOTES, 'UTF-8') ?>');">
                        <button type="submit"
                            class="px-4 py-2 rounded-full text-sm font-semibold text-white shadow flex items-center gap-2 <?= $btnColor ?>">
                            <span class="inline-flex items-center w-9 h-5 rounded-full bg-white/20 border border-white/40 relative">
                                <span class="inline-block w-4 h-4 rounded-full bg-white shadow transform <?= $anyActive ? 'translate-x-4' : 'translate-x-0' ?>"></span>
                            </span>
                            <span><?= htmlspecialchars($btnLabel) ?></span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- FILTER & SEARCH -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:space-x-4 space-y-3 lg:space-y-0 mt-2">
                <!-- FILTER (auto submit) -->
                <form id="filterForm" method="get" class="w-full lg:w-auto">
                    <input type="hidden" name="controller" value="admin">
                    <input type="hidden" name="action" value="ruangan">
                    <input type="hidden" name="q" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="page" value="1">

                    <select name="status"
                        onchange="document.getElementById('filterForm').submit()"
                        class="bg-white border border-gray-300 rounded-full px-4 py-2 text-sm shadow w-full lg:w-auto">
                        <option value="all" <?= $statusFilter === 'all' ? 'selected' : '' ?>>Semua Status</option>
                        <option value="aktif" <?= $statusFilter === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                        <option value="nonaktif" <?= $statusFilter === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </form>

                <!-- SEARCH (butuh tombol submit) -->
                <form method="get" class="flex flex-1 items-center gap-0">
                    <input type="hidden" name="controller" value="admin">
                    <input type="hidden" name="action" value="ruangan">
                    <input type="hidden" name="status" value="<?= htmlspecialchars($statusFilter, ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="page" value="1">

                    <div class="flex flex-1 items-center bg-white rounded-full px-4 py-2 shadow border border-gray-200">
                        <input type="text"
                            name="q"
                            value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="Cari ruangan berdasarkan nama atau kategori"
                            class="flex-1 text-sm bg-transparent focus:outline-none">
                    </div>

                    <button type="submit"
                        class="ml-2 w-10 h-10 rounded-full bg-[#1e3a5f] flex items-center justify-center text-white hover:bg-[#163052] transition">
                        🔍
                    </button>
                </form>
            </div>

            <!-- TABEL RUANGAN -->
            <div class="overflow-x-auto mt-4">
                <table class="min-w-full border-collapse bg-white shadow rounded-lg overflow-hidden">
                    <thead>
                        <tr class="bg-[#1e3a5f] text-white text-left text-sm">
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3">Lokasi</th>
                            <th class="px-4 py-3">Kapasitas</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (!empty($rooms)): ?>
                            <?php foreach ($rooms as $i => $r): ?>
                                <?php
                                $id        = (int)($r['id_ruangan'] ?? 0);
                                $nama      = $r['nama_ruangan'] ?? '-';
                                $kategori  = $r['kategori'] ?? '-';
                                $lokasi    = $r['lokasi'] ?? '-';
                                $kapMin    = (int)($r['kapasitas_min'] ?? 0);
                                $kapMax    = (int)($r['kapasitas_max'] ?? 0);
                                $kapasitas = ($kapMin > 0 && $kapMax > 0) ? "{$kapMin} - {$kapMax}" : '-';
                                $status    = $r['status_operasional'] ?? 'nonaktif';

                                $badgeClass = $status === 'aktif'
                                    ? 'bg-green-100 text-green-800'
                                    : 'bg-gray-200 text-gray-700';

                                $rowClass = $i % 2 === 0 ? 'bg-gray-50' : 'bg-gray-100';
                                ?>
                                <tr class="<?= $rowClass ?> text-sm text-gray-800 border-b last:border-b-0">
                                    <td class="px-4 py-3">
                                        <span class="font-medium text-slate-900">
                                            <?= htmlspecialchars($nama) ?>
                                        </span>
                                    </td>

                                    <td class="px-4 py-3"><?= htmlspecialchars($kategori) ?></td>
                                    <td class="px-4 py-3"><?= htmlspecialchars($lokasi) ?></td>
                                    <td class="px-4 py-3">
                                        <?= htmlspecialchars($kapasitas) ?> orang
                                    </td>

                                    <td class="px-4 py-3">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $badgeClass ?>">
                                            <?= htmlspecialchars(ucfirst($status)) ?>
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        <div class="relative inline-block text-left">
                                            <button type="button"
                                                onclick="toggleMenu('room-<?= $id ?>')"
                                                class="inline-flex justify-center w-8 h-8 rounded-full hover:bg-gray-200 text-xl leading-none">
                                                &#8226;&#8226;&#8226;
                                            </button>

                                            <div id="room-<?= $id ?>"
                                                class="hidden origin-top-right absolute right-0 mt-2 w-44 rounded-md shadow-lg bg-white ring-1 ring-black/5 z-20 text-left text-sm">
                                                <a href="index.php?controller=admin&action=detailRuangan&id=<?= $id ?>"
                                                    class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                                    Detail
                                                </a>

                                                <a href="index.php?controller=admin&action=editRoom&id=<?= $id ?>"
                                                    class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                                    Edit
                                                </a>

                                                <?php if ($status === 'aktif'): ?>
                                                    <form action="index.php?controller=admin&action=setRoomStatus"
                                                        method="POST"
                                                        onsubmit="return confirm('Yakin ingin menonaktifkan ruangan ini?');">
                                                        <input type="hidden" name="id_ruangan" value="<?= $id ?>">
                                                        <input type="hidden" name="status" value="nonaktif">
                                                        <button type="submit"
                                                            class="w-full text-left px-4 py-2 text-red-700 hover:bg-red-50">
                                                            Nonaktifkan
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <form action="index.php?controller=admin&action=setRoomStatus"
                                                        method="POST"
                                                        onsubmit="return confirm('Aktifkan kembali ruangan ini?');">
                                                        <input type="hidden" name="id_ruangan" value="<?= $id ?>">
                                                        <input type="hidden" name="status" value="aktif">
                                                        <button type="submit"
                                                            class="w-full text-left px-4 py-2 text-green-700 hover:bg-green-50">
                                                            Aktifkan
                                                        </button>
                                                    </form>
                                                <?php endif; ?>

                                                <form action="index.php?controller=admin&action=deleteRuangan"
                                                    method="POST"
                                                    onsubmit="return confirm('Yakin ingin menghapus ruangan ini?');">
                                                    <input type="hidden" name="id_ruangan" value="<?= $id ?>">
                                                    <button type="submit"
                                                        class="w-full text-left px-4 py-2 border-t text-red-700 hover:bg-red-50">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    <?php if ($search !== '' || $statusFilter !== 'all'): ?>
                                        Tidak ada ruangan yang cocok dengan filter.
                                        <a href="index.php?controller=admin&action=ruangan"
                                            class="text-[#1e3a5f] underline text-sm ml-1">
                                            Reset filter
                                        </a>
                                    <?php else: ?>
                                        Belum ada data ruangan.
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION (pakai komponen) -->
            <?php
            if ((int)$totalPages > 1) {
                $pagination = [
                    'pageKey'     => 'page',
                    'currentPage' => (int)$currentPage,
                    'totalPages'  => (int)$totalPages,
                    'params'      => [
                        'controller' => 'admin',
                        'action'     => 'ruangan',
                        'q'          => $search,
                        'status'     => $statusFilter,
                    ],
                ];
                include __DIR__ . '/../layout/pagination.php';
            }
            ?>

        </div>
    </div>

    <script>
        function toggleMenu(id) {
            const menu = document.getElementById(id);
            if (!menu) return;
            const isHidden = menu.classList.contains('hidden');

            document.querySelectorAll("[id^='room-']").forEach(m => m.classList.add('hidden'));
            if (isHidden) menu.classList.remove('hidden');
        }

        document.addEventListener('click', function(e) {
            document.querySelectorAll("[id^='room-']").forEach(menu => {
                const btn = menu.previousElementSibling;
                if (!menu.contains(e.target) && btn && !btn.contains(e.target)) {
                    menu.classList.add('hidden');
                }
            });
        });
    </script>
</body>

</html>