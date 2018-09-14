<?php
require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();
$module = NewsletterEmails::instance();

if ($controls->is_action('theme')) {
    if ($controls->data['theme'] != 'rawhtml') {
        $controls->merge($module->themes->get_options($controls->data['theme']));
        $module->save_options($controls->data);
    }

    // If this theme has no intermediate options...
    if ($controls->data['theme'] == 'rawhtml' || !file_exists($module->get_current_theme_file_path('theme-options.php'))) {
        $email = array();
        $email['status'] = 'new';
        $email['subject'] = __('Here the email subject', 'newsletter');
        $email['track'] = Newsletter::instance()->options['track'];
        $email['token'] = $module->get_token();

        if ($controls->data['theme'] == 'rawhtml') {
            $email['editor'] = 1;
            $email['message'] = "<!DOCTYPE html>\n<html>\n<head>\n<title>Your email title</title>\n</head>\n<body>\n</body>\n</html>";
        } else {
            $theme_options = $module->get_current_theme_options();
            $theme_url = $module->get_current_theme_url();
            $theme_subject = '';

            ob_start();
            include $module->get_current_theme_file_path('theme.php');
            $email['message'] = ob_get_clean();

            if (!empty($theme_subject)) {
                $email['subject'] = $theme_subject;
            }

            $file = $module->get_current_theme_file_path('theme-text.php');
            if (file_exists($file)) {
                ob_start();
                include $module->get_current_theme_file_path('theme-text.php');
                $email['message_text'] = ob_get_clean();
            } else {
                $email['message_text'] = 'You need a modern email client to read this email. Read it online: {email_url}.';
            }
        }

        $email['type'] = 'message';
        $email['send_on'] = time();
        $email = Newsletter::instance()->save_email($email);
        ?>
        <script>
            location.href = "<?php echo $module->get_admin_page_url('edit'); ?>&id=<?php echo $email->id; ?>";
        </script>
        <div class="wrap">
            <p>If you are not automatically redirected to the composer, <a href="<?php echo $module->get_admin_page_url('edit'); ?>&id=<?php echo $email->id; ?>">click here</a>.</p>
        </div>
        <?php
        return;
    }
}

if ($controls->is_action('save')) {
    $module->save_options($controls->data);
    //$controls->messages = 'Saved.';
}

if ($controls->is_action('create')) {
    $module->save_options($controls->data);

    $email = array();
    $email['status'] = 'new';
    $email['subject'] = __('Here the email subject', 'newsletter');
    $email['track'] = Newsletter::instance()->options['track'];

    $theme_options = $module->get_current_theme_options();

    $theme_url = $module->get_current_theme_url();
    $theme_subject = '';

    ob_start();
    include $module->get_current_theme_file_path('theme.php');
    $email['message'] = ob_get_clean();

    if (!empty($theme_subject)) {
        $email['subject'] = $theme_subject;
    }

    ob_start();
    include $module->get_current_theme_file_path('theme-text.php');
    $email['message_text'] = ob_get_clean();

    $email['type'] = 'message';
    $email['send_on'] = time();
    $email = Newsletter::instance()->save_email($email);
    ?>
    <script>
        location.href = "<?php echo $module->get_admin_page_url('edit'); ?>&id=<?php echo $email->id; ?>";
    </script>
    <div class="wrap">
        <p><a href="<?php echo $module->get_admin_page_url('edit'); ?>&id=<?php echo $email->id; ?>">click here to proceed</a>.</p>
    </div>
    <?php
    return;
}

if ($controls->data == null) {
    $controls->data = $module->get_options();
}

function newsletter_emails_update_options($options) {
    add_option('newsletter_emails', '', null, 'no');
    update_option('newsletter_emails', $options);
}

function newsletter_emails_update_theme_options($theme, $options) {
    $x = strrpos($theme, '/');
    if ($x !== false) {
        $theme = substr($theme, $x + 1);
    }
    add_option('newsletter_emails_' . $theme, '', null, 'no');
    update_option('newsletter_emails_' . $theme, $options);
}

function newsletter_emails_get_options() {
    $options = get_option('newsletter_emails', array());
    return $options;
}

function newsletter_emails_get_theme_options($theme) {
    $x = strrpos($theme, '/');
    if ($x !== false) {
        $theme = substr($theme, $x + 1);
    }
    $options = get_option('newsletter_emails_' . $theme, array());
    return $options;
}
?>

<div class="wrap tnp-emails tnp-emails-new" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">

        <h2><?php _e('Create a newsletter', 'newsletter') ?>
            <a class="tnp-btn-h1" href="<?php echo NewsletterEmails::instance()->get_admin_page_url('theme'); ?>"><?php _e('Back to newsletter themes', 'newsletter') ?></a>
        </h2>
        <br>
        <p>Theme options are saved for next time you'll use this theme.</p>

    </div>

    <div id="tnp-body" class="tnp-body-lite"> 

        <form method="post" action="<?php echo $module->get_admin_page_url('new'); ?>">
            <?php $controls->init(); ?>
            <?php $controls->hidden('theme'); ?>

            <table style="width: 100%; border-collapse: collapse">
                <tr>
                    <td style="text-align: left; vertical-align: top; border-bottom: 1px solid #ddd; padding-bottom: 10px">
                        <div style="float: right; margin-left: 15px;"><?php $controls->button_primary('save', __('Refresh the preview', 'newsletter')); ?></div>

                    </td>
                    <td style="text-align: left; vertical-align: top; border-bottom: 1px solid #ddd; padding-bottom: 10px">
                        <div style="float: right"><?php $controls->button_primary('create', 'Proceed to edit &raquo;', 'this.form.action=\'' . home_url('/', is_ssl() ? 'https' : 'http') . '?na=emails-create\';this.form.submit()'); ?></div>
                        <img style="position: relative; left: 5px; top: 10px;"src="<?php echo plugins_url('newsletter') ?>/images/arrow.png" height="35">
                    </td>
                </tr>
                <tr>
                    <td style="width: 500px; vertical-align: top;">
                        <div class="tnp-emails-theme-options">
                        <?php @include $module->get_current_theme_file_path('theme-options.php'); ?>
                        </div>
                    </td>
                    <td style="vertical-align: top; padding-top: 15px; padding-left: 15px">
                        <iframe src="<?php echo wp_nonce_url(home_url('/', is_ssl() ? 'https' : 'http') . '?na=emails-preview&ts=' . time(), 'view'); ?>" height="700" style="width: 100%; border: 1px solid #ccc"></iframe>
                    </td>
                </tr>
            </table>

        </form>
    </div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

</div>