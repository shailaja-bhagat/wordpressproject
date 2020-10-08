<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}
?>

<h1 class="page-title">
	<?php if ( have_posts() ) : ?>
		<?php printf( __( 'Search Results for: %s', THEME_DOMAIN ), '<span>' . get_search_query() . '</span>' ); ?>
	<?php else : ?>
		<?php _e( 'Nothing Found', THEME_DOMAIN ); ?>
	<?php endif; ?>
</h1>
