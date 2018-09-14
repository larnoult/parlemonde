<?php

defined('ABSPATH') || exit;

class NewsletterControls {

    var $data;
    var $action = false;
    var $button_data = '';
    var $errors = '';
    var $messages = '';
    var $warnings = array();
    var $countries = array(
        'AF' => 'Afghanistan',
        'AX' => 'Aland Islands',
        'AL' => 'Albania',
        'DZ' => 'Algeria',
        'AS' => 'American Samoa',
        'AD' => 'Andorra',
        'AO' => 'Angola',
        'AI' => 'Anguilla',
        'AQ' => 'Antarctica',
        'AG' => 'Antigua And Barbuda',
        'AR' => 'Argentina',
        'AM' => 'Armenia',
        'AW' => 'Aruba',
        'AU' => 'Australia',
        'AT' => 'Austria',
        'AZ' => 'Azerbaijan',
        'BS' => 'Bahamas',
        'BH' => 'Bahrain',
        'BD' => 'Bangladesh',
        'BB' => 'Barbados',
        'BY' => 'Belarus',
        'BE' => 'Belgium',
        'BZ' => 'Belize',
        'BJ' => 'Benin',
        'BM' => 'Bermuda',
        'BT' => 'Bhutan',
        'BO' => 'Bolivia',
        'BA' => 'Bosnia And Herzegovina',
        'BW' => 'Botswana',
        'BV' => 'Bouvet Island',
        'BR' => 'Brazil',
        'IO' => 'British Indian Ocean Territory',
        'BN' => 'Brunei Darussalam',
        'BG' => 'Bulgaria',
        'BF' => 'Burkina Faso',
        'BI' => 'Burundi',
        'KH' => 'Cambodia',
        'CM' => 'Cameroon',
        'CA' => 'Canada',
        'CV' => 'Cape Verde',
        'KY' => 'Cayman Islands',
        'CF' => 'Central African Republic',
        'TD' => 'Chad',
        'CL' => 'Chile',
        'CN' => 'China',
        'CX' => 'Christmas Island',
        'CC' => 'Cocos (Keeling) Islands',
        'CO' => 'Colombia',
        'KM' => 'Comoros',
        'CG' => 'Congo',
        'CD' => 'Congo, Democratic Republic',
        'CK' => 'Cook Islands',
        'CR' => 'Costa Rica',
        'CI' => 'Cote D\'Ivoire',
        'HR' => 'Croatia',
        'CU' => 'Cuba',
        'CY' => 'Cyprus',
        'CZ' => 'Czech Republic',
        'DK' => 'Denmark',
        'DJ' => 'Djibouti',
        'DM' => 'Dominica',
        'DO' => 'Dominican Republic',
        'EC' => 'Ecuador',
        'EG' => 'Egypt',
        'SV' => 'El Salvador',
        'GQ' => 'Equatorial Guinea',
        'ER' => 'Eritrea',
        'EE' => 'Estonia',
        'ET' => 'Ethiopia',
        'FK' => 'Falkland Islands (Malvinas)',
        'FO' => 'Faroe Islands',
        'FJ' => 'Fiji',
        'FI' => 'Finland',
        'FR' => 'France',
        'GF' => 'French Guiana',
        'PF' => 'French Polynesia',
        'TF' => 'French Southern Territories',
        'GA' => 'Gabon',
        'GM' => 'Gambia',
        'GE' => 'Georgia',
        'DE' => 'Germany',
        'GH' => 'Ghana',
        'GI' => 'Gibraltar',
        'GR' => 'Greece',
        'GL' => 'Greenland',
        'GD' => 'Grenada',
        'GP' => 'Guadeloupe',
        'GU' => 'Guam',
        'GT' => 'Guatemala',
        'GG' => 'Guernsey',
        'GN' => 'Guinea',
        'GW' => 'Guinea-Bissau',
        'GY' => 'Guyana',
        'HT' => 'Haiti',
        'HM' => 'Heard Island & Mcdonald Islands',
        'VA' => 'Holy See (Vatican City State)',
        'HN' => 'Honduras',
        'HK' => 'Hong Kong',
        'HU' => 'Hungary',
        'IS' => 'Iceland',
        'IN' => 'India',
        'ID' => 'Indonesia',
        'IR' => 'Iran, Islamic Republic Of',
        'IQ' => 'Iraq',
        'IE' => 'Ireland',
        'IM' => 'Isle Of Man',
        'IL' => 'Israel',
        'IT' => 'Italy',
        'JM' => 'Jamaica',
        'JP' => 'Japan',
        'JE' => 'Jersey',
        'JO' => 'Jordan',
        'KZ' => 'Kazakhstan',
        'KE' => 'Kenya',
        'KI' => 'Kiribati',
        'KR' => 'Korea',
        'KW' => 'Kuwait',
        'KG' => 'Kyrgyzstan',
        'LA' => 'Lao People\'s Democratic Republic',
        'LV' => 'Latvia',
        'LB' => 'Lebanon',
        'LS' => 'Lesotho',
        'LR' => 'Liberia',
        'LY' => 'Libyan Arab Jamahiriya',
        'LI' => 'Liechtenstein',
        'LT' => 'Lithuania',
        'LU' => 'Luxembourg',
        'MO' => 'Macao',
        'MK' => 'Macedonia',
        'MG' => 'Madagascar',
        'MW' => 'Malawi',
        'MY' => 'Malaysia',
        'MV' => 'Maldives',
        'ML' => 'Mali',
        'MT' => 'Malta',
        'MH' => 'Marshall Islands',
        'MQ' => 'Martinique',
        'MR' => 'Mauritania',
        'MU' => 'Mauritius',
        'YT' => 'Mayotte',
        'MX' => 'Mexico',
        'FM' => 'Micronesia, Federated States Of',
        'MD' => 'Moldova',
        'MC' => 'Monaco',
        'MN' => 'Mongolia',
        'ME' => 'Montenegro',
        'MS' => 'Montserrat',
        'MA' => 'Morocco',
        'MZ' => 'Mozambique',
        'MM' => 'Myanmar',
        'NA' => 'Namibia',
        'NR' => 'Nauru',
        'NP' => 'Nepal',
        'NL' => 'Netherlands',
        'AN' => 'Netherlands Antilles',
        'NC' => 'New Caledonia',
        'NZ' => 'New Zealand',
        'NI' => 'Nicaragua',
        'NE' => 'Niger',
        'NG' => 'Nigeria',
        'NU' => 'Niue',
        'NF' => 'Norfolk Island',
        'MP' => 'Northern Mariana Islands',
        'NO' => 'Norway',
        'OM' => 'Oman',
        'PK' => 'Pakistan',
        'PW' => 'Palau',
        'PS' => 'Palestinian Territory, Occupied',
        'PA' => 'Panama',
        'PG' => 'Papua New Guinea',
        'PY' => 'Paraguay',
        'PE' => 'Peru',
        'PH' => 'Philippines',
        'PN' => 'Pitcairn',
        'PL' => 'Poland',
        'PT' => 'Portugal',
        'PR' => 'Puerto Rico',
        'QA' => 'Qatar',
        'RE' => 'Reunion',
        'RO' => 'Romania',
        'RU' => 'Russian Federation',
        'RW' => 'Rwanda',
        'BL' => 'Saint Barthelemy',
        'SH' => 'Saint Helena',
        'KN' => 'Saint Kitts And Nevis',
        'LC' => 'Saint Lucia',
        'MF' => 'Saint Martin',
        'PM' => 'Saint Pierre And Miquelon',
        'VC' => 'Saint Vincent And Grenadines',
        'WS' => 'Samoa',
        'SM' => 'San Marino',
        'ST' => 'Sao Tome And Principe',
        'SA' => 'Saudi Arabia',
        'SN' => 'Senegal',
        'RS' => 'Serbia',
        'SC' => 'Seychelles',
        'SL' => 'Sierra Leone',
        'SG' => 'Singapore',
        'SK' => 'Slovakia',
        'SI' => 'Slovenia',
        'SB' => 'Solomon Islands',
        'SO' => 'Somalia',
        'ZA' => 'South Africa',
        'GS' => 'South Georgia And Sandwich Isl.',
        'ES' => 'Spain',
        'LK' => 'Sri Lanka',
        'SD' => 'Sudan',
        'SR' => 'Suriname',
        'SJ' => 'Svalbard And Jan Mayen',
        'SZ' => 'Swaziland',
        'SE' => 'Sweden',
        'CH' => 'Switzerland',
        'SY' => 'Syrian Arab Republic',
        'TW' => 'Taiwan',
        'TJ' => 'Tajikistan',
        'TZ' => 'Tanzania',
        'TH' => 'Thailand',
        'TL' => 'Timor-Leste',
        'TG' => 'Togo',
        'TK' => 'Tokelau',
        'TO' => 'Tonga',
        'TT' => 'Trinidad And Tobago',
        'TN' => 'Tunisia',
        'TR' => 'Turkey',
        'TM' => 'Turkmenistan',
        'TC' => 'Turks And Caicos Islands',
        'TV' => 'Tuvalu',
        'UG' => 'Uganda',
        'UA' => 'Ukraine',
        'AE' => 'United Arab Emirates',
        'GB' => 'United Kingdom',
        'US' => 'United States',
        'UM' => 'United States Outlying Islands',
        'UY' => 'Uruguay',
        'UZ' => 'Uzbekistan',
        'VU' => 'Vanuatu',
        'VE' => 'Venezuela',
        'VN' => 'Viet Nam',
        'VG' => 'Virgin Islands, British',
        'VI' => 'Virgin Islands, U.S.',
        'WF' => 'Wallis And Futuna',
        'EH' => 'Western Sahara',
        'YE' => 'Yemen',
        'ZM' => 'Zambia',
        'ZW' => 'Zimbabwe',
    );

