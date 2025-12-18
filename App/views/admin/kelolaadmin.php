<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Admin | Kubooking</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/kubooking/public/src/output.css">
</head>

<body class="bg-[#f2f7fc] text-gray-800 flex">

    <?php
    // SIDEBAR
    $sidebarPath = __DIR__ . '/../layout/sidebar.php';
    if (file_exists($sidebarPath)) {
        include $sidebarPath;
    }
    ?>

    <div class="flex-1 flex flex-col h-screen overflow-y-auto">

        <?php
        // FLASH MESSAGE
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
        // Data dari controller (SuperAdminController)
        $admins       = $admins      ?? [];
        $currentPage  = isset($currentPage) ? (int)$currentPage : (int)($_GET['page'] ?? 1);
        if ($currentPage < 1) $currentPage = 1;
        $totalPages   = isset($totalPages) ? (int)$totalPages : 1;
        $statusFilter = $statusFilter ?? 'all';
        $search       = $search       ?? '';

        // Base URL untuk pagination & filter
        $pageBaseUrl = 'index.php?controller=superAdmin&action=kelolaAdmin'
            . '&status=' . urlencode($statusFilter)
            . '&q=' . urlencode($search);
        ?>

        <div class="px-4 sm:px-8 pb-10 space-y-6 max-w-6xl mx-auto w-full">

            <!-- HEADER -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-[#1e3a5f]">Kelola Admin</h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Tambah, ubah, dan kelola akun admin dan super admin.
                    </p>
                </div>

                <!-- TOMBOL TAMBAH ADMIN -->
                <a href="index.php?controller=superAdmin&action=createAdmin"
                    class="inline-flex items-center justify-center px-4 py-2 rounded-full text-sm font-semibold text-white bg-[#1e3a5f] hover:bg-[#163052] shadow">
                    Tambah Admin
                </a>
            </div>

            <!-- FILTER & SEARCH -->
            <form method="get"
                class="flex flex-col lg:flex-row lg:items-center lg:space-x-4 space-y-3 lg:space-y-0 mt-2">
                <input type="hidden" name="controller" value="superAdmin">
                <input type="hidden" name="action" value="kelolaAdmin">

                <!-- FILTER STATUS -->
                <select name="status"
                    class="bg-white border border-gray-300 rounded-full px-4 py-2 text-sm shadow w-full lg:w-auto">
                    <option value="all" <?= $statusFilter === 'all' ? 'selected' : '' ?>>Semua Status</option>
                    <option value="aktif" <?= $statusFilter === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                    <option value="nonaktif" <?= $statusFilter === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                </select>

                <!-- SEARCH -->
                <div class="flex flex-1 items-center bg-white rounded-full px-4 py-2 shadow border border-gray-200">
                    <input type="text"
                        name="q"
                        value="<?= htmlspecialchars($search) ?>"
                        placeholder="Cari nama, email, atau NIM/NIP admin"
                        class="flex-1 text-sm bg-transparent focus:outline-none">
                </div>

                <button type="submit"
                    class="w-10 h-10 rounded-full bg-[#1e3a5f] flex items-center justify-center text-white hover:bg-[#163052] transition">
                    🔍
                </button>
            </form>

            <!-- TABEL ADMIN -->
            <div class="mt-4">
                <!-- boleh tetap overflow-hidden, karena menu aksi tidak hidup di dalam card -->
                <div class="bg-white shadow rounded-lg border border-slate-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto text-sm">
                            <thead>
                                <tr class="bg-[#1e3a5f] text-white text-left text-xs uppercase tracking-wide">
                                    <th class="px-4 py-3">Nama</th>
                                    <th class="px-4 py-3">NIM / NIP</th>
                                    <th class="px-4 py-3">Email</th>
                                    <th class="px-4 py-3">Role</th>
                                    <th class="px-4 py-3">Status Akun</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (!empty($admins)): ?>
                                    <?php foreach ($admins as $i => $a): ?>
                                        <?php
                                        $id          = (int)($a['id_account'] ?? 0);
                                        $nama        = $a['nama']         ?? '-';
                                        $nimnip      = $a['nim_nip']      ?? '-';
                                        $email       = $a['email']        ?? '-';
                                        $role        = $a['role']         ?? '-';
                                        $statusAktif = $a['status_aktif'] ?? 'nonaktif';

                                        // Badge role
                                        $roleLabel = ucfirst($role);
                                        $roleBadgeClass = 'bg-slate-100 text-slate-700';
                                        if ($role === 'admin') {
                                            $roleBadgeClass = 'bg-blue-100 text-blue-800';
                                        } elseif ($role === 'super_admin') {
                                            $roleBadgeClass = 'bg-purple-100 text-purple-800';
                                            $roleLabel = 'Super Admin';
                                        }

                                        // Badge status
                                        $statusLabel = ($statusAktif === 'aktif') ? 'Aktif' : ucfirst($statusAktif);
                                        $statusBadgeClass = ($statusAktif === 'aktif')
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-gray-200 text-gray-700';

                                        // Row striping
                                        $rowBase = ($i % 2 === 0) ? 'bg-gray-50' : 'bg-gray-100';
                                        ?>
                                        <tr class="<?= $rowBase ?> text-gray-800 border-b last:border-b-0 align-top">
                                            <!-- NAMA -->
                                            <td class="px-4 py-3">
                                                <span class="font-medium text-slate-900">
                                                    <?= htmlspecialchars($nama) ?>
                                                </span>
                                            </td>

                                            <!-- NIM / NIP -->
                                            <td class="px-4 py-3">
                                                <span class="text-slate-800">
                                                    <?= htmlspecialchars($nimnip) ?>
                                                </span>
                                            </td>

                                            <!-- EMAIL -->
                                            <td class="px-4 py-3">
                                                <span class="text-slate-800">
                                                    <?= htmlspecialchars($email) ?>
                                                </span>
                                            </td>

                                            <!-- ROLE -->
                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold <?= $roleBadgeClass ?>">
                                                    <?= htmlspecialchars($roleLabel) ?>
                                                </span>
                                            </td>

                                            <!-- STATUS AKUN -->
                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold <?= $statusBadgeClass ?>">
                                                    <?= htmlspecialchars($statusLabel) ?>
                                                </span>
                                            </td>

                                            <!-- AKSI -->
                                            <td class="px-4 py-3 text-center">
                                                <button type="button"
                                                    class="inline-flex w-8 h-8 items-center justify-center rounded-full
                                                           hover:bg-gray-200 focus:outline-none focus:ring-2 
                                                           focus:ring-offset-2 focus:ring-slate-400"
                                                    onclick="openActionMenu(event, <?= $id ?>, '<?= htmlspecialchars($statusAktif, ENT_QUOTES) ?>')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                        fill="currentColor" class="w-4 h-4 text-gray-600">
                                                        <path d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 8.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM11.5 15a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0z" />
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                            <?php if ($statusFilter !== 'all' || $search !== ''): ?>
                                                Tidak ada admin yang cocok dengan filter / kata kunci.
                                            <?php else: ?>
                                                Belum ada data admin.
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- PAGINATION -->
                <?php if ($totalPages > 1 && !empty($admins)): ?>
                    <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                        <div class="text-slate-500">
                            Halaman <span class="font-semibold"><?= $currentPage ?></span> dari
                            <span class="font-semibold"><?= $totalPages ?></span>
                        </div>

                        <div class="flex items-center gap-1">
                            <!-- Prev -->
                            <?php if ($currentPage > 1): ?>
                                <a href="<?= $pageBaseUrl ?>&page=<?= $currentPage - 1 ?>"
                                    class="px-2.5 py-1 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-50">
                                    ‹ Sebelumnya
                                </a>
                            <?php else: ?>
                                <span class="px-2.5 py-1 rounded-lg border border-slate-100 text-slate-300 cursor-not-allowed">
                                    ‹ Sebelumnya
                                </span>
                            <?php endif; ?>

                            <!-- Numbers -->
                            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                                <?php if ($p == $currentPage): ?>
                                    <span class="px-3 py-1 rounded-lg bg-[#1e3a5f] text-white text-xs font-semibold">
                                        <?= $p ?>
                                    </span>
                                <?php else: ?>
                                    <a href="<?= $pageBaseUrl ?>&page=<?= $p ?>"
                                        class="px-3 py-1 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-50 text-xs">
                                        <?= $p ?>
                                    </a>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <!-- Next -->
                            <?php if ($currentPage < $totalPages): ?>
                                <a href="<?= $pageBaseUrl ?>&page=<?= $currentPage + 1 ?>"
                                    class="px-2.5 py-1 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-50">
                                    Berikutnya ›
                                </a>
                            <?php else: ?>
                                <span class="px-2.5 py-1 rounded-lg border border-slate-100 text-slate-300 cursor-not-allowed">
                                    Berikutnya ›
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

        </div>
    </div>

    <!-- GLOBAL ACTION MENU (floating, tidak ketutupan overflow table/card) -->
    <div id="actionMenu"
        class="hidden fixed z-[9999] w-52 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
        <div class="py-1 text-sm text-gray-700" id="actionMenuContent"></div>
    </div>

    <script>
        const menuEl = document.getElementById('actionMenu');
        const contentEl = document.getElementById('actionMenuContent');
        let activeAdminId = null;

        function buildMenuHTML(id, statusAktif) {
            const detailUrl = `index.php?controller=superAdmin&action=detailAdmin&id=${id}`;
            const editUrl = `index.php?controller=superAdmin&action=editAdmin&id=${id}`;

            const toggleForm = (statusAktif === 'aktif') ?
                `
                <form action="index.php?controller=superAdmin&action=setAdminStatus"
                      method="POST"
                      onsubmit="return confirm('Nonaktifkan akun admin ini?');">
                    <input type="hidden" name="id_admin" value="${id}">
                    <input type="hidden" name="status" value="nonaktif">
                    <button type="submit"
                        class="w-full text-left px-4 py-2 hover:bg-red-50 text-red-600">
                        Nonaktifkan Akun
                    </button>
                </form>
                ` :
                `
                <form action="index.php?controller=superAdmin&action=setAdminStatus"
                      method="POST"
                      onsubmit="return confirm('Aktifkan kembali akun admin ini?');">
                    <input type="hidden" name="id_admin" value="${id}">
                    <input type="hidden" name="status" value="aktif">
                    <button type="submit"
                        class="w-full text-left px-4 py-2 hover:bg-emerald-50 text-emerald-700">
                        Aktifkan Akun
                    </button>
                </form>
                `;

            return `
                <a href="${detailUrl}" class="block px-4 py-2 hover:bg-gray-100">Detail Admin</a>
                <a href="${editUrl}" class="block px-4 py-2 hover:bg-gray-100">Edit</a>
                ${toggleForm}
                <form action="index.php?controller=superAdmin&action=deleteAdmin"
                      method="POST"
                      onsubmit="return confirm('Yakin ingin menghapus akun admin ini?');">
                    <input type="hidden" name="id_admin" value="${id}">
                    <button type="submit"
                        class="w-full text-left px-4 py-2 border-t hover:bg-red-50 text-red-600">
                        Hapus
                    </button>
                </form>
            `;
        }

        function openActionMenu(e, id, statusAktif) {
            e.stopPropagation();

            // toggle: klik tombol yang sama lagi -> tutup
            if (!menuEl.classList.contains('hidden') && activeAdminId === id) {
                closeActionMenu();
                return;
            }

            activeAdminId = id;
            contentEl.innerHTML = buildMenuHTML(id, statusAktif);

            const btnRect = e.currentTarget.getBoundingClientRect();
            const menuWidth = 208; // w-52

            let left = btnRect.right - menuWidth;
            let top = btnRect.bottom + 8;

            left = Math.max(8, Math.min(left, window.innerWidth - menuWidth - 8));

            const estimatedHeight = 190;
            if (top + estimatedHeight > window.innerHeight - 8) {
                top = btnRect.top - estimatedHeight - 8;
            }
            top = Math.max(8, top);

            menuEl.style.left = left + 'px';
            menuEl.style.top = top + 'px';

            menuEl.classList.remove('hidden');
        }

        function closeActionMenu() {
            menuEl.classList.add('hidden');
            activeAdminId = null;
        }

        // klik di luar = tutup
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#actionMenu')) closeActionMenu();
        });

        // scroll/resize = tutup (biar posisinya nggak nyasar)
        window.addEventListener('scroll', closeActionMenu, true);
        window.addEventListener('resize', closeActionMenu);

        // ESC = tutup
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeActionMenu();
        });
    </script>
</body>

</html>