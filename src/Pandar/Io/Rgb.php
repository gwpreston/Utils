<?php

namespace Pandar\io;

class Rgb {

  protected $red;
  protected $green;
  protected $blue;

  public function __construct($red, $green, $blue) {
    $this->red = $red;
    $this->green = $green;
    $this->blue = $blue;

    return $this;
  }

  public function getRed() {
    return $this->red;
  }

  public function getGreen() {
    return $this->green;
  }

  public function getBlue() {
    return $this->blue;
  }
  
	public static function fromHex( $colour ) {

    $colour = str_replace('#', '', $colour);

    if(strlen( $colour ) == 6 || strlen( $colour ) == 3) {
      preg_match_all('/#?(?<red>[\w]{1,2})(?<green>[\w]{1,2})(?<blue>[\w]{1,2})/', $colour, $matches);
      if($matches &&
          array_key_exists('red', $matches) &&
          array_key_exists('green', $matches) &&
          array_key_exists('blue', $matches)
        ) {
        return new Rgb( hexdec( $matches['red'][0] ), hexdec( $matches['green'][0] ), hexdec( $matches['blue'][0] ) );
      }
    }

    return false;
	}

}

?>
