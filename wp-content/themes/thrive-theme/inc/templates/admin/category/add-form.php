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
<div class="form-field">
	<label for="<?php echo Thrive_Category::PAGE_REDIRECT; ?>"><?php echo __( 'Redirect Category to a Page', THEME_DOMAIN ) ?></label>
	<select name="<?php echo Thrive_Category::PAGE_REDIRECT; ?>">
		<option value="0"><?php echo __( 'None', THEME_DOMAIN ); ?></option>
		<?php foreach ( get_pages() as $page ): ?>
			<option value="<?php echo $page->ID; ?>"><?php echo get_the_title( $page->ID ); ?></option>
		<?php endforeach; ?>
	</select>

	<p class="description"><?php echo __( 'If set you can replace the WordPress category page with your own highly optimised landing page', THEME_DOMAIN ); ?></p>
</div>
