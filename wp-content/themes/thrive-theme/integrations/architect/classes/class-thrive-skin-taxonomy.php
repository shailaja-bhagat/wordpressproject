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
 * Class Thrive_Skin_Taxonomy
 */
class Thrive_Skin_Taxonomy {

	/**
	 * @var array|false|WP_Term
	 */
	public $terms;

	/**
	 * Register taxonomy for skins
	 */
	public static function register_thrive_templates_tax() {

		$tax_labels = self::get_taxonomy_labels();

		register_taxonomy( SKIN_TAXONOMY, [ THRIVE_TEMPLATE ], [
			'hierarchical'      => false,
			'labels'            => $tax_labels,
			'show_ui'           => true,
			'show_in_nav_menus' => false,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => [ 'slug' => THRIVE_TEMPLATE ],
			'show_in_rest'      => true,
			'public'            => false,
		] );

		register_taxonomy_for_object_type( SKIN_TAXONOMY, THRIVE_TEMPLATE );
		register_taxonomy_for_object_type( SKIN_TAXONOMY, THRIVE_SECTION );
		register_taxonomy_for_object_type( SKIN_TAXONOMY, THRIVE_LAYOUT );
		register_taxonomy_for_object_type( SKIN_TAXONOMY, THRIVE_TYPOGRAPHY );
	}

	/**
	 * Get the labels for the taxonomy
	 *
	 * @return array
	 */
	public static function get_taxonomy_labels() {
		$default_labels = [
			'name'              => __( 'Skins', THEME_DOMAIN ),
			'singular_name'     => __( 'Skin', THEME_DOMAIN ),
			'search_items'      => __( 'Search Skins', THEME_DOMAIN ),
			'all_items'         => __( 'All Skins', THEME_DOMAIN ),
			'parent_item'       => __( 'Parent Skin', THEME_DOMAIN ),
			'parent_item_colon' => __( 'Parent Skin', THEME_DOMAIN ),
			'edit_item'         => __( 'Edit Skin', THEME_DOMAIN ),
			'update_item'       => __( 'Update Skin', THEME_DOMAIN ),
			'add_new_item'      => __( 'Add New Skin', THEME_DOMAIN ),
			'new_item_name'     => __( 'New Skin Name', THEME_DOMAIN ),
			'menu_name'         => __( 'Skins', THEME_DOMAIN ),
		];

		return apply_filters( 'thrive_templates_taxonomy_labels', $default_labels );
	}

	/**
	 * Make skin active
	 *
	 * @param int $term_id
	 */
	public static function set_skin_active( $term_id ) {

		//make the current skin inactive
		$current_skin = new Thrive_Skin( 0 );
		$current_skin->set_meta( Thrive_Skin::SKIN_META_ACTIVE, 0 );

		//set the new skin as active
		$new_skin = new Thrive_Skin( $term_id );
		$new_skin->set_meta( Thrive_Skin::SKIN_META_ACTIVE, 1 );

		Thrive_Template_Fallback::refresh();
	}

	/**
	 * Return all terms from the taxonomy
	 *
	 * @param string $output
	 *
	 * @return [WP_Term]|int|WP_Error
	 */
	public static function get_all( $output = 'object' ) {
		$terms = get_terms( [
			'taxonomy'   => SKIN_TAXONOMY,
			'hide_empty' => 0,
		] );

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			$terms = [];
		} else {
			$terms = array_map( function ( $term ) use ( $output ) {
				if ( $output === 'ids' ) {
					$term = $term->term_id;
				} else {
					$skin = new Thrive_Skin( $term->term_id );

					$term->preview_url = $skin->get_preview_url();
					foreach ( Thrive_Skin::META_FIELDS as $field ) {
						$term->$field = $skin->get_meta( $field );
					}
				}

				return $term;
			}, $terms );
		}

		return $terms;
	}

	/**
	 * Returns a skin with the specified id
	 *
	 * @param int $skin_id
	 *
	 * @return array|false|WP_Term
	 */
	public static function get_skin_by_id( $skin_id ) {
		return get_term_by( 'term_id', $skin_id, SKIN_TAXONOMY );
	}

	/**
	 * Get a list of skins from the cloud
	 *
	 * @return array
	 */
	public static function get_cloud_skins() {

		try {
			$skins = Thrive_Theme_Cloud_Api_Factory::build( 'skins' )->get_items();
		} catch ( Exception $e ) {
			$skins = $e->getMessage();
		}

		return $skins;
	}
}
