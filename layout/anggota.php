<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola Anggota | KoBooking</title>
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
    <div class="px-8 pb-8 space-y-6">
      <h1 class="text-2xl font-bold text-[#1e3a5f]">Kelola User</h1>

      <!-- FILTER DAN SEARCH -->
      <div class="flex flex-wrap items-center gap-4">
        <select class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2 focus:outline-none">
          <option>Filter</option>
          <option>Pending</option>
          <option>Approved</option>
          <option>Rejected</option>
        </select>

        <div class="flex items-center bg-gray-200 rounded-full px-3 py-2 w-full sm:w-1/3">
          <input type="text" placeholder="Cari user..." class="bg-transparent w-full focus:outline-none">
          <button class="text-[#1e3a5f] ml-2">🔍</button>
        </div>
      </div>

      <!-- TABEL USER -->
      <div class="overflow-x-auto mt-4">
        <table class="min-w-full border-collapse rounded-lg overflow-hidden">
          <thead class="bg-[#1e3a5f] text-white">
            <tr>
              <th class="px-4 py-3 text-left">Nama</th>
              <th class="px-4 py-3 text-left">Email</th>
              <th class="px-4 py-3 text-left">Jurusan</th>
              <th class="px-4 py-3 text-left">Screenshot</th>
              <th class="px-4 py-3 text-left">Status</th>
              <th class="px-4 py-3 text-left">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php for ($i=0; $i<5; $i++): ?>
            <tr class="<?= $i % 2 == 0 ? 'bg-gray-100' : 'bg-gray-200'; ?>">
              <td class="px-4 py-3">Budi Budian</td>
              <td class="px-4 py-3">andra@gmail.com</td>
              <td class="px-4 py-3">TIK</td>
              <td class="px-4 py-3 text-blue-600 hover:underline"><a href="#">lihat bukti</a></td>
              <td class="px-4 py-3">Approved</td>
              <td class="px-4 py-3 space-x-2">
                <button class="bg-[#1e3a5f] text-white px-3 py-1 rounded">Edit</button>
                <button class="bg-red-600 text-white px-3 py-1 rounded">Delete</button>
              </td>
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
</body>
</html>
