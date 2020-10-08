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

<div class="error-container"></div>
<div class="tve-modal-content">
	<div id="cb-cloud-menu">
		<div class="fixed top">
			<div class="lp-search">
				<input type="text" data-source="search" class="keydown" data-fn="filter" placeholder="<?php echo esc_html__( 'Search', THEME_DOMAIN ); ?>"/>
				<?php tcb_icon( 'search-regular' ); ?>
				<?php tcb_icon( 'close2', false, 'sidebar', 'click', array( 'data-fn' => 'domClearSearch' ) ); ?>
			</div>
		</div>
		<div class="lp-menu-wrapper fixed">
			<div id="lp-blk-pack-categories"></div>
			<div class="lp-label-wrapper mt-30">
				<span><?php echo __( 'SOURCE', THEME_DOMAIN ); ?></span>
				<span class="separator"></span>
			</div>
			<div id="lp-groups-wrapper"></div>
		</div>
	</div>
	<div id="cb-cloud-templates">
		<div id="lp-blk-pack-title" class="mb-5"></div>
		<div id="lp-blk-pack-description"></div>
		<div id="cb-pack-content"></div>
	</div>
</div>
