<?php

namespace Pandar;

/**
* A class that implements the singleton design pattern.
*
* ```php
* class A  {
*	 use Singleton;
* }
* A::getInstance();
* ```
*
* @link https://www.slideshare.net/go_oh/singletons-in-php-why-they-are-bad-and-how-you-can-eliminate-them-from-your-applications
*/
trait Singleton {

	/**
   * @var class $instance
   */
	protected static $instance;

	/**
	* Make constructor private, so nobody can call "new Class".
	*
	*/
	private final function __construct() {
		static::init();
	}

	/**
	* Call this method to get singleton
	*
	*/
	final public static function getInstance() {
		return isset(static::$instance) ? static::$instance : static::$instance = new static;
	}

	/**
	*
	*
	*/
	protected function init() { }

	/**
	* Make wakeup magic method private, so nobody can unserialize instance.
	*
	*/
	final private function __wakeup() { }

	/**
   * Make sleep magic method private, so nobody can serialize instance.
   */
  final private function __sleep() {}

	/**
	* Make clone magic method private, so nobody can clone instance.
	*
	*/
	final private function __clone() { }
}

?>
