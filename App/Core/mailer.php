<?php

namespace App\Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    private array $config;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../../config/mail.php';
    }

    public function send(string $toEmail, string $toName, string $subject, string $htmlBody, string $textBody = ''): bool
    {
        // Pastikan autoload composer terpanggil (biasanya sudah dari public/index.php)
        // require_once __DIR__ . '/../../vendor/autoload.php';

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->SMTPDebug = 2;
            $mail->Debugoutput = 'error_log';
            $mail->Host       = $this->config['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->config['username'];
            $mail->Password   = $this->config['password'];
            $mail->SMTPSecure = $this->config['secure'];
            $mail->Port       = (int)$this->config['port'];

            $mail->CharSet = 'UTF-8';
            $mail->setFrom($this->config['from_email'], $this->config['from_name']);
            $mail->addAddress($toEmail, $toName);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;

            // fallback plain text (opsional tapi bagus)
            $mail->AltBody = $textBody ?: strip_tags($htmlBody);

            return $mail->send();
        } catch (Exception $e) {
            error_log("[MAILER ERROR] " . $e->getMessage());
            return false;
        }
    }
}
