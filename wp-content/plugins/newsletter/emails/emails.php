<?php

defined('ABSPATH') || exit;

require_once NEWSLETTER_INCLUDES_DIR . '/themes.php';
require_once NEWSLETTER_INCLUDES_DIR . '/module.php';

class NewsletterEmails extends NewsletterModule {

    static $instance;

    /**
     * @return NewsletterEmails
     */
    static function instance() {
        if (self::$instance == null) {
            self::$instance = new NewsletterEmails();
        }
        return self::$instance;
    }

    function __construct() {
        $this->themes = new NewsletterThemes('emails');
        parent::__construct('emails', '1.1.5');
        add_action('wp_loaded', array($this, 'hook_wp_loaded'));

        if (is_admin()) {
            add_action('wp_ajax_tnpc_render', array($this, 'tnpc_render_callback'));
            add_action('wp_ajax_tnpc_preview', array($this, 'tnpc_preview_callback'));
            add_action('wp_ajax_tnpc_css', array($this, 'tnpc_css_callback'));
            add_action('wp_ajax_tnpc_options', array($this, 'hook_wp_ajax_tnpc_options'));
        }
    }

    function hook_wp_ajax_tnpc_options() {
        global $wpdb;

        $block = $this->get_block($_REQUEST['id']);
        if (!$block) {
            die('Block not found with id ' . esc_html($_REQUEST['id']));
        }

        if (!class_exists('NewsletterControls')) {
            include NEWSLETTER_INCLUDES_DIR . '/controls.php';
        }
        $options = stripslashes_deep($_REQUEST['options']);
        $controls = new NewsletterControls($options);

        $controls->init();
        echo '<input type="hidden" name="action" value="tnpc_render">';
        echo '<input type="hidden" name="b" value="' . esc_attr($_REQUEST['id']) . '">';

        ob_start();
        include $block['dir'] . '/options.php';
        $content = ob_get_clean();
        echo $content;
        wp_die();
    }

