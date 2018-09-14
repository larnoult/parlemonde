<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/4.7.13/tinymce.min.js"></script>
<script type="text/javascript">

    // https://www.tinymce.com/docs/advanced/editor-control-identifiers/#toolbarcontrols
    tinymce.init({
        height: 700,
        mode: "specific_textareas",
        editor_selector: "visual",
        statusbar: true,
        allow_conditional_comments: true,
        table_toolbar: "tableprops tablecellprops tabledelete | tableinsertrowbefore tableinsertrowafter tabledeleterow | " +
                "tableinsertcolbefore tableinsertcolafter tabledeletecol",
        toolbar: "formatselect fontselect fontsizeselect | bold italic underline strikethrough forecolor backcolor | alignleft alignright aligncenter alignjustify | bullist numlist | link unlink | image",
        //theme: "advanced",
        entity_encoding: "raw",
        image_advtab: true,
        image_title: true,
        plugins: "table fullscreen legacyoutput textcolor colorpicker link image code lists advlist",
        relative_urls: false,
        convert_urls: false,
        remove_script_host: false,
        document_base_url: "<?php echo esc_js(get_option('home')) ?>/",
        content_css: ["<?php echo plugins_url('newsletter') ?>/emails/editor.css", "<?php echo home_url('/') . '?na=emails-css&id=' . $email_id . '&' . time(); ?>"]
    });

</script>
<script>
    function tnp_media() {
        var tnp_uploader = wp.media({
            title: "Select an image",
            button: {
                text: "Select"
            },
            frame: 'post',
            multiple: false,
            displaySetting: true,
            displayUserSettings: true
        }).on("insert", function () {
            wp.media;
            var media = tnp_uploader.state().get("selection").first();
            if (media.attributes.url.indexOf("http") !== 0)
                media.attributes.url = "http:" + media.attributes.url;

            if (!media.attributes.mime.startsWith("image")) {

                tinyMCE.execCommand('mceInsertLink', false, media.attributes.url);

            } else {
                var display = tnp_uploader.state().display(media);
                var url = media.attributes.sizes[display.attributes.size].url;

                tinyMCE.execCommand('mceInsertContent', false, '<img src="' + url + '" style="max-width: 100%">');

            }
        }).open();
    }

</script>
<input type="button" class="button-primary" value="Add media" onclick="tnp_media()">

<a href="https://www.thenewsletterplugin.com/plugins/newsletter/newsletter-tags" target="_blank"><?php _e('Available tags', 'newsletter') ?></a>
<br><br>

<?php $controls->editor('message', 30); ?>