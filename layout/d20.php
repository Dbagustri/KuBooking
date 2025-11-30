<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Kelompok | Roomify</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#274269] min-h-screen flex flex-col text-gray-900 font-[Poppins]">
<?php require '../layout/navbars.php'; ?>
  <!-- Navbar -->

  <!-- Container utama -->
  <div class="w-full max-w-5xl mx-auto px-8 py-10 space-y-10">

    <!-- Card Informasi Ruangan -->
    <div class="bg-white rounded-xl p-8 shadow-md w-full">
      <div class="grid grid-cols-[500px_1fr] gap-y-4 text-[15px] items-start">

        <!-- Baris pertama (tombol kembali + Ruangan) -->
        <div class="flex items-center space-x-3">
          <a href="d6.php" class="inline-block text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
          </a>
          <p>Ruangan</p>
        </div>
        <p>: Ruangan Pertama</p>

        <!-- Baris lainnya -->
        <p class="ml-9">Durasi</p><p>: 3 Jam</p>
        <p class="ml-9">Tanggal & Waktu</p><p>: 15 Maret 2005, 13.00 – 15.00</p>
        <p class="ml-9">Status</p><p>: Approve</p>
        <p class="ml-9">Kode Ruangan</p><p>: B67SGTuY</p>
      </div>
    </div>

    <!-- Card Informasi Kelompok -->
    <div class="bg-white rounded-xl p-8 shadow-md w-full">

      <!-- Header -->
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-[#274269]">Kode Kelompok</h1>
        <div class="flex items-center space-x-2">
          <span class="text-xl font-bold text-[#274269]">heiuf83</span>
          <button class="text-[#274269] hover:text-gray-600" title="Salin kode" onclick="navigator.clipboard.writeText('heiuf83')">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12M8 12h12m-9 5h9" />
            </svg>
          </button>
        </div>
      </div>

      <p class="text-sm text-center text-gray-600 mb-3">Waktu anggota masuk : 05.00</p>

      <div class="flex justify-between items-center mb-3">
        <p class="text-sm text-gray-700">Anggota kelompok</p>
        <div class="flex items-center text-sm text-gray-700 space-x-1">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M7 20h5v-2a3 3 0 00-5.356-1.857M12 12a4 4 0 110-8 4 4 0 010 8zm6 0a4 4 0 110-8 4 4 0 010 8z" />
          </svg>
          <span>5</span>
        </div>
      </div>

      <!-- Daftar anggota -->
      <div class="space-y-2">
        <div class="bg-gray-200 rounded-md px-4 py-2">User</div>
        <div class="bg-gray-200 rounded-md px-4 py-2">User</div>
        <div class="bg-gray-200 rounded-md px-4 py-2">User</div>
        <div class="bg-gray-200 rounded-md px-4 py-2">User</div>
        <div class="bg-gray-200 rounded-md px-4 py-2">User</div>
      </div>
    </div>
  </div>

  <script>
    // Tombol kembali
    document.querySelector('a[href="#"]').addEventListener('click', e => {
      e.preventDefault();
      window.history.back();
    });
  </script>

</body>
</html>
