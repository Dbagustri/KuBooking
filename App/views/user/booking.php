<?php

use App\Core\Auth;

/** @var array $room */
/** @var array|null $booking */
/** @var array $members */
/** @var bool $isExpired */
/** @var array $disabledSlots (opsional, isi jam bentrok 'HH:MM') */


$today        = date('Y-m-d');
$selectedDate = $_GET['tanggal'] ?? $today;

$room          = $room          ?? [];
$booking       = $booking       ?? null;
$members       = $members       ?? [];
$isExpired     = $isExpired     ?? false;
$disabledSlots = $disabledSlots ?? [];

$userSession = Auth::user();
$memberCount = is_array($members) ? count($members) : 0;
$capMin      = $booking['kapasitas_min'] ?? ($room['kapasitas_min'] ?? 0);
$capMax      = $booking['kapasitas_max'] ?? ($room['kapasitas_max'] ?? 0);

$isPJ = $booking && $userSession && !empty($booking['id_pj'])
  && !empty($userSession['id_account'])
  && (int)$booking['id_pj'] === (int)$userSession['id_account'];
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
$startOptions = $timeSlots;
$isTodaySelected = ($selectedDate === $today);
$isPastDay       = ($selectedDate < $today);
$nowMinutes      = (int)date('H') * 60 + (int)date('i');
$selectedTs  = strtotime($selectedDate);
$dayOfWeek   = (int)date('N', $selectedTs);
$isFriday    = ($dayOfWeek === 5);

