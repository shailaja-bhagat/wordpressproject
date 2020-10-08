<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

use Thrive\Theme\Integrations\WooCommerce\Helpers as WooHelper;
use Thrive\Theme\Integrations\WooCommerce\Main;

/**
 * Class Thrive_Wizard_REST
 */
class Thrive_Wizard_REST {

	public static $version = 1;
	public static $route   = '/wizard';

	public function __construct() {
		$this->register_routes();
	}

	public function register_routes() {
		register_rest_route( TTB_REST_NAMESPACE, self::$route . '/templates', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'fetch_templates' ],
				'permission_callback' => [ $this, 'route_permission' ],

			],
		] );

		register_rest_route( TTB_REST_NAMESPACE, self::$route . '/templates/(?P<id>.+)', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'fetch_template' ],
				'permission_callback' => [ $this, 'route_permission' ],

			],
		] );

		register_rest_route( TTB_REST_NAMESPACE, self::$route, [
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'save_wizard' ],
				'permission_callback' => [ $this, 'route_permission' ],

			],
		] );

		register_rest_route( TTB_REST_NAMESPACE, self::$route, [
			[
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => [ $this, 'restart_wizard' ],
				'permission_callback' => [ $this, 'route_permission' ],

			],
		] );

		register_rest_route( TTB_REST_NAMESPACE, self::$route . '/pages', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'suggest_pages' ],
				'permission_callback' => [ $this, 'route_permission' ],

			],
		] );
	}

	/**
	 * Get dynamic list by type
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_Error|WP_REST_Response|boolean
	 * @throws Exception
	 */
	public function fetch_templates( $request ) {
		$type = $request->get_param( 'type' );
		$data = [];

		switch ( $type ) {
			case THRIVE_HEADER_SECTION:
			case THRIVE_FOOTER_SECTION:
				$section_rest = new Thrive_Section_REST();
				$request->set_param( 'type', $type );
				/* remove "blank" templates for now */
				$data = array_values( array_filter( $section_rest->get_cloud_sections( $request )->get_data()['data'], static function ( $template ) {
					return strpos( $template['post_title'], 'Blank' ) !== 0;
				} ) );
				break;
			case 'homepage':
				$templates = tve_get_cloud_templates( [
					'home'     => '1',
					'skin_tag' => thrive_skin()->get_tag(),
				] );
				/* fetch a list of Landing Pages that have been marked as homepages */
				$data = array_values( array_map( static function ( $template, $id ) {
					return [
						'id'         => $id,
						'post_title' => $template['name'],
						'thumb'      => isset( $template['preview_image'] ) ? $template['preview_image'] : [],
					];
				}, $templates, array_keys( $templates ) ) );
				break;
			case 'post':
			case 'blog':
			case 'page':
				$data = Thrive_Wizard::get_templates( $type );
				break;
			default:
				break;
		}

		/**
		 * Change wizard templates for each step if necessary
		 *
		 * @param array           $data    The templates specific for each step
		 * @param WP_REST_Request $request Rest request
		 */
		$data = apply_filters( 'thrive_theme_wizard_templates', $data, $request );

		return new WP_REST_Response( [
			'success' => 1,
			'data'    => $data,
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
		return Thrive_Theme_Product::has_access();
	}

	/**
	 * Renders a single template instance and returns the output.
	 * Used initially for headers / footers
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public function fetch_template( $request ) {
		$id   = $request['id'];
		$type = $request->get_param( 'type' );

		$data = [
			'id'   => $id,
			'html' => '',
		];

		switch ( $type ) {
			case THRIVE_HEADER_SECTION:
			case THRIVE_FOOTER_SECTION:
				/* get the HTML / CSS for a cloud template */
				$content = thrive_wizard()->get_hf_preview_content( $type, $id );

				$data['id']   = $id;
				$data['html'] = ( new Thrive_HF_Section( 0, $type, [ 'content' => $content ] ) )->render();
				break;
		}

		/**
		 * Maybe fetch another template based on the type
		 *
		 * @param array  $data
		 * @param string $type
		 * @param int    $id
		 */
		$data = apply_filters( 'thrive_theme_wizard_fetch_template', $data, $type, $id );

		return new WP_REST_Response( [
			'success' => 1,
			'data'    => $data,
		] );
	}

	/**
	 * Saves wizard data
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response $response
	 * @throws
	 *
	 */
	public function save_wizard( $request ) {
		$wizard = json_decode( $request->get_body(), true );
		$step   = $request->get_param( 'step' );

		$response = [
			'success' => true,
		];

		if ( $wizard && $step ) {
			/* get it here and use it when needed */

			$template_id = isset( $wizard['settings'][ $step ]['template_id'] ) ? $wizard['settings'][ $step ]['template_id'] : 0;
			$settings    = $wizard['settings'][ $step ];

			/* step -> step being saved */
			switch ( $step ) {
				case 'logo':
					if ( ! empty( $settings['dark_id'] ) ) {
						/* forward request to branding controller */
						$request->set_method( 'PUT' );
						$request->set_param( 'id', 0 );
						$request->set_param( 'attachment_id', $settings['dark_id'] );
						$result = TCB_Logo_REST::update( $request );

						/* next, "light" logo */
						if ( $result->get_status() === 200 ) {
							$request->set_param( 'id', 1 );
							$result = TCB_Logo_REST::update( $request );

							/* also, update the URL of the logo to point to the homepage, if nothing is already set */
							if ( ! Thrive_Branding::get_logo_url() ) {
								Thrive_Branding::set_logo_url( home_url() );
							}
						}

						if ( $result->get_status() !== 200 ) {
							$response['success'] = false;
							$response['message'] = $result->get_data();
						}
					}
					break;
				case 'color':
					/**
					 * Store the main skin color selected by the user, if any
					 */
					$saved = false;
					if ( ! empty( $settings['save_data'] ) ) {
						/* forward the request to the skin_variables route */
						$request->set_body_params( $settings['save_data'] );
						$skin_controller = new Thrive_Skins_Rest();

						$result = $skin_controller->skin_variables( $request );
						if ( $result instanceof WP_Error ) {
							$response['success'] = false;
							$response['message'] = $result->get_error_message();
						} else {
							$saved = true;
						}
					}

					$wizard['settings'][ $step ] = [
						'saved' => $saved, // we don't need to actually save anything here. Just store a flag that this has been saved
					];
					break;
				case 'header':
				case 'footer':
					$id = thrive_skin()->get_default_data( $step );
					/* go through all templates and set the same header / footer */
					$symbol_id = Thrive_HF_Section::populate_from_cloud_template(
						$template_id,
						$step,
						'Default ' . ucfirst( $step ) . ' for ' . thrive_skin()->term->name,
						get_post( $id )
					);

					if ( ! is_wp_error( $symbol_id ) ) {
						/* make sure this is the default H/F section for the skin */
						thrive_skin()->set_default_data( $step, $symbol_id );
						$response['id'] = $symbol_id;

						/* update all templates */
						foreach ( thrive_skin()->get_templates( 'object' ) as $template ) {
							/** @var Thrive_Template $template */
							$template->set_header_footer( $step, $symbol_id );
						}

						/* if page_on_front -> also set header / footer in there */
						if ( get_option( 'show_on_front' ) === 'page' ) {
							$page_id = get_option( 'page_on_front' );

							if ( $page_id ) {
								update_post_meta( $page_id, "_tve_{$step}", $symbol_id );
							}
						}
					} else {
						$response['success'] = false;
						$response['message'] = $symbol_id->get_error_message();
					}
					break;
				case 'homepage':
					if ( $settings['type'] === 'blog' ) {
						/* directly update the WP option */
						update_option( 'show_on_front', 'posts' );
					} else {
						$is_landing_page = $settings['type'] === 'template';
						$page_id         = $is_landing_page ? Thrive_Defaults::get_default_post_id( 'homepage', 'Generated Homepage', 'page' ) : (int) $settings['page_id'];

						/* apply landing page template on homepage */
						if ( $is_landing_page && method_exists( TCB_Landing_Page::class, 'apply_cloud_template' ) ) {
							/* append header and footer before save */
							TCB_Landing_Page::apply_cloud_template( $page_id, $template_id );

							update_post_meta( $page_id, '_tve_header', thrive_skin()->get_default_data( THRIVE_HEADER_SECTION ) );
							update_post_meta( $page_id, '_tve_footer', thrive_skin()->get_default_data( THRIVE_FOOTER_SECTION ) );
							delete_post_meta( $page_id, 'tve_disable_theme_dependency' );
						}

						update_option( 'show_on_front', 'page' );
						update_option( 'page_on_front', $page_id );

						$page_for_posts_id = get_option( 'page_for_posts' );
						$page_for_posts    = get_post( $page_for_posts_id );

						if ( empty( $page_for_posts_id ) || ! $page_for_posts || $page_for_posts->post_status !== 'publish' ) {
							/* set a default "Posts" page */
							update_option( 'page_for_posts', Thrive_Defaults::get_default_post_id( 'blog', 'Blog', 'page' ) );
						}
					}
					break;
				case 'post':
				case 'blog':
				case 'page':
					/* save Single Post default template */
					$template = new Thrive_Template( $wizard['settings'][ $step ]['template_id'] );
					$template->make_default();
					break;
				case 'menu':
					try {
						$wizard['settings'][ $step ] = $this->replace_menu_in_sections( $wizard['settings'][ $step ] );
						thrive_skin()->set_default_data( 'header_menu', $wizard['settings'][ $step ]['header'] > 0 ? $wizard['settings'][ $step ]['header'] : 0 );
						thrive_skin()->set_default_data( 'footer_menu', $wizard['settings'][ $step ]['footer'] > 0 ? $wizard['settings'][ $step ]['footer'] : 0 );
						$response['success'] = true;
					} catch ( Exception $ex ) {
						$response = [
							'success' => false,
							'message' => $ex->getMessage(),
						];
					}
					break;
				default:
					break;
			}

			/**
			 * Change wizard response when saving
			 * Also, give the possibility to add other steps to the wizard with specific functionality
			 *
			 * @param array  $response The response obtained up to this point
			 * @param array  $wizard   Wizard data
			 * @param string $step     The step that we are saving
			 */
			$response = apply_filters( 'thrive_theme_wizard_save_response', $response, $wizard, $step );
		}

		if ( ! empty( $response['success'] ) ) {
			/* store next step */
			$wizard['active']      = $request->get_param( 'next' );
			$wizard['activeIndex'] = $request->get_param( 'nextIndex' );
			thrive_skin()->set_meta( 'ttb_wizard', $wizard );
		}

		/* always refresh these */
		$localized_data = Thrive_Wizard::localize_admin();
		unset( $localized_data['suggest_pages'] );

		return new WP_REST_Response( $response + $localized_data );
	}

	/**
	 * Restart the current wizard
	 *
	 * @param WP_REST_Request $request
	 */
	public function restart_wizard( $request ) {
		thrive_skin()->set_meta( 'ttb_wizard', [] );

		return new WP_REST_Response( [] );
	}

	/**
	 * Suggest a list of pages
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public function suggest_pages( $request ) {

		return new WP_REST_Response( Thrive_Wizard::autocomplete_pages( [
			's'           => $request->get_param( 'search' ),
			'numberposts' => $request->get_param( 'limit' ) ?: 5,
			'offset'      => $request->get_param( 'offset' ) ?: 0,
		] ) );
	}

	/**
	 * Replace default menu ids in all the headers/footers from the templates
	 *
	 * @param array $default_ids
	 *
	 * @return array sanitized array with IDs
	 *
	 * @throws Exception
	 *
	 */
	public function replace_menu_in_sections( $default_ids = [] ) {
		$default_ids = array_map( 'intval', $default_ids );

		$filtered = array_filter( $default_ids, static function ( $id ) {
			return $id > 0;
		} );

		if ( empty( $filtered ) ) {
			return $default_ids; // nothing more to do here
		}
		$processed = [];
		foreach ( thrive_skin()->get_templates( 'object' ) as $template ) {
			/** @var Thrive_Template $template */

			foreach ( $filtered as $section_type => $default_menu_id ) {
				$instance = $template->get_hf_section_instance( $section_type );
				if ( ! $instance->is_dynamic() || ( $instance->is_dynamic() && empty( $processed[ $instance->ID ] ) ) ) {
					$section_id = $instance->replace_menu( $default_menu_id );
					if ( ! empty( $section_id ) ) {
						$processed[ $section_id ] = 1;
					}
				}
			}
		}

		return $default_ids;
	}
}

new Thrive_Wizard_REST();
