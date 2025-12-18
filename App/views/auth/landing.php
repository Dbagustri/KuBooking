<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kubooking | Peminjaman Ruangan</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="font-sans text-gray-800 bg-gray-50">

  <?php
  // NAVBAR
  $navbarPath = __DIR__ . '/../layout/navbar.php';
  if (file_exists($navbarPath)) {
    require $navbarPath;
  }

  // pastikan variabel aman
  $rooms = $rooms ?? [];

  // load komponen kartu ruangan
  $roomCardPath = __DIR__ . '/../layout/roomCard.php';
  if (file_exists($roomCardPath)) {
    require $roomCardPath;
  }
  ?>
  <?php
  $flashPath = __DIR__ . '/../layout/flash.php';
  if (file_exists($flashPath)) {
    include $flashPath;
  }
  ?>
  <!-- HERO -->
  <section class="relative bg-cover bg-center h-[420px]"
    style="background-image: url('img/default-room.jpg');">
    <div class="absolute inset-0 bg-black/50 flex flex-col justify-center items-center text-white text-center px-4">
      <h2 class="text-3xl sm:text-4xl font-extrabold mb-3">
        Peminjaman Ruangan Perpustakaan
      </h2>
      <p class="text-base sm:text-lg max-w-xl mb-6">
        Ajukan peminjaman ruang diskusi, ruang rapat, atau kelas dengan mudah dan cepat.
      </p>
      <a href="index.php?controller=auth&action=login"
        class="bg-blue-700 hover:bg-blue-600 px-6 py-2 rounded text-white font-medium transition">
        Login
      </a>
    </div>
  </section>

  <!-- DAFTAR RUANGAN UNGGULAN -->
  <section class="w-full max-w-7xl mx-auto px-4 py-10 mb-6">
    <h3 class="text-center text-2xl sm:text-3xl font-semibold mb-8">
      Daftar Ruangan Unggulan
    </h3>

    <?php if (!empty($rooms)): ?>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php
        // di landing, tombol booking boleh kita anggap non-aktif (user belum login)
        // kalau fungsi renderRoomCards punya parameter $buttonDisabled, kita kirim true.
        if (function_exists('renderRoomCards')) {
          // tampilkan hanya 3 (jaga-jaga kalau controller kirim lebih banyak)
          $featured = array_slice($rooms, 0, 3);
          renderRoomCards($featured, true);
        }
        ?>
      </div>
    <?php else: ?>
      <p class="text-center text-gray-500">
        Belum ada ruangan terdaftar.
      </p>
    <?php endif; ?>
  </section>

  <?php
  // FOOTER
  $footerPath = __DIR__ . '/../layout/footer.php';
  if (file_exists($footerPath)) {
    require $footerPath;
  }
  ?>

</body>

</html>