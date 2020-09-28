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

<h3><?php echo __( 'Reset Template', THEME_DOMAIN ); ?></h3>
<p>
	<?php echo __( 'Are you sure you want to reset the template ?', THEME_DOMAIN ); ?>
	<?php echo __( 'You will lose any customizations made to it.', THEME_DOMAIN ); ?>
</p>

<div class="ttd-modal-footer">
	<button class="ttb-left grey click" data-fn="close"><?php echo __( 'Cancel', THEME_DOMAIN ); ?></button>
	<button class="ttb-right red click" data-fn="reset"><?php echo __( 'Reset Template', THEME_DOMAIN ); ?></button>
</div>
