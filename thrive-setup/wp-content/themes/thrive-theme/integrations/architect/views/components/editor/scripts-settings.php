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
<div id="tve-scripts_settings-component" class="tve-component" data-view="ScriptsSettings">
	<div class="mouseover" data-fn="hideTooltip">
		<div class="dropdown-header" data-prop="docked">
			<div class="group-description">
				<?php echo __( 'Custom Scripts', THEME_DOMAIN ); ?>
			</div>
			<i></i>
		</div>
		<div class="dropdown-content mb-10">
			<div class="state-custom-scripts state">
				<section>
					<div class="field-section s-setting">
						<label class="s-name"><?php echo __( sprintf( 'Header scripts (Before the %shead %s end tag)', '<b>&lt;/', '&gt;</b>' ), THEME_DOMAIN ) ?></label>
						<textarea class="input" data-fn="setScript" rows="5" title="<?php echo __( 'Header Scripts', THEME_DOMAIN ); ?>"
						          name="<?php echo Thrive_Scripts::HEAD_SCRIPT ?>"><?php echo $scripts[ Thrive_Scripts::HEAD_SCRIPT ] ?></textarea>
					</div>
					<div class="field-section no-border s-setting">
						<label class="s-name"><?php echo __( sprintf( 'Body (header) scripts (Immediately after the %sbody%s tag)', '<b>&lt;', '&gt;</b>' ), THEME_DOMAIN ) ?></label>
						<textarea class="input" data-fn="setScript" rows="5" title="<?php echo __( 'Body Scripts', THEME_DOMAIN ); ?>"
						          name="<?php echo Thrive_Scripts::BODY_SCRIPT ?>"><?php echo $scripts[ Thrive_Scripts::BODY_SCRIPT ] ?></textarea>
					</div>
					<div class="field-section no-border s-setting">
						<label class="s-name"><?php echo __( sprintf( 'Body (footer) scripts (Before the %sbody %s end tag)', '<b>&lt;/', '&gt;</b>' ), THEME_DOMAIN ); ?></label>
						<textarea class="input" data-fn="setScript" rows="5" title="<?php echo __( 'Footer Scripts', THEME_DOMAIN ); ?>"
						          name="<?php echo Thrive_Scripts::FOOTER_SCRIPT ?>"><?php echo $scripts[ Thrive_Scripts::FOOTER_SCRIPT ] ?></textarea>
					</div>
				</section>
			</div>
		</div>
	</div>
</div>
