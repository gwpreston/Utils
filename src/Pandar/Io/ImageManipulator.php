<?php

namespace Pandar\io;

use \RuntimeException;

/**
* A simple class to that makes working with images easier.
*
* ```php
* $image = new ImageManipulator(realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'titanic-belfast.jpg');
* $image
*   ->resize(100, 100)
*   ->show(IMAGETYPE_PNG);
* ```
*
* @author Phil Brown
* @author Gareth Preston
* @link https://gist.github.com/philBrown/880506
*/
class ImageManipulator {

  /**
   * @var int
   */
  protected $width;

  /**
   * @var int
   */
  protected $height;

  /**
   * @var resource
   */
  protected $image;

  /**
   * Image manipulator constructor
   *
   * @param string $file OPTIONAL Path to image file or image data as string
   * @return ImageManipulator for a fluent interface
   */
  public function __construct($file = null) {

    // Check for the required GD extension
    if(!extension_loaded('gd'))
      throw new RuntimeException('Required extension GD is not loaded.');

    // Ignore JPEG warnings that cause imagecreatefromjpeg() to fail
		ini_set('gd.jpeg_ignore_warning', 1);

    if (null !== $file) {

      if (is_file($file)) {
        $this->setImageFile($file);
      }

      else {
        $this->setImageString($file);
      }

    }

    return $this;
  }

  /**
   * Set image resource from file
   *
   * @param string $file Path to image file
   * @return ImageManipulator for a fluent interface
   * @throws InvalidArgumentException
   */
  public function setImageFile($file) {

    if (!(is_readable($file) && is_file($file))) {
      throw new InvalidArgumentException('Image file' . $file . ' is not readable');
    }

    if (is_resource($this->image)) {
      imagedestroy($this->image);
    }

    list ($this->width, $this->height, $type) = getimagesize($file);

    switch ($type) {
      case IMAGETYPE_GIF :
        $this->image = imagecreatefromgif($file);
        break;
      case IMAGETYPE_JPEG :
        $this->image = imagecreatefromjpeg($file);
        break;
      case IMAGETYPE_PNG :
        $this->image = imagecreatefrompng($file);
        break;
      default :
        throw new InvalidArgumentException('Image type' . $type . 'not supported');
    }

    return $this;
  }

  /**
   * Set image resource from string data
   *
   * @param string $data
   * @return ImageManipulator for a fluent interface
   * @throws RuntimeException
   */
  public function setImageString($data) {

    if (is_resource($this->image)) {
        imagedestroy($this->image);
    }

    if (!$this->image = imagecreatefromstring($data)) {
        throw new RuntimeException('Cannot create image from data string');
    }

    $this->width = imagesx($this->image);
    $this->height = imagesy($this->image);

    return $this;
  }

  /**
   * Resize the current image
   *
   * @param int  $width                New width
   * @param int  $height               New height
   * @param bool $constrainProportions Constrain current image proportions when resizing
   * @return ImageManipulator for a fluent interface
   * @throws RuntimeException
   */
  public function resize($width, $height, $constrainProportions = true) {

    if (!is_resource($this->image)) {
      throw new RuntimeException('No image set');
    }

    if ($constrainProportions) {
      if ($this->height >= $this->width) {
        $height = round($width / $this->width * $this->height);
      } else {
        $width  = round($height / $this->height * $this->width);
      }
    }

    $temp = imagecreatetruecolor($width, $height);

    // PNG/GIF Transparency
    imagealphablending($temp, false);
    imagesavealpha($temp,true);
    $transparent = imagecolorallocatealpha($temp, 255, 255, 255, 127);
    imagefilledrectangle($temp, 0, 0, $width, $height, $transparent);

    imagecopyresampled(
      $temp,
      $this->image,
      0, 0,
      0, 0,
      $width, $height, // Destination width/height
      $this->width, $this->height // Source width/height
    );

    return $this->_replace($temp);
  }

  /**
   * Enlarge canvas
   *
   * @param int   $width  Canvas width
   * @param int   $height Canvas height
   * @param array $rgb    RGB colour values
   * @param int   $xpos   X-Position of image in new canvas, null for centre
   * @param int   $ypos   Y-Position of image in new canvas, null for centre
   * @return ImageManipulator for a fluent interface
   * @throws RuntimeException
   */
  public function enlargeCanvas($width, $height, array $rgb = array(), $xpos = null, $ypos = null) {

    if (!is_resource($this->image)) {
      throw new RuntimeException('No image set');
    }

    $width = max($width, $this->width);
    $height = max($height, $this->height);

    $temp = imagecreatetruecolor($width, $height);
    if (count($rgb) == 3) {
      $bg = imagecolorallocate($temp, $rgb[0], $rgb[1], $rgb[2]);
      imagefill($temp, 0, 0, $bg);
    }

    if (null === $xpos) {
      $xpos = round(($width - $this->width) / 2);
    }

    if (null === $ypos) {
      $ypos = round(($height - $this->height) / 2);
    }

    imagecopy($temp, $this->image, (int) $xpos, (int) $ypos, 0, 0, $this->width, $this->height);

    return $this->_replace($temp);
  }

