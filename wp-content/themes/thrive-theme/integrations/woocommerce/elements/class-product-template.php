<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-theme
 */

namespace Thrive\Theme\Integrations\WooCommerce\Elements;

use Thrive\Theme\Integrations\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

if ( ! class_exists( 'Thrive_Theme_Cloud_Element_Abstract' ) ) {
	require_once ARCHITECT_INTEGRATION_PATH . '/classes/class-thrive-theme-cloud-element-abstract.php';
}

/**
 * Class Product_Template
 * @package Thrive\Theme\Integrations\WooCommerce\Elements
 */
class Product_Template extends \Thrive_Theme_Element_Abstract  {

	/**
	 * Name of the element
	 *
	 * @return string
	 */
	public function name() {
		return __( 'Product', THEME_DOMAIN );
	}

	/**
	 * Return icon class needed for display in menu
	 *
	 * @return string
	 */
	public function icon() {
		return 'woo';
	}

	/**
	 * WordPress element identifier
	 *
	 * @return string
	 */
	public function identifier() {
		return '.product-template-wrapper';
	}

	/**
	 * This element is a shortcode
	 *
	 * @return bool
	 */
	public function is_shortcode() {
		return true;
	}

	/**
	 * Return the shortcode tag of the element.
	 *
	 * @return string
	 */
	public static function shortcode() {
		return WooCommerce\Shortcodes\Product_Template::SHORTCODE;
	}

	/**
	 * If an element has selector or a data-css will be generated
	 *
	 * @return bool
	 */
	public function has_selector() {
		return true;
	}

