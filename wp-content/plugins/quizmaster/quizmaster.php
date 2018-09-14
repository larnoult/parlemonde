<?php
/*
Plugin Name: QuizMaster
Plugin URI: http://wordpress.org/extend/plugins/quizmaster
Description: Best free quiz plugin for WordPress.
Version: 0.8.6
Author: GoldHat Group
Author URI: https://wpquizmaster.com
Copyright: GoldHat Group (https://goldhat.ca), Julius Fischer (WP Pro Quiz)
Text Domain: quizmaster
Domain Path: /languages
*/

define('QUIZMASTER_VERSION', '0.8.6');
define('QUIZMASTER_DEV', true);
define('QUIZMASTER_PATH', dirname(__FILE__));
define('QUIZMASTER_URL', plugins_url('', __FILE__));
define('QUIZMASTER_FILE', __FILE__);
define('QUIZMASTER_PPATH', dirname(plugin_basename(__FILE__)));
define('QUIZMASTER_PLUGIN_PATH', QUIZMASTER_PATH . '/plugin');

define('QUIZMASTER_ANSWER_TYPE_FIELD', 'qmqe_answer_type');
define('QUIZMASTER_QUIZ_QUESTION_SELECTOR_FIELD', 'qmqu_questions');
define('QUIZMASTER_QUESTION_QUIZ_SELECTOR_FIELD', 'qmqe_quizzes');

$uploadDir = wp_upload_dir();

define('QUIZMASTER_CAPTCHA_DIR', $uploadDir['basedir'] . '/quizmaster_captcha');
define('QUIZMASTER_CAPTCHA_URL', $uploadDir['baseurl'] . '/quizmaster_captcha');

spl_autoload_register('quizMaster_autoload');

delete_option('quizMaster_dbVersion');

register_activation_hook(__FILE__, array('QuizMaster_Helper_Upgrade', 'upgrade'));
register_activation_hook( __FILE__, 'quizMasterActivation' );

register_deactivation_hook( __FILE__, 'quizMasterDeactivation' );

add_filter( 'the_content', 'templateContentLoading' );
function templateContentLoading( $content ) {

	if( !is_single() ) {
		return $content;
	}

	global $post;
	$postType = get_post_type( $post->ID );

	if( 'quizmaster' == substr( $postType, 0, 10) ) {

			$templateName = substr( $postType, 11 );
			$content .= quizmaster_parse_template( $templateName . '.php',
				array(
					'post' => $post,
				)
		  );

	}

	return $content;

}

/*
 * Main plugin class
 */
class quizmaster {

	public function __construct() {
		$this->init();
	}

	public function init() {
		$enableCopyPosts = array( 'quizmaster_quiz', 'quizmaster_question' );
		new QuizMaster_Helper_CopyPost( $enableCopyPosts );

		add_filter( quizmaster_get_fields_prefix() . '/load_value/name=qmqe_sorting_choice_answer_id', array( $this, 'makeSortingChoiceAnswerId' ), 10, 3 );
	}

	public function makeSortingChoiceAnswerId( $value ) {

		if( $value == '' || strlen( $value ) <= 6 ) {
			return quizmasterGenerateRandomString( 12 );
		}

		return $value;

	}

}

// initiate quizmaster
new quizmaster();

/* remove stuff at deactivation */
function quizMasterDeactivation() {
  QuizMaster_Extension::doDeactivation();
}

function quizMasterActivation() {
  quizmasterFieldsApiTest();

	// create post types and flush rewrite rules
	quizmasterAddPostTypes();
	flush_rewrite_rules();

  quizMasterAddAdminCaps();
  quizmasterCreateDefaultEmails();
  quizmasterCreateStudentReportPage();

	QuizMaster_Extension::doActivation();
}

