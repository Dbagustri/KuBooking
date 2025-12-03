<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Detail Booking | Kubooking</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#f2f7fc] text-gray-800 flex">

    <?php
    // SIDEBAR
    $sidebarPath = __DIR__ . '/../layout/sidebar.php';
    if (file_exists($sidebarPath)) {
        include $sidebarPath;
    }
    ?>

    <div class="flex-1 flex flex-col h-screen overflow-y-auto">

        <!-- NAVBAR -->
        <div class="m-4">
            <?php
            $navPath = __DIR__ . '/../layout/nav-admin.php';
            if (file_exists($navPath)) {
                include $navPath;
            }
            ?>
        </div>

        <?php
        // Pastikan $booking ada
        $booking = $booking ?? [];

        // =========================
        // Normalisasi data booking
        // =========================

        $kodeBooking = $booking['booking_code'] ?? $booking['kode'] ?? '-';

        $namaPj = $booking['pj_nama']
            ?? $booking['nama_pj']
            ?? $booking['pj']
            ?? '-';

        $nimPj = $booking['pj_nim']
            ?? $booking['pj_nip']
            ?? ($booking['nim_nip'] ?? '');

        $namaRuangan = $booking['nama_ruangan'] ?? $booking['ruang'] ?? '-';
        $tanggal     = $booking['tanggal'] ?? '-';

        $startRaw = $booking['start_time'] ?? null;
        $endRaw   = $booking['end_time']   ?? null;

        $jamMulai   = $startRaw ? date('H:i', strtotime($startRaw)) : '-';
        $jamSelesai = $endRaw   ? date('H:i', strtotime($endRaw))   : '-';

        $jumlahAnggota = $booking['jumlah_anggota'] ?? null;
        $kapMin        = $booking['kapasitas_min'] ?? null;
        $kapMax        = $booking['kapasitas_max'] ?? null;
        $submitted  = (int)($booking['submitted'] ?? 0);
        $statusRaw = strtolower(
            $booking['last_status']
                ?? $booking['status']
                ?? ($submitted === 1 ? 'pending' : 'draft')
        );
        $statusLabel = 'Menunggu Verifikasi';
        $badgeClass  = 'bg-yellow-100 text-yellow-800';

        switch ($statusRaw) {
            case 'approved':
                $statusLabel = 'Disetujui';
                $badgeClass  = 'bg-green-100 text-green-800';
                break;
            case 'rejected':
                $statusLabel = 'Ditolak';
                $badgeClass  = 'bg-red-100 text-red-800';
                break;
            case 'cancelled':
                $statusLabel = 'Dibatalkan';
                $badgeClass  = 'bg-red-100 text-red-800';
                break;
            case 'selesai':
            case 'completed':
                $statusLabel = 'Selesai';
                $badgeClass  = 'bg-blue-100 text-blue-800';
                break;
            case 'ongoing':
                $statusLabel = 'Sedang Berlangsung';
                $badgeClass  = 'bg-emerald-100 text-emerald-800';
                break;
            case 'reschedule_pending':
                $statusLabel = 'Menunggu Reschedule';
                $badgeClass  = 'bg-purple-100 text-purple-800';
                break;
            case 'reschedule_approved':
                $statusLabel = 'Reschedule Disetujui';
                $badgeClass  = 'bg-purple-100 text-purple-800';
                break;
            case 'reschedule_rejected':
                $statusLabel = 'Reschedule Ditolak';
                $badgeClass  = 'bg-red-100 text-red-800';
                break;
            case 'draft':
                $statusLabel = 'Draft Kelompok';
                $badgeClass  = 'bg-gray-100 text-gray-800';
                break;
            case 'pending':
            default:
                $statusLabel = 'Menunggu Verifikasi';
                $badgeClass  = 'bg-yellow-100 text-yellow-800';
                break;
        }

        // =========================
        // Anggota Kelompok
        // =========================

        // dari controller: $members dikirim atau tidak
        $members = isset($members) && is_array($members) ? $members : [];

        // id booking untuk action form
        $idBooking = (int)($booking['id_bookings'] ?? $booking['id'] ?? 0);

        // Status yang membuat booking TIDAK bisa di-approve/reject lagi
        $lockedStatuses = [
            'approved',
            'reschedule_approved',
            'rejected',
            'cancelled',
            'selesai',
            'completed',
            'reschedule_pending', // penting supaya tidak bentrok dengan flow reschedule
        ];
        ?>

        <div class="px-8 pb-10 space-y-6">

            <!-- HEADER + BACK -->
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-[#1e3a5f]">Detail Booking</h1>

                <a href="index.php?controller=adminBooking&action=manage"
                    class="px-4 py-2 rounded-lg bg-gray-300 hover:bg-gray-400 text-gray-800 text-sm font-semibold">
                    Kembali
                </a>
            </div>

            <!-- KARTU DETAIL -->
            <div class="bg-white shadow rounded-lg p-6 space-y-6">

                <!-- INFO UTAMA -->
                <div>
                    <h2 class="text-xl font-semibold mb-4">Informasi Booking</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">

                        <!-- KODE -->
                        <div>
                            <p class="text-gray-500 text-xs">Kode Booking</p>
                            <p class="font-mono font-semibold">
                                <?= htmlspecialchars($kodeBooking, ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>

                        <!-- PJ -->
                        <div>
                            <p class="text-gray-500 text-xs">Penanggung Jawab</p>
                            <p class="font-medium">
                                <?= htmlspecialchars($namaPj, ENT_QUOTES, 'UTF-8') ?>
                                <?php if ($nimPj !== ''): ?>
                                    (<?= htmlspecialchars($nimPj, ENT_QUOTES, 'UTF-8') ?>)
                                <?php endif; ?>
                            </p>
                        </div>

                        <!-- RUANGAN -->
                        <div>
                            <p class="text-gray-500 text-xs">Ruangan</p>
                            <p class="font-medium">
                                <?= htmlspecialchars($namaRuangan, ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>

                        <!-- TANGGAL -->
                        <div>
                            <p class="text-gray-500 text-xs">Tanggal</p>
                            <p class="font-medium">
                                <?= htmlspecialchars($tanggal, ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>

                        <!-- WAKTU -->
                        <div>
                            <p class="text-gray-500 text-xs">Waktu</p>
                            <p class="font-medium">
                                <?= htmlspecialchars($jamMulai, ENT_QUOTES, 'UTF-8') ?>
                                –
                                <?= htmlspecialchars($jamSelesai, ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>

                        <!-- STATUS -->
                        <div>
                            <p class="text-gray-500 text-xs">Status Saat Ini</p>
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[11px] font-semibold <?= $badgeClass ?>">
                                <?= htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </div>

                        <!-- KAPASITAS RUANGAN -->
                        <div>
                            <p class="text-gray-500 text-xs">Kapasitas Ruangan</p>
                            <p class="font-medium">
                                <?php if ($kapMin !== null && $kapMax !== null): ?>
                                    <?= (int)$kapMin ?> - <?= (int)$kapMax ?> orang
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </p>
                        </div>

                        <!-- JUMLAH ANGGOTA -->
                        <div>
                            <p class="text-gray-500 text-xs">Jumlah Anggota</p>
                            <p class="font-medium">
                                <?= $jumlahAnggota !== null ? (int)$jumlahAnggota : '-' ?>
                            </p>
                        </div>

                    </div>
                </div>

                <!-- ANGGOTA KELOMPOK -->
                <div>
                    <h2 class="text-xl font-semibold mb-4">Anggota Kelompok</h2>

                    <?php if (!empty($members)): ?>
                        <table class="min-w-full text-sm bg-white border rounded">
                            <thead>
                                <tr class="bg-gray-100 text-left">
                                    <th class="px-3 py-2">Nama</th>
                                    <th class="px-3 py-2">NIM/NIP</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($members as $m): ?>
                                    <?php
                                    $namaM = $m['nama'] ?? ($m['nama_anggota'] ?? '-');
                                    $nimM  = $m['nim']
                                        ?? $m['nip']
                                        ?? ($m['nim_nip'] ?? '-');
                                    ?>
                                    <tr class="border-t">
                                        <td class="px-3 py-2"><?= htmlspecialchars($namaM, ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="px-3 py-2"><?= htmlspecialchars($nimM, ENT_QUOTES, 'UTF-8') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <!-- fallback: tampilkan PJ sebagai satu-satunya anggota -->
                        <table class="min-w-full text-sm bg-white border rounded">
                            <thead>
                                <tr class="bg-gray-100 text-left">
                                    <th class="px-3 py-2">Nama</th>
                                    <th class="px-3 py-2">NIM/NIP</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="px-3 py-2"><?= htmlspecialchars($namaPj, ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="px-3 py-2"><?= htmlspecialchars($nimPj !== '' ? $nimPj : '-', ENT_QUOTES, 'UTF-8') ?></td>
                                </tr>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>

                <!-- AKSI ADMIN (APPROVE / REJECT BOOKING UTAMA) -->
                <div class="pt-6 border-t flex flex-wrap gap-3">

                    <?php if ($idBooking > 0 && !in_array($statusRaw, $lockedStatuses, true)): ?>
                        <form action="index.php?controller=adminBooking&action=approve"
                            method="POST">
                            <input type="hidden" name="id_booking" value="<?= $idBooking ?>">
                            <button type="submit"
                                class="px-5 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm font-semibold">
                                Setujui Booking
                            </button>
                        </form>

                        <form action="index.php?controller=adminBooking&action=reject"
                            method="POST"
                            onsubmit="return confirm('Yakin ingin menolak booking ini?');">
                            <input type="hidden" name="id_booking" value="<?= $idBooking ?>">
                            <input type="hidden" name="alasan" value="">
                            <button type="submit"
                                class="px-5 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-semibold">
                                Tolak Booking
                            </button>
                        </form>
                    <?php endif; ?>

                </div>

            </div>

        </div>
    </div>

</body>

</html>