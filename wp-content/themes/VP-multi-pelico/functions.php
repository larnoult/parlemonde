<?php 



/* To indicate wether there is a new french post*/ 
add_filter( 'wp_nav_menu_items', 'my_nav_menu_classe' );
function my_nav_menu_classe($menu) { 	
		$profilelink = '<li class="menu-maclasse"><a id="menumaclasse" href="http://www.parlemonde.fr/la-page-de-classe-est-reservee-aux-classes-pelico/">' . __('Ma classe') . '</a></li>';
	//	$menu = $menu . $profilelink;
	if (mnp_new_posts_count()!=0){
		$newpost = '<li id="new-post-alert"><span id="post-counter"> '.mnp_new_posts_count().'</span></li>';
		$menu = $menu .	$newpost;
	}
		return $menu;
}

function my_login_stylesheet() {
    wp_enqueue_style( 'custom-login', get_stylesheet_directory_uri() . '/login/login-style.css' );
}
add_action( 'login_enqueue_scripts', 'my_login_stylesheet' );


function my_custom_scripts() {
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-draggable');
	wp_enqueue_script('jquery-ui-position');
	wp_enqueue_script('jquery-ui-droppable');
	wp_enqueue_script( 'jquery-touch-punch' );
	wp_enqueue_script( 'jquery-effects-highlight' );
	wp_enqueue_script( 'jquery.arctext',get_theme_root_uri().'/scripts/jquery.arctext.js' );
	wp_enqueue_script( 'jQuery-curve-home.js',get_theme_root_uri().'/scripts/jQuery-curve-home.js' );
	wp_enqueue_script( 'toggle-menu',get_theme_root_uri().'/scripts/toggle-menu.js' );
	if (  is_single("2740") ) {
	wp_enqueue_script( 'enigme-famille-2',get_theme_root_uri().'/scripts/enigme-famille-2.js' );
}
	 if (is_single()){
		wp_enqueue_script( 'focusQC',get_theme_root_uri().'/scripts/focusQC.js' );
}

	$member_type = bp_get_member_type( get_current_user_id() ); 
	if ($member_type=="explorateur-en-herbe") {
		wp_enqueue_script( 'removeBPform',get_theme_root_uri().'/scripts/removeBPform.js' );
	}
}
/*
if ( is_front_page()) {
wp_enqueue_script( 'new-menu',get_theme_root_uri(). '/scripts/new-menu.js' );
    }*/

add_action( 'wp_enqueue_scripts', 'my_custom_scripts' );



/* To get buddypress and bbpress pages fullwidth

function bbp_enable_visual_editor( $args = array() ) {
    $args['tinymce'] = true;
    return $args;
}
add_filter( 'bbp_after_get_the_content_parse_args', 'bbp_enable_visual_editor' );
*/ 

/* To remove comments from buddypress and bbpress pages */ 
/* -> Carefull, if buddypress and bbpress desactivated, induces a 500 internal page bug; */
//add_filter('kadence_display_sidebar', 'bbpress_sidebar');
/*
function bbpress_sidebar($sidebar) {
  if (is_bbpress()) {
    return false;
  }
  return $sidebar;
}*/

add_filter('kadence_display_sidebar', 'buddypress_sidebar');

function buddypress_sidebar($sidebar) {
  if (is_buddypress()) {
    return false;
  }
  return $sidebar;
}

/* To remove sidebar from tag pages 

add_filter('kadence_display_sidebar', 'tag_sidebar');

function tag_sidebar($sidebar) {
  if (is_tag()) {
    return false;
  }
  return $sidebar;
}*/

/* To remove sidebar from Les classes nous parlent pages */

add_filter('kadence_display_sidebar', 'classes_sidebar');

function classes_sidebar($sidebar) {
  if (is_tax( 'de-la-vie-a-lecole' )) {
    return false;
  }
  return $sidebar;
}

/* To cancel (just during inscription) mail sending upon password change 

add_filter( 'send_password_change_email', '__return_false');
add_filter( 'send_email_change_email', '__return_false');
*/

/* To load new french translation of virtue */
load_child_theme_textdomain( 'virtue', get_stylesheet_directory() . '/languages' );



/*
function enqueue_wp_help_stylesheets() {
        wp_enqueue_style( 'mytheme-custom', get_template_directory_uri() . '/wp_help.css' );
}
add_action( 'cws_wp_help_load', 'enqueue_wp_help_stylesheets' );
*/

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

