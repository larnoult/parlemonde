<?php

/*
  Plugin Name: Copy Media Url
  Plugin URI: http://techmonastic.com/
  Description: Wordpress Media Url Copy
  Author: Down Town
  Version: 1.0.1
  Author URI: http://techmonastic.com/
 */
add_action('admin_enqueue_scripts', 'dc_media_url_enqueue_script');

add_action('wp_ajax_get_attachment_url','dc_get_attachment_url_callback');

function dc_media_url_enqueue_script($hook) {
    if ('media-new.php' != $hook) {
        return;
    }
    wp_enqueue_script('ZeroClipboard', plugin_dir_url(__FILE__) . 'assets/js/ZeroClipboard.js', array(), '2.2.0', true);
    wp_enqueue_script('media-new-script', plugin_dir_url(__FILE__) . 'assets/js/media-new.js', array('ZeroClipboard'), '1.0.0', true);
    wp_localize_script('media-new-script', 'media_script', array('ajax_url' => admin_url('admin-ajax.php')));
}

function dc_get_attachment_url_callback(){
    $post_id = $_POST['post_id'];
    echo wp_get_attachment_url( $post_id );
    die;
}
