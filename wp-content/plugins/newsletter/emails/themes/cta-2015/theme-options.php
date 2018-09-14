<?php
if (!defined('ABSPATH')) exit;
?>
<table class="form-table">
    <tr>
        <th><?php _e('Primary color', 'newsletter') ?></th>
        <td>
            <?php $controls->color('theme_color'); ?> (eg. #87aa14)
        </td>
    </tr>
</table>