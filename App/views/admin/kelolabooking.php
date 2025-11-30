<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Booking | Kubooking</title>
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
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-[#1e3a5f]">Kelola Booking</h1>

                <!-- BUTTON TAMBAH BOOKING -->
                <div class="flex gap-2">
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
            // dari controller: 'bookings' / 'pendingBookings'
            $bookings = $bookings ?? ($pendingBookings ?? []);
            ?>

            <div class="overflow-x-auto mt-4">
                <!-- wrapper untuk shadow & rounded, TANPA overflow-hidden -->
                <div class="bg-white shadow rounded-lg">
                    <table class="min-w-full border-collapse">
                        <thead>
                            <tr class="bg-[#1e3a5f] text-white text-left text-sm">
                                <th class="px-4 py-3">Kode</th>
                                <th class="px-4 py-3">PJ</th>
                                <th class="px-4 py-3">Ruang</th>
                                <th class="px-4 py-3">Waktu</th>
                                <th class="px-4 py-3">Kapasitas</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (!empty($bookings)): ?>
                                <?php foreach ($bookings as $i => $b): ?>
                                    <?php
                                    $id     = (int)($b['id'] ?? $b['id_bookings'] ?? 0);
                                    $status = strtolower($b['status'] ?? 'pending');

                                    // badge warna status
                                    $badgeClass = 'bg-yellow-100 text-yellow-800'; // pending
                                    if ($status === 'approved')          $badgeClass = 'bg-green-100 text-green-800';
                                    if ($status === 'rejected')          $badgeClass = 'bg-red-100 text-red-800';
                                    if ($status === 'cancelled')         $badgeClass = 'bg-red-100 text-red-800';
                                    if ($status === 'selesai')           $badgeClass = 'bg-blue-100 text-blue-800';
                                    if ($status === 'ongoing')           $badgeClass = 'bg-emerald-100 text-emerald-800';
                                    if ($status === 'reschedule_pending') $badgeClass = 'bg-purple-100 text-purple-800';
                                    ?>
                                    <tr class="<?= $i % 2 === 0 ? 'bg-gray-50' : 'bg-gray-100'; ?> text-sm text-gray-800 border-b">
                                        <td class="px-4 py-3">
                                            <?= htmlspecialchars($b['kode'] ?? $b['booking_code'] ?? '-') ?>
                                        </td>
                                        <td class="px-4 py-3">
                                            <?= htmlspecialchars($b['pj'] ?? '-') ?>
                                        </td>
                                        <td class="px-4 py-3">
                                            <?= htmlspecialchars($b['ruang'] ?? '-') ?>
                                        </td>
                                        <td class="px-4 py-3">
                                            <?= htmlspecialchars($b['waktu'] ?? '-') ?>
                                        </td>
                                        <td class="px-4 py-3">
                                            <?= htmlspecialchars($b['kapasitas'] ?? '-') ?>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $badgeClass ?>">
                                                <?= htmlspecialchars(ucfirst($status)) ?>
                                            </span>
                                        </td>

                                        <!-- AKSI DROPDOWN -->
                                        <td class="px-4 py-3 text-center">
                                            <div class="relative inline-block text-left">
                                                <button type="button"
                                                    class="inline-flex w-8 h-8 items-center justify-center rounded-full
                                                           hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-400"
                                                    onclick="toggleMenu(this)">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                        fill="currentColor" class="w-4 h-4 text-gray-600">
                                                        <path
                                                            d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 8.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM11.5 15a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0z" />
                                                    </svg>
                                                </button>

                                                <div
                                                    class="menu-panel hidden origin-top-right absolute right-0 mt-2 w-44 rounded-md shadow-lg
                                                       bg-white ring-1 ring-black ring-opacity-5 z-50">
                                                    <div class="py-1 text-sm text-gray-700">
                                                        <!-- DETAIL -->
                                                        <a href="index.php?controller=adminBooking&action=detail&id=<?= $id ?>"
                                                            class="block px-4 py-2 hover:bg-gray-100">
                                                            Detail
                                                        </a>

                                                        <!-- EDIT -->
                                                        <a href="index.php?controller=adminBooking&action=edit&id=<?= $id ?>"
                                                            class="block px-4 py-2 hover:bg-gray-100">
                                                            Edit
                                                        </a>

                                                        <!-- PROSES RESCHEDULE (jika status reschedule_pending) -->
                                                        <?php if ($status === 'reschedule_pending'): ?>
                                                            <a href="index.php?controller=adminBooking&action=processReschedule&id_booking=<?= $id ?>"
                                                                class="block px-4 py-2 hover:bg-gray-100 text-purple-700">
                                                                Proses Reschedule
                                                            </a>
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
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                        Belum ada booking yang tercatat.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
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
            panel.classList.toggle('hidden');
        }

        // klik di luar dropdown → tutup semua
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