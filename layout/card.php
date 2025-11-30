<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Room Card Component</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100 p-10">

  <!-- Container untuk menampung semua card -->
  <div id="roomContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></div>

  <script>
    // === Komponen Room Card ===
    function createRoomCard({
      nama,
      lantai,
      kapasitas,
      jenis,
      status,
      gambar
    }) {
      const isPenuh = status.toLowerCase() === "penuh";
      const statusColor = isPenuh ? "bg-red-100 text-red-800" : "bg-green-100 text-green-800";
      const buttonClass = isPenuh
        ? "flex-1 bg-gray-400 text-white py-2 px-4 rounded-lg text-sm font-medium cursor-not-allowed"
        : "flex-1 bg-[#274269] hover:bg-[#1f3553] text-white py-2 px-4 rounded-lg text-sm font-medium transition duration-200";
      const buttonText = isPenuh ? "Penuh" : "Pesan";

      return `
        <div class="room-card bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200 hover:shadow-md transition">
          <!-- Gambar Ruangan -->
          <img src="${gambar}" alt="${nama}" class="w-full h-48 object-cover">
          
          <!-- Isi Card -->
          <div class="p-5">
            <div class="flex justify-between items-start">
              <h3 class="text-lg font-bold text-gray-800">${nama}</h3>
              <span class="px-2 py-1 ${statusColor} text-xs rounded-full">${status}</span>
            </div>

            <div class="mt-4 space-y-1 text-sm text-gray-600">
              <div class="flex items-center">
                <i class="fas fa-layer-group mr-2"></i>
                <span>Lantai: ${lantai}</span>
              </div>
              <div class="flex items-center">
                <i class="fas fa-users mr-2"></i>
                <span>Kapasitas: ${kapasitas}</span>
              </div>
              <div class="flex items-center">
                <i class="fas fa-tag mr-2"></i>
                <span>Jenis: ${jenis}</span>
              </div>
            </div>

            <div class="mt-5 flex space-x-2">
              <button class="${buttonClass}" ${isPenuh ? "disabled" : ""}>${buttonText}</button>
              <button class="bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-4 rounded-lg text-sm font-medium transition duration-200">
                Detail
              </button>
            </div>
          </div>
        </div>
      `;
    }

    // === Contoh data dinamis ===
    const rooms = [
      {
        nama: "Ruang Pertama",
        lantai: 1,
        kapasitas: "4-6 Orang",
        jenis: "Ruang Diskusi",
        status: "Tersedia",
        gambar: "https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80"
      },
      {
        nama: "Ruang Kedua",
        lantai: 2,
        kapasitas: "6-8 Orang",
        jenis: "Ruang Belajar",
        status: "Penuh",
        gambar: "https://images.unsplash.com/photo-1598300056393-4a4e88d1b6d6?auto=format&fit=crop&w=800&q=80"
      },
      {
        nama: "Ruang Ketiga",
        lantai: 3,
        kapasitas: "10 Orang",
        jenis: "Ruang Seminar",
        status: "Tersedia",
        gambar: "https://images.unsplash.com/photo-1593642632559-0c6d3fc62b89?auto=format&fit=crop&w=800&q=80"
      }
    ];

    // === Render ke halaman ===
    const container = document.getElementById("roomContainer");
    rooms.forEach(room => {
      container.innerHTML += createRoomCard(room);
    });
  </script>

</body>
</html>

<!-- container.innerHTML += createRoomCard({
  nama: "Ruang Keempat",
  lantai: 2,
  kapasitas: "8 Orang",
  jenis: "Ruang Meeting",
  status: "Tersedia",
  gambar: "https://images.unsplash.com/photo-1616628188850-27b8b8f2dc7c?auto=format&fit=crop&w=800&q=80"
}); -->
