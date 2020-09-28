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

<div class="section-form-wrapper">
	<h2 class="tcb-modal-title"><?php echo __( 'Create new Layout', THEME_DOMAIN ); ?></h2>

	<input type="text" class="new-template-name" value="<?php echo __( 'Layout name', THEME_DOMAIN ); ?>">

	<div class="tcb-modal-footer clearfix mt-20">
		<button type="button" class="tcb-right tve-button medium blue click w-100" data-fn="save"><?php echo __( 'Save Layout', THEME_DOMAIN ); ?></button>
	</div>
</div>
