<?php

use App\Core\Auth;

/** @var array $booking */
/** @var array $members */
/** @var array $disabledSlots */
/** @var array|null $reschedule */
/** @var string|null $error */

$booking       = $booking       ?? [];
$members       = $members       ?? [];
$disabledSlots = $disabledSlots ?? [];
$reschedule    = $reschedule    ?? null;
$error         = $error         ?? null;

$userSession = Auth::user();
$memberCount = is_array($members) ? count($members) : 0;

$capMin = $booking['kapasitas_min'] ?? 0;
$capMax = $booking['kapasitas_max'] ?? 0;

// apakah user sekarang PJ booking ini
$isPJ = $booking
    && $userSession
    && !empty($booking['id_pj'])
    && !empty($userSession['id_account'])
    && (int)$booking['id_pj'] === (int)$userSession['id_account'];

// id reschedule aktif (kalau ada)
$idReschedule = $reschedule['id_reschedule'] ?? null;

// Tanggal hari ini
$today = date('Y-m-d');

// Tanggal yang dipilih di form
$selectedDate = $_GET['tanggal'] ?? null;
if ($selectedDate === null) {
    if ($reschedule && !empty($reschedule['new_tanggal'])) {
        $selectedDate = $reschedule['new_tanggal'];
    } else {
        $selectedDate = $booking['tanggal'] ?? $today;
    }
}

// Default jam & durasi dari draft reschedule (kalau ada)
$defaultJamMulai = '';
$defaultDurasi   = '';

if ($reschedule && !empty($reschedule['new_start_time']) && !empty($reschedule['new_end_time'])) {
    $defaultJamMulai = date('H:i', strtotime($reschedule['new_start_time']));

    $startTs = strtotime($reschedule['new_start_time']);
    $endTs   = strtotime($reschedule['new_end_time']);
    $hours   = (int)round(($endTs - $startTs) / 3600);

    if ($hours < 1) {
        $hours = 1;
    } elseif ($hours > 3) {
        $hours = 3;
    }
    $defaultDurasi = (string)$hours; // '1','2','3'
}

// daftar jam slot 30 menit (sama pola dengan booking.php)
$timeSlots = [
    '08:00',
    '08:30',
    '09:00',
    '09:30',
    '10:00',
    '10:30',
    '11:00',
    '11:30',
];

// jam mulai yang boleh dipilih di select (per jam)
$startOptions = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00'];

