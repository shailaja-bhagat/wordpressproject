<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

use Thrive\Theme\Integrations\WooCommerce\Main as WooMain;

/**
 * Class Thrive_Skin
 */
class Thrive_Skin {
	/**
	 * Use general singleton methods
	 */
	use Thrive_Singleton;

	/**
	 * Use the shortcuts for term meta setters and getters
	 */
	use Thrive_Term_Meta;

	/**
	 * Meta to know that a skin is active
	 */
	const SKIN_META_ACTIVE = 'is_active';

	const BREADCRUMBS_LABELS = 'breadcrumbs_labels';

	const DEFAULT_LAYOUT = 'default_layout';

	/**
	 * Meta name for skin palettes
	 */
	const SKIN_META_PALETTES = 'palettes';

	/**
	 * Meta name for skin variables
	 */
	const SKIN_META_VARIABLES = 'variables';

	/* Skin tag */
	const TAG = 'tag';

	/**
	 * Name of the default skin
	 */
	const DEFAULT_SKIN = 'Default Theme';

	/**
	 * Meta fields needed when listing skins
	 */
	const META_FIELDS = [
		self::SKIN_META_ACTIVE,
		self::TAG,
		self::DEFAULT_LAYOUT,
		self::SKIN_META_PALETTES,
		self::SKIN_META_VARIABLES,
		self::BREADCRUMBS_LABELS,
	];

	/**
	 * @var array|false|WP_Term
	 */
	public $term;
	/**
	 * Term id
	 *
	 * @var int
	 */
	public $ID;

	public static $meta_fields
		= [
			'is_active'                         => 0,
			'default_layout'                    => 0,
			'tag'                               => 0,
			'palettes'                          => [], //Skin Palettes
			'variables'                         => [], //Skin Variables
			WooMain::GENERATED_TEMPLATES_OPTION => 0,
		];

	/**
	 * Thrive_Skin constructor.
	 *
	 * @param $skin_id
	 */
	public function __construct( $skin_id ) {

		$this->set_term( $skin_id );

		if ( is_wp_error( $this->term ) || empty( $this->term ) ) {
			$this->ID = 0;
		} else {
			$this->ID = $this->term->term_id;
		}
	}

	/**
	 * Set the term to be used in this instance
	 *
	 * @param $skin_id
	 */
	public function set_term( $skin_id = 0 ) {
		if ( empty( $skin_id ) ) {
			//get active skin
			$active_skin = get_terms( [
				'taxonomy'   => SKIN_TAXONOMY,
				'meta_query' => [
					[
						'key'     => static::SKIN_META_ACTIVE,
						'value'   => 1,
						'compare' => '=',
					],
				],
				'hide_empty' => 0,
			] );

			$this->term = is_wp_error( $active_skin ) || empty( $active_skin ) ? [] : $active_skin[0];
		} else {
			$this->term = Thrive_Skin_Taxonomy::get_skin_by_id( $skin_id );
		}
	}

	/**
	 * Checks if the active skin is the default skin
	 *
	 * @return bool
	 */
	public function is_default() {
		return $this->get_tag() === 'default';
	}

	/**
	 * Get active typography for the skin
	 *
	 * @return mixed
	 */
	public function get_active_typography() {
		$typography_id = 0;

		$posts = get_posts( [
			'post_type'      => THRIVE_TYPOGRAPHY,
			'posts_per_page' => 1,
			'meta_query'     => [
				[
					'key'   => 'default',
					'value' => '1',
				],
			],
			'tax_query'      => [ $this->build_skin_query_params() ],
		] );

		if ( ! empty( $posts ) ) {
			$typography_id = $posts[0]->ID;
		}

		return $typography_id;
	}

	/**
	 * Get skin with meta fields
	 *
	 * @return mixed
	 */
	public function export() {
		/**
		 * @var Thrive_Skin $skin
		 */
		$skin = get_term( $this->ID, SKIN_TAXONOMY, ARRAY_A );

		foreach ( self::$meta_fields as $meta_key => $meta_field ) {
			$skin['term_meta'][ $meta_key ] = get_term_meta( $this->ID, $meta_key, true );
		}

		return $skin;
	}

	/**
	 * Build query params for getting templates based on skin
	 *
	 * @return array
	 */
	public function build_skin_query_params() {
		return [
			'taxonomy' => SKIN_TAXONOMY,
			'field'    => 'id',
			'terms'    => $this->ID,
		];
	}

