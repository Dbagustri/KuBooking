<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Roomify - Notifikasi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">
    <!-- NAVBAR -->
    <header class="bg-slate-900 text-white">
        <div class="max-w-6xl mx-auto flex items-center justify-between px-4 py-3">
            <!-- Brand -->
            <div class="font-semibold text-lg tracking-wide">Roomify</div>

            <!-- Nav -->
            <nav class="hidden md:flex items-center gap-6 text-sm">
                <a href="#" class="hover:text-slate-200">Beranda</a>
                <a href="#" class="hover:text-slate-200">Riwayat</a>
                <a href="#" class="border-b-2 border-white pb-1 font-semibold">Profil</a>
            </nav>

            <!-- Right -->
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex items-center gap-2">
                    <div class="h-8 w-8 rounded-full bg-slate-700"></div>
                    <span class="text-sm">Diandra B.</span>
                </div>
                <button
                    class="rounded-full border border-white/30 px-4 py-1.5 text-sm font-medium hover:bg-white/10">
                    Logout
                </button>
            </div>
        </div>
    </header>

    <!-- CONTENT -->
    <main class="max-w-4xl mx-auto px-4 py-8 space-y-6">
        <!-- Header + back -->
        <div class="flex items-center gap-3">
            <button class="flex items-center justify-center h-9 w-9 rounded-full hover:bg-slate-200">
                <span class="text-lg">←</span>
            </button>
            <div class="flex-1 text-center mr-9">
                <h1 class="text-xl font-semibold text-slate-900">Notifikasi</h1>
                <p class="text-xs text-slate-500 mt-1">
                    Informasi terbaru terkait peminjaman ruangan dan kelompokmu.
                </p>
            </div>
        </div>

        <!-- Filter kecil -->
        <section class="flex flex-wrap items-center justify-between gap-3 text-xs">
            <div class="flex gap-2">
                <button
                    class="rounded-full bg-slate-900 text-white px-3 py-1 font-medium">
                    Semua
                </button>
                <button
                    class="rounded-full border border-slate-300 bg-white px-3 py-1 text-slate-700 hover:bg-slate-50">
                    Belum dibaca
                </button>
                <button
                    class="rounded-full border border-slate-300 bg-white px-3 py-1 text-slate-700 hover:bg-slate-50">
                    Sudah dibaca
                </button>
            </div>
            <button class="text-xs text-slate-500 hover:text-slate-800">
                Tandai semua sudah dibaca
            </button>
        </section>

        <!-- LIST NOTIFIKASI -->
        <section class="space-y-3">
            <!-- Notif 1 - UNREAD -->
            <article
                class="bg-white rounded-2xl shadow-sm border border-slate-200/80 px-4 py-3 sm:px-5 sm:py-4 flex flex-col gap-2">
                <div class="flex items-start justify-between gap-3">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <h2 class="text-sm font-semibold text-slate-900">
                                Peminjaman disetujui
                            </h2>
                            <span
                                class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-semibold text-emerald-700">
                                BARU
                            </span>
                        </div>
                        <p class="text-xs text-slate-600">
                            Peminjaman <span class="font-medium">Ruang Pertama</span> pada
                            27 Okt 2025 pukul 10.00–12.00 telah disetujui oleh petugas.
                        </p>
                        <button class="text-xs font-medium text-slate-700 hover:text-slate-900">
                            Lihat detail booking
                        </button>
                    </div>

                    <div class="flex flex-col items-end text-[11px] text-slate-400 whitespace-nowrap">
                        <span>27 Okt 2025</span>
                        <span>12.00</span>
                    </div>
                </div>
            </article>

            <!-- Notif 2 -->
            <article
                class="bg-white rounded-2xl shadow-sm border border-slate-200/80 px-4 py-3 sm:px-5 sm:py-4 flex flex-col gap-2">
                <div class="flex items-start justify-between gap-3">
                    <div class="space-y-1">
                        <h2 class="text-sm font-semibold text-slate-900">
                            Pengingat peminjaman
                        </h2>
                        <p class="text-xs text-slate-600">
                            Peminjaman <span class="font-medium">Ruang Diskusi 2</span> akan
                            dimulai 30 menit lagi (27 Okt 2025, 13.00–15.00).
                        </p>
                        <button class="text-xs font-medium text-slate-700 hover:text-slate-900">
                            Lihat detail jadwal
                        </button>
                    </div>

                    <div class="flex flex-col items-end text-[11px] text-slate-400 whitespace-nowrap">
                        <span>27 Okt 2025</span>
                        <span>12.30</span>
                    </div>
                </div>
            </article>

            <!-- Notif 3 -->
            <article
                class="bg-white rounded-2xl shadow-sm border border-slate-200/80 px-4 py-3 sm:px-5 sm:py-4 flex flex-col gap-2">
                <div class="flex items-start justify-between gap-3">
                    <div class="space-y-1">
                        <h2 class="text-sm font-semibold text-slate-900">
                            Perubahan jadwal kelompok
                        </h2>
                        <p class="text-xs text-slate-600">
                            Jadwal kelompok <span class="font-medium">heiuF83</span> dipindah
                            ke <span class="font-medium">Ruang Ketiga</span> pada 28 Okt 2025,
                            09.00–11.00.
                        </p>
                        <button class="text-xs font-medium text-slate-700 hover:text-slate-900">
                            Lihat kelompok
                        </button>
                    </div>

                    <div class="flex flex-col items-end text-[11px] text-slate-400 whitespace-nowrap">
                        <span>26 Okt 2025</span>
                        <span>16.45</span>
                    </div>
                </div>
            </article>

            <!-- Notif 4 -->
            <article
                class="bg-white rounded-2xl shadow-sm border border-slate-200/80 px-4 py-3 sm:px-5 sm:py-4 flex flex-col gap-2">
                <div class="flex items-start justify-between gap-3">
                    <div class="space-y-1">
                        <h2 class="text-sm font-semibold text-slate-900">
                            Sesi peminjaman selesai
                        </h2>
                        <p class="text-xs text-slate-600">
                            Terima kasih telah menggunakan <span class="font-medium">Ruang Pertama</span>.
                            Jangan lupa merapikan ruangan sebelum meninggalkan area.
                        </p>
                    </div>

                    <div class="flex flex-col items-end text-[11px] text-slate-400 whitespace-nowrap">
                        <span>25 Okt 2025</span>
                        <span>15.05</span>
                    </div>
                </div>
            </article>
        </section>
    </main>
</body>

</html>