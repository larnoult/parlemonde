<?php 


add_filter( 'wp_nav_menu_items', 'my_nav_menu_profile_link' );
function my_nav_menu_profile_link($menu) { 	
	$user = wp_get_current_user();
	global $user_ID;
	if ( in_array( 'author', (array) $user->roles ) ){ // Si c'est des classes Pelico+

		get_currentuserinfo();
//		$profilelink = '<li><a href="'.get_author_posts_url($user_ID).'/">' . __('Ma classe') . '</a></li>';
		
		$profilelink = '<li class="menu-maclasse sf-dropdown menu-item-30"><a href="'.bp_core_get_user_domain( $user_ID ).'" class="sf-with-ul">Ma classe<span class="sf-sub-indicator"> »</span></a>
		<ul class="sf-dropdown-menu" style="display: none;">
			<li class="menu-monprofil menu-item-33"><a href="'.bp_core_get_user_domain( $user_ID ).'">Mon profil</a></li>
			<li class="menu-mesarticles menu-item-32"><a href="'.get_author_posts_url($user_ID).'">Mes articles</a></li>
		</ul>
		</li>' ;
		$menu = $menu . $profilelink;
		return $menu;				
	}	
	else if (in_array( 'contributor', (array) $user->roles ) | (in_array( 'subscriber', (array) $user->roles ) && (count_user_posts($user_ID)==0))){ // Si c'est une classe Pelico ou si c'est une classe PelicoFLE, n'afficher que le profil, 

		get_currentuserinfo();

		$profilelink = '<li class="menu-maclasse sf-dropdown menu-item-30"><a href="'.bp_core_get_user_domain( $user_ID ).'" class="sf-with-ul">Ma classe<span class="sf-sub-indicator"> »</span></a>
		<ul class="sf-dropdown-menu" style="display: none;">
			<li class="menu-monprofil menu-item-33"><a href="'.bp_core_get_user_domain( $user_ID ).'">Mon profil</a></li>
		</ul>
		</li>' ;
		$menu = $menu . $profilelink;
		return $menu;		
	}
	else if ( in_array( 'subscriber', (array) $user->roles ) && (count_user_posts($user_ID)!=0)){ // Si c'est une classe Alumni, afficher le profil et les anciens articles

		get_currentuserinfo();
		$profilelink = '<li class="menu-maclasse sf-dropdown menu-item-30"><a href="'.bp_core_get_user_domain( $user_ID ).'" class="sf-with-ul">Ma classe<span class="sf-sub-indicator"> »</span></a>
		<ul class="sf-dropdown-menu" style="display: none;">
			<li class="menu-monprofil menu-item-33"><a href="'.bp_core_get_user_domain( $user_ID ).'">Mon profil</a></li>
			<li class="menu-mesarticles menu-item-32"><a href="'.get_author_posts_url($user_ID).'">Mes articles privés</a></li>
		</ul>
		</li>' ;
		$menu = $menu . $profilelink;
		return $menu;		
	}
	else { // Pour l'administrateur ou un visiteur, renvoyer sur une page spéciale !
		global $user_ID;
		get_currentuserinfo();
		$profilelink = '<li class="menu-maclasse sf-dropdown menu-item-30"><a href="http://www.parlemonde.fr/le-coin-des-professeurs-est-reserve-aux-classes-pelico/">' . __('Ma classe') . '</a></li>';
		$menu = $menu . $profilelink;
		return $menu;		
	}
}

/*add_filter( 'wp_nav_menu_items', 'my_nav_menu_classe' );
function my_nav_menu_classe($menu) { 	
	$user = wp_get_current_user();
	if ( in_array( 'author', (array) $user->roles ) ){
		global $user_ID;
		get_currentuserinfo();
		$profilelink = '<li class="menu-maclasse"><a id="menumaclasse" href="'.get_author_posts_url($user_ID).'">' . __('Ma classe') . '</a></li>';
		$menu = $menu . $profilelink;
		return $menu;				
	}	
	else {
		$profilelink = '<li class="menu-maclasse"><a id="menumaclasse" href="http://www.parlemonde.fr/la-page-de-classe-est-reservee-aux-classes-pelico/">' . __('Ma classe') . '</a></li>';
		$menu = $menu . $profilelink;
		return $menu;
	
	}
}*/