/* To add a checkbox to sign conditions 

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
}*/

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




// Remove Buddypress links in Admin toolbar
function update_adminbar($wp_adminbar) {

  // remove unnecessary items
  $wp_adminbar->remove_node('my-account-activity');
  $wp_adminbar->remove_node('my-account-friends');
  $wp_adminbar->remove_node('my-account-groups');
  $wp_adminbar->remove_node('my-account-settings');
 // $wp_adminbar->remove_node('my-account-xprofile');
  $wp_adminbar->remove_node('edit-profile');
  $wp_adminbar->remove_node('customize');
  $wp_adminbar->remove_node('kad_options');
  $wp_adminbar->remove_node('WPML_ALS');
  $wp_adminbar->remove_node('jwl_links');
  $wp_adminbar->remove_node('dem_settings');
  $wp_adminbar->remove_node('lmm'); 

	$user = wp_get_current_user();
	if ( in_array( 'author', (array) $user->roles ) ) {
	   $wp_adminbar->remove_node('new-content');
	}
	/*
	else if (in_array( 'parents', (array) $user->roles )){
	   $wp_adminbar->remove_node('lmm'); 
	}*/
	
	

}
// admin_bar_menu hook
add_action('admin_bar_menu', 'update_adminbar', 999);


function my_remove_menu_pages() {
		$user = wp_get_current_user();
	if ( in_array( 'author', (array) $user->roles ) ) {
		remove_menu_page('edit.php?post_type=sdm_downloads');	
		remove_menu_page('profile.php');	
	}
}
add_action( 'admin_menu', 'my_remove_menu_pages' );


