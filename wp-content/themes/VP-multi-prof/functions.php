<?php
/* To remove sidebars from buddypress and bbpress pages */ 
/*
function bbpress_sidebar($sidebar) {
  if (is_bbpress()) {
    return false;
  }
  return $sidebar;
}

add_filter('kadence_display_sidebar', 'bbpress_sidebar');
*/

/* To style Bbpress, this function works in the two bbpress template files: single-loop 
function mycustom_new_label_bbp($now, $last_active) {
$now = new DateTime($now);
$last_active = new dateTime($last_active);
$interval = $last_active->diff($now);
$difference = $interval->format('%R%a days');
	if($difference < 8) {
echo "<span class='mycustom_new_label'>";
echo "Nouveau";
echo '</span>';
	}
}*/
/*
function custom_buddypress_sidebar($sidebar) {
  if (is_buddypress()) {
    return false;
  }
  return $sidebar;
}
add_filter('kadence_display_sidebar', 'custom_buddypress_sidebar');
*/

/* To remove stuff from the admin bar */
function update_adminbar($wp_adminbar) {

  // remove unnecessary items
  $wp_adminbar->remove_node('customize');
  $wp_adminbar->remove_node('kad_options');
  $wp_adminbar->remove_node('jwl_links');
  $wp_adminbar->remove_node('WPML_ALS');
$user = wp_get_current_user();
if ( in_array( 'author', (array) $user->roles ) ) {
   $wp_adminbar->remove_node('new-content');
   $wp_adminbar->remove_node('lmm'); 
}

}
// admin_bar_menu hook
add_action('admin_bar_menu', 'update_adminbar', 999);


function my_remove_menu_pages() {
	if ( in_array( 'author', (array) $user->roles ) ) {
		remove_menu_page('edit.php?post_type=sdm_downloads');	
	}
	
}
add_action( 'admin_menu', 'my_remove_menu_pages' );


/* -----------------------------------------------------------------------------
/* To add menu to admin lateral menu in the back office <- deprecated in prof.parlemonde.org */
/* -----------------------------------------------------------------------------
/
/* To add to wp-admin the forum link */ 
/*
add_action( 'admin_menu', 'register_my_custom_menu_page' );

function register_my_custom_menu_page() {

	add_menu_page( 'Forum & Kit', 'Forum & Kit', 'edit_posts', 'le-forum-des-professseurs', 'redirect_to_forum', 'dashicons-format-chat', 3 );
//	add_submenu_page( 'annuaire1', 'Mon profil', 'Mon profil', 'manage_options', 'mon_profil', 'my_custom_submenu_page_callback');
}

function redirect_to_forum (){
	wp_redirect( home_url().'/le-forum-des-professeurs' ); exit;
}
function register_annuaire() {

	add_menu_page( 'Annuaire', 'Annuaire', 'edit_posts', 'l-annuaire-des-professseurs', 'redirect_to_annuaire', 'dashicons-universal-access', 7 );
//	add_submenu_page( 'annuaire1', 'Mon profil', 'Mon profil', 'manage_options', 'mon_profil', 'my_custom_submenu_page_callback');
}

function redirect_to_annuaire (){
	wp_redirect( home_url().'/les-professeurs-partenaires/' ); exit;
}

*/
/* to change wp-admin title */
add_filter('admin_title', 'my_admin_title', 10, 2);

function my_admin_title($admin_title, $title)
{
    return $title .' &#64; '."Le coin des professeurs";
}


//add_action( 'admin_menu', 'register_annuaire' );


function my_login_stylesheet() {
    wp_enqueue_style( 'custom-login', get_stylesheet_directory_uri() . '/login/login-style.css' );
}
add_action( 'login_enqueue_scripts', 'my_login_stylesheet' );

/* to associate LMS lateral view */

function my_llms_sidebar_function( $id ) {
	$my_sidebar_id = 'sidebar-primary'; // replace this with your theme's sidebar ID
	return $my_sidebar_id;
}
add_filter( 'llms_get_theme_default_sidebar', 'my_llms_sidebar_function' );

/* To remove author info in LMS
function my_late_init() {
  
	remove_action( 'lifterlms_single_course_after_summary', 'lifterlms_template_course_author', 40 );
  
}
add_action( 'init', 'my_late_init', 15 );*/


add_action( 'wp_enqueue_scripts', 'mytheme_scripts' );
/**
 * Enqueue Dashicons style for frontend use
 */
function mytheme_scripts() {
	wp_enqueue_style( 'dashicons' );
}



