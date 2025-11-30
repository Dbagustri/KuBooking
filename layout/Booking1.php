<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Roomify - Booking Ruangan</title>
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
          <span class="text-sm">Dhanda B.</span>
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
    <section class="flex flex-col lg:flex-row items-start gap-6">
      <!-- LEFT COLUMN -->
      <div class="lg:w-1/2 space-y-4">
        <!-- Room Card -->
        <article class="bg-white rounded-2xl shadow-sm overflow-hidden">
          <div
            class="h-48 bg-cover bg-center"
            style="background-image: url('https://images.pexels.com/photos/1181408/pexels-photo-1181408.jpeg');"
          ></div>
          <div class="p-4 sm:p-6 space-y-3">
            <div>
              <h1 class="text-xl font-semibold text-slate-900">Ruangan 1</h1>
              <p class="text-sm text-slate-500 mt-1">Jenis: Ruang Diskusi</p>
            </div>

            <dl class="grid grid-cols-2 gap-4 text-sm">
              <div>
                <dt class="text-slate-500">Lantai</dt>
                <dd class="font-medium text-slate-900">1</dd>
              </div>
              <div>
                <dt class="text-slate-500">Kapasitas</dt>
                <dd class="font-medium text-slate-900">3 – 5 orang</dd>
              </div>
            </dl>
          </div>
        </article>

        <!-- GROUP SECTION -->
        <section class="bg-white rounded-2xl shadow-sm p-4 sm:p-5 space-y-4" id="group-section">
          <!-- STATE AWAL: belum ada kelompok -->
          <div id="group-empty" class="space-y-3">
            <h2 class="text-base font-semibold text-slate-900">
              Kelompok
            </h2>
            <p class="text-sm text-slate-500">
              Belum ada kelompok untuk ruangan ini.
            </p>
            <button
              id="btn-buat-kelompok"
              class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-1"
            >
              Buat Kelompok
            </button>
          </div>

          <!-- STATE SETELAH BUAT KELOMPOK -->
          <div id="group-detail" class="space-y-4 hidden">
            <header class="flex items-center justify-between">
              <div>
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">
                  Kode Kelompok
                </h2>
                <p class="text-lg font-semibold text-slate-900">heiuF83</p>
              </div>
              <div class="flex flex-col items-end text-right text-sm">
                <span class="text-slate-500">Anggota</span>
                <span class="font-semibold text-slate-900">5 orang</span>
              </div>
            </header>

            <div class="border-t border-slate-100 pt-3 space-y-2">
              <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                Anggota Kelompok
              </p>

              <div class="space-y-2">
                <!-- USER 1 = PJ (tidak bisa dihapus) -->
                <div class="flex items-center justify-between bg-slate-50 rounded-xl px-3 py-2">
                  <div class="flex items-center gap-2 text-sm">
                    <div class="h-7 w-7 rounded-full bg-slate-200"></div>
                    <div class="flex flex-col">
                      <span>User 1</span>
                      <span class="text-[11px] uppercase tracking-wide text-emerald-600 font-semibold">
                        PJ
                      </span>
                    </div>
                  </div>
                </div>

                <!-- User lain jika masih bisa dihapus -->
                <div class="flex items-center justify-between bg-slate-50 rounded-xl px-3 py-2">
                  <div class="flex items-center gap-2 text-sm">
                    <div class="h-7 w-7 rounded-full bg-slate-200"></div>
                    <span>User 2</span>
                  </div>
                  <button class="text-xs text-slate-400 hover:text-red-500">✕</button>
                </div>

                <div class="flex items-center justify-between bg-slate-50 rounded-xl px-3 py-2">
                  <div class="flex items-center gap-2 text-sm">
                    <div class="h-7 w-7 rounded-full bg-slate-200"></div>
                    <span>User 3</span>
                  </div>
                  <button class="text-xs text-slate-400 hover:text-red-500">✕</button>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>

      <!-- RIGHT COLUMN -->
      <section class="lg:w-1/2 bg-white rounded-2xl shadow-sm p-4 sm:p-6 space-y-5">
        <h2 class="text-lg font-semibold text-slate-900 mb-1">Booking Ruangan</h2>

        <div class="grid gap-4 sm:grid-cols-2">
          <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1">
              Pilih tanggal
            </label>
            <input
              type="date"
              class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm focus:border-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-300"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">
              Pilih jam mulai
            </label>
            <select
              class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm focus:border-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-300"
            >
              <option>08.00</option>
              <option>09.00</option>
              <option>10.00</option>
              <option>11.00</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">
              Pilih durasi
            </label>
            <select
              class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm focus:border-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-300"
            >
              <option>1 jam</option>
              <option>2 jam</option>
              <option>3 jam</option>
            </select>
          </div>
        </div>

        <!-- Jam tersedia -->
        <div class="space-y-2">
          <div class="flex items-center justify-between">
            <label class="block text-sm font-medium text-slate-700">
              Jam tersedia
            </label>
            <span class="text-xs text-slate-400">
              Hijau: tersedia, Merah: penuh
            </span>
          </div>

          <div class="grid grid-cols-4 gap-2 text-xs sm:text-sm">
            <div class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-center text-emerald-700">
              08.00
            </div>
            <div class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-center text-emerald-700">
              08.30
            </div>

            <div class="rounded-full border border-red-200 bg-red-50 px-3 py-1 text-center text-red-600">
              09.00
            </div>

            <div class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-center text-emerald-700">
              09.30
            </div>
            <div class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-center text-emerald-700">
              10.00
            </div>

            <div class="rounded-full border border-red-200 bg-red-50 px-3 py-1 text-center text-red-600">
              10.30
            </div>

            <div class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-center text-emerald-700">
              11.00
            </div>
            <div class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-center text-emerald-700">
              11.30
            </div>
          </div>
        </div>

        <div class="space-y-1">
          <p class="text-sm font-medium text-slate-700">
            Waktu Booking
          </p>
          <div
            class="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50 px-3 py-2 text-sm"
          >
            <span class="text-slate-500">Terpilih</span>
            <span class="font-semibold text-slate-900">08.00 – 10.00</span>
          </div>
        </div>

        <div class="pt-2">
          <button
            class="w-full rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-1"
          >
            Buat Kelompok &amp; Booking
          </button>
        </div>
      </section>
    </section>
  </main>

  <!-- JS Toggle -->
  <script>
    const btn = document.getElementById('btn-buat-kelompok');
    const emptyState = document.getElementById('group-empty');
    const detailState = document.getElementById('group-detail');

    if (btn) {
      btn.addEventListener('click', () => {
        emptyState.classList.add('hidden');
        detailState.classList.remove('hidden');
      });
    }
  </script>

</body>
</html>
