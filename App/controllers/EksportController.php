<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Laporan;

class EksportController extends Controller
{
    /** @var Laporan */
    private $laporanModel;

    public function __construct()
    {
        $this->laporanModel = new Laporan();
    }

    /**
     * URL contoh:
     * index.php?controller=eksport&action=laporan&type=rooms&range=month
     */
    public function laporan()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $type  = $_GET['type']  ?? '';
        $range = $_GET['range'] ?? 'month';

        $allowedTypes = ['rooms', 'prodi', 'jurusan', 'rating'];
        if (!in_array($type, $allowedTypes, true)) {
            http_response_code(400);
            echo "Tipe export tidak valid.";
            return;
        }

        switch ($type) {
            case 'rooms':
                $raw = $this->laporanModel->getRuangan($range); // FULL
                $rows = $this->buildPivotCsv($raw, 'tanggal', 'nama_ruangan', 'total', 'Tanggal');
                $filename = "laporan_ruangan_{$range}_" . date('Ymd_His') . ".csv";
                break;

            case 'prodi':
                $raw = $this->laporanModel->getProdi($range); // FULL
                $rows = $this->buildPivotCsv($raw, 'tanggal', 'prodi', 'total', 'Tanggal');
                $filename = "laporan_prodi_{$range}_" . date('Ymd_His') . ".csv";
                break;

            case 'jurusan':
                $raw = $this->laporanModel->getJurusan($range); // FULL
                $rows = $this->buildPivotCsv($raw, 'tanggal', 'jurusan', 'total', 'Tanggal');
                $filename = "laporan_jurusan_{$range}_" . date('Ymd_His') . ".csv";
                break;

            case 'rating':
                $raw = $this->laporanModel->getRating($range); // FULL
                $rows = [];
                $rows[] = ['Ruangan', 'Rata-rata Rating', 'Jumlah Feedback'];
                foreach ($raw as $r) {
                    $rows[] = [
                        (string)($r['nama_ruangan'] ?? '-'),
                        number_format((float)($r['avg_rating_range'] ?? 0), 2, '.', ''),
                        (int)($r['total_feedback'] ?? 0),
                    ];
                }
                if (count($rows) === 1) $rows[] = ['(Tidak ada data)'];
                $filename = "laporan_rating_{$range}_" . date('Ymd_His') . ".csv";
                break;
        }

        $this->outputCsv($filename, $rows);
    }

    /**
     * Pivot CSV:
     * Header: [Tanggal, kategori1, kategori2, ..., Total]
     */
    private function buildPivotCsv(array $rows, string $keyDate, string $keyCat, string $keyTotal, string $dateLabel = 'Tanggal'): array
    {
        $dates = [];
        $cats  = [];
        $map   = [];

        foreach ($rows as $row) {
            $d = $row[$keyDate] ?? null;
            $c = $row[$keyCat]  ?? null;
            $t = (int)($row[$keyTotal] ?? 0);
            if (!$d || !$c) continue;

            $dates[$d] = true;
            $cats[$c]  = true;

            if (!isset($map[$d])) $map[$d] = [];
            $map[$d][$c] = $t;
        }

        $dates = array_keys($dates);
        $cats  = array_keys($cats);
        sort($dates);
        sort($cats);

        $out = [];
        $out[] = array_merge([$dateLabel], $cats, ['Total']);

        foreach ($dates as $d) {
            $sum = 0;
            $line = [$d];
            foreach ($cats as $c) {
                $val = isset($map[$d][$c]) ? (int)$map[$d][$c] : 0;
                $sum += $val;
                $line[] = $val;
            }
            $line[] = $sum;
            $out[] = $line;
        }

        if (count($out) === 1) {
            $out[] = ['(Tidak ada data)'];
        }

        return $out;
    }

    /**
     * CSV Excel-friendly:
     * - BOM UTF-8 supaya Excel Windows kebaca
     * - delimiter ';' supaya angka & pemisah tidak kacau di locale ID
     */
    private function outputCsv(string $filename, array $rows): void
    {
        if (ob_get_length()) {
            @ob_end_clean();
        }

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $out = fopen('php://output', 'w');

        // BOM
        fwrite($out, "\xEF\xBB\xBF");

        foreach ($rows as $row) {
            if (!is_array($row)) $row = [$row];
            fputcsv($out, $row, ';');
        }

        fclose($out);
        exit;
    }
}
