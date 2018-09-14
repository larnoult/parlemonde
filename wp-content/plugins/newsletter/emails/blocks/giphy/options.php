<?php
/*
 * @var $options array contains all the options the current block we're ediging contains
 * @var $controls NewsletterControls 
 */
?>

<table class="form-table">
    <tr>
        <th><?php _e('Search Giphy', 'newsletter') ?></th>
        <td>
            <?php $controls->text('q') ?>
        </td>
    </tr>
    <tr>
        <th><?php _e('Choose', 'newsletter') ?></th>
        <td>
            <div style="clear: both" id="tnp-giphy-results">Write something in the search above</div>
        </td>
    </tr>
    <tr>
        <th><?php _e('Selected', 'newsletter') ?></th>
        <td>
            <?php $controls->text('giphy_url') ?>
        </td>
    </tr>
    <tr>
        <th>&nbsp;</th>
        <td>
            <div id="giphy-preview"></div>
        </td>
    </tr>
    <tr>
        <th><?php _e('Background', 'newsletter') ?></th>
        <td>
            <?php $controls->block_background() ?>
        </td>
    </tr>
    <tr>
        <th><?php _e('Padding', 'newsletter') ?></th>
        <td>
            <?php $controls->block_padding() ?>
        </td>
    </tr>
</table>

<script type="text/javascript">

    function choose_gif(url) {
        jQuery("#tnp-giphy-results").html("");
        jQuery("#options-giphy_url").val(url);
        jQuery("#giphy-preview").html('<img src="' + url + '" />');
    }

    jQuery("#options-q").keyup(
            function () {
                if (typeof(tid) != "undefined") {
                    window.clearTimeout(tid);
                }
                tid = window.setTimeout(function () {
                            jQuery.get("https://api.giphy.com/v1/gifs/search", {api_key: "57FLbVJJd7oQBZ0fEiRnzhM2VtZp5OP1", q: jQuery("#options-q").val()}, function (data) {
                                jQuery("#tnp-giphy-results").html("");
                                jQuery.each(data.data, function (index, value) {
                                    jQuery("#tnp-giphy-results").append('<img src="' + value.images.fixed_width_small.url + '" onclick="choose_gif(\'' + value.images.fixed_height.url + '\')" style="float:left;" />');
                                });
                            }, "json");
                        }, 500);
            });

</script>
