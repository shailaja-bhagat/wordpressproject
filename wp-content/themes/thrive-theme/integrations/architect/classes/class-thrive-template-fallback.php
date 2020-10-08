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
 * Class Thrive_Template_Fallback
 */
class Thrive_Template_Fallback {

	const OPTION = 'thrive_template_fallback';

	/**
	 * Getter and setter for the fallback option
	 *
	 * @param $value
	 *
	 * @return mixed|void
	 */
	public static function option( $value = false ) {
		if ( $value === false ) {
			return get_option( static::OPTION, [] );
		}

		update_option( static::OPTION, $value );
	}

	/**
	 * Return a list of fallback templates.
	 * When a page has a custom template from another skin, we use fallback templates to render that page.
	 *
	 * @param $force boolean
	 *
	 * @return array|mixed
	 */
	public static function get( $force = false ) {
		$fallback = static::option();

		/* if the fallback needs an update or we just force the update */
		if ( empty( $fallback['updated'] ) || $force ) {
			$current_templates = thrive_skin()->get_templates( 'ids' );

			/* check all pages that have a custom template that is not from the current skin */
			$pages = get_posts( [
				'post_type'      => 'page',
				'posts_per_page' => - 1,
				'meta_query'     => [
					[
						'key'     => THRIVE_META_POST_TEMPLATE,
						'value'   => $current_templates,
						'compare' => 'NOT IN',
					],
				],
			] );

			$templates_ids = [];

			foreach ( $pages as $page ) {
				$thrive_page = new Thrive_Post( $page->ID );
				$template_id = $thrive_page->get_meta( THRIVE_META_POST_TEMPLATE );


				/* The already added templates should not be created again*/
				if ( ! in_array( $template_id, $templates_ids ) ) {
					$templates_ids[] = $template_id;

					$template = new Thrive_Template( $template_id );

					/* we only add fallback only for the templates that we're not previously added */
					if ( empty( $fallback[ $template_id ] ) ) {
						$fallback[ $template_id ] = [
							'ID'         => $template_id,
							'post_title' => $template->post_title,
							'meta_input' => [
								THRIVE_PRIMARY_TEMPLATE   => THRIVE_SINGULAR_TEMPLATE,
								THRIVE_SECONDARY_TEMPLATE => $page->post_type,
							],
							'fallback'   => [],
						];
					} else {
						$fallback[ $template_id ] ['post_title'] = $template->post_title;
					}
				}
			}
			/* The templates that are not used any more are not removed from $fallback*/
			foreach ( $fallback as $key => $value ) {
				if ( ! in_array( $key, $templates_ids ) && is_int( $key ) ) {
					unset( $fallback[ $key ] );
				}
			}
			/* don't do this search again only until we change the skin again */
			$fallback['updated'] = true;

			static::option( $fallback );
		}

		return $fallback;
	}

	/**
	 * Set a template from the current skin as fallback for a template from another skin
	 *
	 * @param $other_template_id
	 * @param $current_template_id
	 */
	public static function update( $other_template_id, $current_template_id ) {
		$fallback = static::get();

		if ( empty( $fallback[ $other_template_id ] ) ) {
			$fallback[ $other_template_id ] = [
				'fallback' => [],
			];
		}

		$fallback[ $other_template_id ]['fallback'][] = $current_template_id;

		static::option( $fallback );
	}

	/**
	 * When we change a skin we want to check for custom templates again
	 */
	public static function refresh() {
		$fallback = static::option();

		$fallback['updated'] = false;

		static::option( $fallback );
	}

}
