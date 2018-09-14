<?php
/*
Plugin Name: Network Site Plugin for parlemonde.org
Description: Site specific code changes for parlemonde.org
*/
/* Start Adding Functions Below this Line */


/* To add submenu in subsites admin*/
function add_mysites_link () {
	global $wp_admin_bar;
	if (current_user_can( 'manage_options' ))
		{
			foreach ( (array) $wp_admin_bar->user->blogs as $blog ) {
				$menu_id  = 'blog-' . $blog->userblog_id;
				/* Add a Log Out Link */
				/* Media Admin */
				$wp_admin_bar->add_menu( array(
					'parent' 	=> $menu_id,
					'id' 	=> $menu_id . '-media',
					'title' 	=> __( 'Media' ),
					'href' 	=> get_home_url( $blog->userblog_id, '/wp-admin/upload.php' ) )
				);

				/* Plugin Admin */
				$wp_admin_bar->add_menu( array(
					'parent' 	=> $menu_id,
					'id' 	=> $menu_id . '-plugins',
					'title' 	=> __( 'Extensions' ),
					'href' 	=> get_home_url( $blog->userblog_id, '/wp-admin/plugins.php' ) )
				);
				/* Users Admin */
				$wp_admin_bar->add_menu( array(
					'parent' 	=> $menu_id,
					'id' 	=> $menu_id . '-users',
					'title' 	=> __( 'Utilisateurs' ),
					'href' 	=> get_home_url( $blog->userblog_id, '/wp-admin/users.php' ) )
				);
		}
	
	}
}
add_action( 'wp_before_admin_bar_render', 'add_mysites_link' );

/* To input some CSS common to all sites */
function add_network_scripts() {
 
  wp_enqueue_style( 'global-css', plugin_dir_url( __FILE__ ) . 'global-css.css', array(), '1.1', 'all');
  wp_enqueue_script( 'front-network-script', plugin_dir_url( __FILE__ ) . 'front-network-script.js' );


}
add_action( 'wp_enqueue_scripts', 'add_network_scripts' );


/* To change adminbar look */
function replace_admin_menu_icons_css() {
  wp_enqueue_style( 'global-admin-css', plugin_dir_url( __FILE__ ) . 'global-admin-css.css', array(), '1.1', 'all');
}
add_action( 'admin_head', 'replace_admin_menu_icons_css' );





/* To get search as shortcode */
add_shortcode('wpbsearch', 'get_search_form');


/*
 * Remove the WordPress Logo from the WordPress Admin Bar
 */
function remove_wp_logo() {
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu('wp-logo');
	$wp_admin_bar->remove_menu('kad_options');
	$wp_admin_bar->remove_menu('dem_settings');
	$wp_admin_bar->remove_menu('comments');

/* To remove dashboard access for parents */
	$user = wp_get_current_user();
	if (in_array( 'parents', (array) $user->roles ) || in_array( 'exterieur', (array) $user->roles ) || in_array( 'none', (array) $user->roles )){
		$wp_admin_bar->remove_menu('my-sites');
		$wp_admin_bar->remove_menu('site-name');
	}
	
}
add_action( 'wp_before_admin_bar_render', 'remove_wp_logo' );


/*function hide_update_notice_to_all_but_admin_users()
{
    if (!current_user_can('update_core')) {
	remove_action('welcome_panel', 'wp_welcome_panel');
        remove_action( 'admin_notices', 'update_nag', 3 );
    }
}
add_action( 'admin_head', 'hide_update_notice_to_all_but_admin_users', 1 );
*/
/*

// hide update notifications
function remove_core_updates(){
global $wp_version;return(object) array('last_checked'=> time(),'version_checked'=> $wp_version,);
}
	if (!current_user_can('manage_options')) {
add_filter('pre_site_transient_update_core','remove_core_updates'); //hide updates for WordPress itself
add_filter('pre_site_transient_update_plugins','remove_core_updates'); //hide updates for all plugins
add_filter('pre_site_transient_update_themes','remove_core_updates'); //hide updates for all themes
}
/*

/* To remove dashboard widgets */


add_action('admin_init', 'rw_remove_dashboard_widgets');
function rw_remove_dashboard_widgets() {
 remove_meta_box('dashboard_right_now', 'dashboard', 'normal');   // right now
 remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal'); // recent comments
 remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');  // incoming links
 remove_meta_box('dashboard_plugins', 'dashboard', 'normal');   // plugins

 remove_meta_box('dashboard_quick_press', 'dashboard', 'normal');  // quick press
 remove_meta_box('dashboard_recent_drafts', 'dashboard', 'normal');  // recent drafts
 remove_meta_box('dashboard_primary', 'dashboard', 'normal');   // wordpress blog
 remove_meta_box('dashboard_secondary', 'dashboard', 'normal');   // other wordpress news
}

/* To allow shortcode in category description */
add_filter( 'term_description', 'do_shortcode' );


function my_enqueue($hook) {
  if ( 'edit.php' == $hook || 'post.php' == $hook || $hook == "post-new.php") {
	if ( 'post' == get_post_type() ) {      // only if post_type is the regular WP post type  or portfolio || 'portfolio' == get_post_type()
	    wp_enqueue_script( 'admin-network-scripts', plugin_dir_url( __FILE__ ) . '/UAM-auto.js' );
	        }
	else if ( 'livre' == get_post_type() ) {      // only if post_type is the regular WP post type 
			    wp_enqueue_script( 'admin-network-scripts', plugin_dir_url( __FILE__ ) . '/Livre.js' );
			  }
	    
    }
    wp_enqueue_script( 'admin-network-scripts', plugin_dir_url( __FILE__ ) . '/admin-network-scripts.js' );
}
add_action( 'admin_enqueue_scripts', 'my_enqueue' );


