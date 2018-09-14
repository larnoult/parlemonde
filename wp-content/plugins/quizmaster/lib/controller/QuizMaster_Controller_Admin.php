<?php

class QuizMaster_Controller_Admin {

    protected $_ajax;

    public function __construct() {

      // init ajax handlers
      $this->_ajax = new QuizMaster_Controller_Ajax();
      $this->_ajax->init();

      // add admin menu items
      add_action( 'admin_menu', array($this, 'addMenuItems'), 50 );
      add_action( 'admin_menu', array($this, 'menuItemSort'), 110 );

      // init controller email
      $emailCtr = new QuizMaster_Controller_Email();

    }

    private function localizeScript() {
        global $wp_locale;

        $isRtl = isset($wp_locale->is_rtl) ? $wp_locale->is_rtl : false;

        $translation_array = array();

        wp_localize_script('quizMaster_admin_javascript', 'quizMasterLocalize', $translation_array);
    }

    public function enqueueScript() {
        wp_enqueue_script(
            'quizMaster_admin_javascript',
            plugins_url('js/quizMaster_admin' . (QUIZMASTER_DEV ? '' : '.min') . '.js', QUIZMASTER_FILE),
            array('jquery', 'jquery-ui-sortable', 'jquery-ui-datepicker'),
            QUIZMASTER_VERSION
        );

        wp_enqueue_style('jquery-ui',
            'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');

				wp_enqueue_style( 'quizmaster-admin-css', plugins_url('css/quizmaster-admin.css', QUIZMASTER_FILE), array(), QUIZMASTER_VERSION );

        $this->localizeScript();
    }

    public function menuItemSort() {
      global $submenu;

      $qm_submenu = $submenu['quizmaster'];
      foreach( $qm_submenu as $i => $item ) {
        $pos = array_search( $item[2], $GLOBALS['quizmaster_menu'] );
        if( !$pos ) {
          $pos = 100; // default position
        }
        $item['pos'] = $pos;
        $qm_submenu[ $i ] = $item;
      }

      usort($qm_submenu, function($a, $b) {
        return $a['pos'] - $b['pos'];
      });

      $submenu['quizmaster'] = $qm_submenu;

    }

    public function addMenuItems() {
        $pages = array();

        $pages[] = add_menu_page(
          'QuizMaster',
          'QuizMaster',
          'quizmaster_show',
          'quizmaster',
          array($this, 'route'),
          'dashicons-welcome-learn-more'
        );

        do_action( 'quizmaster_add_menu_item', 'quizmaster-categories-tags' );
        $pages[] = QuizMaster_Helper_Submenu::add(
          'quizmaster',
          __('Categories', 'quizmaster'),
          __('Categories', 'quizmaster'),
          'quizmaster_manage_settings',
          'quizmaster-categories-tags',
          array($this, 'route'),
          50
        );

        do_action( 'quizmaster_add_menu_item', 'quizmaster-support' );
        $pages[] = QuizMaster_Helper_Submenu::add(
          'quizmaster',
          __('Help', 'quizmaster'),
          __('Help', 'quizmaster'),
          'quizmaster_manage_settings',
          'quizmaster-support',
          array($this, 'route'),
          60
        );

        foreach ($pages as $p) {

          add_action('load-' . $p, array($this, 'routeLoadAction'));
        }

				add_action('admin_print_scripts', array($this, 'enqueueScript'));
    }

    public function routeLoadAction() {

        // screen handling
        $screen = get_current_screen();
        if (!empty($screen)) {
          // Workaround for wp_ajax_hidden_columns() with sanitize_key()
          $name = strtolower($screen->id);

          if (!empty($_GET['module'])) {
              $name .= '_' . strtolower($_GET['module']);
          }

          set_current_screen($name);
          $screen = get_current_screen();
        }

        $helperView = new QuizMaster_View_GlobalHelperTabs();

        $screen->add_help_tab($helperView->getHelperTab());
        $screen->set_help_sidebar($helperView->getHelperSidebar());

        $this->_route(true);
    }

    public function route(){
      $this->_route();
    }

    private function _route( $routeAction = false ) {

      $module = isset($_GET['module']) ? $_GET['module'] : 'overallView';

      if (isset($_GET['page'])) {
        if (preg_match('#quizmaster-(.+)#', trim($_GET['page']), $matches)) {
          $module = $matches[1];
        }
      }

      $c = null;

      switch ($module) {
        case 'support':
          $c = new QuizMaster_Controller_Support();
          break;
        case 'categories-tags':
          $c = new QuizMaster_Controller_Taxonomies();
          break;
      }

      if ($c !== null) {
        if ($routeAction) {
          if (method_exists($c, 'routeAction')) {
            $c->routeAction();
          }
        } else {
          $c->route();
        }
      }

    }
}
