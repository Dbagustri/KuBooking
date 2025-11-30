<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Laporan extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Rekap ruangan paling sering dipinjam per tanggal.
     * $range: week, month, 3month, 6month
     */
    public function getSummaryRuangan(string $range, int $page = 1, int $perPage = 10): array
    {
        [$start, $end] = $this->getRangeDate($range);

        $offset = ($page - 1) * $perPage;

        $sql = "
            SELECT 
                tanggal,
                SUM(CASE WHEN r.nama_ruangan = 'Ruangan Pertama' THEN 1 ELSE 0 END) AS ruang_1,
                SUM(CASE WHEN r.nama_ruangan = 'Ruangan Kedua'   THEN 1 ELSE 0 END) AS ruang_2,
                SUM(CASE WHEN r.nama_ruangan = 'Ruangan Ketiga'  THEN 1 ELSE 0 END) AS ruang_3,
                COUNT(*) AS total
            FROM bookings b
            JOIN ruangan r ON r.id_ruangan = b.id_ruangan
            WHERE b.tanggal BETWEEN :start AND :end
            GROUP BY tanggal
            ORDER BY tanggal DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = self::$db->prepare($sql);
        $stmt->bindValue(':start', $start);
        $stmt->bindValue(':end', $end);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // hitung total halaman
        $sqlCount = "
            SELECT COUNT(DISTINCT tanggal) 
            FROM bookings 
            WHERE tanggal BETWEEN :start AND :end
        ";
        $stmtCount = self::$db->prepare($sqlCount);
        $stmtCount->execute([':start' => $start, ':end' => $end]);
        $totalRows   = (int) $stmtCount->fetchColumn();
        $totalPages  = max(1, (int)ceil($totalRows / $perPage));

        return [
            'data'        => $rows,
            'total_pages' => $totalPages,
        ];
    }

    // nanti bisa tambah:
    // getSummaryProdi(), getSummaryRating(), dst.

    private function getRangeDate(string $range): array
    {
        $end   = date('Y-m-d');
        switch ($range) {
            case 'week':
                $start = date('Y-m-d', strtotime('-7 days'));
                break;
            case '3month':
                $start = date('Y-m-d', strtotime('-3 months'));
                break;
            case '6month':
                $start = date('Y-m-d', strtotime('-6 months'));
                break;
            case 'month':
            default:
                $start = date('Y-m-d', strtotime('-1 month'));
                break;
        }
        return [$start, $end];
    }
}
