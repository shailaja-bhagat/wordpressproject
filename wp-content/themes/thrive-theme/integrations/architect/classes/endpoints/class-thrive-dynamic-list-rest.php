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
 * Class Thrive_Dynamic_List_REST
 */
class Thrive_Dynamic_List_REST {

	public static $namespace = TTB_REST_NAMESPACE;
	public static $route     = '/list';

	public function __construct() {
		$this->register_routes();
	}

	public function register_routes() {

		register_rest_route( self::$namespace, self::$route, [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_list' ],
				'permission_callback' => [ $this, 'route_permission' ],
			],
		] );
	}

	/**
	 * Check if a given request has access to route
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|bool
	 */
	public function route_permission( $request ) {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Get dynamic list by type
	 *
	 * @param $request
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_list( $request ) {
		$args             = $request->get_param( 'args' );
		$use_demo_content = ! empty( $request->get_param( 'demo-content' ) );

		if ( ! isset( $args ) || empty( $args['type'] ) ) {
			return new WP_Error( 'invalid-parameter', __( 'The type parameter is missing from the request', THEME_DOMAIN ), [ 'status' => 400 ] );
		}

		if ( $use_demo_content ) {
			/* initialize demo content data */
			Thrive_Demo_Content::init( true );
		}

		$args = Thrive_Shortcodes::parse_attr( $args, 'thrive_dynamic_list' );

		$content = Thrive_Shortcodes::dynamic_list( $args, $use_demo_content );

		return new WP_REST_Response( [
			'success' => 1,
			'content' => $content,
		] );

	}
}

new Thrive_Dynamic_List_REST();
