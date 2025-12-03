<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Ruangan | Kubooking</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#f2f7fc] text-gray-800 flex">

    <!-- SIDEBAR -->
    <?php
    $sidebarPath = __DIR__ . '/../layout/sidebar.php';
    if (file_exists($sidebarPath)) {
        include $sidebarPath;
    }
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

        <?php
        $rooms        = $rooms ?? [];
        $search       = $_GET['q']      ?? '';
        $statusFilter = $_GET['status'] ?? 'all';

        // Cek apakah ada ruangan aktif (untuk tombol ON/OFF semua)
        $anyActive = false;
        foreach ($rooms as $r) {
            if (($r['status_operasional'] ?? '') === 'aktif') {
                $anyActive = true;
                break;
            }
        }

        $btnLabel     = $anyActive ? 'Nonaktifkan Semua (OFF)' : 'Aktifkan Semua (ON)';
        $btnColor     = $anyActive ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700';
        $confirmText  = $anyActive
            ? 'Yakin ingin menonaktifkan seluruh ruangan?'
            : 'Aktifkan semua ruangan?';
        ?>

        <div class="px-8 pb-10 space-y-6">

            <!-- HEADER + TOGGLE ALL -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-[#1e3a5f]">Kelola Ruangan</h1>
                </div>

                <form action="index.php?controller=admin&action=toggleAllRooms"
                    method="POST"
                    onsubmit="return confirm('<?= $confirmText ?>');">
                    <button type="submit"
                        class="px-4 py-2 rounded-full text-sm font-semibold text-white shadow flex items-center gap-2 <?= $btnColor ?>">

                        <!-- Icon toggle kecil -->
                        <span class="inline-flex items-center w-9 h-5 rounded-full bg-white/20 border border-white/40 relative">
                            <span class="inline-block w-4 h-4 rounded-full bg-white shadow transform <?= $anyActive ? 'translate-x-4' : 'translate-x-0' ?>"></span>
                        </span>

                        <span><?= htmlspecialchars($btnLabel) ?></span>
                    </button>
                </form>
            </div>

            <!-- FILTER & SEARCH -->
            <form method="get"
                class="flex flex-col lg:flex-row lg:items-center lg:space-x-4 space-y-3 lg:space-y-0 mt-2">
                <input type="hidden" name="controller" value="admin">
                <input type="hidden" name="action" value="ruangan">

                <!-- FILTER STATUS -->
                <select name="status"
                    class="bg-white border border-gray-300 rounded-full px-4 py-2 text-sm shadow w-full lg:w-auto">
                    <option value="all" <?= $statusFilter === 'all'      ? 'selected' : '' ?>>Semua Status</option>
                    <option value="aktif" <?= $statusFilter === 'aktif'    ? 'selected' : '' ?>>Aktif</option>
                    <option value="nonaktif" <?= $statusFilter === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                </select>

                <!-- SEARCH -->
                <div class="flex flex-1 items-center bg-white rounded-full px-4 py-2 shadow border border-gray-200">
                    <input type="text"
                        name="q"
                        value="<?= htmlspecialchars($search) ?>"
                        placeholder="Cari ruangan berdasarkan nama atau kategori"
                        class="flex-1 text-sm bg-transparent focus:outline-none">
                </div>

                <button type="submit"
                    class="w-10 h-10 rounded-full bg-[#1e3a5f] flex items-center justify-center text-white hover:bg-[#163052] transition">
                    🔍
                </button>
            </form>

            <!-- TABEL RUANGAN -->
            <div class="overflow-x-auto mt-4">
                <table class="min-w-full border-collapse bg-white shadow rounded-lg overflow-hidden">
                    <thead>
                        <tr class="bg-[#1e3a5f] text-white text-left text-sm">
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3">Kapasitas</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        // Flag untuk cek apakah setelah filter masih ada data
                        $hasVisible = false;

                        if (!empty($rooms)):
                            foreach ($rooms as $i => $r):
                                $id        = (int)$r['id_ruangan'];
                                $nama      = $r['nama_ruangan'] ?? '-';
                                $kategori  = $r['kategori'] ?? '-';
                                $kapasitas = "{$r['kapasitas_min']} - {$r['kapasitas_max']}";
                                $status    = $r['status_operasional'] ?? 'nonaktif';

                                // Filter status di view (all / aktif / nonaktif)
                                if ($statusFilter === 'aktif' && $status !== 'aktif') {
                                    continue;
                                }
                                if ($statusFilter === 'nonaktif' && $status !== 'nonaktif') {
                                    continue;
                                }

                                $hasVisible = true;

                                $badgeClass = $status === 'aktif'
                                    ? 'bg-green-100 text-green-800'
                                    : 'bg-gray-200 text-gray-700';

                                $rowClass = $i % 2 === 0 ? 'bg-gray-50' : 'bg-gray-100';
                        ?>
                                <tr class="<?= $rowClass ?> text-sm text-gray-800 border-b last:border-b-0">
                                    <td class="px-4 py-3">
                                        <div class="flex flex-col">
                                            <span class="font-medium text-slate-900">
                                                <?= htmlspecialchars($nama) ?>
                                            </span>
                                        </div>
                                    </td>

                                    <td class="px-4 py-3">
                                        <span class="text-slate-700">
                                            <?= htmlspecialchars($kategori) ?>
                                        </span>
                                    </td>

                                    <td class="px-4 py-3">
                                        <span class="text-slate-700">
                                            <?= htmlspecialchars($kapasitas) ?> orang
                                        </span>
                                    </td>

                                    <td class="px-4 py-3">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $badgeClass ?>">
                                            <?= htmlspecialchars(ucfirst($status)) ?>
                                        </span>
                                    </td>

                                    <!-- AKSI TITIK TIGA -->
                                    <td class="px-4 py-3 text-center">
                                        <div class="relative inline-block text-left">
                                            <button type="button"
                                                onclick="toggleMenu('room-<?= $id ?>')"
                                                class="inline-flex justify-center w-8 h-8 rounded-full hover:bg-gray-200 text-xl leading-none">
                                                &#8226;&#8226;&#8226;
                                            </button>

                                            <div id="room-<?= $id ?>"
                                                class="hidden origin-top-right absolute right-0 mt-2 w-44 
                                                        rounded-md shadow-lg bg-white ring-1 ring-black/5 
                                                        z-20 text-left text-sm">

                                                <!-- DETAIL -->
                                                <a href="index.php?controller=admin&action=detailRuangan&id=<?= $id ?>"
                                                    class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                                    Detail
                                                </a>

                                                <!-- EDIT -->
                                                <a href="index.php?controller=admin&action=editRoom&id=<?= $id ?>"
                                                    class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                                    Edit
                                                </a>

                                                <!-- AKTIF / NONAKTIF -->
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

                                                <!-- HAPUS -->
                                                <form action="index.php?controller=admin&action=deleteRuangan"
                                                    method="POST"
                                                    onsubmit="return confirm('Yakin ingin menghapus ruangan ini?');">
                                                    <input type="hidden" name="id_ruangan" value="<?= $id ?>">
                                                    <button type="submit"
                                                        class="w-full text-left px-4 py-2 border-t
                                                                   text-red-700 hover:bg-red-50">
                                                        Hapus
                                                    </button>
                                                </form>

                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <?php if (empty($rooms) || !$hasVisible): ?>
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    <?php if (!empty($rooms) && !$hasVisible): ?>
                                        Tidak ada ruangan yang cocok dengan filter.
                                    <?php else: ?>
                                        Belum ada data ruangan.
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <script>
        function toggleMenu(id) {
            const menu = document.getElementById(id);
            if (!menu) return;
            menu.classList.toggle('hidden');
        }

        // Tutup dropdown jika klik di luar
        document.addEventListener('click', function(e) {
            document.querySelectorAll("[id^='room-']").forEach(menu => {
                const btn = menu.previousElementSibling;
                if (!menu.contains(e.target) && !btn.contains(e.target)) {
                    menu.classList.add('hidden');
                }
            });
        });
    </script>

</body>

</html>