// info untuk disable jam yang sudah lewat DI HARI INI
$isTodaySelected = ($selectedDate === $today);
$nowMinutes      = (int)date('H') * 60 + (int)date('i'); // menit sejak 00:00

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Kubooking - Reschedule Peminjaman</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">

    <?php
    $navbarPath = __DIR__ . '/../layout/navbar.php';
    if (file_exists($navbarPath)) {
        require $navbarPath;
    }
    ?>

    <main class="max-w-6xl mx-auto px-4 py-6 space-y-4">

        <!-- Back -->
        <a href="index.php?controller=userBooking&action=riwayat"
            class="flex items-center text-sm text-slate-600 hover:text-slate-900">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Riwayat
        </a>

        <!-- TITLE -->
        <section class="space-y-1">
            <h1 class="text-2xl font-semibold text-slate-900">
                Reschedule Peminjaman
            </h1>
            <p class="text-sm text-slate-500">
                Ubah jadwal peminjaman untuk ruangan yang sudah diajukan / disetujui.
            </p>
        </section>

        <?php if (!empty($error)): ?>
            <div class="bg-red-100 text-red-700 px-4 py-2 rounded-lg shadow mb-2 text-sm">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- MAIN CONTENT -->
        <section class="flex flex-col lg:flex-row items-start gap-6">
            <!-- LEFT COLUMN: Info booking lama + anggota jadwal baru -->
            <div class="lg:w-1/2 space-y-4">

                <!-- Room Card -->
                <article class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    <?php
                    $imgPath = !empty($booking['foto_ruangan'])
                        ? htmlspecialchars($booking['foto_ruangan'])
                        : 'rapat.png';
                    ?>
                    <div
                        class="h-48 bg-cover bg-center"
                        style="background-image: url('<?= $imgPath ?>');"></div>

                    <div class="p-4 sm:p-6 space-y-3">
                        <div>
                            <p class="text-xs font-mono tracking-wide text-slate-400 uppercase">
                                Kode Booking: <?= htmlspecialchars($booking['booking_code'] ?? '-') ?>
                            </p>
                            <h2 class="text-xl font-semibold text-slate-900">
                                <?= htmlspecialchars($booking['nama_ruangan'] ?? 'Ruangan') ?>
                            </h2>
                            <p class="text-sm text-slate-500 mt-1">
                                Lokasi: <?= htmlspecialchars($booking['lokasi'] ?? '-') ?>
                            </p>
                        </div>

                        <dl class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <dt class="text-slate-500">Jadwal Saat Ini</dt>
                                <dd class="font-medium text-slate-900">
                                    <?= date('d M Y', strtotime($booking['start_time'])) ?><br>
                                    <span class="text-xs text-slate-500">
                                        <?= date('H:i', strtotime($booking['start_time'])) ?>
                                        – <?= date('H:i', strtotime($booking['end_time'])) ?>
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-slate-500">Kapasitas</dt>
                                <dd class="font-medium text-slate-900">
                                    <?= (int)$capMin ?> – <?= (int)$capMax ?> orang
                                </dd>
                            </div>
                        </dl>
                    </div>
                </article>

                <!-- Anggota jadwal baru (draft reschedule / booking lama) -->
                <section class="bg-white rounded-2xl shadow-sm p-4 sm:p-5 space-y-3">
                    <header class="flex items-center justify-between mb-1">
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900">
                                Anggota Kelompok
                                <?php if ($idReschedule): ?>
                                    <span class="text-[11px] text-slate-400 font-normal">
                                        (jadwal baru)
                                    </span>
                                <?php endif; ?>
                            </h3>
                            <?php if ($idReschedule && $isPJ): ?>
                                <p class="text-[11px] text-slate-400">
                                    PJ dapat menambah / menghapus anggota sebelum admin menyetujui reschedule.
                                </p>
                            <?php endif; ?>
                        </div>
                        <span class="text-xs text-slate-500">
                            <?= $memberCount ?> orang
                        </span>
                    </header>

                    <?php if ($memberCount > 0): ?>
                        <div class="space-y-2 max-h-60 overflow-y-auto">
                            <?php foreach ($members as $m): ?>
                                <div class="flex items-center justify-between bg-slate-50 rounded-xl px-3 py-2 text-sm">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="h-7 w-7 rounded-full bg-slate-200 flex items-center justify-center text-[11px] font-semibold text-slate-600">
                                            <?= strtoupper(substr($m['nama'], 0, 1)) ?>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="font-medium text-slate-900">
                                                <?= htmlspecialchars($m['nama']) ?>
                                            </span>
                                        </div>
                                    </div>

                                    <?php if ($isPJ && $idReschedule && (int)$m['id_user'] !== (int)$booking['id_pj']): ?>
                                        <form method="POST"
                                            action="index.php?controller=userReschedule&action=removeRescheduleMember"
                                            onsubmit="return confirm('Keluarkan anggota ini dari jadwal baru?');">
                                            <input type="hidden" name="id_reschedule" value="<?= (int)$idReschedule ?>">
                                            <input type="hidden" name="id_user" value="<?= (int)$m['id_user'] ?>">
                                            <button class="text-xs text-slate-400 hover:text-red-500">✕</button>
                                        </form>
                                    <?php endif; ?>

                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-xs text-slate-500">
                            Belum ada anggota yang tergabung dalam booking ini.
                        </p>
                    <?php endif; ?>

                    <?php if ($isPJ): ?>
                        <div class="pt-3 border-t border-slate-100 mt-2 space-y-2">
                            <p class="text-xs font-semibold text-slate-700">
                                Kode Kelompok
                            </p>
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 rounded-lg bg-slate-100 text-sm font-mono">
                                    <?= htmlspecialchars($booking['kode_kelompok'] ?? '-') ?>
                                </span>
                                <button type="button"
                                    onclick="navigator.clipboard.writeText('<?= htmlspecialchars($booking['kode_kelompok'] ?? '') ?>'); alert('Kode kelompok disalin');"
                                    class="text-xs text-slate-500 hover:text-slate-800 border border-slate-200 rounded-full px-2 py-1">
                                    Salin
                                </button>
                            </div>
                            <p class="text-[11px] text-slate-400">
                                Bagikan kode ini ke anggota. Mereka bergabung lewat halaman
                                <span class="font-semibold">"Gabung Kelompok"</span> seperti peminjaman biasa.
                            </p>
                            <p class="text-[11px] text-slate-400">
                                Kapasitas maksimal: <?= (int)$capMax ?> orang.
                            </p>
                        </div>
                    <?php endif; ?>


                    <p class="mt-3 text-xs text-amber-600 bg-amber-50 border border-amber-100 px-3 py-2 rounded-lg">
                        <span class="font-semibold">Catatan:</span>
                        Reschedule akan mengubah jadwal untuk seluruh anggota kelompok.
                    </p>
                </section>
            </div>

            <!-- RIGHT COLUMN: Form Reschedule -->
            <section class="lg:w-1/2 bg-white rounded-2xl shadow-sm p-4 sm:p-6 space-y-5" id="form-reschedule">
                <h2 class="text-lg font-semibold text-slate-900 mb-1">
                    Pilih Jadwal Baru
                </h2>

                <form
                    action="index.php?controller=userReschedule&action=submitReschedule"
                    method="POST"
                    class="space-y-5">
                    <input type="hidden" name="id_booking" value="<?= (int)($booking['id_bookings'] ?? 0) ?>">
                    <input type="hidden" name="id_ruangan" value="<?= (int)($booking['id_ruangan'] ?? 0) ?>">

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                Tanggal baru
                            </label>
                            <input
                                type="date"
                                name="tanggal_baru"
                                id="tanggal-baru"
                                value="<?= htmlspecialchars($selectedDate) ?>"
                                min="<?= $today ?>"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm
                                       focus:border-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-300"
                                required />
                            <p class="mt-1 text-xs text-slate-500">
                                Tidak dapat memilih tanggal yang sudah lewat.
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                Jam mulai baru
                            </label>
                            <select
                                name="jam_mulai_baru"
                                id="jam_mulai_baru"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm
                                       focus:border-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-300"
                                required>
                                <option value="">Pilih jam</option>
                                <?php foreach ($startOptions as $opt):
                                    $optMinutes = (int)substr($opt, 0, 2) * 60 + (int)substr($opt, 3, 2);
                                    $optPast    = $isTodaySelected && ($optMinutes <= $nowMinutes);
                                    $isSelected = ($defaultJamMulai === $opt);
                                ?>
                                    <option
                                        value="<?= $opt ?>"
                                        <?= $optPast ? 'disabled' : '' ?>
                                        <?= $isSelected ? 'selected' : '' ?>>
                                        <?= str_replace(':', '.', $opt) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                Durasi
                            </label>
                            <select
                                name="durasi_baru"
                                id="durasi_baru"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm
                                       focus:border-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-300"
                                required>
                                <option value="">Pilih durasi</option>
                                <option value="1" <?= $defaultDurasi === '1' ? 'selected' : '' ?>>1 jam</option>
                                <option value="2" <?= $defaultDurasi === '2' ? 'selected' : '' ?>>2 jam</option>
                                <option value="3" <?= $defaultDurasi === '3' ? 'selected' : '' ?>>3 jam</option>
                            </select>
                        </div>
                    </div>

                    <!-- Jam tersedia -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="block text-sm font-medium text-slate-700">
                                Jam tersedia
                            </label>
                            <span class="text-xs text-slate-400">
                                Hijau: tersedia, Merah: penuh / sudah lewat
                            </span>
                        </div>

                        <div class="grid grid-cols-4 gap-2 text-xs sm:text-sm">
                            <?php foreach ($timeSlots as $slot):
                                $label = str_replace(':', '.', substr($slot, 0, 5));

                                // cek bentrok dari server
                                $isDisabledByBooking = in_array($slot, $disabledSlots, true);

                                // cek sudah lewat jam sekarang (kalau tanggal hari ini)
                                $slotMinutes = (int)substr($slot, 0, 2) * 60 + (int)substr($slot, 3, 2);
                                $isPast      = $isTodaySelected && ($slotMinutes <= $nowMinutes);

                                $isDisabled  = $isDisabledByBooking || $isPast;
                            ?>
                                <button
                                    type="button"
                                    data-jam="<?= $slot ?>"
                                    <?= $isDisabled ? 'data-disabled="1"' : '' ?>
                                    class="slot-btn rounded-full px-3 py-1 text-center
                                           <?= $isDisabled
                                                ? 'border border-red-200 bg-red-50 text-red-600 cursor-not-allowed'
                                                : 'border border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' ?>">
                                    <?= $label ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Ringkasan Waktu -->
                    <div class="space-y-1">
                        <p class="text-sm font-medium text-slate-700">
                            Waktu Booking Baru
                        </p>
                        <div
                            class="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50 px-3 py-2 text-sm">
                            <span class="text-slate-500">Terpilih</span>
                            <span class="font-semibold text-slate-900" id="waktu-reschedule-text">-</span>
                        </div>
                    </div>

                    <!-- Alasan -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Alasan Reschedule
                        </label>
                        <textarea
                            name="alasan"
                            rows="3"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm
                                   focus:border-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-300"
                            placeholder="Contoh: Jadwal kuliah bentrok, ingin memindahkan ke jam siang."></textarea>
                        <p class="mt-1 text-xs text-slate-400">
                            Alasan ini akan dilihat oleh admin saat memproses permintaan reschedule.
                        </p>
                    </div>

                    <div class="pt-2 space-y-2">
                        <button
                            type="submit"
                            class="w-full rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm
                                   hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-1">
                            Ajukan Reschedule
                        </button>
                        <p class="text-[11px] text-slate-400 text-center">
                            Permintaan reschedule akan menunggu persetujuan admin.
                            Jadwal lama tetap berlaku sampai disetujui.
                        </p>
                    </div>
                </form>
            </section>
        </section>
    </main>

    <script>
        const selectJamBaru = document.getElementById('jam_mulai_baru');
        const selectDurBaru = document.getElementById('durasi_baru');
        const waktuText = document.getElementById('waktu-reschedule-text');
        const slotButtons = document.querySelectorAll('.slot-btn');
        const tanggalBaru = document.getElementById('tanggal-baru');

        function pad(n) {
            return n < 10 ? '0' + n : '' + n;
        }

        function updateWaktuReschedule() {
            const jam = selectJamBaru.value;
            const dur = parseInt(selectDurBaru.value || '0', 10);

            if (!jam || !dur) {
                waktuText.textContent = '-';
                return;
            }

            const [h, m] = jam.split(':').map(Number);
            const d = new Date();
            d.setHours(h, m, 0, 0);

            const end = new Date(d.getTime() + dur * 60 * 60 * 1000);

            const startLabel = pad(d.getHours()) + '.' + pad(d.getMinutes());
            const endLabel = pad(end.getHours()) + '.' + pad(end.getMinutes());

            waktuText.textContent = startLabel + ' – ' + endLabel;
        }

        if (selectJamBaru) selectJamBaru.addEventListener('change', updateWaktuReschedule);
        if (selectDurBaru) selectDurBaru.addEventListener('change', updateWaktuReschedule);

        slotButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                if (btn.dataset.disabled === '1') return;

                const jam = btn.getAttribute('data-jam');

                if (selectJamBaru) {
                    const opt = Array.from(selectJamBaru.options).find(o => o.value === jam);
                    if (opt && !opt.disabled) {
                        selectJamBaru.value = jam;
                    }
                }

                slotButtons.forEach(b => b.classList.remove('ring-2', 'ring-slate-400'));
                btn.classList.add('ring-2', 'ring-slate-400');

                updateWaktuReschedule();
            });
        });

        // hitung label waktu sekali di awal (kalau default jam & durasi sudah di-fill dari draft)
        updateWaktuReschedule();

        // ketika tanggal diganti → reload halaman dengan query ?tanggal=...
        if (tanggalBaru) {
            tanggalBaru.addEventListener('change', () => {
                const t = tanggalBaru.value;
                if (!t) return;

                const url = new URL(window.location.href);
                const params = url.searchParams;

                params.set('tanggal', t);

                // tetap bawa id_booking di query
                <?php if (!empty($booking['id_bookings'])): ?>
                    params.set('id_booking', '<?= (int)$booking['id_bookings'] ?>');
                <?php endif; ?>

                // kalau lagi edit draft reschedule, jangan hilang id_reschedule-nya
                <?php if (!empty($idReschedule)): ?>
                    params.set('id_reschedule', '<?= (int)$idReschedule ?>');
                <?php endif; ?>

                window.location.search = params.toString();
            });
        }
    </script>

    <?php
    $footerPath = __DIR__ . '/../layout/footer.php';
    if (file_exists($footerPath)) {
        require $footerPath;
    }
    ?>

</body>

</html>