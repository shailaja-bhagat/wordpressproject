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
 * Class Thrive_Transfer_Skin
 */
class Thrive_Transfer_Skin extends Thrive_Transfer_Base {

	/**
	 * Json filename where to keep the data for the templates
	 *
	 * @var string
	 */
	public static $file = 'skins.json';

	/**
	 * Element key in the archive
	 *
	 * @var string
	 */
	protected $tag = 'skin';

	/**
	 * Skin object to handle skin related actions
	 *
	 * @var Thrive_Skin
	 */
	protected $skin;

	/**
	 * Read the skin data and all that is related with the skin
	 *
	 * @param int $id
	 *
	 * @return $this
	 */
	public function read( $id ) {
		$this->skin = thrive_skin( $id );
		$this->item = $this->skin->export();

		$content = '';
		$this->controller->process_templates( $this->get_templates() )
		                 ->process_sections( $this->get_sections() )
		                 ->process_typographies( $this->skin->get_typographies( 'object' ) )
		                 ->process_global_styles( $this->skin->get_global_styles() )
		                 ->process_symbols( $content );

		$this->replace_dynamic_items();

		return $this;
	}

	/**
	 * Get templates for export
	 *
	 * @return mixed|void
	 */
	public function get_templates() {
		return apply_filters( 'ttb_skin_export_templates', $this->skin->get_templates( 'ids' ) );
	}

	/**
	 * Get skin sections ids
	 *
	 * @return array
	 */
	public function get_sections() {
		return ( defined( 'THRIVE_THEME_SKIN_SECTIONS' ) && THRIVE_THEME_SKIN_SECTIONS ) ? thrive_skin()->get_sections( [], 'ids' ) : [];
	}

	/**
	 * Replace ids from the skin data with the corresponding hash
	 */
	public function replace_dynamic_items() {
		$layouts = $this->archive_data['layout'];

		foreach ( $layouts as $hash => $layout ) {
			if ( $this->item['term_meta']['default_layout'] === $layout['ID'] ) {
				$this->item['term_meta']['default_layout'] = $hash;
			}
		}
	}

	/**
	 * Add skin to the archive data
	 */
	public function add() {
		$this->archive_data['skin'] = $this->item;
	}

	/**
	 * Import skin from the archive
	 *
	 * @throws Exception
	 */
	public function import() {
		$this->save();

		$this->controller->import_templates()
		                 ->import_typographies()
		                 ->import_global_styles()
		                 ->import_symbols();

		$skin_term = $this->replace_data();

		if ( $skin_term && $skin_term->is_active ) {
			/* if we import a skin that becomes active, we also generate the css file */
			( new Thrive_Skin( $skin_term->term_id ) )->generate_style_file();
		}

		return $skin_term;
	}

	/**
	 * Replace dynamic data at import
	 *
	 * @return array|false|Thrive_Skin|WP_Term
	 */
	public function replace_data() {
		$items = array_merge( $this->archive_data['headers'], $this->archive_data['footers'], $this->archive_data['layout'] );

		/** @var Thrive_Skin $imported_skin */
		$skin = new Thrive_Skin( $this->archive_data['skin_id'] );
		foreach ( Thrive_Skin::$meta_fields as $meta_key => $meta_field ) {
			$default_meta = $skin->get_meta( $meta_key );

			if ( ! empty( $default_meta ) ) {

				if ( is_array( $default_meta ) ) {
					$skin->set_meta( $meta_key, $default_meta );
				} elseif ( ! empty( $items[ $default_meta ] ) ) {
					$skin->set_meta( $meta_key, $items[ $default_meta ] );
				}
			}
		}

		$imported_skin              = Thrive_Skin_Taxonomy::get_skin_by_id( $this->archive_data['skin_id'] );
		$imported_skin->is_imported = true;
		$imported_skin->is_active   = get_term_meta( $this->archive_data['skin_id'], Thrive_Skin::SKIN_META_ACTIVE, true );

		return $imported_skin;
	}

	/**
	 * Save skin in the db
	 *
	 * @throws Exception
	 */
	public function save() {
		if ( ! empty( $this->data ) ) {
			$name = Thrive_Skin::generate_unique_name( $this->data['name'] );

			$new_skin_term = wp_insert_term( $name, SKIN_TAXONOMY );

			if ( is_wp_error( $new_skin_term ) ) {
				throw new Exception( 'The theme import failed' );
			}

			$skin_id  = $new_skin_term['term_id'];
			$new_skin = new Thrive_Skin( $skin_id );

			foreach ( Thrive_Skin::$meta_fields as $meta_key => $meta_field ) {
				if ( ! empty( $this->data['term_meta'][ $meta_key ] ) ) {
					$new_skin->set_meta( $meta_key, $this->data['term_meta'][ $meta_key ] );
				}
			}

			//At first is active is 0 when a skin is just imported
			$is_active = 0;

			//If the active skin is the default one -> then we will make the just imported skin to be active
			if ( thrive_skin()->get_tag() === 'default' ) {
				thrive_skin()->set_meta( Thrive_Skin::SKIN_META_ACTIVE, 0 );
				$is_active = 1;
			}

			$new_skin->set_meta( Thrive_Skin::SKIN_META_ACTIVE, $is_active );
		} else {
			$active_skin = thrive_skin();
			if ( ! empty( $active_skin ) ) {
				$skin_id = $active_skin->ID;
			}
		}

		$this->archive_data['skin_id'] = $skin_id;
	}
}
