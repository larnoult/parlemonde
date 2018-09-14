<?php 

/* Template Feature */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;

$uselightbox = get_post_meta( $post->ID, '_kad_feature_img_lightbox', true ); 
$height = get_post_meta( $post->ID, '_kad_posthead_height', true );
$swidth = get_post_meta( $post->ID, '_kad_posthead_width', true );
if (!empty($uselightbox)) {
	$lightbox = $uselightbox;
} else {
	$lightbox = 'false';
}
if (!empty($height)){ 
	$slideheight = $height;
} else {
	$slideheight = 400; 
}
if (!empty($swidth)) {
	$slidewidth = $swidth;
} else {
	$slidewidth = 1140;
}
$image_gallery = get_post_meta( $post->ID, '_kad_image_gallery', true );
if ( !empty( $image_gallery ) ) {
	$attachments = array_filter( explode( ',', $image_gallery ) );
} else {
	$attach_args = array('order'=> 'ASC','post_type'=> 'attachment','post_parent'=> $post->ID,'post_mime_type' => 'image','post_status'=> null,'orderby'=> 'menu_order','numberposts'=> -1);
	$attachments_posts = get_posts($attach_args);
	$attachments = array();
	foreach ($attachments_posts as $val) {
		$attachments[] = $val->ID;
	}
}
?>
<section class="pagefeat container">
                <div class="flexslider kt-flexslider kad-light-wp-gallery loading" style="max-width:<?php echo esc_attr($slidewidth);?>px;" data-flex-speed="7000" data-flex-anim-speed="400" data-flex-animation="fade" data-flex-auto="true">
                  <ul class="slides">
                  <?php
                      $image_gallery = get_post_meta( $post->ID, '_kad_image_gallery', true );
                          if(!empty($image_gallery)) {
                            $attachments = array_filter( explode( ',', $image_gallery ) );
                              if ($attachments) {
                              foreach ($attachments as $attachment) {
                                $attachment_url = wp_get_attachment_url($attachment , 'full');
                                $alt = get_post_meta($attachment, '_wp_attachment_image_alt', true);
                                $image = aq_resize($attachment_url, $slidewidth, $slideheight, true);
                                if(empty($image)) {$image = $attachment_url;} ?>

                                <li>
                                  <?php if($lightbox == 'yes') {?>
                                    <a href="<?php echo esc_url($attachment_url); ?>" data-rel="lightbox" class="lightboxhover">
                                  <?php } 
                                  echo '<img src="'.esc_url($image).'" width="'.esc_attr($slidewidth).'" height="'.esc_attr($slideheight).'" alt="'.esc_attr($alt).'"/>';
                                  if($lightbox == 'yes') {?>
                                    </a>
                                  <?php } ?>
                                  </li>
                                  <?php
                              }
                            }
                          } else {
                            $attach_args = array('order'=> 'ASC','post_type'=> 'attachment','post_parent'=> $post->ID,'post_mime_type' => 'image','post_status'=> null,'orderby'=> 'menu_order','numberposts'=> -1);
                            $attachments = get_posts($attach_args);
                              if ($attachments) {
                                foreach ($attachments as $attachment) {
                                  $attachment_url = wp_get_attachment_url($attachment->ID , 'full');
                                  $image = aq_resize($attachment_url, $slidewidth, $slideheight, true);
                                    if(empty($image)) {$image = $attachment_url;}
                                  echo '<li><img src="'.$image.'"/></li>';
                                }
                              } 
                          } ?>                  
            </ul>
          </div> <!--Flex Slides-->
        </section>