// add a link with class posts, regional posts and favorites to the right top, for authors (teachers) VS get children posts for parents 
function custom_toolbar_link($wp_admin_bar) {
	
	
	//author category
	function get_user_cat($user_id = null){
		if ($user_id === null){
			global $current_user;
			get_currentuserinfo();
			$user_id = $current_user->ID;
		}
		$cat = get_user_meta($user_id,'_author_cat',true);
		if (empty($cat) || count($cat) <= 0 || !is_array($cat))
			return 0;
		else
			return $cat[0];
	}
	
		$user = wp_get_current_user();
		global $user_ID;



/* For exterieur, add only favorites in buddypress, and reinsert custom navigation menu */

if (in_array( 'exterieur', (array) $user->roles )){
	
	// Add navigation menu
	    $args = array(
	        'id' => 'menu-exterieur',
	        'title' => 'Menu', 
	        'href' => 'http://www.parlemonde.org', 
	        'meta' => array(
	            'class' => 'menu', 
	            'title' => 'Portail Par Le Monde'
	            )
	    );
	    $wp_admin_bar->add_node($args);


		// Add another child link
		$args = array(
		        'id' => 'l-association',
		        'title' => 'L\'association', 
		        'href' => 'http://asso.parlemonde.org/',
		        'parent' => 'menu-exterieur', 
		        'meta' => array(
		            'class' => 'menu', 
		            'title' => 'L\'association'
		            )
		    );
		    $wp_admin_bar->add_node($args);

			// Add the first child link 

			    $args = array(
			        'id' => 'le-voyage-de-pelico',
			        'title' => 'Le Voyage de Pelico', 
			        'href' => 'http://pelico.parlemonde.org',
			        'parent' => 'menu-exterieur', 
			        'meta' => array(
			            'class' => 'menu', 
			            'title' => 'Le Voyage de Pelico'
			            )
			    );
			    $wp_admin_bar->add_node($args);

			// Add another child link
			$args = array(
			        'id' => 'les-anciens-reportages',
			        'title' => 'Les anciens reportages', 
			        'href' => 'http://visa.parlemonde.org/',
			        'parent' => 'menu-exterieur', 
			        'meta' => array(
			            'class' => 'menu', 
			            'title' => 'Les anciens reportages'
			            )
			    );
			    $wp_admin_bar->add_node($args);	

	// Add another child link
	$args = array(
	        'id' => 'le-coin-des-professeurs',
	        'title' => 'Le coin des professeurs', 
	        'href' => 'http://prof.parlemonde.org/',
	        'parent' => 'menu-exterieur', 
	        'meta' => array(
	            'class' => 'menu', 
	            'title' => 'Le coin des professeurs'
	            )
	    );
	    $wp_admin_bar->add_node($args);
	
	
	
	// Add favorite in BP
	$args = array(
		'id' => 'favorited-post',
		'title' => 'Mes reportages favoris',
		'parent' => 'my-account-buddypress', 
		'href' => "http://pelico.parlemonde.org/mes-reportages-favoris/", 
		'meta' => array(
			'class' => 'favorited-posts', 
			'title' => 'Mes reportages favoris'
			)
	);
	$wp_admin_bar->add_node($args);
	
}

/* For parents, add both child posts links, and favorites */		
		if (in_array( 'parents', (array) $user->roles )){
			// Add navigation menu
			    $args = array(
			        'id' => 'menu-exterieur',
			        'title' => 'Menu', 
			        'href' => 'http://www.parlemonde.org', 
			        'meta' => array(
			            'class' => 'menu', 
			            'title' => 'Portail Par Le Monde'
			            )
			    );
			    $wp_admin_bar->add_node($args);


				// Add another child link
				$args = array(
				        'id' => 'l-association',
				        'title' => 'L\'association', 
				        'href' => 'http://asso.parlemonde.org/',
				        'parent' => 'menu-exterieur', 
				        'meta' => array(
				            'class' => 'menu', 
				            'title' => 'L\'association'
				            )
				    );
				    $wp_admin_bar->add_node($args);

					// Add the first child link 

					    $args = array(
					        'id' => 'le-voyage-de-pelico',
					        'title' => 'Le Voyage de Pelico', 
					        'href' => 'http://pelico.parlemonde.org',
					        'parent' => 'menu-exterieur', 
					        'meta' => array(
					            'class' => 'menu', 
					            'title' => 'Le Voyage de Pelico'
					            )
					    );
					    $wp_admin_bar->add_node($args);

					// Add another child link
					$args = array(
					        'id' => 'les-anciens-reportages',
					        'title' => 'Les anciens reportages', 
					        'href' => 'http://visa.parlemonde.org/',
					        'parent' => 'menu-exterieur', 
					        'meta' => array(
					            'class' => 'menu', 
					            'title' => 'Les anciens reportages'
					            )
					    );
					    $wp_admin_bar->add_node($args);	

			// Add another child link
			$args = array(
			        'id' => 'le-coin-des-professeurs',
			        'title' => 'Le coin des professeurs', 
			        'href' => 'http://prof.parlemonde.org/',
			        'parent' => 'menu-exterieur', 
			        'meta' => array(
			            'class' => 'menu', 
			            'title' => 'Le coin des professeurs'
			            )
			    );
			    $wp_admin_bar->add_node($args);
		
		// Add BP 
			$teachername= xprofile_get_field_data('342', $user->ID); // gets teacher's user_login
			$teacherId=get_userdatabylogin($teachername)->ID; // gets teacher's user ID
			$args = array(
				'id' => 'child-post',
				'title' => 'Les reportages de mon enfant',
				'parent' => 'my-account-buddypress', 
				'href' => get_author_posts_url($teacherId), 
				'meta' => array(
					'class' => 'child-posts', 
					'title' => 'Les reportages de mon enfant'
					)
			);
			$wp_admin_bar->add_node($args);
			
			$args = array(
				'id' => 'favorited-post',
				'title' => 'Mes reportages favoris',
				'parent' => 'my-account-buddypress', 
				'href' => "http://pelico.parlemonde.org/mes-reportages-favoris/", 
				'meta' => array(
					'class' => 'favorited-posts', 
					'title' => 'Mes reportages favoris'
					)
			);
			$wp_admin_bar->add_node($args);
			
		}

		else if ( in_array( 'subscriber', (array) $user->roles )){ // Si c'est des classes EH, rajouter juste les favoris
			$args = array(
				'id' => 'favorited-post',
				'title' => 'Mes reportages favoris',
				'parent' => 'my-account-buddypress', 
				'href' => "http://pelico.parlemonde.org/mes-reportages-favoris/", 
				'meta' => array(
					'class' => 'favorited-posts', 
					'title' => 'Mes reportages favoris'
					)
			);
			$wp_admin_bar->add_node($args);
		
		}
		
		else if ( in_array( 'author', (array) $user->roles ) or is_super_admin($user_ID)){ // Si c'est des classes Pelico+, rajouter le lien de classe
			get_currentuserinfo();
			$args = array(
				'id' => 'classroom-post',
				'title' => 'Les reportages de ma classe',
				'parent' => 'my-account-buddypress', 
				'href' => get_author_posts_url($user_ID), 
				'meta' => array(
					'class' => 'classroom-posts', 
					'title' => 'Les reportages de ma classe'
					)
			);
			$wp_admin_bar->add_node($args);
			
			
/* Add regional posts */

	/*		
			$link=get_user_cat($user_ID);
			if ($link=="0"){ // no regional post
				$link="http://pelico.parlemonde.org/lancez-vous/";
						$args = array(
							'id' => 'regional-group-post',
							'title' => 'Les reportages de ma région',
							'parent' => 'my-account-buddypress', 
							'href' => $link, 
							'meta' => array(
								'class' => 'regional-group-posts', 
								'title' => 'Les reportages de ma région'
								)
						);
			}
			
			else{ // there are regional posts
			$args = array(
				'id' => 'regional-group-post',
				'title' => 'Les reportages de ma région',
				'parent' => 'my-account-buddypress', 
				'href' => get_category_link($link), 
				'meta' => array(
					'class' => 'regional-group-posts', 
					'title' => 'Les reportages de ma région'
					)
			);
			}*/
			
			$wp_admin_bar->add_node($args);
			
/* Adding favorited posts */			
			$args = array(
				'id' => 'favorited-post',
				'title' => 'Mes reportages favoris',
				'parent' => 'my-account-buddypress', 
				'href' => "http://pelico.parlemonde.org/mes-reportages-favoris/", 
				'meta' => array(
					'class' => 'favorited-posts', 
					'title' => 'Mes reportages favoris'
					)
			);
			$wp_admin_bar->add_node($args);
			
		}
	

}

