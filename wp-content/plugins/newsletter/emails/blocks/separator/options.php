<?php
/* 
 * @var $options array contains all the options the current block we're ediging contains
 * @var $controls NewsletterControls 
 */
?>

<table class="form-table">
   
    <tr>
        <th><?php _e('Color', 'newsletter')?></th>
        <td>
            <?php $controls->color('color') ?>
        </td>
    </tr>
    <tr>
        <th><?php _e('Height', 'newsletter')?></th>
        <td>
            <?php $controls->select('height', array('1'=>1, '2'=>2, '3'=>3, '4'=>4, '5'=>5)) ?>
        </td>
    </tr>
</table>
