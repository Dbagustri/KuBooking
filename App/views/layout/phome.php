<?php

/** @var array|null $currentUser */
/** @var array|null $booking_aktif */
/** @var bool $canBook */

$currentUser   = $currentUser   ?? null;
$booking_aktif = $booking_aktif ?? null;
$canBook       = $canBook       ?? false;

// Cek disable tombol
if (!isset($buttonDisabled)) {
  $hasActiveBooking = !empty($booking_aktif);
  $buttonDisabled   = (!$canBook || $hasActiveBooking);
}

// Path foto profil default
$fotoProfil = (!empty($currentUser['foto']))
  ? $currentUser['foto']                     // contoh: "img/user1.png"
  : 'img/default-user.png';                  // pastikan file ada di public/img/
?>

<div class="max-w-7xl mx-auto px-4 mt-6">
  <div class="bg-[#1e3a5f] text-white rounded-2xl px-6 py-6
              flex flex-col md:flex-row items-center gap-6 md:justify-between shadow-md">

    <!-- Kiri: Profil -->
    <div class="flex items-center gap-4">

      <!-- Foto Profil Lingkaran -->
      <div class="w-24 h-24 rounded-full overflow-hidden border-2 border-white/40 shadow">
        <img
          src="<?= htmlspecialchars($fotoProfil) ?>"
          alt="Foto profil <?= htmlspecialchars($currentUser['nama'] ?? 'Pengguna') ?>"
          class="w-full h-full object-cover" />
      </div>

      <!-- Info User -->
      <div>
        <p class="text-sm text-white/70">Halo,</p>

        <h1 class="text-xl font-bold">
          <?= htmlspecialchars($currentUser['nama'] ?? 'Pengguna') ?>
        </h1>

        <p class="text-sm text-white/80">
          <?= !empty($currentUser['jurusan'])
            ? "Jurusan " . htmlspecialchars($currentUser['jurusan'])
            : htmlspecialchars($currentUser['unit_jurusan'] ?? 'Profil belum lengkap') ?>
        </p>
      </div>
    </div>

    <!-- Tengah: Booking Aktif -->
    <div class="text-center md:text-left">
      <p class="text-sm font-semibold">Peminjaman Aktif :</p>

      <?php if (!empty($booking_aktif)): ?>
        <p class="text-sm text-white/80">
          <?= htmlspecialchars($booking_aktif['nama_ruangan']) ?><br>
          <?= htmlspecialchars($booking_aktif['jam_mulai']) ?> -
          <?= htmlspecialchars($booking_aktif['jam_selesai']) ?>
        </p>
      <?php else: ?>
        <p class="text-sm text-white/80">Tidak ada peminjaman aktif</p>
      <?php endif; ?>
    </div>

    <!-- Kanan: Tombol -->
    <div class="flex flex-col gap-3 w-full md:w-auto">
      <button id="btnPilih"
        class="px-5 py-2.5 rounded-lg font-semibold bg-white text-[#1e3a5f]
               <?= $buttonDisabled ? 'opacity-40 cursor-not-allowed' : 'hover:bg-gray-100' ?>"
        <?= $buttonDisabled ? 'disabled' : '' ?>>
        Pilih Ruangan
      </button>

      <button id="btnGabung"
        class="px-5 py-2.5 rounded-lg font-semibold bg-white text-[#1e3a5f]
               <?= $buttonDisabled ? 'opacity-40 cursor-not-allowed' : 'hover:bg-gray-100' ?>"
        <?= $buttonDisabled ? 'disabled' : '' ?>>
        Gabung Kelompok
      </button>
    </div>

  </div>
</div>