<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-theme
 */

use Thrive\Theme\AMP\Settings as AMP_Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

/**
 * Class Architect
 */
class Thrive_Architect {

	/**
	 * @var array Architect Elements that will be visible in the theme
	 */
	protected $theme_elements = [];

	public function __construct() {

		defined( 'LIGHT_ARCHITECT' ) || define( 'LIGHT_ARCHITECT', ! defined( 'TVE_IN_ARCHITECT' ) );

		$this->includes();

		$this->register_post_types();

		$this->filters();

		$this->actions();
	}

	/**
	 * Check if we have the light Architect or the full one
	 *
	 * @return bool
	 */
	public static function is_light() {
		return LIGHT_ARCHITECT;
	}

	private function includes() {
		/* set a custom architect URL to allow for symlink-based setups */
		$current_architect_url = get_stylesheet_directory_uri() . '/architect/';
		include THEME_PATH . '/architect/external-architect.php';

		require_once ARCHITECT_INTEGRATION_PATH . '/classes/class-thrive-template.php';

		require_once ARCHITECT_INTEGRATION_PATH . '/classes/class-thrive-section.php';
		require_once ARCHITECT_INTEGRATION_PATH . '/classes/class-thrive-hf-section.php';
		require_once ARCHITECT_INTEGRATION_PATH . '/classes/class-thrive-layout.php';
		require_once ARCHITECT_INTEGRATION_PATH . '/classes/class-thrive-dynamic-list-helper.php';
		require_once ARCHITECT_INTEGRATION_PATH . '/classes/class-thrive-content-switch.php';
		require_once ARCHITECT_INTEGRATION_PATH . '/classes/class-thrive-demo-content.php';

		require_once ARCHITECT_INTEGRATION_PATH . '/classes/class-thrive-skin.php';
		require_once ARCHITECT_INTEGRATION_PATH . '/classes/class-thrive-skin-taxonomy.php';
		require_once ARCHITECT_INTEGRATION_PATH . '/classes/class-thrive-theme-default-data.php';
		require_once ARCHITECT_INTEGRATION_PATH . '/classes/class-thrive-template-fallback.php';
	}

	private function actions() {
		add_action( 'after_setup_theme', [ $this, 'after_setup_theme' ] );

		/* Enqueue scripts needed in the editor */
		add_action( 'wp_enqueue_scripts', [ $this, 'editor_enqueue_scripts' ], 9 );

		add_action( 'tcb_main_frame_enqueue', [ $this, 'tcb_main_frame_enqueue' ], 9 );

		/* register rest routes used by Architect */
		add_action( 'rest_api_init', [ $this, 'rest_api_init' ] );

		add_action( 'parse_request', [ $this, 'parse_request' ] );

		add_action( 'tcb_output_components', [ $this, 'tcb_output_components' ] );

		add_action( 'wp_print_footer_scripts', [ $this, 'wp_print_footer_scripts' ], 9 );

		add_action( 'tcb_editor_enqueue_scripts', [ $this, 'tcb_editor_enqueue_scripts' ] );

		add_action( 'tcb_editor_iframe_after', [ $this, 'tcb_add_editor_svgs' ] );

		add_action( 'tcb_sidebar_extra_links', [ $this, 'add_extra_links' ] );

		add_action( 'tcb_cpanel_top_content', [ $this, 'add_top_content' ] );

		add_action( 'tcb_sidebar_elements_notice', [ $this, 'add_tar_light_notice' ] );

		add_action( 'tcb_output_extra_editor_svg', [ $this, 'tcb_add_extra_svg_in_iframe' ] );

		add_action( 'tcb_ajax_save_post', [ $this, 'tcb_ajax_save_post' ], 10, 2 );

		add_action( 'pre_delete_term', [ $this, 'pre_delete_term' ], 10, 2 );

		add_action( 'updated_postmeta', [ $this, 'updated_postmeta' ], 10, 4 );

		add_action( 'tcb_ajax_before_cloud_content_template_download', [ $this, 'set_global_post_before_cloud_ajax' ] );

		add_action( 'pre_get_posts', [ $this, 'search_filter_post_types' ] );

		add_action( 'tcb_get_extra_global_variables', [ $this, 'output_skin_variables' ] );

		add_action( 'tcb_set_lp_cloud_template', [ $this, 'theme_set_cloud_landing_page' ], 10, 2 );

		add_action( 'tcb_extra_postlist_links', [ $this, 'add_extra_dynamic_links' ] );

		add_action( 'tcb_extra_landing_page_lightbox_set_icons', [ $this, 'add_extra_lp_lightbox_set_icons' ] );
		add_action( 'tcb_extra_landing_page_lightbox_icons', [ $this, 'add_extra_lp_lightbox_icons' ] );

		add_action( 'thrive_theme_template_copied_data', [ 'Thrive_Post_List', 'thrive_theme_template_copied_data' ], 10, 2 );

		add_action( 'tve_after_load_custom_css', [ $this, 'after_load_custom_css' ] );

		add_action( 'wp', static function () {
			/* We still need some of the shortcodes to modify content inside template editing*/
			if ( Thrive_Utils::is_inner_frame() ) {
				remove_filter( 'tcb_clean_frontend_content', 'tcb_clean_frontend_content' );
			}
		}, PHP_INT_MAX );

		if ( self::is_light() ) {
			/* Enqueue scripts needed in frontend */
			add_action( 'wp_enqueue_scripts', 'tve_frontend_enqueue_scripts', 9 );

			/* load custom css for custom post types edited with architect */
			add_action( 'wp_head', 'tve_load_custom_css', 100, 0 );
		}
	}

