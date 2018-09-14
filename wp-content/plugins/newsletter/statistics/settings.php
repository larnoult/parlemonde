<?php
if (!defined('ABSPATH')) exit;

require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$module = NewsletterStatistics::instance();
$controls = new NewsletterControls();

if (!$controls->is_action()) {
    $controls->data = $module->options;
}

do_action('newsletter_statistics_settings_init', $controls);

if ($controls->is_action('save')) {
    $module->save_options($controls->data);
    $controls->add_message_saved();
}
?>

<div class="wrap" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">

        <h2><?php _e('Statistics Settings', 'newsletter') ?></h2>

    </div>

    <div id="tnp-body">

        <form method="post" action="">
            <?php $controls->init(); ?>


            <div id="tabs">
                <ul>
                    <li><a href="#tab-configuration"><?php _e('Configuration', 'newsletter') ?></a></li>
                    <li><a href="#tab-countries"><?php _e('Countries', 'newsletter') ?></a></li>
                </ul>


                <div id="tab-configuration">
                    <table class="form-table">
                        <tr>
                            <th><?php _e('Secret key', 'newsletter') ?></th>
                            <td>
                                <?php $controls->text('key') ?>
                                <p class="description">
                                    <?php _e('This auto-generated key is used to protect the click tracking. If you change it old tracking links to external domains won\'t be registered anymore.', 'newsletter-statistics') ?> 
                                </p>
                            </td>
                        </tr>        
                    </table>
                    <p>
                        <?php $controls->button_save() ?>
                    </p>
                </div>

                <div id="tab-countries">
                    <?php
                    if (!has_action('newsletter_statistics_settings_countries')) {
                        ?>
                        <p>This panel contains information about country detection added by 
                            <a href="https://www.thenewsletterplugin.com/plugins/newsletter/reports-module" target="_blank">Reports Extension</a>.</p>
                        <?php
                    } else {
                        do_action('newsletter_statistics_settings_countries', $controls);
                    }
                    ?>
                </div>
            </div>
        </form>
    </div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php' ?>
</div>
