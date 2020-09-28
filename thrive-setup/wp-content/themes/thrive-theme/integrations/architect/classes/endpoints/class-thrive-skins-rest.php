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
 * Class Thrive_Skins_Rest
 */
class Thrive_Skins_Rest extends WP_REST_Terms_Controller {

	use Thrive_Term_Meta;

	public static $version = 1;
	public static $route   = '/skins';

	public function __construct() {
		parent::__construct( SKIN_TAXONOMY );

		$this->namespace = TTB_REST_NAMESPACE;
		$this->rest_base = self::$route;

		$this->register_routes();
		$this->hooks();
		$this->register_meta_fields();

		static::register_term_routes( $this->namespace, static::$route, [ $this, 'route_permission' ] );
	}

	public function register_routes() {
		parent::register_routes();

		register_rest_route( $this->namespace, self::$route . '/export', [
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'export' ],
				'permission_callback' => [ $this, 'route_permission' ],

			],
		] );

		register_rest_route( $this->namespace, self::$route . '/import', [
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'import' ],
				'permission_callback' => [ $this, 'route_permission' ],

			],
		] );

		register_rest_route( $this->namespace, self::$route . '/cloud_import', [
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'cloud_import' ],
				'permission_callback' => [ $this, 'route_permission' ],

			],
		] );

		register_rest_route( $this->namespace, self::$route . '/(?P<skin_id>[\d]+)/skin_variables', [
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'skin_variables' ],
				'permission_callback' => [ $this, 'route_permission' ],

			],
		] );

		register_rest_route( $this->namespace, self::$route . '/(?P<id>[\d]+)/change_palette', [
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'change_palette' ],
				'permission_callback' => [ $this, 'route_permission' ],

			],
		] );

		register_rest_route( $this->namespace, self::$route . '/(?P<id>[\d]+)/reset_palette', [
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'reset_palette' ],
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
	 * Hooks to change the terms rest api
	 */
	public function hooks() {
		add_action( 'rest_insert_' . SKIN_TAXONOMY, [ $this, 'after_skin_insert' ], 10, 3 );
	}

	/**
	 * Add meta fields to be able to update / get them with the rest api
	 */
	public function register_meta_fields() {
		/* for each meta field, register a new rest field */
		foreach ( Thrive_Skin::META_FIELDS as $meta_field ) {
			$get_callback    = 'get_' . $meta_field;
			$update_callback = 'update_' . $meta_field;

			/* add a get callback and an update callback, if they exist (null is the default callback value) */
			register_rest_field( $this->get_object_type(), $meta_field, [
				'get_callback'    => method_exists( $this, $get_callback ) ? [ $this, $get_callback ] : null,
				'update_callback' => method_exists( $this, $update_callback ) ? [ $this, $update_callback ] : null,
			] );
		}
	}

	/**
	 * @param WP_Term         $term     Inserted or updated term object.
	 * @param WP_REST_Request $request  Request object.
	 * @param bool            $creating True when creating a term, false when updating.
	 */
	public function after_skin_insert( $term, $request, $creating ) {
		if ( $creating ) {
			$skin_id        = $term->term_id;
			$source_skin_id = $request->get_param( 'source_skin_id' );

			if ( $source_skin_id ) {
				$new_skin = new Thrive_Skin( $skin_id );

				/* duplicate the meta fields from the source to the new skin */
				$new_skin->duplicate_meta( $source_skin_id );

				/* set the 'is_active' meta as inactive (we don't want to activate the duplicated skin) */
				$new_skin->set_meta( Thrive_Skin::SKIN_META_ACTIVE, 0 );
			}

			/* create templates */
			Thrive_Theme_Default_Data::create_skin_templates( $skin_id, $source_skin_id );

			/* create default typography */
			Thrive_Theme_Default_Data::create_skin_typographies( $skin_id, $source_skin_id );

			do_action( 'theme_after_skin_insert', $skin_id, $source_skin_id );
		}
	}

	/**
	 * Get meta value for is skin active
	 *
	 * @param $skin_data
	 *
	 * @return mixed
	 */
	public function get_is_active( $skin_data ) {
		return get_term_meta( $skin_data['id'], Thrive_Skin::SKIN_META_ACTIVE, true );
	}

	/**
	 * Make a skin active
	 *
	 * @param $meta_value
	 * @param $skin
	 * @param $meta_key
	 */
	public function update_is_active( $meta_value, $skin, $meta_key ) {
		if ( (int) $meta_value === 1 ) {

			Thrive_Skin_Taxonomy::set_skin_active( $skin->term_id );

			/* We need to make sure that the instance is the one with the active skin before we generate the css file */
			thrive_skin( $skin->term_id )->generate_style_file();
		}
	}

	/**
	 * Export skin
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function export( $request ) {

		$name    = $request->get_param( 'name' );
		$skin_id = $request->get_param( 'skin_id' );

		try {
			$transfer = new Thrive_Transfer_Export( $name );
			$response = $transfer->export( 'skin', $skin_id );
		} catch ( Exception $ex ) {
			$response = $ex->getMessage();
		}

		return new WP_REST_Response( $response, 200 );
	}

	/**
	 * Import skin
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function import( $request ) {
		$archive_file = $this->get_archive_file( $request );

		try {
			$import   = new Thrive_Transfer_Import( $archive_file );
			$skin     = $import->import( 'skin' );
			$response = new WP_REST_Response( $skin, 200 );
		} catch ( Exception $e ) {
			$response = new WP_Error( 'import_error', $e->getMessage(), [ 'status' => 412 ] );
		}

		return $response;
	}

	/**
	 * Get the file path from where the skin will be imported
	 *
	 * @param $request WP_REST_Request
	 *
	 * @return false|string
	 */
	public function get_archive_file( $request ) {
		$attachment_id = $request->get_param( 'attachment_id' );

		if ( isset( $attachment_id ) ) {
			$archive_file = get_attached_file( $attachment_id );
		} else {
			$archive_file = $request->get_param( 'archive_path' );
		}

		return $archive_file;
	}

	/**
	 * Resets the skin palette to the original
	 *
	 * Called from the UI
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_REST_Response
	 */
	public function reset_palette( $request ) {

		$active_id = (int) $request->get_param( 'active_id' );
		$skin_id   = (int) $request->get_param( 'id' );

		$thrive_skin   = thrive_skin( $skin_id );
		$skin_palettes = $thrive_skin->get_palettes();

		$skin_palettes['modified'][ $active_id ] = $skin_palettes['original'][ $active_id ];
		$thrive_skin->update_palettes( $skin_palettes );

		$skin_variables = $thrive_skin->get_variables();

		foreach ( $skin_variables as $type => $values ) {

			if ( empty( $values ) || ! is_array( $values ) ) {
				continue;
			}

			foreach ( $values as $key => $value ) {
				if ( empty( $skin_variables[ $type ][ $key ] ) || empty( $skin_palettes['original'][ $active_id ][ $type ][ $key ] ) ) {
					continue;
				}
				$skin_variables[ $type ][ $key ] = array_merge( $skin_variables[ $type ][ $key ], $skin_palettes['original'][ $active_id ][ $type ][ $key ] );
			}
		}

		$thrive_skin->update_variables( $skin_variables );

		return new WP_REST_Response( [ 'success' => 1 ], 200 );
	}

	/**
	 * Called when a palette is changed from the UI
	 *
	 * Changes a color palette and saved the modification for the previous palette for later use
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function change_palette( $request ) {

		$previous_id = (int) $request->get_param( 'previous_id' );
		$active_id   = (int) $request->get_param( 'active_id' );
		$skin_id     = (int) $request->get_param( 'id' );

		$previous_skin_data = json_decode( stripslashes( $request->get_param( 'previous_skin_data' ) ), true );
		$active_skin_data   = json_decode( stripslashes( $request->get_param( 'active_skin_data' ) ), true );

		if ( empty( $previous_skin_data ) || empty( $active_skin_data ) ) {
			return new WP_Error( 'invalid-parameter', __( 'The previous theme data or active theme data parameters are missing from the request', THEME_DOMAIN ), [ 'status' => 400 ] );
		}

		/**
		 * A whitelist that is being parsed when constructing the array that is stored inside the database
		 */
		$whitelist = [
			'id',
			'color',
			'gradient',
			'hsl',
			'hsl_parent_dependency',
		];

		foreach ( $previous_skin_data as $type => $values ) {
			foreach ( $values as $key => $value ) {
				$filtered = array_intersect_key( $value, array_flip( $whitelist ) );

				$previous_skin_data[ $type ][ $key ] = $filtered;
			}
		}

		$thrive_skin = thrive_skin( $skin_id );

		$skin_palettes                             = $thrive_skin->get_palettes();
		$skin_palettes['active_id']                = $active_id;
		$skin_palettes['modified'][ $previous_id ] = array_merge( $previous_skin_data, [ 'name' => $skin_palettes['original'][ $previous_id ]['name'] ] );

		$thrive_skin->update_palettes( $skin_palettes );

		/**
		 * A whitelist that is being parsed when constructing the array that is stored inside the database
		 */
		$whitelist_skin_meta = [
			'id',
			'color',
			'gradient',
			'name',
			'custom_name',
			'parent',
			'hsl',
			'hsl_parent_dependency',
		];

		foreach ( $active_skin_data as $type => $values ) {
			foreach ( $values as $key => $value ) {
				$filtered = array_intersect_key( $value, array_flip( $whitelist_skin_meta ) );

				$active_skin_data[ $type ][ $key ] = $filtered;
			}
		}

		$thrive_skin->update_variables( $active_skin_data );

		return new WP_REST_Response( [ 'success' => 1 ], 200 );
	}

	/**
	 * Used to update the skin variables
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function skin_variables( $request ) {
		$type = $request->get_param( 'type' );

		if ( ! in_array( $type, [ 'colors', 'gradients' ] ) ) {
			return new WP_Error( 'invalid-parameter', __( 'Invalid parameter: type.', THEME_DOMAIN ), [ 'status' => 400 ] );
		}

		$skin_id     = $request->get_param( 'skin_id' );
		$name        = $request->get_param( 'name' );
		$key         = $request->get_param( 'key' );
		$value       = $request->get_param( 'value' );
		$id          = $request->get_param( 'id' );
		$hsl         = $request->get_param( 'hsl' );
		$linked_vars = $request->get_param( 'linked_variables' );
		$custom_name = is_numeric( $request->get_param( 'custom_name' ) )
		               && in_array( $request->get_param( 'custom_name' ), [ 0, 1 ] ) ? $request->get_param( 'custom_name' ) : 0;

		if ( empty( $name ) || empty( $value ) || ! is_string( $value ) || ! is_string( $name ) || ! is_numeric( $id ) ) {
			/**
			 * The variable has to have a name and it must be a valid string
			 */
			return new WP_Error( 'invalid-parameter', __( 'Invalid parameters! A variable must contain a name, an id and a value string!', THEME_DOMAIN ), [ 'status' => 400 ] );
		}

		$thrive_skin = thrive_skin( $skin_id );

		$variables      = $thrive_skin->get_variables();
		$variables_type = $variables[ $type ];

		$index = array_search( $id, array_column( $variables_type, 'id' ) );

		if ( $index !== false ) {
			$variables[ $type ][ $index ][ $key ] = $value;
			$variables[ $type ][ $index ]['name'] = $name;

			if ( ! empty( $hsl ) && is_array( $hsl ) ) {
				$variables[ $type ][ $index ]['hsl'] = $hsl;
			}

			if ( $custom_name ) {
				/**
				 * Update the custom name only if the value is 1
				 */
				$variables[ $type ][ $index ]['custom_name'] = $custom_name;
			}
		}
		if ( is_array( $linked_vars ) ) {
			foreach ( $linked_vars as $var_id => $new_value ) {

				$index = array_search( $var_id, array_column( $variables_type, 'id' ) );

				if ( $index !== false ) {
					$variable_value = $new_value;

					if ( is_array( $new_value ) && ! empty( $new_value['hsl_parent_dependency'] ) ) {
						$variables[ $type ][ $index ]['hsl_parent_dependency'] = $new_value['hsl_parent_dependency'];
						$variable_value                                        = $new_value['value'];
					}

					$variables[ $type ][ $index ][ $key ] = $variable_value;
				}
			}
		}

		$thrive_skin->update_variables( $variables );

		return new WP_REST_Response( $variables, 200 );
	}

	/**
	 * Download a skin archive from the cloud
	 *
	 * @param $request WP_REST_Request
	 *
	 * @return WP_REST_Response
	 */
	public function cloud_import( $request ) {
		$skin_id = $request->get_param( 'skin_id' );

		/* First download the skin from the cloud */
		try {
			$archive_file = Thrive_Theme_Cloud_Api_Factory::build( 'skins' )->download_item( $skin_id, $request );
		} catch ( Exception $e ) {
			$archive_file = $e;
		}

		if ( $archive_file instanceof Exception ) {
			$response = $archive_file->getMessage();
		} else {
			/* If everything it's ok with the download go ahead and import the skin */
			try {
				$import   = new Thrive_Transfer_Import( $archive_file );
				$response = $import->import( 'skin' );
			} catch ( Exception $e ) {
				$response = $e->getMessage();
			}
		}

		return new WP_REST_Response( $response, 200 );
	}
}

new Thrive_Skins_Rest();
