<?php

if (!defined('ABSPATH'))
    exit;

function tnp_post_thumbnail_src($post, $size = 'thumbnail', $alternative = '') {
    if (is_object($post)) {
        $post = $post->ID;
    }

    if (is_array($size)) {
        $media_id = get_post_thumbnail_id($post);
        if (!$media_id) {
            return $alternative;
        }
        $src = tnp_media_resize($media_id, $size);
        if (is_wp_error($src)) {
            Newsletter::instance()->logger->error($src);
        } else {
            return $src;
        }
    }

    $media = wp_get_attachment_image_src(get_post_thumbnail_id($post), $size);
    if (strpos($media[0], 'http') !== 0) {
        $media[0] = 'http:' . $media[0];
    }
    return $media[0];
}

function tnp_post_excerpt($post, $length = 30) {
    if (empty($post->post_excerpt)) {
        $excerpt = wp_strip_all_tags(strip_shortcodes($post->post_content));
        $excerpt = wp_trim_words($excerpt, $length);
    } else {
        $excerpt = wp_trim_words($post->post_excerpt, $length);
    }
    return $excerpt;
}

function tnp_post_permalink($post) {
    return get_permalink($post->ID);
}

function tnp_post_content($post) {
    return $post->post_content;
}

function tnp_post_title($post) {
    return $post->post_title;
}

function tnp_post_date($post, $format = null) {
    if (empty($format)) {
        $format = get_option('date_format');
    }
    return mysql2date($format, $post->post_date);
}

function tnp_media_resize($media_id, $size) {
    $relative_file = get_post_meta($media_id, '_wp_attached_file', true);
    $width = $size[0];
    $height = $size[1];
    $crop = false;
    if (isset($size[2])) {
        $crop = (boolean) $size[2];
    }

    $uploads = wp_upload_dir();
    $absolute_file = $uploads['basedir'] . '/' . $relative_file;
    // Relative and absolute name of the thumbnail.
    $pathinfo = pathinfo($relative_file);
    $relative_thumb = $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '-' . $width . 'x' .
            $height . ($crop ? '-c' : '') . '.' . $pathinfo['extension'];
    $absolute_thumb = WP_CONTENT_DIR . '/newsletter/thumbnails/' . $relative_thumb;

    // Thumbnail generation if needed.
    if (!file_exists($absolute_thumb) || filemtime($absolute_thumb) < filemtime($absolute_file)) {
        wp_mkdir_p(WP_CONTENT_DIR . '/newsletter/thumbnails/' . $pathinfo['dirname']);

        $editor = wp_get_image_editor($absolute_file);
        if (is_wp_error($editor)) {
            return $editor;
            //return $uploads['baseurl'] . '/' . $relative_file;
        }

        $editor->set_quality(80);
        $resized = $editor->resize($width, $height, $crop);

        if (is_wp_error($resized)) {
            return $uploads['baseurl'] . '/' . $relative_file;
        }

        $saved = $editor->save($absolute_thumb);
        if (is_wp_error($saved)) {
            return $saved;
            //return $uploads['baseurl'] . '/' . $relative_file;
        }
    }

    return WP_CONTENT_URL . '/newsletter/thumbnails/' . $relative_thumb;
}
