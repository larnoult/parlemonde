<?php
defined('ABSPATH') || exit;

require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();
$module = NewsletterEmails::instance();

wp_enqueue_style('wp-color-picker');
wp_enqueue_script('wp-color-picker');

// TNP Composer style
wp_enqueue_style('tnpc-style', plugins_url('/tnp-composer/_css/newsletter-builder.css', __FILE__), array(), time());
//wp_enqueue_style('tnpc-newsletter-style', plugins_url('/tnp-composer/css/newsletter.css', __FILE__));
wp_enqueue_style('tnpc-newsletter-style', home_url('/') . '?na=emails-composer-css');

wp_enqueue_style('tnpc-cm-style1', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.37.0/codemirror.css');
wp_enqueue_style('tnpc-cm-style2', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.37.0/addon/hint/show-hint.css');

wp_enqueue_script('tnpc-cm-1', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.37.0/codemirror.js');
wp_enqueue_script('tnpc-cm-2', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.37.0/mode/xml/xml.js');
wp_enqueue_script('tnpc-cm-3', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.37.0/mode/css/css.js');
wp_enqueue_script('tnpc-cm-4', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.37.0/mode/javascript/javascript.js');
wp_enqueue_script('tnpc-cm-5', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.37.0/mode/htmlmixed/htmlmixed.js');
wp_enqueue_script('tnpc-cm-6', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.37.0/addon/hint/show-hint.js');
wp_enqueue_script('tnpc-cm-7', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.37.0/addon/hint/xml-hint.js');
wp_enqueue_script('tnpc-cm-8', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.37.0/addon/hint/html-hint.js');

if (($controls->is_action('save') || $controls->is_action('preview')) && !isset($_GET['id'])) {

    $module->save_options($controls->data);

    $email = array();
    $email['status'] = 'new';
    $email['subject'] = __('Here the email subject', 'newsletter');
    $email['track'] = Newsletter::instance()->options['track'];
    $email['token'] = $module->get_token();

    $email['message'] = $controls->data['body'];
    $email['subject'] = $controls->data['subject'];

    $email['message_text'] = 'This email requires a modern e-mail reader but you can view the email online here:
{email_url}.

Thank you, ' . wp_specialchars_decode(get_option('blogname'), ENT_QUOTES) . '

To change your subscription follow: {profile_url}.';

    $email['options'] = serialize(array('composer' => true));

    $email['type'] = 'message';
    $email['send_on'] = time();
    $email['query'] = "select * from " . NEWSLETTER_USERS_TABLE . " where status='C'";

    $email = Newsletter::instance()->save_email($email, ARRAY_A);
} elseif (isset($_GET['id'])) {

    $email = Newsletter::instance()->get_email((int) $_GET['id'], ARRAY_A);

    if (empty($email)) {
        echo 'Wrong email identifier';
        return;
    }

    if ($controls->is_action('save') || $controls->is_action('preview')) {

        $module->save_options($controls->data);
        $email['message'] = $controls->data['body'];
        $email = Newsletter::instance()->save_email($email, ARRAY_A);
    }
}

if ($controls->is_action('preview')) {
    ?>
    <script>
        location.href = "<?php echo $module->get_admin_page_url('edit'); ?>&id=<?php echo $email['id']; ?>";
    </script>
    <div class="wrap">
        <p><a href="<?php echo $module->get_admin_page_url('edit'); ?>&id=<?php echo $email['id']; ?>">click here to proceed</a>.</p>
    </div>
    <?php
    return;
}

if ($controls->data == null) {
    $controls->data = $module->get_options();
}

$body = "";
if (isset($email)) {
    $body = $email['message'];
}
?>

<div class="wrap tnp-emails-composer" id="tnp-wrap">

    <div id="tnp-heading" class="tnp-composer-heading">
        <!--
                <img src="https://cdn.thenewsletterplugin.com/tests/tnp-composer-heading.png">
                <h2><?php _e('Compose a newsletter', 'newsletter') ?></h2>
                <a href="https://www.thenewsletterplugin.com/plugins/newsletter/composer" target="_blank"><i class="fa fa-life-ring"></i> <?php _e('Read the guide', 'newsletter') ?></a>
        -->        
        <form method="post" action="" id="tnpc-form">
            <?php $controls->init(); ?>
            <?php $controls->hidden('subject'); ?>
            <?php $controls->hidden('body'); ?>
            <?php $controls->button_reset(); ?>
            <?php $controls->button('save', __('Save', 'newsletter'), 'create();'); ?>
            <?php $controls->button('preview', __('Save & Preview', 'newsletter') . ' &raquo;', 'create();'); ?>
        </form>
    </div>



    <?php include NEWSLETTER_DIR . '/emails/tnp-composer/index.php'; ?>


</div>