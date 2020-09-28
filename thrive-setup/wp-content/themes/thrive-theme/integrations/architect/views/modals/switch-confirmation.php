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

<h3>
	<?php echo __( 'Unsaved Changes', THEME_DOMAIN ); ?>
</h3>

<p>
	<?php echo __( 'Do you want to save your changes before exiting?', THEME_DOMAIN ); ?>
</p>

<div class="ttd-modal-footer">
	<button class="ttb-left red click" data-fn="continueWithoutSaving"><?php echo __( 'Continue without Saving', THEME_DOMAIN ); ?></button>
	<button class="ttb-right green click" data-fn="saveAndContinue"><?php echo __( 'Save and Exit', THEME_DOMAIN ); ?></button>
</div>
