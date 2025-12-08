<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman - Kubooking</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
</head>

<body class="bg-gray-50 text-slate-800">

    <?php
    // Include navbar
    $navbarPath = __DIR__ . '/../layout/navbar.php';
    if (file_exists($navbarPath)) include $navbarPath;

    // default variables
    $history    = $history    ?? [];
    $page       = $page       ?? 1;
    $totalPages = $totalPages ?? 1;
    $totalData  = $totalData  ?? 0;
    $startData  = $startData  ?? 0;
    $endData    = $endData    ?? 0;
    ?>

    <main class="max-w-6xl mx-auto px-4 py-6 space-y-4">

        <a href="index.php?controller=userBooking&action=home"
            class="flex items-center text-sm text-slate-600 hover:text-slate-900 w-fit">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>

        <section class="space-y-1">
            <h1 class="text-2xl font-semibold text-slate-900">Riwayat Peminjaman</h1>
            <p class="text-sm text-slate-500">Daftar semua peminjaman ruangan yang pernah kamu lakukan.</p>
        </section>

        <!-- FILTER BAR -->
        <section class="bg-white rounded-2xl shadow-sm p-4 sm:p-5 flex flex-col gap-3 sm:flex-row sm:justify-between">
            <div class="w-full sm:w-1/2 flex items-center gap-2">
                <label class="text-sm text-slate-600">Cari</label>
                <div class="relative flex-1">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">🔍</span>
                    <input
                        type="text"
                        placeholder="Ruangan / kode booking..."
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 pl-8 pr-3 py-2 text-sm focus:outline-none focus:ring-slate-300">
                </div>
            </div>

            <div class="w-full sm:w-auto flex items-center gap-2">
                <label class="text-sm text-slate-600">Status</label>
                <select class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
                    <option>Semua</option>
                    <option>Disetujui</option>
                    <option>Pending</option>
                    <option>Ditolak</option>
                </select>
            </div>
        </section>

        <!-- TABLE -->
        <section class="bg-white rounded-2xl shadow-sm p-4 sm:p-5">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b bg-slate-50">
                            <th class="py-3 px-3 text-left">Ruangan</th>
                            <th class="py-3 px-3 text-left">Tanggal & Waktu</th>
                            <th class="py-3 px-3 text-center">Durasi</th>
                            <th class="py-3 px-3 text-center">Status</th>
                            <th class="py-3 px-3 text-center">Kode</th>
                            <th class="py-3 px-3 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        <?php if (!empty($history)): ?>
                            <?php foreach ($history as $h):

                                $start = new DateTime($h['start_time']);
                                $end   = new DateTime($h['end_time']);
                                $durasiJam = $start->diff($end)->h + ($start->diff($end)->days * 24);

                                $statusLower = strtolower($h['status']);

                                // Badge style
                                $badgeClass = 'bg-gray-100 text-gray-600';
                                $statusLabel = 'Pending';
                                $statusIcon = '⏳';

                                switch ($statusLower) {
                                    case 'approved':
                                        $badgeClass = 'bg-emerald-50 text-emerald-700';
                                        $statusLabel = 'Disetujui';
                                        $statusIcon = '✔';
                                        break;

                                    case 'rejected':
                                        $badgeClass = 'bg-red-50 text-red-600';
                                        $statusLabel = 'Ditolak';
                                        $statusIcon = '✖';
                                        break;

                                    case 'selesai':
                                        $badgeClass = 'bg-blue-50 text-blue-700';
                                        $statusLabel = 'Selesai';
                                        $statusIcon = '✓';
                                        break;

                                    case 'cancelled':
                                        $badgeClass = 'bg-gray-100 text-gray-500';
                                        $statusLabel = 'Dibatalkan';
                                        $statusIcon = '🚫';
                                        break;

                                    case 'reschedule_pending':
                                        $badgeClass = 'bg-purple-50 text-purple-700';
                                        $statusLabel = 'Menunggu Reschedule';
                                        $statusIcon = '🔁';
                                        break;

                                    case 'reschedule_approved':
                                        $badgeClass = 'bg-purple-50 text-purple-700';
                                        $statusLabel = 'Reschedule Disetujui';
                                        $statusIcon = '🔁';
                                        break;

                                    case 'reschedule_rejected':
                                        $badgeClass = 'bg-red-50 text-red-600';
                                        $statusLabel = 'Reschedule Ditolak';
                                        $statusIcon = '✖';
                                        break;
                                }

                                // RATING ELIGIBILITY
                                $now = new DateTime();
                                $eligible = (
                                    $statusLower === 'selesai' ||
                                    ($statusLower === 'ongoing' && $end < $now)
                                );
                            ?>

                                <tr class="hover:bg-slate-50">
                                    <td class="py-3 px-3">
                                        <p class="font-medium"><?= $h['nama_ruangan'] ?></p>
                                        <p class="text-xs text-slate-500"><?= $h['lokasi'] ?></p>
                                    </td>

                                    <td class="py-3 px-3">
                                        <p><?= date('d M Y', strtotime($h['start_time'])) ?></p>
                                        <p class="text-xs text-slate-500">
                                            <?= date('H:i', strtotime($h['start_time'])) ?>–<?= date('H:i', strtotime($h['end_time'])) ?>
                                        </p>
                                    </td>

                                    <td class="py-3 px-3 text-center"><?= $durasiJam ?> jam</td>

                                    <td class="py-3 px-3 text-center">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium border <?= $badgeClass ?>">
                                            <?= $statusIcon ?> <?= $statusLabel ?>
                                        </span>
                                    </td>

                                    <td class="py-3 px-3 text-center font-mono text-xs"><?= $h['booking_code'] ?></td>

                                    <td class="py-3 px-3 text-center">

                                        <!-- ⭐ RATING BUTTON -->
                                        <?php if ($eligible): ?>
                                            <?php if ($h['has_rated'] == 0): ?>
                                                <button
                                                    class="open-rating-modal px-3 py-1 bg-yellow-100 text-yellow-700 rounded-lg text-xs"
                                                    data-id="<?= $h['id_bookings'] ?>">
                                                    ⭐ Beri Rating
                                                </button>
                                            <?php else: ?>
                                                <button
                                                    disabled
                                                    class="px-3 py-1 bg-gray-100 text-gray-400 rounded-lg text-xs cursor-not-allowed">
                                                    ⭐ Sudah Dinilai
                                                </button>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <!-- ⋮ ACTION MENU -->
                                        <div class="relative inline-block text-left ml-2">
                                            <button class="action-menu-btn w-8 h-8 flex items-center justify-center rounded-full border">
                                                ⋮
                                            </button>

                                            <?php
                                            $canReschedule = in_array($statusLower, ['pending', 'approved'], true)
                                                && $start > $now;

                                            $canCancel = $canReschedule;
                                            ?>

                                            <div class="action-menu hidden absolute right-0 mt-2 w-40 bg-white rounded-md shadow-lg p-1 text-xs">
                                                <a href="index.php?controller=userBooking&action=booking&id_ruangan=<?= $h['id_ruangan'] ?>&id_booking=<?= $h['id_bookings'] ?>"
                                                    class="block px-3 py-1 hover:bg-slate-50">Detail</a>

                                                <?php if ($canReschedule): ?>
                                                    <a href="index.php?controller=userReschedule&action=reschedule&id_booking=<?= $h['id_bookings'] ?>"
                                                        class="block px-3 py-1 hover:bg-slate-50">Reschedule</a>
                                                <?php endif; ?>

                                                <?php if ($canCancel): ?>
                                                    <button
                                                        class="cancel-booking-btn block w-full text-left px-3 py-1 hover:bg-slate-50"
                                                        data-id-booking="<?= $h['id_bookings'] ?>">
                                                        Batalkan
                                                    </button>
                                                <?php endif; ?>
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
                <div class="mt-4 flex justify-between text-xs text-slate-500">
                    <p>Menampilkan <?= $startData ?>–<?= $endData ?> dari <?= $totalData ?> peminjaman.</p>

                    <?php if ($totalPages > 1): ?>
                        <div class="flex items-center gap-1">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?= $page - 1 ?>" class="px-2 py-1 border rounded-lg">‹</a>
                            <?php else: ?>
                                <span class="px-2 py-1 border rounded-lg text-slate-300">‹</span>
                            <?php endif; ?>

                            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                <a href="?page=<?= $i ?>"
                                    class="px-2.5 py-1 border rounded-lg <?= $i == $page ? 'bg-slate-900 text-white' : '' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($page < $totalPages): ?>
                                <a href="?page=<?= $page + 1 ?>" class="px-2 py-1 border rounded-lg">›</a>
                            <?php else: ?>
                                <span class="px-2 py-1 border rounded-lg text-slate-300">›</span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <!-- ⭐ MODAL RATING -->
    <div id="ratingModal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center">
        <div class="bg-white w-80 rounded-xl p-5 space-y-4">

            <h2 class="text-lg font-semibold">Beri Rating</h2>

            <form action="index.php?controller=userFeedback&action=submit" method="POST">

                <input type="hidden" name="id_booking" id="rating_booking_id">

                <div class="flex justify-center gap-2 text-2xl">
                    <span class="rating-star cursor-pointer" data-value="1">😡</span>
                    <span class="rating-star cursor-pointer" data-value="2">😞</span>
                    <span class="rating-star cursor-pointer" data-value="3">😐</span>
                    <span class="rating-star cursor-pointer" data-value="4">😊</span>
                    <span class="rating-star cursor-pointer" data-value="5">🤩</span>
                </div>

                <input type="hidden" name="rating" id="rating_value">

                <textarea
                    name="komentar"
                    rows="3"
                    placeholder="Komentar (opsional)"
                    class="w-full border rounded-lg p-2 text-sm"></textarea>

                <div class="flex justify-end gap-2">
                    <button type="button" id="ratingClose" class="text-sm text-slate-600">Batal</button>
                    <button type="submit"
                        class="px-3 py-1 bg-blue-600 text-white text-sm rounded-lg">
                        Kirim
                    </button>
                </div>

            </form>
        </div>
    </div>


    <!-- JS -->
    <script>
        // Open rating modal
        document.querySelectorAll(".open-rating-modal").forEach(btn => {
            btn.addEventListener("click", () => {
                document.querySelector("#rating_booking_id").value = btn.dataset.id;
                document.querySelector("#ratingModal").classList.remove("hidden");
            });
        });

        // Close rating modal
        document.querySelector("#ratingClose").addEventListener("click", () => {
            document.querySelector("#ratingModal").classList.add("hidden");
        });

        // Rating selection
        document.querySelectorAll(".rating-star").forEach(star => {
            star.addEventListener("click", () => {
                document.querySelector("#rating_value").value = star.dataset.value;

                document.querySelectorAll(".rating-star")
                    .forEach(s => s.classList.remove("opacity-30"));

                star.classList.add("opacity-100");
            });
        });

        // ⋮ dropdown
        document.addEventListener("click", e => {
            const btn = e.target.closest(".action-menu-btn");
            const menus = document.querySelectorAll(".action-menu");

            if (btn) {
                const menu = btn.parentElement.querySelector(".action-menu");
                menus.forEach(m => m !== menu && m.classList.add("hidden"));
                menu.classList.toggle("hidden");
                return;
            }

            if (!e.target.closest(".action-menu"))
                menus.forEach(m => m.classList.add("hidden"));
        });

        // Cancel booking
        document.querySelectorAll(".cancel-booking-btn").forEach(btn => {
            btn.addEventListener("click", () => {
                if (confirm("Yakin ingin membatalkan booking ini?")) {
                    window.location.href =
                        "index.php?controller=userBooking&action=cancel&id_booking=" + btn.dataset.idBooking;
                }
            });
        });
    </script>

</body>

</html>