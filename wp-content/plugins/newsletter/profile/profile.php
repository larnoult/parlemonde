<?php

defined('ABSPATH') || exit;

require_once NEWSLETTER_INCLUDES_DIR . '/module.php';

class NewsletterProfile extends NewsletterModule {

    static $instance;

    /**
     * @return NewsletterProfile
     */
    static function instance() {
        if (self::$instance == null) {
            self::$instance = new NewsletterProfile();
        }
        return self::$instance;
    }

    function __construct() {
        parent::__construct('profile', '1.1.0');
        add_action('init', array($this, 'hook_init'));
        add_action('wp_loaded', array($this, 'hook_wp_loaded'));
        add_shortcode('newsletter_profile', array($this, 'shortcode_newsletter_profile'));
    }

    function hook_init() {
        if (is_admin()) {
            add_action('wp_ajax_newsletter_users_export', array($this, 'hook_wp_ajax_newsletter_users_export'));
        }
        add_filter('newsletter_replace', array($this, 'hook_newsletter_replace'), 10, 3);
        add_filter('newsletter_page_text', array($this, 'hook_newsletter_page_text'), 10, 3);
    }

    function hook_wp_loaded() {
        global $wpdb;

        switch (Newsletter::instance()->action) {
            case 'profile':
            case 'p':
            case 'pe':
                $user = $this->check_user();
                if ($user == null) {
                    die('No subscriber found.');
                }
                $profile_url = $this->build_message_url($this->options['url'], 'profile', $user);
                $profile_url = apply_filters('newsletter_profile_url', $profile_url, $user);

                wp_redirect($profile_url);
                die();

                break;

            case 'profile-save':
            case 'ps':
                $user = $this->save_profile();
                // $user->alert is a temporary field
                wp_redirect($this->build_message_url($this->options['url'], 'profile', $user, null, $user->alert));
                die();
                break;

            case 'profile_export':
                $user = $this->get_user_from_request(true);
                header('Content-Type: application/json;charset=UTF-8');
                echo $this->to_json($user);
                die();
        }
    }

    /**
     * 
     * @param stdClass $user
     */
    function get_profile_export_url($user) {
        return $this->build_action_url('profile_export', $user);
    }

    /**
     * 
     * @param stdClass $user
     */
    function get_profile_url($user) {
        return $this->build_action_url('profile', $user);
    }

    function hook_newsletter_replace($text, $user, $email) {
        if (!$user) {
            return $text;
        }

        // Profile edit page URL and link
        $url = $this->get_profile_url($user);
        $text = $this->replace_url($text, 'PROFILE_URL', $url);
        // Profile export URL and link
        $url = $this->get_profile_export_url($user);
        $text = $this->replace_url($text, 'PROFILE_EXPORT_URL', $url);

        if (strpos($text, '{profile_form}') !== false) {
            $text = str_replace('{profile_form}', $this->get_profile_form($user), $text);
        }
        return $text;
    }

    /**
     * 
     * @param type $text
     * @param type $key
     * @param TNP_User $user
     * @return string
     */
    function hook_newsletter_page_text($text, $key, $user) {
        if ($key == 'profile') {
            if (!$user || $user->status == TNP_User::STATUS_UNSUBSCRIBED) {
                return 'Subscriber not found.';
            }
            $options = $this->get_options('main', $this->get_current_language($user));
            return $options['text'];
        }
        return $text;
    }

    function shortcode_newsletter_profile($attrs, $content) {
        $user = $this->check_user();

        if (empty($user)) {
            if (empty($content)) {
                return __('Subscriber not found.', 'newsletter');
            } else {
                return $content;
            }
        }

        return $this->get_profile_form($user);
    }

