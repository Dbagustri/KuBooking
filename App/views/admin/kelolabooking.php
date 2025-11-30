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
            // di sini kita fallback ke salah satu agar kompatibel
            $bookings = $bookings ?? ($pendingBookings ?? []);
            ?>

            <div class="overflow-x-auto mt-4">
                <table class="min-w-full border-collapse bg-white shadow rounded-lg overflow-hidden">
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
                                // struktur menyesuaikan getPendingForDashboard()
                                $id     = (int)($b['id'] ?? $b['id_bookings'] ?? 0);
                                $status = strtolower($b['status'] ?? 'pending');

                                // badge warna status
                                $badgeClass = 'bg-yellow-100 text-yellow-800'; // default pending
                                if ($status === 'approved')   $badgeClass = 'bg-green-100 text-green-800';
                                if ($status === 'rejected')   $badgeClass = 'bg-red-100 text-red-800';
                                if ($status === 'cancelled')  $badgeClass = 'bg-red-100 text-red-800';
                                if ($status === 'selesai')    $badgeClass = 'bg-blue-100 text-blue-800';
                                if ($status === 'ongoing')    $badgeClass = 'bg-emerald-100 text-emerald-800';
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

                                    <!-- AKSI: DETAIL / EDIT / HAPUS -->
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-2">

                                            <!-- DETAIL -->
                                            <a href="index.php?controller=adminBooking&action=detail&id=<?= $id ?>"
                                                class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold
                                                      bg-slate-100 text-slate-700 hover:bg-slate-200">
                                                Detail
                                            </a>

                                            <!-- EDIT -->
                                            <a href="index.php?controller=adminBooking&action=edit&id=<?= $id ?>"
                                                class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold
                                                      bg-amber-100 text-amber-800 hover:bg-amber-200">
                                                Edit
                                            </a>

                                            <!-- HAPUS -->
                                            <form action="index.php?controller=adminBooking&action=delete" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus booking ini?');">
                                                <input type="hidden" name="id_booking" value="<?= $id ?>">
                                                <button type="submit"
                                                    class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold
                                                               bg-red-100 text-red-700 hover:bg-red-200">
                                                    Hapus
                                                </button>
                                            </form>
                                            <?php if ($booking['status'] === 'approved' && empty($booking['checkin_time'])): ?>
                                                <form method="POST" action="index.php?controller=adminBooking&action=start" style="display:inline">
                                                    <input type="hidden" name="id_booking" value="<?= htmlspecialchars($booking['id_bookings']) ?>">
                                                    <button type="submit" class="px-3 py-1 rounded bg-blue-600 text-white text-sm">
                                                        Mulai
                                                    </button>
                                                </form>
                                            <?php endif; ?>


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

</body>

</html>