    /**
     * Renders a block identified by its id, using the block options and adding a wrapper
     * if required (for the first block rendering.
     * @param type $block_id
     * @param type $wrapper
     * @param type $options
     */
    function render_block($block_id = null, $wrapper = false, $options = array()) {
        $width = 600;
        $font_family = 'Helvetica, Arial, sans-serif'; 

        $block_options = get_option('newsletter_main');

        $block = $this->get_block($block_id);

        // Block not found
        if (!$block) {
            if ($wrapper) {
                echo '<table border="0" cellpadding="0" cellspacing="0" align="center" width="100%" style="border-collapse: collapse; width: 100%;" class="tnpc-row tnpc-row-block" data-id="', esc_attr($block_id), '">';
                echo '<tr>';
                echo '<td data-options="', esc_attr($data), '" bgcolor="#ffffff" align="center" style="padding: 0; font-family: Helvetica, Arial, sans-serif;" class="edit-block">';
            }
            echo '<!--[if mso]><table border="0" cellpadding="0" align="center" cellspacing="0" width="' . $width . '"><tr><td width="' . $width . '"><![endif]-->';
            echo "\n";

            echo 'Block not found';

            echo '<!--[if mso]></td></tr></table><![endif]-->';
            if ($wrapper) {
                echo '</td></tr></table>';
            }
            return;
        }
        $is_old_block = isset($block['filename']) && strpos($block['filename'], '.block');
        
        if ($is_old_block) {
            ob_start();
            include NEWSLETTER_DIR . '/emails/tnp-composer/blocks/' . $block['filename'] . '.php';
            $content = ob_get_clean();
        } else {
            ob_start();
            include $block['dir'] . '/block.php';
            $content = ob_get_clean();
        }
        
        // Obsolete
        $content = str_replace('{width}', $width, $content);
        $content = $this->inline_css($content, true);

        // CSS driven by the block
        if (!isset($options['block_background'])) {
            $options['block_background'] = '';
        }
        $style = '';
            if (isset($options['block_padding_top'])) $style .= 'padding-top: ' . $options['block_padding_top'] . 'px; ';
            if (isset($options['block_padding_left'])) $style .= 'padding-left: ' . $options['block_padding_left'] . 'px; ';
            if (isset($options['block_padding_right'])) $style .= 'padding-right: ' . $options['block_padding_right'] . 'px; ';
            if (isset($options['block_padding_bottom'])) $style .= 'padding-bottom: ' . $options['block_padding_bottom'] . 'px; ';
            
        // Old block type
        if ($is_old_block) {
            
            echo '<table border="0" cellpadding="0" cellspacing="0" align="center" width="100%" style="border-collapse: collapse; width: 100%;" class="tnpc-row" data-id="', esc_attr($block_id), "\">\n";
            echo "<tr>\n";
            echo '<td align="center" style="padding: 0;">', "\n";
            echo '<!--[if mso]><table border="0" cellpadding="0" align="center" cellspacing="0" width="' . $width . '"><tr><td width="' . $width . '"><![endif]-->', "\n";

            echo '<table border="0" cellpadding="0" align="center" cellspacing="0" width="100%" style="width: 100%!important; max-width: ', $width, 'px!important">', "\n";
            echo "<tr>\n";
            echo '<td class="edit-block" align="center" style="', $style, 'text-align: center;" bgcolor="', $options['block_background'], '" width="100%">', "\n";

            echo $content;
            
            echo "</td>\n</tr>\n</table>";
            echo '<!--[if mso]></td></tr></table><![endif]-->';
            echo "\n</td>\n</tr></table>\n\n";
            
        } else {

            $data = '';
            foreach ($options as $key => $value) {
                if (!is_array($value)) {
                    $data .= 'options[' . $key . ']=' . urlencode($value) . '&';
                } else {
                    foreach ($value as $v) {
                        $data .= 'options[' . $key . '][]=' . urlencode($v) . '&';
                    }
                }
            }
                    
            if ($wrapper) {
                echo '<table border="0" cellpadding="0" cellspacing="0" align="center" width="100%" style="border-collapse: collapse; width: 100%;" class="tnpc-row tnpc-row-block" data-id="', esc_attr($block_id), '">';
                echo '<tr>';
                echo '<td data-options="', esc_attr($data), '" align="center" style="padding: 0; font-family: Helvetica, Arial, sans-serif;" class="edit-block">';
            }
            
            // Container that fixes the width and makes the block responsive
            echo '<!--[if mso]><table border="0" cellpadding="0" align="center" cellspacing="0" width="' . $width . '"><tr><td width="' . $width . '"><![endif]-->';
            echo "\n";
            echo '<table border="0" cellpadding="0" align="center" cellspacing="0" width="100%" style="width: 100%!important; max-width: ', $width, 'px!important">', "\n";
            echo "<tr>\n";
            echo '<td align="center" style="', $style, 'text-align: center;" bgcolor="', $options['block_background'], '" width="100%">', "\n";

            echo $content;
            
            echo "</td>\n</tr>\n</table>";
            echo '<!--[if mso]></td></tr></table><![endif]-->';
            if ($wrapper) {
                echo '</td></tr></table>';
            }
        }
    }

    /**
     * Ajax call to render a block with a new set of options after the settings popup
     * has been saved.
     * 
     * @param type $block_id
     * @param type $wrapper
     */
    function tnpc_render_callback() {
        $block_id = $_POST['b'];
        $wrapper = isset($_POST['full']);
        if (isset($_POST['options']) && is_array($_POST['options'])) {
            $options = stripslashes_deep($_POST['options']);
        } else {
            $options = array();
        }
        $this->render_block($block_id, $wrapper, $options);
        wp_die();
    }

    function tnpc_preview_callback() {
        $email = Newsletter::instance()->get_email($_REQUEST['id'], ARRAY_A);

        if (empty($email)) {
            echo 'Wrong email identifier';
            return;
        }

        echo $email['message'];

        wp_die(); // this is required to terminate immediately and return a proper response
    }

    function tnpc_css_callback() {
        include NEWSLETTER_DIR . '/emails/tnp-composer/css/newsletter.css';
        wp_die(); // this is required to terminate immediately and return a proper response
    }

