<a class="button-primary" href="?page=newsletter_emails_composer&id=<?php echo $email['id'] ?>"><?php _e('Edit', 'newsletter') ?></a>
<div class="tnpc-preview">
    <!-- Flat Laptop Browser -->
    <div class="fake-browser-ui">
        <div class="frame">
            <span class="bt-1"></span>
            <span class="bt-2"></span>
            <span class="bt-3"></span>
        </div>
        <iframe id="tnpc-preview-desktop" src="" width="700" height="507" alt="Test" frameborder="0"></iframe>
    </div>

    <!-- Flat Mobile Browser -->
    <div class="fake-mobile-browser-ui">
        <iframe id="tnpc-preview-mobile" src="" width="320" height="445" alt="Test" frameborder="0"></iframe>
        <div class="frame">
            <span class="bt-4"></span>
        </div>
    </div>
</div>

<script type="text/javascript">
    preview_url = ajaxurl + "?action=tnpc_preview&id=<?php echo $email_id ?>";
    jQuery('#tnpc-preview-desktop, #tnpc-preview-mobile').attr("src", preview_url);
    setTimeout(function () {
        jQuery('#tnpc-preview-desktop, #tnpc-preview-mobile').contents().find("a").click(function (e) {
            e.preventDefault();
        })
    }, 500);
</script>