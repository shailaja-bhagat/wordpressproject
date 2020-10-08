<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-theme
 */

namespace Thrive\Theme\Integrations\WooCommerce\Shortcodes;

use TCB\Integrations\WooCommerce\Shortcodes\MiniCart\Main as MiniCart;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

/**
 * Class Checkout_Template
 * @package Thrive\Theme\Integrations\WooCommerce\Shortcodes
 */
class Checkout_Template {

	const SHORTCODE = 'thrive_checkout_template';

	public static function init() {
		add_shortcode( static::SHORTCODE, [ __CLASS__, 'render' ] );
	}

	/**
	 * Render the checkout element.
	 * @param array $attr
	 *
	 * @return string
	 */
	public static function render( $attr = [] ) {
		$classes = [ 'checkout-template-wrapper', THRIVE_WRAPPER_CLASS ];

		if ( \Thrive_Utils::is_inner_frame() || \Thrive_Utils::during_ajax() ) {
			$classes[] = 'tcb-selector-no_clone';

			if ( empty( wc()->cart->get_cart() ) ) {
				MiniCart::generate_dummy_cart();

				/**
				 * In the editor, we want to display some products in the checkout cart even if the cart is currently empty.
				 *
				 * In order to make this happen, we also have to add a filter to prevent the redirect that Woo does by default
				 * @see allow_checkout_redirect() from Thrive\Theme\Integrations\WooCommerce\Filters, filter name: woocommerce_checkout_redirect_empty_cart
				 */
				$checkout = \WC_Shortcodes::checkout( [] );

				/* empty the cart to remove the products that we just added */
				WC()->cart->empty_cart();
			}
		}

		if ( empty( $checkout ) ) {
			$checkout = \WC_Shortcodes::checkout( [] );
		}

		return \TCB_Utils::wrap_content( $checkout, 'div', '', $classes, \Thrive_Utils::create_attributes( $attr ) );
	}
}

return Checkout_Template::class;