function my_login_stylesheet() {
    wp_enqueue_style( 'custom-login', get_stylesheet_directory_uri() . '/login/login-style.css' );
}
add_action( 'login_enqueue_scripts', 'my_login_stylesheet' );
	
function my_login_logo_url() {
return get_bloginfo( 'url' );
}
add_filter( 'login_headerurl', 'my_login_logo_url' );

function my_login_logo_url_title() {
return 'Your Site Name and Info';
}
add_filter( 'login_headertitle', 'my_login_logo_url_title' );

	
function my_custom_scripts() {
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-draggable');
	wp_enqueue_script('jquery-ui-position');
	wp_enqueue_script('jquery-ui-droppable');
	wp_enqueue_script( 'jquery-touch-punch' );
	wp_enqueue_script( 'jquery-effects-highlight' );

    }
add_action( 'wp_enqueue_scripts', 'my_custom_scripts' );


/* To get buddypress and bbpress pages fullwidth*/ 
/*
function bbp_enable_visual_editor( $args = array() ) {
    $args['tinymce'] = true;
    return $args;
}
add_filter( 'bbp_after_get_the_content_parse_args', 'bbp_enable_visual_editor' );
add_filter('kadence_display_sidebar', 'buddypress_sidebar');

function buddypress_sidebar($sidebar) {
  if (is_buddypress()) {
    return false;
  }
  return $sidebar;
}

*/
/* To remove comments from buddypress and bbpress pages */ 
/*
add_filter('kadence_display_sidebar', 'bbpress_sidebar');

function bbpress_sidebar($sidebar) {
  if (is_bbpress()) {
    return false;
  }
  return $sidebar;
}
*/

/* To remove sidebar from tag pages */

add_filter('kadence_display_sidebar', 'tag_sidebar');

function tag_sidebar($sidebar) {
  if (is_tag()) {
    return false;
  }
  return $sidebar;
}

/* To remove sidebar from Les classes nous parlent pages */

add_filter('kadence_display_sidebar', 'classes_sidebar');

function classes_sidebar($sidebar) {
  if (is_tax( 'de-la-vie-a-lecole' )) {
    return false;
  }
  return $sidebar;
}

/* To add to wp-admin the forum link */ 

add_action( 'admin_menu', 'register_my_custom_menu_page' );

function register_my_custom_menu_page() {

	add_menu_page( 'Forum & Kit', 'Forum & Kit', 'edit_posts', 'le-forum-des-professseurs', 'redirect_to_forum', 'dashicons-format-chat', 3 );
//	add_submenu_page( 'annuaire1', 'Mon profil', 'Mon profil', 'manage_options', 'mon_profil', 'my_custom_submenu_page_callback');
}

function redirect_to_forum (){
	wp_redirect( home_url().'/le-forum-des-professeurs' ); exit;
}

/* to change wp-admin title */
add_filter('admin_title', 'my_admin_title', 10, 2);

function my_admin_title($admin_title, $title)
{
    return $title .' &#64; '."Le coin des professeurs";
}


add_action( 'admin_menu', 'register_annuaire' );

function register_annuaire() {

	add_menu_page( 'Annuaire', 'Annuaire', 'edit_posts', 'l-annuaire-des-professseurs', 'redirect_to_annuaire', 'dashicons-universal-access', 7 );
//	add_submenu_page( 'annuaire1', 'Mon profil', 'Mon profil', 'manage_options', 'mon_profil', 'my_custom_submenu_page_callback');
}

function redirect_to_annuaire (){
	wp_redirect( home_url().'/members' ); exit;
}

/* To cancel (just during inscription) mail sending upon password change 

add_filter( 'send_password_change_email', '__return_false');
add_filter( 'send_email_change_email', '__return_false');
*/

/* To load new french translation of virtue */
load_child_theme_textdomain( 'virtue', get_stylesheet_directory() . '/languages' );


// Generated from http://generatewp.com/taxonomy/

