<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Feedback extends Model
{
    protected static $table = 'feedback';

    public function hasRated(int $idBooking, int $idUser): bool
    {
        $sql = "SELECT id_feedback 
                FROM feedback 
                WHERE id_bookings = :b AND id_user = :u
                LIMIT 1";

        $stmt = self::$db->prepare($sql);
        $stmt->execute(['b' => $idBooking, 'u' => $idUser]);

        return (bool)$stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function submit(int $idBooking, int $idUser, int $rating, ?string $komentar)
    {
        $sql = "INSERT INTO feedback (id_bookings, id_user, rating, komentar)
                VALUES (:b, :u, :r, :k)";

        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            'b' => $idBooking,
            'u' => $idUser,
            'r' => $rating,
            'k' => $komentar,
        ]);
    }
    public function getAdminList(int $page, int $perPage, string $search, $roomFilter, $ratingFilter): array
    {
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT 
                f.id_feedback,
                f.id_bookings,
                f.id_user,
                f.rating,
                f.komentar,
                f.created_at,
                b.tanggal,
                b.start_time,
                r.nama_ruangan,
                a.nama AS nama_user,
                a.nim_nip
            FROM Feedback f
            JOIN Bookings b   ON f.id_bookings = b.id_bookings
            JOIN Ruangan r    ON b.id_ruangan = r.id_ruangan
            JOIN Account a    ON f.id_user = a.id_account
            WHERE 1=1";

        $params = [];

        if ($roomFilter !== 'all' && ctype_digit((string)$roomFilter)) {
            $sql .= " AND r.id_ruangan = :id_ruangan";
            $params[':id_ruangan'] = (int)$roomFilter;
        }

        if ($ratingFilter !== 'all' && ctype_digit((string)$ratingFilter)) {
            $sql .= " AND f.rating >= :min_rating";
            $params[':min_rating'] = (int)$ratingFilter;
        }

        if ($search !== '') {
            $sql .= " AND (
                    a.nama LIKE :search 
                    OR a.nim_nip LIKE :search 
                    OR r.nama_ruangan LIKE :search
                    OR f.komentar LIKE :search
                  )";
            $params[':search'] = '%' . $search . '%';
        }

        // ⬇⬇⬇ PENTING: pakai self::$db, jangan $this->db
        $sqlCount = "SELECT COUNT(*) FROM ({$sql}) AS sub";
        $stmtCount = self::$db->prepare($sqlCount);
        $stmtCount->execute($params);
        $totalRows  = (int)$stmtCount->fetchColumn();
        $totalPages = $totalRows > 0 ? (int)ceil($totalRows / $perPage) : 1;

        $sql .= " ORDER BY f.created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = self::$db->prepare($sql);

        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'data'        => $data,
            'total_pages' => $totalPages,
        ];
    }
    public function deleteById(int $id): bool
    {
        $sql = "DELETE FROM Feedback WHERE id_feedback = :id";
        $stmt = self::$db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
