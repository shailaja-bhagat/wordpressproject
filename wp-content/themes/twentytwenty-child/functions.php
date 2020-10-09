<?php
require_once locate_template( '/functions/enqueues.php' );
require_once locate_template( '/functions/scripts.php' );
require_once locate_template( '/functions/custom-functions.php' );
require_once locate_template( '/functions/custom-posts.php' );

add_action('admin_menu', 'custom_menu');
 
function custom_menu() {
    add_menu_page( 'Custom Menu Page Title', 'Custom Menu Page', 'manage_options', 'custom_menu_page' );
}
function custom_menu_page(){
    echo "hello";
}