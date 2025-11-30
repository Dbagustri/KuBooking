<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Detail Ruangan - Roomify</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-gray-800">
  
  <?php require '../layout/navbars.php'; ?>
<a href="d2.php" class="inline-block text-gray-500 hover:text-gray-700 px-10 mt-3 mx-8 ">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
  <!-- Konten Utama -->
  <main class="max-w-7xl mx-auto px-0 pb-10 py-2 grid grid-cols-1 lg:grid-cols-2 gap-12">
    
    <!-- Kiri -->
    <div class="flex flex-col">



      <!-- Gambar -->
      <img src="https://images.unsplash.com/photo-1504384308090-c894fdcc538d" 
           alt="Ruangan" 
           class="rounded-0g shadow-md mb-6 w-full object-cover">

      <!-- Deskripsi -->
      <p class="text-justify leading-relaxed mb-6">
        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. 
        Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. 
        Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. 
        Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
      </p>

      <!-- Tombol Pesan -->
      <a href="d5.php" class="w-full text-center md:w-auto px-10 py-3 bg-[#274269] text-white font-medium rounded-md hover:bg-[#1f3553] transition">
        Pesan
      </a>
    </div>

    <!-- Kanan -->
    <div class="flex flex-col space-y-8">
      <div>
        <h1 class="text-2xl font-bold mb-4">Ruangan 1</h1>

        <!-- Info -->
        <div class="grid grid-cols-2 gap-4">
                        <div class="bg-[#274269] text-white p-4 rounded-lg flex items-start space-x-3">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0v-4a2 2 0 012-2h6a2 2 0 012 2v4m-6 0v-4"></path></svg>
                            <div>
                                <h3 class="font-semibold text-sm">Jenis</h3>
                                <p class="text-sm">Ruang Diskusi</p>
                            </div>
                        </div>
                        <div class="bg-[#274269] text-white p-4 rounded-lg flex items-start space-x-3">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div>
                                <h3 class="font-semibold text-sm">Status</h3>
                                <p class="text-sm">Tersedia</p>
                            </div>
                        </div>
                        <div class="bg-[#274269] text-white p-4 rounded-lg flex items-start space-x-3">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.08-1.283-.23-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.653.08-1.283.23-1.857m0 0A5.002 5.002 0 0112 13a5 5 0 014.77 4.143M5 13c0-3.31 2.69-6 6-6s6 2.69 6 6H5z"></path></svg>
                            <div>
                                <h3 class="font-semibold text-sm">Kapasitas</h3>
                                <p class="text-sm">5-10 Orang</p>
                            </div>
                        </div>
                        <div class="bg-[#274269] text-white p-4 rounded-lg flex items-start space-x-3">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 4v12l-4-2-4 2V4M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <div>
                                <h3 class="font-semibold text-sm">Lantai</h3>
                                <p class="text-sm">Lantai 1</p>
                            </div>
                        </div>
        </div>
      </div>

      <!-- Fasilitas -->
      <div>
        <h2 class="font-semibold text-lg mb-4">Fasilitas Ruangan</h2>
        <ul class="space-y-3">
                            <li class="flex items-center"><svg class="w-5 h-5 mr-2 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>TV</li>
                            <li class="flex items-center"><svg class="w-5 h-5 mr-2 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>Meja</li>
                            <li class="flex items-center"><svg class="w-5 h-5 mr-2 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 0l-3.536 3.536M14.828 9.172L11.293 12.707m3.535-3.535l3.536 3.536m-3.536-3.536l-3.536-3.536m3.536 3.536l3.536-3.536m-3.536 3.536L11.293 5.636m3.535 3.536L18.364 12.707m-3.535-3.535l-3.536 3.536"></path></svg>Kursi</li>
                            <li class="flex items-center"><svg class="w-5 h-5 mr-2 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Mainan</li>
        </ul>
      </div>

      <!-- Jam Operasional -->
      <div>
        <h2 class="font-semibold text-lg mb-4">Jam Operasional</h2>
        <div class="space-y-4">
          <div>
            <p class="text-sm text-gray-600 mb-1">Senin–Kamis</p>
            <div class="bg-gray-100 h-10 rounded-md"></div>
          </div>
          <div>
            <p class="text-sm text-gray-600 mb-1">Jumat</p>
            <div class="bg-gray-100 h-10 rounded-md"></div>
          </div>
        </div>
      </div>
    </div>
  </main>

</body>
</html>
