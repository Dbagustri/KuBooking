<?php

namespace App\Core;

use App\Models\TemplateNotifikasi;

class NotificationService
{
    private Mailer $mailer;
    private TemplateNotifikasi $tplModel;

    public function __construct()
    {
        $this->mailer   = new Mailer();
        $this->tplModel = new TemplateNotifikasi();
    }

    public function sendByJenis(string $jenis, string $toEmail, string $toName, array $data = []): bool
    {
        $tpl = $this->tplModel->findActiveEmailTemplate($jenis);
        if (!$tpl) {
            error_log("[NOTIF] Template not found: {$jenis}");
            return false;
        }
        $subject = $this->render($tpl['judul'] ?? '', $data);
        $body    = $this->render($tpl['pesan'] ?? '', $data);
        $htmlBody = nl2br($body);

        return $this->mailer->send($toEmail, $toName, $subject, $htmlBody);
    }
    //Mesin pengganti placeholder:
    private function render(string $text, array $data): string
    {
        return preg_replace_callback('/{{\s*([a-zA-Z0-9_]+)\s*}}/', function ($m) use ($data) {
            $key = $m[1];
            return isset($data[$key]) ? (string)$data[$key] : '';
        }, $text);
    }
}