function quizmasterFieldsApiTest() {
  include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
  $isAcfActive = is_plugin_active('advanced-custom-fields-pro/acf.php');
	$isFieldMasterActive = is_plugin_active('fieldmaster/fieldmaster.php');
  if( !$isAcfActive && !$isFieldMasterActive ) {
    deactivate_plugins( plugin_basename( __FILE__ ) );
		$activationMsg = __( 'QuizMaster requires either FieldMaster (Free Fields Plugin, download latest release at' , 'quizmaster');
		$activationMsg .= '<a href="https://github.com/goldhat/fieldmaster/releases/latest/">https://github.com/goldhat/fieldmaster/releases/tag/</a>';
		$activationMsg .= __( 'or ACF (Advanced Custom Fields) Pro! Visit ', 'quizmaster' );
		$activationMsg .= '<a href="https://www.advancedcustomfields.com/">https://www.advancedcustomfields.com/</a>';
		$activationMsg .= __( 'to purchase or return to ', 'quizmaster' );
		$activationMsg .= '<a href="' . get_admin_url( null, 'plugins.php' ) . '">';
		$activationMsg .= __( 'Manage Plugins', 'quizmaster' );
		$activationMsg .= '</a>.';
    wp_die( $activationMsg );
  }
}

add_action('init', 'quizmasterPluginsLoaded', 5);
add_action('init', 'quizmasterAddPostTypes', 10, 2);
add_action('init', 'quizmasterMenuItems', 20, 2);

if (is_admin()) {
  new QuizMaster_Controller_Admin();
} else {
  new QuizMaster_Controller_Front();
}

function quizMaster_autoload( $class ) {

    $c = explode('_', $class);

    if ($c === false || count($c) != 3 || $c[0] !== 'QuizMaster') {
        return;
    }

    switch ($c[1]) {
      case 'View':
        $dir = 'view';
        break;
      case 'Model':
        $dir = 'model';
        break;
      case 'Question':
        $dir = 'model/question_types';
        break;
      case 'Answer':
        $dir = 'model/answer_types';
        break;
      case 'Helper':
        $dir = 'helper';
        break;
      case 'Controller':
        $dir = 'controller';
        break;
      case 'Plugin':
        $dir = 'plugin';
        break;
      default:
        return;
    }

    $classPath = QUIZMASTER_PATH . '/lib/' . $dir . '/' . $class . '.php';

		if (file_exists($classPath)) {
	    /** @noinspection PhpIncludeInspection */
	    include_once $classPath;
	  } else {
			// load extension classes
			QuizMaster_Extension::autoload( $class, $dir );
		}

}

function quizmasterPluginsLoaded() {

  $lang = load_plugin_textdomain('quizmaster', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

}

/**
 * Get template.
 *
 * Search for the template and include the file.
 *
 * @since 1.0.0
 *
 * @see wcpt_locate_template()
 *
 * @param string 	$template_name			Template to load.
 * @param array 	$args					Args passed for the template file.
 * @param string 	$string $template_path	Path to templates.
 * @param string	$default_path			Default path to template files.
 */
function quizmaster_parse_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
  ob_start();
  quizmaster_get_template( $template_name, $args, $template_path, $default_path );
  $contents = ob_get_contents();
  ob_end_clean();
  return $contents;
}

/**
 * Get template.
 *
 * Search for the template and include the file.
 *
 * @since 1.0.0
 *
 * @see wcpt_locate_template()
 *
 * @param string 	$template_name			Template to load.
 * @param array 	$args					Args passed for the template file.
 * @param string 	$string $template_path	Path to templates.
 * @param string	$default_path			Default path to template files.
 */
function quizmaster_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	if ( is_array( $args ) && isset( $args ) ) :
		extract( $args );
	endif;
	$template_file = quizmaster_locate_template( $template_name, $template_path, $default_path );

  if ( ! file_exists( $template_file ) ) :
		_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $template_file ), '1.0.0' );
		return;
	endif;

	include $template_file;
}

/**
 * Locate template.
 *
 * Locate the called template.
 * Search Order:
 * 1. /themes/theme/quizmaster/$template_name
 * 2. /themes/theme/$template_name
 * 3. /plugins/quizmaster/templates/$template_name.
 *
 * @since 1.0.0
 *
 * @param 	string 	$template_name			Template to load.
 * @param 	string 	$string $template_path	Path to templates.
 * @param 	string	$default_path			Default path to template files.
 * @return 	string 							Path to the template file.
 */
function quizmaster_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	// Set variable to search in /quizmaster folder of theme.
	if ( ! $template_path ) :
		$template_path = 'quizmaster/';
	endif;
	// Set default plugin templates path.
	if ( ! $default_path ) :
		$default_path = plugin_dir_path( __FILE__ ) . 'templates/'; // Path to the template folder
	endif;
	// Search template file in theme folder.
	$template = locate_template( array(
		$template_path . $template_name,
		$template_name
	) );
	// Get plugins template file.
	if ( ! $template ) :
		$template = $default_path . $template_name;
	endif;

	return apply_filters( 'quizmaster_locate_template', $template, $template_name, $template_path, $default_path );
}

