<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}
$scripts = thrive_scripts()->get_all();
?>
<div>
	<div class="components-base-control__field">
		<label class="components-base-control__label">Header scripts (Before the <b>&lt;/head&gt;</b> end tag)</label>
		<textarea class="components-textarea-control__input" rows="5" title="<?php echo __( 'Header Scripts', THEME_DOMAIN ); ?>"
		          name="thrive_head_scripts"><?php echo $scripts[ Thrive_Scripts::HEAD_SCRIPT ] ?></textarea>
	</div>
	<div class="field-section no-border s-setting">
		<label class="components-base-control__label">Body (header) scripts (Immediately after the <b>&lt;body&gt;</b> tag)</label>
		<textarea class="components-textarea-control__input" rows="5" title="<?php echo __( 'Body Scripts', THEME_DOMAIN ); ?>"
		          name="thrive_body_scripts"><?php echo $scripts[ Thrive_Scripts::BODY_SCRIPT ] ?></textarea>
	</div>
	<div class="field-section no-border s-setting">
		<label class="components-base-control__label">Body (footer) scripts (Before the <b>&lt;/body&gt;</b> end tag)</label>
		<textarea class="components-textarea-control__input" rows="5" title="<?php echo __( 'Footer Scripts', THEME_DOMAIN ); ?>"
		          name="thrive_footer_scripts"><?php echo $scripts[ Thrive_Scripts::FOOTER_SCRIPT ] ?></textarea>
	</div>
</div>
