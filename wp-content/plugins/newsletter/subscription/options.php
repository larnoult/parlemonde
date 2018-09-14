<?php
defined('ABSPATH') || exit;

@include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();
$module = NewsletterSubscription::instance();

$current_language = $module->get_current_language();

$is_all_languages = $module->is_all_languages();

$controls->add_language_warning();

$options = $module->get_options('', $current_language);

if ($controls->is_action()) {
    if ($controls->is_action('save')) {

        $defaults = $module->get_default_options();

        // Without the last curly bracket since there can be a form number apended
        if (empty($controls->data['subscription_text'])) {
            $controls->data['subscription_text'] = $defaults['subscription_text'];
        }

        if (empty($controls->data['confirmation_text'])) {
            $controls->data['confirmation_text'] = $defaults['confirmation_text'];
        }

        if (empty($controls->data['confirmation_subject'])) {
            $controls->data['confirmation_subject'] = $defaults['confirmation_subject'];
        }

        if (empty($controls->data['confirmation_message'])) {
            $controls->data['confirmation_message'] = $defaults['confirmation_message'];
        }

        if (empty($controls->data['confirmed_text'])) {
            $controls->data['confirmed_text'] = $defaults['confirmed_text'];
        }

        if (empty($controls->data['confirmed_subject'])) {
            $controls->data['confirmed_subject'] = $defaults['confirmed_subject'];
        }

        if (empty($controls->data['confirmed_message'])) {
            $controls->data['confirmed_message'] = $defaults['confirmed_message'];
        }

        $controls->data['confirmed_message'] = NewsletterModule::clean_url_tags($controls->data['confirmed_message']);
        $controls->data['confirmed_text'] = NewsletterModule::clean_url_tags($controls->data['confirmed_text']);
        $controls->data['confirmation_text'] = NewsletterModule::clean_url_tags($controls->data['confirmation_text']);
        $controls->data['confirmation_message'] = NewsletterModule::clean_url_tags($controls->data['confirmation_message']);

        $controls->data['confirmed_url'] = trim($controls->data['confirmed_url']);
        $controls->data['confirmation_url'] = trim($controls->data['confirmation_url']);

        $module->save_options($controls->data, '', null, $current_language);
        $controls->add_message_saved();
    }

    if ($controls->is_action('reset')) {
        $controls->data = $module->reset_options();
    }

    if ($controls->is_action('test-confirmation')) {

        $users = NewsletterUsers::instance()->get_test_users();
        if (count($users) == 0) {
            $controls->errors = 'There are no test subscribers. Read more about test subscribers <a href="https://www.thenewsletterplugin.com/plugins/newsletter/subscribers-module#test" target="_blank">here</a>.';
        } else {
            $addresses = array();
            foreach ($users as &$user) {
                $addresses[] = $user->email;
                $res = $module->mail($user->email, $module->replace($module->options['confirmation_subject']), $module->replace($module->options['confirmation_message'], $user));
                if (!$res) {
                    $controls->errors = 'The email address ' . $user->email . ' failed.';
                    break;
                }
            }
            $controls->messages .= 'Test emails sent to ' . count($users) . ' test subscribers: ' .
                    implode(', ', $addresses) . '. Read more about test subscribers <a href="https://www.thenewsletterplugin.com/plugins/newsletter/subscribers-module#test" target="_blank">here</a>.';
            $controls->messages .= '<br>If the message is not received, try to change the message text it could trigger some antispam filters.';
        }
    }

    if ($controls->is_action('test-confirmed')) {

        $users = NewsletterUsers::instance()->get_test_users();
        if (count($users) == 0) {
            $controls->errors = 'There are no test subscribers. Read more about test subscribers <a href="https://www.thenewsletterplugin.com/plugins/newsletter/subscribers-module#test" target="_blank">here</a>.';
        } else {
            $addresses = array();
            foreach ($users as $user) {
                $addresses[] = $user->email;
                $res = $module->mail($user->email, $module->replace($module->options['confirmed_subject']), $module->replace($module->options['confirmed_message'], $user));
                if (!$res) {
                    $controls->errors = 'The email address ' . $user->email . ' failed.';
                    break;
                }
            }
            $controls->messages .= 'Test emails sent to ' . count($users) . ' test subscribers: ' .
                    implode(', ', $addresses) . '. Read more about test subscribers <a href="https://www.thenewsletterplugin.com/plugins/newsletter/subscribers-module#test" target="_blank">here</a>.';
            $controls->messages .= '<br>If the message is not received, try to change the message text it could trigger some antispam filters.';
        }
    }
} else {
    $controls->data = $module->get_options('', $current_language);
}

?>

