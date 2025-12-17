<?php
// =================== SAFE DEFAULTS (JANGAN STUCK) ===================
$list        = $list ?? [];
$total_pages = isset($total_pages) ? (int)$total_pages : 1;
if ($total_pages < 1) $total_pages = 1;

// filter & search (prioritas dari controller, fallback GET)
$filter = isset($filter) ? (string)$filter : (string)($_GET['filter'] ?? 'pending');
$search = isset($search) ? (string)$search : (string)trim($_GET['q'] ?? '');

// ✅ INI KUNCI: current page selalu ikut URL (?page=...)
$current_page = max(1, (int)($_GET['page'] ?? ($current_page ?? 1)));
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Verifikasi User | Kubooking</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#f2f7fc] text-gray-800 flex">

    <!-- SIDEBAR -->
    <?php
    $sidebarPath = __DIR__ . '/../layout/sidebar.php';
    if (file_exists($sidebarPath)) include $sidebarPath;
    ?>

    <!-- CONTENT -->
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

        <div class="px-8 pb-10 space-y-6">
            <h1 class="text-2xl font-bold text-[#1e3a5f]">Verifikasi User</h1>

            <!-- FILTER & SEARCH -->
            <form id="verifForm" method="get" action="index.php"
                class="flex flex-col lg:flex-row lg:items-center gap-4">

                <input type="hidden" name="controller" value="admin">
                <input type="hidden" name="action" value="verifikasiUser">
                <input type="hidden" name="page" value="1">

                <!-- FILTER (pill) -->
                <div class="w-full lg:w-[260px]">
                    <select name="filter" id="filterSelect"
                        class="w-full px-5 py-3 rounded-full bg-white border border-slate-200 shadow-sm
                       text-sm text-slate-700 outline-none
                       focus:ring-2 focus:ring-slate-200">
                        <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>Semua Status</option>
                        <option value="pending" <?= $filter === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="rejected" <?= $filter === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                        <option value="approved" <?= $filter === 'approved' ? 'selected' : '' ?>>Approved</option>
                    </select>
                </div>

                <!-- SEARCH (pill panjang + tombol bulat) -->
                <div class="w-full flex items-center gap-3">
                    <div class="flex-1">
                        <input type="text" name="q"
                            placeholder="Cari kode booking, nama PJ, ruangan, atau instansi"
                            value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>"
                            class="w-full px-6 py-3 rounded-full bg-white border border-slate-200 shadow-sm
                          text-sm text-slate-700 placeholder:text-slate-400 outline-none
                          focus:ring-2 focus:ring-slate-200">
                    </div>

                    <button type="submit"
                        class="w-12 h-12 rounded-full bg-[#0b2a4a] text-white shadow-sm
                       hover:opacity-95 active:scale-95 transition flex items-center justify-center"
                        aria-label="Search">
                        <!-- icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-4.35-4.35m1.35-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </div>
            </form>


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
                                $rowId  = (int)($u['id_registrasi'] ?? 0);
                                $status = $u['status'] ?? 'pending';

                                // =================== ROLE BADGE (CONSISTENT) ===================
                                $roleRaw = $u['role_registrasi'] ?? '-';
                                $role    = strtolower(trim((string)$roleRaw));

                                $roleBadgeClass = 'bg-slate-100 text-slate-700';
                                if ($role === 'mahasiswa') {
                                    $roleBadgeClass = 'bg-blue-100 text-blue-800';
                                } elseif ($role === 'dosen') {
                                    $roleBadgeClass = 'bg-emerald-100 text-emerald-800';
                                } elseif ($role === 'tendik') {
                                    $roleBadgeClass = 'bg-amber-100 text-amber-800';
                                }

                                $roleLabel = ucfirst($role);
                                if ($role === 'mahasiswa') {
                                    $roleLabel = 'Mahasiswa';
                                } elseif ($role === 'dosen') {
                                    $roleLabel = 'Dosen';
                                } elseif ($role === 'tendik') {
                                    $roleLabel = 'Tendik';
                                } elseif ($role === '-' || $role === '') {
                                    $roleLabel = '-';
                                }

                                // =================== STATUS BADGE ===================
                                $badgeClass = 'bg-yellow-100 text-yellow-700';
                                if ($status === 'approved') {
                                    $badgeClass = 'bg-green-100 text-green-700';
                                } elseif ($status === 'rejected') {
                                    $badgeClass = 'bg-red-100 text-red-700';
                                }

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

                                    <!-- ✅ ROLE BADGE (UPDATED) -->
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold <?= $roleBadgeClass ?>">
                                            <?= htmlspecialchars($roleLabel, ENT_QUOTES, 'UTF-8') ?>
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

            <!-- PAGINATION (layout/pagination.php kamu TETAP) -->
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