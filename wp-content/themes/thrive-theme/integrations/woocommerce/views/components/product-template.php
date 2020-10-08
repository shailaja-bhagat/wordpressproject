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

<div id="tve-product-template-component" class="tve-component" data-view="ProductOptions">
	<div class="dropdown-header" data-prop="docked">
		<?php echo __( 'Product Display Options', THEME_DOMAIN ); ?>
		<i></i>
	</div>
	<div class="dropdown-content">
		<div class="tve-control" data-view="TitleVisibility"></div>
		<div class="tve-control" data-view="PriceVisibility"></div>
		<div class="tve-control" data-view="DescriptionVisibility"></div>
		<div class="tve-control" data-view="ButtonVisibility"></div>
		<div class="tve-control" data-view="MetaVisibility"></div>
		<div class="tve-control" data-view="ReviewVisibility"></div>
	</div>
</div>

