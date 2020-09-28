<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

use Thrive\Theme\Integrations\WooCommerce\Main;

class Thrive_Wizard {
	/**
	 * Stores all active wizard steps
	 *
	 * @var array
	 */
	public static $active_steps
		= [
			'logo',
			'color',
			'header',
			'footer',
			'homepage',
			'post',
			'blog',
			'page',
			'menu',
			'woo',
		];

	/* wizard request data */
	protected $request = [];

	/**
	 * Wizard completion data
	 *
	 * @var array
	 */
	protected $completion_data = [];

	public function __construct() {
		if ( static::is_frontend() ) {
			$this->init_frontend();
		}
	}

	/**
	 * Get request data
	 *
	 * @param string $key
	 * @param string $default
	 *
	 * @return string|array
	 */
	public function request( $key, $default = '' ) {
		return isset( $this->request[ $key ] ) ? $this->request[ $key ] : $default;
	}

	/**
	 * Return the active steps from the wizard
	 *
	 * @return mixed|void
	 */
	public static function get_active_steps() {
		return apply_filters( 'thrive_theme_wizard_active_steps', static::$active_steps );
	}

	/**
	 * Get current step
	 *
	 * @return string
	 */
	public function step() {
		return $this->request( 'step' );
	}

	/**
	 * Checks if current request is for a wizard preview step
	 *
	 * @return bool
	 */
	public static function is_frontend() {
		return ! empty( $_REQUEST['ttb-wizard'] );
	}

	/**
	 * Whether or not the current request is for a template preview
	 *
	 * @return bool
	 */
	public function is_template_preview() {
		return static::is_frontend() && $this->request( 'template_id' ) && ! in_array( $this->step(), [ THRIVE_HEADER_SECTION, THRIVE_FOOTER_SECTION ] );
	}

	/**
	 * Inits the frontend request for a wizard step preview
	 */
	public function init_frontend() {

		show_admin_bar( false );
		add_filter( 'show_admin_bar', '__return_false' );
		$this->request = $_REQUEST['ttb-wizard'];

		/* register filters */
		add_filter( 'thrive_hf_section', [ $this, 'maybe_render_custom_hf' ], 10, 2 );

		/* conditional rendering */
		add_filter( 'thrive_template_render_header', [ $this, 'should_render_header' ] );
		add_filter( 'thrive_template_render_footer', [ $this, 'should_render_footer' ] );
		add_filter( 'thrive_template_render_structure', [ $this, 'should_render_structure' ] );

		/* output placeholders during wizard preview steps */
		add_action( 'thrive_template_header_before', [ $this, 'header_placeholder' ] );
		add_action( 'thrive_template_footer_before', [ $this, 'structure_footer_placeholder' ] );

		add_filter( 'thrive_html_class', function ( $classes ) {
			$step       = str_replace( 'woo_', '', $this->step() );
			$classes [] = "ttb-wizard ttb-wizard--{$step}";
			if ( $this->step() === 'homepage' ) {
				$classes [] = 'home--' . ( $this->request( 'type' ) ?: 'splash' );
			}

			return $classes;
		} );

		add_filter( 'body_class', function ( $classes ) {
			return apply_filters( 'thrive_theme_wizard_body_classes', $classes, $this->step() );
		} );

		add_filter( 'thrive_template_style', [ $this, 'filter_css' ] );

		if ( $this->is_template_preview() ) {
			add_filter( 'thrive_template_default_id', function () {
				return (int) $this->request( 'template_id' );
			} );
		}

		/* handles homepage previews */
		if ( $this->step() === 'homepage' ) {
			$homepage_type = $this->request( 'type' );
			$page_id       = $this->request( 'page_id' );
			$template_id   = $this->request( 'template_id' );

			add_filter( 'pre_option_show_on_front', static function () use ( $homepage_type ) {
				/* use blog by default, and modify that accordingly */
				return $homepage_type && $homepage_type !== 'blog' ? 'page' : 'posts';
			} );
			add_filter( 'pre_option_page_on_front', static function () use ( $homepage_type, $page_id ) {
				if ( ! empty( $GLOBALS['thrive_during_post_insert'] ) ) {
					return false; // don't cause an infinite loop!
				}
				$value = false;
				if ( $homepage_type === 'page' && $page_id ) {
					$value = $page_id;
				}

				return $value;
			} );

			if ( $homepage_type === 'template' && $template_id ) {

				/**
				 * displays a custom post type on which a landing page has been applied
				 */
				add_action( 'wp', static function () use ( $template_id ) {
					/* apply a TCB cloud LP on this page */
					if ( method_exists( TCB_Landing_Page::class, 'apply_cloud_template' ) ) {
						$post = get_post();
						TCB_Landing_Page::apply_cloud_template( $post->ID, $template_id );

						update_post_meta( $post->ID, '_tve_header', thrive_skin()->get_default_data( THRIVE_HEADER_SECTION ) );
						update_post_meta( $post->ID, '_tve_footer', thrive_skin()->get_default_data( THRIVE_FOOTER_SECTION ) );
						delete_post_meta( $post->ID, 'tve_disable_theme_dependency' );

					}
				}, 11 );
			}
		}

		add_filter( 'thrive_template_structure', static function ( $structure ) {
			return TCB_Utils::wrap_content( $structure, 'div', '', 'content-wrap' );
		} );

		add_filter( 'thrive_theme_display_css', static function ( $display ) {
			/* We do not need to show the theme css on the wizard setup complete page */
			if ( ! empty( $_REQUEST['ttb-wizard']['step'] ) && $_REQUEST['ttb-wizard']['step'] === '__welcome' ) {
				$display = false;
			}

			return $display;
		} );

		add_action( 'wp_enqueue_scripts', [ $this, 'dequeue_conflicting_scripts' ], PHP_INT_MAX );
	}

