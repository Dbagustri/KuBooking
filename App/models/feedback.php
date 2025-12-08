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
}
