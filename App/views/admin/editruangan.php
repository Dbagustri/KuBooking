<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Ruangan | Kubooking</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#f2f7fc] text-gray-800 flex">

    <?php
    // SIDEBAR
    $sidebarPath = __DIR__ . '/../layout/sidebar.php';
    if (file_exists($sidebarPath)) {
        include $sidebarPath;
    }
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

        <?php
        // Normalisasi data ruangan
        /** @var array $room */
        $room = $room ?? [];

        $idRuangan   = (int)($room['id_ruangan'] ?? 0);
        $nama        = $room['nama_ruangan']     ?? '';
        $kategori    = $room['kategori']         ?? '';
        $lokasi      = $room['lokasi']           ?? '';
        $kapMin      = $room['kapasitas_min']    ?? '';
        $kapMax      = $room['kapasitas_max']    ?? '';
        $status      = $room['status_operasional'] ?? 'nonaktif';
        $foto        = $room['foto_ruangan']     ?? '';

        // Jadwal ruangan (optional)
        /** @var array|null $schedule */
        $schedule = isset($schedule) && is_array($schedule) ? $schedule : [];
        ?>

        <div class="px-8 pb-10 space-y-6">

            <!-- HEADER + BACK -->
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-[#1e3a5f]">Edit Ruangan</h1>

                <a href="index.php?controller=admin&action=ruangan"
                    class="px-4 py-2 rounded-lg bg-gray-300 hover:bg-gray-400 text-gray-800 text-sm font-semibold">
                    Kembali
                </a>
            </div>

            <!-- FORM EDIT RUANGAN -->
            <div class="bg-white shadow rounded-lg p-6 space-y-6">
                <h2 class="text-xl font-semibold mb-2">Informasi Ruangan</h2>

                <form action="index.php?controller=admin&action=updateRoom"
                    method="POST"
                    enctype="multipart/form-data"
                    class="space-y-5">

                    <input type="hidden" name="id_ruangan" value="<?= $idRuangan ?>">

                    <!-- Nama + Kategori -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Nama Ruangan <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                name="nama_ruangan"
                                required
                                value="<?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8') ?>"
                                class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Kategori
                            </label>
                            <input type="text"
                                name="kategori"
                                value="<?= htmlspecialchars($kategori, ENT_QUOTES, 'UTF-8') ?>"
                                placeholder="Contoh: Diskusi, Komputer, Rapat"
                                class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <!-- Lokasi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Lokasi
                        </label>
                        <input type="text"
                            name="lokasi"
                            value="<?= htmlspecialchars($lokasi, ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="Contoh: Lantai 2, Gedung Perpustakaan"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Kapasitas -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Kapasitas Minimum <span class="text-red-500">*</span>
                            </label>
                            <input type="number"
                                min="1"
                                name="kapasitas_min"
                                required
                                value="<?= htmlspecialchars((string)$kapMin, ENT_QUOTES, 'UTF-8') ?>"
                                class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Kapasitas Maksimum <span class="text-red-500">*</span>
                            </label>
                            <input type="number"
                                min="1"
                                name="kapasitas_max"
                                required
                                value="<?= htmlspecialchars((string)$kapMax, ENT_QUOTES, 'UTF-8') ?>"
                                class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <!-- Foto -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Foto Ruangan
                            </label>
                            <input type="file"
                                name="foto_ruangan"
                                accept="image/*"
                                class="w-full text-sm text-gray-700
                                          file:mr-3 file:py-1.5 file:px-3
                                          file:rounded-full file:border-0
                                          file:text-sm file:font-semibold
                                          file:bg-blue-50 file:text-blue-700
                                          hover:file:bg-blue-100">
                            <p class="text-xs text-gray-500 mt-1">
                                Biarkan kosong jika tidak ingin mengubah foto.
                            </p>
                        </div>

                        <?php if (!empty($foto)): ?>
                            <div class="mt-2">
                                <p class="text-sm font-medium text-gray-700 mb-1">Preview Foto Saat Ini</p>
                                <img src="<?= htmlspecialchars($foto, ENT_QUOTES, 'UTF-8') ?>"
                                    alt="Foto ruangan"
                                    class="w-full max-w-xs rounded-lg border shadow-sm">
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Status Operasional -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Status Operasional <span class="text-red-500">*</span>
                            </label>
                            <select name="status_operasional"
                                class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="aktif" <?= $status === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                <option value="nonaktif" <?= $status === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <!-- (OPSIONAL) TAMPILAN JADWAL RUANGAN -->
                    <?php if (!empty($schedule)): ?>
                        <div class="pt-4 border-t">
                            <h2 class="text-lg font-semibold mb-3">Jadwal Operasional Ruangan</h2>
                            <p class="text-xs text-gray-500 mb-2">
                                Pengaturan detail jadwal bisa dibuat di halaman khusus jadwal. Saat ini hanya ditampilkan sebagai informasi.
                            </p>
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm bg-white border rounded">
                                    <thead>
                                        <tr class="bg-gray-100 text-left">
                                            <th class="px-3 py-2">Hari</th>
                                            <th class="px-3 py-2">Buka</th>
                                            <th class="px-3 py-2">Tutup</th>
                                            <th class="px-3 py-2">Istirahat Dari</th>
                                            <th class="px-3 py-2">Istirahat Sampai</th>
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
                        </div>
                    <?php endif; ?>

                    <!-- TOMBOL AKSI -->
                    <div class="pt-6 border-t flex flex-wrap gap-3">
                        <button type="submit"
                            class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-semibold">
                            Simpan Perubahan
                        </button>

                        <a href="index.php?controller=admin&action=ruangan"
                            class="px-5 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 text-sm font-semibold">
                            Batal
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </div>

</body>

</html>