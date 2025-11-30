<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Profil - Roomify</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#274269] min-h-screen font-sans">

  <?php require '../layout/navbars.php'; ?>
    <a href="d8.php" class="inline-block text-gray-500 hover:text-gray-700 px-50 mx-40 px-4 mt-3 mb-2">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
    </a>
  <!-- Container Utama -->
  <div class="flex justify-center items-center mt-0 px-10 pb-20">
    <div class="bg-[#F6FAFF] w-[1100px] rounded-2xl shadow-lg p-10 relative">

      <!-- Foto Profil -->
      <div class="flex flex-col items-center mb-8">
        <div class="relative">
          <img src="images.jpeg" alt="Profile" class="w-44 h-44 rounded-full object-cover shadow-lg">
          <!-- Tombol Edit Foto -->
          <button class="absolute bottom-3 right-4 bg-[#274269] p-3 rounded-full text-white shadow-md hover:bg-[#1f3454] transition">
            <!-- Icon Pensil SVG -->
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232a2.5 2.5 0 013.536 3.536L7.5 20.036l-4 1 1-4 11.732-11.804z" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Form Edit Profil -->
      <form class="grid grid-cols-2 gap-x-16 gap-y-6 px-8">
        <!-- Kolom kiri -->
        <div>
          <label class="block text-[#274269] font-semibold mb-1 text-lg">Nama</label>
          <input type="text" value="Diandra Bagustri" class="w-full h-10 px-5 py-0 border border-gray-300 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-[#274269]" />
        </div>

        <div>
          <label class="block text-[#274269] font-semibold mb-1 text-lg">Email</label>
          <input type="email" value="User@gmail.com" class="w-full h-10 px-5 py-0 border border-gray-300 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-[#274269]" />
        </div>

        <div>
          <label class="block text-[#274269] font-semibold mb-1 text-lg">NIM</label>
          <input type="text" value="2407411043" class="w-full px-5 h-10 py-0 border border-gray-300 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-[#274269]" />
        </div>

        <div>
          <label class="block text-[#274269] font-semibold mb-1 text-lg">No. Hp</label>
          <input type="text" value="082180023726" class="w-full h-10 px-5 py-0 border border-gray-300 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-[#274269]" />
        </div>

        <div>
          <label class="block text-[#274269] font-semibold mb-1 text-lg">Jurusan</label>
          <input type="text" value="TIK" class="w-full px-5 py-0 h-10 border border-gray-300 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-[#274269]" />
        </div>

        <div>
          <label class="block text-[#274269] font-semibold mb-1 text-lg">Status</label>
          <input type="text" value="Aktif" class="w-full px-5 py-0 h-10 border border-gray-300 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-[#274269]" />
        </div>
      </form>

      <!-- Tombol Edit -->
      <div class="flex justify-center mt-8">
        <button class="bg-[#274269] hover:bg-[#1f3454] text-white font-semibold text-lg h-12  px-60 rounded-lg shadow-md transition">
          Edit
        </button>
      </div>
    </div>
  </div>

</body>
</html>
