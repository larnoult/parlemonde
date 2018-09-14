<?php
/*
 * This is a pre packaged theme options page. Every option name
 * must start with "theme_" so Newsletter can distinguish them from other
 * options that are specific to the object using the theme.
 *
 * An array of theme default options should always be present and that default options
 * should be merged with the current complete set of options as shown below.
 *
 * Every theme can define its own set of options, the will be used in the theme.php
 * file while composing the email body. Newsletter knows nothing about theme options
 * (other than saving them) and does not use or relies on any of them.
 *
 * For multilanguage purpose you can actually check the constants "WP_LANG", until
 * a decent system will be implemented.
 */

/* @var $controls NewsletterControls */

defined('ABSPATH') || exit;
?>
<div id="tabs">
    <ul>
        <li><a href="#tabs-a"><?php _e('General', 'newsletter') ?></a></li>
        <li><a href="#tabs-b"><?php _e('Social', 'newsletter') ?></a></li>
    </ul>


    <div id="tabs-a">
        <table class="form-table">
            <tr>
                <th>Max new posts to include</th>
                <td>
                    <?php $controls->select_number('theme_max_posts', 1, 50); ?>
                </td>
            </tr>
            <tr>
                <th>Categories to include</th>
                <td><?php $controls->categories_group('theme_categories'); ?></td>
            </tr>
            <tr>
                <th>Post types</th>
                <td>
                    <?php $controls->post_types('theme_post_types'); ?>
                    <p class="description">Leave all uncheck for a default behavior.</p>
                </td>
            </tr>
            <tr>
                <th>Pre header message</th>
                <td>
                    <?php $controls->textarea_fixed('theme_pre_message', '100%', 120); ?>
                </td>
            </tr>
            <tr>
                <th>Footer message</th>
                <td>
                    <?php $controls->textarea_fixed('theme_footer_message', '100%', 120); ?>
                </td>
            </tr>
            <tr>
                <th>Read more label</th>
                <td>
                    <?php $controls->text('theme_read_more'); ?>
                </td>
            </tr>
        </table>
    </div>
    <div id="tabs-b">
        <?php include WP_PLUGIN_DIR . '/newsletter/emails/themes/default/social-options.php'; ?>
    </div>
</div>
