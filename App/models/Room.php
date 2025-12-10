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

    // app/Models/Room.php

    public function getAdminList(int $limit, int $offset, string $search = '', string $status = 'all')
    {
        $where  = [];
        $params = [];

        // Filter search (nama_ruangan / kategori)
        if ($search !== '') {
            $where[]           = "(nama_ruangan LIKE :search OR kategori LIKE :search)";
            $params[':search'] = "%{$search}%";
        }

        // Filter status (aktif / nonaktif)
        if (in_array($status, ['aktif', 'nonaktif'], true)) {
            $where[]                          = "status_operasional = :status_operasional";
            $params[':status_operasional']    = $status;
        }

        $whereSql = '';
        if (!empty($where)) {
            $whereSql = 'WHERE ' . implode(' AND ', $where);
        }

        // Hitung total baris untuk pagination
        $sqlCount  = "SELECT COUNT(*) AS total FROM " . self::$table . " {$whereSql}";
        $stmtCount = self::$db->prepare($sqlCount);
        $stmtCount->execute($params);
        $totalRows = (int)$stmtCount->fetchColumn();

        $totalPages = $totalRows > 0 ? (int)ceil($totalRows / $limit) : 1;

        // Ambil data
        $sql = "SELECT 
                id_ruangan, 
                nama_ruangan, 
                kategori, 
                kapasitas_min, 
                kapasitas_max, 
                status_operasional,
                lokasi
            FROM " . self::$table . "
            {$whereSql}
            ORDER BY nama_ruangan ASC
            LIMIT :limit OFFSET :offset";

        $stmt = self::$db->prepare($sql);

        // Bind parameter search/status
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
                SELECT b.start_time, b.end_time
                FROM bookings b
                LEFT JOIN booking_status bs_latest
                    ON bs_latest.id_status = (
                        SELECT MAX(bs2.id_status)
                        FROM booking_status bs2
                        WHERE bs2.id_bookings = b.id_bookings
                    )
                WHERE 
                    b.id_ruangan = :id
                    AND b.tanggal = CURDATE()
                    AND (
                        b.submitted = 1
                        OR b.group_expire_at IS NULL
                        OR b.group_expire_at >= NOW()
                    )
                    AND (
                        bs_latest.status IS NULL
                        OR bs_latest.status NOT IN ('cancelled','rejected')
                    )
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
    // Update status 1 ruangan
    public function updateStatus(int $idRuangan, string $status): bool
    {
        if (!in_array($status, ['aktif', 'nonaktif'], true)) {
            return false;
        }

        $sql = "UPDATE " . self::$table . " 
            SET status_operasional = :status
            WHERE id_ruangan = :id";

        $stmt = self::$db->prepare($sql);
        return $stmt->execute([
            'status' => $status,
            'id'     => $idRuangan,
        ]);
    }

    // Nonaktifkan / aktifkan semua ruangan
    public function updateAllStatus(string $status): bool
    {
        if (!in_array($status, ['aktif', 'nonaktif'], true)) {
            return false;
        }

        $sql = "UPDATE " . self::$table . " 
            SET status_operasional = :status";

        $stmt = self::$db->prepare($sql);
        return $stmt->execute(['status' => $status]);
    }

    /**
     * Cek apakah 1 ruangan punya booking aktif (hari ini & ke depan).
     * "Aktif" = 
     *   - submitted = 1 ATAU group_expire_at masih berlaku / null
     *   - status terakhir bukan rejected/cancelled
     *   - tanggal >= hari ini
     */
    public function hasActiveBookings(int $idRuangan): bool
    {
        $sql = "
        SELECT b.id_bookings
        FROM bookings b
        LEFT JOIN Booking_status bs_latest
            ON bs_latest.id_status = (
                SELECT MAX(bs2.id_status)
                FROM Booking_status bs2
                WHERE bs2.id_bookings = b.id_bookings
            )
        WHERE 
            b.id_ruangan = :room
            AND b.tanggal >= CURDATE()
            AND (
                b.submitted = 1
                OR b.group_expire_at IS NULL
                OR b.group_expire_at >= NOW()
            )
            AND (
                bs_latest.status IS NULL
                OR bs_latest.status NOT IN ('rejected', 'cancelled')
            )
        LIMIT 1
    ";

        $stmt = self::$db->prepare($sql);
        $stmt->execute(['room' => $idRuangan]);

        return (bool)$stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cek apakah ADA ruangan manapun yang masih punya booking aktif.
     */
    public function hasAnyActiveBookings(): bool
    {
        $sql = "
        SELECT b.id_bookings
        FROM bookings b
        LEFT JOIN Booking_status bs_latest
            ON bs_latest.id_status = (
                SELECT MAX(bs2.id_status)
                FROM Booking_status bs2
                WHERE bs2.id_bookings = b.id_bookings
            )
        WHERE 
            b.tanggal >= CURDATE()
            AND (
                b.submitted = 1
                OR b.group_expire_at IS NULL
                OR b.group_expire_at >= NOW()
            )
            AND (
                bs_latest.status IS NULL
                OR bs_latest.status NOT IN ('rejected', 'cancelled')
            )
        LIMIT 1
    ";

        $stmt = self::$db->query($sql);
        return (bool)$stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function setAllStatus(string $status): bool
    {
        $sql = "UPDATE " . self::$table . " SET status_operasional = :status";
        $stmt = self::$db->prepare($sql);
        return $stmt->execute(['status' => $status]);
    }

    public function anyActive(): bool
    {
        $sql = "SELECT COUNT(*) FROM " . self::$table . " WHERE status_operasional = 'aktif'";
        $stmt = self::$db->query($sql);
        return ((int)$stmt->fetchColumn()) > 0;
    }
    public function updateRoom(int $idRuangan, array $data): bool
    {
        $sql = "UPDATE " . self::$table . "
            SET 
                nama_ruangan       = :nama_ruangan,
                lokasi             = :lokasi,
                kategori           = :kategori,
                kapasitas_min      = :kapasitas_min,
                kapasitas_max      = :kapasitas_max,
                status_operasional = :status_operasional,
                foto_ruangan       = :foto_ruangan
            WHERE id_ruangan = :id";

        $stmt = self::$db->prepare($sql);
        return $stmt->execute([
            'nama_ruangan'       => $data['nama_ruangan'],
            'lokasi'             => $data['lokasi'] ?? null,
            'kategori'           => $data['kategori'] ?? null,
            'kapasitas_min'      => $data['kapasitas_min'],
            'kapasitas_max'      => $data['kapasitas_max'],
            'status_operasional' => $data['status_operasional'],
            'foto_ruangan'       => $data['foto_ruangan'] ?? null,
            'id'                 => $idRuangan,
        ]);
    }
}
