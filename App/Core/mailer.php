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
            $mail->AltBody = $textBody ?: strip_tags($htmlBody);

            return $mail->send();
        } catch (Exception $e) {
            error_log("[MAILER ERROR] " . $e->getMessage());
            return false;
        }
    }
}
