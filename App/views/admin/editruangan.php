<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Ruangan | Kubooking</title>
    <link rel="stylesheet" href="/kubooking/public/src/output.css">
</head>

<body class="bg-[#f2f7fc] text-gray-800 flex min-h-screen">

    <?php
    // SIDEBAR
    $sidebarPath = __DIR__ . '/../layout/sidebar.php';
    if (file_exists($sidebarPath)) {
        include $sidebarPath;
    }
    ?>

    <!-- KONTEN UTAMA -->
    <div class="flex-1 flex flex-col h-screen overflow-y-auto">
        <?php
        $flashPath = __DIR__ . '/../layout/flash.php';
        if (file_exists($flashPath)) {
            include $flashPath;
        }

        $navPath = __DIR__ . '/../layout/nav-admin.php';

        // Normalisasi data ruangan
        $room  = $room ?? [];

        $idRuangan = (int)($room['id_ruangan'] ?? 0);
        $nama      = $room['nama_ruangan']       ?? '';
        $kategori  = $room['kategori']           ?? '';
        $lokasi    = $room['lokasi']             ?? '';
        $kapMin    = $room['kapasitas_min']      ?? '';
        $kapMax    = $room['kapasitas_max']      ?? '';
        $status    = $room['status_operasional'] ?? 'nonaktif';
        $foto      = $room['foto_ruangan']       ?? '';

        // Jadwal ruangan
        $schedule = isset($schedule) && is_array($schedule) ? $schedule : [];
        $facilities = isset($facilities) && is_array($facilities) ? $facilities : [];
        $selectedFacilities = isset($selectedFacilities) && is_array($selectedFacilities) ? $selectedFacilities : [];

        $isSelectedFasilitas = function ($id) use ($selectedFacilities) {
            return in_array($id, (array)$selectedFacilities, true);
        };
        ?>

        <!-- NAVBAR -->
        <div class="m-4">
            <?php if (file_exists($navPath)) include $navPath; ?>
        </div>

        <main class="px-4 md:px-8 pb-10">
            <div class="max-w-4xl mx-auto space-y-5">

                <!-- HEADER -->
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-blue-900/70 font-semibold">
                            Manajemen Ruangan
                        </p>
                        <h1 class="text-2xl md:text-3xl font-bold text-[#1e3a5f] mt-1">
                            Edit Ruangan
                        </h1>
                        <p class="text-sm text-gray-600 mt-1">
                            Perbarui informasi ruangan dan fasilitas yang tersedia di Kubooking.
                        </p>
                    </div>

                    <a href="index.php?controller=admin&action=ruangan"
                        class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium 
                          border border-slate-300 text-slate-600 bg-white hover:bg-slate-50 shadow-sm">
                        Kembali
                    </a>
                </div>

                <!-- CARD FORM EDIT -->
                <section class="bg-white rounded-2xl shadow-md border border-blue-50 p-6 md:p-7 space-y-6">

                    <!-- MINI INFO -->
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="space-y-1">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                Form Edit Ruangan
                            </div>
                            <p class="text-sm text-gray-600">
                                Pastikan data ruangan akurat agar proses booking berjalan lancar.
                            </p>
                            <p class="text-xs text-gray-500">
                                Perubahan kapasitas dan status operasional akan langsung berpengaruh pada proses booking.
                            </p>
                        </div>

                        <?php if (!empty($foto)): ?>
                            <div class="flex flex-col items-start md:items-end gap-2">
                                <p class="text-sm font-medium text-gray-700">Preview Foto Saat Ini</p>
                                <img src="<?= htmlspecialchars($foto, ENT_QUOTES, 'UTF-8') ?>"
                                    alt="Foto ruangan"
                                    class="w-full max-w-[220px] rounded-lg border shadow-sm object-cover">
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- FORM EDIT RUANGAN -->
                    <form action="index.php?controller=admin&action=updateRoom"
                        method="POST"
                        enctype="multipart/form-data"
                        class="space-y-6">

                        <input type="hidden" name="id_ruangan" value="<?= $idRuangan ?>">

                        <!-- DATA UTAMA -->
                        <div class="space-y-4">
                            <h2 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                                <span class="w-1 h-5 rounded-full bg-blue-500"></span>
                                Data Utama
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Nama Ruangan -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Nama Ruangan <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        name="nama_ruangan"
                                        value="<?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8') ?>"
                                        class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm px-3 py-2.5 shadow-sm"
                                        placeholder="Contoh: Ruang Diskusi A"
                                        required>
                                </div>

                                <!-- Lokasi -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Lokasi <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        name="lokasi"
                                        value="<?= htmlspecialchars($lokasi, ENT_QUOTES, 'UTF-8') ?>"
                                        class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm px-3 py-2.5 shadow-sm"
                                        placeholder="Contoh: Lantai 3, Gedung Perpustakaan"
                                        required>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Kategori -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Kategori
                                    </label>
                                    <input
                                        type="text"
                                        name="kategori"
                                        value="<?= htmlspecialchars($kategori, ENT_QUOTES, 'UTF-8') ?>"
                                        class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm px-3 py-2.5 shadow-sm"
                                        placeholder="Contoh: Diskusi, Rapat, Presentasi">
                                    <p class="text-xs text-gray-500 mt-1">
                                        Opsional, gunakan untuk mengelompokkan ruangan (misalnya: Diskusi, Rapat, Kelas).
                                    </p>
                                </div>

                                <!-- Status Operasional -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Status Operasional <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        name="status_operasional"
                                        class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm px-3 py-2.5 shadow-sm bg-white">
                                        <option value="aktif" <?= $status === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                        <option value="nonaktif" <?= $status === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Ruangan nonaktif tidak akan muncul di daftar peminjaman user.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- KAPASITAS & FASILITAS -->
                        <div class="space-y-4">
                            <h2 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                                <span class="w-1 h-5 rounded-full bg-blue-500"></span>
                                Kapasitas & Fasilitas
                            </h2>

                            <!-- Kapasitas -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Kapasitas Minimum -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Kapasitas Minimum <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        name="kapasitas_min"
                                        min="1"
                                        value="<?= htmlspecialchars((string)$kapMin, ENT_QUOTES, 'UTF-8') ?>"
                                        class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm px-3 py-2.5 shadow-sm"
                                        placeholder="Contoh: 2"
                                        required>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Minimal jumlah anggota agar booking ruangan ini dianggap valid.
                                    </p>
                                </div>

                                <!-- Kapasitas Maksimum -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Kapasitas Maksimum <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        name="kapasitas_max"
                                        min="1"
                                        value="<?= htmlspecialchars((string)$kapMax, ENT_QUOTES, 'UTF-8') ?>"
                                        class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm px-3 py-2.5 shadow-sm"
                                        placeholder="Contoh: 10"
                                        required>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Maksimal jumlah anggota yang diizinkan di ruangan ini.
                                    </p>
                                </div>
                            </div>

                            <!-- Fasilitas -->
                            <?php if (!empty($facilities)): ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">
                                        Fasilitas Ruangan
                                    </label>
                                    <p class="text-xs text-gray-500">
                                        Centang fasilitas yang tersedia. Informasi ini akan tampil di halaman detail ruangan.
                                    </p>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 mt-2">
                                        <?php foreach ($facilities as $f):
                                            $fid     = (int)$f['id_fasilitas'];
                                            $namaF   = $f['nama_fasilitas'] ?? 'Tanpa nama';
                                            $checked = $isSelectedFasilitas($fid) ? 'checked' : '';
                                        ?>
                                            <label class="flex items-start gap-3 p-3 rounded-xl border border-gray-200 bg-gray-50 hover:border-blue-300 hover:bg-blue-50 cursor-pointer transition">
                                                <input
                                                    type="checkbox"
                                                    name="fasilitas[]"
                                                    value="<?= $fid ?>"
                                                    class="mt-1 w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                    <?= $checked ?>>
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-gray-800">
                                                        <?= htmlspecialchars($namaF, ENT_QUOTES, 'UTF-8') ?>
                                                    </p>
                                                    <?php if (!empty($f['icon'])): ?>
                                                        <p class="text-xs text-gray-500 mt-0.5">
                                                            <?= htmlspecialchars($f['icon'], ENT_QUOTES, 'UTF-8') ?>
                                                        </p>
                                                    <?php endif; ?>
                                                </div>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="mt-2 text-xs text-gray-500 italic bg-gray-50 border border-dashed border-gray-200 rounded-lg px-3 py-2">
                                    Belum ada data fasilitas. Tambahkan data fasilitas terlebih dahulu di menu fasilitas.
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- FOTO RUANGAN -->
                        <div class="space-y-2">
                            <h2 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                                <span class="w-1 h-5 rounded-full bg-blue-500"></span>
                                Foto Ruangan
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Ubah Foto Ruangan
                                    </label>
                                    <input
                                        type="file"
                                        name="foto_ruangan"
                                        accept="image/*"
                                        class="block w-full text-sm text-gray-700
                                               file:mr-4 file:py-2 file:px-4
                                               file:rounded-lg file:border-0
                                               file:text-sm file:font-semibold
                                               file:bg-blue-50 file:text-blue-700
                                               hover:file:bg-blue-100">
                                    <p class="text-xs text-gray-500 mt-1">
                                        Biarkan kosong jika tidak ingin mengubah foto. Format disarankan: JPG/PNG/WEBP.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- TOMBOL AKSI -->
                        <div class="pt-4 border-t border-gray-100 flex flex-col md:flex-row md:justify-end gap-3">
                            <a href="index.php?controller=admin&action=ruangan"
                                class="inline-flex justify-center px-4 py-2 rounded-3xl bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-medium">
                                Batal
                            </a>
                            <button
                                type="submit"
                                class="inline-flex justify-center px-5 py-2 rounded-3xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-sm">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </section>

                <!-- CARD JADWAL (OPSIONAL) -->
                <?php if (!empty($schedule)): ?>
                    <section class="bg-white rounded-2xl shadow-md border border-blue-50 p-6 md:p-7 space-y-3">
                        <h2 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                            <span class="w-1 h-5 rounded-full bg-blue-500"></span>
                            Jadwal Operasional Ruangan
                        </h2>
                        <p class="text-xs text-gray-500">
                            Pengaturan detail jadwal bisa dibuat di halaman khusus jadwal. Saat ini hanya ditampilkan sebagai informasi.
                        </p>

                        <div class="overflow-x-auto mt-2">
                            <table class="min-w-full text-sm bg-white border rounded-lg overflow-hidden">
                                <thead>
                                    <tr class="bg-gray-100 text-left">
                                        <th class="px-3 py-2 border-b text-xs font-semibold text-gray-700">Hari</th>
                                        <th class="px-3 py-2 border-b text-xs font-semibold text-gray-700">Buka</th>
                                        <th class="px-3 py-2 border-b text-xs font-semibold text-gray-700">Tutup</th>
                                        <th class="px-3 py-2 border-b text-xs font-semibold text-gray-700">Istirahat Dari</th>
                                        <th class="px-3 py-2 border-b text-xs font-semibold text-gray-700">Istirahat Sampai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($schedule as $row): ?>
                                        <tr class="border-t">
                                            <td class="px-3 py-2">
                                                <?= htmlspecialchars($row['hari'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                            </td>
                                            <td class="px-3 py-2">
                                                <?= htmlspecialchars($row['open_time'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                            </td>
                                            <td class="px-3 py-2">
                                                <?= htmlspecialchars($row['close_time'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                            </td>
                                            <td class="px-3 py-2">
                                                <?= htmlspecialchars($row['break_start'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                            </td>
                                            <td class="px-3 py-2">
                                                <?= htmlspecialchars($row['break_end'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                <?php endif; ?>

            </div>
        </main>
    </div>

</body>

</html>