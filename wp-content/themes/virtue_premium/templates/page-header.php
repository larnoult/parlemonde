<div id="pageheader" class="titleclass">
	<div class="container">
		<div class="page-header">
			<h1 class="entry-title" itemprop="name">
				<?php 
					echo apply_filters('kadence_page_title', virtue_title() ); 
					?>
			</h1>
			<?php 
			if(kadence_display_page_breadcrumbs()) {
				echo '<div class="page-bread-container clearfix">';
					kadence_breadcrumbs();
				echo '</div>';
			} 
			if(is_page()) {
				global $post;
				$bsub = get_post_meta( $post->ID, '_kad_subtitle', true ); 
				if(!empty($bsub)) {
					echo '<p class="subtitle"> '.__($bsub).' </p>'; 
				}
			} else if(is_category()) {
				echo '<p class="subtitle">'.__(category_description()).' </p>';
			} else if(is_tag()) {
				echo '<p class="subtitle">'.__(tag_description()).' </p>';
			}
			?>
		</div>
	</div>
</div> <!--titleclass-->