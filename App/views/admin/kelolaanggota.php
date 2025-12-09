<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola User | Kubooking</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#f2f7fc] text-gray-800 flex">

    <!-- SIDEBAR -->
    <?php
    $sidebarPath = __DIR__ . '/../layout/sidebar.php';
    if (file_exists($sidebarPath)) {
        include $sidebarPath;
    }

    // data dari controller (AdminController::anggota)
    $users        = $users        ?? [];
    $currentPage  = $current_page ?? 1;
    $totalPages   = $total_pages  ?? 1;
    $filter       = $filter       ?? '';
    $search       = $search       ?? '';
    $currentLogin = \App\Core\Auth::user() ?? [];
    ?>

    <div class="flex-1 flex flex-col h-screen overflow-y-auto">
        <div class="m-4">
            <?php
            $navPath = __DIR__ . '/../layout/nav-admin.php';
            if (file_exists($navPath)) {
                include $navPath;
            }
            ?>
        </div>

        <div class="px-8 pb-10 space-y-6">
            <div>
                <h1 class="text-2xl font-bold text-[#1e3a5f]">Kelola User</h1>
                <p class="text-sm text-gray-600 mt-1">
                    Kelola akun anggota perpustakaan: ubah profil, atur status aktif, dan hapus akun jika diperlukan.
                </p>
            </div>

            <form method="get"
                class="flex flex-col lg:flex-row lg:items-end lg:space-x-4 space-y-3 lg:space-y-0">

                <input type="hidden" name="controller" value="admin">
                <input type="hidden" name="action" value="anggota">

                <div class="flex flex-col">
                    <label class="text-xs font-medium text-gray-600 mb-1">Filter role</label>
                    <select name="filter"
                        class="bg-white border border-gray-300 rounded-full px-4 py-2 text-sm shadow">
                        <option value="">Semua Role</option>
                        <option value="mahasiswa" <?= $filter === 'mahasiswa'   ? 'selected' : '' ?>>Mahasiswa</option>
                        <option value="dosen" <?= $filter === 'dosen'       ? 'selected' : '' ?>>Dosen</option>
                        <option value="tendik" <?= $filter === 'tendik'      ? 'selected' : '' ?>>Tendik</option>
                        <option value="admin" <?= $filter === 'admin'       ? 'selected' : '' ?>>Admin</option>
                        <option value="super_admin" <?= $filter === 'super_admin' ? 'selected' : '' ?>>Super Admin</option>
                    </select>
                </div>

                <!-- SEARCH -->
                <div class="flex-1 flex flex-col">
                    <label class="text-xs font-medium text-gray-600 mb-1">Cari user</label>
                    <div class="flex items-center bg-white rounded-full px-4 py-2 shadow border border-gray-200">
                        <input type="text"
                            name="q"
                            value="<?= htmlspecialchars($search) ?>"
                            placeholder="Cari berdasarkan nama atau email"
                            class="flex-1 text-sm bg-transparent focus:outline-none">
                    </div>
                </div>
                <!-- BUTTON -->
                <button type="submit"
                    class="mt-1 lg:mt-0 w-full lg:w-28 rounded-full bg-[#1e3a5f] px-4 py-2
                               flex items-center justify-center text-white text-sm font-medium
                               hover:bg-[#163052] transition">
                    <span class="mr-1">Cari</span> 🔍
                </button>
            </form>

            <!-- TABEL USER -->
            <div class="overflow-x-auto mt-4">
                <table class="min-w-full border-collapse bg-white shadow rounded-lg overflow-hidden">
                    <thead>
                        <tr class="bg-[#1e3a5f] text-white text-left text-sm">
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">NIM / NIP</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Jurusan / Unit</th>
                            <th class="px-4 py-3">Role</th>
                            <th class="px-4 py-3">Screenshot</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php
                            // mapping class role sekali saja
                            $roleBadgeMap = [
                                'mahasiswa'   => 'bg-blue-50 text-blue-700',
                                'dosen'       => 'bg-purple-50 text-purple-700',
                                'tendik'      => 'bg-amber-50 text-amber-700',
                                'admin'       => 'bg-slate-100 text-slate-800',
                                'super_admin' => 'bg-rose-50 text-rose-700',
                            ];

                            $statusBadgeMap = [
                                'aktif'    => 'bg-green-100 text-green-800',
                                'nonaktif' => 'bg-red-100 text-red-800',
                            ];

                            $currentLoginId   = (int)($currentLogin['id_account'] ?? 0);
                            $currentLoginRole = $currentLogin['role'] ?? null;
                            ?>
                            <?php foreach ($users as $i => $u): ?>
                                <?php
                                $id      = (int)($u['id_account'] ?? 0);
                                $status  = $u['status_aktif'] ?? 'nonaktif';
                                $jurusan = $u['jurusan'] ?? ($u['unit_jurusan'] ?? '-');
                                $nimNip  = $u['nim_nip'] ?? '-';
                                $role    = $u['role'] ?? '-';

                                $badgeClass = $statusBadgeMap[$status] ?? 'bg-gray-100 text-gray-800';
                                $roleClass  = $roleBadgeMap[$role]    ?? 'bg-gray-100 text-gray-700';

                                $isSelf = $id === $currentLoginId;
                                ?>
                                <tr class="<?= $i % 2 === 0 ? 'bg-gray-50' : 'bg-gray-100'; ?> text-sm text-gray-800 border-b">

                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <?= htmlspecialchars($u['nama'] ?? '-') ?>
                                    </td>

                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <?= htmlspecialchars($nimNip) ?>
                                    </td>

                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <?= htmlspecialchars($u['email'] ?? '-') ?>
                                    </td>

                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <?= htmlspecialchars($jurusan) ?>
                                    </td>

                                    <td class="px-4 py-3">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium <?= $roleClass ?>">
                                            <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $role))) ?>
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 text-blue-600 hover:underline text-sm">
                                        <?php if (!empty($u['screenshot_kubaca'])): ?>
                                            <a href="<?= htmlspecialchars($u['screenshot_kubaca']) ?>" target="_blank">
                                                Lihat bukti
                                            </a>
                                        <?php else: ?>
                                            <span class="text-gray-400">Tidak ada</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="px-4 py-3">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $badgeClass ?>">
                                            <?= htmlspecialchars(ucfirst($status)) ?>
                                        </span>
                                    </td>

                                    <!-- Aksi: Edit / Aktif/nonaktif / Delete (menu titik tiga) -->
                                    <td class="px-4 py-3 text-center">
                                        <div class="relative inline-block text-left">
                                            <button type="button"
                                                onclick="toggleMenu('user-<?= $id ?>')"
                                                class="inline-flex justify-center w-8 h-8 rounded-full hover:bg-gray-200 text-xl leading-none"
                                                aria-haspopup="true"
                                                aria-expanded="false">
                                                &#8226;&#8226;&#8226;
                                            </button>

                                            <div id="user-<?= $id ?>"
                                                class="hidden origin-top-right absolute right-0 mt-2 w-48
                                                        rounded-md shadow-lg bg-white ring-1 ring-black/5
                                                        z-20 text-left text-sm">

                                                <!-- EDIT -->
                                                <a href="index.php?controller=admin&action=editUser&id=<?= $id ?>"
                                                    class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                                    Edit profil
                                                </a>

                                                <!-- DETAIL -->
                                                <a href="index.php?controller=admin&action=detailUser&id=<?= $id ?>"
                                                    class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                                    Detail
                                                </a>

                                                <?php if (!$isSelf): ?>
                                                    <!-- AKTIF / NONAKTIF -->
                                                    <?php if ($status === 'aktif'): ?>
                                                        <form action="index.php?controller=admin&action=setUserStatus"
                                                            method="POST"
                                                            onsubmit="return confirm('Nonaktifkan user ini? User tidak dapat melakukan booking.');">
                                                            <input type="hidden" name="id_user" value="<?= $id ?>">
                                                            <input type="hidden" name="status" value="nonaktif">
                                                            <button type="submit"
                                                                class="w-full text-left px-4 py-2 text-red-700 hover:bg-red-50">
                                                                Nonaktifkan
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <form action="index.php?controller=admin&action=setUserStatus"
                                                            method="POST"
                                                            onsubmit="return confirm('Aktifkan kembali user ini?');">
                                                            <input type="hidden" name="id_user" value="<?= $id ?>">
                                                            <input type="hidden" name="status" value="aktif">
                                                            <button type="submit"
                                                                class="w-full text-left px-4 py-2 text-green-700 hover:bg-green-50">
                                                                Aktifkan
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>

                                                    <!-- DELETE (jangan tampilkan kalau user yang login sendiri) -->
                                                    <form action="index.php?controller=admin&action=deleteUser"
                                                        method="POST"
                                                        onsubmit="return confirm('Yakin ingin menghapus user ini? Tindakan ini tidak dapat dibatalkan.');">
                                                        <input type="hidden" name="id_user" value="<?= $id ?>">
                                                        <button type="submit"
                                                            class="w-full text-left px-4 py-2 border-t
                                                                       text-red-700 hover:bg-red-50">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                <?php endif; ?>

                                            </div>
                                        </div>
                                    </td>
                                </tr>

                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="px-4 py-10 text-center text-gray-500 text-sm">
                                    Tidak ada user yang cocok dengan filter saat ini.<br>
                                    Coba ubah kata kunci atau filter role.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <?php if ($totalPages > 1): ?>
                <div class="flex justify-center mt-6 space-x-1 text-sm">
                    <!-- Prev -->
                    <?php if ($currentPage > 1): ?>
                        <a href="index.php?controller=admin&action=anggota&page=<?= $currentPage - 1 ?>&filter=<?= urlencode($filter) ?>&q=<?= urlencode($search) ?>"
                            class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300 text-gray-700">&lt;</a>
                    <?php else: ?>
                        <span class="px-3 py-1 rounded bg-gray-100 text-gray-400 cursor-not-allowed">&lt;</span>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="index.php?controller=admin&action=anggota&page=<?= $i ?>&filter=<?= urlencode($filter) ?>&q=<?= urlencode($search) ?>"
                            class="px-3 py-1 rounded <?= $i == $currentPage ? 'bg-[#1e3a5f] text-white' : 'bg-gray-200 hover:bg-gray-300 text-gray-700' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    <!-- Next -->
                    <?php if ($currentPage < $totalPages): ?>
                        <a href="index.php?controller=admin&action=anggota&page=<?= $currentPage + 1 ?>&filter=<?= urlencode($filter) ?>&q=<?= urlencode($search) ?>"
                            class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300 text-gray-700">&gt;</a>
                    <?php else: ?>
                        <span class="px-3 py-1 rounded bg-gray-100 text-gray-400 cursor-not-allowed">&gt;</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <script>
        function toggleMenu(id) {
            const menu = document.getElementById(id);
            if (!menu) return;
            menu.classList.toggle('hidden');
        }

        document.addEventListener('click', function(e) {
            document.querySelectorAll("[id^='user-']").forEach(menu => {
                const btn = menu.previousElementSibling;
                if (!menu.contains(e.target) && !btn.contains(e.target)) {
                    menu.classList.add('hidden');
                }
            });
        });
    </script>

</body>

</html>