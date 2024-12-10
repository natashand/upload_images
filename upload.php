<?php

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $context = stream_context_create([
        'http' => [
            'header' => "User-Agent: MyAgent/1.0\r\n"
        ]
    ]);
    $imageUrl = $_POST['imageUrl'];

    if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
        $imageData = getimagesize($imageUrl, $context);
        if ($imageData === false) {
            echo 'Ошибка: Невозможно получить размер изображения.';
            exit;
        }
    } else {
        echo 'Ошибка: Неверный URL.';
        exit;
    }

    $customText = $_POST['customText'];

    if ($imageData && $imageData[0] >= 200 && $imageData[1] >= 200) {
        $image = imagecreatefromstring(file_get_contents($imageUrl));
        $resizedImage = imagecreatetruecolor(200, 200);
        imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, 200, 200, imagesx($image), imagesy($image));

        $fontPath = 'path/to/font.ttf';
        $white = imagecolorallocate($resizedImage, 255, 255, 255);
        imagettftext($resizedImage, 20, 0, 10, 30, $white, $fontPath, $customText);

        $filename = 'images/' . uniqid() . '.png';
        imagepng($resizedImage, $filename);
        imagedestroy($image);
        imagedestroy($resizedImage);
    }
}

