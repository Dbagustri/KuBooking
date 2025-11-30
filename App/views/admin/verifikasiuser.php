<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Verifikasi User | Kubooking</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#f2f7fc] text-gray-800 flex">

    <!-- SIDEBAR -->
    <?php include __DIR__ . '/../layout/sidebar.php'; ?>

    <!-- CONTENT -->
    <div class="flex-1 flex flex-col h-screen overflow-y-auto">

        <!-- NAVBAR -->
        <div class="m-4">
            <?php include __DIR__ . '/../layout/nav-admin.php'; ?>
        </div>

        <div class="px-8 pb-10 space-y-6">

            <h1 class="text-2xl font-bold text-[#1e3a5f]">Verifikasi User</h1>

            <!-- FILTER & SEARCH -->
            <div class="flex flex-col md:flex-row md:space-x-4 space-y-3 md:space-y-0">

                <!-- FILTER -->
                <form class="flex items-center space-x-2" method="get">
                    <input type="hidden" name="controller" value="admin">
                    <input type="hidden" name="action" value="verifikasiUser">

                    <select name="filter"
                        class="px-4 py-2 rounded-lg bg-white border shadow text-sm">
                        <option value="">Filter</option>
                        <option value="pending" <?= $filter === 'pending'  ? 'selected' : '' ?>>Pending</option>
                        <option value="approved" <?= $filter === 'approved' ? 'selected' : '' ?>>Approved</option>
                    </select>

                    <button class="px-3 py-2 bg-[#1e3a5f] text-white rounded-lg text-sm">Terapkan</button>
                </form>

                <!-- SEARCH -->
                <form class="flex flex-1 items-center" method="get">
                    <input type="hidden" name="controller" value="admin">
                    <input type="hidden" name="action" value="verifikasiUser">

                    <input type="text" name="q" placeholder="Cari..."
                        value="<?= htmlspecialchars($search ?? '') ?>"
                        class="flex-1 px-4 py-2 rounded-l-full bg-white border shadow text-sm">

                    <button class="px-4 py-2 bg-[#1e3a5f] text-white rounded-r-full text-sm">
                        🔍
                    </button>
                </form>

            </div>

            <!-- TABLE -->
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse bg-white shadow rounded-lg">
                    <thead>
                        <tr class="bg-[#1e3a5f] text-white text-left text-sm">
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Jurusan</th>
                            <th class="px-4 py-3">Screenshot</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (!empty($list)): ?>
                            <?php foreach ($list as $i => $u): ?>
                                <tr class="<?= $i % 2 === 0 ? 'bg-gray-50' : 'bg-gray-100' ?> text-sm">
                                    <td class="px-4 py-3"><?= $u['nama'] ?></td>
                                    <td class="px-4 py-3"><?= $u['email'] ?></td>
                                    <td class="px-4 py-3"><?= $u['jurusan'] ?></td>
                                    <td class="px-4 py-3 text-blue-600 hover:underline">
                                        <?php if ($u['screenshot_kubaca']): ?>
                                            <a href="<?= $u['screenshot_kubaca'] ?>" target="_blank">lihat bukti</a>
                                        <?php else: ?>
                                            <span class="text-gray-400">tidak ada</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3">
                                        <?php
                                        $status = $u['status'];
                                        $badge = 'bg-yellow-100 text-yellow-700';
                                        if ($status === 'approved') $badge = 'bg-green-100 text-green-700';
                                        if ($status === 'rejected') $badge = 'bg-red-100 text-red-700';
                                        ?>
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $badge ?>">
                                            <?= ucfirst($status) ?>
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        <?php
                                        $status   = $u['status'];                         // dari tabel registrasi
                                        $isFinal  = in_array($status, ['approved', 'rejected'], true);
                                        $rowId    = (int)$u['id_registrasi'];
                                        ?>
                                        <div class="relative inline-block text-left">
                                            <!-- Tombol titik tiga -->
                                            <button type="button"
                                                onclick="toggleMenu(<?= $rowId ?>)"
                                                class="inline-flex justify-center w-8 h-8 rounded-full hover:bg-gray-200 text-xl leading-none">
                                                &#8226;&#8226;&#8226;
                                            </button>

                                            <!-- Dropdown menu -->
                                            <div id="menu-<?= $rowId ?>"
                                                class="hidden origin-top-right absolute right-0 mt-2 w-40 rounded-md shadow-lg bg-white ring-1 ring-black/5 z-20 text-left">
                                                <!-- DETAIL -->
                                                <a href="index.php?controller=admin&action=detailUser&id=<?= $rowId ?>"
                                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    Detail
                                                </a>

                                                <!-- APPROVE -->
                                                <form action="index.php?controller=admin&action=approveUser" method="POST">
                                                    <input type="hidden" name="id_registrasi" value="<?= $rowId ?>">
                                                    <button type="submit"
                                                        <?= $isFinal ? 'disabled' : '' ?>
                                                        class="w-full text-left px-4 py-2 text-sm 
                                                                <?= $isFinal
                                                                    ? 'text-gray-400 cursor-not-allowed opacity-50'
                                                                    : 'text-green-700 hover:bg-green-50' ?>">
                                                        Approve
                                                    </button>
                                                </form>

                                                <!-- REJECT -->
                                                <form action="index.php?controller=admin&action=rejectUser" method="POST"
                                                    onsubmit="return confirm('Yakin ingin menolak user ini?');">
                                                    <input type="hidden" name="id_registrasi" value="<?= $rowId ?>">
                                                    <button type="submit"
                                                        <?= $isFinal ? 'disabled' : '' ?>
                                                        class="w-full text-left px-4 py-2 text-sm border-t 
                                                                <?= $isFinal
                                                                    ? 'text-gray-400 cursor-not-allowed opacity-50'
                                                                    : 'text-red-700 hover:bg-red-50' ?>">
                                                        Reject
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-gray-500">Tidak ada data.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <?php if ($total_pages > 1): ?>
                <div class="flex justify-center mt-4 space-x-2">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="index.php?controller=admin&action=verifikasiUser&page=<?= $i ?>"
                            class="px-3 py-1 rounded <?= $i == $current_page ? 'bg-[#1e3a5f] text-white' : 'bg-gray-200 text-gray-700' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
    <script>
        function toggleMenu(id) {
            const menu = document.getElementById('menu-' + id);
            if (!menu) return;
            menu.classList.toggle('hidden');
        }

        // Tutup semua menu kalau klik di luar
        document.addEventListener('click', function(e) {
            document.querySelectorAll("[id^='menu-']").forEach(menu => {
                const btn = menu.previousElementSibling; // tombol titik tiga
                if (!menu.contains(e.target) && !btn.contains(e.target)) {
                    menu.classList.add('hidden');
                }
            });
        });
    </script>

</body>

</html>