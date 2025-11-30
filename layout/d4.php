<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Akun Baru - Kubooking</title>
    <script src="https://cdn.tailwindcss.com"></script>
    

</head>
<body class="bg-[#1e3a5f] min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-4xl bg-white rounded-xl shadow-2xl overflow-hidden grid md:grid-cols-2">

        <div class="p-8 md:p-12">
            <a href="login.php" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            
            <h1 class="text-3xl font-bold text-gray-800 mt-4">Buat Akun Baru</h1>
            <p class="text-gray-600 mb-6">Daftar untuk mulai menggunakan Kubooking</p>

            <form action="#" method="POST" class="space-y-4">
                <input type="text" placeholder="Nama Lengkap" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input type="email" placeholder="Email" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input type="text" placeholder="NIM/NPM" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input type="text" placeholder="No Telp" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input type="password" placeholder="Password" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input type="password" placeholder="Ulangi Password" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload Kuitansi</label>
                    <input type="file" class="w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-md file:border-0
                        file:text-sm file:font-semibold
                        file:bg-blue-50 file:text-blue-700
                        hover:file:bg-blue-100
                    "/>
                </div>

                <button type="submit" href="d3.php" class="w-full bg-[#1e3a5f] text-white py-3 rounded-md font-semibold hover:bg-blue-700 transition duration-300 !mt-6">
                    Register
                </button>
            </form>

            <p class="text-center text-sm text-gray-600 mt-6">
                Sudah punya akun? 
                <a href="d3.php" class="text-blue-600 font-medium hover:underline">
                    Login Sekarang
                </a>
            </p>
        </div>

        <div class="hidden md:block">
            <img src="rapat.png" alt="Ilustrasi Ruangan Rapat" class="w-full h-full object-cover">
        </div>

    </div>

</body>
</html>