<?php

/**
 * Regenerates the favicon set from the "E" mark in public/img/logos/icon.png.
 *
 *   php tools/generate-favicon.php
 *
 * Writes into public/: favicon.ico (16/32/48), favicon-16x16.png, favicon-32x32.png,
 * apple-touch-icon.png, android-chrome-192x192.png, android-chrome-512x512.png,
 * img/icon/faviconV2.png (512, used as the social-share fallback) and site.webmanifest.
 * Needs the GD extension. The mark is white on the brand red.
 */
$root = dirname(__DIR__);
$logo = imagecreatefrompng($root.'/public/img/logos/icon.png');

// 1. Cut the "E" and its waves out of the wordmark (mask off the neighbouring "V" and "COOLING").
$box = ['x' => 4, 'y' => 4, 'w' => 128, 'h' => 116];
$mark = imagecreatetruecolor($box['w'], $box['h']);
imagealphablending($mark, false);
imagesavealpha($mark, true);
imagefill($mark, 0, 0, imagecolorallocatealpha($mark, 255, 255, 255, 127));

$minX = $minY = PHP_INT_MAX;
$maxX = $maxY = 0;
for ($y = 0; $y < $box['h']; $y++) {
    for ($x = 0; $x < $box['w']; $x++) {
        $ox = $box['x'] + $x;
        $oy = $box['y'] + $y;
        $inE = $ox <= 86;                                   // the letter E
        $inWaves = $oy >= 88 && $oy <= 112 && $ox <= 131;   // the two wave lines under it
        if (! ($inE || $inWaves)) {
            continue;
        }
        $rgb = imagecolorat($logo, $ox, $oy);
        // "Redness" => opacity. The logo red has green=30, so rescale so the solid red becomes fully opaque white.
        $alpha = (int) min(255, round((255 - (($rgb >> 8) & 255)) * 255 / 225) * (1 - (($rgb >> 24) & 127) / 127));
        if ($alpha < 8) {
            continue;
        }
        imagesetpixel($mark, $x, $y, imagecolorallocatealpha($mark, 255, 255, 255, 127 - (int) round($alpha / 255 * 127)));
        $minX = min($minX, $x);
        $maxX = max($maxX, $x);
        $minY = min($minY, $y);
        $maxY = max($maxY, $y);
    }
}
$markW = $maxX - $minX + 1;
$markH = $maxY - $minY + 1;

// 2. Compose a master at 1024px: brand red tile with the white mark centred.
function tile(int $size, $mark, array $src, bool $rounded, float $scale): GdImage
{
    $img = imagecreatetruecolor($size, $size);
    imagealphablending($img, false);
    imagesavealpha($img, true);
    $clear = imagecolorallocatealpha($img, 0, 0, 0, 127);
    imagefill($img, 0, 0, $clear);
    $red = imagecolorallocate($img, 232, 30, 37);

    if ($rounded) {
        $r = (int) round($size * 0.22);
        imagefilledrectangle($img, $r, 0, $size - $r - 1, $size - 1, $red);
        imagefilledrectangle($img, 0, $r, $size - 1, $size - $r - 1, $red);
        foreach ([[$r, $r], [$size - $r - 1, $r], [$r, $size - $r - 1], [$size - $r - 1, $size - $r - 1]] as [$cx, $cy]) {
            imagefilledellipse($img, $cx, $cy, $r * 2, $r * 2, $red);
        }
    } else {
        imagefilledrectangle($img, 0, 0, $size - 1, $size - 1, $red);
    }

    imagealphablending($img, true);
    $target = $size * $scale;
    $ratio = min($target / $src['w'], $target / $src['h']);
    $w = (int) round($src['w'] * $ratio);
    $h = (int) round($src['h'] * $ratio);
    imagecopyresampled($img, $mark, (int) (($size - $w) / 2), (int) (($size - $h) / 2), $src['x'], $src['y'], $w, $h, $src['w'], $src['h']);

    return $img;
}

$src = ['x' => $minX, 'y' => $minY, 'w' => $markW, 'h' => $markH];
$master = tile(1024, $mark, $src, true, 0.66);
$masterSquare = tile(1024, $mark, $src, false, 0.58); // full-bleed for touch icons (the OS rounds it)

function scaled(GdImage $master, int $size): GdImage
{
    $out = imagecreatetruecolor($size, $size);
    imagealphablending($out, false);
    imagesavealpha($out, true);
    imagefill($out, 0, 0, imagecolorallocatealpha($out, 0, 0, 0, 127));
    imagecopyresampled($out, $master, 0, 0, 0, 0, $size, $size, imagesx($master), imagesy($master));

    return $out;
}

function png(GdImage $img): string
{
    ob_start();
    imagepng($img, null, 9);

    return ob_get_clean();
}

$public = $root.'/public';
$files = [
    'favicon-16x16.png' => png(scaled($master, 16)),
    'favicon-32x32.png' => png(scaled($master, 32)),
    'apple-touch-icon.png' => png(scaled($masterSquare, 180)),
    'android-chrome-192x192.png' => png(scaled($master, 192)),
    'android-chrome-512x512.png' => png(scaled($master, 512)),
    'img/icon/faviconV2.png' => png(scaled($master, 512)),
];
foreach ($files as $name => $data) {
    file_put_contents($public.'/'.$name, $data);
}

// favicon.ico with 16, 32 and 48px PNG images inside.
$sizes = [16, 32, 48];
$images = array_map(fn ($s) => png(scaled($master, $s)), $sizes);
$ico = pack('vvv', 0, 1, count($sizes));
$offset = 6 + 16 * count($sizes);
foreach ($sizes as $i => $s) {
    $ico .= pack('CCCCvvVV', $s, $s, 0, 0, 1, 32, strlen($images[$i]), $offset);
    $offset += strlen($images[$i]);
}
file_put_contents($public.'/favicon.ico', $ico.implode('', $images));

file_put_contents($public.'/site.webmanifest', json_encode([
    'name' => 'Evanx Cooling Systems',
    'short_name' => 'Evanx Cooling',
    'icons' => [
        ['src' => '/android-chrome-192x192.png', 'sizes' => '192x192', 'type' => 'image/png'],
        ['src' => '/android-chrome-512x512.png', 'sizes' => '512x512', 'type' => 'image/png'],
    ],
    'theme_color' => '#e81e25',
    'background_color' => '#ffffff',
    'display' => 'browser',
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n");

// Preview sheet for a quick visual check (not committed).
$sheet = imagecreatetruecolor(900, 420);
imagefill($sheet, 0, 0, imagecolorallocate($sheet, 245, 245, 245));
imagealphablending($sheet, true);
$x = 20;
foreach ([256, 128, 64, 32, 16] as $s) {
    imagecopy($sheet, scaled($master, $s), $x, 20, 0, 0, $s, $s);
    $x += $s + 20;
}
imagecopyresampled($sheet, scaled($masterSquare, 180), 20, 250, 0, 0, 150, 150, 180, 180);
// What a browser tab strip shows: 16px on white and on dark.
foreach ([[200, 255], [320, 40]] as [$px, $bg]) {
    imagefilledrectangle($sheet, $px + 20, 280, $px + 120, 320, imagecolorallocate($sheet, $bg, $bg, $bg));
    imagecopy($sheet, scaled($master, 16), $px + 30, 292, 0, 0, 16, 16);
}
imagepng($sheet, sys_get_temp_dir().'/favicon-preview.png');

echo "Favicon set written to public/ (mark {$markW}x{$markH}px). Preview: ".sys_get_temp_dir()."/favicon-preview.png\n";
