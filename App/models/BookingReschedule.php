<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class BookingReschedule extends Model
{
    protected static $table = 'Booking_reschedule';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Buat request reschedule baru + set anggota awal.
     * Sekaligus mengisi join_reschedule_until (window join anggota via kode).
     *
     * @param array $data [
     *   'id_bookings', 'id_ruangan', 'id_user',
     *   'new_start_time', 'new_end_time', 'new_tanggal',
     *   'alasan' (optional), 'join_reschedule_until' (optional)
     * ]
     * @param int[] $memberIds daftar id_user anggota awal yang ikut ke jadwal baru
     */
    public function createRequest(array $data, array $memberIds = []): int
    {
        $sql = "INSERT INTO " . self::$table . "
                (
                    id_bookings,
                    id_ruangan,
                    id_user,
                    new_start_time,
                    new_end_time,
                    new_tanggal,
                    alasan,
                    join_reschedule_until
                )
                VALUES
                (
                    :id_bookings,
                    :id_ruangan,
                    :id_user,
                    :new_start_time,
                    :new_end_time,
                    :new_tanggal,
                    :alasan,
                    :join_reschedule_until
                )";

        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            'id_bookings'           => (int)$data['id_bookings'],
            'id_ruangan'            => (int)$data['id_ruangan'],
            'id_user'               => (int)$data['id_user'],
            'new_start_time'        => $data['new_start_time'],
            'new_end_time'          => $data['new_end_time'],
            'new_tanggal'           => $data['new_tanggal'],
            'alasan'                => $data['alasan'] ?? null,
            // window join anggota reschedule (default +10 menit)
            'join_reschedule_until' => $data['join_reschedule_until']
                ?? date('Y-m-d H:i:s', strtotime('+10 minutes')),
        ]);

        $idReschedule = (int) self::$db->lastInsertId();

        // Copy anggota awal (biasanya dari Booking_member)
        if (!empty($memberIds)) {
            $this->setMembers($idReschedule, $memberIds);
        }

        return $idReschedule;
    }

    /**
     * Replace semua member draft reschedule dengan daftar baru.
     *
     * @param int   $idReschedule
     * @param int[] $memberIds
     */
    public function updateRequest(int $idReschedule, array $data): void
    {
        // Build bagian SET dinamis sesuai field yang dikirim
        $fields = [];
        $params = ['id_reschedule' => $idReschedule];

        if (isset($data['new_start_time'])) {
            $fields[] = 'new_start_time = :new_start_time';
            $params['new_start_time'] = $data['new_start_time'];
        }

        if (isset($data['new_end_time'])) {
            $fields[] = 'new_end_time = :new_end_time';
            $params['new_end_time'] = $data['new_end_time'];
        }

        if (isset($data['new_tanggal'])) {
            $fields[] = 'new_tanggal = :new_tanggal';
            $params['new_tanggal'] = $data['new_tanggal'];
        }

        if (array_key_exists('alasan', $data)) {
            // boleh null
            $fields[] = 'alasan = :alasan';
            $params['alasan'] = $data['alasan'];
        }

        if (isset($data['join_reschedule_until'])) {
            $fields[] = 'join_reschedule_until = :join_reschedule_until';
            $params['join_reschedule_until'] = $data['join_reschedule_until'];
        }

        if (empty($fields)) {
            // tidak ada yang di-update, langsung keluar
            return;
        }

        $sql = "UPDATE " . self::$table . "
            SET " . implode(', ', $fields) . "
            WHERE id_reschedule = :id_reschedule";

        $stmt = self::$db->prepare($sql);
        $stmt->execute($params);
    }
    public function markSubmitted(int $idReschedule): void
    {
        $sql = "UPDATE " . self::$table . "
            SET 
                join_reschedule_until = NOW(),
                submitted = 1
            WHERE id_reschedule = :id";

        $stmt = self::$db->prepare($sql);
        $stmt->execute(['id' => $idReschedule]);
    }


    public function setMembers(int $idReschedule, array $memberIds): void
    {
        // Hapus dulu seluruh anggota
        $del = self::$db->prepare(
            "DELETE FROM Booking_reschedule_member WHERE id_reschedule = :id"
        );
        $del->execute(['id' => $idReschedule]);

        if (empty($memberIds)) {
            return;
        }

        $sql  = "INSERT INTO Booking_reschedule_member (id_reschedule, id_user)
                 VALUES (:id_reschedule, :id_user)";
        $stmt = self::$db->prepare($sql);

        foreach ($memberIds as $uid) {
            if (!ctype_digit((string)$uid)) {
                continue;
            }
            $stmt->execute([
                'id_reschedule' => $idReschedule,
                'id_user'       => (int)$uid,
            ]);
        }
    }

    /**
     * Ambil detail reschedule + info booking & ruangan.
     * Dipakai di halaman reschedule user / admin.
     */
    public function findWithBooking(int $idReschedule): ?array
    {
        $sql = "SELECT 
                    br.*,
                    b.booking_code,
                    b.start_time AS old_start_time,
                    b.end_time   AS old_end_time,
                    b.tanggal    AS old_tanggal,
                    b.id_pj,
                    r.nama_ruangan,
                    r.lokasi,
                    r.foto_ruangan,
                    r.kapasitas_min,
                    r.kapasitas_max
                FROM Booking_reschedule br
                JOIN Bookings b ON b.id_bookings = br.id_bookings
                JOIN Ruangan r ON r.id_ruangan = br.id_ruangan
                WHERE br.id_reschedule = :id
                LIMIT 1";

        $stmt = self::$db->prepare($sql);
        $stmt->execute(['id' => $idReschedule]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * Ambil anggota dari draft reschedule.
     */
    public function getMembers(int $idReschedule): array
    {
        $sql = "SELECT 
                    brm.id_user,
                    a.nama
                FROM Booking_reschedule_member brm
                JOIN Account a ON a.id_account = brm.id_user
                WHERE brm.id_reschedule = :id";

        $stmt = self::$db->prepare($sql);
        $stmt->execute(['id' => $idReschedule]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Copy semua anggota dari Booking_member → Booking_reschedule_member.
     * (opsional kalau mau dipakai dari luar)
     */
    public function copyMembersFromBooking(int $idReschedule, int $idBooking): void
    {
        $sql = "INSERT IGNORE INTO Booking_reschedule_member (id_reschedule, id_user)
                SELECT :id_reschedule, bm.id_user
                FROM Booking_member bm
                WHERE bm.id_bookings = :id_booking";

        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            'id_reschedule' => $idReschedule,
            'id_booking'    => $idBooking,
        ]);
    }

    /**
     * Hapus 1 anggota dari draft reschedule.
     */
    public function removeMember(int $idReschedule, int $idUser): void
    {
        $sql = "DELETE FROM Booking_reschedule_member
                WHERE id_reschedule = :res AND id_user = :user";

        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            'res'  => $idReschedule,
            'user' => $idUser,
        ]);
    }

    /**
     * Tambah 1 anggota ke draft reschedule.
     */
    public function addMember(int $idReschedule, int $idUser): void
    {
        $sql = "INSERT IGNORE INTO Booking_reschedule_member (id_reschedule, id_user)
                VALUES (:res, :user)";

        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            'res'  => $idReschedule,
            'user' => $idUser,
        ]);
    }

    /**
     * Ambil reschedule yang MASIH DALAM WINDOW JOIN (join_reschedule_until >= NOW())
     * untuk 1 booking tertentu.
     *
     * Dipakai saat user join pakai KODE KELOMPOK
     * ketika status booking = 'reschedule_pending'.
     *
     * Flow yang terjadi:
     * - PJ sudah ajukan reschedule → record Booking_reschedule dibuat.
     * - join_reschedule_until menentukan batas waktu anggota lain bisa ikut jadwal baru.
     * - Setelah lewat join_reschedule_until, anggota baru TIDAK bisa join lagi via kode.
     */
    public function findActiveRescheduleForBooking(int $idBooking): ?array
    {
        $sql = "SELECT 
                    br.*,
                    r.kapasitas_max
                FROM Booking_reschedule br
                JOIN Ruangan r ON r.id_ruangan = br.id_ruangan
                WHERE 
                    br.id_bookings = :id_booking
                    AND br.join_reschedule_until IS NOT NULL
                    AND br.join_reschedule_until >= NOW()
                ORDER BY br.created_at DESC, br.id_reschedule DESC
                LIMIT 1";

        $stmt = self::$db->prepare($sql);
        $stmt->execute(['id_booking' => $idBooking]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * Ambil draft reschedule TERBARU untuk 1 booking (tanpa cek window join).
     * Bisa dipakai untuk admin / keperluan lain.
     */
    public function findLatestByBooking(int $idBooking): ?array
    {
        $sql = "SELECT 
                    br.*,
                    r.kapasitas_max
                FROM Booking_reschedule br
                JOIN Ruangan r ON r.id_ruangan = br.id_ruangan
                WHERE br.id_bookings = :id
                ORDER BY br.created_at DESC, br.id_reschedule DESC
                LIMIT 1";

        $stmt = self::$db->prepare($sql);
        $stmt->execute(['id' => $idBooking]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }
}
