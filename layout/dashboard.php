<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin | KoBooking</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#f2f7fc] text-gray-800 flex">

  <!-- SIDEBAR -->
  <?php include 'sidebar.php'; ?>

  <!-- KONTEN UTAMA -->
  <div class="flex-1 flex flex-col">

    <!-- NAVBAR -->
    <div class="m-4"> <!-- jarak luar atas, kiri, kanan -->
      <?php include 'nav-admin.php'; ?>
    </div>

    <!-- DASHBOARD -->
    <div class="px-8 pb-8 space-y-6">
      <h1 class="text-2xl font-bold text-[#1e3a5f]">Dashboard Admin</h1>

      <!-- Statistik -->
      <!-- Statistik -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

  <!-- Verifikasi Hari Ini -->
  <div class="bg-[#1e3a5f] text-white rounded-lg p-6 text-center shadow">
    <h2 class="text-4xl font-bold">
      <?php echo $verifikasi_hari_ini ?? 8; ?>
    </h2>
    <p class="text-sm mt-2">Verifikasi hari ini</p>
  </div>

  <!-- Booking Hari Ini -->
  <div class="bg-[#1e3a5f] text-white rounded-lg p-6 text-center shadow">
    <h2 class="text-4xl font-bold">
      <?php echo $booking_hari_ini ?? 24; ?>
    </h2>
    <p class="text-sm mt-2">Booking hari ini</p>
  </div>

  <!-- Ruang Kosong Hari Ini -->
  <div class="bg-[#1e3a5f] text-white rounded-lg p-6 text-center shadow">
    <h2 class="text-4xl font-bold">
      <?php echo $ruang_kosong_hari_ini ?? 12; ?>
    </h2>
    <p class="text-sm mt-2">Ruang aktif hari ini</p>
  </div>

  <!-- Total User Aktif -->
  <div class="bg-[#1e3a5f] text-white rounded-lg p-6 text-center shadow">
    <h2 class="text-4xl font-bold">
      <?php echo $user_aktif ?? 105; ?>
    </h2>
    <p class="text-sm mt-2">User aktif</p>
  </div>

</div>

      <!-- TAB -->
      <div class="flex bg-gray-200 rounded-lg overflow-hidden mt-6">
        <button id="tabBooking" class="flex-1 py-3 bg-[#1e3a5f] text-white font-semibold transition">Booking Pending</button>
        <button id="tabUser" class="flex-1 py-3 text-gray-800 font-semibold transition">User Pending</button>
      </div>

      <!-- TABEL BOOKING PENDING -->
      <div id="bookingTable" class="overflow-x-auto mt-4">
        <table class="min-w-full border-collapse">
          <thead>
            <tr class="bg-[#1e3a5f] text-white text-left">
              <th class="px-4 py-3">Kode</th>
              <th class="px-4 py-3">PJ</th>
              <th class="px-4 py-3">Ruang</th>
              <th class="px-4 py-3">Waktu</th>
              <th class="px-4 py-3">Kapasitas</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php for ($i=0; $i<5; $i++): ?>
            <tr class="<?= $i % 2 == 0 ? 'bg-gray-100' : 'bg-gray-200'; ?> text-gray-800">
              <td class="px-4 py-3">XJA5IK</td>
              <td class="px-4 py-3">Budi Budian</td>
              <td class="px-4 py-3">Ruang Diskusi 3</td>
              <td class="px-4 py-3">13 Nov, 13:00–15:00</td>
              <td class="px-4 py-3">4 / 6</td>
              <td class="px-4 py-3">Pending</td>
              <td class="px-4 py-3 space-x-2">
                <button class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded">Setujui</button>
                <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">Tolak</button>
              </td>
            </tr>
            <?php endfor; ?>
          </tbody>
        </table>

        <!-- Pagination -->
        <div class="flex justify-center mt-4 space-x-2">
          <button class="px-2 py-1 rounded bg-gray-200">&lt;</button>
          <button class="px-3 py-1 rounded bg-gray-200">1</button>
          <button class="px-3 py-1 rounded bg-[#1e3a5f] text-white">2</button>
          <button class="px-3 py-1 rounded bg-gray-200">3</button>
          <button class="px-2 py-1 rounded bg-gray-200">&gt;</button>
        </div>
      </div>

      <!-- TABEL USER PENDING -->
      <div id="userTable" class="overflow-x-auto mt-4 hidden">
        <table class="min-w-full border-collapse">
          <thead>
            <tr class="bg-[#1e3a5f] text-white text-left">
              <th class="px-4 py-3">Nama</th>
              <th class="px-4 py-3">Email</th>
              <th class="px-4 py-3">Jurusan</th>
              <th class="px-4 py-3">Screenshot</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php for ($i=0; $i<5; $i++): ?>
            <tr class="<?= $i % 2 == 0 ? 'bg-gray-100' : 'bg-gray-200'; ?> text-gray-800">
              <td class="px-4 py-3">Budi Budian</td>
              <td class="px-4 py-3">andra@gmail.com</td>
              <td class="px-4 py-3">TIK</td>
              <td class="px-4 py-3 text-blue-600 hover:underline"><a href="#">lihat bukti</a></td>
              <td class="px-4 py-3">Approved</td>
              <td class="px-4 py-3 space-x-2">
                <button class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded">Setujui</button>
                <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">Tolak</button>
              </td>
            </tr>
            <?php endfor; ?>
          </tbody>
        </table>

        <!-- Pagination -->
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

  <!-- SCRIPT TAB -->
  <script>
    const tabBooking = document.getElementById('tabBooking');
    const tabUser = document.getElementById('tabUser');
    const bookingTable = document.getElementById('bookingTable');
    const userTable = document.getElementById('userTable');

    tabBooking.addEventListener('click', () => {
      tabBooking.classList.add('bg-[#1e3a5f]', 'text-white');
      tabUser.classList.remove('bg-[#1e3a5f]', 'text-white');
      bookingTable.classList.remove('hidden');
      userTable.classList.add('hidden');
    });

    tabUser.addEventListener('click', () => {
      tabUser.classList.add('bg-[#1e3a5f]', 'text-white');
      tabBooking.classList.remove('bg-[#1e3a5f]', 'text-white');
      bookingTable.classList.add('hidden');
      userTable.classList.remove('hidden');
    });
  </script>

</body>
</html>
