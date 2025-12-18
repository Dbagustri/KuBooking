<?php
// Nonaktifkan cache supaya ketika user tekan Back, browser tidak pakai halaman lama
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kubooking</title>
    <link rel="stylesheet" href="/kubooking/public/src/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="bg-[#1e3a5f] min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-5xl bg-white rounded-2xl shadow-2xl overflow-hidden grid md:grid-cols-2">

        <div class="p-10 md:p-16 bg-gray-200">
            <a href="index.php?controller=auth&action=landing" class="text-gray-500 hover:text-gray-700 inline-flex items-center">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali
            </a>

            <h1 class="text-4xl font-bold text-gray-800 mt-6">Selamat Datang!</h1>
            <p class="text-gray-700 mb-4 text-lg">Masuk untuk melanjutkan ke Kubooking</p>

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

            <?php if (!empty($_SESSION['flash_message'])): ?>
                <div class="mb-4 px-4 py-3 rounded 
                    <?= $_SESSION['flash_message']['type'] === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                    <?= htmlspecialchars($_SESSION['flash_message']['text']) ?>
                </div>
                <?php unset($_SESSION['flash_message']); ?>
            <?php endif; ?>

            <form action="index.php?controller=auth&action=loginProcess" method="POST" class="space-y-6">
                <div>
                    <input
                        type="text"
                        name="npm"
                        placeholder="Masukan NIM/NIP"
                        class="w-full px-5 py-4 text-lg border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <div>
                    <div class="relative">
                        <input
                            id="passwordLogin"
                            type="password"
                            name="password"
                            placeholder="Password"
                            class="w-full px-5 py-4 text-lg border border-gray-300 rounded-lg pr-12 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required>
                        <button
                            type="button"
                            class="absolute inset-y-0 right-4 flex items-center text-gray-500"
                            data-toggle-password="passwordLogin">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- CAPTCHA -->
                <div class="space-y-2">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                        <!-- Gambar + tombol refresh -->
                        <div class="flex items-center gap-2 sm:w-auto shrink-0">
                            <img
                                src="captcha.php?rand=<?= time() ?>"
                                alt="CAPTCHA"
                                class="h-12 rounded-lg border border-gray-300 bg-white"
                                id="captchaImage">
                            <button
                                type="button"
                                class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100"
                                onclick="refreshCaptcha()">
                                <i class="fa-solid fa-arrows-rotate"></i>
                            </button>
                        </div>

                        <!-- Input kode captcha -->
                        <input
                            type="text"
                            name="captcha"
                            placeholder="Kode CAPTCHA"
                            class="w-full sm:flex-1 px-5 py-4 text-lg border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            autocomplete="off"
                            required>
                    </div>
                </div>


                <button
                    type="submit"
                    class="block text-center w-full bg-[#1e3a5f] text-white py-4 text-lg rounded-lg font-semibold hover:bg-blue-700 transition duration-300">
                    Login
                </button>
            </form>

            <p class="text-center text-sm text-gray-700 mt-8">
                Belum punya akun?
                <a href="index.php?controller=auth&action=register&role=mahasiswa" class="text-blue-600 font-medium hover:underline">
                    Daftar Sekarang
                </a>
            </p>
        </div>

        <div class="hidden md:block">
            <img src="img/rapat.png" alt="Ilustrasi Ruangan Rapat" class="w-full h-full object-cover">
        </div>

    </div>

    <script>
        // Toggle show / hide password
        const toggleButtons = document.querySelectorAll('[data-toggle-password]');

        function refreshCaptcha() {
            const img = document.getElementById('captchaImage');
            if (!img) return;
            img.src = 'captcha.php?rand=' + Date.now();
        }
        toggleButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const targetId = btn.getAttribute('data-toggle-password');
                const input = document.getElementById(targetId);
                const icon = btn.querySelector('i');
                if (!input || !icon) return;

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
    </script>

</body>

</html>