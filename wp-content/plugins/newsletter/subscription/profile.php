<?php
if (!defined('ABSPATH'))
    exit;

@include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();
$module = NewsletterSubscription::instance();

$current_language = $module->get_current_language();

$is_all_languages = $module->is_all_languages();

if (!$is_all_languages) {
    $controls->warnings[] = 'You are configuring the language "<strong>' . $current_language . '</strong>". Switch to "all languages" to see every options.';
}

if (!$controls->is_action()) {
    $controls->data = $module->get_options('profile', $current_language);
} else {
    if ($controls->is_action('save')) {
        $module->save_options($controls->data, 'profile', null, $current_language);
        $controls->add_message_saved();
    }

    if ($controls->is_action('reset')) {
        $controls->data = $module->reset_options('profile');
        $controls->add_message_done();
    }
}

$status = array(0 => __('Private', 'newsletter'), 1 => __('Show on profile page', 'newsletter'), 2 => __('Show on subscription form', 'newsletter'));
$rules = array(0 => __('Optional', 'newsletter'), 1 => __('Required', 'newsletter'));
?>

<div class="wrap" id="tnp-wrap">

    <?php $help_url = 'https://www.thenewsletterplugin.com/plugins/newsletter/subscription-module'; ?>

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">

        <h2><?php _e('Subscription Form Fields and Layout', 'newsletter') ?></h2>
        <p>
            This panel contains the configuration of the subscription and profile editing forms which collect the subscriber data you want to have.<br>
            And let you to <strong>translate</strong> every single button and label.<br>
            <strong>Preferences</strong> can be an important setting for your newsletter: <a href="https://www.thenewsletterplugin.com/plugins/newsletter/newsletter-preferences" target="_blank">here you can read more about them</a>.
        </p>

    </div>

    <div id="tnp-body">  

        <form action="" method="post">
            <?php $controls->init(); ?>

            <div id="tabs">

                <ul>
                    <li><a href="#tabs-2">Main profile fields</a></li>
                    <li><a href="#tabs-3">Extra profile fields</a></li>
                    <li><a href="#tabs-5">Form code</a></li>
                </ul>

                <div id="tabs-2">

                    <p>The main subscriber fields. Only the email field is, of course, mandatory.</p>

                    <table class="form-table">
                        <tr>
                            <th>Email</th>
                            <td>
                                <table class="newsletter-option-grid">
                                    <tr><th>Field label</th><td><?php $controls->text('email', 50); ?></td></tr>
                                    <tr><th>Error message</th><td><?php $controls->text('email_error', 50); ?></td></tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('First name', 'newsletter') ?></th>
                            <td>
                                <table class="newsletter-option-grid">
                                    <tr><th>Field label</th><td><?php $controls->text('name', 50); ?></td></tr>
                                    <?php if ($is_all_languages) { ?>
                                    <tr><th>When to show</th><td><?php $controls->select('name_status', $status); ?></td></tr>
                                    <tr><th>Rules</th><td><?php $controls->select('name_rules', $rules); ?></td></tr>
                                    <?php } ?>
                                    <tr><th>Error message</th><td><?php $controls->text('name_error', 50); ?></td></tr>
                                </table>
                                <p class="description">
                                    If you want to collect only a generic "name", use only this field and not the
                                    last name field.
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Last name', 'newsletter') ?></th>
                            <td>
                                <table class="newsletter-option-grid">
                                    <tr><th>Field label</th><td><?php $controls->text('surname', 50); ?></td></tr>
                                    <?php if ($is_all_languages) { ?>
                                    <tr><th>When to show</th><td><?php $controls->select('surname_status', $status); ?></td></tr>
                                    <tr><th>Rules</th><td><?php $controls->select('surname_rules', $rules); ?></td></tr>
                                    <?php } ?>
                                    <tr><th>Error message</th><td><?php $controls->text('surname_error', 50); ?></td></tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Gender', 'newsletter') ?></th>
                            <td>
                                <table class="newsletter-option-grid">
                                    <tr><th>Field label</th><td><?php $controls->text('sex', 50); ?></td></tr>
                                    <?php if ($is_all_languages) { ?>
                                    <tr><th>When to show</th><td><?php $controls->select('sex_status', $status); ?></td></tr>
                                    <tr><th>Rules</th><td><?php $controls->select('sex_rules', $rules); ?></td></tr>
                                    <?php } ?>
                                    <tr><th>Value labels</th><td>
                                            female: <?php $controls->text('sex_female'); ?>
                                            male: <?php $controls->text('sex_male'); ?>
                                            not specified: <?php $controls->text('sex_none'); ?>
                                        </td></tr>
                                    

                                    <tr><th>Salutation titles</th><td>

                                            for males: <?php $controls->text('title_male'); ?> (ex. "Mr")<br>
                                            for females: <?php $controls->text('title_female'); ?> (ex. "Mrs")<br>
                                            for others: <?php $controls->text('title_none'); ?>
                                        </td></tr>
                                </table>
                                <p class="description">
                                    Salutation titles are inserted in emails message when the tag {title} is used. For example
                                    "Good morning {title} {surname} {name}".
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th><?php _e('"Subscribe" label', 'newsletter') ?></th>
                            <td>
                                <?php $controls->text('subscribe', 40); ?>

                                <p class="description">
                                    You can use an image URL (http://...).
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th>Privacy checkbox/notice</th>
                            <td>
                                <table class="newsletter-option-grid">
                                    <?php if ($is_all_languages) { ?>
                                    <tr><th>Enabled?</th><td><?php $controls->select('privacy_status', array(0 => 'No', 1 => 'Yes', 2 => 'Only the notice')); ?></td></tr>
                                    <?php } ?>
                                    <tr><th>Label</th><td><?php $controls->text('privacy', 50); ?></td></tr>
                                    <tr><th>Privacy URL</th><td>
                                            <?php if (function_exists('get_privacy_policy_url') && get_privacy_policy_url()) { ?>
                                                <?php $controls->checkbox('privacy_use_wp_url', __('User WordPress privacy URL', 'newsletter')); ?>
                                                (<a href="<?php echo esc_attr(get_privacy_policy_url()) ?>"><?php echo esc_html(get_privacy_policy_url()) ?></a>)
                                                <br>OR<br>
                                            <?php } ?>

                                            <?php $controls->text_url('privacy_url', 50); ?>
                                        </td></tr>
                                    <tr><th>Error message</th><td><?php $controls->text('privacy_error', 50); ?></td></tr>
                                </table>
                                <p class="description">
                                    The privacy acceptance checkbox (required in many Europen countries) force the subscriber to
                                    check it before proceeding. If an URL is specified the label become a link.
                                </p>
                            </td>
                        </tr>

                    </table>
                </div>


                <div id="tabs-3">
                    <p>
                        Generic textual profile fields that can be collected during the subscription. Field formats can be one line text
                        or selection list. Fields of type "list" must be configured with a set of options, comma separated
                        like: "first option, second option, third option".
                    </p>
                    <p>
                        The placeholder works only on HTML 5 compliant browsers.
                    </p>

                    <table class="form-table">
                        <tr>
                            <th>Error message</th>
                            <td>
                                <?php $controls->text('profile_error', 50); ?>
                            </td>
                        </tr>
                    </table>

                    <table class="widefat">
                        <thead>
                            <tr>
                                <th>Field</th><th>Name/Label</th><th>Placeholder</th><th>When/Where</th><th>Type</th><th>Rule</th><th>List values comma separated</th>
                            </tr>
                        </thead>
                        <?php for ($i = 1; $i <= NEWSLETTER_PROFILE_MAX; $i++) { ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td><?php $controls->text('profile_' . $i); ?></td>
                                <td><?php $controls->text('profile_' . $i . '_placeholder'); ?></td>
                                <?php if ($is_all_languages) { ?>
                                <td><?php $controls->select('profile_' . $i . '_status', $status); ?></td>
                                <td><?php $controls->select('profile_' . $i . '_type', array('text' => 'Text', 'select' => 'List')); ?></td>
                                <td><?php $controls->select('profile_' . $i . '_rules', $rules); ?></td>
                                <?php } ?>
                                <td>
                                    <?php $controls->textarea_fixed('profile_' . $i . '_options', '200px', '50px'); ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>

                </div>

                <div id="tabs-5">

                    <p>This panel shows the form HTML code generated by Newsletter if you want to copy it as starting point for a custom form.</p>

                    <h3>Standard form code</h3>
                    <textarea readonly style="width: 100%; height: 500px; font-family: monospace"><?php echo esc_html(NewsletterSubscription::instance()->get_subscription_form()); ?></textarea>

                    <h3>Widget form code</h3>
                    <textarea readonly style="width: 100%; height: 500px; font-family: monospace"><?php echo htmlspecialchars(NewsletterSubscription::instance()->get_subscription_form()); ?></textarea>

                </div>

            </div>

            <p>
                <?php $controls->button_save(); ?>
                <?php $controls->button_reset(); ?>
            </p>

        </form>
    </div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

</div>
