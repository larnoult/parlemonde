<?php

// Default values for main configuration
$sitename = strtolower($_SERVER['SERVER_NAME']);
if (substr($sitename, 0, 4) == 'www.') {
    $sitename = substr($sitename, 4);
}

$options = array(
    'return_path' => '',
    'reply_to' => '',
    'sender_email' => 'newsletter@' . $sitename,
    'sender_name' => get_option('blogname'),
    'editor' => 0,
    'scheduler_max' => 100,
    'phpmailer'=>0,
    'debug'=>0,
    'track'=>1,
    'css'=>'',
    'css_disabled'=>0,
    'ip'=>'',
    'page'=>0,
    'disable_cron_notice'=>0,
    
    'header_logo' => '',
    'header_title' => get_bloginfo('name'),
    'header_sub' => get_bloginfo('description'),
    'footer_title' => '',
    'footer_contact' => '',
    'footer_legal' => '',
    'facebook_url' => '',
    'twitter_url' => '',
    'instagram_url' => '',
    'googleplus_url' => '',
    'pinterest_url' => '',
    'linkedin_url' => '',
    'tumblr_url' => '',
    'youtube_url' => '',
    'vimeo_url' => '',
    'soundcloud_url' => ''
);
