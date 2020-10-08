<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-visual-editor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden
} ?>
<div id="tve-thrive_author_links-component" class="tve-component" data-view="ThriveAuthorLinks">
	<div class="dropdown-header" data-prop="docked">
		<?php echo __( 'Main Options', THEME_DOMAIN ); ?>
		<i></i>
	</div>
	<div class="dropdown-content">
		<div class="tve-control" data-key="type" data-view="ButtonGroup"></div>
		<div class="tve-control" data-key="style" data-initializer="style_control"></div>
		<div class="tve-control" data-key="orientation" data-view="ButtonGroup"></div>
		<hr>
		<div class="tve-control" data-key="size" data-view="Slider"></div>
		<hr>
		<div class="control-grid">
			<span class="input-label"><?php echo __( 'Social Networks', THEME_DOMAIN ) ?></span>
			<button class="blue tve-button click" data-fn="open_selector_panel"><?php echo __( 'Change', THEME_DOMAIN ) ?></button>
		</div>
		<div class="tve-control" data-key="selector" data-initializer="selector_control"></div>
		<div class="tve-control" data-key="preview" data-view="PreviewList"></div>
		<div class="tve-control no-space" data-key="has_custom_url" data-view="Switch"></div>
		<div class="tve-control no-space pt-5 pb-5 full-width" data-key="custom_url" data-view="LabelInput"></div>
		<div class="tve-control no-space" data-key="total_share" data-view="Switch"></div>
		<div class="tve-control no-space pt-5 input-small" data-key="counts" data-view="LabelInput" data-label="<?php echo __( 'Greater Than', THEME_DOMAIN ); ?>"></div>
	</div>
</div>
