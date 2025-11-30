<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Account extends Model
{
    protected static $table = 'Account';

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

    /**
     * Dipakai saat admin approve registrasi → buat akun di tabel Account
     */
    public function createFromRegistrasi(array $data)
    {
        $sql = "INSERT INTO " . self::$table . " 
            (id_registrasi, nama, jurusan, prodi, email, password, role, nim_nip, unit_jurusan, 
             angkatan, durasi_studi, aktif_sampai, status_aktif)
            VALUES
            (:id_registrasi, :nama, :jurusan, :prodi, :email, :password, :role, :nim_nip, :unit_jurusan,
             :angkatan, :durasi_studi, :aktif_sampai, :status_aktif)";

        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            'id_registrasi' => $data['id_registrasi'],
            'nama'          => $data['nama'],
            'jurusan'       => $data['jurusan'] ?? null,
            'prodi'         => $data['prodi'] ?? null,
            'email'         => $data['email'],
            'password'      => $data['password'],
            'role'          => $data['role'],
            'nim_nip'       => $data['nim_nip'],
            'unit_jurusan'  => $data['unit_jurusan'] ?? null,
            'angkatan'      => $data['angkatan'],
            'durasi_studi'  => $data['durasi_studi'],
            'aktif_sampai'  => $data['aktif_sampai'],
            'status_aktif'  => $data['status_aktif'] ?? 'aktif',
        ]);

        return self::$db->lastInsertId();
    }
    public function getAdminUserList(int $page = 1, string $filter = '', string $search = '')
    {
        $limit  = 10;
        $offset = ($page - 1) * $limit;

        $where  = "WHERE role NOT IN ('admin','super_admin')";
        $params = [];

        // FILTER ROLE (mahasiswa / dosen / tendik)
        if ($filter !== '') {
            $where .= " AND role = :filter";
            $params[':filter'] = $filter;
        }

        // SEARCH nama / email / jurusan
        if ($search !== '') {
            $where .= " AND (nama LIKE :search OR email LIKE :search OR jurusan LIKE :search)";
            $params[':search'] = "%{$search}%";
        }

        // HITUNG TOTAL
        $sqlCount = "SELECT COUNT(*) FROM account {$where}";
        $stmtCount = self::$db->prepare($sqlCount);
        $stmtCount->execute($params);
        $totalRows = (int)$stmtCount->fetchColumn();

        $totalPages = $totalRows > 0 ? (int)ceil($totalRows / $limit) : 1;

        // DATA UTAMA
        $sql = "SELECT 
                id_account,
                nama,
                email,
                jurusan,
                prodi,
                role,
                status_aktif,
                screenshot_kubaca
            FROM account
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
    public function updateBasicProfile($id, array $data)
    {
        $sql = "UPDATE account
            SET nama = :nama,
                email = :email
            WHERE id_account = :id";

        $stmt = self::$db->prepare($sql);
        return $stmt->execute([
            'nama'  => $data['nama'],
            'email' => $data['email'],
            'id'    => $id,
        ]);
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
}