	private function filters() {
		/* Include what post types should be editable with Theme Architect  */
		add_filter( 'tcb_post_types', [ $this, 'tcb_post_types' ] );

		add_filter( 'tcb_allow_landing_page_set_data', [ $this, 'tcb_allow_smart_lp_set_data' ], 10, 2 );

		add_filter( 'tcb_get_page_palettes', [ $this, 'tcb_get_smart_lp_palettes' ], 10, 2 );

		add_filter( 'tcb_get_page_variables', [ $this, 'tcb_get_smart_lp_variables' ], 10, 3 );

		/* Filter the layout to be displayed when editing a template with Theme Architect */
		add_filter( 'tcb_custom_post_layouts', [ $this, 'tcb_custom_post_layouts' ], 10, 3 );

		/* Elements to be displayed  */
		add_filter( 'tcb_remove_instances', [ $this, 'tcb_remove_instances' ], 100 );

		add_filter( 'tcb_element_instances', [ $this, 'add_theme_element_instance' ], 10, 2 );

		/* add extra classes for body */
		add_filter( 'body_class', [ $this, 'body_class' ] );

		/* add extra classes for body */
		add_filter( 'body_class', [ $this, 'body_class' ] );

		add_filter( 'tcb_hide_post_list_element', [ $this, 'tcb_hide_post_list_element' ] );

		/* parse inner frame uri */
		add_filter( 'tcb_frame_request_uri', [ $this, 'tcb_frame_request_uri' ] );

		add_filter( 'tcb_main_frame_localize', [ $this, 'tcb_main_frame_localize' ] );

		add_filter( 'tve_main_js_dependencies', [ $this, 'tve_main_js_dependencies' ] );

		add_filter( 'tcb_backbone_templates', [ $this, 'tcb_backbone_templates' ] );

		add_filter( 'tcb_divider_prefix', [ $this, 'tcb_divider_prefix' ], 10, 2 );

		add_filter( 'tcb_overwrite_scripts_enqueue', '__return_true' );

		add_filter( 'tcb_categories_order', [ $this, 'tcb_categories_order' ] );

		add_filter( 'preview_post_link', [ $this, 'tcb_frame_request_uri' ] );

		add_filter( 'thrive_post_attributes', [ $this, 'thrive_post_attributes' ], 10, 2 );

		add_filter( 'post_class', [ $this, 'post_class' ] );

		add_filter( 'tcb_close_url', [ $this, 'tcb_close_url' ] );

		add_filter( 'architect.branding', [ $this, 'architect_branding' ], 10, 2 );

		add_filter( 'tcb_can_use_landing_pages', [ $this, 'can_use_landing_pages' ] );

		add_filter( 'tcb_modal_templates', [ $this, 'tcb_modal_templates' ] );

		add_filter( 'tcb_global_styles_before_save', [ $this, 'assign_global_styles' ], 10, 3 );

		add_filter( 'tcb_global_styles', [ $this, 'tcb_global_styles' ], 10, 2 );

		add_filter( 'tcb_post_list.disable_related', [ 'Thrive_Post_List', 'disable_query_builder_related_posts' ] );

		add_filter( 'tcb_post_list.related_text', [ 'Thrive_Post_List', 'query_builder_related_posts_text' ] );

		add_filter( 'tcb_post_list.show_exclude', [ 'Thrive_Post_List', 'query_builder_show_exclude_current_post' ] );

		add_filter( 'tcb_post_list_pagination_types', [ 'Thrive_Post_List', 'add_pagination_types' ] );

		add_filter( 'tcb_post_list_query_args', [ $this, 'change_featured_list_args' ], 10, 2 );

		add_filter( 'tcb_cloud_templates', [ $this, 'tcb_cloud_templates' ], 10, 2 );

		add_filter( 'tcb_landing_page_templates_list', [ $this, 'tcb_landing_page_templates_list' ], 10, 2 );

		add_filter( 'tcb_cloud_request_params', [ $this, 'tcb_cloud_request_params' ], 10 );

		add_filter( 'tcb_editor_title', [ $this, 'tcb_editor_title' ] );

		add_filter( 'tcb_localize_existing_post_list', [ $this, 'tcb_localize_existing_post_list' ], 10, 2 );

		/* extends the config of TCB_Post_Element from Architect by adding extra components */
		add_filter( 'tcb_post_element_extend_config', [ __CLASS__, 'tcb_post_element_extend_config' ] );

		/* extends the config of TCB_Landing_Page_Element */
		add_filter( 'tcb_lp_element_extend_config', [ __CLASS__, 'tcb_lp_element_extend_config' ] );

		add_filter( 'tcb_allow_central_style_panel', [ $this, 'tcb_skin_allow_central_style_panel' ] );
		add_filter( 'tcb_has_central_style_panel', [ $this, 'tcb_skin_allow_central_style_panel' ] );

		add_filter( 'tcb.template_path', [ $this, 'tcb_change_template_path' ], 10, 4 );

		/* always include theme css */
		add_filter( 'tcb_theme_dependency', '__return_false' );

		if ( static::is_light() ) {
			add_filter( 'tve_landing_page_content', 'tve_editor_content' );

			/* for the case when he only has the theme license */
			add_filter( 'tcb_skip_license_check', '__return_true' );
		}

		add_filter( 'tcb_element_post_config', [ 'Thrive_Utils', 'tcb_element_post_config' ] );

		add_filter( 'tcb_element_footer_config', [ 'Thrive_Utils', 'tcb_element_hf_config' ] );
		add_filter( 'tcb_element_header_config', [ 'Thrive_Utils', 'tcb_element_hf_config' ] );

		add_filter( 'tcb_editor_javascript_params', [ $this, 'tcb_editor_localize' ] );

		add_filter( 'tcb_js_translate', [ $this, 'tcb_js_translate' ] );

		add_filter( 'tcb_remove_theme_css', [ $this, 'tcb_remove_theme_css' ], 10, 2 );

		add_filter( 'tcb_ajax_response_load_content_template', [ $this, 'tcb_ajax_response_load_content_template' ], 10, 2 );

		/* Show TAR button on the post edit page when gutenberg is used */
		add_filter( 'tcb_gutenberg_switch', '__return_false' );

		add_filter( 'tcb_add_post_breadcrumb_option', [ Thrive_Utils::class, 'is_architect_editor' ] );

		add_filter( 'tcb_post_breadcrumb_data', [ $this, 'post_breadcrumb_data' ] );

		add_filter( 'get_search_query', [ $this, 'alter_search_element_query_string' ] );

		if ( Thrive_Utils::is_theme_template() ) {
			/**
			 * populate sections when editing a theme template
			 */
			add_filter( 'tcb_lazy_load_data', [ $this, 'add_lazy_load_data' ], 10, 2 );
		}

		add_filter( 'tcb_post_element_name', [ $this, 'post_element_name' ] );

		/**
		 * Modify default style provider - TTB overwrites TAr's style provider
		 */
		add_filter( 'tcb_default_style_provider_class', function () {
			require_once ARCHITECT_INTEGRATION_PATH . '/classes/class-thrive-theme-style-provider.php';

			return Thrive_Theme_Style_Provider::class;
		} );

		/**
		 * Adds the theme block set to the blocks cloud call if the requirements are met
		 */
		add_filter( 'tcb_get_special_blocks_set', static function ( $special_set = '' ) {
			$post_id = get_the_ID();

			if ( wp_doing_ajax() && empty( $post_id ) && isset( $_POST['post_id'] ) ) {
				$post_id = $_POST['post_id'];
			}

			$landing_page = tve_post_is_landing_page( $post_id );

			if ( $landing_page === false || $landing_page === 'blank_v2' || strtolower( $special_set ) === 'blank' ) {
				$skin_tag    = thrive_skin()->get_meta( Thrive_Skin::TAG );
				$result      = explode( '_', $skin_tag );
				$special_set = $result[0];
			}

			return $special_set;
		} );

		/**
		 * Adds extra functionality to page wizard when TTB is active
		 */
		add_filter( 'tcb_get_page_wizard_items', [ $this, 'tcb_add_page_wizard_items' ] );

		/**
		 * Added the ttb_skin param to the request that fetches the Cloud Template List.
		 *
		 * For the theme, the cloud templates are skin based
		 */
		add_filter( 'tcb_get_cloud_templates_default_args', static function ( $args = [] ) {

			$args['ttb_skin'] = thrive_skin()->get_tag();

			return $args;
		} );

		/**
		 * When editing theme typography, default styles should be output in a reachable style node
		 */
		add_filter( 'tcb_output_default_styles', static function ( $output_styles ) {
			if ( Thrive_Utils::is_theme_typography() ) {
				$output_styles = false;
			}

			return $output_styles;
		} );

		/**
		 * Hook into the page fonts area and include fonts used in the current template
		 */
		add_filter( 'tcb_css_imports', static function ( $imports ) {
			/* current template + all sections and header / footer */
			return array_merge( $imports, thrive_template()->get_css_imports() );
		} );

		/**
		 * Print default Theme styles in landing pages if the "disable theme CSS" option has NOT been selected
		 */
		add_filter( 'tcb_should_print_unified_styles', static function ( $print ) {
			if ( is_singular() && tve_post_is_landing_page() && ! tcb_landing_page( get_the_ID() )->should_remove_theme_css() ) {
				$print = true;
			}

			if ( Thrive_Utils::is_theme_typography() ) {
				$print = false;
			}

			return $print;
		} );

		/**
		 * All editing landing pages for all custom posts
		 */
		add_filter( 'tcb_allow_landing_page_edit', static function () {
			return is_singular();
		} );

		/**
		 * Makes sure that when TTB is active, there are no left-over cached transients from TAr for landing pages
		 */
		add_filter( 'tve_cloud_templates_transient_name', static function ( $transient_name ) {
			return $transient_name . '_ttb_' . thrive_skin()->ID;
		} );

		/* while in editor, don't let thrive leads shortcodes render */
		add_filter( 'tve_leads_allow_shortcodes', static function () {
			return ! is_editor_page_raw( true );
		} );
	}

	/**
	 * Fired when variables are being fetched for a smart landing page
	 *
	 * For a landing page associated with the theme, the variables must come from the theme itself
	 *
	 * @param array            $page_variables
	 * @param TCB_Landing_Page $landing_page
	 * @param string           $key
	 *
	 * @return array
	 */
	public function tcb_get_smart_lp_variables( $page_variables = [], $landing_page = null, $key = '' ) {
		if ( ! empty( $landing_page ) && ! empty( $landing_page->meta( 'theme_skin_tag' ) ) ) {
			$skin_variables = thrive_skin()->get_variables();
			if ( ! empty( $skin_variables ) ) {
				if ( 'colours' === $key ) {
					$key = 'colors';
				}
				$page_variables = $skin_variables[ $key ];
			}
		}

		return $page_variables;
	}

	/**
	 * Fired when palettes are being fetched for a smart landing page
	 *
	 * For a landing page associated with the theme, the palettes must come from the theme itself
	 *
	 * @param array            $page_palettes
	 * @param TCB_Landing_Page $landing_page
	 *
	 * @return array
	 */
	public function tcb_get_smart_lp_palettes( $page_palettes = [], $landing_page = null ) {
		if ( ! empty( $landing_page ) && ! empty( $landing_page->meta( 'theme_skin_tag' ) ) ) {
			$page_palettes = thrive_skin()->get_palettes();
		}

		return $page_palettes;
	}

