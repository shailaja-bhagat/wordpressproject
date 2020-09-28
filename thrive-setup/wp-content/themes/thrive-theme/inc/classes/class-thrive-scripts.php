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
 * Class Thrive_Scripts
 */
class Thrive_Scripts {

	/**
	 * Use general singleton methods
	 */
	use Thrive_Singleton;

	const HEAD_SCRIPT   = 'head';
	const BODY_SCRIPT   = 'body';
	const FOOTER_SCRIPT = 'footer';

	/**
	 * All types of scripts that can be saved for a post
	 */
	const ALL = [ self::HEAD_SCRIPT, self::BODY_SCRIPT, self::FOOTER_SCRIPT ];

	/**
	 * Option name where we are saving the scripts ( the same as the one from TAR )
	 */
	const OPTION_NAME = 'tve_global_scripts';

	/**
	 * @var Thrive_Post
	 */
	private $post;

	/**
	 * Thrive_Scripts constructor.
	 *
	 * @param int $id
	 */
	public function __construct( $id = 0 ) {
		$this->post = new Thrive_Post( $id );
	}

	/**
	 * Add actions in order to insert the scripts properly
	 */
	public function hooks() {
		add_action( 'wp_head', function () {
			echo $this->get_all( self::HEAD_SCRIPT );
		} );

		add_action( 'theme_after_body_open', function () {
			echo $this->get_all( self::BODY_SCRIPT );
		} );

		add_action( 'theme_before_body_close', function () {
			echo $this->get_all( self::FOOTER_SCRIPT );
		} );
	}

	/**
	 * Get the posts global scripts
	 *
	 * @param string $type
	 *
	 * @return array|mixed|string
	 */
	public function get_all( $type = '' ) {
		$scripts = $this->post->get_meta( static::OPTION_NAME );
		$all     = [];

		foreach ( static::ALL as $value ) {
			$all[ $value ] = isset( $scripts[ $value ] ) ? $scripts[ $value ] : '';
		}

		if ( empty( $type ) ) {
			$scripts = $all;
		} else {
			$scripts = isset( $all[ $type ] ) ? $all[ $type ] : '';
		}


		return $scripts;
	}

	/**
	 * Save scripts
	 *
	 * @param $data
	 */
	public function save( $data ) {
		$scripts = [];

		foreach ( static::ALL as $value ) {
			$key               = "thrive_{$value}_scripts";
			$scripts[ $value ] = isset( $data[ $key ] ) ? $data[ $key ] : '';
		}

		if ( ! empty( $scripts ) ) {
			$this->post->set_meta( static::OPTION_NAME, $scripts );
		}
	}

	/**
	 * Render the post scripts meta box from post / page edit view
	 */
	public static function admin_metabox() {
		include THEME_PATH . '/inc/templates/admin/scripts-metabox.php';
	}
}

if ( ! function_exists( 'thrive_scripts' ) ) {
	/**
	 * Return Thrive_Post instance
	 *
	 * @param int id - post id
	 *
	 * @return Thrive_Scripts
	 */
	function thrive_scripts( $id = 0 ) {
		return Thrive_Scripts::instance_with_id( $id );
	}
}
