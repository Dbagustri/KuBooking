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

    public function getAvailableSlotsInternal()
    {
        Auth::requireRole(['admin', 'super_admin']);

        $idRuangan = (int)($this->input('id_ruangan') ?? 0);
        $tanggal   = $this->input('tanggal');

        // Validasi awal
        if ($this->isWeekend($tanggal)) {
            return $this->jsonResponse([
                'success' => true,
                'slots'   => [],
                'message' => 'Peminjaman tidak tersedia pada Sabtu/Minggu. Pilih hari kerja (Senin–Jumat).',
            ]);
        }
        if (
            !$idRuangan ||
            !$tanggal ||
            !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)
        ) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Data ruangan atau tanggal tidak valid.',
            ]);
        }

        $roomModel    = new Room();
        $bookingModel = new BookingUser();

        $room = $roomModel->findById($idRuangan);
        if (!$room) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Ruangan tidak ditemukan.',
            ]);
        }
        $OPEN_TIME  = '08:00:00';
        $CLOSE_TIME = '16:00:00';

        $openTs  = strtotime($tanggal . ' ' . $OPEN_TIME);
        $closeTs = strtotime($tanggal . ' ' . $CLOSE_TIME);

        if ($closeTs <= $openTs) {
            return $this->jsonResponse([
                'success' => true,
                'slots'   => [],
                'message' => 'Jam operasional perpustakaan tidak valid.',
            ]);
        }
        $workingTimes = [];
        for ($t = $openTs; $t < $closeTs; $t += 30 * 60) {
            $workingTimes[] = date('H:i', $t);
        }
        $workingTimes = array_values(array_unique($workingTimes));
        $today = date('Y-m-d');
        if ($tanggal === $today) {
            $nowTs = time();
            $workingTimes = array_filter($workingTimes, function ($time) use ($tanggal, $nowTs) {
                $slotTs = strtotime($tanggal . ' ' . $time . ':00');
                return $slotTs > $nowTs;
            });
            $workingTimes = array_values($workingTimes);
        }

        if (empty($workingTimes)) {
            return $this->jsonResponse([
                'success' => true,
                'slots'   => [],
                'message' => 'Tidak ada slot yang tersedia pada hari tersebut.',
            ]);
        }
        $disabled = $bookingModel->getDisabledSlotsForRoomDate($idRuangan, $tanggal);
        $disabled = array_unique($disabled);
        $freeTimes = array_values(array_diff($workingTimes, $disabled));
        sort($freeTimes);

        if (empty($freeTimes)) {
            return $this->jsonResponse([
                'success' => true,
                'slots'   => [],
                'message' => 'Tidak ada slot yang tersedia pada hari dan ruangan yang dipilih.',
            ]);
        }
        $freeSet = array_flip($freeTimes);

        $slots = [];

        foreach ($freeTimes as $startTime) {
            $startTs = strtotime($tanggal . ' ' . $startTime . ':00');
            $maxDur  = 0;
            for ($dur = 1; $dur <= 3; $dur++) {
                $endTs = $startTs + $dur * 3600;
                $ok    = true;
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

        return $this->jsonResponse([
            'success' => true,
            'slots'   => $slots,
            'message' => empty($slots)
                ? 'Tidak ada slot yang tersedia pada hari dan ruangan yang dipilih.'
                : '',
        ]);
    }

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
