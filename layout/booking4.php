<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Roomify - Beranda</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">
  <!-- NAVBAR -->
  <header class="bg-slate-900 text-white">
    <div class="max-w-6xl mx-auto flex items-center justify-between px-4 py-3 gap-4">
      <!-- Kiri: brand -->
      <div class="flex items-center gap-2">
        <span class="font-semibold text-lg tracking-wide">Roomify</span>
      </div>

      <!-- Tengah: nav -->
      <nav class="hidden md:flex items-center gap-6 text-sm">
        <a href="#" class="pb-1 border-b-2 border-white font-semibold">
          Beranda
        </a>
        <a href="#" class="pb-1 border-b-2 border-transparent hover:border-slate-200">
          Riwayat
        </a>
        <a href="#" class="pb-1 border-b-2 border-transparent hover:border-slate-200">
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
          class="rounded-full border border-slate-200 bg-white/0 px-4 py-1.5 text-xs sm:text-sm font-medium text-white hover:bg-white/10"
        >
          Logout
        </button>
        <!-- menu ikon untuk mobile -->
        <button
          class="inline-flex h-8 w-8 items-center justify-center rounded-full hover:bg-slate-800 md:hidden"
        >
          <span class="sr-only">Menu</span>
          <div class="space-y-1">
            <span class="block h-0.5 w-5 bg-white"></span>
            <span class="block h-0.5 w-5 bg-white"></span>
            <span class="block h-0.5 w-5 bg-white"></span>
          </div>
        </button>
      </div>
    </div>
  </header>

  <!-- CONTENT -->
  <main class="max-w-6xl mx-auto px-4 py-6 space-y-8">
    <!-- HERO SECTION -->
    <section
      class="bg-slate-900 text-white rounded-2xl shadow-sm p-4 sm:p-6 grid md:grid-cols-3 gap-6 items-center"
    >
      <!-- KIRI : PROFIL -->
      <div class="flex items-center gap-4">
        <div class="h-16 w-16 rounded-full bg-slate-700"></div>
        <div>
          <p class="text-sm text-slate-200">Halo,</p>
          <h1 class="text-xl font-semibold">Diandra Bagustri</h1>
          <p class="text-sm text-slate-300 mt-1">Jurusan TIK</p>
        </div>
      </div>

      <!-- TENGAH : PEMINJAMAN AKTIF -->
      <div class="rounded-xl p-4 text-center">
        <p class="text-xs uppercase tracking-wide text-slate-300 font-semibold">
          Peminjaman Aktif
        </p>
        <p class="mt-1 text-sm font-semibold">Ruang Pertama</p>
        <p class="text-xs text-slate-200 mt-1">08.00–12.00 · 26 Okt 2025</p>

        <button
          class="mt-3 inline-flex items-center justify-center rounded-lg border border-slate-500 px-3 py-1.5 text-xs font-medium text-slate-100 hover:bg-slate-700"
        >
          Lihat Detail
        </button>
      </div>

      <!-- KANAN : BUTTON -->
      <div class="flex flex-col gap-3 text-center md:text-right">
        <button
          class="w-full md:w-auto rounded-xl bg-white text-slate-900 px-4 py-2 text-sm font-semibold shadow-sm hover:bg-slate-100"
        >
          Pilih Ruangan
        </button>
        <button
          class="w-full md:w-auto rounded-xl border border-slate-300 bg-slate-800/60 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700"
        >
          Gabung Kelompok
        </button>
      </div>
    </section>

    <!-- DAFTAR RUANGAN + SEARCH -->
    <section class="space-y-4">
      <!-- Judul -->
      <h2 class="text-xl font-semibold text-slate-900">
        Daftar ruangan tersedia
      </h2>

      <!-- Search ruangan -->
      <div class="relative w-full">
        <span
          class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"
        >
          🔍
        </span>
        <input
          type="text"
          placeholder="Cari ruangan (nama / kapasitas)..."
          class="w-full rounded-xl border border-slate-200 bg-slate-50 pl-8 pr-3 py-2 text-sm focus:border-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-300"
        />
      </div>

      <!-- Filter kapasitas (chip) -->
      <div class="flex flex-wrap gap-2">
        <button
          class="rounded-full border border-slate-200 bg-slate-50 px-4 py-1.5 text-xs sm:text-sm text-slate-700 hover:bg-slate-100"
        >
          4–6 Orang
        </button>
        <button
          class="rounded-full border border-slate-200 bg-slate-50 px-4 py-1.5 text-xs sm:text-sm text-slate-700 hover:bg-slate-100"
        >
          6–8 Orang
        </button>
        <button
          class="rounded-full border border-slate-200 bg-slate-50 px-4 py-1.5 text-xs sm:text-sm text-slate-700 hover:bg-slate-100"
        >
          8–10 Orang
        </button>
        <button
          class="rounded-full border border-slate-200 bg-slate-50 px-4 py-1.5 text-xs sm:text-sm text-slate-700 hover:bg-slate-100"
        >
          10–12 Orang
        </button>
      </div>
    </section>

    <!-- GRID RUANGAN -->
    <section class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
      <!-- Card 1 -->
      <article class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col">
        <div class="h-32 bg-slate-800"></div>
        <div class="flex-1 p-4 space-y-3">
          <div class="flex items-start justify-between gap-2">
            <div>
              <h3 class="text-base font-semibold text-slate-900">
                Ruang Pertama
              </h3>
              <p class="text-xs text-slate-500 mt-0.5">Ruang Diskusi</p>
            </div>
            <button class="text-xs text-slate-500 hover:text-slate-800">
              View Detail
            </button>
          </div>

          <div class="space-y-1 text-xs text-slate-600">
            <p>📍 Lantai 1</p>
            <p>👥 Kapasitas 5–10 orang</p>
            <p>⏱ Maks. 2 jam / sesi</p>
          </div>

          <div class="pt-2">
            <button
              class="w-full rounded-xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800"
            >
              Pesan
            </button>
          </div>
        </div>
      </article>

      <!-- Card 2 -->
      <article class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col">
        <div class="h-32 bg-slate-700"></div>
        <div class="flex-1 p-4 space-y-3">
          <div class="flex items-start justify-between gap-2">
            <div>
              <h3 class="text-base font-semibold text-slate-900">
                Ruang Kedua
              </h3>
              <p class="text-xs text-slate-500 mt-0.5">Ruang Presentasi</p>
            </div>
            <button class="text-xs text-slate-500 hover:text-slate-800">
              View Detail
            </button>
          </div>

          <div class="space-y-1 text-xs text-slate-600">
            <p>📍 Lantai 2</p>
            <p>👥 Kapasitas 8–12 orang</p>
            <p>🖥 TV / Proyektor</p>
          </div>

          <div class="pt-2">
            <button
              class="w-full rounded-xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800"
            >
              Pesan
            </button>
          </div>
        </div>
      </article>

      <!-- Card 3 -->
      <article class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col">
        <div class="h-32 bg-slate-600"></div>
        <div class="flex-1 p-4 space-y-3">
          <div class="flex items-start justify-between gap-2">
            <div>
              <h3 class="text-base font-semibold text-slate-900">
                Ruang Ketiga
              </h3>
              <p class="text-xs text-slate-500 mt-0.5">Ruang Belajar</p>
            </div>
            <button class="text-xs text-slate-500 hover:text-slate-800">
              View Detail
            </button>
          </div>

          <div class="space-y-1 text-xs text-slate-600">
            <p>📍 Lantai 1</p>
            <p>👥 Kapasitas 4–6 orang</p>
            <p>🔇 Area tenang</p>
          </div>

          <div class="pt-2">
            <button
              class="w-full rounded-xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800"
            >
              Pesan
            </button>
          </div>
        </div>
      </article>

      <!-- Card 4 -->
      <article class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col">
        <div class="h-32 bg-slate-800/80"></div>
        <div class="flex-1 p-4 space-y-3">
          <div class="flex items-start justify-between gap-2">
            <div>
              <h3 class="text-base font-semibold text-slate-900">
                Ruang Keempat
              </h3>
              <p class="text-xs text-slate-500 mt-0.5">Ruang Diskusi</p>
            </div>
            <button class="text-xs text-slate-500 hover:text-slate-800">
              View Detail
            </button>
          </div>

          <div class="space-y-1 text-xs text-slate-600">
            <p>📍 Lantai 3</p>
            <p>👥 Kapasitas 6–8 orang</p>
            <p>📝 Whiteboard</p>
          </div>

          <div class="pt-2">
            <button
              class="w-full rounded-xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800"
            >
              Pesan
            </button>
          </div>
        </div>
      </article>

      <!-- Card 5 -->
      <article class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col">
        <div class="h-32 bg-slate-700/80"></div>
        <div class="flex-1 p-4 space-y-3">
          <div class="flex items-start justify-between gap-2">
            <div>
              <h3 class="text-base font-semibold text-slate-900">
                Ruang Kelima
              </h3>
              <p class="text-xs text-slate-500 mt-0.5">Ruang Presentasi</p>
            </div>
            <button class="text-xs text-slate-500 hover:text-slate-800">
              View Detail
            </button>
          </div>

          <div class="space-y-1 text-xs text-slate-600">
            <p>📍 Lantai 2</p>
            <p>👥 Kapasitas 10–12 orang</p>
            <p>🎤 Mic & Speaker</p>
          </div>

          <div class="pt-2">
            <button
              class="w-full rounded-xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800"
            >
              Pesan
            </button>
          </div>
        </div>
      </article>

      <!-- Card 6 -->
      <article class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col">
        <div class="h-32 bg-slate-600/80"></div>
        <div class="flex-1 p-4 space-y-3">
          <div class="flex items-start justify-between gap-2">
            <div>
              <h3 class="text-base font-semibold text-slate-900">
                Ruang Keenam
              </h3>
              <p class="text-xs text-slate-500 mt-0.5">Ruang Belajar</p>
            </div>
            <button class="text-xs text-slate-500 hover:text-slate-800">
              View Detail
            </button>
          </div>

          <div class="space-y-1 text-xs text-slate-600">
            <p>📍 Lantai 1</p>
            <p>👥 Kapasitas 4–6 orang</p>
            <p>📚 Dekat rak referensi</p>
          </div>

          <div class="pt-2">
            <button
              class="w-full rounded-xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800"
            >
              Pesan
            </button>
          </div>
        </div>
      </article>
    </section>
  </main>
</body>
</html>
