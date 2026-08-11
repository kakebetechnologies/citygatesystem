<?php
/**
 * One-off script: crops the transparent padding off images/logo/citygatelogo.png,
 * centers the badge on a square canvas, and writes a proper favicon set.
 * Run once via CLI: php scripts/generate_favicons.php
 */
$src = __DIR__ . '/../images/logo/citygatelogo.png';
$im = imagecreatefrompng($src);
if (!$im) { fwrite(STDERR, "Could not open $src\n"); exit(1); }
imagealphablending($im, false);
imagesavealpha($im, true);

$w = imagesx($im);
$h = imagesy($im);

// Find bounding box of non-transparent pixels.
$minX = $w; $minY = $h; $maxX = 0; $maxY = 0;
for ($y = 0; $y < $h; $y += 2) {
   for ($x = 0; $x < $w; $x += 2) {
      $rgba = imagecolorat($im, $x, $y);
      $alpha = ($rgba >> 24) & 0x7F;
      if ($alpha < 100) { // has visible content
         if ($x < $minX) $minX = $x;
         if ($x > $maxX) $maxX = $x;
         if ($y < $minY) $minY = $y;
         if ($y > $maxY) $maxY = $y;
      }
   }
}
$cropW = $maxX - $minX;
$cropH = $maxY - $minY;
$pad = (int) (max($cropW, $cropH) * 0.06);
$side = max($cropW, $cropH) + $pad * 2;

// Square transparent canvas with the badge centered.
$square = imagecreatetruecolor($side, $side);
imagealphablending($square, false);
imagesavealpha($square, true);
$transparent = imagecolorallocatealpha($square, 0, 0, 0, 127);
imagefilledrectangle($square, 0, 0, $side, $side, $transparent);
imagealphablending($square, true);
imagecopy($square, $im, (int) (($side - $cropW) / 2), (int) (($side - $cropH) / 2), $minX, $minY, $cropW, $cropH);

$outDir = __DIR__ . '/../images/logo/';
$sizes = [16, 32, 48, 192, 512];
foreach ($sizes as $s) {
   $canvas = imagecreatetruecolor($s, $s);
   imagealphablending($canvas, false);
   imagesavealpha($canvas, true);
   $t = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
   imagefilledrectangle($canvas, 0, 0, $s, $s, $t);
   imagecopyresampled($canvas, $square, 0, 0, 0, 0, $s, $s, $side, $side);
   imagepng($canvas, $outDir . "favicon-{$s}.png");
   imagedestroy($canvas);
   echo "Wrote favicon-{$s}.png\n";
}

// Apple touch icon needs an opaque background (iOS ignores alpha).
$appleSize = 180;
$apple = imagecreatetruecolor($appleSize, $appleSize);
$cream = imagecolorallocate($apple, 0xFA, 0xF7, 0xF0);
imagefilledrectangle($apple, 0, 0, $appleSize, $appleSize, $cream);
imagecopyresampled($apple, $square, 0, 0, 0, 0, $appleSize, $appleSize, $side, $side);
imagepng($apple, $outDir . 'apple-touch-icon.png');
imagedestroy($apple);
echo "Wrote apple-touch-icon.png\n";

imagedestroy($im);
imagedestroy($square);
echo "Done.\n";