    function to_json($user) {
        global $wpdb;


        $fields = array('name', 'surname', 'sex', 'created', 'ip', 'email');
        $data = array(
            'email'=>$user->email,
            'name'=>$user->name,
            'last_name'=>$user->surname,
            'gender'=>$user->sex,
            'created'=>$user->created,
            'ip'=>$user->ip,
            );
        
        // Lists
        $data['lists'] = array();
        
        $lists = $this->get_lists_public();
        foreach ($lists as $list) {
            $field = 'list_' . $list->id;
            if ($user->$field == 1) {
                $data['lists'][] = $list->name;
            } 
        }
        
        // Profile
        $options_profile = get_option('newsletter_profile', array());
        $data['profiles'] = array();
        for ($i=1; $i<NEWSLETTER_PROFILE_MAX; $i++) {
            $field = 'profile_' . $i;
            if ($options_profile[$field . '_status'] != 1 && $options_profile[$field . '_status'] != 2) {
                continue;
            }
            $data['profiles'][] = array('name' => $options_profile[$field], 'value' => $user->$field);
        }

        // Newsletters
        if ($this->options['export_newsletters']) {
            $sent = $wpdb->get_results($wpdb->prepare("select * from {$wpdb->prefix}newsletter_sent where user_id=%d order by email_id asc", $user->id));
            $newsletters = array();
            foreach ($sent as $item) {
                $action = 'none';
                if ($item->open == 1)
                    $action = 'read';
                else if ($item->open == 2)
                    $action = 'click';

                $email = $this->get_email($item->email_id);
                if (!$email)
                    continue;
                // 'id'=>$item->email_id, 
                $newsletters[] = array('subject' => $email->subject, 'action' => $action, 'sent' => date('Y-m-d h:i:s', $email->send_on));
            }

            $data['newsletters'] = $newsletters;
        }

        $extra = apply_filters('newsletter_profile_export_extra', array());

        $data = array_merge($extra, $data);

        return json_encode($data, JSON_PRETTY_PRINT);
    }

