<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Roomify - Profil</title>
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
        <a href="#" class="pb-1 border-b-2 border-transparent hover:border-slate-200">
          Beranda
        </a>
        <a href="#" class="pb-1 border-b-2 border-transparent hover:border-slate-200">
          Riwayat
        </a>
        <a href="#" class="pb-1 border-b-2 border-white font-semibold">
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
  <main class="max-w-6xl mx-auto px-4 py-6 space-y-6">
    <!-- Judul halaman -->
    <section class="flex items-center justify-between gap-2">
      <div>
        <h1 class="text-2xl font-semibold text-slate-900">Profil Saya</h1>
      </div>
    </section>

    <!-- GRID PROFIL -->
    <section class="grid gap-6 lg:grid-cols-[1.2fr,1fr]">
      <!-- KIRI: CARD PROFIL & FORM -->
      <section class="bg-white rounded-2xl shadow-sm p-5 space-y-5">
        <!-- Header profil -->
        <div class="flex items-center gap-4">
          <div class="relative">
            <div class="h-20 w-20 rounded-full bg-slate-300"></div>
            <button
              class="absolute bottom-0 right-0 h-7 w-7 rounded-full bg-slate-900 text-white text-xs flex items-center justify-center shadow"
            >
              ✎
            </button>
          </div>
          <div>
            <h2 class="text-lg font-semibold text-slate-900">
              Diandra Bagustri
            </h2>
            <p class="text-sm text-slate-500">diandra@student.univ.ac.id</p>
            <p class="text-xs text-slate-400 mt-1">
              Terdaftar sebagai mahasiswa &amp; peminjam aktif.
            </p>
          </div>
        </div>

        <hr class="border-slate-100" />

        <!-- Form data diri -->
        <form class="grid gap-4 sm:grid-cols-2">
          <div class="sm:col-span-1">
            <label class="block text-xs font-medium text-slate-600 mb-1">
              Nama Lengkap
            </label>
            <input
              type="text"
              value="Diandra Bagustri"
              class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm
                     focus:border-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-300"
            />
          </div>

          <div class="sm:col-span-1">
            <label class="block text-xs font-medium text-slate-600 mb-1">
              NIM
            </label>
            <input
              type="text"
              value="21.11.0001"
              class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm
                     focus:border-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-300"
            />
          </div>

          <div class="sm:col-span-1">
            <label class="block text-xs font-medium text-slate-600 mb-1">
              Jurusan
            </label>
            <input
              type="text"
              value="Teknik Informatika & Komputer"
              class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm
                     focus:border-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-300"
            />
          </div>

          <div class="sm:col-span-1">
            <label class="block text-xs font-medium text-slate-600 mb-1">
              Angkatan
            </label>
            <input
              type="text"
              value="2021"
              class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm
                     focus:border-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-300"
            />
          </div>

          <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-slate-600 mb-1">
              Email
            </label>
            <input
              type="email"
              value="diandra@student.univ.ac.id"
              class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm
                     focus:border-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-300"
            />
          </div>

          <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-slate-600 mb-1">
              No. HP
            </label>
            <input
              type="tel"
              placeholder="+62..."
              class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm
                     focus:border-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-300"
            />
          </div>
        </form>
      </section>

      <!-- KANAN: PENGATURAN AKUN -->
      <section class="space-y-4">
        <!-- Kartu pengaturan -->
        <div class="bg-white rounded-2xl shadow-sm p-5 space-y-3">
          <h3 class="text-sm font-semibold text-slate-900">
            Pengaturan Akun
          </h3>
          <p class="text-xs text-slate-500 mb-2">
            Atur preferensi notifikasi, keamanan, dan aktivitas peminjamanmu.
          </p>

          <div class="space-y-2">
            <!-- item -->
            <button
              class="w-full flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2 text-sm text-slate-800 hover:bg-slate-100"
            >
              <span>Edit Profil</span>
              <span class="text-lg text-slate-400">&rsaquo;</span>
            </button>

            <button
              class="w-full flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2 text-sm text-slate-800 hover:bg-slate-100"
            >
              <span>Notifikasi</span>
              <span class="text-lg text-slate-400">&rsaquo;</span>
            </button>

            <button
              class="w-full flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2 text-sm text-slate-800 hover:bg-slate-100"
            >
              <span>Riwayat Peminjaman</span>
              <span class="text-lg text-slate-400">&rsaquo;</span>
            </button>

            <button
              class="w-full flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2 text-sm text-slate-800 hover:bg-slate-100"
            >
              <span>Ganti Password</span>
              <span class="text-lg text-slate-400">&rsaquo;</span>
            </button>
          </div>
        </div>

        <!-- Kartu keamanan / logout -->
        <div class="bg-white rounded-2xl shadow-sm p-5 space-y-3">
            <h3 class="text-sm font-semibold text-slate-900">
                Keamanan &amp; Keluar
            </h3>
            <p class="text-xs text-slate-500">
                Keluar dari akun Roomify pada perangkat ini.
            </p>

            <button
                class="w-full rounded-xl bg-red-50 px-4 py-2 text-sm font-semibold text-red-600 border border-red-100 hover:bg-red-100"
            >
                Logout
            </button>
            </div>
      </section>
    </section>
  </main>
</body>
</html>
