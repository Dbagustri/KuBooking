<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Registrasi extends Model
{
    protected static $table = 'Registrasi';

    public function __construct()
    {
        parent::__construct();
    }

    public function create(array $data)
    {
        $sql = "INSERT INTO " . self::$table . " 
            (nama, jurusan, prodi, email, password, nim_nip, unit_jurusan, screenshot_kubaca, status, role_registrasi, created_at)
            VALUES
            (:nama, :jurusan, :prodi, :email, :password, :nim_nip, :unit_jurusan, :screenshot_kubaca, :status, :role_registrasi, NOW())";

        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            'nama'             => $data['nama'],
            'jurusan'          => $data['jurusan'] ?? null,
            'prodi'            => $data['prodi'] ?? null,
            'email'            => $data['email'],
            'password'         => $data['password'],
            'nim_nip'          => $data['nim_nip'],
            'unit_jurusan'     => $data['unit_jurusan'] ?? null,
            'screenshot_kubaca' => $data['screenshot_kubaca'] ?? null,
            'status'           => $data['status'] ?? 'pending',
            'role_registrasi'  => $data['role_registrasi'] ?? 'mahasiswa',
        ]);

        return self::$db->lastInsertId();
    }

    public function existsByNimOrEmail($nim_nip, $email)
    {
        $sql = "SELECT id_registrasi FROM " . self::$table . " 
                WHERE nim_nip = :nim OR email = :email 
                LIMIT 1";
        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            'nim'   => $nim_nip,
            'email' => $email,
        ]);
        return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByNimNip($nim_nip)
    {
        $sql = "SELECT * FROM " . self::$table . " 
                WHERE nim_nip = :nim 
                LIMIT 1";
        $stmt = self::$db->prepare($sql);
        $stmt->execute(['nim' => $nim_nip]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {
        $sql = "SELECT * FROM " . self::$table . " 
                WHERE id_registrasi = :id 
                LIMIT 1";
        $stmt = self::$db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status)
    {
        $sql = "UPDATE " . self::$table . " 
                SET status = :status 
                WHERE id_registrasi = :id";
        $stmt = self::$db->prepare($sql);
        return $stmt->execute([
            'status' => $status,
            'id'     => $id,
        ]);
    }

    public function getPending($limit = 20)
    {
        $sql = "SELECT 
                    id_registrasi,
                    nama,
                    email,
                    jurusan,
                    prodi,
                    unit_jurusan,
                    screenshot_kubaca,
                    status,
                    role_registrasi,
                    created_at
                FROM " . self::$table . "
                WHERE status = 'pending'
                ORDER BY created_at ASC
                LIMIT :limit";

        $stmt = self::$db->prepare($sql);
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countPendingToday()
    {
        $sql = "SELECT COUNT(*) AS total
                FROM " . self::$table . "
                WHERE status = 'pending'
                  AND DATE(created_at) = CURDATE()";
        $stmt = self::$db->query($sql);
        $row  = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) ($row['total'] ?? 0);
    }
    public function getPendingUsers($page, $filter, $search)
    {
        $limit  = 10;
        $offset = ($page - 1) * $limit;

        // hanya pending + rejected
        $where  = "WHERE status IN ('pending','rejected')";
        $params = [];

        // kalau filter diisi (pending atau rejected), tambahkan
        if (in_array($filter, ['pending', 'rejected'], true)) {
            $where .= " AND status = :filter";
            $params[':filter'] = $filter;
        }

        // kalau search diisi, filter nama / email, tapi tetap hanya di pending+rejected
        if ($search !== '') {
            $where .= " AND (nama LIKE :search OR email LIKE :search)";
            $params[':search'] = "%{$search}%";
        }

        // data
        $sql = "SELECT * FROM registrasi {$where}
            ORDER BY created_at ASC
            LIMIT :offset, :limit";

        $stmt = self::$db->prepare($sql);

        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit',  (int)$limit,  PDO::PARAM_INT);

        $stmt->execute();
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // total rows (pakai where yang sama, tanpa limit)
        $sqlCount = "SELECT COUNT(*) FROM registrasi {$where}";
        $stmtCount = self::$db->prepare($sqlCount);
        foreach ($params as $key => $val) {
            $stmtCount->bindValue($key, $val);
        }
        $stmtCount->execute();
        $count = (int)$stmtCount->fetchColumn();

        return [
            'list'         => $list,
            'current_page' => $page,
            'total_pages'  => max(1, ceil($count / $limit)),
            'filter'       => $filter,
            'search'       => $search,
        ];
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
