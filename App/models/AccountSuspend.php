<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class AccountSuspend extends Model
{
    protected static $table = 'Account_suspend';

    public function __construct()
    {
        parent::__construct();
    }

    public function findByUserId(int $idUser): ?array
    {
        $sql  = "SELECT * FROM " . self::$table . " WHERE id_user = :id LIMIT 1";
        $stmt = self::$db->prepare($sql);
        $stmt->execute(['id' => $idUser]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function incrementCancel(int $idUser): array
    {
        try {
            self::$db->beginTransaction();

            // Ambil data suspend user (LOCK)
            $sql  = "SELECT * FROM " . self::$table . " WHERE id_user = :id FOR UPDATE";
            $stmt = self::$db->prepare($sql);
            $stmt->execute(['id' => $idUser]);
            $row  = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                // belum ada record, insert baru
                $suspendCount = 1;

                $ins = self::$db->prepare("
                    INSERT INTO " . self::$table . "
                        (id_user, suspend_count, suspended, tanggal_suspend, tanggal_berakhir, alasan)
                    VALUES
                        (:id_user, :suspend_count, 'no', NULL, NULL, NULL)
                ");
                $ins->execute([
                    'id_user'       => $idUser,
                    'suspend_count' => $suspendCount,
                ]);

                $isSuspended = false;
            } else {
                $suspendCount = (int)$row['suspend_count'] + 1;
                $isSuspended  = ($row['suspended'] === 'yes');

                // update counter
                $upd = self::$db->prepare("
                    UPDATE " . self::$table . "
                    SET suspend_count = :cnt
                    WHERE id_suspend = :id_suspend
                ");
                $upd->execute([
                    'cnt'        => $suspendCount,
                    'id_suspend' => $row['id_suspend'],
                ]);
            }

            // Kalau belum suspended dan sudah mencapai 3 kali → suspend akun
            if (!$isSuspended && $suspendCount >= 3) {
                // update Account_suspend
                $upd2 = self::$db->prepare("
                    UPDATE " . self::$table . "
                    SET suspended = 'yes',
                        tanggal_suspend = NOW(),
                        alasan = :alasan
                    WHERE id_user = :id_user
                ");
                $upd2->execute([
                    'alasan'  => 'Suspend otomatis: 3x membatalkan booking.',
                    'id_user' => $idUser,
                ]);

                // update Account.status_aktif
                $updAcc = self::$db->prepare("
                    UPDATE Account
                    SET status_aktif = 'nonaktif'
                    WHERE id_account = :id
                ");
                $updAcc->execute(['id' => $idUser]);

                $isSuspended = true;
            }

            self::$db->commit();

            return [
                'success'       => true,
                'suspend_count' => $suspendCount,
                'is_suspended'  => $isSuspended,
            ];
        } catch (\Exception $e) {
            self::$db->rollBack();
            return [
                'success'       => false,
                'suspend_count' => 0,
                'is_suspended'  => false,
            ];
        }
    }
}