	/**
	 * Copy all the meta fields from the target skin to the current skin.
	 *
	 * @param $source_skin_id
	 */
	public function duplicate_meta( $source_skin_id ) {
		$metas = get_term_meta( $source_skin_id );

		foreach ( self::META_FIELDS as $meta_field ) {
			if ( ! empty( $metas[ $meta_field ] ) ) {
				$value = maybe_unserialize( $metas[ $meta_field ][0] );
				$this->set_meta( $meta_field, $value );
			}
		}
	}

	/**
	 * Duplicate the templates from the current skin and assigned them to the other one
	 *
	 * @param int $source_skin_id
	 */
	public function duplicate_templates( $source_skin_id ) {
		$source_skin = new Thrive_Skin( $source_skin_id );
		$templates   = $source_skin->get_templates();

		$layout_map = [];

		foreach ( $templates as $data ) {
			$source_id = $data['ID'];
			unset( $data['ID'], $data['meta_input']['tag'] );

			$template_id = wp_insert_post( $data );
			$template    = new Thrive_Template( $template_id );

			$template->copy_data_from( $source_id );
			$template->assign_to_skin( $this->ID );

			$source_template_layout = get_post_meta( $source_id, 'layout', true );
			/* create a copy for the layout so we can assign it to the new template */
			if ( empty( $layout_map[ $source_template_layout ] ) ) {
				$layout_map[ $source_template_layout ] = Thrive_Layout::create( $this->ID, $source_template_layout );
			}

			$template->set_meta( 'layout', $layout_map[ $source_template_layout ] );
		}

		$default_source_skin_layout = $source_skin->get_default_layout();
		/* default skin layout is either the copy of the one from the other skin, or one from the newly created, or just a default one */
		if ( empty( $layout_map[ $default_source_skin_layout ] ) ) {
			if ( empty( $layout_map ) ) {
				$default_layout_id = Thrive_Layout::create( $this->ID );
			} else {
				$default_layout_id = array_pop( $layout_map );
			}
		} else {
			$default_layout_id = $layout_map[ $default_source_skin_layout ];
		}

		$this->set_meta( static::DEFAULT_LAYOUT, $default_layout_id );
	}

	/**
	 * Get templates all the data from a certain skin
	 *
	 * @param       $output  string
	 * @param       $default bool
	 * @param array $filters allow filtering results (just in case of $output !== 'ids')
	 *
	 * @return array
	 */
	public function get_templates( $output = 'array', $default = false, $filters = [] ) {
		$args = [
			'post_type'      => THRIVE_TEMPLATE,
			'posts_per_page' => - 1,
			'tax_query'      => [ $this->build_skin_query_params() ],
		];

		if ( $default ) {
			$args['meta_query'] = [
				[
					'key'   => 'default',
					'value' => '1',
				],
			];
		}

		array_map( static function ( $key ) use ( &$filters ) {
			if ( isset( $filters[ $key ] ) ) {
				/* make sure these are always arrays */
				$filters[ $key ] = (array) $filters[ $key ];
			}
		}, [ 'primary', 'secondary', 'variable' ] );

		$posts = get_posts( $args );

		return array_filter( array_map( function ( $post ) use ( $output, $filters ) {
			if ( 'ids' === $output ) {
				$template = $post->ID;
			} else {
				$template = new Thrive_Template( $post->ID );

				if ( isset( $filters['primary'] ) && ! in_array( $template->primary_template, $filters['primary'], true ) ) {
					return false; // so it gets filtered out
				}

				if ( isset( $filters['secondary'] ) && ! in_array( $template->secondary_template, $filters['secondary'], true ) ) {
					return false; // so it gets filtered out
				}

				if ( isset( $filters['variable'] ) && ! in_array( $template->variable_template, $filters['variable'], true ) ) {
					return false; // so it gets filtered out
				}

				if ( 'array' === $output ) {
					$template = $template->export();
				}
			}

			return $template;
		}, $posts ) );
	}

