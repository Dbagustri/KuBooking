<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin | Kubooking</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#f2f7fc] text-gray-800 flex">

  <!-- SIDEBAR -->
  <?php
  $sidebarPath = __DIR__ . '/../layout/sidebar.php';
  if (file_exists($sidebarPath)) {
    include $sidebarPath;
  }

  // --- LOGIKA HALAMAN & TAB ---
  // Menangkap tab mana yang sedang aktif dari URL (default: booking)
  $activeTab = $_GET['tab'] ?? 'booking';

  // Default value untuk pagination (jika controller belum mengirim data, dianggap halaman 1 dari 1)
  $bookingPage  = $booking_page ?? 1;
  $bookingTotal = $booking_total_pages ?? 1;

  $userPage     = $user_page ?? 1;
  $userTotal    = $user_total_pages ?? 1;
  ?>

  <!-- KONTEN UTAMA -->
  <div class="flex-1 flex flex-col h-screen overflow-y-auto">

    <!-- NAVBAR -->
    <div class="m-4">
      <?php
      $navPath = __DIR__ . '/../layout/nav-admin.php';
      if (file_exists($navPath)) {
        include $navPath;
      }
      ?>
    </div>

    <!-- DASHBOARD -->
    <div class="px-8 pb-8 space-y-6">
      <h1 class="text-2xl font-bold text-[#1e3a5f]">Dashboard Admin</h1>

      <!-- Statistik -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Verifikasi Hari Ini -->
        <div class="bg-[#1e3a5f] text-white rounded-lg p-6 text-center shadow">
          <h2 class="text-4xl font-bold"><?= htmlspecialchars($verifikasi_hari_ini ?? 0) ?></h2>
          <p class="text-sm mt-2">Verifikasi hari ini</p>
        </div>
        <!-- Booking Hari Ini -->
        <div class="bg-[#1e3a5f] text-white rounded-lg p-6 text-center shadow">
          <h2 class="text-4xl font-bold"><?= htmlspecialchars($booking_hari_ini ?? 0) ?></h2>
          <p class="text-sm mt-2">Booking hari ini</p>
        </div>
        <!-- Ruang Aktif Hari Ini -->
        <div class="bg-[#1e3a5f] text-white rounded-lg p-6 text-center shadow">
          <h2 class="text-4xl font-bold"><?= htmlspecialchars($ruang_kosong_hari_ini ?? 0) ?></h2>
          <p class="text-sm mt-2">Ruang aktif hari ini</p>
        </div>
        <!-- Total User Aktif -->
        <div class="bg-[#1e3a5f] text-white rounded-lg p-6 text-center shadow">
          <h2 class="text-4xl font-bold"><?= htmlspecialchars($user_aktif ?? 0) ?></h2>
          <p class="text-sm mt-2">User aktif</p>
        </div>
      </div>

      <!-- TAB BUTTONS -->
      <div class="flex bg-gray-200 rounded-lg overflow-hidden mt-6">
        <button id="tabBooking"
          class="flex-1 py-3 font-semibold transition <?= $activeTab === 'booking' ? 'bg-[#1e3a5f] text-white' : 'text-gray-800' ?>">
          Booking Pending
        </button>
        <button id="tabUser"
          class="flex-1 py-3 font-semibold transition <?= $activeTab === 'user' ? 'bg-[#1e3a5f] text-white' : 'text-gray-800' ?>">
          User Pending
        </button>
      </div>

      <!-- === TABEL BOOKING PENDING === -->
      <div id="bookingTable" class="overflow-x-auto mt-4 <?= $activeTab === 'booking' ? '' : 'hidden' ?>">
        <table class="min-w-full border-collapse bg-white shadow-sm rounded-lg overflow-hidden">
          <thead>
            <tr class="bg-[#1e3a5f] text-white text-left">
              <th class="px-4 py-3">Kode</th>
              <th class="px-4 py-3">PJ</th>
              <th class="px-4 py-3">Ruang</th>
              <th class="px-4 py-3">Waktu</th>
              <th class="px-4 py-3">Kapasitas</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($booking_pending)): ?>
              <?php foreach ($booking_pending as $i => $b): ?>
                <tr class="<?= $i % 2 == 0 ? 'bg-gray-50' : 'bg-gray-100'; ?> text-gray-800 border-b">
                  <td class="px-4 py-3"><?= htmlspecialchars($b['kode'] ?? '-') ?></td>
                  <td class="px-4 py-3"><?= htmlspecialchars($b['pj'] ?? '-') ?></td>
                  <td class="px-4 py-3"><?= htmlspecialchars($b['ruang'] ?? '-') ?></td>
                  <td class="px-4 py-3"><?= htmlspecialchars($b['waktu'] ?? '-') ?></td>
                  <td class="px-4 py-3"><?= htmlspecialchars($b['kapasitas'] ?? '-') ?></td>
                  <td class="px-4 py-3">
                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">
                      <?= htmlspecialchars($b['status'] ?? 'Pending') ?>
                    </span>
                  </td>
                  <td class="px-4 py-3 space-x-2">
                    <form action="index.php?controller=adminBooking&action=approve" method="POST" class="inline">
                      <input type="hidden" name="id_booking" value="<?= (int)($b['id'] ?? 0) ?>">
                      <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs transition">Setujui</button>
                    </form>
                    <form action="index.php?controller=adminBooking&action=reject" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menolak?');">
                      <input type="hidden" name="id_booking" value="<?= (int)($b['id'] ?? 0) ?>">
                      <input type="hidden" name="alasan" value="">
                      <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs transition">Tolak</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="px-4 py-8 text-center text-gray-500">Belum ada booking pending.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>

        <!-- PAGINATION BOOKING (REAL) -->
        <?php if ($bookingTotal > 1): ?>
          <div class="flex justify-center mt-4 space-x-2">
            <!-- Prev -->
            <?php if ($bookingPage > 1): ?>
              <a href="index.php?controller=admin&action=home&tab=booking&booking_page=<?= $bookingPage - 1 ?>"
                class="px-2 py-1 rounded bg-gray-200 hover:bg-gray-300 text-gray-700">&lt;</a>
            <?php else: ?>
              <span class="px-2 py-1 rounded bg-gray-100 text-gray-400 cursor-not-allowed">&lt;</span>
            <?php endif; ?>

            <!-- Numbers -->
            <?php for ($i = 1; $i <= $bookingTotal; $i++): ?>
              <a href="index.php?controller=admin&action=home&tab=booking&booking_page=<?= $i ?>"
                class="px-3 py-1 rounded <?= $i == $bookingPage ? 'bg-[#1e3a5f] text-white' : 'bg-gray-200 hover:bg-gray-300 text-gray-700' ?>">
                <?= $i ?>
              </a>
            <?php endfor; ?>

            <!-- Next -->
            <?php if ($bookingPage < $bookingTotal): ?>
              <a href="index.php?controller=admin&action=home&tab=booking&booking_page=<?= $bookingPage + 1 ?>"
                class="px-2 py-1 rounded bg-gray-200 hover:bg-gray-300 text-gray-700">&gt;</a>
            <?php else: ?>
              <span class="px-2 py-1 rounded bg-gray-100 text-gray-400 cursor-not-allowed">&gt;</span>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>

      <!-- === TABEL USER PENDING === -->
      <div id="userTable" class="overflow-x-auto mt-4 <?= $activeTab === 'user' ? '' : 'hidden' ?>">
        <table class="min-w-full border-collapse bg-white shadow-sm rounded-lg overflow-hidden">
          <thead>
            <tr class="bg-[#1e3a5f] text-white text-left">
              <th class="px-4 py-3">Nama</th>
              <th class="px-4 py-3">Email</th>
              <th class="px-4 py-3">Jurusan</th>
              <th class="px-4 py-3">Screenshot</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($user_pending)): ?>
              <?php foreach ($user_pending as $i => $u): ?>
                <tr class="<?= $i % 2 == 0 ? 'bg-gray-50' : 'bg-gray-100'; ?> text-gray-800 border-b">
                  <td class="px-4 py-3"><?= htmlspecialchars($u['nama'] ?? '-') ?></td>
                  <td class="px-4 py-3"><?= htmlspecialchars($u['email'] ?? '-') ?></td>
                  <td class="px-4 py-3"><?= htmlspecialchars($u['jurusan'] ?? '-') ?></td>
                  <td class="px-4 py-3 text-blue-600 hover:underline text-sm">
                    <?php if (!empty($u['screenshot_kubaca'])): ?>
                      <a href="<?= htmlspecialchars($u['screenshot_kubaca']) ?>" target="_blank">Lihat Bukti</a>
                    <?php else: ?>
                      <span class="text-gray-400">Tidak ada</span>
                    <?php endif; ?>
                  </td>
                  <td class="px-4 py-3">
                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">
                      <?= htmlspecialchars($u['status'] ?? 'Pending') ?>
                    </span>
                  </td>
                  <td class="px-4 py-3 space-x-2">
                    <!-- Setujui -->
                    <form action="index.php?controller=admin&action=approveUser" method="POST" class="inline">
                      <input type="hidden" name="id_registrasi" value="<?= (int)($u['id_registrasi'] ?? 0) ?>">
                      <button type="submit"
                        class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs transition">
                        Setujui
                      </button>
                    </form>

                    <!-- Tolak -->
                    <form action="index.php?controller=admin&action=rejectUser" method="POST" class="inline"
                      onsubmit="return confirm('Yakin ingin menolak?');">
                      <input type="hidden" name="id_registrasi" value="<?= (int)($u['id_registrasi'] ?? 0) ?>">
                      <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs transition">
                        Tolak
                      </button>
                    </form>
                  </td>

                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" class="px-4 py-8 text-center text-gray-500">Belum ada user pending verifikasi.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>

        <!-- PAGINATION USER (REAL) -->
        <?php if ($userTotal > 1): ?>
          <div class="flex justify-center mt-4 space-x-2">
            <!-- Prev -->
            <?php if ($userPage > 1): ?>
              <a href="index.php?controller=admin&action=home&tab=user&user_page=<?= $userPage - 1 ?>"
                class="px-2 py-1 rounded bg-gray-200 hover:bg-gray-300 text-gray-700">&lt;</a>
            <?php else: ?>
              <span class="px-2 py-1 rounded bg-gray-100 text-gray-400 cursor-not-allowed">&lt;</span>
            <?php endif; ?>

            <!-- Numbers -->
            <?php for ($i = 1; $i <= $userTotal; $i++): ?>
              <a href="index.php?controller=admin&action=home&tab=user&user_page=<?= $i ?>"
                class="px-3 py-1 rounded <?= $i == $userPage ? 'bg-[#1e3a5f] text-white' : 'bg-gray-200 hover:bg-gray-300 text-gray-700' ?>">
                <?= $i ?>
              </a>
            <?php endfor; ?>

            <!-- Next -->
            <?php if ($userPage < $userTotal): ?>
              <a href="index.php?controller=admin&action=home&tab=user&user_page=<?= $userPage + 1 ?>"
                class="px-2 py-1 rounded bg-gray-200 hover:bg-gray-300 text-gray-700">&gt;</a>
            <?php else: ?>
              <span class="px-2 py-1 rounded bg-gray-100 text-gray-400 cursor-not-allowed">&gt;</span>
            <?php endif; ?>
          </div>
        <?php endif; ?>

      </div>

    </div>
  </div>

  <!-- SCRIPT TAB (Hanya untuk interaksi visual instan, logic utama via URL di PHP) -->
  <script>
    const tabBooking = document.getElementById('tabBooking');
    const tabUser = document.getElementById('tabUser');
    const bookingTable = document.getElementById('bookingTable');
    const userTable = document.getElementById('userTable');

    // Fungsi helper untuk ganti URL tanpa refresh (agar kalau di refresh tetap di tab yg sama)
    function setTabUrl(tabName) {
      const url = new URL(window.location);
      url.searchParams.set('tab', tabName);
      window.history.pushState({}, '', url);
    }

    tabBooking.addEventListener('click', () => {
      tabBooking.classList.add('bg-[#1e3a5f]', 'text-white');
      tabBooking.classList.remove('text-gray-800');

      tabUser.classList.remove('bg-[#1e3a5f]', 'text-white');
      tabUser.classList.add('text-gray-800');

      bookingTable.classList.remove('hidden');
      userTable.classList.add('hidden');

      setTabUrl('booking');
    });

    tabUser.addEventListener('click', () => {
      tabUser.classList.add('bg-[#1e3a5f]', 'text-white');
      tabUser.classList.remove('text-gray-800');

      tabBooking.classList.remove('bg-[#1e3a5f]', 'text-white');
      tabBooking.classList.add('text-gray-800');

      bookingTable.classList.add('hidden');
      userTable.classList.remove('hidden');

      setTabUrl('user');
    });
  </script>

</body>

</html>