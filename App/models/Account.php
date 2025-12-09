<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Account extends Model
{
    protected static $table = 'account';
    public function __construct()
    {
        parent::__construct();
    }
    public function findById($id)
    {
        $stmt = self::$db->prepare("SELECT * FROM " . self::$table . " WHERE id_account = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function findByNimNip($nim_nip)
    {
        $stmt = self::$db->prepare("SELECT * FROM " . self::$table . " WHERE nim_nip = :nim LIMIT 1");
        $stmt->execute(['nim' => $nim_nip]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function existsByNimOrEmail($nim_nip, $email)
    {
        $sql = "SELECT id_account FROM " . self::$table . " 
                WHERE nim_nip = :nim OR email = :email 
                LIMIT 1";
        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            'nim'   => $nim_nip,
            'email' => $email,
        ]);
        return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function countActive()
    {
        $sql = "SELECT COUNT(*) AS total
                FROM " . self::$table . "
                WHERE status_aktif = 'aktif'";
        $stmt = self::$db->query($sql);
        $row  = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) ($row['total'] ?? 0);
    }
    public function createFromRegistrasi(array $data)
    {
        $sql = "INSERT INTO " . self::$table . " 
        (id_registrasi, nama, jurusan, prodi, email, password, role, nim_nip, unit_jurusan, 
         angkatan, durasi_studi, aktif_sampai, status_aktif, screenshot_kubaca)
        VALUES
        (:id_registrasi, :nama, :jurusan, :prodi, :email, :password, :role, :nim_nip, :unit_jurusan,
         :angkatan, :durasi_studi, :aktif_sampai, :status_aktif, :screenshot_kubaca)";

        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            'id_registrasi'    => $data['id_registrasi'],
            'nama'             => $data['nama'],
            'jurusan'          => $data['jurusan'] ?? null,
            'prodi'            => $data['prodi'] ?? null,
            'email'            => $data['email'],
            'password'         => $data['password'],
            'role'             => $data['role'],
            'nim_nip'          => $data['nim_nip'],
            'unit_jurusan'     => $data['unit_jurusan'] ?? null,
            'angkatan'         => $data['angkatan'],
            'durasi_studi'     => $data['durasi_studi'],
            'aktif_sampai'     => $data['aktif_sampai'],
            'status_aktif'     => $data['status_aktif'] ?? 'aktif',
            'screenshot_kubaca' => $data['screenshot_kubaca'] ?? null,
        ]);
        return self::$db->lastInsertId();
    }
    public function getAdminUserList(int $page = 1, string $filter = '', string $search = '')
    {
        $limit  = 10;
        $offset = ($page - 1) * $limit;

        // kalau memang mau sembunyikan admin/super_admin di list default
        $where  = "WHERE role NOT IN ('admin','super_admin')";
        $params = [];

        if ($filter !== '') {
            $where .= " AND role = :filter";
            $params[':filter'] = $filter;
        }

        if ($search !== '') {
            $where .= " AND (nama LIKE :search 
                     OR email LIKE :search 
                     OR jurusan LIKE :search 
                     OR unit_jurusan LIKE :search 
                     OR nim_nip LIKE :search)";
            $params[':search'] = "%{$search}%";
        }

        // hitung total data
        $sqlCount  = "SELECT COUNT(*) FROM " . self::$table . " {$where}";
        $stmtCount = self::$db->prepare($sqlCount);
        $stmtCount->execute($params);
        $totalRows  = (int)$stmtCount->fetchColumn();
        $totalPages = $totalRows > 0 ? (int)ceil($totalRows / $limit) : 1;

        // AMBIL DATA LIST USER
        $sql = "SELECT 
                id_account,
                nama,
                nim_nip,          -- ✅ tambahkan ini
                email,
                jurusan,
                unit_jurusan,     -- ✅ dan ini biar view nggak kosong
                prodi,
                role,
                status_aktif,
                screenshot_kubaca
            FROM " . self::$table . "
            {$where}
            ORDER BY nama ASC
            LIMIT :limit OFFSET :offset";

        $stmt = self::$db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'users'        => $data,
            'current_page' => $page,
            'total_pages'  => $totalPages,
            'filter'       => $filter,
            'search'       => $search,
        ];
    }

    public function updateBasicProfile(int $id, array $data): bool
    {
        $fields = [];
        $params = ['id' => $id];
        if (isset($data['nama'])) {
            $fields[]         = 'nama = :nama';
            $params['nama']   = $data['nama'];
        }
        if (isset($data['email'])) {
            $fields[]         = 'email = :email';
            $params['email']  = $data['email'];
        }
        if (isset($data['status_aktif'])) {
            $fields[]               = 'status_aktif = :status_aktif';
            $params['status_aktif'] = $data['status_aktif'];
        }
        if (isset($data['role'])) {
            $fields[]       = 'role = :role';
            $params['role'] = $data['role'];
        }
        if (empty($fields)) {
            return true;
        }
        $sql  = "UPDATE " . self::$table . " SET " . implode(', ', $fields) . " WHERE id_account = :id";
        $stmt = self::$db->prepare($sql);
        return $stmt->execute($params);
    }
    public function updatePasswordByNimNip(string $nim_nip, string $hashedPassword): bool
    {
        $sql = "UPDATE " . self::$table . " 
            SET password = :password 
            WHERE nim_nip = :nim_nip
            LIMIT 1";

        $stmt = self::$db->prepare($sql);
        return $stmt->execute([
            'password' => $hashedPassword,
            'nim_nip'  => $nim_nip,
        ]);
    }
    public function updateStatusAktif(int $idAccount, string $status): bool
    {
        if (!in_array($status, ['aktif', 'nonaktif'], true)) {
            return false;
        }

        $sql = "UPDATE " . self::$table . " 
            SET status_aktif = :status
            WHERE id_account = :id
            LIMIT 1";

        $stmt = self::$db->prepare($sql);
        return $stmt->execute([
            'status' => $status,
            'id'     => $idAccount,
        ]);
    }
    public function deleteById(int $idAccount): bool
    {
        $sql = "DELETE FROM " . self::$table . " 
            WHERE id_account = :id
            LIMIT 1";

        $stmt = self::$db->prepare($sql);
        return $stmt->execute(['id' => $idAccount]);
    }
}
