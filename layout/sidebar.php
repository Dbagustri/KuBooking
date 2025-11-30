<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="w-56 min-h-screen bg-[#274269] text-white flex flex-col">
  <div class="py-6 px-4 bg-[#1f3552]">
    <h1 class="text-lg font-semibold">KoBooking</h1>
  </div>

  <nav class="flex-1">
    <ul>
      <li class="<?= $current_page == 'dashboard.php' ? 'bg-[#396b9c]' : '' ?>">
        <a href="dashboard.php" class="block px-6 py-3 font-medium">Dashboard</a>
      </li>
      <li class="<?= $current_page == 'verifikasi.php' ? 'bg-[#396b9c]' : '' ?>">
        <a href="verifikasi.php" class="block px-6 py-3 hover:bg-[#396b9c]">Verifikasi User</a>
      </li>
      <li class="<?= $current_page == 'booking.php' ? 'bg-[#396b9c]' : '' ?>">
        <a href="booking.php" class="block px-6 py-3 hover:bg-[#396b9c]">Kelola Booking</a>
      </li>
      <li class="<?= $current_page == 'ruangan.php' ? 'bg-[#396b9c]' : '' ?>">
        <a href="ruangan.php" class="block px-6 py-3 hover:bg-[#396b9c]">Ruangan</a>
      </li>
      <li class="<?= $current_page == 'anggota.php' ? 'bg-[#396b9c]' : '' ?>">
        <a href="anggota.php" class="block px-6 py-3 hover:bg-[#396b9c]">Anggota</a>
      </li>
      <li class="<?= $current_page == 'laporan.php' ? 'bg-[#396b9c]' : '' ?>">
        <a href="laporan.php" class="block px-6 py-3 hover:bg-[#396b9c]">Laporan</a>
      </li>
      <li class="<?= $current_page == 'laporan.php' ? 'bg-[#396b9c]' : '' ?>">
        <a href="admin.php" class="block px-6 py-3 hover:bg-[#396b9c]">Admin</a>
      </li>
    </ul>
  </nav>
</div>
