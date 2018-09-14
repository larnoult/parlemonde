<?php
/**
 * Base Page
 */


		global $wpdb;
		$sliders = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'ksp_sliders');
?>

		<div class="ksp-sliders-list ksp-table ksp-clearfix">
		<div class="ksp-table-title">
			<h3>
				<?php _e('Kadence Pro Sliders List', 'kadence-slider'); ?>
			</h3>
		</div>
		<div class="ksp_table_body ksp-clearfix">
			<div class="ksp-table-header ksp-clearfix">
				<div class="ksp-table-column ksp_column_01"></div>
				<div class="ksp-table-column ksp_column_02"><?php _e('ID', 'kadence-slider'); ?></div>
				<div class="ksp-table-column ksp_column_03"><?php _e('Name', 'kadence-slider'); ?></div>
				<div class="ksp-table-column ksp_column_04"><?php _e('Shortcode', 'kadence-slider'); ?></div>
				<div class="ksp-table-column ksp_column_05"><?php _e('Actions', 'kadence-slider'); ?></div>
			</div>
		<?php if(!empty($sliders)){
			foreach($sliders as $slider) { ?>
		<div class="ksp_table_content ksp_table_row ksp-clearfix">
				<div class="ksp-table-column ksp_column_01">
					<?php $slides = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'ksp_slides WHERE slider_parent = ' . $slider->id . ' ORDER BY position');
				if(!empty($slides)) {
			       if(isset($slides[0]->background_type_image) && !empty($slides[0]->background_type_image) && $slides[0]->background_type_image != 'none') {
				       	$slide_image_id = ksp_get_image_id_by_link($slides[0]->background_type_image);
				       	echo wp_get_attachment_image( $slide_image_id, 'thumbnail' );
			       } else if(isset($slides[0]->background_type_color) && !empty($slides[0]->background_type_color) && $slides[0]->background_type_color != 'transparent') {
			       	 echo '<div class="kt_placement_sliders" style="background:'.esc_attr($slides[0]->background_type_color).'"></div>';
			       } else {
			       	 echo '<div class="kt_placement_sliders">Placement<br>Image</div>';
			       }
			    } else {
			    	echo '<div class="kt_placement_sliders">Placement<br>Image</div>';
			    }
			    ?>
				</div>
				<div class="ksp-table-column ksp_column_02"><?php echo $slider->id;?></div>
				<div class="ksp-table-column ksp_column_03"><a href="<?php echo '?page=kadenceslider&view=layeredit&id=' . $slider->id . '"';?>"><?php echo esc_html($slider->name)?></a></div>
				<div class="ksp-table-column ksp_column_04">[kadence_slider_pro id="<?php echo esc_attr($slider->id); ?>"]</div>
				<div class="ksp-table-column ksp_column_05">
					<a class="ksp-edit-slider ksp-button ksp-is-success" href="<?php echo '?page=kadenceslider&view=layeredit&id=' . $slider->id . '"';?>"><?php echo __('Edit Slider', 'kadence-slider');?></a>
					<a class="ksp-duplicate-slider ksp-button ksp-is-warning" href="javascript:void(0)" data-duplicate="<?php echo esc_attr($slider->id); ?>"><?php echo __('Duplicate Slider', 'kadence-slider');?></a>
					<a class="ksp-export-slider ksp-button ksp-is-warning" href="javascript:void(0)" data-export="<?php echo esc_attr($slider->id); ?>"><?php echo __('Export Slider', 'kadence-slider');?></a>
					<a class="ksp-delete-slider ksp-button ksp-is-danger" href="javascript:void(0)" data-delete="<?php echo esc_attr($slider->id); ?>"><?php echo __('Delete Slider', 'kadence-slider');?></a>
				</div>
				</div>
				<?php }
			} else { ?>
				<div class="ksp-no-sliders">
			<?php _e('No Sliders found. Please add a new one.', 'kadence-slider'); ?>
		</div>
			<?php }
			?>
			</div>
			</div>



<a class="ksp-button ksp-is-primary ksp-add-slider" href="?page=kadenceslider&view=layeradd"><?php _e('Add Pro Slider', 'kadence-slider'); ?></a>
<a class="ksp-button ksp-is-warning ksp-import-slider" href="javascript:void(0)"><?php _e('Import Slider', 'kadence-slider'); ?></a>
<input id="ksp-import-file" type="file" style="display: none;">

<?php 
$wp_query = null; 
$wp_query = new WP_Query();
$wp_query->query(array(
	'orderby' => 'DESC',
	'order' => 'date',
	'post_type' => 'kadslider',
	'posts_per_page' => '-1'));
	if ( $wp_query ) : ?>
	<div class="ksp-sliders-list ksp-table ksp-clearfix">
		<div class="ksp-table-title">
			<h3>
				<?php _e('Kadence Legacy Sliders List', 'kadence-slider'); ?>
			</h3>
		</div>
		<div class="ksp_table_body ksp-clearfix">
			<div class="ksp-table-header ksp-clearfix">
				<div class="ksp-table-column ksp_column_01"></div>
				<div class="ksp-table-column ksp_column_02"><?php _e('ID', 'kadence-slider'); ?></div>
				<div class="ksp-table-column ksp_column_03"><?php _e('Name', 'kadence-slider'); ?></div>
				<div class="ksp-table-column ksp_column_04"><?php _e('Shortcode', 'kadence-slider'); ?></div>
				<div class="ksp-table-column ksp_column_05"><?php _e('Actions', 'kadence-slider'); ?></div>
			</div>

	<?php 
	while ( $wp_query->have_posts() ) : $wp_query->the_post();
		global $post;
		?>
		<div class="ksp_table_content ksp_table_row ksp-clearfix">
				<div class="ksp-table-column ksp_column_01">
				<?php $slides = get_post_meta( $post->ID, '_kt_slider_slides', true );
				if(!empty($slides[0]['image_id'])) {
					echo wp_get_attachment_image( $slides[0]['image_id'], 'thumbnail' );;
				} else {
				}?>
				</div>
				<div class="ksp-table-column ksp_column_02"><?php echo esc_html($post->ID) ;?></div>
				<div class="ksp-table-column ksp_column_03"><a href="<?php echo get_edit_post_link($post->ID);?>">
				<?php echo get_the_title();?></a></div>
				<div class="ksp-table-column ksp_column_04">[kadence_slider id="<?php echo $post->ID; ?>"]</div>
				<div class="ksp-table-column ksp_column_05">
					<a class="ksp-edit-slider ksp-button ksp-button ksp-is-success" href="<?php echo get_edit_post_link($post->ID);?>"><?php echo __('Edit Slider', 'kadence-slider');?></a>
					<a class="ksp-delete-slider ksp-button ksp-button ksp-is-danger" href="<?php echo get_delete_post_link($post->ID);?>"><?php echo __('Delete Slider', 'kadence-slider');?></a>
				</div>
		</div>
		<?php endwhile; ?>
		</div>
		</div>

		<?php else: ?>
		<div class="ksp-no-sliders">
			<?php _e('No Sliders found. Please add a new one.', 'kadence-slider'); ?>
		</div>
		<?php endif; 
		wp_reset_query();
?>
<a class="ksp-button ksp-is-primary ksp-add-slider" href="<?php echo admin_url( "post-new.php?post_type=kadslider" );?>"><?php _e('Add Legacy Slider', 'kadence-slider'); ?></a>






