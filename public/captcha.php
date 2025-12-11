<?php
session_start();

// ====== GENERATE KODE CAPTCHA ======
$length = 5;
$chars  = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // tanpa O, 0, I, 1 biar gak membingungkan
$code   = '';

for ($i = 0; $i < $length; $i++) {
    $code .= $chars[random_int(0, strlen($chars) - 1)];
}

// Simpan ke session
$_SESSION['captcha_code'] = $code;

// ====== BUAT GAMBAR ======
$width  = 130;
$height = 40;

$im  = imagecreatetruecolor($width, $height);
$bg  = imagecolorallocate($im, 240, 240, 240);
$txt = imagecolorallocate($im, 30, 60, 100);
$line = imagecolorallocate($im, 180, 180, 180);

// Background
imagefilledrectangle($im, 0, 0, $width, $height, $bg);

// Noise garis
for ($i = 0; $i < 6; $i++) {
    imageline(
        $im,
        rand(0, $width),
        rand(0, $height),
        rand(0, $width),
        rand(0, $height),
        $line
    );
}

// Noise titik
for ($i = 0; $i < 80; $i++) {
    imagesetpixel($im, rand(0, $width), rand(0, $height), $line);
}

// Tulis teks (pakai font built-in GD)
$font = 5;
$textWidth  = imagefontwidth($font) * strlen($code);
$textHeight = imagefontheight($font);
$x = ($width - $textWidth) / 2;
$y = ($height - $textHeight) / 2;

imagestring($im, $font, (int)$x, (int)$y, $code, $txt);

// Header response
header('Content-Type: image/png');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

imagepng($im);
imagedestroy($im);
