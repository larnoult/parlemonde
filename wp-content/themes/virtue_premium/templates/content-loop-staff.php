<?php 
global $post, $kt_staff_loop;
	if ( $kt_staff_loop[ 'columns' ] == '2' ) {
		$itemsize 	= 'tcol-md-6 tcol-sm-6 tcol-xs-12 tcol-ss-12';
		$imgwidth 	= 560;
		$imgheight 	= 560; 
	} else if ( $kt_staff_loop[ 'columns' ] == '1' ){
		$itemsize 	= 'tcol-md-12 tcol-ss-12';
		$imgwidth 	= 560;
		$imgheight 	= 560;
	} else if ( $kt_staff_loop[ 'columns' ] == '3' ){
		$itemsize 	= 'tcol-md-4 tcol-sm-4 tcol-xs-6 tcol-ss-12';
		$imgwidth 	= 366;
		$imgheight 	= 366;
	} else if ( $kt_staff_loop[ 'columns' ] == '6' ){
		$itemsize 	= 'tcol-md-2 tcol-sm-3 tcol-xs-4 tcol-ss-6';
		$imgwidth 	= 240;
		$imgheight 	= 240;
	} else if ( $kt_staff_loop[ 'columns' ] == '5' ){
		$itemsize 	= 'tcol-md-25 tcol-sm-3 tcol-xs-4 tcol-ss-6';
		$imgwidth	= 240;
		$imgheight	= 240;
	} else {
		$itemsize 	= 'tcol-md-3 tcol-sm-4 tcol-xs-6 tcol-ss-12';
		$imgwidth 	= 270;
		$imgheight 	= 270;
	}  
	$crop = true; 
	if ( ! empty(  $kt_staff_loop[ 'cropheight' ] ) ) {
		$imgheight = $kt_staff_loop[ 'cropheight' ];
	}
	if ( 'no' == $kt_staff_loop[ 'crop' ] ) {
		$imgheight = '';
		$crop = false;
	}
	$image_width = apply_filters( 'kt_staff_grid_image_width', $imgwidth );
    $image_height = apply_filters( 'kt_staff_grid_image_height', $imgheight );

	$terms = get_the_terms( $post->ID, 'staff-group' );
	if ( $terms && ! is_wp_error( $terms ) ) : 
		$links = array();
		foreach ( $terms as $term ) { 
			$links[] = $term->name;
		}
		$links = preg_replace("/[^a-zA-Z 0-9]+/", " ", $links);
		$links = str_replace(' ', '-', $links);	
		$tax = join( " ", $links );		
	else :	
		$tax = '';	
	endif; ?>
	<div class="<?php echo esc_attr( $itemsize );?> <?php echo esc_attr( strtolower( $tax ) ); ?> s_item">
		<div class="grid_item staff_item kt_item_fade_in kad_staff_fade_in postclass">
			<?php if ( has_post_thumbnail( $post->ID ) ) {
				$img_args = array(
					'width' 		=> $image_width,
					'height' 		=> $image_height,
					'crop'			=> true,
					'class'			=> null,
					'alt'			=> null,
					'id'			=> get_post_thumbnail_id(),
					'placeholder'	=> false,
					'schema'		=> false,
					'intrinsic'       => true,
					'intrinsic_max'   => true,
				);
				?>
				<div class="imghoverclass">
				<?php if ( 'true' == $kt_staff_loop[ 'link' ] ) {?>
					<a href="<?php the_permalink(); ?>"> 
				<?php } else if ( 'lightbox' == $kt_staff_loop[ 'link' ] ) {?>
					<a href="<?php echo esc_url( $img[ 'full' ] ); ?>" data-rel="lightbox"  class="lightboxhover"> 
				<?php }?>
						<?php virtue_print_full_image_output( $img_args ); ?>
				<?php if ( 'true' == $kt_staff_loop[ 'link' ] || 'lightbox' == $kt_staff_loop[ 'link' ] ) {?>
					</a>
				<?php } ?>
				</div>
			<?php } ?>

			<div class="staff_item_info">   
				<?php if ( 'true' == $kt_staff_loop[ 'link' ] ) {?>
					<a href="<?php the_permalink(); ?>"> 
				<?php }?>
						<h3><?php the_title();?></h3>
				<?php if ( 'true' == $kt_staff_loop[ 'link' ] ) {?>
					</a>
				<?php } 
					if( 'false' == $kt_staff_loop[ 'content' ] ) {
						the_excerpt();
					} else {
						the_content(); 
					} ?>
				</div>
		</div>
	</div>