/* Meta Capability Mapping */
add_filter( 'map_meta_cap', 'quizMasterMapMetaCapQuiz', 10, 4 );
add_filter( 'map_meta_cap', 'quizMasterMapMetaCapQuestion', 10, 4 );
add_filter( 'map_meta_cap', 'quizMasterMapMetaCapScore', 10, 4 );
add_filter( 'map_meta_cap', 'quizMasterMapMetaCapEmail', 10, 4 );

function quizMasterMapMetaCapScore( $caps, $cap, $user_id, $args ) {

  /* If editing, deleting, or reading a quiz, get the post and post type object. */
	if ( 'quizmaster_edit_score' == $cap || 'quizmaster_delete_score' == $cap || 'quizmaster_read_score' == $cap ) {
		$post = get_post( $args[0] );
		$post_type = get_post_type_object( $post->post_type );

    /* Set an empty array for the caps. */
		$caps = array();
	}

	/* If editing a quiz, assign the required capability. */
	if ( 'quizmaster_edit_score' == $cap ) {
    $caps[] = $post_type->cap->edit_posts;
	}

	/* If deleting a quiz, assign the required capability. */
	elseif ( 'quizmaster_delete_score' == $cap ) {
    $caps[] = $post_type->cap->delete_posts;
	}

	/* If reading a private quiz, assign the required capability. */
	elseif ( 'quizmaster_read_score' == $cap ) {

		if ( 'private' != $post->post_status )
			$caps[] = 'read';
		elseif ( $user_id == $post->post_author )
			$caps[] = 'read';
		else
			$caps[] = $post_type->cap->read_private_posts;
	}

	/* Return the capabilities required by the user. */
	return $caps;

}

function quizMasterMapMetaCapEmail( $caps, $cap, $user_id, $args ) {

  /* If editing, deleting, or reading a quiz, get the post and post type object. */
	if ( 'quizmaster_edit_email' == $cap || 'quizmaster_delete_email' == $cap || 'quizmaster_read_email' == $cap ) {
		$post = get_post( $args[0] );
		$post_type = get_post_type_object( $post->post_type );

    /* Set an empty array for the caps. */
		$caps = array();
	}

	/* If editing a quiz, assign the required capability. */
	if ( 'quizmaster_edit_email' == $cap ) {
    $caps[] = $post_type->cap->edit_posts;
	}

	/* If deleting a quiz, assign the required capability. */
	elseif ( 'quizmaster_delete_email' == $cap ) {
    $caps[] = $post_type->cap->delete_posts;
	}

	/* If reading a private quiz, assign the required capability. */
	elseif ( 'quizmaster_read_email' == $cap ) {

		if ( 'private' != $post->post_status )
			$caps[] = 'read';
		elseif ( $user_id == $post->post_author )
			$caps[] = 'read';
		else
			$caps[] = $post_type->cap->read_private_posts;
	}

	/* Return the capabilities required by the user. */
	return $caps;

}

function quizMasterMapMetaCapQuiz( $caps, $cap, $user_id, $args ) {

  /* If editing, deleting, or reading a quiz, get the post and post type object. */
	if ( 'quizmaster_edit_quiz' == $cap || 'quizmaster_delete_quiz' == $cap || 'quizmaster_read_quiz' == $cap ) {
		$post = get_post( $args[0] );
		$post_type = get_post_type_object( $post->post_type );

    /* Set an empty array for the caps. */
		$caps = array();
	}

	/* If editing a quiz, assign the required capability. */
	if ( 'quizmaster_edit_quiz' == $cap ) {
		if ( $user_id == $post->post_author )
			$caps[] = $post_type->cap->edit_posts;
		else
			$caps[] = $post_type->cap->edit_others_posts;
	}

	/* If deleting a quiz, assign the required capability. */
	elseif ( 'quizmaster_delete_quiz' == $cap ) {
		if ( $user_id == $post->post_author )
			$caps[] = $post_type->cap->delete_posts;
		else
			$caps[] = $post_type->cap->delete_others_posts;
	}

	/* If reading a private quiz, assign the required capability. */
	elseif ( 'quizmaster_read_quiz' == $cap ) {

		if ( 'private' != $post->post_status )
			$caps[] = 'read';
		elseif ( $user_id == $post->post_author )
			$caps[] = 'read';
		else
			$caps[] = $post_type->cap->read_private_posts;
	}

	/* Return the capabilities required by the user. */
	return $caps;

}

