<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class TemplateNotifikasi extends Model
{
  protected static string $table = 'Template_notifikasi';

  public function findActiveEmailTemplate(string $jenis): ?array
  {
    $sql = "SELECT * FROM " . self::$table . "
                WHERE jenis = :jenis
                  AND kanal = 'email'
                  AND is_active = 1
                LIMIT 1";
    $stmt = self::$db->prepare($sql);
    $stmt->execute(['jenis' => $jenis]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ?: null;
  }
}
