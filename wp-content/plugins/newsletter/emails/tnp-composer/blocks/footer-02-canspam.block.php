

<table width="100%" style="width: 100%!important" border="0" cellspacing="0" cellpadding="0" align="center" class="responsive-table">
    <tr>
        <td align="center" style="padding: 20px 15px 20px 15px; font-size: 12px; line-height: 18px; font-family: Helvetica, Arial, sans-serif; color:#666666;">
            <div class="tnpc-row-edit" data-type="text" style="color:#666666;">
                <?php echo!empty($block_options['footer_title']) ? $block_options['footer_title'] : 'Your Company' ?>
                <br/>
                <?php echo!empty($block_options['footer_contact']) ? $block_options['footer_contact'] : 'Company Address, Phone Number' ?>
                <br/>
                <em><?php echo!empty($block_options['footer_legal']) ? $block_options['footer_legal'] : 'Copyright or Legal text' ?></em>
            </div>
        </td>
    </tr>
</table>