	/**
	 * If a different header / footer should be rendered, output that instead
	 *
	 * @param array  $hf_data
	 * @param string $type
	 *
	 * @return array
	 */
	public function maybe_render_custom_hf( $hf_data, $type ) {
		if ( $this->step() === $type && $this->request( 'template_id' ) ) {
			/* get the default html / css content from the cloud */
			$content = $this->get_hf_preview_content( $type, $this->request( 'template_id' ) );

			if ( $content ) {
				$hf_data['id']      = '';
				$hf_data['content'] = $content;
			}
		}

		return $hf_data;
	}

	/**
	 * Get CSS + content from a cloud template
	 *
	 * @param string $type
	 * @param string $cloud_id
	 *
	 * @return string
	 */
	public function get_hf_preview_content( $type, $cloud_id ) {
		$template = tve_get_cloud_template_data( $type, [
			'id'   => $cloud_id,
			'type' => $type,
		] );

		if ( ! is_array( $template ) ) {
			return '';
		}
		$template['head_css'] = str_replace( '|TEMPLATE_ID|', $cloud_id, $template['head_css'] );

		/**
		 * Add CSS classes so that styles are applied
		 */
		add_filter( 'thrive_hf_class', function ( $classes ) use ( $cloud_id ) {
			$classes [] = 'thrv_symbol_' . $cloud_id;

			return $classes;
		} );

		return '<style type="text/css">' . $template['head_css'] . '</style>' . $template['content'];
	}

	/**
	 * Hide the header in some steps of the wizard
	 *
	 * @param bool $show
	 *
	 * @return bool
	 */
	public function should_render_header( $show ) {
		switch ( $this->step() ) {
			case '__welcome':
			case THRIVE_HEADER_SECTION:
				/* in the "Choose header" step, only render header if the user is currently previewing a header */
				$show = (bool) $this->request( 'template_id', false );
				break;
			case THRIVE_FOOTER_SECTION:
			case 'logo':
			case 'color':
			case 'menu':
			case 'woo':
				$show = false;
				break;
			case 'homepage':
				$show = (bool) $this->request( 'type' );
				break;
			case 'post':
			case 'page':
			case 'blog':
				$show = (bool) thrive_skin()->get_default_data( THRIVE_HEADER_SECTION );
				break;
		}

		return $show;
	}

