<?php

namespace App\Core;

class Helper
{
    //Ambil nomor halaman
    public static function page(array $get, string $key = 'page', int $default = 1): int
    {
        $page = isset($get[$key]) ? (int)$get[$key] : $default;
        return $page < 1 ? 1 : $page;
    }
    public static function isDigits($value): bool
    {
        return $value !== null && $value !== '' && ctype_digit((string)$value);
    }
    public static function paginate(array $get, int $perPage, string $key = 'page'): array
    {
        if ($perPage < 1) $perPage = 5;
        $page = self::page($get, $key, 1);
        return [
            'page'   => $page,
            'limit'  => $perPage,
            'offset' => ($page - 1) * $perPage,
        ];
    }
    public static function requireIdOrRedirect($value, callable $onFail): int
    {
        if (!self::isDigits($value)) {
            $onFail();
            return 0;
        }
        return (int)$value;
    }
    public static function requirePost(callable $onFail): bool
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $onFail();
            return false;
        }
        return true;
    }
    public static function uploadRoomPhoto(string $inputName, ?string $oldPath): array
    {
        if (empty($_FILES[$inputName]['name'])) {
            return ['ok' => true, 'path' => $oldPath];
        }
        $file     = $_FILES[$inputName];
        $tmpName  = $file['tmp_name'];
        $fileName = $file['name'];
        if (!($file['error'] === UPLOAD_ERR_OK && is_uploaded_file($tmpName))) {
            return ['ok' => false, 'error' => 'Terjadi kesalahan saat mengupload file.'];
        }
        $ext     = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($ext, $allowed, true)) {
            return ['ok' => false, 'error' => 'Format gambar tidak didukung. Gunakan JPG, JPEG, PNG, atau WEBP.'];
        }
        $uploadDir = __DIR__ . '/../../public/uploads/rooms/';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
        }
        $newName  = 'room_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
        $fullPath = $uploadDir . $newName;
        if (!move_uploaded_file($tmpName, $fullPath)) {
            return ['ok' => false, 'error' => 'Gagal mengupload foto ruangan.'];
        }
        return ['ok' => true, 'path' => 'uploads/rooms/' . $newName];
    }
}
