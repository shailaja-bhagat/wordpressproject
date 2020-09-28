<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

/**
 * Class Thrive_Theme_Main_Container_Element
 */
class Thrive_Theme_Main_Container_Element extends Thrive_Theme_Element_Abstract {
	/**
	 * Name of the element
	 *
	 * @return string
	 */
	public function name() {
		return __( 'Content Wrapper', THEME_DOMAIN );
	}

	/**
	 * WordPress element identifier
	 *
	 * @return string
	 */
	public function identifier() {
		return '.main-container';
	}

	/**
	 * Temporary hide this
	 */
	public function hide() {
		return true;
	}

	/**
	 * Check if the element has icons or not
	 *
	 * @return bool
	 */
	public function has_icons() {
		return false;
	}

	/**
	 * Add the theme section component
	 *
	 * @return array
	 */
	public function own_components() {
		$components = parent::own_components();

		$components['responsive'] = [ 'hidden' => true ];

		$components['main-container'] = [
			'config' => [
				'Position'          => [
					'config'  => [
						'name'    => __( 'Sidebar Position', THEME_DOMAIN ),
						'options' => [
							[
								'name'  => __( 'Right', THEME_DOMAIN ),
								'value' => 'right',
							],
							[
								'name'  => __( 'Left', THEME_DOMAIN ),
								'value' => 'left',
							],
						],
						'default' => 'right',
					],
					'extends' => 'Select',
				],
				'Wrap'              => [
					'config'  => [
						'name'    => '',
						'label'   => __( 'Wrap', THEME_DOMAIN ),
						'default' => false,
					],
					'extends' => 'Switch',
				],
				'Gutter'            => [
					'config'  => [
						'default' => '20',
						'min'     => '0',
						'max'     => '240',
						'label'   => __( 'Gutter Width', THEME_DOMAIN ),
						'um'      => [ 'px' ],
						'css'     => 'max-width',
					],
					'extends' => 'Slider',
				],
				'SidebarVisibility' => [
					'config'  => [
						'name'    => '',
						'label'   => __( 'Sidebar visibility', THEME_DOMAIN ),
						'default' => false,
					],
					'extends' => 'Switch',
				],
			],
		];

		return $components;
	}
}

return new Thrive_Theme_Main_Container_Element( 'main-container' );