function quizMasterMapMetaCapQuestion( $caps, $cap, $user_id, $args ) {

  /* If editing, deleting, or reading a quiz, get the post and post type object. */
	if ( 'quizmaster_edit_question' == $cap || 'quizmaster_delete_question' == $cap || 'quizmaster_read_question' == $cap ) {
		$post = get_post( $args[0] );
		$post_type = get_post_type_object( $post->post_type );

    /* Set an empty array for the caps. */
		$caps = array();
	}

	/* If editing a quiz, assign the required capability. */
	if ( 'quizmaster_edit_question' == $cap ) {
		if ( $user_id == $post->post_author )
			$caps[] = $post_type->cap->edit_posts;
		else
			$caps[] = $post_type->cap->edit_others_posts;
	}

	/* If deleting a quiz, assign the required capability. */
	elseif ( 'quizmaster_delete_question' == $cap ) {
		if ( $user_id == $post->post_author )
			$caps[] = $post_type->cap->delete_posts;
		else
			$caps[] = $post_type->cap->delete_others_posts;
	}

	/* If reading a private quiz, assign the required capability. */
	elseif ( 'quizmaster_read_question' == $cap ) {

		if ( 'private' != $post->post_status )
			$caps[] = 'read';
		elseif ( $user_id == $post->post_author )
			$caps[] = 'read';
		else
			$caps[] = $post_type->cap->read_private_posts;
	}

	/* Return the capabilities required by the user. */
	return $caps;

}

function quizmasterMenuItems() {
  QuizMaster_Helper_Submenu::position('edit.php?post_type=quizmaster_quiz', 10);
  QuizMaster_Helper_Submenu::position('edit.php?post_type=quizmaster_question', 20);
  QuizMaster_Helper_Submenu::position('edit.php?post_type=quizmaster_score', 30);
  QuizMaster_Helper_Submenu::position('edit.php?post_type=quizmaster_email', 40);
}