    function __construct($options = null) {
        if ($options == null) {
            if (isset($_POST['options'])) {
                $this->data = stripslashes_deep($_POST['options']);
            }
        } else {
            $this->data = $options;
        }

        if (isset($_REQUEST['act'])) {
            $this->action = $_REQUEST['act'];
        }

        if (isset($_REQUEST['btn'])) {
            $this->button_data = $_REQUEST['btn'];
        }
        // Fields analysis
        if (isset($_REQUEST['fields'])) {
            $fields = $_REQUEST['fields'];
            if (is_array($fields)) {
                foreach ($fields as $name => $type) {
                    if ($type == 'datetime') {
                        // Ex. The user insert 01/07/2012 14:30 and it set the time zone to +2. We cannot use the
                        // mktime, since it uses the time zone of the machine. We create the time as if we are on
                        // GMT 0 and then we subtract the GMT offset (the example date and time on GMT+2 happens
                        // "before").

                        $time = gmmktime($_REQUEST[$name . '_hour'], 0, 0, $_REQUEST[$name . '_month'], $_REQUEST[$name . '_day'], $_REQUEST[$name . '_year']);
                        $time -= get_option('gmt_offset') * 3600;
                        $this->data[$name] = $time;
                    }
                }
            }
        }
    }

    function merge($options) {
        if (!is_array($options))
            return;
        if ($this->data == null)
            $this->data = array();
        $this->data = array_merge($this->data, $options);
    }

    function merge_defaults($defaults) {
        if ($this->data == null)
            $this->data = $defaults;
        else
            $this->data = array_merge($defaults, $this->data);
    }

    /**
     * Return true is there in an asked action is no action name is specified or
     * true is the requested action matches the passed action.
     * Dies if it is not a safe call.
     */
    function is_action($action = null) {
        if ($action == null)
            return $this->action != null;
        if ($this->action == null)
            return false;
        if ($this->action != $action)
            return false;
        if (check_admin_referer('save'))
            return true;
        die('Invalid call');
    }

    function get_value($name) {
        if (!isset($this->data[$name]))
            return null;
        return $this->data[$name];
    }

    function get_value_array($name) {
        if (!isset($this->data[$name]) || !is_array($this->data[$name]))
            return array();
        return $this->data[$name];
    }

    /**
     * Show the errors and messages.
     */
    function show() {
        static $shown = false;

        if ($shown) {
            return;
        }
        $shown = true;

        if (!empty($this->errors)) {
            echo '<div class="tnp-error">';
            echo $this->errors;
            echo '</div>';
        }
        if (!empty($this->warnings)) {
            foreach ((array) $this->warnings as $warning) {
                echo '<div class="tnp-warning">';
                echo $warning;
                echo '</div>';
            }
        }
        if (!empty($this->messages)) {
            echo '<div class="tnp-message">';
            echo $this->messages;
            echo '</div>';
        }
    }

    function add_message_saved() {
        if (!empty($this->messages)) {
            $this->messages .= '<br><br>';
        }
        $this->messages .= __('Saved.', 'newsletter');
    }

    function add_message_deleted() {
        if (!empty($this->messages)) {
            $this->messages .= '<br><br>';
        }
        $this->messages .= __('Deleted.', 'newsletter');
    }

    function add_message_reset() {
        if (!empty($this->messages)) {
            $this->messages .= '<br><br>';
        }
        $this->messages .= __('Options reset.', 'newsletter');
    }

    function add_message_done() {
        if (!empty($this->messages)) {
            $this->messages .= '<br><br>';
        }
        $this->messages .= __('Done.', 'newsletter');
    }

    function add_language_warning() {
        $newsletter = Newsletter::instance();
        $current_language = $newsletter->get_current_language();

        if (!$current_language) {
            return;
        }
        $this->warnings[] = 'You are configuring the language <strong>' . $newsletter->get_language_label($current_language) . '</strong>. Switch to "all languages" to see every options.';
    }

    function hint($text, $url = '') {
        echo '<div class="hints">';
        // Do not escape that, it can be formatted
        echo $text;
        if (!empty($url)) {
            echo ' <a href="' . esc_attr($url) . '" target="_blank">Read more</a>.';
        }
        echo '</div>';
    }

