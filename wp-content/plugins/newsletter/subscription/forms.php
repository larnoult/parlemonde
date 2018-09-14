<?php
if (!defined('ABSPATH')) exit;

@include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();
$module = NewsletterSubscription::instance();

if (!$controls->is_action()) {
    $controls->data = $module->get_options('forms');
}

if ($controls->is_action('save')) {
    $module->save_options($controls->data, 'forms');
    $controls->add_message_saved();
}
?>

<div class="wrap" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">

        <h2><?php _e('Custom Forms', 'newsletter') ?></h2>
        <p>
            Here you can store your hand coded forms to recall them from short codes.
            <a href="https://www.thenewsletterplugin.com/plugins/newsletter/newsletter-forms" target="_blank">Read more about forms</a>.
        </p>

    </div>

    <div id="tnp-body">

        <form method="post" action="">
            <?php $controls->init(); ?>

            <div id="tabs">

                <ul>
                    <li><a href="#tabs-forms">Forms</a></li>
                </ul>

                <div id="tabs-forms">
                    <table class="form-table">
                        <?php for ($i = 1; $i <= 10; $i++) { ?>
                            <tr>
                                <th>Form <?php echo $i; ?></th>
                                <td>
                                    <?php $controls->textarea('form_' . $i); ?>
                                    <br />
                                    <?php $controls->button_save(); ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>

            </div>
        </form>

    </div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

</div> 