	/**
	 * Get all the sections assigned to the skin
	 *
	 * @param array  $args
	 * @param string $output
	 *
	 * @return array
	 */
	public function get_sections( $args = [], $output = 'array' ) {

		$args = array_merge_recursive( [
			'post_type'      => THRIVE_SECTION,
			'posts_per_page' => - 1,
			'tax_query'      => [ $this->build_skin_query_params() ],
		], $args );

		$posts = get_posts( $args );

		return array_map( static function ( $post ) use ( $output ) {
			if ( $output === 'ids' ) {
				$section = $post->ID;
			} else {
				$section = new Thrive_Section( $post->ID );
				if ( $output === 'array' ) {
					$section = [
						'id'    => $section->ID,
						'name'  => $section->name(),
						'type'  => $section->type(),
						'thumb' => $section->thumbnail(),
					];
				}
			}

			return $section;
		}, $posts );
	}

	/**
	 * Get all the layouts assigned to the skin
	 *
	 * @param string $output
	 * @param array  $meta_fields
	 *
	 * @return array
	 */
	public function get_layouts( $output = 'array', $meta_fields = [] ) {
		$posts = get_posts( [
			'post_type'      => THRIVE_LAYOUT,
			'posts_per_page' => - 1,
			'tax_query'      => [ $this->build_skin_query_params() ],
		] );

		return array_map( static function ( $post ) use ( $output, $meta_fields ) {
			if ( $output === 'ids' ) {
				$layout = $post->ID;
			} else {
				$layout = new Thrive_Layout( $post->ID );

				if ( $output === 'array' ) {
					$layout = $layout->export( $meta_fields );
				}
			}

			return $layout;
		}, $posts );
	}

	/**
	 * Get all typographies for output
	 *
	 * @param string $output
	 *
	 * @return mixed
	 */
	public function get_typographies( $output = 'array' ) {

		$typographies = get_posts( [
			'post_type'      => THRIVE_TYPOGRAPHY,
			'posts_per_page' => - 1,
			'tax_query'      => [ $this->build_skin_query_params() ],
		] );

		/* used to fix cases where there are multiple active typography sets */
		$has_default_typography = false;

		return array_map( static function ( $post ) use ( $output, &$has_default_typography ) {

			if ( $output === 'ids' ) {
				$typography = $post->ID;
			} else {
				$typography = new Thrive_Typography( $post->ID );
				$is_default = $typography->is_default();
				/* cleanup - if an active typography already exists, this one should not be active */
				if ( $is_default && $has_default_typography ) {
					$is_default = false;
					$typography->set_meta( Thrive_Typography::META_DEFAULT, 0 );
				}

				if ( $output === 'array' ) {
					$typography = [
						'ID'          => $post->ID,
						'post_title'  => $post->post_title,
						'meta_input'  => [
							'default' => $is_default,
							'style'   => $typography->style(),
						],
						'edit_url'    => tcb_get_editor_url( $post->ID ),
						'preview_url' => tcb_get_preview_url( $post->ID ),
					];
				}
				if ( $is_default ) {
					$has_default_typography = true;
				}
			}

			return $typography;
		}, $typographies );
	}

	/**
	 * Get skin preview url
	 *
	 * @return string
	 */
	public function get_preview_url() {

		$url = add_query_arg( [
			THRIVE_NO_BAR       => 1,
			THRIVE_SKIN_PREVIEW => $this->ID,
		], home_url() );

		return $url;
	}

	/**
	 * Reset default template / typography / layout and remove the rest
	 */
	public function reset() {

		$templates    = $this->get_templates( 'object' );
		$typographies = $this->get_typographies( 'object' );

		foreach ( array_merge( $templates, $typographies ) as $post ) {
			if ( $post->is_default() ) {
				$post->reset();
			} else {
				wp_trash_post( $post->ID );
			}
		}
		$layout = new Thrive_Layout( $this->get_default_layout() );
		$layout->reset();

		/* remove wizard data */
		$this->delete_meta( 'thrive_defaults' );
		$this->delete_meta( 'ttb_wizard' );
	}

	/**
	 * Get all global styles specific for the current skin
	 *
	 * @return array
	 */
	public function get_global_styles() {
		$skin_styles = [];
		$tag         = $this->get_tag();

		$global_styles_options = [ 'tve_global_button_styles', 'tve_global_section_styles', 'tve_global_contentbox_styles' ];

		foreach ( $global_styles_options as $option ) {
			$styles = get_option( $option, [] );
			if ( ! empty( $styles ) && is_array( $styles ) ) {
				foreach ( $styles as $key => $value ) {
					if ( ! empty( $value['skin_tag'] ) && $value['skin_tag'] === $tag ) {
						$skin_styles[ $option ][ $key ] = $value;
					}
				}
			}
		}

		return $skin_styles;
	}

