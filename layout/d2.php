<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Roomify Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>

</head>
<body class="bg-gray-50 text-gray-800">

  <?php require '../layout/navbars.php'; ?>
    <?php require '../layout/phome.php'; ?>
    <h2 class="text-center text-xl font-bold mt-6 mb-4">Daftar ruangan tersedia</h2>
    <form class="flex flex-wrap justify-center gap-4">
      <input type="text" placeholder="Ruangan"
        class="border border-gray-300 rounded-lg px-3 py-2 w-60 focus:ring focus:ring-blue-300">
      <input type="text" placeholder="Kapasitas"
        class="border border-gray-300 rounded-lg px-3 py-2 w-60 focus:ring focus:ring-blue-300">
      <button type="button" class="bg-blue-900 text-white px-4 py-2 rounded">Terapkan filter</button>
      <button type="reset" class="bg-gray-300 px-4 py-2 rounded">Reset</button>
    </form>



<?php
require 'roomCard.php';

$rooms = [
  ["nama" => "Ruang Pertama", "lantai" => 1, "kapasitas" => "5–10 Orang", "jenis" => "Ruang Diskusi", "gambar" => "rapat.png", "linkDetail" => "d7.php"],
  ["nama" => "Ruang Kedua", "lantai" => 2, "kapasitas" => "10–15 Orang", "jenis" => "Ruang Meeting", "gambar" => "rapat.png", "linkDetail" => "d7.php"],
  ["nama" => "Ruang Ketiga", "lantai" => 3, "kapasitas" => "8–12 Orang", "jenis" => "Ruang Kolaborasi", "gambar" => "rapat.png", "linkDetail" => "d7.php"],
  ["nama" => "Ruang Keempat", "lantai" => 1, "kapasitas" => "6–8 Orang", "jenis" => "Ruang Rapat", "gambar" => "rapat.png", "linkDetail" => "d7.php"],
  ["nama" => "Ruang Kelima", "lantai" => 2, "kapasitas" => "15–20 Orang", "jenis" => "Ruang Presentasi", "gambar" => "rapat.png", "linkDetail" => "d7.php"],
  ["nama" => "Ruang Keenam", "lantai" => 1, "kapasitas" => "4–6 Orang", "jenis" => "Ruang Diskusi", "gambar" => "rapat.png", "linkDetail" => "d7.php"],
  ["nama" => "Ruang Ketujuh", "lantai" => 3, "kapasitas" => "8–10 Orang", "jenis" => "Ruang Kolaborasi", "gambar" => "rapat.png", "linkDetail" => "d7.php"],
  ["nama" => "Ruang Kedelapan", "lantai" => 2, "kapasitas" => "10–12 Orang", "jenis" => "Ruang Meeting", "gambar" => "rapat.png", "linkDetail" => "d7.php"],
  ["nama" => "Ruang Kesembilan", "lantai" => 1, "kapasitas" => "5–7 Orang", "jenis" => "Ruang Diskusi", "gambar" => "rapat.png", "linkDetail" => "d7.php"]
];
?>

<div class="flex flex-wrap justify-center gap-10 max-w-screen-xl mx-auto mt-8">
  <?php renderRoomCards($rooms); ?>
</div>
      <?php require '../layout/footerr.php'; ?>
</body>
</html>
