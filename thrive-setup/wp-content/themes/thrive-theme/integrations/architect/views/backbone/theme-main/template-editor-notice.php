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

<div class="thrive-template-editor-notice info-text grey-text mt-5">
	<p>
		<?php echo __( 'You can edit your site templates from the ', THEME_DOMAIN ); ?>
		<a href="<?php echo admin_url( 'admin.php?page=thrive-theme-dashboard&tab=other#templates' ); ?>" target="_blank">
			<?php echo __( 'Template Editor', THEME_DOMAIN ); ?>
		</a>
	</p>
</div>