	/**
	 * Hide the footer in some steps of the wizard
	 *
	 * @param bool $show
	 *
	 * @return bool
	 */
	public function should_render_footer( $show ) {
		switch ( $this->step() ) {
			case '__welcome':
			case THRIVE_HEADER_SECTION:
			case 'logo':
			case 'color':
			case 'menu':
			case 'woo':
				$show = false;
				break;
			case THRIVE_FOOTER_SECTION:
				/* in the "Choose footer" step, only render if the user is currently previewing a footer */
				$show = (bool) $this->request( 'template_id', false );
				break;
			case 'homepage':
				$show = (bool) $this->request( 'type' );
				break;
		}

		return $show;
	}

	/**
	 * Append default header and footer to the content
	 *
	 * @param $content
	 *
	 * @return string
	 */
	public static function append_header_footer_to_lp( $content ) {
		$header_id = thrive_skin()->get_default_data( THRIVE_HEADER_SECTION );
		$footer_id = thrive_skin()->get_default_data( THRIVE_FOOTER_SECTION );

		if ( ! empty( $header_id ) ) {
			$header = ( new Thrive_HF_Section( $header_id, THRIVE_HEADER_SECTION ) )->render();
		} else {
			$header = '';
		}

		if ( ! empty( $footer_id ) ) {
			$footer = ( new Thrive_HF_Section( $footer_id, THRIVE_FOOTER_SECTION ) )->render();
		} else {
			$footer = '';
		}

		return $header . $content . $footer;
	}

	/**
	 * Don't render page structure during some steps of the wizard
	 *
	 * @param bool $show
	 *
	 * @return bool
	 */
	public function should_render_structure( $show ) {
		$step = $this->step();

		switch ( $step ) {
			case '__welcome':
			case THRIVE_HEADER_SECTION:
			case THRIVE_FOOTER_SECTION:
			case 'logo':
			case 'color':
			case 'menu':
				$show = false;
				break;
			case 'post':
			case 'blog':
			case 'page':
				$show = (bool) $this->request( 'template_id', false );
				break;
			case 'homepage':
				$show = (bool) $this->request( 'type' );
				break;
		}

		return apply_filters( 'thrive_theme_wizard_render_structure', $show, $step );
	}

