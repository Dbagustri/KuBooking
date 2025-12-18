<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Ganti Password - Kubooking</title>
    <link rel="stylesheet" href="/kubooking/public/src/output.css">
    <link rel="stylesheet" href="/kubooking/public/src/output.css">
</head>

<body class="bg-[#1e3a5f] min-h-screen flex items-center justify-center p-4">
    <?php
    $flashPath = __DIR__ . '/../layout/flash.php';
    if (file_exists($flashPath)) {
        include $flashPath;
    }
    ?>
    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="p-8">

            <a href="index.php?controller=userBooking&action=profil"
                class="text-gray-500 hover:text-gray-700 inline-flex items-center text-sm">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali ke Profil
            </a>

            <h1 class="text-2xl font-bold text-gray-800 mt-4 mb-2">Ganti Password</h1>
            <p class="text-sm text-gray-600 mb-4">
                Untuk keamanan akun, gunakan password yang kuat dan mudah diingat.
            </p>

            <?php if (!empty($error)): ?>
                <div class="mb-4 bg-red-100 text-red-700 px-4 py-2 rounded">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="mb-4 bg-green-100 text-green-700 px-4 py-2 rounded">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <form action="index.php?controller=auth&action=gantiPassword" method="POST" class="space-y-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Password Lama
                    </label>
                    <input
                        type="password"
                        name="password_lama"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg 
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Password Baru
                    </label>
                    <input
                        type="password"
                        name="password_baru"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg 
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                    <p class="text-xs text-gray-500 mt-1">
                        Minimal 6 karakter.
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Ulangi Password Baru
                    </label>
                    <input
                        type="password"
                        name="password_baru2"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg 
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <button
                    type="submit"
                    class="w-full bg-[#1e3a5f] text-white py-3 rounded-lg font-semibold 
                           hover:bg-blue-700 transition duration-300">
                    Simpan Password
                </button>
            </form>
        </div>
    </div>

</body>

</html>