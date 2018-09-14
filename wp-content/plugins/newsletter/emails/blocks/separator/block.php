<?php
/*
 * Name: Separator
 * Section: content
 * Description: Separator
 * 
 */

/* @var $options array */
/* @var $wpdb wpdb */

$default_options = array(
    'color'=>'#dddddd',
    'height'=>1
);

$options = array_merge($default_options, $options);

?>


<table border="0" cellpadding="0" align="center" cellspacing="0" width="100%">
    <tr>
        <td style="padding: 20px;">
            <div style="height: <?php echo $options['height'] ?>px!important; background-color: <?php echo $options['color'] ?>; border: 0; margin:0; padding: 0; line-height: 0; width: 100%!important; display: block;"></div>
        </td>
    </tr>
</table>
