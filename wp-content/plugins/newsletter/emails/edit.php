<?php
/* @var $wpdb wpdb */
require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();
$module = NewsletterEmails::instance();

// Always required
$email = $module->get_email($_GET['id'], ARRAY_A);
if (empty($email['query'])) {
    $email['query'] = "select * from " . NEWSLETTER_USERS_TABLE . " where status='C'";
}

if (empty($email)) {
    echo 'Wrong email identifier';
    return;
}

$email_id = $email['id'];

$composer = isset($email['options']['composer']);

if ($composer) {
    wp_enqueue_style('tnpc-style', plugins_url('/tnp-composer/_css/newsletter-builder.css', __FILE__));
}

if (!$controls->is_action()) {
// Preferences conversions
    if (!isset($email['options']['lists'])) {

        $options_profile = get_option('newsletter_profile');

        if (empty($controls->data['preferences_status_operator'])) {
            $email['options']['lists_operator'] = 'or';
        } else {
            $email['options']['lists_operator'] = 'and';
        }
        $controls->data['options_lists'] = array();
        $controls->data['options_lists_exclude'] = array();

        if (!empty($email['preferences'])) {
            $preferences = explode(',', $email['preferences']);
            $value = empty($email['options']['preferences_status']) ? 'on' : 'off';

            foreach ($preferences as $x) {
                if ($value == 'on') {
                    $controls->data['options_lists'][] = $x;
                } else {
                    $controls->data['options_lists_exclude'][] = $x;
                }
            }
        }
    }
}

if (!$controls->is_action()) {
    $controls->data = $email;

    foreach ($email['options'] as $name => $value) {
        $controls->data['options_' . $name] = $value;
    }
}

if ($controls->is_action('change-private')) {
    $data = array();
    $data['private'] = $controls->data['private'] ? 0 : 1;
    $data['id'] = $email['id'];
    $email = Newsletter::instance()->save_email($data, ARRAY_A);
    $controls->add_message_saved();

    $controls->data = $email;
    foreach ($email['options'] as $name => $value) {
        $controls->data['options_' . $name] = $value;
    }
}

