<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Roomify - Peraturan Ruangan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">
    <!-- NAVBAR -->
    <header class="bg-slate-900 text-white">
        <div class="max-w-6xl mx-auto flex items-center justify-between px-4 py-3 gap-4">
            <!-- Brand -->
            <div class="font-semibold text-lg tracking-wide">Roomify</div>

            <!-- Nav tengah -->
            <nav class="hidden md:flex items-center gap-6 text-sm">
                <a href="#" class="border-b-2 border-transparent hover:border-slate-200 pb-1">
                    Beranda
                </a>
                <a href="#" class="border-b-2 border-transparent hover:border-slate-200 pb-1">
                    Riwayat
                </a>
                <a href="#" class="border-b-2 border-transparent hover:border-slate-200 pb-1">
                    Profil
                </a>
            </nav>

            <!-- Kanan: profil + logout -->
            <div class="flex items-center gap-3 ml-auto md:ml-0">
                <div class="hidden sm:flex items-center gap-2">
                    <div class="h-8 w-8 rounded-full bg-slate-700"></div>
                    <span class="text-sm">Diandra B.</span>
                </div>
                <button
                    class="rounded-full border border-white/30 bg-transparent px-4 py-1.5 text-xs sm:text-sm font-medium hover:bg-white/10">
                    Logout
                </button>
            </div>
        </div>
    </header>

    <!-- CONTENT -->
    <main class="max-w-5xl mx-auto px-4 py-8 space-y-8">
        <!-- Back + Judul -->
        <section class="flex items-center gap-3">
            <button class="flex items-center justify-center h-9 w-9 rounded-full hover:bg-slate-200">
                <span class="text-lg">←</span>
            </button>
            <div class="flex-1 text-center mr-9">
                <h1 class="text-2xl font-semibold text-slate-900">
                    Peraturan Peminjaman Ruangan
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Harap membaca dan mematuhi peraturan berikut sebelum menggunakan ruangan di perpustakaan.
                </p>
            </div>
        </section>

        <!-- Banner ringkas -->
        <section class="bg-slate-900 text-white rounded-2xl shadow-sm p-5 sm:p-6 flex flex-col sm:flex-row gap-4 sm:items-center">
            <div class="flex-1 space-y-1">
                <h2 class="text-lg font-semibold">Gunakan ruangan dengan bijak</h2>
                <p class="text-sm text-slate-200">
                    Ruangan disediakan untuk kegiatan akademik seperti diskusi, presentasi, dan belajar kelompok.
                    Jaga kenyamanan pengguna lain dengan mengikuti peraturan yang berlaku.
                </p>
            </div>
            <div class="sm:w-52 bg-white/5 rounded-xl px-4 py-3 text-xs space-y-1 border border-white/10">
                <p class="uppercase tracking-wide text-[10px] text-slate-200 font-semibold">Ringkas</p>
                <p>• Maks. 2 jam / sesi</p>
                <p>• Datang tepat waktu</p>
                <p>• Jaga kebersihan & ketenangan</p>
            </div>
        </section>

        <!-- 2 kolom: peraturan umum & selama penggunaan -->
        <section class="grid gap-6 lg:grid-cols-2">
            <!-- Peraturan umum -->
            <section class="bg-white rounded-2xl shadow-sm p-5 sm:p-6 space-y-3">
                <h2 class="text-base font-semibold text-slate-900">
                    1. Peraturan Umum
                </h2>
                <ul class="text-sm text-slate-700 space-y-2 list-disc list-inside">
                    <li>
                        Peminjaman hanya boleh dilakukan oleh
                        <span class="font-medium">mahasiswa dan civitas yang terdaftar</span>
                        di sistem perpustakaan.
                    </li>
                    <li>
                        Satu akun hanya boleh memiliki
                        <span class="font-medium">maksimal 2 peminjaman aktif</span> dalam satu hari.
                    </li>
                    <li>
                        Ruangan hanya boleh digunakan untuk
                        <span class="font-medium">kegiatan akademik</span> (belajar, diskusi, presentasi).
                        Kegiatan non-akademik wajib mendapat izin khusus.
                    </li>
                    <li>
                        Dilarang meminjamkan ruangan kepada pihak lain tanpa sepengetahuan petugas.
                    </li>
                    <li>
                        Pengguna wajib membawa kartu identitas (KTM/Kartu Pegawai) saat menggunakan ruangan.
                    </li>
                </ul>
            </section>

            <!-- Saat menggunakan ruangan -->
            <section class="bg-white rounded-2xl shadow-sm p-5 sm:p-6 space-y-3">
                <h2 class="text-base font-semibold text-slate-900">
                    2. Saat Menggunakan Ruangan
                </h2>
                <ul class="text-sm text-slate-700 space-y-2 list-disc list-inside">
                    <li>
                        Datanglah <span class="font-medium">tepat waktu</span>. Keterlambatan lebih dari
                        <span class="font-medium">15 menit</span> dapat menyebabkan peminjaman dibatalkan otomatis.
                    </li>
                    <li>
                        Jaga <span class="font-medium">kebersihan</span> ruangan: buang sampah pada tempatnya,
                        rapikan meja dan kursi sebelum meninggalkan ruangan.
                    </li>
                    <li>
                        Dilarang makan dan minum berlebihan di dalam ruangan, kecuali minuman tertutup (botol/tumbler).
                    </li>
                    <li>
                        Jaga <span class="font-medium">ketenangan</span>. Hindari berbicara terlalu keras atau memainkan musik tanpa izin.
                    </li>
                    <li>
                        Gunakan fasilitas (TV, proyektor, whiteboard) dengan hati-hati.
                        Laporkan kerusakan kepada petugas segera setelah digunakan.
                    </li>
                </ul>
            </section>
        </section>

        <!-- Perpanjangan & pembatalan + Sanksi -->
        <section class="grid gap-6 lg:grid-cols-[1.4fr,1fr]">
            <!-- Perpanjangan & pembatalan -->
            <section class="bg-white rounded-2xl shadow-sm p-5 sm:p-6 space-y-3">
                <h2 class="text-base font-semibold text-slate-900">
                    3. Perpanjangan &amp; Pembatalan
                </h2>
                <ul class="text-sm text-slate-700 space-y-2 list-disc list-inside">
                    <li>
                        Perpanjangan waktu hanya dapat dilakukan jika
                        <span class="font-medium">slot setelahnya masih kosong</span>
                        dan sesuai kebijakan perpustakaan.
                    </li>
                    <li>
                        Permohonan perpanjangan dilakukan melalui aplikasi Roomify
                        atau langsung ke petugas perpustakaan.
                    </li>
                    <li>
                        Jika tidak jadi menggunakan ruangan,
                        <span class="font-medium">batalkan peminjaman</span> melalui menu
                        <span class="font-medium">Riwayat</span> minimal 30 menit sebelum jadwal mulai.
                    </li>
                    <li>
                        Pembatalan tanpa konfirmasi berulang dapat memengaruhi
                        <span class="font-medium">riwayat dan prioritas peminjaman</span> di masa depan.
                    </li>
                </ul>
            </section>

            <!-- Sanksi -->
            <aside class="bg-white rounded-2xl shadow-sm p-5 sm:p-6 space-y-3">
                <h2 class="text-base font-semibold text-slate-900">
                    4. Sanksi Pelanggaran
                </h2>
                <p class="text-sm text-slate-700">
                    Perpustakaan berhak memberikan tindakan kepada pengguna yang melanggar peraturan:
                </p>
                <ul class="text-sm text-slate-700 space-y-2 list-disc list-inside">
                    <li>Peringatan lisan atau tertulis.</li>
                    <li>Penangguhan hak peminjaman ruangan untuk sementara waktu.</li>
                    <li>Penggantian biaya jika terjadi kerusakan fasilitas.</li>
                    <li>
                        Pelaporan kepada program studi / fakultas untuk pelanggaran berat dan berulang.
                    </li>
                </ul>
            </aside>
        </section>

        <!-- Footer kecil -->
        <section class="text-[11px] text-slate-500 text-center pt-4">
            Dengan menggunakan Roomify, Anda dianggap telah membaca dan menyetujui seluruh
            peraturan peminjaman ruangan di atas.
        </section>
    </main>
</body>

</html>