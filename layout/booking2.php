<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Roomify - Detail Ruangan</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">
  <!-- NAVBAR -->
  <header class="bg-slate-900 text-white">
    <div class="max-w-6xl mx-auto flex items-center justify-between px-4 py-3">
      <div class="flex items-center gap-2">
        <span class="font-semibold text-lg tracking-wide">Roomify</span>
      </div>
      <div class="flex items-center gap-4">
        <div class="flex items-center gap-2">
          <div class="h-8 w-8 rounded-full bg-slate-700"></div>
          <span class="text-sm">Diandra B.</span>
        </div>
        <button class="inline-flex h-8 w-8 items-center justify-center rounded-full hover:bg-slate-800">
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

  <main class="max-w-6xl mx-auto px-4 py-6 space-y-4">
    <!-- Back -->
    <button class="flex items-center text-sm text-slate-600 hover:text-slate-900">
      <span class="mr-1">&larr;</span> Kembali
    </button>

    <!-- MAIN CONTENT -->
    <section class="grid gap-8 lg:grid-cols-[1.2fr,1fr]">
      <!-- LEFT: IMAGE + RATING -->
      <div class="space-y-4">
        <article class="bg-white rounded-2xl shadow-sm overflow-hidden">
          <div
            class="h-64 sm:h-80 bg-cover bg-center"
            style="background-image:url('https://images.pexels.com/photos/1181408/pexels-photo-1181408.jpeg');"
          ></div>
          <div class="p-4 sm:p-6">
            <div class="flex items-center justify-between gap-4">
              <div>
                <h1 class="text-xl sm:text-2xl font-semibold text-slate-900">
                  Ruangan 1
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                  Ruang diskusi di perpustakaan utama.
                </p>
              </div>
              <!-- Rating bintang -->
              <div class="flex flex-col items-end">
                <span class="text-xs text-slate-400 mb-1">Rating</span>
                <div class="flex">
                  <span class="text-yellow-400 text-xl">★</span>
                  <span class="text-yellow-400 text-xl">★</span>
                  <span class="text-yellow-400 text-xl">★</span>
                  <span class="text-yellow-400 text-xl">★</span>
                  <span class="text-slate-300 text-xl">★</span>
                </div>
              </div>
            </div>
          </div>
        </article>

        <!-- Sekilas aturan & ringkasan -->
        <section class="bg-white rounded-2xl shadow-sm p-4 sm:p-5 space-y-3">
          <h2 class="text-base font-semibold text-slate-900">
            Ringkasan Ruangan
          </h2>
          <div class="grid sm:grid-cols-3 gap-3 text-sm">
            <div class="flex flex-col gap-1">
              <span class="text-slate-500">Jenis</span>
              <span class="font-medium text-slate-900">Ruang Diskusi</span>
            </div>
            <div class="flex flex-col gap-1">
              <span class="text-slate-500">Kapasitas</span>
              <span class="font-medium text-slate-900">5–10 orang</span>
            </div>
            <div class="flex flex-col gap-1">
              <span class="text-slate-500">Lokasi</span>
              <span class="font-medium text-slate-900">Lantai 1 - Sayap Timur</span>
            </div>
          </div>

          <div class="grid sm:grid-cols-2 gap-3 text-sm pt-2 border-t border-slate-100 mt-2">
            <div>
              <h3 class="font-semibold text-slate-900 mb-1 text-sm">Jam Operasional</h3>
              <p class="text-slate-600">
                Senin–Jumat: 08.00–20.00<br />
                Sabtu–Minggu: 09.00–18.00
              </p>
            </div>
            <div>
              <h3 class="font-semibold text-slate-900 mb-1 text-sm">Aturan Singkat</h3>
              <ul class="text-slate-600 space-y-1 list-disc list-inside">
                <li>Dilarang merokok di dalam ruangan.</li>
                <li>Harap menjaga kebersihan & ketenangan.</li>
                <li>Maks. peminjaman 2 jam per sesi.</li>
              </ul>
            </div>
          </div>
        </section>
      </div>

      <!-- RIGHT: DETAILS + FACILITIES + CTA -->
      <aside class="space-y-5">
        <!-- Info utama (kartu kecil seperti di desain awal) -->
        <section class="bg-white rounded-2xl shadow-sm p-4 sm:p-5 space-y-4">
          <h2 class="text-lg font-semibold text-slate-900">Detail Ruangan</h2>

          <div class="grid grid-cols-2 gap-3 text-sm">
            <div class="bg-slate-900 text-white rounded-xl px-3 py-3 flex flex-col justify-center">
              <span class="text-[11px] uppercase tracking-wide text-slate-300">
                Jenis
              </span>
              <span class="font-semibold">Ruang Diskusi</span>
            </div>
            <div class="bg-emerald-600 text-white rounded-xl px-3 py-3 flex flex-col justify-center">
              <span class="text-[11px] uppercase tracking-wide text-emerald-100">
                Status
              </span>
              <span class="font-semibold">Tersedia</span>
            </div>
            <div class="bg-slate-900 text-white rounded-xl px-3 py-3 flex flex-col justify-center">
              <span class="text-[11px] uppercase tracking-wide text-slate-300">
                Kapasitas
              </span>
              <span class="font-semibold">5–10 orang</span>
            </div>
            <div class="bg-slate-900 text-white rounded-xl px-3 py-3 flex flex-col justify-center">
              <span class="text-[11px] uppercase tracking-wide text-slate-300">
                Lantai
              </span>
              <span class="font-semibold">Lantai 1</span>
            </div>
          </div>

          <!-- Info tambahan jadwal ketersediaan singkat -->
          <div class="pt-3 border-t border-slate-100 space-y-2">
            <h3 class="text-sm font-semibold text-slate-900">
              Ketersediaan Hari Ini
            </h3>
            <p class="text-xs text-slate-500">Hijau: tersedia, Merah: terisi.</p>
            <div class="grid grid-cols-3 gap-2 text-xs">
              <div class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-center text-emerald-700">
                08.00–10.00
              </div>
              <div class="rounded-full border border-red-200 bg-red-50 px-3 py-1 text-center text-red-600 line-through">
                10.00–12.00
              </div>
              <div class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-center text-emerald-700">
                13.00–15.00
              </div>
            </div>
          </div>
        </section>

        <!-- Fasilitas -->
        <section class="bg-white rounded-2xl shadow-sm p-4 sm:p-5 space-y-3">
          <h2 class="text-lg font-semibold text-slate-900">
            Fasilitas Ruangan
          </h2>

          <div class="grid grid-cols-2 gap-3 text-sm">
            <div class="flex items-center gap-2">
              <span class="text-xl">🖥️</span>
              <span>TV / Monitor</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="text-xl">📶</span>
              <span>Wi-Fi</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="text-xl">📚</span>
              <span>Akses koleksi dekat</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="text-xl">🪑</span>
              <span>Kursi ergonomis</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="text-xl">📝</span>
              <span>Whiteboard / Spidol</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="text-xl">🔇</span>
              <span>Peredam suara</span>
            </div>
          </div>
        </section>

        <!-- CTA -->
        <section class="bg-white rounded-2xl shadow-sm p-4 flex items-center justify-between gap-3">
          <div class="text-sm">
            <p class="font-semibold text-slate-900">
              Siap memesan ruangan ini?
            </p>
            <p class="text-slate-500">
              Pilih jadwal dan buat kelompok pada langkah berikutnya.
            </p>
          </div>
          <a
            href="#"
            class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-1 whitespace-nowrap"
          >
            Pesan
          </a>
        </section>
      </aside>
    </section>
  </main>
</body>
</html>
