<?php
$images = glob('images/*.png');

if (!empty($images)) {
    $output = '<div class="image-gallery">';

    foreach ($images as $image) {
        $output .= '<div class="image-item"><img src="' . htmlspecialchars($image) . '" alt="Image"></div>';
    }
    $output .= '</div>';

    echo $output;
} else {
    echo '<p>Нет доступных изображений.</p>';
}
?>

<style>
    .image-gallery {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .image-item {
        display: inline-block;
        margin: 10px;
        text-align: center;
    }
</style>