add_action('admin_bar_menu', 'custom_toolbar_link', 999);




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
    echo '<p><label><input type="checkbox" checked="checked" name="login_accept" id="login_accept" /> J\'accepte les <a href="http://pelico.parlemonde.org/conditions-dutilisation-pour-les-professeurs-connectes/" target="_blank">conditions d\'utilisation du site</a> </label></p>';
}

/*
function menu_page_removal() {
    global $menu;
    $menu[25.071][0] = 'Cartes';
}
add_action( 'admin_menu', 'menu_page_removal');


if (!function_exists('debug_admin_menus')):
function debug_admin_menus() {
    global $submenu, $menu, $pagenow;
  //  if ( current_user_can('manage_options') ) { // ONLY DO THIS FOR ADMIN
        if( $pagenow == 'index.php' ) {  // PRINTS ON DASHBOARD
            echo '<pre>'; print_r( $menu ); echo '</pre>'; // TOP LEVEL MENUS
            echo '<pre>'; print_r( $submenu ); echo '</pre>'; // SUBMENUS
//        }
    }
}
add_action( 'admin_notices', 'debug_admin_menus' );
endif;*/

/* To get buddypress and bbpress pages fullwidth*/ 

function bbp_enable_visual_editor( $args = array() ) {
    $args['tinymce'] = true;
    return $args;
}
add_filter( 'bbp_after_get_the_content_parse_args', 'bbp_enable_visual_editor' );


/* To add topbar if not connected*/
add_action('virtue_after_body', 'custom_add_topbar_after_body', 20);
function custom_add_topbar_after_body() {
    if ( !is_user_logged_in() ) {
	get_template_part('templates/header', 'topbar');
    } 
	echo '<div id="toggle-menu">-</div>';
}

/* To add a "Ajouter une vidéo" button */
add_action('media_buttons', 'add_video_button',10);
function add_video_button() {
    echo '<span data-balloon-length="large" data-balloon="Mettez votre vidéo d\'abord en ligne sur Vimeo, puis collez son url dans votre web-reportage. Les codes d\'accès Vimeo sont disponibles dans le cours Web du coin des professeurs." data-balloon-pos="up"><a href="https://vimeo.com/fr/upload" target="_blank" id="insert-video-vimeo" class="button"> <span class="wp-media-buttons-icon" id="vimeoIcone"></span>Ajouter une vidéo</a></span>';
}


function remove_image_option() {
	wp_enqueue_script( 'remove-media-options',get_theme_root_uri().'/scripts/remove-media-options.js', array('jquery'), '1.0', true );

}
add_action('wp_enqueue_media', 'remove_image_option');


