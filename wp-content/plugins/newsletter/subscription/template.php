<?php
defined('ABSPATH') || exit;

@include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();
$module = NewsletterSubscription::instance();

$current_language = $module->get_current_language();

$is_all_languages = $module->is_all_languages();

if (!$is_all_languages) {
    $controls->warnings[] = 'You are configuring the language <strong>' . $current_language . '</strong>.';
}

if (!$controls->is_action()) {
    $controls->data = $module->get_options('template', $current_language);
} else {
    if ($controls->is_action('save')) {
        $module->save_options($controls->data, 'template', null, $current_language);

        if (strpos($controls->data['template'], '{message}') === false) {
            $controls->errors = __('The tag {message} is missing in your template', 'newsletter');
        }

        $controls->add_message_saved();
    }

    if ($controls->is_action('reset')) {
        // TODO: Reset by language?
        $module->reset_options('template');
        $controls->data = $module->get_options('template', $current_language);
        $controls->add_message_done();
    }

    if ($controls->is_action('test')) {

        $users = $module->get_test_users();
        if (count($users) == 0) {
            $controls->errors = __('No test subscribers found.', 'newsletter') . ' <a href="https://www.thenewsletterplugin.com/plugins/newsletter/subscribers-module#test" target="_blank"><i class="fa fa-info-circle"></i></a>';
        } else {
            $template = $controls->data['template'];

            $message = '<p>This is a generic example of message embedded inside the template.</p>';
            $message .= '<p>Subscriber data can be referenced using tags. See the <a href="https://www.thenewsletterplugin.com/documentation">plugin documentation</a>.</p>';
            $message .= '<p>First name: {name}</p>';
            $message .= '<p>Last name: {surname}</p>';
            $message .= '<p>Email: {email}</p>';
            $message .= '<p>Here an image as well. Make them styled with the CSS rule "max-width: 100%"</p>';
            $message .= '<p><img src="' . plugins_url('newsletter') . '/images/test.jpg" style="max-width: 100%"></p>';
            
            $message = str_replace('{message}', $message, $template);
            $addresses = array();
            foreach ($users as $user) {
                $addresses[] = $user->email;
                Newsletter::instance()->mail($user->email, 'Newsletter Messages Template Test', $module->replace($message, $user));
            }
            $controls->messages .= 'Test emails sent to ' . count($users) . ' test subscribers: ' .
                    implode(', ', $addresses) . '.' . ' <a href="https://www.thenewsletterplugin.com/plugins/newsletter/subscribers-module#test" target="_blank"><i class="fa fa-info-circle"></i></a>';
        }
    }
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.37.0/codemirror.css" type="text/css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.37.0/addon/hint/show-hint.css">
<style>    
    .CodeMirror {
        height: 100%;
    }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.37.0/codemirror.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.37.0/mode/xml/xml.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.37.0/mode/css/css.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.37.0/mode/javascript/javascript.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.37.0/mode/htmlmixed/htmlmixed.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.37.0/addon/hint/show-hint.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.37.0/addon/hint/xml-hint.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.37.0/addon/hint/html-hint.js"></script>
<script>
    jQuery(function () {
        templateEditor = CodeMirror.fromTextArea(document.getElementById("options-template"), {
            lineNumbers: true,
            mode: 'htmlmixed',
            extraKeys: {"Ctrl-Space": "autocomplete"}
        });
    });
</script>

<div class="wrap" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">

        <h2><?php _e('Messages template', 'newsletter') ?></h2>
        <p>
            Edit the default template of confirmation, welcome and cancellation emails. Add the {message} tag where you
            want the specific message text to be included.
        </p>

    </div>

    <div id="tnp-body">

        <form method="post" action="">
            <?php $controls->init(); ?>
            <p>
                <?php $controls->button_save(); ?>
                <?php $controls->button_reset(); ?>
                <?php $controls->button('test', 'Send a test'); ?>
            </p>


            <h3><?php _e('Template', 'newsletter') ?></h3>

            <?php $controls->textarea_preview('template', '100%', '700px'); ?>
            <br><br>


            <p>
                <?php $controls->button_save(); ?>
                <?php $controls->button_reset(); ?>
                <?php $controls->button('test', 'Send a test'); ?>
            </p>
        </form>
    </div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

</div>