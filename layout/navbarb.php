<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Navbar Belum Login</title>
  <script src="https://cdn.tailwindcss.com"></script>

  <style type="text/tailwindcss">
    @layer components {
      /* Tombol outline putih */
      .btn-outline {
        @apply border border-white text-white px-4 py-2 rounded-md 
               hover:bg-white hover:text-[#274269] transition;
      }

      /* Tombol putih solid */
      .btn-solid {
        @apply bg-white text-[#274269] px-4 py-2 rounded-md font-semibold 
               hover:bg-gray-200 transition;
      }
    }
  </style>
</head>

<body class="bg-white">

  <!-- ================= NAVBAR (BELUM LOGIN) ================= -->
  <nav class="bg-[#274269] text-white px-8 py-4 flex justify-between items-center shadow-md">
    <h1 class="text-2xl font-bold">Roomify</h1>

    <!-- Tombol kanan -->
    <div class="flex gap-4">
      <a href="d17.php" class="btn-outline">Eksternal</a>
      <a href="d3.php" class="btn-outline">Login</a>
      <a href="d4.php" class="btn-solid">Register</a>
    </div>
  </nav>

</body>
</html>
