<?php

/*
Plugin Name: Pages Widget Extended
Plugin URI: http://github.com/kasparsj/pages-widget-extended
Description: Extends the default Pages widget with more otions: show content, show thumbnail, choose post type.
Version: 1.0
Author: Kaspars Jaudzems
Author URI: http://kasparsj.wordpress.com
*/

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

function register_pages_widget() {
    require_once('pages_widget.class.php');
    
    unregister_widget("WP_Widget_Pages");
    return register_widget("Pages_Widget");
}

add_action('widgets_init', 'register_pages_widget');

?>