    function hook_wp_loaded() {
        global $wpdb;

        $newsletter = Newsletter::instance();

        switch ($newsletter->action) {
            case 'v':
            case 'view':
                $email = $this->get_email($_GET['id']);
                if (empty($email)) {
                    header("HTTP/1.0 404 Not Found");
                    die('Email not found');
                }

                $user = NewsletterSubscription::instance()->get_user_from_request();

                if (!is_user_logged_in() || !(current_user_can('editor') || current_user_can('administrator'))) {

                    if ($email->status == 'new') {
                        header("HTTP/1.0 404 Not Found");
                        die('Not sent yet');
                    }

                    if ($email->private == 1) {
                        if (!$user) {
                            header("HTTP/1.0 404 Not Found");
                            die('No available for online view');
                        }
                        $sent = $wpdb->get_row($wpdb->prepare("select * from " . NEWSLETTER_SENT_TABLE . " where email_id=%d and user_id=%d limit 1", $email->id, $user->id));
                        if (!$sent) {
                            header("HTTP/1.0 404 Not Found");
                            die('No available for online view');
                        }
                    }
                }


                header('Content-Type: text/html;charset=UTF-8');
                header('X-Robots-Tag: noindex,nofollow,noarchive');
                header('Cache-Control: no-cache,no-store,private');

                echo $newsletter->replace($email->message, $user, $email->id);

                die();
                break;

            case 'emails-css':
                $email_id = (int) $_GET['id'];

                $body = Newsletter::instance()->get_email_field($email_id, 'message');

                $x = strpos($body, '<style');
                if ($x === false)
                    return;

                $x = strpos($body, '>', $x);
                $y = strpos($body, '</style>');

                header('Content-Type: text/css;charset=UTF-8');

                echo substr($body, $x + 1, $y - $x - 1);

                die();
                break;

            case 'emails-composer-css':
                header('Cache: no-cache');
                header('Content-Type: text/css');
                echo file_get_contents(__DIR__ . '/tnp-composer/css/newsletter.css');
                $dirs = apply_filters('newsletter_blocks_dir', array());
                foreach ($dirs as $dir) {
                    $dir = str_replace('\\', '/', $dir);
                    $list = NewsletterEmails::instance()->scan_blocks_dir($dir);

                    foreach ($list as $key => $data) {
                        if (!file_exists($data['dir'] . '/style.css'))
                            continue;
                        echo "\n\n";
                        echo "/* ", $data['name'], " */\n";
                        echo file_get_contents($data['dir'] . '/style.css');
                    }
                }

                die();
                break;

            case 'emails-preview':
                if (!current_user_can('manage_categories')) {
                    die('Not enough privileges');
                }

                if (Newsletter::instance()->options['editor'] != 1 && !current_user_can('manage_options')) {
                    die('Not enough privileges');
                }
                if (!check_admin_referer('view')) {
                    die();
                }

                // Used by theme code
                $theme_options = $this->get_current_theme_options();
                $theme_url = $this->get_current_theme_url();
                header('Content-Type: text/html;charset=UTF-8');

                include($this->get_current_theme_file_path('theme.php'));

                die();
                break;

            case 'emails-preview-text':
                header('Content-Type: text/plain;charset=UTF-8');
                if (!current_user_can('manage_categories')) {
                    die('Not enough privileges');
                }

                if (Newsletter::instance()->options['editor'] != 1 && !current_user_can('manage_options')) {
                    die('Not enough privileges');
                }

                if (!check_admin_referer('view')) {
                    die();
                }

                // Used by theme code
                $theme_options = $this->get_current_theme_options();

                $file = $this->get_current_theme_file_path('theme-text.php');
                if (is_file($file)) {
                    include($this->get_current_theme_file_path('theme-text.php'));
                }

                die();
                break;


            case 'emails-create':

                if (!current_user_can('manage_categories')) {
                    die('Not enough privileges');
                }

                if ($newsletter->options['editor'] != 1 && !current_user_can('manage_options')) {
                    die('Not enough privileges');
                }

                require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
                $controls = new NewsletterControls();

                if ($controls->is_action('create')) {
                    $this->save_options($controls->data);

                    $email = array();
                    $email['status'] = 'new';
                    $email['subject'] = ''; //__('Here the email subject', 'newsletter');
                    $email['track'] = 1;

                    $theme_options = $this->get_current_theme_options();

                    $theme_url = $this->get_current_theme_url();
                    $theme_subject = '';

                    ob_start();
                    include $this->get_current_theme_file_path('theme.php');
                    $email['message'] = ob_get_clean();

                    if (!empty($theme_subject)) {
                        $email['subject'] = $theme_subject;
                    }

                    ob_start();
                    include $this->get_current_theme_file_path('theme-text.php');
                    $email['message_text'] = ob_get_clean();

                    $email['type'] = 'message';
                    $email['send_on'] = time();
                    $email = $newsletter->save_email($email);

                    header('Location: ' . $this->get_admin_page_url('edit') . '&id=' . $email->id);
                }
                die();
                break;
        }
    }