	/**
	 * Fired when Landing Page set data is being set.
	 *
	 * For Landing Pages associated with the theme, the set data must come from the theme not form the page itself
	 *
	 * @param bool             $return
	 * @param TCB_Landing_Page $landing_page
	 *
	 * @return bool
	 */
	public function tcb_allow_smart_lp_set_data( $return = true, $landing_page = null ) {
		if ( ! empty( $landing_page ) && ! empty( $landing_page->meta( 'theme_skin_tag' ) ) ) {
			$return = false;
		}

		return $return;
	}

	/**
	 * Fired when a landing page is set from cloud
	 *
	 * If a landing page is associated to a skin, it sets the skin tag inside post meta
	 *
	 * @param TCB_Landing_Page $tcb_landing_page
	 * @param array            $config
	 */
	public function theme_set_cloud_landing_page( $tcb_landing_page, $config ) {

		$tcb_landing_page->meta_delete( 'theme_skin_tag' );

		if ( ! empty( $config['skin_tag'] ) ) {

			$tcb_landing_page->meta( 'theme_skin_tag', $config['skin_tag'] );
			$page_vars     = empty( $config['page_vars'] ) ? [] : $config['page_vars'];
			$page_palettes = empty( $config['page_palettes'] ) ? [] : $config['page_palettes'];

			if ( ! empty( $config['silo'] ) ) {
				$tcb_landing_page->meta( '_tve_header', thrive_skin()->get_default_data( THRIVE_HEADER_SECTION ) );
				$tcb_landing_page->meta( '_tve_footer', thrive_skin()->get_default_data( THRIVE_FOOTER_SECTION ) );
			}

			/**
			 * Updates the missing skin variables & palette items for pages that comes from cloud
			 */
			thrive_skin()->update_missing_skin_variables_from_cloud( $page_vars, $page_palettes );
		}
	}

	/**
	 * Add a theme instance to the instances from TAR
	 *
	 * @param $instances
	 * @param $element_type
	 *
	 * @return mixed
	 */
	public function add_theme_element_instance( $instances, $element_type ) {

		if ( ! empty( $element_type ) && empty( $this->theme_elements[ $element_type ] ) ) {
			$this->set_theme_element( $element_type );
		}

		if ( ! empty( $this->theme_elements[ $element_type ] ) ) {
			$instances[ $element_type ] = $this->theme_elements[ $element_type ];
		}

		$this->overwrite_elements( $instances );

		return $instances;
	}

	/**
	 * Overwrite elements instances if they are available
	 *
	 * @param $instances
	 */
	private function overwrite_elements( &$instances ) {
		if ( ! is_dir( ARCHITECT_INTEGRATION_PATH . '/classes/overrides' ) ) {
			return;
		}

		$overridden_elements = static::get_architect_theme_elements( ARCHITECT_INTEGRATION_PATH . '/classes/overrides' );

		/**
		 * @var TCB_Element_Abstract $overridden_elem
		 */
		foreach ( $overridden_elements as $overridden_elem ) {
			if ( $overridden_elem->is_available() ) {
				$instances[ $overridden_elem->tag() ] = $overridden_elem;
			}
		}
	}

	/**
	 * Register post types used in theme
	 */
	private function register_post_types() {
		Thrive_Template::register_post_type();
		Thrive_Section::register_post_type();
		Thrive_Layout::register_post_type();
		Thrive_Typography::register_post_type();
	}

	/**
	 * Actions done after the theme has been loaded. We load some elements only after just to make sure the dependencies are loaded
	 */
	public function after_setup_theme() {
		require_once ARCHITECT_INTEGRATION_PATH . '/classes/class-thrive-post-list.php';
		require_once ARCHITECT_INTEGRATION_PATH . '/classes/class-tcb-pagination-infinite-scroll.php';

		add_action( 'pre_get_posts', [ 'Thrive_Post_List', 'blog_pre_get_posts' ], 10, 1 );
	}

	/**
	 * Depending on the template we're editing, load the layout for Architect
	 *
	 * @param $layouts
	 * @param $post_id
	 * @param $post_type
	 *
	 * @return mixed
	 */
	public function tcb_custom_post_layouts( $layouts, $post_id, $post_type ) {

		switch ( $post_type ) {
			case THRIVE_TEMPLATE:
				$layout = thrive_template()->editor_layout();
				break;

			case THRIVE_TYPOGRAPHY:
				$layout = thrive_typography()->prepare_layout();
				break;

			default:
				$layout = null;
		}

		if ( $layout ) {
			/* added here to prevent google indexing */
			if ( ! is_user_logged_in() ) {
				wp_redirect( home_url() );
				exit();
			}

			$layouts['template'] = $layout;
		}


		return $layouts;
	}

	/**
	 * Set Architect to edit the theme page templates
	 *
	 * @param $post_types
	 *
	 * @return mixed
	 */
	public function tcb_post_types( $post_types ) {

		if ( static::is_light() ) {
			if ( ! isset( $post_types['force_whitelist'] ) ) {
				$post_types['force_whitelist'] = [];
			}

			$post_types['force_whitelist'] = array_merge(
				$post_types['force_whitelist'],
				[
					Thrive_Demo_Content::POST_TYPE,
					Thrive_Demo_Content::PAGE_TYPE,
					THRIVE_TYPOGRAPHY,
					THRIVE_TEMPLATE,
				],
				array_keys( Thrive_Utils::get_content_types() )
			);

			/* we do this in order for tve_is_post_type_editable() to return true (post_type of 404 is null) */
			if ( thrive_template()->is404() ) {
				$post_types['force_whitelist'][] = null;
			}
		}

		return $post_types;
	}

	/**
	 * Depending on the template we're editing, load the elements inside Architect
	 *
	 * @param $used_elements
	 *
	 * @return array
	 */
	public function tcb_remove_instances( $used_elements ) {
		$elements_to_hide = [];

		/* first, add the elements used by other elements (example: post list, pagination button ) */
		$inherited_theme_elements = static::get_architect_theme_elements( ARCHITECT_INTEGRATION_PATH . '/classes/inherited-elements' );

		$only_theme_elements = static::get_architect_theme_elements();
		$only_theme_elements = array_merge( $only_theme_elements, $inherited_theme_elements );

		if ( Thrive_Utils::allow_theme_scripts() ) {
			/* these are replaced by theme elements (more tag is no longer needed because we have a read more element) */
			$elements_to_hide = [ 'moretag', 'postgrid' ];

			/* display only the elements needed for the template editing */
			$used_elements = array_merge( $used_elements, $only_theme_elements );

			if ( thrive_template()->is_singular() ) {
				unset( $used_elements['blog_list'] );
			}
		}

		/* if the user doesn't have full TAR, add some elements to the list of elements to hide */
		if ( self::is_light() ) {
			$elements_to_hide = array_merge( $elements_to_hide, Thrive_Defaults::unavailable_elements() );
		}

		foreach ( $elements_to_hide as $tag ) {
			unset( $used_elements[ $tag ] );
		}

		$this->theme_elements = $used_elements;

		$used_elements = Thrive_Typography::tcb_remove_instances( $used_elements );

		return $used_elements;
	}

	/**
	 * Set theme element instance
	 *
	 * @param      $element_type
	 * @param null $path
	 *
	 */
	public function set_theme_element( $element_type, $path = null ) {

		$root_path = ARCHITECT_INTEGRATION_PATH . '/classes/elements';
		$path      = ( null === $path ) ? $root_path : $path;
		$items     = array_diff( scandir( $path ), [ '.', '..' ] );

		$file_name = 'class-' . str_replace( '_', '-', $element_type ) . '-element.php';

		foreach ( $items as $item ) {
			$item_path = $path . '/' . $item;
			if ( is_dir( $item_path ) ) {
				$this->set_theme_element( $element_type, $item_path );
			}

			/* if the item is what we are searching for and it's a file, include it */
			if ( $item === $file_name && is_file( $item_path ) ) {
				$element = require_once $item_path;

				$this->theme_elements[ $element->tag() ] = $element;
			}
		}
	}

	/**
	 * Enqueue editor script
	 */
	public function editor_enqueue_scripts() {
		if ( Thrive_Utils::is_inner_frame() ) {
			/* template editor js and css */
			tve_dash_enqueue_script( 'thrive-theme-editor', THEME_ASSETS_URL . '/editor.min.js', [ 'jquery', 'underscore' ], THEME_VERSION );
			wp_enqueue_style( 'thrive-theme-editor-styles', THEME_ASSETS_URL . '/editor.css', [], THEME_VERSION );

			wp_localize_script( 'thrive-theme-editor', 'thrive_page_params', apply_filters( 'theme_editor_page_params_localize', $this->localization_params() ) );
		}
	}

