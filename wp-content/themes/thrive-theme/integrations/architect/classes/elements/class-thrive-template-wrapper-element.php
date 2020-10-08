<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

/**
 * Class Thrive_Template_Wrapper_Element
 */
class Thrive_Template_Wrapper_Element extends Thrive_Theme_Element_Abstract {
	/**
	 * Name of the element
	 *
	 * @return string
	 */
	public function name() {
		return thrive_template()->title() . ' ' . __( 'Settings', THEME_DOMAIN );
	}

	/**
	 * WordPress element identifier
	 *
	 * @return string
	 */
	public function identifier() {
		return '#wrapper';
	}

	/**
	 * Component and control config
	 *
	 * @return array
	 */
	public function own_components() {
		$default = parent::own_components();

		$default['animation']  = [ 'hidden' => true ];
		$default['responsive'] = [ 'hidden' => true ];
		$default['typography'] = [ 'hidden' => true ];

		$default['layout']['disabled_controls'] = [
			'Width',
			'Height',
			'Display',
			'Alignment',
			'.tve-advanced-controls',
			'hr',
		];

		$default['background'] = [
			'config' => [ 'to' => 'main::#wrapper' ],
		];

		$visibility = [
			[
				'name'  => __( 'Show', THEME_DOMAIN ),
				'value' => 1,
			],
			[
				'name'  => __( 'Hide', THEME_DOMAIN ),
				'value' => 0,
			],
		];

		$controls = [
			'template-wrapper' => [
				'config' => [
					'ContentWidth'      => [
						'config'  => [
							'default' => '1080',
							'min'     => '420',
							'max'     => '1980',
							'label'   => __( 'Content Width', THEME_DOMAIN ),
							'um'      => [ 'px', '%' ],
							'css'     => 'max-width',
						],
						'extends' => 'Slider',
					],
					'LayoutWidth'       => [
						'config'  => [
							'default' => '1080',
							'min'     => '420',
							'max'     => '1980',
							'label'   => __( 'Layout Width', THEME_DOMAIN ),
							'um'      => [ 'px', '%' ],
							'css'     => 'max-width',
						],
						'extends' => 'Slider',
					],
					'TopVisibility'     => [
						'config'  => [
							'name'    => __( 'Top', THEME_DOMAIN ),
							'options' => $visibility,
						],
						'extends' => 'Select',
					],
					'SidebarVisibility' => [
						'config'  => [
							'name'    => __( 'Sidebar', THEME_DOMAIN ),
							'options' => $visibility,
						],
						'extends' => 'Select',
					],
					'BottomVisibility'  => [
						'config'  => [
							'name'    => __( 'Bottom', THEME_DOMAIN ),
							'options' => $visibility,
						],
						'extends' => 'Select',
					],
				],
			],
		];

		return array_merge( $default, $controls );
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

return new Thrive_Template_Wrapper_Element( 'template-wrapper' );