    function get_profile_form($user) {
        // Do not pay attention to option name here, it's a compatibility problem
        
        $language = $this->get_user_language($user);
        $options = NewsletterSubscription::instance()->get_options('profile', $language);

        $buffer = '';

        $buffer .= '<div class="tnp tnp-profile">';
        $buffer .= '<form action="' . $this->build_action_url('ps') . '" method="post" onsubmit="return newsletter_check(this)">';
        $buffer .= '<input type="hidden" name="nk" value="' . esc_attr($user->id . '-' . $user->token) . '">';

        $buffer .= '<div class="tnp-field tnp-field-email">';
        $buffer .= '<label>' . esc_html($options['email']) . '</label>';
        $buffer .= '<input class="tnp-email" type="text" name="ne" required value="' . esc_attr($user->email) . '">';
        $buffer .= "</div>\n";


        if ($options['name_status'] >= 1) {
            $buffer .= '<div class="tnp-field tnp-field-firstname">';
            $buffer .= '<label>' . esc_html($options['name']) . '</label>';
            $buffer .= '<input class="tnp-firstname" type="text" name="nn" value="' . esc_attr($user->name) . '"' . ($options['name_rules'] == 1 ? ' required' : '') . '>';
            $buffer .= "</div>\n";
        }

        if ($options['surname_status'] >= 1) {
            $buffer .= '<div class="tnp-field tnp-field-lastname">';
            $buffer .= '<label>' . esc_html($options['surname']) . '</label>';
            $buffer .= '<input class="tnp-lastname" type="text" name="ns" value="' . esc_attr($user->surname) . '"' . ($options['surname_rules'] == 1 ? ' required' : '') . '>';
            $buffer .= "</div>\n";
        }

        if ($options['sex_status'] >= 1) {
            $buffer .= '<div class="tnp-field tnp-field-gender">';
            $buffer .= '<label>' . esc_html($options['sex']) . '</label>';
            $buffer .= '<select name="nx" class="tnp-gender">';
            $buffer .= '<option value="f"' . ($user->sex == 'f' ? ' selected' : '') . '>' . esc_html($options['sex_female']) . '</option>';
            $buffer .= '<option value="m"' . ($user->sex == 'm' ? ' selected' : '') . '>' . esc_html($options['sex_male']) . '</option>';
            $buffer .= '<option value="n"' . ($user->sex == 'n' ? ' selected' : '') . '>' . esc_html($options['sex_none']) . '</option>';
            $buffer .= '</select>';
            $buffer .= "</div>\n";
        }

        // Profile
        for ($i = 1; $i <= NEWSLETTER_PROFILE_MAX; $i++) {
            if ($options['profile_' . $i . '_status'] == 0) {
                continue;
            }

            $buffer .= '<div class="tnp-field tnp-field-profile">';
            $buffer .= '<label>' . esc_html($options['profile_' . $i]) . '</label>';

            $field = 'profile_' . $i;

            if ($options['profile_' . $i . '_type'] == 'text') {
                $buffer .= '<input class="tnp-profile tnp-profile-' . $i . '" type="text" name="np' . $i . '" value="' . esc_attr($user->$field) . '"' .
                        ($options['profile_' . $i . '_rules'] == 1 ? ' required' : '') . '>';
            }

            if ($options['profile_' . $i . '_type'] == 'select') {
                $buffer .= '<select class="tnp-profile tnp-profile-' . $i . '" name="np' . $i . '"' .
                        ($options['profile_' . $i . '_rules'] == 1 ? ' required' : '') . '>';
                $opts = explode(',', $options['profile_' . $i . '_options']);
                for ($j = 0; $j < count($opts); $j++) {
                    $opts[$j] = trim($opts[$j]);
                    $buffer .= '<option';
                    if ($opts[$j] == $user->$field)
                        $buffer .= ' selected';
                    $buffer .= '>' . esc_html($opts[$j]) . '</option>';
                }
                $buffer .= '</select>';
            }

            $buffer .= "</div>\n";
        }

        // Lists
        $lists = $this->get_lists_for_profile($language);
        $tmp = '';
        foreach ($lists as $list) {

            $tmp .= '<div class="tnp-field tnp-field-list">';
            $tmp .= '<label><input class="tnp-list tnp-list-' . $list->id . '" type="checkbox" name="nl[]" value="' . $list->id . '"';
            $field = 'list_' . $list->id;
            if ($user->$field == 1) {
                $tmp .= ' checked';
            }
            $tmp .= '><span class="tnp-list-label">' . esc_html($list->name) . '</span></label>';
            $tmp .= "</div>\n";
        }

        if (!empty($tmp)) {
            $buffer .= '<div class="tnp-lists">' . "\n" . $tmp . "\n" . '</div>';
        }

        $extra = apply_filters('newsletter_profile_extra', array(), $user);
        foreach ($extra as $x) {
            $buffer .= '<div class="tnp-field">';
            $buffer .= '<label>' . $x['label'] . "</label>";
            $buffer .= $x['field'];
            $buffer .= "</div>\n";
        }

        // Privacy
        $privacy_url = NewsletterSubscription::instance()->get_privacy_url();
        if (!empty($this->options['privacy_label']) && !empty($privacy_url)) {
            $buffer .= '<div class="tnp-field tnp-field-privacy">';
            if ($privacy_url) {
                $buffer .= '<a href="' . $privacy_url . '" target="_blank">';
            }

            $buffer .= $this->options['privacy_label'];

            if ($privacy_url) {
                $buffer .= '</a>';
            }
            $buffer .= "</div>\n";
        }

        $buffer .= '<div class="tnp-field tnp-field-button">';
        $buffer .= '<input class="tnp-submit" type="submit" value="' . esc_attr($this->options['save_label']) . '">';
        $buffer .= "</div>\n";

        $buffer .= "</form>\n</div>\n";

        return $buffer;
    }

