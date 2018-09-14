<?php
if (!defined('ABSPATH'))
    exit;


@include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$module = Newsletter::instance();
$controls = new NewsletterControls();
/* @var $wpdb wpdb */


$wp_cron_calls = get_option('newsletter_diagnostic_cron_calls', array());
if (count($wp_cron_calls) > 20) {
    $total = 0;
    $wp_cron_calls_max = 0;
    $wp_cron_calls_min = 0;
    for ($i = 1; $i < count($wp_cron_calls); $i++) {
        $diff = $wp_cron_calls[$i] - $wp_cron_calls[$i - 1];
        $total += $diff;
        if ($wp_cron_calls_min == 0 || $wp_cron_calls_min > $diff) {
            $wp_cron_calls_min = $diff;
        }
        if ($wp_cron_calls_max < $diff) {
            $wp_cron_calls_max = $diff;
        }
    }
    $wp_cron_calls_avg = (int) ($total / (count($wp_cron_calls) - 1));
}

if ($controls->is_action('delete_logs')) {
    $files = glob(WP_CONTENT_DIR . '/logs/newsletter/*.txt');
    foreach ($files as $file) {
        if (is_file($file))
            unlink($file);
    }
    $secret = NewsletterModule::get_token(8);
    update_option('newsletter_logger_secret', $secret);
    $controls->messages = 'Logs deleted';
}

if ($controls->is_action('reschedule')) {
    wp_clear_scheduled_hook('newsletter');
    wp_schedule_event(time() + 30, 'newsletter', 'newsletter');
    $controls->add_message_done();
}

if ($controls->is_action('trigger')) {
    Newsletter::instance()->hook_newsletter();
    $controls->messages = 'Triggered';
}

if ($controls->is_action('test')) {

    if (!NewsletterModule::is_email($controls->data['test_email'])) {
        $controls->errors = 'The test email address is not set or is not correct.';
    }

    if (empty($controls->errors)) {

        $options = $controls->data;

        if ($controls->data['test_email'] == $module->options['sender_email']) {
            $controls->messages .= '<strong>Warning:</strong> you are using as test email the same address configured as sender in main configuration. Test can fail because of that.<br>';
        }

        // Newsletter mail 
        $text = array();
        $text['html'] = '<p>This is an <b>HTML</b> test email sent using the sender data set on Newsletter main setting. <a href="https://www.thenewsletterplugin.com">This is a link to an external site</a>.</p>';
        $text['text'] = 'This is a textual test email part sent using the sender data set on Newsletter main setting.';
        $r = $module->mail($controls->data['test_email'], 'Newsletter test email at ' . date(DATE_ISO8601), $text);

        $controls->messages .= 'Email sent with Newsletter';
        if ($module->the_mailer) {
            $controls->messages .= ' (with a mailer extension)';
        } else if ($module->mail_method) {
            $controls->messages .= ' (with a mail delivery extension)';
        } else {
            $smtp_options = $module->get_smtp_options();

            if (!empty($smtp_options['enabled'])) {
                $controls->messages .= ' (with an SMTP)';
            }
        }
        $controls->messages .= ': ';

        if ($r) {
            $options['mail'] = 1;
            $controls->messages .= '<strong>SUCCESS</strong><br>';
        } else {
            $options['mail'] = 0;
            $options['mail_error'] = $module->mail_last_error;

            $controls->messages .= '<strong>FAILED</strong> (' . $module->mail_last_error . ')<br>';

            if ($module->mail_method) {
                $controls->messages .= '- You are using a mail delivery extension. Check and test its configuration.<br>';
            } else {
                $smtp_options = $module->get_smtp_options();
                if (!empty($smtp_options['enabled'])) {
                    $controls->messages .= '- You are using an SMTP (' . $smtp_options['host'] . '). Check its configuration on main configuration or on SMTP Newsletter extensions if used.<br>';
                }
            }

            if (!empty($module->options['return_path'])) {
                $controls->messages .= '- Try to remove the return path on main settings.<br>';
            }

            $controls->messages .= '<a href="https://www.thenewsletterplugin.com/documentation/email-sending-issues" target="_blank">Read more</a>.';

            $parts = explode('@', $module->options['sender_email']);
            $sitename = strtolower($_SERVER['SERVER_NAME']);
            if (substr($sitename, 0, 4) == 'www.') {
                $sitename = substr($sitename, 4);
            }
            if (strtolower($sitename) != strtolower($parts[1])) {
                $controls->messages .= '- Try to set on main setting a sender address with the same domain of your blog: ' . $sitename . ' (you are using ' . $module->options['sender_email'] . ')<br>';
            }
        }
        $module->save_options($options, 'status');
    }
}

