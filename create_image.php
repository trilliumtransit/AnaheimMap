<?php
header('Content-type: image/png');

if (isset($_GET['fg'])) {$fg = $_GET['fg'];}
if (isset($_GET['bg'])) {$bg = $_GET['bg'];}
if (isset($_GET['bc'])) {$bc = $_GET['bc'];}
if (isset($_GET['r'])) {$r = $_GET['r'];}
if (isset($_GET['bw'])) {$bw = $_GET['bw'];}

is_numeric($r) or $r = 5;
is_numeric($bw) or $bw = 2;
strlen($fg)==6 or $fg = 'ffffff';
strlen($bg)==6 or $bg = 'fcfcfc';
strlen($bc)==6 or $bc = '575757';

function hex2rgb($im,$hex) {
    return imagecolorallocate($im,
        hexdec(substr($hex,0,2)),
        hexdec(substr($hex,2,2)),
        hexdec(substr($hex,4,2))
        );
}

$a = $r*2;
$b = $a*4;
$c = $b/2;
$d = $b-2;
$e = $d-($bw*8)-2;

$im1 = imagecreatetruecolor($b,$b);
$im2 = imagecreatetruecolor($a,$a);
$bg_color = hex2rgb($im1,$bg); 
imagefill($im1,0,0,$bg_color);
if($bw) imagefilledellipse($im1,$c,$c,$d,$d,hex2rgb($im1,$bc));
imagefilledellipse($im1,$c,$c,$e,$e,hex2rgb($im1,$fg));
imagecopyresampled($im2,$im1,0,0,0,0,$a,$a,$b,$b);
imagecolortransparent($im2, $bg_color);
imagepng($im2);
?>