    /**
     * Saves the subscriber data.
     * 
     * @return type
     */
    function save_profile() {
        global $wpdb;

        // Get the current subscriber, fail if not found
        $user = $this->get_user_from_request(true);

        // Conatains the cleaned up user data to be saved
        $data = array();
        $data['id'] = $user->id;

        $options_profile = get_option('newsletter_profile', array());
        $options_main = get_option('newsletter_main', array());

        // Not an elegant interaction between modules but...
        $subscription_module = NewsletterSubscription::instance();

        if (!$this->is_email($_REQUEST['ne'])) {
            $user->alert = $this->options['profile_error'];
            return $user;
        }

        $email = $this->normalize_email(stripslashes($_REQUEST['ne']));
        $email_changed = ($email != $user->email);

        // If the email has been changed, check if it is available
        if ($email_changed) {
            $tmp = $this->get_user($email);
            if ($tmp != null && $tmp->id != $user->id) {
                // TODO: Move the label on profile setting panel
                $user->alert = $this->options['error'];
                return $user;
            }
            $data['status'] = Newsletter::STATUS_NOT_CONFIRMED;
        }

        // General data
        $data['email'] = $email;
        if (isset($_REQUEST['nn'])) {
            $data['name'] = $this->normalize_name(stripslashes($_REQUEST['nn']));
        }
        if (isset($_REQUEST['ns'])) {
            $data['surname'] = $this->normalize_name(stripslashes($_REQUEST['ns']));
        }
        if ($options_profile['sex_status'] >= 1) {
            $data['sex'] = $_REQUEST['nx'][0];
            // Wrong data injection check
            if ($data['sex'] != 'm' && $data['sex'] != 'f' && $data['sex'] != 'n') {
                die('Wrong sex field');
            }
        }

        // Lists. If not list is present or there is no list to choose or all are unchecked.
        $nl = array();
        if (isset($_REQUEST['nl']) && is_array($_REQUEST['nl'])) {
            $nl = $_REQUEST['nl'];
        }

        // Every possible list shown in the profile must be processed
        $lists = $this->get_lists_for_profile();
        foreach ($lists as $list) {
            $field_name = 'list_' . $list->id;
            $data[$field_name] = in_array($list->id, $nl) ? 1 : 0;
        }

        // Profile
        for ($i = 1; $i <= NEWSLETTER_PROFILE_MAX; $i++) {
            // Private fields cannot be changed by the subscriber
            if ($options_profile['profile_' . $i . '_status'] == 0) {
                continue;
            }
            $data['profile_' . $i] = stripslashes($_REQUEST['np' . $i]);
        }


        // Feed by Mail service is saved here
        $data = apply_filters('newsletter_profile_save', $data);

        if ($user->status == TNP_User::STATUS_NOT_CONFIRMED) {
            $data['status'] = TNP_User::STATUS_CONFIRMED;
        }

        $user = $this->save_user($data);
        $this->add_user_log($user, 'profile');

        // Send the activation again only if we use double opt-in, otherwise it has no meaning
        // TODO: Maybe define a specific email for that and not the activation email
        if ($email_changed && $subscription_module->is_double_optin()) {
            $subscription_module->send_activation_email($user);
            // TODO: Move this option on new profile configuration panel
            $alert = $this->options['profile_email_changed'];
        }

        if (isset($alert)) {
            $user->alert = $alert;
        } else {
            // TODO: Move this label on profile settings panel
            $user->alert = $this->options['saved'];
        }
        return $user;
    }

    function upgrade() {
        global $wpdb, $charset_collate;

        parent::upgrade();

        // Migration code
        if (empty($this->options) || empty($this->options['email_changed'])) {
            // Options of the subscription module (worng name, I know)
            $options = get_option('newsletter');
            $this->options['saved'] = $options['profile_saved'];
            $this->options['text'] = $options['profile_text'];
            $this->options['email_changed'] = $options['profile_email_changed'];
            $this->options['error'] = $options['profile_error'];
            $this->options['url'] = $options['profile_url'];
            $this->save_options($this->options);
        }

        if (empty($this->options) || empty($this->options['save_label'])) {
            $options = get_option('newsletter_profile');
            $this->options['save_label'] = $options['save'];
            $this->save_options($this->options);
        }
    }

    function admin_menu() {
        $this->add_admin_page('index', 'Profile');
    }

    // Patch to avoid conflicts with the "newsletter_profile" option of the subscription module
    // TODO: Fix it
    public function get_prefix($sub = '', $language='') {
        if (empty($sub)) {
            $sub = 'main';
        }
        return parent::get_prefix($sub, $language);
    }

}

NewsletterProfile::instance();