	/**
	 * Localize data in the architect editor inner frame.
	 *
	 * @param $data
	 *
	 * @return mixed
	 */
	public function tcb_editor_localize( $data ) {
		/* Only localize this data if we're on a TAr post or page */
		if ( Thrive_Utils::is_architect_editor() ) {
			$data['theme'] = [
				'template_data' => [
					'url'  => tcb_get_editor_url( thrive_template()->ID ),
					'name' => thrive_template()->title(),
				],
			];

			/* Get template section visibility info. We can't do this in the main frame because we don't have the template data there yet. */
			$template_visibility = [];

			foreach ( Thrive_Post::get_visibility_config( 'sections' ) as $type => $config ) {
				/* calculate the 'real' visibility value for the template ( without checking page flags, etc ) */
				$template_visibility[ $type ] = Thrive_Utils::get_template_visibility( $type ) ? 'show' : 'hide';
			}

			/* add the template section visibility data to the inner frame localize */
			$data['theme']['template_visibility'] = $template_visibility;
		}

		return $data;
	}

	/**
	 * Main frame localize parameters
	 *
	 * @param $data
	 *
	 * @return mixed
	 */
	public function tcb_main_frame_localize( $data ) {
		$template = thrive_template();
		$skin     = thrive_skin();

		$data['theme'] = [
			'sidebars'              => Thrive_Utils::get_sidebars(),
			'demo_content_url'      => Thrive_Demo_Content::url(),
			'demo_content_preview'  => Thrive_Demo_Content::url( true ),
			'routes'                => Thrive_Utils::get_rest_routes(),
			'element_selectors'     => $this->get_architect_elements_selector(),
			'template'              => $template->export(),
			'layouts'               => $skin->get_layouts( 'array', [ 'sidebar_on_left', 'hide_sidebar', 'content_width' ] ),
			'post_types'            => [
				'all' => array_merge( Thrive_Utils::get_post_types(), [ 'attachment' => 'Media' ] ),
			],
			'comments_form'         => [
				'error_defaults' => thrive_theme_comments::get_comment_form_error_labels(),
			],
			'is_theme_template'     => Thrive_Utils::is_theme_template(),
			'content_switch'        => thrive_content_switch()->get_localized_data(),
			'taxonomies'            => get_object_taxonomies( $template->meta( THRIVE_SECONDARY_TEMPLATE ), 'object' ),
			'dynamic_list_types'    => Thrive_Dynamic_Styled_List_Element::get_list_type_options(),
			'skin_id'               => $skin->ID,
			'skin_tag'              => $skin->get_tag(),
			'skin_styles'           => $skin->get_global_styles(),
			'logo_url'              => Thrive_Branding::get_logo_url( site_url() ),
			'templates_layouts_map' => $skin->get_layouts_templates_map(),
			'breadcrumbs_labels'    => $skin->get_breadcrumbs_labels(),
		];

		if ( ! empty( $data['landing_page'] ) && ! empty( tcb_landing_page( $data['post']->ID )->meta( 'theme_skin_tag' ) ) ) {
			$skin_variables = $skin->get_variables();

			$data['colors']['lp_set_prefix'] = THEME_SKIN_COLOR_VARIABLE_PREFIX;
			$data['colors']['templates']     = empty( $skin_variables['colors'] ) ? [] : $skin_variables['colors'];
			$data['gradients']['templates']  = empty( $skin_variables['gradients'] ) ? [] : $skin_variables['gradients'];
			$data['template_palettes']       = $skin->get_palettes();
			$data['external_palettes']       = 1;
		} elseif ( ! empty( $data['theme']['is_theme_template'] ) ) {
			$data['external_palettes'] = 1;
		}

		/**
		 * Include The skin Variables only for the end user. So not for Theme Builder Site
		 */
		if ( Thrive_Utils::is_end_user_site() ) {
			$data['theme'] = array_merge( $data['theme'], [
				'skin_palettes'  => $skin->get_palettes(),
				'skin_variables' => $skin->get_variables( true ),
			] );
		}

		/* only localize this data if we're on a TAr post or page */
		if ( Thrive_Utils::is_architect_editor() ) {
			$thrive_post = thrive_post();

			$data['theme'] = array_merge( $data['theme'], [
				'element_visibility'        => [
					'config' => Thrive_Post::get_visibility_config(),
					'values' => $thrive_post->localize_visibility_meta(),
				],
				'amp_status'                => thrive_post()->is_amp_disabled() ? 'disabled' : '',
				/* add a list of template IDs, names, and other information needed in JS for templates */
				'templates'                 => $thrive_post->get_all_templates(),
				'post_format_options_video' => thrive_video_post_format( Thrive_Video_Post_Format_Main::get_type() )->get_video_options(),
				'post_format_options_audio' => thrive_audio_post_format( Thrive_Audio_Post_Format_Main::get_type() )->get_audio_options(),
				'post_featured_image'       => thrive_image_post_format()->get_image(),
				'post_formats'              => array_merge( [ THRIVE_STANDARD_POST_FORMAT ], Thrive_Theme::post_formats() ),
				'scripts'                   => thrive_scripts()->get_all(),
			] );
		}

		if ( ! empty( $data['landing_page'] ) || get_post_type() === 'page' ) {
			/* localize a default header and footer to use in a landing page */
			$data['default_header_id'] = thrive_skin()->get_default_data( 'header' );
			$data['default_footer_id'] = thrive_skin()->get_default_data( 'footer' );
		}

		return $data;
	}

	/**
	 * Load scripts for main frame
	 */
	public function tcb_main_frame_enqueue() {
		if ( Thrive_Utils::allow_theme_scripts() ) {
			tve_dash_enqueue_script( 'thrive-theme-main', THEME_ASSETS_URL . '/main.min.js', [ 'jquery', 'underscore' ], THEME_VERSION );
		}

		if ( Thrive_Utils::is_theme_template() ) {
			wp_enqueue_style( 'thrive-theme-main', THEME_ASSETS_URL . '/main-frame.css', [], THEME_VERSION );
		}

		if ( Thrive_Utils::is_theme_typography() ) {
			tve_dash_enqueue_script( 'thrive-theme-typography', THEME_ASSETS_URL . '/typography.min.js', [ 'jquery', 'underscore' ], THEME_VERSION );
		}

		/* enqueue this JS & CSS only on architect posts and pages */
		if ( Thrive_Utils::is_architect_editor() ) {
			tve_dash_enqueue_script( 'thrive-theme-tar-editor', THEME_ASSETS_URL . '/tar-editor.min.js', [ 'jquery', 'underscore' ], THEME_VERSION );

			/* some main frame CSS only for the architect editor */
			wp_enqueue_style( 'thrive-editor-tar-main-frame', THEME_ASSETS_URL . '/editor-main-frame.css', [], THEME_VERSION );
		}
	}

	/**
	 * Set the theme js file a dependency for the main file so it will load before
	 *
	 * @param $dependencies
	 *
	 * @return array
	 */
	public function tve_main_js_dependencies( $dependencies ) {
		if ( Thrive_Utils::allow_theme_scripts() ) {
			$dependencies[] = 'thrive-theme-main';
		}

		if ( Thrive_Utils::is_theme_typography() ) {
			$dependencies[] = 'thrive-theme-typography';
		}

		if ( Thrive_Utils::is_architect_editor() ) {
			$dependencies[] = 'thrive-theme-tar-editor';
		}

		return $dependencies;
	}

	/**
	 * Params needed in the editor js
	 *
	 * @return array
	 */
	private function localization_params() {
		global $post;

		return [
			'ID'                   => get_the_ID(),
			'query_vars'           => Thrive_Utils::get_query_vars(),
			'body_class'           => thrive_template()->body_class( false, 'string', true ),
			'posts'                => [],
			'post_image'           => [
				'featured' => THRIVE_FEATURED_IMAGE_PLACEHOLDER,
				'author'   => THRIVE_AUTHOR_IMAGE_PLACEHOLDER,
			],
			'featured_image_sizes' => Thrive_Featured_Image::get_image_sizes( get_option( THRIVE_FEATURED_IMAGE_OPTION ) ),
			'default_sizes'        => array_keys( Thrive_Featured_Image::filter_available_sizes() ),
			'social_urls'          => null === $post ? '' : get_the_author_meta( THRIVE_SOCIAL_OPTION_NAME, $post->post_author ),
			'comments'             => Thrive_Theme_Comments::get_comments_meta(),
			'is_demo_content'      => Thrive_Demo_Content::on_demo_content_page(),
			'archive_description'  => thrive_template()->is_archive() ? Thrive_Shortcodes::taxonomy_term_description() : '',
			'taxonomy'             => thrive_template()->is_archive() ? [
				'thrive_archive_name'        => Thrive_Shortcodes::archive_name(),
				'thrive_archive_description' => Thrive_Shortcodes::archive_description(),
				'thrive_archive_parent_name' => Thrive_Shortcodes::archive_parent_name(),
			] : [],
		];
	}

