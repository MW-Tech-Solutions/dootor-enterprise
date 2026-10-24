<?php

$srcPath = __DIR__ . '/../public/storage/branding/uTTaNjw06EC6ORR85xiS4LtJwVuoLcqSOUeZfS9f.png';
$iconsDir = __DIR__ . '/../public/icons';

if (!is_dir($iconsDir)) {
    mkdir($iconsDir, 0755, true);
}

if (!file_exists($srcPath)) {
    echo "Source missing: $srcPath\n";
    exit(1);
}

$src = imagecreatefrompng($srcPath);
imagealphablending($src, true);
imagesavealpha($src, true);

$sw = imagesx($src);
$sh = imagesy($src);
echo "Source dimensions: {$sw}x{$sh}\n";

$sizes = [
    'icon-72x72.png' => [72, false],
    'icon-96x96.png' => [96, false],
    'icon-128x128.png' => [128, false],
    'icon-144x144.png' => [144, false],
    'icon-152x152.png' => [152, false],
    'apple-touch-icon.png' => [180, true], // iOS requires no transparency or nice background
    'icon-192x192.png' => [192, false],
    'icon-384x384.png' => [384, false],
    'icon-512x512.png' => [512, false],
    'icon-maskable-192x192.png' => [192, true],
    'icon-maskable-512x512.png' => [512, true],
];

foreach ($sizes as $filename => [$size, $withBackground]) {
    $dst = imagecreatetruecolor($size, $size);
    imagealphablending($dst, false);
    imagesavealpha($dst, true);

    if ($withBackground) {
        // Dark green luxury background for maskable and apple-touch
        $bgColor = imagecolorallocate($dst, 0, 66, 37); // #004225
        imagefill($dst, 0, 0, $bgColor);
        imagealphablending($dst, true);

        // Calculate fitted size inside safe zone (padding ~18%)
        $pad = (int) ($size * 0.16);
        $targetW = $size - ($pad * 2);
        $targetH = $size - ($pad * 2);

        $ratio = min($targetW / $sw, $targetH / $sh);
        $finalW = (int) ($sw * $ratio);
        $finalH = (int) ($sh * $ratio);
        $dstX = (int) (($size - $finalW) / 2);
        $dstY = (int) (($size - $finalH) / 2);

        imagecopyresampled($dst, $src, $dstX, $dstY, 0, 0, $finalW, $finalH, $sw, $sh);
    } else {
        // Transparent background
        $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
        imagefill($dst, 0, 0, $transparent);
        imagealphablending($dst, true);

        // Center with minimal padding (~8%)
        $pad = (int) ($size * 0.08);
        $targetW = $size - ($pad * 2);
        $targetH = $size - ($pad * 2);

        $ratio = min($targetW / $sw, $targetH / $sh);
        $finalW = (int) ($sw * $ratio);
        $finalH = (int) ($sh * $ratio);
        $dstX = (int) (($size - $finalW) / 2);
        $dstY = (int) (($size - $finalH) / 2);

        imagecopyresampled($dst, $src, $dstX, $dstY, 0, 0, $finalW, $finalH, $sw, $sh);
    }

    imagealphablending($dst, false);
    imagesavealpha($dst, true);

    $outPath = $iconsDir . '/' . $filename;
    imagepng($dst, $outPath, 9);
    imagedestroy($dst);
    echo "Generated: $filename ($size x $size)\n";
}

echo "All PWA and iOS icons generated successfully!\n";