	public function own_components() {
		$components = [
			'typography'       => [ 'hidden' => true ],
			'background'       => [ 'hidden' => true ],
			'layout'           => [ 'hidden' => true ],
			'borders'          => [ 'hidden' => true ],
			'animation'        => [ 'hidden' => true ],
			'shadow'           => [ 'hidden' => true ],
			'responsive'       => [ 'hidden' => true ],
			'styles-templates' => [ 'hidden' => true ],

		];

		$components['product-template'] = [
			'config' => [
				'TitleVisibility'       => [
					'config'  => [
						'name'    => '',
						'label'   => __( 'Title', THEME_DOMAIN ),
						'default' => true,
					],
					'extends' => 'Switch',
				],
				'PriceVisibility'       => [
					'config'  => [
						'name'    => '',
						'label'   => __( 'Price', THEME_DOMAIN ),
						'default' => true,
					],
					'extends' => 'Switch',
				],
				'DescriptionVisibility' => [
					'config'  => [
						'name'    => '',
						'label'   => __( 'Short Description', THEME_DOMAIN ),
						'default' => true,
					],
					'extends' => 'Switch',
				],
				'ButtonVisibility'      => [
					'config'  => [
						'name'    => '',
						'label'   => __( 'Add to cart', THEME_DOMAIN ),
						'default' => true,
					],
					'extends' => 'Switch',
				],
				'MetaVisibility'        => [
					'config'  => [
						'name'    => '',
						'label'   => __( 'Meta', THEME_DOMAIN ),
						'default' => true,
					],
					'extends' => 'Switch',
				],
				'ReviewVisibility'      => [
					'config'  => [
						'name'    => '',
						'label'   => __( 'Review tab', THEME_DOMAIN ),
						'default' => true,
					],
					'extends' => 'Switch',
				],
			],
		];

		$components['product-gallery'] = [
			'config' => [
				'DisplayMagnifier' => [
					'config'  => [
						'name'    => '',
						'label'   => __( 'Magnifier', THEME_DOMAIN ),
						'default' => true,
					],
					'extends' => 'Switch',
				],
				'GalleryWidth'     => [
					'config'  => [
						'min'   => '24',
						'max'   => '1024',
						'label' => __( 'Gallery Width', THEME_DOMAIN ),
						'um'    => [ 'px' ],
						'css'   => 'width',
					],
					'extends' => 'Slider',
				],
				'Columns'          => [
					'config'  => [
						'min'   => WooCommerce\Shortcodes\Product_Template::GALLERY_MIN_COLUMNS,
						'max'   => WooCommerce\Shortcodes\Product_Template::GALLERY_MAX_COLUMNS,
						'um'    => [],
						'label' => __( 'Columns', THEME_DOMAIN ),
					],
					'extends' => 'Slider',
				],
			],
		];

		$components['related-products'] = [
			'order'  => 24,
			'config' => [
				'Display'      => [
					'config'  => [
						'name'    => '',
						'label'   => __( 'Display related products', THEME_DOMAIN ),
						'default' => true,
					],
					'extends' => 'Switch',
				],
				'Columns'      => [
					'config'  => [
						'min'   => '1',
						'max'   => '6',
						'um'    => [],
						'label' => __( 'Columns', THEME_DOMAIN ),
					],
					'extends' => 'Slider',
				],
				'PostsPerPage' => [
					'config'  => [
						'min'   => '1',
						'max'   => '24',
						'um'    => [],
						'label' => __( 'Products to display', THEME_DOMAIN ),
					],
					'extends' => 'Slider',
				],
				'OrderBy'      => [
					'config'  => [
						'name'    => __( 'Order by', THEME_DOMAIN ),
						'options' => [
							[
								'name'  => __( 'Product title', THEME_DOMAIN ),
								'value' => 'title',
							],
							[
								'name'  => __( 'Product ID', THEME_DOMAIN ),
								'value' => 'id',
							],
							[
								'name'  => __( 'Published date', THEME_DOMAIN ),
								'value' => 'date',
							],
							[
								'name'  => __( 'Last modified date', THEME_DOMAIN ),
								'value' => 'modified',
							],
							[
								'name'  => __( 'Menu order', THEME_DOMAIN ),
								'value' => 'menu_order',
							],
							[
								'name'  => __( 'Price', THEME_DOMAIN ),
								'value' => 'price',
							],
							[
								'name'  => __( 'Random', THEME_DOMAIN ),
								'value' => 'rand',
							],
						],
						'default' => 'rand',
					],
					'extends' => 'Select',
				],
				'Order'        => [
					'config'  => [
						'name'    => __( 'Order', THEME_DOMAIN ),
						'options' => [
							[
								'name'  => __( 'ASC', THEME_DOMAIN ),
								'value' => 'asc',
							],
							[
								'name'  => __( 'DESC', THEME_DOMAIN ),
								'value' => 'desc',
							],
						],
						'default' => 'desc',
					],
					'extends' => 'Select',
				],
				'Alignment'    => [
					'config'  => [
						'name'    => __( 'Alignment', THEME_DOMAIN ),
						'buttons' => [
							[
								'icon'    => 'a_left',
								'value'   => 'left',
								'tooltip' => __( 'Align Left', THEME_DOMAIN ),
							],
							[
								'icon'    => 'a_center',
								'value'   => 'center',
								'default' => true,
								'tooltip' => __( 'Align Center', THEME_DOMAIN ),
							],
							[
								'icon'    => 'a_right',
								'value'   => 'right',
								'tooltip' => __( 'Align Right', THEME_DOMAIN ),
							],
						],
					],
					'extends' => 'ButtonGroup',
				],
				'ImageSize'    => [
					'config'  => [
						'default' => '100',
						'min'     => '0',
						'max'     => '100',
						'label'   => __( 'Image Size', THEME_DOMAIN ),
						'um'      => [ '%' ],
						'css'     => 'width',
					],
					'extends' => 'Slider',
				],
			],
		];

		$components['upsells-products'] = $components['related-products'];

		$components['upsells-products']['config']['Display']['config']['label'] = __( 'Display upsell', THEME_DOMAIN );

		/* upsells should be in front of the related */
		$components['upsells-products']['order'] = 23;

		return $components;
	}

	/**
	 * Element category that will be displayed in the sidebar
	 * @return string
	 */
	public function category() {
		return WooCommerce\Helpers::get_products_category_label();
	}
}

return new Product_Template( 'product-template' );
