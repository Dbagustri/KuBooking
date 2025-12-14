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
    public function getRuanganPaged(string $range, int $page = 1, int $perPage = 5): array
    {
        [$from, $to] = $this->resolveRange($range);
        if ($page < 1) $page = 1;
        if ($perPage < 1) $perPage = 5;

        // 1) hitung jumlah tanggal unik
        $sqlCount = "
        SELECT COUNT(*) 
        FROM (
            SELECT DISTINCT tanggal
            FROM v_lap_ruangan_harian
            WHERE tanggal BETWEEN :from AND :to
        ) t
    ";
        $stmt = self::$db->prepare($sqlCount);
        $stmt->execute([':from' => $from, ':to' => $to]);
        $totalDates = (int)$stmt->fetchColumn();

        $totalPages = $totalDates > 0 ? (int)ceil($totalDates / $perPage) : 1;
        if ($page > $totalPages) $page = $totalPages;

        $offset = ($page - 1) * $perPage;

        // 2) ambil tanggal untuk halaman ini
        $sqlDates = "
        SELECT DISTINCT tanggal
        FROM v_lap_ruangan_harian
        WHERE tanggal BETWEEN :from AND :to
        ORDER BY tanggal ASC
        LIMIT :limit OFFSET :offset
    ";
        $stmtDates = self::$db->prepare($sqlDates);
        $stmtDates->bindValue(':from', $from);
        $stmtDates->bindValue(':to', $to);
        $stmtDates->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmtDates->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmtDates->execute();

        $dates = $stmtDates->fetchAll(PDO::FETCH_COLUMN);
        if (empty($dates)) {
            return [
                'rows' => [],
                'current_page' => $page,
                'total_pages' => $totalPages,
            ];
        }

        // 3) ambil rows untuk tanggal tersebut (pivot tetap di view)
        $in = implode(',', array_fill(0, count($dates), '?'));
        $sqlRows = "
        SELECT tanggal, id_ruangan, nama_ruangan, total
        FROM v_lap_ruangan_harian
        WHERE tanggal IN ($in)
        ORDER BY tanggal ASC, nama_ruangan ASC
    ";
        $stmtRows = self::$db->prepare($sqlRows);
        $stmtRows->execute($dates);

        return [
            'rows' => $stmtRows->fetchAll(PDO::FETCH_ASSOC),
            'current_page' => $page,
            'total_pages' => $totalPages,
        ];
    }

    public function getProdiPaged(string $range, int $page = 1, int $perPage = 5): array
    {
        [$from, $to] = $this->resolveRange($range);
        if ($page < 1) $page = 1;
        if ($perPage < 1) $perPage = 5;

        $sqlCount = "
        SELECT COUNT(*) FROM (
            SELECT DISTINCT tanggal
            FROM v_lap_prodi_harian
            WHERE tanggal BETWEEN :from AND :to
        ) t
    ";
        $stmt = self::$db->prepare($sqlCount);
        $stmt->execute([':from' => $from, ':to' => $to]);
        $totalDates = (int)$stmt->fetchColumn();

        $totalPages = $totalDates > 0 ? (int)ceil($totalDates / $perPage) : 1;
        if ($page > $totalPages) $page = $totalPages;

        $offset = ($page - 1) * $perPage;

        $sqlDates = "
        SELECT DISTINCT tanggal
        FROM v_lap_prodi_harian
        WHERE tanggal BETWEEN :from AND :to
        ORDER BY tanggal ASC
        LIMIT :limit OFFSET :offset
    ";
        $stmtDates = self::$db->prepare($sqlDates);
        $stmtDates->bindValue(':from', $from);
        $stmtDates->bindValue(':to', $to);
        $stmtDates->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmtDates->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmtDates->execute();

        $dates = $stmtDates->fetchAll(PDO::FETCH_COLUMN);
        if (empty($dates)) {
            return ['rows' => [], 'current_page' => $page, 'total_pages' => $totalPages];
        }

        $in = implode(',', array_fill(0, count($dates), '?'));
        $sqlRows = "
        SELECT tanggal, prodi, total
        FROM v_lap_prodi_harian
        WHERE tanggal IN ($in)
        ORDER BY tanggal ASC, prodi ASC
    ";
        $stmtRows = self::$db->prepare($sqlRows);
        $stmtRows->execute($dates);

        return [
            'rows' => $stmtRows->fetchAll(PDO::FETCH_ASSOC),
            'current_page' => $page,
            'total_pages' => $totalPages,
        ];
    }

    public function getJurusanPaged(string $range, int $page = 1, int $perPage = 5): array
    {
        [$from, $to] = $this->resolveRange($range);
        if ($page < 1) $page = 1;
        if ($perPage < 1) $perPage = 5;

        $sqlCount = "
        SELECT COUNT(*) FROM (
            SELECT DISTINCT tanggal
            FROM v_lap_jurusan_harian
            WHERE tanggal BETWEEN :from AND :to
        ) t
    ";
        $stmt = self::$db->prepare($sqlCount);
        $stmt->execute([':from' => $from, ':to' => $to]);
        $totalDates = (int)$stmt->fetchColumn();

        $totalPages = $totalDates > 0 ? (int)ceil($totalDates / $perPage) : 1;
        if ($page > $totalPages) $page = $totalPages;

        $offset = ($page - 1) * $perPage;

        $sqlDates = "
        SELECT DISTINCT tanggal
        FROM v_lap_jurusan_harian
        WHERE tanggal BETWEEN :from AND :to
        ORDER BY tanggal ASC
        LIMIT :limit OFFSET :offset
    ";
        $stmtDates = self::$db->prepare($sqlDates);
        $stmtDates->bindValue(':from', $from);
        $stmtDates->bindValue(':to', $to);
        $stmtDates->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmtDates->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmtDates->execute();

        $dates = $stmtDates->fetchAll(PDO::FETCH_COLUMN);
        if (empty($dates)) {
            return ['rows' => [], 'current_page' => $page, 'total_pages' => $totalPages];
        }

        $in = implode(',', array_fill(0, count($dates), '?'));
        $sqlRows = "
        SELECT tanggal, jurusan, total
        FROM v_lap_jurusan_harian
        WHERE tanggal IN ($in)
        ORDER BY tanggal ASC, jurusan ASC
    ";
        $stmtRows = self::$db->prepare($sqlRows);
        $stmtRows->execute($dates);

        return [
            'rows' => $stmtRows->fetchAll(PDO::FETCH_ASSOC),
            'current_page' => $page,
            'total_pages' => $totalPages,
        ];
    }

    public function getRatingPaged(string $range, int $page = 1, int $perPage = 5): array
    {
        [$from, $to] = $this->resolveRange($range);
        if ($page < 1) $page = 1;
        if ($perPage < 1) $perPage = 5;

        // count total ruangan yang punya feedback di range
        $sqlCount = "
        SELECT COUNT(*) FROM (
            SELECT id_ruangan
            FROM v_lap_rating_ruangan_harian
            WHERE tanggal BETWEEN :from AND :to
            GROUP BY id_ruangan
        ) t
    ";
        $stmt = self::$db->prepare($sqlCount);
        $stmt->execute([':from' => $from, ':to' => $to]);
        $totalRows = (int)$stmt->fetchColumn();

        $totalPages = $totalRows > 0 ? (int)ceil($totalRows / $perPage) : 1;
        if ($page > $totalPages) $page = $totalPages;

        $offset = ($page - 1) * $perPage;

        $sql = "
        SELECT 
            id_ruangan,
            nama_ruangan,
            AVG(avg_rating)      AS avg_rating_range,
            SUM(jumlah_feedback) AS total_feedback
        FROM v_lap_rating_ruangan_harian
        WHERE tanggal BETWEEN :from AND :to
        GROUP BY id_ruangan, nama_ruangan
        ORDER BY avg_rating_range DESC
        LIMIT :limit OFFSET :offset
    ";
        $stmt2 = self::$db->prepare($sql);
        $stmt2->bindValue(':from', $from);
        $stmt2->bindValue(':to', $to);
        $stmt2->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt2->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt2->execute();

        return [
            'rows' => $stmt2->fetchAll(PDO::FETCH_ASSOC),
            'current_page' => $page,
            'total_pages' => $totalPages,
        ];
    }
}
