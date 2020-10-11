<?php
/**
 * Clients Section for our theme
 *
 * @package WordPress
 * @subpackage Integral
 * @since Integral 1.0
 */
?>
<?php global $integral; ?>
<?php if($integral['clients-section-toggle']==1) { ?> 
<section id="our-clients" class="our-clients lite">
    <div id="ourclients_widget">
        <?php if ( is_active_sidebar( 'our-clients' ) ) : ?>
                <?php dynamic_sidebar( 'our-clients' ); ?>
        <?php endif; ?>
    </div>   
</section>
<section id="clients" class="clients lite <?php echo esc_attr($integral['clients-custom-class']); ?>">
	<div class="container">
		<?php if ($integral['clients-maintitle']) { ?>
        <div class="row">
			<div class="col-md-12">			
				<h2 class="smalltitle"><?php echo esc_html($integral['clients-maintitle']); ?><span></span></h2>
			</div>
        </div>
        <?php } ?>
        <?php if ( is_active_sidebar( 'client-widgets' ) ) : ?>
        <div class="row multi-columns-row">
            <?php dynamic_sidebar( 'client-widgets' ); ?>
		</div>
        <?php endif; ?>
	</div>
</section> 
<section id="customized-tailoring" class="customized-tailoring lite">
    <div id="customized_tailoring_widget">
        <?php if ( is_active_sidebar( 'customized-tailoring' ) ) : ?>
                <?php dynamic_sidebar( 'customized-tailoring' ); ?>
        <?php endif; ?>
    </div>   
</section>
<?php } ?>