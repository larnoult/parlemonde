<?php
/*
Plugin Name: Private Content Login Redirect
Plugin URI: http://increasy.com
Description: This plugin redirects non-logged users to the login page when they follow a link to private post or private page.After successful login, it automatically redirects users with private content access to the private content link they followed.
Author: Kumar Abhisek
Author URI: http://increasy.com
Version:1.0.1
License: GPLv2

 Copyright 2014 Kumar Abhisek (email:meabhi[at]outlook dot com)
 
 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License version 2,
 as published by the Free Software Foundation.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the
 GNU General Public License for more details.
 
 The license for this software can likely be found here:
 http://www.gnu.org/licenses/gpl-2.0.html

*/

add_action('template_redirect', 'private_content_redirect_to_login', 9);
function private_content_redirect_to_login() {
  global $wp_query,$wpdb;
  if (is_404()&&is_page(3791)) {
    $private = $wpdb->get_row($wp_query->request);
 /*   $location = wp_login_url($_SERVER["REQUEST_URI"]);*/
	$location = get_permalink(3793);
    if( 'private' == $private->post_status  ) {
      wp_safe_redirect($location);
      exit;
    }
  }

 if (is_404()&&is_page(3806)) {
   $private = $wpdb->get_row($wp_query->request);
/*   $location = wp_login_url($_SERVER["REQUEST_URI"]);*/
	$location = get_permalink(3812);
   if( 'private' == $private->post_status  ) {
     wp_safe_redirect($location);
     exit;
   }
 }

}