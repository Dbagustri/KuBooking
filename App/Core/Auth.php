<?php

namespace App\Core;

class Auth
{
    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function check(): bool
    {
        return isset($_SESSION['user']) && !empty($_SESSION['user']);
    }
    public static function id(): ?int
    {
        return self::check() ? ($_SESSION['user']['id'] ?? null) : null;
    }

    public static function role(): ?string
    {
        return self::check() ? ($_SESSION['user']['role'] ?? null) : null;
    }

    public static function status(): ?string
    {
        return self::check() ? ($_SESSION['user']['status'] ?? null) : null;
    }

    public static function source(): ?string
    {
        return self::check() ? ($_SESSION['user']['source'] ?? null) : null;
    }

    public static function loginFromAccount(array $user): void
    {
        $_SESSION['user'] = [
            'id'             => $user['id_account'] ?? null,
            'id_account'     => $user['id_account'] ?? null,
            'id_registrasi'  => $user['id_registrasi'] ?? null,
            'nama'           => $user['nama'] ?? null,
            'email'          => $user['email'] ?? null,
            'nim_nip'        => $user['nim_nip'] ?? null,
            'role'           => $user['role'] ?? 'mahasiswa',
            'status'         => $user['status_aktif'] ?? 'aktif',
            'source'         => 'account',
        ];

        session_regenerate_id(true);
    }

    public static function loginFromRegistrasi(array $reg): void
    {
        $_SESSION['user'] = [
            'id'             => $reg['id_registrasi'] ?? null,
            'id_registrasi'  => $reg['id_registrasi'] ?? null,
            'nama'           => $reg['nama'] ?? null,
            'email'          => $reg['email'] ?? null,
            'nim_nip'        => $reg['nim_nip'] ?? null,
            'role'           => $reg['role_registrasi'] ?? 'mahasiswa',
            'status'         => $reg['status'] ?? 'pending',
            'source'         => 'registrasi',
        ];

        session_regenerate_id(true);
    }
    public static function isActive(): bool
    {
        if (!self::check()) return false;
        $user = self::user();
        if (($user['source'] ?? null) === 'registrasi') {
            return false;
        }
        return ($user['status'] ?? 'aktif') === 'aktif';
    }

    public static function requireAuth(): void
    {
        if (!self::check()) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }

    public static function requireRole(array|string $roles): void
    {
        if (!self::check()) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $userRole = self::role();
        $roles    = (array) $roles;

        if (!in_array($userRole, $roles, true)) {
            header('Location: index.php?controller=userBooking&action=home');
            exit;
        }
    }

    public static function logout(): void
    {
        unset($_SESSION['user']);
        session_regenerate_id(true);

        header('Location: index.php?controller=auth&action=login');
        exit;
    }
}
