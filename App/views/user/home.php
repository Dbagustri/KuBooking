<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kubooking Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen font-sans">

  <?php
  // NAVBAR
  $navbarPath = __DIR__ . '/../layout/navbar.php';
  if (file_exists($navbarPath)) {
    include $navbarPath;
  }

  // pastikan variabel aman
  $rooms            = $rooms            ?? [];
  $join_error       = $join_error       ?? null;
  $currentUser      = $currentUser      ?? null;
  $booking_aktif    = $booking_aktif    ?? null;
  $canBook          = $canBook          ?? false;
  $unratedBooking   = $unratedBooking   ?? null; // 👈 PENTING: booking yang belum dirating

  $hasActiveBooking = !empty($booking_aktif);
  $buttonDisabled   = (!$canBook || $hasActiveBooking);
  ?>
  <?php
  $flashPath = __DIR__ . '/../layout/flash.php';
  if (file_exists($flashPath)) {
    include $flashPath;
  }
  ?>
  <!-- ALERT JOIN ERROR (jika ada) -->
  <div class="w-full max-w-7xl mx-auto mt-6 px-4 space-y-3">
    <?php if (!empty($join_error)): ?>
      <div class="bg-red-100 text-red-700 px-4 py-2 rounded-lg shadow">
        <?= htmlspecialchars($join_error) ?>
      </div>
    <?php endif; ?>

    <!-- 🔔 ALERT WAJIB RATING -->
    <?php if (!empty($unratedBooking)): ?>
      <div class="bg-yellow-50 border-l-4 border-yellow-400 px-4 py-3 rounded-lg shadow flex items-start gap-3">
        <div class="mt-0.5">⚠️</div>
        <div class="text-sm text-yellow-800">
          <p class="font-semibold mb-1">Kamu punya peminjaman yang belum diberi rating.</p>
          <p class="mb-1">
            Ruangan:
            <span class="font-medium">
              <?= htmlspecialchars($unratedBooking['nama_ruangan'] ?? 'Ruangan') ?>
            </span>
            —
            Tanggal:
            <span class="font-medium">
              <?= isset($unratedBooking['start_time'])
                ? date('d M Y', strtotime($unratedBooking['start_time']))
                : htmlspecialchars($unratedBooking['tanggal'] ?? '-') ?>
            </span>
          </p>
          <p class="mb-2">
            Kamu belum bisa melakukan booking baru sebelum memberi rating pada peminjaman ini.
          </p>
          <button
            type="button"
            class="open-rating-modal-home inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-yellow-500 text-white text-xs font-semibold hover:bg-yellow-600"
            data-id="<?= (int)($unratedBooking['id_bookings'] ?? 0) ?>">
            ⭐ Beri Rating Sekarang
          </button>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <?php
  // SECTION PROFIL + TOMBOL (pakai partial phome)
  $phomePath = __DIR__ . '/../layout/phome.php';
  if (file_exists($phomePath)) {
    include $phomePath;
  }

  // komponen kartu ruangan
  $roomCardPath = __DIR__ . '/../layout/roomCard.php';
  if (file_exists($roomCardPath)) {
    require $roomCardPath;
  }
  ?>

  <!-- DAFTAR RUANGAN -->
  <div class="w-full max-w-7xl mx-auto px-4 mb-10">
    <h2 class="text-2xl font-bold text-[#274269] mb-4">Daftar Ruangan</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php if (!empty($rooms)): ?>
        <?php renderRoomCards($rooms, $buttonDisabled); ?>
      <?php else: ?>
        <p class="text-gray-500">Belum ada ruangan yang aktif.</p>
      <?php endif; ?>
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
          <?php if (!empty($rooms)): ?>
            <?php foreach ($rooms as $room): ?>
              <option value="<?= htmlspecialchars($room['id_ruangan']) ?>">
                <?= htmlspecialchars($room['nama_ruangan']) ?>
                (<?= htmlspecialchars($room['kapasitas_min']) ?>–<?= htmlspecialchars($room['kapasitas_max']) ?> org)
              </option>
            <?php endforeach; ?>
          <?php endif; ?>
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
      <h2 class="text-xl font-bold mb-6">Masukkan Kode Kelompok</h2>
      <input id="kodeKelompokInput" type="text" placeholder="Masukkan kode"
        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300 text-gray-700 mb-6">
      <div class="flex justify-center gap-3">
        <button id="closeGabung" class="bg-[#274269] text-white px-5 py-2 rounded hover:bg-[#1f3555] transition">
          Tutup
        </button>
        <button id="btnGabungOk" class="bg-[#274269] text-white px-5 py-2 rounded hover:bg-[#1f3555] transition">
          Oke
        </button>
      </div>
    </div>
  </div>

  <!-- ⭐ MODAL RATING (HOME) -->
  <div id="ratingModalHome" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center">
    <div class="bg-white w-80 rounded-xl p-5 space-y-4">
      <h2 class="text-lg font-semibold">Beri Rating</h2>

      <form action="index.php?controller=userFeedback&action=submit" method="POST">
        <input type="hidden" name="id_booking" id="rating_booking_id_home">
        <input type="hidden" name="rating" id="rating_value_home">

        <!-- ⭐ STAR RATING (HOME) -->
        <div class="flex justify-center gap-1 text-3xl" id="starRatingHome">
          <button type="button" class="star-home text-gray-300 leading-none" data-value="1">★</button>
          <button type="button" class="star-home text-gray-300 leading-none" data-value="2">★</button>
          <button type="button" class="star-home text-gray-300 leading-none" data-value="3">★</button>
          <button type="button" class="star-home text-gray-300 leading-none" data-value="4">★</button>
          <button type="button" class="star-home text-gray-300 leading-none" data-value="5">★</button>
        </div>

        <textarea
          name="komentar"
          rows="3"
          placeholder="Komentar (opsional)"
          class="w-full border rounded-lg p-2 text-sm mt-3"></textarea>

        <div class="flex justify-end gap-2 mt-3">
          <button type="button" id="ratingCloseHome" class="text-sm text-slate-600">Batal</button>
          <button type="submit"
            class="px-3 py-1 bg-blue-600 text-white text-sm rounded-lg">
            Kirim
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- === SCRIPT: Logika Modal + Redirect + Rating === -->
  <script>
    const btnPilih = document.getElementById('btnPilih');
    const btnGabung = document.getElementById('btnGabung');
    const modalPilih = document.getElementById('modalPilih');
    const modalGabung = document.getElementById('modalGabung');
    const closePilih = document.getElementById('closePilih');
    const closeGabung = document.getElementById('closeGabung');
    const selectRuangan = document.getElementById('selectRuangan');
    const btnGabungOk = document.getElementById('btnGabungOk');
    const kodeInput = document.getElementById('kodeKelompokInput');

    if (btnPilih) {
      btnPilih.addEventListener('click', () => {
        if (btnPilih.disabled) return;
        modalPilih.classList.remove('hidden');
      });
    }
    if (btnGabung) {
      btnGabung.addEventListener('click', () => {
        if (btnGabung.disabled) return;
        modalGabung.classList.remove('hidden');
      });
    }

    if (closePilih) closePilih.addEventListener('click', () => modalPilih.classList.add('hidden'));
    if (closeGabung) closeGabung.addEventListener('click', () => modalGabung.classList.add('hidden'));

    [modalPilih, modalGabung].forEach(modal => {
      if (!modal) return;
      modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.classList.add('hidden');
      });
    });

    // redirect ke halaman detail/booking ruangan
    if (selectRuangan) {
      selectRuangan.addEventListener('change', (e) => {
        const value = e.target.value;
        if (value) {
          window.location.href =
            "index.php?controller=userBooking&action=booking&id=" + encodeURIComponent(value);
        }
      });
    }

    // gabung kelompok: redirect GET ke joinGroup dengan kode_kelompok
    if (btnGabungOk && kodeInput) {
      btnGabungOk.addEventListener('click', () => {
        const kode = kodeInput.value.trim();
        if (!kode) {
          alert('Kode kelompok harus diisi');
          return;
        }
        window.location.href =
          "index.php?controller=userBooking&action=joinGroup&kode_kelompok=" + encodeURIComponent(kode);
      });
    }

    // ===== ⭐ RATING MODAL HOME (BINTANG NYALA SESUAI KLIK) =====
    const ratingModalHome = document.getElementById('ratingModalHome');
    const ratingCloseHome = document.getElementById('ratingCloseHome');
    const ratingBookingIdHome = document.getElementById('rating_booking_id_home');
    const ratingValueHome = document.getElementById('rating_value_home');

    const starWrapHome = document.getElementById('starRatingHome');
    const starsHome = starWrapHome ? starWrapHome.querySelectorAll('.star-home') : [];

    function setStarsHome(rating) {
      const r = parseInt(rating || 0, 10);
      starsHome.forEach(star => {
        const v = parseInt(star.dataset.value || '0', 10);
        if (v <= r) {
          star.classList.remove('text-gray-300');
          star.classList.add('text-yellow-400');
        } else {
          star.classList.remove('text-yellow-400');
          star.classList.add('text-gray-300');
        }
      });
    }

    // buka modal dari tombol di alert
    document.querySelectorAll('.open-rating-modal-home').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        if (!id) return;

        ratingBookingIdHome.value = id;
        ratingValueHome.value = ''; // reset rating
        setStarsHome(0);

        ratingModalHome.classList.remove('hidden');
      });
    });

    // close
    if (ratingCloseHome) {
      ratingCloseHome.addEventListener('click', () => {
        ratingModalHome.classList.add('hidden');
      });
    }

    // klik backdrop = close
    if (ratingModalHome) {
      ratingModalHome.addEventListener('click', (e) => {
        if (e.target === ratingModalHome) {
          ratingModalHome.classList.add('hidden');
        }
      });
    }

    // hover preview + klik set rating
    starsHome.forEach(star => {
      star.addEventListener('mouseenter', () => {
        setStarsHome(star.dataset.value);
      });

      star.addEventListener('click', () => {
        ratingValueHome.value = star.dataset.value; // klik 3 => value 3
        setStarsHome(star.dataset.value); // nyala 3 bintang
      });
    });

    // mouse keluar => balik ke rating terakhir
    if (starWrapHome) {
      starWrapHome.addEventListener('mouseleave', () => {
        setStarsHome(ratingValueHome.value || 0);
      });
    }
  </script>


  <?php
  $footerPath = __DIR__ . '/../layout/footer.php';
  if (file_exists($footerPath)) {
    require $footerPath;
  }
  ?>
</body>

</html>