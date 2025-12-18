<?php

/** @var array $booking */
/** @var array $reschedule */
/** @var array $newMembers */

$oldDate   = $booking['tanggal'] ?? $reschedule['old_tanggal'] ?? null;
$oldStart  = $reschedule['old_start_time'] ?? $booking['start_time'] ?? null;
$oldEnd    = $reschedule['old_end_time']   ?? $booking['end_time']   ?? null;
$roomName  = $booking['nama_ruangan'] ?? $reschedule['nama_ruangan'] ?? '-';
$lokasi    = $booking['lokasi'] ?? $reschedule['lokasi'] ?? '-';
$pjNama    = $booking['pj_nama'] ?? '-';

$oldDateLabel  = $oldDate  ? date('d M Y', strtotime($oldDate))   : '-';
$oldStartLabel = $oldStart ? date('H:i',   strtotime($oldStart))  : '-';
$oldEndLabel   = $oldEnd   ? date('H:i',   strtotime($oldEnd))    : '-';

$newDate   = $reschedule['new_tanggal'] ?? null;
$newStart  = $reschedule['new_start_time'] ?? null;
$newEnd    = $reschedule['new_end_time'] ?? null;
$newDateLabel  = $newDate  ? date('d M Y', strtotime($newDate))   : '-';
$newStartLabel = $newStart ? date('H:i',   strtotime($newStart))  : '-';
$newEndLabel   = $newEnd   ? date('H:i',   strtotime($newEnd))    : '-';

$alasanReschedule = $reschedule['alasan'] ?? ($booking['alasan_reschedule'] ?? '');
$bookingMembers   = $booking['members'] ?? [];
$idReschedule     = (int)($reschedule['id_reschedule'] ?? 0);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Proses Reschedule | Kubooking</title>
    <link rel="stylesheet" href="/kubooking/public/src/output.css">
</head>