if ($isFriday) {
  $openMinutes   = 8 * 60;
  $closeMinutes  = 17 * 60;      // 17.00
  $breakStart    = 11 * 60;      // 11.00
  $breakEnd      = 13 * 60;      // 13.00
} else {
  $openMinutes   = 8 * 60;
  $closeMinutes  = 16 * 60;      // 16.00
  $breakStart    = 12 * 60;      // 12.00
  $breakEnd      = 13 * 60;      // 13.00
}
$formDisabled = $booking !== null;
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <title>Kubooking - Booking Ruangan</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">
  <?php require __DIR__ . '/../layout/navbar.php'; ?>
  <?php
  $flashPath = __DIR__ . '/../layout/flash.php';
  if (file_exists($flashPath)) {
    include $flashPath;
  }
  ?>
  <main class="max-w-6xl mx-auto px-4 py-6 space-y-4">
    <a href="index.php?controller=userBooking&action=home"
      class="flex items-center text-sm text-slate-600 hover:text-slate-900">
      <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
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

        <!-- Room Card -->
        <article class="bg-white rounded-2xl shadow-sm overflow-hidden">
          <?php
          $imgPath = !empty($room['foto_ruangan'])
            ? htmlspecialchars($room['foto_ruangan'])
            : 'img/rapat.png';
          ?>
          <div class="h-48 bg-cover bg-center"
            style="background-image: url('<?= $imgPath ?>');"></div>

          <div class="p-4 sm:p-6 space-y-3">
            <div>
              <h1 class="text-xl font-semibold text-slate-900">
                <?= htmlspecialchars($room['nama_ruangan'] ?? 'Ruangan') ?>
              </h1>
              <p class="text-sm text-slate-500 mt-1">
                Jenis: <?= htmlspecialchars($room['kategori'] ?? '-') ?>
              </p>
            </div>

            <dl class="grid grid-cols-2 gap-4 text-sm">
              <div>
                <dt class="text-slate-500">Lantai / Lokasi</dt>
                <dd class="font-medium text-slate-900">
                  <?= htmlspecialchars($room['lokasi'] ?? '-') ?>
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

        <!-- GROUP SECTION -->
        <section class="bg-white rounded-2xl shadow-sm p-4 sm:p-5 space-y-4" id="group-section">
          <?php if (!$booking): ?>
            <!-- STATE AWAL: belum ada kelompok -->
            <div id="group-empty" class="space-y-3">
              <h2 class="text-base font-semibold text-slate-900">
                Kelompok
              </h2>
              <p class="text-sm text-slate-500">
                Belum ada kelompok untuk ruangan ini.
                Isi form di kanan lalu klik
                <span class="font-semibold">"Buat Kelompok &amp; Booking"</span>
                untuk membuat kelompok baru.
              </p>
            </div>
          <?php else: ?>
            <!-- STATE SETELAH ADA KELOMPOK -->
            <div id="group-detail" class="space-y-4">
              <header class="flex items-center justify-between">
                <div>
                  <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">
                    Kode Kelompok
                  </h2>
                  <div class="flex items-center gap-2">
                    <p class="text-lg font-semibold text-slate-900" id="kodeKelompokText">
                      <?= htmlspecialchars($booking['kode_kelompok']) ?>
                    </p>
                    <button type="button"
                      onclick="copyKodeKelompok()"
                      class="text-xs text-slate-500 hover:text-slate-800 border border-slate-200 rounded-full px-2 py-1">
                      Salin
                    </button>
                  </div>
                </div>
                <div class="flex flex-col items-end text-right text-sm">
                  <span class="text-slate-500">Anggota</span>
                  <span class="font-semibold text-slate-900"><?= $memberCount ?> orang</span>
                </div>
              </header>

              <?php if (!$isExpired && (int)$booking['submitted'] === 0 && !empty($booking['group_expire_at'])): ?>
                <div class="text-xs text-slate-500">
                  Waktu anggota masuk berakhir dalam:
                  <span class="font-semibold text-slate-900" id="countdown"></span>
                </div>
              <?php endif; ?>

              <?php if ($isExpired && (int)$booking['submitted'] === 0): ?>
                <p class="text-xs text-red-600 font-medium">
                  Waktu 5 menit pembentukan kelompok sudah habis.
                  Silakan buat kelompok baru jika ingin melanjutkan.
                </p>
              <?php endif; ?>

              <div class="border-t border-slate-100 pt-3 space-y-2">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                  Anggota Kelompok
                </p>

                <div class="space-y-2">
                  <?php foreach ($members as $m): ?>
                    <div class="flex items-center justify-between bg-slate-50 rounded-xl px-3 py-2">
                      <div class="flex items-center gap-2 text-sm">
                        <div class="h-7 w-7 rounded-full bg-slate-200"></div>
                        <div class="flex flex-col">
                          <span><?= htmlspecialchars($m['nama']) ?></span>
                          <?php if (!empty($booking['id_pj']) && (int)$m['id_user'] === (int)$booking['id_pj']): ?>
                            <span class="text-[11px] uppercase tracking-wide text-emerald-600 font-semibold">
                              PJ
                            </span>
                          <?php endif; ?>
                        </div>
                      </div>

                      <?php if ($isPJ && (int)$m['id_user'] !== (int)$booking['id_pj'] && !$isExpired && (int)$booking['submitted'] === 0): ?>
                        <form method="POST"
                          action="index.php?controller=userBooking&action=kickMember">
                          <input type="hidden" name="id_booking" value="<?= (int)$booking['id_bookings'] ?>">
                          <input type="hidden" name="id_user" value="<?= (int)$m['id_user'] ?>">
                          <button class="text-xs text-slate-400 hover:text-red-500" title="Keluarkan anggota">
                            ✕
                          </button>
                        </form>
                      <?php endif; ?>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>

              <?php if ($isPJ): ?>
                <div class="pt-3 space-y-2">
                  <!-- Ajukan Booking -->
                  <form action="index.php?controller=userBooking&action=submitBooking"
                    method="POST">
                    <input type="hidden" name="id_booking" value="<?= (int)$booking['id_bookings'] ?>">
                    <?php
                    $disableSubmit = ($memberCount < $capMin) || $isExpired || ((int)$booking['submitted'] === 1);
                    ?>
                    <button
                      class="w-full rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm 
                                               hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-1
                                               <?= $disableSubmit ? 'opacity-40 cursor-not-allowed' : '' ?>"
                      <?= $disableSubmit ? 'disabled' : '' ?>>
                      Ajukan Booking
                    </button>
                  </form>

                  <?php if ((int)$booking['submitted'] === 0): ?>
                    <form action="index.php?controller=userBooking&action=deleteGroup"
                      method="POST"
                      onsubmit="return confirm('Yakin ingin menghapus kelompok ini?');">
                      <input type="hidden" name="id_booking" value="<?= (int)$booking['id_bookings'] ?>">
                      <button
                        class="w-full rounded-xl bg-red-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm 
                                                   hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-1">
                        Hapus Kelompok
                      </button>
                    </form>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </section>
      </div>

      <!-- RIGHT COLUMN -->
      <section class="lg:w-1/2 bg-white rounded-2xl shadow-sm p-4 sm:p-6 space-y-5" id="form-booking">
        <h2 class="text-lg font-semibold text-slate-900 mb-1">Booking Ruangan</h2>

        <?php if ($formDisabled): ?>
          <p class="text-xs text-slate-500 mb-2">
            Kelompok untuk jadwal ini sudah dibuat. Form booking dinonaktifkan.
            Untuk mengubah jadwal, hapus kelompok ini dulu atau gunakan fitur reschedule
            setelah booking disetujui.
          </p>
        <?php endif; ?>

        <!-- FORM BOOKING -->
        <form action="index.php?controller=userBooking&action=createGroup"
          method="POST"
          class="space-y-5">
          <input type="hidden" name="id_ruangan" value="<?= (int)($room['id_ruangan'] ?? 0) ?>">

          <fieldset <?= $formDisabled ? 'disabled' : '' ?> class="space-y-5">
            <div class="grid gap-4 sm:grid-cols-2">
              <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">
                  Pilih tanggal
                </label>
                <input
                  type="date"
                  name="tanggal"
                  id="tanggal"
                  value="<?= htmlspecialchars($selectedDate) ?>"
                  min="<?= $today ?>"
                  class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm
                                       focus:border-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-300"
                  required />
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                  Pilih jam mulai
                </label>
                <select
                  name="jam_mulai"
                  id="jam_mulai"
                  class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm
                                       focus:border-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-300"
                  required>
                  <option value="">Pilih jam</option>
                  <?php foreach ($startOptions as $opt):
                    $optMinutes = (int)substr($opt, 0, 2) * 60 + (int)substr($opt, 3, 2);
                    $optPastToday = $isTodaySelected && ($optMinutes <= $nowMinutes);
                    $optPastDay = $isPastDay;
                    $outsideOp = ($optMinutes < $openMinutes || $optMinutes >= $closeMinutes);
                    $insideBreak = ($optMinutes >= $breakStart && $optMinutes < $breakEnd);
                    $isBooked = in_array($opt, $disabledSlots, true);

                    $disabled = $optPastDay || $optPastToday || $isBooked || $outsideOp || $insideBreak;
                  ?>
                    <option
                      value="<?= $opt ?>"
                      <?= $disabled ? 'disabled' : '' ?>>
                      <?= str_replace(':', '.', $opt) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                  Pilih durasi
                </label>
                <select
                  name="durasi"
                  id="durasi"
                  class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm
                                       focus:border-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-300"
                  required>
                  <option value="">Pilih durasi</option>
                  <option value="1">1 jam</option>
                  <option value="2">2 jam</option>
                  <option value="3">3 jam</option>
                </select>
              </div>
            </div>
            <div class="space-y-2">
              <div class="flex items-center justify_between">
                <label class="block text-sm font-medium text-slate-700">
                  Jam tersedia
                </label>
                <span class="text-xs text-slate-400">
                  Hijau: tersedia, Merah: penuh / istirahat / di luar jam buka / sudah lewat / hari lewat
                </span>
              </div>

              <div class="grid grid-cols-3 sm:grid-cols-4 gap-2 text-xs sm:text-sm">
                <?php foreach ($timeSlots as $slot):
                  $label       = str_replace(':', '.', substr($slot, 0, 5));
                  $slotMinutes = (int)substr($slot, 0, 2) * 60 + (int)substr($slot, 3, 2);
                  $isDisabledByBooking = in_array($slot, $disabledSlots, true);
                  $isPastToday         = $isTodaySelected && ($slotMinutes <= $nowMinutes);
                  $isPastDaySlot       = $isPastDay;
                  $outsideOp           = ($slotMinutes < $openMinutes || $slotMinutes >= $closeMinutes);
                  $insideBreak         = ($slotMinutes >= $breakStart && $slotMinutes < $breakEnd);

                  $isDisabled = $isDisabledByBooking || $isPastDaySlot || $isPastToday || $outsideOp || $insideBreak;
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

            <div class="space-y-1">
              <p class="text-sm font-medium text-slate-700">
                Waktu Booking
              </p>
              <div
                class="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50 px-3 py-2 text-sm">
                <span class="text-slate-500">Terpilih</span>
                <span class="font-semibold text-slate-900" id="waktu-booking-text">-</span>
              </div>
            </div>
          </fieldset>

          <div class="pt-2">
            <button
              type="submit"
              <?= $formDisabled ? 'disabled' : '' ?>
              class="w-full rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm
                               hover:bg-slate-800 focus:outline_none focus:ring-2 focus:ring-slate-400 focus:ring-offset-1
                               <?= $formDisabled ? 'opacity-40 cursor-not-allowed' : '' ?>">
              Buat Kelompok &amp; Booking
            </button>
          </div>
        </form>
      </section>
    </section>
  </main>
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

    const selectJam = document.getElementById('jam_mulai');
    const selectDur = document.getElementById('durasi');
    const waktuText = document.getElementById('waktu-booking-text');
    const tanggalInput = document.getElementById('tanggal');
    const slotButtons = document.querySelectorAll('.slot-btn');

    function pad(n) {
      return n < 10 ? '0' + n : '' + n;
    }

    function getDayInfo(dateStr) {
      const d = dateStr ? new Date(dateStr + 'T00:00:00') : new Date();
      const day = d.getDay();

      let info = {
        openMinutes: 8 * 60,
        closeMinutes: 16 * 60,
        breakStartMinutes: 12 * 60,
        breakEndMinutes: 13 * 60
      };

      // Jumat
      if (day === 5) {
        info = {
          openMinutes: 8 * 60,
          closeMinutes: 17 * 60,
          breakStartMinutes: 11 * 60,
          breakEndMinutes: 13 * 60
        };
      }
      return info;
    }

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

      for (let dur = 1; dur <= 3; dur++) {
        const endMinutes = startMinutes + dur * 60;

        if (endMinutes > close) break;

        const kenaIstirahat = (startMinutes < bEnd && endMinutes > bStart);
        if (kenaIstirahat) continue;

        maxDur = dur;
      }

      return maxDur;
    }

    function updateDurasiOptions() {
      if (!selectDur) return;

      const dateStr = tanggalInput ? tanggalInput.value : '';
      const jamMulai = selectJam ? selectJam.value : '';

      const opts = Array.from(selectDur.options).filter(o => o.value);
      opts.forEach(o => {
        o.disabled = true;
      });

      if (!jamMulai || !dateStr) return;
      const todayStr = new Date().toISOString().slice(0, 10);
      if (dateStr < todayStr) {
        selectDur.value = '';
        return;
      }

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

    function updateWaktuBooking() {
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
        updateWaktuBooking();
      });
    }

    if (selectDur) {
      selectDur.addEventListener('change', updateWaktuBooking);
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
        updateWaktuBooking();
      });
    });

    if (tanggalInput) {
      tanggalInput.addEventListener('change', () => {
        const t = tanggalInput.value;
        if (!t) return;

        const url = new URL(window.location.href);
        const params = url.searchParams;

        const roomIdInput = document.querySelector('input[name="id_ruangan"]');
        if (roomIdInput) {
          params.set('id_ruangan', roomIdInput.value);
        }

        params.set('tanggal', t);

        const idBooking = params.get('id_booking');
        if (idBooking) {
          params.set('id_booking', idBooking);
        }

        window.location.search = params.toString();
      });
    }

    document.addEventListener('DOMContentLoaded', () => {
      updateDurasiOptions();
      updateWaktuBooking();
    });
  </script>

  <?php if ($booking && !$isExpired && (int)$booking['submitted'] === 0 && !empty($booking['group_expire_at'])): ?>
    <script>
      (function() {
        const expireAt = <?= (int)strtotime($booking['group_expire_at']) * 1000 ?>;
        const el = document.getElementById('countdown');
        if (!el) return;

        function updateCountdown() {
          const now = Date.now();
          const diff = expireAt - now;

          if (diff <= 0) {
            el.textContent = "WAKTU HABIS";
            clearInterval(timer);
            return;
          }

          const minutes = Math.floor(diff / (1000 * 60));
          const seconds = Math.floor((diff % (1000 * 60)) / 1000);

          el.textContent = minutes + " menit " + seconds + " detik";
        }

        updateCountdown();
        const timer = setInterval(updateCountdown, 1000);
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