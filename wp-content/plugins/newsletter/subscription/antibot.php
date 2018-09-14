<?php
defined('ABSPATH') || exit;

@include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();
$module = NewsletterSubscription::instance();

// TODO: Remove and use the $module->options.
$options = get_option('newsletter', array());

if ($controls->is_action()) {

    if ($controls->is_action('save')) {

        $blacklist = trim($controls->data['ip_blacklist']);
        if (empty($blacklist))
            $blacklist = array();
        else {
            $blacklist = preg_split("/\\r\\n/", $blacklist);
            $blacklist = array_map('trim', $blacklist);
            $blacklist = array_map('strtolower', $blacklist);
            $blacklist = array_filter($blacklist);

            $controls->data['ip_blacklist'] = $blacklist;
        }

        $blacklist = trim($controls->data['address_blacklist']);
        if (empty($blacklist))
            $blacklist = array();
        else {
            $blacklist = preg_split("/\\r\\n/", $blacklist);
            $blacklist = array_map('trim', $blacklist);
            $blacklist = array_map('strtolower', $blacklist);
            $blacklist = array_filter($blacklist);

            $controls->data['address_blacklist'] = $blacklist;
        }

        $module->merge_options($controls->data);
        $controls->add_message_saved();
    }
} else {
    $controls->data = get_option('newsletter', array());
}
?>

<div class="wrap" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">

        <h2><?php _e('Security', 'newsletter') ?></h2>
        <?php $controls->page_help('https://www.thenewsletterplugin.com/documentation/antiflood') ?>

    </div>

    <div id="tnp-body">

        <form method="post" action="">
            <?php $controls->init(); ?>


            <p>
                <?php $controls->button_save() ?>
            </p>

            <div id="tabs">
                <ul>
                    <li><a href="#tabs-general"><?php _e('Security', 'newsletter') ?></a></li>
                    <li><a href="#tabs-blacklists"><?php _e('Blacklists', 'newsletter') ?></a></li>
                </ul>

                <div id="tabs-general">


                    <table class="form-table">
                        <tr>
                            <th><?php _e('Disable antibot/antispam?', 'newsletter') ?></th>
                            <td>
                                <?php $controls->yesno('antibot_disable'); ?>
                                <p class="description">
                                    <?php _e('Disable for ajax form submission', 'newsletter'); ?>
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th>Akismet</th>
                            <td>
                                <?php
                                $controls->select('akismet', array(
                                    0 => __('Disabled', 'newsletter'),
                                    1 => __('Enabled', 'newsletter')
                                ));
                                ?>
                                <?php $controls->help('https://www.thenewsletterplugin.com/documentation/antiflood') ?>
                            </td>
                        </tr>

                        <tr>
                            <th><?php _e('Antiflood', 'newsletter') ?></th>
                            <td>
                                <?php
                                $controls->select('antiflood', array(
                                    0 => __('Disabled', 'newsletter'),
                                    5 => '5 ' . __('seconds', 'newsletter'),
                                    10 => '10 ' . __('seconds', 'newsletter'),
                                    15 => '15 ' . __('seconds', 'newsletter'),
                                    30 => '30 ' . __('seconds', 'newsletter'),
                                    60 => '1 ' . __('minute', 'newsletter'),
                                    120 => '2 ' . __('minutes', 'newsletter'),
                                    300 => '5 ' . __('minutes', 'newsletter'),
                                    600 => '10 ' . __('minutes', 'newsletter'),
                                    900 => '15 ' . __('minutes', 'newsletter'),
                                    1800 => '30 ' . __('minutes', 'newsletter'),
                                    360 => '60 ' . __('minutes', 'newsletter')
                                ));
                                ?>
                                <?php $controls->help('https://www.thenewsletterplugin.com/documentation/antiflood') ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Captcha', 'newsletter') ?></th>
                            <td>
                                <?php
                                $controls->enabled('captcha');
                                ?>
                            </td>
                        </tr>
                        <?php /*
                          <tr>
                          <th><?php _e('Domain check', 'newsletter') ?></th>
                          <td>
                          <?php
                          $controls->yesno('domain_check');
                          ?>
                          </td>
                          </tr>
                         */ ?>

                    </table>


                </div>

                <div id="tabs-blacklists">
                    <table class="form-table">
                        <tr>
                            <th><?php _e('IP black list', 'newsletter') ?></th>
                            <td>
                                <?php
                                $controls->textarea('ip_blacklist');
                                ?>
                                <?php $controls->help('https://www.thenewsletterplugin.com/documentation/antiflood') ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Address black list', 'newsletter') ?></th>
                            <td>
                                <?php
                                $controls->textarea('address_blacklist');
                                ?>
                                <?php $controls->help('https://www.thenewsletterplugin.com/documentation/antiflood') ?>
                            </td>
                        </tr>
                    </table>
                </div>

            </div>

            <p>
                <?php $controls->button_save() ?>
            </p>

        </form>
    </div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

</div>