    function upgrade() {
        global $wpdb, $charset_collate;

        parent::upgrade();

        $this->upgrade_query("alter table " . NEWSLETTER_EMAILS_TABLE . " change column `type` `type` varchar(50) not null default ''");
        $this->upgrade_query("alter table " . NEWSLETTER_EMAILS_TABLE . " add column token varchar(10) not null default ''");
        $this->upgrade_query("alter table " . NEWSLETTER_EMAILS_TABLE . " drop column visibility");
        $this->upgrade_query("alter table " . NEWSLETTER_EMAILS_TABLE . " add column private tinyint(1) not null default 0");

        // Force a token to email without one already set.
        //$token = self::get_token();
        //$wpdb->query("update " . NEWSLETTER_EMAILS_TABLE . " set token='" . $token . "' where token=''");
        if ($this->old_version < '1.1.5') {
            $this->upgrade_query("update " . NEWSLETTER_EMAILS_TABLE . " set type='message' where type=''");
            $wpdb->query("update " . NEWSLETTER_EMAILS_TABLE . " set token=''");
        }
        $wpdb->query("update " . NEWSLETTER_EMAILS_TABLE . " set total=sent where status='sent' and type='message'");

        return true;
    }

    function admin_menu() {
        $this->add_menu_page('index', 'Newsletters');
        $this->add_admin_page('list', 'Email List');
        $this->add_admin_page('new', 'Email New');
        $this->add_admin_page('edit', 'Email Edit');
        $this->add_admin_page('theme', 'Email Themes');
        $this->add_admin_page('composer', 'The Composer');
        //$this->add_admin_page('cpreview', 'The Composer Preview');
    }

    /**
     * Returns the current selected theme.
     */
    function get_current_theme() {
        $theme = $this->options['theme'];
        if (empty($theme))
            return 'blank';
        else
            return $theme;
    }

    function get_current_theme_options() {
        $theme_options = $this->themes->get_options($this->get_current_theme());
        // main options merge
        $main_options = Newsletter::instance()->options;
        foreach ($main_options as $key => $value) {
            $theme_options['main_' . $key] = $value;
        }
        $info_options = Newsletter::instance()->get_options('info');
        foreach ($info_options as $key => $value) {
            $theme_options['main_' . $key] = $value;
        }
        return $theme_options;
    }

    /**
     * Returns the file path to a theme using the theme overriding rules.
     * @param type $theme
     * @param type $file
     */
    function get_theme_file_path($theme, $file) {
        return $this->themes->get_file_path($theme);
    }

    function get_current_theme_file_path($file) {
        return $this->themes->get_file_path($this->get_current_theme(), $file);
    }

    function get_current_theme_url() {
        return $this->themes->get_theme_url($this->get_current_theme());
    }

    /**
     * Returns true if the emails database still contain old 2.5 format emails.
     *
     * @return boolean
     */
    function has_old_emails() {
        return $this->store->get_count(NEWSLETTER_EMAILS_TABLE, "where type='email'") > 0;
    }

