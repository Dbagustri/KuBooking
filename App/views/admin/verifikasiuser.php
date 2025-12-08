<?php
// Pastikan variabel ada biar nggak notice
$list         = $list         ?? [];
$current_page = $current_page ?? 1;
$total_pages  = $total_pages  ?? 1;
$filter       = $filter       ?? '';
$search       = $search       ?? '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Verifikasi User | Kubooking</title>
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

    <!-- CONTENT -->
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

            <h1 class="text-2xl font-bold text-[#1e3a5f]">Verifikasi User</h1>

            <!-- FILTER & SEARCH -->
            <div class="flex flex-col md:flex-row md:space-x-4 space-y-3 md:space-y-0">

                <!-- FILTER (status pending/rejected, tanpa search) -->
                <form class="flex items-center space-x-2" method="get">
                    <input type="hidden" name="controller" value="admin">
                    <input type="hidden" name="action" value="verifikasiUser">
                    <input type="hidden" name="mode" value="filter">

                    <select name="filter"
                        class="px-4 py-2 rounded-lg bg-white border shadow text-sm">
                        <option value="">Semua</option>
                        <option value="pending" <?= $filter === 'pending'  ? 'selected' : '' ?>>Pending</option>
                        <option value="rejected" <?= $filter === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                    </select>

                    <button type="submit"
                        class="px-3 py-2 bg-[#1e3a5f] text-white rounded-lg text-sm">
                        Terapkan
                    </button>
                </form>

                <!-- SEARCH (nama/email, tanpa filter status spesifik) -->
                <form class="flex flex-1 items-center" method="get">
                    <input type="hidden" name="controller" value="admin">
                    <input type="hidden" name="action" value="verifikasiUser">
                    <input type="hidden" name="mode" value="search">

                    <input type="text" name="q" placeholder="Cari nama / email..."
                        value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>"
                        class="flex-1 px-4 py-2 rounded-l-full bg-white border shadow text-sm">

                    <button type="submit"
                        class="px-4 py-2 bg-[#1e3a5f] text-white rounded-r-full text-sm">
                        🔍
                    </button>
                </form>

            </div>

            <!-- INFO RANGE DATA (opsional, kalau mau ditambahin nanti) -->
            <!-- <p class="text-xs text-gray-500">Menampilkan user dengan status pending / rejected saja.</p> -->

            <!-- TABLE -->
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse bg-white shadow rounded-lg overflow-hidden">
                    <thead>
                        <tr class="bg-[#1e3a5f] text-white text-left text-sm">
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Jurusan / Unit</th>
                            <th class="px-4 py-3">Role</th>
                            <th class="px-4 py-3">Screenshot</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (!empty($list)): ?>
                            <?php foreach ($list as $i => $u): ?>
                                <?php
                                $rowId   = (int)($u['id_registrasi'] ?? 0);
                                $status  = $u['status'] ?? 'pending';
                                $role    = $u['role_registrasi'] ?? '-';

                                // Badge warna status
                                $badgeClass = 'bg-yellow-100 text-yellow-700';
                                if ($status === 'approved') {
                                    $badgeClass = 'bg-green-100 text-green-700';
                                } elseif ($status === 'rejected') {
                                    $badgeClass = 'bg-red-100 text-red-700';
                                }

                                // Hanya pending/rejected yang idealnya muncul dari model
                                $isFinal = in_array($status, ['approved', 'rejected'], true);
                                ?>
                                <tr class="<?= $i % 2 === 0 ? 'bg-gray-50' : 'bg-gray-100' ?> text-sm">
                                    <td class="px-4 py-3">
                                        <?= htmlspecialchars($u['nama'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                    <td class="px-4 py-3">
                                        <?= htmlspecialchars($u['email'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                    <td class="px-4 py-3">
                                        <?php
                                        $jurusan = $u['jurusan'] ?? '';
                                        $unit    = $u['unit_jurusan'] ?? '';
                                        echo htmlspecialchars($jurusan ?: $unit ?: '-', ENT_QUOTES, 'UTF-8');
                                        ?>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded-full bg-gray-100 text-xs font-medium text-gray-700">
                                            <?= htmlspecialchars(ucfirst($role), ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <?php if (!empty($u['screenshot_kubaca'])): ?>
                                            <a href="<?= htmlspecialchars($u['screenshot_kubaca'], ENT_QUOTES, 'UTF-8') ?>"
                                                target="_blank"
                                                class="text-blue-600 hover:underline text-xs">
                                                lihat bukti
                                            </a>
                                        <?php else: ?>
                                            <span class="text-gray-400 text-xs">tidak ada</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $badgeClass ?>">
                                            <?= htmlspecialchars(ucfirst($status), ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        <div class="relative inline-block text-left">
                                            <!-- Tombol titik tiga -->
                                            <button type="button"
                                                onclick="toggleMenu(<?= $rowId ?>)"
                                                class="inline-flex justify-center w-8 h-8 rounded-full hover:bg-gray-200 text-xl leading-none">
                                                &#8226;&#8226;&#8226;
                                            </button>

                                            <!-- Dropdown menu -->
                                            <div id="menu-<?= $rowId ?>"
                                                class="hidden origin-top-right absolute right-0 mt-2 w-44 rounded-md shadow-lg bg-white ring-1 ring-black/5 z-20 text-left">

                                                <!-- DETAIL -->
                                                <a href="index.php?controller=admin&action=detailRegistrasi&id=<?= $rowId ?>"
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
                                <td colspan="7" class="px-4 py-6 text-center text-gray-500 text-sm">
                                    Tidak ada data registrasi (pending / rejected).
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <?php if ($total_pages > 1): ?>
                <div class="flex justify-center mt-4 space-x-2">
                    <?php
                    // susun base url sesuai mode (search / filter)
                    $baseParams = [
                        'controller' => 'admin',
                        'action'     => 'verifikasiUser',
                    ];

                    if (!empty($search)) {
                        $baseParams['mode'] = 'search';
                        $baseParams['q']    = $search;
                    } elseif (!empty($filter)) {
                        $baseParams['mode']   = 'filter';
                        $baseParams['filter'] = $filter;
                    }

                    $baseUrl = 'index.php?' . http_build_query($baseParams);
                    ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="<?= $baseUrl . '&page=' . $i ?>"
                            class="px-3 py-1 rounded text-sm
                                <?= $i == $current_page
                                    ? 'bg-[#1e3a5f] text-white'
                                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?>">
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