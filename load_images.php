<?php

$images = glob('images/*.png');
foreach ($images as $image) {
    echo '<div style="display:inline-block; margin:10px;"><img src="' . $image . '" alt="Image"><br></div>';
}
