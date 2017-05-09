<?php

namespace Pandar;

use \InvalidArgumentException;

/**
* A registry whereby you can store different peices of data in a single place.
*
* ```php
* Registry::set('car', 'Ford Escort');
* echo Registry::set('car');
* Registry::remove('car');
* ```
*/
class Registry {

  use Singleton;

  /**
   * @var array $store
   */
  private static $store = array();

  /**
	* Saves the item in the store
	*
  * ```php
  * Registry::set('car', 'Ford Escort');
  * ```
  *
  * @param string $label The key of the stored item
  * @return true|false Returns returns true if item was successfully added else false
  * @throws InvalidArgumentException
	*/
  public static function set($label, $object) {
    if(!is_string($label))
      throw new InvalidArgumentException('Label must be a string.');

    $instance = self::getInstance();
    if(!array_key_exists($label, $instance::$store))
      $instance::$store[$label] = $object;
  }

  /**
	* Removes the item that is stored
	*
  * ```php
  * Registry::remove('car');
  * ```
  *
  * @param string $label The key of the stored item
  * @return true|false Returns returns true if item was successfully removed else false
  * @throws InvalidArgumentException
	*/
  public static function remove($label) {
    if(!is_string($label))
      throw new InvalidArgumentException('Label must be a string.');

    $instance = self::getInstance();
    if(array_key_exists($label, $instance::$store)) {
      unset($instance::$store[$label]);
      return true;
    }
    return false;
  }

  /**
	* Gets the item that is stored
	*
  * ```php
  * Registry::get('car');
  * ```
  *
  * @param string $label The key of the stored item
  * @return mixed Returns the object that is stored
  * @throws InvalidArgumentException
	*/
  public static function get($label) {
    if(!is_string($label))
      throw new InvalidArgumentException('Label must be a string.');

    $instance = self::getInstance();
    return array_key_exists($label, $instance::$store) ? $instance::$store[$label] : false;
  }

  /**
	* Checks if the given item exists
  *
  * ```php
  * Registry::exist('car');
  * ```
  * @param string $label The key of the stored item
  * @return true|false If the items exists or else returns false
  * @throws InvalidArgumentException
	*/
  public static function exist($label) {
    if(!is_string($label))
      throw new InvalidArgumentException('Label must be a string.');

    $instance = self::getInstance();
    return array_key_exists($label, $instance::$store) ? true : false;
  }

  /**
	* Clears all stored items
  *
  * ```php
  * Registry::clearAll();
  * ```
  *
	* @return void
	*/
  public static function clearAll() {
    $instance = self::getInstance();
    $instance::$store = array();
  }

}

?>