function pippin_gallery_shortcode( $atts, $content = null ) {
	
	extract( shortcode_atts( array(
      'size' => ''
      ), $atts ) );
	
	$image_size = 'medium';
	if($size =! '') { $image_size = $size; }
	
	$images = get_children(array(
		'post_parent' => get_the_ID(),
		'post_type' => 'attachment',
		'numberposts' => -1,
		'post_mime_type' => 'image',
		)
	);
		
	if($images) {
		$gallery = '<div class="gallery clearfix kad-light-wp-gallery">';
		foreach( $images as $image ) {
			$gallery .= '<div>';
				$gallery .= '<div class="gallery-image"><a href="' . wp_get_attachment_url($image->ID) . '" rel="shadowbox">';
					$gallery .= wp_get_attachment_image($image->ID, $image_size);
				$gallery .= '</a></div>';
			$gallery .= '</div>';
		}
		$gallery .= '</div>';
		
		return $gallery;
	}
	
}
add_shortcode('photo_gallery', 'pippin_gallery_shortcode');

/* Remove Pod shortcode */
add_action( 'admin_init', 'remove_pods_shortcode_button', 14 );
/*
function remove_pods_shortcode_button () {
    remove_action( 'media_buttons', array( PodsInit::$admin, 'media_button' ), 12 );
}*/





/* ALL MODIFICATIONS FOR THE BOOK */ 

/* Register livre sidebar */
/*
add_action( 'widgets_init', 'theme_slug_widgets_init' );
function theme_slug_widgets_init() {
    register_sidebar( array(
        'name' => __( 'Widget livre', 'theme-slug' ),
        'id' => 'sidebar-livre',
        'description' => __( 'Widgets in this area will be shown on all livre contributions.', 'theme-slug' ),
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
	'after_widget'  => '</li>',
	'before_title'  => '<h2 class="widgettitle">',
	'after_title'   => '</h2>',
    ) );
}

add_filter('kadence_sidebar_id', 'custompost_sidebar_id');
function custompost_sidebar_id($sidebar) {
  if(is_singular( 'livre' )||is_tax()||is_page('le-livre')||is_post_type_archive('livre')) {
      $sidebar = 'sidebar-livre';
  }
  return $sidebar;
}
*/

/* To tweak display of information */
/*function my_author_data( $author ) {
   $url = get_author_posts_url( $author );
   $display_name = get_the_author_meta( 'display_name', $author );
   /* I'm escaping the quotes so I can just return a string */
  /* return "<a href=\"$url\">$display_name</a>";
}
function my_taxonomy_link($ID){
	return the_terms( $ID, 'reportage_de_pelico', 'Réaction au reportage : ', ' / ' );
//	return "<a href=\"$url\">$display_name</a>";
//return $post->ID;
}

function my_day($ID){
	$dateJ=get_the_date('j',$ID);
	$dateM=get_the_date('F Y',$ID);
	return "<span class=\"postday\">$dateJ</span> $dateM";
}

function my_excerpt($ID){
	$content = get_post_field('post_content', $ID);
	return wp_trim_words( $content, 55 ) ;
}

/* To tweak archive page so it displays both posts and book contribution 
-> issue: it does mix both in the back end// */
/*
function namespace_add_custom_types( $query ) {
  if( is_category() || is_tag() || is_author() && empty( $query->query_vars['suppress_filters'] ) ) {
    $query->set( 'post_type', array(
     'post', 'nav_menu_item', 'livre'
		));
	  return $query;
	}
}
add_filter( 'pre_get_posts', 'namespace_add_custom_types' );*/




/**
 * Enable unfiltered_html capability for Editors. : carefull, this allow editors to access admin rights (if they are geeks)
 *
 * @param  array  $caps    The user's capabilities.
 * @param  string $cap     Capability name.
 * @param  int    $user_id The user ID.
 * @return array  $caps    The user's capabilities, with 'unfiltered_html' potentially added.
 */
function km_add_unfiltered_html_capability_to_editors( $caps, $cap, $user_id ) {
	if ( 'unfiltered_html' === $cap && user_can( $user_id, 'editor' ) ) {
		$caps = array( 'unfiltered_html' );
	}
	return $caps;
}
add_filter( 'map_meta_cap', 'km_add_unfiltered_html_capability_to_editors', 1, 3 );



/* To allow autocomplete site-wide
*/
add_filter( 'bp_activity_maybe_load_mentions_scripts', 'buddydev_enable_mention_autosuggestions', 10, 2 );
 
function buddydev_enable_mention_autosuggestions( $load, $mentions_enabled ) {
    
    if( ! $mentions_enabled ) {
        return $load;//activity mention is  not enabled, so no need to bother
    }
    //modify this condition to suit yours
    if( is_user_logged_in() && bp_is_current_component( 'mediapress' ) ) {
        $load = true;
    }
    
    return $load;
}

