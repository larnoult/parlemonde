<?php
defined('ABSPATH') || exit;
?>

<table class="form-table">
    <tr><td colspan="2">General options for header, social links and footer sections could also be set in <a href="?page=newsletter_main_main">Blog Info panel</a>.</td></tr>
    <tr>
        <th><?php _e('Primary color', 'newsletter') ?></th>
        <td>
            <?php $controls->color('theme_color'); ?>
            <p class="description" style="display: inline">Hex values, e.g. #FF0000</p>
        </td>
    </tr>
    <tr>
        <th><?php _e('Disable social links', 'newsletter') ?></th>
        <td><?php $controls->checkbox('theme_social_disable', ''); ?></td>
    </tr>

</table>

<h3><?php _e('Posts', 'newsletter') ?></h3>
<table class="form-table">
    <tr>
        <th>Language</th>
        <td>
            <?php $controls->language(); ?>
        </td>
    </tr>
    <tr>
        <th>&nbsp;</th>
        <td>
            <?php $controls->checkbox('theme_posts', 'Add latest posts'); ?>
            <br>
            <?php $controls->checkbox('theme_thumbnails', 'Add post thumbnails'); ?>
            <br>
            <?php $controls->checkbox('theme_excerpts', 'Add post excerpts'); ?>
        </td>
    </tr>
    <tr>
        <th><?php _e('Categories', 'newsletter') ?></th>
        <td>
            <?php $controls->categories_group('theme_categories'); ?>
        </td>
    </tr>
    <tr>
        <th><?php _e('Tags', 'newsletter') ?></th>
        <td>
            <?php $controls->text('theme_tags', 30); ?>
            <p class="description" style="display: inline"> comma separated</p>
        </td>
    </tr>
    <tr>
        <th><?php _e('Max posts', 'newsletter') ?></th>
        <td>
            <?php $controls->text('theme_max_posts', 5); ?>
        </td>
    </tr>
    <tr>
        <th><?php _e('Post types', 'newsletter') ?></th>
        <td>
            <?php $controls->post_types('theme_post_types'); ?>
        </td>
    </tr>
</table>
