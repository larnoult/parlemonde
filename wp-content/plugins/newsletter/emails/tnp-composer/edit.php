<?php
if (!defined('ABSPATH')) exit;
?>
<div class="tnpc-edit" id="tnpc-edit-image">
    <div class="tnpc-edit-box">
        <div class="tnpc-edit-box-title"><?php _e("Edit Image", "newsletter") ?></div>
        <div class="tnpc-edit-box-content">
            <div class="tnpc-edit-box-content-text"><?php _e("SOURCE", "newsletter") ?> <span>(full image URL including http://)</span></div>
            <div class="tnpc-edit-box-content-field">
                <input type="text" class="tnpc-edit-box-content-field-input image"/>
                <input class="button select_image" value="Select or Upload Image" type="button">
            </div>
            <div class="tnpc-edit-box-content-text"><?php _e("ALT TEXT", "newsletter") ?><span>(optional but recommended)</span></div>
            <div class="tnpc-edit-box-content-field"><input type="text" class="tnpc-edit-box-content-field-input alt"/></div>
            <div class="tnpc-edit-box-content-text"><?php _e("LINK", "newsletter") ?> <span>(optional link address including http://)</span></div>
            <div class="tnpc-edit-box-content-field"><input type="text" class="tnpc-edit-box-content-field-input url"/></div>
        </div>
        <div class="tnpc-edit-box-buttons">
            <div class="tnpc-edit-box-buttons-save"><?php _e("Save", "newsletter") ?></div>
            <div class="tnpc-edit-box-buttons-cancel"><?php _e("Cancel", "newsletter") ?></div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var file_frame;
    jQuery('#tnpc-edit-image .select_image').live('click', function (event) {
        event.preventDefault();
        // If the media frame already exists, reopen it.
        if (file_frame) {
            file_frame.open();
            return;
        }
        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
            title: jQuery('#tnpc-edit-image .image').val(),
            button: {
                text: 'Insert',
            },
            multiple: false  // Set to true to allow multiple files to be selected
        });
        // When an image is selected, run a callback.
        file_frame.on('select', function () {
            // We set multiple to false so only get one image from the uploader
            attachment = file_frame.state().get('selection').first().toJSON();
            // Do something with attachment.id and/or attachment.url here
            // Patch for plugins which remove the protocol (not good for a newsletter...)
            if (attachment.url.substring(0, 0) == "/") {
                attachment.url = "<?php echo site_url('/') ?>" + attachment.url;
            }
            if (attachment.url.indexOf("http") !== 0) attachment.url = "http:" + attachment.url;
            jQuery('#tnpc-edit-image .image').val(attachment.url);
        });
        // Finally, open the modal
        file_frame.open();
    });
</script>

