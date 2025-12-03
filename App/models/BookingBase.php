<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class BookingBase extends Model
{
    protected static $table = 'Bookings';

    public function __construct()
    {
        parent::__construct();
    }
    public function findWithRoom(int $idBooking): ?array
    {
        $sql = "SELECT 
                    b.*, 
                    r.nama_ruangan, 
                    r.kapasitas_min, 
                    r.kapasitas_max
                FROM " . self::$table . " b
                JOIN Ruangan r ON r.id_ruangan = b.id_ruangan
                WHERE b.id_bookings = :id
                LIMIT 1";

        $stmt = self::$db->prepare($sql);
        $stmt->execute(['id' => $idBooking]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function deleteBooking(int $idBooking): bool
    {
        $sql  = "DELETE FROM " . self::$table . " WHERE id_bookings = :id";
        $stmt = self::$db->prepare($sql);
        return $stmt->execute(['id' => $idBooking]);
    }

    public function addStatus(
        int $idBooking,
        string $status,
        ?string $alasanReject = null,
        ?int $idReschedule = null,
        ?string $alasanReschedule = null
    ): void {
        $sql = "INSERT INTO Booking_status 
                (id_bookings, id_reschedule, status, alasan_reject, alasan_reschedule)
                VALUES
                (:id_bookings, :id_reschedule, :status, :alasan_reject, :alasan_reschedule)";

        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            'id_bookings'       => $idBooking,
            'id_reschedule'     => $idReschedule,
            'status'            => $status,
            'alasan_reject'     => $alasanReject,
            'alasan_reschedule' => $alasanReschedule,
        ]);
    }

    public function addMember(int $idBooking, int $idUser): void
    {
        // insert member
        $sql = "INSERT INTO Booking_member (id_bookings, id_user)
                VALUES (:booking, :user)";
        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            'booking' => $idBooking,
            'user'    => $idUser,
        ]);

        // naikkan counter jumlah_anggota
        $sql2  = "UPDATE " . self::$table . "
                  SET jumlah_anggota = jumlah_anggota + 1
                  WHERE id_bookings = :booking";
        $stmt2 = self::$db->prepare($sql2);
        $stmt2->execute(['booking' => $idBooking]);
    }

    public function removeMember(int $idBooking, int $idUser): void
    {
        $sql = "DELETE FROM Booking_member
                WHERE id_bookings = :booking AND id_user = :user";
        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            'booking' => $idBooking,
            'user'    => $idUser,
        ]);

        $sql2  = "UPDATE " . self::$table . "
                  SET jumlah_anggota = GREATEST(jumlah_anggota - 1, 0)
                  WHERE id_bookings = :booking";
        $stmt2 = self::$db->prepare($sql2);
        $stmt2->execute(['booking' => $idBooking]);
    }

    public function getMembers(int $idBooking): array
    {
        $sql = "SELECT 
                bm.id_user, 
                a.nama,
                a.nim_nip
            FROM Booking_member bm
            JOIN Account a ON a.id_account = bm.id_user
            WHERE bm.id_bookings = :booking
            ORDER BY a.nama ASC";

        $stmt = self::$db->prepare($sql);
        $stmt->execute(['booking' => $idBooking]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    protected function buildActiveBookingWhereFragment(): string
    {
        return "
            (
                b.submitted = 1
                OR b.group_expire_at IS NULL
                OR b.group_expire_at >= NOW()
            )
            AND (
                bs_latest.status IS NULL
                OR bs_latest.status NOT IN ('rejected', 'cancelled')
            )
        ";
    }
    public function isBentrok(int $idRuangan, string $start, string $end): bool
    {
        $activeFragment = $this->buildActiveBookingWhereFragment();

        $sql = "
            SELECT b.id_bookings
            FROM " . self::$table . " b
            LEFT JOIN Booking_status bs_latest
                ON bs_latest.id_status = (
                    SELECT MAX(bs2.id_status)
                    FROM Booking_status bs2
                    WHERE bs2.id_bookings = b.id_bookings
                )
            WHERE 
                b.id_ruangan = :room
                AND (b.start_time < :end AND b.end_time > :start)
                AND {$activeFragment}
            LIMIT 1
        ";

        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            'room'  => $idRuangan,
            'start' => $start,
            'end'   => $end,
        ]);

        return (bool)$stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getDisabledSlotsForRoomDate(int $idRuangan, string $tanggal): array
    {
        $activeFragment = $this->buildActiveBookingWhereFragment();

        $sql = "
            SELECT 
                b.start_time, 
                b.end_time
            FROM " . self::$table . " b
            LEFT JOIN Booking_status bs_latest
                ON bs_latest.id_status = (
                    SELECT MAX(bs2.id_status)
                    FROM Booking_status bs2
                    WHERE bs2.id_bookings = b.id_bookings
                )
            WHERE 
                b.id_ruangan = :room
                AND b.tanggal = :tanggal
                AND {$activeFragment}
        ";

        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            'room'    => $idRuangan,
            'tanggal' => $tanggal,
        ]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $disabled = [];

        foreach ($rows as $row) {
            $start = strtotime($row['start_time']);
            $end   = strtotime($row['end_time']);

            // loop per 30 menit dari start sampai sebelum end
            for ($t = $start; $t < $end; $t += 30 * 60) {
                $disabled[] = date('H:i', $t); // '08:00', '08:30', ...
            }
        }

        // unikkan biar gak dobel
        return array_values(array_unique($disabled));
    }

    public function getLastStatus(int $idBooking): ?string
    {
        $sql = "SELECT status
                FROM Booking_status
                WHERE id_bookings = :id
                ORDER BY created_at DESC, id_status DESC
                LIMIT 1";

        $stmt = self::$db->prepare($sql);
        $stmt->execute(['id' => $idBooking]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['status'] ?? null;
    }

    public function isBentrokExcept(int $idRuangan, string $start, string $end, int $excludeBookingId): bool
    {
        $activeFragment = $this->buildActiveBookingWhereFragment();

        $sql = "
            SELECT b.id_bookings
            FROM " . self::$table . " b
            LEFT JOIN Booking_status bs_latest
                ON bs_latest.id_status = (
                    SELECT MAX(bs2.id_status)
                    FROM Booking_status bs2
                    WHERE bs2.id_bookings = b.id_bookings
                )
            WHERE 
                b.id_ruangan = :room
                AND b.id_bookings <> :exclude_id
                AND (b.start_time < :end AND b.end_time > :start)
                AND {$activeFragment}
            LIMIT 1
        ";

        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            'room'       => $idRuangan,
            'start'      => $start,
            'end'        => $end,
            'exclude_id' => $excludeBookingId,
        ]);

        return (bool)$stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function getDisabledSlotsForRoomDateExcept(int $idRuangan, string $tanggal, int $excludeBookingId): array
    {
        $activeFragment = $this->buildActiveBookingWhereFragment();

        $sql = "
            SELECT 
                b.start_time, 
                b.end_time
            FROM " . self::$table . " b
            LEFT JOIN Booking_status bs_latest
                ON bs_latest.id_status = (
                    SELECT MAX(bs2.id_status)
                    FROM Booking_status bs2
                    WHERE bs2.id_bookings = b.id_bookings
                )
            WHERE 
                b.id_ruangan = :room
                AND b.tanggal = :tanggal
                AND b.id_bookings <> :exclude_id
                AND {$activeFragment}
        ";

        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            'room'       => $idRuangan,
            'tanggal'    => $tanggal,
            'exclude_id' => $excludeBookingId,
        ]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $disabled = [];

        foreach ($rows as $row) {
            $start = strtotime($row['start_time']);
            $end   = strtotime($row['end_time']);

            for ($t = $start; $t < $end; $t += 30 * 60) {
                $disabled[] = date('H:i', $t);
            }
        }

        return array_values(array_unique($disabled));
    }
}
