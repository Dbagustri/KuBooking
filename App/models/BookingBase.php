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

    /**
     * Ambil booking + info ruangan (nama, kapasitas_min, kapasitas_max)
     */
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

    /**
     * Hapus booking (cascading ke Booking_member & Booking_status lewat FK)
     */
    public function deleteBooking(int $idBooking): bool
    {
        $sql  = "DELETE FROM " . self::$table . " WHERE id_bookings = :id";
        $stmt = self::$db->prepare($sql);
        return $stmt->execute(['id' => $idBooking]);
    }

    /**
     * Tambah entry status baru ke Booking_status
     */
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

    /**
     * Tambah anggota ke Booking_member + update jumlah_anggota
     */
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

    /**
     * Hapus anggota dari Booking_member + turunkan jumlah_anggota (minimal 0)
     */
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

    /**
     * Ambil anggota booking (id_user + nama)
     */
    public function getMembers(int $idBooking): array
    {
        $sql = "SELECT bm.id_user, a.nama
                FROM Booking_member bm
                JOIN Account a ON a.id_account = bm.id_user
                WHERE bm.id_bookings = :booking";

        $stmt = self::$db->prepare($sql);
        $stmt->execute(['booking' => $idBooking]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cek bentrok jadwal di ruangan tertentu
     *
     * - Bentrok jika: (start_time < :end AND end_time > :start)
     * - Hanya hitung booking:
     *   - submitted = 1, atau
     *   - group_expire_at masih berlaku (hold 10 menit join)
     * - Abaikan status yang sudah rejected / cancelled / dibatalkan
     */
    public function isBentrok(int $idRuangan, string $start, string $end): bool
    {
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
                AND (
                    b.submitted = 1
                    OR (b.group_expire_at IS NOT NULL AND b.group_expire_at >= NOW())
                )
                AND (
                    bs_latest.status IS NULL
                    OR bs_latest.status NOT IN ('rejected', 'cancelled')
                )
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

    /**
     * Ambil slot 30 menit yang sudah terpakai untuk ruangan & tanggal tertentu
     * return: array ['08:00','08:30', ...]
     */
    public function getDisabledSlotsForRoomDate(int $idRuangan, string $tanggal): array
    {
        $sql = "
            SELECT 
                b.start_time, 
                b.end_time,
                bs_latest.status AS last_status
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
                AND (
                    b.submitted = 1
                    OR (b.group_expire_at IS NOT NULL AND b.group_expire_at >= NOW())
                )
                AND (
                    bs_latest.status IS NULL
                    OR bs_latest.status NOT IN ('rejected', 'cancelled')
                )
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

    /**
     * Ambil status terakhir suatu booking
     */
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
    /**
     * Cek bentrok jadwal di ruangan tertentu
     * namun MENGABAIKAN 1 booking tertentu (mis. saat reschedule).
     */
    public function isBentrokExcept(int $idRuangan, string $start, string $end, int $excludeBookingId): bool
    {
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
                AND (
                    b.submitted = 1
                    OR (b.group_expire_at IS NOT NULL AND b.group_expire_at >= NOW())
                )
                AND (
                    bs_latest.status IS NULL
                    OR bs_latest.status NOT IN ('rejected', 'cancelled')
                )
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

    /**
     * Ambil slot 30 menit yang sudah terpakai untuk ruangan & tanggal tertentu,
     * mengabaikan 1 booking (dipakai di halaman reschedule).
     */
    public function getDisabledSlotsForRoomDateExcept(int $idRuangan, string $tanggal, int $excludeBookingId): array
    {
        $sql = "
            SELECT 
                b.start_time, 
                b.end_time,
                bs_latest.status AS last_status
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
                AND (
                    b.submitted = 1
                    OR (b.group_expire_at IS NOT NULL AND b.group_expire_at >= NOW())
                )
                AND (
                    bs_latest.status IS NULL
                    OR bs_latest.status NOT IN ('rejected', 'cancelled', 'dibatalkan')
                )
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