    function convert_old_emails() {
        global $newsletter;
        $list = $newsletter->get_emails('email', ARRAY_A);
        foreach ($list as &$email) {
            $email['type'] = 'message';
            $query = "select * from " . NEWSLETTER_USERS_TABLE . " where status='C'";

            if ($email['list'] != 0)
                $query .= " and list_" . $email['list'] . "=1";
            $email['preferences'] = $email['list'];

            if (!empty($email['sex'])) {
                $query .= " and sex='" . $email['sex'] . "'";
            }
            $email['query'] = $query;

            $newsletter->save_email($email);
        }
    }

    function scan_blocks_dir($dir) {

        if (!is_dir($dir)) {
            return array();
        }

        $handle = opendir($dir);
        $list = array();
        $relative_dir = substr($dir, strlen(WP_CONTENT_DIR));
        while ($file = readdir($handle)) {

            if ($file == '.' || $file == '..') continue;
            
            // The block unique key, we should find out how to biuld it, maybe an hash of the (relative) dir?
            $block_id = sanitize_key($file);

            $full_file = $dir . '/' . $file . '/block.php';
            if (!is_file($full_file)) {
                continue;
            }

            $data = get_file_data($full_file, array('name' => 'Name', 'section' => 'Section', 'description' => 'Description'));

            if (empty($data['name'])) {
                $data['name'] = $file;
            }
            if (empty($data['section'])) {
                $data['section'] = 'content';
            }
            if (empty($data['description'])) {
                $data['description'] = '';
            }
            // Absolute path of the block files
            $data['dir'] = $dir . '/' . $file;

            $data['icon'] = content_url($relative_dir . '/' . $file . '/icon.png');
            $list[$block_id] = $data;
        }
        closedir($handle);
        return $list;
    }

    /**
     * Array of arrays with every registered block and legacy block converted to the new
     * format.
     * 
     * @return array
     */
    function get_blocks() {

        static $blocks = null;
        
        if (!is_null($blocks)) return $blocks;
        
        $blocks = array();

        // Legacy blocks
        $handle = opendir(NEWSLETTER_DIR . '/emails/tnp-composer/blocks');
        while ($file = readdir($handle)) {
            if (strpos($file, '.php') === false) {
                continue;
            }

            $path_parts = pathinfo($file);
            $filename = $path_parts['filename'];
            $section = substr($filename, 0, strpos($filename, '-'));
            $index = substr($filename, strpos($filename, '-') + 1, 2);
            $block = array();
            $block['name'] = substr($filename, strrpos($filename, '-') + 1);
            $block['filename'] = $filename;
            $block['icon'] = plugins_url('newsletter') . '/emails/tnp-composer/blocks/' . $filename . '.png';
            $block['section'] = $section;
            $block['description'] = '';
            // The block ID is the file name for legacy blocks
            $blocks[sanitize_key($filename)] = $block;
        }
        closedir($handle);

        // Packaged standard blocks
        $list = $this->scan_blocks_dir(__DIR__ . '/blocks');

        $blocks = array_merge($list, $blocks);

        $dirs = apply_filters('newsletter_blocks_dir', array());

        foreach ($dirs as $dir) {
            $dir = str_replace('\\', '/', $dir);
            $list = $this->scan_blocks_dir($dir);
            $blocks = array_merge($list, $blocks);
        }
        $blocks = array_reverse($blocks);
        return $blocks;
    }

    /**
     * Return a single block (associative array) checking for legacy ID as well.
     * 
     * @param string $id
     * @return array
     */
    function get_block($id) {
        switch ($id) {
            case 'content-07-twocols.block': 
            case 'content-06-posts.block': 
                $id = 'posts';
                break;
            case 'content-04-cta.block': $id = 'cta';
                break;
//            case 'content-02-heading.block': $id = '/plugins/newsletter/emails/blocks/heading';
//                break;
        }
        
        // Conversion for old full path ID
        $id = sanitize_key(basename($id));

        // TODO: Correct id for compatibility
        $blocks = $this->get_blocks();
        if (!isset($blocks[$id])) {
            return null;
        }
        return $blocks[$id];
    }

}

NewsletterEmails::instance();
