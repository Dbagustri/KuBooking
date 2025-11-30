<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tata Cara Memesan Ruangan - Kubooking</title>
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
                Tata Cara Memesan Ruangan
            </h1>
            <p class="text-sm text-slate-500">
                Ikuti langkah-langkah berikut untuk memesan ruang diskusi di perpustakaan melalui Kubooking.
            </p>
        </section>

        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            <!-- LEFT SIDE (2/3) -->
            <div class="space-y-4 lg:col-span-2">

                <!-- Sebelum memesan -->
                <div class="bg-[#17375b] text-white rounded-2xl p-5 sm:p-6 shadow">
                    <h2 class="text-lg font-semibold mb-2">Sebelum memesan</h2>
                    <p class="text-sm text-slate-100 leading-relaxed">
                        Pastikan kamu sudah login dengan akun mahasiswa/dosen/tendik, dan menyiapkan informasi jadwal
                        (tanggal, jam, dan durasi) yang ingin digunakan.
                    </p>
                </div>

                <!-- Langkah-langkah -->
                <div class="bg-white rounded-2xl p-5 sm:p-6 shadow space-y-4">
                    <h2 class="text-lg font-semibold text-slate-900">
                        Langkah 1–6: Proses pemesanan ruangan
                    </h2>

                    <!-- Step list -->
                    <ol class="space-y-4 text-sm text-slate-700">

                        <li class="flex items-start gap-3">
                            <div
                                class="flex items-center justify-center w-7 h-7 rounded-full bg-slate-900 text-white text-xs font-semibold mt-0.5">
                                1
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900 mb-1">Masuk ke halaman beranda Kubooking</p>
                                <p class="text-slate-600">
                                    Setelah login, kamu akan melihat daftar ruangan yang tersedia. Gunakan kolom pencarian
                                    dan informasi kapasitas untuk menemukan ruangan yang sesuai dengan kebutuhan kelompokmu.
                                </p>
                            </div>
                        </li>

                        <li class="flex items-start gap-3">
                            <div
                                class="flex items-center justify-center w-7 h-7 rounded-full bg-slate-900 text-white text-xs font-semibold mt-0.5">
                                2
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900 mb-1">Pilih ruangan yang ingin digunakan</p>
                                <p class="text-slate-600">
                                    Klik tombol <span class="font-semibold">“View Detail”</span> atau nama ruangan untuk
                                    melihat informasi lengkap seperti lokasi, kapasitas, fasilitas, dan status ketersediaan.
                                    Jika sudah cocok, tekan tombol <span class="font-semibold">“Pesan”</span>.
                                </p>
                            </div>
                        </li>

                        <li class="flex items-start gap-3">
                            <div
                                class="flex items-center justify-center w-7 h-7 rounded-full bg-slate-900 text-white text-xs font-semibold mt-0.5">
                                3
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900 mb-1">Atur tanggal, jam, dan durasi</p>
                                <p class="text-slate-600">
                                    Pada halaman pemesanan, pilih tanggal peminjaman, kemudian jam mulai dan durasi
                                    peminjaman. Sistem akan menampilkan slot waktu yang tersedia. Slot yang tidak tersedia
                                    akan ditandai dengan warna merah.
                                </p>
                            </div>
                        </li>

                        <li class="flex items-start gap-3">
                            <div
                                class="flex items-center justify-center w-7 h-7 rounded-full bg-slate-900 text-white text-xs font-semibold mt-0.5">
                                4
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900 mb-1">Buat kelompok</p>
                                <p class="text-slate-600">
                                    Jika ruangan akan digunakan bersama teman, kamu bisa membuat kelompok.
                                    Setelah menekan tombol <span class="font-semibold">“Buat Kelompok &amp; Booking”</span>,
                                    sistem akan menghasilkan kode kelompok.
                                    Teman-temanmu dapat bergabung dengan memasukkan kode tersebut di fitur
                                    <span class="font-semibold">“Gabung Kelompok”</span> pada halaman beranda.
                                </p>
                            </div>
                        </li>

                        <li class="flex items-start gap-3">
                            <div
                                class="flex items-center justify-center w-7 h-7 rounded-full bg-slate-900 text-white text-xs font-semibold mt-0.5">
                                5
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900 mb-1">Konfirmasi pemesanan</p>
                                <p class="text-slate-600">
                                    Setelah anggota kelompok cukup dan detail sudah benar,
                                    ketua kelompok (PJ) menekan tombol
                                    <span class="font-semibold">“Ajukan Booking”</span>. Permintaan akan dikirim ke admin
                                    perpustakaan untuk diverifikasi.
                                </p>
                            </div>
                        </li>

                        <li class="flex items-start gap-3">
                            <div
                                class="flex items-center justify-center w-7 h-7 rounded-full bg-slate-900 text-white text-xs font-semibold mt-0.5">
                                6
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900 mb-1">Lihat status di halaman Riwayat</p>
                                <p class="text-slate-600">
                                    Setelah pemesanan diajukan, kamu bisa memantau status peminjaman di menu
                                    <span class="font-semibold">“Riwayat”</span>. Di sana kamu dapat melihat apakah
                                    peminjaman masih menunggu persetujuan, sudah disetujui, atau ditolak. Dari halaman ini
                                    kamu juga dapat membuka detail booking atau membatalkan jika masih diperbolehkan.
                                </p>
                            </div>
                        </li>
                    </ol>
                </div>
            </div>

            <!-- RIGHT SIDE (NOTE & FAQ) -->
            <div class="space-y-4">

                <!-- Catatan penting -->
                <div class="bg-white rounded-2xl p-4 sm:p-5 shadow">
                    <h3 class="text-base font-semibold text-slate-900 mb-2">Catatan penting</h3>
                    <ul class="list-disc list-inside text-sm text-slate-600 space-y-1">
                        <li>Pemesanan hanya bisa dilakukan oleh akun yang sudah aktif.</li>
                        <li>Jika kamu tidak hadir lebih dari 15 menit, ruangan dapat dialihkan ke pengguna lain.</li>
                        <li>Satu ruangan hanya boleh digunakan oleh satu kelompok dalam satu slot waktu.</li>
                        <li>Jaga kebersihan dan ketenangan di dalam ruangan selama digunakan.</li>
                    </ul>
                </div>

                <!-- FAQ -->
                <div class="bg-white rounded-2xl p-4 sm:p-5 shadow space-y-4">
                    <h3 class="text-base font-semibold text-slate-900">Pertanyaan yang sering muncul</h3>

                    <div class="space-y-3 text-sm text-slate-700">
                        <div>
                            <p class="font-semibold">Apa bedanya “Pilih Ruangan” dan “Gabung Kelompok”?</p>
                            <p class="text-slate-600">
                                “Pilih Ruangan” digunakan untuk membuat pemesanan baru sebagai ketua kelompok (PJ).
                                “Gabung Kelompok” dipakai jika temanmu sudah membuat kelompok dan membagikan kode
                                kelompok kepadamu.
                            </p>
                        </div>

                        <div>
                            <p class="font-semibold">Bisakah saya mengubah jam setelah memesan?</p>
                            <p class="text-slate-600">
                                Jam dan tanggal tidak bisa diubah langsung. Batalkan pemesanan lewat menu
                                <span class="font-semibold">Riwayat</span>, lalu buat pemesanan baru dengan jadwal yang
                                benar.
                            </p>
                        </div>

                        <div>
                            <p class="font-semibold">Di mana saya bisa melihat kode kelompok?</p>
                            <p class="text-slate-600">
                                Kode kelompok akan muncul di halaman detail pemesanan setelah kamu menekan tombol
                                <span class="font-semibold">“Buat Kelompok &amp; Booking”</span>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
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