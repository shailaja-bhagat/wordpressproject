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
 * Class Thrive_Demo_Content_REST
 */
class Thrive_Demo_Content_REST {

	public static $namespace = TTB_REST_NAMESPACE;
	public static $route     = '/demo-content';

	public function __construct() {
		$this->register_routes();
	}

	public function register_routes() {

		register_rest_route( self::$namespace, self::$route . '/generate', [
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'generate' ],
				'permission_callback' => [ $this, 'route_permission' ],
			],
		] );

	}

	/**
	 * Generate demo content data
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_REST_Response
	 */
	public function generate( $request ) {

		$singular    = $request->get_param( 'singular' );
		$template_id = $request->get_param( 'template_id' );

		Thrive_Demo_Content::init( true );

		Thrive_Demo_Content::generate();

		$urls = [
			'edit'    => Thrive_Demo_Content::url( false, $singular, $template_id ),
			'preview' => Thrive_Demo_Content::url( true, $singular, $template_id ),
		];

		return new WP_REST_Response( $urls, 200 );
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
}

new Thrive_Demo_Content_REST();
