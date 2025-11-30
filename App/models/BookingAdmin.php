<?php

namespace App\Models;

use PDO;

class BookingAdmin extends BookingBase
{
    /**
     * Hitung booking hari ini (untuk dashboard)
     */
    public function countToday()
    {
        $sql = "SELECT COUNT(*) AS total 
                FROM " . self::$table . "
                WHERE tanggal = CURDATE()";
        $stmt = self::$db->query($sql);
        $row  = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['total'] ?? 0);
    }

    /**
     * Booking pending saja (kalau masih mau dipakai di dashboard)
     */
    public function getPendingForDashboard($limit = 10)
    {
        $sql = "
            SELECT 
                b.id_bookings,
                b.booking_code,
                b.jumlah_anggota,
                b.start_time,
                b.end_time,
                b.tanggal,
                r.nama_ruangan,
                r.kapasitas_max,
                acc.nama AS pj_nama,
                bs.status
            FROM Bookings b
            LEFT JOIN Account acc ON b.id_pj = acc.id_account
            LEFT JOIN Ruangan r ON b.id_ruangan = r.id_ruangan
            LEFT JOIN Booking_status bs 
                ON bs.id_status = (
                    SELECT MAX(bs2.id_status) 
                    FROM Booking_status bs2 
                    WHERE bs2.id_bookings = b.id_bookings
                )
            WHERE bs.status = 'pending'
            ORDER BY b.start_time ASC
            LIMIT :limit
        ";

        $stmt = self::$db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($rows as $row) {
            $start = strtotime($row['start_time']);
            $end   = strtotime($row['end_time']);

            $tanggalLabel = date('d M', strtotime($row['tanggal']));
            $jamLabel     = date('H:i', $start) . '–' . date('H:i', $end);

            $result[] = [
                'id'        => $row['id_bookings'],
                'kode'      => $row['booking_code'],
                'pj'        => $row['pj_nama'] ?? '-',
                'ruang'     => $row['nama_ruangan'] ?? '-',
                'waktu'     => $tanggalLabel . ', ' . $jamLabel,
                'kapasitas' => ($row['jumlah_anggota'] ?? 0) . ' / ' . ($row['kapasitas_max'] ?? '-'),
                'status'    => ucfirst($row['status'] ?? 'pending'),
            ];
        }

        return $result;
    }

    /**
     * Ambil SEMUA booking untuk halaman kelola (dengan join room & pj)
     */
    public function getAllForAdmin($limit = 200, $offset = 0)
    {
        $sql = "
            SELECT 
                b.id_bookings,
                b.booking_code,
                b.jumlah_anggota,
                b.start_time,
                b.end_time,
                b.tanggal,
                b.is_external,
                b.guest_name,
                r.nama_ruangan,
                r.kapasitas_max,
                acc.nama AS pj_nama,
                (
                    SELECT bs2.status
                    FROM Booking_status bs2
                    WHERE bs2.id_bookings = b.id_bookings
                    ORDER BY bs2.created_at DESC, bs2.id_status DESC
                    LIMIT 1
                ) AS status
            FROM Bookings b
            LEFT JOIN Account acc ON b.id_pj = acc.id_account
            LEFT JOIN Ruangan r ON b.id_ruangan = r.id_ruangan
            ORDER BY b.tanggal DESC, b.start_time DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = self::$db->prepare($sql);
        $stmt->bindValue(':limit',  (int)$limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = [];

        foreach ($rows as $row) {
            $start = strtotime($row['start_time']);
            $end   = strtotime($row['end_time']);

            $tanggalLabel = date('d M', strtotime($row['tanggal']));
            $jamLabel     = date('H:i', $start) . '–' . date('H:i', $end);

            // jika eksternal, pakai guest_name kalau PJ kosong
            $pjName = $row['pj_nama'] ?: ($row['guest_name'] ?: '-');

            $result[] = [
                'id'        => $row['id_bookings'],
                'kode'      => $row['booking_code'],
                'pj'        => $pjName,
                'ruang'     => $row['nama_ruangan'] ?? '-',
                'waktu'     => $tanggalLabel . ', ' . $jamLabel,
                'kapasitas' => ($row['jumlah_anggota'] ?? 0) . ' / ' . ($row['kapasitas_max'] ?? '-'),
                'status'    => $row['status'] ?? 'pending',
            ];
        }

        return $result;
    }

    /**
     * Detail booking untuk admin (booking + room + pj + members)
     */
    public function findAdminDetail($idBooking)
    {
        $sql = "
            SELECT 
                b.*,
                r.nama_ruangan,
                r.lokasi,
                r.kapasitas_min,
                r.kapasitas_max,
                acc.nama  AS pj_nama,
                acc.email AS pj_email,
                (
                    SELECT bs2.status
                    FROM Booking_status bs2
                    WHERE bs2.id_bookings = b.id_bookings
                    ORDER BY bs2.created_at DESC, bs2.id_status DESC
                    LIMIT 1
                ) AS last_status
            FROM Bookings b
            LEFT JOIN Ruangan r ON r.id_ruangan = b.id_ruangan
            LEFT JOIN Account acc ON acc.id_account = b.id_pj
            WHERE b.id_bookings = :id
            LIMIT 1
        ";
        $stmt = self::$db->prepare($sql);
        $stmt->execute(['id' => $idBooking]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$booking) return null;

        $members = $this->getMembers($idBooking);
        $booking['members'] = $members;

        return $booking;
    }

    /**
     * Generate booking_code random 8 char
     */
    protected function generateBookingCode()
    {
        return substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
    }

    /**
     * CREATE booking internal (pj = user internal)
     * Expect $data:
     * - id_pj, id_ruangan, tanggal (Y-m-d), jam_mulai (HH:MM), durasi (jam),
     * - jumlah_anggota, keperluan
     */
    public function createInternalBooking(array $data)
    {
        $idRuangan  = (int)($data['id_ruangan'] ?? 0);
        $idPj       = (int)($data['id_pj'] ?? 0);
        $tanggal    = $data['tanggal'] ?? null;
        $jamMulai   = $data['jam_mulai'] ?? null;
        $durasi     = (int)($data['durasi'] ?? 0);
        $jml        = (int)($data['jumlah_anggota'] ?? 1);
        $keperluan  = $data['keperluan'] ?? null;

        if (!$idRuangan || !$idPj || !$tanggal || !$jamMulai || !$durasi) {
            return false;
        }

        // batas durasi 1–3 jam
        if ($durasi < 1 || $durasi > 3) {
            return false;
        }

        $start = $tanggal . ' ' . $jamMulai . ':00';
        $end   = date('Y-m-d H:i:s', strtotime("$start +{$durasi} hour"));
        // cek bentrok jadwal
        if ($this->isBentrok($idRuangan, $start, $end)) {
            return false;
        }

        $kode = $this->generateBookingCode();

        try {
            self::$db->beginTransaction();

            $sql = "INSERT INTO " . self::$table . "
                (id_pj, id_ruangan, booking_code,
                 start_time, end_time, jumlah_anggota,
                 is_external, tanggal, keperluan, submitted)
                VALUES
                (:id_pj, :id_ruangan, :booking_code,
                 :start_time, :end_time, 0,
                 0, :tanggal, :keperluan, 1)";

            $stmt = self::$db->prepare($sql);
            $stmt->execute([
                'id_pj'        => $idPj,
                'id_ruangan'   => $idRuangan,
                'booking_code' => $kode,
                'start_time'   => $start,
                'end_time'     => $end,
                'tanggal'      => $tanggal,
                'keperluan'    => $keperluan,
            ]);

            $idBooking = (int) self::$db->lastInsertId();

            // Masukkan PJ sebagai member → jumlah_anggota jadi 1
            $this->addMember($idBooking, $idPj);

            // Set status awal: pending (meniru flow user)
            $this->addStatus($idBooking, 'pending');

            self::$db->commit();

            return $idBooking;
        } catch (\Exception $e) {
            self::$db->rollBack();
            return false;
        }
    }

    public function createExternalBooking(array $data)
    {
        $idRuangan  = (int)($data['id_ruangan'] ?? 0);
        $tanggal    = $data['tanggal'] ?? null;
        $jamMulai   = $data['jam_mulai'] ?? null;
        $durasi     = (int)($data['durasi'] ?? 0);
        $jml        = (int)($data['jumlah_anggota'] ?? 1);
        $keperluan  = $data['keperluan'] ?? null;

        $guestName  = $data['guest_name'] ?? null;
        $guestEmail = $data['guest_email'] ?? null;
        $guestPhone = $data['guest_phone'] ?? null;
        $asal       = $data['asal_instansi'] ?? null;
        $surat      = $data['surat_izin'] ?? null;

        if (!$idRuangan || !$tanggal || !$jamMulai || !$durasi || !$guestName) {
            return false;
        }

        $start = $tanggal . ' ' . $jamMulai . ':00';
        $end   = date('Y-m-d H:i:s', strtotime("$start +{$durasi} hour"));

        if ($this->isBentrok($idRuangan, $start, $end)) {
            return false;
        }

        $kode = $this->generateBookingCode();

        $sql = "INSERT INTO " . self::$table . "
                (id_pj, id_ruangan, booking_code,
                 start_time, end_time, jumlah_anggota,
                 is_external, guest_name, guest_email, guest_phone,
                 asal_instansi, surat_izin,
                 tanggal, keperluan, submitted)
                VALUES
                (NULL, :id_ruangan, :booking_code,
                 :start_time, :end_time, :jumlah_anggota,
                 1, :guest_name, :guest_email, :guest_phone,
                 :asal_instansi, :surat_izin,
                 :tanggal, :keperluan, 1)";

        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            'id_ruangan'     => $idRuangan,
            'booking_code'   => $kode,
            'start_time'     => $start,
            'end_time'       => $end,
            'jumlah_anggota' => $jml,
            'guest_name'     => $guestName,
            'guest_email'    => $guestEmail,
            'guest_phone'    => $guestPhone,
            'asal_instansi'  => $asal,
            'surat_izin'     => $surat,
            'tanggal'        => $tanggal,
            'keperluan'      => $keperluan,
        ]);

        $idBooking = self::$db->lastInsertId();

        // Status awal: pending
        $this->addStatus($idBooking, 'pending');

        return $idBooking;
    }

    /**
     * UPDATE booking oleh admin.
     * Expect $data minimal:
     * - id_booking, id_ruangan, tanggal, jam_mulai, durasi, jumlah_anggota, keperluan (+ guest field jika eksternal)
     */
    public function updateAdminBooking(array $data)
    {
        $idBooking = (int)($data['id_booking'] ?? 0);
        if (!$idBooking) return false;

        $booking = $this->findWithRoom($idBooking);
        if (!$booking) return false;

        $idRuangan  = (int)($data['id_ruangan'] ?? $booking['id_ruangan']);
        $tanggal    = $data['tanggal']   ?? $booking['tanggal'];
        $jamMulai   = $data['jam_mulai'] ?? date('H:i', strtotime($booking['start_time']));
        $durasi     = (int)($data['durasi'] ?? 1);
        $jml        = (int)($data['jumlah_anggota'] ?? $booking['jumlah_anggota']);
        $keperluan  = $data['keperluan'] ?? $booking['keperluan'];

        // batas durasi 1–3 jam
        if ($durasi < 1 || $durasi > 3) {
            return false;
        }

        $start = $tanggal . ' ' . $jamMulai . ':00';
        $end   = date('Y-m-d H:i:s', strtotime("$start +{$durasi} hour"));

        // cek bentrok, tapi abaikan booking ini sendiri
        if ($this->isBentrokExcept($idRuangan, $start, $end, $idBooking)) {
            return false;
        }

        if ((int)$booking['is_external'] === 1) {
            $guestName  = $data['guest_name']  ?? $booking['guest_name'];
            $guestEmail = $data['guest_email'] ?? $booking['guest_email'];
            $guestPhone = $data['guest_phone'] ?? $booking['guest_phone'];
            $asal       = $data['asal_instansi'] ?? $booking['asal_instansi'];
            $surat      = $data['surat_izin'] ?? $booking['surat_izin'];

            $sql = "UPDATE " . self::$table . "
                SET id_ruangan   = :id_ruangan,
                    start_time   = :start_time,
                    end_time     = :end_time,
                    jumlah_anggota = :jumlah_anggota,
                    tanggal      = :tanggal,
                    keperluan    = :keperluan,
                    guest_name   = :guest_name,
                    guest_email  = :guest_email,
                    guest_phone  = :guest_phone,
                    asal_instansi = :asal_instansi,
                    surat_izin    = :surat_izin
                WHERE id_bookings = :id";
            $paramsExtra = [
                'guest_name'    => $guestName,
                'guest_email'   => $guestEmail,
                'guest_phone'   => $guestPhone,
                'asal_instansi' => $asal,
                'surat_izin'    => $surat,
            ];
        } else {
            $sql = "UPDATE " . self::$table . "
                SET id_ruangan   = :id_ruangan,
                    start_time   = :start_time,
                    end_time     = :end_time,
                    jumlah_anggota = :jumlah_anggota,
                    tanggal      = :tanggal,
                    keperluan    = :keperluan
                WHERE id_bookings = :id";
            $paramsExtra = [];
        }

        $params = array_merge([
            'id'             => $idBooking,
            'id_ruangan'     => $idRuangan,
            'start_time'     => $start,
            'end_time'       => $end,
            'jumlah_anggota' => $jml,
            'tanggal'        => $tanggal,
            'keperluan'      => $keperluan,
        ], $paramsExtra);

        $stmt = self::$db->prepare($sql);
        return $stmt->execute($params);
    }


    /**
     * AUTO CANCEL booking approved yang sudah lewat 10 menit dari jam mulai
     */
    public function autoCancelLateArrivals()
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
            b.tanggal = CURDATE()
            AND b.submitted = 1
            AND bs_latest.status = 'approved'
            AND b.checkin_time IS NULL       -- BELUM diklik Mulai
            AND b.start_time <= DATE_SUB(NOW(), INTERVAL 10 MINUTE)
    ";

        $stmt = self::$db->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
            $this->addStatus(
                $row['id_bookings'],
                'cancelled',
                'Auto cancel: anggota tidak hadir lengkap > 10 menit',
                null,
                null
            );
        }
    }


    public function approveReschedule(int $idReschedule): array
    {
        $resModel = new \App\Models\BookingReschedule();
        $res      = $resModel->findWithBooking($idReschedule);

        if (!$res) {
            return ['success' => false, 'message' => 'Data reschedule tidak ditemukan.'];
        }

        $idBooking = (int)$res['id_bookings'];

        // Pastikan status terakhir memang reschedule_pending
        $lastStatus = $this->getLastStatus($idBooking);
        if ($lastStatus !== 'reschedule_pending') {
            return [
                'success' => false,
                'message' => 'Reschedule ini sudah diproses atau tidak dalam status pending.'
            ];
        }

        // Cek bentrok jadwal baru
        // Cek bentrok jadwal baru, abaikan booking ini sendiri
        if ($this->isBentrokExcept(
            (int)$res['id_ruangan'],
            $res['new_start_time'],
            $res['new_end_time'],
            (int)$res['id_bookings']
        )) {
            return [
                'success' => false,
                'message' => 'Jadwal baru bentrok dengan booking lain.'
            ];
        }


        try {
            self::$db->beginTransaction();

            // Update Bookings dengan jadwal baru
            $sqlUpd = "UPDATE " . self::$table . "
                   SET id_ruangan = :id_ruangan,
                       start_time = :start_time,
                       end_time   = :end_time,
                       tanggal    = :tanggal
                   WHERE id_bookings = :id";

            $stmt = self::$db->prepare($sqlUpd);
            $stmt->execute([
                'id_ruangan' => $res['id_ruangan'],
                'start_time' => $res['new_start_time'],
                'end_time'   => $res['new_end_time'],
                'tanggal'    => $res['new_tanggal'],
                'id'         => $idBooking,
            ]);

            // Sinkron anggota dari Booking_reschedule_member → Booking_member
            $members = $resModel->getMembers($idReschedule);

            $del = self::$db->prepare("DELETE FROM Booking_member WHERE id_bookings = :id");
            $del->execute(['id' => $idBooking]);

            if (!empty($members)) {
                $ins = self::$db->prepare(
                    "INSERT INTO Booking_member (id_bookings, id_user)
                 VALUES (:id_bookings, :id_user)"
                );
                foreach ($members as $m) {
                    $ins->execute([
                        'id_bookings' => $idBooking,
                        'id_user'     => (int)$m['id_user'],
                    ]);
                }

                $updCount = self::$db->prepare(
                    "UPDATE " . self::$table . "
                 SET jumlah_anggota = :cnt
                 WHERE id_bookings = :id"
                );
                $updCount->execute([
                    'cnt' => count($members),
                    'id'  => $idBooking,
                ]);
            } else {
                $updCount = self::$db->prepare(
                    "UPDATE " . self::$table . "
                 SET jumlah_anggota = 0
                 WHERE id_bookings = :id"
                );
                $updCount->execute(['id' => $idBooking]);
            }

            // Tambah status reschedule_approved
            $this->addStatus(
                $idBooking,
                'reschedule_approved',
                null,
                $idReschedule,
                $res['alasan'] ?? null
            );

            self::$db->commit();

            return ['success' => true];
        } catch (\Exception $e) {
            self::$db->rollBack();
            return [
                'success' => false,
                'message' => 'Gagal menyetujui reschedule. Silakan coba lagi.'
            ];
        }
    }


    /**
     * REJECT reschedule oleh admin
     */
    public function rejectReschedule(int $idReschedule, ?string $alasan = null): array
    {
        $resModel = new \App\Models\BookingReschedule();
        $res      = $resModel->findWithBooking($idReschedule);

        if (!$res) {
            return ['success' => false, 'message' => 'Data reschedule tidak ditemukan.'];
        }

        $idBooking = (int)$res['id_bookings'];

        $lastStatus = $this->getLastStatus($idBooking);
        if ($lastStatus !== 'reschedule_pending') {
            return [
                'success' => false,
                'message' => 'Reschedule ini sudah diproses atau tidak dalam status pending.'
            ];
        }

        $this->addStatus(
            $idBooking,
            'reschedule_rejected',
            $alasan,
            $idReschedule,
            $alasan
        );

        return ['success' => true];
    }
    /**
     * Membatalkan semua booking pada tanggal tertentu (YYYY-mm-dd)
     * tanpa menyentuh logic suspend.
     * 
     * return: jumlah booking yang di-cancel
     */
    public function cancelBookingsByDate(string $tanggal): int
    {
        // Ambil booking yang aktif di tanggal tsb
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
                b.tanggal = :tanggal
                AND b.submitted = 1
                AND (
                    bs_latest.status IS NULL
                    OR bs_latest.status NOT IN ('cancelled', 'rejected', 'selesai')
                )
        ";

        $stmt = self::$db->prepare($sql);
        $stmt->execute(['tanggal' => $tanggal]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) {
            return 0;
        }

        self::$db->beginTransaction();

        try {
            $count = 0;
            foreach ($rows as $row) {
                $this->addStatus(
                    (int)$row['id_bookings'],
                    'cancelled',
                    'Dibatalkan karena penutupan perpustakaan',
                    null,
                    null
                );
                $count++;
            }

            self::$db->commit();
            return $count;
        } catch (\Exception $e) {
            self::$db->rollBack();
            return 0;
        }
    }
    public function setCheckinTime(int $idBooking): bool
    {
        $sql = "UPDATE " . self::$table . "
            SET checkin_time = NOW()
            WHERE id_bookings = :id";

        $stmt = self::$db->prepare($sql);
        return $stmt->execute(['id' => $idBooking]);
    }
}
