<?php
/*
 * Name: Last posts
 * Section: content
 * Description: Last opsts list with different layouts
 */

/* @var $options array */
/* @var $wpdb wpdb */
include NEWSLETTER_INCLUDES_DIR . '/helper.php';

$defaults = array(
    'title' => 'Last news',
    'block_background' => '#E6E9ED',
    'color' => '#999999',
    'font_family' => 'Helvetica, Arial, sans-serif',
    'font_size' => '13',
    'max' => 4,
    'read_more' => __('Read more...', 'newsletter'),
    'categories' => '',
    'tags' => '',
    'block_background' => '#ffffff',
    'layout' => 'one',
    'language' => '',
    'button_color' => '#256F9C'
);

$options = array_merge($defaults, $options);

$filters = array();
$filters['posts_per_page'] = (int) $options['max'];

if (!empty($options['categories'])) {
    $filters['category__in'] = $options['categories'];
}

if (!empty($options['tags'])) {
    $filters['tag'] = $options['tags'];
}

$posts = Newsletter::instance()->get_posts($filters, $options['language']);

$button_color = $options['button_color'];

$alternative = plugins_url('newsletter') . '/emails/blocks/posts/images/blank.png';
?>

<?php if ($options['layout'] == 'one') { ?>
    <style>
        .posts-title {
            padding: 0 0 10px 0; 
            font-size: 25px; 
            font-family: <?php echo $font_family ?>; 
            font-weight: normal; 
            color: #333333;
        }
        .posts-post-date {
            padding: 0 0 5px 25px; 
            font-size: 13px; 
            font-family: <?php echo $font_family ?>; 
            font-weight: normal; 
            color: #aaaaaa;
        }
        .posts-post-title {
            padding: 0 0 5px 25px; 
            font-size: 22px; 
            font-family: <?php echo $font_family ?>; 
            font-weight: normal; 
            color: #333333;
        }
    </style>
    <!-- COMPACT ARTICLE SECTION -->



    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="responsive-table">
        <!-- SPACER -->
        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>

        <!-- TITLE -->
        <tr>
            <td align="center" inline-class="posts-title" class="padding-copy tnpc-row-edit" data-type="title" colspan="2"><?php echo $options['title'] ?></td>
        </tr>

        <?php foreach ($posts AS $post) { ?>

            <tr>
                <td valign="top" style="padding: 40px 0 0 0;" class="mobile-hide">
                    <a href="<?php echo tnp_post_permalink($post) ?>" target="_blank">
                        <img src="<?php echo tnp_post_thumbnail_src($post, array(105, 105, true), $alternative) ?>" width="105" height="105" border="0" style="display: block; font-family: Arial; color: #666666; font-size: 14px; width: 105px!important; height: 105px!important;">
                    </a>
                </td>
                <td style="padding: 40px 0 0 0;" class="no-padding">
                    <!-- ARTICLE -->
                    <table border="0" cellspacing="0" cellpadding="0" width="100%">
                        <?php if (!empty($options['show_date'])) { ?>
                            <tr>
                                <td align="left" inline-class="posts-post-date" class="padding-meta">
                                    <?php echo tnp_post_date($post) ?>
                                </td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td align="left" inline-class="posts-post-title" class="padding-copy tnpc-row-edit" data-type="title">
                                <?php echo tnp_post_title($post) ?>
                            </td>  
                        </tr>
                        <tr>
                            <td align="left" style="padding: 10px 0 15px 25px; font-size: 16px; line-height: 24px; font-family: Helvetica, Arial, sans-serif; color: #666666;" class="padding-copy tnpc-row-edit" data-type="text">
                                <?php echo tnp_post_excerpt($post) ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:0 0 45px 25px;" align="left" class="padding">
                                <table border="0" cellspacing="0" cellpadding="0" class="mobile-button-container">
                                    <tr>
                                        <td align="center">
                                            <!-- BULLETPROOF BUTTON -->
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="mobile-button-container">
                                                <tr>
                                                    <td align="center" style="padding: 0;" class="padding-copy">
                                                        <table border="0" cellspacing="0" cellpadding="0" class="responsive-table">
                                                            <tr>
                                                                <td align="center">
                                                                    <a href="<?php echo tnp_post_permalink($post) ?>" target="_blank" style="font-size: 15px; font-family: Helvetica, Arial, sans-serif; font-weight: normal; color: #ffffff; text-decoration: none; background-color: <?php echo $button_color?>; border-top: 10px solid <?php echo $button_color?>; border-bottom: 10px solid <?php echo $button_color?>; border-left: 20px solid <?php echo $button_color?>; border-right: 20px solid <?php echo $button_color?>; border-radius: 3px; -webkit-border-radius: 3px; -moz-border-radius: 3px; display: inline-block;" class="mobile-button tnpc-row-edit" data-type="link"><?php echo $options['read_more'] ?></a>
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
                </td>
            </tr>

        <?php } ?>

    </table>



<?php } else { ?>

    <style>
        .posts-title {
            font-size: 25px; 
            line-height: 30px; 
            font-family: <?php echo $font_family ?>; 
            color: #333333;
        }
        .post-subtitle {
            padding: 20px 0 20px 0; 
            font-size: 16px; 
            line-height: 25px; 
            font-family: <?php echo $font_family ?>; 
            color: #666666;
        }
        .posts-post-title {
            padding: 15px 0 0 0; 
            font-family: <?php echo $font_family ?>; 
            color: #333333; 
            font-size: 20px;
            line-height: 25px; 
        }
        .posts-post-excerpt {
            padding: 5px 0 0 0; 
            font-family: <?php echo $font_family ?>; 
            color: #666666; 
            font-size: 14px; 
            line-height: 20px;
        }
    </style>
    <!-- TWO COLUMN SECTION -->
    <br><br>
    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="responsive-table">
        <tr>
            <td>
                <!-- TITLE SECTION AND COPY -->
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td align="center" inline-class="posts-title" class="padding-copy tnpc-row-edit" data-type="title"><?php echo $options['title'] ?></td>
                    </tr>
                    <tr>
                        <td align="center" inline-class="posts-subtitle" class="padding-copy tnpc-row-edit" data-type="text">The twelve jurors were all writing very busily on slates.</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- TWO COLUMNS -->
    <table cellspacing="0" cellpadding="0" border="0" width="100%">
        <?php foreach (array_chunk($posts, 2) AS $row) { ?>        
            <tr>
                <td valign="top" style="padding: 10px;" class="mobile-wrapper">

                    <!-- LEFT COLUMN -->
                    <table cellpadding="0" cellspacing="0" border="0" width="47%" align="left" class="responsive-table">
                        <tr>
                            <td style="padding: 20px 0 40px 0;">
                                <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                    <tr>
                                        <td align="center" valign="middle" class="tnpc-row-edit" data-type="image">
                                            <a href="<?php echo tnp_post_permalink($row[0]) ?>" target="_blank">
                                                <img src="<?php echo tnp_post_thumbnail_src($row[0], array(240, 160, true)) ?>" width="240" height="160" border="0" class="img-max">
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center" inline-class="posts-post-title" class="tnpc-row-edit" data-type="title"><?php echo tnp_post_title($row[0]) ?></td>
                                    </tr>
                                    <tr>
                                        <td align="center" inline-class="posts-post-excerpt" class="tnpc-row-edit" data-type="text"><?php echo tnp_post_excerpt($row[0]) ?></td>
                                    </tr>
                                    <tr>
                                        <td align="center" style="padding: 5px 0 0 0; font-family: Arial, sans-serif; color: #666666; font-size: 14px; line-height: 20px;"><a href="<?php echo tnp_post_permalink($row[0]) ?>" style="color: #256F9C; text-decoration: none;" class="tnpc-row-edit" data-type="link"><?php echo $options['read_more'] ?></a></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <?php if (!empty($row[1])) { ?>
                        <!-- RIGHT COLUMN -->
                        <table cellpadding="0" cellspacing="0" border="0" width="47%" align="right" class="responsive-table">
                            <tr>
                                <td style="padding: 20px 0 40px 0;">
                                    <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                        <tr>
                                            <td align="center" valign="middle" class="tnpc-row-edit" data-type="image">
                                                <a href="<?php echo tnp_post_permalink($row[1]) ?>" target="_blank">
                                                    <img src="<?php echo tnp_post_thumbnail_src($row[1], array(240, 160, true)) ?>" width="240" height="160" border="0" class="img-max">
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="center" inline-class="posts-post-title" class="tnpc-row-edit" data-type="title"><?php echo tnp_post_title($row[1]) ?></td>
                                        </tr>
                                        <tr>
                                            <td align="center" inline-class="posts-post-excerpt" class="tnpc-row-edit" data-type="text"><?php echo tnp_post_excerpt($row[1]) ?></td>
                                        </tr>
                                        <tr>
                                            <td align="center" style="padding: 5px 0 0 0; font-family: Arial, sans-serif; color: #666666; font-size: 14px; line-height: 20px;"><a href="<?php echo tnp_post_permalink($row[1]) ?>" style="color: #256F9C; text-decoration: none;" class="tnpc-row-edit" data-type="link"><?php echo $options['read_more'] ?></a></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    <?php } ?>

                </td>
            </tr>

        <?php } ?>

    </table>



<?php } ?>
