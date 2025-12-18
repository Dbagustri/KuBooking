<?php

use App\Core\Auth;

/** @var array $booking */
/** @var array $members */
/** @var array|null $reschedule */
/** @var array $disabledSlots */
$today         = date('Y-m-d');
$booking       = $booking ?? [];
$reschedule    = $reschedule ?? null;
$members       = $members ?? [];
$disabledSlots = $disabledSlots ?? [];

// data dari booking lama
$idBooking    = (int)($booking['id_bookings'] ?? 0);
$idRuangan    = (int)($booking['id_ruangan'] ?? 0);
$kodeKelompok = $booking['kode_kelompok'] ?? null;

$oldTanggal = $booking['tanggal'] ?? $today;
$oldStart   = $booking['start_time'] ?? ($today . ' 08:00:00');
$oldEnd     = $booking['end_time'] ?? ($today . ' 09:00:00');

$oldStartLabel = date('H:i', strtotime($oldStart));
$oldEndLabel   = date('H:i', strtotime($oldEnd));
$selectedDate = $_GET['tanggal'] ?? null;

if ($reschedule) {
    if (!$selectedDate) {
        $selectedDate = $reschedule['new_tanggal'];
    }
    $newStartTime = $reschedule['new_start_time'] ?? null;
    $newEndTime   = $reschedule['new_end_time'] ?? null;
    $joinUntil    = $reschedule['join_reschedule_until'] ?? null;
} else {
    if (!$selectedDate) {
        $selectedDate = $oldTanggal;
    }
    $newStartTime = null;
    $newEndTime   = null;
    $joinUntil    = null;
}
$selectedJamMulai = null;
$selectedDurasi   = null;

if ($newStartTime && $newEndTime) {
    $selectedJamMulai = date('H:i', strtotime($newStartTime));
    $diffSeconds      = strtotime($newEndTime) - strtotime($newStartTime);
    $selectedDurasi   = max(1, (int)round($diffSeconds / 3600)); // 1–3 jam
}

// kapasitas
$capMin      = (int)($booking['kapasitas_min'] ?? 0);
$capMax      = (int)($booking['kapasitas_max'] ?? 0);
$memberCount = is_array($members) ? count($members) : 0;

// identifikasi PJ
$userSession = Auth::user();
$isPJ        = $booking
    && $userSession
    && !empty($booking['id_pj'])
    && !empty($userSession['id_account'])
    && ((int)$booking['id_pj'] === (int)$userSession['id_account']);

// sederhanakan akses ke id_reschedule & submitted
$rescheduleId          = $reschedule['id_reschedule'] ?? null;
$isRescheduleSubmitted = (int)($reschedule['submitted'] ?? 0) === 1;

// slot 1 jam untuk visual
$timeSlots = [
    '08:00',
    '09:00',
    '10:00',
    '11:00',
    '12:00',
    '13:00',
    '14:00',
    '15:00',
    '16:00',
];

// pilihan jam mulai tiap jam
$startOptions = $timeSlots;

// flag hari
$isTodaySelected = ($selectedDate === $today);
$isPastDay       = ($selectedDate < $today);

// menit sekarang (pakai timezone server)
$nowMinutes = (int)date('H') * 60 + (int)date('i'); // menit sejak 00:00

// jam operasional & istirahat berdasar hari (PHP)
$selectedTs = strtotime($selectedDate);
$dayOfWeek  = (int)date('N', $selectedTs); // 1 = Senin ... 5 = Jumat
$isFriday   = ($dayOfWeek === 5);

if ($isFriday) {
    // Jumat: 08.00–16.50 (dibulatkan ke 17.00), istirahat 11.00–13.00
    $openMinutes  = 8 * 60;
    $closeMinutes = 17 * 60; // 17.00
    $breakStart   = 11 * 60;
    $breakEnd     = 13 * 60;
} else {
    // Sen–Kam (dan selain Jumat): 08.00–15.50 (dibulatkan ke 16.00), istirahat 12.00–13.00
    $openMinutes  = 8 * 60;
    $closeMinutes = 16 * 60; // 16.00
    $breakStart   = 12 * 60;
    $breakEnd     = 13 * 60;
}

// cek apakah batas join reschedule sudah lewat
$joinExpired = false;
if (!empty($joinUntil)) {
    $joinExpired = (strtotime($joinUntil) <= time());
}