	/**
	 * Get all templates and typography and generate a new css file that will be used by the whole skin
	 */
	public function generate_style_file() {

		$style = thrive_css_helper( $this, [ 'default' => true ] )
			->generate_style( true, false, false ) // fonts are no longer included in the generated file
			->get_style();

		$templates_dir = UPLOAD_DIR_PATH . '/thrive/';

		if ( ! is_dir( $templates_dir ) ) {
			wp_mkdir_p( $templates_dir );
		}

		/* remove old template style files */
		foreach ( scandir( $templates_dir ) as $file ) {
			if ( strpos( $file, 'theme-template' ) === 0 ) {
				unlink( $templates_dir . $file );
			}
		}

		require_once( ABSPATH . 'wp-admin/includes/file.php' );

		global $wp_filesystem;

		WP_Filesystem();

		/* save file name with time included so it won't be cached */
		$filename = 'theme-template-' . time() . '.css';

		/**
		 * Filters the actual CSS string that's going to be saved in the file.
		 *
		 * @param string      $style    CSS full style string
		 * @param string      $filename Name of the file where this will be saved
		 * @param Thrive_Skin $this     thrive skin instance
		 *
		 * @return string filtered CSS style string
		 */
		$style = apply_filters( 'thrive_css_file_content', $style, $filename, $this );

		$wp_filesystem->put_contents( $templates_dir . $filename, $style, 0644 );

		update_option( THRIVE_TEMPLATE_STYLE, $filename, true );

		/* each time we generate a new css file we also have to clear the cache */
		Thrive_Utils::clear_cache();
	}

	/**
	 * Remove all templates and typographies
	 */
	public function remove() {
		$everything_related_to_skin = array_merge(
			$this->get_sections( [], 'ids' ),
			$this->get_templates( 'ids' ),
			$this->get_layouts( 'ids' ),
			$this->get_typographies( 'ids' )
		);

		foreach ( $everything_related_to_skin as $id ) {
			wp_delete_post( $id );
		}
	}

	/**
	 * Get the default layout for this skin. This one is automatically assigned when creating a new template
	 *
	 * @return mixed
	 */
	public function get_default_layout() {
		return $this->get_meta( static::DEFAULT_LAYOUT );
	}

	/**
	 * Get skin tag
	 *
	 * @return mixed
	 */
	public function get_tag() {
		return $this->get_meta( static::TAG );
	}

	/**
	 * Returns the Skin Palettes
	 *
	 * @return mixed
	 */
	public function get_palettes() {
		$palettes = $this->get_meta( static::SKIN_META_PALETTES );

		if ( empty( $palettes ) ) {
			$palettes = Thrive_Defaults::skin_pallets();
		}

		return $palettes;
	}

	/**
	 * Updates the skin palette meta with the corresponding value received as parameter
	 *
	 * @param array $palettes
	 */
	public function update_palettes( $palettes = [] ) {
		$this->set_meta( static::SKIN_META_PALETTES, $palettes );
	}

	/**
	 * Returns the skin variables
	 *
	 * @param bool $add_prefixes
	 *
	 * @return array
	 */
	public function get_variables( $add_prefixes = false ) {
		$variables = $this->get_meta( static::SKIN_META_VARIABLES );

		if ( empty( $variables ) ) {
			$variables = Thrive_Defaults::skin_variables();
		} else {
			foreach ( $variables as $key => $variable ) {
				if ( $key === 'gradients' ) {
					/**
					 * For the moment we do not support skin gradients
					 */
					$variable = [];
				}

				$variables[ $key ] = array_values( $variable );
			}
		}

		if ( $add_prefixes ) {
			$variables = array_merge( $variables, [
				'colors_prefix'    => THEME_SKIN_COLOR_VARIABLE_PREFIX,
				'gradients_prefix' => THEME_SKIN_GRADIENT_VARIABLE_PREFIX,
			] );
		}

		return $variables;
	}

