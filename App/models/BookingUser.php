<?php

namespace App\Models;

use PDO;

class BookingUser extends BookingBase
{
    /**
     * Riwayat booking user (hanya booking yang sudah disubmit).
     */
    public function getHistoryByUser(int $idUser, int $limit = 10, int $offset = 0): array
    {
        $sql = "SELECT 
                    b.id_bookings,
                    b.id_ruangan,
                    b.id_pj,
                    b.booking_code,
                    b.start_time,
                    b.end_time,
                    b.tanggal,
                    b.submitted,
                    r.nama_ruangan,
                    r.lokasi,
                    COALESCE(
                        (
                            SELECT bs.status 
                            FROM Booking_status bs 
                            WHERE bs.id_bookings = b.id_bookings 
                            ORDER BY bs.created_at DESC, bs.id_status DESC
                            LIMIT 1
                        ),
                        CASE 
                            WHEN b.submitted = 1 THEN 'pending' 
                            ELSE 'draft' 
                        END
                    ) AS status
                FROM Bookings b
                JOIN Booking_member bm ON b.id_bookings = bm.id_bookings
                JOIN Ruangan r ON b.id_ruangan = r.id_ruangan
                WHERE bm.id_user = :id_user
                  AND b.submitted = 1
                ORDER BY b.start_time DESC
                LIMIT :limit OFFSET :offset";

        $stmt = self::$db->prepare($sql);
        $stmt->bindValue(':id_user', $idUser, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countHistoryByUser(int $idUser): int
    {
        $sql = "SELECT COUNT(*) 
                FROM Bookings b
                JOIN Booking_member bm ON b.id_bookings = bm.id_bookings
                WHERE bm.id_user = :id_user
                  AND b.submitted = 1";

        $stmt = self::$db->prepare($sql);
        $stmt->bindValue(':id_user', $idUser, PDO::PARAM_INT);
        $stmt->execute();

        return (int)$stmt->fetchColumn();
    }

    /**
     * Cek apakah user punya booking aktif (lock user):
     * - submitted = 1
     * - status terakhir salah satu dari:
     *   'pending', 'approved', 'reschedule_pending', 'reschedule_approved'
     * - tanggal >= hari ini
     *
     * Return: ringkasan untuk ditampilkan di home, atau null kalau tidak ada.
     */
    public function getActiveBookingForUser(int $idUser): ?array
    {
        $sql = "
            SELECT 
                b.*,
                r.nama_ruangan,
                (
                    SELECT bs2.status
                    FROM Booking_status bs2
                    WHERE bs2.id_bookings = b.id_bookings
                    ORDER BY bs2.created_at DESC, bs2.id_status DESC
                    LIMIT 1
                ) AS last_status
            FROM Bookings b
            JOIN Booking_member bm ON bm.id_bookings = b.id_bookings
            JOIN Ruangan r ON b.id_ruangan = r.id_ruangan
            WHERE bm.id_user = :id_user
              AND b.submitted = 1
              AND COALESCE(
                    (
                        SELECT bs2.status
                        FROM Booking_status bs2
                        WHERE bs2.id_bookings = b.id_bookings
                        ORDER BY bs2.created_at DESC, bs2.id_status DESC
                        LIMIT 1
                    ),
                    CASE 
                        WHEN b.submitted = 1 THEN 'pending' 
                        ELSE 'draft' 
                    END
                  ) IN ('pending', 'approved', 'reschedule_pending', 'reschedule_approved')
              AND b.tanggal >= CURDATE()
            ORDER BY b.tanggal ASC, b.start_time ASC
            LIMIT 1
        ";

        $stmt = self::$db->prepare($sql);
        $stmt->execute(['id_user' => $idUser]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return [
            'nama_ruangan'  => $row['nama_ruangan'],
            'jam_mulai'     => date('H:i', strtotime($row['start_time'])),
            'jam_selesai'   => date('H:i', strtotime($row['end_time'])),
            'tanggal_label' => date('d M Y', strtotime($row['tanggal'])),
        ];
    }

    /**
     * Buat draft booking kelompok (PJ).
     * Catatan: jumlah_anggota diset 0 dulu, nanti ditambah lewat addMember().
     */
    public function createGroupBooking(array $data): int
    {
        $sql = "INSERT INTO " . self::$table . "
            (
                id_pj,
                id_ruangan,
                booking_code,
                kode_kelompok,
                start_time,
                end_time,
                jumlah_anggota,
                is_external,
                surat_izin,
                reschedule_request,
                asal_instansi,
                tanggal,
                keperluan,
                group_expire_at,
                submitted
            )
            VALUES
            (
                :id_pj,
                :id_ruangan,
                :booking_code,
                :kode_kelompok,
                :start_time,
                :end_time,
                :jumlah_anggota,
                :is_external,
                NULL,
                0,
                NULL,
                :tanggal,
                :keperluan,
                :group_expire_at,
                :submitted
            )";

        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            'id_pj'           => $data['id_pj'],
            'id_ruangan'      => $data['id_ruangan'],
            'booking_code'    => $data['booking_code'],
            'kode_kelompok'   => $data['kode_kelompok'],
            'start_time'      => $data['start_time'],
            'end_time'        => $data['end_time'],
            // mulai dari 0, lalu PJ & anggota lain dimasukkan via addMember()
            'jumlah_anggota'  => 0,
            'is_external'     => 0,
            'tanggal'         => $data['tanggal'],
            'keperluan'       => $data['keperluan'] ?? null,
            'group_expire_at' => $data['group_expire_at'],
            'submitted'       => $data['submitted'] ?? 0,
        ]);

        return (int)self::$db->lastInsertId();
    }

    public function findByKodeKelompok(string $kode): ?array
    {
        $sql = "SELECT * FROM " . self::$table . "
                WHERE kode_kelompok = :kode
                LIMIT 1";

        $stmt = self::$db->prepare($sql);
        $stmt->execute(['kode' => $kode]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function markSubmitted(int $idBooking): void
    {
        $sql = "UPDATE " . self::$table . " 
                SET submitted = 1 
                WHERE id_bookings = :id";

        $stmt = self::$db->prepare($sql);
        $stmt->execute(['id' => $idBooking]);
    }

    /**
     * Cancel booking oleh PJ.
     */
    public function cancelBooking(int $idBooking, int $idUser): array
    {
        $sql = "
            SELECT 
                b.id_bookings,
                b.id_pj,
                b.submitted,
                b.start_time,
                b.end_time,
                (
                    SELECT bs.status
                    FROM Booking_status bs
                    WHERE bs.id_bookings = b.id_bookings
                    ORDER BY bs.created_at DESC, bs.id_status DESC
                    LIMIT 1
                ) AS last_status
            FROM " . self::$table . " b
            WHERE b.id_bookings = :id
              AND b.id_pj = :user
            LIMIT 1
        ";

        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            ':id'   => $idBooking,
            ':user' => $idUser,
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return [
                'success' => false,
                'message' => 'Booking tidak ditemukan atau Anda bukan PJ.',
            ];
        }

        $currentStatus = $row['last_status'] ?? ($row['submitted'] ? 'pending' : 'draft');

        // Hanya boleh batalkan kalau status pending / approved
        if (!in_array($currentStatus, ['pending', 'approved'], true)) {
            return [
                'success' => false,
                'message' => 'Booking dengan status ini tidak dapat dibatalkan.',
            ];
        }

        // Tidak boleh cancel kalau sudah lewat / sedang berjalan
        $now = date('Y-m-d H:i:s');
        if ($row['start_time'] <= $now) {
            return [
                'success' => false,
                'message' => 'Booking yang sudah berjalan / lewat tidak dapat dibatalkan.',
            ];
        }

        // Tambahkan status cancelled
        $this->addStatus(
            $idBooking,
            'cancelled',
            'Dibatalkan oleh PJ',
            null,
            null
        );

        // Increment counter suspend
        $accSuspend = new \App\Models\AccountSuspend();
        $accSuspend->incrementCancel($idUser);

        return ['success' => true];
    }
}
