<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Notifikasi | Roomify</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white min-h-screen font-sans">
    <?php require '../layout/navbars.php'; ?>
  <!-- Header biru -->


  <!-- Konten -->
  <div class="max-w-6xl mx-auto px-10 py-10">

    <!-- Panah kembali dan judul -->
    <div class="flex items-center mb-8">
      <a href="javascript:history.back()" class="text-black hover:text-gray-600">
        <!-- Ikon panah kiri -->
      <a href="d8.php" class="inline-block text-gray-500 hover:text-gray-700 px-0 mx-0 px-0 mb-5">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
    </a>
      </a>
      <h2 class="text-2xl font-bold text-center flex-1 -ml-6">Notifikasi</h2>
    </div>

    <!-- Daftar notifikasi -->
    <div class="space-y-8">

      <!-- Template Notifikasi -->
      <?php for ($i = 0; $i < 4; $i++): ?>
      <div class="border-b border-gray-300 pb-5">
        <div class="flex items-center justify-between">
          <h3 class="font-semibold text-lg text-black">Notifikasi</h3>
          <!-- Rating -->
          <div class="flex items-center space-x-1 text-yellow-400">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.1 3.389a1 1 0 00.95.69h3.565c.969 0 1.371 1.24.588 1.81l-2.887 2.1a1 1 0 00-.364 1.118l1.1 3.389c.3.921-.755 1.688-1.538 1.118l-2.887-2.1a1 1 0 00-1.176 0l-2.887 2.1c-.783.57-1.838-.197-1.538-1.118l1.1-3.389a1 1 0 00-.364-1.118l-2.887-2.1c-.783-.57-.38-1.81.588-1.81h3.565a1 1 0 00.95-.69l1.1-3.389z" /></svg>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.1 3.389a1 1 0 00.95.69h3.565c.969 0 1.371 1.24.588 1.81l-2.887 2.1a1 1 0 00-.364 1.118l1.1 3.389c.3.921-.755 1.688-1.538 1.118l-2.887-2.1a1 1 0 00-1.176 0l-2.887 2.1c-.783.57-1.838-.197-1.538-1.118l1.1-3.389a1 1 0 00-.364-1.118l-2.887-2.1c-.783-.57-.38-1.81.588-1.81h3.565a1 1 0 00.95-.69l1.1-3.389z" /></svg>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.1 3.389a1 1 0 00.95.69h3.565c.969 0 1.371 1.24.588 1.81l-2.887 2.1a1 1 0 00-.364 1.118l1.1 3.389c.3.921-.755 1.688-1.538 1.118l-2.887-2.1a1 1 0 00-1.176 0l-2.887 2.1c-.783.57-1.838-.197-1.538-1.118l1.1-3.389a1 1 0 00-.364-1.118l-2.887-2.1c-.783-.57-.38-1.81.588-1.81h3.565a1 1 0 00.95-.69l1.1-3.389z" /></svg>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.1 3.389a1 1 0 00.95.69h3.565c.969 0 1.371 1.24.588 1.81l-2.887 2.1a1 1 0 00-.364 1.118l1.1 3.389c.3.921-.755 1.688-1.538 1.118l-2.887-2.1a1 1 0 00-1.176 0l-2.887 2.1c-.783.57-1.838-.197-1.538-1.118l1.1-3.389a1 1 0 00-.364-1.118l-2.887-2.1c-.783-.57-.38-1.81.588-1.81h3.565a1 1 0 00.95-.69l1.1-3.389z" /></svg>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="#d1d5db" class="w-4 h-4"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.1 3.389a1 1 0 00.95.69h3.565c.969 0 1.371 1.24.588 1.81l-2.887 2.1a1 1 0 00-.364 1.118l1.1 3.389c.3.921-.755 1.688-1.538 1.118l-2.887-2.1a1 1 0 00-1.176 0l-2.887 2.1c-.783.57-1.838-.197-1.538-1.118l1.1-3.389a1 1 0 00-.364-1.118l-2.887-2.1c-.783-.57-.38-1.81.588-1.81h3.565a1 1 0 00.95-.69l1.1-3.389z" /></svg>
          </div>
        </div>

        <p class="text-sm text-gray-700 mt-1">
          Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
          Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
        </p>

        <a href="#" class="text-sm text-[#274269] font-medium mt-2 inline-block hover:underline">Lihat Selengkapnya</a>

        <div class="flex justify-end text-xs text-gray-500 mt-1">
          <span>27 Okt 2025</span>
          <span class="ml-3">12.00</span>
        </div>
      </div>
      <?php endfor; ?>

    </div>
  </div>

</body>
</html>