    function yesno($name) {
        $value = isset($this->data[$name]) ? (int) $this->data[$name] : 0;

        echo '<select style="width: 60px" name="options[' . esc_attr($name) . ']">';
        echo '<option value="0"';
        if ($value == 0) {
            echo ' selected';
        }
        echo '>', __('No', 'newsletter'), '</option>';
        echo '<option value="1"';
        if ($value == 1) {
            echo ' selected';
        }
        echo '>', __('Yes', 'newsletter'), '</option>';
        echo '</select>&nbsp;&nbsp;&nbsp;';
    }

    function enabled($name) {
        $value = isset($this->data[$name]) ? (int) $this->data[$name] : 0;

        echo '<select style="width: 100px" name="options[' . esc_attr($name) . ']">';
        echo '<option value="0"';
        if ($value == 0) {
            echo ' selected';
        }
        echo '>', __('Disabled', 'newsletter'), '</option>';
        echo '<option value="1"';
        if ($value == 1) {
            echo ' selected';
        }
        echo '>', __('Enabled', 'newsletter'), '</option>';
        echo '</select>';
    }

    function disabled($name) {
        $value = isset($this->data[$name]) ? (int) $this->data[$name] : 0;

        echo '<select style="width: 100px" name="options[' . esc_attr($name) . ']">';
        echo '<option value="0"';
        if ($value == 0) {
            echo ' selected';
        }
        echo '>Enabled</option>';
        echo '<option value="1"';
        if ($value == 1) {
            echo ' selected';
        }
        echo '>Disabled</option>';
        echo '</select>';
    }

    /**
     * Creates a set of checkbox all named as $name with values and labels extracted from
     * $values_labels. A checkbox will be checked if internal data under key $name is an array
     * and contains the value of the current (echoing) checkbox.
     *
     * On submit it produces an array under the name $name IF at least one checkbox has
     * been checked. Otherwise the key won't be present.
     *
     * @param array $values
     * @param string $name
     * @param array $values_labels
     */
    function checkboxes_group($name, $values_labels) {
        $value_array = $this->get_value_array($name);

        echo "<div class='newsletter-checkboxes-group'>";
        foreach ($values_labels as $value => $label) {
            echo "<div class='newsletter-checkboxes-item'>";
            echo '<label><input type="checkbox" id="' . esc_attr($name) . '" name="options[' . esc_attr($name) . '][]" value="' . esc_attr($value) . '"';
            if (array_search($value, $value_array) !== false) {
                echo ' checked';
            }
            echo '>';
            if ($label != '') {
                echo esc_html($label);
            }
            echo "</label></div>";
        }
        echo "</div><div style='clear: both'></div>";
    }

    /** Creates a checkbox group with all public post types.
     */
    function post_types($name = 'post_types') {
        $list = array();
        $post_types = get_post_types(array('public' => true), 'objects', 'and');
        foreach ($post_types as $post_type) {
            $list[$post_type->name] = $post_type->labels->name;
        }

        $this->checkboxes_group($name, $list);
    }

    function posts_select($name, $max = 20, $args = array()) {
        $args = array_merge(array(
            'posts_per_page' => 5,
            'offset' => 0,
            'category' => '',
            'category_name' => '',
            'orderby' => 'date',
            'order' => 'DESC',
            'include' => '',
            'exclude' => '',
            'meta_key' => '',
            'meta_value' => '',
            'post_type' => 'post',
            'post_mime_type' => '',
            'post_parent' => '',
            'author' => '',
            'author_name' => '',
            'post_status' => 'publish',
            'suppress_filters' => true
                ), $args);
        $args['posts_per_page'] = $max;

        $posts = get_posts($args);
        $options = array();
        foreach ($posts as $post) {
            $options['' . $post->ID] = $post->post_title;
        }

        $this->select($name, $options);
    }

    function select_number($name, $min, $max) {
        $options = array();
        for ($i = $min; $i <= $max; $i++) {
            $options['' . $i] = $i;
        }
        $this->select($name, $options);
    }

    function page($name = 'page', $first = null, $language = '') {
        $args = array(
            'post_type' => 'page',
            'posts_per_page' => 1000,
            'offset' => 0,
            'orderby' => 'post_title',
            'post_status' => 'any',
            'suppress_filters' => true
        );

        $pages = get_posts($args);
        //$pages = get_pages();
        $options = array();
        foreach ($pages as $page) {
            /* @var $page WP_Post */
            $label = $page->post_title;
            if ($page->post_status != 'publish') {
                $label .= ' (' . $page->post_status . ')';
            }
            $options[$page->ID] = $label;
        }
        $this->select($name, $options, $first);
    }

    /** Used to create a select which is part of a group of controls identified by $name that will
     * produce an array of values as $_REQUEST['name'].
     * @param string $name
     * @param array $options Associative array
     */
    function select_group($name, $options) {
        $value_array = $this->get_value_array($name);

        echo '<select name="options[' . esc_attr($name) . '][]">';

        foreach ($options as $key => $label) {
            echo '<option value="' . esc_attr($key) . '"';
            if (array_search($key, $value_array) !== false) {
                echo ' selected';
            }
            echo '>' . esc_html($label) . '</option>';
        }

        echo '</select>';
    }

    function select($name, $options, $first = null) {
        $value = $this->get_value($name);

        echo '<select id="options-' . esc_attr($name) . '" name="options[' . esc_attr($name) . ']">';
        if (!empty($first)) {
            echo '<option value="">' . esc_html($first) . '</option>';
        }
        foreach ($options as $key => $label) {
            echo '<option value="' . esc_attr($key) . '"';
            if ($value == $key)
                echo ' selected';
            echo '>' . esc_html($label) . '</option>';
        }
        echo '</select>';
    }

    function select_images($name, $options, $first = null) {
        $value = $this->get_value($name);

        echo '<select id="options-' . esc_attr($name) . '" name="options[' . esc_attr($name) . ']" style="min-width: 200px">';
        if (!empty($first)) {
            echo '<option value="">' . esc_html($first) . '</option>';
        } else {
//            if (empty($value)) {
//                $keys = array_keys($options);
//                $value = $keys[0];
//            }
        }
        foreach ($options as $key => $data) {
            echo '<option value="' . esc_attr($key) . '" image="' . esc_attr($data['image']) . '"';
            if ($value == $key)
                echo ' selected';
            echo '>' . esc_html($data['label']) . '</option>';
        }
        echo '</select>';
        echo '<script>jQuery("#options-' . esc_attr($name) . '").select2({templateResult: tnp_select_images, templateSelection: tnp_select_images_selection});</script>';
    }

    function select2($name, $options, $first = null, $multiple = false, $style = null, $placeholder = '') {

        if ($multiple) {
            $option_name = "options[" . esc_attr($name) . "][]";
        } else {
            $option_name = "options[" . esc_attr($name) . "]";
        }

        if (is_null($style)) {
            $style = 'width: 100%';
        }

        $value = $this->get_value($name);

        echo '<select id="options-', esc_attr($name), '" name="', $option_name, '" style="', $style, '"',
        ($multiple ? ' multiple' : ''), '>';
        if (!empty($first)) {
            echo '<option value="">' . esc_html($first) . '</option>';
        }

        foreach ($options as $key => $data) {
            echo '<option value="' . esc_attr($key) . '"';
            if (is_array($value) && in_array($key, $value) || $value == $key)
                echo ' selected';
            echo '>' . esc_html($data) . '</option>';
        }
        echo '</select>';
        echo '<script>jQuery("#options-' . esc_attr($name) . '").select2({placeholder: "', esc_js($placeholder), '"});</script>';
    }

