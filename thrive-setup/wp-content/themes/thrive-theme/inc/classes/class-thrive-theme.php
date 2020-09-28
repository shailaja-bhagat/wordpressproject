<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

use Thrive\Theme\Integrations\WooCommerce\Main as Woo;

/**
 * Class Thrive_Theme
 */
class Thrive_Theme {

	/**
	 * Thrive_Theme constructor.
	 */
	public function __construct() {
		$this->includes();

		$this->integrations();

		$this->actions();

		$this->filters();
	}

	private function includes() {

		require_once THEME_PATH . '/inc/traits/trait-thrive-singleton.php';

		require_once THEME_PATH . '/inc/traits/trait-thrive-term-meta.php';

		require_once THEME_PATH . '/inc/traits/trait-thrive-post-meta.php';

		require_once THEME_PATH . '/inc/classes/class-thrive-shortcodes.php';

		require_once THEME_PATH . '/inc/classes/utils/class-thrive-utils.php';
		require_once THEME_PATH . '/inc/classes/utils/class-thrive-dom-helper.php';
		require_once THEME_PATH . '/inc/classes/utils/class-thrive-css-helper.php';

		require_once THEME_PATH . '/inc/classes/class-thrive-defaults.php';

		require_once THEME_PATH . '/inc/classes/class-thrive-breadcrumbs.php';


		require_once THEME_PATH . '/inc/classes/class-thrive-views.php';

		require_once THEME_PATH . '/inc/classes/class-thrive-post.php';

		require_once THEME_PATH . '/inc/classes/class-thrive-scripts.php';

		require_once THEME_PATH . '/inc/classes/class-thrive-prev-next.php';

		require_once THEME_PATH . '/inc/classes/class-thrive-category.php';

		require_once THEME_PATH . '/inc/classes/transfer/class-thrive-transfer-import.php';

		require_once THEME_PATH . '/inc/classes/transfer/class-thrive-transfer-export.php';

		require_once THEME_PATH . '/inc/classes/transfer/class-thrive-transfer-utils.php';

		require_once THEME_PATH . '/inc/classes/class-thrive-theme-update.php';

		require_once THEME_PATH . '/inc/classes/class-thrive-theme-db.php';

		require_once THEME_PATH . '/inc/classes/class-thrive-branding.php';

		require_once THEME_PATH . '/inc/classes/class-thrive-theme-comments.php';

		require_once THEME_PATH . '/inc/classes/class-thrive-reset.php';

		/* files needed for video posts */
		require_once THEME_PATH . '/inc/classes/video-post-format/class-thrive-video-post-format-main.php';
		require_once THEME_PATH . '/inc/classes/video-post-format/class-thrive-video-post-format.php';
		require_once THEME_PATH . '/inc/classes/video-post-format/class-thrive-video-post-custom.php';
		require_once THEME_PATH . '/inc/classes/video-post-format/class-thrive-video-post-youtube.php';
		require_once THEME_PATH . '/inc/classes/video-post-format/class-thrive-video-post-vimeo.php';
		require_once THEME_PATH . '/inc/classes/video-post-format/class-thrive-video-post-wistia.php';

		/* files needed for audio posts */
		require_once THEME_PATH . '/inc/classes/audio-post-format/class-thrive-audio-post-format-main.php';
		require_once THEME_PATH . '/inc/classes/audio-post-format/class-thrive-audio-post-format.php';
		require_once THEME_PATH . '/inc/classes/audio-post-format/class-thrive-audio-post-custom.php';
		require_once THEME_PATH . '/inc/classes/audio-post-format/class-thrive-audio-post-spotify.php';
		require_once THEME_PATH . '/inc/classes/audio-post-format/class-thrive-audio-post-soundcloud.php';

		require_once THEME_PATH . '/inc/classes/image-post-format/class-thrive-image-post-format.php';

		if ( is_dir( THEME_PATH . '/tests' ) ) {
			require_once THEME_PATH . '/tests/inc/classes/class-thrive-theme-tests.php';
		}

		/**
		 * Handle compatibility with 3rd party plugins.
		 */
		require THEME_PATH . '/integrations/compatibility.php';
	}

	private function integrations() {
		/**
		 * Load Jetpack compatibility file.
		 */
		if ( defined( 'JETPACK__VERSION' ) ) {
			require THEME_PATH . '/integrations/jetpack/jetpack.php';
		}

		/* include the AMP integration class */
		require_once THEME_PATH . '/integrations/amp/classes/class-main.php';

		/* Cache integration */
		require_once THEME_PATH . '/integrations/cache/class-thrive-fastest-cache.php';
		require_once THEME_PATH . '/integrations/cache/class-thrive-total-cache.php';
		require_once THEME_PATH . '/integrations/optimole-wp/class-thrive-optimole-wp.php';
		require_once THEME_PATH . '/integrations/cache/class-thrive-plugins-manager.php';

		/* Load Thrive Dashboard and integrate the theme insides */
		require_once THEME_PATH . '/integrations/dashboard/class-thrive-theme-dashboard.php';

		require_once THEME_PATH . '/integrations/typography/class-thrive-typography.php';

		require_once THEME_PATH . '/integrations/architect/class-thrive-architect.php';

		/* Wizard */
		require THEME_PATH . '/integrations/wizard/class-thrive-wizard.php';

		/* Landing page integration */
		require_once THEME_PATH . '/integrations/landingpage/class-thrive-landingpage.php';
	}

