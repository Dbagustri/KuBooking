<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman - Kubooking</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="bg-gray-50 text-slate-800">

    <!-- NAVBAR INCLUDE -->
    <?php
    $navbarPath = __DIR__ . '/../layout/navbar.php';
    if (file_exists($navbarPath)) {
        include $navbarPath;
    }

    // Default values if not passed from controller
    $history    = $history    ?? [];
    $page       = $page       ?? 1;
    $totalPages = $totalPages ?? 1;
    $totalData  = $totalData  ?? 0;
    $startData  = $startData  ?? 0;
    $endData    = $endData    ?? 0;
    ?>

    <!-- MAIN CONTENT -->
    <main class="max-w-6xl mx-auto px-4 py-6 space-y-4">
        <!-- Back -->
        <a href="index.php?controller=userBooking&action=home" class="flex items-center text-sm text-slate-600 hover:text-slate-900 w-fit">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>

        <!-- TITLE -->
        <section class="space-y-1">
            <h1 class="text-2xl font-semibold text-slate-900">
                Riwayat Peminjaman
            </h1>
            <p class="text-sm text-slate-500">
                Daftar semua peminjaman ruangan yang pernah kamu lakukan.
            </p>
        </section>

        <!-- FILTER BAR (UI) -->
        <section class="bg-white rounded-2xl shadow-sm p-4 sm:p-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="w-full sm:w-1/2 flex items-center gap-2">
                <label for="search" class="text-sm text-slate-600 whitespace-nowrap">
                    Cari
                </label>
                <div class="relative flex-1">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">🔍</span>
                    <input
                        id="search"
                        type="text"
                        placeholder="Ruangan / kode booking..."
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 pl-8 pr-3 py-2 text-sm focus:border-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-300" />
                </div>
            </div>

            <div class="w-full sm:w-auto flex flex-col sm:flex-row gap-2 sm:items-center">
                <div class="flex items-center gap-2">
                    <label class="text-sm text-slate-600 whitespace-nowrap">Status</label>
                    <select
                        class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm focus:border-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-300">
                        <option>Semua</option>
                        <option>Disetujui</option>
                        <option>Pending</option>
                        <option>Ditolak</option>
                    </select>
                </div>
            </div>
        </section>

        <!-- TABLE CARD -->
        <section class="bg-white rounded-2xl shadow-sm p-4 sm:p-5">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50">
                            <th class="py-3 px-3 text-left font-semibold text-slate-600">Ruangan</th>
                            <th class="py-3 px-3 text-left font-semibold text-slate-600">Tanggal &amp; Waktu</th>
                            <th class="py-3 px-3 text-center font-semibold text-slate-600">Durasi</th>
                            <th class="py-3 px-3 text-center font-semibold text-slate-600">Status</th>
                            <th class="py-3 px-3 text-center font-semibold text-slate-600">Kode</th>
                            <th class="py-3 px-3 text-center font-semibold text-slate-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (!empty($history)): ?>
                            <?php foreach ($history as $h):
                                // Hitung Durasi (Asumsi format Y-m-d H:i:s)
                                $start = new DateTime($h['start_time']);
                                $end   = new DateTime($h['end_time']);
                                $diff  = $start->diff($end);
                                $durasiJam = $diff->h + ($diff->days * 24);

                                // Status Badge Logic
                                $status = strtolower($h['status'] ?? 'pending');
                                $badgeClass = 'bg-gray-100 text-gray-700';
                                $statusIcon = '⏳';
                                $statusLabel = 'Pending';

                                if ($status === 'approved' || $status === 'disetujui') {
                                    $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                    $statusIcon = '✔';
                                    $statusLabel = 'Disetujui';
                                } elseif ($status === 'rejected' || $status === 'ditolak') {
                                    $badgeClass = 'bg-red-50 text-red-600 border-red-200';
                                    $statusIcon = '✖';
                                    $statusLabel = 'Ditolak';
                                } elseif ($status === 'completed' || $status === 'selesai') {
                                    $badgeClass = 'bg-blue-50 text-blue-700 border-blue-200';
                                    $statusIcon = '✓';
                                    $statusLabel = 'Selesai';
                                } elseif ($status === 'cancelled' || $status === 'dibatalkan') {
                                    $badgeClass = 'bg-gray-100 text-gray-500 border-gray-200';
                                    $statusIcon = '🚫';
                                    $statusLabel = 'Dibatalkan';
                                }
                            ?>
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="py-3 px-3">
                                        <p class="font-medium text-slate-900"><?= htmlspecialchars($h['nama_ruangan'] ?? 'Unknown Room') ?></p>
                                        <p class="text-xs text-slate-500"><?= htmlspecialchars($h['lokasi'] ?? 'Lantai -') ?></p>
                                    </td>
                                    <td class="py-3 px-3">
                                        <p class="text-slate-800"><?= date('d M Y', strtotime($h['start_time'])) ?></p>
                                        <p class="text-xs text-slate-500">
                                            <?= date('H:i', strtotime($h['start_time'])) ?> – <?= date('H:i', strtotime($h['end_time'])) ?>
                                        </p>
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        <?= $durasiJam ?> jam
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-medium <?= $badgeClass ?>">
                                            <?= $statusIcon ?> <?= $statusLabel ?>
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        <span class="text-xs font-mono text-slate-700"><?= htmlspecialchars($h['booking_code'] ?? '-') ?></span>
                                    </td>

                                    <!-- AKSI: menu titik tiga -->
                                    <td class="py-3 px-3 text-center">
                                        <div class="relative inline-block text-left">
                                            <button
                                                type="button"
                                                class="action-menu-btn inline-flex items-center justify-center w-8 h-8 rounded-full border border-slate-200 text-slate-600 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-300 focus:ring-offset-1"
                                                aria-haspopup="true"
                                                aria-expanded="false">
                                                ⋮
                                            </button>

                                            <div
                                                class="action-menu hidden origin-top-right absolute right-0 mt-2 w-36 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-20">
                                                <div class="py-1 text-xs text-slate-700">
                                                    <!-- Detail -->
                                                    <a
                                                        href="index.php?controller=userBooking&action=booking&id_ruangan=<?= (int)$h['id_ruangan'] ?>&id_booking=<?= (int)$h['id_bookings'] ?>"
                                                        class="block px-3 py-1 hover:bg-slate-50">
                                                        Detail
                                                    </a>

                                                    <!-- Reschedule (endpoint bisa kamu sesuaikan nanti) -->
                                                    <?php
                                                    $statusLower = strtolower($h['status'] ?? 'pending');
                                                    $now = new DateTime();
                                                    $startTime = new DateTime($h['start_time']);
                                                    $canReschedule = in_array($statusLower, ['pending', 'approved'], true)
                                                        && $startTime > $now;
                                                    ?>
                                                    <?php if ($canReschedule): ?>
                                                        <a
                                                            href="index.php?controller=userReschedule&action=reschedule&id_booking=<?= (int)$h['id_bookings'] ?>"
                                                            class="block px-3 py-1 hover:bg-slate-50">
                                                            Reschedule
                                                        </a>
                                                    <?php endif; ?>



                                                    <!-- Batalkan (nanti arahkan ke action cancel booking kalau sudah ada) -->
                                                    <button
                                                        type="button"
                                                        class="block w-full text-left px-4 py-2 text-xs hover:bg-slate-100 cancel-booking-btn"
                                                        data-id-booking="<?= (int)$h['id_bookings'] ?>">
                                                        Batalkan
                                                    </button>


                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-500">
                                    Belum ada riwayat peminjaman.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <?php if ($totalData > 0): ?>
                <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-500">
                    <p>Menampilkan <?= $startData ?>–<?= $endData ?> dari <?= $totalData ?> peminjaman.</p>

                    <?php if ($totalPages > 1): ?>
                        <div class="inline-flex items-center gap-1">
                            <!-- PREV -->
                            <?php if ($page > 1): ?>
                                <a href="index.php?controller=userBooking&action=riwayat&page=<?= $page - 1 ?>"
                                    class="px-2 py-1 rounded-lg border border-slate-200 hover:bg-slate-50 transition">
                                    ‹
                                </a>
                            <?php else: ?>
                                <span class="px-2 py-1 rounded-lg border border-slate-100 text-slate-300 cursor-not-allowed">‹</span>
                            <?php endif; ?>

                            <!-- NUMBER LINKS -->
                            <?php
                            $startPage = max(1, $page - 2);
                            $endPage   = min($totalPages, $page + 2);

                            if ($startPage > 1) {
                                echo '<span class="px-1">...</span>';
                            }

                            for ($i = $startPage; $i <= $endPage; $i++):
                            ?>
                                <a href="index.php?controller=userBooking&action=riwayat&page=<?= $i ?>"
                                    class="px-2.5 py-1 rounded-lg border <?= $i == $page ? 'bg-slate-900 text-white border-slate-900' : 'border-slate-200 hover:bg-slate-50' ?> transition">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($endPage < $totalPages) {
                                echo '<span class="px-1">...</span>';
                            } ?>

                            <!-- NEXT -->
                            <?php if ($page < $totalPages): ?>
                                <a href="index.php?controller=userBooking&action=riwayat&page=<?= $page + 1 ?>"
                                    class="px-2 py-1 rounded-lg border border-slate-200 hover:bg-slate-50 transition">
                                    ›
                                </a>
                            <?php else: ?>
                                <span class="px-2 py-1 rounded-lg border border-slate-100 text-slate-300 cursor-not-allowed">›</span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <!-- SCRIPT: dropdown titik tiga & konfirmasi batal -->
    <script>
        // Toggle menu titik tiga
        document.addEventListener('click', function(e) {
            const isButton = e.target.closest('.action-menu-btn');
            const menus = document.querySelectorAll('.action-menu');

            // Kalau klik di tombol titik tiga
            if (isButton) {
                const menu = isButton.parentElement.querySelector('.action-menu');

                menus.forEach(m => {
                    if (m !== menu) m.classList.add('hidden');
                });

                if (menu) {
                    menu.classList.toggle('hidden');
                }
                return;
            }

            // Kalau klik di luar menu, tutup semua
            if (!e.target.closest('.action-menu')) {
                menus.forEach(m => m.classList.add('hidden'));
            }
        });

        // Tombol "Batalkan" — sekarang cuma alert, nanti bisa diarahkan ke endpoint cancel booking
        document.querySelectorAll('.cancel-booking-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const idBooking = this.dataset.idBooking;
                if (!idBooking) return;

                if (confirm('Yakin ingin membatalkan booking ini?')) {
                    window.location.href =
                        'index.php?controller=userBooking&action=cancel&id_booking=' +
                        encodeURIComponent(idBooking);
                }
            });
        });
    </script>
    <?php
    $footerPath = __DIR__ . '/../layout/footer.php';
    if (file_exists($footerPath)) {
        require $footerPath;
    }
    ?>
</body>

</html>