  /**
   * Crop image
   *
   * @param int|array $x1 Top left x-coordinate of crop box or array of coordinates
   * @param int       $y1 Top left y-coordinate of crop box
   * @param int       $x2 Bottom right x-coordinate of crop box
   * @param int       $y2 Bottom right y-coordinate of crop box
   * @return ImageManipulator for a fluent interface
   * @throws RuntimeException
   */
  public function crop($x1, $y1 = 0, $x2 = 0, $y2 = 0) {

    if (!is_resource($this->image)) {
      throw new RuntimeException('No image set');
    }

    if (is_array($x1) && 4 == count($x1)) {
      list($x1, $y1, $x2, $y2) = $x1;
    }

    $x1 = max($x1, 0);
    $y1 = max($y1, 0);

    $x2 = min($x2, $this->width);
    $y2 = min($y2, $this->height);

    $width = $x2 - $x1;
    $height = $y2 - $y1;

    $temp = imagecreatetruecolor($width, $height);
    imagecopy($temp, $this->image, 0, 0, $x1, $y1, $width, $height);

    return $this->_replace($temp);
  }

  /**
   * Rotates current image
   *
   * @param float $degrees Degrees to rotate the image
   * @param int $bgColour Backgrount colour
   * @return ImageManipulator for a fluent interface
   * @throws InvalidArgumentException
   */
  public function rotate($degrees, $bgColour = 0) {

    if($degrees > 360 || $degrees < -360)
      throw new InvalidArgumentException('Degrees is invalid');

    $this->image = imagerotate($this->image, $degrees, $bgColour);

    return $this;
  }

  /**
   * Flips the current image
   *
   * @param mixed $mode Direction to flip the image
   * @return ImageManipulator for a fluent interface
   * @throws InvalidArgumentException
   */
  public function flip($mode = null) {

    if(null !== $mode) {

      if($mode === IMG_FLIP_VERTICAL)
        imageflip($this->image, IMG_FLIP_VERTICAL);

      else if($mode === IMG_FLIP_VERTICAL)
        imageflip($this->image, IMG_FLIP_VERTICAL);

      else if($mode === IMG_FLIP_BOTH)
        imageflip($this->image, IMG_FLIP_BOTH);

    }

    return $this;
  }

  /**
   * Replace current image resource with a new one
   *
   * @param resource $res New image resource
   * @return ImageManipulator for a fluent interface
   * @throws UnexpectedValueException
   */
  protected function _replace($res) {

    if (!is_resource($res)) {
      throw new UnexpectedValueException('Invalid resource');
    }

    if (is_resource($this->image)) {
      imagedestroy($this->image);
    }

    $this->image = $res;

    $this->width = imagesx($res);
    $this->height = imagesy($res);

    return $this;
  }

  /**
   * Save current image to file
   *
   * @param string $fileName
   * @return void
   * @throws RuntimeException
   */
  public function save($fileName, $type = IMAGETYPE_JPEG, $quality = 100, $destroy = true) {

    $dir = dirname($fileName);
    if (!is_dir($dir)) {
      if (!mkdir($dir, 0755, true)) {
        throw new RuntimeException('Error creating directory ' . $dir);
      }
    }

    try {
      switch ($type) {
        case IMAGETYPE_GIF :

          // GIF Transparency
          imagecolortransparent($this->image, imagecolorallocatealpha($this->image, 0, 0, 0, 127));
          imagealphablending($this->image, false);
          imagesavealpha($this->image, true);

          if (!imagegif($this->image, $fileName)) {
            throw new RuntimeException;
          }

          break;
        case IMAGETYPE_PNG :

          // PNG Transparency
          imagecolortransparent($this->image, imagecolorallocatealpha($this->image, 0, 0, 0, 127));
          imagealphablending($this->image, false);
          imagesavealpha($this->image, true);

          if (!imagepng($this->image, $fileName)) {
            throw new RuntimeException;
          }

          break;
        case IMAGETYPE_JPEG :
        default :
          if (!imagejpeg($this->image, $fileName, $quality)) {
            throw new RuntimeException;
          }
      }

      if($destroy)
        $this->destroy();

    }

    catch (Exception $e) {
        throw new RuntimeException('Error saving image file to ' . $fileName);
    }

  }

  public function show($type = IMAGETYPE_JPEG, $quality = 100, $destroy = true) {

    try {
      switch ($type) {
        case IMAGETYPE_GIF :

          // GIF Transparency
          imagecolortransparent($this->image, imagecolorallocatealpha($this->image, 0, 0, 0, 127));
          imagealphablending($this->image, false);
          imagesavealpha($this->image, true);

          header('Content-type: image/gif');
          if (!imagegif($this->image, null)) {
            throw new RuntimeException;
          }

          break;
        case IMAGETYPE_PNG :

          // PNG Transparency
          imagecolortransparent($this->image, imagecolorallocatealpha($this->image, 0, 0, 0, 127));
          imagealphablending($this->image, false);
          imagesavealpha($this->image, true);

          header('Content-type: image/png');
          if (!imagepng($this->image, null)) {
            throw new RuntimeException;
          }

          break;
        case IMAGETYPE_JPEG :
        default :
          header('Content-type: image/jpeg');

          if (!imagejpeg($this->image, null, $quality)) {
            throw new RuntimeException;
          }
      }

      if($destroy)
        $this->destroy();

    }

    catch (Exception $ex) {
      throw new RuntimeException('Error saving image file to ' . $fileName);
    }

  }

  public function destroy() {

		if(is_resource($this->image))
			@imagedestroy($this->image);

  }

  /**
   * Returns the GD image resource
   *
   * @return resource
   */
  public function getResource() {
      return $this->image;
  }

  /**
   * Get current image resource width
   *
   * @return int
   */
  public function getWidth() {
      return $this->width;
  }

  /**
   * Get current image height
   *
   * @return int
   */
  public function getHeight() {
      return $this->height;
  }

	public function __destruct() {
		$this->destroy();
  }
}

?>
