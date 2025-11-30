<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kode Kelompok - Roomify</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#274269] min-h-screen flex flex-col">

  <!-- Navbar -->
  <?php require '../layout/navbars.php'; ?>

  <!-- Konten -->
  <main class="flex-grow flex justify-center items-center pb-4 pt-0 mt-0 ">
    <div class="bg-gray-100 w-full max-w-5xl rounded-2xl shadow-md px-40 py-10">
      
      <!-- Header: Tombol Kembali + Judul + Kode -->
      <div class="flex justify-between items-center mb-6">
        <!-- Tombol kembali -->

        <a href="d5.php" class="inline-block text-gray-500 hover:text-gray-700 pr-20 ">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>

        <!-- Judul -->
        <h1 class="text-2xl font-bold text-[#274269]">Kode Kelompok</h1>

        <!-- Kode kelompok + tombol salin -->
        <div class="flex items-center space-x-2">
          <span class="text-xl font-bold text-[#274269]">heiuf83</span>
          <button class="text-[#274269] hover:text-gray-600" title="Salin kode">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12M8 12h12m-9 5h9" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Waktu anggota masuk -->
      <p class="text-center text-sm text-gray-600 mb-6">
        Waktu anggota masuk: <span class="font-semibold text-gray-800">05.00</span>
      </p>

      <!-- Daftar Anggota -->
      <div>
        <div class="flex justify-between items-center mb-3">
          <p class="text-[#274269] font-medium">Kapasitas : 5-10</p>
          <div class="flex items-center space-x-2 text-[#274269] font-semibold">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path d="M13 7a3 3 0 11-6 0 3 3 0 016 0zM4 13a4 4 0 018 0v1H4v-1zM15 13v1h-1v-1a5 5 0 00-10 0v1H3v-1a7 7 0 0114 0z" />
            </svg>
            <span>5</span>
          </div>
        </div>

        <!-- List User -->
        <div class="space-y-3">
          <div class="flex items-center space-x-2">
            <input type="text" value="User" class="flex-1 bg-gray-300 px-4 py-2 rounded-lg text-gray-700" disabled>
          </div>

          <!-- Anggota dengan tombol hapus -->
          <div class="flex items-center space-x-2">
            <input type="text" value="User" class="flex-1 bg-gray-300 px-4 py-2 rounded-lg text-gray-700" disabled>
            <button class="bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg p-2">
              ✕
            </button>
          </div>
          <div class="flex items-center space-x-2">
            <input type="text" value="User" class="flex-1 bg-gray-300 px-4 py-2 rounded-lg text-gray-700" disabled>
            <button class="bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg p-2">
              ✕
            </button>
          </div>
          <div class="flex items-center space-x-2">
            <input type="text" value="User" class="flex-1 bg-gray-300 px-4 py-2 rounded-lg text-gray-700" disabled>
            <button class="bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg p-2">
              ✕
            </button>
          </div>
          <div class="flex items-center space-x-2">
            <input type="text" value="User" class="flex-1 bg-gray-300 px-4 py-2 rounded-lg text-gray-700" disabled>
            <button class="bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg p-2">
              ✕
            </button>
          </div>
        </div>
      </div>

      <!-- Tombol Aksi -->
<div class="mt-8 space-y-3">
  <a href="d20.php" 
     class="block text-center w-full bg-[#274269] hover:bg-[#1f3756] text-white py-3 rounded-lg font-medium transition">
     Booking
  </a>

  <button class="w-full bg-red-500 hover:bg-red-600 text-white py-3 rounded-lg font-medium transition">
    Hapus Kelompok
  </button>
</div>

    </div>
  </main>

</body>
</html>