	/**
	 * For thrive templates, add a specific body class so we can better handle css
	 *
	 * @param $classes
	 *
	 * @return array
	 */
	public function body_class( $classes ) {

		if ( Thrive_Utils::is_inner_frame() ) {
			$classes[] = 'tve_editor_page';
		}

		return $classes;
	}

	/**
	 * Register REST Routes for Architect
	 */
	public function rest_api_init() {
		$dir = ARCHITECT_INTEGRATION_PATH . '/classes/endpoints/';
		foreach ( scandir( $dir ) as $file ) {
			if ( in_array( $file, [ '.', '..' ] ) ) {
				continue;
			}

			include $dir . $file;
		}

		Thrive_Typography::rest_api_init();

		Thrive_Demo_Content::init( true );
	}

	/**
	 * Parse tcb inner frame url. Add Theme Editor Flag with the template id
	 *
	 * @param $uri
	 *
	 * @return string
	 */
	public function tcb_frame_request_uri( $uri ) {

		if ( ! Thrive_Utils::is_theme_template() ) {
			return $uri;
		}

		$new = thrive_template()->url( true );

		if ( empty( $new ) ) {
			return $uri;
		}

		$args = [
			TVE_EDITOR_FLAG   => 'true',
			THRIVE_THEME_FLAG => thrive_template()->ID,
		];

		/* add an extra param for the preview link, so we will know how to display stuff */
		if ( doing_filter( 'preview_post_link' ) ) {
			$args[ THRIVE_PREVIEW_FLAG ] = 'true';
			unset( $args[ TVE_EDITOR_FLAG ] );
		}

		$new = add_query_arg( $args, $new );

		return $new;
	}

	/**
	 * When we're in the inner frame, we let Architect know that he has the power
	 */
	public function parse_request() {
		if ( Thrive_Utils::is_inner_frame() ) {
			add_filter( 'tcb_is_inner_frame_override', [ $this, 'tcb_is_inner_frame_override' ] );
			add_filter( 'tcb_is_editor_page', [ $this, 'tcb_is_inner_frame_override' ] );
		}
	}

	/**
	 * While doing the content filter, we tell Architect that he's not in editor mode
	 *
	 * @return bool
	 */
	public function tcb_is_inner_frame_override() {
		return ! doing_filter( 'the_content' );
	}

	/**
	 * Get all elements used by architect in the theme
	 *
	 * @param null $path
	 *
	 * @return array
	 */
	public static function get_architect_theme_elements( $path = null ) {
		$root_path = ARCHITECT_INTEGRATION_PATH . '/classes/elements';

		/* if there's no recursion, use the root path */
		$path = ( $path === null ) ? $root_path : $path;

		$items    = array_diff( scandir( $path ), [ '.', '..' ] );
		$elements = [];

		require_once ARCHITECT_INTEGRATION_PATH . '/classes/class-thrive-theme-element-abstract.php';

		foreach ( $items as $item ) {
			$item_path = $path . '/' . $item;
			/* if the item is a folder, enter it and do recursion */
			if ( is_dir( $item_path ) ) {
				$elements = array_merge( $elements, static::get_architect_theme_elements( $item_path ) );
			}

			/* if the item is a file, include it */
			if ( is_file( $item_path ) ) {
				$element = include $item_path;

				if ( ! empty( $element ) ) {
					$elements[ $element->tag() ] = $element;
				}
			}
		}

		return $elements;
	}

	/**
	 * Selector for each element that we'll use in the theme
	 *
	 * @return array
	 */
	public function get_architect_elements_selector() {
		$selectors = [];

		foreach ( $this->theme_elements as $element ) {
			$identifier = $element->identifier();

			if ( ! empty( $identifier ) ) {
				$selectors[ $element->tag() ] = $identifier;
			}
		}

		return $selectors;
	}

	/**
	 * Load our custom components
	 */
	public function tcb_output_components() {
		$files = [];

		/* load these components only on theme templates */
		if ( Thrive_Utils::allow_theme_scripts() ) {
			$path  = ARCHITECT_INTEGRATION_PATH . '/views/components/theme/';
			$files += array_diff( scandir( $path ), [ '.', '..' ] );
		}

		/* only load these components on TAr posts or pages */
		if ( Thrive_Utils::is_architect_editor() ) {
			$path  = ARCHITECT_INTEGRATION_PATH . '/views/components/editor/';
			$files += array_diff( scandir( $path ), [ '.', '..' ] );
		}

		/* include all the files we collected */
		foreach ( $files as $file ) {
			include $path . $file;
		}
	}

	/**
	 * Include theme architect backbone templates
	 *
	 * @param $templates
	 *
	 * @return array
	 */
	public function tcb_backbone_templates( $templates ) {
		$theme_templates = tve_dash_get_backbone_templates( ARCHITECT_INTEGRATION_PATH . '/views/backbone/theme-main', 'backbone' );

		/* add these templates only in the architect editor */
		if ( Thrive_Utils::is_architect_editor() ) {
			$architect_templates = tve_dash_get_backbone_templates( ARCHITECT_INTEGRATION_PATH . '/views/backbone/architect-main' );

			$theme_templates = array_merge( $architect_templates, $theme_templates );
		}

		return array_merge( $theme_templates, $templates );
	}

	/**
	 * Specify the themes divider prefix
	 *
	 * @param $prefix
	 *
	 * @return string
	 */
	public function tcb_divider_prefix( $prefix ) {

		if ( Thrive_Utils::is_theme_template() ) {
			$prefix = '.thrv-divider';
		}

		return $prefix;
	}

	/**
	 * Add some backbone templates for the editor.
	 */
	public function wp_print_footer_scripts() {
		if ( Thrive_Utils::is_inner_frame() ) {
			$templates = tve_dash_get_backbone_templates( ARCHITECT_INTEGRATION_PATH . '/views/backbone/theme-editor', 'theme-editor' );
			tve_dash_output_backbone_templates( $templates, 'tve-theme-' );
		}
	}

	/**
	 * Add Article Section order inside the elements sidebar
	 *
	 * @param $order
	 *
	 * @return mixed
	 */
	public function tcb_categories_order( $order ) {
		$order[4] = Thrive_Defaults::theme_group_label();

		return $order;
	}

	public function tcb_editor_enqueue_scripts() {
		if ( isset( $_GET[ THRIVE_PREVIEW_FLAG ] ) ) {
			wp_dequeue_style( 'tve_inner_style' );
			wp_dequeue_style( 'tve_editor_style' );
		}
	}

	/**
	 * Add custom class to article wrapper
	 *
	 * @param array $post_class
	 *
	 * @return array
	 */
	public function post_class( $post_class = [] ) {

		$post_class[] = THRIVE_POST_WRAPPER_CLASS;
		$post_class[] = THRIVE_WRAPPER_CLASS;

		return $post_class;
	}

	/**
	 * Exit url for theme builder
	 *
	 * @param $url
	 *
	 * @return string
	 */
	public function tcb_close_url( $url ) {
		if ( Thrive_Utils::is_theme_template() ) {
			$url = thrive_template()->url();
		}

		return $url;
	}

	/**
	 * Add the SVGs for the editor.
	 */
	public function tcb_add_extra_svg_in_iframe() {
		include THEME_PATH . '/inc/assets/svg/iframe.svg';
	}

	/**
	 * Add the SVGs for the editor.
	 */
	public function tcb_add_editor_svgs() {
		include THEME_PATH . '/inc/assets/svg/editor.svg';
	}

	/**
	 * Add attributes to the post wrapper
	 *
	 * @param array   $attributes
	 * @param WP_Post $post
	 *
	 * @return mixed
	 */
	public static function thrive_post_attributes( $attributes, $post ) {
		if ( Thrive_Utils::is_inner_frame() ) {
			$attributes['data-id'] = $post->ID;
		}

		return $attributes;
	}

