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

    // data dari controller
    $users        = $users ?? [];
    $currentPage  = $current_page ?? 1;
    $totalPages   = $total_pages ?? 1;
    $filter       = $filter ?? '';
    $search       = $search ?? '';
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

            <h1 class="text-2xl font-bold text-[#1e3a5f]">Kelola User</h1>

            <!-- FILTER & SEARCH -->
            <form method="get" class="flex flex-col lg:flex-row lg:items-center lg:space-x-4 space-y-3 lg:space-y-0">

                <input type="hidden" name="controller" value="admin">
                <input type="hidden" name="action" value="anggota">

                <!-- FILTER -->
                <select name="filter" class="bg-white border border-gray-300 rounded-full px-4 py-2 text-sm shadow">
                    <option value="">Filter</option>
                    <option value="mahasiswa" <?= $filter == 'mahasiswa' ? 'selected' : '' ?>>Mahasiswa</option>
                    <option value="dosen" <?= $filter == 'dosen' ? 'selected' : '' ?>>Dosen</option>
                    <option value="tendik" <?= $filter == 'tendik' ? 'selected' : '' ?>>Tendik</option>
                    <option value="super_admin" <?= $filter == 'super_admin' ? 'selected' : '' ?>>Super Admin</option>
                </select>

                <!-- SEARCH -->
                <div class="flex flex-1 items-center bg-white rounded-full px-4 py-2 shadow border border-gray-200">
                    <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Cari"
                        class="flex-1 text-sm bg-transparent focus:outline-none">
                </div>

                <button type="submit"
                    class="w-10 h-10 rounded-full bg-[#1e3a5f] flex items-center justify-center text-white hover:bg-[#163052] transition">
                    🔍
                </button>
            </form>

            <!-- TABEL USER -->
            <div class="overflow-x-auto mt-4">
                <table class="min-w-full border-collapse bg-white shadow rounded-lg overflow-hidden">
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
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $i => $u): ?>
                                <?php
                                $id      = (int)($u['id_account'] ?? 0);
                                $status  = $u['status_aktif'] ?? 'aktif';
                                ?>
                                <tr class="<?= $i % 2 === 0 ? 'bg-gray-50' : 'bg-gray-100'; ?> text-sm text-gray-800 border-b">

                                    <td class="px-4 py-3"><?= htmlspecialchars($u['nama'] ?? '-') ?></td>
                                    <td class="px-4 py-3"><?= htmlspecialchars($u['email'] ?? '-') ?></td>
                                    <td class="px-4 py-3"><?= htmlspecialchars($u['jurusan'] ?? '-') ?></td>

                                    <td class="px-4 py-3 text-blue-600 hover:underline text-sm">
                                        <?php if (!empty($u['screenshot_kubaca'])): ?>
                                            <a href="<?= htmlspecialchars($u['screenshot_kubaca']) ?>" target="_blank">lihat bukti</a>
                                        <?php else: ?>
                                            <span class="text-gray-400">Tidak ada</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="px-4 py-3">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                                            <?= $status == 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                            <?= ucfirst($status) ?>
                                        </span>
                                    </td>

                                    <!-- Aksi: Edit / Delete -->
                                    <td class="px-4 py-3 text-center space-x-2">

                                        <a href="index.php?controller=admin&action=editUser&id=<?= $id ?>"
                                            class="inline-block px-4 py-1.5 rounded bg-[#1e3a5f] text-white text-xs font-medium hover:bg-[#163052] transition">
                                            Edit
                                        </a>

                                        <form action="index.php?controller=admin&action=deleteUser" method="POST" class="inline"
                                            onsubmit="return confirm('Yakin ingin menghapus user ini?');">
                                            <input type="hidden" name="id_user" value="<?= $id ?>">
                                            <button type="submit"
                                                class="inline-block px-4 py-1.5 rounded bg-red-600 text-white text-xs font-medium hover:bg-red-700 transition">
                                                Delete
                                            </button>
                                        </form>

                                    </td>
                                </tr>

                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    Tidak ada data user.
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
                        <a href="index.php?controller=admin&action=anggota&page=<?= $currentPage - 1 ?>&filter=<?= $filter ?>&q=<?= urlencode($search) ?>"
                            class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300 text-gray-700">&lt;</a>
                    <?php else: ?>
                        <span class="px-3 py-1 rounded bg-gray-100 text-gray-400 cursor-not-allowed">&lt;</span>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="index.php?controller=admin&action=anggota&page=<?= $i ?>&filter=<?= $filter ?>&q=<?= urlencode($search) ?>"
                            class="px-3 py-1 rounded <?= $i == $currentPage ? 'bg-[#1e3a5f] text-white' : 'bg-gray-200 hover:bg-gray-300 text-gray-700' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <!-- Next -->
                    <?php if ($currentPage < $totalPages): ?>
                        <a href="index.php?controller=admin&action=anggota&page=<?= $currentPage + 1 ?>&filter=<?= $filter ?>&q=<?= urlencode($search) ?>"
                            class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300 text-gray-700">&gt;</a>
                    <?php else: ?>
                        <span class="px-3 py-1 rounded bg-gray-100 text-gray-400 cursor-not-allowed">&gt;</span>
                    <?php endif; ?>

                </div>
            <?php endif; ?>

        </div>
    </div>

</body>

</html>