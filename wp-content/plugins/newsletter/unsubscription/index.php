<?php
defined('ABSPATH') || exit;

@include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();
$module = NewsletterUnsubscription::instance();

$current_language = $module->get_current_language();

$is_all_languages = $module->is_all_languages();

if (!$is_all_languages) {
    $controls->warnings[] = 'You are configuring the language <strong>' . $current_language . '</strong>. Switch to "all languages" to see every options.';
}

if (!$controls->is_action()) {
    $controls->data = $module->get_options('', $current_language);
} else {
    if ($controls->is_action('save')) {
        //$controls->data['unsubscription_text'] = NewsletterModule::clean_url_tags($controls->data['unsubscription_text']);
        //$controls->data['unsubscribed_text'] = NewsletterModule::clean_url_tags($controls->data['unsubscribed_text']);
        //$controls->data['unsubscribed_message'] = NewsletterModule::clean_url_tags($controls->data['unsubscribed_message']);

        $module->save_options($controls->data, '', null, $current_language);
        $controls->data = $module->get_options('', $current_language);
        $controls->add_message_saved();
    }

    if ($controls->is_action('reset')) {
        // On reset we ignore the current language
        $controls->data = $module->reset_options();
    }
}
?>

<div class="wrap" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">

        <h2><?php _e('Cancellation', 'newsletter')?></h2>
        <?php $controls->panel_help('https://www.thenewsletterplugin.com/documentation/cancellation')?>

    </div>

    <div id="tnp-body"> 

        <form method="post" action="">
            <?php $controls->init(); ?>
             <p>
                <?php $controls->button_save() ?>
                <?php $controls->button_reset() ?>
            </p>
            <div id="tabs">
                <ul>
                    <li><a href="#tabs-cancellation"><?php _e('Cancellation', 'newsletter') ?></a></li>
                    <li><a href="#tabs-reactivation"><?php _e('Reactivation', 'newsletter') ?></a></li>

                </ul>
                <div id="tabs-cancellation">
                    <table class="form-table">
                        <tr>
                            <th><?php _e('Cancellation message', 'newsletter') ?></th>
                            <td>
                                <?php $controls->wp_editor('unsubscribe_text', array('editor_height'=>250)); ?>
                                <p class="description">
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th><?php _e('Goodbye message', 'newsletter') ?></th>
                            <td>
                                <?php $controls->wp_editor('unsubscribed_text', array('editor_height'=>250)); ?>
                                <p class="description">
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th><?php _e('Goodbye email', 'newsletter') ?></th>
                            <td>
                                <?php $controls->email('unsubscribed', 'wordpress', true, array('editor_height'=>250)); ?>
                                <p class="description">

                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('On error', 'newsletter')?></th>
                            <td>
                                <?php $controls->wp_editor('error_text', array('editor_height'=>150)); ?>
                                <p class="description">
                                   
                                </p>
                            </td>
                        </tr>                       
                    </table>
                </div>
                
                <div id="tabs-reactivation">
                    <table class="form-table">
                        <tr>
                            <th><?php _e('Reactivated message', 'newsletter') ?></th>
                            <td>
                                <?php $controls->wp_editor('reactivated_text', array('editor_height'=>250)); ?>
                                <p class="description">
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>
                <?php $controls->button_save() ?>
                <?php $controls->button_reset() ?>
            </p>
        </form>
    </div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

</div>