?>
<?php
$flashPath = __DIR__ . '/../layout/flash.php';
if (file_exists($flashPath)) {
    include $flashPath;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Roomify - Reschedule Ruangan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">

    <?php require __DIR__ . '/../layout/navbar.php'; ?>

    <main class="max-w-6xl mx-auto px-4 py-6 space-y-4">

        <!-- Back -->
        <a href="index.php?controller=userBooking&action=riwayat"
            class="flex items-center text-sm text-slate-600 hover:text-slate-900">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Riwayat
        </a>

        <?php if (!empty($error)): ?>
            <div class="bg-red-100 text-red-700 px-4 py-2 rounded-lg shadow mb-2">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- MAIN CONTENT -->
        <section class="flex flex-col lg:flex-row items-start gap-6">

            <!-- LEFT COLUMN -->
            <div class="lg:w-1/2 space-y-4">

                <!-- Room & Jadwal Card -->
                <article class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    <?php
                    $imgPath = !empty($booking['foto_ruangan'])
                        ? htmlspecialchars($booking['foto_ruangan'])
                        : 'img/rapat.png';
                    ?>
                    <div class="h-48 bg-cover bg-center"
                        style="background-image: url('<?= $imgPath ?>');"></div>

                    <div class="p-4 sm:p-6 space-y-4">
                        <div>
                            <h1 class="text-xl font-semibold text-slate-900">
                                <?= htmlspecialchars($booking['nama_ruangan'] ?? 'Ruangan') ?>
                            </h1>
                            <p class="text-xs text-slate-500 mt-1">
                                Jadwal ulang peminjaman ruangan ini.
                            </p>
                        </div>

                        <dl class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <dt class="text-slate-500">Tanggal Lama</dt>
                                <dd class="font-medium text-slate-900">
                                    <?= date('d M Y', strtotime($oldTanggal)) ?>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-slate-500">Waktu Lama</dt>
                                <dd class="font-medium text-slate-900">
                                    <?= $oldStartLabel ?> – <?= $oldEndLabel ?>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-slate-500">Kapasitas</dt>
                                <dd class="font-medium text-slate-900">
                                    <?= $capMin ?> – <?= $capMax ?> orang
                                </dd>
                            </div>
                            <div>
                                <dt class="text-slate-500">Kode Kelompok</dt>
                                <dd class="font-medium text-slate-900 flex items-center gap-2">
                                    <span id="kodeKelompokText">
                                        <?= htmlspecialchars($kodeKelompok ?? '-') ?>
                                    </span>
                                    <?php if (!empty($kodeKelompok)): ?>
                                        <button type="button"
                                            onclick="copyKodeKelompok()"
                                            class="text-[11px] text-slate-500 hover:text-slate-800 border border-slate-200 rounded-full px-2 py-1">
                                            Salin
                                        </button>
                                    <?php endif; ?>
                                </dd>
                            </div>
                        </dl>

                        <!-- Info Jadwal Baru -->
                        <div class="mt-2 border-t border-slate-100 pt-3 text-sm space-y-1">
                            <p class="text-slate-500">Jadwal Baru (Draft Reschedule)</p>
                            <?php if ($newStartTime && $newEndTime): ?>
                                <p class="font-medium text-slate-900">
                                    <?= date('d M Y', strtotime($selectedDate)) ?>,
                                    <?= date('H:i', strtotime($newStartTime)) ?> –
                                    <?= date('H:i', strtotime($newEndTime)) ?>
                                </p>
                            <?php else: ?>
                                <p class="text-slate-500">
                                    Jadwal baru belum diatur. Silakan isi form di kanan dan klik
                                    <span class="font-semibold">"Edit Kelompok (Simpan Jadwal Baru)"</span>.
                                </p>
                            <?php endif; ?>

                            <?php if ($joinUntil && $reschedule && !$isRescheduleSubmitted): ?>
                                <?php if (!$joinExpired): ?>
                                    <p class="text-xs text-slate-500">
                                        Batas anggota lain untuk bergabung ke jadwal baru sampai:
                                        <span class="font-semibold text-slate-900" id="join-deadline">
                                            <?= date('d M Y H:i', strtotime($joinUntil)) ?>
                                        </span>
                                    </p>
                                    <p class="text-xs text-slate-500">
                                        Waktu tersisa:
                                        <span class="font-semibold text-slate-900" id="countdown"></span>
                                    </p>
                                <?php else: ?>
                                    <p class="text-xs text-red-600 font-medium">
                                        Waktu anggota lain untuk bergabung ke jadwal reschedule ini sudah habis.
                                    </p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>

                <!-- GROUP RESCHEDULE SECTION -->
                <section class="bg-white rounded-2xl shadow-sm p-4 sm:p-5 space-y-4">
                    <header class="flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">
                                Kelompok Jadwal Baru
                            </h2>
                            <p class="text-xs text-slate-500">
                                Anggota yang akan mengikuti jadwal reschedule.
                            </p>
                        </div>
                        <div class="text-right text-sm">
                            <p class="text-slate-500">Anggota</p>
                            <p class="font-semibold text-slate-900">
                                <?= $memberCount ?> orang
                            </p>
                        </div>
                    </header>

                    <?php if (empty($members)): ?>
                        <p class="text-sm text-slate-500">
                            Belum ada anggota. Saat pertama kali menyimpan reschedule,
                            anggota dari jadwal lama akan disalin ke jadwal baru.
                        </p>
                    <?php else: ?>
                        <div class="space-y-2">
                            <?php foreach ($members as $m): ?>
                                <div class="flex items-center justify-between bg-slate-50 rounded-xl px-3 py-2">
                                    <div class="flex items-center gap-2 text-sm">
                                        <div class="h-7 w-7 rounded-full bg-slate-200"></div>
                                        <div class="flex flex-col">
                                            <span><?= htmlspecialchars($m['nama']) ?></span>
                                            <?php if (!empty($booking['id_pj']) && $m['id_user'] == $booking['id_pj']): ?>
                                                <span class="text-[11px] uppercase tracking-wide text-emerald-600 font-semibold">
                                                    PJ
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <?php if ($isPJ && $rescheduleId && $m['id_user'] != $booking['id_pj'] && !$isRescheduleSubmitted): ?>
                                        <form method="POST"
                                            action="index.php?controller=userReschedule&action=removeRescheduleMember">
                                            <input type="hidden" name="id_reschedule" value="<?= (int)$rescheduleId ?>">
                                            <input type="hidden" name="id_user" value="<?= (int)$m['id_user'] ?>">
                                            <button
                                                class="text-xs text-slate-400 hover:text-red-500"
                                                title="Keluarkan anggota dari jadwal baru">
                                                ✕
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>

            </div>

            <!-- RIGHT COLUMN: FORM RESCHEDULE -->
            <section class="lg:w-1/2 bg-white rounded-2xl shadow-sm p-4 sm:p-6 space-y-5" id="form-reschedule">
                <h2 class="text-lg font-semibold text-slate-900 mb-1">
                    Reschedule Jadwal
                </h2>
                <p class="text-xs text-slate-500 mb-3">
                    Pilih tanggal &amp; jam baru, kemudian simpan sebagai draft.
                    Setelah anggota jadwal baru sudah sesuai, klik
                    <span class="font-semibold">"Ajukan Reschedule"</span>
                    untuk mengirim permintaan ke admin.
                </p>

                <?php if (!$isPJ): ?>
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-sm text-slate-600">
                        Jadwal reschedule hanya bisa diubah oleh
                        <span class="font-semibold">Penanggung Jawab (PJ)</span> kelompok.
                        Kamu hanya dapat melihat informasi jadwal dan status reschedule.
                    </div>
                <?php else: ?>

                    <!-- FORM EDIT JADWAL (DRAFT) – hanya PJ -->
                    <form action="index.php?controller=userReschedule&action=submitReschedule"
                        method="POST"
                        class="space-y-5">
                        <input type="hidden" name="id_booking" value="<?= $idBooking ?>">
                        <input type="hidden" name="id_ruangan" value="<?= $idRuangan ?>">

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-1">
                                    Tanggal Baru
                                </label>
                                <input
                                    type="date"
                                    name="tanggal_baru"
                                    id="tanggal_baru"
                                    value="<?= htmlspecialchars($selectedDate) ?>"
                                    min="<?= $today ?>"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm
                                           focus:border-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-300"
                                    required />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">
                                    Jam Mulai Baru
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

                                        // flag untuk hari
                                        $optPastDay   = $isPastDay; // kalau tanggal sudah lewat, semua jam nonaktif
                                        $optPastToday = $isTodaySelected && ($optMinutes <= $nowMinutes);

                                        // di luar jam buka / tutup
                                        $outsideOp = ($optMinutes < $openMinutes || $optMinutes >= $closeMinutes);

                                        // jam istirahat
                                        $insideBreak = ($optMinutes >= $breakStart && $optMinutes < $breakEnd);

                                        // sudah bentrok
                                        $isBooked = in_array($opt, $disabledSlots, true);

                                        $disabled = $optPastDay || $optPastToday || $outsideOp || $insideBreak || $isBooked;
                                        $selected = ($selectedJamMulai === $opt) ? 'selected' : '';
                                    ?>
                                        <option
                                            value="<?= $opt ?>"
                                            <?= $disabled ? 'disabled' : '' ?>
                                            <?= $selected ?>>
                                            <?= str_replace(':', '.', $opt) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">
                                    Durasi Baru
                                </label>
                                <select
                                    name="durasi_baru"
                                    id="durasi_baru"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm
                                           focus:border-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-300"
                                    required>
                                    <option value="">Pilih durasi</option>
                                    <option value="1" <?= $selectedDurasi === 1 ? 'selected' : '' ?>>1 jam</option>
                                    <option value="2" <?= $selectedDurasi === 2 ? 'selected' : '' ?>>2 jam</option>
                                    <option value="3" <?= $selectedDurasi === 3 ? 'selected' : '' ?>>3 jam</option>
                                </select>
                            </div>
                        </div>

                        <!-- Jam tersedia -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="block text-sm font-medium text-slate-700">
                                    Jam Tersedia (Tanggal Dipilih)
                                </label>
                                <span class="text-xs text-slate-400">
                                    Hijau: tersedia, Merah: penuh / istirahat / di luar jam buka / sudah lewat
                                </span>
                            </div>

                            <div class="grid grid-cols-3 sm:grid-cols-4 gap-2 text-xs sm:text-sm">
                                <?php foreach ($timeSlots as $slot):
                                    $label       = str_replace(':', '.', substr($slot, 0, 5));
                                    $slotMinutes = (int)substr($slot, 0, 2) * 60 + (int)substr($slot, 3, 2);

                                    $isDisabledByBooking = in_array($slot, $disabledSlots, true);

                                    $isPastDaySlot   = $isPastDay;
                                    $isPastTodaySlot = $isTodaySelected && ($slotMinutes <= $nowMinutes);

                                    $outsideOp   = ($slotMinutes < $openMinutes || $slotMinutes >= $closeMinutes);
                                    $insideBreak = ($slotMinutes >= $breakStart && $slotMinutes < $breakEnd);

                                    $isDisabled = $isDisabledByBooking || $isPastDaySlot || $isPastTodaySlot || $outsideOp || $insideBreak;
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

                        <!-- Ringkasan waktu -->
                        <div class="space-y-1">
                            <p class="text-sm font-medium text-slate-700">
                                Waktu Reschedule
                            </p>
                            <?php
                            $displayWaktu = '-';
                            if ($selectedJamMulai && $selectedDurasi) {
                                $tmpStart = strtotime($selectedJamMulai);
                                $tmpEnd   = strtotime($selectedJamMulai . " + {$selectedDurasi} hour");
                                $displayWaktu =
                                    date('H.i', $tmpStart) . ' – ' . date('H.i', $tmpEnd);
                            }
                            ?>
                            <div
                                class="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50 px-3 py-2 text-sm">
                                <span class="text-slate-500">Terpilih</span>
                                <span class="font-semibold text-slate-900" id="waktu-reschedule-text">
                                    <?= $displayWaktu ?>
                                </span>
                            </div>
                        </div>

                        <!-- Alasan Reschedule -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                Alasan Reschedule
                            </label>
                            <textarea
                                name="alasan"
                                rows="3"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm
                                       focus:border-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-300"
                                placeholder="Contoh: Jadwal bentrok dengan ujian, menyesuaikan ketersediaan anggota, dsb."><?= htmlspecialchars($reschedule['alasan'] ?? '') ?></textarea>
                        </div>

                        <div class="pt-2">
                            <button
                                type="submit"
                                class="w-full rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm
                                       hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-1">
                                Edit Kelompok (Simpan Jadwal Baru)
                            </button>
                        </div>
                    </form>

                    <!-- FORM AJUKAN RESCHEDULE – hanya PJ -->
                    <form action="index.php?controller=userReschedule&action=finalizeReschedule"
                        method="POST"
                        class="pt-2">
                        <input type="hidden" name="id_booking" value="<?= $idBooking ?>">
                        <input type="hidden" name="id_reschedule" value="<?= (int)$rescheduleId ?>">

                        <?php
                        // tombol aktif hanya kalau sudah ada draft reschedule dan belum submitted
                        $disableFinalize = empty($rescheduleId) || $isRescheduleSubmitted;
                        ?>
                        <button
                            type="submit"
                            <?= $disableFinalize ? 'disabled' : '' ?>
                            class="w-full rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm
                                   hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-1
                                   <?= $disableFinalize ? 'opacity-40 cursor-not-allowed' : '' ?>">
                            Ajukan Reschedule
                        </button>
                        <?php if ($disableFinalize && !$isRescheduleSubmitted): ?>
                            <p class="mt-1 text-xs text-slate-500">
                                Simpan dulu jadwal baru dengan tombol
                                <span class="font-semibold">"Edit Kelompok"</span>
                                sebelum mengajukan reschedule.
                            </p>
                        <?php endif; ?>
                    </form>

                <?php endif; // end if isPJ 
                ?>
            </section>
        </section>
    </main>

    <!-- JS utama -->
    <script>
        function copyKodeKelompok() {
            const el = document.getElementById('kodeKelompokText');
            if (!el) return;
            const text = el.textContent.trim();
            if (!text) return;
            navigator.clipboard.writeText(text).then(() => {
                alert('Kode kelompok disalin: ' + text);
            });
        }

        const selectJam = document.getElementById('jam_mulai_baru');
        const selectDur = document.getElementById('durasi_baru');
        const waktuText = document.getElementById('waktu-reschedule-text');
        const slotButtons = document.querySelectorAll('.slot-btn');
        const tanggalInput = document.getElementById('tanggal_baru');

        function pad(n) {
            return n < 10 ? '0' + n : '' + n;
        }

        // ==== INFO JAM OPERASIONAL & ISTIRAHAT (JS) ====
        function getDayInfo(dateStr) {
            const d = dateStr ? new Date(dateStr + 'T00:00:00') : new Date();
            const day = d.getDay(); // 0 = Minggu, 1 = Senin, ..., 5 = Jumat

            // default Sen–Kam (dan selain Jumat)
            let info = {
                openMinutes: 8 * 60,
                closeMinutes: 16 * 60, // 16.00 (dibulatkan dari 15.50)
                breakStartMinutes: 12 * 60,
                breakEndMinutes: 13 * 60
            };

            // Jumat
            if (day === 5) {
                info = {
                    openMinutes: 8 * 60,
                    closeMinutes: 17 * 60, // 17.00 (dibulatkan dari 16.50)
                    breakStartMinutes: 11 * 60,
                    breakEndMinutes: 13 * 60
                };
            }

            return info;
        }

        // Hitung durasi maksimal yang boleh dari jam mulai tertentu
        function getMaxDuration(startTime, dateStr) {
            if (!startTime) return 0;

            const [h, m] = startTime.split(':').map(Number);
            const startMinutes = h * 60 + m;
            const info = getDayInfo(dateStr);

            const open = info.openMinutes;
            const close = info.closeMinutes;
            const bStart = info.breakStartMinutes;
            const bEnd = info.breakEndMinutes;

            if (startMinutes < open || startMinutes >= close) {
                return 0;
            }

            let maxDur = 0;

            // coba durasi 1–3 jam
            for (let dur = 1; dur <= 3; dur++) {
                const endMinutes = startMinutes + dur * 60;

                // tidak boleh lewat jam tutup
                if (endMinutes > close) break;

                // tidak boleh memotong jam istirahat
                const kenaIstirahat = (startMinutes < bEnd && endMinutes > bStart);
                if (kenaIstirahat) continue;

                maxDur = dur;
            }

            return maxDur;
        }

        // aktif/nonaktifkan pilihan durasi berdasarkan jam & hari
        function updateDurasiOptions() {
            if (!selectDur) return;

            const dateStr = tanggalInput ? tanggalInput.value : '';
            const jamMulai = selectJam ? selectJam.value : '';

            const opts = Array.from(selectDur.options).filter(o => o.value);
            opts.forEach(o => o.disabled = true);

            if (!jamMulai || !dateStr) return;

            const maxDur = getMaxDuration(jamMulai, dateStr);

            opts.forEach(o => {
                const val = parseInt(o.value, 10);
                if (val >= 1 && val <= maxDur) {
                    o.disabled = false;
                }
            });

            if (selectDur.value) {
                const v = parseInt(selectDur.value, 10);
                if (v > maxDur || maxDur === 0) {
                    selectDur.value = '';
                }
            }
        }

        function updateWaktuReschedule() {
            if (!selectJam || !selectDur || !waktuText) return;

            const jam = selectJam.value;
            const dur = parseInt(selectDur.value || '0', 10);

            if (!jam || !dur) {
                waktuText.textContent = '-';
                return;
            }

            const [h, m] = jam.split(':').map(Number);
            const startDate = new Date();
            startDate.setHours(h, m, 0, 0);

            const endDate = new Date(startDate.getTime() + dur * 60 * 60 * 1000);

            const startLabel = pad(startDate.getHours()) + '.' + pad(startDate.getMinutes());
            const endLabel = pad(endDate.getHours()) + '.' + pad(endDate.getMinutes());

            waktuText.textContent = startLabel + ' – ' + endLabel;
        }

        if (selectJam) {
            selectJam.addEventListener('change', () => {
                updateDurasiOptions();
                updateWaktuReschedule();
            });
        }

        if (selectDur) {
            selectDur.addEventListener('change', updateWaktuReschedule);
        }

        slotButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                if (btn.dataset.disabled === '1') return;

                const jam = btn.getAttribute('data-jam');

                if (selectJam) {
                    const opt = Array.from(selectJam.options).find(o => o.value === jam);
                    if (opt && !opt.disabled) {
                        selectJam.value = jam;
                    }
                }

                slotButtons.forEach(b => b.classList.remove('ring-2', 'ring-slate-400'));
                btn.classList.add('ring-2', 'ring-slate-400');

                updateDurasiOptions();
                updateWaktuReschedule();
            });
        });

        // jika tanggal diganti → reload halaman dengan ?id_booking=...&id_reschedule=...&tanggal=...
        if (tanggalInput) {
            tanggalInput.addEventListener('change', () => {
                const t = tanggalInput.value;
                if (!t) return;

                const url = new URL(window.location.href);
                const params = url.searchParams;

                params.set('controller', 'userReschedule');
                params.set('action', 'reschedule');
                params.set('id_booking', '<?= $idBooking ?>');

                <?php if ($rescheduleId): ?>
                    params.set('id_reschedule', '<?= (int)$rescheduleId ?>');
                <?php endif; ?>

                params.set('tanggal', t);

                window.location.search = params.toString();
            });
        }

        // init awal
        document.addEventListener('DOMContentLoaded', () => {
            updateDurasiOptions();
            updateWaktuReschedule();
        });
    </script>

    <!-- JS countdown join_reschedule_until (kalau ada & belum diajukan & belum expired) -->
    <?php if (!empty($joinUntil) && !$isRescheduleSubmitted && !$joinExpired): ?>
        <script>
            (function() {
                const expireAt = new Date("<?= date('c', strtotime($joinUntil)) ?>").getTime();
                const el = document.getElementById('countdown');
                if (!el) return;

                function updateCountdown() {
                    const now = Date.now();
                    const diff = expireAt - now;

                    if (diff <= 0) {
                        el.textContent = "WAKTU HABIS";
                        return;
                    }

                    const minutes = Math.floor(diff / (1000 * 60));
                    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                    el.textContent = minutes + " menit " + seconds + " detik";
                }

                updateCountdown();
                setInterval(updateCountdown, 1000);
            })();
        </script>
    <?php endif; ?>

    <?php
    $footerPath = __DIR__ . '/../layout/footer.php';
    if (file_exists($footerPath)) {
        require $footerPath;
    }
    ?>
</body>

</html>