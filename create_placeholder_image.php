<?php
// Script para crear una imagen predeterminada para placeholders
$width = 800;
$height = 600;
$image = imagecreatetruecolor($width, $height);

// Colores
$background = imagecolorallocate($image, 240, 240, 240);
$text_color = imagecolorallocate($image, 100, 100, 100);
$border = imagecolorallocate($image, 200, 200, 200);

// Rellenar fondo
imagefilledrectangle($image, 0, 0, $width, $height, $background);

// Borde
imagerectangle($image, 0, 0, $width-1, $height-1, $border);

// Texto informativo
$text = 'Imagen no disponible';
$font = 5; // Fuente incorporada

// Centrar texto
$text_width = imagefontwidth($font) * strlen($text);
$text_height = imagefontheight($font);
$text_x = ($width - $text_width) / 2;
$text_y = ($height - $text_height) / 2;

// Añadir texto
imagestring($image, $font, $text_x, $text_y, $text, $text_color);

// Guardar imagen
imagepng($image, 'assets/image-placeholder.png');
imagedestroy($image);

echo 'Imagen placeholder creada con éxito en assets/image-placeholder.png';
?> 