<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Navbar Sudah Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Animasi muncul dropdown */
    .dropdown-enter {
      opacity: 0;
      transform: translateY(-10px);
      transition: opacity 0.2s ease, transform 0.2s ease;
    }
    .dropdown-enter-active {
      opacity: 1;
      transform: translateY(0);
    }
  </style>
</head>

<body class="bg-white">

  <!-- ================= NAVBAR (SUDAH LOGIN) ================= -->
  <nav class="bg-[#274269] text-white px-8 py-4 flex justify-between items-center relative shadow-lg">
    <a href="d2.php">
    <h1 class="text-2xl font-bold">Roomify</h1>
    </a>
    <div class="flex items-center gap-3 relative">
      <img src="images.jpeg" alt="Profile" class="w-10 h-10 rounded-full border-2 border-white">
      <span class="font-medium">Diandra B.</span>

      <!-- Tombol Menu -->
      <button id="menuBtn" class="ml-2 focus:outline-none">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
             viewBox="0 0 24 24" stroke-width="2.5" stroke="white"
             class="w-7 h-7">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>

      <!-- Dropdown Menu -->
      <div id="dropdownMenu"
           class="hidden absolute top-14 right-0 bg-white text-[#274269] rounded-lg shadow-lg w-44 z-50">
        <ul class="flex flex-col py-2">
          <li><a href="d13.php" class="block px-4 py-2 hover:bg-gray-100">Riwayat</a></li>
          <li><a href="d8.php" class="block px-4 py-2 hover:bg-gray-100">Profil</a></li>
          <li><a href="d1.php" class="block px-4 py-2 hover:bg-gray-100">Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <script>
    const menuBtn = document.getElementById('menuBtn');
    const dropdown = document.getElementById('dropdownMenu');

    // Toggle dropdown saat tombol diklik
    menuBtn.addEventListener('click', (e) => {
      e.stopPropagation(); // cegah window click langsung menutup
      dropdown.classList.toggle('hidden');

      // Tambahkan animasi halus saat muncul
      if (!dropdown.classList.contains('hidden')) {
        dropdown.classList.add('dropdown-enter');
        setTimeout(() => dropdown.classList.add('dropdown-enter-active'), 10);
        setTimeout(() => dropdown.classList.remove('dropdown-enter', 'dropdown-enter-active'), 250);
      }
    });

    // Tutup dropdown kalau klik di luar
    window.addEventListener('click', (e) => {
      if (!menuBtn.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.classList.add('hidden');
      }
    });

    // Biarkan link di dropdown bisa diklik tanpa gangguan
    dropdown.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', (e) => {
        e.stopPropagation(); // biar klik link tidak menutup sebelum pindah halaman
      });
    });
  </script>

</body>
</html>
