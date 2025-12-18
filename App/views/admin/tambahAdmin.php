<?php
$old   = $old   ?? [];
$error = $error ?? null;

$nama         = $old['nama']          ?? '';
$email        = $old['email']         ?? '';
$nim_nip      = $old['nim_nip']       ?? '';
$role         = $old['role']          ?? 'admin';
$status_aktif = $old['status_aktif']  ?? 'aktif';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Admin | Kubooking</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/kubooking/public/src/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="bg-[#f2f7fc] text-gray-800 flex">

    <?php
    // SIDEBAR
    $sidebarPath = __DIR__ . '/../layout/sidebar.php';
    if (file_exists($sidebarPath)) {
        include $sidebarPath;
    }
    ?>

    <div class="flex-1 flex flex-col h-screen overflow-y-auto">
        <?php
        // FLASH MESSAGE
        $flashPath = __DIR__ . '/../layout/flash.php';
        if (file_exists($flashPath)) {
            include $flashPath;
        }
        ?>

        <!-- NAVBAR -->
        <div class="m-4">
            <?php
            $navPath = __DIR__ . '/../layout/nav-admin.php';
            if (file_exists($navPath)) {
                include $navPath;
            }
            ?>
        </div>

        <div class="px-4 sm:px-8 pb-10 max-w-3xl mx-auto w-full space-y-6">
            <!-- HEADER -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-[#1e3a5f]">Tambah Admin</h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Tambahkan akun baru untuk admin atau super admin.
                    </p>
                </div>

                <a href="index.php?controller=superAdmin&action=kelolaAdmin"
                    class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium 
                          border border-slate-300 text-slate-600 bg-white hover:bg-slate-50 shadow-sm">
                    Kembali
                </a>
            </div>

            <?php if (!empty($error)): ?>
                <div class="bg-red-100 text-red-800 px-4 py-3 rounded-md text-sm">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="bg-white shadow rounded-xl p-6 sm:p-8">
                <form action="index.php?controller=superAdmin&action=storeAdmin" method="POST" class="space-y-4">
                    <!-- NAMA -->
                    <div>
                        <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Lengkap *
                        </label>
                        <input
                            id="nama"
                            type="text"
                            name="nama"
                            value="<?= htmlspecialchars($nama) ?>"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required>
                    </div>

                    <!-- EMAIL -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            Email *
                        </label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="<?= htmlspecialchars($email) ?>"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required>
                    </div>

                    <!-- NIM / NIP -->
                    <div>
                        <label for="nim_nip" class="block text-sm font-medium text-gray-700 mb-1">
                            NIM / NIP *
                        </label>
                        <input
                            id="nim_nip"
                            type="text"
                            name="nim_nip"
                            value="<?= htmlspecialchars($nim_nip) ?>"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required>
                    </div>

                    <!-- ROLE ADMIN -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">
                            Role *
                        </label>
                        <select
                            id="role"
                            name="role"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required>
                            <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="super_admin" <?= $role === 'super_admin' ? 'selected' : '' ?>>Super Admin</option>
                        </select>
                    </div>

                    <!-- PASSWORD -->
                    <div class="grid sm:grid-cols-2 gap-4">
                        <!-- PASSWORD 1 -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                Password *
                            </label>

                            <div class="relative">
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-md 
                                           focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required minlength="8">

                                <button type="button"
                                    onclick="togglePassword('password', 'icon-pass')"
                                    class="absolute inset-y-0 right-3 flex items-center text-gray-500 hover:text-gray-700">
                                    <i id="icon-pass" class="fa-solid fa-eye text-lg"></i>
                                </button>
                            </div>

                            <p class="text-xs text-gray-500 mt-1">
                                Minimal 8 karakter, kombinasi huruf dan angka.
                            </p>
                        </div>

                        <!-- PASSWORD 2 -->
                        <div>
                            <label for="password2" class="block text-sm font-medium text-gray-700 mb-1">
                                Ulangi Password *
                            </label>

                            <div class="relative">
                                <input
                                    id="password2"
                                    type="password"
                                    name="password2"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-md 
                                           focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required>

                                <button type="button"
                                    onclick="togglePassword('password2', 'icon-pass2')"
                                    class="absolute inset-y-0 right-3 flex items-center text-gray-500 hover:text-gray-700">
                                    <i id="icon-pass2" class="fa-solid fa-eye text-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- STATUS AKUN -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Status Akun
                        </label>
                        <div class="flex gap-4 text-sm">
                            <label class="inline-flex items-center gap-2">
                                <input type="radio" name="status_aktif" value="aktif"
                                    <?= $status_aktif === 'aktif' ? 'checked' : '' ?>>
                                <span>Aktif</span>
                            </label>
                            <label class="inline-flex items-center gap-2">
                                <input type="radio" name="status_aktif" value="nonaktif"
                                    <?= $status_aktif === 'nonaktif' ? 'checked' : '' ?>>
                                <span>Nonaktif</span>
                            </label>
                        </div>
                    </div>

                    <!-- SUBMIT -->
                    <div class="pt-4">
                        <button type="submit"
                            class="w-full sm:w-auto px-6 py-2.5 rounded-md bg-[#1e3a5f] text-white text-sm font-semibold hover:bg-[#163052]">
                            Simpan Admin
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>
</body>

</html>