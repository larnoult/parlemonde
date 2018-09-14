<?php
if (!empty($block_options['header_logo']['url'])) { 
    $logo_url = $block_options['header_logo']['url'];
} else {
    $logo_url = 'https://placehold.it/180x100&text=' . urlencode($block_options['header_title']);
}
$logo_alt = $block_options['header_title'];
$options['block_background'] = '#333333';
?>


<table border="0" cellpadding="0" align="center" cellspacing="0" width="100%">
    <tr>
        <td align="center" style="padding: 0px 15px 0px 15px;">
            
                <table border="0" cellpadding="0" cellspacing="0" width="500" class="wrapper">
                    <!-- LOGO/PREHEADER TEXT -->
                    <tr>
                        <td style="padding: 20px 0px 30px 0px;" class="logo">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td width="100" align="left" class="tnpc-row-edit" data-type="image">
                                        <a href="#" target="_blank">
                                            <img alt="<?php echo esc_attr($logo_alt) ?>" src="<?php echo $logo_url ?>" style="display: block; width: 180px;" border="0">
                                        </a>
                                    </td>
                                    <td width="400" align="right" class="mobile-hide">
                                        <table border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td align="right" style="padding: 0 0 5px 0; font-size: 14px; font-family: Arial, sans-serif; color: #666666; text-decoration: none;" class="tnpc-row-edit" data-type="text">
                                                    <span style="color: #666666; text-decoration: none;" ><?php echo !empty($block_options['header_sub']) ? $block_options['header_sub'] : 'A little text up top can be nice.<br>Maybe a link to tweet?' ?></span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
           
        </td>
    </tr>
</table>
