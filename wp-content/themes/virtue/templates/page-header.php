<?php 
/**
 * Page Header Template
 *
 * @version 3.2.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div id="pageheader" class="titleclass">
	<div class="container">
		<div class="page-header">
			<h1 class="entry-title" itemprop="name">
				<?php echo wp_kses_post( virtue_title() ); ?>
			</h1>
			<?php 
			if( is_page() ) {
				global $post;
				$bsub = get_post_meta( $post->ID, '_kad_subtitle', true );
				if( !empty( $bsub ) ){
					echo '<p class="subtitle"> '.wp_kses_post( $bsub ).' </p>';
				} 
			} else if( is_category() ) { 
				echo '<p class="subtitle">'.wp_kses_post( category_description() ).' </p>';
			} else if( is_tag() ) {
				echo '<p class="subtitle">'.wp_kses_post( tag_description() ).' </p>';
			} ?>
		</div>
	</div><!--container-->
</div><!--titleclass-->