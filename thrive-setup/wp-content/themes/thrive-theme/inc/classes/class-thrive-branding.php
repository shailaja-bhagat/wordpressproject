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
 * Class Thrive_Branding
 */
class Thrive_Branding {
	/**
	 * Thrive_Branding constructor.
	 */
	public function __construct() {
		$this->hooks();
	}

	private function hooks() {
		/* add the branding routes to the rest api */
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );

		/* Filter over the WP getter for the favicon. Whenever this is called, return the favicon for the current active skin instead. */
		add_filter( 'get_site_icon_url', function ( $general_favicon_url, $size = '', $blog_id = 0 ) {
			return $this->get_favicon( $general_favicon_url );
		}, 10, 3 );

		/* Filter for setting the skin's favicon when the favicon is changed from the Customizer. */
		add_filter( 'customize_changeset_save_data', [ $this, 'save_favicon' ], 10, 2 );

		/* Filter for changing the default logo URL, which by default is the site_url() */
		add_filter( 'tcb_logo_site_url', [ $this, 'change_logo_url' ] );
	}

	/**
	 * Registers the route for returning the source for the image id.
	 */
	public function register_routes() {
		register_rest_route( TTB_REST_NAMESPACE, '/image' . '/(?P<id>[\d]+)', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get' ],
				'permission_callback' => [ $this, 'route_permission' ],
			],
		] );
	}

	/**
	 * Return the image source for the given attachment id.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_REST_Response
	 */
	public function get( $request ) {
		$attachment_id = $request->get_param( 'id' );

		if ( empty( $request->get_param( 'is_favicon' ) ) ) {
			$url = TCB_Utils::get_image_src( $attachment_id );
		} else {
			/* favicon has some additional logic to do before returning the URL */
			$url = $this->get_favicon( '', $attachment_id );
		}

		return new WP_REST_Response( $url, 200 );
	}

	/**
	 * Get the data that has to be localized for branding.
	 *
	 * @return array
	 */
	public static function localize() {
		$logos = TCB_Logo::get_logos();

		return [
			'logo_data' => [
				'dark'          => [
					'attachment_id' => $logos[0]['attachment_id'],
					'src'           => $logos[0]['attachment_id'] ? TCB_Utils::get_image_src( $logos[0]['attachment_id'] ) : '',
					'placeholder'   => TCB_Logo::get_placeholder_src( 0 ),
					'logo_id'       => 0,
				],
				'light'         => [
					'attachment_id' => $logos[1]['attachment_id'],
					'src'           => $logos[1]['attachment_id'] ? TCB_Utils::get_image_src( $logos[1]['attachment_id'] ) : '',
					'placeholder'   => TCB_Logo::get_placeholder_src( 1 ),
					'logo_id'       => 1,
				],
				'redirect_url'  => Thrive_Branding::get_logo_url( site_url() ),
				'url_db_option' => THRIVE_LOGO_URL_OPTION,
			],
			'favicon'   => [
				'placeholder' => THRIVE_FAVICON_PLACEHOLDER,
				'id'          => get_option( THRIVE_FAVICON_OPTION, 0 ),
				'db_option'   => THRIVE_FAVICON_OPTION,
			],
		];
	}

	/**
	 * Filter over the logo site URL ( which by default is the site url ).
	 *
	 * @param $logo_url
	 *
	 * @return mixed
	 */
	public function change_logo_url( $logo_url ) {
		$theme_logo_url = static::get_logo_url();

		if ( isset( $theme_logo_url ) && $theme_logo_url !== false ) {
			$logo_url = $theme_logo_url;
		}

		return $logo_url;
	}

	/**
	 * Filter over 'Publish' from the Customizer - when the favicon is modified there, propagate the changes to the theme favicon option.
	 *
	 * @param $data
	 * @param $filter_context
	 *
	 * @return mixed
	 */
	public static function save_favicon( $data, $filter_context ) {
		if ( isset( $data['site_icon']['value'] ) ) {
			update_option( THRIVE_FAVICON_OPTION, $data['site_icon']['value'] );
		}

		return $data;
	}

	/**
	 * Get the favicon of this site. When the URL is empty or the ID does not exist, return the general favicon.
	 *
	 * @param string $general_favicon_url
	 * @param string $favicon_id
	 *
	 * @return string
	 */
	public function get_favicon( $general_favicon_url = '', $favicon_id = '' ) {
		if ( empty( $favicon_id ) ) {
			$favicon_id = get_option( THRIVE_FAVICON_OPTION, 0 );
		}

		/* if the id is 0, then the favicon has been reset and is supposed to show the placeholder */
		if ( is_numeric( $favicon_id ) && (int) $favicon_id === 0 ) {
			$url = '';
		} else {
			$url = TCB_Utils::get_image_src( $favicon_id );

			/* if the skin favicon url is empty (or it was not set), get the general favicon url */
			if ( empty( $url ) ) {
				$url = $general_favicon_url ? $general_favicon_url : static::get_general_favicon_url();
			}
		}

		return $url;
	}

	/**
	 * Get the site favicon (the one initially set from the Customizer).
	 *
	 * @return string
	 */
	public static function get_general_favicon_url() {
		$site_icon_id = get_option( 'site_icon' );

		$url = $site_icon_id ? wp_get_attachment_image_url( $site_icon_id, 'full' ) : '';

		return $url ? $url : '';
	}

	/**
	 * Reset all the branding section settings.
	 */
	public static function reset() {
		$logos = TCB_Logo::get_logos();

		$logos[0]['attachment_id'] = '';
		$logos[1]['attachment_id'] = '';

		update_option( THRIVE_LOGO_URL_OPTION, site_url() );
		update_option( THRIVE_FAVICON_OPTION, site_url() );
	}

	/**
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|bool
	 */
	public function route_permission( $request ) {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Get logo url from theme branding
	 *
	 * @param bool $default
	 *
	 * @return mixed|void
	 */
	public static function get_logo_url( $default = false ) {
		return get_option( THRIVE_LOGO_URL_OPTION, $default );
	}

	/**
	 * Updates the logo url to $new_url
	 *
	 * @param string $new_url
	 *
	 * @return bool
	 */
	public static function set_logo_url( $new_url ) {
		return update_option( THRIVE_LOGO_URL_OPTION, $new_url );
	}
}

new Thrive_Branding();