	/**
	 * Constructs the variable structure for css output
	 *
	 * @return string
	 */
	public function get_variables_for_css() {
		$skin_variables = $this->get_variables();
		$data           = '';

		if ( ! empty( $skin_variables ) ) {
			foreach ( $skin_variables['colors'] as $color ) {
				$data .= THEME_SKIN_COLOR_VARIABLE_PREFIX . $color['id'] . ':' . $color['color'] . ';';
			}

			foreach ( $skin_variables['gradients'] as $gradient ) {
				$data .= THEME_SKIN_GRADIENT_VARIABLE_PREFIX . $gradient['id'] . ':' . $gradient['gradient'] . ';';
			}

			if ( ! empty( $skin_variables['colors'] ) && function_exists( 'tve_prepare_master_variable' ) ) {

				$master_variables = array_filter( $skin_variables['colors'], static function ( $ar ) {
					return ( isset( $ar['parent'] ) && (int) $ar['parent'] === - 1 );
				} );

				if ( ! empty( $master_variables ) ) {
					$master_variable = reset( $master_variables );

					$data .= tve_prepare_master_variable( $master_variable );
				}
			}
		}

		return $data;
	}


	/**
	 * Updates the skin variables and skin palettes with data that comes from cloud.
	 *
	 * It updates only if the variable or palette key doesn't exist.
	 * If doesn't override what the user has modified. It only brings new content and stores it in the system
	 *
	 * @param array $cloud_vars
	 * @param array $cloud_palettes
	 */
	public function update_missing_skin_variables_from_cloud( $cloud_vars = [], $cloud_palettes = [] ) {

		$existing_variables = $this->get_variables();
		$absent_variables   = [];

		if ( ! empty( $cloud_vars['colors'] ) && is_array( $cloud_vars['colors'] ) && ! empty( $existing_variables ) ) {

			foreach ( $cloud_vars['colors'] as $cloud_color_var ) {
				$cloud_color_id = (int) $cloud_color_var['id'];

				if ( ! in_array( $cloud_color_id, array_column( $existing_variables['colors'], 'id' ) ) ) {
					$absent_variables[] = $cloud_color_id;
				}
			}
		}

		if ( count( $absent_variables ) > 0 ) {
			$existing_palettes = $this->get_palettes();

			foreach ( $cloud_palettes as $c_palette_key => $c_palette ) {
				/* on older Theme versions, it seems these are not built correctly in some cases */
				if ( ! isset( $existing_palettes['original'][ $c_palette_key ] ) ) {
					$existing_palettes['original'][ $c_palette_key ]['colors'] = [];
				}
				if ( ! isset( $existing_palettes['modified'][ $c_palette_key ] ) ) {
					$existing_palettes['modified'][ $c_palette_key ]['colors'] = [];
				}
				/**
				 * Union for color palettes with the cloud data
				 */
				$existing_palettes['original'][ $c_palette_key ]['colors'] += $c_palette['colors'];
				$existing_palettes['modified'][ $c_palette_key ]['colors'] += $c_palette['colors'];
			}

			foreach ( $cloud_vars['colors'] as $key => $value ) {
				$cloud_vars['colors'][ $key ]['id'] = (int) $cloud_vars['colors'][ $key ]['id'];
			}

			$existing_variables['colors'] += $cloud_vars['colors'];

			$this->update_palettes( $existing_palettes );
			$this->update_variables( $existing_variables );
		}
	}


	/**
	 * Updates the skin variables
	 *
	 * @param array $variables
	 */
	public function update_variables( $variables = [] ) {
		$this->set_meta( static::SKIN_META_VARIABLES, $variables );
	}

	/**
	 * Filter templates based on the current active skin
	 *
	 * @param array  $templates
	 * @param string $type
	 *
	 * @return mixed
	 */
	public function filter_templates( $templates, $type = '' ) {

		$active_skin_tag = $this->get_tag();

		foreach ( $templates as $key => & $template ) {
			$order = 0; // this is used to place the theme templates first in the list
			if ( Thrive_Architect::is_light() ) {
				if ( empty( $template['unlocked'] ) ) {
					if ( ! empty( $template['skin_tag'] ) && $template['skin_tag'] !== $active_skin_tag ) {
						unset( $templates[ $key ] );
					} elseif ( empty( $type ) || ! Thrive_Utils::ct_type_is_unlocked_light_template( $type ) ) {
						$template['locked'] = 1;
						$order              = PHP_INT_MAX;
					}
				}
			} else {
				/* Here we just need to set theme templates to be the first on the list */
				if ( ! empty( $template['skin_tag'] ) ) {
					$order = - 1;
				}
			}

			$template['order'] = $order;

			// If the skin tag is set and different from the active skin tag => we do not show it at all
			if ( ! empty( $template['skin_tag'] && $template['skin_tag'] !== $active_skin_tag ) ) {
				unset( $templates[ $key ] );
			}
		}

		return $templates;
	}

