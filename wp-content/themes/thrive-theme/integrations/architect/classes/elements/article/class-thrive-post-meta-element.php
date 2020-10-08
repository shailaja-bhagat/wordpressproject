<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

/* Class Thrive_Post_Meta_Element */

class Thrive_Post_Meta_Element extends Thrive_Theme_Element_Abstract {

	/**
	 * Element name
	 * @return string
	 */
	public function name() {
		return __( 'Post Meta', THEME_DOMAIN );
	}

	/**
	 * @return string
	 */
	public function icon() {
		return 'post-meta';
	}

	/**
	 * @return string
	 */
	public function identifier() {
		return '.no_class_is_needed';
	}

	/**
	 * @return string
	 */
	public function html() {
		return Thrive_Utils::get_element( 'meta-element', [], false );
	}

	/**
	 * @return string
	 */
	public function category() {
		return TCB_Post_List::elements_group_label();
	}
}

return new Thrive_Post_Meta_Element( 'thrive_post_meta' );