	private function actions() {

		add_action( 'init', [ $this, 'init' ], 11 );

		add_action( 'after_setup_theme', [ $this, 'theme_setup' ], 11 );

		add_action( 'widgets_init', [ $this, 'widgets_init' ] );

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ], 11 );

		add_action( 'wp_head', [ $this, 'wp_head' ] );

		add_action( 'theme_after_body_open', [ $this, 'theme_after_body_open' ] );

		add_action( 'wp_footer', [ $this, 'wp_footer' ] );

		add_action( 'template_redirect', [ $this, 'template_redirect' ] );

		add_action( 'rest_delete_tcb_symbol', [ $this, 'unlink_hf_from_templates' ], 10, 1 );

		add_action( 'tcb_before_get_content_template', [ $this, 'change_section_menu' ], 10, 3 );

		add_action( 'tcb_landing_head', [ $this, 'print_amp_link_in_landing_page' ] );

		add_action( 'wp', function () {
			/* Add hooks for custom post scripts */
			if ( ! tve_post_is_landing_page() ) {
				thrive_scripts()->hooks();
			}
		} );

		if ( is_admin() ) {

			add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );

			add_action( 'admin_body_class', [ $this, 'admin_body_class' ], PHP_INT_MAX );

			/* actions to show and edit the social URL fields in the profile */
			add_action( 'show_user_profile', [ 'Thrive_Views', 'social_fields_display' ], 10 );
			add_action( 'edit_user_profile', [ 'Thrive_Views', 'social_fields_display' ], 10 );

			/* function called when the URL fields are saved */
			add_action( 'personal_options_update', [ 'Thrive_Utils', 'save_user_fields' ] );
			add_action( 'edit_user_profile_update', [ 'Thrive_Utils', 'save_user_fields' ] );

			add_action( 'admin_footer', [ $this, 'admin_footer' ] );

			add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );

			/* action for saving a post ( from both WP and Architect!! ) */
			add_action( 'save_post', [ $this, 'save_post' ] );

			add_action( 'pre_get_posts', [ $this, 'hide_attachment_from_media_library_dashboard' ] );

			add_action( 'tcb_after_symbol_save', [ $this, 'after_symbol_save' ] );

			add_action( 'admin_action_' . Thrive_Post::CLONE_ACTION, [ $this, 'clone_item' ] );
		}
	}

	private function filters() {

		add_filter( 'option_posts_per_page', [ $this, 'posts_per_page' ] );

		/* add extra classes for body */
		add_filter( 'body_class', [ $this, 'body_class' ] );

		add_filter( 'show_admin_bar', [ $this, 'show_admin_bar' ] );

		add_filter( 'tcb_symbol_css_before', [ $this, 'change_symbols_css' ] );

		add_filter( 'tve_dash_features', [ $this, 'enable_script_manager' ] );

		add_filter( 'td_include_script_manager', '__return_true' );

		add_filter( 'tcb_post_list_content_default_attr', [ $this, 'post_list_content_default_attr' ], 10, 1 );

		add_filter( 'tcb_user_has_post_access', [ $this, 'architect_access' ] );
		add_filter( 'tcb_user_has_plugin_edit_cap', [ $this, 'architect_access' ] );

		add_filter( 'tve_dash_admin_bar_nodes', [ $this, 'theme_admin_bar_menu' ], 10, 1 );

		add_filter( 'ajax_query_attachments_args', [ $this, 'hide_attachment_from_media_library_lightbox' ] );

		add_filter( 'tcb_edit_post_default_url', [ $this, 'template_dashboard_redirect' ], 10, 2 );

		add_filter( 'tve_intrusive_forms', [ $this, 'intrusive_forms' ], 10, 2 );

		add_filter( 'tve_leads_do_not_show_two_step', [ $this, 'do_not_show_two_step_lighbox' ], 10, 2 );

		add_filter( 'tcb_inline_shortcodes', [ $this, 'inline_shortcodes' ], 11 );

		add_filter( 'tve_allowed_post_type', [ $this, 'tve_allowed_post_type' ], 11, 2 );

		/* Used to add a clone link in the post / page listing */
		add_filter( 'post_row_actions', [ $this, 'clone_link' ], 10, 2 );
		add_filter( 'page_row_actions', [ $this, 'clone_link' ], 10, 2 );

		add_filter( 'tcm_allow_comments_editor', [ $this, 'allow_thrive_comments' ] );
	}

	public function init() {
		/**
		 * Require this file here because we need to load it after the file from architect ( TCB_Landing_Page_Transfer.php ) is loaded if this exists
		 */
		require_once THEME_PATH . '/inc/classes/cloud-api/class-thrive-theme-cloud-api-factory.php';

		require_once THEME_PATH . '/inc/classes/class-thrive-featured-image.php';

		/** If a file called .flag-staging-templates exists, show staging templates */
		if ( file_exists( get_template_directory() . '/.flag-staging-templates' ) && ! defined( 'TCB_CLOUD_API_LOCAL' ) ) {
			define( 'TCB_CLOUD_API_LOCAL', 'https://staging.landingpages.thrivethemes.com/cloud-api/index-api.php' );
		}

		/* WooCommerce integration */
		if ( ! class_exists( 'TCB_Woo', false ) ) {
			//todo change this to class-main in 2-3 releases ( we want to ensure compatibility with old TCB until then )
			require_once THEME_PATH . '/architect/inc/classes/class-tcb-woo.php';
		}
		require_once THEME_PATH . '/integrations/woocommerce/class-main.php';

		Thrive\Theme\AMP\Main::init();

		Thrive_Theme_Update::init();

		Thrive_Skin_Taxonomy::register_thrive_templates_tax();

		Thrive_Theme_Default_Data::init();

		Thrive_Demo_Content::init( is_admin() );

		Thrive_Reset::init();

		Thrive_Category::init();

		Woo::init();

		Thrive_Landingpage::init();
	}

	/**
	 * check if there is a valid activated license for the theme
	 *
	 * @return bool
	 */
	public static function licence_check() {
		return TVE_Dash_Product_LicenseManager::getInstance()->itemActivated( Thrive_Theme_Product::TAG );
	}

	/**
	 * Add specific class to body
	 *
	 * @param $classes
	 *
	 * @return array
	 */
	public function body_class( $classes ) {
		return array_merge( $classes, thrive_template()->body_class( true, 'array' ) );
	}

	public function theme_setup() {

		load_theme_textdomain( THEME_DOMAIN, get_template_directory() . '/languages' );

		/* Add default posts and comments RSS feed links to head. */
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		/* This theme uses wp_nav_menu() in one location. */
		register_nav_menus( [
			'theme-menu' => __( 'Primary', THEME_DOMAIN ),
		] );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ] );

		/* Add theme support for selective refresh for widgets. */
		add_theme_support( 'customize-selective-refresh-widgets' );

		/*
		 * Enable support for Post Formats.
		 *
		 * See: https://codex.wordpress.org/Post_Formats
		 */
		add_theme_support( 'post-formats', static::post_formats() );

		add_theme_support( 'tve-wc-mini-cart' );

		add_theme_support( 'woocommerce' );

		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );

		require_once THEME_PATH . '/integrations/dashboard/class-thrive-theme-product.php';
	}

	public function widgets_init() {
		register_sidebar( [
			'name'          => __( 'Default Widget Area', THEME_DOMAIN ),
			'id'            => THRIVE_DEFAULT_SIDEBAR,
			'description'   => __( 'Add widgets here.', THEME_DOMAIN ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		] );

		$sidebars = get_option( THRIVE_SIDEBARS_OPTION, [] );
		if ( is_array( $sidebars ) ) {
			foreach ( $sidebars as $sidebar ) {
				register_sidebar( [
					'name'          => $sidebar['name'],
					'id'            => 'thrive-theme-sidebar-' . $sidebar['id'],
					'description'   => __( 'Add widgets here.', THEME_DOMAIN ),
					'before_widget' => '<section id="%1$s" class="widget %2$s">',
					'after_widget'  => '</section>',
					'before_title'  => '<h2 class="widget-title">',
					'after_title'   => '</h2>',
				] );
			}
		}
	}

	/**
	 * Enqueue front scripts
	 */
	public function enqueue_scripts() {

		$template_style = get_option( THRIVE_TEMPLATE_STYLE, '' );

		tve_enqueue_style_family( 0 );

		wp_enqueue_style( 'thrive', get_stylesheet_uri(), [], THEME_VERSION );

		tve_dash_enqueue_script( 'theme-frontend', THEME_ASSETS_URL . '/frontend.min.js', [ 'jquery', 'jquery-ui-resizable' ], THEME_VERSION, true );

		wp_localize_script( 'theme-frontend', 'thrive_front_localize', $this->localize_object( 'front' ) );

		if ( apply_filters( 'thrive_theme_display_css', ! empty( $template_style ) && thrive_template()->is_default() && ( ! is_singular() || ! tve_post_is_landing_page() ) && ! (
				Thrive_Utils::is_inner_frame() ||
				Thrive_Utils::is_preview() ||
				Thrive_Utils::is_skin_preview() ||
				Thrive_Utils::is_theme_typography() ||
				Thrive_Utils::use_inline_css()
			) ) ) {
			/* display the css file only on frontend because that's the only place where we display only the default templates */
			wp_enqueue_style( 'thrive-template', UPLOAD_DIR_URL_NO_PROTOCOL . '/thrive/' . $template_style, [ 'tve_style_family_tve_flt' ] );
		}

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		if ( Thrive_Wizard::is_frontend() ) {
			tve_dash_enqueue_script( 'ttb-wizard-preview', THEME_ASSETS_URL . '/wizard.min.js', [ 'theme-frontend' ] );
			wp_enqueue_style( 'ttb-wizard', THEME_ASSETS_URL . '/wizard.css' );
		}

		thrive_template()->enqueue_global_scripts();
	}

	/**
	 * Enqueue admin scripts
	 */
	public function admin_enqueue_scripts() {
		if ( Thrive_Utils::is_thrive_page( THRIVE_THEME_DASH_PAGE ) ) {
			wp_enqueue_media();
			wp_enqueue_style( 'thrive-admin-style', THEME_ASSETS_URL . '/admin.css', false, THEME_VERSION );

			wp_enqueue_style( 'ttb-wizard', THEME_ASSETS_URL . '/wizard.css' );
			/**
			 * Output the skin variables also inside dashboard (main frame)
			 */
			wp_add_inline_style( 'thrive-admin-style', ':root{' . thrive_skin()->get_variables_for_css() . '}' );
			wp_enqueue_script( 'jquery-masonry', [ 'jquery' ] );
			tve_dash_enqueue_script( 'thrive-admin-script', THEME_ASSETS_URL . '/admin.min.js', [
				'jquery',
				'backbone',
				'underscore',
				'jquery-ui-autocomplete',
			], THEME_VERSION, true );

			tve_dash_enqueue_script( 'thrive-admin-libs', THEME_ASSETS_URL . '/admin-libs.min.js', [
				'jquery',
				'backbone',
				'underscore',
				'jquery-ui-tooltip',
			] );
			wp_enqueue_script( 'tar-lazyload', tve_editor_url() . '/editor/js/libs/lazyload.min.js', [ 'thrive-admin-libs' ] );

			wp_localize_script( 'thrive-admin-script', 'ttd_admin_localize', $this->localize_object( 'admin' ) );
		}

		if ( Thrive_Utils::is_thrive_page( 'widgets' ) ) {
			tve_dash_enqueue_script( 'thrive-widgets', THEME_ASSETS_URL . '/widgets.min.js', [ 'jquery', 'backbone', 'underscore' ], THEME_VERSION, true );

			wp_localize_script( 'thrive-widgets', 'ttb_widgets', $this->localize_object( 'widgets' ) );
		}

		/* add this css on the 'add new post/page' and 'edit post/page' screens */
		if ( is_admin() && Thrive_Utils::is_allowed_post_type( thrive_post()->ID ) && ! thrive_post()->is_landing_page() ) {
			wp_enqueue_style( 'thrive-admin-style', THEME_ASSETS_URL . '/post.css', false, THEME_VERSION );
		}

		/* add this script only on the 'add new post' and 'edit post' pages */
		if ( Thrive_Utils::is_thrive_page( 'post' ) ) {
			tve_dash_enqueue_script( 'thrive-admin-post-edit', THEME_ASSETS_URL . '/post-edit.min.js', [
				'jquery',
				'backbone',
				'underscore',
			], THEME_VERSION, true );
		}
	}

	/**
	 * Localize object for site scripts
	 *
	 * @param string $context for where to localize things.
	 *
	 * @return array
	 */
	private function localize_object( $context = '' ) {
		$blog_id = get_current_blog_id();

		switch ( $context ) {
			case 'admin':
				$object = [
					'debug'                      => defined( 'TVE_DEBUG' ) && TVE_DEBUG,
					'templates'                  => Thrive_Template::localize_all(),
					'post_formats'               => array_merge( [ THRIVE_STANDARD_POST_FORMAT ], static::post_formats() ),
					//we need to add standard because it doesn't really exists
					'content_types'              => Thrive_Utils::get_content_types( 'localize' ),
					'woocommerce'                => Woo::admin_localize(),
					'skins'                      => Thrive_Skin_Taxonomy::get_all(),
					'skin_id'                    => thrive_skin()->ID,
					'typography'                 => thrive_skin()->get_typographies(),
					'cloud_skins'                => Thrive_Skin_Taxonomy::get_cloud_skins(),
					'branding'                   => Thrive_Branding::localize(),
					'settings'                   => [
						'featured_image' => [
							'default'     => Thrive_Featured_Image::get_default_url(),
							'placeholder' => THRIVE_FEATURED_IMAGE_PLACEHOLDER,
						],
						'inline_css'     => Thrive_Utils::use_inline_css(),
					],
					'options'                    => Thrive_Utils::get_homepage_options(),
					'nonce'                      => wp_create_nonce( 'wp_rest' ),
					'editor_nonce'               => wp_create_nonce( TCB_Editor_Ajax::NONCE_KEY ),
					'routes'                     => [
						'templates'  => get_rest_url( $blog_id, TTB_REST_NAMESPACE . '/templates' ),
						'typography' => get_rest_url( $blog_id, TTB_REST_NAMESPACE . '/typography' ),
						'skins'      => get_rest_url( $blog_id, TTB_REST_NAMESPACE . '/skins' ),
						'options'    => get_rest_url( $blog_id, TTB_REST_NAMESPACE . '/options' ),
						'images'     => get_rest_url( $blog_id, TTB_REST_NAMESPACE . '/image' ),
						'logo'       => get_rest_url( $blog_id, TCB_REST_NAMESPACE . '/logo' ),
						'wizard'     => get_rest_url( $blog_id, TTB_REST_NAMESPACE . '/wizard' ),
						'content'    => get_rest_url( $blog_id, TTB_REST_NAMESPACE . '/content' ),
						'plugins'    => get_rest_url( $blog_id, TTB_REST_NAMESPACE . '/plugins' ),
						'amp'        => get_rest_url( $blog_id, TTB_REST_NAMESPACE . '/amp' ),
					],
					'list_templates'             => Thrive_Utils::list_templates(),
					'fallback'                   => Thrive_Template_Fallback::option(),
					'theme_url'                  => THEME_URL,
					'wizard'                     => Thrive_Wizard::localize_admin(),
					'home_url'                   => home_url( '/' ),
					'admin_url'                  => trailingslashit( admin_url() ),
					'menus'                      => tve_get_custom_menus(), //todo maybe we should call our function for getting the menus
					'dismissed_tooltips'         => (array) get_user_meta( wp_get_current_user()->ID, 'ttb_dismissed_tooltips', true ),
					'architect_url'              => tve_editor_url() . '/',
					'cache_plugins'              => Thrive_Plugins_Manager::get_cache_plugins(),
					'image_optimization_plugins' => Thrive_Plugins_Manager::get_image_optimization_plugins(),
					'amp'                        => Thrive\Theme\AMP\Settings::localize(),
				];

				/**
				 * Include The skin Variables only for the end user. So not for Theme Builder Site
				 */
				if ( Thrive_Utils::is_end_user_site() ) {
					$object = array_merge( $object, [
						'skin_palettes'  => thrive_skin()->get_palettes(),
						'skin_variables' => thrive_skin()->get_variables( true ),
					] );
				}

				break;
			case 'front':
				$queried_object    = Thrive_Utils::get_filtered_queried_object();
				$queried_object_id = empty( $queried_object['ID'] ) ? '' : $queried_object['ID'];

				$object = [
					'comments_form'   => [
						'error_defaults' => thrive_theme_comments::get_comment_form_error_labels(),
					],
					'routes'          => [
						'posts' => get_rest_url( $blog_id, 'tcb/v1' . '/posts' ),
					],
					'queried_object'  => $queried_object,
					'tar_post_url'    => add_query_arg( 'from_theme', true, tcb_get_editor_url( $queried_object_id ) ),
					'is_editor'       => is_editor_page_raw(),
					'ID'              => thrive_template()->ID,
					'template_url'    => add_query_arg( 'from_tar', $queried_object_id, tcb_get_editor_url( thrive_template()->ID ) ),
					'pagination_href' => Thrive_Utils::get_pagination_href(),
					'blog_url'        => Thrive_Utils::get_blog_url(),
					'is_singular'     => is_singular(),
				];
				if ( Thrive_Wizard::is_frontend() ) {
					$object['wizard'] = thrive_wizard()->localize_frontend();
				}
				break;
			case 'widgets':
				$object = [
					'nonce' => wp_create_nonce( 'wp_rest' ),
					'route' => get_rest_url( $blog_id, TTB_REST_NAMESPACE . '/sidebar' ),
				];
				break;
			default:
				$object = [];
		}

		return $object;
	}

	public function wp_head() {
		/* if we're on a landing page and we want to remove the theme css, we don't run anything from here */
		if ( tve_post_is_landing_page() && ! empty( get_post_meta( get_the_ID(), 'tve_disable_theme_dependency', true ) ) ) {
			return;
		}

		/* output the head scripts added from Analytics & Scripts if we're not on a LP */
		if ( tve_post_is_landing_page() === false && class_exists( 'TVD_SM_Frontend', false ) && method_exists( 'TVD_SM_Frontend', 'theme_scripts' ) ) {
			echo TVD_SM_Frontend()->theme_scripts( 'head' );
		}

		/* Add a pingback url auto-discovery header for singularly identifiable articles. */
		if ( is_singular() && pings_open() ) {
			echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
		}

		$thrive_template = thrive_template();

		if ( ! $thrive_template->is_default() || Thrive_Utils::is_inner_frame() || Thrive_Utils::is_preview() || Thrive_Utils::is_skin_preview() || Thrive_Utils::use_inline_css() ) {
			/* inside the editor we print the css because the file contains only styles from default templates */
			echo thrive_layout()->style( true );
			echo $thrive_template->style();
		}

		/**
		 * output typography CSS in a custom style node just when editing TTB typography.
		 * Rest of the time this is printed from tcb_print_frontend_styles()
		 */
		if ( Thrive_Utils::is_theme_typography() ) {
			echo thrive_typography( get_the_ID() )->style( true );
		}

		if ( is_singular() && ! Thrive_Utils::is_inner_frame() ) {
			echo $thrive_template->dynamic_style();
		}

		if ( ! tve_post_is_landing_page() ) {
			/* on landing pages we don't look for shared styles because the template does not render */
			$thrive_template->check_for_meta_tags();
		}
	}

	/**
	 * Scripts that render after body open
	 */
	public function theme_after_body_open() {
		/* output the body_open scripts added from Analytics & Scripts if we're not on a LP */
		if ( tve_post_is_landing_page() === false && class_exists( 'TVD_SM_Frontend', false ) && method_exists( 'TVD_SM_Frontend', 'theme_scripts' ) ) {
			echo TVD_SM_Frontend()->theme_scripts( 'body_open' );
		}
	}

	public function wp_footer() {
		/* output the body_close scripts added from Analytics & Scripts if we're not on a LP */
		if ( tve_post_is_landing_page() === false && class_exists( 'TVD_SM_Frontend', false ) && method_exists( 'TVD_SM_Frontend', 'theme_scripts' ) ) {
			echo TVD_SM_Frontend()->theme_scripts( 'body_close' );
		}
	}

	/**
	 * Prepare Thrive Theme node
	 *
	 * @param array $nodes
	 *
	 * @return array
	 */
	public function theme_admin_bar_menu( $nodes ) {
		$compatibility = Thrive_Architect::version_compatibility();
		if ( $compatibility['compatible'] && ! is_admin() && ! tve_post_is_landing_page() && Thrive_Utils::is_allowed_post_type( get_the_ID() ) && current_user_can( 'edit_posts' ) ) {

			$post_title    = thrive_template()->post_title;
			$template_name = empty( $post_title ) ? '' : ' "' . $post_title . '"';
			$args          = [
				'id'    => 'thrive-builder',
				'title' => __( 'Edit Theme Template', THEME_DOMAIN ) . $template_name,
				'href'  => add_query_arg( [ 'from_tar' => get_the_ID() ], tcb_get_editor_url( thrive_template()->ID ) ),
				'order' => 1,
			];

			/* Add the node to the others */
			$nodes[] = $args;
		}

		return $nodes;
	}

	/**
	 * Print admin backbone templates and dashboard svgs
	 */
	public function admin_footer() {
		if ( Thrive_Utils::is_thrive_page( THRIVE_THEME_DASH_PAGE ) ) {
			$templates = tve_dash_get_backbone_templates( THEME_PATH . '/inc/templates/backbone', 'backbone' );

			tve_dash_output_backbone_templates( $templates, 'ttd-' );

			include THEME_PATH . '/inc/assets/svg/dashboard.svg';
		}
	}

	/**
	 * Add animation class to the admin body
	 *
	 * @param $classes
	 *
	 * @return string
	 */
	public function admin_body_class( $classes ) {
		if ( ! defined( 'TVE_DEBUG' ) && Thrive_Utils::is_thrive_page( THRIVE_THEME_DASH_PAGE ) ) {
			$classes .= ' ttd-init ';
		}

		return $classes;
	}

	/**
	 * Add meta boxes for post/page settings.
	 */
	public function add_meta_boxes() {
		$thrive_post = thrive_post();

		/* don't add anything on landing pages; add these meta boxes only for post / page / custom post types */
		if ( ! $thrive_post->is_landing_page() && Thrive_Utils::is_allowed_post_type( $thrive_post->ID ) ) {
			if ( tcb_admin()->tcb_enabled( $thrive_post->ID ) ) {
				add_meta_box(
					'thrive-template-notice',
					__( 'Thrive Theme Builder', THEME_DOMAIN ),
					[ Thrive_Views::class, 'no_template_settings_notice' ],
					null,
					'side',
					'high'
				);
			} else {
				/* we can edit the shop page in admin, but we don't want to display the template dropdown */
				if ( ! Woo::is_admin_shop_page() ) {
					add_meta_box(
						'thrive-template-meta',
						__( 'Theme Builder Templates', THEME_DOMAIN ),
						[ Thrive_Views::class, 'template_meta_box' ],
						null,
						'side',
						'high'
					);
				}

				/* add a meta box for post visibility settings */
				add_meta_box(
					'thrive-visibility-meta',
					__( 'Theme Builder Visibility', THEME_DOMAIN ),
					[ Thrive_Views::class, 'visibility_meta_box' ],
					null,
					'side',
					'high'
				);
			}

			add_meta_box(
				'thrive_post_format_options',
				__( 'Thrive Post Format Options', THEME_DOMAIN ),
				[ Thrive_Views::class, 'post_format_options' ],
				'post',
				'normal',
				'high'
			);

			add_meta_box(
				'thrive_post_scripts',
				__( 'Custom Scripts', THEME_DOMAIN ),
				[ Thrive_Scripts::class, 'admin_metabox' ],
				null,
				'side',
				'high'
			);

		}
	}


	/**
	 * Save the information ( visibility, video, audio ) from the meta boxes.
	 *
	 * @param $post_id
	 */
	public function save_post( $post_id ) {

		/* only save any post meta if the save was done from the WP screen ( this is also called by TAr save, but we handle that case elsewhere ) */
		if ( isset( $_POST['action'] ) && $_POST['action'] === 'editpost' ) {
			/* instantiate a Thrive_Post with this post ID and save the visibility meta */
			$post = new Thrive_Post( $post_id );

			/* save the visibility settings if they exist( if the post is edited with TAr, the visibility settings are hidden in the WP post screen! )  */
			if ( isset( $_POST['thrive_visibility_settings_enabled'] ) ) {
				$post->save_visibility_meta_from_wp();
			}

			/* save the template settings only if they exist ( same as above ) */
			if ( isset( $_POST['thrive_template_settings_enabled'] ) ) {
				$post->save_template_meta_from_wp();
			}

			/* save the information from the video format meta box */
			Thrive_Video_Post_Format_Main::save_video_meta_fields();

			/* save the information from the audio format meta box */
			Thrive_Audio_Post_Format_Main::save_audio_meta_fields();

			/* save custom post / page scripts */
			thrive_scripts()->save( $_POST );
		}
	}

	/**
	 * Post formats supported by the theme
	 *
	 * @return array
	 */
	public static function post_formats() {
		return [ 'image', 'video', 'audio' ];
	}

	/**
	 * Hide admin bar when preview on iframe
	 *
	 * @param $show_admin_bar
	 *
	 * @return bool
	 */
	public function show_admin_bar( $show_admin_bar ) {

		if ( isset( $_GET[ THRIVE_NO_BAR ] ) ) {
			$show_admin_bar = false;
		}

		return $show_admin_bar;
	}

	/**
	 * Check if we are in preview mode and add parameters accordingly, then redirect
	 */
	public function template_redirect() {
		global $wp;
		$current_url = home_url( add_query_arg( $_GET, $wp->request ) );

		if (
			isset( $_SERVER['HTTP_REFERER'] )
			&& strpos( $_SERVER['HTTP_REFERER'], THRIVE_NO_BAR ) !== false //if the referer has thrive_no_bar ( so we are in preview mode )
			&& strpos( $current_url, THRIVE_NO_BAR ) === false // and the current url doesn't have thrive_no_bar -> than we should add it to the current url
		) {
			$location = add_query_arg( THRIVE_NO_BAR, '1', $current_url );

			//check if we are also in the skin preview mode
			if ( strpos( $_SERVER['HTTP_REFERER'], THRIVE_SKIN_PREVIEW ) !== false ) {
				//match the thrive_skin_preview=123 pattern
				preg_match( '/' . THRIVE_SKIN_PREVIEW . '=\\d+/', $_SERVER['HTTP_REFERER'], $matches, PREG_OFFSET_CAPTURE );

				if ( ! empty( $matches ) ) {
					//take the skin_preview_id from the matches
					$skin_preview_id = (int) filter_var( $matches[0][0], FILTER_SANITIZE_NUMBER_INT );
				}

				$location = add_query_arg( THRIVE_SKIN_PREVIEW, $skin_preview_id, $location );
			}
			wp_redirect( $location );
		}
	}

	/**
	 * Apply do_shortcode on the symbol's css, just in case there is some dynamic css in it
	 *
	 * @param $css
	 *
	 * @return string
	 */
	public function change_symbols_css( $css ) {
		return do_shortcode( $css );
	}

	/**
	 * If the symbol has theme shortcodes in it we need to save the meta values from their data attr
	 *
	 * @param $data
	 */
	public function after_symbol_save( $data ) {
		$theme_metas = [ 'icons', 'decorations' ];

		foreach ( $theme_metas as $meta ) {
			if ( isset( $data[ $meta ] ) ) {
				update_post_meta( $data['symbol']->ID, $meta, $data[ $meta ] );
			}
		}
	}

	/**
	 * Enable the "Script manager" feature from thrive dashboard
	 *
	 * @param array $enabled
	 *
	 * @return array
	 */
	public function enable_script_manager( $enabled ) {
		$enabled['script_manager'] = true;

		return $enabled;
	}

	/**
	 * Even if we have Architect light, we still display the buttons to edit
	 *
	 * @param $access
	 *
	 * @return bool
	 */
	public function architect_access( $access ) {

		if ( Thrive_Architect::is_light() ) {
			$access = Thrive_Theme_Product::has_access();
		}

		return $access;
	}

	/**
	 * Blog list has default content as default size
	 *
	 * @param $attr
	 *
	 * @return mixed
	 */
	public function post_list_content_default_attr( $attr ) {

		if ( Thrive_Shortcodes::is_inside_shortcode( [ 'thrive_blog_list' ] ) ) {
			$attr['size'] = is_search() ? 'excerpt' : 'content';
		}

		return $attr;
	}

	/**
	 * When a header or footer is deleted from the admin area ( Global Elements ), unlink it from all the templates where it's used
	 *
	 * @param $post
	 */
	public function unlink_hf_from_templates( $post ) {
		if ( ! empty( $post ) ) {
			/* determine the type of the symbol */
			if ( has_term( 'headers', TCB_Symbols_Taxonomy::SYMBOLS_TAXONOMY, $post ) ) {
				$type = THRIVE_HEADER_SECTION;
			} elseif ( has_term( 'footers', TCB_Symbols_Taxonomy::SYMBOLS_TAXONOMY, $post ) ) {
				$type = THRIVE_FOOTER_SECTION;
			}

			if ( ! empty( $type ) && ! empty( $post->ID ) ) {
				$section_to_delete = new Thrive_HF_Section( $post->ID, $type );

				$section_to_delete->unlink_from_templates();
			}
		}
	}

	/**
	 * Hide attachment files from the Media Library's overlay (modal) view if they have theme section meta key set.
	 *
	 * @param array $args An array of query variables.
	 *
	 * @return mixed
	 */
	public function hide_attachment_from_media_library_lightbox( $args ) {

		if ( is_admin() ) {
			// Modify the query.
			$args['meta_query'] = [
				[
					'key'     => THRIVE_DEMO_CONTENT_THUMBNAIL,
					'compare' => 'NOT EXISTS',
				],
			];
		}

		return $args;
	}

	/**
	 * Hide attachment files from the Media Library's list view if they have the 'demo content' flag set.
	 * Taken from https://wordpress.stackexchange.com/a/271592
	 *
	 * @param WP_Query $query
	 */
	public function hide_attachment_from_media_library_dashboard( $query ) {
		if ( is_admin() && $query->is_main_query() ) {
			$screen = get_current_screen();

			if ( $screen && $screen->id === 'upload' && $screen->post_type === 'attachment' ) {
				$query->set( 'meta_query', [
					[
						'key'     => THRIVE_DEMO_CONTENT_THUMBNAIL,
						'compare' => 'NOT EXISTS',
					],
				] );
			}
		}
	}

	/**
	 * Replace the redirect link from edit page to template dashboard
	 *
	 * @param $redirect_link
	 * @param $post
	 *
	 * @return mixed
	 */
	public function template_dashboard_redirect( $redirect_link, $post ) {

		if ( Thrive_Utils::is_theme_template() ) {
			$redirect_link = admin_url( 'admin.php?page=thrive-theme-dashboard&tab=other#templates' );
		}

		return $redirect_link;
	}

	/**
	 * Output <html>'s CSS class attribute
	 */
	public static function html_class() {
		/**
		 * Allows adding dynamic classes on the HTML element
		 *
		 * @param array $classes current list of classes
		 *
		 * @return array
		 */
		$classes = (array) apply_filters( 'thrive_html_class', [] );
		$attr    = '';

		if ( $classes ) {
			$attr = ' class="' . implode( ' ', $classes ) . '"';
		}

		echo $attr;
	}

	/**
	 * Change the menu from the header and replace it with the default one
	 *
	 * @param WP_Post $post
	 * @param array   $meta
	 */
	public function change_section_menu( $post, $meta ) {

		if ( $meta['type'] === THRIVE_HEADER_SECTION ) {
			$menu_id = thrive_skin()->get_default_data( 'header_menu' ) ?: thrive_skin()->get_default_data( 'menu' );
		} elseif ( $meta['type'] === THRIVE_FOOTER_SECTION ) {
			$menu_id = thrive_skin()->get_default_data( 'footer_menu' );
		}

		if ( ! empty( $menu_id ) ) {
			$html = Thrive_Utils::replace_menu_in_html( $menu_id, $post->post_content );
			if ( ! empty( $html ) ) {
				$post->post_content = is_editor_page_raw( true ) ? tve_thrive_shortcodes( $html, true ) : $html;
			}
		}
	}

	/**
	 * If this is a List template and we're not in the editor, get the posts_per_page from the content section meta
	 * The reason for doing this is that WP manually checks the 'global' posts_per_page set from 'Reading' and
	 * sets pages as 'not found' even if they exist according to the specific 'posts_per_page' setting of the blog list
	 *
	 * @param $value
	 *
	 * @return mixed
	 */
	public function posts_per_page( $value ) {

		global $wp_query;

		/* we do this only on the frontend and only for list templates and only when we have the query set, otherwise we can't detect stuff */
		if ( ! empty( $wp_query->query ) && ! is_admin() && ! TCB_Utils::is_rest() && ! is_singular() && ! TCB_Utils::in_editor_render( true ) ) {
			$posts_per_page = thrive_template()->get_meta_from_sections( 'posts_per_page' );

			if ( ! empty( $posts_per_page ) ) {
				$value = $posts_per_page;
			}
		}

		return $value;
	}

	/**
	 * Do not allow some TL form types to be show in the wizard and branding iframe
	 *
	 * @param array $items
	 *
	 * @return array
	 */
	public function intrusive_forms( $items, $product ) {
		if ( Thrive_Utils::is_iframe() ) {

			switch ( $product ) {
				case 'tl':
					$do_not_show = [ 'lightbox', 'ribbon', 'screen_filler', 'greedy_ribbon', 'slide_in' ];
					$filter_fn   = static function ( $item ) use ( $do_not_show ) {
						return ! in_array( $item->tve_form_type, $do_not_show, true );
					};
					break;
				case 'tu':
					$allowed_items = [ 'shortcode', 'widget' ];
					$filter_fn     = static function ( $item ) use ( $allowed_items ) {
						return in_array( $item['post_type'], $allowed_items, true );
					};
					break;
				case 'tcb':
					$items = [];
					break;
				default:
					break;
			}

			if ( ! empty( $filter_fn ) && is_callable( $filter_fn ) ) {
				$items = array_filter( $items, $filter_fn );
			}
		}

		return $items;
	}

	/**
	 * Show clone link next to the post name in the post list page
	 *
	 * @param array   $actions
	 * @param WP_Post $post
	 *
	 * @return mixed
	 */
	public function clone_link( $actions, $post ) {
		if ( current_user_can( 'edit_posts' ) ) {
			$actions['edit_as_new_draft'] = thrive_post( $post->ID )->get_clone_link_html();
		}

		return $actions;
	}

	/**
	 * Entry point for cloning a post / page
	 */
	public function clone_item() {
		if ( isset( $_GET['post'] ) ) {
			try {
				$id = thrive_post( $_GET['post'] )->duplicate();

				// Redirect to the edit screen for the new post / page
				wp_redirect( admin_url( 'post.php?action=edit&post=' . $id ) );
			} catch ( Exception $exception ) {
				wp_die( $exception->getMessage() );
			}
		} else {
			wp_die( __( 'No post to duplicate has been supplied!', THEME_DOMAIN ) );
		}
	}

	/**
	 * When we're on a landing page and AMP is active, print the link towards the AMP equivalent of this LP.
	 *
	 * @param $landing_page_id
	 */
	public function print_amp_link_in_landing_page( $landing_page_id ) {
		Thrive\Theme\AMP\Main::print_amp_permalink( $landing_page_id );
	}

	/**
	 * Decide if we will show two step lighbox in the theme
	 * We would like to prevent that only within our iframes and if there is a page event setup for that specific lightbox
	 *
	 * @param bool $do_not_show
	 * @param int  $id
	 *
	 * @return bool
	 */
	public function do_not_show_two_step_lighbox( $do_not_show, $id ) {
		if ( Thrive_Utils::is_iframe() ) {
			$events = tve_get_post_meta( get_the_ID(), 'tve_page_events' );

			if ( ! empty( $events ) ) {
				foreach ( $events as $event ) {
					if ( ! empty( $event['config']['l_id'] ) && $event['config']['l_id'] === $id ) {
						$do_not_show = true;
					}
				}
			}
		}

		return $do_not_show;
	}

	/**
	 * Change the inline shortcodes for taxonomies when we are on a list pge
	 *
	 * @param $shortcodes
	 *
	 * @return mixed
	 */
	public function inline_shortcodes( $shortcodes ) {
		if ( thrive_template()->is_archive() ) {
			$shortcodes['Archive'] = [
				[
					'name'   => __( 'Archive Name', THEME_DOMAIN ),
					'option' => __( 'Archive Name', THEME_DOMAIN ),
					'value'  => 'thrive_archive_name',
				],
				[
					'name'   => __( 'Archive Description', THEME_DOMAIN ),
					'option' => __( 'Archive Description', THEME_DOMAIN ),
					'value'  => 'thrive_archive_description',
				],
				[
					'name'   => __( 'Archive Parent Name', THEME_DOMAIN ),
					'option' => __( 'Archive Parent Name', THEME_DOMAIN ),
					'value'  => 'thrive_archive_parent_name',
				],
			];
		}

		return $shortcodes;
	}

	/**
	 * Do not allow Thrive Optimize A/B Test option to show when the user is no a theme template
	 * This filter is also user in TAR
	 *
	 * @param bool   $allow
	 * @param string $post_type
	 *
	 * @return bool
	 */
	public function tve_allowed_post_type( $allow, $post_type ) {
		if ( $post_type === THRIVE_TEMPLATE ) {
			$allow = false;
		}

		return $allow;
	}

	/**
	 * Allow Thrive Comments on singular templates
	 *
	 * @param bool $allow
	 *
	 * @return bool
	 */
	public function allow_thrive_comments( $allow ) {
		if ( ! Thrive_Utils::is_iframe() && Thrive_Utils::is_editor() && thrive_template()->is_singular() ) {
			$allow = true;
		}

		return $allow;
	}
}