function quizmasterAddPostTypes() {

  register_post_type( 'quizmaster_quiz',
    array(
      'labels' => array(
        'name' => __( 'Quizzes', 'quizmaster' ),
        'singular_name' => __( 'Quiz', 'quizmaster' ),
        'add_new' => __( 'Add New Quiz', 'quizmaster' ),
        'add_new_item' => __( 'Add New Quiz', 'quizmaster' ),
      ),
      'public' => true,
      'has_archive' => true,
      'rewrite' => array('slug' => 'quiz'),
      'show_in_menu' => 'quizmaster',
      'supports' => array('title', 'revisions'),
      'capabilities' => array(
        'create_posts' => 'quizmaster_manage_quizzes',
        'publish_posts' => 'quizmaster_publish_quizzes',
        'edit_posts' => 'quizmaster_edit_quizzes',
        'edit_post' => 'quizmaster_edit_quiz',
        'edit_others_posts' => 'quizmaster_edit_others_quizzes',
        'edit_published_posts' => 'quizmaster_manage_quizzes',
        'delete_posts' => 'quizmaster_delete_quizzes',
        'delete_post' => 'quizmaster_delete_quiz',
        'delete_others_posts' => 'quizmaster_delete_others_quizzes',
        'manage_posts' => 'quizmaster_manage_quizzes',
        'read_private_posts' => 'quizmaster_read_private_quizzes',
        'read_post' => 'quizmaster_read_quiz',
      ),
      'map_meta_cap' => true,
    )
  );

  register_post_type( 'quizmaster_question',
    array(
      'labels' => array(
        'name' => __( 'Questions', 'quizmaster' ),
        'singular_name' => __( 'Question', 'quizmaster' ),
        'add_new' => __( 'Add New Question', 'quizmaster' ),
        'add_new_item' => __( 'Add New Question', 'quizmaster' ),
      ),
      'public' => true,
      'has_archive' => true,
      'rewrite' => array('slug' => 'question'),
      'show_in_menu' => 'quizmaster',
      'supports' => array('title', 'revisions'),
      'capabilities' => array(
        'create_posts' => 'quizmaster_manage_questions',
        'publish_posts' => 'quizmaster_manage_questions',
        'edit_posts' => 'quizmaster_manage_questions',
        'edit_post' => 'quizmaster_edit_question',
        'edit_others_posts' => 'quizmaster_manage_others_questions',
        'edit_published_posts' => 'quizmaster_manage_questions',
        'delete_posts' => 'quizmaster_manage_others_questions',
        'delete_post' => 'quizmaster_delete_question',
        'delete_others_posts' => 'quizmaster_manage_others_questions',
        'manage_posts' => 'quizmaster_manage_questions',
        'read_private_posts' => 'quizmaster_manage_questions',
        'read_post' => 'quizmaster_read_question',
      ),
      'map_meta_cap' => true,
    )
  );

  register_post_type( 'quizmaster_email',
    array(
      'labels' => array(
        'name' => __( 'Emails', 'quizmaster' ),
        'singular_name' => __( 'Email', 'quizmaster' )
      ),
      'public' => true,
      'has_archive' => false,
      'show_in_menu' => 'quizmaster',
      'capabilities' => array(
        'create_posts' => 'quizmaster_manage_emails',
        'publish_posts' => 'quizmaster_manage_emails',
        'edit_posts' => 'quizmaster_manage_emails',
        'edit_post' => 'quizmaster_edit_email',
        'edit_others_posts' => 'quizmaster_manage_emails',
        'edit_published_posts' => 'quizmaster_manage_emails',
        'delete_posts' => 'quizmaster_manage_emails',
        'delete_post' => 'quizmaster_delete_email',
        'delete_others_posts' => 'quizmaster_manage_emails',
        'manage_posts' => 'quizmaster_manage_emails',
        'read_private_posts' => 'quizmaster_manage_emails',
        'read_post' => 'quizmaster_read_email',
      ),
      'map_meta_cap' => true,
    )
  );

  register_post_type( 'quizmaster_score',
    array(
      'labels' => array(
        'name' => __( 'Scores', 'quizmaster' ),
        'singular_name' => __( 'Score', 'quizmaster' )
      ),
      'public' => true,
      'has_archive' => false,
      'show_in_menu' => 'quizmaster',
      'capabilities' => array(
        'create_posts' 					=> 'do_not_allow',
        'publish_posts' 				=> 'quizmaster_manage_scores',
        'edit_posts' 						=> 'quizmaster_manage_scores',
        'edit_post' 						=> 'quizmaster_edit_score',
        'edit_others_posts' 		=> 'quizmaster_manage_scores',
        'edit_published_posts' 	=> 'quizmaster_manage_scores',
        'delete_posts' 					=> 'quizmaster_manage_scores',
        'delete_post'						=> 'quizmaster_delete_score',
        'delete_others_posts' 	=> 'quizmaster_manage_scores',
        'manage_posts' 					=> 'quizmaster_manage_scores',
        'read_private_posts' 		=> 'quizmaster_manage_scores',
        'read_post' 						=> 'quizmaster_read_score',
      ),
      'map_meta_cap' => true,
    )
  );

}

add_action( 'init', 'quizmasterRegisterTaxonomies' );

function quizmasterRegisterTaxonomies() {
	register_taxonomy(
		'quizmaster_quiz_category',
		'quizmaster_quiz',
		array(
			'label' => __( 'Quiz Category', 'quizmaster' ),
			'rewrite' => array( 'slug' => 'quiz-category' ),
			'hierarchical' => true,
		)
	);
  register_taxonomy(
		'quizmaster_quiz_tag',
		'quizmaster_quiz',
		array(
			'label' => __( 'Quiz Tag', 'quizmaster' ),
			'rewrite' => array( 'slug' => 'quiz-tag' ),
			'hierarchical' => false,
		)
	);
  register_taxonomy(
		'quizmaster_question_category',
		'quizmaster_question',
		array(
			'label' => __( 'Question Category', 'quizmaster' ),
			'rewrite' => array( 'slug' => 'question-category' ),
			'hierarchical' => true,
		)
	);
  register_taxonomy(
		'quizmaster_question_tag',
		'quizmaster_question',
		array(
			'label' => __( 'Question Tag', 'quizmaster' ),
			'rewrite' => array( 'slug' => 'question-tag' ),
			'hierarchical' => false,
		)
	);
}

