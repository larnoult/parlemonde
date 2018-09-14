<?php 
//Shortcode for staff Posts
function kad_staff_shortcode_function( $atts, $content) {
	extract(shortcode_atts(array(
		'orderby' => '',
		'order' => '',
		'cat' => '',
		'offset' => null,
		'columns' => '3',
		'limit_content' => 'true',
		'lightbox' => 'true',
		'link' => 'false',
		'height' => '',
		'filter' => 'false',
		'id' => (rand(10,100)),
		'items' => '4'
), $atts));
	if ( empty( $orderby ) ) {
		$orderby = 'menu_order';
	}
	if ( ! empty( $order ) ) {
		$order = $order;
	} else if( $orderby == 'menu_order' ) {
		$order = 'ASC';
	} else {
		$order = 'DESC';
	} 
	if(empty($cat)) {
		$cat = '';
		$staff_cat_ID = '';
	} else {
		$staff_cat 		= get_term_by( 'slug',$cat,'staff-group' );
		$staff_cat_ID 	= $staff_cat->term_id;
	}
	if( ! empty( $height ) && 'null' == $height ) {
		$crop = 'false';
	} else {
		$crop = 'true';
	}
	if ( 'false' == $link && 'true' == $lightbox ) {
		$link = 'lightbox';
	}
	if ( 'true' == $limit_content ) {
		$show_full_content = 'false';
	} else {
		$show_full_content = 'true';
	}
	global $kt_staff_loop;
	$kt_staff_loop = array(
		'content' 	=> $show_full_content,
		'link' 		=> $link,
		'crop' 		=> $crop,
		'cropheight'=> $height,
		'columns' 	=> $columns,
	);
ob_start(); ?>
	<div class="home-staff sc-staff">
	<?php if ($filter == "true") {
	  		$sft = "true"; ?>
      			<section id="options" class="clearfix">
			<?php 	
					global $virtue_premium;
					if(!empty($virtue_premium['filter_all_text'])) {
						$alltext = $virtue_premium['filter_all_text'];
					} else {
						$alltext = __('All', 'virtue');
					}
					if(!empty($virtue_premium['portfolio_filter_text'])) {
						$stafffiltertext = $virtue_premium['portfolio_filter_text'];
					} else {
						$stafffiltertext = __('Filter Staff', 'virtue');
					}
					$termtypes  = array( 'child_of' => $staff_cat_ID );
					$categories = get_terms( 'staff-group', $termtypes );
					$count      = count( $categories );
						echo '<a class="filter-trigger headerfont" data-toggle="collapse" data-target=".filter-collapse"><i class="icon-tags"></i> '.esc_html( $stafffiltertext ).'</a>';
						echo '<ul id="filters" class="clearfix option-set filter-collapse">';
						echo '<li class="postclass"><a href="#" data-filter="*" title="All" class="selected"><h5>'.esc_html( $alltext ).'</h5><div class="arrow-up"></div></a></li>';
						 if ( $count > 0 ){
							foreach ( $categories as $category ){ 
								$termname = strtolower( $category->name );
								$termname = preg_replace( "/[^a-zA-Z 0-9]+/", " ", $termname );
								$termname = str_replace( ' ', '-', $termname );
								echo '<li class="postclass"><a href="#" data-filter=".'.esc_attr( $termname ).'" title="" rel="'.esc_attr( $termname ).'"><h5>'.esc_html( $category->name ).'</h5><div class="arrow-up"></div></a></li>';
							}
				 		}
				 		echo "</ul>"; ?>
				</section>
            <?php } else {
            	$sft = "false";
            } ?>
		<div id="staffwrapper-<?php echo esc_attr( $id );?>" class="rowtight init-isotope reinit-isotope" data-fade-in="<?php echo esc_attr( virtue_animate() );?>" data-iso-selector=".s_item" data-iso-style="masonry" data-iso-filter="<?php echo esc_attr( $sft );?>"> 
            <?php $wp_query = null; 
				  $wp_query = new WP_Query();
					  $wp_query->query(array(
					  	'orderby' 			=> $orderby,
					  	'order' 			=> $order,
					  	'offset' 			=> $offset,
					  	'post_type' 		=> 'staff',
					  	'staff-group'		=> $cat,
					  	'posts_per_page' 	=> $items,
					  	)
					  );
					if ( $wp_query ) : while ( $wp_query->have_posts() ) : $wp_query->the_post();
						get_template_part( 'templates/content', 'loop-staff' );
					endwhile; else: ?>
					<div class="error-not-found"><?php esc_html_e( 'Sorry, no staff entries found.', 'virtue' );?></div>
				<?php endif; ?>
                </div> <!-- staffwrapper -->
                    <?php 
                    $wp_query = null;
                    wp_reset_query(); ?>
		</div><!-- /.sc-staff -->
            		

	<?php  $output = ob_get_contents();
		ob_end_clean();
		wp_reset_postdata();
	return $output;
}