if ($controls->is_action('test') || $controls->is_action('save') || $controls->is_action('send') || $controls->is_action('editor')) {


    // If we were editing with visual editor (==0), we must read the extra <body> content
    if (!empty($controls->data['message'])) {
        $controls->data['message'] = str_ireplace('<script', '<noscript', $controls->data['message']);
        $controls->data['message'] = str_ireplace('</script', '</noscript', $controls->data['message']);
    }

    if ($email['editor'] == 0) {
        if (!empty($controls->data['message'])) {
            $x = strpos($email['message'], '<body');
            if ($x !== false) {
                $x = strpos($email['message'], '>', $x);
                $email['message'] = substr($email['message'], 0, $x + 1) . $controls->data['message'] . '</body></html>';
            } else {
                $email['message'] = '<html><body>' . $controls->data['message'] . '</body></html>';
            }
        }
    } else {
        $email['message'] = $controls->data['message'];
    }
    $email['message_text'] = str_ireplace('<script', '', $controls->data['message_text']);
    $email['subject'] = $controls->data['subject'];
    $email['track'] = $controls->data['track'];
    $email['private'] = $controls->data['private'];

    // Reset the options
    $email['options'] = array();
    if ($composer)
        $email['options']['composer'] = true;

    foreach ($controls->data as $name => $value) {
        if (strpos($name, 'options_') === 0) {
            $email['options'][substr($name, 8)] = $value;
        }
    }

    //var_dump($email);
    // Before send, we build the query to extract subscriber, so the delivery engine does not
    // have to worry about the email parameters
    if ($email['options']['status'] == 'S') {
        $query = "select * from " . NEWSLETTER_USERS_TABLE . " where status='S'";
    } else {
        $query = "select * from " . NEWSLETTER_USERS_TABLE . " where status='C'";
    }

    if ($email['options']['wp_users'] == '1') {
        $query .= " and wp_user_id<>0";
    }

    $list_where = array();
    if (isset($email['options']['lists']) && count($email['options']['lists'])) {
        foreach ($email['options']['lists'] as $list) {
            $list = (int) $list;
            $list_where[] = 'list_' . $list . '=1';
        }
    }

    if (!empty($list_where)) {
        if (isset($email['options']['lists_operator']) && $email['options']['lists_operator'] == 'and') {
            $query .= ' and (' . implode(' and ', $list_where) . ')';
        } else {
            $query .= ' and (' . implode(' or ', $list_where) . ')';
        }
    }

    $list_where = array();
    if (isset($email['options']['lists_exclude']) && count($email['options']['lists_exclude'])) {
        foreach ($email['options']['lists_exclude'] as $list) {
            $list = (int) $list;
            $list_where[] = 'list_' . $list . '=0';
        }
    }
    if (!empty($list_where)) {
        // Must not be in one of the excluded lists
        $query .= ' and (' . implode(' and ', $list_where) . ')';
    }

    if (isset($email['options']['sex'])) {
        $sex = $email['options']['sex'];
        if (is_array($sex) && count($sex)) {
            $query .= " and sex in (";
            foreach ($sex as $x) {
                $query .= "'" . esc_sql((string) $x) . "', ";
            }
            $query = substr($query, 0, -2);
            $query .= ")";
        }
    }

    $e = Newsletter::instance()->save_email($email);

    $query = apply_filters('newsletter_emails_email_query', $query, $e);

    $email['query'] = $query;
    if ($email['status'] == 'sent') {
        $email['total'] = $email['sent'];
    } else {
        $email['total'] = $wpdb->get_var(str_replace('*', 'count(*)', $query));
    }
    if ($controls->is_action('send') && $controls->data['send_on'] < time()) {
        $controls->data['send_on'] = time();
    }
    $email['send_on'] = $controls->data['send_on'];

    if ($controls->is_action('editor')) {
        $email['editor'] = $email['editor'] == 0 ? 1 : 0;
    }

    // Cleans up of tag
    $email['message'] = NewsletterModule::clean_url_tags($email['message']);

    //$email = apply_filters('newsletter_emails_pre_save', $email);
    //$module->logger->fatal($email);

    $res = Newsletter::instance()->save_email($email);
    if ($res === false) {
        $controls->errors = 'Unable to save. Try to deactivate and reactivate the plugin may be the database is out of sync.';
    }

    $controls->data['message'] = $email['message'];

    $controls->add_message_saved();
}

if ($controls->is_action('send')) {
    // Todo subject check
    if ($email['subject'] == '') {
        $controls->errors = __('A subject is required to send', 'newsletter');
    } else {
        $wpdb->update(NEWSLETTER_EMAILS_TABLE, array('status' => 'sending'), array('id' => $email_id));
        $email['status'] = 'sending';
        $controls->messages .= __('Now sending, see the progress on newsletter list', 'newsletter');
    }
}

if ($controls->is_action('pause')) {
    $wpdb->update(NEWSLETTER_EMAILS_TABLE, array('status' => 'paused'), array('id' => $email_id));
    $email['status'] = 'paused';
}

if ($controls->is_action('continue')) {
    $wpdb->update(NEWSLETTER_EMAILS_TABLE, array('status' => 'sending'), array('id' => $email_id));
    $email['status'] = 'sending';
}

if ($controls->is_action('abort')) {
    $wpdb->query("update " . NEWSLETTER_EMAILS_TABLE . " set last_id=0, sent=0, status='new' where id=" . $email_id);
    $email['status'] = 'new';
    $email['sent'] = 0;
    $email['last_id'] = 0;
    $controls->messages = __('Delivery definitively cancelled', 'newsletter');
}