add_action('init', 'quizMasterInit', 20);
function quizMasterInit() {

  // add fieldgroups and option pages
  if( !QUIZMASTER_DEV ) {
    add_filter( quizmaster_get_fields_prefix() . '/settings/show_admin', '__return_false');
    include_once( QUIZMASTER_PATH . '/fields/fieldgroups/email.php' );
    include_once( QUIZMASTER_PATH . '/fields/fieldgroups/question.php' );
    include_once( QUIZMASTER_PATH . '/fields/fieldgroups/quiz.php' );
    include_once( QUIZMASTER_PATH . '/fields/fieldgroups/score.php' );
    include_once( QUIZMASTER_PATH . '/fields/fieldgroups/settings.php' );
  }

	quizMasterAddOptionsPages();

	$fieldCtr = new QuizMaster_Controller_Fields();
	$fieldCtr->loadFieldGroups();

}

function quizMasterAddOptionsPages() {

  /* Options Pages */
	$addOptionsPageFunc = quizmaster_get_fields_prefix() . '_add_options_page';
  $option_page = $addOptionsPageFunc(array(
		'page_title' 	=> 'QuizMaster Settings',
		'menu_title' 	=> 'Settings',
		'menu_slug' 	=> 'quizmaster-settings',
    'parent_slug' => 'quizmaster',
 		'capability' 	=> 'quizmaster_manage_settings',
	));
  QuizMaster_Helper_Submenu::position( 'quizmaster-settings', 70 );
}



/* Single Quiz Template */
// add_filter('single_template', 'quizmaster_quiz_template');

function quizmaster_quiz_template($single) {
  global $post;
  if ($post->post_type == "quizmaster_quiz") {
    return quizmaster_locate_template( 'quiz.php' );
  }
  if ($post->post_type == "quizmaster_score") {
    return quizmaster_locate_template( 'score.php' );
  }
  return $single;
}

/* Scores Link in Quiz Table */
add_filter('post_row_actions','quizmasterCustomPostRows', 10, 2);
function quizmasterCustomPostRows( $actions, $quizPost ) {

  if ( $quizPost->post_type == "quizmaster_quiz") {

		// Add scores link
	  $scoresUrl = admin_url( '/edit.php?post_type=quizmaster_score&qm_quiz=' . $quizPost->ID );
	  $actions['scores'] = '<a href="'. $scoresUrl .'">Scores</a>';

	}

	if ( $quizPost->post_type == "quizmaster_score") {

	  // Remove the Quick Edit link
	  if ( isset( $actions['inline hide-if-no-js'] ) ) {
	    unset( $actions['inline hide-if-no-js'] );
	  }

		// remove edit, trash
		//unset( $actions['edit'] );
		unset( $actions['trash'] );

	}

  return $actions;
}

/* Activate Revisions for Quizzes & Questions */
add_filter( 'wp_revisions_to_keep', 'filter_function_name', 10, 2 );
function filter_function_name( $num, $post ) {
  if( $post->post_type == 'quizmaster_quiz' || $post->post_type == 'quizmaster_question' ) {
    return -1;
  }
  return $num;
}



add_action( 'admin_footer-edit.php', 'quizmasterRemovePostEditTitle' );
function quizmasterRemovePostEditTitle() {
  ?>
  <script type="text/javascript">
    jQuery('body.post-type-quizmaster_score table.wp-list-table a.row-title').contents().unwrap();
  </script>
  <?php
}

/*
 * Setup Admin Columns, Filters
 */
if( is_admin() ) {
	add_action('init', 'setup_admin_helper');
}

function setup_admin_helper() {
	QuizMaster_Helper_Admin::init();

}

