<div class="flex justify-between items-center bg-[#274269] text-white px-6 py-3 rounded relative">
  <h2 class="text-lg font-semibold">Menu Admin</h2>
  <div class="flex items-center space-x-4">
    <span>Admin 1</span>
    <button id="logoutBtn" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">
      Logout
    </button>
  </div>

  <!-- Modal Konfirmasi Logout -->
  <div id="logoutModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-80 text-center">
      <h3 class="text-lg font-semibold text-gray-800 mb-4">Konfirmasi Logout</h3>
      <p class="text-gray-600 mb-6">Apakah kamu yakin ingin logout?</p>
      <div class="flex justify-center space-x-4">
        <button id="confirmLogout" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Ya</button>
        <button id="cancelLogout" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Batal</button>
      </div>
    </div>
  </div>
</div>

<script>
  const logoutBtn = document.getElementById('logoutBtn');
  const logoutModal = document.getElementById('logoutModal');
  const confirmLogout = document.getElementById('confirmLogout');
  const cancelLogout = document.getElementById('cancelLogout');

  // Tampilkan modal saat klik logout
  logoutBtn.addEventListener('click', () => {
    logoutModal.classList.remove('hidden');
  });

  // Tombol batal → sembunyikan modal
  cancelLogout.addEventListener('click', () => {
    logoutModal.classList.add('hidden');
  });

  // Tombol ya → redirect ke login.php
  confirmLogout.addEventListener('click', () => {
    window.location.href = 'login.php';
  });
</script>
