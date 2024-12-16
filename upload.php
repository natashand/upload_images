<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url = $_POST['imageUrl'] ?? '';
    $customText = $_POST['customText'] ?? '';

    $imageUrls = fetchImageFromUrl($url);
    echo $imageUrls;

    if (!empty($imageUrls)) {
        foreach ($imageUrls as $url) {
            processImage($url, $customText);
        }
    }
}

function processImage($url, $customText) {
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        echo 'Ошибка: Неверный URL.';
        return;
    }

    $imageData = getImageSizeUsingCurl($url);
    if ($imageData === false) {
        echo 'Ошибка: Невозможно получить размер изображения.';
        return;
    }

    if ($imageData[0] >= 200 && $imageData[1] >= 200) {
        $image = imagecreatefromstring(file_get_contents($url));
        if ($image) {
            $resizedImage = imagecreatetruecolor(200, 200);
            imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, 200, 200, imagesx($image), imagesy($image));
            addTextToImage($resizedImage, $customText);
            saveImage($resizedImage);
            imagedestroy($image);
            imagedestroy($resizedImage);
        } else {
            echo 'Ошибка: Невозможно создать изображение.';
        }
    }
}

function getImageSizeUsingCurl($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($responseCode === 200) {
        return getimagesize($url);
    } else {
        return false;
    }
}

function fetchImageFromUrl($url) {
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return 'Неверный URL.';
    }

    $htmlContent = file_get_contents($url);
    if ($htmlContent === FALSE) {
        return 'Не удалось получить содержимое страницы.';
    }

    $dom = new DOMDocument();
    @$dom->loadHTML($htmlContent);
    $images = $dom->getElementsByTagName('img');

    if ($images->length === 0) {
        return 'Изображения не найдены.';
    }

    $imageUrls = [];
    foreach ($images as $img) {
        $src = $img->getAttribute('src');

        if (!filter_var($src, FILTER_VALIDATE_URL)) {
            $src = rtrim($url, '/') . '/' . ltrim($src, '/');
        }

        $imageUrls[] = $src;
    }

    return $imageUrls;
}

function addTextToImage($image, $customText) {
    $fontPath = 'path/to/font.ttf';
    $white = imagecolorallocate($image, 255, 255, 255);
    imagettftext($image, 20, 0, 10, 30, $white, $fontPath, $customText);
}

function saveImage($image) {
    $filename = 'images/' . uniqid() . '.png';
    imagepng($image, $filename);
}