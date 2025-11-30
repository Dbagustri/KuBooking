<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Booking Ruangan Eksternal | Roomify</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#274269] min-h-screen flex flex-col">

  <?php require '../layout/navbars.php'; ?>

  <div class="flex-grow flex items-center justify-center px-4 pb-10">
    <div class="w-full max-w-5xl bg-white rounded-2xl shadow-2xl overflow-hidden grid md:grid-cols-2">

      <!-- Kiri -->
      <div class="p-8 md:p-12 bg-gray-100">
        <!-- Tombol kembali -->
        <a href="d1.php" class="text-gray-600 hover:text-gray-800 inline-flex items-center mb-6">
    <a href="d1.php" class="inline-block text-gray-500 hover:text-gray-700 px-50 mx-0 px-0 mb-5">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
    </a>
         
        </a>

        <!-- Judul -->
        <h1 class="text-3xl font-bold text-black">Booking Ruangan Eksternal</h1>
        <p class="text-gray-600 mb-6">Isi data untuk pinjam ruangan eksternal</p>

        <form action="#" method="POST" enctype="multipart/form-data" class="space-y-4">
          <input type="text" placeholder="Instansi" 
                 class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#274269]">
          <input type="text" placeholder="Keperluan" 
                 class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#274269]">
          <input type="number" placeholder="Jumlah Orang" 
                 class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#274269]">
          <select class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#274269]">
            <option selected hidden>Pilih Ruangan</option>
            <option>Ruang Rapat A</option>
            <option>Ruang Rapat B</option>
            <option>Ruang Auditorium</option>
          </select>

          <!-- Upload Surat -->
          <div>
            <label class="block text-gray-700 mb-1">Upload Surat</label>
            <span class="inline-block w-full cursor-pointer text-gray-500 bg-gray-200 text-center py-3 rounded-md hover:bg-gray-300 transition">
              Pilih File
            </span>
          </div>

          <!-- Tombol -->
<a href="d20.php" 
   class="block text-center w-full bg-[#274269] text-white py-3 rounded-md font-semibold hover:bg-[#1e3556] transition duration-300">
  Booking
</a>

        </form>
      </div>

      <!-- Kanan (Gambar) -->
      <div class="hidden md:block relative">
        <img src="rapat.png" alt="Gambar Ruangan"
             class="w-full h-full object-cover">
      </div>

    </div>
  </div>

</body>
</html>