    function select_grouped($name, $groups) {
        $value = $this->get_value($name);

        echo '<select name="options[' . $name . ']">';

        foreach ($groups as $group) {
            echo '<optgroup label="' . esc_attr($group['']) . '">';
            if (!empty($group)) {
                foreach ($group as $key => $label) {
                    if ($key == '') {
                        continue;
                    }
                    echo '<option value="' . esc_attr($key) . '"';
                    if ($value == $key) {
                        echo ' selected';
                    }
                    echo '>' . esc_html($label) . '</option>';
                }
            }
            echo '</optgroup>';
        }
        echo '</select>';
    }

    /**
     * Generated a select control with all available templates. From version 3 there are
     * only on kind of templates, they are no more separated by type.
     */
    function themes($name, $themes, $submit_on_click = true) {
        foreach ($themes as $key => $data) {
            echo '<label style="display: block; float: left; text-align: center; margin-right: 10px;">';
            echo esc_html($key) . '<br>';
            echo '<img src="' . esc_attr($data['screenshot']) . '" width="100" height="100" style="border: 1px solid #666; padding: 5px"><br>';
            echo '<input style="position: relative; top: -40px" type="radio" onchange="this.form.act.value=\'theme\';this.form.submit()" name="options[' . esc_attr($name) . ']" value="' . esc_attr($key) . '"';
            if ($this->data[$name] == $key) {
                echo ' checked';
            }
            echo '>';
            echo '</label>';
        }
        echo '<div style="clear: both"></div>';
    }

    function value($name) {
        echo htmlspecialchars($this->data[$name]);
    }

    function value_date($name, $show_remaining = true) {
        $time = $this->get_value($name);

        echo gmdate(get_option('date_format') . ' ' . get_option('time_format'), $time + get_option('gmt_offset') * 3600);
        $delta = $time - time();
        if ($show_remaining && $delta > 0) {
            echo 'Remaining: ';
            $delta = $time - time();
            $days = floor($delta / (24 * 3600));
            $delta = $delta - $days * 24 * 3600;
            $hours = floor($delta / 3600);
            $delta = $delta - $hours * 3600;
            $minutes = floor($delta / 60);

            if ($days > 0)
                echo $days . ' days ';
            echo $hours . ' hours ';
            echo $minutes . ' minutes ';
        }
    }

    function text($name, $size = 20, $placeholder = '') {
        $value = $this->get_value($name);
        echo '<input id="options-', esc_attr($name), '" placeholder="' . esc_attr($placeholder) . '" name="options[' . $name . ']" type="text" size="' . $size . '" value="';
        echo esc_attr($value);
        echo '">';
    }

    function text_email($name, $size = 40) {
        $value = $this->get_value($name);
        echo '<input name="options[' . esc_attr($name) . ']" type="email" placeholder="';
        echo esc_attr__('Valid email address', 'newsletter');
        echo '" size="' . esc_attr($size) . '" value="';
        echo esc_attr($value);
        echo '">';
    }

    function text_url($name, $size = 40) {
        $value = $this->get_value($name);
        echo '<input name="options[' . esc_attr($name) . ']" type="url" placeholder="http://..." size="' . esc_attr($size) . '" value="';
        echo esc_attr($value);
        echo '"/>';
    }

    function hidden($name) {
        $value = $this->get_value($name);
        echo '<input name="options[' . $name . ']" type="hidden" value="';
        echo esc_attr($value);
        echo '"/>';
    }

    function button($action, $label, $function = null) {
        if ($function != null) {
            echo '<input class="button-secondary" type="button" value="' . esc_attr($label) . '" onclick="this.form.act.value=\'' . esc_attr($action) . '\';' . esc_html($function) . '"/>';
        } else {
            echo '<input class="button-secondary" type="submit" value="' . esc_attr($label) . '" onclick="this.form.act.value=\'' . esc_attr($action) . '\';return true;"/>';
        }
    }

    /**
     * With translated "Save" label.
     */
    function button_save($function = null) {
        $this->button_primary('save', '<i class="fa fa-save"></i> ' . __('Save', 'newsletter'), $function);
    }

    function button_reset($data = '') {
        echo '<button class="button-secondary" onclick="this.form.btn.value=\'' . esc_attr($data) . '\';this.form.act.value=\'reset\';if (!confirm(\'';
        echo esc_attr(esc_js(__('Proceed?', 'newsletter')));
        echo '\')) return false;">';
        echo '<i class="fa fa-reply"></i> ';
        echo esc_html(__('Reset', 'newsletter'));
        echo '</button>';
    }

    function button_back($url) {
        echo '<a href="';
        echo esc_attr($url);
        echo '" class="button-primary"><i class="fa fa-chevron-left"></i>&nbsp;';
        _e('Back', 'newsletter');
        echo '</a>';
    }

    /**
     * Creates a button with "copy" action.
     * @param type $data
     */
    function button_copy($data = '') {
        echo '<button class="button-secondary" onclick="this.form.btn.value=\'' . esc_attr($data) . '\';this.form.act.value=\'copy\';if (!confirm(\'';
        echo esc_attr(esc_js(__('Proceed?', 'newsletter')));
        echo '\')) return false;">';
        echo '<i class="fa fa-copy"></i> ';
        echo esc_html(__('Duplicate', 'newsletter'));
        echo '</button>';
    }

    /**
     * Creates a button wirh "delete" action.
     * @param type $data
     */
    function button_delete($data = '') {
        echo '<button class="button-secondary" onclick="this.form.btn.value=\'' . esc_attr($data) . '\';this.form.act.value=\'delete\';if (!confirm(\'';
        echo esc_attr(esc_js(__('Proceed?', 'newsletter')));
        echo '\')) return false;">';
        echo '<i class="fa fa-times"></i> ';
        echo esc_html(__('Delete', 'newsletter'));
        echo '</button>';
    }

    function button_primary($action, $label, $function = null) {
        if ($function != null) {
            echo '<button class="button-primary" onclick="this.form.act.value=\'' . esc_attr($action) . '\';' . esc_attr($function) . '">', $label, '</button>';
        } else {
            echo '<button class="button-primary" onclick="this.form.act.value=\'' . esc_attr($action) . '\';this.form.submit()"/>', $label, '</button>';
        }
    }

    function button_confirm($action, $label, $message = '', $data = '') {
        if (empty($message)) {
            $message = __('Are you sure?', 'newsletter');
        }

        echo '<input class="button-secondary" type="button" value="' . esc_attr($label) . '" onclick="this.form.btn.value=\'' . esc_attr($data) . '\';this.form.act.value=\'' . esc_attr($action) . '\';if (confirm(\'' .
        esc_attr(esc_js($message)) . '\')) this.form.submit()"/>';
    }

