<?php
// app/views/layout/roomCard.php

if (!function_exists('renderRoomCards')) {

  function renderRoomCards(array $rooms, bool $buttonDisabled = false)
  {
    foreach ($rooms as $room):
      // handle foto_ruangan biar gak notice kalau kosong
      $foto = !empty($room['foto_ruangan'])
        ? $room['foto_ruangan']
        : 'img/default-room.jpg';
?>

      <div class="bg-white rounded-2xl shadow hover:shadow-lg transition
                        overflow-hidden flex flex-col">

        <img src="<?= htmlspecialchars($foto) ?>"
          alt="Ruangan <?= htmlspecialchars($room['nama_ruangan'] ?? 'Ruangan') ?>"
          class="h-40 w-full object-cover">

        <div class="p-4 flex flex-col gap-1 flex-1">

          <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold text-slate-900">
              <?= htmlspecialchars($room['nama_ruangan'] ?? 'Ruangan') ?>
            </h3>

            <a href="index.php?controller=user&action=detailroom&id=<?= (int)($room['id_ruangan'] ?? 0) ?>"
              class="text-blue-600 text-sm hover:underline">
              Detail
            </a>
          </div>

          <p class="text-sm text-slate-600">
            <?= htmlspecialchars($room['kategori'] ?? '-') ?>
          </p>

          <p class="text-xs text-slate-500">
            Kapasitas:
            <?= (int)($room['kapasitas_min'] ?? 0) ?>–<?= (int)($room['kapasitas_max'] ?? 0) ?> orang
          </p>

          <div class="mt-3 pt-3 border-t border-slate-200 flex justify-end">
            <?php if ($buttonDisabled): ?>
              <span class="px-4 py-2 rounded-lg bg-slate-200 text-slate-500 text-sm cursor-not-allowed">
                Pesan
              </span>
            <?php else: ?>
              <a href="index.php?controller=userBooking&action=booking&id=<?= (int)($room['id_ruangan'] ?? 0) ?>"
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
