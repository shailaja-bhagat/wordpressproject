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

<?php if ( Thrive_Utils::is_theme_template() ) : ?>
    <div class="tcb-top-container ttb">
        <span class="icon"><?php tcb_icon( 'ttb-strong', false, 'sidebar', '' ); ?></span>
        <div class="text"><?php echo __( 'You are editing a template', THEME_DOMAIN ); ?></div>
        <div class="tcb-switch-tooltip tcb-switch-blue">
            <?php tcb_icon( 'info-circle-solid' ); ?>
            <div class="tcb-switch-drop">
                <p class="title"><?php echo __( 'Edit template with Theme Builder', THEME_DOMAIN ); ?></p>
                <p class="tooltip-text">
                    <?php echo __( 'Using Thrive Theme Builder, you are able to build a theme from scratch. This means that you can create templates for 
                    various types of content (pages/posts/lists). Once you have created and customized the templates, you can apply a single template to multiple pages 
                    or posts of your site. You can also set global settings as colors, logos and fonts.', THEME_DOMAIN ); ?>
                </p>
                <a class="w-separator" target="_blank" href="https://thrivethemes.com/tkb_item/how-do-thrive-architect-and-thrive-theme-builder-complement-each-other/"><?php echo __( 'Find more about Theme Builder', THEME_DOMAIN ); ?></a>
            </div>
        </div>
    </div>
<?php else : ?>
	<div class="tcb-top-container tcb">
		<span class="icon"><?php tcb_icon( 'tar-strong', false, 'sidebar', '' ); ?></span>
		<div class="text"><?php echo __( 'You are editing content', THEME_DOMAIN ); ?></div>
        <div class="tcb-switch-tooltip tcb-switch-green">
            <?php tcb_icon( 'info-circle-solid' ); ?>
            <div class="tcb-switch-drop">
                <p class="title"><?php echo __( 'Edit content with Thrive Architect', THEME_DOMAIN ); ?></p>
                <p class="tooltip-text">
                    <?php echo sprintf( esc_html__( 'Thrive Architect is a %sWordPress Page Builder%s, with the help of which you can edit 
                    the actual content of your posts/pages individually. Just drag & drop content elements with your mouse 
                    into the page and then customize each element directly using the left sidebar options.', THEME_DOMAIN ), '<strong>', '</strong>' ); ?>
                </p>
                <a class="w-separator" target="_blank" href="https://thrivethemes.com/tkb_item/how-do-thrive-architect-and-thrive-theme-builder-complement-each-other/"><?php echo __( 'Find more about Thrive Architect', THEME_DOMAIN ); ?></a>
            </div>
        </div>
	</div>
<?php endif; ?>
