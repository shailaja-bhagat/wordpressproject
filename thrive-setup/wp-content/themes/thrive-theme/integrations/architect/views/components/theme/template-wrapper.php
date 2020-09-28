<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden
}
?>

<div id="tve-template-wrapper-component" class="tve-component" data-view="ThemeLayout">
	<div class="dropdown-header" data-prop="docked">
		<?php echo __( 'Main Options', THEME_DOMAIN ); ?>
		<i></i>
	</div>
	<div class="dropdown-content">

		<div class="tve-control" data-view="PageMap"></div>

		<div class="tve-control" data-view="LayoutVisibility"></div>

		<hr class="mt-5">

		<div class="tve-currently-editing">
			<p><?php echo __( 'Currently editing', THEME_DOMAIN ); ?></p>
			<h3><?php echo thrive_template()->title(); ?></h3>

			<div class="need-help">
				<?php Thrive_Views::svg_icon( 'question-circle-light' ); ?>
				<span>
					<?php echo __( 'Need help?', THEME_DOMAIN ); ?>
					<a href="#"><?php echo __( 'Open the Quick Guide', THEME_DOMAIN ); ?></a>
				</span>
			</div>
		</div>

		<div class="full-width-control mt-10 mb-10">
			<button class="click" data-fn="toggleFullWidth" data-boxed="1">
				<span class="boxed-icon button-icon" data-icon="boxed"></span>
				<?php echo __( 'Boxed', THEME_DOMAIN ); ?>
			</button>

			<button class="click" data-fn="toggleFullWidth" data-boxed="0">
				<span class="button-icon" data-icon="full"></span>
				<?php echo __( 'Full Width', THEME_DOMAIN ); ?>
			</button>
		</div>

		<div class="tve-control	" data-view="ContentWidth"></div>
		<div class="tve-control	" data-view="LayoutWidth"></div>
	</div>
</div>
