<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Anggota | Kubooking</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        $users       = $users       ?? [];
        $currentPage = isset($currentPage) ? (int)$currentPage : (int)($_GET['page'] ?? 1);
        if ($currentPage < 1) $currentPage = 1;
        $totalPages  = isset($totalPages) ? (int)$totalPages : 1;

        $filter = $filter ?? ($_GET['filter'] ?? 'all');
        $search = $search ?? (trim($_GET['q'] ?? ''));
        ?>

        <div class="px-4 sm:px-8 pb-10 space-y-6 max-w-6xl mx-auto w-full">

            <!-- HEADER -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-[#1e3a5f]">Kelola Anggota</h1>
                </div>

                <a href="index.php?controller=admin&action=tambahAnggota"
                    class="inline-flex items-center justify-center px-4 py-2 rounded-full text-sm font-semibold text-white bg-[#1e3a5f] hover:bg-[#163052] shadow">
                    Tambah Anggota
                </a>
            </div>

            <!-- FILTER & SEARCH -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:space-x-4 space-y-3 lg:space-y-0 mt-2">

                <!-- FILTER ROLE (auto submit, tanpa tombol) -->
                <form id="filterForm" method="get" class="w-full lg:w-auto">
                    <input type="hidden" name="controller" value="admin">
                    <input type="hidden" name="action" value="anggota">
                    <input type="hidden" name="q" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="page" value="1">

                    <select name="filter"
                        onchange="document.getElementById('filterForm').submit()"
                        class="bg-white border border-gray-300 rounded-full px-4 py-2 text-sm shadow w-full lg:w-auto">
                        <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>Semua Role</option>
                        <option value="mahasiswa" <?= $filter === 'mahasiswa' ? 'selected' : '' ?>>Mahasiswa</option>
                        <option value="dosen" <?= $filter === 'dosen' ? 'selected' : '' ?>>Dosen</option>
                        <option value="tendik" <?= $filter === 'tendik' ? 'selected' : '' ?>>Tendik</option>
                    </select>
                </form>

                <!-- SEARCH (butuh tombol submit) -->
                <form method="get" class="flex flex-1 items-center">
                    <input type="hidden" name="controller" value="admin">
                    <input type="hidden" name="action" value="anggota">
                    <input type="hidden" name="filter" value="<?= htmlspecialchars($filter, ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="page" value="1">

                    <div class="flex flex-1 items-center bg-white rounded-full px-4 py-2 shadow border border-gray-200">
                        <input type="text"
                            name="q"
                            value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="Cari nama, email, NIM/NIP, jurusan, atau unit"
                            class="flex-1 text-sm bg-transparent focus:outline-none">
                    </div>

                    <button type="submit"
                        class="ml-2 w-10 h-10 rounded-full bg-[#1e3a5f] flex items-center justify-center text-white hover:bg-[#163052] transition">
                        🔍
                    </button>
                </form>
            </div>

            <!-- TABEL ANGGOTA -->
            <div class="mt-4">
                <div class="bg-white shadow rounded-lg border border-slate-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto text-sm">
                            <thead>
                                <tr class="bg-[#1e3a5f] text-white text-left text-xs uppercase tracking-wide">
                                    <th class="px-4 py-3">Nama</th>
                                    <th class="px-4 py-3">NIM / NIP</th>
                                    <th class="px-4 py-3">Email</th>
                                    <th class="px-4 py-3">Jurusan / Unit</th>
                                    <th class="px-4 py-3">Role</th>
                                    <th class="px-4 py-3">Status Akun</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (!empty($users)): ?>
                                    <?php foreach ($users as $i => $u): ?>
                                        <?php
                                        $id          = (int)($u['id_account'] ?? 0);
                                        $nama        = $u['nama']          ?? '-';
                                        $nimnip      = $u['nim_nip']       ?? '-';
                                        $email       = $u['email']         ?? '-';
                                        $jurusan     = $u['jurusan']       ?? '';
                                        $prodi       = $u['prodi']         ?? '';
                                        $unitJurusan = $u['unit_jurusan']  ?? '';
                                        $role        = $u['role']          ?? '-';
                                        $statusAktif = $u['status_aktif']  ?? 'nonaktif';

                                        $jurusanText = '-';
                                        if ($role === 'mahasiswa') {
                                            $jurusanText = trim($jurusan . ' - ' . $prodi);
                                        } elseif ($role === 'dosen') {
                                            $jurusanText = $jurusan ?: '-';
                                        } elseif ($role === 'tendik') {
                                            $jurusanText = $unitJurusan ?: '-';
                                        }

                                        $roleLabel = ucfirst($role);
                                        $roleBadgeClass = 'bg-slate-100 text-slate-700';
                                        if ($role === 'mahasiswa') {
                                            $roleBadgeClass = 'bg-blue-100 text-blue-800';
                                        } elseif ($role === 'dosen') {
                                            $roleBadgeClass = 'bg-emerald-100 text-emerald-800';
                                        } elseif ($role === 'tendik') {
                                            $roleBadgeClass = 'bg-amber-100 text-amber-800';
                                        }

                                        $statusLabel = ($statusAktif === 'aktif') ? 'Aktif' : ucfirst($statusAktif);
                                        $statusBadgeClass = ($statusAktif === 'aktif')
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-gray-200 text-gray-700';

                                        $rowBase = ($i % 2 === 0) ? 'bg-gray-50' : 'bg-gray-100';
                                        ?>
                                        <tr class="<?= $rowBase ?> text-gray-800 border-b last:border-b-0 align-top">
                                            <td class="px-4 py-3">
                                                <span class="font-medium text-slate-900">
                                                    <?= htmlspecialchars($nama) ?>
                                                </span>
                                            </td>

                                            <td class="px-4 py-3"><?= htmlspecialchars($nimnip) ?></td>
                                            <td class="px-4 py-3"><?= htmlspecialchars($email) ?></td>

                                            <td class="px-4 py-3">
                                                <span class="text-slate-800 text-xs">
                                                    <?= htmlspecialchars($jurusanText) ?>
                                                </span>
                                            </td>

                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold <?= $roleBadgeClass ?>">
                                                    <?= htmlspecialchars($roleLabel) ?>
                                                </span>
                                            </td>

                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold <?= $statusBadgeClass ?>">
                                                    <?= htmlspecialchars($statusLabel) ?>
                                                </span>
                                            </td>

                                            <td class="px-4 py-3 text-center">
                                                <div class="relative inline-block text-left">
                                                    <button type="button"
                                                        class="inline-flex w-8 h-8 items-center justify-center rounded-full
                                                                   hover:bg-gray-200 focus:outline-none focus:ring-2 
                                                                   focus:ring-offset-2 focus:ring-slate-400"
                                                        onclick="toggleMenu(this)">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                            fill="currentColor" class="w-4 h-4 text-gray-600">
                                                            <path d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 8.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM11.5 15a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0z" />
                                                        </svg>
                                                    </button>

                                                    <div class="menu-panel hidden origin-top-right absolute right-0 mt-2 w-52 rounded-md shadow-lg
                                                                bg-white ring-1 ring-black ring-opacity-5 z-50">
                                                        <div class="py-1 text-sm text-gray-700">

                                                            <a href="index.php?controller=admin&action=detailUser&id=<?= $id ?>"
                                                                class="block px-4 py-2 hover:bg-gray-100">
                                                                Detail Anggota
                                                            </a>

                                                            <a href="index.php?controller=admin&action=editUser&id=<?= $id ?>"
                                                                class="block px-4 py-2 hover:bg-gray-100">
                                                                Edit
                                                            </a>

                                                            <?php if ($statusAktif === 'aktif'): ?>
                                                                <form action="index.php?controller=admin&action=setUserStatus"
                                                                    method="POST"
                                                                    onsubmit="return confirm('Nonaktifkan / suspend akun ini?');">
                                                                    <input type="hidden" name="id_user" value="<?= $id ?>">
                                                                    <input type="hidden" name="status" value="nonaktif">
                                                                    <button type="submit"
                                                                        class="w-full text-left px-4 py-2 hover:bg-red-50 text-red-600">
                                                                        Nonaktifkan Akun
                                                                    </button>
                                                                </form>
                                                            <?php else: ?>
                                                                <form action="index.php?controller=admin&action=setUserStatus"
                                                                    method="POST"
                                                                    onsubmit="return confirm('Aktifkan kembali akun ini?');">
                                                                    <input type="hidden" name="id_user" value="<?= $id ?>">
                                                                    <input type="hidden" name="status" value="aktif">
                                                                    <button type="submit"
                                                                        class="w-full text-left px-4 py-2 hover:bg-emerald-50 text-emerald-700">
                                                                        Aktifkan Akun
                                                                    </button>
                                                                </form>
                                                            <?php endif; ?>

                                                            <form action="index.php?controller=admin&action=deleteUser"
                                                                method="POST"
                                                                onsubmit="return confirm('Yakin ingin menghapus akun ini?');">
                                                                <input type="hidden" name="id_user" value="<?= $id ?>">
                                                                <button type="submit"
                                                                    class="w-full text-left px-4 py-2 border-t hover:bg-red-50 text-red-600">
                                                                    Hapus
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>

                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                            <?php if ($filter !== 'all' || $search !== ''): ?>
                                                Tidak ada anggota yang cocok dengan filter / kata kunci.
                                            <?php else: ?>
                                                Belum ada data anggota.
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- PAGINATION (pakai komponen pagination.php biar seragam) -->
                <?php
                if ((int)$totalPages > 1) {
                    $pagination = [
                        'pageKey'     => 'page',
                        'currentPage' => (int)$currentPage,
                        'totalPages'  => (int)$totalPages,
                        'params'      => [
                            'controller' => 'admin',
                            'action'     => 'anggota',
                            'filter'     => $filter,
                            'q'          => $search,
                        ],
                    ];
                    include __DIR__ . '/../layout/pagination.php';
                }
                ?>
            </div>

        </div>
    </div>

    <script>
        function toggleMenu(btn) {
            const panel = btn.parentElement.querySelector('.menu-panel');
            const all = document.querySelectorAll('.menu-panel');
            all.forEach(p => {
                if (p !== panel) p.classList.add('hidden');
            });
            if (panel) panel.classList.toggle('hidden');
        }

        document.addEventListener('click', function(e) {
            const isButton = e.target.closest('button[onclick="toggleMenu(this)"]');
            const isMenu = e.target.closest('.menu-panel');
            if (!isButton && !isMenu) {
                document.querySelectorAll('.menu-panel').forEach(p => p.classList.add('hidden'));
            }
        });
    </script>
</body>

</html>