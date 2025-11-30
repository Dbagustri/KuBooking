<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Riwayat Peminjaman | Roomify</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white min-h-screen font-sans relative">
<?php require '../layout/navbars.php'; ?>

  <!-- Konten utama -->
  <div id="main-content" class="max-w-6xl mx-auto px-8 py-10 transition-all duration-300">

    <!-- Panah kembali & judul -->
    <div class="flex items-center mb-8">
      <a href="javascript:history.back()" class="text-black hover:text-gray-600">
        <!-- Ikon panah kiri -->
        <a href="d8.php" class="inline-block text-gray-500 hover:text-gray-700 px-50 mx-0 px-0 mb-5">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round"
            stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
      </a>
      <h2 class="text-2xl font-bold text-center flex-1 -ml-6">Riwayat Peminjaman</h2>
    </div>

    <!-- Tabel -->
    <div class="overflow-x-auto shadow-md rounded-lg">
      <table class="min-w-full text-sm text-left border border-gray-200">
        <thead class="bg-gray-200 text-gray-800 font-semibold">
          <tr>
            <th scope="col" class="px-6 py-3">Ruangan</th>
            <th scope="col" class="px-6 py-3">Tanggal & Waktu</th>
            <th scope="col" class="px-6 py-3">Durasi</th>
            <th scope="col" class="px-6 py-3">Status</th>
            <th scope="col" class="px-6 py-3">Kode</th>
            <th scope="col" class="px-6 py-3 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <!-- Row 1 -->
          <tr>
            <td class="px-6 py-4">Ruang Diskusi 1</td>
            <td class="px-6 py-4">15 Mar 2025, 13:00 – 15:00</td>
            <td class="px-6 py-4">3 Jam</td>
            <td class="px-6 py-4 text-blue-600 font-medium">Approve</td>
            <td class="px-6 py-4">76SGBU</td>
            <td class="px-6 py-4 flex items-center space-x-2 justify-center">
              <button onclick="openRatingModal()" class="bg-[#274269] text-white px-4 py-1 rounded-md hover:bg-[#1f3554]">Detail</button>
              <button class="bg-red-500 text-white px-4 py-1 rounded-md hover:bg-red-600">Batalkan</button>
            </td>
          </tr>

          <!-- Row 2 -->
          <tr>
            <td class="px-6 py-4">Ruang Diskusi 1</td>
            <td class="px-6 py-4">15 Mar 2025, 13:00 – 15:00</td>
            <td class="px-6 py-4">3 Jam</td>
            <td class="px-6 py-4 text-red-600 font-medium">Batal</td>
            <td class="px-6 py-4">-</td>
            <td class="px-6 py-4 flex items-center space-x-2 justify-center">
              <button onclick="openRatingModal()" class="bg-[#274269] text-white px-4 py-1 rounded-md hover:bg-[#1f3554]">Detail</button>
              <button class="bg-gray-400 text-white px-4 py-1 rounded-md cursor-not-allowed">Batalkan</button>
            </td>
          </tr>

          <!-- Row 3 -->
          <tr>
            <td class="px-6 py-4">Ruang Diskusi 1</td>
            <td class="px-6 py-4">15 Mar 2025, 13:00 – 15:00</td>
            <td class="px-6 py-4">3 Jam</td>
            <td class="px-6 py-4 text-yellow-500 font-medium">Pending</td>
            <td class="px-6 py-4">-</td>
            <td class="px-6 py-4 flex items-center space-x-2 justify-center">
              <button onclick="openRatingModal()" class="bg-[#274269] text-white px-4 py-1 rounded-md hover:bg-[#1f3554]">Detail</button>
              <button class="bg-red-500 text-white px-4 py-1 rounded-md hover:bg-red-600">Batalkan</button>
            </td>
          </tr>

          <!-- Row 4 -->
          <tr>
            <td class="px-6 py-4">Ruang Diskusi 1</td>
            <td class="px-6 py-4">15 Mar 2025, 13:00 – 15:00</td>
            <td class="px-6 py-4">3 Jam</td>
            <td class="px-6 py-4 text-gray-700 font-medium">Selesai</td>
            <td class="px-6 py-4">-</td>
            <td class="px-6 py-4 flex items-center space-x-2 justify-center">
              <button onclick="openRatingModal()" class="bg-[#274269] text-white px-4 py-1 rounded-md hover:bg-[#1f3554]">Detail</button>
              <button class="bg-gray-400 text-white px-4 py-1 rounded-md cursor-not-allowed">Batalkan</button>
            </td>
          </tr>

          <!-- Tambahan baris contoh -->
          <?php for ($i=0; $i<4; $i++): ?>
          <tr>
            <td class="px-6 py-4">Ruang Diskusi 1</td>
            <td class="px-6 py-4">15 Mar 2025, 13:00 – 15:00</td>
            <td class="px-6 py-4">3 Jam</td>
            <td class="px-6 py-4 text-gray-700 font-medium">Selesai</td>
            <td class="px-6 py-4">-</td>
            <td class="px-6 py-4 flex items-center space-x-2 justify-center">
              <button onclick="openRatingModal()" class="bg-[#274269] text-white px-4 py-1 rounded-md hover:bg-[#1f3554]">Detail</button>
              <button class="bg-gray-400 text-white px-4 py-1 rounded-md cursor-not-allowed">Batalkan</button>
            </td>
          </tr>
          <?php endfor; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal Rating -->
  <div id="ratingModal" class="hidden fixed inset-0 bg-black bg-opacity-30 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl p-8 w-[90%] max-w-md text-center">
      <div id="stars" class="flex justify-center space-x-2 mb-6">
        <button class="star text-3xl text-gray-400" onclick="setRating(1)">★</button>
        <button class="star text-3xl text-gray-400" onclick="setRating(2)">★</button>
        <button class="star text-3xl text-gray-400" onclick="setRating(3)">★</button>
        <button class="star text-3xl text-gray-400" onclick="setRating(4)">★</button>
        <button class="star text-3xl text-gray-400" onclick="setRating(5)">★</button>
      </div>

      <p class="text-gray-700 mb-3">Punya saran untuk Kami?</p>
      <textarea class="w-full border rounded-md p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#274269]"
        rows="3" placeholder="Tulis saranmu di sini..."></textarea>

      <div class="flex justify-center mt-6 space-x-4">
        <button class="bg-[#274269] text-white px-6 py-2 rounded-md hover:bg-[#1f3554]" onclick="closeRatingModal()">Kirim</button>
        <button class="bg-red-500 text-white px-6 py-2 rounded-md hover:bg-red-600" onclick="closeRatingModal()">Nanti</button>
      </div>
    </div>
  </div>

  <script>
    const modal = document.getElementById('ratingModal');
    const content = document.getElementById('main-content');
    let selectedRating = 0;

    function openRatingModal() {
      modal.classList.remove('hidden');
      content.classList.add('blur-sm');
    }

    function closeRatingModal() {
      modal.classList.add('hidden');
      content.classList.remove('blur-sm');
    }

    function setRating(num) {
      selectedRating = num;
      const stars = document.querySelectorAll('.star');
      stars.forEach((star, index) => {
        star.classList.toggle('text-yellow-400', index < num);
        star.classList.toggle('text-gray-400', index >= num);
      });
    }

    modal.addEventListener('click', (e) => {
      if (e.target === modal) closeRatingModal();
    });
  </script>

</body>
</html>
