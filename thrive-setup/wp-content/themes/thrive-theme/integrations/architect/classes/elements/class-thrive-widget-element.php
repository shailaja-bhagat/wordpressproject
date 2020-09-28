<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

/**
 * Class Thrive_Widget_Element
 */
class Thrive_Widget_Element extends Thrive_Theme_Element_Abstract {
	/**
	 * Name of the element
	 *
	 * @return string
	 */
	public function name() {
		return __( 'Widget', THEME_DOMAIN );
	}

	/**
	 * Wordpress element identifier
	 *
	 * @return string
	 */
	public function identifier() {
		return '.widget';
	}

	/**
	 * Component and control config
	 *
	 * @return array
	 */
	public function own_components() {
		$components = parent::own_components();

		$components['animation']  = [ 'hidden' => true ];
		$components['shadow']     = [ 'hidden' => true ];
		$components['responsive'] = [ 'hidden' => true ];

		$components['layout']['disabled_controls'] = [
			'Width',
			'Height',
			'Display',
			'Alignment',
			'.tve-advanced-controls',
			'hr',
		];

		return $components;
	}

	/**
	 * Hide this.
	 */
	public function hide() {
		return true;
	}

	/**
	 * This element has a selector
	 * @return bool
	 */
	public function has_selector() {
		return true;
	}

	/**
	 * No icons for the wrapper
	 * @return bool
	 */
	public function has_icons() {
		return false;
	}
}

return new Thrive_Widget_Element( 'thrive-widget' );
