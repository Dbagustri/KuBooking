<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Room;
use App\Models\BookingUser;
use App\Models\Account;

class AdminAjaxController extends Controller
{
    private function jsonResponse(array $data): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    /**
     * Ambil slot jam mulai + durasi maksimal untuk booking internal.
     * Input: id_ruangan, tanggal (Y-m-d)
     * Output: { success: true, slots: { "08:00": 2, "09:30": 1, ... }, message?: "" }
     */
    public function getAvailableSlotsInternal()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $idRuangan = (int)($this->input('id_ruangan') ?? 0);
        $tanggal   = $this->input('tanggal');

        if (
            !$idRuangan ||
            !$tanggal ||
            !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)
        ) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Data ruangan atau tanggal tidak valid.',
            ]);
        }

        $roomModel    = new Room();
        $bookingModel = new BookingUser();

        $room = $roomModel->findById($idRuangan);
        if (!$room) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Ruangan tidak ditemukan.',
            ]);
        }

        // Ambil jadwal ruangan untuk hari tersebut
        $scheduleRows = $roomModel->getScheduleByRoom($idRuangan);
        if (empty($scheduleRows)) {
            $this->jsonResponse([
                'success' => true,
                'slots'   => [],
                'message' => 'Ruangan tidak memiliki jadwal operasional.',
            ]);
        }

        $dayOfWeek = date('N', strtotime($tanggal)); // 1 (Senin) - 7 (Minggu)
        $mapHari   = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];
        $hari = $mapHari[$dayOfWeek] ?? null;

        $schedule = null;
        foreach ($scheduleRows as $row) {
            if (isset($row['hari']) && $row['hari'] === $hari) {
                $schedule = $row;
                break;
            }
        }

        if (!$schedule || empty($schedule['open_time']) || empty($schedule['close_time'])) {
            $this->jsonResponse([
                'success' => true,
                'slots'   => [],
                'message' => 'Ruangan tidak beroperasi pada hari yang dipilih.',
            ]);
        }

        $openTime   = $schedule['open_time'];   // '08:00:00'
        $closeTime  = $schedule['close_time'];  // '16:00:00'
        $breakStart = $schedule['break_start'] ?? null;
        $breakEnd   = $schedule['break_end'] ?? null;

        $openTs  = strtotime($tanggal . ' ' . $openTime);
        $closeTs = strtotime($tanggal . ' ' . $closeTime);

        if ($closeTs <= $openTs) {
            $this->jsonResponse([
                'success' => true,
                'slots'   => [],
                'message' => 'Jam operasional ruangan tidak valid.',
            ]);
        }

        // Bangun segmen kerja (memisahkan jam istirahat jika ada)
        $segments = [];
        if ($breakStart && $breakEnd) {
            $bs = strtotime($tanggal . ' ' . $breakStart);
            $be = strtotime($tanggal . ' ' . $breakEnd);

            if ($bs > $openTs) {
                $segments[] = [$openTs, $bs];
            }
            if ($be < $closeTs) {
                $segments[] = [$be, $closeTs];
            }
        } else {
            $segments[] = [$openTs, $closeTs];
        }

        if (empty($segments)) {
            $this->jsonResponse([
                'success' => true,
                'slots'   => [],
                'message' => 'Tidak ada jam operasional yang tersedia pada hari tersebut.',
            ]);
        }

        // Semua slot 30 menit yang diperbolehkan (jam kerja)
        $workingTimes = [];
        foreach ($segments as [$segStart, $segEnd]) {
            for ($t = $segStart; $t < $segEnd; $t += 30 * 60) {
                $workingTimes[] = date('H:i', $t);
            }
        }
        $workingTimes = array_values(array_unique($workingTimes));

        // Ambil slot yang sudah terpakai berdasarkan booking aktif
        $disabled = $bookingModel->getDisabledSlotsForRoomDate($idRuangan, $tanggal);
        $disabled = array_unique($disabled);

        // Free = jam operasional - jam yang sudah dibooking
        $freeTimes = array_values(array_diff($workingTimes, $disabled));
        sort($freeTimes);

        // Set untuk cek cepat
        $freeSet = array_flip($freeTimes);

        $slots = []; // 'H:i' => maxDur (1–3 jam)

        foreach ($freeTimes as $startTime) {
            $startTs = strtotime($tanggal . ' ' . $startTime);
            $maxDur  = 0;

            // Coba durasi 1–3 jam
            for ($dur = 1; $dur <= 3; $dur++) {
                $endTs = $startTs + $dur * 3600;
                $ok    = true;

                // cek tiap 30 menit dari start sampai sebelum end
                for ($t = $startTs; $t < $endTs; $t += 30 * 60) {
                    $key = date('H:i', $t);
                    if (!isset($freeSet[$key])) {
                        $ok = false;
                        break;
                    }
                }

                if (!$ok) {
                    break;
                }

                $maxDur = $dur;
            }

            if ($maxDur > 0) {
                $slots[$startTime] = $maxDur;
            }
        }

        ksort($slots);

        $this->jsonResponse([
            'success' => true,
            'slots'   => $slots,
            'message' => empty($slots)
                ? 'Tidak ada slot yang tersedia pada hari dan ruangan yang dipilih.'
                : '',
        ]);
    }

    /**
     * Cek user berdasarkan NIM:
     * - Harus ada di tabel Account
     * - status_aktif = 'aktif'
     * - Tidak memiliki booking aktif (getActiveBookingForUser)
     * - Tidak memiliki booking 'selesai' yang belum ia rating
     *
     * Input: nim
     * Output: { success: true, user: {id_account, nama, nim_nip, role, status_aktif} } atau error.
     */
    public function checkUserByNim()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $nim = trim($this->input('nim') ?? '');
        if ($nim === '') {
            $this->jsonResponse([
                'success' => false,
                'message' => 'NIM wajib diisi.',
            ]);
        }

        $accountModel = new Account();
        $bookingModel = new BookingUser();

        $user = $accountModel->findByNimNip($nim);
        if (!$user) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'NIM tidak ditemukan di database.',
            ]);
        }

        if (($user['status_aktif'] ?? '') !== 'aktif') {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Akun dengan NIM ini tidak aktif.',
            ]);
        }

        $idUser = (int)$user['id_account'];
        if ($idUser <= 0) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Data akun tidak lengkap (id_account tidak valid).',
            ]);
        }

        // Cek booking aktif
        $activeBooking = $bookingModel->getActiveBookingForUser($idUser);
        if ($activeBooking) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'User ini masih memiliki peminjaman aktif.',
            ]);
        }

        // ✅ Cek booking selesai yang belum diberi rating oleh user ini
        $unrated = $bookingModel->getUnratedFinishedBookingForUser($idUser);
        if ($unrated) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'User ini memiliki peminjaman yang sudah selesai namun belum memberi rating. ' .
                    'Minta user untuk memberi rating terlebih dahulu sebelum dibuatkan booking baru.',
            ]);
        }

        $this->jsonResponse([
            'success' => true,
            'user'    => [
                'id_account'   => $user['id_account'],
                'nama'         => $user['nama'],
                'nim_nip'      => $user['nim_nip'],
                'role'         => $user['role'] ?? null,
                'status_aktif' => $user['status_aktif'] ?? null,
            ],
        ]);
    }
}