	/**
	 * Output header placeholder
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	public function header_placeholder( $content = '' ) {
		if ( ! $this->should_render_header( true ) ) {
			/* output a header placeholder */
			$content .= Thrive_Utils::return_part( '/integrations/wizard/views/placeholder-header.php' );
		}

		return $content;
	}

	/**
	 * Output structure and footer placeholders
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	public function structure_footer_placeholder( $content = '' ) {
		if ( ! $this->should_render_structure( true ) ) {
			/* output a structure placeholder */
			/* first, try custom structure */
			switch ( $this->step() ) {
				case 'post':
					$content .= Thrive_Utils::return_part( '/integrations/wizard/views/placeholder-structure-post.php' );
					break;
				case 'blog':
					$content .= Thrive_Utils::return_part( '/integrations/wizard/views/placeholder-structure-blog.php' );
					break;

				default:
					$content .= Thrive_Utils::return_part( '/integrations/wizard/views/placeholder-structure.php' );
					break;
			}
		}
		if ( ! $this->should_render_footer( true ) ) {
			/* output a footer placeholder */
			$content .= Thrive_Utils::return_part( '/integrations/wizard/views/placeholder-footer.php' );
		}

		return $content;
	}

	/**
	 * Filter current template's CSS based on current wizard step
	 * Removes CSS that should not be displayed when previewing a header / footer template (those come with their own styles)
	 *
	 * @param string $css CSS style node
	 *
	 * @return string
	 */
	public function filter_css( $css ) {
		$step = $this->step();

		if ( $step === THRIVE_HEADER_SECTION || $step === THRIVE_FOOTER_SECTION ) {
			$css = str_replace( ".thrv_{$step}", '.thrv_header_pending', $css );
		}

		return $css;
	}

	/**
	 * Frontend wizard page localization
	 *
	 * @return array
	 */
	public function localize_frontend() {
		$data = [ 'step' => $this->step() ];

		/* get next and previous pages when previewing a page as homepage */
		$current_page_id = $this->request( 'page_id' );
		if ( $current_page_id && $data['step'] === 'homepage' ) {
			$data['next_page'] = Thrive_Theme_DB::instance()->get_wizard_adjacent_page( $current_page_id, 'next' );
			$data['prev_page'] = Thrive_Theme_DB::instance()->get_wizard_adjacent_page( $current_page_id, 'prev' );
		}

		return $data;
	}

	/**
	 * Get the ID of the first post with post type $post_type. If nothing found, it generates a demo post and returns its ID.
	 *
	 * @param string $post_type
	 * @param array  $query_args allows more control over the searched posts. if 'post_type' is sent, it will overwrite the $post_type variable
	 *
	 * @return int
	 */
	public static function get_post_or_demo_content_id( $post_type = 'post', $query_args = [] ) {
		$query_args = wp_parse_args( $query_args, [
			'post_type'        => $post_type,
			'post_status'      => 'publish',
			'tax_query'        => Thrive_Utils::get_post_format_tax_query( '' ),
			'meta_query'       => Thrive_Utils::meta_query_no_landing_pages(),
			'exclude'          => [
				get_option( 'page_for_posts' ),
				get_option( 'page_on_front' ),
				Thrive_Defaults::get_default_post_id( 'blog' ),
				Thrive_Defaults::get_default_post_id( 'homepage', 'Generated Homepage' ),
			],
			'numberposts'      => 1,
			'suppress_filters' => false,
		] );

		$query_args['fields'] = 'ids'; // always get IDs

		Thrive_Theme_DB::add_post_content_length_filter( $post_type, 1500 );

		$post_ids = get_posts( $query_args );
		if ( ! empty( $post_ids ) ) {
			$post_id = $post_ids[0];
		} else {
			$post    = Thrive_Demo_Content::get_one();
			$post_id = $post ? $post->ID : 0;
		}

		return $post_id;
	}

	/**
	 *
	 * @return array
	 * @throws Exception
	 */
	public static function localize_admin() {
		/* pages that are automatically suggested for the homepage "PAGE" option */
		$suggested_pages = static::autocomplete_pages();

		$blog_url = get_home_url();

		if ( get_option( 'show_on_front' ) === 'page' ) {
			$page_for_posts = get_option( 'page_for_posts' );

			if ( ! $page_for_posts ) {
				/* ensure that a "Blog" page exists */
				$page_for_posts = Thrive_Defaults::get_default_post_id( 'blog' );
				update_option( 'page_for_posts', $page_for_posts );
			}

			$blog_url = get_permalink( $page_for_posts );
		}

		/**
		 * Preload these templates to allow faster wizard navigation
		 */
		$templates       = [
			'post' => static::get_templates( 'post' ),
			'blog' => static::get_templates( 'blog' ),
			'page' => static::get_templates( 'page' ),
		];
		$completion_data = static::get_completion_data();

		/**
		 * Fill in a default template ID so that the preview is loaded directly in the wizard, without loading a placeholder page
		 */
		foreach ( $templates as $type => $template_list ) {
			if ( ! empty( $template_list ) ) {
				/* synchronize wizard selected IDs with the currently selected default templates - by default take the first template */
				$default_template_id = $template_list[0]['id'];

				/* get the ID of the default template */
				$completion_data['settings'][ $type ]['template_id'] = array_reduce( $template_list, static function ( $current_id, $template ) {
					if ( $template['default'] && 'standard' === $template['format'] ) {
						$current_id = $template['id'];
					}

					return $current_id;
				}, $default_template_id );
			}
		}

		/**
		 * Fill in a default page_id/title/url for the homepage "PAGE" option so that the preview is loaded immediately
		 */
		if ( empty( $completion_data['settings']['homepage']['page_id'] ) && ! empty( $suggested_pages ) ) {
			$page                                    = reset( $suggested_pages );
			$completion_data['settings']['homepage'] += [
				'page_id'    => $page['id'],
				'page_url'   => $page['url'],
				'page_title' => $page['label'],
			];
		}

		$wizard_structure = include THEME_PATH . '/integrations/wizard/structure.php';

		return [
			'data'          => $completion_data,
			'suggest_pages' => $suggested_pages,
			'urls'          => apply_filters( 'thrive_theme_wizard_urls', [
				'home'               => home_url( '', is_ssl() ? 'https' : null ),
				'post'               => Thrive_Utils::ensure_https( get_permalink( static::get_post_or_demo_content_id() ) ),
				'blog'               => Thrive_Utils::ensure_https( $blog_url ),
				'page'               => Thrive_Utils::ensure_https( get_permalink( static::get_post_or_demo_content_id( 'page' ) ) ),
				'draft_homepage'     => Thrive_Utils::ensure_https( get_permalink( Thrive_Defaults::get_default_post_id( 'homepage_draft', 'Generated Homepage (Draft)', Thrive_Demo_Content::PAGE_TYPE ) ) ),
				'homepage_architect' => tcb_get_editor_url( get_option( 'page_on_front' ) ),
			] ),
			'templates'     => $templates,
			'is_completed'  => ! empty( $completion_data['done'] ) && count( $completion_data['done'] ) === count( static::get_active_steps() ),
			'active_steps'  => static::get_active_steps(),
			'structure'     => apply_filters( 'thrive_theme_wizard_structure', $wizard_structure ),
		];
	}

	/**
	 * Get autocomplete search results for pages (used in homepage wizard step)
	 *
	 * @param array $query_args WP's get_posts()-compatible arguments array
	 *
	 * @return array
	 */
	public static function autocomplete_pages( $query_args = [] ) {

		$is_search = ! empty( $query_args['s'] );
		$exclude   = ! empty( $query_args['exclude'] ) ? $query_args['exclude'] : [];

		$defaults = [
			'post_status' => 'publish',
			'post_type'   => 'page',
			'numberposts' => 10,
			'offset'      => 0,
			'order'       => $is_search ? 'ASC' : 'DESC',
			'orderby'     => $is_search
				? 'relevance'
				: [
					'post_modified' => 'DESC',
					'ID'            => 'DESC',
				],
			's'           => '',
			'exclude'     => array_merge(
				$exclude,
				[
					get_option( 'page_for_posts' ),
					Thrive_Defaults::get_default_post_id( 'blog' ),
					Thrive_Defaults::get_default_post_id( 'homepage', 'Generated Homepage' ),
				]
			),
		];

		$query_args = wp_parse_args( $query_args, $defaults );

		/**
		 * Exclude various pages from the query
		 */
		$query_args = Thrive_Utils::filter_default_get_posts_args( $query_args, 'wizard_pages' );

		return array_map( static function ( $page ) {
			return [
				'id'    => $page->ID,
				'value' => $page->ID,
				'label' => $page->post_title,
				'lp'    => (bool) $page->tve_landing_page,
				'url'   => Thrive_Utils::ensure_https( get_permalink( $page ) ),
			];
		}, get_posts( $query_args ) );
	}

	/**
	 * Get completion data for the current skin.
	 * Makes sure settings are up-to-date. (e.g. homepage options are correlated with the wordpress ones)
	 *
	 * @return array|stdClass
	 */
	public static function get_completion_data() {
		$wizard_data = thrive_skin()->get_meta( 'ttb_wizard' );

		if ( empty( $wizard_data ) || ! is_array( $wizard_data ) ) {
			$wizard_data = [];
		}

		/* make sure there's no residual data here */
		unset( $wizard_data['data'] );

		/* make sure some keys exist in the settings */
		if ( ! isset( $wizard_data['settings']['homepage'] ) ) {
			$wizard_data['settings']['homepage'] = [
				'type' => '',
			];
		}

		if ( isset( $wizard_data['settings'] ) ) {
			$settings = &$wizard_data['settings'];
		}

		if ( ! empty( $settings['homepage']['type'] ) ) {
			/* align with WP options */
			$show_on_front = get_option( 'show_on_front' );
			$page_id       = get_option( 'page_on_front' );

			if ( 'posts' === $show_on_front || empty( $page_id ) ) {
				$settings['homepage'] = [ 'type' => 'blog' ]; // no extra setting needed here.
			} else {
				if ( isset( $settings['homepage']['template_id'] ) ) {
					$settings['homepage']['type'] = 'template';
				} else {
					$settings['homepage']['type'] = 'page';
				}
				$id   = get_option( 'page_on_front' );
				$page = get_post( $id );
				if ( $id && $page ) {
					$settings['homepage']['page_id']         = $id;
					$settings['homepage']['page_title']      = $page->post_title;
					$settings['homepage']['page_url']        = get_permalink( $page );
					$settings['homepage']['is_landing_page'] = (bool) tve_post_is_landing_page( $id );
				} else {
					unset( $settings['homepage']['page_id'], $settings['homepage']['page_title'], $settings['homepage']['is_landing_page'] );
				}
			}

			if ( $settings['homepage']['type'] === 'template' ) {
				unset( $settings['homepage']['page_id'], $settings['homepage']['page_title'], $settings['homepage']['page_url'], $settings['homepage']['is_landing_page'] );
			} elseif ( $settings['homepage']['type'] === 'page' ) {
				unset( $settings['homepage']['template_id'] );
			}
		}

		/* make sure the "done" steps are always valid */
		if ( isset( $wizard_data['done'] ) ) {
			$wizard_data['done'] = array_filter( $wizard_data['done'] );
		}

		$wizard_data['activeIndex'] = isset( $wizard_data['activeIndex'] ) ? (int) $wizard_data['activeIndex'] : - 1;

		/* Backwards compat for the menu settings - un-inspired name "id", we need to change that to "header", because it applies to headers */
		if ( isset( $wizard_data['settings']['menu']['id'] ) ) {
			$wizard_data['settings']['menu'] = [
				'header' => $wizard_data['settings']['menu']['id'],
			];
		}

		return $wizard_data;
	}

	/**
	 * Get templates for a step in the wizard
	 *
	 * @param string $type wizard step (type)
	 *
	 * @return array
	 * @throws Exception
	 */
	public static function get_templates( $type ) {
		/* fetch cloud templates just so that we can use the thumbnails from them */
		switch ( $type ) {
			case 'homepage':
				$filters = [
					'primary'   => THRIVE_HOMEPAGE_TEMPLATE,
					'secondary' => THRIVE_PAGE_TEMPLATE,
				];
				break;
			case 'post':
				$filters = [
					'primary'   => THRIVE_SINGULAR_TEMPLATE,
					'secondary' => THRIVE_POST_TEMPLATE,
				];
				break;
			case 'blog':
				$filters = [
					'primary'   => THRIVE_HOMEPAGE_TEMPLATE,
					'secondary' => THRIVE_BLOG_TEMPLATE,
				];
				break;
			case 'page':
				$filters = [
					'primary'   => THRIVE_SINGULAR_TEMPLATE,
					'secondary' => THRIVE_PAGE_TEMPLATE,
				];
				break;
			default:
				$filters = [];
				break;
		}

		$filters['skin_tag'] = thrive_skin()->get_tag();

		/**
		 * Change the filters before the cloud request
		 *
		 * @param array  $filters
		 * @param string $type
		 */
		$filters = apply_filters( 'thrive_theme_wizard_templates_filters', $filters, $type );
		/* get template preview images from cloud */
		try {
			$cloud_data = Thrive_Theme_Cloud_Api_Factory::build( 'templates' )->get_items( [ 'filters' => $filters ] );
		} catch ( Exception $e ) {
			$cloud_data = [];
		}

		$local_templates = array_values( array_filter( array_map( static function ( $template ) use ( &$cloud_data ) {
			$thumb = isset( $cloud_data[ $template->tag ]['thumb'] ) && $cloud_data[ $template->tag ]['thumb']['w'] ? $cloud_data[ $template->tag ]['thumb'] : $template->thumbnail();

			if ( ! empty( $thumb['url'] ) ) {
				$thumb['url'] = Thrive_Utils::ensure_https( $thumb['url'] );
				unset( $cloud_data[ $template->tag ] ); //unset this to not have it twice when we will merge the local and cloud templates
			}

			/** @var Thrive_Template $template */
			return [
				'id'         => $template->ID,
				'post_title' => $template->post_title,
				'thumb'      => $thumb,
				'default'    => (bool) $template->default,
				'format'     => $template->format,
				'source'     => 'local',
			];

		}, thrive_skin()->get_templates( 'object', false, $filters ) ) ) );


		$cloud_templates = array_values( array_map( static function ( $template ) {
			return [
				'id'         => $template['id'],
				'post_title' => $template['post_title'],
				'thumb'      => $template['thumb'],
				'default'    => 0,
				'format'     => 'standard',
				'source'     => 'cloud',
			];
		}, $cloud_data ) );

		/**
		 * Merge local and cloud templates in order for the user to have them all
		 */
		return array_merge( $local_templates, $cloud_templates );
	}

	/**
	 * Return a template based on a tag
	 * If the template doesn't exists locally, we will download it from the cloud and save it locally
	 *
	 * @param string $tag
	 *
	 * @return Thrive_Template
	 * @throws Exception
	 */
	public function get_template_by_tag( $tag ) {
		$posts = get_posts(
			[
				'post_type'   => THRIVE_TEMPLATE,
				'meta_key'    => 'tag',
				'meta_value'  => $tag,
				'tax_query'   => [ thrive_skin()->build_skin_query_params() ],
				'numberposts' => 1,
			]
		);

		/* If the template doesn't exist, just download and save it from the cloud */
		if ( empty( $posts ) ) {
			$id = Thrive_Theme_Cloud_Api_Factory::build( 'templates' )->download_item( $tag );
		} else {
			$id = $posts[0];
		}

		return new Thrive_Template( $id );
	}

	/**
	 * Dequeue various javascript that's preventing the wizard iframe from functioning properly
	 */
	public function dequeue_conflicting_scripts() {
		/**
		 * Membermouse scripts doing some unspeakable stuff in frontend
		 */
		wp_dequeue_script( 'membermouse-blockUI' );
		wp_deregister_script( 'membermouse-blockUI' );
		wp_dequeue_script( 'mm-common-core.js' );
		wp_deregister_script( 'mm-common-core.js' );
		wp_dequeue_script( 'mm-preview.js' );
		wp_deregister_script( 'mm-preview.js' );
		wp_dequeue_script( 'membermouse-socialLogin' );
		wp_deregister_script( 'membermouse-socialLogin' );
	}
}

/**
 * Get wizard instance
 *
 * @return Thrive_Wizard
 */
function thrive_wizard() {

	static $thrive_wizard;
	if ( null === $thrive_wizard ) {
		$thrive_wizard = new Thrive_Wizard();
	}

	return $thrive_wizard;
}

/* always instantiate */
thrive_wizard();
