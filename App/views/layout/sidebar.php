<?php
$current_page = basename($_SERVER['PHP_SELF']);

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Ambil role dari session
$userRole = $_SESSION['user']['role'] ?? ($_SESSION['role'] ?? null);
?>

<div class="w-56 min-h-screen bg-[#274269] text-white flex flex-col">

  <div class="py-6 px-4 bg-[#1f3552]">
    <h1 class="text-lg font-semibold">KoBooking</h1>
  </div>

  <nav class="flex-1">
    <ul>

      <!-- DASHBOARD -->
      <li class="<?= ($_GET['action'] ?? '') === 'home' ? 'bg-[#396b9c]' : '' ?>">
        <a href="index.php?controller=admin&action=home" class="block px-6 py-3">Dashboard</a>
      </li>

      <!-- VERIFIKASI USER -->
      <li class="<?= ($_GET['action'] ?? '') === 'verifikasiUser' ? 'bg-[#396b9c]' : '' ?>">
        <a href="index.php?controller=admin&action=verifikasiUser" class="block px-6 py-3">Verifikasi User</a>
      </li>

      <!-- KELOLA BOOKING -->
      <li class="<?= ($_GET['controller'] ?? '') === 'adminBooking' && ($_GET['action'] ?? '') === 'manage' ? 'bg-[#396b9c]' : '' ?>">
        <a href="index.php?controller=adminBooking&action=manage" class="block px-6 py-3">Kelola Booking</a>
      </li>

      <!-- RUANGAN -->
      <li class="<?= ($_GET['action'] ?? '') === 'ruangan' ? 'bg-[#396b9c]' : '' ?>">
        <a href="index.php?controller=admin&action=ruangan" class="block px-6 py-3">Ruangan</a>
      </li>

      <!-- ANGGOTA -->
      <li class="<?= ($_GET['action'] ?? '') === 'anggota' ? 'bg-[#396b9c]' : '' ?>">
        <a href="index.php?controller=admin&action=anggota" class="block px-6 py-3">Anggota</a>
      </li>
      <!-- feedback -->
      <li class="<?= ($_GET['controller'] ?? '') === 'userFeedback' ? 'bg-[#396b9c]' : '' ?>">
        <a href="index.php?controller=userFeedback&action=adminIndex" class="block px-6 py-3">Feedback</a>
      </li>

      <!-- LAPORAN -->
      <li class="<?= ($_GET['action'] ?? '') === 'laporan' ? 'bg-[#396b9c]' : '' ?>">
        <a href="index.php?controller=admin&action=laporan" class="block px-6 py-3">Laporan</a>
      </li>

      <!-- ADMIN (ONLY SUPER ADMIN) -->
      <?php if ($userRole === 'super_admin'): ?>
        <li class="<?= ($_GET['action'] ?? '') === 'kelolaAdmin' ? 'bg-[#396b9c]' : '' ?>">
          <a href="index.php?controller=admin&action=kelolaAdmin" class="block px-6 py-3">Admin</a>
        </li>
      <?php endif; ?>

    </ul>
  </nav>
</div>