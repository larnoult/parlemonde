<div class="sliderclass">
<?php global $post; $cycloneslider = get_post_meta( $post->ID, '_kad_post_cyclone', true );
if(!empty($cycloneslider)) { echo do_shortcode( $cycloneslider ); } ?>
</div><!--sliderclass-->