	/**
	 * Set branding elements for the theme
	 *
	 * @param $string
	 * @param $type
	 *
	 * @return string
	 */
	public function architect_branding( $string, $type = 'text' ) {
		if ( Thrive_Utils::is_theme_template() ) {
			switch ( $type ) {
				case 'text':
					$string = 'Thrive Theme Builder';
					break;
				case 'logo_src':
					$string = THEME_URL . '/inc/assets/images/theme-logo.png';
					break;
				default:
					break;
			}
		}

		return $string;
	}

	/**
	 * Disable landing page options for architect light.
	 *
	 * @param $allow
	 *
	 * @return mixed
	 */
	public function can_use_landing_pages( $allow ) {

		if ( Thrive_Utils::is_theme_template() || Thrive_Utils::is_theme_typography() ) {
			$allow = false;
		}

		return $allow;
	}

	/**
	 * Include theme modals inside architect
	 *
	 * @param $files
	 *
	 * @return mixed
	 */
	public function tcb_modal_templates( $files ) {

		$path   = ARCHITECT_INTEGRATION_PATH . '/views/modals/';
		$modals = array_diff( scandir( $path ), [ '.', '..' ] );

		foreach ( $modals as $key => $file ) {
			$files[] = $path . $file;
		}

		return $files;
	}

	/**
	 * Include extra links in the editor's right sidebar
	 * Allow this only on the theme templates or in the allowed theme post types
	 */
	public function add_extra_links() {
		if ( Thrive_Utils::is_theme_template() || Thrive_Utils::is_allowed_post_type( get_the_ID() ) ) {
			include THEME_PATH . '/inc/templates/parts/extra-links.php';
		}
	}

	/**
	 * Include extra content in the editor's left sidebar
	 * Allow this only on the theme templates or in the allowed theme post types
	 */
	public function add_top_content() {
		if ( Thrive_Utils::is_theme_template() || Thrive_Utils::is_allowed_post_type( get_the_ID() ) ) {
			include THEME_PATH . '/inc/templates/parts/top-content.php';
		}
	}

	/**
	 * Include TAR light notice for elements
	 */
	public function add_tar_light_notice() {
		if ( self::is_light() && ! Thrive_Utils::is_theme_template() ) {
			include THEME_PATH . '/inc/templates/parts/tar-light-notice.php';
		}
	}

	/**
	 * Don't hide the post list element if TTB is active.
	 *
	 * @param $hide
	 *
	 * @return bool
	 */
	public function tcb_hide_post_list_element( $hide ) {
		return $hide && ! Thrive_Utils::is_theme_template();
	}

	/**
	 * Assign a global style to a specific skin
	 *
	 * @param $global_styles
	 * @param $request
	 *
	 * @return mixed
	 */
	public function assign_global_styles( $global_styles, $is_create, $request ) {
		$identifier = $request['identifier'];

		/* If we are sending a skin tag and we are creating the style we assign it also to a skin */
		if ( ! empty( $request['skin_tag'] ) && $is_create ) {
			$global_styles[ $identifier ]['skin_tag'] = thrive_skin()->get_tag();
		}

		return $global_styles;
	}

	/**
	 * We don't need the skin styles in the TAR localization array. We will take those separately
	 *
	 * @param $items
	 *
	 * @return array
	 */
	public function tcb_global_styles( $items ) {

		$items = empty( $items ) ? [] : $items;

		$items = array_filter( $items, static function ( $value ) {
			return empty( $value['skin_tag'] );
		} );

		return $items;
	}

	/**
	 * Reorder template from cloud based on the ones are from a theme skin
	 *
	 * @param $templates
	 *
	 * @return mixed
	 */
	public function tcb_cloud_templates( $templates, $type ) {
		/* Content blocks / headers / footers have a different template structure, so they shouldn't be affected by skin logic*/
		if ( ! in_array( $type, [ 'contentblock', THRIVE_HEADER_SECTION, THRIVE_FOOTER_SECTION ] ) ) {
			$templates = thrive_skin()->filter_templates( $templates, $type );
		}

		return $templates;
	}

	/**
	 * Handle tar light landing pages lp_templates
	 *
	 * @param $lp_templates
	 *
	 * @return array
	 */
	public function tcb_landing_page_templates_list( $lp_templates ) {
		return thrive_skin()->filter_landing_pages( $lp_templates );
	}

	/**
	 * If TAR is inactive we need to download the template based on the active skin tag
	 *
	 * @return mixed
	 */
	public function tcb_cloud_request_params( $params ) {

		$params['ttb_skin'] = thrive_skin()->get_tag();

		return $params;
	}

	/**
	 * Display custom title based on what we're editing
	 *
	 * @param $title
	 *
	 * @return string
	 */
	public function tcb_editor_title( $title ) {

		switch ( get_post_type() ) {
			case THRIVE_TEMPLATE:
				$title = 'Thrive Theme Builder';
				break;
			case THRIVE_TYPOGRAPHY:
				$title = 'Typography';
				break;
		}

		return $title;
	}

	/**
	 * Hook for the architect post save - save the element visibility data or the theme typography.
	 *
	 * @param $post_id
	 * @param $post_request_data
	 */
	public function tcb_ajax_save_post( $post_id, $post_request_data ) {
		$post = new Thrive_Post( $post_id );

		if ( isset( $post_request_data['element_visibility'] ) ) {
			$post->set_visibility_meta( $post_request_data['element_visibility'] );
		} elseif ( isset( $post_request_data['theme_typography_style'] ) ) {
			foreach ( $post_request_data['theme_typography_style'] as $type => & $json_data ) {
				$json_data = json_decode( stripslashes( $json_data ), true );
			}
			thrive_typography( $post_id )->set_style( $post_request_data['theme_typography_style'] );
		}

		if ( isset( $post_request_data[ THRIVE_META_POST_AMP_STATUS ] ) ) {
			if ( empty( $post_request_data[ THRIVE_META_POST_AMP_STATUS ] ) ) {
				$post->delete_meta( THRIVE_META_POST_AMP_STATUS );
			} else {
				$post->set_meta( THRIVE_META_POST_AMP_STATUS, $post_request_data[ THRIVE_META_POST_AMP_STATUS ] );
			}
		}

		if ( isset( $post_request_data['tve_video_attributes']['type'] ) ) {
			$type        = $post_request_data['tve_video_attributes']['type'];
			$post_format = thrive_video_post_format( $type, $post_id );

			if ( ! empty( $post_format ) ) {
				$settings = $post_format->process_options( $post_request_data['tve_video_attributes'], $type );
				$post_format->save_options( $settings );
			}
		}

		if ( isset( $post_request_data['tve_audio_attributes']['type'] ) ) {
			$type        = $post_request_data['tve_audio_attributes']['type'];
			$post_format = thrive_audio_post_format( $type, $post_id );

			if ( ! empty( $post_format ) ) {

				$settings = $post_format->process_options( $post_request_data['tve_audio_attributes'], $type );
				$post_format->save_options( $settings );
			}
		}

		if ( isset( $post_request_data['scripts'] ) ) {
			thrive_scripts( $post_id )->save( $post_request_data['scripts'] );
		}
	}

	/**
	 * Action called when deleting a term. If the term is a skin, make sure we delete everything from the skin
	 *
	 * @param $term_id
	 * @param $taxonomy
	 */
	public function pre_delete_term( $term_id, $taxonomy ) {
		if ( $taxonomy === SKIN_TAXONOMY ) {
			$skin = new Thrive_Skin( $term_id );

			$skin->remove();
		}
	}

	/**
	 * When updating css for typography, we also recreate the style file.
	 *
	 * @param $meta_id
	 * @param $object_id
	 * @param $meta_key
	 * @param $meta_value
	 */
	public function updated_postmeta( $meta_id, $object_id, $meta_key, $meta_value ) {

		if ( $meta_key === 'style' && get_post_type( $object_id ) === THRIVE_TYPOGRAPHY ) {
			thrive_skin()->generate_style_file();
		}
	}

	/**
	 * Outputs skin variables
	 */
	public function output_skin_variables() {
		echo thrive_skin()->get_variables_for_css();
	}

	/**
	 * Change tcb template path in some instances
	 *
	 * @param $file_path
	 * @param $file
	 * @param $data
	 * @param $namespace
	 *
	 * @return string
	 */
	public function tcb_change_template_path( $file_path, $file, $data, $namespace ) {

		if ( Thrive_Utils::has_skin_style_panel() && strpos( $file, 'central-style-panel' ) !== false ) {
			$file_path = THEME_PATH . '/inc/templates/parts/theme-style-panel.php';
		}

		if ( strpos( $file, 'custom-scripts' ) !== false && tve_post_is_landing_page( get_the_ID() ) ) {
			$file_path = THEME_PATH . '/inc/templates/parts/lp-custom-scripts.php';
		}

		return $file_path;
	}

