<?php

/**
 * Generates the default social share image (1200x630) used when a page has no image of its own.
 *
 *   php tools/generate-og-image.php
 *
 * Writes public/img/og-default.jpg. Needs GD with FreeType and a bold sans font (Arial on Windows,
 * DejaVu/Liberation on Linux); the output is committed so servers don't need either.
 */
$root = dirname(__DIR__);

$fontCandidates = [
    'C:/Windows/Fonts/arialbd.ttf', 'C:/Windows/Fonts/segoeuib.ttf',
    '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf', '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf',
];
$bold = current(array_filter($fontCandidates, 'is_file')) ?: null;
$regular = str_replace('bd.ttf', '.ttf', $bold ?? '');
if (! $bold) {
    fwrite(STDERR, "No TrueType font found; edit \$fontCandidates.\n");
    exit(1);
}
if (! is_file($regular)) {
    $regular = $bold;
}

$w = 1200;
$h = 630;
$img = imagecreatetruecolor($w, $h);
imageantialias($img, true);

$red = imagecolorallocate($img, 232, 30, 37);
$navy = imagecolorallocate($img, 0, 16, 100);
$white = imagecolorallocate($img, 255, 255, 255);
$light = imagecolorallocate($img, 246, 247, 248);
$grey = imagecolorallocate($img, 90, 98, 112);

// Background: light panel with a red band on the left and a navy footer bar.
imagefilledrectangle($img, 0, 0, $w, $h, $light);
imagefilledrectangle($img, 0, 0, 24, $h, $red);
imagefilledrectangle($img, 0, $h - 86, $w, $h, $navy);

// Logo (white card behind it so the red-on-white wordmark keeps its look).
$logo = imagecreatefrompng($root.'/public/img/logos/icon.png');
$logoW = 560;
$logoH = (int) round(imagesy($logo) * $logoW / imagesx($logo));
imagefilledrectangle($img, 90, 55, 90 + $logoW + 40, 55 + $logoH + 30, $white);
imagecopyresampled($img, $logo, 110, 70, 0, 0, $logoW, $logoH, imagesx($logo), imagesy($logo));

// Headline + supporting line.
imagettftext($img, 44, 0, 90, 360, $navy, $bold, 'HVAC & Refrigeration Experts');
imagettftext($img, 44, 0, 90, 424, $red, $bold, 'in Nairobi, Kenya');
imagettftext($img, 25, 0, 90, 482, $grey, $regular, 'AC installation  •  Repairs & maintenance  •  Cold rooms');
imagettftext($img, 25, 0, 90, 521, $grey, $regular, 'Refrigeration parts & HVAC tools  •  Order on WhatsApp');

// Footer.
imagettftext($img, 30, 0, 90, $h - 30, $white, $bold, 'evanxcoolingsystems.co.ke');

// Snowflake-style mark (top right), drawn with filled polygons so the strokes are thick and smooth.
function thickLine($img, float $x1, float $y1, float $x2, float $y2, float $width, int $color): void
{
    $dx = $y2 - $y1;
    $dy = $x1 - $x2;
    $len = max(sqrt($dx * $dx + $dy * $dy), 0.0001);
    $dx = $dx / $len * $width / 2;
    $dy = $dy / $len * $width / 2;
    imagefilledpolygon($img, [
        (int) ($x1 + $dx), (int) ($y1 + $dy), (int) ($x2 + $dx), (int) ($y2 + $dy),
        (int) ($x2 - $dx), (int) ($y2 - $dy), (int) ($x1 - $dx), (int) ($y1 - $dy),
    ], $color);
}
$cx = 1010;
$cy = 235;
$arm = 120;
for ($a = 0; $a < 6; $a++) {
    $rad = deg2rad($a * 60 - 90);
    thickLine($img, $cx, $cy, $cx + cos($rad) * $arm, $cy + sin($rad) * $arm, 12, $red);
    foreach ([-1, 1] as $side) {
        $bx = $cx + cos($rad) * $arm * 0.6;
        $by = $cy + sin($rad) * $arm * 0.6;
        $brad = $rad + $side * deg2rad(50);
        thickLine($img, $bx, $by, $bx + cos($brad) * 44, $by + sin($brad) * 44, 9, $red);
    }
}
imagefilledellipse($img, $cx, $cy, 38, 38, $navy);
imagejpeg($img, $root.'/public/img/og-default.jpg', 88);
echo "Wrote public/img/og-default.jpg (".round(filesize($root.'/public/img/og-default.jpg') / 1024)." KB)\n";
