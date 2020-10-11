<?php 

function owp_widgets_init() {
 
    register_sidebar( array(
        'name'          => 'Our Story',
        'id'            => 'our-story',
        'before_widget' => '<div class="our_story_widget text-center"><div class="container">',
        'after_widget'  => '</div></div>',
        'before_title'  => '<h1 class="our-story-title">',
        'after_title'   => '</h1>',
    ) );
    register_sidebar( array(
        'name'          => 'Our Team',
        'id'            => 'our-team',
        'before_widget' => '<div class="our_team_widget"><div class="container">',
        'after_widget'  => '</div></div>',
        'before_title'  => '<h2 class="our_team-title">',
        'after_title'   => '</h2>',
    ) );
    register_sidebar( array(
        'name'          => 'Our Strategy',
        'id'            => 'our-strategy',
        'before_widget' => '<div class="our_strategy_widget"><div class="container">',
        'after_widget'  => '</div></div>',
        'before_title'  => '<h2 class="our_strategy-title">',
        'after_title'   => '</h2>',
    ) );
    register_sidebar( array(
        'name'          => 'Our Pride Work',
        'id'            => 'pride-work',
        'before_widget' => '<div class="pride_work_widget"><div class="container">',
        'after_widget'  => '</div></div>',
        'before_title'  => '<h2 class="pride_work-title">',
        'after_title'   => '</h2>',
    ) );
    register_sidebar( array(
        'name'          => 'Technology',
        'id'            => 'ra-technology',
        'before_widget' => '<div class="ra_technology_widget"><div class="container">',
        'after_widget'  => '</div></div>',
        'before_title'  => '<h2 class="ra_technology-title">',
        'after_title'   => '</h2>',
    ) );
    register_sidebar( array(
        'name'          => 'Gallery Section',
        'id'            => 'img-gallery',
        'before_widget' => '<div class="img_gallery_widget"><div class="container">',
        'after_widget'  => '</div></div>',
        'before_title'  => '<h2 class="img_gallery-title">',
        'after_title'   => '</h2>',
    ) );
    register_sidebar( array(
        'name'          => 'Who work with us Our Clients',
        'id'            => 'our-clients',
        'before_widget' => '<div class="our_clients_widget"><div class="container">',
        'after_widget'  => '</div></div>',
        'before_title'  => '<h2 class="our_clients-title">',
        'after_title'   => '</h2>',
    ) );
    register_sidebar( array(
        'name'          => 'Customized Tailoring',
        'id'            => 'customized-tailoring',
        'before_widget' => '<div class="customized-tailoring_widget"><div class="container">',
        'after_widget'  => '</div></div>',
        'before_title'  => '<h2 class="customized-tailoring-title">',
        'after_title'   => '</h2>',
    ) );
}
add_action( 'widgets_init', 'owp_widgets_init' );