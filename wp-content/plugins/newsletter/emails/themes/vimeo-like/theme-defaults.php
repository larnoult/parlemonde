<?php

defined('ABSPATH') || exit;

$theme_defaults = array(
    'theme_max_posts' => 10,
    'theme_read_more' => __('Read More', 'newsletter'),
    'theme_pre_message' => 'This email has been sent to {email} because subscribed and confirmed on ' . get_option('blogname') . '. <a href="{profile_url}">Click here to modify you subscription or unsubscribe</a>.',
    'theme_footer_message' => 'To change your subscription, <a target="_blank"  href="{profile_url}">click here</a>.',
    'theme_categories' => array()
);

