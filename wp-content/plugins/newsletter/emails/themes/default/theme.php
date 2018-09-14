<?php
/*
 * Name: Default
 * Type: standard
 * Some variables are already defined:
 *
 * - $theme_options An array with all theme options
 * - $theme_url Is the absolute URL to the theme folder used to reference images
 * - $theme_subject Will be the email subject if set by this theme
 *
 */

global $newsletter, $post;

defined('ABSPATH') || exit;

if (empty($theme_options['theme_color']))
    $color = '#555555';
else
    $color = $theme_options['theme_color'];

if (isset($theme_options['theme_posts'])) {
    $filters = array();

    if (empty($theme_options['theme_max_posts']))
        $filters['posts_per_page'] = 10;
    else
        $filters['posts_per_page'] = (int) $theme_options['theme_max_posts'];

    if (!empty($theme_options['theme_categories'])) {
        $filters['category__in'] = $theme_options['theme_categories'];
    }

    if (!empty($theme_options['theme_tags'])) {
        $filters['tag'] = $theme_options['theme_tags'];
    }

    if (!empty($theme_options['theme_post_types'])) {
        $filters['post_type'] = $theme_options['theme_post_types'];
    }

    if (!empty($theme_options['language'])) {
        $filters['suppress_filters'] = false;
        do_action('wpml_switch_language', $theme_options['language']);
    }

    $posts = get_posts($filters);
}
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <!-- Not all email client take care of styles inserted here -->
        <style type="text/css" media="all">
            a {
                text-decoration: none;
                color: <?php echo $color; ?>;
            }
        </style>
    </head>
    <body style="margin: 0!important; padding: 0!important">
        <div style="background-color: #f4f4f4; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px; color: #666; margin: 0 auto; padding: 0;">

            <br>
            <table align="center" bgcolor="#ffffff" width="100%" style="max-width: 600px; width: 100%; border-collapse: collapse; background-color: #000" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td valign="top" bgcolor="#ffffff" width="100%" style="font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px; color: #666;">
                        <div style="text-align: left; background-color: #fff;">
                            <div style="text-align: center">
                                <?php
                                //HEADER
//                        if (!empty($theme_options['theme_banner'])) { 
//                            echo $theme_options['theme_banner'];
                                if (!empty($theme_options['theme_header_logo']['url'])) {
                                    ?>
                                    <img style="max-width: 500px" alt="<?php echo esc_attr($theme_options['main_header_title']) ?>" src="<?php echo esc_attr($theme_options['theme_header_logo']['url']) ?>">
                                <?php } elseif (!empty($theme_options['main_header_logo']['url'])) { ?>
                                    <img style="max-width: 500px" alt="<?php echo esc_attr($theme_options['main_header_title']) ?>" src="<?php echo esc_attr($theme_options['main_header_logo']['url']) ?>">
                                    <?php } elseif (!empty($theme_options['main_header_title'])) { ?>
                                    <div style="padding: 30px 0; color: #000; font-size: 28px; background-color: #EFEFEF; border-bottom: 1px solid #ddd; text-align: center;">
                                    <?php echo $theme_options['main_header_title'] ?>
                                    </div>
                                    <?php if (!empty($theme_options['main_header_sub'])) { ?>
                                        <div style="padding: 10px 0; color: #000; font-size: 16px; text-align: center;">
                                            <?php echo $theme_options['main_header_sub'] ?>
                                        </div>
                                    <?php } ?>
                                    <?php } else { ?>
                                    <div style="padding: 30px 20px; color: #000; font-size: 28px; background-color: #EFEFEF; border-bottom: 1px solid #ddd; text-align: center;">
                                    <?php echo get_option('blogname'); ?>
                                    </div>
                                    <?php if (!empty($theme_options['main_header_sub'])) { ?>
                                        <div style="padding: 10px 0; color: #000; font-size: 16px; text-align: center;">
        <?php echo $theme_options['main_header_sub'] ?>
                                        </div>
    <?php } ?>
<?php } ?>
                            </div>


                            <div style="padding: 10px 20px 20px 20px; background-color: #fff; line-height: 18px">

                                <p style="text-align: center; font-size: small;"><a target="_blank"  href="{email_url}">View this email online</a></p>

                                <p>Here you can start to write your message. Be polite with your readers! Don't forget the subject of this message.</p>
                                        <?php if (!empty($posts)) { ?>
                                    <table cellpadding="5">
    <?php foreach ($posts as $post) {
        setup_postdata($post); ?>
                                            <tr>
        <?php if (isset($theme_options['theme_thumbnails'])) { ?>
                                                    <td valign="top"><a target="_blank"  href="<?php echo get_permalink($post); ?>"><img width="75" style="width: 75px; min-width: 75px" src="<?php echo newsletter_get_post_image($post->ID); ?>" alt="image"></a></td>
                                            <?php } ?>
                                                <td valign="top">
                                                    <a target="_blank"  href="<?php echo get_permalink(); ?>" style="font-size: 20px; line-height: 26px"><?php the_title(); ?></a>
                                        <?php if (isset($theme_options['theme_excerpts'])) newsletter_the_excerpt($post); ?>
                                                </td>
                                            </tr>
    <?php } ?>
                                    </table>
<?php } ?>

<?php include WP_PLUGIN_DIR . '/newsletter/emails/themes/default/footer.php'; ?>

                            </div>

                        </div>
                    </td>
                </tr>
            </table>
            <br><br>
        </div>
    </body>
</html>