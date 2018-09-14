 <?php

global $post;
    $id = $post->ID;
     echo do_shortcode('[kadence_slider id="'.esc_attr($id).'"]');
     