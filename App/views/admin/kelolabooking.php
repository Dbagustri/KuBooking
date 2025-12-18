<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Booking | Kubooking</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#f2f7fc] text-gray-800 flex">

    <?php
    $sidebarPath = __DIR__ . '/../layout/sidebar.php';
    if (file_exists($sidebarPath)) include $sidebarPath;

    // Data dari controller (WAJIB controller yang handle filter/search/pagination)
    $bookings    = $bookings ?? [];
    $currentPage = isset($currentPage) ? (int)$currentPage : (int)($_GET['page'] ?? 1);
    $totalPages  = isset($totalPages) ? (int)$totalPages : 1;

    if ($currentPage < 1) $currentPage = 1;
    if ($totalPages < 1)  $totalPages  = 1;

    // GET state
    $statusFilter = $_GET['status'] ?? 'all';
    $search       = trim($_GET['q'] ?? '');
    $typeFilter   = $_GET['tipe'] ?? 'internal'; // default internal
    if (!in_array($typeFilter, ['internal', 'external', 'all'], true)) $typeFilter = 'internal';

    $activeTab = ($typeFilter === 'external') ? 'external' : 'internal';

    $nowTs = time();
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

        <div class="px-4 sm:px-8 pb-10 space-y-6 max-w-6xl mx-auto w-full">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <h1 class="text-2xl font-bold text-[#1e3a5f]">Kelola Booking</h1>

                <div class="flex flex-wrap gap-2">
                    <a href="index.php?controller=adminBooking&action=createInternal"
                        class="inline-flex items-center justify-center px-4 py-2 rounded-full text-sm font-semibold text-white bg-[#1e3a5f] hover:bg-[#163052] shadow">
                        Tambah Booking Internal
                    </a>

                    <a href="index.php?controller=adminBooking&action=createExternal"
                        class="inline-flex items-center justify-center px-4 py-2 rounded-full text-sm font-semibold text-white bg-emerald-600 text-white hover:bg-emerald-700 shadow">
                        Tambah Booking Eksternal
                    </a>
                </div>
            </div>

            <!-- TAB INTERNAL / EKSTERNAL (jaga q/status) -->
            <?php
            $tabBase = 'index.php?' . http_build_query([
                'controller' => 'adminBooking',
                'action'     => 'manage',
                'status'     => $statusFilter,
                'q'          => $search,
                'page'       => 1, // pindah tab reset page
            ]);
            ?>
            <div class="flex bg-gray-200 rounded-lg overflow-hidden mt-2">
                <a href="<?= $tabBase . '&tipe=internal' ?>"
                    class="flex-1 text-center py-3 font-semibold text-sm transition
                          <?= $activeTab === 'internal' ? 'bg-[#1e3a5f] text-white' : 'text-gray-800' ?>">
                    Booking Internal
                </a>
                <a href="<?= $tabBase . '&tipe=external' ?>"
                    class="flex-1 text-center py-3 font-semibold text-sm transition
                          <?= $activeTab === 'external' ? 'bg-[#1e3a5f] text-white' : 'text-gray-800' ?>">
                    Booking Eksternal
                </a>
            </div>

            <!-- BAR FILTER & SEARCH (1 form, filter auto submit, search pakai tombol) -->
            <form id="bookingFilterForm" method="get" action="index.php"
                class="flex flex-col lg:flex-row items-stretch gap-3 mt-4 w-full">

                <input type="hidden" name="controller" value="adminBooking">
                <input type="hidden" name="action" value="manage">
                <input type="hidden" name="tipe" value="<?= htmlspecialchars($typeFilter, ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="page" value="1">

                <!-- STATUS -->
                <select name="status" id="statusSelect"
                    class="bg-white border border-gray-300 rounded-full px-4 py-2 text-sm shadow min-w-[180px]">
                    <option value="all" <?= $statusFilter === 'all' ? 'selected' : '' ?>>Semua Status</option>
                    <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="approved" <?= $statusFilter === 'approved' ? 'selected' : '' ?>>Disetujui</option>
                    <option value="rejected" <?= $statusFilter === 'rejected' ? 'selected' : '' ?>>Ditolak</option>
                    <option value="ongoing" <?= $statusFilter === 'ongoing' ? 'selected' : '' ?>>Sedang Berlangsung</option>
                    <option value="selesai" <?= $statusFilter === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                    <option value="reschedule_pending" <?= $statusFilter === 'reschedule_pending' ? 'selected' : '' ?>>
                        Menunggu Reschedule
                    </option>
                </select>

                <!-- SEARCH -->
                <div class="flex flex-1 items-center bg-white rounded-full px-4 py-2 shadow border border-gray-200">
                    <input type="text"
                        name="q"
                        value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="Cari kode booking, nama PJ, ruangan, atau instansi"
                        class="flex-1 text-sm bg-transparent focus:outline-none">
                </div>

                <!-- BUTTON 🔍 -->
                <button type="submit"
                    class="w-10 h-10 rounded-full bg-[#1e3a5f] flex items-center justify-center text-white hover:bg-[#163052] transition">
                    🔍
                </button>
            </form>


            <!-- TABEL BOOKING -->
            <div class="mt-4">
                <!-- overflow-hidden aman, karena menu aksi global (fixed) -->
                <div class="bg-white shadow rounded-lg border border-slate-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto text-sm">
                            <thead>
                                <tr class="bg-[#1e3a5f] text-white text-left text-xs uppercase tracking-wide">
                                    <th class="px-4 py-3">Kode</th>
                                    <th class="px-4 py-3">Penanggung Jawab</th>
                                    <th class="px-4 py-3">Ruangan</th>
                                    <th class="px-4 py-3">Waktu</th>
                                    <th class="px-4 py-3">Kapasitas</th>
                                    <?php if ($activeTab === 'external'): ?>
                                        <th class="px-4 py-3">Instansi</th>
                                    <?php endif; ?>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (!empty($bookings)): ?>
                                    <?php foreach ($bookings as $i => $b): ?>
                                        <?php
                                        $id         = (int)($b['id'] ?? $b['id_bookings'] ?? 0);
                                        $statusRaw  = strtolower($b['status'] ?? 'pending');
                                        $tanggal    = $b['tanggal']      ?? null;
                                        $startField = $b['start_time']   ?? null;
                                        $checkin    = $b['checkin_time'] ?? null;

                                        $tipe = (!empty($b['is_external']) && (int)$b['is_external'] === 1) ? 'external' : 'internal';

                                        $startTs = null;
                                        if ($tanggal && $startField) $startTs = strtotime($tanggal . ' ' . $startField);
                                        elseif ($startField) $startTs = strtotime($startField);

                                        $canStart = false;
                                        if ($startTs !== null && in_array($statusRaw, ['approved', 'reschedule_approved'], true) && empty($checkin)) {
                                            $graceTs = $startTs + 10 * 60;
                                            if ($nowTs >= $startTs && $nowTs <= $graceTs) $canStart = true;
                                        }

                                        $statusLabel = 'Menunggu Verifikasi';
                                        $badgeClass  = 'bg-yellow-100 text-yellow-800';
                                        switch ($statusRaw) {
                                            case 'approved':
                                                $statusLabel = 'Disetujui';
                                                $badgeClass = 'bg-green-100 text-green-800';
                                                break;
                                            case 'rejected':
                                                $statusLabel = 'Ditolak';
                                                $badgeClass = 'bg-red-100 text-red-800';
                                                break;
                                            case 'cancelled':
                                                $statusLabel = 'Dibatalkan';
                                                $badgeClass = 'bg-red-100 text-red-800';
                                                break;
                                            case 'selesai':
                                            case 'completed':
                                                $statusLabel = 'Selesai';
                                                $badgeClass = 'bg-blue-100 text-blue-800';
                                                break;
                                            case 'ongoing':
                                                $statusLabel = 'Sedang Berlangsung';
                                                $badgeClass = 'bg-emerald-100 text-emerald-800';
                                                break;
                                            case 'reschedule_pending':
                                                $statusLabel = 'Menunggu Reschedule';
                                                $badgeClass = 'bg-purple-100 text-purple-800';
                                                break;
                                            case 'reschedule_approved':
                                                $statusLabel = 'Reschedule Disetujui';
                                                $badgeClass = 'bg-purple-100 text-purple-800';
                                                break;
                                            case 'reschedule_rejected':
                                                $statusLabel = 'Reschedule Ditolak';
                                                $badgeClass = 'bg-red-100 text-red-800';
                                                break;
                                        }

                                        $rowBase = ($i % 2 === 0) ? 'bg-gray-50' : 'bg-gray-100';
                                        if ($statusRaw === 'reschedule_pending') $rowBase = 'bg-purple-50';

                                        $isRescheduleView = in_array($statusRaw, ['reschedule_pending', 'reschedule_approved', 'reschedule_rejected'], true);

                                        // untuk dropdown: apakah item "Mulai" muncul
                                        $canStartJs = $canStart ? '1' : '0';
                                        $isRescheduleJs = $isRescheduleView ? '1' : '0';
                                        ?>
                                        <tr class="<?= $rowBase ?> text-gray-800 border-b last:border-b-0 align-top">
                                            <td class="px-4 py-3">
                                                <div class="flex flex-col gap-0.5">
                                                    <span class="font-mono text-sm font-semibold text-slate-900">
                                                        <?= htmlspecialchars($b['kode'] ?? $b['booking_code'] ?? '-') ?>
                                                    </span>
                                                </div>
                                            </td>

                                            <td class="px-4 py-3">
                                                <div class="flex flex-col gap-0.5">
                                                    <span class="font-medium text-slate-900">
                                                        <?= htmlspecialchars($b['pj'] ?? $b['guest_name'] ?? '-') ?>
                                                    </span>
                                                    <?php if (!empty($b['pj_nim']) || !empty($b['pj_nip']) || !empty($b['guest_phone'])): ?>
                                                        <span class="text-[11px] text-slate-500">
                                                            <?= htmlspecialchars($b['pj_nim'] ?? $b['pj_nip'] ?? $b['guest_phone'] ?? '') ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>

                                            <td class="px-4 py-3">
                                                <div class="flex flex-col gap-0.5">
                                                    <span class="font-medium text-slate-900">
                                                        <?= htmlspecialchars($b['ruang'] ?? '-') ?>
                                                    </span>
                                                    <?php if (!empty($b['lokasi'])): ?>
                                                        <span class="text-[11px] text-slate-500">
                                                            <?= htmlspecialchars($b['lokasi']) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>

                                            <td class="px-4 py-3">
                                                <div class="flex flex-col gap-0.5 text-xs">
                                                    <span class="text-slate-900">
                                                        <?= htmlspecialchars($b['waktu'] ?? '-') ?>
                                                    </span>
                                                    <?php if (!empty($b['tanggal'])): ?>
                                                        <span class="text-slate-500">
                                                            <?= htmlspecialchars($b['tanggal']) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>

                                            <td class="px-4 py-3">
                                                <div class="flex flex-col gap-0.5 text-xs">
                                                    <span class="text-slate-900">
                                                        <?= htmlspecialchars($b['kapasitas'] ?? '-') ?>
                                                    </span>
                                                    <?php if (!empty($b['jumlah_anggota'])): ?>
                                                        <span class="text-slate-500">
                                                            Anggota: <?= (int)$b['jumlah_anggota'] ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>

                                            <?php if ($activeTab === 'external'): ?>
                                                <td class="px-4 py-3 text-xs">
                                                    <div class="flex flex-col gap-0.5">
                                                        <span class="text-slate-900">
                                                            <?= htmlspecialchars($b['asal_instansi'] ?? '-') ?>
                                                        </span>
                                                        <?php if (!empty($b['guest_email'])): ?>
                                                            <span class="text-slate-500">
                                                                <?= htmlspecialchars($b['guest_email']) ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            <?php endif; ?>

                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[11px] font-semibold <?= $badgeClass ?>">
                                                    <span><?= htmlspecialchars($statusLabel) ?></span>
                                                </span>
                                            </td>

                                            <!-- AKSI: global floating menu -->
                                            <td class="px-4 py-3 text-center">
                                                <button type="button"
                                                    class="inline-flex w-8 h-8 items-center justify-center rounded-full
                                                           hover:bg-gray-200 focus:outline-none focus:ring-2
                                                           focus:ring-offset-2 focus:ring-slate-400"
                                                    onclick="openActionMenu(event, <?= $id ?>, '<?= htmlspecialchars($statusRaw, ENT_QUOTES, 'UTF-8') ?>', <?= $isRescheduleJs ?>, <?= $canStartJs ?>)">
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
                                        <td colspan="<?= $activeTab === 'external' ? 8 : 7 ?>" class="px-4 py-8 text-center text-gray-500">
                                            Tidak ada booking yang cocok dengan filter / kata kunci.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- PAGINATION: pakai komponen kamu -->
                <?php
                $pagination = [
                    'pageKey'     => 'page',
                    'currentPage' => (int)$currentPage,
                    'totalPages'  => (int)$totalPages,
                    'params'      => [
                        'controller' => 'adminBooking',
                        'action'     => 'manage',
                        'tipe'       => $typeFilter,
                        'status'     => $statusFilter,
                        'q'          => $search,
                    ],
                ];
                include __DIR__ . '/../layout/pagination.php';
                ?>
            </div>

        </div>
    </div>

    <!-- GLOBAL ACTION MENU (floating, tidak ketutupan overflow table/card) -->
    <div id="actionMenu"
        class="hidden fixed z-[9999] w-52 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
        <div class="py-1 text-sm text-gray-700" id="actionMenuContent"></div>
    </div>

    <script>
        // Filter auto jalan (status), search tetap tombol submit
        document.getElementById('statusSelect')?.addEventListener('change', function() {
            const form = document.getElementById('bookingFilterForm');
            const pageInput = form?.querySelector('input[name="page"]');
            if (pageInput) pageInput.value = '1';
            form?.submit();
        });

        const menuEl = document.getElementById('actionMenu');
        const contentEl = document.getElementById('actionMenuContent');
        let activeBookingId = null;

        function buildMenuHTML(id, isRescheduleView, canStart) {
            const detailUrl = `index.php?controller=adminBooking&action=detail&id=${id}`;
            const editUrl = `index.php?controller=adminBooking&action=edit&id=${id}`;
            const resUrl = `index.php?controller=adminBooking&action=processReschedule&id_booking=${id}`;

            const detailItem = (isRescheduleView) ?
                `<a href="${resUrl}" class="block px-4 py-2 hover:bg-gray-100 text-purple-700">Detail Reschedule</a>` :
                `<a href="${detailUrl}" class="block px-4 py-2 hover:bg-gray-100">Detail Booking</a>`;

            const startForm = (canStart) ?
                `
                <form action="index.php?controller=adminBooking&action=start"
                      method="POST"
                      onsubmit="return confirm('Mulai peminjaman ruangan ini sekarang?');">
                    <input type="hidden" name="id_booking" value="${id}">
                    <button type="submit"
                        class="w-full text-left px-4 py-2 hover:bg-emerald-50 text-emerald-700">
                        Mulai Peminjaman
                    </button>
                </form>
                ` :
                ``;

            return `
                ${detailItem}
                <a href="${editUrl}" class="block px-4 py-2 hover:bg-gray-100">Edit</a>
                ${startForm}
                <form action="index.php?controller=adminBooking&action=delete"
                      method="POST"
                      onsubmit="return confirm('Yakin ingin menghapus booking ini?');">
                    <input type="hidden" name="id_booking" value="${id}">
                    <button type="submit"
                        class="w-full text-left px-4 py-2 hover:bg-red-50 text-red-600">
                        Hapus
                    </button>
                </form>
            `;
        }

        function openActionMenu(e, id, statusRaw, isRescheduleView, canStart) {
            e.stopPropagation();

            // toggle: klik tombol yang sama lagi -> tutup
            if (!menuEl.classList.contains('hidden') && activeBookingId === id) {
                closeActionMenu();
                return;
            }

            activeBookingId = id;
            contentEl.innerHTML = buildMenuHTML(id, !!isRescheduleView, !!canStart);

            const btnRect = e.currentTarget.getBoundingClientRect();
            const menuWidth = 208; // w-52

            let left = btnRect.right - menuWidth;
            let top = btnRect.bottom + 8;

            // clamp biar gak keluar layar
            left = Math.max(8, Math.min(left, window.innerWidth - menuWidth - 8));

            // kalau mepet bawah, munculkan ke atas
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
            activeBookingId = null;
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