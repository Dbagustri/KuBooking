<?php
// app/views/layout/roomCard.php

if (!function_exists('renderRoomCards')) {

  /**
   * Render daftar kartu ruangan
   *
   * @param array $rooms          list ruangan (tiap elemen: nama_ruangan, foto_ruangan, kapasitas_min, kapasitas_max, id_ruangan, kategori, dll)
   * @param bool  $buttonDisabled kalau true, tombol "Pesan" akan disabled
   */
  function renderRoomCards(array $rooms, bool $buttonDisabled = false): void
  {
    foreach ($rooms as $room):
      // handle foto_ruangan biar gak notice kalau kosong
      $foto = !empty($room['foto_ruangan'])
        ? $room['foto_ruangan']
        : 'img/rapat.png'; // samain default dengan view booking
      $idRuangan = (int)($room['id_ruangan'] ?? 0);
      $nama      = $room['nama_ruangan'] ?? 'Ruangan';
      $kategori  = $room['kategori'] ?? '-';
      $capMin    = (int)($room['kapasitas_min'] ?? 0);
      $capMax    = (int)($room['kapasitas_max'] ?? 0);
?>
      <div class="bg-white rounded-2xl shadow hover:shadow-lg transition
                        overflow-hidden flex flex-col">

        <img src="<?= htmlspecialchars($foto, ENT_QUOTES, 'UTF-8') ?>"
          alt="Ruangan <?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8') ?>"
          class="h-40 w-full object-cover">

        <div class="p-4 flex flex-col gap-1 flex-1">

          <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold text-slate-900">
              <?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8') ?>
            </h3>

            <a href="index.php?controller=user&action=detailroom&id=<?= (int)($room['id_ruangan'] ?? 0) ?>"
              class="text-blue-600 text-sm hover:underline">
              Detail
            </a>
          </div>

          <p class="text-sm text-slate-600">
            <?= htmlspecialchars($kategori, ENT_QUOTES, 'UTF-8') ?>
          </p>

          <p class="text-xs text-slate-500">
            Kapasitas:
            <?= $capMin ?>–<?= $capMax ?> orang
          </p>

          <div class="mt-3 pt-3 border-t border-slate-200 flex justify-end">
            <?php if ($buttonDisabled): ?>
              <span class="px-4 py-2 rounded-lg bg-slate-200 text-slate-500 text-sm cursor-not-allowed">
                Pesan
              </span>
            <?php else: ?>
              <a href="index.php?controller=userBooking&action=booking&id_ruangan=<?= $idRuangan ?>"
                class="px-4 py-2 rounded-lg bg-[#1e3a5f] text-white text-sm hover:bg-[#274269]">
                Pesan
              </a>
            <?php endif; ?>
          </div>

        </div>
      </div>
<?php
    endforeach;
  }
}
