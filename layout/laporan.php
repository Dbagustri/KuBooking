<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan | KoBooking</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#f2f7fc] text-gray-800 flex">

  <!-- SIDEBAR -->
  <?php include 'sidebar.php'; ?>

  <!-- KONTEN -->
  <div class="flex-1 flex flex-col">

    <!-- NAVBAR -->
    <div class="m-4">
      <?php include 'nav-admin.php'; ?>
    </div>

    <!-- ISI HALAMAN -->
    <div class="px-8 pb-8 space-y-10">
      <h1 class="text-2xl font-bold text-[#1e3a5f]">Laporan</h1>

      <!-- ====== BLOK LAPORAN 1 ====== -->
      <div class="bg-gray-100 rounded-xl shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-lg font-semibold text-[#1e3a5f]">
            Ruangan Dipinjam Terbanyak Bulan Ini
          </h2>
          <button class="flex items-center gap-2 bg-[#1e3a5f] text-white px-4 py-2 rounded-lg hover:bg-[#274269]">
            <span>⬇️</span> Export
          </button>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full text-center border-collapse rounded-lg overflow-hidden">
            <thead class="bg-[#2b698b] text-white">
              <tr>
                <th class="px-4 py-2">No</th>
                <th class="px-4 py-2">Tanggal</th>
                <th class="px-4 py-2">Ruangan Pertama</th>
                <th class="px-4 py-2">Ruangan Kedua</th>
                <th class="px-4 py-2">Ruangan Ketiga</th>
                <th class="px-4 py-2">Total</th>
              </tr>
            </thead>
            <tbody>
              <?php for ($i=0; $i<5; $i++): ?>
              <tr class="<?= $i % 2 == 0 ? 'bg-white' : 'bg-gray-50'; ?>">
                <td class="px-4 py-2">1</td>
                <td class="px-4 py-2">13 Nov, 13:00–15:00</td>
                <td class="px-4 py-2">35</td>
                <td class="px-4 py-2">35</td>
                <td class="px-4 py-2">35</td>
                <td class="px-4 py-2">35</td>
              </tr>
              <?php endfor; ?>
            </tbody>
          </table>

          <!-- PAGINATION -->
          <div class="flex justify-center mt-4 space-x-2">
            <button class="px-2 py-1 rounded bg-gray-200">&lt;</button>
            <button class="px-3 py-1 rounded bg-gray-200">1</button>
            <button class="px-3 py-1 rounded bg-[#1e3a5f] text-white">2</button>
            <button class="px-3 py-1 rounded bg-gray-200">3</button>
            <button class="px-2 py-1 rounded bg-gray-200">&gt;</button>
          </div>
        </div>
      </div>

      <!-- ====== BLOK LAPORAN 2 ====== -->
      <div class="bg-gray-100 rounded-xl shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-lg font-semibold text-[#1e3a5f]">
            Jurusan Dipinjam Terbanyak Bulan Ini
          </h2>
          <button class="flex items-center gap-2 bg-[#1e3a5f] text-white px-4 py-2 rounded-lg hover:bg-[#274269]">
            <span>⬇️</span> Export
          </button>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full text-center border-collapse rounded-lg overflow-hidden">
            <thead class="bg-[#2b698b] text-white">
              <tr>
                <th class="px-4 py-2">No</th>
                <th class="px-4 py-2">Tanggal</th>
                <th class="px-4 py-2">TIK</th>
                <th class="px-4 py-2">TS</th>
                <th class="px-4 py-2">TM</th>
                <th class="px-4 py-2">TGP</th>
                <th class="px-4 py-2">AK</th>
                <th class="px-4 py-2">AN</th>
                <th class="px-4 py-2">TE</th>
                <th class="px-4 py-2">Total</th>
              </tr>
            </thead>
            <tbody>
              <?php for ($i=0; $i<5; $i++): ?>
              <tr class="<?= $i % 2 == 0 ? 'bg-white' : 'bg-gray-50'; ?>">
                <td class="px-4 py-2">1</td>
                <td class="px-4 py-2">13 Nov, 13:00–15:00</td>
                <td class="px-4 py-2">35</td>
                <td class="px-4 py-2">35</td>
                <td class="px-4 py-2">35</td>
                <td class="px-4 py-2">35</td>
                <td class="px-4 py-2">30</td>
                <td class="px-4 py-2">30</td>
                <td class="px-4 py-2">30</td>
                <td class="px-4 py-2">100</td>
              </tr>
              <?php endfor; ?>
            </tbody>
          </table>

          <!-- PAGINATION -->
          <div class="flex justify-center mt-4 space-x-2">
            <button class="px-2 py-1 rounded bg-gray-200">&lt;</button>
            <button class="px-3 py-1 rounded bg-gray-200">1</button>
            <button class="px-3 py-1 rounded bg-[#1e3a5f] text-white">2</button>
            <button class="px-3 py-1 rounded bg-gray-200">3</button>
            <button class="px-2 py-1 rounded bg-gray-200">&gt;</button>
          </div>
        </div>
      </div>

      <!-- ====== BLOK LAPORAN 3 ====== -->
      <div class="bg-gray-100 rounded-xl shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-lg font-semibold text-[#1e3a5f]">
            Rating Ruangan
          </h2>
          <button class="flex items-center gap-2 bg-[#1e3a5f] text-white px-4 py-2 rounded-lg hover:bg-[#274269]">
            <span>⬇️</span> Export
          </button>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full text-center border-collapse rounded-lg overflow-hidden">
            <thead class="bg-[#2b698b] text-white">
              <tr>
                <th class="px-4 py-2">No</th>
                <th class="px-4 py-2">Tanggal</th>
                <th class="px-4 py-2">Ruangan Pertama</th>
                <th class="px-4 py-2">Ruangan Kedua</th>
                <th class="px-4 py-2">Ruangan Ketiga</th>
                <th class="px-4 py-2">Total</th>
              </tr>
            </thead>
            <tbody>
              <?php for ($i=0; $i<5; $i++): ?>
              <tr class="<?= $i % 2 == 0 ? 'bg-white' : 'bg-gray-50'; ?>">
                <td class="px-4 py-2">1</td>
                <td class="px-4 py-2">13 Nov, 13:00–15:00</td>
                <td class="px-4 py-2">4.4</td>
                <td class="px-4 py-2">4.6</td>
                <td class="px-4 py-2">4.87</td>
                <td class="px-4 py-2">4.86</td>
              </tr>
              <?php endfor; ?>
            </tbody>
          </table>

          <!-- PAGINATION -->
          <div class="flex justify-center mt-4 space-x-2">
            <button class="px-2 py-1 rounded bg-gray-200">&lt;</button>
            <button class="px-3 py-1 rounded bg-gray-200">1</button>
            <button class="px-3 py-1 rounded bg-[#1e3a5f] text-white">2</button>
            <button class="px-3 py-1 rounded bg-gray-200">3</button>
            <button class="px-2 py-1 rounded bg-gray-200">&gt;</button>
          </div>
        </div>
      </div>

    </div>
  </div>
</body>
</html>
