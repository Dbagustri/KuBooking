<?php

/** @var array $booking */
/** @var array $rooms */
/** @var array $members */

$idBooking      = (int)($booking['id_bookings'] ?? 0);
$selectedRoomId = (int)$booking['id_ruangan'];

// tanggal
$tanggal = !empty($booking['tanggal'])
    ? date('Y-m-d', strtotime($booking['tanggal']))
    : date('Y-m-d');

// hitung durasi
$startTime = !empty($booking['start_time']) ? date('H:i', strtotime($booking['start_time'])) : '08:00';
$endTime   = !empty($booking['end_time']) ? date('H:i', strtotime($booking['end_time'])) : '09:00';
$diffSeconds = strtotime($endTime) - strtotime($startTime);
$durasiJam   = max(1, (int)round($diffSeconds / 3600));

// keperluan
$keperluan = $booking['keperluan'] ?? '';

// jam opsi
$jamOptions = [
    '08:00',
    '09:00',
    '10:00',
    '11:00',
    '12:00',
    '13:00',
    '14:00',
    '15:00',
    '16:00',
    '17:00',
];

// mapping members awal untuk JS
$membersJs = [];
if (!empty($members)) {
    foreach ($members as $m) {
        $membersJs[] = [
            'id'     => (int)$m['id_user'],
            'nama'   => $m['nama'] ?? '',
            'nim_nip' => $m['nim_nip'] ?? '',
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Booking | Kubooking</title>
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
                    <h1 class="text-2xl font-bold text-[#1e3a5f]">
                        Edit Booking
                    </h1>
                </div>


                <a href="index.php?controller=adminBooking&action=manage"
                    class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium 
                          border border-slate-300 text-slate-600 bg-white hover:bg-slate-50 shadow-sm">
                    Kembali
                </a>
            </div>

            <!-- FORM -->
            <form action="index.php?controller=adminBooking&action=edit&id=<?= $idBooking; ?>"
                method="POST"
                class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

                <!-- KIRI: Informasi Ruangan & Jadwal -->
                <div class="lg:col-span-2 space-y-4">

                    <div class="bg-white rounded-3xl shadow-md p-6 space-y-4 border border-slate-100">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <h2 class="text-lg font-semibold text-slate-900">
                                Informasi Ruangan & Jadwal
                            </h2>
                        </div>
                        <p class="text-[11px] text-slate-500 mb-3">
                            Ubah ruangan, tanggal, jam, atau durasi. Backend tetap cek bentrok saat simpan.
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
                                            <?php
                                            $rid      = (int)$r['id_ruangan'];
                                            $kapMin   = (int)($r['kapasitas_min'] ?? 0);
                                            $kapMax   = (int)($r['kapasitas_max'] ?? 0);
                                            $selected = $rid === $selectedRoomId ? 'selected' : '';
                                            ?>
                                            <option
                                                value="<?= $rid; ?>"
                                                data-kap-min="<?= $kapMin; ?>"
                                                data-kap-max="<?= $kapMax; ?>"
                                                <?= $selected; ?>>
                                                <?= htmlspecialchars($r['nama_ruangan']); ?>
                                                (<?= $kapMin; ?>–<?= $kapMax; ?> org)
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
                                <input type="date"
                                    name="tanggal"
                                    id="tanggal"
                                    value="<?= htmlspecialchars($tanggal); ?>"
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
                                               bg-white shadow-sm
                                               focus:outline-none focus:ring-2 focus:ring-[#1e3a5f] focus:border-transparent"
                                    required>
                                    <option value="">-- Pilih Jam --</option>
                                    <?php foreach ($jamOptions as $jam): ?>
                                        <option value="<?= $jam; ?>" <?= ($jam === $startTime) ? 'selected' : ''; ?>>
                                            <?= $jam; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Durasi -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Durasi (jam) <span class="text-red-500">*</span>
                                </label>
                                <select name="durasi" id="durasi"
                                    class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm
                                               bg-white shadow-sm
                                               focus:outline-none focus:ring-2 focus:ring-[#1e3a5f] focus:border-transparent"
                                    required>
                                    <option value="">-- Pilih Durasi --</option>
                                    <?php for ($d = 1; $d <= 3; $d++): ?>
                                        <option value="<?= $d; ?>" <?= ($d === $durasiJam) ? 'selected' : ''; ?>>
                                            <?= $d; ?> jam
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <!-- Kapasitas & Jumlah Anggota -->
                            <div class="space-y-2">
                                <div class="bg-slate-50 border border-dashed border-slate-200 rounded-2xl px-3 py-2">
                                    <p class="text-[11px] font-semibold text-slate-700 mb-1">
                                        Kapasitas Ruangan
                                    </p>
                                    <p class="text-xs text-slate-700">
                                        Min: <span id="kap_min_label">-</span> org<br>
                                        Maks: <span id="kap_max_label">-</span> org
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">
                                        Jumlah Anggota (otomatis)
                                    </label>
                                    <input type="text"
                                        id="jumlah_anggota_display"
                                        value="<?= count($membersJs); ?>"
                                        class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm
                                                  bg-slate-50 text-slate-600"
                                        readonly>
                                    <input type="hidden"
                                        name="jumlah_anggota"
                                        id="jumlah_anggota"
                                        value="<?= max(1, count($membersJs)); ?>">
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Keperluan Peminjaman
                            </label>
                            <textarea name="keperluan"
                                rows="3"
                                class="w-full border border-slate-300 rounded-2xl px-3 py-2 text-sm
                                             bg-white shadow-sm
                                             focus:outline-none focus:ring-2 focus:ring-[#1e3a5f] focus:border-transparent"
                                placeholder="Contoh: Rapat himpunan, diskusi kelas, pelatihan, dll."><?= htmlspecialchars($keperluan); ?></textarea>
                        </div>
                    </div>

                    <!-- Keperluan & Tombol Simpan -->
                    <div class="bg-white rounded-3xl shadow-md p-6 border border-slate-100 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-t pt-3">
                            <p class="text-[11px] text-slate-500 max-w-md">
                                Perubahan akan langsung tersimpan tanpa approval ulang. Pastikan jadwal dan anggota sudah benar.
                            </p>

                            <button type="submit"
                                id="btn_submit"
                                class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold
                                           hover:bg-emerald-700 shadow-sm">
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>

                <!-- KANAN: Anggota -->
                <div class="space-y-4">

                    <div class="bg-white rounded-3xl shadow-md p-6 space-y-4 border border-slate-100">
                        <div class="flex items-center justify-between mb-1">
                            <h2 class="text-lg font-semibold text-slate-900">
                                Anggota Peminjam
                            </h2>
                        </div>
                        <p class="text-xs text-slate-500">
                            Tambah/hapus anggota booking. Anggota pertama diasumsikan sebagai Penanggung Jawab (PJ).
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
                                Penanggung jawab sebaiknya tetap orang yang sama, kecuali ada perubahan khusus.
                            </p>
                        </div>

                        <!-- Daftar Anggota -->
                        <div class="mt-3 border-t pt-3">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-xs font-semibold text-slate-700">
                                    Daftar Anggota (<span id="jumlah_anggota_label"><?= count($membersJs); ?></span>)
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
                                                Memuat data anggota...
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
        // --- DATA AWAL DARI PHP ---
        let members = <?= json_encode($membersJs, JSON_UNESCAPED_UNICODE); ?> || [];

        const selectRuangan = document.getElementById('id_ruangan');
        const kapMinLabel = document.getElementById('kap_min_label');
        const kapMaxLabel = document.getElementById('kap_max_label');
        const nimInput = document.getElementById('nim_input');
        const btnAddMember = document.getElementById('btn_add_member');
        const membersTable = document.getElementById('membersTableBody');
        const hiddenMembers = document.getElementById('hiddenMembers');
        const jumlahLabel = document.getElementById('jumlah_anggota_label');
        const jumlahHidden = document.getElementById('jumlah_anggota');
        const jumlahDisplay = document.getElementById('jumlah_anggota_display');
        const btnSubmit = document.getElementById('btn_submit');

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
            const min = getSelectedKapMin() || '-';
            const max = getSelectedKapMax() || '-';
            kapMinLabel.textContent = min;
            kapMaxLabel.textContent = max;
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

            const count = members.length || 0;
            jumlahLabel.textContent = count.toString();
            jumlahHidden.value = count > 0 ? count : 1;
            jumlahDisplay.value = count.toString();
        }

        window.removeMember = function(id) {
            members = members.filter(m => m.id !== id);
            renderMembers();
        };

        function encodeFormData(obj) {
            return Object.keys(obj)
                .map(k => encodeURIComponent(k) + '=' + encodeURIComponent(obj[k]))
                .join('&');
        }

        // Tambah anggota via NIM
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

            // cek duplikasi
            if (members.some(m => m.nim_nip === nim)) {
                alert('NIM ini sudah ada dalam daftar anggota.');
                return;
            }

            const body = encodeFormData({
                nim
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

        // Validasi sebelum submit
        btnSubmit.addEventListener('click', (e) => {
            if (members.length === 0) {
                e.preventDefault();
                alert('Minimal 1 anggota harus ada pada booking.');
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
                    return;
                }
            }
        });
        refreshKapLabel();
        renderMembers();
        selectRuangan.addEventListener('change', refreshKapLabel);
    </script>
</body>

</html>