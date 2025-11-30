<?php
function createRoomCard($nama, $lantai, $kapasitas, $jenis, $gambar, $linkDetail) {
  return "
    <div class='bg-gray-200 rounded-3xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 w-[380px]'>
      <div class='h-56 bg-cover bg-center' style='background-image: url($gambar);'></div>
      <div class='p-6 flex flex-col justify-between h-64'>
        <div>
          <div class='flex justify-between items-center mb-4'>
            <h2 class='text-2xl font-semibold text-black'>$nama</h2>
            <a href='$linkDetail' class='text-blue-600 text-base font-medium hover:underline'>
              View Detail
            </a>
          </div>
          <p class='text-gray-700 text-base mb-1'>Lantai : $lantai</p>
          <p class='text-gray-700 text-base mb-1'>Kapasitas : $kapasitas</p>
          <p class='text-gray-700 text-base'>Jenis : $jenis</p>
        </div>
        
        <a href='d5.php' 
           class='block text-center bg-[#273C5A] text-white w-full py-3 mt-5 rounded-xl text-lg font-medium hover:bg-[#1e2f45] transition'>
          Pesan
        </a>
      </div>
    </div>
  ";
}

function renderRoomCards($rooms) {
  foreach ($rooms as $room) {
    echo createRoomCard(
      $room['nama'],
      $room['lantai'],
      $room['kapasitas'],
      $room['jenis'],
      $room['gambar'],
      $room['linkDetail']
    );
  }
}
?>
