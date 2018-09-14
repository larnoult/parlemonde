<?php
/*
 * Name: Preheader
 * Section: header
 * Description: Preheader
 * 
 */

/* @var $options array */
/* @var $wpdb wpdb */

$default_options = array(
    'view'=>'View online',
    'text'=>'Few words summary',
    'block_background'=>'#ffffff',
    'font_family'=>$font_family,
    'font_size'=>13,
    'color'=>'#999999'
);

$options = array_merge($default_options, $options);

?>
<style>
    .preheader-link {
        padding: 20px; 
        text-align: center; 
        font-size: <?php echo $options['font_size'] ?>px; 
        font-family: <?php echo $options['font_family'] ?>; 
        color: <?php echo $options['color'] ?>;
    }
</style>

<table width="100%" border="0" cellpadding="0" align="center" cellspacing="0">
    <tr>
        <td class="preheader-link" width="50%" valign="top" align="center">
            <?php echo $options['text'] ?>
        </td>
        <td class="preheader-link" width="50%" valign="top" align="center">
            <a href="{email_url}" target="_blank" rel="noopener" style="text-decoration: none; font-size: <?php echo $options['font_size'] ?>px; font-family: <?php echo $options['font_family'] ?>; color: <?php echo $options['color'] ?>"><?php echo $options['view'] ?></a>
        </td>
    </tr>
</table>