/* Quiz Scores Filters */
add_action( 'restrict_manage_posts', 'quizmaster_score_filter_quiz' );
function quizmaster_score_filter_quiz() {
  $type = 'post';
  if (isset($_GET['post_type'])) {
    $type = $_GET['post_type'];
  }

  //only add filter to post type you want
  if( 'quizmaster_score' == $type ) {

    // load quizzes
    $quizMapper = new QuizMaster_Model_QuizMapper;
    $quizzes = $quizMapper->fetchAll();
    if( !$quizzes ) {
      return;
    }

    // make values array
    $values = array();
    foreach( $quizzes as $quiz ) {
      $values[ $quiz->getId() ] = get_the_title( $quiz->getId() );
    }

    // selected quiz
    $selectKey = 'qm_quiz';
    $selected = isset($_GET[ $selectKey ])? $_GET[ $selectKey ]:'';

    // filter select
    quizmaster_get_template('/reports/score-filter.php', array(
      'selectName' => $selectKey,
      'values' => $values,
      'defaultLabel' => 'All quizzes',
      'selected' => $selected,
    ));


    $users = get_users( 'orderby=nicename' );

    $values = array();
    foreach( $users as $user ) {
      $values[ $user->ID ] = $user->data->user_nicename;
    }

    // selected user
    $selectKey = 'qm_user';
    $selected = isset($_GET[ $selectKey ])? $_GET[ $selectKey ]:'';

    // filter select
    quizmaster_get_template('/reports/score-filter.php', array(
      'selectName' => $selectKey,
      'values' => $values,
      'defaultLabel' => 'All users',
      'selected' => $selected,
    ));

  }
}

add_action( 'pre_get_posts', 'quizmaster_posts_filter', 10, 1 );
function quizmaster_posts_filter( $query ){

  global $pagenow;
  if( $pagenow != 'edit.php' ) {
    return $query;
  }

  if( !$query->is_main_query() ) return;
  if( !is_admin() ) return;

  $type = 'post';
  if (isset($_GET['post_type'])) {
    $type = $_GET['post_type'];
  }

  $metaQuery = $query->get('meta_query');

	$scoreModel = new QuizMaster_Model_Score;

  if ( 'quizmaster_score' == $type && isset($_GET['qm_quiz']) && $_GET['qm_quiz'] != 0) {
    $metaQuery[] = array(
      'key'       => $scoreModel->getFieldPrefix() . 'quiz',
      'value'     => $_GET['qm_quiz'],
      'compare'   => '='
    );
  }

  if ( 'quizmaster_score' == $type && isset($_GET['qm_user']) && $_GET['qm_user'] != 0) {
    $metaQuery[] = array(
      'key'       => $scoreModel->getFieldPrefix() . 'user',
      'value'     => $_GET['qm_user'],
      'compare'   => '='
    );
  }

  $query->set('meta_query', $metaQuery);

}


/* Revision Cloning */
add_action( 'save_post', 'revisionTest', 50, 3 );
function revisionTest( $post_id, $post, $update ) {

  // exit if not quiz or question type
  $types = array('quizmaster_quiz', 'quizmaster_question');
  if( !in_array( $post->post_type, $types ) ) {
    return;
  }

  // Autosave, do nothing
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
    return;
  // AJAX? Not used here
  if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
    return;

  // exit if currently saving the revision
  if( false !== wp_is_post_revision( $post_id ) ) {
    return;
  }

  $revision_id = wp_save_post_revision( $post_id );

  if( !$revision_id ) {
    return;
  }

	$copyPostMetaFunc = quizmaster_get_fields_prefix() . '_copy_postmeta';
  $copyPostMetaFunc( $post_id, $revision_id );

  return $post_id;

}

function quizmasterCreateDefaultEmails() {
  quizmasterCreateDefaultEmailstudentCompletion();
}

function quizmasterCreateDefaultEmailstudentCompletion() {

  $emailCtr = new QuizMaster_Controller_Email;
  if( $emailCtr->emailExists('student_completion') ) {
    return;
  }

  $post = array(
    'post_type'     => 'quizmaster_email',
    'post_title'    => "Student Completion Email",
    'post_status'   => 'publish',
    'post_author'   => 1,
  );
  $post_id = wp_insert_post( $post );
  update_field('qm_email_key', 'student_completion', $post_id);
  update_field('qm_email_enabled', 1, $post_id);
  update_field('qm_email_trigger', 'completed_quiz', $post_id);
  update_field('qm_email_from', get_option('admin_email'), $post_id);
  update_field('qm_email_recipients', '[quiztaker_email]', $post_id);
  update_field('qm_email_subject', 'You Completed a Quiz', $post_id);
  update_field('qm_email_type', 'html', $post_id);
}

