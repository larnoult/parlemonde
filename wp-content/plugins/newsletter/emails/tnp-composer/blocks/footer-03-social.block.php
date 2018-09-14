<?php 
$social_icon_url = plugins_url('newsletter') . '/emails/themes/default/images'; 
$configured = false;
?>
<table border="0" cellpadding="0" align="center" cellspacing="0" width="100%" style="width: 100%!important; max-width: <?php echo $width ?>px!important">
    <tr>
        <td bgcolor="#ffffff" align="center" style="padding: 20px 15px 20px 15px;" class="section-padding edit-block">

            <table border="0" cellspacing="0" cellpadding="0" align="center" class="responsive-table">
                <tr>
                    <td align="center" style="font-size: 12px; line-height: 18px; font-family: Helvetica, Arial, sans-serif; color:#666666;">
                        <?php if (!empty($block_options['facebook_url'])) { $configured = true; ?>
                            <span class="tnpc-row-edit" data-type="image">
                                <a href="<?php echo $block_options['facebook_url'] ?>"><img src="<?php echo $social_icon_url ?>/facebook.png" alt=""></a>
                            </span>
                        <?php } ?>
                        <?php if (!empty($block_options['twitter_url'])) { $configured = true; ?>
                            <span class="tnpc-row-edit" data-type="image">
                                <a href="<?php echo $block_options['twitter_url'] ?>"><img src="<?php echo $social_icon_url ?>/twitter.png" alt="Twitter"></a>
                            </span>
                        <?php } ?>
                        <?php if (!empty($block_options['googleplus_url'])) { $configured = true; ?>
                            <span class="tnpc-row-edit" data-type="image">
                                <a href="<?php echo $block_options['googleplus_url'] ?>"><img src="<?php echo $social_icon_url ?>/googleplus.png" alt="Google+"></a>
                            </span>
                        <?php } ?>
                        <?php if (!empty($block_options['pinterest_url'])) { $configured = true; ?>
                            <span class="tnpc-row-edit" data-type="image">
                                <a href="<?php echo $block_options['pinterest_url'] ?>"><img src="<?php echo $social_icon_url ?>/pinterest.png" alt="Pinterest"></a>
                            </span>
                        <?php } ?>
                        <?php if (!empty($block_options['linkedin_url'])) { $configured = true; ?>
                            <span class="tnpc-row-edit" data-type="image">
                                <a href="<?php echo $block_options['linkedin_url'] ?>"><img src="<?php echo $social_icon_url ?>/linkedin.png" alt="LinkedIn"></a>
                            </span>
                        <?php } ?>
                        <?php if (!empty($block_options['tumblr_url'])) { $configured = true; ?>
                            <span class="tnpc-row-edit" data-type="image">
                                <a href="<?php echo $block_options['tumblr_url'] ?>"><img src="<?php echo $social_icon_url ?>/tumblr.png" alt="Tumblr"></a>
                            </span>
                        <?php } ?>
                        <?php if (!empty($block_options['youtube_url'])) { $configured = true; ?>
                            <span class="tnpc-row-edit" data-type="image">
                                <a href="<?php echo $block_options['youtube_url'] ?>"><img src="<?php echo $social_icon_url ?>/youtube.png" alt="Youtube"></a>
                            </span>
                        <?php } ?>
                        <?php if (!empty($block_options['soundcloud_url'])) { $configured = true; ?>
                            <span class="tnpc-row-edit" data-type="image">
                                <a href="<?php echo $block_options['soundcloud_url'] ?>"><img src="<?php echo $social_icon_url ?>/soundcloud.png" alt="SoundCloud"></a>
                            </span>
                        <?php } ?>
                        <?php if (!empty($block_options['instagram_url'])) { $configured = true; ?>
                            <span class="tnpc-row-edit" data-type="image">
                                <a href="<?php echo $block_options['instagram_url'] ?>"><img src="<?php echo $social_icon_url ?>/instagram.png" alt="Instagram"></a>
                            </span>
                        <?php } ?>
                        <?php if (!empty($block_options['vimeo_url'])) { $configured = true; ?>
                            <span class="tnpc-row-edit" data-type="image">
                                <a href="<?php echo $block_options['vimeo_url'] ?>"><img src="<?php echo $social_icon_url ?>/vimeo.png" alt="Vimeo"></a>
                            </span>
                        <?php } ?>
                        <?php if (!$configured) { ?>
                            <p>Configure your social links in the <a href="?page=newsletter_main_info">Social configuration section</a>.<br/>
                                Then remove and add again this block.</p>
                        <?php } ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

