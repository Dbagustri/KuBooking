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

        <div class="px-8 pb-10 space-y-6">
            <h1 class="text-2xl font-bold text-[#1e3a5f]">Kelola Ruangan</h1>

            <?php
            $rooms = $rooms ?? [];
            ?>

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
                        <?php if (!empty($rooms)): ?>
                            <?php foreach ($rooms as $i => $r): ?>
                                <?php
                                $id         = (int)$r['id_ruangan'];
                                $nama       = $r['nama_ruangan'] ?? '-';
                                $kategori   = $r['kategori'] ?? '-';
                                $kapasitas  = "{$r['kapasitas_min']} - {$r['kapasitas_max']}";
                                $status     = $r['status_operasional'] ?? 'nonaktif';

                                $badgeClass = $status === 'aktif'
                                    ? 'bg-green-100 text-green-800'
                                    : 'bg-gray-200 text-gray-700';
                                ?>
                                <tr class="<?= $i % 2 === 0 ? 'bg-gray-50' : 'bg-gray-100'; ?> 
                                           text-sm text-gray-800 border-b">
                                    <td class="px-4 py-3"><?= htmlspecialchars($nama) ?></td>
                                    <td class="px-4 py-3"><?= htmlspecialchars($kategori) ?></td>
                                    <td class="px-4 py-3"><?= htmlspecialchars($kapasitas) ?></td>
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
                                                class="hidden origin-top-right absolute right-0 mt-2 w-40 
                                                       rounded-md shadow-lg bg-white ring-1 ring-black/5 
                                                       z-20 text-left">

                                                <!-- DETAIL -->
                                                <a href="index.php?controller=admin&action=detailRuangan&id=<?= $id ?>"
                                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    Detail
                                                </a>

                                                <!-- EDIT -->
                                                <a href="index.php?controller=admin&action=editRuangan&id=<?= $id ?>"
                                                    class="block px-4 py-2 text-sm text-blue-700 hover:bg-blue-50">
                                                    Edit
                                                </a>

                                                <!-- HAPUS -->
                                                <form action="index.php?controller=admin&action=deleteRuangan"
                                                    method="POST"
                                                    onsubmit="return confirm('Yakin ingin menghapus ruangan ini?')">
                                                    <input type="hidden" name="id_ruangan" value="<?= $id ?>">
                                                    <button type="submit"
                                                        class="w-full text-left px-4 py-2 text-sm border-t
                                                               text-red-700 hover:bg-red-50">
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
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    Belum ada data ruangan.
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