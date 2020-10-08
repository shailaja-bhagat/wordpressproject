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
<?php /* Handle here also the link from the theme template -> tar and also tar -> theme template*/ ?>
<?php if ( Thrive_Utils::is_theme_template() ) : ?>
	<a href="javascript:void(0)" class="sidebar-item click" data-fn="reset_template" data-position="left"
	   data-tooltip="<?php echo __( 'Reset template', THEME_DOMAIN ); ?>">
		<?php tcb_icon( 'template-reset', false, 'sidebar', '' ); ?>
	</a>
	<?php if ( thrive_template()->is_singular() ) : ?>
		<a href="<?php echo add_query_arg( 'from_theme', true, tcb_get_editor_url( url_to_postid( thrive_template()->url( true ) ) ) ) ?>" class="click sidebar-item tar-redirect" data-fn="switchClickedFromTheme">
			<?php tcb_icon( 'tar', false, 'sidebar', '' ); ?>
			<span class="tar-redirect-tooltip">
				<?php echo __( 'To edit the content', THEME_DOMAIN ); ?>
				<span class="ttb-redirect-switch"><?php echo __( 'switch to Thrive Architect', THEME_DOMAIN ); ?></span>
			</span>
		</a>
	<?php endif; ?>
<?php elseif ( apply_filters( 'thrive_theme_allow_page_edit', ! tve_post_is_landing_page( get_the_ID() ) ) ) : ?>
	<a href="" class="sidebar-item theme-template-redirect click" data-fn="switchClickedFromArchitect">
		<?php tcb_icon( 'ttb', false, 'sidebar', '' ); ?>
		<span class="ttb-redirect-tooltip">
			<?php echo __( 'To Edit Current Template', THEME_DOMAIN ); ?>
			<span class="ttb-redirect-switch"><?php echo __( 'Switch to Thrive Theme Builder', THEME_DOMAIN ); ?></span>
		</span>
	</a>
<?php endif; ?>