if ( ! function_exists( 'custom_taxonomy' ) ) {

// Register Custom Taxonomy
function custom_taxonomy() {

	$labels = array(
		'name'                       => _x( 'Les classes nous parlent', 'Taxonomy General Name', 'text_domain' ),
		'singular_name'              => _x( 'Les classes nous parlent', 'Taxonomy Singular Name', 'text_domain' ),
		'menu_name'                  => __( 'Les classes nous parlent', 'text_domain' ),
		'all_items'                  => __( 'Tous les thèmes', 'text_domain' ),
		'parent_item'                => __( 'Thème parent', 'text_domain' ),
		'parent_item_colon'          => __( 'Thème parent', 'text_domain' ),
		'new_item_name'              => __( 'Nouveau thème ', 'text_domain' ),
		'add_new_item'               => __( 'Rajouter un nouveau thème', 'text_domain' ),
		'edit_item'                  => __( 'Éditer un thème', 'text_domain' ),
		'update_item'                => __( 'Éditer un thème', 'text_domain' ),
		'view_item'                  => __( 'Voir le thème', 'text_domain' ),
		'separate_items_with_commas' => __( 'Séparer les thèmes par des virgules', 'text_domain' ),
		'add_or_remove_items'        => __( 'Rajouter ou enlever des thèmes', 'text_domain' ),
		'choose_from_most_used'      => __( 'Choisir parmi les thèmes les plus utilisés', 'text_domain' ),
		'popular_items'              => __( 'Thèmes populaires', 'text_domain' ),
		'search_items'               => __( 'Rechercher un thème', 'text_domain' ),
		'not_found'                  => __( 'Introuvable', 'text_domain' ),
	);
	$rewrite = array(
		'slug'                       => 'les-classes-nous-parlent',
		'with_front'                 => true,
		'hierarchical'               => false,
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => false,
		'show_tagcloud'              => false,
		'rewrite'                    => $rewrite,
	);
	register_taxonomy( 'schooltheme', array( 'post' ), $args );

}
add_action( 'init', 'custom_taxonomy', 0 );

}

function enqueue_wp_help_stylesheets() {
        wp_enqueue_style( 'mytheme-custom', get_template_directory_uri() . '/wp_help.css' );
}
add_action( 'cws_wp_help_load', 'enqueue_wp_help_stylesheets' );


/* To add the 'new' label in bbpress forum */

function mycustom_new_label_bbp($now, $last_active) {
$now = new DateTime($new);
$last_active = new dateTime($last_active);
$interval = $last_active->diff($now);
$difference = $interval->format('%R%a days');
if($difference < 15) {
echo "<span class='mycustom_new_label'>";
echo "Nouveau";
echo '</span>';

}
}

/* To add a checkbox to sign conditions */


// As part of WP authentication process, call our function
add_filter('wp_authenticate_user', 'wp_authenticate_user_acc', 99999, 2);

function wp_authenticate_user_acc($user, $password) {
    // See if the checkbox #login_accept was checked
    if ( isset( $_REQUEST['login_accept'] ) && $_REQUEST['login_accept'] == 'on' ) {
        // Checkbox on, allow login
        return $user;
    } else {
        // Did NOT check the box, do not allow login
        $error = new WP_Error();
        $error->add('did_not_accept', 'Vous devez accepter les conditions d\'utilisation du site' );
        return $error;
    }
}

// As part of WP login form construction, call our function
add_filter ( 'login_form', 'login_form_acc' );

function login_form_acc(){
    // Add an element to the login form, which must be checked
    echo '<p><label><input type="checkbox" checked="checked" name="login_accept" id="login_accept" /> J\'accepte les <a href="http://www.parlemonde.fr/conditions-dutilisation-pour-les-professeurs-connectes/" target="_blank">conditions d\'utilisation du site</a> </label></p>';
}

/* to restrict non-admin to upload video */
add_filter( 'upload_mimes','gkp_restrict_mime', 999 );
function gkp_restrict_mime($mimes) {
 
    if( !current_user_can( 'administrator' ) ) {
        $forbidden_mimes = array( 'asf|asx|wax|wmv|wmx', 'avi', 'divx', 'flv', 'mov|qt', 'mpeg|mpg|mpe','mp4' );
		
	foreach( $forbidden_mimes as $fm ) {
	    if( isset( $mimes[$fm] ) )
                unset( $mimes[$fm] );
	    }
	}
	
    return $mimes;
}

/* to change default image embedding option */

function options_defaut_img() {
 update_option( 'image_default_align', 'center' );
 update_option( 'image_default_link_type', 'file' );
 update_option( 'image_default_size', 'full' );
}
add_action( 'after_setup_theme', 'options_defaut_img' );
