<?php

// Enqueue filter script.js

function load_scripts() {

    wp_enqueue_script('popper-min', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js?'.time(), array('jquery'), true);
	wp_enqueue_script('jquery-min', 'https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js?'.time(), array('jquery'), true);
    wp_enqueue_script('bootstrap-min', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js?'.time(), array('jquery'), true);

    wp_enqueue_script('ajax', '/wp-content/themes/twentytwenty-child/assets/js/post-filter.js?'.time(), array('jquery'), NULL, true );

    wp_localize_script('ajax', 'wp_ajax',
        array('ajax_url' => admin_url('admin-ajax.php'))
    );

    // Enqueue CSS
    wp_register_style( 'bootstrap-css', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css', false, '4.4.0', null );
    wp_enqueue_style( 'bootstrap-css' );
    
    wp_register_style( 'post-filter-css', '/wp-content/themes/twentytwenty-child/assets/css/post-filter.css?', false, '4.4.0', null );
    wp_enqueue_style( 'post-filter-css' );

}

add_action( 'init', 'load_scripts' );
