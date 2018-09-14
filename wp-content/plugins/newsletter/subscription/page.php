<?php
// This page is used to show subscription messages to users along the various
// subscription and unsubscription steps.
//
// This page is used ONLY IF, on main configutation, you have NOT set a specific
// WordPress page to be used to show messages and when there are no alternative
// URLs specified on single messages.
//
// To create an alternative to this file, just copy the page-alternative.php on
//
//   wp-content/extensions/newsletter/subscription/page.php
//
// and modify that copy.

if (!defined('ABSPATH')) exit;

$module = NewsletterSubscription::instance();
$user = $module->get_user_from_request(true);
$message_key = $module->get_message_key_from_request();
$message = apply_filters('newsletter_page_text', '', $message_key, $user);
$options = $module->get_options('', $module->get_user_language($user));
if (!$message) {
    $message = $options[$message_key . '_text'];
}
$message = $module->replace($message, $user);

if (isset($options[$message_key . '_tracking'])) {
    $message .= $options[$message_key . '_tracking'];
}
$alert = '';
if (isset($_REQUEST['alert'])) $alert = stripslashes($_REQUEST['alert']);

// Force the UTF-8 charset
header('Content-Type: text/html;charset=UTF-8');

if (is_file(WP_CONTENT_DIR . '/extensions/newsletter/subscription/page.php')) {
    include WP_CONTENT_DIR . '/extensions/newsletter/subscription/page.php';
    die();
}
?>
<html>
    <head>
        <style type="text/css">
            body {
                font-family: verdana;
                background-color: #ddd;
                font-size: 14px;
                color: #333;
            }
            #container {
                border: 1px solid #ccc;
                border-radius: 0px;
                background-color: #fff;
                margin: 40px auto;
                width: 650px;
                padding: 30px
            }
            h1 {
                font-size: 30px;
                font-weight: normal;
                border-bottom: 0px solid #aaa;
                margin-top: 0;
            }
            h2 {
                font-size: 20px;
            }
            th, td {
                font-size: 12px;
            }
            th {
                padding-right: 10px;
                text-align: right;
                vertical-align: middle;
                font-weight: normal;
            }
            
            #message {
                line-height: 1.6em;
            }
        </style>
    </head>

    <body>
        <?php if (!empty($alert)) { ?>
        <script>
            alert("<?php echo addslashes(strip_tags($alert)); ?>");
        </script>
        <?php } ?>
        <div id="container">
            <h1><?php echo get_option('blogname'); ?></h1>
            <div id="message">
            <?php echo $message; ?>
            </div>
        </div>
    </body>
</html>