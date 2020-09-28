<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

/**
 * Thrive_Dynamic_List_Text_Element
 *
 * This is a default element used for displaying only default menus for a component
 * It is not displayed in the sidebar elements
 */
class Thrive_Dynamic_List_Text_Element extends Thrive_Theme_Element_Abstract {
	/**
	 * Name of the element
	 *
	 * @return string
	 */
	public function name() {
		return __( 'Inline Text', THEME_DOMAIN );
	}

	/**
	 * Default element identifier
	 *
	 * @return string
	 */
	public function identifier() {
		return '.thrive-dynamic-styled-list-text';
	}

	/**
	 * Either to display or not the element in the sidebar menu
	 *
	 * @return bool
	 */
	public function hide() {
		return true;
	}

	/**
	 * Whether or not the this element can be edited while under :hover state
	 *
	 * @return bool
	 */
	public function has_hover_state() {
		return true;
	}

	/**
	 * Component and control config
	 *
	 * @return array
	 */
	public function own_components() {
		return [
			'link'             => [
				'config' => [
					'ToggleColor'  => [
						'config'  => [
							'name'    => __( 'Color', THEME_DOMAIN ),
							'buttons' => [
								[ 'value' => 'inherit', 'text' => __( 'Inherit', THEME_DOMAIN ), 'default' => true ],
								[ 'value' => 'specific', 'text' => __( 'Specific', THEME_DOMAIN ) ],
							],
						],
						'extends' => 'Tabs',
					],
					'FontColor'    => [
						'css_prefix' => tcb_selection_root() . ' ',
						'read_from'  => 'head',
						'config'     => [
							'default' => '000',
							'label'   => ' ',
							'options' => [
								'output' => 'object',
							],
						],
						'extends'    => 'ColorPicker',
					],
					'BgColor'      => [
						'css_prefix' => tcb_selection_root() . ' ',
						'read_from'  => 'head',
						'config'     => [
							'default' => '000',
							'label'   => __( 'Highlight', THEME_DOMAIN ),
							'options' => [
								'output' => 'object',
							],
						],
						'extends'    => 'ColorPicker',
					],
					'ToggleFont'   => [
						'config'  => [
							'name'    => __( 'Font', THEME_DOMAIN ),
							'buttons' => [
								[ 'value' => 'inherit', 'text' => __( 'Inherit', THEME_DOMAIN ), 'default' => true ],
								[ 'value' => 'specific', 'text' => __( 'Specific', THEME_DOMAIN ) ],
							],
						],
						'extends' => 'Tabs',
					],
					'FontFace'     => [
						'css_prefix' => tcb_selection_root() . ' ',
						'read_from'  => 'head',
						'config'     => [
							'label'     => ' ',
							'template'  => 'controls/font-manager',
							'inline'    => true,
							'important' => true,
						],
					],
					'ToggleSize'   => [
						'config'  => [
							'name'    => __( 'Size', THEME_DOMAIN ),
							'buttons' => [
								[ 'value' => 'inherit', 'text' => __( 'Inherit', THEME_DOMAIN ), 'default' => true ],
								[ 'value' => 'specific', 'text' => __( 'Specific', THEME_DOMAIN ) ],
							],
						],
						'extends' => 'Tabs',
					],
					'FontSize'     => [
						'css_prefix' => tcb_selection_root() . ' ',
						'read_from'  => 'head',
						'config'     => [
							'default' => '16',
							'min'     => '1',
							'max'     => '100',
							'label'   => '',
							'um'      => [ 'px', 'em' ],
							'css'     => 'fontSize',
						],
						'extends'    => 'FontSize',
					],
					'TextStyle'    => [
						'css_prefix' => tcb_selection_root() . ' ',
						'read_from'  => 'head',
						'config'     => [
							'important' => true,
							'buttons'   => [
								'underline'    => [
									'data' => [ 'style' => 'text-decoration-line' ],
								],
								'line-through' => [
									'data' => [ 'style' => 'text-decoration-line' ],
								],
							],
						],
					],
					'Effect'       => [
						'css_prefix' => tcb_selection_root() . ' ',
						'read_from'  => 'head',
						'config'     => [
							'label' => __( 'Effect', THEME_DOMAIN ),
						],
						'extends'    => 'StyleChange',
					],
					'EffectPicker' => [
						'css_prefix' => tcb_selection_root() . ' ',
						'read_from'  => 'head',
						'config'     => [
							'label'   => __( 'Choose link effect', THEME_DOMAIN ),
							'default' => 'none',
						],
					],
					'EffectColor'  => [
						'css_prefix' => tcb_selection_root() . ' ',
						'read_from'  => 'head',
						'config'     => [
							'label'   => __( 'Effect Color', THEME_DOMAIN ),
							'options' => [
								'output'      => 'object',
								'showGlobals' => false,
							],
						],
						'extends'    => 'ColorPicker',
					],
					'EffectSpeed'  => [
						'css_prefix' => tcb_selection_root() . ' ',
						'read_from'  => 'head',
						'label'      => __( 'Effect Speed', THEME_DOMAIN ),
						'config'     => [
							'default' => '0.2',
							'min'     => '0.05',
							'step'    => '0.05',
							'max'     => '1',
							'label'   => __( 'Speed', THEME_DOMAIN ),
							'um'      => [ 's' ],
						],
						'extends'    => 'Slider',
					],
				],
			],
			'layout'           => [ 'hidden' => true ],
			'typography'       => [ 'hidden' => true ],
			'animation'        => [ 'hidden' => true ],
			'responsive'       => [ 'hidden' => true ],
			'styles-templates' => [ 'hidden' => true ],
			'shadow'           => [ 'hidden' => true ],
		];
	}

	/**
	 * This element has no icons
	 *
	 * @return bool
	 */
	public function has_icons() {
		return false;
	}
}

return new Thrive_Dynamic_List_Text_Element( 'dynamic-list-text' );
