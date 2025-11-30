<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width= <div class="bg-white rounded-xl shadow-2xl p-6 md:p-8 w-full max-w-6xl">

    <title>Document</title>
</head>
<body>
    <?php require '../layout/navbars.php'; ?>

                        <a href="d2.php" class="inline-block text-gray-500 hover:text-gray-700 mb-4 mt-3 mx-10 px-5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </a>

     <main class="flex justify-center pb-12 px-8 sm:px-10 lg:px-10 mx-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <div>                    
                    <img src="rapat.png" alt="Ruang Rapat 1" class="w-full h-100 object-cover  mb-4">
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                    </p>
                </div>
                
                <div class="space-y-5">
                    <h1 class="text-3xl font-bold text-gray-800">Ruangan 1</h1>
                    
                    <p class="text-gray-700"><span class="font-medium">Jenis:</span> Ruang Diskusi</p>
                    <p class="text-gray-700"><span class="font-medium">Lantai:</span> 1</p>
                    <p class="text-gray-700"><span class="font-medium">Kapasitas:</span> 3-5</p>

                    <form action="#" class="space-y-4">
                        <div>
                            <label for="tanggal" class="block text-sm font-medium text-gray-700">Pilih tanggal</label>
                            <input type="date" id="tanggal" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="jam_mulai" class="block text-sm font-medium text-gray-700">Pilih jam mulai</label>
                            <select id="jam_mulai" class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option>Pilih jam</option>
                                <option>08.00</option>
                                <option>09.00</option>
                                <option>10.00</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="durasi" class="block text-sm font-medium text-gray-700">Pilih Durasi</label>
                            <select id="durasi" class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option>Pilih durasi</option>
                                <option>1 Jam</option>
                                <option>2 Jam</option>
                                <option>3 Jam</option>
                            </select>
                        </div>
                    </form>

                    <div>
                        <h2 class="text-lg font-medium text-gray-800 mb-2">Jam tersedia</h2>
                        <div class="grid grid-cols-4 gap-2">
                            <?php 
                            $jam = ['08.00', '09.00', '10.00', '11.00', '12.00', '13.00', '14.00', '15.00', '16.00', '17.00', '18.00', '19.00'];
                            foreach ($jam as $j): ?>
                                <button class="bg-gray-200 text-gray-700 py-2 px-3 rounded-md text-sm hover:bg-gray-300 focus:bg-blue-600 focus:text-white focus:outline-none">
                                    <?php echo $j; ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <p class="text-sm font-medium text-gray-800 !mt-6">
                        Waktu Booking: 08.00 - 10.00
                    </p>
                    
<a href="d6.php" 
   class="block text-center w-full bg-[#1e3a5f] text-white py-3 rounded-md font-semibold hover:bg-blue-900 transition duration-300">
  Buat Kelompok
</a>

                </div>
            </div>
        </div>
     </main>
</body>
</html>