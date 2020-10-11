<?php
/**
 * Work Section for our theme
 *
 * @package WordPress
 * @subpackage Integral
 * @since Integral 1.0
 */
?>
<?php global $integral; ?>
<?php if($integral['work-section-toggle']==1) { ?>
<section id="work" class="work lite <?php echo esc_attr($integral['work-custom-class']); ?>">
	<div class="container">
		<div class="row">
			<div class="col-md-12 heading">
				<?php if ($integral['work-title-icon']) { ?><i class="fa <?php echo esc_attr($integral['work-title-icon']); ?>"></i><?php } ?>
                <?php if ($integral['work-title']) { ?><h2 class="bigtitle"><span><?php echo esc_html($integral['work-title']); ?></span></h2><?php } ?>
                <?php if ($integral['work-subtitle']) { ?><p class="subtitle"><?php echo wp_kses_post($integral['work-subtitle']); ?></p><?php } ?>
			</div>
			<div class="col-md-12">
                <div id="ourstory_widget">
                    <?php if ( is_active_sidebar( 'our-story' ) ) : ?>
                            <?php dynamic_sidebar( 'our-story' ); ?>
                    <?php endif; ?>
                </div>            
            </div>
		</div>
	</div>
</section>
<section id="our_team" class="our-team lite">
    <div id="ourteam_widget">
        <?php if ( is_active_sidebar( 'our-team' ) ) : ?>
                <?php dynamic_sidebar( 'our-team' ); ?>
        <?php endif; ?>
    </div>   
</section> 
<section id="our_strategy" class="our-strategy lite">
    <div id="ourteam_widget">
        <?php if ( is_active_sidebar( 'our-strategy' ) ) : ?>
                <?php dynamic_sidebar( 'our-strategy' ); ?>
        <?php endif; ?>
    </div>   
</section> 
<section id="pride_work" class="pride-work lite">
    <div id="pridework_widget">
        <?php if ( is_active_sidebar( 'pride-work' ) ) : ?>
                <?php dynamic_sidebar( 'pride-work' ); ?>
        <?php endif; ?>
    </div>   
</section> 
<section id="ra_technology" class="ra-technology lite">
    <div id="ratechnology_widget">
        <?php if ( is_active_sidebar( 'ra-technology' ) ) : ?>
                <?php dynamic_sidebar( 'ra-technology' ); ?>
        <?php endif; ?>
    </div>   
</section>
<section id="image_gallery" class="image-gallery lite">
    <div id="imggallery_widget">
    <?php if ( is_active_sidebar( 'img-gallery' ) ) : ?>
        <?php dynamic_sidebar( 'img-gallery' ); ?>
    <?php endif; ?>
    <?php
        $my_id = $integral['work-text'];
        $post_id = get_post($my_id);
        $content = $post_id->post_content;
        $content = apply_filters('the_content', $content);
        $content = str_replace(']]>', ']]>', $content);
        echo $content;
    ?>      
    </div>   
</section>
<?php } ?>