<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Roomify Superadmin - Detail Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">

    <!-- NAV SUPERADMIN / ADMIN UTAMA -->
    <header class="bg-slate-900 text-white">
        <div class="max-w-5xl mx-auto flex items-center justify-between px-4 py-3">
            <div class="font-semibold text-lg tracking-wide">
                Roomify <span class="text-xs opacity-70">Superadmin</span>
            </div>

            <nav class="hidden md:flex items-center gap-6 text-sm">
                <a class="hover:text-slate-200">Dashboard</a>
                <a class="border-b-2 border-white pb-1 font-semibold">Verifikasi Admin</a>
                <a class="hover:text-slate-200">Manajemen User</a>
            </nav>

            <div class="flex items-center gap-2">
                <span class="text-sm hidden sm:block">Superadmin</span>
                <div class="h-8 w-8 rounded-full bg-slate-700"></div>
            </div>
        </div>
    </header>

    <!-- CONTENT -->
    <main class="max-w-3xl mx-auto px-4 py-8 space-y-6">

        <!-- Back -->
        <button class="flex items-center gap-2 text-slate-600 hover:text-slate-900">
            <span class="text-lg">←</span> Kembali
        </button>

        <!-- CARD DETAIL ADMIN -->
        <section class="bg-white rounded-2xl shadow-sm p-6 space-y-6">

            <h1 class="text-xl font-semibold text-slate-900 text-center">
                Detail Data Admin
            </h1>

            <!-- DATA ADMIN -->
            <div class="grid gap-4 text-sm sm:grid-cols-2">

                <div class="space-y-1">
                    <p class="text-xs font-medium text-slate-500">Nama</p>
                    <p class="font-semibold text-slate-800">Rani Putri</p>
                </div>

                <div class="space-y-1">
                    <p class="text-xs font-medium text-slate-500">Email</p>
                    <p class="font-semibold text-slate-800">rani.admin@univ.ac.id</p>
                </div>

                <div class="space-y-1">
                    <p class="text-xs font-medium text-slate-500">ID Admin / NIP</p>
                    <p class="font-semibold text-slate-800">ADM-2025-001</p>
                </div>

                <div class="space-y-1">
                    <p class="text-xs font-medium text-slate-500">No Telepon</p>
                    <p class="font-semibold text-slate-800">0813-2222-4444</p>
                </div>

                <div class="space-y-1">
                    <p class="text-xs font-medium text-slate-500">Role</p>
                    <p class="font-semibold text-slate-800">Admin Perpustakaan</p>
                </div>

                <div class="space-y-1 sm:col-span-2">
                    <p class="text-xs font-medium text-slate-500">Keterangan</p>
                    <p class="text-slate-800">
                        Admin yang bertanggung jawab mengelola peminjaman ruangan dan verifikasi user.
                    </p>
                </div>
            </div>

            <hr class="border-slate-200" />

            <!-- FOTO ADMIN (opsional) -->
            <div class="space-y-2">
                <p class="text-xs font-medium text-slate-500">Foto yang diunggah</p>
                <div class="rounded-xl overflow-hidden border border-slate-200 bg-slate-50">
                    <img
                        src="/path-to-admin-photo.jpg"
                        alt="Foto Admin"
                        class="w-full h-64 object-cover" />
                </div>
                <button class="text-xs text-slate-600 hover:text-slate-900 underline">
                    Buka foto dalam tab baru
                </button>
            </div>

            <!-- BUTTONS -->
            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                <button
                    class="flex-1 rounded-xl bg-red-50 border border-red-200 text-red-700 px-4 py-2 text-sm font-semibold hover:bg-red-100">
                    Tolak
                </button>
                <button
                    class="flex-1 rounded-xl bg-emerald-600 text-white px-4 py-2 text-sm font-semibold hover:bg-emerald-700">
                    Setujui
                </button>
            </div>

        </section>
    </main>
</body>

</html>