<div class="sliderclass">
<?php global $post; $revslider = get_post_meta( $post->ID, '_kad_post_rev', true );
if(!empty($revslider)) { putRevSlider( $revslider ); } ?>
</div><!--sliderclass-->