<?php
/*
 * @var $options array contains all the options the current block we're ediging contains
 * @var $controls NewsletterControls 
 */
?>

<table class="form-table">
    <tr>
        <th>Text</th>
        <td>
            <?php $controls->text('text') ?>
        </td>
    </tr>
    <tr>
        <th>Button color</th>
        <td>
            <?php $controls->color('background') ?>
        </td>
    </tr>
    <tr>
        <th><?php _e('Color', 'newsletter') ?></th>
        <td>
            <?php $controls->color('color') ?>
        </td>
    </tr>
    <tr>
        <th>Link to</th>
        <td>
            <?php $controls->text('url', 50, 'https://...') ?>
        </td>
    </tr>
    <tr>
        <th>Font size</th>
        <td>
            <?php $controls->css_font_size('font_size') ?>
        </td>
    </tr>
    <tr>
        <th>Font family</th>
        <td>
            <?php $controls->css_font_family('font_family') ?>
        </td>
    </tr>

    <tr>
        <th><?php _e('Background', 'newsletter') ?></th>
        <td>
            <?php $controls->color('block_background') ?>
        </td>
    </tr>
</table>
