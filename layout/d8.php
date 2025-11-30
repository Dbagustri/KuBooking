<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Profil Pengguna - Roomify</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#274269] min-h-screen text-gray-900 font-sans">

  <?php require '../layout/navbars.php'; ?>

  <!-- Container utama -->
  <div class="flex justify-center items-start mb-10 px-16 space-x-10">

    <!-- Bagian Kiri -->
    <div class="bg-gray-300 rounded-2xl shadow-md p-10 w-[560px] h-[640px] flex flex-col items-center">
      <img src="images.jpeg" alt="Profile" class="w-40 h-40 rounded-full object-cover mb-6">
      <h2 class="text-3xl font-bold">Diandra Bagustri</h2>
      <p class="text-gray-600 text-lg mb-10">User@gmail.com</p>

      <div class="w-full space-y-6">
        <div>
          <label class="block text-base font-semibold mb-2">Status</label>
          <input type="text" value="Aktif" class="w-full h-10 px-5 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-300 text-lg">
        </div>
        <div>
          <label class="block text-base font-semibold mb-2">NIM</label>
          <input type="text" value="2407411043" class="w-full h-10 px-5 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-300 text-lg">
        </div>
        <div>
          <label class="block text-base font-semibold mb-2">Jurusan</label>
          <input type="text" value="TIK" class="w-full h-10 px-5 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-300 text-lg">
        </div>
      </div>
    </div>

    <!-- Bagian Kanan -->
    <div class="flex flex-col justify-start w-[600px] h-[640px] space-y-8">
      <!-- Kotak atas -->
      <div class="bg-white rounded-2xl h-[190px] shadow-md"></div>

      <!-- Kotak bawah -->
      <div class="bg-white rounded-2xl shadow-md p-8 flex flex-col justify-between h-[500px]">
        <div class="space-y-4">
          <a href="d10.php" class="w-full flex justify-between items-center bg-gray-300 rounded-xl px-6 py-4 text-lg font-semibold text-black hover:bg-gray-300 transition">
            Edit Profile <span class="text-2xl">›</span>
          </a>
          <a href="d12.php" class="w-full flex justify-between items-center bg-gray-300 rounded-xl px-6 py-4 text-lg font-semibold text-black hover:bg-gray-300 transition">
            Notifikasi <span class="text-2xl">›</span>
          </a>
          <a href="d13.php" class="w-full flex justify-between items-center bg-gray-300 rounded-xl px-6 py-4 text-lg font-semibold text-black hover:bg-gray-300 transition">
            Riwayat Peminjaman <span class="text-2xl">›</span>
          </a>
          <a href="d11.php" class="w-full flex justify-between items-center bg-gray-300 rounded-xl px-6 py-4 text-lg font-semibold text-black hover:bg-gray-300 transition">
            Ganti Password <span class="text-2xl">›</span>
          </a>
        </div>

        <a href="d1.php" class="w-full bg-[#274269] hover:bg-[#1f3454] text-white text-center rounded-xl py-4 text-lg font-semibold transition">
          Log Out
        </a>
      </div>
    </div>
  </div>

</body>
</html>
