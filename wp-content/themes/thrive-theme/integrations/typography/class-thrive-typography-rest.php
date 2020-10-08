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
 * Class Thrive_Typography_REST
 */
class Thrive_Typography_REST {

	public static $namespace = TTB_REST_NAMESPACE;
	public static $route     = '/typography';

	public function __construct() {
		$this->register_routes();
	}

	public function register_routes() {

		register_rest_route( self::$namespace, self::$route, [
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'create' ],
				'permission_callback' => [ $this, 'route_permission' ],
			],
		] );

		register_rest_route( self::$namespace, self::$route . '/(?P<id>[\d]+)', [
			[
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => [ $this, 'update' ],
				'permission_callback' => [ $this, 'route_permission' ],
			],
			[
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => [ $this, 'delete' ],
				'permission_callback' => [ $this, 'route_permission' ],
			],
		] );

		register_rest_route( self::$namespace, self::$route . '/export', [
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'export' ],
				'permission_callback' => [ $this, 'route_permission' ],

			],
		] );

		register_rest_route( self::$namespace, self::$route . '/import_preview', [
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'import_preview' ],
				'permission_callback' => [ $this, 'route_permission' ],

			],
		] );

		register_rest_route( self::$namespace, self::$route . '/import', [
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'import' ],
				'permission_callback' => [ $this, 'route_permission' ],

			],
		] );
	}

	/**
	 * Check if a given request has access to route
	 *
	 * @return WP_Error|bool
	 */
	public function route_permission() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Create new typography set
	 *
	 * @param $request WP_REST_Request
	 *
	 * @return WP_REST_Response
	 */
	public function create( $request ) {

		$post = [
			'post_status' => 'publish',
			'post_type'   => THRIVE_TYPOGRAPHY,
			'post_title'  => $request->get_param( 'post_title' ),
		];

		$id = wp_insert_post( $post );

		thrive_typography( $id )->assign_to_skin();
		$post['ID']          = $id;
		$post['edit_url']    = tcb_get_editor_url( $id );
		$post['preview_url'] = tcb_get_preview_url( $id );

		return new WP_REST_Response( $post, 200 );
	}

	/**
	 * Update a typography set
	 *
	 * @param $request WP_REST_Request
	 *
	 * @return WP_REST_Response
	 */
	public function update( $request ) {
		$action = $request->get_param( 'action' );

		if ( empty( $action ) || ! method_exists( $this, $action ) ) {
			$response = new WP_REST_Response( __( 'No action found!' ), 404 );
		} else {
			$response = $this->$action( $request );

			thrive_skin()->generate_style_file();
		}

		return $response;
	}

	/**
	 * Reset typography set
	 *
	 * @param $request WP_REST_Request
	 *
	 * @return WP_REST_Response
	 */
	public function reset( $request ) {

		$id = $request->get_param( 'id' );

		thrive_typography( $id )->reset();

		return new WP_REST_Response( $id, 200 );
	}

	/**
	 * Reset typography set
	 *
	 * @param $request WP_REST_Request
	 *
	 * @return WP_REST_Response
	 */
	public function set_default( $request ) {
		$id = $request->get_param( 'id' );

		thrive_typography( $id )->set_default();

		thrive_skin()->generate_style_file();

		return new WP_REST_Response( $id, 200 );
	}

	/**
	 * Update typography fields
	 *
	 * @param $request WP_REST_Request
	 *
	 * @return WP_REST_Response
	 */
	public function update_fields( $request ) {
		$id   = $request->get_param( 'id' );
		$post = $request->get_param( 'fields' );
		$meta = $request->get_param( 'meta' );

		$post['ID'] = $id;

		if ( is_array( $meta ) ) {
			foreach ( $meta as $key => $value ) {
				update_post_meta( $id, $key, $value );
			}
		}

		$result = wp_update_post( $post );

		return new WP_REST_Response( $result, 200 );

	}

	/**
	 * Delete typography set
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function delete( $request ) {

		$id = $request->get_param( 'id' );
		/* better to trash it "just in case" */
		wp_trash_post( $id );

		return new WP_REST_Response( $id, 200 );
	}
}

new Thrive_Typography_REST();
