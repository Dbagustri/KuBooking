<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kubooking</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#1e3a5f] min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-5xl bg-white rounded-2xl shadow-2xl overflow-hidden grid md:grid-cols-2">

        <!-- Bagian kiri: Form login -->
        <div class="p-10 md:p-16 bg-gray-200">
            <a href="d1.php" class="text-gray-500 hover:text-gray-700 inline-flex items-center">
                <svg class="w-6 h-6 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
            
            <h1 class="text-4xl font-bold text-gray-800 mt-6">Selamat Datang!</h1>
            <p class="text-gray-700 mb-8 text-lg">Masuk untuk melanjutkan ke Kubooking</p>

            <form action="#" method="POST" class="space-y-6">
                <div>
                    <input type="text" placeholder="Masukan NIM/NPM" class="w-full px-5 py-4 text-lg border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <input type="password" placeholder="Password" class="w-full px-5 py-4 text-lg border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="flex items-center space-x-4">
                    <span class="bg-[#1e3a5f] text-white text-lg font-bold px-6 py-3 rounded-lg select-none">
                        Mv659
                    </span>
                    <input type="text" placeholder="Kode CAPTCHA" class="w-full px-5 py-4 text-lg border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
<a href="d2.php" 
   class="block text-center w-full bg-[#1e3a5f] text-white py-4 text-lg rounded-lg font-semibold hover:bg-blue-700 transition duration-300">
  Login
</a>

            </form>

            <p class="text-center text-sm text-gray-700 mt-8">
                Belum punya akun? 
                <a href="d4.php" class="text-blue-600 font-medium hover:underline">
                    Daftar Sekarang
                </a>
            </p>
        </div>

        <!-- Bagian kanan: Gambar -->
        <div class="hidden md:block">
            <img src="rapat.png" alt="Ilustrasi Ruangan Rapat" class="w-full h-full object-cover">
        </div>

    </div>

</body>
</html>
