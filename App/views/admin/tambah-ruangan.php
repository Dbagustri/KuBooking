<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Ruangan | Kubooking</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
        ?>

        <!-- NAVBAR -->
        <div class="m-4">
            <?php if (file_exists($navPath)) include $navPath; ?>
        </div>

        <?php
        // Normalisasi data (kalau controller kirim $room/$facilities)
        $room        = $room ?? [];
        $facilities  = $facilities ?? [];
        $selectedFacilities = $selectedFacilities ?? ($room['fasilitas_ids'] ?? []);

        $namaRuangan  = $room['nama_ruangan'] ?? '';
        $lokasi       = $room['lokasi'] ?? '';
        $kategori     = $room['kategori'] ?? '';
        $kapMin       = $room['kapasitas_min'] ?? '';
        $kapMax       = $room['kapasitas_max'] ?? '';
        $status       = $room['status_operasional'] ?? 'aktif';

        $isSelectedFasilitas = function ($id) use ($selectedFacilities) {
            return in_array($id, (array)$selectedFacilities);
        };
        ?>

        <main class="px-4 md:px-8 pb-10">
            <div class="max-w-4xl mx-auto space-y-5">

                <!-- HEADER -->
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-blue-900/70 font-semibold">
                            Manajemen Ruangan
                        </p>
                        <h1 class="text-2xl md:text-3xl font-bold text-[#1e3a5f] mt-1">
                            Tambah Ruangan
                        </h1>
                        <p class="text-sm text-gray-600 mt-1">
                            Isi data ruangan yang akan tersedia untuk peminjaman.
                        </p>
                    </div>

                    <a href="index.php?controller=admin&action=ruangan"
                        class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium 
                          border border-slate-300 text-slate-600 bg-white hover:bg-slate-50 shadow-sm">
                        Kembali
                    </a>
                </div>

                <!-- CARD FORM -->
                <section class="bg-white rounded-2xl shadow-md border border-blue-50 p-6 md:p-7">
                    <!-- FORM -->
                    <form action="index.php?controller=admin&action=tambahRuangan"
                        method="POST"
                        enctype="multipart/form-data"
                        class="space-y-6">

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
                                        value="<?= htmlspecialchars($namaRuangan, ENT_QUOTES, 'UTF-8') ?>"
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
                                </div>

                                <!-- Status Operasional -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Status Operasional
                                    </label>
                                    <select
                                        name="status_operasional"
                                        class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm px-3 py-2.5 shadow-sm bg-white">
                                        <option value="aktif" <?= $status === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                        <option value="nonaktif" <?= $status === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- KAPASITAS & FASILITAS -->
                        <div class="space-y-4">
                            <h2 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                                <span class="w-1 h-5 rounded-full bg-blue-500"></span>
                                Kapasitas & Fasilitas
                            </h2>

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
                                        value="<?= htmlspecialchars($kapMin, ENT_QUOTES, 'UTF-8') ?>"
                                        class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm px-3 py-2.5 shadow-sm"
                                        placeholder="Contoh: 2"
                                        required>
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
                                        value="<?= htmlspecialchars($kapMax, ENT_QUOTES, 'UTF-8') ?>"
                                        class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm px-3 py-2.5 shadow-sm"
                                        placeholder="Contoh: 10"
                                        required>
                                </div>
                            </div>

                            <!-- FASILITAS -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">
                                    Fasilitas Ruangan
                                </label>
                                <p class="text-xs text-gray-500">
                                    Pilih fasilitas yang tersedia. Informasi ini akan tampil di halaman detail ruangan.
                                </p>

                                <?php if (!empty($facilities)): ?>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 mt-2">
                                        <?php foreach ($facilities as $f):
                                            $fid = (int)$f['id_fasilitas'];
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
                                                        <?= htmlspecialchars($f['nama_fasilitas'] ?? 'Tanpa nama', ENT_QUOTES, 'UTF-8') ?>
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
                                <?php else: ?>
                                    <div class="mt-2 text-xs text-gray-500 italic bg-gray-50 border border-dashed border-gray-200 rounded-lg px-3 py-2">
                                        Belum ada data fasilitas. Tambahkan data fasilitas terlebih dahulu di menu fasilitas.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- FOTO RUANGAN -->
                        <div class="space-y-2">
                            <h2 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                                <span class="w-1 h-5 rounded-full bg-blue-500"></span>
                                Foto Ruangan
                            </h2>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Upload Foto (Opsional)
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
                            </div>
                        </div>

                        <!-- ACTION BUTTONS -->
                        <div class="pt-4 border-t border-gray-100 flex flex-col md:flex-row md:justify-end gap-3">
                            <a href="index.php?controller=admin&action=ruangan"
                                class="inline-flex justify-center px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-medium">
                                Batal
                            </a>
                            <button
                                type="submit"
                                class="inline-flex justify-center px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-sm">
                                Simpan Ruangan
                            </button>
                        </div>
                    </form>
                </section>
            </div>
        </main>
    </div>

</body>

</html>