<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Peraturan Peminjaman Ruangan - Kubooking</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="bg-gray-50 text-slate-800">

    <?php
    // NAVBAR
    $navbarPath = __DIR__ . '/../layout/navbar.php';
    if (file_exists($navbarPath)) {
        include $navbarPath;
    }
    ?>

    <main class="max-w-6xl mx-auto px-4 py-6 space-y-6">

        <!-- Back -->
        <a href="index.php?controller=auth&action=landing"
            class="inline-flex items-center text-sm text-slate-600 hover:text-slate-900">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>

        <!-- TITLE -->
        <section class="space-y-1">
            <h1 class="text-2xl sm:text-3xl font-semibold text-slate-900">
                Peraturan Peminjaman Ruangan
            </h1>
            <p class="text-sm text-slate-500">
                Harap membaca dan mematuhi peraturan berikut sebelum menggunakan ruangan di perpustakaan.
            </p>
        </section>

        <!-- TOP BANNERS -->
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="lg:col-span-2 bg-[#17375b] text-white rounded-2xl p-5 sm:p-6 shadow">
                <h2 class="text-lg font-semibold mb-2">Gunakan ruangan dengan bijak</h2>
                <p class="text-sm text-slate-100 leading-relaxed">
                    Ruangan disediakan untuk kegiatan akademik seperti diskusi, presentasi, dan belajar kelompok.
                    Agar kenyamanan pengguna lain terjaga, ikuti peraturan yang berlaku di bawah ini.
                </p>
            </div>

            <div class="bg-white rounded-2xl p-5 sm:p-6 shadow">
                <h3 class="text-base font-semibold text-slate-900 mb-2">Ringkas</h3>
                <ul class="list-disc list-inside text-sm text-slate-600 space-y-1">
                    <li>Maksimal peminjaman 2 jam per sesi.</li>
                    <li>Datang tepat waktu sesuai jadwal.</li>
                    <li>Jaga kebersihan dan ketenangan ruangan.</li>
                </ul>
            </div>
        </section>

        <!-- RULES GRID -->
        <section class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6">

            <!-- 1. Peraturan Umum -->
            <article class="bg-white rounded-2xl p-5 sm:p-6 shadow space-y-3">
                <h2 class="text-base sm:text-lg font-semibold text-slate-900">
                    1. Peraturan Umum
                </h2>
                <ul class="list-disc list-inside text-sm text-slate-700 space-y-1.5">
                    <li>Peminjaman hanya boleh dilakukan oleh mahasiswa, dosen, dan tendik yang terdaftar di sistem perpustakaan.</li>
                    <li>Satu akun hanya boleh memiliki maksimal 2 peminjaman aktif dalam satu hari.</li>
                    <li>Ruangan hanya boleh digunakan untuk kegiatan akademik (belajar, diskusi, presentasi).</li>
                    <li>Kegiatan non-akademik wajib mendapatkan izin khusus dari pengelola perpustakaan.</li>
                    <li>Dilarang meminjamkan ruangan kepada pihak lain tanpa sepengetahuan petugas.</li>
                    <li>Peminjam wajib membawa kartu identitas (KTM/Kartu Pegawai) saat menggunakan ruangan.</li>
                </ul>
            </article>

            <!-- 2. Saat Menggunakan Ruangan -->
            <article class="bg-white rounded-2xl p-5 sm:p-6 shadow space-y-3">
                <h2 class="text-base sm:text-lg font-semibold text-slate-900">
                    2. Saat Menggunakan Ruangan
                </h2>
                <ul class="list-disc list-inside text-sm text-slate-700 space-y-1.5">
                    <li>Datanglah tepat waktu. Keterlambatan lebih dari 15 menit dapat menyebabkan peminjaman dibatalkan otomatis.</li>
                    <li>Jaga kebersihan ruangan, jangan meninggalkan sampah di meja atau lantai.</li>
                    <li>Jaga ketenangan. Hindari berbicara terlalu keras atau memutar musik tanpa izin.</li>
                    <li>Dilarang memindahkan atau merusak tata letak fasilitas ruangan tanpa persetujuan petugas.</li>
                    <li>Gunakan fasilitas (TV, proyektor, whiteboard) dengan hati-hati. Laporkan kerusakan kepada petugas segera setelah digunakan.</li>
                </ul>
            </article>

            <!-- 3. Perpanjangan & Pembatalan -->
            <article class="bg-white rounded-2xl p-5 sm:p-6 shadow space-y-3">
                <h2 class="text-base sm:text-lg font-semibold text-slate-900">
                    3. Perpanjangan &amp; Pembatalan
                </h2>
                <ul class="list-disc list-inside text-sm text-slate-700 space-y-1.5">
                    <li>Perpanjangan waktu hanya dapat dilakukan jika slot setelahnya masih kosong dan sesuai kebijakan perpustakaan.</li>
                    <li>Permohonan perpanjangan dilakukan melalui aplikasi Kubooking atau langsung ke petugas.</li>
                    <li>Jika tidak jadi menggunakan ruangan, batalkan peminjaman melalui menu
                        <span class="font-semibold">Riwayat</span> minimal 30 menit sebelum jadwal mulai.
                    </li>
                    <li>Pembatalan tanpa konfirmasi berulang dapat memengaruhi prioritas peminjaman di masa depan.</li>
                </ul>
            </article>

            <!-- 4. Sanksi Pelanggaran -->
            <article class="bg-white rounded-2xl p-5 sm:p-6 shadow space-y-3">
                <h2 class="text-base sm:text-lg font-semibold text-slate-900">
                    4. Sanksi Pelanggaran
                </h2>
                <ul class="list-disc list-inside text-sm text-slate-700 space-y-1.5">
                    <li>Perpustakaan berhak memberikan tindakan kepada pengguna yang melanggar peraturan.</li>
                    <li>Sanksi yang mungkin diberikan antara lain:
                        <ul class="list-disc list-inside ml-4 mt-1 space-y-1">
                            <li>Peringatan lisan atau tertulis.</li>
                            <li>Penangguhan hak peminjaman ruangan untuk sementara waktu.</li>
                            <li>Pembebanan biaya jika terjadi kerusakan fasilitas.</li>
                        </ul>
                    </li>
                    <li>Pelanggaran berat berulang dapat dilaporkan kepada program studi/fakultas untuk ditindaklanjuti.</li>
                </ul>
            </article>
        </section>
    </main>

    <?php
    // FOOTER
    $footerPath = __DIR__ . '/../layout/footer.php';
    if (file_exists($footerPath)) {
        include $footerPath;
    }
    ?>

</body>

</html>