if ($controls->is_action('test')) {
    if ($email['subject'] == '') {
        $controls->errors = __('A subject is required to send', 'newsletter');
    } else {
        $users = NewsletterUsers::instance()->get_test_users();
        if (count($users) == 0) {
            $controls->messages = '<strong>' . __('There are no test subscribers to send to', 'newsletter') . '</strong>';
        } else {
            Newsletter::instance()->send(Newsletter::instance()->get_email($email_id), $users);
            $controls->messages = __('Test newsletter sent to:', 'newsletter');
            foreach ($users as &$user) {
                $controls->messages .= ' ' . $user->email;
            }
            $controls->messages .= '.';
        }

        $controls->messages .= '<br>';
        $controls->messages .= '<a href="https://www.thenewsletterplugin.com/plugins/newsletter/subscribers-module#test" target="_blank">' .
                __('Read more about test subscribers', 'newsletter') . '</a>.';
    }
}

if ($email['editor'] == 0) {
    $controls->data['message'] = $module->extract_body($controls->data['message']);
}

if (isset($controls->data['options_status']) && $controls->data['options_status'] == 'S') {
    $controls->warnings[] = __('This newsletter will be sent to not confirmed subscribers.', 'newsletter');
}

if (strpos($controls->data['message'], '{profile_url}') === false && strpos($controls->data['message'], '{unsubscription_url}') === false
        && strpos($controls->data['message'], '{unsubscription_confirm_url}') === false) {
    $controls->warnings[] = __('The message is missing the subscriber profile or cancellation link.', 'newsletter');
}

/*
  $host = parse_url(home_url(), PHP_URL_HOST);
  $parts = array_reverse(explode('.', $host));
  $host = $parts[1] . '.' . $parts[0];

  $re = '/["\'](https?:\/\/[^\/\s]+\/\S+\.(jpg|png|gif))["\']/i';
  preg_match_all($re, $controls->data['message'], $matches);
  $images = array();
  if (isset($matches[1])) {
  //echo 'Ci sono immagini';
  //var_dump($matches[1]);
  foreach ($matches[1] as $url) {
  $h = parse_url($url, PHP_URL_HOST);
  $p = array_reverse(explode('.', $h));
  $h = $p[1] . '.' . $p[0];
  if ($h == $host)
  continue;
  $images[] = $url;
  }
  }

  if ($images) {
  //$controls->warnings[] = __('Message body contains images from external domains.', 'newsletter') . ' <a href="">' . __('Read more', 'newsletter') . '</a>';
  }
 */
/*
  if ($images) {
  $upload = wp_upload_dir();
  $dir = $upload['basedir'] . '/newsletter/' . $email['id'];
  $baseurl = $upload['baseurl'] . '/newsletter/' . $email['id'];

  // Cannot work on systems with forced relative paths
  if (strpos($baseurl, 'http') === 0) {
  wp_mkdir_p($dir);
  foreach ($images as $url) {
  $file = basename(parse_url($url, PHP_URL_PATH));
  $file = sanitize_file_name($file);
  if (copy($url, $dir . '/' . $file)) {
  $controls->data['message'] = str_replace($url, $baseurl . '/' . $file, $controls->data['message']);
  }
  }
  }
  }
 */
?>

