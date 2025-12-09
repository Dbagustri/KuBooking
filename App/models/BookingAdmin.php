<?php

namespace App\Models;

use PDO;

class BookingAdmin extends BookingBase
{
    public function countToday()
    {
        $sql = "SELECT COUNT(*) AS total 
                FROM " . self::$table . "
                WHERE tanggal = CURDATE()";
        $stmt = self::$db->query($sql);
        $row  = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['total'] ?? 0);
    }
    public function getPendingForDashboard($limit = 10)
    {
        $sql = "
        SELECT 
            id_bookings, booking_code, jumlah_anggota, start_time, 
            end_time, tanggal, nama_ruangan, kapasitas_max, pj_nama, last_status
        FROM v_admin_booking
        WHERE last_status = 'pending'
        ORDER BY start_time ASC
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
                'status'    => ucfirst($row['last_status'] ?? 'pending'),
            ];
        }

        return $result;
    }

    public function getAllForAdmin($limit = 200, $offset = 0)
    {
        $sql = "
        SELECT 
            id_bookings, booking_code, jumlah_anggota, start_time, 
            end_time, tanggal, is_external, submitted, guest_name, 
            guest_email, guest_phone, asal_instansi,
            nama_ruangan, kapasitas_max, lokasi, pj_nama, pj_nim, last_status
        FROM v_admin_booking
        ORDER BY tanggal DESC, start_time DESC
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
            $pjName = $row['pj_nama'] ?: ($row['guest_name'] ?: '-');

            $result[] = [
                'id'             => (int)$row['id_bookings'],
                'kode'           => $row['booking_code'],
                'pj'             => $pjName,
                'pj_nim'         => $row['pj_nim'] ?? null,
                'ruang'          => $row['nama_ruangan'] ?? '-',
                'lokasi'         => $row['lokasi'] ?? null,
                'waktu'          => $tanggalLabel . ', ' . $jamLabel,
                'tanggal'        => $row['tanggal'],
                'kapasitas'      => ($row['jumlah_anggota'] ?? 0) . ' / ' . ($row['kapasitas_max'] ?? '-'),
                'jumlah_anggota' => (int)($row['jumlah_anggota'] ?? 0),
                'status'         => $row['last_status'],
                'is_external'    => (int)$row['is_external'],
                'submitted'      => (int)$row['submitted'],
                'guest_name'     => $row['guest_name'] ?? null,
                'guest_email'    => $row['guest_email'] ?? null,
                'guest_phone'    => $row['guest_phone'] ?? null,
                'asal_instansi'  => $row['asal_instansi'] ?? null,
                'tipe'           => ((int)$row['is_external'] === 1) ? 'external' : 'internal',
            ];
        }

        return $result;
    }


    public function findAdminDetail($idBooking)
    {
        $sql = "
        SELECT *
        FROM v_admin_booking
        WHERE id_bookings = :id
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

    protected function generateBookingCode()
    {
        return substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
    }
    public function createInternalBooking(array $data): int
    {
        $idRuangan   = (int)($data['id_ruangan'] ?? 0);
        $tanggal     = $data['tanggal'] ?? null;
        $jamMulai    = $data['jam_mulai'] ?? null;
        $durasi      = (int)($data['durasi'] ?? 0);
        $keperluan   = $data['keperluan'] ?? null;
        $members     = $data['members'] ?? [];
        $pjIdUser    = (int)($data['pj_id_user'] ?? 0);
        if (
            !$idRuangan ||
            !$tanggal ||
            !$jamMulai ||
            $durasi <= 0 ||
            empty($members) ||
            !$pjIdUser
        ) {
            return 0;
        }
        $start = $tanggal . ' ' . $jamMulai . ':00';
        $end   = date('Y-m-d H:i:s', strtotime($start . " + {$durasi} hour"));

        try {
            self::$db->beginTransaction();
            $sqlCheck = "
            SELECT COUNT(*) 
            FROM " . self::$table . " b
            WHERE b.id_ruangan = :room
              AND b.tanggal = :tanggal
              AND (
                    (b.start_time < :end_time AND b.end_time > :start_time)
                  )
        ";

            $stmtCheck = self::$db->prepare($sqlCheck);
            $stmtCheck->execute([
                'room'       => $idRuangan,
                'tanggal'    => $tanggal,
                'start_time' => $start,
                'end_time'   => $end,
            ]);

            $bentrok = (int)$stmtCheck->fetchColumn();
            if ($bentrok > 0) {
                self::$db->rollBack();
                return 0;
            }

            $bookingCode = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
            $sqlInsert = "
            INSERT INTO " . self::$table . " (
                id_pj,id_ruangan,booking_code,
                kode_kelompok,start_time,end_time,
                jumlah_anggota,is_external,surat_izin,
                reschedule_request,asal_instansi,tanggal,
                keperluan,group_expire_at,submitted
            ) VALUES (
                :id_pj,:id_ruangan,:booking_code,NULL,:start_time,:end_time,
                :jumlah_anggota,0,NULL,0,NULL,:tanggal,:keperluan,NULL,1
            )
        ";
            $stmtInsert = self::$db->prepare($sqlInsert);
            $stmtInsert->execute([
                'id_pj'          => $pjIdUser,
                'id_ruangan'     => $idRuangan,
                'booking_code'   => $bookingCode,
                'start_time'     => $start,
                'end_time'       => $end,
                'jumlah_anggota' => count($members),
                'tanggal'        => $tanggal,
                'keperluan'      => $keperluan,
            ]);

            $idBooking = (int)self::$db->lastInsertId();
            $sqlMember = "
            INSERT INTO Booking_member (id_bookings, id_user)
            VALUES (:id_bookings, :id_user)
        ";
            $stmtMember = self::$db->prepare($sqlMember);
            foreach ($members as $uid) {
                if (!ctype_digit((string)$uid)) {
                    continue;
                }
                $stmtMember->execute([
                    'id_bookings' => $idBooking,
                    'id_user'     => (int)$uid,
                ]);
            }
            $this->addStatus($idBooking, 'approved');

            self::$db->commit();
            return $idBooking;
        } catch (\Throwable $e) {
            if (self::$db->inTransaction()) {
                self::$db->rollBack();
            }
            return 0;
        }
    }


    public function createExternalBooking(array $data)
    {
        $idRuangan  = (int)($data['id_ruangan'] ?? 0);
        $tanggal    = $data['tanggal'] ?? null;
        $jamMulai   = $data['jam_mulai'] ?? null; // HH:MM
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
        if ($durasi < 1 || $durasi > 3) {
            return false;
        }
        $roomModel = new \App\Models\Room();
        $room      = $roomModel->findById($idRuangan);
        if (!$room) {
            return false;
        }
        $kapMin = (int)($room['kapasitas_min'] ?? 0);
        $kapMax = (int)($room['kapasitas_max'] ?? 0);

        if ($jml <= 0) {
            $jml = 1;
        }
        if ($kapMax > 0) {
            if (($kapMin > 0 && $jml < $kapMin) || $jml > $kapMax) {
                return false;
            }
        } elseif ($kapMin > 0 && $jml < $kapMin) {
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
        $this->addStatus($idBooking, 'approved');

        return $idBooking;
    }
    public function updateAdminBooking(array $data, array $memberIds = []): bool
    {
        self::$db->beginTransaction();

        try {
            $idBooking = (int)($data['id_booking'] ?? 0);
            $idRuangan = (int)($data['id_ruangan'] ?? 0);
            $tanggal   = $data['tanggal'] ?? null;
            $jamMulai  = $data['jam_mulai'] ?? null;
            $durasi    = (int)($data['durasi'] ?? 0);
            $keperluan = $data['keperluan'] ?? '';
            if ($idBooking <= 0 || $idRuangan <= 0 || !$tanggal || !$jamMulai || $durasi <= 0) {
                self::$db->rollBack();
                return false;
            }
            $start = $tanggal . ' ' . $jamMulai . ':00';
            $end   = date('Y-m-d H:i:s', strtotime($start . " + {$durasi} hour"));
            if ($this->isBentrokExcept($idRuangan, $start, $end, $idBooking)) {
                self::$db->rollBack();
                return false;
            }

            if (empty($memberIds)) {
                self::$db->rollBack();
                return false;
            }

            $jumlahAnggota = count($memberIds);
            $sql = "UPDATE bookings
                SET id_ruangan      = :id_ruangan,
                    start_time      = :start_time,
                    end_time        = :end_time,
                    tanggal         = :tanggal,
                    jumlah_anggota  = :jumlah_anggota,
                    keperluan       = :keperluan
                WHERE id_bookings   = :id_booking";

            $stmt = self::$db->prepare($sql);
            $stmt->execute([
                'id_ruangan'     => $idRuangan,
                'start_time'     => $start,
                'end_time'       => $end,
                'tanggal'        => $tanggal,
                'jumlah_anggota' => $jumlahAnggota,
                'keperluan'      => $keperluan,
                'id_booking'     => $idBooking,
            ]);
            $del = self::$db->prepare("DELETE FROM Booking_member WHERE id_bookings = :id");
            $del->execute(['id' => $idBooking]);
            $ins = self::$db->prepare("
            INSERT INTO Booking_member (id_bookings, id_user)
            VALUES (:id_bookings, :id_user)
        ");

            foreach ($memberIds as $uid) {
                $ins->execute([
                    'id_bookings' => $idBooking,
                    'id_user'     => (int)$uid,
                ]);
            }
            $firstPj = (int)$memberIds[0];
            if ($firstPj > 0) {
                $upPj = self::$db->prepare("
                UPDATE bookings
                SET id_pj = :id_pj
                WHERE id_bookings = :id_booking
            ");
                $upPj->execute([
                    'id_pj'      => $firstPj,
                    'id_booking' => $idBooking,
                ]);
            }

            self::$db->commit();
            return true;
        } catch (\Throwable $e) {
            self::$db->rollBack();
            return false;
        }
    }
    public function autoCancelLateArrivals()
    {
        $sql = "
        SELECT 
            b.id_bookings,
            b.id_pj
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
            AND bs_latest.status IN ('approved', 'reschedule_approved')
            AND b.checkin_time IS NULL
            AND b.start_time <= DATE_SUB(NOW(), INTERVAL 10 MINUTE)
    ";
        $stmt = self::$db->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $accSuspend = new \App\Models\AccountSuspend();
        foreach ($rows as $row) {
            $bookingId = (int)$row['id_bookings'];
            $pjId      = !empty($row['id_pj']) ? (int)$row['id_pj'] : null;

            $this->addStatus(
                $bookingId,
                'cancelled',
                'Auto cancel: anggota tidak hadir lengkap > 10 menit',
                null,
                null
            );
            if ($pjId) {
                $accSuspend->incrementCancel($pjId);
            }
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
        $lastStatus = $this->getLastStatus($idBooking);
        if ($lastStatus !== 'reschedule_pending') {
            return [
                'success' => false,
                'message' => 'Reschedule ini sudah diproses atau tidak dalam status pending.'
            ];
        }
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

    public function cancelBookingsByDate(string $tanggal): int
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
    public function getPendingWithPagination(int $page = 1, int $limit = 10): array
    {
        $offset = ($page - 1) * $limit;
        $sqlCount = "
        SELECT COUNT(*)
        FROM v_admin_booking
        WHERE last_status = 'pending'
    ";
        $stmtCount = self::$db->query($sqlCount);
        $totalRows = (int)$stmtCount->fetchColumn();
        $totalPages = $totalRows > 0 ? (int)ceil($totalRows / $limit) : 1;
        $sql = "
        SELECT 
            id_bookings, booking_code, jumlah_anggota, start_time, 
            end_time, tanggal, nama_ruangan, kapasitas_max, pj_nama, last_status
        FROM v_admin_booking
        WHERE last_status = 'pending'
        ORDER BY start_time ASC
        LIMIT :limit OFFSET :offset
    ";

        $stmt = self::$db->prepare($sql);
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
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
                'status'    => ucfirst($row['last_status'] ?? 'pending'),
            ];
        }

        return [
            'list'         => $result,
            'current_page' => $page,
            'total_pages'  => $totalPages,
        ];
    }

    public function autoCancelLateBookings(): int
    {
        $sql = "
        SELECT b.id_bookings
        FROM Bookings b
        LEFT JOIN Booking_status bs_latest
            ON bs_latest.id_status = (
                SELECT MAX(bs2.id_status)
                FROM Booking_status bs2
                WHERE bs2.id_bookings = b.id_bookings
            )
        WHERE 
            b.submitted = 1
            AND b.checkin_time IS NULL
            AND b.start_time < DATE_SUB(NOW(), INTERVAL 10 MINUTE)
            AND (
                bs_latest.status IN ('approved', 'reschedule_approved')
            )
    ";

        $stmt = self::$db->query($sql);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $count = 0;
        foreach ($rows as $row) {
            $idBooking = (int)$row['id_bookings'];

            // Tambahkan status cancelled
            $this->addStatus(
                $idBooking,
                'cancelled',
                'Auto cancel: tidak check-in dalam 10 menit setelah jadwal mulai.',
                null,
                null
            );
            $count++;
        }

        return $count;
    }
    public function countAllForAdmin(): int
    {
        $sql = "
        SELECT COUNT(*)
        FROM v_admin_booking
        -- kalau mau exclude draft:
        -- WHERE last_status <> 'draft'
    ";
        $stmt = self::$db->query($sql);
        return (int)$stmt->fetchColumn();
    }

    public function hasAnyUpcomingBookings(): bool
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
                OR bs_latest.status NOT IN ('rejected','cancelled')
            )
        LIMIT 1
    ";

        $stmt = self::$db->query($sql);
        return (bool)$stmt->fetch(\PDO::FETCH_ASSOC);
    }


    public function cancelAllUpcomingBookings(): int
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
                OR bs_latest.status NOT IN ('rejected','cancelled')
            )
    ";

        $stmt = self::$db->query($sql);
        $bookings = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        $count = 0;
        foreach ($bookings as $idBooking) {
            $this->addStatus(
                (int)$idBooking,
                'cancelled',
                'Dibatalkan karena penutupan semua ruangan.'
            );
            $count++;
        }

        return $count;
    }
}
