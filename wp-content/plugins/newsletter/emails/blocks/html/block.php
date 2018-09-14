<?php
/*
 * Name: Html
 * Section: content
 * Description: Free HTML block
 * 
 */

/* @var $options array */
/* @var $wpdb wpdb */

$default_options = array(
    'html'=>'<p>My <strong>HTML</strong> code<p>'
);

$options = array_merge($default_options, $options);

?>

<table width="100%" border="0" cellpadding="0" align="center" cellspacing="0">
    <tr>
        <td width="100%" valign="top" align="center">
            <?php echo $options['html'] ?>
        </td>
    </tr>
</table>