<div class="tnpc-edit" id="tnpc-edit-link">
    <div class="tnpc-edit-box">
        <div class="tnpc-edit-box-title"><?php _e("Edit Link", "newsletter") ?></div>

        <div class="tnpc-edit-box-content">
            <div class="tnpc-edit-box-content-text"><?php _e("TITLE", "newsletter") ?> </div>
            <div class="tnpc-edit-box-content-field"><input type="text" class="tnpc-edit-box-content-field-input title"/></div>
            <div class="tnpc-edit-box-content-text"><?php _e("URL", "newsletter") ?> <span>(full address including http://)</span></div>
            <div class="tnpc-edit-box-content-field"><input type="text" class="tnpc-edit-box-content-field-input url"/></div>
        </div>
        <div class="tnpc-edit-box-buttons">
            <div class="tnpc-edit-box-buttons-save"><?php _e("Save", "newsletter") ?></div>
            <div class="tnpc-edit-box-buttons-cancel"><?php _e("Cancel", "newsletter") ?></div>
        </div>
    </div>
</div>

<div class="tnpc-edit" id="tnpc-edit-button">
    <div class="tnpc-edit-box">
        <div class="tnpc-edit-box-title"><?php _e("Edit Button", "newsletter") ?></div>

        <div class="tnpc-edit-box-content">
            <div class="tnpc-edit-box-content-text"><?php _e("TITLE", "newsletter") ?> </div>
            <div class="tnpc-edit-box-content-field"><input type="text" class="tnpc-edit-box-content-field-input title"/></div>
            <div class="tnpc-edit-box-content-text"><?php _e("URL", "newsletter") ?> <span>(full address including http://)</span></div>
            <div class="tnpc-edit-box-content-field"><input type="text" class="tnpc-edit-box-content-field-input url"/></div>
            <div class="tnpc-edit-box-content-text"><?php _e("Text Color", "newsletter") ?></div>
            <div class="tnpc-edit-box-content-field"><input type="text" class="tnpc-edit-box-content-field-input fgcolor"/></div>
            <div class="tnpc-edit-box-content-text"><?php _e("Background Color", "newsletter") ?></div>
            <div class="tnpc-edit-box-content-field"><input type="text" class="tnpc-edit-box-content-field-input bgcolor"/></div>
        </div>
        <div class="tnpc-edit-box-buttons">
            <div class="tnpc-edit-box-buttons-save"><?php _e("Save", "newsletter") ?></div>
            <div class="tnpc-edit-box-buttons-cancel"><?php _e("Cancel", "newsletter") ?></div>
        </div>
    </div>
</div>

<div class="tnpc-edit" id="tnpc-edit-title">
    <div class="tnpc-edit-box">
        <div class="tnpc-edit-box-title"><?php _e("Edit Title", "newsletter") ?></div>

        <div class="tnpc-edit-box-content">
            <div class="tnpc-edit-box-content-text"><?php _e("Title", "newsletter") ?></div>
            <div class="tnpc-edit-box-content-field"><input type="text" class="tnpc-edit-box-content-field-input title"/></div>
            <div class="tnpc-edit-box-content-text"><?php _e("Text Color", "newsletter") ?></div>
            <div class="tnpc-edit-box-content-field"><input type="text" class="tnpc-edit-box-content-field-input color"/></div>
            
            <div class="tnpc-edit-box-content-text"><?php _e("Font family", "newsletter") ?></div>
            <div class="tnpc-edit-box-content-field">
                <select id="tnpc-edit-title-font-family">
                    <optgroup label="Sans Serif Web Safe Fonts">
                        <option value="Arial">Arial</option>
                        <option value="Arial Black">Arial Black</option>
                        <option value="Tahoma">Tahoma</option>
                        <option value="Trebuchet MS">Trebuchet MS</option>
                        <option value="Verdana">Verdana</option>
                    </optgroup>
                    <optgroup label="Serif Web Safe Fonts">
                        <option value="Georgia">Georgia</option>
                        <option value="Times">Times</option>
                        <option value="Times New Roman">Times New Roman</option>
                    </optgroup>
                    <optgroup label="Monospace Fonts">
                        <option value="Courier">Courier</option>
                        <option value="Courier New">Courier New</option>
                    </optgroup>
                </select>
            </div>
            
            <div class="tnpc-edit-box-content-text"><?php _e("Font size", "newsletter") ?></div>
            <div class="tnpc-edit-box-content-field">
                <select id="tnpc-edit-title-font-size">
                    <?php for ($i=10; $i<50; $i++) echo '<option>', $i, '</option>'?>
                </select>
            </div>
            
            <div class="tnpc-edit-box-content-text"><?php _e("Text align", "newsletter") ?></div>
            <div class="tnpc-edit-box-content-field">
                <select id="tnpc-edit-title-text-align">
                    <option value="center">Center</option>
                    <option value="left">Left</option>
                    <option value="right">Right</option>
                </select>
            </div>
        </div>
        <div class="tnpc-edit-box-buttons">
            <div class="tnpc-edit-box-buttons-save"><?php _e("Save", "newsletter") ?></div>
            <div class="tnpc-edit-box-buttons-cancel"><?php _e("Cancel", "newsletter") ?></div>
        </div>
    </div>
</div>


<div class="tnpc-edit" id="tnpc-edit-text">
    <div class="tnpc-edit-box">
        <div class="tnpc-edit-box-title"><?php _e("Edit Text", "newsletter") ?></div>

        <div class="tnpc-edit-box-content">
            <div class="tnpc-edit-box-content-text"><?php _e("Text", "newsletter") ?></div>
            <div class="tnpc-edit-box-content-field"><textarea class="tnpc-edit-box-content-field-textarea text"></textarea></div>
        </div>
        <div class="tnpc-edit-box-buttons">
            <div class="tnpc-edit-box-buttons-save"><?php _e("Save", "newsletter") ?></div>
            <div class="tnpc-edit-box-buttons-cancel"><?php _e("Cancel", "newsletter") ?></div>
        </div>
    </div>
</div>

<!-- On eline text block, like headings -->
<div class="tnpc-edit" id="tnpc-edit-block">
    <div class="tnpc-edit-box">
        <div class="tnpc-edit-box-title"><?php _e("Edit Block", "newsletter") ?></div>

        <div class="tnpc-edit-box-content">
            <div class="tnpc-edit-box-content-text"><?php _e("Background Color", "newsletter") ?></div>
            <div class="tnpc-edit-box-content-field"><input type="text" class="tnpc-edit-box-content-field-input bgcolor"/></div>
            <div class="tnpc-edit-box-content-text"><?php _e("Font Family", "newsletter") ?></div>
            <div class="tnpc-edit-box-content-field">
                <select class="tnpc-edit-box-content-field-input font">
                    <optgroup label="Sans Serif Web Safe Fonts">
                        <option value="Arial">Arial</option>
                        <option value="Arial Black">Arial Black</option>
                        <option value="Tahoma">Tahoma</option>
                        <option value="Trebuchet MS">Trebuchet MS</option>
                        <option value="Verdana">Verdana</option>
                    </optgroup>
                    <optgroup label="Serif Web Safe Fonts">
                        <option value="Georgia">Georgia</option>
                        <option value="Times">Times</option>
                        <option value="Times New Roman">Times New Roman</option>
                    </optgroup>
                    <optgroup label="Monospace Fonts">
                        <option value="Courier">Courier</option>
                        <option value="Courier New">Courier New</option>
                    </optgroup>
                </select>
            </div>
            <div class="tnpc-edit-box-content-field">
                <select class="tnpc-edit-box-content-field-input font-size">
                    <?php for ($i=9; $i<51; $i++) { ?>
                    <option value="<?php echo $i?>"><?php echo $i?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="tnpc-edit-box-buttons">
            <div class="tnpc-edit-box-buttons-save"><?php _e("Save", "newsletter") ?></div>
            <div class="tnpc-edit-box-buttons-cancel"><?php _e("Cancel", "newsletter") ?></div>
        </div>
    </div>
</div>

<div class="tnpc-edit" id="tnpc-edit-posts">
    <div class="tnpc-edit-box">
        <div class="tnpc-edit-box-title"><?php _e("Edit Block", "newsletter") ?></div>

        <div class="tnpc-edit-box-content">
            <div class="tnpc-edit-box-content-text"><?php _e("Background Color", "newsletter") ?></div>
            <div class="tnpc-edit-box-content-field"><input type="text" class="tnpc-edit-box-content-field-input bgcolor"/></div>
            <div class="tnpc-edit-box-content-text"><?php _e("Font Family", "newsletter") ?></div>
            <div class="tnpc-edit-box-content-field">
                <select class="tnpc-edit-box-content-field-input font">
                    <optgroup label="Sans Serif Web Safe Fonts">
                        <option value="Arial">Arial</option>
                        <option value="Arial Black">Arial Black</option>
                        <option value="Tahoma">Tahoma</option>
                        <option value="Trebuchet MS">Trebuchet MS</option>
                        <option value="Verdana">Verdana</option>
                    </optgroup>
                    <optgroup label="Serif Web Safe Fonts">
                        <option value="Courier">Courier</option>
                        <option value="Courier New">Courier New</option>
                        <option value="Georgia">Georgia</option>
                        <option value="Times">Times</option>
                        <option value="Times New Roman">Times New Roman</option>
                    </optgroup>
                    <optgroup label="Monospace Fonts">
                        <option value="Courier">Courier</option>
                        <option value="Courier New">Courier New</option>
                    </optgroup>
                </select>
            </div>
            <div class="tnpc-edit-box-content-text"><?php _e("Number of posts", "newsletter") ?></div>
            <div class="tnpc-edit-box-content-field"><input type="number" class="tnpc-edit-box-content-field-input number" value="3"/></div>
            <div class="tnpc-edit-box-content-text"><?php _e("Categories", "newsletter") ?></div>
            <div class="tnpc-edit-box-content-field">
<!--                <input type="text" class="tnpc-edit-box-content-field-input categories"/>-->
                <?php $controls->categories_group('theme_categories'); ?>
            </div>
            <div class="tnpc-edit-box-content-text"><?php _e("Tags (comma separated)", "newsletter") ?></div>
            <div class="tnpc-edit-box-content-field"><input type="text" class="tnpc-edit-box-content-field-input tags"/></div>
        </div>
        <div class="tnpc-edit-box-buttons">
            <?php _e("Any prior changes to single posts will be lost when editing these settings.", "newsletter") ?>
            <div class="tnpc-edit-box-buttons-save"><?php _e("Save", "newsletter") ?></div>
            <div class="tnpc-edit-box-buttons-cancel"><?php _e("Cancel", "newsletter") ?></div>
        </div>
    </div>
</div>

<div class="tnpc-edit" id="tnpc-block-options">
    <div class="tnpc-edit-box">
        <form id="tnpc-block-options-form" onsubmit="return false;"></form>
        <div class="tnpc-edit-box-buttons">
            <div class="tnpc-edit-box-buttons-save"><?php _e("Save", "newsletter") ?></div>
            <div class="tnpc-edit-box-buttons-cancel"><?php _e("Cancel", "newsletter") ?></div>
        </div>
    </div>
</div>