<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class AdminAccount extends Model
{
    /**
     * List admin dengan pagination + filter status + search.
     *
     * @param int    $page         Halaman saat ini (1-based)
     * @param int    $perPage      Data per halaman
     * @param string $statusFilter 'all' | 'aktif' | 'nonaktif'
     * @param string $search       kata kunci (nama / email / nim_nip)
     *
     * @return array [
     *   'data'        => array list admin,
     *   'total_pages' => int,
     *   'total_rows'  => int,
     * ]
     */
    public function getAdminList(
        int $page = 1,
        int $perPage = 20,
        string $statusFilter = 'all',
        string $search = ''
    ): array {
        if ($page < 1) {
            $page = 1;
        }
        if ($perPage < 1) {
            $perPage = 20;
        }

        $offset = ($page - 1) * $perPage;

        $where  = ["role IN ('admin', 'super_admin')"];
        $params = [];

        // Filter status_aktif
        if ($statusFilter === 'aktif' || $statusFilter === 'nonaktif') {
            $where[]                = 'status_aktif = :status_aktif';
            $params['status_aktif'] = $statusFilter;
        }

        // Search by nama / email / nim_nip
        if ($search !== '') {
            $where[]      = '(nama LIKE :kw OR email LIKE :kw OR nim_nip LIKE :kw)';
            $params['kw'] = '%' . $search . '%';
        }

        $whereSql = '';
        if (!empty($where)) {
            $whereSql = 'WHERE ' . implode(' AND ', $where);
        }

        // Hitung total baris
        $countSql = "
            SELECT COUNT(*) AS total
            FROM Account
            $whereSql
        ";
        $countStmt = self::$db->prepare($countSql);
        $countStmt->execute($params);
        $totalRow = (int)($countStmt->fetchColumn() ?: 0);

        $totalPages = $totalRow > 0 ? (int)ceil($totalRow / $perPage) : 1;

        // Ambil data pada halaman tertentu
        $sql = "
            SELECT 
                id_account,
                nama,
                nim_nip,
                email,
                role,
                status_aktif,
                created_at
            FROM Account
            $whereSql
            ORDER BY created_at DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = self::$db->prepare($sql);

        // Bind params filter/search
        foreach ($params as $key => $val) {
            $stmt->bindValue(':' . $key, $val);
        }

        // Bind limit & offset sebagai integer
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return [
            'data'        => $rows,
            'total_pages' => $totalPages,
            'total_rows'  => $totalRow,
        ];
    }

    /**
     * Ambil detail admin by id_account.
     * Hanya untuk role admin/super_admin.
     */
    public function findAdminById(int $id): ?array
    {
        $sql = "
            SELECT 
                id_account,
                nama,
                nim_nip,
                email,
                role,
                status_aktif,
                created_at
            FROM Account
            WHERE id_account = :id
              AND role IN ('admin', 'super_admin')
            LIMIT 1
        ";

        $stmt = self::$db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * Cek duplikasi NIM/NIP atau email di akun admin.
     * Bisa dipakai di controller untuk validasi sebelum create/update.
     *
     * @param string   $nim_nip
     * @param string   $email
     * @param int|null $ignoreId  id_account yang di-skip (saat update)
     */
    public function existsByNimOrEmail(string $nim_nip, string $email, ?int $ignoreId = null): bool
    {
        $sql = "
            SELECT COUNT(*) 
            FROM Account
            WHERE role IN ('admin', 'super_admin')
              AND (nim_nip = :nim_nip OR email = :email)
        ";

        $params = [
            'nim_nip' => $nim_nip,
            'email'   => $email,
        ];

        if ($ignoreId !== null) {
            $sql .= " AND id_account <> :ignore_id";
            $params['ignore_id'] = $ignoreId;
        }

        $stmt = self::$db->prepare($sql);
        $stmt->execute($params);

        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Tambah admin baru.
     * $data = [
     *   'nama', 'nim_nip', 'email', 'password', 'role', 'status_aktif'
     * ]
     */
    public function createAdmin(array $data): int
    {
        $sql = "
            INSERT INTO Account
                (nama, nim_nip, email, password, role, status_aktif, created_at)
            VALUES
                (:nama, :nim_nip, :email, :password, :role, :status_aktif, NOW())
        ";

        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            'nama'         => $data['nama'],
            'nim_nip'      => $data['nim_nip'],
            'email'        => $data['email'],
            'password'     => $data['password'],      // sudah di-hash di controller
            'role'         => $data['role'],          // 'admin' atau 'super_admin'
            'status_aktif' => $data['status_aktif'],  // 'aktif' atau 'nonaktif'
        ]);

        return (int) self::$db->lastInsertId();
    }

    /**
     * Update data admin (tanpa password).
     * Kalau mau ubah password, pakai updateAdminPassword().
     *
     * $data = [
     *   'nama', 'nim_nip', 'email', 'role', 'status_aktif'
     * ]
     */
    public function updateAdmin(int $id, array $data): bool
    {
        $sql = "
            UPDATE Account
            SET nama = :nama,
                nim_nip = :nim_nip,
                email = :email,
                role = :role,
                status_aktif = :status_aktif
            WHERE id_account = :id
              AND role IN ('admin', 'super_admin')
        ";

        $stmt = self::$db->prepare($sql);
        return $stmt->execute([
            'nama'         => $data['nama'],
            'nim_nip'      => $data['nim_nip'],
            'email'        => $data['email'],
            'role'         => $data['role'],
            'status_aktif' => $data['status_aktif'],
            'id'           => $id,
        ]);
    }

    /**
     * Update password admin (sudah di-hash di controller).
     */
    public function updateAdminPassword(int $id, string $hashedPassword): bool
    {
        $sql = "
            UPDATE Account
            SET password = :password
            WHERE id_account = :id
              AND role IN ('admin', 'super_admin')
        ";

        $stmt = self::$db->prepare($sql);
        return $stmt->execute([
            'password' => $hashedPassword,
            'id'       => $id,
        ]);
    }

    /**
     * Set status aktif/nonaktif admin.
     */
    public function setAdminStatus(int $id, string $status): bool
    {
        $sql = "
            UPDATE Account
            SET status_aktif = :status
            WHERE id_account = :id
              AND role IN ('admin', 'super_admin')
        ";

        $stmt = self::$db->prepare($sql);
        return $stmt->execute([
            'status' => $status,
            'id'     => $id,
        ]);
    }

    /**
     * Hapus admin (hanya admin/super_admin).
     */
    public function deleteAdmin(int $id): bool
    {
        $sql = "
            DELETE FROM Account
            WHERE id_account = :id
              AND role IN ('admin', 'super_admin')
        ";

        $stmt = self::$db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