/* To add google analytics in all subdomains */
add_action('wp_head', 'wpb_add_googleanalytics');

function wpb_add_googleanalytics() { ?>
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-51264033-1', 'auto');
	  ga('send', 'pageview');

	</script>
	
<?php }

/* Move admin poll menu */

function poll_toplevel_menu() {
		
	/* add a new menu item */
	add_menu_page(
		'Sondages', // page title
		'Sondages', // menu title
		'edit_posts', // capability
		'democracy-poll', // menu slug	);
		'', // function
		'dashicons-chart-bar', // icon
		90
	);
	
}
add_action( 'admin_menu', 'poll_toplevel_menu' );

function remove_menu() {

	remove_menu_page('edit.php?post_type=staff');
	remove_menu_page('edit.php?post_type=testimonial');
	remove_menu_page('edit.php?post_type=staff');
	remove_menu_page('edit.php?post_type=cycloneslider');

}	
add_action( 'admin_menu', 'remove_menu' );


/* To display date */

function displaydate(){
return date('F j, Y');
}
add_shortcode('date', 'displaydate');

/* register shortcodes for getting author ID */
  function author_ID() {
    return get_the_author_meta( 'ID' );
}
add_shortcode('author', 'author_ID');

//add_filter( 'bd_xprofile_field_type_membertype_as_radio', '__return_true');

/*
/**
 * Filter and limit member types to the following.
 * 
 * @param array $allowed_options an array of member_type => Member type labels
 *
 * @param $registered_types array of member type objects as returned by bp_get_member_types()
 */
/*function buddydev_filter_allowed_xprofile_member_types( $allowed_options, $registered_types ) {
    // say we have 3 registered member types, 'student', 'teacher' and 'staff'
    // And we only want to allow users to select between 'student' or 'teacher'
    $allowed_options = array(
        'explorateur-en-herbe'   =>  'Explorateur en herbe',
        'grand-explorateur'   => 'Grand explorateur'
    );
 
    return $allowed_options;
}
add_filter( 'bp_xprofile_member_type_field_allowed_types', 'buddydev_filter_allowed_xprofile_member_types', 10, 2 );
*/


/**
 * Plugin Name: Multisite: Passwort Reset on Local Blog
 * Plugin URI:  https://gist.github.com/eteubert/293e07a49f56f300ddbb
 * Description: By default, WordPress Multisite uses the main blog for passwort resets. This plugin enables users to stay in their blog during the whole reset process.
 * Version:     1.0.0
 * Author:      Eric Teubert
 * Author URI:  http://ericteubert.de
 * License:     MIT
 */
// fixes "Lost Password?" URLs on login page
add_filter("lostpassword_url", function ($url, $redirect) {	
	
	$args = array( 'action' => 'lostpassword' );
	
	if ( !empty($redirect) )
		$args['redirect_to'] = $redirect;
	return add_query_arg( $args, site_url('wp-login.php') );
}, 10, 2);
// fixes other password reset related urls
add_filter( 'network_site_url', function($url, $path, $scheme) {
  
  	if (stripos($url, "action=lostpassword") !== false)
		return site_url('wp-login.php?action=lostpassword', $scheme);
  
   	if (stripos($url, "action=resetpass") !== false)
		return site_url('wp-login.php?action=resetpass', $scheme);
  
	return $url;
}, 10, 3 );
// fixes URLs in email that goes out.
add_filter("retrieve_password_message", function ($message, $key) {
  	return str_replace(get_site_url(1), get_site_url(), $message);
}, 10, 2);
// fixes email title
add_filter("retrieve_password_title", function($title) {
	return "[" . wp_specialchars_decode(get_option('blogname'), ENT_QUOTES) . "] Choix d'un nouveau mot de passe";
});



/*
 * Multisite Dashboard Widget, from https://wordpress.stackexchange.com/questions/54742/how-to-do-i-get-a-list-of-active-plugins-on-my-wordpress-blog-programmatically
 */
add_action('wp_network_dashboard_setup', 'wpse_54742_network_dashboard_setup');

function wpse_54742_network_dashboard_setup() {
    wp_add_dashboard_widget( 'wpse_54742_active_network_plugins', __( 'Liste des extensions activées' ), 'wpse_54742_active_network_plugins' );
}

function wpse_54742_active_network_plugins() {
    /*
     * Network Activated Plugins
     */
    $the_plugs = get_site_option('active_sitewide_plugins'); 
    echo '<h3>Extensions activées sur le réseau</h3><ul>';
    foreach($the_plugs as $key => $value) {
        $string = explode('/',$key); // Folder name will be displayed
        echo '<li>'.$string[0] .'</li>';
    }
    echo '</ul>';


    /*
     * Iterate Through All Sites
     */
    global $wpdb;
    $blogs = $wpdb->get_results($wpdb->prepare("
        SELECT blog_id
        FROM {$wpdb->blogs}
        WHERE site_id = '{$wpdb->siteid}'
        AND spam = '0'
        AND deleted = '0'
        AND archived = '0'
    "));

    echo '<h3>Extensions activées, site par site</h3>';

    foreach ($blogs as $blog) {
        $the_plugs = get_blog_option($blog->blog_id, 'active_plugins'); 
        echo '<hr /><h4><strong>SITE</strong> : '. get_blog_option($blog->blog_id, 'blogname') .'</h4>';
        echo '<ul>';
        foreach($the_plugs as $key => $value) {
            $string = explode('/',$value); // Folder name will be displayed
            echo '<li>'.$string[0] .'</li>';
        }
        echo '</ul>';
    }
}


?>

