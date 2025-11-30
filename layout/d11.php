<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password | Roomify</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#274269] min-h-screen font-sans">
  
<?php require '../layout/navbars.php'; ?>
    <a href="d8.php" class="inline-block text-gray-500 hover:text-gray-700 px-50 mx-10 px-0 mt-3 mb-5">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
    </a>
  <!-- Container utama -->
  <div class="flex justify-center items-center ">
  <div class="w-full max-w-md bg-[#f3f9fc] rounded-2xl shadow-lg p-10 text-center">
    <h1 class="text-2xl font-bold my-12 text-black">Reset Password</h1>

    <form action="#" method="POST" class="text-left space-y-4">
      <!-- Password Sekarang -->
      <div>
        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Password Sekarang</label>
        <input type="password" id="current_password" name="current_password" required 
               class="w-full border border-gray-300 rounded-md px-5 py-2 focus:outline-none focus:ring-2 focus:ring-[#274269]">
      </div>

      <!-- Password Baru -->
      <div>
        <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
        <input type="password" id="new_password" name="new_password" required 
               class="w-full border border-gray-300 rounded-md px-5 py-2 focus:outline-none focus:ring-2 focus:ring-[#274269]">
      </div>

      <!-- Ulang Password Baru -->
      <div>
        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Ulang Password Baru</label>
        <input type="password" id="confirm_password" name="confirm_password" required 
               class="w-full border border-gray-300 rounded-md px-5 py-2 focus:outline-none focus:ring-2 focus:ring-[#274269]">
      </div>

      <!-- Tombol Edit -->
      <div class="pt-4">
        <button type="submit" 
                class="w-full py-2 rounded-md text-white bg-[#274269] hover:bg-[#1f3554] transition duration-200 font-semibold">
          Edit
        </button>
      </div>
    </form>
  </div>
</div>

</body>
</html>
