<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Roomify - Riwayat Peminjaman</title>
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

    <!-- TITLE -->
    <section class="space-y-1">
      <h1 class="text-2xl font-semibold text-slate-900">
        Riwayat Peminjaman
      </h1>
      <p class="text-sm text-slate-500">
        Daftar semua peminjaman ruangan yang pernah kamu lakukan.
      </p>
    </section>

    <!-- FILTER BAR -->
    <section class="bg-white rounded-2xl shadow-sm p-4 sm:p-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <!-- Search -->
      <div class="w-full sm:w-1/2 flex items-center gap-2">
        <label for="search" class="text-sm text-slate-600 whitespace-nowrap">
          Cari
        </label>
        <div class="relative flex-1">
          <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">🔍</span>
          <input
            id="search"
            type="text"
            placeholder="Ruangan / kode booking..."
            class="w-full rounded-xl border border-slate-200 bg-slate-50 pl-8 pr-3 py-2 text-sm focus:border-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-300"
          />
        </div>
      </div>

      <!-- Filters -->
      <div class="w-full sm:w-auto flex flex-col sm:flex-row gap-2 sm:items-center">
        <div class="flex items-center gap-2">
          <label class="text-sm text-slate-600 whitespace-nowrap">Status</label>
          <select
            class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm focus:border-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-300"
          >
            <option>Semua</option>
            <option>Disetujui</option>
            <option>Pending</option>
            <option>Selesai</option>
            <option>Ditolak</option>
            <option>Dibatalkan</option>
          </select>
        </div>
        <div class="flex items-center gap-2">
          <label class="text-sm text-slate-600 whitespace-nowrap">Periode</label>
          <select
            class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm focus:border-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-300"
          >
            <option>30 hari terakhir</option>
            <option>3 bulan terakhir</option>
            <option>Semua</option>
          </select>
        </div>
      </div>
    </section>

    <!-- TABLE CARD -->
    <section class="bg-white rounded-2xl shadow-sm p-4 sm:p-5">
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="border-b border-slate-200 bg-slate-50">
              <th class="py-3 px-3 text-left font-semibold text-slate-600">Ruangan</th>
              <th class="py-3 px-3 text-left font-semibold text-slate-600">Tanggal &amp; Waktu</th>
              <th class="py-3 px-3 text-center font-semibold text-slate-600">Durasi</th>
              <th class="py-3 px-3 text-center font-semibold text-slate-600">Status</th>
              <th class="py-3 px-3 text-center font-semibold text-slate-600">Kode</th>
              <th class="py-3 px-3 text-center font-semibold text-slate-600">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <!-- Row 1 -->
            <tr class="hover:bg-slate-50">
              <td class="py-3 px-3">
                <p class="font-medium text-slate-900">Ruang Diskusi 1</p>
                <p class="text-xs text-slate-500">Lantai 1</p>
              </td>
              <td class="py-3 px-3">
                <p class="text-slate-800">15 Mar 2025</p>
                <p class="text-xs text-slate-500">10.00 – 12.00</p>
              </td>
              <td class="py-3 px-3 text-center">
                2 jam
              </td>
              <td class="py-3 px-3 text-center">
                <span class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">
                  ✔ Disetujui
                </span>
              </td>
              <td class="py-3 px-3 text-center">
                <span class="text-xs font-mono text-slate-700">H56A9W</span>
              </td>
              <td class="py-3 px-3 text-center">
                <div class="inline-flex gap-2">
                  <button class="rounded-full border border-slate-200 px-3 py-1 text-xs font-medium text-slate-700 hover:bg-slate-50">
                    Detail
                  </button>
                  <button class="rounded-full border border-red-200 bg-red-50 px-3 py-1 text-xs font-medium text-red-600 hover:bg-red-100">
                    Batalkan
                  </button>
                </div>
              </td>
            </tr>

            <!-- Row 2 -->
            <tr class="hover:bg-slate-50">
              <td class="py-3 px-3">
                <p class="font-medium text-slate-900">Ruang Diskusi 2</p>
                <p class="text-xs text-slate-500">Lantai 2</p>
              </td>
              <td class="py-3 px-3">
                <p class="text-slate-800">12 Mar 2025</p>
                <p class="text-xs text-slate-500">13.00 – 15.00</p>
              </td>
              <td class="py-3 px-3 text-center">
                2 jam
              </td>
              <td class="py-3 px-3 text-center">
                <span class="inline-flex items-center rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700">
                  ⏳ Pending
                </span>
              </td>
              <td class="py-3 px-3 text-center">
                <span class="text-xs font-mono text-slate-400">-</span>
              </td>
              <td class="py-3 px-3 text-center">
                <div class="inline-flex gap-2">
                  <button class="rounded-full border border-slate-200 px-3 py-1 text-xs font-medium text-slate-700 hover:bg-slate-50">
                    Detail
                  </button>
                  <button class="rounded-full border border-red-200 bg-red-50 px-3 py-1 text-xs font-medium text-red-600 hover:bg-red-100">
                    Batalkan
                  </button>
                </div>
              </td>
            </tr>

            <!-- Row 3 -->
            <tr class="hover:bg-slate-50">
              <td class="py-3 px-3">
                <p class="font-medium text-slate-900">Ruang Diskusi 1</p>
                <p class="text-xs text-slate-500">Lantai 1</p>
              </td>
              <td class="py-3 px-3">
                <p class="text-slate-800">8 Mar 2025</p>
                <p class="text-xs text-slate-500">09.00 – 12.00</p>
              </td>
              <td class="py-3 px-3 text-center">
                3 jam
              </td>
              <td class="py-3 px-3 text-center">
                <span class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-medium text-slate-700">
                  ✓ Selesai
                </span>
              </td>
              <td class="py-3 px-3 text-center">
                <span class="text-xs font-mono text-slate-700">X9F20B</span>
              </td>
              <td class="py-3 px-3 text-center">
                <button class="rounded-full border border-slate-200 px-3 py-1 text-xs font-medium text-slate-700 hover:bg-slate-50">
                  Detail
                </button>
              </td>
            </tr>

            <!-- Row 4 -->
            <tr class="hover:bg-slate-50">
              <td class="py-3 px-3">
                <p class="font-medium text-slate-900">Ruang Diskusi 3</p>
                <p class="text-xs text-slate-500">Lantai 3</p>
              </td>
              <td class="py-3 px-3">
                <p class="text-slate-800">1 Mar 2025</p>
                <p class="text-xs text-slate-500">14.00 – 16.00</p>
              </td>
              <td class="py-3 px-3 text-center">
                2 jam
              </td>
              <td class="py-3 px-3 text-center">
                <span class="inline-flex items-center rounded-full border border-red-200 bg-red-50 px-3 py-1 text-xs font-medium text-red-600">
                  ✖ Ditolak
                </span>
              </td>
              <td class="py-3 px-3 text-center">
                <span class="text-xs font-mono text-slate-400">-</span>
              </td>
              <td class="py-3 px-3 text-center">
                <button class="rounded-full border border-slate-200 px-3 py-1 text-xs font-medium text-slate-700 hover:bg-slate-50">
                  Detail
                </button>
              </td>
            </tr>

            <!-- Tambah row lain sesuai kebutuhan -->
          </tbody>
        </table>
      </div>

      <!-- FOOTER TABLE / PAGINATION -->
      <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-500">
        <p>Menampilkan 1–4 dari 12 peminjaman.</p>
        <div class="inline-flex items-center gap-1">
          <button class="px-2 py-1 rounded-lg border border-slate-200 hover:bg-slate-50">
            ‹
          </button>
          <button class="px-2.5 py-1 rounded-lg bg-slate-900 text-white">
            1
          </button>
          <button class="px-2.5 py-1 rounded-lg border border-slate-200 hover:bg-slate-50">
            2
          </button>
          <button class="px-2.5 py-1 rounded-lg border border-slate-200 hover:bg-slate-50">
            3
          </button>
          <button class="px-2 py-1 rounded-lg border border-slate-200 hover:bg-slate-50">
            ›
          </button>
        </div>
      </div>
    </section>
  </main>
</body>
</html>
