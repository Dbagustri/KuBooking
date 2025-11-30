<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Roomify Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">

  <!-- PROFIL & TOMBOL -->
  <div class="flex items-center justify-between bg-[#274269] text-white rounded-2xl p-8 my-10 w-full max-w-7xl mx-auto shadow-lg">
    <!-- Kiri -->
    <div class="flex items-center space-x-6">
      <img src="images.jpeg" alt="Profile" class="w-32 h-32 rounded-full object-cover shadow-lg">
      <div>
        <h2 class="text-2xl font-bold">Halo Diandra Bagustri</h2>
        <p class="text-gray-200 text-lg">Jurusan TIK</p>
      </div>
    </div>

    <!-- Tengah -->
    <div class="text-left">
      <p class="text-xl font-semibold">Peminjaman Aktif :</p>
      <p class="text-gray-200 text-base">Ruangan Pertama 09.00–12.00<br>26 Okt 2025</p>
    </div>

    <!-- Kanan -->
    <div class="flex flex-col space-y-3">
      <button id="btnPilih" class="bg-white text-[#1e3a5f] font-semibold py-3 px-6 rounded-lg hover:bg-gray-100 transition">
        Pilih Ruangan
      </button>
      <button id="btnGabung" class="bg-white text-[#1e3a5f] font-semibold py-3 px-6 rounded-lg hover:bg-gray-100 transition">
        Gabung Kelompok
      </button>
    </div>
  </div>

  <!-- === MODAL 1: PILIH RUANGAN === -->
  <div id="modalPilih" class="fixed inset-0 z-50 hidden flex items-center justify-center">
    <!-- Background blur -->
    <div class="absolute inset-0 bg-black/40 backdrop-blur-md"></div>

    <!-- Box Modal -->
    <div class="relative bg-white rounded-2xl shadow-xl p-8 w-[450px] text-center z-10">
      <h2 class="text-xl font-bold mb-6">Pilih Ruangan</h2>
      <div class="border rounded-lg overflow-hidden">
        <select id="selectRuangan" class="w-full border-none px-3 py-2 focus:ring-0 text-gray-700">
          <option selected disabled>Pilih Ruangan</option>
          <option value="1">Ruangan 1</option>
          <option value="2">Ruangan 2</option>
          <option value="3">Ruangan 3</option>
          <option value="4">Ruangan 4</option>
          <option value="5">Ruangan 5</option>
        </select>
      </div>
      <div class="mt-6 flex justify-center">
        <button id="closePilih" class="bg-[#274269] text-white px-5 py-2 rounded hover:bg-[#1f3555] transition">
          Tutup
        </button>
      </div>
    </div>
  </div>

  <!-- === MODAL 2: GABUNG KELOMPOK === -->
  <div id="modalGabung" class="fixed inset-0 z-50 hidden flex items-center justify-center">
    <!-- Background blur -->
    <div class="absolute inset-0 bg-black/40 backdrop-blur-md"></div>

    <!-- Box Modal -->
    <div class="relative bg-white rounded-2xl shadow-xl p-8 w-[450px] text-center z-10">
      <h2 class="text-xl font-bold mb-6">Masukkan Kode Ruangan</h2>
      <input type="text" placeholder="Masukkan kode"
             class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300 text-gray-700 mb-6">
      <div class="flex justify-center">
        <button id="closeGabung" class="bg-[#274269] text-white px-5 py-2 rounded hover:bg-[#1f3555] transition">
          Tutup
        </button>
        <button href="d20.php" class="bg-[#274269] text-white px-5 py-2 rounded hover:bg-[#1f3555] transition">
          oke
        </button>
      </div>
    </div>
  </div>

  <!-- === SCRIPT: Logika Modal + Redirect === -->
  <script>
    const btnPilih = document.getElementById('btnPilih');
    const btnGabung = document.getElementById('btnGabung');
    const modalPilih = document.getElementById('modalPilih');
    const modalGabung = document.getElementById('modalGabung');
    const closePilih = document.getElementById('closePilih');
    const closeGabung = document.getElementById('closeGabung');
    const selectRuangan = document.getElementById('selectRuangan');

    // buka modal
    btnPilih.addEventListener('click', () => modalPilih.classList.remove('hidden'));
    btnGabung.addEventListener('click', () => modalGabung.classList.remove('hidden'));

    // tutup modal
    closePilih.addEventListener('click', () => modalPilih.classList.add('hidden'));
    closeGabung.addEventListener('click', () => modalGabung.classList.add('hidden'));

    // klik di luar box modal untuk menutup
    [modalPilih, modalGabung].forEach(modal => {
      modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.classList.add('hidden');
      });
    });

    // redirect ke d5.php setelah pilih ruangan
    selectRuangan.addEventListener('change', (e) => {
      const value = e.target.value;
      if (value) {
        // redirect ke d5.php (bisa ditambah query string nanti)
        window.location.href = "d5.php";
      }
    });
  </script>
</body>
</html>
