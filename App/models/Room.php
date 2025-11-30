<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Room extends Model
{
    protected static $table = 'ruangan';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Hitung jumlah ruangan aktif
     */
    public function countActive()
    {
        $sql = "SELECT COUNT(*) AS total
                FROM " . self::$table . "
                WHERE status_operasional = 'aktif'";
        $stmt = self::$db->query($sql);
        $row  = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) ($row['total'] ?? 0);
    }

    /**
     * Ambil detail ruangan berdasarkan id
     */
    public function findById($id)
    {
        $sql = "SELECT * FROM " . self::$table . " WHERE id_ruangan = :id LIMIT 1";
        $stmt = self::$db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Alias untuk kompatibilitas dengan RoomModel lama
     */
    public function getRoomById($id)
    {
        return $this->findById($id);
    }

    /**
     * Ambil semua ruangan aktif
     */
    public function getAllActive()
    {
        $sql = "SELECT * FROM " . self::$table . " WHERE status_operasional = 'aktif'";
        $stmt = self::$db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil beberapa ruangan unggulan (untuk landing page dsb)
     */
    public function getFeaturedRooms($limit = 3)
    {
        $sql = "SELECT * FROM " . self::$table . " 
                WHERE status_operasional = 'aktif'
                ORDER BY id_ruangan ASC
                LIMIT :limit";
        $stmt = self::$db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * List ruangan untuk admin (dengan search + pagination)
     */
    public function getAdminList(int $limit, int $offset, string $search = '')
    {
        $where  = '';
        $params = [];

        if ($search !== '') {
            $where             = "WHERE nama_ruangan LIKE :search OR kategori LIKE :search";
            $params[':search'] = "%{$search}%";
        }

        // hitung total
        $sqlCount  = "SELECT COUNT(*) AS total FROM " . self::$table . " {$where}";
        $stmtCount = self::$db->prepare($sqlCount);
        $stmtCount->execute($params);
        $totalRows = (int)$stmtCount->fetchColumn();

        $totalPages = $totalRows > 0 ? (int)ceil($totalRows / $limit) : 1;

        // ambil data
        $sql = "SELECT id_ruangan, nama_ruangan, kategori, kapasitas_min, kapasitas_max, status_operasional
                FROM " . self::$table . "
                {$where}
                ORDER BY nama_ruangan ASC
                LIMIT :limit OFFSET :offset";

        $stmt = self::$db->prepare($sql);

        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'data'        => $data,
            'total_pages' => $totalPages,
        ];
    }

    /**
     * Ambil fasilitas ruangan (join fasilitas_ruangan + fasilitas)
     */
    public function getFasilitas($idRuangan)
    {
        $sql = "
            SELECT f.nama_fasilitas, f.icon
            FROM fasilitas_ruangan fr
            JOIN fasilitas f ON f.id_fasilitas = fr.id_facility
            WHERE fr.id_ruangan = :id
            ORDER BY f.nama_fasilitas
        ";

        $stmt = self::$db->prepare($sql);
        $stmt->execute(['id' => $idRuangan]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil slot waktu yang sudah dibooking hari ini untuk ruangan tertentu
     */
    public function getBookedSlotsToday($idRuangan)
    {
        $sql = "
            SELECT start_time, end_time
            FROM bookings
            WHERE id_ruangan = :id
              AND tanggal = CURDATE()
        ";

        $stmt = self::$db->prepare($sql);
        $stmt->execute(['id' => $idRuangan]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Ambil jadwal ruangan dari tabel jadwal_ruangan
    public function getScheduleByRoom($id)
    {
        $sql = "
        SELECT hari, open_time, close_time, break_start, break_end
        FROM jadwal_ruangan
        WHERE id_ruangan = :id
        ORDER BY FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')
    ";

        $stmt = self::$db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
