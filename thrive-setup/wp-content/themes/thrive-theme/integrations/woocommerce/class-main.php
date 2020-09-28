<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-theme
 */

namespace Thrive\Theme\Integrations\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

/**
 * Class Main
 *
 * @package Thrive\Theme\Integrations\WooCommerce
 */
class Main extends \TCB_Woo {

	const KEY              = 'woocommerce';
	const INTEGRATION_PATH = THEME_PATH . '/integrations/woocommerce/';

	const SHOP_TEMPLATE     = 'shop';
	const CART_TEMPLATE     = 'cart';
	const CHECKOUT_TEMPLATE = 'checkout';
	const ACCOUNT_TEMPLATE  = 'account';
	const HEADER            = 'woo_header';
	const FOOTER            = 'woo_footer';

	const PAGE_TEMPLATES = [ Main::CART_TEMPLATE, Main::CHECKOUT_TEMPLATE, Main::ACCOUNT_TEMPLATE ];
	const ALL_TEMPLATES  = [ Main::CART_TEMPLATE, Main::CHECKOUT_TEMPLATE, Main::ACCOUNT_TEMPLATE, Main::POST_TYPE, Main::SHOP_TEMPLATE ];

	const GENERATED_TEMPLATES_OPTION = 'thrive_woocommerce_generated_templates';
	/**
	 * File from WooCommerce that has the product template functionality
	 */
	const SINGLE_PRODUCT_CONTENT = 'content-single-product.php';

	public static $elements   = [];
	public static $shortcodes = [];

	public static function init() {
		static::include_core();

		if ( static::active() ) {
			static::include_elements();

			Actions::add();
			Filters::add();
			Wizard::init();
		} else {
			require_once __DIR__ . '/class-filters.php';

			Filters::inactive();
		}
	}

	/**
	 * Include WooCommerce files
	 */
	public static function include_core() {
		require_once __DIR__ . '/class-helpers.php';
		require_once __DIR__ . '/class-filters.php';
		require_once __DIR__ . '/class-actions.php';
		require_once __DIR__ . '/class-wizard.php';
	}

	/**
	 * Include WooCommerce files for elements
	 */
	public static function include_elements() {
		static::$elements = \Thrive_Architect::get_architect_theme_elements( static::INTEGRATION_PATH . 'elements' );

		$shortcodes_path = static::INTEGRATION_PATH . 'shortcodes/';
		$files           = array_diff( scandir( $shortcodes_path ), [ '.', '..' ] );
		foreach ( $files as $file ) {
			static::$shortcodes[] = include $shortcodes_path . $file;
		}
	}

	/**
	 * Check if we're on the admin edit page of the shop
	 *
	 * @return bool
	 */
	public static function is_admin_shop_page() {
		return parent::active() && wc_get_page_id( static::SHOP_TEMPLATE ) === get_the_ID();
	}

	/**
	 * Get data to localize in the TTB dashboard
	 *
	 * @return array
	 */
	public static function admin_localize() {
		$is_active = static::active();

		$data = [
			'key'           => static::KEY,
			'is_active'     => $is_active ? 1 : 0,
			'has_templates' => Helpers::has_woo_templates(),
		];

		/* localize the rest of the data only if woo is active */
		if ( $is_active ) {
			$data = array_merge( $data, [
				'label'      => static::get_label(),
				'shop_label' => esc_html__( 'Shop', THEME_DOMAIN ),
				'templates'  => array_merge( static::PAGE_TEMPLATES, [ static::POST_TYPE, 'product_tag', 'product_cat' ] ),
				'shop_url'   => static::get_shop_url(),
			] );
		}

		return $data;
	}

	/**
	 * @return string
	 */
	public static function get_label() {
		return esc_html__( 'WooCommerce', THEME_DOMAIN );
	}
}
