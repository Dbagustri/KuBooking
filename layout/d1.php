<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Roomify | Peminjaman Ruangan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Warna biru kalem gelap */
        :root {
            --biru-lembut: #1F2A44;
            --biru-hover: #24304D;
        }
    </style>
</head>
<body class="font-sans text-gray-800 bg-gray-50">

<?php require '../layout/navbarb.php'; ?>

<!-- HERO -->
<section class="relative bg-cover bg-center h-[500px]" style="background-image: url('rapat.png');">
  <div class="absolute inset-0 bg-black bg-opacity-50 flex flex-col justify-center items-center text-white">
    <h2 class="text-4xl font-extrabold text-center">Peminjaman Ruangan Perpustakaan</h2>
    <p class="text-center mt-3 mb-6 text-lg max-w-xl">
      Ajukan peminjaman ruang diskusi, ruang rapat, atau kelas dengan mudah dan cepat.
    </p>
    <a href="d3.php" 
       class="bg-blue-800 hover:bg-blue-700 px-6 py-2 rounded text-white font-medium transition">
       Login
    </a>
  </div>
</section>


<h3 class="text-center text-3xl font-semibold mb-10 mt-10">Daftar Ruangan Unggulan</h3>
     
<?php
require 'roomCard.php'; // Pastikan file ada di folder yang sama

$rooms = [
  ["nama" => "Ruang Pertama", "lantai" => 1, "kapasitas" => "5–10 Orang", "jenis" => "Ruang Diskusi", "gambar" => "rapat.png", "linkDetail" => "d7.php"],
  ["nama" => "Ruang Kedua", "lantai" => 2, "kapasitas" => "10–15 Orang", "jenis" => "Ruang Meeting", "gambar" => "rapat.png", "linkDetail" => "d7.php"],
  ["nama" => "Ruang Ketiga", "lantai" => 3, "kapasitas" => "8–12 Orang", "jenis" => "Ruang Kolaborasi", "gambar" => "rapat.png", "linkDetail" => "d7.php"]
];
?>

<div class="flex flex-wrap justify-center gap-10 max-w-screen-xl mx-auto mt-8">
  <?php renderRoomCards($rooms); ?>
</div>

      <?php require '../layout/footerr.php'; ?>

</body>
</html>
