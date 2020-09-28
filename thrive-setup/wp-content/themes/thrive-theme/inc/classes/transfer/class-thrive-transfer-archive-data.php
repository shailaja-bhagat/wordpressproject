<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

/**
 * Class Thrive_Transfer_Archive_Data
 */
class Thrive_Transfer_Archive_Data implements ArrayAccess {

	/**
	 * @var Thrive_Transfer_Archive_Data
	 */
	protected static $_instance = null;

	/**
	 * All the data from the website that will end up in the archive
	 *
	 * @var array
	 */
	private $container = [];

	/**
	 * Thrive_Transfer_Archive_Data singleton constructor.
	 */
	private function __construct() {
		$this->container = [];
	}

	/**
	 * Returns a singleton instance
	 *
	 * @return Thrive_Transfer_Archive_Data
	 */
	public static function get_instance() {
		if ( null === self::$_instance ) {
			self::$_instance = new static();
		}

		return self::$_instance;
	}

	/**
	 * Return the array container with all its data
	 *
	 * @return array
	 */
	public function get_data() {
		return $this->container;
	}

	/**
	 * Reset instance for the new export
	 */
	public static function reset() {
		self::$_instance = null;
	}

	/**
	 * Below are functions that we need because the class implements ArrayAccess -> we can access class properties as indexes
	 */

	/**
	 * Assign a value to the specified offset
	 *
	 * @param mixed $offset
	 * @param mixed $value
	 */
	public function offsetSet( $offset, $value ) {
		if ( is_null( $offset ) ) {
			$this->container[] = $value;
		} else {
			$this->container[ $offset ] = $value;
		}
	}

	/**
	 * Whether an offset exists
	 *
	 * @param mixed $offset
	 *
	 * @return bool
	 */
	public function offsetExists( $offset ) {
		return isset( $this->container[ $offset ] );
	}

	/**
	 * Unset an offset
	 *
	 * @param mixed $offset
	 */
	public function offsetUnset( $offset ) {
		unset( $this->container[ $offset ] );
	}

	/**
	 * Offset to retrieve
	 *
	 * @param mixed $offset
	 *
	 * @return mixed|null
	 */
	public function offsetGet( $offset ) {
		return isset( $this->container[ $offset ] ) ? $this->container[ $offset ] : null;
	}

}
