<?php
/*
 * Name: Giphy
 * Section: content
 * Description: Add a Giphy image
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
    'color'=>'#999999',
    'block_padding_top'=>15,
    'block_padding_bottom'=>15,
    'block_padding_left'=>0,
    'block_padding_right'=>0
);

$options = array_merge($default_options, $options);

?>

<table width="100%" border="0" cellpadding="0" align="center" cellspacing="0">
    <tr>
        <td width="100%" valign="top" align="center">
            <img src="<?php echo $options['giphy_url'] ?>" style="max-width: 100%!important; height: auto!important;" />
        </td>
    </tr>
</table>

