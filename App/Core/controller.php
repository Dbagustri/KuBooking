<?php
namespace App\Core;

class Controller
{
    protected function view($path, $data = [])
    {
        $file = __DIR__ . '/../views/' . $path . '.php';
        if (!file_exists($file)) {
            die("View not found: {$file}");
        }
        extract($data);
        require $file;
    }

    protected function redirect($url)
    {
        header("Location: {$url}");
        exit;
    }

    protected function input($key, $default = null)
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }
public function redirectWithMessage($url, $message, $type = 'success')
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['flash_message'] = [
            'type' => $type,   // 'success' atau 'error'
            'text' => $message
        ];

        header("Location: $url");
        exit;
    }    
}
