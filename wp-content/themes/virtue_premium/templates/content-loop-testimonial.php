<?php 
global $post, $kt_testimonial_loop;
	$image_width = apply_filters( 'kt_testimonial_grid_image_width', 60 );
    $image_height = apply_filters( 'kt_testimonial_grid_image_height', 60 );
    $defaults = array(
    	'columns' 			=> '3',
		'limit' 			=> 'true',
		'words' 			=> 25,
		'link' 				=> 'false',
		'linktext'			=> __('Read More', 'virtue'),
		'conditional_link'	=> 'false',
		'pagelink'			=> null,
    );
   	$kt_t_loop = wp_parse_args( $kt_testimonial_loop, $defaults );
   	if ( $kt_t_loop[ 'columns' ] == '2' ) {
		$itemsize 	= 'tcol-md-6 tcol-sm-6 tcol-xs-12 tcol-ss-12';
	} else if ( $kt_t_loop[ 'columns' ] == '1' ){
		$itemsize = 'tcol-md-12 tcol-sm-12 tcol-xs-12 tcol-ss-12';
	} else if ( $kt_t_loop[ 'columns' ] == '3' ){
		$itemsize = 'tcol-md-4 tcol-sm-4 tcol-xs-6 tcol-ss-12';
	} else if ( $kt_t_loop[ 'columns' ] == '6' ){
		$itemsize = 'tcol-md-2 tcol-sm-3 tcol-xs-4 tcol-ss-6';
	} else if ( $kt_t_loop[ 'columns' ] == '5' ){
		$itemsize = 'tcol-md-25 tcol-sm-3 tcol-xs-4 tcol-ss-6';
	} else {
		$itemsize = 'tcol-md-3 tcol-sm-4 tcol-xs-6 tcol-ss-12';
	}
   	$show_link = true;
?>
<div class="<?php echo esc_attr( $itemsize );?> t_item">
	<div class="grid_item testimonial_item kt_item_fade_in kad_testimonial_fade_in postclass">
		<div class="testimonialbox clearfix">
			<?php if ( has_post_thumbnail( $post->ID ) ) {
				$img = virtue_get_image_array( $image_width , $image_height, true, null, null, get_post_thumbnail_id() );
				$img['extras'] = 'style="display: block; max-width:60px;"'; 
				?>
				<div class="alignleft testimonialimg" style="max-width: <?php echo esc_attr($img[ 'width' ]);?>px">
					<div class="kt-intrinsic" style="padding-bottom:<?php echo esc_attr( ($img[ 'height' ]/$img[ 'width' ]) * 100 );?>%;">
						<?php virtue_print_image_output( $img ); ?>
					</div>
				</div>
			<?php } else { ?>
				<div class="alignleft testimonialimg">
					<i class="icon-user2" style="font-size:60px"></i>
				</div>
			<?php } 
			if( 'true' == $kt_t_loop[ 'limit' ] ) {
				echo esc_attr( strip_tags( virtue_content( $kt_t_loop[ 'words' ] ) ) );
				if ( $kt_t_loop[ 'conditional_link' ] == 'true' ) {
					$test_content = get_post_field( 'post_content', $post->ID );
					$wc_count = str_word_count( strip_tags( $test_content ) );
					if( $wc_count < $wordcount ) {
						$show_link = false;
					}
				}
			} else if( 'custom_excerpt' == $kt_t_loop[ 'limit' ] ) {
				the_excerpt(); 
			} else {
				the_content(); 
			}
			if( $show_link ) {
				if( 'true' == $kt_t_loop[ 'link' ] ) {
					echo '<a href="'.esc_url( get_the_permalink() ).'" class="kadtestimoniallink">';
						echo wp_kses_post( $kt_t_loop[ 'linktext' ] );
					echo '</a>';
				} else if( 'page' == $kt_t_loop[ 'link' ]  ) {
					if( ! empty( $kt_t_loop[ 'pagelink' ] ) ) { 
							$thepagelink =  $kt_t_loop[ 'pagelink' ];
						} else {
							$thepagelink = get_the_permalink();
						}
					echo '<a href="'.esc_attr( $thepagelink ).'" class="kadtestimoniallink">';
						echo wp_kses_post( $kt_t_loop[ 'linktext' ] );
					echo '</a>';
				}
			}?>
		</div>
		<div class="testimonialbottom">
			<div class="lipbg kad-arrow-down"></div>
			<p><strong><?php the_title();?></strong>
				<?php $location = get_post_meta( $post->ID, '_kad_testimonial_location', true ); 
					if ( ! empty( $location ) ) {
						echo ' - ' . wp_kses_post( $location );
					}
				?>
			</p>
		</div>
	</div>
</div>