<body class="bg-[#f2f7fc] text-gray-800 flex">

    <?php
    $sidebarPath = __DIR__ . '/../layout/sidebar.php';
    if (file_exists($sidebarPath)) {
        include $sidebarPath;
    }
    ?>

    <div class="flex-1 flex flex-col h-screen overflow-y-auto">
        <?php
        $flashPath = __DIR__ . '/../layout/flash.php';
        if (file_exists($flashPath)) {
            include $flashPath;
        }
        ?>
        <div class="m-4">
            <?php
            $navPath = __DIR__ . '/../layout/nav-admin.php';
            if (file_exists($navPath)) {
                include $navPath;
            }
            ?>
        </div>

        <main class="px-8 pb-10 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-[#1e3a5f]">Proses Reschedule</h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Review jadwal lama & jadwal baru, lalu setujui atau tolak permintaan reschedule.
                    </p>
                </div>

                <a href="index.php?controller=adminBooking&action=home"
                    class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium 
                          border border-slate-300 text-slate-600 bg-white hover:bg-slate-50 shadow-sm">
                    Kembali
                </a>
            </div>

            <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- KARTU JADWAL LAMA -->
                <article class="bg-white rounded-2xl shadow p-5 space-y-4">
                    <header>
                        <h2 class="text-lg font-semibold text-slate-900">Jadwal Lama</h2>
                        <p class="text-xs text-slate-500 mt-1">
                            Detail peminjaman sebelum reschedule.
                        </p>
                    </header>

                    <dl class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="text-slate-500">Ruangan</dt>
                            <dd class="font-medium text-slate-900">
                                <?= htmlspecialchars($roomName) ?>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Lokasi</dt>
                            <dd class="font-medium text-slate-900">
                                <?= htmlspecialchars($lokasi) ?>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Penanggung Jawab</dt>
                            <dd class="font-medium text-slate-900">
                                <?= htmlspecialchars($pjNama) ?>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Kode Booking</dt>
                            <dd class="font-medium text-slate-900">
                                <?= htmlspecialchars($booking['booking_code'] ?? '-') ?>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Tanggal</dt>
                            <dd class="font-medium text-slate-900">
                                <?= $oldDateLabel ?>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Waktu</dt>
                            <dd class="font-medium text-slate-900">
                                <?= $oldStartLabel ?> – <?= $oldEndLabel ?>
                            </dd>
                        </div>
                    </dl>

                    <div class="pt-3 border-t border-slate-100">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">
                            Anggota Lama
                        </p>
                        <?php if (!empty($bookingMembers)): ?>
                            <ul class="space-y-1 text-sm">
                                <?php foreach ($bookingMembers as $m): ?>
                                    <li class="flex items-center justify-between">
                                        <span><?= htmlspecialchars($m['nama'] ?? '-') ?></span>
                                        <?php if (!empty($booking['id_pj']) && $m['id_user'] == $booking['id_pj']): ?>
                                            <span class="text-[11px] px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700">
                                                PJ
                                            </span>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-sm text-slate-500">
                                Tidak ada data anggota lama.
                            </p>
                        <?php endif; ?>
                    </div>
                </article>

                <!-- KARTU JADWAL BARU -->
                <article class="bg-white rounded-2xl shadow p-5 space-y-4">
                    <header>
                        <h2 class="text-lg font-semibold text-slate-900">Jadwal Baru (Reschedule)</h2>
                        <p class="text-xs text-slate-500 mt-1">
                            Jadwal yang diajukan oleh PJ sebagai pengganti jadwal lama.
                        </p>
                    </header>

                    <dl class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="text-slate-500">Tanggal Baru</dt>
                            <dd class="font-medium text-slate-900">
                                <?= $newDateLabel ?>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Waktu Baru</dt>
                            <dd class="font-medium text-slate-900">
                                <?= $newStartLabel ?> – <?= $newEndLabel ?>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Kapasitas Min / Max</dt>
                            <dd class="font-medium text-slate-900">
                                <?= (int)($reschedule['kapasitas_min'] ?? $booking['kapasitas_min'] ?? 0) ?>
                                –
                                <?= (int)($reschedule['kapasitas_max'] ?? $booking['kapasitas_max'] ?? 0) ?>
                                orang
                            </dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Total Anggota Baru</dt>
                            <dd class="font-medium text-slate-900">
                                <?= count($newMembers) ?> orang
                            </dd>
                        </div>
                    </dl>

                    <!-- Alasan -->
                    <div class="space-y-1">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                            Alasan Reschedule
                        </p>
                        <?php if ($alasanReschedule): ?>
                            <p class="text-sm text-slate-800 bg-slate-50 rounded-lg px-3 py-2 whitespace-pre-line">
                                <?= nl2br(htmlspecialchars($alasanReschedule)) ?>
                            </p>
                        <?php else: ?>
                            <p class="text-sm text-slate-500">
                                Tidak ada alasan yang diisi oleh PJ.
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Anggota baru -->
                    <div class="pt-3 border-t border-slate-100">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">
                            Anggota Jadwal Baru
                        </p>
                        <?php if (!empty($newMembers)): ?>
                            <ul class="space-y-1 text-sm">
                                <?php foreach ($newMembers as $m): ?>
                                    <li class="flex items-center justify-between">
                                        <span><?= htmlspecialchars($m['nama'] ?? '-') ?></span>
                                        <?php if (!empty($booking['id_pj']) && $m['id_user'] == $booking['id_pj']): ?>
                                            <span class="text-[11px] px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700">
                                                PJ
                                            </span>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-sm text-slate-500">
                                Belum ada anggota yang terdaftar di jadwal baru.
                            </p>
                        <?php endif; ?>
                    </div>
                </article>
            </section>

            <!-- FORM APPROVE / REJECT -->
            <section class="bg-white rounded-2xl shadow p-5 space-y-4">
                <h2 class="text-lg font-semibold text-slate-900">Keputusan Admin</h2>
                <p class="text-xs text-slate-500">
                    Setelah diperiksa, pilih apakah permintaan reschedule ini akan disetujui atau ditolak.
                </p>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-start">
                    <!-- APPROVE -->
                    <div class="space-y-2">
                        <form action="index.php?controller=adminBooking&action=approveReschedule"
                            method="POST"
                            onsubmit="return confirm('Setujui reschedule dan update jadwal booking utama?');">
                            <input type="hidden" name="id_reschedule" value="<?= $idReschedule ?>">
                            <button type="submit"
                                class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-lg
                                       bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700
                                       focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-1">
                                Setujui Reschedule
                            </button>
                        </form>
                    </div>

                    <!-- REJECT -->
                    <div class="lg:col-span-2 space-y-2">
                        <form action="index.php?controller=adminBooking&action=rejectReschedule"
                            method="POST">
                            <input type="hidden" name="id_reschedule" value="<?= $idReschedule ?>">

                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                Alasan Penolakan (Opsional)
                            </label>
                            <textarea name="alasan_reject"
                                rows="3"
                                class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm
                                         focus:border-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-300"
                                placeholder="Contoh: Jadwal perpustakaan penuh pada waktu tersebut, silakan ajukan jadwal lain."></textarea>

                            <div class="mt-2">
                                <button type="submit"
                                    class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg
                                           bg-red-500 text-white text-sm font-semibold hover:bg-red-600
                                           focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-1">
                                    Tolak Reschedule
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </main>
    </div>

</body>

</html>