	/**
	 * Function that allows the central style panel to be displayed on a content edited with TAR
	 *
	 * @param bool $return
	 *
	 * @return bool
	 */
	public function tcb_skin_allow_central_style_panel( $return = false ) {

		if ( Thrive_Utils::has_skin_style_panel() ) {
			$return = true;
		}

		return $return;
	}

	/**
	 * At page load we require info for the posts from the page. For demo content posts we need a separate query
	 * Use cases for demo content posts:
	 * 1) blog with sample posts
	 * 2) single templates with demo content posts that also contain post lists with normal posts
	 *
	 * @param array $posts
	 * @param array $post_ids
	 *
	 * @return array|int[]|WP_Post[]
	 */
	public function tcb_localize_existing_post_list( $posts = [], $post_ids = [] ) {
		/* if we're localizing demo content posts, those are private so we have to search them in a specific way. */
		$demo_content_posts = get_posts( [
			'posts_per_page' => count( $post_ids ),
			'post__in'       => $post_ids,
			'post_type'      => Thrive_Demo_Content::POST_TYPE,
		] );

		if ( ! empty( $demo_content_posts ) ) {
			$posts = array_merge( $posts, $demo_content_posts );
		}

		return $posts;
	}

	/**
	 * Changes the Query attributes for the Featured List in order to correspond to the ones form blog list
	 *
	 * @param $query
	 * @param $post_list TCB_Post_List
	 *
	 * @return mixed
	 */
	public function change_featured_list_args( $query, $post_list ) {
		/* If we are on an Archive(all except search and date) we should change the query for the Featured List*/
		$template_id = $post_list->get_attr( 'template-id' );

		if ( $post_list->is_featured() && is_numeric( $template_id ) ) {
			$query_vars = Thrive_Utils::get_query_vars();
			$template   = new Thrive_Template( $template_id );

			$query['tax_query'] = [];

			if ( ! empty( $query_vars['post_type'] ) ) {
				$query['post_type'] = $query_vars['post_type'];
			}

			if ( $template->is_archive() ) {

				if ( is_date() ) {
					/*When the Post List is on a date archive page, the Featured List should also be updated*/
					if ( ! empty( $query_vars['year'] ) && ! empty( $query_vars['monthnum'] ) ) {
						global $wp_query;

						$query['year'] = (int) $wp_query->query['year'];

						if ( isset( $wp_query->query['monthnum'] ) && is_numeric( $wp_query->query['monthnum'] ) ) {
							$query['monthnum'] = (int) $wp_query->query['monthnum'];
						}

						if ( isset( $wp_query->query['day'] ) && is_numeric( $wp_query->query['day'] ) ) {
							$query['day'] = (int) $wp_query->query['day'];
						}
					}
				} else if ( isset( $query_vars['rules'] ) ) {
					if ( is_author() ) {
						$query['author__in'] = $query_vars['rules'][0]['terms'];
					} else {
						$query['tax_query'][] = $query_vars['rules'][0];
					}
				}

			}

			if ( $template->is_search() ) {
				/*When the Post List is on a search page, add the serarch term in Featured List also*/
				if ( ! empty( $query_vars['s'] ) ) {
					global $wp_query;

					$query['s'] = $wp_query->query['s'];
				}
			}
		}

		return $query;
	}

	/**
	 * Extend the config of the LP element
	 *
	 * @param $lp_config
	 *
	 * @return array
	 */
	public static function tcb_lp_element_extend_config( $lp_config ) {

		if ( Thrive_Utils::is_architect_editor() ) {

			if ( AMP_Settings::enabled_on_post_type( get_the_ID() ) ) {
				$lp_config = array_merge( $lp_config, static::get_amp_component() );
			}

			$lp_config['scripts_settings'] = [ 'order' => 752 ];
		}

		return $lp_config;
	}

	/**
	 * Add more components to the post element config.
	 *
	 * @param $post_config
	 *
	 * @return mixed
	 */
	public static function tcb_post_element_extend_config( $post_config ) {
		$is_architect_editor = Thrive_Utils::is_architect_editor();

		if ( $is_architect_editor && AMP_Settings::enabled_on_post_type( get_the_ID() ) ) {
			$post_config = array_merge( $post_config, static::get_amp_component() );
		}

		/* don't add anything on landing pages */
		if ( ! thrive_post()->is_landing_page() ) {
			/* only add this component on page templates; it has no TAr controls, only a custom-made list */
			if ( $is_architect_editor ) {
				$post_config['post-type-template-settings'] = [];
				$post_config['page_content_settings']       = [];
			}

			$visibility_config  = [];
			$visibility_options = [
				[
					'name'  => __( 'Inherit', THEME_DOMAIN ),
					'value' => 'inherit',
				],
				[
					'name'  => __( 'Show', THEME_DOMAIN ),
					'value' => 'show',
				],
				[
					'name'  => __( 'Hide', THEME_DOMAIN ),
					'value' => 'hide',
				],
			];

			/* the section visibility has select controls because they also have an inherit option */
			foreach ( Thrive_Post::get_visibility_config( 'sections' ) as $key => $data ) {
				$visibility_config[ $data['view'] ] = [
					'config'  => [
						'label'   => $data['label'],
						'options' => $visibility_options,
						'default' => 'inherit',
					],
					'extends' => 'Select',
				];
			}
			$controls['Visibility']['config']['default'] = 'inherit';

			/* the normal elements have toggles, not selects */
			foreach ( Thrive_Post::get_visibility_config( 'elements' ) as $key => $data ) {
				/* add the config for each view */
				$visibility_config[ $data['view'] ] = [
					'config'  => [
						'label' => $data['label'],
					],
					'extends' => 'Switch',
				];
			}

			/* add the config for showing the hidden elements in the editor ( this works like that toggle in the responsive component )*/
			$visibility_config['ShowAllHidden'] = [
				'config'  => [
					'label' => __( 'Show all hidden modules', THEME_DOMAIN ),
				],
				'extends' => 'Switch',
			];

			/* add everything we have just set up to the post element config */
			$post_config['visibility_settings'] = [
				'config' => $visibility_config,
			];

			$post_config['scripts_settings'] = [];
		}

		return $post_config;
	}

	/**
	 * Get the AMP settings component
	 *
	 * @return array
	 */
	public static function get_amp_component() {
		return [
			'amp-settings' => [
				'config' => [
					'DisableAMP' => [
						'config'  => [
							'label' => __( 'Disable AMP for this post', THEME_DOMAIN ),
						],
						'extends' => 'Switch',
					],
				],
				'order'  => 999,
			],
		];
	}

	/**
	 * Add extra elements to the translate array from the editor
	 *
	 * @param $translate
	 *
	 * @return mixed
	 */
	public function tcb_js_translate( $translate ) {

		$translate['elements'] = array_merge( $translate['elements'], [
			'thrive_author_box' => __( 'About the Author', THEME_DOMAIN ),
		] );

		return $translate;
	}

	/**
	 * Adds extra items to Page Wizard from TAR when the Theme in active
	 *
	 * @param array $items
	 *
	 * @return array
	 */
	public function tcb_add_page_wizard_items( $items = [] ) {

		if ( thrive_skin()->is_default() ) {
			/**
			 * The default skin doesn't have access to Blank Page & Blank Page with H&F
			 */
			return $items;
		}

		return array_merge( $items, [
			[
				'title'   => __( 'Blank Page with Header and Footer', THEME_DOMAIN ),
				'layout'  => 'blank_hf',
				'order'   => 10,
				'picture' => THEME_URL . '/inc/assets/images/page-wizard/blank-h-f.png',
				'text'    => [
					__( 'Start with a blank page that  includes your header and footer.', THEME_DOMAIN ),
					__( 'Use this template to design full landing pages from scratch using blocks.', THEME_DOMAIN ),
					__( 'This is mostly useful if you want to build a marketing page from scratch (sales pages, lead generation pages, webinar registrations).', THEME_DOMAIN ),
				],
			],
			[
				'title'   => __( 'Completely Blank Page', THEME_DOMAIN ),
				'layout'  => 'completely_blank',
				'order'   => 20,
				'picture' => THEME_URL . '/inc/assets/images/page-wizard/blank.png',
				'text'    => [
					__( 'Start with a completely empty canvas.', THEME_DOMAIN ),
					__( 'Use our page blocks feature to build a page from nothing. Build anything you want - your imagination is your only limit.', THEME_DOMAIN ),
				],
			],
		] );
	}

