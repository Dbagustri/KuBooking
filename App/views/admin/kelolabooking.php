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
    // SIDEBAR
    $sidebarPath = __DIR__ . '/../layout/sidebar.php';
    if (file_exists($sidebarPath)) {
        include $sidebarPath;
    }
    ?>

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

        <div class="px-4 sm:px-8 pb-10 space-y-6 max-w-6xl mx-auto w-full">

            <!-- HEADER + ACTION -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <h1 class="text-2xl font-bold text-[#1e3a5f]">Kelola Booking</h1>

                <div class="flex flex-wrap gap-2">
                    <a href="index.php?controller=adminBooking&action=createInternal"
                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold 
                              bg-[#1e3a5f] text-white hover:bg-[#163152] shadow">
                        Tambah Booking Internal
                    </a>

                    <a href="index.php?controller=adminBooking&action=createExternal"
                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold 
                              bg-emerald-600 text-white hover:bg-emerald-700 shadow">
                        Tambah Booking Eksternal
                    </a>
                </div>
            </div>

            <?php
            // Data utama dari controller
            $bookings    = $bookings ?? [];
            $currentPage = isset($currentPage) ? (int)$currentPage : (int)($_GET['page'] ?? 1);
            if ($currentPage < 1) $currentPage = 1;
            $totalPages  = isset($totalPages) ? (int)$totalPages : 1;

            // Filter & search dari GET
            $statusFilter = $_GET['status'] ?? 'all';
            $search       = trim($_GET['q'] ?? '');

            // Filter tipe booking: internal / external / all
            $typeFilter = $_GET['tipe'] ?? 'all';
            // Untuk tampilan tab: default ke 'internal' kalau bukan 'external'
            $activeTab  = ($typeFilter === 'external') ? 'external' : 'internal';

            // Waktu sekarang (timestamp) – untuk logic "Mulai Peminjaman"
            $nowTs = time();

            // ==========================
            // FILTER & SEARCH
            // ==========================
            $displayBookings = $bookings;

            // 1) Search (override filter status)
            if ($search !== '') {
                $tmp = [];
                foreach ($bookings as $b) {
                    $haystackParts = [
                        $b['kode']           ?? $b['booking_code'] ?? '',
                        $b['pj']             ?? '',
                        $b['pj_nim']         ?? $b['pj_nip'] ?? '',
                        $b['ruang']          ?? '',
                        $b['lokasi']         ?? '',
                        $b['guest_name']     ?? '',
                        $b['asal_instansi']  ?? '',
                    ];
                    $haystack = strtolower(implode(' ', $haystackParts));
                    if (strpos($haystack, strtolower($search)) !== false) {
                        $tmp[] = $b;
                    }
                }
                $displayBookings = $tmp;
            }
            // 2) Filter status
            elseif ($statusFilter !== 'all') {
                $tmp = [];
                foreach ($bookings as $b) {
                    $statusRaw = strtolower($b['status'] ?? 'pending');
                    if ($statusRaw === strtolower($statusFilter)) {
                        $tmp[] = $b;
                    }
                }
                $displayBookings = $tmp;
            }

            // 3) Filter tipe internal / external (PAKAI is_external)
            if (in_array($typeFilter, ['internal', 'external'], true)) {
                $tmp = [];
                foreach ($displayBookings as $b) {
                    $tipe = (!empty($b['is_external']) && (int)$b['is_external'] === 1)
                        ? 'external'
                        : 'internal';

                    if ($tipe === $typeFilter) {
                        $tmp[] = $b;
                    }
                }
                $displayBookings = $tmp;
            }

            // Kalau pakai search / filter status, pagination dari controller diabaikan (all-in-one)
            if ($statusFilter !== 'all' || $search !== '') {
                $totalPages  = 1;
                $currentPage = 1;
            }

            // Base URL untuk tab dan pagination
            $baseUrl = 'index.php?controller=adminBooking&action=manage'
                . '&status=' . urlencode($statusFilter)
                . '&q=' . urlencode($search);
            ?>

            <!-- TAB INTERNAL / EKSTERNAL -->
            <div class="flex bg-gray-200 rounded-lg overflow-hidden mt-2">
                <a href="<?= $baseUrl ?>&tipe=internal"
                    class="flex-1 text-center py-3 font-semibold text-sm transition
                          <?= $activeTab === 'internal' ? 'bg-[#1e3a5f] text-white' : 'text-gray-800' ?>">
                    Booking Internal
                </a>
                <a href="<?= $baseUrl ?>&tipe=external"
                    class="flex-1 text-center py-3 font-semibold text-sm transition
                          <?= $activeTab === 'external' ? 'bg-[#1e3a5f] text-white' : 'text-gray-800' ?>">
                    Booking Eksternal
                </a>
            </div>

            <!-- BAR FILTER & SEARCH -->
            <form method="get"
                class="flex flex-col lg:flex-row lg:items-center lg:space-x-4 space-y-3 lg:space-y-0 mt-4">

                <input type="hidden" name="controller" value="adminBooking">
                <input type="hidden" name="action" value="manage">
                <input type="hidden" name="tipe" value="<?= htmlspecialchars($typeFilter) ?>">

                <!-- FILTER STATUS -->
                <select name="status"
                    class="bg-white border border-gray-300 rounded-full px-4 py-2 text-sm shadow">
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
                        value="<?= htmlspecialchars($search) ?>"
                        placeholder="Cari kode booking, nama PJ, ruangan, atau instansi"
                        class="flex-1 text-sm bg-transparent focus:outline-none">
                </div>

                <button type="submit"
                    class="w-10 h-10 rounded-full bg-[#1e3a5f] flex items-center justify-center text-white hover:bg-[#163052] transition">
                    🔍
                </button>
            </form>

            <!-- TABEL BOOKING -->
            <div class="mt-4">
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
                                <?php if (!empty($displayBookings)): ?>
                                    <?php foreach ($displayBookings as $i => $b): ?>
                                        <?php
                                        $id         = (int)($b['id'] ?? $b['id_bookings'] ?? 0);
                                        $statusRaw  = strtolower($b['status'] ?? 'pending');
                                        $tanggal    = $b['tanggal']      ?? null;
                                        $startField = $b['start_time']   ?? null;
                                        $checkin    = $b['checkin_time'] ?? null;

                                        // TIPE: hitung dari is_external
                                        $tipe = (!empty($b['is_external']) && (int)$b['is_external'] === 1)
                                            ? 'external'
                                            : 'internal';

                                        $startTs = null;
                                        if ($tanggal && $startField) {
                                            $startTs = strtotime($tanggal . ' ' . $startField);
                                        } elseif ($startField) {
                                            $startTs = strtotime($startField);
                                        }

                                        $canStart = false;
                                        if (
                                            $startTs !== null
                                            && in_array($statusRaw, ['approved', 'reschedule_approved'], true)
                                            && empty($checkin)
                                        ) {
                                            $graceTs = $startTs + 10 * 60; // 10 menit setelah mulai
                                            if ($nowTs >= $startTs && $nowTs <= $graceTs) {
                                                $canStart = true;
                                            }
                                        }

                                        // Mapping status → label & warna
                                        $statusLabel = 'Menunggu Verifikasi';
                                        $badgeClass  = 'bg-yellow-100 text-yellow-800';

                                        switch ($statusRaw) {
                                            case 'approved':
                                                $statusLabel = 'Disetujui';
                                                $badgeClass  = 'bg-green-100 text-green-800';
                                                break;
                                            case 'rejected':
                                                $statusLabel = 'Ditolak';
                                                $badgeClass  = 'bg-red-100 text-red-800';
                                                break;
                                            case 'cancelled':
                                                $statusLabel = 'Dibatalkan';
                                                $badgeClass  = 'bg-red-100 text-red-800';
                                                break;
                                            case 'selesai':
                                            case 'completed':
                                                $statusLabel = 'Selesai';
                                                $badgeClass  = 'bg-blue-100 text-blue-800';
                                                break;
                                            case 'ongoing':
                                                $statusLabel = 'Sedang Berlangsung';
                                                $badgeClass  = 'bg-emerald-100 text-emerald-800';
                                                break;
                                            case 'reschedule_pending':
                                                $statusLabel = 'Menunggu Reschedule';
                                                $badgeClass  = 'bg-purple-100 text-purple-800';
                                                break;
                                            case 'reschedule_approved':
                                                $statusLabel = 'Reschedule Disetujui';
                                                $badgeClass  = 'bg-purple-100 text-purple-800';
                                                break;
                                            case 'reschedule_rejected':
                                                $statusLabel = 'Reschedule Ditolak';
                                                $badgeClass  = 'bg-red-100 text-red-800';
                                                break;
                                            case 'pending':
                                            default:
                                                $statusLabel = 'Menunggu Verifikasi';
                                                $badgeClass  = 'bg-yellow-100 text-yellow-800';
                                                break;
                                        }

                                        // striping row
                                        $rowBase = ($i % 2 === 0) ? 'bg-gray-50' : 'bg-gray-100';
                                        if ($statusRaw === 'reschedule_pending') {
                                            $rowBase = 'bg-purple-50';
                                        }

                                        $isRescheduleView = in_array($statusRaw, [
                                            'reschedule_pending',
                                            'reschedule_approved',
                                            'reschedule_rejected',
                                        ], true);
                                        ?>
                                        <tr class="<?= $rowBase ?> text-gray-800 border-b last:border-b-0 align-top">
                                            <!-- KODE -->
                                            <td class="px-4 py-3">
                                                <div class="flex flex-col gap-0.5">
                                                    <span class="font-mono text-sm font-semibold text-slate-900">
                                                        <?= htmlspecialchars($b['kode'] ?? $b['booking_code'] ?? '-') ?>
                                                    </span>
                                                    <span class="inline-flex items-center rounded-full bg-slate-100 text-[10px] px-2 py-0.5 text-slate-600">
                                                        <?= htmlspecialchars(ucfirst($tipe)) ?>
                                                    </span>
                                                </div>
                                            </td>

                                            <!-- PJ -->
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

                                            <!-- RUANG -->
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

                                            <!-- WAKTU -->
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

                                            <!-- KAPASITAS -->
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

                                            <!-- INSTANSI (hanya eksternal) -->
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

                                            <!-- STATUS -->
                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[11px] font-semibold <?= $badgeClass ?>">
                                                    <?php if (in_array($statusRaw, ['approved', 'reschedule_approved', 'ongoing', 'selesai', 'completed'], true)): ?>
                                                        ✅
                                                    <?php elseif (in_array($statusRaw, ['rejected', 'cancelled', 'reschedule_rejected'], true)): ?>
                                                        ✖
                                                    <?php elseif ($statusRaw === 'reschedule_pending'): ?>
                                                        🔁
                                                    <?php else: ?>
                                                        ⏳
                                                    <?php endif; ?>
                                                    <span><?= htmlspecialchars($statusLabel) ?></span>
                                                </span>
                                            </td>

                                            <!-- AKSI -->
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

                                                            <!-- DETAIL -->
                                                            <?php if ($isRescheduleView): ?>
                                                                <a href="index.php?controller=adminBooking&action=processReschedule&id_booking=<?= $id ?>"
                                                                    class="block px-4 py-2 hover:bg-gray-100 text-purple-700">
                                                                    Detail Reschedule
                                                                </a>
                                                            <?php else: ?>
                                                                <a href="index.php?controller=adminBooking&action=detail&id=<?= $id ?>"
                                                                    class="block px-4 py-2 hover:bg-gray-100">
                                                                    Detail Booking
                                                                </a>
                                                            <?php endif; ?>

                                                            <!-- EDIT -->
                                                            <a href="index.php?controller=adminBooking&action=edit&id=<?= $id ?>"
                                                                class="block px-4 py-2 hover:bg-gray-100">
                                                                Edit
                                                            </a>

                                                            <!-- MULAI PEMINJAMAN -->
                                                            <?php if ($canStart): ?>
                                                                <form action="index.php?controller=adminBooking&action=start"
                                                                    method="POST"
                                                                    onsubmit="return confirm('Mulai peminjaman ruangan ini sekarang?');">
                                                                    <input type="hidden" name="id_booking" value="<?= $id ?>">
                                                                    <button type="submit"
                                                                        class="w-full text-left px-4 py-2 hover:bg-emerald-50 text-emerald-700">
                                                                        Mulai Peminjaman
                                                                    </button>
                                                                </form>
                                                            <?php endif; ?>

                                                            <!-- HAPUS -->
                                                            <form action="index.php?controller=adminBooking&action=delete"
                                                                method="POST"
                                                                onsubmit="return confirm('Yakin ingin menghapus booking ini?');">
                                                                <input type="hidden" name="id_booking" value="<?= $id ?>">
                                                                <button type="submit"
                                                                    class="w-full text-left px-4 py-2 hover:bg-red-50 text-red-600">
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
                                        <td colspan="<?= $activeTab === 'external' ? 8 : 7 ?>" class="px-4 py-8 text-center text-gray-500">
                                            <?php if ($statusFilter !== 'all' || $search !== '' || in_array($typeFilter, ['internal', 'external'], true)): ?>
                                                Tidak ada booking yang cocok dengan filter / kata kunci.
                                            <?php else: ?>
                                                Belum ada booking yang tercatat.
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- PAGINATION -->
                <?php if ($totalPages > 1 && !empty($displayBookings)): ?>
                    <?php
                    $pageBaseUrl = $baseUrl . '&tipe=' . urlencode($typeFilter);
                    ?>
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