    function editor($name, $rows = 5, $cols = 75) {
        echo '<textarea class="visual" name="options[' . esc_attr($name) . ']" style="width: 100%" wrap="off" rows="' . esc_attr($rows) . '">';
        echo esc_html($this->get_value($name));
        echo '</textarea>';
    }

    function wp_editor($name, $settings = array()) {
        $value = $this->get_value($name);
        wp_editor($value, $name, array_merge(array(
            'tinymce' => array('content_css' => plugins_url('newsletter') . '/css/wp-editor.css?ver=' . filemtime(NEWSLETTER_DIR . '/css/wp-editor.css')),
            'textarea_name' => 'options[' . esc_attr($name) . ']',
            'wpautop' => false
                        ), $settings));
        //echo '<p class="description">You can install <a href="https://wordpress.org/plugins/tinymce-advanced/" target="_blank">TinyMCE Advanced</a> for advanced editing features</p>';
    }

    function textarea($name, $width = '100%', $height = '50') {
        $value = $this->get_value($name);
        if (is_array($value)) {
            $value = implode("\n", $value);
        }
        echo '<textarea id="options-' . esc_attr($name) . '" class="dynamic" name="options[' . esc_attr($name) . ']" wrap="off" style="width:' . esc_attr($width) . ';height:' . esc_attr($height) . '">';
        echo esc_html($value);
        echo '</textarea>';
    }

    function textarea_fixed($name, $width = '100%', $height = '200') {
        $value = $this->get_value($name);
        echo '<textarea id="options-' . esc_attr($name) . '" name="options[' . esc_attr($name) . ']" wrap="off" style="width:' . esc_attr($width) . ';height:' . esc_attr($height) . 'px">';
        echo esc_html($value);
        echo '</textarea>';
    }

    function textarea_preview($name, $width = '100%', $height = '200', $header = '', $footer = '', $switch_button = true) {
        $value = $this->get_value($name);
        //do_action('newsletter_controls_textarea_preview', $name);
        if ($switch_button) {
            echo '<input class="button-primary" type="button" onclick="newsletter_textarea_preview(\'options-' . esc_attr($name) . '\', \'\', \'\')" value="Switch editor/preview">';
            echo '<br><br>';
        }
        echo '<div style="box-sizing: border-box; position: relative; margin: 0; padding: 0; width:' . esc_attr($width) . '; height:' . esc_attr($height) . '">';
        echo '<textarea id="options-' . esc_attr($name) . '" name="options[' . esc_attr($name) . ']" wrap="off" style="width:' . esc_attr($width) . ';height:' . esc_attr($height) . 'px">';
        echo esc_html($value);
        echo '</textarea>';
        echo '<div id="options-' . esc_attr($name) . '-preview" style="box-sizing: border-box; background-color: #eee; border: 1px solid #bbb; padding: 15px; width: auto; position: absolute; top: 20px; left: 20px; box-shadow: 0 0 20px #777; z-index: 10000; display: none">';
        echo '<iframe id="options-' . esc_attr($name) . '-iframe" class="tnp-editor-preview-desktop"></iframe>';
        echo '<iframe id="options-' . esc_attr($name) . '-iframe-phone" class="tnp-editor-preview-mobile"></iframe>';
        echo '</div>';
        echo '</div>';
    }

    function email($prefix, $editor = null, $disable_option = false, $settings = array()) {
        if ($disable_option) {
            $this->disabled($prefix . '_disabled');
            echo '<br>';
        }

        $this->text($prefix . '_subject', 90, 'Subject');
        echo '<br><br>';

        if ($editor == 'wordpress') {
            $this->wp_editor($prefix . '_message', $settings);
        } else if ($editor == 'textarea') {
            $this->textarea($prefix . '_message');
        } else {
            $this->editor($prefix . '_message');
        }
    }

    function checkbox($name, $label = '') {
        if ($label != '') {
            echo '<label>';
        }
        echo '<input type="checkbox" id="' . esc_attr($name) . '" name="options[' . esc_attr($name) . ']" value="1"';
        if (!empty($this->data[$name])) {
            echo ' checked';
        }
        echo '>';
        if ($label != '') {
            echo '&nbsp;' . esc_html($label) . '</label>';
        }
    }

    function checkbox2($name, $label = '') {
        if ($label != '') {
            echo '<label>';
        }
        echo '<input type="checkbox" id="' . esc_attr($name) . '" onchange="document.getElementById(\'' . esc_attr($name) . '_hidden\').value=this.checked?\'1\':\'0\'"';
        if (!empty($this->data[$name])) {
            echo ' checked="checked"';
        }
        echo '>';
        if ($label != '') {
            echo '&nbsp;' . esc_html($label) . '</label>';
        }
        echo '<input type="hidden" id="' . esc_attr($name) . '_hidden" name="options[' . esc_attr($name) . ']" value="';

        echo empty($this->data[$name]) ? '0' : '1';
        echo '">';
    }

    function radio($name, $value, $label = '') {
        if ($label != '') {
            echo '<label>';
        }
        echo '<input type="radio" id="' . esc_attr($name) . '" name="options[' . esc_attr($name) . ']" value="' . esc_attr($value) . '"';
        $v = $this->get_value($name);
        if ($v == $value) {
            echo ' checked="checked"';
        }
        echo '>';
        if ($label != '') {
            echo '&nbsp;' . esc_html($label) . '</label>';
        }
    }

    /**
     * Creates a checkbox named $name and checked if the internal data contains under
     * the key $name an array containig the passed value.
     */
    function checkbox_group($name, $value, $label = '') {
        echo '<label><input type="checkbox" id="' . esc_attr($name) . '" name="options[' . esc_attr($name) . '][]" value="' . esc_attr($value) . '"';
        if (isset($this->data[$name]) && is_array($this->data[$name]) && array_search($value, $this->data[$name]) !== false) {
            echo ' checked';
        }
        echo '>';
        if ($label != '') {
            echo esc_html($label);
        }
        echo '</label>';
    }

    function checkboxes($name, $options) {
        echo '<div class="tnp-checkboxes">';
        foreach ($options as $value => $label) {
            $this->checkbox_group($name, $value, $label);
        }
        echo '<div style="clear: both"></div>';
        echo '</div>';
    }

    function color($name) {

        $value = $this->get_value($name);
        echo '<input id="options-', esc_attr($name), '" class="tnp-controls-color" name="options[' . $name . ']" type="text" value="';
        echo esc_attr($value);
        echo '">';
    }

    /** Creates a set of checkbox named $name_[category id] (so they are posted with distinct names).
     */
    function categories($name = 'category') {
        $categories = get_categories();
        echo '<div class="newsletter-checkboxes-group">';
        foreach ($categories as $c) {
            echo '<div class="newsletter-checkboxes-item">';
            $this->checkbox($name . '_' . $c->cat_ID, esc_html($c->cat_name));
            echo '</div>';
        }
        echo '<div style="clear: both"></div>';
        echo '</div>';
    }

