<?php
defined('ABSPATH') || exit;

$list = NewsletterEmails::instance()->get_blocks();

$blocks = array();
foreach ($list as $key => $data) {
    if (!isset($blocks[$data['section']]))
        $blocks[$data['section']] = array();
    $blocks[$data['section']][$key]['name'] = $data['name'];
    $blocks[$data['section']][$key]['filename'] = $key;
    $blocks[$data['section']][$key]['icon'] = $data['icon'];
}

// order the sections
$blocks = array_merge(array_flip(array('header', 'content', 'footer')), $blocks);

// prepare the options for the default blocks
$block_options = get_option('newsletter_main');
?>
<style>
    .placeholder {
        border: 3px dashed #ddd!important;
        background-color: #eee!important;
        height: 50px;
        margin: 0;
        width: 100%;
        box-sizing: border-box!important;
    }
    #newsletter-builder-area-center-frame-content {
        min-height: 300px!important;
    }
</style>

<div id="newsletter-builder">  

    <div id="newsletter-builder-sidebar" class="tnp-builder-column">

        <?php foreach ($blocks as $k => $section) { ?>
                    <div class="newsletter-sidebar-add-buttons" id="sidebar-add-<?php echo $k ?>">
                        <h4><span><?php echo ucfirst($k) ?></span></h4>
            <?php foreach ($section AS $key => $block) { ?>
                            <div class="newsletter-sidebar-buttons-content-tab" data-id="<?php echo $key ?>" data-name="<?php echo esc_attr($block['name']) ?>">
                                <img src="<?php echo $block['icon'] ?>" title="<?php echo esc_attr($block['name']) ?>">
                            </div>
                    <?php } ?>
                    </div>
        <?php } ?>

    </div>

    <div id="newsletter-builder-area" class="tnp-builder-column">

        <div id="newsletter-builder-area-center-frame-content">

            <?php
            if (isset($email) && !$controls->is_action('reset')) {
                echo NewsletterModule::extract_body($body);
            } else {
                NewsletterEmails::instance()->render_block('preheader', true, array());
                NewsletterEmails::instance()->render_block('header-01-header.block', true, array());
                NewsletterEmails::instance()->render_block('content-01-hero.block', true, array());
                NewsletterEmails::instance()->render_block('footer-02-canspam.block', true, array());
                NewsletterEmails::instance()->render_block('footer-01-footer.block', true, array());
                //NewsletterEmails::instance()->render_block('footer-01-footer.block', true, array());
                //NewsletterEmails::instance()->render_block('footer-02-canspam.block', true, array());
                //include __DIR__ . '/blocks/header-01-header.block.php';
                //include __DIR__ . '/blocks/content-05-image.block.php';
                //include __DIR__ . '/blocks/content-01-hero.block.php';
                //include __DIR__ . '/blocks/footer-01-footer.block.php';
                //include __DIR__ . '/blocks/footer-02-canspam.block.php';
            }
            ?>

        </div>
    </div>

    <div id="newsletter-mobile-preview-area" class="tnp-builder-column">
        <iframe id="tnp-mobile-preview"></iframe>
    </div>

    <div style="clear: both"></div>
</div>


<div style="display: none">
    <div id="newsletter-preloaded-export"></div>
    <div id="draggable-helper" style="width: 500px; border: 3px dashed #ddd; opacity: .7; background-color: #fff; text-align: center; text-transform: uppercase; font-size: 14px; color: #aaa; padding: 20px;"></div>
    <div id="sortable-helper" style="width: 700px; height: 75px;border: 3px dashed #ddd; opacity: .7; background-color: #fff; text-align: center; text-transform: uppercase; font-size: 14px; color: #aaa; padding: 20px;"></div>
</div>

<div id="tnp-body" style="margin: 0; padding: 0; overflow: hidden; border: 0;"> 
<?php include NEWSLETTER_DIR . '/emails/tnp-composer/edit.php'; ?>
</div>



<script type="text/javascript">
    TNP_PLUGIN_URL = "<?php echo NEWSLETTER_URL ?>";
    TNP_HOME_URL = "<?php echo home_url('/', is_ssl() ? 'https' : 'http') ?>";
</script>
<script type="text/javascript" src="<?php echo plugins_url('newsletter'); ?>/emails/tnp-composer/_scripts/newsletter-builder.js?ver=<?php echo time() ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/4.7.13/tinymce.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.6.8-fix/jquery.nicescroll.min.js"></script>
<script>
    jQuery(function () {
        //jQuery("#tnp-mobile-preview").niceScroll();
        tnp_mobile_preview();
    });
</script>
