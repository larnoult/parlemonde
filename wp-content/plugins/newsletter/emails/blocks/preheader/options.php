<?php
/* 
 * @var $options array contains all the options the current block we're ediging contains
 * @var $controls NewsletterControls 
 */
?>

<table class="form-table">
    <tr>
        <th><?php _e('Text', 'newsletter') ?></th>
        <td>
            <?php $controls->text('text', 70) ?>
        </td>
    </tr>
    <tr>
        <th><?php _e('View', 'newsletter') ?></th>
        <td>
            <?php $controls->text('view', 70) ?>
        </td>
    </tr>
            <tr>
        <th><?php _e('Background', 'newsletter')?></th>
        <td>
            <?php $controls->color('block_background') ?>
        </td>
    </tr>
</table>
