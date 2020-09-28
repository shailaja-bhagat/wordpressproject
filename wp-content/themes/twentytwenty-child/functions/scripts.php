<?php
add_action( 'wp_ajax_nopriv_filter', 'filter_ajax' );
add_action( 'wp_ajax_filter', 'filter_ajax' );

function filter_ajax(){

    $category = $_POST['category'];

    $args = array([
        'post_type' 	=> 'post',
        'posts_per_page'=> -1,
        'orderby' 		=> 'menu_order', 
        'order' 		=> 'desc',
    ]);

    if(isset($category)){
        $args['category__in'] = array($category);
    }
    
    $ajaxposts = new WP_Query($args);

    $response = '';

    if($ajaxposts->have_posts()) {
        while($ajaxposts->have_posts()) : $ajaxposts->the_post();
        $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); 
        // $response .= ?>
                <div class="col-sm-12 col-md-6">
                    <div class="container">
                        <div class="row inner-post">
                            <div class="col-sm-12 col-md-5">	
                                <div class="post-section">
                                    <div class="post-img-section"> 
                                        <a href="<?= the_permalink(); ?>"> 
                                            <img src="<?= $featured_img_url ?>"> 
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-7">
                                <div class="post-title-section"> 
                                    <a href="<?= the_permalink(); ?>"> 
                                        <h3 class="home-post-title"><?= get_the_title(); ?></h3> 
                                    </a>
                                </div>
                                <div class="post-excerpt-section"> 
                                    <p class="home-post-excerpt">
                                        <?= the_excerpt(); ?>
                                    </p>
                                </div>
                                <div class="post-date-section"> 
                                    <p>
                                        <?= get_the_date(); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        <?php
        endwhile;
    } else {
        $response = 'empty';
    }
    wp_reset_postdata();


    die();
}

?>