    /**
     * Creates a set of checkbox to activate the profile preferences. Every checkbox has a DIV around to
     * be formatted.
     */
    function categories_group($name, $show_mode = false) {
        $categories = get_categories();
        if ($show_mode) {
            $this->select($name . '_mode', array('include' => 'To be included', 'exclude' => 'To be excluded'));
        }
        echo '<div class="newsletter-checkboxes-group">';
        foreach ($categories as &$c) {
            echo '<div class="newsletter-checkboxes-item">';
            $this->checkbox_group($name, $c->cat_ID, esc_html($c->cat_name));
            echo '</div>';
        }
        echo '<div style="clear: both"></div>';
        echo '</div>';
    }

    /**
     * Creates a set of checkboxes named $name_[preference number] (so they are
     * distinct fields).
     * Empty preferences are skipped.
     */
    function preferences($name = 'preferences') {
        $lists = NewsletterSubscription::instance()->options_lists;
        echo '<div class="newsletter-preferences-group">';

        for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) {
            if (empty($lists['list_' . $i])) {
                continue;
            }
            echo '<div class="newsletter-preferences-item">';
            $this->checkbox2($name . '_' . $i, esc_html($lists['list_' . $i]));
            echo '</div>';
        }
    }

    /**
     * Creates a set of checkboxes all names $name[] and the preference number as value
     * so the selected checkboxes are retrieved as an array of values ($REQUEST[$name]
     * will be an array if at east one preference is checked).
     */
    function preferences_group($name = 'preferences') {
        $lists = NewsletterSubscription::instance()->options_lists;

        echo '<div class="newsletter-preferences-group">';
        for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) {
            if (empty($lists['list_' . $i])) {
                continue;
            }
            echo '<div class="newsletter-preferences-item">';
            $this->checkbox_group($name, $i, '(' . $i . ') ' . esc_html($lists['list_' . $i]));
            echo '</div>';
        }
        echo '<div style="clear: both"></div>';
        echo '<a href="https://www.thenewsletterplugin.com/plugins/newsletter/newsletter-preferences" target="_blank">'
        . 'Click here to read more about preferences.'
        . '</a> They can be configured on Subscription Form - Profile fields panel.';
        echo '</div>';
    }

    /** Creates as many selects as the active preferences with the three values
     * 'any', 'yes', 'no' corresponding to the values 0, 1, 2.
     */
    function preferences_selects($name = 'preferences', $skip_empty = false) {
        $lists = NewsletterSubscription::instance()->options_lists;

        echo '<div class="newsletter-preferences-group">';
        for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) {
            if (empty($lists['list_' . $i])) {
                continue;
            }

            echo '<div class="newsletter-preferences-item">';

            $this->select($name . '_' . $i, array(0 => 'Any', 1 => 'Yes', 2 => 'No'));
            echo '(' . $i . ') ' . esc_html($lists['list_' . $i]);

            echo '</div>';
        }
        echo '<div style="clear: both"></div>';
        echo '<a href="https://www.thenewsletterplugin.com/plugins/newsletter/newsletter-preferences" target="_blank">Click here know more about preferences.</a> They can be configured on Subscription/Form field panel.';
        echo '</div>';
    }

    /**
     * Creates a single select with the active preferences. 
     */
    function preferences_select($name = 'preference', $empty_label = null) {
        $options = NewsletterSubscription::instance()->options_lists;

        $lists = array();
        if ($empty_label) {
            $lists[''] = $empty_label;
        }
        for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) {
            $lists['' . $i] = '(' . $i . ') ' . $options['list_' . $i];
        }
        $this->select($name, $lists);
        echo ' <a href="admin.php?page=newsletter_subscription_lists" target="_blank"><i class="fa fa-edit"></i></a>';
    }

    function lists_select($name = 'list', $empty_label = null) {
        $objs = Newsletter::instance()->get_lists();
        $lists = array();
        if ($empty_label) {
            $lists[''] = $empty_label;
        }
        foreach ($objs as $list) {
            $lists['' . $list->id] = '[' . $list->id . '] ' . $list->name;
        }
        $this->select($name, $lists);
    }

    /**
     * Generates an associative array with the active lists to be used in a select.
     * @param string $empty_label
     * @return array
     */
    function get_list_options($empty_label = null) {
        $options_profile = NewsletterSubscription::instance()->options_lists;
        $lists = array();
        if ($empty_label) {
            $lists[''] = $empty_label;
        }
        for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) {
            $lists['' . $i] = '(' . $i . ') ' . $options_profile['list_' . $i];
        }
        return $lists;
    }

    function date($name) {
        $this->hidden($name);
        $year = date('Y', $this->data[$name]);
        $day = date('j', $this->data[$name]);
        $month = date('m', $this->data[$name]);
        $onchange = "this.form.elements['options[" . esc_attr($name) . "]'].value = new Date(document.getElementById('" . esc_attr($name) . "_year').value, document.getElementById('" . esc_attr($name) . "_month').value, document.getElementById('" . esc_attr($name) . "_day').value, 12, 0, 0).getTime()/1000";
        echo '<select id="' . $name . '_month" onchange="' . esc_attr($onchange) . '">';
        for ($i = 0; $i < 12; $i++) {
            echo '<option value="' . $i . '"';
            if ($month - 1 == $i) {
                echo ' selected';
            }
            echo '>' . date('F', mktime(0, 0, 0, $i + 1, 1, 2000)) . '</option>';
        }
        echo '</select>';

        echo '<select id="' . esc_attr($name) . '_day" onchange="' . esc_attr($onchange) . '">';
        for ($i = 1; $i <= 31; $i++) {
            echo '<option value="' . $i . '"';
            if ($day == $i) {
                echo ' selected';
            }
            echo '>' . $i . '</option>';
        }
        echo '</select>';

        echo '<select id="' . esc_attr($name) . '_year" onchange="' . esc_attr($onchange) . '">';
        for ($i = 2011; $i <= 2021; $i++) {
            echo '<option value="' . $i . '"';
            if ($year == $i) {
                echo ' selected';
            }
            echo '>' . $i . '</option>';
        }
        echo '</select>';
    }

    /**
     * Date and time (hour) selector. Timestamp stored.
     */
    function datetime($name) {
        echo '<input type="hidden" name="fields[' . esc_attr($name) . ']" value="datetime">';
        $time = $this->data[$name] + get_option('gmt_offset') * 3600;
        $year = gmdate('Y', $time);
        $day = gmdate('j', $time);
        $month = gmdate('m', $time);
        $hour = gmdate('H', $time);

        echo '<select name="' . esc_attr($name) . '_month">';
        for ($i = 1; $i <= 12; $i++) {
            echo '<option value="' . $i . '"';
            if ($month == $i) {
                echo ' selected';
            }
            echo '>' . date('F', mktime(0, 0, 0, $i, 1, 2000)) . '</option>';
        }
        echo '</select>';

        echo '<select name="' . esc_attr($name) . '_day">';
        for ($i = 1; $i <= 31; $i++) {
            echo '<option value="' . $i . '"';
            if ($day == $i) {
                echo ' selected';
            }
            echo '>' . $i . '</option>';
        }
        echo '</select>';

        echo '<select name="' . esc_attr($name) . '_year">';
        for ($i = 2011; $i <= 2021; $i++) {
            echo '<option value="' . $i . '"';
            if ($year == $i) {
                echo ' selected';
            }
            echo '>' . $i . '</option>';
        }
        echo '</select>';

        echo '<select name="' . esc_attr($name) . '_hour">';
        for ($i = 0; $i <= 23; $i++) {
            echo '<option value="' . $i . '"';
            if ($hour == $i) {
                echo ' selected';
            }
            echo '>' . $i . ':00</option>';
        }
        echo '</select>';
    }

    function hours($name) {
        $hours = array();
        for ($i = 0; $i < 24; $i++) {
            $hours['' . $i] = sprintf('%02d', $i) . ':00';
        }
        $this->select($name, $hours);
    }

    function days($name) {
        $days = array(0 => 'Every day', 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday');
        $this->select($name, $days);
    }

    function init($options = array()) {
        $cookie_name = 'newsletter_tab';
        if (isset($options['cookie_name'])) {
            $cookie_name = $options['cookie_name'];
        }
        echo '<script type="text/javascript">
    jQuery(document).ready(function(){
    jQuery(".tnp-controls-color").wpColorPicker();
        jQuery("textarea.dynamic").focus(function() {
            jQuery("textarea.dynamic").css("height", "50px");
            jQuery(this).css("height", "400px");
        });
      tabs = jQuery("#tabs").tabs({
        active : jQuery.cookie("' . $cookie_name . '"),
        activate : function( event, ui ){
            jQuery.cookie("' . $cookie_name . '", ui.newTab.index(),{expires: 1});
        }
      });
    });
    function newsletter_media(name) {
        var tnp_uploader = wp.media({
            title: "Select an image",
            button: {
                text: "Select"
            },
            multiple: false
        }).on("select", function() {
            var media = tnp_uploader.state().get("selection").first();
            document.getElementById(name + "_id").value = media.id;
            //alert(media.attributes.url);
            if (media.attributes.url.substring(0, 0) == "/") {
                media.attributes.url = "' . site_url('/') . '" + media.attributes.url;
            }
            document.getElementById(name + "_url").value = media.attributes.url;
            
            var img_url = media.attributes.url;
            if (typeof media.attributes.sizes.medium !== "undefined") img_url = media.attributes.sizes.medium.url;
            if (img_url.substring(0, 0) == "/") {
                img_url = "' . site_url('/') . '" + img_url;
            }
            document.getElementById(name + "_img").src = img_url;
        }).open();
    }
    function newsletter_media_remove(name) {
        if (confirm("Are you sure?")) {
            document.getElementById(name + "_id").value = "";
            document.getElementById(name + "_url").value = "";
            document.getElementById(name + "_img").src = "' . plugins_url('newsletter') . '/images/nomedia.png";
        }
    }
    function newsletter_textarea_preview(id, header, footer) {
        var d = document.getElementById(id + "-iframe").contentWindow.document;
        d.open();
        if (templateEditor) {
            d.write(templateEditor.getValue());
        } else {
            d.write(header + document.getElementById(id).value + footer);
        }
        d.close();
        
        var d = document.getElementById(id + "-iframe-phone").contentWindow.document;
        d.open();
        if (templateEditor) {
            d.write(templateEditor.getValue());
        } else {
            d.write(header + document.getElementById(id).value + footer);
        }
        d.close();
        //jQuery("#" + id + "-iframe-phone").toggle();
        jQuery("#" + id + "-preview").toggle();
    }
    function tnp_select_images(state) {
        if (!state.id) { return state.text; }
        var $state = jQuery("<span class=\"tnp-select2-option\"><img style=\"height: 20px!important; position: relative; top: 5px\" src=\"" + state.element.getAttribute("image") + "\"> " + state.text + "</span>");
        return $state;
    }
    function tnp_select_images_selection(state) {
        if (!state.id) { return state.text; }
        var $state = jQuery("<span class=\"tnp-select2-option\"><img style=\"height: 20px!important; position: relative; top: 5px\" src=\"" + state.element.getAttribute("image") + "\"> " + state.text + "</span>");
        return $state;
    }
</script>
';
        echo '<input name="act" type="hidden" value=""/>';
        echo '<input name="btn" type="hidden" value=""/>';
        wp_nonce_field('save');
    }

    function log_level($name = 'log_level') {
        $this->select($name, array(0 => 'None', 2 => 'Error', 3 => 'Normal', 4 => 'Debug'));
    }

    function update_option($name, $data = null) {
        if ($data == null) {
            $data = $this->data;
        }
        update_option($name, $data);
        if (isset($data['log_level'])) {
            update_option($name . '_log_level', $data['log_level']);
        }
    }

    function js_redirect($url) {
        echo '<script>';
        echo 'location.href="' . esc_js($url) . '"';
        echo '</script>';
    }

    /**
     * @deprecated
     */
    function get_test_subscribers() {
        return NewsletterUsers::instance()->get_test_users();
    }

    function css_font_size($name = 'font_size') {
        $value = $this->get_value($name);

        echo '<select id="options-' . esc_attr($name) . '" name="options[' . esc_attr($name) . ']">';
        for ($i = 8; $i < 50; $i++) {
            echo '<option value="' . $i . '"';
            if ($value == $i) {
                echo ' selected';
            }
            echo '>' . $i . '</option>';
        }
        echo '</select>&nbsp;px';
    }

    function css_font_family($name = 'font_family') {
        $value = $this->get_value($name);

        $fonts = array('Helvetica, Arial, sans-serif', 'Arial Black, Gadget, sans-serif', 'Garamond, serif', 'Courier, monospace', 'Cominc Sans MS, cursive', 'Impact, Charcoal, sans-serif',
            'Tahoma, Geneva, sans-serif', 'Times New Roman, Times, serif', 'Verdana, Geneva, sans-serif');

        echo '<select id="options-' . esc_attr($name) . '" name="options[' . esc_attr($name) . ']">';
        foreach ($fonts as $font) {
            echo '<option value="', esc_attr($font), '"';
            if ($value == $font) {
                echo ' selected';
            }
            echo '>', esc_html($font), '</option>';
        }
        echo '</select>';
    }

    function css_text_align($name) {
        $options = array('left' => __('Left', 'newsletter'), 'right' => __('Right', 'newsletter'),
            'center' => __('Center', 'newsletter'));
        $this->select($name, $options);
    }

    function css_border($name) {
        $value = $this->get_value($name . '_width');

        echo 'width&nbsp;<select id="options-' . esc_attr($name) . '-width" name="options[' . esc_attr($name) . '_width]">';
        for ($i = 0; $i < 10; $i++) {
            echo '<option value="' . $i . '"';
            if ($value == $i) {
                echo ' selected';
            }
            echo '>' . $i . '</option>';
        }
        echo '</select>&nbsp;px&nbsp;&nbsp;';

        $this->select($name . '_type', array('solid' => 'Solid', 'dashed' => 'Dashed'));

        $this->color($name . '_color');

        $value = $this->get_value($name . '_radius');

        echo '&nbsp;&nbsp;radius&nbsp;<select id="options-' . esc_attr($name) . '-radius" name="options[' . esc_attr($name) . '_radius]">';
        for ($i = 0; $i < 10; $i++) {
            echo '<option value="' . $i . '"';
            if ($value == $i) {
                echo ' selected';
            }
            echo '>' . $i . '</option>';
        }
        echo '</select>&nbsp;px';
    }

    /**
     * Media selector using the media library of WP. Produces a field which values is an array containing 'id' and 'url'.
     * 
     * @param string $name
     */
    function media($name) {
        if (isset($this->data[$name])) {
            $media_id = (int) $this->data[$name]['id'];
            $media = wp_get_attachment_image_src($media_id, 'medium');
            $media_full = wp_get_attachment_image_src($media_id, 'full');
        } else {
            $media = false;
        }
        echo '<div style="position: relative">';
        echo '<a style="position: absolute; top: 5px; right: 5px; background-color: #fff; color: #000; padding: 0px 5px 6px 5px; font-size: 24px; display: block; text-decoration: none" href="#" onclick="newsletter_media_remove(\'' . esc_attr($name) . '\'); return false">&times;</a>';
        if ($media === false) {
            $media = array('', '', '');
            $media_full = array('', '', '');
            $media_id = 0;
            echo '<img style="max-width: 200px; max-height: 200px;" id="' . esc_attr($name) . '_img" src="' . plugins_url('newsletter') . '/images/nomedia.png" onclick="newsletter_media(\'' . esc_attr($name) . '\')">';
        } else {
            echo '<img style="max-width: 200px; max-height: 200px;" id="' . esc_attr($name) . '_img" src="' . esc_attr($media[0]) . '" onclick="newsletter_media(\'' . esc_attr($name) . '\')">';
        }

        echo '</div>';
        echo '<input type="hidden" id="' . esc_attr($name) . '_id" name="options[' . esc_attr($name) . '][id]" value="' . esc_attr($media_id) . '" size="5">';
        echo '<input type="hidden" id="' . esc_attr($name) . '_url" name="options[' . esc_attr($name) . '][url]" value="' . esc_attr($media_full[0]) . '" size="50">';
    }

    function media_input($option, $name, $label) {

        if (!empty($label)) {
            $output = '<label class="select" for="tnp_' . esc_attr($name) . '">' . esc_html($label) . ':</label>';
        }
        $output .= '<input id="tnp_' . esc_attr($name) . '" type="text" size="36" name="' . esc_attr($option) . '[' . esc_attr($name) . ']" value="' . esc_attr($val) . '" />';
        $output .= '<input id="tnp_' . esc_attr($name) . '_button" class="button-primary" type="button" value="Select Image" />';
        $output .= '<br class="clear"/>';

        echo $output;
    }

    function language($name = 'language') {
        if (!class_exists('SitePress')) {
            echo __('Install WPML for multilangue support', 'newsletter');
            return;
        }

        $languages = apply_filters('wpml_active_languages', null);
        $language_options = array('' => 'All');
        foreach ($languages as $language) {
            $language_options[$language['language_code']] = $language['translated_name'];
        }


        $this->select($name, $language_options);
    }

    function is_multilanguage() {
        return Newsletter::instance()->is_multilanguage();
    }

    /**
     * Creates a checkbox group with all active languages. Each checkbox is named
     * $name[] and values with the relative language code.
     * 
     * @param string $name
     */
    function languages($name = 'languages') {
        if (!$this->is_multilanguage()) {
            echo __('Install WPML or Polylang for multilangue support', 'newsletter');
            return;
        }

        $language_options = Newsletter::instance()->get_languages();

        if (empty($language_options)) {
            echo __('Your multilangiage plugin is not supported or there are no languages defined', 'newsletter');
            return;
        }

        $this->checkboxes_group($name, $language_options);
    }

    /**
     * Prints a formatted date using the formats and timezone of WP, including the current date and time and the
     * time left to the passed time.
     * 
     * @param int $time
     * @param int $now
     * @param bool $left
     * @return string
     */
    static function print_date($time = null, $now = false, $left = false) {
        if (is_null($time)) {
            $time = time();
        }
        if ($time == false) {
            $buffer = 'none';
        } else {
            $buffer = gmdate(get_option('date_format') . ' ' . get_option('time_format'), $time + get_option('gmt_offset') * 3600);
        }
        if ($now) {
            $buffer .= ' (now: ' . gmdate(get_option('date_format') . ' ' .
                            get_option('time_format'), time() + get_option('gmt_offset') * 3600);
            $buffer .= ')';
        }
        if ($left) {
            if ($time - time() < 0) {
                $buffer .= ', ' . (time() - $time) . ' seconds late';
            } else {
                $buffer .= ', ' . gmdate('H:i:s', $time - time()) . ' left';
            }
        }
        return $buffer;
    }

    /**
     * Prints the help button near a form field. The label is used as icon title.
     * 
     * @param string $url
     * @param string $label
     */
    static function help($url, $label = '') {
        echo '<a href="', $url, '" target="_blank" title="', esc_attr($label), '"><i class="fa fa-question-circle-o"></i></a>';
    }

    static function idea($url, $label = '') {
        echo '<a href="', $url, '" target="_blank" title="', esc_attr($label), '"><i class="fa fa-lightbulb-o"></i></a>';
    }

    static function field_help($url, $text = '') {
        if (empty($text))
            $text = __('Read more', 'newsletter');
        echo '<i class="fa fa-question-circle"></i>&nbsp;<a href="', $url, '" target="_blank">', $text, '</a>';
    }

    /**
     * Prints a panel link to the documentation.
     * 
     * @param type $url
     * @param type $text
     */
    static function panel_help($url, $text = '') {
        if (empty($text))
            $text = __('Need help?', 'newsletter');
        echo '<span class="tnp-panel-help"><a href="', $url, '" target="_blank">', $text, '</a></span>';
    }

    /**
     * Prints an administration page link to the documentation (just under the administration page title.
     * @param type $url
     * @param type $text
     */
    static function page_help($url, $text = '') {
        if (empty($text))
            $text = __('Need help?', 'newsletter');
        echo '<div class="tnp-page-help"><a href="', $url, '" target="_blank">', $text, '</a></div>';
    }

    static function print_truncated($text, $size = 50) {
        if (mb_strlen($text) < $size)
            return esc_html($text);
        $sub = mb_substr($text, 0, $size);
        echo '<span title="', esc_attr($text), '">', esc_html($sub), '...</span>';
    }

    function block_background($name = 'block_background') {
        $this->color($name);
    }

    function block_padding($name = 'block_padding') {
        $this->text($name . '_top', 5);
        echo 'px (top)<br>';
        $this->text($name . '_right', 5);
        echo 'px (right)<br>';
        $this->text($name . '_bottom', 5);
        echo 'px (bottom)<br>';
        $this->text($name . '_left', 5);
        echo 'px (left)<br>';
    }

}