	/**
	 * Filter landing pages based on their skin tag
	 *
	 * @param $landing_pages
	 *
	 * @return mixed
	 */
	public function filter_landing_pages( $landing_pages ) {
		$active_skin_tag = $this->get_tag();
		foreach ( $landing_pages as $key => & $template ) {
			$order = 0; // this is used to place the theme templates first in the list

			//if it's from current skin -> show it first and marked it as being part of the theme
			if ( $active_skin_tag === $template['skin_tag'] ) {
				$order                 = - 1;
				$template['from_skin'] = 1;
			} else {
				if ( $template['skin_tag'] !== '' ) {
					unset( $landing_pages[ $key ] );
				}
			}

			/* If TAR is not installed -> we show only the landing pages which are set as homepages or as silo */
			if ( Thrive_Architect::is_light() && $template['home'] !== '1' && $template['silo'] !== '1' ) {
				$landing_pages[ $key ]['locked'] = 1;
			}

			$template['order'] = $order;
		}

		return $landing_pages;
	}

	/**
	 * Get an array where the keys are each layout ID used on this skin, and the values are the names and edit URLs of the templates which use that layout.
	 *
	 * @return array
	 */
	public function get_layouts_templates_map() {
		$layouts_templates_map = [];

		foreach ( $this->get_templates( 'object' ) as $template ) {
			$layouts_templates_map[ (int) $template->get_layout() ][] = [
				'name'     => $template->post_title,
				'edit_url' => $template->edit_url(),
			];
		}

		return $layouts_templates_map;
	}

	/**
	 * Check for duplicate skin names and generates a unique one
	 *
	 * @param string $name
	 * @param int    $index
	 *
	 * @return string
	 */
	public static function generate_unique_name( $name = '', $index = 0 ) {
		$additional = '';
		if ( $index ) {
			$additional = ' ' . $index;
		}

		$same_name_skin = get_term_by( 'name', $name . $additional, SKIN_TAXONOMY );

		if ( ! empty( $same_name_skin ) ) {
			return static::generate_unique_name( $name, ++ $index );
		}

		return $name . $additional;
	}

	/**
	 * Breadcrumbs labels are saved at skin level
	 *
	 * @return array
	 */
	public function get_breadcrumbs_labels() {
		$labels         = $this->get_meta( static::BREADCRUMBS_LABELS );
		$default_labels = Thrive_Breadcrumbs::get_default_labels();

		return is_array( $labels ) ? array_merge( $default_labels, $labels ) : $default_labels;
	}

	/**
	 * Get the default header / footer WP_Post instance
	 *
	 * @param string $key
	 *
	 * @return int|mixed
	 */
	public function get_default_data( $key ) {
		$skin_defaults = $this->get_meta( 'thrive_defaults' ) ?: [];

		return isset( $skin_defaults[ $key ] ) ? $skin_defaults[ $key ] : 0;
	}

	/**
	 * Update the default meta for the skin
	 *
	 * @param string $key
	 * @param mixed  $value
	 *
	 * @return $this
	 */
	public function set_default_data( $key, $value ) {
		$skin_defaults         = $this->get_meta( 'thrive_defaults' ) ?: [];
		$skin_defaults[ $key ] = $value;

		$this->set_meta( 'thrive_defaults', $skin_defaults );

		return $this;
	}

	/**
	 * Generate a new tag for the skin
	 */
	public function regenerate_tag() {
		$tag = uniqid();
		$this->set_meta( static::TAG, $tag );

		return $tag;
	}
}

/**
 * Return a Thrive_Skin instance
 *
 * @param int $id
 *
 * @return Thrive_Skin
 */
function thrive_skin( $id = 0 ) {

	if ( empty( $id ) && Thrive_Utils::is_skin_preview() ) {
		$id = (int) $_GET[ THRIVE_SKIN_PREVIEW ];
	}

	return Thrive_Skin::instance_with_id( $id );
}
