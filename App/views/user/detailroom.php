<?php

/** @var array       $room */
/** @var array       $slots */
/** @var array       $slotStatus */
/** @var array       $fasilitas */
/** @var bool        $buttonDisabled */
/** @var string|null $jamOperasionalText */

$buttonDisabled     = $buttonDisabled ?? false;
$jamOperasionalText = $jamOperasionalText ?? '';

?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Detail Ruangan - <?= htmlspecialchars($room['nama_ruangan']); ?></title>
  <!-- Kalau Tailwind sudah di-include di layout utama, script ini boleh dihapus -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="bg-[#f2f4f7]">
  <?php
  $navbarPath = __DIR__ . '/../layout/navbar.php';
  if (file_exists($navbarPath)) {
    include $navbarPath;
  }
  ?>

  <div class="min-h-screen px-8 py-6">

    <!-- Back -->
    <div class="mb-4">
      <a href="index.php?controller=user&action=home" class="inline-flex items-center text-gray-600 hover:text-gray-800">
        <i class="fa-solid fa-arrow-left mr-2"></i>
        <span>Kembali</span>
      </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

      <!-- KIRI: gambar + nama + deskripsi singkat -->
      <div class="lg:col-span-2 space-y-4">

        <!-- Card gambar -->
        <div class="bg-white rounded-3xl shadow-md overflow-hidden">
          <?php
          $fotoRuangan = !empty($room['foto_ruangan'])
            ? $room['foto_ruangan']
            : 'img/default-room.jpg';
          ?>

          <img src="<?= htmlspecialchars($fotoRuangan) ?>"
            alt="Ruangan <?= htmlspecialchars($room['nama_ruangan']); ?>"
            class="w-full h-72 object-cover">

          <div class="p-6">
            <h1 class="text-2xl font-semibold text-gray-900 mb-1">
              <?= htmlspecialchars($room['nama_ruangan']); ?>
            </h1>
            <p class="text-gray-600 mb-3">
              <?= htmlspecialchars($room['deskripsi'] ?? 'Ruang diskusi di perpustakaan utama'); ?>
            </p>

            <!-- dummy rating -->
            <div class="flex items-center space-x-1 text-yellow-400 text-xl">
              <span>★</span><span>★</span><span>★</span><span>★</span>
              <span class="text-gray-300">★</span>
            </div>
          </div>
        </div>

        <!-- Ringkasan Ruangan -->
        <div class="bg-white rounded-3xl shadow-md p-6 space-y-4">
          <h2 class="text-lg font-semibold text-gray-900 mb-2">Ringkasan Ruangan</h2>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
              <p class="text-gray-500">Lantai</p>
              <p class="font-semibold text-gray-800">
                <?= htmlspecialchars($room['lokasi'] ?? '-'); ?>
              </p>
            </div>
            <div>
              <p class="text-gray-500">Kapasitas</p>
              <p class="font-semibold text-gray-800">
                <?= (int)$room['kapasitas_min']; ?> – <?= (int)$room['kapasitas_max']; ?> Orang
              </p>
            </div>
            <div>
              <p class="text-gray-500">Lokasi</p>
              <p class="font-semibold text-gray-800">
                <?= htmlspecialchars($room['lokasi'] ?? '-'); ?>
              </p>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm pt-4 border-t mt-4">
            <div>
              <h3 class="font-semibold mb-1">Jam Operasional</h3>
              <p class="text-gray-700 whitespace-pre-line">
                <?php if ($jamOperasionalText !== ''): ?>
                  <?= htmlspecialchars($jamOperasionalText); ?>
                <?php else: ?>
                  Belum ada jadwal operasional yang terdaftar.
                <?php endif; ?>
              </p>
            </div>
            <div>
              <h3 class="font-semibold mb-1">Aturan Singkat</h3>
              <p class="text-gray-700 whitespace-pre-line">
                <?= htmlspecialchars($room['aturan_singkat'] ?? "- Dilarang merokok di dalam ruangan.\n- Harap menjaga kebersihan & ketenangan.\n- Maks. peminjaman 2 jam per sesi."); ?>
              </p>
            </div>
          </div>
        </div>

      </div>

      <!-- KANAN: detail ruangan + ketersediaan + fasilitas + CTA Pesan -->
      <div class="space-y-4">

        <!-- Detail Ruangan -->
        <div class="bg-white rounded-3xl shadow-md p-6 space-y-4">
          <h2 class="text-lg font-semibold text-gray-900 mb-2">Detail Ruangan</h2>

          <div class="grid grid-cols-2 gap-3 text-sm">
            <div class="bg-[#0F315B] text-white rounded-2xl px-3 py-3">
              <p class="text-xs opacity-80 mb-1">Jenis</p>
              <p class="font-semibold">
                <?= htmlspecialchars($room['kategori']); ?>
              </p>
            </div>
            <div class="bg-[#0F315B] text-white rounded-2xl px-3 py-3">
              <p class="text-xs opacity-80 mb-1">Status</p>
              <p class="font-semibold">
                <?= htmlspecialchars($room['status_operasional'] ?? 'aktif'); ?>
              </p>
            </div>
            <div class="bg-[#0F315B] text-white rounded-2xl px-3 py-3">
              <p class="text-xs opacity-80 mb-1">Kapasitas</p>
              <p class="font-semibold">
                <?= (int)$room['kapasitas_min']; ?>–<?= (int)$room['kapasitas_max']; ?> orang
              </p>
            </div>
            <div class="bg-[#0F315B] text-white rounded-2xl px-3 py-3">
              <p class="text-xs opacity-80 mb-1">Lantai</p>
              <p class="font-semibold">
                <?= htmlspecialchars($room['lokasi'] ?? '-'); ?>
              </p>
            </div>
          </div>

          <!-- Ketersediaan Hari ini -->
          <div class="pt-3 border-t mt-3">
            <p class="font-semibold text-sm mb-1">Ketersediaan Hari ini</p>
            <p class="text-xs text-gray-600 mb-3">
              Hijau: Tersedia &nbsp;&nbsp; Merah: Terisi
            </p>

            <div class="flex flex-wrap gap-2">
              <?php if (!empty($slots)): ?>
                <?php foreach ($slots as $slot):
                  $colorClass = (isset($slotStatus[$slot]) && $slotStatus[$slot] === 'red')
                    ? 'bg-red-100 text-red-600 border-red-300'
                    : 'bg-green-100 text-green-700 border-green-300';
                ?>
                  <span class="px-3 py-1 rounded-full text-xs border <?= $colorClass; ?>">
                    <?= htmlspecialchars($slot); ?>
                  </span>
                <?php endforeach; ?>
              <?php else: ?>
                <span class="text-xs text-gray-500">Belum ada data ketersediaan.</span>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- Fasilitas Ruangan -->
        <div class="bg-white rounded-3xl shadow-md p-6 space-y-3">
          <h2 class="text-lg font-semibold text-gray-900">Fasilitas Ruangan</h2>

          <?php if (!empty($fasilitas)): ?>
            <ul class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-1 text-sm text-gray-700">
              <?php foreach ($fasilitas as $fas): ?>
                <li class="flex items-center space-x-2">
                  <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span>
                  <span>
                    <?= htmlspecialchars(is_array($fas) && isset($fas['nama_fasilitas']) ? $fas['nama_fasilitas'] : $fas); ?>
                  </span>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <p class="text-sm text-gray-600">Belum ada data fasilitas untuk ruangan ini.</p>
          <?php endif; ?>
        </div>

        <!-- CTA Pesan -->
        <div class="bg-white rounded-3xl shadow-md p-6 flex items-center justify-between">
          <div>
            <h2 class="text-base font-semibold text-gray-900">Siap memesan ruangan ini?</h2>
            <p class="text-xs text-gray-600">
              Pilih jadwal dan buat kelompok pada langkah berikutnya.
            </p>
          </div>

          <?php if ($buttonDisabled): ?>
            <span
              class="ml-4 px-5 py-2.5 rounded-xl bg-gray-300 text-gray-500 text-sm font-medium
                     cursor-not-allowed select-none">
              Pesan
            </span>
          <?php else: ?>
            <a href="index.php?controller=userBooking&action=booking&id=<?= (int)$room['id_ruangan']; ?>"
              class="ml-4 px-5 py-2.5 rounded-xl bg-[#0F315B] text-white text-sm font-medium hover:bg-[#0b2441] transition">
              Pesan
            </a>
          <?php endif; ?>
        </div>

      </div>
    </div>
  </div>

  <?php
  $footerPath = __DIR__ . '/../layout/footer.php';
  if (file_exists($footerPath)) {
    require $footerPath;
  }
  ?>
</body>

</html>