<?php
session_start();
$code = substr(str_shuffle('A'), 0, 5);
$_SESSION['code'] = $code;

$im = imagecreatetruecolor(120, 40);
$bg = imagecolorallocate($im, 240,240,240);
$txt = imagecolorallocate($im, 30, 60, 100);
$line = imagecolorallocate($im, 180,180,180);
imagefilledrectangle($im, 0,0,120,40,$bg);
for ($i=0;$i<5;$i++){ imageline($im, rand(0,120),rand(0,40),rand(0,120),rand(0,40),$line); }
imagestring($im, 5, 14, 10, $code, $txt);
header('Content-Type: image/png');
imagepng($im);
imagedestroy($im);
