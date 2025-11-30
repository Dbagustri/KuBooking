  <?php require '../layout/navbars.php'; ?>
    <?php require '../layout/phome.php'; ?>
  <?php require '../layout/footerr.php'; ?>

    <!-- Judul -->
    <h2 class="text-center text-xl font-bold mt-6 mb-4">Daftar ruangan tersedia</h2>

    <!-- Filter Form -->
    <form class="flex flex-wrap justify-center gap-4">
      <input type="text" placeholder="Ruangan"
        class="border border-gray-300 rounded-lg px-3 py-2 w-60 focus:ring focus:ring-blue-300">
      <input type="text" placeholder="Kapasitas"
        class="border border-gray-300 rounded-lg px-3 py-2 w-60 focus:ring focus:ring-blue-300">
      <button type="button" class="bg-blue-900 text-white px-4 py-2 rounded">Terapkan filter</button>
      <button type="reset" class="bg-gray-300 px-4 py-2 rounded">Reset</button>
    </form>

    <!-- Daftar Ruangan -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">

    <?php
// index.php
// Pastikan server PHP berjalan; simpan rapat.png di folder yang sama atau sesuaikan path
include_once 'roomCard.php';

// Contoh data (bisa diambil dari DB atau file lain)
$rooms = [
    [
        'nama' => 'Ruang Pertama',
        'lantai' => '1',
        'kapasitas' => '5–10 Orang',
        'jenis' => 'Ruang Diskusi',
        'gambar' => 'rapat.png',
        'linkDetail' => '#'
    ],
    [
        'nama' => 'Ruang Kedua',
        'lantai' => '2',
        'kapasitas' => '10–15 Orang',
        'jenis' => 'Ruang Meeting',
        'gambar' => 'rapat.png',
        'linkDetail' => '#'
    ],
    [
        'nama' => 'Ruang Tiga',
        'lantai' => '1',
        'kapasitas' => '8–12 Orang',
        'jenis' => 'Ruang Kolaborasi',
        'gambar' => 'rapat.png',
        'linkDetail' => '#'
    ],
    [
        'nama' => 'Ruang Empat',
        'lantai' => '3',
        'kapasitas' => '15–20 Orang',
        'jenis' => 'Ruang Presentasi',
        'gambar' => 'rapat.png',
        'linkDetail' => '#'
    ],
];

?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Daftar Ruangan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
      /* Atur supaya container maksimal 3 kartu per baris di desktop */
      /* Kita gunakan flex + gap; lebar kartu tetap sehingga otomatis 3 per baris */
    </style>
  </head>
  <body class="bg-white min-h-screen p-10">
    <h1 class="text-2xl font-bold mb-6">Daftar Ruangan</h1>

    <div id="roomsContainer" class="flex flex-wrap justify-center gap-10 max-w-screen-xl mx-auto">
      <?php
        // Cetak HTML kartu yang dihasilkan fungsi renderRoomCards
        echo renderRoomCards($rooms);
      ?>
    </div>
  </body>
</html>