	/**
	 * Extra check if we want to remove the theme css in landing pages
	 *
	 * @param bool   $remove to remove or not the theme css
	 * @param string $src
	 *
	 * @return bool
	 */
	public function tcb_remove_theme_css( $remove, $src ) {

		/**
		 * We don't need to remove the css that comes from the TAR within the theme builder when TAR is not active on the user's site
		 * That css is actually necessary for the editor
		 */
		if ( strpos( $src, TVE_EDITOR_URL ) !== false ) {
			$remove = false;
		}
		/**
		 * If on a TAr Landing page and user has setup "remove theme CSS" - do not include the theme's css
		 */
		if ( ! $remove && strpos( $src, UPLOAD_DIR_URL_NO_PROTOCOL ) !== false ) {
			$remove = true;
		}

		return $remove;
	}

	/**
	 * Just before downloading a template, set the global post so we have post data available when do_shortcode() is called on the template content.
	 */
	public static function set_global_post_before_cloud_ajax() {
		if ( isset( $_GET['post_id'] ) ) {
			$existing_post = get_post( $_GET['post_id'] );

			if ( ! empty( $existing_post ) ) {
				global $post;

				$post = $existing_post;
			}
		}
	}

	/**
	 * Make sure shortcodes are rendered in templates
	 *
	 * @param $response
	 * @param $ajax_handler
	 *
	 * @return mixed
	 */
	public function tcb_ajax_response_load_content_template( $response, $ajax_handler ) {

		$response['html_code'] = do_shortcode( $response['html_code'] );

		return $response;
	}

	/**
	 * Change breadcrumb data based on post type
	 *
	 * @param array $data
	 *
	 * @return mixed
	 */
	public function post_breadcrumb_data( $data ) {
		$post_type_name = Thrive_Utils::get_post_type_name();

		if ( ! empty( $post_type_name ) ) {
			$data['label'] = $post_type_name;
		}

		return $data;
	}

	/**
	 * For editor page and preview page the query string of the search element should not be shown in the search input
	 *
	 * @param string $query_string
	 *
	 * @return string
	 */
	public function alter_search_element_query_string( $query_string = '' ) {

		if ( is_editor_page_raw() || Thrive_Utils::is_preview() ) {
			$query_string = '';
		}

		return $query_string;
	}

	/**
	 * Exclude Landing Pages from being displayed on the search archive list ( in the template editor and in preview ).
	 * When a LP is displayed there, it prevents the regular archive list from rendering and glitches the editor.
	 *
	 * @param WP_Query $query
	 *
	 * @return mixed
	 */
	public function search_filter_post_types( $query ) {
		/* make sure we never return landing pages when displaying the search template in the template editor */
		if ( is_search() && ( Thrive_Utils::is_inner_frame() || Thrive_Utils::is_preview() ) ) {
			$query->set( 'meta_query', Thrive_Utils::meta_query_no_landing_pages() );
		}

		return $query;
	}

	/**
	 * Appends all sections needed for a theme template.
	 * General sections & headers/footers
	 * Also add all the custom fields for this post type.
	 *
	 * @param array $data
	 * @param int   $post_id
	 *
	 * @return array
	 */
	public function add_lazy_load_data( $data, $post_id ) {

		$data['headers_and_footers'] = Thrive_HF_Section::get_all();
		$data['theme_sections']      = thrive_skin()->get_sections();
		$data['custom_fields']       = $this->get_filtered_custom_fields_data( $post_id );

		return $data;
	}

	/**
	 * Get the custom field keys from the DB and collect some additional data that is used for inline shortcodes
	 *
	 * @param $post_id
	 *
	 * @return array
	 */
	public function get_filtered_custom_fields_data( $post_id ) {
		$custom_fields      = [];
		$custom_field_links = [];
		$real_data          = [];
		$labels             = [];

		/* filter the CF keys and keep only those that are not protected meta */
		$filtered_cf = array_filter( thrive_theme_db()->get_custom_fields( $post_id ), static function ( $meta_key ) {
			return ! tve_hide_custom_fields( false, $meta_key );
		} );

		/* for each custom field key, collect relevant data */
		foreach ( $filtered_cf as $meta_key ) {
			/* get the custom field value for this specific post */
			$meta_value = get_post_meta( $post_id, $meta_key, true );

			/* check if this key + post have ACF data [ when more CF plugins are integrated in TCB, move each integration to another function ) */
			$acf_data = TCB_Custom_Fields_Shortcode::get_post_acf_data( $meta_key, $post_id );

			/* if we have ACF data for this key, then we have to append a prefix to the key ( to mirror the TCB implementation ) */
			if ( ! empty( $acf_data ) ) {
				$meta_key = TCB_Custom_Fields_Shortcode::ACF_PREFIX . $meta_key;

				/* retrieve the label if it exists */
				if ( ! empty( $acf_data['label'] ) ) {
					$label = $acf_data['label'];
				}
			}

			/* if this is an URL custom field, add it to the array of links */
			if ( filter_var( $meta_value, FILTER_VALIDATE_URL ) ) {
				$custom_field_links[ $meta_key ] = [
					'name' => empty( $label ) ? $meta_key : $label,
					'url'  => $meta_value,
					'show' => true,
					'id'   => $meta_key,
				];
			} else {
				/* the data is added to separate arrays because it's mapped directly like this to the inline shortcode config in TCB */
				$real_data[ $meta_key ]     = $meta_value;
				$labels[ $meta_key ]        = empty( $label ) ? '' : $label;
				$custom_fields[ $meta_key ] = $meta_key;
			}
		}

		return [
			'value'     => $custom_fields,
			'real_data' => $real_data,
			'labels'    => $labels,
			/* if not empty, this has to be wrapped in an extra array to be compatible with the TCB shortcode config format */
			'links'     => empty( $custom_field_links ) ? [] : [ $custom_field_links ],
		];
	}

	/**
	 * Change post element name based on post type
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	public function post_element_name( $name ) {
		$post_type_name = Thrive_Utils::get_post_type_name();

		if ( ! empty( $post_type_name ) ) {
			$name = $post_type_name;
		}

		return $name;
	}

	/**
	 * Get version of bundled TAr plugin
	 *
	 * @return string
	 */
	public static function internal_version() {
		return include trailingslashit( THEME_PATH ) . 'architect/version.php';
	}

	/**
	 * Checks version compatibility with standalone version of TAr
	 * Only relevant if TAr plugin is installed and activated
	 *
	 * @return array having the following structure:
	 *      $compatible boolean whether or not TTB and TAr are both up-to-date
	 *      $needs_update string which of the 2 needs updating ('theme', 'plugin')
	 */
	public static function version_compatibility() {
		$status = [
			'compatible'   => true,
			'needs_update' => '',
		];

		/* only if TAr is active as a standalone plugin */
		if ( defined( 'TVE_IN_ARCHITECT' ) && TVE_IN_ARCHITECT && defined( 'TVE_VERSION' ) ) {
			/* check inner TAr version against TAr plugin version */
			$result = version_compare( static::internal_version(), TVE_VERSION );

			if ( $result !== 0 ) {
				$status['compatible']   = false;
				$status['needs_update'] = $result < 0 ? 'theme' : 'plugin';
			}
		}

		return $status;
	}

	/**
	 * Include extra dynamic links in the editor
	 *
	 * Allow this only on the theme templates or in the allowed theme post types
	 */
	public function add_extra_dynamic_links() {
		if ( apply_filters( 'thrive_theme_show_extra_dynamic_links', Thrive_Utils::is_theme_template() ) ) {
			include THEME_PATH . '/inc/templates/parts/extra-dynamic-links.php';
		}
	}

	/**
	 * Adds the landing page lightbox extra icons
	 */
	public function add_extra_lp_lightbox_set_icons() {
		include THEME_PATH . '/inc/templates/parts/tar-extra-lp-lightbox-set-icons.php';
	}

	/**
	 * Adds extra Landing Page Lightbox icons into the Templates Preview View
	 */
	public function add_extra_lp_lightbox_icons() {
		echo '<span>' . tcb_icon( 'ttb-skin', false, 'sidebar', 'set-skin' ) . '</span>';
	}

	public function after_load_custom_css() {
		/**
		 * For 404 pages load global styles because those are not rendered outside posts
		 */
		if ( is_404() ) {
			echo tve_get_shared_styles( '' );
		}
	}
}

new Thrive_Architect();