/* Enabling the imath's activities in portfolio */
/**
 * Enables the Excerpt meta box in Page edit screen.
 */
function Pelicoactivities() {
	add_post_type_support( 'portfolio', 'activites_de_publication' );
}
add_action( 'init', 'Pelicoactivities' );








// Add Widget for Peliquestions


/* Widget Cartes and PeliQuestion. WARNING remove Widget Cartes */
// Register and load the widget
function wpb_load_widget() {
    register_widget( 'wpb_widget' );
    register_widget( 'wpBP_widget' );

}
add_action( 'widgets_init', 'wpb_load_widget' );

 
// Creating the widget 
class wpb_widget extends WP_Widget {
 
function __construct() {
parent::__construct(
 
// Base ID of your widget
'wpb_widget', 
 
// Widget name will appear in UI
__('Carte de la classe', 'wpb_widget_domain'), 
 
// Widget description
array( 'description' => __( 'Permet d\'afficher l\'adresse de la classe dont on consulte le web-reportage', 'wpb_widget_domain' ), ) 
);
}
 
// Creating widget front-end
 
public function widget( $args, $instance ) {
$title = apply_filters( 'widget_title', $instance['title'] );
 
// before and after widget arguments are defined by themes
echo $args['before_widget'];
if ( ! empty( $title ) )
echo $args['before_title'] . $title . $args['after_title'];
 
// This is where you run the code and display the output
global $post;
$marker=$post->post_author; // The id of the map equals the id of the author
	$shortcode = sprintf(
	    '[mapsmarker marker="%1$s"]',
	    $marker
	);
$content=do_shortcode($shortcode);
//$content=$marker;
    echo __( $content, 'wpb_widget_domain' );
echo $args['after_widget'];
}
         
// Widget Backend 
public function form( $instance ) {
if ( isset( $instance[ 'title' ] ) ) {
$title = $instance[ 'title' ];
}
else {
$title = __( 'Le titre', 'wpb_widget_domain' );
}
// Widget admin form
?>
<p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>
<?php 
}
     
// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {
$instance = array();
$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
return $instance;
}
} // Class wpb_widget ends here

// Creating the widget 
class wpBP_widget extends WP_Widget {
 
function __construct() {
parent::__construct(
 
// Base ID of your widget
'wpBP_widget', 
 
// Widget name will appear in UI
__('PeliQuestions', 'wpBP_widget_domain'), 
 
// Widget description
array( 'description' => __( 'Permet d\'afficher les PeliQuestions de la classe dont on consulte le web-reportage', 'wpBP_widget_domain' ), ) 
);
}
 
// Creating widget front-end
 
public function widget( $args, $instance ) {
$title = apply_filters( 'widget_title', $instance['title'] );
 
// before and after widget arguments are defined by themes
echo $args['before_widget'];
if ( ! empty( $title ) )
echo $args['before_title'] . $title . $args['after_title'];
 
// This is where you run the code and display the output
global $post;
$marker=$post->post_author; // The id of the map equals the id of the author

	$shortcode = sprintf( // À compléter à la fin pour afficher les activités pertinentes, avec l'argument "action" adéquate
	    '[activity-stream user_id="%1$s"]',
	    $marker
	);
$content=do_shortcode($shortcode);
//$content=$marker;
    echo __( $content, 'wpBP_widget_domain' );
echo $args['after_widget'];
}
         
// Widget Backend 
public function form( $instance ) {
if ( isset( $instance[ 'title' ] ) ) {
$title = $instance[ 'title' ];
}
else {
$title = __( 'Le titre', 'wpBP_widget_domain' );
}
// Widget admin form
?>
<p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>
<?php 
}
     
// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {
$instance = array();
$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
return $instance;
}
} // Class wpBP_widget ends here


/* To add Ask and comment button, if user connected */
add_action('kt_after_content', 'question_comment', 20);
function question_comment() {
	
    if ( is_user_logged_in() ) {
	// add conditional tags to check for member type
	if (is_single()){
		
		$member_type = bp_get_member_type( get_current_user_id() ); 
	  if ($member_type=="explorateur-en-herbe"){
		echo '<div id="comment-EH">...</div>';
	}
			  else {
				echo '<div id="question-GE">?</div>';
			}
	}
    } 
}
