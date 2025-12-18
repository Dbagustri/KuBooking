<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman - Kubooking</title>
    <link rel="stylesheet" href="/kubooking/public/src/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
</head>

<body class="bg-gray-50 text-slate-800">

    <?php
    $navbarPath = __DIR__ . '/../layout/navbar.php';
    if (file_exists($navbarPath)) include $navbarPath;

    $history    = $history    ?? [];
    $page       = $page       ?? 1;
    $totalPages = $totalPages ?? 1;
    $totalData  = $totalData  ?? 0;
    $startData  = $startData  ?? 0;
    $endData    = $endData    ?? 0;
    ?>
    <?php
    $flashPath = __DIR__ . '/../layout/flash.php';
    if (file_exists($flashPath)) {
        include $flashPath;
    }
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

                                switch ($statusLower) {
                                    case 'approved':
                                        $badgeClass = 'bg-emerald-50 text-emerald-700';
                                        $statusLabel = 'Disetujui';
                                        break;

                                    case 'rejected':
                                        $badgeClass = 'bg-red-50 text-red-600';
                                        $statusLabel = 'Ditolak';
                                        break;

                                    case 'selesai':
                                        $badgeClass = 'bg-blue-50 text-blue-700';
                                        $statusLabel = 'Selesai';
                                        break;

                                    case 'cancelled':
                                        $badgeClass = 'bg-gray-100 text-gray-500';
                                        $statusLabel = 'Dibatalkan';
                                        break;

                                    case 'reschedule_pending':
                                        $badgeClass = 'bg-purple-50 text-purple-700';
                                        $statusLabel = 'Menunggu Reschedule';
                                        break;

                                    case 'reschedule_approved':
                                        $badgeClass = 'bg-purple-50 text-purple-700';
                                        $statusLabel = 'Reschedule Disetujui';
                                        break;

                                    case 'reschedule_rejected':
                                        $badgeClass = 'bg-red-50 text-red-600';
                                        $statusLabel = 'Reschedule Ditolak';
                                        break;
                                }

                                // RATING ELIGIBILITY
                                $now = new DateTime();
                                $eligible = (
                                    $statusLower === 'selesai' ||
                                    ($statusLower === 'ongoing' && $end < $now)
                                );

                                $canReschedule = in_array($statusLower, ['pending', 'approved'], true)
                                    && $start > $now;

                                $canCancel = $canReschedule;
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
                                            <?= $statusLabel ?>
                                        </span>
                                    </td>

                                    <td class="py-3 px-3 text-center font-mono text-xs"><?= $h['booking_code'] ?></td>

                                    <!-- AKSI -->
                                    <td class="py-3 px-3 text-center">
                                        <!-- ⭐ RATING BUTTON -->
                                        <?php if ($eligible): ?>
                                            <?php if ((int)($h['has_rated'] ?? 0) === 0): ?>
                                                <button
                                                    class="open-rating-modal px-3 py-1 bg-yellow-100 text-yellow-700 rounded-lg text-xs"
                                                    data-id="<?= (int)$h['id_bookings'] ?>">
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

                                        <!-- ⋮ ACTION BUTTON (GLOBAL FLOATING MENU) -->
                                        <button type="button"
                                            class="ml-2 inline-flex w-8 h-8 items-center justify-center rounded-full border
                                                   hover:bg-slate-100 focus:outline-none focus:ring-2
                                                   focus:ring-offset-2 focus:ring-slate-300"
                                            onclick="openActionMenu(event, <?= (int)$h['id_bookings'] ?>, <?= $canReschedule ? 1 : 0 ?>, <?= $canCancel ? 1 : 0 ?>, <?= (int)$h['id_ruangan'] ?>)">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                fill="currentColor" class="w-4 h-4 text-slate-600">
                                                <path d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 8.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM11.5 15a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0z" />
                                            </svg>
                                        </button>
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

    <!-- GLOBAL ACTION MENU (floating, tidak ketutupan overflow) -->
    <div id="actionMenu"
        class="hidden fixed z-[9999] w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
        <div class="py-1 text-xs text-slate-700" id="actionMenuContent"></div>
    </div>

    <!-- ⭐ MODAL RATING -->
    <div id="ratingModal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center">
        <div class="bg-white w-80 rounded-xl p-5 space-y-4">

            <h2 class="text-lg font-semibold">Beri Rating</h2>

            <form action="index.php?controller=userFeedback&action=submit" method="POST">

                <input type="hidden" name="id_booking" id="rating_booking_id">

                <!-- ⭐ STAR RATING -->
                <div class="flex justify-center gap-1 text-2xl" id="starRating">
                    <button type="button" class="star text-gray-300" data-value="1">★</button>
                    <button type="button" class="star text-gray-300" data-value="2">★</button>
                    <button type="button" class="star text-gray-300" data-value="3">★</button>
                    <button type="button" class="star text-gray-300" data-value="4">★</button>
                    <button type="button" class="star text-gray-300" data-value="5">★</button>
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
        // ===== GLOBAL ACTION MENU =====
        const menuEl = document.getElementById('actionMenu');
        const contentEl = document.getElementById('actionMenuContent');
        let activeBookingId = null;

        function buildMenuHTML(idBooking, canReschedule, canCancel, idRuangan) {
            const detailUrl = `index.php?controller=userBooking&action=booking&id_ruangan=${idRuangan}&id_booking=${idBooking}`;
            const resUrl = `index.php?controller=userReschedule&action=reschedule&id_booking=${idBooking}`;

            const resItem = canReschedule ?
                `<a href="${resUrl}" class="block px-3 py-2 hover:bg-slate-50">Reschedule</a>` :
                ``;

            const cancelItem = canCancel ?
                `
            <button type="button"
                class="block w-full text-left px-3 py-2 hover:bg-slate-50"
                onclick="handleCancel(${idBooking})">
                Batalkan
            </button>
            ` :
                ``;

            return `
            <a href="${detailUrl}" class="block px-3 py-2 hover:bg-slate-50">Detail</a>
            ${resItem}
            ${cancelItem}
        `;
        }

        function openActionMenu(e, idBooking, canReschedule, canCancel, idRuangan) {
            e.stopPropagation();

            // toggle: klik tombol yang sama lagi -> tutup
            if (!menuEl.classList.contains('hidden') && activeBookingId === idBooking) {
                closeActionMenu();
                return;
            }

            activeBookingId = idBooking;
            contentEl.innerHTML = buildMenuHTML(idBooking, !!canReschedule, !!canCancel, idRuangan);

            const btnRect = e.currentTarget.getBoundingClientRect();
            const menuWidth = 160; // w-40

            let left = btnRect.right - menuWidth;
            let top = btnRect.bottom + 8;

            left = Math.max(8, Math.min(left, window.innerWidth - menuWidth - 8));

            const estimatedHeight = 140;
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

        // scroll/resize = tutup
        window.addEventListener('scroll', closeActionMenu, true);
        window.addEventListener('resize', closeActionMenu);

        // ESC = tutup
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeActionMenu();
        });

        // Cancel booking (dipanggil dari menu global)
        function handleCancel(idBooking) {
            closeActionMenu();
            if (confirm("Yakin ingin membatalkan booking ini?")) {
                window.location.href =
                    "index.php?controller=userBooking&action=cancel&id_booking=" + idBooking;
            }
        }

        // ===== RATING MODAL (⭐ bintang nyala sesuai klik) =====
        const ratingModal = document.getElementById('ratingModal');
        const ratingBookingId = document.getElementById('rating_booking_id');
        const ratingValueInput = document.getElementById('rating_value');
        const ratingCloseBtn = document.getElementById('ratingClose');

        // Pastikan HTML rating bintang pakai container id="starRating"
        // dan tiap bintang punya class="star" data-value="1..5"
        const starWrap = document.getElementById('starRating');
        const stars = starWrap ? starWrap.querySelectorAll('.star') : [];

        function setStars(rating) {
            const r = parseInt(rating || 0, 10);
            stars.forEach(star => {
                const v = parseInt(star.dataset.value || '0', 10);
                if (v <= r) {
                    star.classList.remove('text-gray-300');
                    star.classList.add('text-yellow-400');
                } else {
                    star.classList.remove('text-yellow-400');
                    star.classList.add('text-gray-300');
                }
            });
        }

        // Open rating modal
        document.querySelectorAll(".open-rating-modal").forEach(btn => {
            btn.addEventListener("click", () => {
                const id = btn.dataset.id || '';
                ratingBookingId.value = id;
                ratingValueInput.value = ''; // reset pilihan
                setStars(0);
                ratingModal.classList.remove("hidden");
            });
        });

        // Close rating modal
        if (ratingCloseBtn) {
            ratingCloseBtn.addEventListener("click", () => {
                ratingModal.classList.add("hidden");
            });
        }

        // Klik backdrop untuk tutup (opsional, aman)
        if (ratingModal) {
            ratingModal.addEventListener('click', (e) => {
                if (e.target === ratingModal) ratingModal.classList.add('hidden');
            });
        }

        // Hover & klik bintang
        stars.forEach(star => {
            star.addEventListener('mouseenter', () => setStars(star.dataset.value));
            star.addEventListener('click', () => {
                ratingValueInput.value = star.dataset.value; // klik 3 => value 3
                setStars(star.dataset.value); // nyalakan 3 bintang
            });
        });

        if (starWrap) {
            starWrap.addEventListener('mouseleave', () => {
                setStars(ratingValueInput.value || 0);
            });
        }
    </script>


</body>

</html>