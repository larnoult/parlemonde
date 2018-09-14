<?php
defined('ABSPATH') || exit;

@include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$module = Newsletter::instance();
$controls = new NewsletterControls();

if (!$controls->is_action()) {
    $controls->data = get_option('newsletter_main');
} else {

    if ($controls->is_action('save')) {
        $module->merge_options($controls->data);
        $module->save_options($controls->data, 'info');
        $controls->add_message_saved();
    }
}
?>

<div class="wrap" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">

        <h2><?php _e('Company Info', 'newsletter') ?></h2>

    </div>
    <div id="tnp-body">

        <form method="post" action="">
            <?php $controls->init(); ?>

            <div id="tabs">

                <ul>
                    <li><a href="#tabs-general"><?php _e('General', 'newsletter') ?></a></li>
                    <li><a href="#tabs-social"><?php _e('Social', 'newsletter') ?></a></li>
                </ul>

                <div id="tabs-general">
                    <h3><?php _e('Header Settings', 'newsletter') ?></h3>

                    <table class="form-table">
                        <tr>
                            <th>
                                <?php _e('Logo', 'newsletter') ?><br>
                                <?php $controls->help('https://www.thenewsletterplugin.com/documentation/newsletter-configuration#company-logo') ?>
                            </th>
                            <td style="cursor: pointer">
                                <?php $controls->media('header_logo', 'medium'); ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Title', 'newsletter') ?></th>
                            <td>
                                <?php $controls->text('header_title', 40); ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Motto', 'newsletter') ?></th>
                            <td>
                                <?php $controls->text('header_sub', 40); ?>
                            </td>
                        </tr>
                    </table>

                    <h3><?php _e('Footer Settings', 'newsletter') ?></h3>

                    <table class="form-table">
                        <tr>
                            <th><?php _e('Company name', 'newsletter') ?></th>
                            <td>
                                <?php $controls->text('footer_title', 40); ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Address', 'newsletter') ?></th>
                            <td>
                                <?php $controls->text('footer_contact', 40); ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Copyright or legal text', 'newsletter') ?></th>
                            <td>
                                <?php $controls->text('footer_legal', 40); ?>
                            </td>
                        </tr>
                    </table>                
                </div>

                <div id="tabs-social">

                    <table class="form-table">
                        <tr>
                            <th>Facebook URL</th>
                            <td>
                                <?php $controls->text('facebook_url', 40); ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Twitter URL</th>
                            <td>
                                <?php $controls->text('twitter_url', 40); ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Instagram URL</th>
                            <td>
                                <?php $controls->text('instagram_url', 40); ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Google+ URL</th>
                            <td>
                                <?php $controls->text('googleplus_url', 40); ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Pinterest URL</th>
                            <td>
                                <?php $controls->text('pinterest_url', 40); ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Linkedin URL</th>
                            <td>
                                <?php $controls->text('linkedin_url', 40); ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Tumblr URL</th>
                            <td>
                                <?php $controls->text('tumblr_url', 40); ?>
                            </td>
                        </tr>
                        <tr>
                            <th>YouTube URL</th>
                            <td>
                                <?php $controls->text('youtube_url', 40); ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Vimeo URL</th>
                            <td>
                                <?php $controls->text('vimeo_url', 40); ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Soundcloud URL</th>
                            <td>
                                <?php $controls->text('soundcloud_url', 40); ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>
                <?php $controls->button_save(); ?>
            </p>

        </form>
    </div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

</div>
