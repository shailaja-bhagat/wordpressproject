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
 * Class Thrive_Templates_REST
 */
class Thrive_Templates_REST {

	public static $namespace = TTB_REST_NAMESPACE;

	public static $route = '/templates';

	/**
	 * Thrive_Templates_REST constructor.
	 */
	public function __construct() {
		$this->register_routes();
	}

	public function register_routes() {
		register_rest_route( self::$namespace, self::$route,
			[
				[
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'create' ],
					'permission_callback' => [ $this, 'route_permission' ],

				],
			]
		);

		register_rest_route( self::$namespace, self::$route . '/export',
			[
				[
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'export' ],
					'permission_callback' => [ $this, 'route_permission' ],

				],
			]
		);

		register_rest_route( self::$namespace, self::$route . '/import_preview', [
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'import_preview' ],
				'permission_callback' => [ $this, 'route_permission' ],

			],
		] );

		register_rest_route( self::$namespace, self::$route . '/factory_reset', [
			[
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => [ $this, 'factory_reset' ],
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

		register_rest_route( self::$namespace, self::$route . '/(?P<id>[\d]+)' . '/preview', [
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'preview' ],
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
	 * Create one item from the collection
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function create( $request ) {

		$data = $this->prepare_template_for_insert( $request );

		$id = wp_insert_post( $data );

		$fallback_template = $request->get_param( 'fallback' );
		if ( $fallback_template ) {
			Thrive_Template_Fallback::update( $fallback_template, $id );
		}

		/* Assign the template to the current skin */
		$template = new Thrive_Template( $id );
		$template->assign_to_skin();

		$template->update( [
			'style' => Thrive_Theme_Default_Data::template_default_styles( $template ),
		] );

		/* If we have no inherit from or it's default => we build the template from scratch */
		$inherit_from = $request->get_param( 'inherit_from' );
		if ( ! empty( $inherit_from ) && 'default' !== $inherit_from ) {
			$template->copy_data_from( $inherit_from );
		}

		/* Set the template as default if there is no default one of its type */
		$similar_templates = $template->get_similar_templates( true );
		if ( empty( $similar_templates ) ) {
			$template->meta_default        = 1;
			$data['meta_input']['default'] = 1;

			/* if we create a new default template, we need to regenerate the style file */
			thrive_skin()->generate_style_file();
		}

		$data['ID']          = $id;
		$data['edit_url']    = $template->edit_url();
		$data['preview_url'] = $template->preview_url();
		$data['layout']      = get_the_title( $template->get_layout() );
		$data['thumbnail']   = $template->thumbnail();

		return new WP_REST_Response( $data, 200 );
	}

	/**
	 * Prepare template before wp_insert_post
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return array
	 */
	private function prepare_template_for_insert( $request ) {

		$template = Thrive_Template::default_values(
			[
				'post_title' => $request->get_param( 'post_title' ),
				'meta_input' => $request->get_param( 'meta_input' ),
			]
		);

		$template['meta_input']['layout'] = thrive_skin()->get_default_layout();
		$template['meta_input']['tag']    = uniqid();

		$format = $template['meta_input']['format'];
		if ( ! empty( $format ) && ( 'video' === $format || 'audio' === $format ) ) {
			$template['meta_input']['sections']['content'] = [
				'id'      => 0,
				'content' => Thrive_Utils::return_part( '/inc/templates/content/content-' . $format . '.php', [], false ),
			];
		}

		return $template;

	}

	/**
	 * Manage update requests
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function update( $request ) {

		$action = $request->get_param( 'action' );

		if ( empty( $action ) || ! method_exists( $this, $action ) ) {
			$response = new WP_REST_Response( __( 'No action found!' ), 404 );
		} else {
			$response = $this->$action( $request );
		}

		return $response;
	}

	/**
	 * General update function
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function update_fields( $request ) {
		$id   = $request->get_param( 'id' );
		$post = $request->get_param( 'fields' );
		$meta = $request->get_param( 'meta' );

		if ( is_array( $meta ) ) {
			foreach ( $meta as $key => $value ) {
				update_post_meta( $id, $key, $value );
			}
		}

		if ( is_array( $post ) ) {
			wp_update_post( array_merge(
				[ 'ID' => $id ], $post
			) );
		}

		return new WP_REST_Response( $id, 200 );
	}

	/**
	 * Create one item from the collection
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function update_template( $request ) {

		$id   = $request->get_param( 'id' );
		$data = $request->get_param( 'template' );

		$template = new Thrive_Template( $id );

		if ( is_string( $data ) ) {
			$data = json_decode( $data, true );
		}

		$template->update( $data );

		return new WP_REST_Response( $id, 200 );
	}

	/**
	 * Make a template default
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public function make_default( $request ) {
		$id = $request->get_param( 'id' );

		$template = new Thrive_Template( $id );
		$template->make_default();

		return new WP_REST_Response( $id, 200 );
	}

	/**
	 * Delete template
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

	/**
	 * Reset template = download the initial template from the cloud or take the default structure + basic css
	 *
	 * @param WP_REST_Request $request Full data about the request
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function reset_template( $request ) {

		$id       = $request->get_param( 'id' );
		$template = new Thrive_Template( $id );
		$tag      = $template->meta( 'tag' );

		try {
			/* If the tag is empty ( when the user tries to reset a default theme template, just break here */
			if ( empty( $tag ) ) {
				throw new Exception( 'The tag parameter is empty, the template will be back to the blank theme form' );
			}
			$data = Thrive_Theme_Cloud_Api_Factory::build( 'templates' )->download_item( $tag, '', [ 'update' => 1 ] );
		} catch ( Exception $e ) {
			$template->reset();
			$data = [
				'success' => false,
				'message' => __( 'There was an error during the download process but the template it\'s back to the blank theme form', THEME_DOMAIN ),
				'error'   => $e->getMessage(),
			];
		}

		thrive_skin()->generate_style_file();

		return new WP_REST_Response( $data, 200 );
	}

	/**
	 * Reset all templates and regenerate the css file
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function factory_reset() {

		foreach ( thrive_skin()->get_templates( 'object' ) as $template ) {
			if ( $template->is_default() ) {
				$template->reset();
			} else {
				wp_trash_post( $template->ID );
			}
		}

		thrive_skin()->generate_style_file();

		return new WP_REST_Response( 1, 200 );
	}

	/**
	 * Save and return a preview for the template.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return mixed
	 */
	public function preview( $request ) {

		if ( ! isset( $_FILES['img_data'] ) ) {
			return new WP_Error( 'template_preview_error', __( 'Wrong template preview data', THEME_DOMAIN ) );
		}

		$id = $request->get_param( 'id' );

		try {
			$preview_data = thrive_template( $id )->create_preview();

			$response = new WP_REST_Response( $preview_data, 200 );
		} catch ( Exception $ex ) {
			$response = new WP_Error( 'template_preview_error', $ex->getMessage() );
		}

		return $response;
	}
}

new Thrive_Templates_REST();
