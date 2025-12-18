<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin | Kubooking</title>
  <link rel="stylesheet" href="/kubooking/public/src/output.css">
</head>

<body class="bg-[#f2f7fc] text-gray-800 flex">

  <!-- SIDEBAR -->
  <?php
  $sidebarPath = __DIR__ . '/../layout/sidebar.php';
  if (file_exists($sidebarPath)) {
    include $sidebarPath;
  }

  // --- DATA DARI CONTROLLER (fallback aman) ---
  $activeTab = $activeTab ?? ($_GET['tab'] ?? 'booking');

  $booking_pending     = $booking_pending ?? [];
  $bookingPage         = $booking_page ?? 1;
  $bookingTotal        = $booking_total_pages ?? 1;

  $user_pending        = $user_pending ?? [];
  $userPage            = $user_page ?? 1;
  $userTotal           = $user_total_pages ?? 1;

  $verifikasi_hari_ini   = $verifikasi_hari_ini ?? 0;
  $booking_hari_ini      = $booking_hari_ini ?? 0;
  $ruang_kosong_hari_ini = $ruang_kosong_hari_ini ?? 0;
  $user_aktif            = $user_aktif ?? 0;
  ?>

  <!-- KONTEN UTAMA -->
  <div class="flex-1 flex flex-col h-screen overflow-y-auto">

    <?php
    $flashPath = __DIR__ . '/../layout/flash.php';
    if (file_exists($flashPath)) {
      include $flashPath;
    }
    ?>

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
        <div class="bg-[#1e3a5f] text-white rounded-lg p-6 text-center shadow">
          <h2 class="text-4xl font-bold"><?= htmlspecialchars((string)$verifikasi_hari_ini) ?></h2>
          <p class="text-sm mt-2">Verifikasi hari ini</p>
        </div>
        <div class="bg-[#1e3a5f] text-white rounded-lg p-6 text-center shadow">
          <h2 class="text-4xl font-bold"><?= htmlspecialchars((string)$booking_hari_ini) ?></h2>
          <p class="text-sm mt-2">Booking hari ini</p>
        </div>
        <div class="bg-[#1e3a5f] text-white rounded-lg p-6 text-center shadow">
          <h2 class="text-4xl font-bold"><?= htmlspecialchars((string)$ruang_kosong_hari_ini) ?></h2>
          <p class="text-sm mt-2">Ruang aktif hari ini</p>
        </div>
        <div class="bg-[#1e3a5f] text-white rounded-lg p-6 text-center shadow">
          <h2 class="text-4xl font-bold"><?= htmlspecialchars((string)$user_aktif) ?></h2>
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
                      <?= htmlspecialchars($b['status'] ?? 'pending') ?>
                    </span>
                  </td>
                  <td class="px-4 py-3 space-x-2">
                    <form action="index.php?controller=adminBooking&action=approve" method="POST" class="inline">
                      <input type="hidden" name="id_booking" value="<?= (int)($b['id'] ?? 0) ?>">
                      <button type="submit"
                        class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs transition">
                        Setujui
                      </button>
                    </form>

                    <form action="index.php?controller=adminBooking&action=reject" method="POST" class="inline"
                      onsubmit="return confirm('Yakin ingin menolak?');">
                      <input type="hidden" name="id_booking" value="<?= (int)($b['id'] ?? 0) ?>">
                      <input type="hidden" name="alasan" value="">
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
                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                  Belum ada booking pending.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>

        <!-- PAGINATION BOOKING (pakai komponen) -->
        <?php
        $pagination = [
          'pageKey'     => 'booking_page',
          'currentPage' => (int)$bookingPage,
          'totalPages'  => (int)$bookingTotal,
          'params'      => [
            'controller'  => 'admin',
            'action'      => 'home',
            'tab'         => 'booking',
            // jaga state pagination tab lain biar nggak reset
            'user_page'   => (int)$userPage,
          ],
        ];
        include __DIR__ . '/../layout/pagination.php';
        ?>
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
                      <?= htmlspecialchars($u['status'] ?? 'pending') ?>
                    </span>
                  </td>

                  <td class="px-4 py-3 space-x-2">
                    <form action="index.php?controller=admin&action=approveUser" method="POST" class="inline">
                      <input type="hidden" name="id_registrasi" value="<?= (int)($u['id_registrasi'] ?? 0) ?>">
                      <button type="submit"
                        class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs transition">
                        Setujui
                      </button>
                    </form>

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
                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                  Belum ada user pending verifikasi.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>

        <!-- PAGINATION USER (pakai komponen) -->
        <?php
        $pagination = [
          'pageKey'     => 'user_page',
          'currentPage' => (int)$userPage,
          'totalPages'  => (int)$userTotal,
          'params'      => [
            'controller'    => 'admin',
            'action'        => 'home',
            'tab'           => 'user',
            // jaga state pagination tab lain biar nggak reset
            'booking_page'  => (int)$bookingPage,
          ],
        ];
        include __DIR__ . '/../layout/pagination.php';
        ?>
      </div>

    </div>
  </div>

  <!-- SCRIPT TAB (visual instan) -->
  <script>
    const tabBooking = document.getElementById('tabBooking');
    const tabUser = document.getElementById('tabUser');
    const bookingTable = document.getElementById('bookingTable');
    const userTable = document.getElementById('userTable');

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