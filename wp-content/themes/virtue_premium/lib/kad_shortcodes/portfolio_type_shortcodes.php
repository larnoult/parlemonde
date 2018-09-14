<?php 
//Shortcode for portfolio Posts
function kad_portfolio_type_shortcode_function( $atts, $content) {
	extract(shortcode_atts(array(
		'orderby' => 'menu_order',
		'order' => 'ASC',
		'columns' => '4',
		'id' => rand(10,100),
		'childof' => '0',
		'height' => '',
		'childcategories' => false,
		'showexcerpt' => false,
), $atts));
	if($childcategories == true) {$parent = "";} else {$parent = "0";}
						if ($columns == '2') {$itemsize = 'tcol-md-6 tcol-sm-6 tcol-xs-12 tcol-ss-12'; $slidewidth = 560; $slideheight = 560; $md = 2; $sm = 2; $xs = 1; $ss = 1;} 
						else if ($columns == '1') {$itemsize = 'tcol-md-12 tcol-sm-12 tcol-xs-12 tcol-ss-12'; $slidewidth = 560; $slideheight = 560; $md = 1; $sm = 1; $xs = 1; $ss = 1;} 
		                   else if ($columns == '3'){ $itemsize = 'tcol-md-4 tcol-sm-4 tcol-xs-6 tcol-ss-12'; $slidewidth = 366; $slideheight = 366; $md = 3; $sm = 3; $xs = 2; $ss = 1;} 
		                   else if ($columns == '6'){ $itemsize = 'tcol-md-2 tcol-sm-3 tcol-xs-4 tcol-ss-6'; $slidewidth = 240; $slideheight = 240; $md = 6; $sm = 4; $xs = 3; $ss = 2;} 
		                   else if ($columns == '5'){ $itemsize = 'tcol-md-25 tcol-sm-3 tcol-xs-4 tcol-ss-6'; $slidewidth = 240; $slideheight = 240; $md = 5; $sm = 4; $xs = 3; $ss = 2;} 
		                   else {$itemsize = 'tcol-md-3 tcol-sm-4 tcol-xs-6 tcol-ss-12'; $slidewidth = 270; $slideheight = 270; $md = 4; $sm = 3; $xs = 2; $ss = 1;}
		                	if(!empty($height) && $height == 'none') {$slideheight = null;} else if(!empty($height)) {$slideheight = $height;}
		global $virtue_premium; if(isset($virtue_premium['virtue_animate_in']) && $virtue_premium['virtue_animate_in'] == 1) {$animate = 1;} else {$animate = 0;}
ob_start(); ?>
		<div id="portfoliowrapper-<?php echo $id;?>" class="rowtight init-isotope reinit-isotope" data-fade-in="<?php echo $animate;?>" data-iso-selector=".p-item" data-iso-style="masonry" data-iso-filter="false"> 
            <?php 	$meta = get_option('portfolio_cat_image');
						if (empty($meta)) {$meta = array();}
						if (!is_array($meta)) { $meta = (array) $meta; }
						$args = array( 'parent'=>$parent,'hide_empty'=>'1', 'child_of' => $childof, 'orderby' => $orderby, 'order'=>$order, );
            			$terms = get_terms("portfolio-type", $args);
					if ( !empty( $terms ) && !is_wp_error( $terms ) ) {
						foreach ( $terms as $term ) : ?>
				<div class="<?php echo esc_attr($itemsize);?> p-item">
					<div class="portfolio_item grid_item kt_item_fade_in postclass kad-light-gallery kad_portfolio_fade_in">
                				<?php $cat_term_id = $term -> term_id;
									if(isset($meta[$cat_term_id])) {$item_meta = $meta[$cat_term_id];} else {$item_meta = array();}
									if(isset($item_meta['category_image'])) { $bg_image_array = $item_meta['category_image']; $src = wp_get_attachment_image_src($bg_image_array[0], 'full'); $ct_image = $src[0];}
									if (!empty($ct_image)) {
									 $image = aq_resize($ct_image, $slidewidth, $slideheight, true);
									if(empty($image)) {$image = $ct_image;} ?>
										<div class="imghoverclass">
	                                       <a href="<?php echo get_term_link( $term );  ?>" title="<?php echo esc_attr($term->name); ?>">
	                                       <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($term->name); ?>" class="lightboxhover" style="display: block;">
	                                       </a> 
	                                	</div>
		                          <?php $ct_image = null; ?>
	                           <?php } ?>

					      	<?php echo '<a href="' . get_term_link( $term ) . '" class="portfoliolink">'; ?>

		              		<div class="piteminfo">   
		                          <h5><?php echo esc_html($term->name); ?></h5>
		                        <?php if($showexcerpt == true) { ?> <p><?php echo esc_html($term->description); ?></p> <?php } ?>
		                    </div>
		                </a>
		                </div>
                    </div>
					        
					   <?php endforeach;  
					 } ?>
                </div> <!--portfoliowrapper-->
                    <?php $wp_query = null; wp_reset_query(); ?>
	
	<?php  $output = ob_get_contents();
		ob_end_clean();
		wp_reset_postdata();
	return $output;
}