<div class="wrap tnp-emails tnp-emails-edit" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">

        <h2><?php _e('Edit Newsletter', 'newsletter') ?></h2>

    </div>

    <div id="tnp-body">



        <form method="post" action="" id="newsletter-form">
            <?php $controls->init(array('cookie_name' => 'newsletter_emails_edit_tab')); ?>

            <div class="tnp-submit">

                <?php $controls->button_back('?page=newsletter_emails_index') ?>
                <?php if ($email['status'] != 'sending' && $email['status'] != 'sent') $controls->button_save(); ?>
                <?php if ($email['status'] != 'sending' && $email['status'] != 'sent') $controls->button('test', __('Test', 'newsletter')); ?>

                <?php if ($email['status'] == 'new') $controls->button_confirm('send', __('Send', 'newsletter'), __('Start real delivery?', 'newsletter')); ?>
                <?php if ($email['status'] == 'sending') $controls->button_confirm('pause', __('Pause', 'newsletter'), __('Pause the delivery?', 'newsletter')); ?>
                <?php if ($email['status'] == 'paused') $controls->button_confirm('continue', __('Continue', 'newsletter'), 'Continue the delivery?'); ?>
                <?php if ($email['status'] == 'paused') $controls->button_confirm('abort', __('Stop', 'newsletter'), __('This totally stop the delivery, ok?', 'newsletter')); ?>
                <?php if ($email['status'] != 'sending' && $email['status'] != 'sent') $controls->button('editor', __('Switch editor')); ?>
                <?php //if ($images) $controls->button_confirm('import', __('Import images', 'newsletter'), 'Proceed?')  ?>
            </div>

            <?php $controls->text('subject', 70, 'Subject'); ?> 
            
            <a href="#" class="tnp-suggest-button" onclick="tnp_suggest_subject(); return false;"><?php _e('Get ideas', 'newsletter') ?></a></a>
            <!--
            <a href="#" class="tnp-suggest-button" onclick="tnp_emoji(); return false;"><?php _e('Insert emoji', 'newsletter') ?></a>
            -->

            <div id="tabs">
                <ul>
                    <li><a href="#tabs-a"><?php _e('Message', 'newsletter') ?></a></li>
                    <li><a href="#tabs-b"><?php _e('Message (textual)', 'newsletter') ?></a></li>
                    <li><a href="#tabs-c"><?php _e('Targeting', 'newsletter') ?></a></li>
                    <li><a href="#tabs-d"><?php _e('Other', 'newsletter') ?></a></li>
                    <li><a href="#tabs-status"><?php _e('Status', 'newsletter') ?></a></li>
                </ul>


                <div id="tabs-a">

                    <?php
                    if ($email['editor'] == 0) {
                        if ($composer) {
                            include __DIR__ . '/edit-composer.php';
                        } else {
                            include __DIR__ . '/edit-editor.php';
                        }
                    } else {
                        include __DIR__ . '/edit-html.php';
                    }
                    ?>

                </div>


                <div id="tabs-b">
                    <?php if (Newsletter::instance()->options['phpmailer'] == 0) { ?>
                        <p class="tnp-tab-warning">The text part is sent only when Newsletter manages directly the sending process. <a href="admin.php?page=newsletter_main_main" target="_blank">See the main settings</a>.</p>
                    <?php } ?>
                    <p>
                        This is the textual version of your newsletter. If you empty it, only an HTML version will be sent but
                        is an anti-spam best practice to include a text only version.
                    </p>

                    <?php $controls->textarea_fixed('message_text', '100%', '500'); ?>
                </div>


                <div id="tabs-c" class="tnp-list-conditions">
                    <p>
                        <?php $controls->panel_help('https://www.thenewsletterplugin.com/documentation/newsletter-targeting') ?>
                    </p>

                    <p>
                        <?php _e('Leaving all multichoice options unselected is like to select all them', 'newsletter'); ?>
                    </p>
                    <table class="form-table">
                        <tr>
                            <th><?php _e('Lists', 'newsletter') ?></th>
                            <td>
                                <?php
                                $lists = $controls->get_list_options();
                                ?>
                                <?php $controls->select('options_lists_operator', array('or' => __('Match at least one of', 'newsletter'), 'and' => __('Match all of', 'newsletter'))); ?>

                                <?php $controls->select2('options_lists', $lists, null, true, null, __('All', 'newsletter')); ?>

                                <p><?php _e('must not in one of', 'newsletter') ?></p>

                                <?php $controls->select2('options_lists_exclude', $lists, null, true, null, __('None', 'newsletter')); ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Gender', 'newsletter') ?></th>
                            <td>
                                <?php $controls->checkboxes_group('options_sex', array('f' => 'Women', 'm' => 'Men', 'n' => 'Not specified')); ?>
                            </td>
                        </tr>


                        <tr>
                            <th><?php _e('Status', 'newsletter') ?></th>
                            <td>
                                <?php $controls->select('options_status', array('C' => __('Confirmed', 'newsletter'), 'S' => __('Not confirmed', 'newsletter'))); ?>

                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Only to subscribers linked to WP users', 'newsletter') ?></th>
                            <td>
                                <?php $controls->yesno('options_wp_users'); ?>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <?php _e('Approximated subscribers count', 'newsletter') ?>
                            </th>
                            <td>
                                <?php
                                if ($email['status'] != 'sent') {
                                    echo $wpdb->get_var(str_replace('*', 'count(*)', $email['query']));
                                } else {
                                    echo $email['sent'];
                                }
                                ?>
                                <p class="description">
                                    <?php _e('Save to update if on targeting filters have been changed', 'newsletter') ?>
                                </p>
                            </td>
                        </tr>
                    </table>

                    <?php do_action('newsletter_emails_edit_target', $module->get_email($email_id), $controls) ?>
                </div>


                <div id="tabs-d">
                    <table class="form-table">
                        <tr>
                            <th><?php _e('Keep private', 'newsletter') ?></th>
                            <td>
                                <?php $controls->yesno('private'); ?>
                                <?php if ($email['status'] == 'sent') { ?>
                                    <?php $controls->button('change-private', __('Toggle')) ?>
                                <?php } ?>
                                <p class="description">
                                    <?php _e('Hide/show from public sent newsletter list.', 'newsletter') ?>
                                    <?php _e('Required', 'newsletter') ?>: <a href="" target="_blank">Newsletter Archive Extension</a>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Track clicks and message opening', 'newsletter') ?></th>
                            <td>
                                <?php $controls->yesno('track'); ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Send on', 'newsletter') ?></th>
                            <td>
                                <?php $controls->datetime('send_on'); ?> (now: <?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format')); ?>)
                            </td>
                        </tr>
                    </table>

                    <?php do_action('newsletter_emails_edit_other', $module->get_email($email_id), $controls) ?>
                </div>

                <div id="tabs-status">
                    <table class="form-table">
                        <tr>
                            <th>Email status</th>
                            <td><?php echo esc_html($email['status']); ?></td>
                        </tr>
                        <tr>
                            <th>Messages sent</th>
                            <td><?php echo $email['sent']; ?> of <?php echo $email['total']; ?></td>
                        </tr>
                        <tr>
                            <th>Query (tech)</th>
                            <td><?php echo esc_html($email['query']); ?></td>
                        </tr>
                        <tr>
                            <th>Token (tech)</th>
                            <td><?php echo esc_html($email['token']); ?></td>
                        </tr>
                    </table>
                </div>
            </div>

        </form>
    </div>

    <script>
        function tnp_suggest_subject() {
            jQuery("#tnp-edit-subjects").show();
        }

        function tnp_emoji() {
            jQuery("#tnp-edit-emoji").show();
        }

        jQuery(function () {
            jQuery("#tnp-edit-subjects-list a").click(function (e) {
                e.preventDefault();
                document.getElementById("options-subject").value = this.innerText;
                jQuery("#tnp-edit-subjects").hide();
            });

            jQuery("#tnp-edit-emoji-list a").click(function (e) {
                e.preventDefault();
                document.getElementById("options-subject").value = this.title + document.getElementById("options-subject").value;
                jQuery("#tnp-edit-emoji").hide();
            });

            jQuery(".tnp-popup-close").click(function () {
                $(this).parent().parent().hide();

            });
        });
    </script> 
    <style>
/*        .tnp-popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, .8);
            z-index: 10000;
        }

        .tnp-popup {
            width: 40vw;
            height: 66vh;
            overflow: auto;
            margin: 100px auto 0 auto;
            background-color: #181818;
            padding: 20px;
            position: relative;
        }
        .tnp-popup-close {
            display: block;
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: #181818;
            color: #fff;
            font-size: 40px;
            padding: 10px;
            text-align: right;
            cursor: pointer;
        }
        
        .tnp-subjects-header {
            font-size: 16px;
            color: #fff;
            padding: 0px 70px 20px 20px;
            font-family: "Montserrat", sans-serif;
            border-bottom: 1px solid #282828;
        }
        
        #tnp-edit-subjects-list {
            padding: 0px 70px 20px 20px;
        }
        
        #tnp-edit-subjects-list a {
            padding: 5px;
        }
        
        #tnp-edit-subjects-list svg {
            margin: 0px 10px 0px 0px;
            vertical-align: middle;
        }
        
        .tnp-subject-category {
            color: #565656;
            margin: 25px 0px 10px 0px;
             font-family: "Montserrat"; 
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }
        
        
        #tnp-edit-emoji-list {
            font-size: 28px;
        }
        #tnp-edit-emoji-list a {
            display: inline-block;
            margin-right: 5px;
            margin-bottom: 5px;  
        }*/
    </style>
    <div id="tnp-edit-subjects" class="tnp-popup-overlay">
        <div class="tnp-popup">
            <a class="tnp-popup-close">&times;</a>
            <p class="tnp-subjects-header">
                Here are some subject examples you can start from to further engage your subscribers.
            </p>
            <div id="tnp-edit-subjects-list">
                
                <h3 class="tnp-subject-category"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 48 48" xml:space="preserve" width="18" height="18"><g class="nc-icon-wrapper"><path fill="#43A6DD" d="M10,5C9.668,5,9.358,5.165,9.172,5.439C8.986,5.714,8.948,6.063,9.071,6.371 c0.078,0.194,7.669,19.475,0.222,26.922c-0.286,0.286-0.372,0.716-0.217,1.09C9.23,34.757,9.596,35,10,35h28c0.553,0,1-0.447,1-1 C39,18.01,25.99,5,10,5z"></path> <path fill="#BADEFC" d="M46,43c0.552,0,1-0.448,1-1V30.544c-0.646,0.29-1.257,0.684-1.787,1.214c-2.343,2.343-6.142,2.343-8.485,0 c-2.343-2.343-6.142-2.343-8.485,0c-2.343,2.343-6.142,2.343-8.485,0s-6.142-2.343-8.485,0s-6.142,2.343-8.485,0 c-0.53-0.53-1.141-0.924-1.787-1.214V42c0,0.552,0.448,1,1,1H46z"></path></g></svg>Dangers</h3>
                <a href="#"><?php _e('How safe is your <em>[something]</em> from <em>[danger]</em>?', 'newsletter') ?></a><br>
                <a href="#"><?php _e('10 Warning Signs That <em>[something]</em>', 'newsletter') ?></a><br>
                <a href="#"><?php _e('10 Lies <em>[kind of people]</em> Likes to Tell', 'newsletter') ?></a><br>

                <h3 class="tnp-subject-category"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 48 48" xml:space="preserve" width="18" height="18"><g class="nc-icon-wrapper"><path fill="#7C5839" d="M38.75586,31.34473C38.56543,31.12598,38.29004,31,38,31H10c-0.29004,0-0.56543,0.12598-0.75586,0.34473 c-0.18945,0.21924-0.27539,0.50977-0.23438,0.79688l2,14C11.08008,46.63428,11.50195,47,12,47h24 c0.49805,0,0.91992-0.36572,0.99023-0.8584l2-14C39.03125,31.85449,38.94531,31.56396,38.75586,31.34473z"></path> <path fill="#72C472" d="M34,6c-3.96655,0-7.38348,2.31537-9,5.66302V2c0-0.55225-0.44727-1-1-1s-1,0.44775-1,1v26 c0,0.55225,0.44727,1,1,1s1-0.44775,1-1v-8h1c5.52283,0,10-4.47717,10-10V6H34z"></path> <path fill="#A67C52" d="M42,33H6c-0.55273,0-1-0.44775-1-1v-4c0-0.55225,0.44727-1,1-1h36c0.55273,0,1,0.44775,1,1v4 C43,32.55225,42.55273,33,42,33z"></path></g></svg>Better life, problem management</h3>
                <a href="#"><?php _e('10 Ways to Simplify Your <em>[something]</em>', 'newsletter') ?></a><br>
                <a href="#"><?php _e('Get Rid of <em>[problem]</em> Once and Forever', 'newsletter') ?></a><br>
                <a href="#"><?php _e('How to End [problem]', 'newsletter') ?></a><br>
                <a href="#"><?php _e('Secrets of [famous people]', 'newsletter') ?></a><br>

                <h3 class="tnp-subject-category"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 48 48" xml:space="preserve" width="18" height="18"><g class="nc-icon-wrapper"><path fill="#444444" d="M8,27H2c-0.553,0-1-0.447-1-1s0.447-1,1-1h6c0.553,0,1,0.447,1,1S8.553,27,8,27z"></path> <path fill="#444444" d="M12.185,15.19c-0.256,0-0.513-0.098-0.708-0.293l-4.185-4.19c-0.39-0.392-0.39-1.024,0.001-1.415 c0.391-0.389,1.024-0.39,1.415,0.001l4.185,4.19c0.39,0.392,0.39,1.024-0.001,1.415C12.696,15.093,12.44,15.19,12.185,15.19z"></path> <path fill="#444444" d="M35.815,15.19c-0.256,0-0.512-0.098-0.707-0.292c-0.391-0.391-0.391-1.023-0.001-1.415l4.185-4.19 c0.391-0.391,1.024-0.39,1.415-0.001c0.391,0.391,0.391,1.023,0.001,1.415l-4.185,4.19C36.328,15.093,36.071,15.19,35.815,15.19z"></path> <path fill="#444444" d="M8,45c-0.256,0-0.513-0.098-0.708-0.293c-0.39-0.392-0.39-1.024,0.001-1.415l4.189-4.184 c0.391-0.389,1.024-0.39,1.415,0.001c0.39,0.392,0.39,1.024-0.001,1.415l-4.189,4.184C8.512,44.902,8.256,45,8,45z"></path> <path fill="#444444" d="M40,45c-0.256,0-0.512-0.098-0.707-0.292l-4.189-4.184c-0.391-0.391-0.391-1.023-0.001-1.415 c0.391-0.391,1.024-0.39,1.415-0.001l4.189,4.184c0.391,0.391,0.391,1.023,0.001,1.415C40.513,44.902,40.256,45,40,45z"></path> <path fill="#444444" d="M46,27h-6c-0.553,0-1-0.447-1-1s0.447-1,1-1h6c0.553,0,1,0.447,1,1S46.553,27,46,27z"></path> <path fill="#E86C60" d="M32.324,9.555c-0.164-0.108-0.355-0.166-0.552-0.166c-0.001,0-0.001,0-0.002,0L16.188,9.413 c-0.196,0-0.389,0.059-0.552,0.167C10.31,13.125,7,19.799,7,27c0,11.028,7.626,20,17,20s17-8.972,17-20 C41,19.777,37.676,13.093,32.324,9.555z"></path> <path fill="#444444" d="M34.707,1.293c-0.391-0.391-1.023-0.391-1.414,0l-3.694,3.694C28.051,3.744,26.1,3,24,3 s-4.051,0.744-5.599,1.987l-3.694-3.694c-0.391-0.391-1.023-0.391-1.414,0s-0.391,1.023,0,1.414l3.689,3.689 c-0.993,1.243-1.669,2.757-1.891,4.426c-0.021,0.156-0.004,0.316,0.049,0.466C15.425,12.096,20.198,15,24,15s8.575-2.904,8.86-3.712 c0.053-0.149,0.069-0.31,0.049-0.466c-0.223-1.669-0.898-3.183-1.891-4.426l3.689-3.689C35.098,2.316,35.098,1.684,34.707,1.293z"></path> <path fill="#444444" d="M24,47c0.338,0,0.667-0.037,1-0.06V14c0-0.553-0.447-1-1-1s-1,0.447-1,1v32.94 C23.333,46.963,23.662,47,24,47z"></path> <circle fill="#444444" cx="15" cy="23" r="3"></circle> <circle fill="#444444" cx="17.5" cy="34.5" r="2.5"></circle> <circle fill="#444444" cx="33" cy="23" r="3"></circle> <circle fill="#444444" cx="30.5" cy="34.5" r="2.5"></circle></g></svg>Mistakes</h3>
                <a href="#"><?php _e('Do You Make These 10 <em>[something]</em> Mistakes?', 'newsletter') ?></a><br>
                <a href="#"><?php _e('10 <em>[something]</em> Mistakes That Make You Look Dumb', 'newsletter') ?></a><br>
                <a href="#"><?php _e('Don\'t do These 10 Things When <em>[something]</em>', 'newsletter') ?></a><br>

                <h3 class="tnp-subject-category"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 48 48" xml:space="preserve" width="18" height="18"><g class="nc-icon-wrapper"><path fill="#5A7A84" d="M6,13c2.75684,0,5-2.24316,5-5S8.75684,3,6,3S1,5.24316,1,8S3.24316,13,6,13z"></path> <path fill="#5A7A84" d="M6,19c-2.75684,0-5,2.24316-5,5s2.24316,5,5,5s5-2.24316,5-5S8.75684,19,6,19z"></path> <path fill="#5A7A84" d="M6,35c-2.75684,0-5,2.24316-5,5s2.24316,5,5,5s5-2.24316,5-5S8.75684,35,6,35z"></path> <path fill="#76B5B5" d="M46,10H16c-0.55229,0-1-0.44771-1-1V7c0-0.55228,0.44771-1,1-1h30c0.55228,0,1,0.44772,1,1v2 C47,9.55229,46.55228,10,46,10z"></path> <path fill="#76B5B5" d="M34,26H16c-0.55229,0-1-0.44772-1-1v-2c0-0.55228,0.44771-1,1-1h18c0.55228,0,1,0.44772,1,1v2 C35,25.55228,34.55228,26,34,26z"></path> <path fill="#76B5B5" d="M46,42H16c-0.55229,0-1-0.44772-1-1v-2c0-0.55228,0.44771-1,1-1h30c0.55228,0,1,0.44772,1,1v2 C47,41.55228,46.55228,42,46,42z"></path></g></svg>Lists (classic)</h3>
                <a href="#"><?php _e('10 Ways to <em>[something]</em>', 'newsletter') ?></a><br>
                <a href="#"><?php _e('The Top 10 <em>[something]</em>', 'newsletter') ?></a><br>
                <a href="#"><?php _e('The 10 Rules for <em>[something]</em>', 'newsletter') ?></a><br>
                <a href="#"><?php _e('Get/Become <em>[something]</em>. 10 Ideas That Work', 'newsletter') ?></a><br>

            </div>
        </div>
    </div>
<?php /*
    <div id="tnp-edit-emoji" class="tnp-popup-overlay">
        <div class="tnp-popup">
            <a class="tnp-popup-close">&times;</a>
            <p>
                Emoji are usually rendered differently on different systems. Don't expect everyone will see what you see.
            </p>
            <div id="tnp-edit-emoji-list">  
                <?php
                $emojiRangesCustom = array(
                    '128512' => '128590',
                    '129296' => '129310',
                    '129312' => '129319',
                    '127744' => '128511',
                    '128640' => '128705',
                    '129296' => '129310',
                    '129312' => '129319',
                    '129360' => '129374',
                    '129408' => '129425'
                );

                foreach ($emojiRangesCustom as $start => $end) {
                    $current = $start;
                    while ($current != $end) {
                        echo '<a href="#" title="&#' . $current . ';">&#' . $current . ';</a> ';
                        $current ++;
                    }
                }
                ?>
            </div>
        </div>
    </div>
  
 */ ?>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

</div>
