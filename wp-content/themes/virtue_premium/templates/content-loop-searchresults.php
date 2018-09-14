<?php
/**
 * Simple Search Grid output.
 *
 * @package Virtue Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
global $post;
?>
<div id="post-<?php the_ID(); ?>" class="blog_item search_results_item kt_item_fade_in kad_blog_fade_in grid_item">
	<?php
	if ( has_post_thumbnail( $post->ID ) ) {
		$img_args = array(
			'width'         => 260,
			'height'        => null,
			'crop'          => false,
			'class'         => 'iconhover',
			'alt'           => null,
			'id'            => get_post_thumbnail_id(),
			'placeholder'   => false,
			'schema'        => false,
			'intrinsic'     => true,
			'intrinsic_max' => true,
		);
		?>
		<div class="imghoverclass img-margin-center">
			<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
				<?php virtue_print_full_image_output( $img_args ); ?>
			</a> 
		</div>
	<?php } ?>
	<div class="postcontent">
		<header>
			<a href="<?php the_permalink(); ?>">
				<h5 class="entry-title"><?php the_title(); ?></h5>
			</a>
		</header>
		<div class="entry-content">
			<span class="kt_search_post_type color_gray">
				<?php
				$post_type_obj = get_post_type_object( get_post_type( $post->ID ) );
				echo esc_html( $post_type_obj->labels->singular_name );
				?>
			</span>
		</div>
	</div><!-- search content -->
</div> <!-- Search Item -->
