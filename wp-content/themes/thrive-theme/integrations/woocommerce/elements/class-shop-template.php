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
 * Class Shop_Template
 * @package Thrive\Theme\Integrations\WooCommerce\Elements
 */
class Shop_Template extends \Thrive_Theme_Cloud_Element_Abstract {

	/**
	 * Name of the element
	 *
	 * @return string
	 */
	public function name() {
		return __( 'Shop', THEME_DOMAIN );
	}

	/**
	 * Set WooCommerce as alternate text for search
	 *
	 * @return string
	 */
	public function alternate() {
		return 'woocommerce';
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
		return '.shop-template-wrapper';
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
		return WooCommerce\Shortcodes\Shop_Template::SHORTCODE;
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
			'animation'        => [ 'hidden' => true ],
			'shadow'           => [ 'hidden' => true ],
			'responsive'       => [ 'hidden' => true ],
			'styles-templates' => [ 'hidden' => true ],

		];

		$components['shop-template'] = [
			'config' => [
				'PostsPerPage'                => [
					'config'  => [
						'min'   => '1',
						'max'   => '24',
						'um'    => [],
						'label' => __( 'Products per page', THEME_DOMAIN ),
					],
					'extends' => 'Slider',
				],
				'Columns'                     => [
					'config'  => [
						'min'   => '1',
						'max'   => '8',
						'um'    => [],
						'label' => __( 'Columns', THEME_DOMAIN ),
					],
					'extends' => 'Slider',
				],
				'OrderBy'                     => [
					'config'  => [
						'name'    => __( 'Order by', THEME_DOMAIN ),
						'options' => [
							[
								'name'  => __( 'Product title', THEME_DOMAIN ),
								'value' => 'title',
							],
							[
								'name'  => __( 'Popularity', THEME_DOMAIN ),
								'value' => 'popularity',
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
				'Order'                       => [
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
				'result-count-visibility'     => [
					'config'  => [
						'name'    => '',
						'label'   => __( 'Result count', THEME_DOMAIN ),
						'default' => true,
					],
					'extends' => 'Switch',
				],
				'catalog-ordering-visibility' => [
					'config'  => [
						'name'    => '',
						'label'   => __( 'Catalog ordering', THEME_DOMAIN ),
						'default' => true,
					],
					'extends' => 'Switch',
				],
				'sale-flash-visibility'       => [
					'config'  => [
						'name'    => '',
						'label'   => __( 'Sale Flash ', THEME_DOMAIN ),
						'default' => true,
					],
					'extends' => 'Switch',
				],
				'title-visibility'            => [
					'config'  => [
						'name'    => '',
						'label'   => __( 'Title', THEME_DOMAIN ),
						'default' => true,
					],
					'extends' => 'Switch',
				],
				'rating-visibility'           => [
					'config'  => [
						'name'    => '',
						'label'   => __( 'Rating', THEME_DOMAIN ),
						'default' => true,
					],
					'extends' => 'Switch',
				],
				'price-visibility'            => [
					'config'  => [
						'name'    => '',
						'label'   => __( 'Price', THEME_DOMAIN ),
						'default' => true,
					],
					'extends' => 'Switch',
				],
				'cart-visibility'             => [
					'config'  => [
						'name'    => '',
						'label'   => __( 'Add to cart', THEME_DOMAIN ),
						'default' => true,
					],
					'extends' => 'Switch',
				],
				'pagination-visibility'       => [
					'config'  => [
						'name'    => '',
						'label'   => __( 'Pagination', THEME_DOMAIN ),
						'default' => true,
					],
					'extends' => 'Switch',
				],
				'Alignment'                   => [
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
				'ImageSize'                   => [
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

return new Shop_Template( 'shop-template' );
