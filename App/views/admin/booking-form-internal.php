<?php

/** @var array $rooms */
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Buat Booking Internal | Kubooking</title>
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

        <div class="px-4 sm:px-8 pb-10 space-y-6 max-w-6xl mx-auto w-full">

            <!-- HEADER -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-2">
                <div>
                    <h1 class="text-2xl font-bold text-[#1e3a5f]">Buat Booking Internal</h1>
                </div>

                <a href="index.php?controller=adminBooking&action=manage"
                    class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium 
                          border border-slate-300 text-slate-600 bg-white hover:bg-slate-50 shadow-sm">
                    Kembali ke daftar booking
                </a>
            </div>

            <!-- FORM -->
            <form action="index.php?controller=adminBooking&action=createInternal"
                method="POST"
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
                            Pilih ruangan dan tanggal terlebih dahulu untuk melihat slot waktu yang tersedia.
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
                                <?php
                                $today = date('Y-m-d');
                                ?>
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
                                    Durasi (jam) <span class="text-red-500">*</span>
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
                    </div>

                    <!-- Info Simpan -->
                    <div class="bg-white rounded-3xl shadow p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border border-slate-100">
                        <p class="text-[11px] text-slate-500 max-w-md">
                            Pastikan data ruangan, jadwal, dan anggota sudah benar sebelum menyimpan.
                            Anda masih dapat mengubah status booking dari halaman daftar booking.
                        </p>

                        <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold
                                       hover:bg-emerald-700 disabled:bg-slate-300 disabled:text-slate-500 shadow-sm"
                            id="btn_submit">
                            Simpan Booking
                        </button>
                    </div>
                </div>

                <!-- KANAN: Anggota -->
                <div class="space-y-4">

                    <!-- Tambah Anggota -->
                    <div class="bg-white rounded-3xl shadow-md p-6 space-y-4 border border-slate-100">
                        <div class="flex items-center justify-between mb-1">
                            <h2 class="text-lg font-semibold text-slate-900">
                                Anggota Peminjam
                            </h2>
                        </div>
                        <p class="text-xs text-slate-500">
                            Masukkan NIM anggota satu per satu.
                        </p>

                        <!-- Input NIM -->
                        <div class="flex flex-col gap-2">
                            <div class="flex gap-2">
                                <input type="text"
                                    id="nim_input"
                                    class="flex-1 border border-slate-300 rounded-xl px-3 py-2.5 text-sm
                                              bg-white shadow-sm
                                              focus:outline-none focus:ring-2 focus:ring-[#1e3a5f] focus:border-transparent"
                                    placeholder="Masukkan NIM anggota">
                                <button type="button"
                                    id="btn_add_member"
                                    class="px-4 py-2 rounded-xl bg-[#1e3a5f] text-white text-sm font-semibold
                                               hover:bg-[#163152] disabled:bg-slate-300 disabled:text-slate-500 shadow-sm">
                                    Tambah
                                </button>
                            </div>
                            <p class="text-[11px] text-slate-500">
                                Anggota pertama otomatis menjadi <span class="font-semibold">Penanggung Jawab (PJ)</span>.
                            </p>
                        </div>

                        <!-- Daftar Anggota -->
                        <div class="mt-3 border-t pt-3">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-xs font-semibold text-slate-700">
                                    Daftar Anggota (<span id="jumlah_anggota_label">0</span>)
                                </p>

                            </div>

                            <div class="max-h-60 overflow-y-auto border border-slate-100 rounded-2xl">
                                <table class="w-full text-xs">
                                    <thead class="bg-slate-50 border-b border-slate-200">
                                        <tr>
                                            <th class="px-3 py-2 text-left">Nama</th>
                                            <th class="px-3 py-2 text-left">NIM</th>
                                            <th class="px-3 py-2 text-center">Peran</th>
                                            <th class="px-3 py-2 text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="membersTableBody">
                                        <tr class="empty-row">
                                            <td colspan="4" class="px-3 py-4 text-center text-slate-400 text-[11px]">
                                                Belum ada anggota. Tambahkan minimal 1 anggota.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Hidden inputs untuk members[] -->
                        <div id="hiddenMembers"></div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        let slotMaxDur = {};
        let members = [];

        const selectRuangan = document.getElementById('id_ruangan');
        const inputTanggal = document.getElementById('tanggal');
        const selectJamMulai = document.getElementById('jam_mulai');
        const selectDurasi = document.getElementById('durasi');
        const kapMinLabel = document.getElementById('kap_min_label');
        const kapMaxLabel = document.getElementById('kap_max_label');
        const nimInput = document.getElementById('nim_input');
        const btnAddMember = document.getElementById('btn_add_member');
        const membersTable = document.getElementById('membersTableBody');
        const hiddenMembers = document.getElementById('hiddenMembers');
        const jumlahLabel = document.getElementById('jumlah_anggota_label');
        const btnSubmit = document.getElementById('btn_submit');

        // ---------- UTIL ----------
        function encodeFormData(obj) {
            return Object.keys(obj)
                .map(k => encodeURIComponent(k) + '=' + encodeURIComponent(obj[k]))
                .join('&');
        }

        function resetSlots() {
            slotMaxDur = {};
            selectJamMulai.innerHTML = '<option value="">-- Pilih Jam --</option>';
            selectDurasi.innerHTML = '<option value="">-- Pilih Durasi --</option>';
            selectJamMulai.disabled = true;
            selectDurasi.disabled = true;
        }

        function getSelectedKapMax() {
            const opt = selectRuangan.options[selectRuangan.selectedIndex];
            if (!opt || !opt.dataset.kapMax) return 0;
            return parseInt(opt.dataset.kapMax, 10) || 0;
        }

        function getSelectedKapMin() {
            const opt = selectRuangan.options[selectRuangan.selectedIndex];
            if (!opt || !opt.dataset.kapMin) return 0;
            return parseInt(opt.dataset.kapMin, 10) || 0;
        }

        function refreshKapLabel() {
            const kapMin = getSelectedKapMin() || '-';
            const kapMax = getSelectedKapMax() || '-';
            kapMinLabel.textContent = kapMin;
            kapMaxLabel.textContent = kapMax;
        }

        function renderMembers() {
            membersTable.innerHTML = '';
            hiddenMembers.innerHTML = '';

            if (members.length === 0) {
                const tr = document.createElement('tr');
                tr.className = 'empty-row';
                tr.innerHTML = `
                    <td colspan="4" class="px-3 py-4 text-center text-slate-400 text-[11px]">
                        Belum ada anggota. Tambahkan minimal 1 anggota.
                    </td>`;
                membersTable.appendChild(tr);
            } else {
                members.forEach((m, idx) => {
                    const tr = document.createElement('tr');
                    tr.className = idx % 2 === 0 ? 'bg-slate-50' : 'bg-white';
                    tr.innerHTML = `
                        <td class="px-3 py-2 text-[13px] text-slate-800">${m.nama}</td>
                        <td class="px-3 py-2 text-[13px] text-slate-700">${m.nim_nip}</td>
                        <td class="px-3 py-2 text-center">
                            ${idx === 0
                                ? '<span class="inline-flex px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-semibold">PJ</span>'
                                : '<span class="text-[11px] text-slate-500">Anggota</span>'
                            }
                        </td>
                        <td class="px-3 py-2 text-center">
                            <button type="button"
                                    class="text-[11px] text-red-600 hover:text-red-700"
                                    onclick="removeMember(${m.id})">
                                Hapus
                            </button>
                        </td>
                    `;
                    membersTable.appendChild(tr);

                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'members[]';
                    input.value = m.id;
                    hiddenMembers.appendChild(input);
                });
            }

            jumlahLabel.textContent = members.length.toString();
        }

        window.removeMember = function(id) {
            members = members.filter(m => m.id !== id);
            renderMembers();
        };

        // ---------- LOAD SLOT (JAM MULAI KELIPATAN 1 JAM) ----------
        function loadSlotsIfReady() {
            const roomId = selectRuangan.value;
            const tanggal = inputTanggal.value;

            if (!roomId || !tanggal) {
                resetSlots();
                return;
            }

            refreshKapLabel();
            resetSlots();

            const body = encodeFormData({
                id_ruangan: roomId,
                tanggal: tanggal
            });

            selectJamMulai.disabled = true;
            selectDurasi.disabled = true;

            fetch('index.php?controller=adminAjax&action=getAvailableSlotsInternal', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body
                })
                .then(res => res.json())
                .then(data => {
                    if (!data || !data.success) {
                        alert(data && data.message ? data.message : 'Gagal mengambil slot tersedia.');
                        return;
                    }

                    slotMaxDur = data.slots || {};

                    // Ambil key jam dan SORT, lalu hanya ambil yang menitnya ":00"
                    let keys = Object.keys(slotMaxDur).sort();
                    const hourOnlyKeys = keys.filter(time => {
                        // Format yang diharapkan: HH:MM atau HH:MM:SS
                        const parts = time.split(':');
                        if (parts.length < 2) return false;
                        return parts[1] === '00'; // hanya jam bulat
                    });

                    if (hourOnlyKeys.length > 0) {
                        keys = hourOnlyKeys;
                    }

                    if (keys.length === 0) {
                        selectJamMulai.innerHTML = '<option value="">Tidak ada slot tersedia</option>';
                        return;
                    }

                    selectJamMulai.innerHTML = '<option value="">-- Pilih Jam --</option>';
                    keys.forEach(time => {
                        const opt = document.createElement('option');
                        opt.value = time;
                        opt.textContent = time;
                        selectJamMulai.appendChild(opt);
                    });

                    selectJamMulai.disabled = true;
                    // Jam mulai baru bisa dipilih setelah ruangan & tanggal ok → sekarang enable
                    selectJamMulai.disabled = false;
                })
                .catch(() => {
                    alert('Terjadi kesalahan saat memuat slot.');
                });
        }

        selectRuangan.addEventListener('change', () => {
            refreshKapLabel();
            loadSlotsIfReady();
        });

        inputTanggal.addEventListener('change', () => {
            loadSlotsIfReady();
        });

        selectJamMulai.addEventListener('change', () => {
            const t = selectJamMulai.value;
            selectDurasi.innerHTML = '<option value="">-- Pilih Durasi --</option>';

            if (!t || !slotMaxDur[t]) {
                selectDurasi.disabled = true;
                return;
            }

            const maxDur = slotMaxDur[t];
            for (let d = 1; d <= maxDur; d++) {
                const opt = document.createElement('option');
                opt.value = d;
                opt.textContent = d + ' jam';
                selectDurasi.appendChild(opt);
            }

            selectDurasi.disabled = false;
        });

        // ---------- ADD MEMBER ----------
        btnAddMember.addEventListener('click', () => {
            const nim = nimInput.value.trim();
            if (!nim) {
                alert('NIM wajib diisi.');
                return;
            }

            const roomId = selectRuangan.value;
            if (!roomId) {
                alert('Pilih ruangan terlebih dahulu sebelum menambah anggota.');
                return;
            }

            const kapMax = getSelectedKapMax();
            if (kapMax > 0 && members.length >= kapMax) {
                alert('Jumlah anggota sudah mencapai kapasitas maksimum ruangan.');
                return;
            }

            // Cek duplikasi
            if (members.some(m => m.nim_nip === nim)) {
                alert('NIM ini sudah ada dalam daftar anggota.');
                return;
            }

            const body = encodeFormData({
                nim: nim
            });

            btnAddMember.disabled = true;

            fetch('index.php?controller=adminAjax&action=checkUserByNim', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body
                })
                .then(res => res.json())
                .then(data => {
                    if (!data || !data.success) {
                        alert(data && data.message ? data.message : 'NIM tidak valid atau tidak bisa digunakan.');
                        return;
                    }

                    const u = data.user;
                    members.push({
                        id: parseInt(u.id_account, 10),
                        nama: u.nama,
                        nim_nip: u.nim_nip
                    });

                    nimInput.value = '';
                    renderMembers();
                })
                .catch(() => {
                    alert('Terjadi kesalahan saat memeriksa NIM.');
                })
                .finally(() => {
                    btnAddMember.disabled = false;
                });
        });

        // ---------- VALIDASI SEBELUM SUBMIT ----------
        btnSubmit.addEventListener('click', (e) => {
            if (members.length === 0) {
                e.preventDefault();
                alert('Minimal 1 anggota harus ditambahkan.');
                return;
            }

            const kapMax = getSelectedKapMax();
            if (kapMax > 0 && members.length > kapMax) {
                e.preventDefault();
                alert('Jumlah anggota melebihi kapasitas maksimum ruangan.');
                return;
            }

            const kapMin = getSelectedKapMin();
            if (kapMin > 0 && members.length < kapMin) {
                if (!confirm('Jumlah anggota kurang dari kapasitas minimum ruangan. Lanjutkan?')) {
                    e.preventDefault();
                }
            }
        });
    </script>
</body>

</html>