function quizmasterCreateStudentReportPage() {
  $studentReportPage = get_page_by_path('student-report');
  if( $studentReportPage ) {
    setStudentReportPageOption( $studentReportPage->ID );
    return;
  }
  $post = array(
    'post_type'     => 'page',
    'post_title'    => "Student Report",
    'post_status'   => 'publish',
    'post_content'  => '[quizmaster_student_report]',
    'post_author'   => 1,
  );
  $post_id = wp_insert_post( $post );
  setStudentReportPageOption( $post_id );
}

function setStudentReportPageOption( $post_id ) {
  update_option( 'qm_student_report_page', $post_id );
}

function getStudentReportPageOption() {
  return get_field('qm_student_report_page', 'option');
}

function quizMasterAddAdminCaps() {

  // quiz caps
  $admins = get_role('administrator');
  $admins->add_cap( 'quizmaster_edit_quizzes' );
  $admins->add_cap( 'quizmaster_edit_others_quizzes' );
  $admins->add_cap( 'quizmaster_delete_quizzes' );
  $admins->add_cap( 'quizmaster_delete_others_quizzes' );
  $admins->add_cap( 'quizmaster_publish_quizzes' );
  $admins->add_cap( 'quizmaster_manage_quizzes' );
  $admins->add_cap( 'quizmaster_read_private_quizzes' );
  $admins->add_cap( 'quizmaster_manage_settings' );

  // question caps
  $admins->add_cap( 'quizmaster_manage_questions' );
  $admins->add_cap( 'quizmaster_manage_others_questions' );

  // email caps
  $admins->add_cap( 'quizmaster_manage_emails' );

  // score caps
  $admins->add_cap( 'quizmaster_manage_scores' );

}

function quizmaster_camelize($input, $separator = '_') {
  return str_replace($separator, '', ucwords($input, $separator));
}
function quizmaster_simplify_key( $key ) {
  return str_replace( 'quizmaster_', '', $key);
}

function quizmasterGenerateRandomString($length = 10) {
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $charactersLength = strlen($characters);
  $randomString = '';
  for ($i = 0; $i < $length; $i++) {
    $randomString .= $characters[rand(0, $charactersLength - 1)];
  }
  return $randomString;
}

/* Remove Email View Link */
add_filter( 'post_row_actions', 'remove_row_actions', 10, 1 );
function remove_row_actions( $actions ) {
  if( get_post_type() === 'quizmaster_email' )
    unset( $actions['view'] );
  return $actions;
}

/* Make QuizMaster admin menu item active for different taxonomies under Category & Tags */
add_filter( 'parent_file', 'qm_parent_file' );
function qm_parent_file( $parent_file ){
    /* Get current screen */
    global $current_screen, $self;

    if ( 'edit-tags'==$current_screen->base && in_array( $current_screen->taxonomy, array( 'quizmaster_quiz_category', 'quizmaster_quiz_tag','quizmaster_question_category','quizmaster_question_tag' ) ) ) {
        $parent_file = 'quizMaster';
    }

    return $parent_file;
}


/**
 * Fix Sub Menu Item Highlights for different taxonomies under Category & Tags
 */
add_filter( 'submenu_file', 'qm_submenu_file' );
function qm_submenu_file( $submenu_file ){

    /* Get current screen */
    global $current_screen, $self;
    //$screen = get_current_screen();

    if ( 'edit-tags'==$current_screen->base && in_array( $current_screen->taxonomy, array( 'quizmaster_quiz_category', 'quizmaster_quiz_tag','quizmaster_question_category','quizmaster_question_tag' ) )) {
        $submenu_file = 'quizmaster-categories-tags';
    }

    return $submenu_file;
}

// Logging Function
function quizmaster_log( $message ) {
  $log = new QuizMaster_Helper_Log();
  $log->log( $message );
}

/* Define QuizMaster_Extension for convenience */
class QuizMaster_Extension extends QuizMaster_Helper_Extension {}

/* Load all extensions */
add_action('init', 'quizmasterLoadExtensions', 15);
function quizmasterLoadExtensions() {
	QuizMaster_Extension::loadAll();
}

// convenience function to get the fields prefix
function quizmaster_get_fields_prefix() {
	return QuizMaster_Helper_Fields::getFieldApiPrefix();
}

// basic implementation of loaded hook but needs to be moved when this file is refactored
do_action('quizmaster_loaded');
