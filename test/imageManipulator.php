<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once ('autoloader.php');

use Pandar\Io\ImageManipulator;
use Pandar\Io\Rgb;

// Prints out the RGB value
//echo '<h1>' . Rgb::fromHex('#0000FF') . '</h1>';

// Prints out the image to the browser
//$image1 = new ImageManipulator(realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'titanic-belfast.jpg');
//$image1->show(IMAGETYPE_PNG);

// Saves image
//$image2 = new ImageManipulator(realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'titanic-belfast.jpg');
//$image2->save('test.jpg', IMAGETYPE_JPEG, 60);

?>
