<?php

/** @var array $rooms */
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Buat Booking Eksternal | Kubooking</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#f2f7fc] text-gray-800 flex">

    <!-- SIDEBAR -->
    <?php
    $sidebarPath = __DIR__ . '/../layout/sidebar.php';
    if (file_exists($sidebarPath)) {
        include $sidebarPath;
    }
    ?>

    <!-- KONTEN -->
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

        <div class="px-4 sm:px-8 pb-10 space-y-6 max-w-6xl mx-auto w-full">

            <!-- HEADER -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-2">
                <div>
                    <h1 class="text-2xl font-bold text-[#1e3a5f]">Buat Booking Eksternal</h1>
                    <p class="text-xs text-slate-500 mt-1">
                        Untuk peminjaman oleh tamu / instansi luar kampus.
                    </p>
                </div>

                <a href="index.php?controller=adminBooking&action=manage"
                    class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium 
                          border border-slate-300 text-slate-600 bg-white hover:bg-slate-50 shadow-sm">
                    Kembali ke daftar booking
                </a>
            </div>

            <!-- FORM -->
            <form action="index.php?controller=adminBooking&action=createExternal"
                method="POST"
                enctype="multipart/form-data"
                class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

                <!-- KIRI: Informasi Ruangan & Jadwal -->
                <div class="lg:col-span-2 space-y-4">

                    <!-- Ruangan & Jadwal -->
                    <div class="bg-white rounded-3xl shadow-md p-6 space-y-4 border border-slate-100">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <h2 class="text-lg font-semibold text-slate-900">
                                Informasi Ruangan & Jadwal
                            </h2>
                        </div>
                        <p class="text-[11px] text-slate-500 mb-3">
                            Pilih ruangan dan tanggal terlebih dahulu. Jam & durasi diatur manual oleh admin.
                        </p>

                        <!-- Ruangan + Tanggal -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Ruangan -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Pilih Ruangan <span class="text-red-500">*</span>
                                </label>
                                <select name="id_ruangan" id="id_ruangan"
                                    class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm
                                           bg-white shadow-sm
                                           focus:outline-none focus:ring-2 focus:ring-[#1e3a5f] focus:border-transparent"
                                    required>
                                    <option value="">-- Pilih Ruangan --</option>
                                    <?php if (!empty($rooms)): ?>
                                        <?php foreach ($rooms as $r): ?>
                                            <option
                                                value="<?= (int)$r['id_ruangan']; ?>"
                                                data-kap-min="<?= (int)$r['kapasitas_min']; ?>"
                                                data-kap-max="<?= (int)$r['kapasitas_max']; ?>">
                                                <?= htmlspecialchars($r['nama_ruangan']); ?>
                                                (<?= (int)$r['kapasitas_min']; ?>–<?= (int)$r['kapasitas_max']; ?> org)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Tanggal -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Tanggal Peminjaman <span class="text-red-500">*</span>
                                </label>
                                <?php $today = date('Y-m-d'); ?>
                                <input type="date"
                                    name="tanggal"
                                    id="tanggal"
                                    min="<?= $today; ?>"
                                    class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm
                                              bg-white shadow-sm
                                              focus:outline-none focus:ring-2 focus:ring-[#1e3a5f] focus:border-transparent"
                                    required>
                            </div>
                        </div>

                        <!-- Jam & Durasi & Kapasitas -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
                            <!-- Jam Mulai -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Jam Mulai <span class="text-red-500">*</span>
                                </label>
                                <select name="jam_mulai" id="jam_mulai"
                                    class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm
                                           bg-slate-50 shadow-sm
                                           focus:outline-none focus:ring-2 focus:ring-[#1e3a5f] focus:border-transparent
                                           disabled:bg-slate-100 disabled:text-slate-400"
                                    disabled
                                    required>
                                    <option value="">-- Pilih Jam --</option>
                                </select>
                            </div>

                            <!-- Durasi -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Durasi (jam, max 3) <span class="text-red-500">*</span>
                                </label>
                                <select name="durasi" id="durasi"
                                    class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm
                                           bg-slate-50 shadow-sm
                                           focus:outline-none focus:ring-2 focus:ring-[#1e3a5f] focus:border-transparent
                                           disabled:bg-slate-100 disabled:text-slate-400"
                                    disabled
                                    required>
                                    <option value="">-- Pilih Durasi --</option>
                                </select>
                            </div>

                            <!-- Kapasitas info -->
                            <div class="bg-slate-50 border border-dashed border-slate-200 rounded-2xl px-3 py-2.5">
                                <p class="text-[11px] font-semibold text-slate-700 mb-1">
                                    Kapasitas Ruangan
                                </p>
                                <p class="text-xs text-slate-700">
                                    Min: <span id="kap_min_label">-</span> org<br>
                                    Maks: <span id="kap_max_label">-</span> org
                                </p>
                            </div>
                        </div>

                        <!-- Keperluan -->
                        <div class="mt-4">
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Keperluan Peminjaman
                            </label>
                            <textarea
                                name="keperluan"
                                rows="3"
                                class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm
                                       bg-white shadow-sm
                                       focus:outline-none focus:ring-2 focus:ring-[#1e3a5f] focus:border-transparent"
                                placeholder="Contoh: Seminar, rapat, pelatihan, dsb."></textarea>
                        </div>
                    </div>

                    <!-- Info Simpan -->
                    <div class="bg-white rounded-3xl shadow p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border border-slate-100">
                        <p class="text-[11px] text-slate-500 max-w-md">
                            Pastikan data ruangan, jadwal, dan data peminjam eksternal sudah benar sebelum menyimpan.
                            Status awal booking eksternal: <span class="font-semibold">pending</span>.
                            Admin dapat mengubah status dari halaman daftar booking.
                        </p>

                        <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold
                                       hover:bg-emerald-700 disabled:bg-slate-300 disabled:text-slate-500 shadow-sm"
                            id="btn_submit">
                            Simpan Booking
                        </button>
                    </div>
                </div>

                <!-- KANAN: Data Peminjam Eksternal -->
                <div class="space-y-4">
                    <div class="bg-white rounded-3xl shadow-md p-6 space-y-4 border border-slate-100">
                        <div class="flex items-center justify-between mb-1">
                            <h2 class="text-lg font-semibold text-slate-900">
                                Data Peminjam Eksternal
                            </h2>
                        </div>
                        <p class="text-xs text-slate-500">
                            Isi identitas peminjam dari instansi luar kampus.
                        </p>

                        <!-- Nama -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Nama Peminjam <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                name="nama_peminjam"
                                class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm
                                          bg-white shadow-sm
                                          focus:outline-none focus:ring-2 focus:ring-[#1e3a5f] focus:border-transparent"
                                placeholder="Nama lengkap"
                                required>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email"
                                name="email_peminjam"
                                class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm
                                          bg-white shadow-sm
                                          focus:outline-none focus:ring-2 focus:ring-[#1e3a5f] focus:border-transparent"
                                placeholder="email@example.com"
                                required>
                        </div>

                        <!-- Nomor HP -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Nomor HP / WA <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                name="no_hp"
                                class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm
                                          bg-white shadow-sm
                                          focus:outline-none focus:ring-2 focus:ring-[#1e3a5f] focus:border-transparent"
                                placeholder="08xxxxxxxxxx"
                                required>
                        </div>

                        <!-- Jumlah Anggota -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Jumlah Anggota / Peserta <span class="text-red-500">*</span>
                            </label>
                            <input type="number"
                                min="1"
                                name="jumlah_anggota"
                                id="jumlah_anggota"
                                class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm
                                          bg-white shadow-sm
                                          focus:outline-none focus:ring-2 focus:ring-[#1e3a5f] focus:border-transparent"
                                placeholder="Misal: 20"
                                required>
                            <p class="text-[11px] text-slate-500 mt-1">
                                Harus berada di antara kapasitas minimum dan maksimum ruangan.
                            </p>
                        </div>

                        <!-- Asal Instansi -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Asal Instansi <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                name="asal_instansi"
                                class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm
                                          bg-white shadow-sm
                                          focus:outline-none focus:ring-2 focus:ring-[#1e3a5f] focus:border-transparent"
                                placeholder="Nama instansi / lembaga"
                                required>
                        </div>

                        <!-- Surat Izin (opsional) -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Surat Izin (opsional, PDF/JPG/PNG, max 5MB)
                            </label>
                            <input type="file"
                                name="surat_izin"
                                accept=".pdf,.jpg,.jpeg,.png"
                                class="w-full text-xs text-slate-600">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        const selectRuangan = document.getElementById('id_ruangan');
        const inputTanggal = document.getElementById('tanggal');
        const selectJamMulai = document.getElementById('jam_mulai');
        const selectDurasi = document.getElementById('durasi');
        const kapMinLabel = document.getElementById('kap_min_label');
        const kapMaxLabel = document.getElementById('kap_max_label');
        const jumlahAnggotaEl = document.getElementById('jumlah_anggota');
        const btnSubmit = document.getElementById('btn_submit');

        // Slot jam statis untuk admin eksternal (silakan sesuaikan)
        const BASE_TIMES = [
            '08:00',
            '09:00',
            '10:00',
            '11:00',
            '13:00',
            '14:00',
            '15:00'
        ];

        function getSelectedKapMin() {
            const opt = selectRuangan.options[selectRuangan.selectedIndex];
            if (!opt || !opt.dataset.kapMin) return 0;
            return parseInt(opt.dataset.kapMin, 10) || 0;
        }

        function getSelectedKapMax() {
            const opt = selectRuangan.options[selectRuangan.selectedIndex];
            if (!opt || !opt.dataset.kapMax) return 0;
            return parseInt(opt.dataset.kapMax, 10) || 0;
        }

        function refreshKapLabel() {
            const kapMin = getSelectedKapMin() || '-';
            const kapMax = getSelectedKapMax() || '-';
            kapMinLabel.textContent = kapMin;
            kapMaxLabel.textContent = kapMax;
        }

        function resetSlots() {
            selectJamMulai.innerHTML = '<option value="">-- Pilih Jam --</option>';
            selectDurasi.innerHTML = '<option value="">-- Pilih Durasi --</option>';
            selectJamMulai.disabled = true;
            selectDurasi.disabled = true;
        }

        function loadSlotsSimple() {
            const roomId = selectRuangan.value;
            const tanggal = inputTanggal.value;

            if (!roomId || !tanggal) {
                resetSlots();
                return;
            }

            refreshKapLabel();
            resetSlots();

            // isi jam mulai dari BASE_TIMES
            selectJamMulai.innerHTML = '<option value="">-- Pilih Jam --</option>';
            BASE_TIMES.forEach(time => {
                const opt = document.createElement('option');
                opt.value = time;
                opt.textContent = time;
                selectJamMulai.appendChild(opt);
            });

            selectJamMulai.disabled = false;
        }

        // event ketika pilih ruangan / tanggal
        selectRuangan.addEventListener('change', () => {
            loadSlotsSimple();
        });

        inputTanggal.addEventListener('change', () => {
            loadSlotsSimple();
        });

        // ketika pilih jam mulai → isi durasi 1–3 jam
        selectJamMulai.addEventListener('change', () => {
            const t = selectJamMulai.value;

            selectDurasi.innerHTML = '<option value="">-- Pilih Durasi --</option>';

            if (!t) {
                selectDurasi.disabled = true;
                return;
            }

            for (let d = 1; d <= 3; d++) {
                const opt = document.createElement('option');
                opt.value = d;
                opt.textContent = d + ' jam';
                selectDurasi.appendChild(opt);
            }

            selectDurasi.disabled = false;
        });

        // VALIDASI sebelum submit (kapMin <= jumlah <= kapMax, durasi max 3)
        btnSubmit.addEventListener('click', (e) => {
            const roomId = selectRuangan.value;
            const tanggal = inputTanggal.value;
            const jam = selectJamMulai.value;
            const durasi = parseInt(selectDurasi.value || '0', 10);
            const jumlah = parseInt(jumlahAnggotaEl.value || '0', 10);
            const kapMin = getSelectedKapMin();
            const kapMax = getSelectedKapMax();

            if (!roomId || !tanggal || !jam || !durasi) {
                e.preventDefault();
                alert('Ruangan, tanggal, jam mulai, dan durasi wajib diisi.');
                return;
            }

            if (durasi < 1 || durasi > 3) {
                e.preventDefault();
                alert('Durasi harus antara 1 sampai 3 jam.');
                return;
            }

            if (!jumlah || jumlah < 1) {
                e.preventDefault();
                alert('Jumlah anggota minimal 1.');
                return;
            }

            if (kapMin > 0 && jumlah < kapMin) {
                e.preventDefault();
                alert('Jumlah anggota kurang dari kapasitas minimum ruangan (' + kapMin + ').');
                return;
            }

            if (kapMax > 0 && jumlah > kapMax) {
                e.preventDefault();
                alert('Jumlah anggota melebihi kapasitas maksimum ruangan (' + kapMax + ').');
                return;
            }
        });
    </script>
</body>

</html>