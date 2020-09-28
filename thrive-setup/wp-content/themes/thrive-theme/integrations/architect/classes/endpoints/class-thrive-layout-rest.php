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
 * Class Thrive_Layout_REST
 */
class Thrive_Layout_REST {

	public static $namespace = TTB_REST_NAMESPACE;
	public static $route = '/layouts';

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
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'read' ],
				'permission_callback' => [ $this, 'route_permission' ],
			],
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
	}

	/**
	 * Create a new layout
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public function create( $request ) {

		$data = $this->prepare_layout_for_insert( $request );

		$layout_id = wp_insert_post( $data );

		/* assign layout to skin */
		wp_set_object_terms( $layout_id, thrive_skin()->ID, SKIN_TAXONOMY );

		return new WP_REST_Response( $layout_id, 200 );
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public function read( $request ) {

		$layout_id = $request->get_param( 'id' );

		$layout = new Thrive_Layout( $layout_id );

		$response = array_merge(
			[ 'style' => $layout->style() ],
			$layout->export( [ 'sidebar_on_left', 'hide_sidebar', 'content_width' ] )
		);

		return new WP_REST_Response( $response, 200 );
	}

	/**
	 * Update layout data
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public function update( $request ) {

		$meta      = $request->get_param( 'meta_input' );
		$layout_id = (int) $request->get_param( 'id' );

		if ( ! empty( $meta ) ) {
			foreach ( Thrive_Layout::$meta_fields as $key => $value ) {
				update_post_meta( $layout_id, $key, isset( $meta[ $key ] ) ? $meta[ $key ] : $value );
			}

			/* reset the layout data for the current template */
			thrive_template( $meta['template_id'] )->set_meta( 'layout_data', [] );
		}

		thrive_skin()->generate_style_file();

		$layout = new Thrive_Layout( $layout_id );

		$response = [
			'data'  => $layout->export( [ 'sidebar_on_left', 'hide_sidebar', 'content_width' ] ),
			'style' => $layout->style(),
		];

		return new WP_REST_Response( $response, 200 );
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public function delete( $request ) {

		$layout_id = $request->get_param( 'id' );

		wp_trash_post( $layout_id );

		//TODO: maybe remove it from templates?

		return new WP_REST_Response( 1, 200 );
	}

	/**
	 * Prepare layout before wp_insert_post
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return array
	 */
	private function prepare_layout_for_insert( $request ) {

		$layout = [
			'post_status' => 'publish',
			'post_type'   => THRIVE_LAYOUT,
		];

		$meta                 = $request->get_param( 'meta_input' );
		$layout['post_title'] = $request->get_param( 'post_title' );

		/* First let's make sure we have all the default meta values*/
		$meta_input = wp_parse_args( $meta, Thrive_Layout::$meta_fields );

		$layout['meta_input'] = $meta_input;

		return $layout;
	}

	/**
	 * Check if a given request has access to the route.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|bool
	 */
	public function route_permission( $request ) {
		return Thrive_Theme_Product::has_access();
	}

}

new Thrive_Layout_REST();
