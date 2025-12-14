<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Feedback Pengguna | Kubooking</title>
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
        // Normalisasi variabel (fallback aman + sinkron dengan GET)
        $feedbacks    = $feedbacks    ?? [];
        $rooms        = $rooms        ?? [];

        $search       = $search       ?? (trim($_GET['q'] ?? ''));
        $roomFilter   = $roomFilter   ?? ($_GET['room'] ?? 'all');
        $ratingFilter = $ratingFilter ?? ($_GET['rating'] ?? 'all');

        $currentPage  = $current_page ?? (int)($_GET['page'] ?? 1);
        if ($currentPage < 1) $currentPage = 1;

        $totalPages   = $total_pages  ?? 1;
        ?>

        <div class="px-8 pb-10 space-y-6">

            <!-- HEADER -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-[#1e3a5f]">Feedback Pengguna</h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Lihat ringkasan rating dan komentar pengguna untuk setiap peminjaman ruangan.
                    </p>
                </div>
            </div>

            <!-- FILTER & SEARCH -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:space-x-4 space-y-3 lg:space-y-0 mt-2">

                <!-- FILTER (AUTO SUBMIT) -->
                <form id="filterForm" method="get" class="flex flex-col lg:flex-row lg:items-center lg:space-x-4 space-y-3 lg:space-y-0 w-full">
                    <input type="hidden" name="controller" value="userFeedback">
                    <input type="hidden" name="action" value="adminIndex">
                    <input type="hidden" name="q" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="page" value="1">

                    <!-- FILTER RUANGAN -->
                    <select name="room"
                        onchange="document.getElementById('filterForm').submit()"
                        class="bg-white border border-gray-300 rounded-full px-4 py-2 text-sm shadow w-full lg:w-auto">
                        <option value="all" <?= (string)$roomFilter === 'all' ? 'selected' : '' ?>>Semua Ruangan</option>
                        <?php foreach ($rooms as $room): ?>
                            <?php
                            $rid   = (int)($room['id_ruangan'] ?? 0);
                            $rname = $room['nama_ruangan'] ?? 'Ruangan';
                            ?>
                            <option value="<?= $rid ?>" <?= (string)$roomFilter === (string)$rid ? 'selected' : '' ?>>
                                <?= htmlspecialchars($rname) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <!-- FILTER MINIMUM RATING -->
                    <select name="rating"
                        onchange="document.getElementById('filterForm').submit()"
                        class="bg-white border border-gray-300 rounded-full px-4 py-2 text-sm shadow w-full lg:w-auto">
                        <option value="all" <?= (string)$ratingFilter === 'all' ? 'selected' : '' ?>>Semua Rating</option>
                        <option value="5" <?= (string)$ratingFilter === '5' ? 'selected' : '' ?>>⭐ 5 ke atas</option>
                        <option value="4" <?= (string)$ratingFilter === '4' ? 'selected' : '' ?>>⭐ 4 ke atas</option>
                        <option value="3" <?= (string)$ratingFilter === '3' ? 'selected' : '' ?>>⭐ 3 ke atas</option>
                        <option value="2" <?= (string)$ratingFilter === '2' ? 'selected' : '' ?>>⭐ 2 ke atas</option>
                        <option value="1" <?= (string)$ratingFilter === '1' ? 'selected' : '' ?>>⭐ 1 ke atas</option>
                    </select>
                </form>

                <!-- SEARCH (BUTUH SUBMIT) -->
                <form method="get" class="flex flex-1 items-center w-full">
                    <input type="hidden" name="controller" value="userFeedback">
                    <input type="hidden" name="action" value="adminIndex">
                    <input type="hidden" name="room" value="<?= htmlspecialchars((string)$roomFilter, ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="rating" value="<?= htmlspecialchars((string)$ratingFilter, ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="page" value="1">

                    <div class="flex flex-1 items-center bg-white rounded-full px-4 py-2 shadow border border-gray-200">
                        <input type="text"
                            name="q"
                            value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="Cari berdasarkan nama user, NIM/NIP, ruangan, atau komentar"
                            class="flex-1 text-sm bg-transparent focus:outline-none">
                    </div>

                    <button type="submit"
                        class="ml-2 w-10 h-10 rounded-full bg-[#1e3a5f] flex items-center justify-center text-white hover:bg-[#163052] transition">
                        🔍
                    </button>
                </form>
            </div>

            <!-- TABEL FEEDBACK -->
            <div class="overflow-x-auto mt-4">
                <table class="min-w-full border-collapse bg-white shadow rounded-lg overflow-hidden">
                    <thead>
                        <tr class="bg-[#1e3a5f] text-white text-left text-sm">
                            <th class="px-4 py-3">Ruangan</th>
                            <th class="px-4 py-3">User</th>
                            <th class="px-4 py-3">Rating</th>
                            <th class="px-4 py-3">Komentar</th>
                            <th class="px-4 py-3">Waktu</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (!empty($feedbacks)): ?>
                            <?php foreach ($feedbacks as $i => $f): ?>
                                <?php
                                $idFeedback  = (int)($f['id_feedback'] ?? 0);
                                $idBooking   = (int)($f['id_bookings'] ?? 0);
                                $namaRuangan = $f['nama_ruangan'] ?? 'Ruangan';
                                $namaUser    = $f['nama_user'] ?? 'User';
                                $nimNip      = $f['nim_nip'] ?? '';
                                $rating      = (int)($f['rating'] ?? 0);
                                $komentar    = $f['komentar'] ?? '';
                                $createdAt   = $f['created_at'] ?? null;

                                $waktuTampil = $createdAt ? date('d M Y, H:i', strtotime($createdAt)) : '-';
                                $rowClass = $i % 2 === 0 ? 'bg-gray-50' : 'bg-gray-100';

                                if ($rating >= 4) $ratingBadge = 'bg-green-100 text-green-800';
                                elseif ($rating === 3) $ratingBadge = 'bg-yellow-100 text-yellow-800';
                                else $ratingBadge = 'bg-red-100 text-red-800';
                                ?>
                                <tr class="<?= $rowClass ?> text-sm text-gray-800 border-b last:border-b-0">
                                    <td class="px-4 py-3 align-top">
                                        <div class="flex flex-col">
                                            <span class="font-medium text-slate-900">
                                                <?= htmlspecialchars($namaRuangan) ?>
                                            </span>
                                            <?php if ($idBooking): ?>
                                                <span class="text-xs text-slate-500">
                                                    Booking ID: #<?= (int)$idBooking ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 align-top">
                                        <div class="flex flex-col">
                                            <span class="font-medium text-slate-900">
                                                <?= htmlspecialchars($namaUser) ?>
                                            </span>
                                            <?php if ($nimNip !== ''): ?>
                                                <span class="text-xs text-slate-500">
                                                    <?= htmlspecialchars($nimNip) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 align-top">
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold <?= $ratingBadge ?>">
                                            <?= (int)$rating ?> / 5
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 align-top">
                                        <?php if ($komentar !== '' && $komentar !== null): ?>
                                            <button type="button"
                                                class="text-xs text-blue-600 underline hover:text-blue-800"
                                                data-comment="<?= htmlspecialchars($komentar, ENT_QUOTES, 'UTF-8') ?>"
                                                onclick="openCommentModal(this)">
                                                Lihat komentar
                                            </button>
                                        <?php else: ?>
                                            <span class="text-xs text-slate-400 italic">(Tanpa komentar)</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="px-4 py-3 align-top whitespace-nowrap">
                                        <?= htmlspecialchars($waktuTampil) ?>
                                    </td>

                                    <td class="px-4 py-3 align-top text-center">
                                        <form action="index.php?controller=userFeedback&action=delete"
                                            method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus feedback ini?');">
                                            <input type="hidden" name="id_feedback" value="<?= (int)$idFeedback ?>">
                                            <button type="submit"
                                                class="inline-flex items-center px-3 py-1.5 rounded-full bg-red-600 text-white text-xs font-semibold hover:bg-red-700 shadow">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    <?php if ($search !== '' || (string)$roomFilter !== 'all' || (string)$ratingFilter !== 'all'): ?>
                                        Tidak ada feedback yang cocok dengan filter.
                                        <a href="index.php?controller=userFeedback&action=adminIndex"
                                            class="text-[#1e3a5f] underline text-sm ml-1">
                                            Reset filter
                                        </a>
                                    <?php else: ?>
                                        Belum ada feedback dari pengguna.
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION (pakai komponen) -->
            <?php
            if ((int)$totalPages > 1) {
                $pagination = [
                    'pageKey'     => 'page',
                    'currentPage' => (int)$currentPage,
                    'totalPages'  => (int)$totalPages,
                    'params'      => [
                        'controller' => 'userFeedback',
                        'action'     => 'adminIndex',
                        'q'          => $search,
                        'room'       => $roomFilter,
                        'rating'     => $ratingFilter,
                    ],
                ];
                include __DIR__ . '/../layout/pagination.php';
            }
            ?>

            <!-- MODAL LIHAT KOMENTAR -->
            <div id="commentModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40">
                <div class="bg-white rounded-xl p-5 w-full max-w-md shadow-lg" onclick="event.stopPropagation()">
                    <h3 class="text-lg font-semibold mb-3 text-[#1e3a5f]">Komentar Pengguna</h3>

                    <div id="commentModalBody"
                        class="text-sm text-slate-700 whitespace-pre-line max-h-80 overflow-y-auto border border-gray-200 rounded-lg p-3 bg-gray-50"></div>

                    <div class="mt-4 flex justify-end">
                        <button type="button"
                            class="px-3 py-1.5 text-sm rounded-lg bg-[#1e3a5f] text-white hover:bg-[#163052] transition"
                            onclick="closeCommentModal()">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function openCommentModal(button) {
            const modal = document.getElementById('commentModal');
            const body = document.getElementById('commentModalBody');
            if (!modal || !body) return;

            body.textContent = button.dataset.comment || '';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeCommentModal() {
            const modal = document.getElementById('commentModal');
            if (!modal) return;
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        const commentModal = document.getElementById('commentModal');
        if (commentModal) {
            commentModal.addEventListener('click', function(e) {
                if (e.target === commentModal) closeCommentModal();
            });
        }
    </script>
</body>

</html>