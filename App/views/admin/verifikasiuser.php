<?php
// Pastikan variabel ada biar nggak notice
$list         = $list         ?? [];
$current_page = (int)($current_page ?? 1);
$total_pages  = (int)($total_pages ?? 1);
$filter       = (string)($filter ?? 'pending'); // default sesuai controller kamu
$search       = (string)($search ?? '');

if ($current_page < 1) $current_page = 1;
if ($total_pages < 1)  $total_pages  = 1;
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

        <div class="px-8 pb-10 space-y-6">
            <h1 class="text-2xl font-bold text-[#1e3a5f]">Verifikasi User</h1>

            <!-- FILTER & SEARCH (satu form biar selaras) -->
            <form id="verifForm" method="get" action="index.php"
                class="flex flex-col md:flex-row md:items-center md:space-x-4 space-y-3 md:space-y-0">

                <input type="hidden" name="controller" value="admin">
                <input type="hidden" name="action" value="verifikasiUser">
                <input type="hidden" name="page" value="1"> <!-- reset saat filter/search submit -->

                <!-- FILTER (auto submit) -->
                <div class="flex items-center space-x-2">
                    <select name="filter" id="filterSelect"
                        class="px-4 py-2 rounded-lg bg-white border shadow text-sm">
                        <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>Semua</option>
                        <option value="pending" <?= $filter === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="rejected" <?= $filter === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                        <option value="approved" <?= $filter === 'approved' ? 'selected' : '' ?>>Approved</option>
                    </select>
                </div>

                <!-- SEARCH (butuh tombol) -->
                <div class="flex flex-1 items-center">
                    <input type="text" name="q" placeholder="Cari nama / email..."
                        value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>"
                        class="flex-1 px-4 py-2 rounded-l-full bg-white border shadow text-sm">

                    <button type="submit"
                        class="px-4 py-2 bg-[#1e3a5f] text-white rounded-r-full text-sm">
                        🔍
                    </button>
                </div>
            </form>

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

                                $badgeClass = 'bg-yellow-100 text-yellow-700';
                                if ($status === 'approved') {
                                    $badgeClass = 'bg-green-100 text-green-700';
                                } elseif ($status === 'rejected') {
                                    $badgeClass = 'bg-red-100 text-red-700';
                                }

                                // kalau status final, disable approve/reject
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
                                            <button type="button"
                                                onclick="toggleMenu(<?= $rowId ?>)"
                                                class="inline-flex justify-center w-8 h-8 rounded-full hover:bg-gray-200 text-xl leading-none">
                                                &#8226;&#8226;&#8226;
                                            </button>

                                            <div id="menu-<?= $rowId ?>"
                                                class="hidden origin-top-right absolute right-0 mt-2 w-44 rounded-md shadow-lg bg-white ring-1 ring-black/5 z-20 text-left">

                                                <a href="index.php?controller=admin&action=detailRegistrasi&id=<?= $rowId ?>"
                                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    Detail
                                                </a>

                                                <form action="index.php?controller=admin&action=approveUser" method="POST">
                                                    <input type="hidden" name="id_registrasi" value="<?= $rowId ?>">
                                                    <button type="submit"
                                                        <?= $isFinal ? 'disabled' : '' ?>
                                                        class="w-full text-left px-4 py-2 text-sm
                                                        <?= $isFinal ? 'text-gray-400 cursor-not-allowed opacity-50' : 'text-green-700 hover:bg-green-50' ?>">
                                                        Approve
                                                    </button>
                                                </form>

                                                <form action="index.php?controller=admin&action=rejectUser" method="POST"
                                                    onsubmit="return confirm('Yakin ingin menolak user ini?');">
                                                    <input type="hidden" name="id_registrasi" value="<?= $rowId ?>">
                                                    <button type="submit"
                                                        <?= $isFinal ? 'disabled' : '' ?>
                                                        class="w-full text-left px-4 py-2 text-sm border-t
                                                        <?= $isFinal ? 'text-gray-400 cursor-not-allowed opacity-50' : 'text-red-700 hover:bg-red-50' ?>">
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
                                    Tidak ada data registrasi.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION (pakai komponen yang kamu punya) -->
            <?php
            $pagination = [
                'pageKey'     => 'page',
                'currentPage' => (int)$current_page,
                'totalPages'  => (int)$total_pages,
                'params'      => [
                    'controller' => 'admin',
                    'action'     => 'verifikasiUser',
                    'filter'     => $filter,
                    'q'          => $search,
                ],
            ];
            include __DIR__ . '/../layout/pagination.php';
            ?>

        </div>
    </div>

    <script>
        // Filter auto jalan, search tetap lewat tombol submit
        document.getElementById('filterSelect')?.addEventListener('change', function() {
            const form = document.getElementById('verifForm');
            const pageInput = form?.querySelector('input[name="page"]');
            if (pageInput) pageInput.value = '1';
            form?.submit();
        });

        function toggleMenu(id) {
            const menu = document.getElementById('menu-' + id);
            if (!menu) return;
            menu.classList.toggle('hidden');
        }

        document.addEventListener('click', function(e) {
            document.querySelectorAll("[id^='menu-']").forEach(menu => {
                const btn = menu.previousElementSibling;
                if (!menu.contains(e.target) && !btn.contains(e.target)) {
                    menu.classList.add('hidden');
                }
            });
        });
    </script>

</body>

</html>