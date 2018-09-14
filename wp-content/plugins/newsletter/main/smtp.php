<?php
if (!defined('ABSPATH')) exit;

@include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$module = Newsletter::instance();
$controls = new NewsletterControls();

if (!$controls->is_action()) {
    $controls->data = $module->get_options('smtp');
} else {


    if ($controls->is_action('save')) {

        if ($controls->data['enabled'] && empty($controls->data['host'])) {
            $controls->errors = 'The host must be set to enable the SMTP';
        }

        if (empty($controls->errors)) {
            $module->save_options($controls->data, 'smtp');
            $controls->messages .= __('Saved. Remember to test your changes right now!', 'newsletter');
        }
    }

    if ($controls->is_action('test')) {

        require_once ABSPATH . WPINC . '/class-phpmailer.php';
        require_once ABSPATH . WPINC . '/class-smtp.php';
        $mail = new PHPMailer();
        ob_start();
        $mail->IsSMTP();
        $mail->SMTPDebug = true;
        $mail->CharSet = 'UTF-8';
        $message = 'This Email is sent by PHPMailer of WordPress';
        $mail->IsHTML(false);
        $mail->Body = $message;
        $mail->From = $module->options['sender_email'];
        $mail->FromName = $module->options['sender_name'];
        if (!empty($module->options['return_path'])) {
            $mail->Sender = $module->options['return_path'];
        }
        if (!empty($module->options['reply_to'])) {
            $mail->AddReplyTo($module->options['reply_to']);
        }

        $mail->Subject = '[' . get_option('blogname') . '] SMTP test';

        $mail->Host = $controls->data['host'];
        if (!empty($controls->data['port'])) {
            $mail->Port = (int) $controls->data['port'];
        }

        $mail->SMTPSecure = $controls->data['secure'];
        $mail->SMTPAutoTLS = false;
        
        if ($controls->data['ssl_insecure'] == 1) {
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );
        }

        if (!empty($controls->data['user'])) {
            $mail->SMTPAuth = true;
            $mail->Username = $controls->data['user'];
            $mail->Password = $controls->data['pass'];
        }

        $mail->SMTPKeepAlive = true;
        $mail->ClearAddresses();
        $mail->AddAddress($controls->data['test_email']);

        $mail->Send();
        $mail->SmtpClose();
        $debug = htmlspecialchars(ob_get_clean());

        if ($mail->IsError()) {
            $controls->errors = '<strong>Connection/email delivery failed.</strong><br>You should contact your provider reporting the SMTP parameter and asking about connection to that SMTP.<br><br>';
            $controls->errors = $mail->ErrorInfo;
        } else
            $controls->messages = 'Success.';

        $controls->messages .= '<textarea style="width:100%; height:200px; font-size:12px; font-family: monospace">';
        $controls->messages .= $debug;
        $controls->messages .= '</textarea>';
    }
}

if (empty($controls->data['enabled']) && !empty($controls->data['host'])) {
    $controls->warnings[] = 'SMTP configured but NOT enabled.';
}
?>

<div class="wrap" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

	<div id="tnp-heading">

        <h2><?php _e('SMTP Settings', 'newsletter') ?></h2>
    
    <p>
        <i class="fa fa-info-circle"></i> <a href="https://www.thenewsletterplugin.com/extensions" target="_blank">Discover how SMTP services can boost your newsletters!</a>
        <!--
    <p>SMTP (Simple Mail Transfer Protocol) refers to external delivery services you can use to send emails.</p>
    <p>SMTP services are usually more reliable, secure and spam-aware than the standard delivery method available to your blog.</p>
    <p>Even better, using the <a href="https://www.thenewsletterplugin.com/extensions">integration extensions</a>, you can benefit of more efficient service connections, bounce detection and other nice features.</p>
        -->
    </p>
    
    <p>
            <strong>These options can be overridden by extensions which integrates with external
                SMTPs (like MailJet, SendGrid, ...) if installed and activated.</strong>
        </p>
        <p>

            What you need to know to use an external SMTP can be found
            <a href="https://www.thenewsletterplugin.com/plugins/newsletter/newsletter-configuration#smtp" target="_blank">here</a>.
            <br>
            On GoDaddy you should follow this <a href="https://www.thenewsletterplugin.com/godaddy-using-smtp-external-server-shared-hosting" target="_blank">special setup</a>.
        </p>
        <p>
            Consider <a href="https://www.thenewsletterplugin.com/affiliate/sendgrid" target="_blank">SendGrid</a> for a serious and reliable SMTP service.
        </p>
    
    </div>

	<div id="tnp-body">

    <form method="post" action="">
        <?php $controls->init(); ?>

        <table class="form-table">
            <tr>
                <th>Enable the SMTP?</th>
                <td><?php $controls->yesno('enabled'); ?></td>
            </tr>
            <tr>
                <th>SMTP host/port</th>
                <td>
                    host: <?php $controls->text('host', 30); ?>
                    port: <?php $controls->text('port', 6); ?>
                    <?php $controls->select('secure', array('' => 'No secure protocol', 'tls' => 'TLS protocol', 'ssl' => 'SSL protocol')); ?>
                    <p class="description">
                        Leave port empty for default value (25). To use Gmail try host "smtp.gmail.com" and port "465" and SSL protocol (without quotes).
                        For GoDaddy use "relay-hosting.secureserver.net".
                    </p>
                </td>
            </tr>
            <tr>
                <th>Authentication</th>
                <td>
                    user: <?php $controls->text('user', 30); ?>
                    password: <?php $controls->text('pass', 30); ?>
                    <p class="description">
                        If authentication is not required, leave "user" field blank.
                    </p>
                </td>
            </tr>
            <tr>
                <th>Insecure SSL Connections</th>
                <td>
                    <?php $controls->yesno('ssl_insecure'); ?> <a href="https://www.thenewsletterplugin.com/?p=21989" target="_blank">Read more</a>.
                </td>
            </tr>
            <tr>
                <th>Test email address</th>
                <td>
                    <?php $controls->text_email('test_email', 30); ?>
                    <?php $controls->button('test', 'Send a test email to this address'); ?>
                    <p class="description">
                        If the test reports a "connection failed", review your settings and, if correct, contact
                        your provider to unlock the connection (if possible).
                    </p>
                </td>
            </tr>
        </table>

        <p>
            <?php $controls->button_save(); ?>
        </p>

    </form>
</div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>
    
</div>
