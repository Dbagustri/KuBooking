<?php

/**
 * layout/pagination.php
 * - Reusable pagination component
 * - Support multiple page keys: page, booking_page, user_page, dll
 * - Current page diprioritaskan dari URL agar tidak "stuck"
 */

if (!isset($pagination) || !is_array($pagination)) return;

// Ambil param utama dulu (urutannya penting!)
$params  = $pagination['params'] ?? [];
$pageKey = $pagination['pageKey'] ?? 'page';

$totalPages = max(1, (int)($pagination['totalPages'] ?? 1));

// ✅ KUNCI: currentPage prioritas dari URL sesuai pageKey, fallback dari pagination[]
$currentPage = max(1, (int)($_GET[$pageKey] ?? ($pagination['currentPage'] ?? 1)));

// Clamp biar aman
if ($currentPage > $totalPages) $currentPage = $totalPages;

if ($totalPages <= 1) return;

// Helper URL builder (sekali saja)
if (!function_exists('kubooking_pagination_build_url')) {
    function kubooking_pagination_build_url(array $params, string $pageKey, int $page): string
    {
        $params[$pageKey] = $page;
        return 'index.php?' . http_build_query($params);
    }
}

$makeUrl = fn($p) => htmlspecialchars(kubooking_pagination_build_url($params, $pageKey, (int)$p), ENT_QUOTES, 'UTF-8');

// ====== 3 angka tengah: [current-1, current, current+1] ======
$start = max(1, $currentPage - 1);
$end   = min($totalPages, $currentPage + 1);

// Kalau di awal, paksa 1..3
if ($currentPage === 1) {
    $start = 1;
    $end   = min($totalPages, 3);
}

// Kalau di akhir, paksa last-2..last
if ($currentPage === $totalPages) {
    $end   = $totalPages;
    $start = max(1, $totalPages - 2);
}

// Styles
$btnBase   = "inline-flex items-center justify-center min-w-10 h-10 px-3 rounded-lg text-sm border transition";
$btnNormal = "bg-white text-slate-700 border-slate-200 hover:bg-slate-50 hover:border-slate-300";
$btnActive = "bg-[#1e3a5f] text-white border-[#1e3a5f] shadow-sm";
$btnDis    = "bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed";
?>

<div class="mt-5 flex flex-col sm:flex-row items-center justify-between gap-3">
    <div class="text-sm text-slate-600">
        Halaman <span class="font-semibold text-slate-800"><?= (int)$currentPage ?></span>
        dari <span class="font-semibold text-slate-800"><?= (int)$totalPages ?></span>
    </div>

    <nav class="flex items-center gap-2" aria-label="Pagination">
        <!-- Prev -->
        <?php if ($currentPage > 1): ?>
            <a class="<?= $btnBase ?> <?= $btnNormal ?>"
                href="<?= $makeUrl($currentPage - 1) ?>"
                aria-label="Previous page">
                Prev
            </a>
        <?php else: ?>
            <span class="<?= $btnBase ?> <?= $btnDis ?>" aria-disabled="true">Prev</span>
        <?php endif; ?>

        <!-- Left ellipsis -->
        <?php if ($start > 1): ?>
            <span class="px-1 text-slate-400 select-none">…</span>
        <?php endif; ?>

        <!-- 3 numbers -->
        <?php for ($p = $start; $p <= $end; $p++): ?>
            <a class="<?= $btnBase ?> <?= ((int)$p === (int)$currentPage) ? $btnActive : $btnNormal ?>"
                href="<?= $makeUrl($p) ?>"
                <?= ((int)$p === (int)$currentPage) ? 'aria-current="page"' : '' ?>>
                <?= (int)$p ?>
            </a>
        <?php endfor; ?>

        <!-- Right ellipsis -->
        <?php if ($end < $totalPages): ?>
            <span class="px-1 text-slate-400 select-none">…</span>
        <?php endif; ?>

        <!-- Last -->
        <?php if ($currentPage < $totalPages): ?>
            <a class="<?= $btnBase ?> <?= $btnNormal ?>"
                href="<?= $makeUrl($totalPages) ?>"
                aria-label="Last page">
                Last
            </a>
        <?php else: ?>
            <span class="<?= $btnBase ?> <?= $btnDis ?>" aria-disabled="true">Last</span>
        <?php endif; ?>
    </nav>
</div>