/*To clean the profile interface profile.php*/
function remove_personal_options(){
    echo '<script type="text/javascript">jQuery(document).ready(function($) {
  
$(\'form#your-profile > h2:first\').remove(); // remove the "Personal Options" title
  
$(\'form#your-profile tr.user-rich-editing-wrap\').remove(); // remove the "Visual Editor" field
  
$(\'form#your-profile tr.user-admin-color-wrap\').remove(); // remove the "Admin Color Scheme" field
  
$(\'form#your-profile tr.user-comment-shortcuts-wrap\').remove(); // remove the "Keyboard Shortcuts" field
  
$(\'form#your-profile tr.user-admin-bar-front-wrap\').remove(); // remove the "Toolbar" field
  
$(\'form#your-profile tr.user-language-wrap\').remove(); // remove the "Language" field
  
$(\'form#your-profile tr.user-first-name-wrap\').remove(); // remove the "First Name" field
  
$(\'form#your-profile tr.user-last-name-wrap\').remove(); // remove the "Last Name" field
  
$(\'form#your-profile tr.user-nickname-wrap\').hide(); // Hide the "nickname" field
  
$(\'table.form-table tr.user-display-name-wrap\').remove(); // remove the “Display name publicly as” field
  
$(\'table.form-table tr.user-url-wrap\').remove();// remove the "Website" field in the "Contact Info" section
  
$(\'h2:contains("A propos de vous"), h3:contains("Extra profile information")\').remove(); // remove the "About Yourself" and "About the user" titles
  
$(\'form#your-profile tr.user-description-wrap\').remove(); // remove the "Biographical Info" field
  
$(\'form#your-profile tr.user-profile-picture\').remove(); // remove the "Profile Picture" field


$(\'h2:contains("LifterLMS Extra Information")\').remove(); // remove the "About Yourself" and "About the user" titles
  
$(\'form#your-profile tr.user-llms_billing_address_1-wrap\').remove(); // remove LMS
$(\'form#your-profile tr.user-llms_billing_address_2-wrap\').remove(); // remove LMS
$(\'form#your-profile tr.user-llms_billing_city-wrap\').remove(); // remove LMS
$(\'form#your-profile tr.user-llms_billing_state-wrap\').remove(); // remove LMS
$(\'form#your-profile tr.user-llms_billing_zip-wrap\').remove(); // remove LMS
$(\'form#your-profile tr.user-llms_billing_country-wrap\').remove(); // remove LMS
$(\'form#your-profile tr.user-llms_phone-wrap\').remove(); // remove LMS
$(\'form#your-profile > h2:nth-child(15)\').remove(); // remove LMS


$(\'form#your-profile table.form-table\:last-of-type\').remove(); // remove extraProfile table




});</script>';
  
}

$pagesToAffect = [
    '/wp-admin/profile.php'
];

$user = wp_get_current_user();
global $user_ID;
if ( in_array( 'subscriber', (array) $user->roles ) && isset($PHP_SELF) && in_array($PHP_SELF, $pagesToAffect))  {
add_action('admin_head','remove_personal_options');
}

/* To add topbar if not connected*/
add_action('virtue_after_body', 'custom_add_topbar_after_body', 20);
function custom_add_topbar_after_body() {
    if ( !is_user_logged_in() ) {
	get_template_part('templates/header', 'topbar');
    } 
	echo '<div id="toggle-menu">-</div>'; // Add toggle-menu
}


//filter to add description after forums titles on forum index
function rw_singleforum_description() {
  echo '<div class="bbp-forum-content">';
  echo bbp_forum_content();
  echo '</div>';
}
add_action( 'bbp_template_before_single_forum' , 'rw_singleforum_description');





function my_custom_scripts() {
	wp_enqueue_script( 'toggle-menu',get_theme_root_uri().'/scripts/toggle-menu.js' );	
		if (  is_page(86) ) {
			wp_enqueue_script('sozi',get_theme_root_uri().'/scripts/sozi-pelico.js' );
	}
    }
add_action( 'wp_enqueue_scripts', 'my_custom_scripts' );

function bbp_enable_visual_editor( $args = array() ) {
    $args['tinymce'] = true;
    return $args;
}
add_filter( 'bbp_after_get_the_content_parse_args', 'bbp_enable_visual_editor' );


/* To remove lifterLMS filters */
remove_filter( 'show_admin_bar', 'llms_disable_admin_bar', 10, 1 );

remove_filter( 'lostpassword_url', 'llms_lostpassword_url', 10, 0 );

/* To redirect users if they are not connected, and wish to attempt forum */

//redirect user to log in page if accessing forum from external link
add_action( 'template_redirect', 'forum_redirect_from_external_link' );
function forum_redirect_from_external_link() {
  $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  $actual_link = explode('/',$actual_link);
  if($actual_link[3]=="forums" && !is_user_logged_in() ) {
    wp_redirect( home_url('les-forums-ne-sont-accessibles-quaux-professeurs-connectes/') ); 
      exit;
  }
}