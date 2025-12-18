<?php
// app/views/layout/navbar.php

use App\Core\Auth;

$userSession = Auth::user();

// untuk tandai link aktif
$currentController = $_GET['controller'] ?? '';
$currentAction     = $_GET['action'] ?? '';

function navLink(string $label, string $href, bool $active = false)
{
  $base   = 'px-3 text-xs md:text-sm';
  $class  = $base . ' hover:underline';
  if ($active) {
    $class .= ' font-semibold';
  }

  echo '<a href="' . $href . '" class="' . $class . '">' . htmlspecialchars($label) . '</a>';
}

?>
<nav class="bg-[#274269] text-white shadow-md">
  <div class="max-w-7xl mx-auto px-4 md:px-8 py-3 flex items-center justify-between">

    <!-- KIRI: BRAND -->
    <?php
    // default home
    if ($userSession) {
      if (in_array($userSession['role'], ['admin', 'super_admin'])) {
        $homeUrl = "index.php?controller=admin&action=home";
      } else {
        $homeUrl = "index.php?controller=userBooking&action=home";
      }
    } else {
      $homeUrl = "index.php?controller=auth&action=landing";
    }
    ?>
    <a href="<?= $homeUrl ?>" class="text-xl md:text-2xl font-bold">
      KuBooking
    </a>

    <!-- TENGAH: MENU -->
    <?php if ($userSession): ?>
      <div class="hidden md:flex items-center gap-6 text-[11px] md:text-xs">
        <?php if (!in_array($userSession['role'], ['admin', 'super_admin'])): ?>
          <?php
          navLink(
            'Beranda',
            'index.php?controller=userBooking&action=home',
            $currentController === 'userBooking' && $currentAction === 'home'
          );
          navLink(
            'Riwayat',
            'index.php?controller=userBooking&action=riwayat',
            $currentController === 'userBooking' && $currentAction === 'riwayat'
          );
          navLink(
            'Profil',
            'index.php?controller=userBooking&action=profil',
            $currentController === 'userBooking' && $currentAction === 'profil'
          );
          ?>
        <?php else: ?>
          <?php
          navLink(
            'Beranda',
            'index.php?controller=admin&action=home',
            $currentController === 'admin' && $currentAction === 'home'
          );
          navLink(
            'Verifikasi',
            'index.php?controller=admin&action=verifikasiUser',
            $currentController === 'admin' && $currentAction === 'verifikasiUser'
          );
          navLink(
            'Ruangan',
            'index.php?controller=admin&action=ruangan',
            $currentController === 'admin' && $currentAction === 'ruangan'
          );
          navLink(
            'Anggota',
            'index.php?controller=admin&action=anggota',
            $currentController === 'admin' && $currentAction === 'anggota'
          );
          navLink(
            'Laporan',
            'index.php?controller=admin&action=laporan',
            $currentController === 'admin' && $currentAction === 'laporan'
          );
          ?>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <!-- KANAN: AUTH BUTTONS -->
    <div class="flex items-center gap-4 text-xs md:text-sm">
      <?php if ($userSession): ?>
        <a href="index.php?controller=auth&action=logout"
          class="font-semibold hover:underline">
          Logout
        </a>
      <?php else: ?>
        <a href="index.php?controller=auth&action=login"
          class="hover:underline">
          Login
        </a>
        <a href="index.php?controller=auth&action=register"
          class="hover:underline">
          Register
        </a>
      <?php endif; ?>
    </div>

  </div>
</nav>