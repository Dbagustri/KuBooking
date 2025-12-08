<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Laporan extends Model
{
    public function __construct()
    {
        parent::__construct(); // inisialisasi self::$db dari base Model
    }

    /**
     * Konversi range (week, month, dll) -> [from, to]
     */
    private function resolveRange(string $range): array
    {
        $to = date('Y-m-d'); // hari ini

        switch ($range) {
            case 'week':
                $from = date('Y-m-d', strtotime('-7 days'));
                break;
            case '3month':
                $from = date('Y-m-d', strtotime('-3 months'));
                break;
            case '6month':
                $from = date('Y-m-d', strtotime('-6 months'));
                break;
            case 'month':
            default:
                $from = date('Y-m-d', strtotime('-1 month'));
                break;
        }

        return [$from, $to];
    }

    /**
     * LAPORAN RUANGAN
     * Ambil data mentah per tanggal & ruangan dari VIEW v_lap_ruangan_harian
     * (view ini yang isinya: tanggal, id_ruangan, nama_ruangan, total)
     */
    public function getRuangan(string $range): array
    {
        [$from, $to] = $this->resolveRange($range);

        $sql = "SELECT tanggal, id_ruangan, nama_ruangan, total
                FROM v_lap_ruangan_harian
                WHERE tanggal BETWEEN :from AND :to
                ORDER BY tanggal ASC, nama_ruangan ASC";

        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            ':from' => $from,
            ':to'   => $to,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * LAPORAN PRODI
     * Data mentah per tanggal & prodi dari VIEW v_lap_prodi_harian
     * (tanggal, prodi, total)
     */
    public function getProdi(string $range): array
    {
        [$from, $to] = $this->resolveRange($range);

        $sql = "SELECT tanggal, prodi, total
                FROM v_lap_prodi_harian
                WHERE tanggal BETWEEN :from AND :to
                ORDER BY tanggal ASC, prodi ASC";

        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            ':from' => $from,
            ':to'   => $to,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * LAPORAN JURUSAN
     * Data mentah per tanggal & jurusan dari VIEW v_lap_jurusan_harian
     * (tanggal, jurusan, total)
     */
    public function getJurusan(string $range): array
    {
        [$from, $to] = $this->resolveRange($range);

        $sql = "SELECT tanggal, jurusan, total
                FROM v_lap_jurusan_harian
                WHERE tanggal BETWEEN :from AND :to
                ORDER BY tanggal ASC, jurusan ASC";

        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            ':from' => $from,
            ':to'   => $to,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * LAPORAN RATING RUANGAN (range waktu)
     * Pakai VIEW v_lap_rating_ruangan_harian:
     *   tanggal, id_ruangan, nama_ruangan, avg_rating, jumlah_feedback
     * Di sini di-aggregate lagi per ruangan untuk 1 range.
     */
    public function getRating(string $range): array
    {
        [$from, $to] = $this->resolveRange($range);

        $sql = "SELECT 
                    id_ruangan,
                    nama_ruangan,
                    AVG(avg_rating)      AS avg_rating_range,
                    SUM(jumlah_feedback) AS total_feedback
                FROM v_lap_rating_ruangan_harian
                WHERE tanggal BETWEEN :from AND :to
                GROUP BY id_ruangan, nama_ruangan
                ORDER BY avg_rating_range DESC";

        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            ':from' => $from,
            ':to'   => $to,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