$options = $module->get_options('status');
?>

<div class="wrap tnp-main-status" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">

        <h2><?php _e('System Status', 'newsletter') ?></h2>

    </div>

    <div id="tnp-body">

        <form method="post" action="">
            <?php $controls->init(); ?>

            <h3>General checks</h3>
            <table class="widefat" id="tnp-status-table">

                <thead>
                    <tr>
                        <th>Parameter</th>
                        <th><?php _e('Status', 'newsletter') ?></th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    <tr>
                        <td>Mailing</td>
                        <td>
                            <?php if (empty($options['mail'])) { ?>
                                <span class="tnp-ko">KO</span>
                            <?php } else { ?>
                                <span class="tnp-ok">OK</span>
                            <?php } ?>

                        </td>
                        <td>
                            <?php if (empty($options['mail'])) { ?>
                                <?php if (empty($options['mail_error'])) { ?>
                                    A test has never run.
                                <?php } else { ?>
                                    Last test failed with error "<?php echo esc_html($options['mail_error']) ?>".

                                <?php } ?>
                            <?php } else { ?>
                                Last test was successful. If you didn't receive the test email:
                                <ol>
                                    <li>If you set the Newsletter SMTP, do a test from that panel</li>
                                    <li>If you're using a integration extension do a test from its configuration panel</li>
                                    <li>If previous points do not apply to you, ask for support to your provider reporting the emails from your blog are not delivered</li>
                                </ol>
                            <?php } ?>
                            <br>
                            <a href="https://www.thenewsletterplugin.com/documentation/email-sending-issues" target="_blank">Read more to solve your issues, if any</a>.    
                            <br>
                            Email: <?php $controls->text_email('test_email') ?> <?php $controls->button('test', __('Send a test message')) ?>
                        </td>

                    </tr>

                    <?php
                    $return_path = $module->options['return_path'];
                    if (!empty($return_path)) {
                        list($return_path_local, $return_path_domain) = explode('@', $return_path);
                    }
                    $sender = $module->options['sender_email'];
                    if (!empty($sender)) {
                        list($sender_local, $sender_domain) = explode('@', $sender);
                    }
                    ?>
                    <tr>
                        <td>Return path</td>
                        <td>
                            <?php if (empty($return_path)) { ?>
                                <span class="tnp-ok">OK</span>
                            <?php } else { ?>
                                <?php if ($sender_domain != $return_path_domain) { ?>
                                    <span class="tnp-maybe">MAYBE</span>
                                <?php } else { ?>
                                    <span class="tnp-ok">OK</span>
                                <?php } ?>
                            <?php } ?>

                        </td>
                        <td>
                            <?php if (!empty($return_path)) { ?>
                                Some providers require the return path domain <code><?php echo esc_html($return_path_domain) ?></code> to be identical
                                to the sender domain <code><?php echo esc_html($sender_domain) ?></code>. See the main settings.
                            <?php } else { ?>
                            <?php } ?>
                        </td>

                    </tr>

                    <tr>
                        <td>Blog Charset</td>
                        <td>
                            <?php if (get_option('blog_charset') == 'UTF-8') { ?>
                                <span class="tnp-ok">OK</span>
                            <?php } else { ?>
                                <span class="tnp-ko">KO</span>
                            <?php } ?>
                        </td>
                        <td>
                            Charset: <?php echo esc_html(get_option('blog_charset')) ?>
                            <br>

                            <?php if (get_option('blog_charset') == 'UTF-8') { ?>

                            <?php } else { ?>
                                Your blog charset is <?php echo esc_html(get_option('blog_charset')) ?> but it is recommended to use
                                the <code>UTF-8</code> charset but the <a href="https://codex.wordpress.org/Converting_Database_Character_Sets" target="_blank">conversion</a>
                                could be tricky. If you're not experiencing problem, leave things as is.
                            <?php } ?>
                        </td>
                    </tr>

                    <tr>
                        <td>PHP version</td>
                        <td>
                            <?php if (version_compare(phpversion(), '5.3', '<')) { ?>
                                <span class="tnp-ko">KO</span>
                            <?php } else { ?>
                                <span class="tnp-ok">OK</span>
                            <?php } ?>

                        </td>
                        <td>
                            Your PHP version is <?php echo phpversion() ?><br>
                            <?php if (version_compare(phpversion(), '5.3', '<')) { ?>
                                Newsletter plugin works correctly with PHP version 5.3 or greater. Ask your provider to upgrade your PHP. Your version is
                                unsupported even by the PHP community.
                            <?php } ?>
                        </td>

                    </tr>

                    <tr>
                        <td>Curl version</td>
                        <td>
                            <?php if (!function_exists('curl_version')) { ?>
                                <span class="tnp-ko">KO</span>
                            <?php } else { ?>
                                <span class="tnp-ok">OK</span>
                            <?php } ?>

                        </td>
                        <td>
                            <?php if (!function_exists('curl_version')) { ?>
                                cUrl is not available, ask the provider to install it and activate the PHP cUrl library
                                <?php
                            } else {
                                $version = curl_version();
                                echo 'Version: ' . $version['version'] . '<br>';
                                echo 'SSL Version: ' . $version['ssl_version'] . '<br>';
                            }
                            ?>
                        </td>

                    </tr>


                    <?php
                    $value = (int) ini_get('max_execution_time');
                    $res = true;
                    if ($value != 0 && $value < NEWSLETTER_CRON_INTERVAL) {
                        $res = set_time_limit(NEWSLETTER_CRON_INTERVAL);
                    }
                    ?>

                    <tr>
                        <td>PHP execution time limit</td>
                        <td>
                            <?php if ($res) { ?>
                                <span class="tnp-ok">OK</span>
                            <?php } else { ?>
                                <span class="tnp-ko">KO</span>
                            <?php } ?>

                        </td>
                        <td>
                            <?php if (!$res) { ?>
                                Your PHP execution time limit is <?php echo $value ?> seconds and cannot be changed or 
                                is too lower to grant the maximum delivery rate of Newsletter.    
                            <?php } else { ?>
                                Your PHP execution time limit is <?php echo $value ?> seconds and can be eventually changed by Newsletter<br>
                            <?php } ?>

                        </td>

                    </tr>

                    <tr>
                        <td>Home URL</td>
                        <td>
                            <?php if (strpos(home_url('/'), 'http') !== 0) { ?>
                                <span class="tnp-ko">KO</span>
                            <?php } else { ?>
                                <span class="tnp-ok">OK</span>
                            <?php } ?>

                        </td>
                        <td>
                            Value: <?php echo home_url('/'); ?>
                            <br>
                            <?php if (strpos(home_url('/'), 'http') !== 0) { ?>
                                Your home URL is not absolute, emails require absolute URLs.
                                Probably you have a protocol agnostic plugin installed to manage both HTTPS and HTTP in your
                                blog.
                            <?php } else { ?>

                            <?php } ?>
                        </td>
                    </tr>

                    <tr>
                        <td>WP_CONTENT_URL</td>
                        <td>
                            <?php if (strpos(WP_CONTENT_URL, 'http') !== 0) { ?>
                                <span class="tnp-ko">KO</span>
                            <?php } else { ?>
                                <span class="tnp-ok">OK</span>
                            <?php } ?>

                        </td>
                        <td>
                            Value: <?php echo WP_CONTENT_URL; ?>
                            <br>
                            <?php if (strpos(WP_CONTENT_URL, 'http') !== 0) { ?>
                                Your content URL is not absolute, emails require absolute URLs when they have images inside.
                                Newsletter tries to deal with this problem but when a problem with images persists, you should try to remove
                                from your wp-config.php the WP_CONTENT_URL define and check again.
                            <?php } else { ?>

                            <?php } ?>
                        </td>
                    </tr>

                    <tr>
                        <td>Database Charset</td>
                        <td>
                            <?php if (DB_CHARSET != 'utf8' && DB_CHARSET != 'utf8mb4') { ?>
                                <span class="tnp-ko">KO</span>
                            <?php } else { ?>
                                <span class="tnp-ok">OK</span>
                            <?php } ?>

                        </td>
                        <td>
                            Charset: <?php echo DB_CHARSET; ?>
                            <br>
                            <?php if (DB_CHARSET != 'utf8' && DB_CHARSET != 'utf8mb4') { ?>
                                The recommended charset for your database is <code>utf8</code> or <code>utf8mb4</code>
                                but the <a href="https://codex.wordpress.org/Converting_Database_Character_Sets" target="_blank">conversion</a>
                                could be tricky. If you're not experiencing problem, leave things as is.
                            <?php } else { ?>

                            <?php } ?>
                        </td>
                    </tr>


                    <?php $wait_timeout = $wpdb->get_var("select @@wait_timeout"); ?>
                    <tr>
                        <td>Database wait timeout</td>
                        <td>
                            <?php if ($wait_timeout < 30) { ?>
                                <span class="tnp-ko">KO</span>
                            <?php } else { ?>
                                <span class="tnp-ok">OK</span>
                            <?php } ?>

                        </td>
                        <td>
                            Your database wait timeout is <?php echo $wait_timeout; ?> seconds<br>
                            <?php if ($wait_timeout < 30) { ?>
                                That value is low and could produce database connection errors while sending emails or during long import
                                sessions. Ask the provider to raise it at least to 60 seconds.
                            <?php } ?>
                        </td>
                    </tr>

                    <?php
                    $res = $wpdb->query("drop table if exists {$wpdb->prefix}newsletter_test");
                    $res = $wpdb->query("create table if not exists {$wpdb->prefix}newsletter_test (id int(20))");
                    ?>
                    <tr>
                        <td>Database table creation</td>
                        <td>
                            <?php if ($res === false) { ?>
                                <span class="tnp-ko">KO</span>
                            <?php } else { ?>
                                <span class="tnp-ok">OK</span>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if ($res === false) { ?>
                                Check the privileges of the user you use to connect to the database, it seems it cannot create tables.<br>
                                (<?php echo esc_html($wpdb->last_error) ?>)
                            <?php } else { ?>
                            <?php } ?>
                        </td>
                    </tr>

                    <?php
                    $res = $wpdb->query("alter table {$wpdb->prefix}newsletter_test add column id1 int(20)");
                    ?>
                    <tr>
                        <td>Database table change</td>
                        <td>
                            <?php if ($res === false) { ?>
                                <span class="tnp-ko">KO</span>
                            <?php } else { ?>
                                <span class="tnp-ok">OK</span>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if ($res === false) { ?>
                                Check the privileges of the user you use to connect to the database, it seems it cannot change the tables. It's require to update the
                                plugin.<br>
                                (<?php echo esc_html($wpdb->last_error) ?>)
                            <?php } else { ?>
                            <?php } ?>
                        </td>
                    </tr> 

                    <?php
                    // Clean up
                    $res = $wpdb->query("drop table if exists {$wpdb->prefix}newsletter_test");
                    ?>

                    <?php
                    set_transient('newsletter_transient_test', 1, 300);
                    delete_transient('newsletter_transient_test');
                    $res = get_transient('newsletter_transient_test');
                    ?>
                    <tr>
                        <td>WordPress transients</td>
                        <td>
                            <?php if ($res !== false) { ?>
                                <span class="tnp-ko">KO</span>
                            <?php } else { ?>
                                <span class="tnp-ok">OK</span>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if ($res !== false) { ?>
                                Transients cannot be delete. This can block the delivery engine. Usually it is due to a not well coded plugin installed.
                            <?php } else { ?>
                            <?php } ?>
                        </td>
                    </tr>

                    <?php
                    $time = wp_next_scheduled('newsletter');
                    $res = true;
                    if ($time === false) {
                        $res = false;
                    }
                    $delta = $time - time();
                    if ($delta <= -600) {
                        $res = false;
                    }
                    ?>
                    <tr>
                        <td>Newsletter schedule timing</td>
                        <td>
                            <?php if ($res === false) { ?>
                                <span class="tnp-ko">KO</span>
                            <?php } else { ?>
                                <span class="tnp-ok">OK</span>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if ($time === false) { ?>
                                No next execution is planned.
                                <?php $controls->button('reschedule', 'Reset') ?>
                            <?php } else if ($delta <= -600) { ?>
                                The scheduler is very late: <?php echo $delta ?> seconds (<a href="https://www.thenewsletterplugin.com/plugins/newsletter/newsletter-delivery-engine" target="_blank">read more</a>)
                                <?php $controls->button('trigger', 'Trigger') ?>
                            <?php } else { ?>
                                Next execution is planned in <?php echo $delta ?> seconds (negative values are ok).
                            <?php } ?>
                        </td>
                    </tr>

                    <?php
                    $schedules = wp_get_schedules();
                    $res = false;
                    if (!empty($schedules)) {
                        foreach ($schedules as $key => $data) {
                            if ($key == 'newsletter') {
                                $res = true;
                                break;
                            }
                        }
                    }
                    ?>

                    <tr>
                        <td>
                            Newsletter schedule
                        </td>
                        <td>
                            <?php if ($res === false) { ?>
                                <span class="tnp-ko">KO</span>
                            <?php } else { ?>
                                <span class="tnp-ok">OK</span>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if ($res === false) { ?>
                                The Newsletter schedule is not present probably another plugin is interfering with the starndard WordPress schuling system.<br>
                            <?php } else { ?>
                            <?php } ?>

                            WordPress registered schedules:<br>
                            <?php
                            if (!empty($schedules)) {
                                foreach ($schedules as $key => $data) {
                                    echo esc_html($key . ' - ' . $data['interval']) . ' seconds<br>';
                                }
                            }
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            WordPress scheduler auto trigger
                        </td>
                        <td>
                            <?php if (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON) { ?>
                                <span class="tnp-maybe">MAYBE</span>
                            <?php } else { ?>
                                <span class="tnp-ok">OK</span>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON) { ?>
                                The constant DISABLE_WP_CRON is set to true (probably in wp-config.php). That disables the scheduler auto triggering and it's
                                good ONLY if you setup an external trigger.
                            <?php } else { ?>

                            <?php } ?>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            Cron calls
                        </td>
                        <td>
                            <?php if ($wp_cron_calls_avg > NEWSLETTER_CRON_INTERVAL * 1.1) { ?>
                                <span class="tnp-ko">KO</span>
                            <?php } else { ?>
                                <span class="tnp-ok">OK</span>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if ($wp_cron_calls_avg > NEWSLETTER_CRON_INTERVAL * 1.1) { ?>
                                The blog cron system is NOT triggered enough often.

                            <?php } else { ?>

                            <?php } ?>
                            <br>
                            Trigger interval: average <?php echo $wp_cron_calls_avg ?>&nbsp;s, max <?php echo $wp_cron_calls_max ?>&nbsp;s, min <?php echo $wp_cron_calls_min ?>&nbsp;s 
                        </td>
                    </tr>

                    <?php
                    $res = true;
                    $response = wp_remote_post(home_url('/') . '?na=test');
                    if (is_wp_error($response)) {
                        $res = false;
                        $message = $response->get_error_message();
                    } else {
                        if (wp_remote_retrieve_response_code($response) != 200) {
                            $res = false;
                            $message = wp_remote_retrieve_response_message($response);
                        }
                    }
                    ?>
                    <tr>
                        <td>
                            Action call
                        </td>
                        <td>
                            <?php if (!$res) { ?>
                                <span class="tnp-ko">KO</span>
                            <?php } else { ?>
                                <span class="tnp-ok">OK</span>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if (!$res) { ?>
                                The blog is not responding to Newsletter URLs: ask the provider or your IT consultant to check this problem. Report the URL and error blow<br>
                                Error: <?php echo esc_html($message) ?><br>
                            <?php } else { ?>

                            <?php } ?>
                            Url: <?php echo esc_html(home_url('/') . '?na=test') ?><br>
                        </td>
                    </tr>

                    <?php
                    $res = true;
                    $response = wp_remote_get(site_url('/wp-cron.php') . '?' . time());
                    if (is_wp_error($response)) {
                        $res = false;
                        $message = $response->get_error_message();
                    } else {
                        if (wp_remote_retrieve_response_code($response) != 200) {
                            $res = false;
                            $message = wp_remote_retrieve_response_message($response);
                        }
                    }
                    ?>
                    <tr>
                        <td>
                            WordPress scheduler auto trigger call
                        </td>
                        <td>
                            <?php if (!$res) { ?>
                                <span class="tnp-ko">KO</span>
                            <?php } else { ?>
                                <span class="tnp-ok">OK</span>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if (!$res) { ?>
                                The blog cannot autotrigger the internal scheduler, if an external trigger is used this could not be a real problem.<br>
                                Error: <?php echo esc_html($message) ?><br>
                            <?php } else { ?>

                            <?php } ?>
                            Url: <?php echo esc_html(site_url('/wp-cron.php')) ?><br>
                        </td>
                    </tr>

                    <?php
                    $res = true;
                    $response = wp_remote_get('http://www.thenewsletterplugin.com/wp-content/versions/all.txt');
                    if (is_wp_error($response)) {
                        $res = false;
                        $message = $response->get_error_message();
                    } else {
                        if (wp_remote_retrieve_response_code($response) != 200) {
                            $res = false;
                            $message = wp_remote_retrieve_response_message($response);
                        }
                    }
                    ?>
                    <tr>
                        <td>
                            Extension version check
                        </td>
                        <td>
                            <?php if (!$res) { ?>
                                <span class="tnp-ko">KO</span>
                            <?php } else { ?>
                                <span class="tnp-ok">OK</span>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if (!$res) { ?>
                                The blog cannot contact www.thenewsletterplugin.com to check the license or the extension versions.<br>
                                Error: <?php echo esc_html($message) ?><br>
                            <?php } else { ?>

                            <?php } ?>
                        </td>
                    </tr>    
                    <?php
                    // Send calls stats
                    $send_calls = get_option('newsletter_diagnostic_send_calls', array());
                    if (count($send_calls)) {
                        $send_max = 0;
                        $send_min = PHP_INT_MAX;
                        $send_total_time = 0;
                        $send_total_emails = 0;
                        $send_completed = 0;
                        for ($i = 0; $i < count($send_calls); $i++) {
                            if (empty($send_calls[$i][2]))
                                continue;

                            $delta = $send_calls[$i][1] - $send_calls[$i][0];
                            $send_total_time += $delta;
                            $send_total_emails += $send_calls[$i][2];
                            $send_mean = $delta / $send_calls[$i][2];
                            if ($send_min > $send_mean) {
                                $send_min = $send_mean;
                            }
                            if ($send_max < $send_mean) {
                                $send_max = $send_mean;
                            }
                            if (isset($send_calls[$i][3])) {
                                $send_completed++;
                            }
                        }
                        $send_mean = $send_total_time / $send_total_emails;
                        ?>
                        <tr>
                            <td>
                                Send details
                            </td>
                            <td>
                                <?php if ($send_mean > 1) { ?>
                                    <span class="tnp-ko">KO</span>
                                <?php } else { ?>
                                    <span class="tnp-ok">OK</span>
                                <?php } ?>
                            </td>
                            <td>
                                <?php if ($send_mean > 1) { ?>
                                    <strong>Sending an email is taking more than 1 second, rather slow.</strong>
                                    <a href="https://www.thenewsletterplugin.com/documentation/status-panel#status-performance" target="_blank">Read more</a>.
                                <?php } ?>
                                Average time to send an email: <?php echo sprintf("%.2f", $send_mean) ?> seconds<br>
                                <?php if ($send_mean > 0) { ?>
                                    Max speed: <?php echo sprintf("%.2f", 1.0 / $send_mean * 3600) ?> emails per hour<br>
                                <?php } ?>

                                Max mean time measured: <?php echo sprintf("%.2f", $send_max) ?> seconds<br>
                                Min mean time measured: <?php echo sprintf("%.2f", $send_min) ?> seconds<br>
                                Total email in the sample: <?php echo $send_total_emails ?><br>
                                Runs in the sample: <?php echo count($send_calls); ?><br> 
                                Runs prematurely interrupted: <?php echo sprintf("%.2f", (count($send_calls) - $send_completed) * 100.0 / count($send_calls)) ?>%<br>
                            </td>
                        </tr>    
                        <?php
                    }
                    ?>

                    <tr>
                        <td>
                            WordPress debug mode
                        </td>
                        <td>
                            <?php if (defined('WP_DEBUG') && WP_DEBUG) { ?>
                                <span class="tnp-maybe">MAYBE</span>
                            <?php } else { ?>
                                <span class="tnp-ok">OK</span>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if (defined('WP_DEBUG') && WP_DEBUG) { ?>
                                WordPress is in debug mode it is not recommended on a production system. See the constant WP_DEBUG inside the wp-config.php.
                            <?php } else { ?>

                            <?php } ?>
                        </td>
                    </tr>



                    <?php /*
                    $memory = intval(WP_MEMORY_LIMIT);
                    if (false !== strpos(WP_MEMORY_LIMIT, 'G'))
                        $memory *= 1024;
                    ?>
                    <tr>
                        <td>
                            PHP memory limit
                        </td>
                        <td>
                            <?php if ($memory < 64) { ?>
                                <span class="tnp-ko">MAYBE</span>
                            <?php } else if ($memory < 128) { ?>
                                <span class="tnp-maybe">MAYBE</span>
                            <?php } else { ?>
                                <span class="tnp-ok">OK</span>
                            <?php } ?>    
                        </td>
                        <td>
                            WordPress WP_MEMORY_LIMIT is set to <?php echo $memory ?> megabyte but your PHP setting could allow more than that.
                            Anyway we suggest to set the value to at least 64M.
                            <a href="https://www.thenewsletterplugin.com/documentation/status-panel#status-memory" target="_blank">Read more</a>.
                            <?php if ($memory < 64) { ?>
                                This value is too low you should increase it adding <code>define('WP_MEMORY_LIMIT', '64M');</code> to your <code>wp-config.php</code>.
                                <a href="https://www.thenewsletterplugin.com/documentation/status-panel#status-memory" target="_blank">Read more</a>.
                            <?php } else if ($memory < 128) { ?>
                                The value should be fine, it depends on how many plugins you're running and how many resource are required by your theme.
                                Blank pages may happen with low memory problems. Eventually increase it adding <code>define('WP_MEMORY_LIMIT', '128M');</code>
                                to your <code>wp-config.php</code>.
                                <a href="https://www.thenewsletterplugin.com/documentation/status-panel#status-memory" target="_blank">Read more</a>.
                            <?php } else { ?>

                            <?php } ?>

                        </td>
                    </tr>
                     */ ?>
                    
                    <?php
                    $ip = gethostbyname($_SERVER['HTTP_HOST']);
                    $name = gethostbyaddr($ip);
                    $res = true;
                    if (strpos($name, '.secureserver.net') !== false) {
                        //$smtp = get_option('newsletter_main_smtp');
                        //if (!empty($smtp['enabled']))
                        $res = false;
                        $message = 'If you\'re hosted with GoDaddy, be sure to set their SMTP (relay-hosting.secureserver.net, without username and password) to send emails
                                    on Newsletter SMTP panel.
                                    Remember they limits you to 250 emails per day. Open them a ticket for more details.';
                    }
                    if (strpos($name, '.aruba.it') !== false) {
                        $res = false;
                        $message = 'If you\'re hosted with Aruba consider to use an external SMTP (Sendgrid, Mailjet, Mailgun, Amazon SES, Elasticemail, Sparkpost, ...)
                                    since their mail service is not good. If you have your personal email with them, you can try to use the SMTP of your
                                    pesonal account. Ask the support for the SMTP parameters and configure them on Newsletter SMTP panel.';
                    }
                    ?>
                    <tr>
                        <td>Your Server</td>
                        <td>
                            <?php if ($res === false) { ?>
                                <span class="tnp-maybe">MAYBE</span>
                            <?php } else { ?>
                                <span class="tnp-ok">OK</span>
                            <?php } ?>   


                        </td>
                        <td>
                            <?php if ($res === false) { ?>
                                <?php echo $message ?>
                            <?php } else { ?>

                            <?php } ?>
                            IP: <?php echo $ip ?><br>
                            Name: <?php echo $name ?><br>
                        </td>
                    </tr>

                    <?php
                    wp_mkdir_p(NEWSLETTER_LOG_DIR);
                    $res = is_dir(NEWSLETTER_LOG_DIR);
                    if ($res) {
                        file_put_contents(NEWSLETTER_LOG_DIR . '/test.txt', "");
                        $res = is_file(NEWSLETTER_LOG_DIR . '/test.txt');
                        if ($res) {
                            @unlink(NEWSLETTER_LOG_DIR . '/test.txt');
                        }
                    }
                    ?>
                    <tr>
                        <td>
                            Log folder
                        </td>
                        <td>
                            <?php if (!$res) { ?>
                                <span class="tnp-ko">KO</span>
                            <?php } else { ?>
                                <span class="tnp-ok">OK</span>
                            <?php } ?>
                        </td>
                        <td>
                            The log folder is <?php echo esc_html(NEWSLETTER_LOG_DIR) ?><br>
                            <?php if (!$res) { ?>
                                Cannot create the folder or it is not writable.
                            <?php } else { ?>

                            <?php } ?>
                        </td>
                    </tr>           
                </tbody>
            </table>

            <h3>General parameters</h3>
            <table class="widefat" id="tnp-parameters-table">
                <thead>
                    <tr>
                        <th>Parameter</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>

                    <tr>
                        <td>Newsletter version</td>
                        <td>
                            <?php echo NEWSLETTER_VERSION ?>
                        </td>
                    </tr>

                    <tr>
                        <td>NEWSLETTER_MAX_EXECUTION_TIME</td>
                        <td>
                            <?php
                            if (defined('NEWSLETTER_MAX_EXECUTION_TIME')) {
                                echo NEWSLETTER_MAX_EXECUTION_TIME . ' (seconds)';
                            } else {
                                echo 'Not set';
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>NEWSLETTER_CRON_INTERVAL</td>
                        <td>
                            <?php echo NEWSLETTER_CRON_INTERVAL . ' (seconds)'; ?>
                        </td>
                    </tr>

                    <?php /*
                      <tr>
                      <td>WordPress plugin url</td>
                      <td>
                      <?php echo WP_PLUGIN_URL; ?>
                      <br>
                      Filters:

                      <?php
                      if (isset($wp_filter))
                      $filters = $wp_filter['plugins_url'];
                      if (!isset($filters) || !is_array($filters))
                      echo 'no filters attached to "plugin_urls"';
                      else {
                      echo '<ul>';
                      foreach ($filters as &$filter) {
                      foreach ($filter as &$entry) {
                      echo '<li>';
                      if (is_array($entry['function']))
                      echo esc_html(get_class($entry['function'][0]) . '->' . $entry['function'][1]);
                      else
                      echo esc_html($entry['function']);
                      echo '</li>';
                      }
                      }
                      echo '</ul>';
                      }
                      ?>
                      <p class="description">
                      This value should contains the full URL to your plugin folder. If there are filters
                      attached, the value can be different from the original generated by WordPress and sometime worng.
                      </p>
                      </td>
                      </tr>
                     */ ?>

                    <tr>
                        <td>Absolute path</td>
                        <td>
                            <?php echo esc_html(ABSPATH); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Tables Prefix</td>
                        <td>
                            <?php echo $wpdb->prefix; ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <h3>Log files</h3>

            <ul class="tnp-log-files">
                <?php
                $files = glob(WP_CONTENT_DIR . '/logs/newsletter/*.txt'); // get all file names
                foreach ($files as $file) { // iterate files
                    echo '<li><a href="' . WP_CONTENT_URL . '/logs/newsletter/' . basename($file) . '" target="_blank">' . basename($file) . '</a>';
                    echo ' <span class="tnp-log-size">(' . size_format(filesize($file)) . ')</span>';
                    echo '</li>';
                }
                ?>
            </ul>

            <?php $controls->button('delete_logs', 'Delete all'); ?>


            <?php if (isset($_GET['debug'])) { ?>


                <h3>Database Tables</h3>
                <h4><?php echo $wpdb->prefix ?>newsletter</h4>
                <?php
                $rs = $wpdb->get_results("describe {$wpdb->prefix}newsletter");
                ?>
                <table class="tnp-db-table">
                    <thead>
                        <tr>
                            <th>Field</th>
                            <th>Type</th>
                            <th>Null</th>
                            <th>Key</th>
                            <th>Default</th>
                            <th>Extra</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rs as $r) { ?>
                            <tr>
                                <td><?php echo esc_html($r->Field) ?></td>
                                <td><?php echo esc_html($r->Type) ?></td>
                                <td><?php echo esc_html($r->Null) ?></td>
                                <td><?php echo esc_html($r->Key) ?></td>
                                <td><?php echo esc_html($r->Default) ?></td>
                                <td><?php echo esc_html($r->Extra) ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <h4><?php echo $wpdb->prefix ?>newsletter_emails</h4>
                <?php
                $rs = $wpdb->get_results("describe {$wpdb->prefix}newsletter_emails");
                ?>
                <table class="tnp-db-table">
                    <thead>
                        <tr>
                            <th>Field</th>
                            <th>Type</th>
                            <th>Null</th>
                            <th>Key</th>
                            <th>Default</th>
                            <th>Extra</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rs as $r) { ?>
                            <tr>
                                <td><?php echo esc_html($r->Field) ?></td>
                                <td><?php echo esc_html($r->Type) ?></td>
                                <td><?php echo esc_html($r->Null) ?></td>
                                <td><?php echo esc_html($r->Key) ?></td>
                                <td><?php echo esc_html($r->Default) ?></td>
                                <td><?php echo esc_html($r->Extra) ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>


                <h3>Extensions</h3>
                <pre style="font-size: 11px; font-family: monospace; background-color: #efefef; color: #444"><?php echo esc_html(print_r(get_option('newsletter_extension_versions'), true)); ?></pre>

                <h3>Update plugins data</h3>
                <pre style="font-size: 11px; font-family: monospace; background-color: #efefef; color: #444"><?php echo esc_html(print_r(get_site_transient('update_plugins'), true)); ?></pre>

            <?php } ?>
    </div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

</div>