<div class="wrap" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">

        <h2><?php _e('Subscription Configuration', 'newsletter') ?></h2>
        <?php $controls->page_help('https://www.thenewsletterplugin.com/documentation/subscription') ?>

    </div>

    <div id="tnp-body">
        

        <form method="post" action="">
            <?php $controls->init(); ?>
            <div id="tabs">
                <ul>
                    <li><a href="#tabs-general"><?php _e('General', 'newsletter') ?></a></li>
                    <li><a href="#tabs-2"><?php _e('Subscription', 'newsletter') ?></a></li>
                    <li><a href="#tabs-4"><?php _e('Welcome', 'newsletter') ?></a></li>
                    <li><a href="#tabs-3"><?php _e('Activation', 'newsletter') ?></a></li>
                </ul>

                <div id="tabs-general">
                    <?php if ($is_all_languages) { ?>
                    <table class="form-table">
                        
                        <tr>
                            <th><?php _e('Opt In', 'newsletter') ?></th>
                            <td>
                                <?php $controls->select('noconfirmation', array(0 => __('Double Opt In', 'newsletter'), 1 => __('Single Opt In', 'newsletter'))); ?>
                                <?php $controls->help('https://www.thenewsletterplugin.com/documentation/subscription#opt-in') ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Override Opt In', 'newsletter') ?></th>
                            <td>
                                <?php $controls->yesno('optin_override'); ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Repeated subscriptions', 'newsletter') ?></th>
                            <td>
                                <?php //$controls->select('multiple', array('0'=>__('No', 'newsletter'), '1'=>__('Yes', 'newsletter'), '2'=>__('On new lists added', 'newsletter'))); ?> 
                                <?php $controls->select('multiple', array('0'=>__('No', 'newsletter'), '1'=>__('Yes', 'newsletter'))); ?> 
                                <?php $controls->help('https://www.thenewsletterplugin.com/documentation/subscription#repeated')?>
                            </td>
                        </tr>
                        
                        <tr>
                            <th><?php _e('Notifications', 'newsletter') ?></th>
                            <td>
                                <?php $controls->yesno('notify'); ?>
                                <?php $controls->text_email('notify_email'); ?>
                            </td>
                        </tr>
                    </table>
                    <?php } else { ?>
                    <p>Switch to your main language to manage these options.</p>
                    <?php } ?>
                    
                </div>


                <div id="tabs-2">

                    <table class="form-table">
                        <tr>
                            <th><?php _e('Subscription page', 'newsletter') ?><br><?php echo $controls->help('https://www.thenewsletterplugin.com/documentation/newsletter-tags') ?></th>
                            <td>
                                <?php $controls->wp_editor('subscription_text'); ?>
                            </td>
                        </tr>
                        
                    </table>

                    <h3>Special cases</h3>

                    <table class="form-table">
                        <tr>
                            <th><?php _e('Error page', 'newsletter') ?></th>
                            <td>
                                <?php $controls->wp_editor('error_text'); ?>
                            </td>
                        </tr>
                    </table>
                </div>


                <div id="tabs-3">

                    <p><?php _e('Only for double opt-in mode.', 'newsletter') ?></p>
                    <?php $controls->panel_help('https://www.thenewsletterplugin.com/documentation/subscription#activation') ?>

                    <table class="form-table">
                        <tr>
                            <th><?php _e('Activation message', 'newsletter') ?></th>
                            <td>
                                <?php $controls->wp_editor('confirmation_text'); ?>
                            </td>
                        </tr>

                        <tr>
                            <th><?php _e('Alternative activation page', 'newsletter'); ?></th>
                            <td>
                                <?php $controls->text('confirmation_url', 70, 'https://...'); ?>
                            </td>
                        </tr>


                        <!-- CONFIRMATION EMAIL -->
                        <tr>
                            <th><?php _e('Activation email', 'newsletter') ?></th>
                            <td>
                                <?php $controls->email('confirmation', 'wordpress'); ?>
                                <br>
                                <?php $controls->button('test-confirmation', 'Send a test'); ?>
                            </td>
                        </tr>
                    </table>
                </div>


                <div id="tabs-4">
                    <p>
                        <?php $controls->panel_help('https://www.thenewsletterplugin.com/documentation/subscription#welcome') ?>
                    </p>
                    <table class="form-table">
                        <tr>
                            <th><?php _e('Welcome message', 'newsletter') ?></th>
                            <td>
                                <?php $controls->wp_editor('confirmed_text'); ?>
                            </td>
                        </tr>

                        <tr>
                            <th><?php _e('Alternative welcome page URL', 'newsletter') ?></th>
                            <td>
                                <?php $controls->text('confirmed_url', 70, 'https://...'); ?>
                            </td>
                        </tr>

                        <tr>
                            <th><?php _e('Conversion tracking code', 'newsletter') ?>
                                <?php $controls->help('https://www.thenewsletterplugin.com/documentation/subscription#conversion') ?></th>
                            <td>
                                <?php $controls->textarea('confirmed_tracking'); ?>
                            </td>
                        </tr>

                        <!-- WELCOME/CONFIRMED EMAIL -->
                        <tr>
                            <th>
                                <?php _e('Welcome email', 'newsletter') ?>
                            </th>
                            <td>
                                <?php $controls->email('confirmed', 'wordpress', true); ?>
                                <br>
                                <?php $controls->button('test-confirmed', 'Send a test'); ?>
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
