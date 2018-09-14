<?php 
require_once KADENCE_SLIDER_PATH . 'admin/layers-text.php';
require_once KADENCE_SLIDER_PATH . 'admin/layers-btn.php';
require_once KADENCE_SLIDER_PATH . 'admin/layers-img.php';
function ksp_admin_output_layers($edit, $slider, $slide, $layers) {
?>
	<div class="ksp_layers_list ksp-layers">

		<div class="ksp-layers-actions">
			<div style="float: left;">		
				<a class="ksp-add-text-layer ksp-button ksp-is-warning"><?php _e('Add text', 'kadence-slider'); ?></a>
				<a class="ksp-add-button-layer ksp-button ksp-is-warning"><?php _e('Add button', 'kadence-slider'); ?></a>
				<a class="ksp-add-image-layer ksp-button ksp-is-warning"><?php _e('Add image', 'kadence-slider'); ?></a>
			</div>
			<div style="float: right;">
				<a href="<?php echo add_query_arg(array('action' => 'ksp_preview_slider', 'slider_id' => $slider->id), admin_url('admin-ajax.php')); ?>" class="ksp-live-preview ksp-button ksp-is-success"><?php _e('Live preview', 'kadence-slider'); ?></a>
				<a class="ksp-delete-layer ksp-button ksp-is-danger ksp-is-disabled"><?php _e('Delete layer', 'kadence-slider'); ?></a>
				<a class="ksp-duplicate-layer ksp-button ksp-is-primary ksp-is-disabled"><?php _e('Duplicate layer', 'kadence-slider'); ?></a>
			</div>
			<div style="clear: both;"></div>
		</div>
		<div class="ksp-slide-editing-area"
		<?php if($edit && $slide): 

			if($slide->background_type_image != 'none' and $slide->background_type_image != 'undefined') {
				echo 'data-background-image-src="' . $slide->background_type_image . '"';
			}
			?>
			style="
			width: <?php echo $slider->maxWidth; ?>px;
			height: <?php echo $slider->maxHeight; ?>px;
			<?php if($slide->background_type_image != 'none' and $slide->background_type_image != 'undefined') { ?>
			background-image: url('<?php echo $slide->background_type_image; ?>');
			<?php } ?>
			background-color: <?php echo $slide->background_type_color == 'transparent' ? 'rgb(255, 255, 255)' : $slide->background_type_color; ?>;
			background-position: <?php echo $slide->background_propriety_position; ?>;
			background-repeat: <?php echo $slide->background_repeat; ?>;
			background-size: <?php echo $slide->background_propriety_size; ?>;
			"
		<?php else :
		?>
			style="
				width: <?php echo $slider->maxWidth; ?>px;
				height: <?php echo $slider->maxHeight; ?>px;
				"

		<?php	endif; ?>
		>		
			<?php
			if($edit && $layers != NULL) {
				$data = get_option('kadence_slider');
				$i = 1;
				foreach($layers as $layer) {
					// Wrapper 
					echo '<div style="';
					echo 'z-index: ' . $layer->z_index . ';';
					echo 'left: ' . $layer->data_x . 'px;';
					echo 'top: ' . $layer->data_y . 'px;';
					echo '" class="ksp-layer-wrap ksp-layer-wrap-'.$i.'">';

					
					switch($layer->type) {
						case 'text':
							echo '<div style="';
							if(!empty($layer->font_size)) { echo 'font-size: ' . $layer->font_size . 'px;';}
							if(!empty($layer->font)) { 
								if(isset($data[$layer->font]['font-family'])) { echo 'font-family: ' . $data[$layer->font]['font-family'] . ';'; }
								if(isset($data[$layer->font]['font-weight'])) { echo 'font-weight: ' . $data[$layer->font]['font-weight'] . ';'; }
								if(isset($data[$layer->font]['font-style'])) { echo 'font-style: ' . $data[$layer->font]['font-style'] . ';'; }
							} else {
								if(isset($data['font_option_one']['font-family'])) { echo 'font-family: ' . $data['font_option_one']['font-family'] . ';'; }
								if(isset($data['font_option_one']['font-weight'])) { echo 'font-weight: ' . $data['font_option_one']['font-weight'] . ';'; }
								if(isset($data['font_option_one']['font-style'])) { echo 'font-style: ' . $data['font_option_one']['font-style'] . ';'; }
							}
							if(!empty($layer->letter_spacing)) { echo 'letter-spacing: ' . $layer->letter_spacing . 'px;';}
							if(!empty($layer->font_size)) { echo 'line-height: ' . $layer->line_height . 'px;';}
							if(!empty($layer->font_color)) { echo 'color: ' . $layer->font_color . ';';}
							if(!empty($layer->text_shadow)) {
								$shadow = 'kt-t-shadow-'.$layer->text_shadow;
							} else {
								$shadow = 'kt-t-shadow-none';
							}
							echo '" class="ksp-layer ksp-text-layer '.esc_attr($shadow).'" >';

								echo wp_kses_post($layer->inner_html);
							echo '</div>';

						break;
						case 'button':
							echo '<a style="';
							if(!empty($layer->font_size)) { echo 'font-size: ' . $layer->font_size . 'px;';}
							if(!empty($layer->font)) { 
								if(isset($data[$layer->font]['font-family'])) { echo 'font-family: ' . $data[$layer->font]['font-family'] . ';'; }
								if(isset($data[$layer->font]['font-weight'])) { echo 'font-weight: ' . $data[$layer->font]['font-weight'] . ';'; }
								if(isset($data[$layer->font]['font-style'])) { echo 'font-style: ' . $data[$layer->font]['font-style'] . ';'; }
							} else {
								if(isset($data['font_option_one']['font-family'])) { echo 'font-family: ' . $data['font_option_one']['font-family'] . ';'; }
								if(isset($data['font_option_one']['font-weight'])) { echo 'font-weight: ' . $data['font_option_one']['font-weight'] . ';'; }
								if(isset($data['font_option_one']['font-style'])) { echo 'font-style: ' . $data['font_option_one']['font-style'] . ';'; }
							}
							if(isset($layer->line_height)) { echo 'line-height: ' . $layer->line_height . 'px;';}
							if(isset($layer->border_radius)) { echo 'border-radius: ' . $layer->border_radius . 'px;';}
							if(isset($layer->border_width)) { echo 'border: ' . $layer->border_width . 'px solid;';}
							if(isset($layer->font_color)) { echo 'color: ' . $layer->font_color . ';';}
							if(isset($layer->letter_spacing)) { echo 'letter-spacing: ' . $layer->letter_spacing . 'px;';}
							if(isset($layer->padding)) { echo 'padding-left: ' . $layer->padding . 'px; padding-right: ' . $layer->padding . 'px;';}
							if(isset($layer->border_color)) { echo 'border-color: ' . $layer->border_color . ';';}
							if(isset($layer->background_color)) { echo 'background-color: ' . $layer->background_color . ';';}
							// link
							if(!empty($layer->link)) { echo '" href="' . stripslashes($layer->link).'"'; } else { echo '" href="#"';}
							// normal
							if(isset($layer->font_color)) { echo ' data-color="' . $layer->font_color . '"';}
							if(isset($layer->border_color)) { echo ' data-border-color="' . $layer->border_color . '"';}
							if(isset($layer->background_color)) { echo ' data-background-color="' . $layer->background_color . '"';}
							//hover
							if(isset($layer->font_hover_color)) { echo ' data-hcolor="' . $layer->font_hover_color . '"';}
							if(isset($layer->border_hover_color)) { echo ' data-hborder-color="' . $layer->border_hover_color . '"';}
							if(isset($layer->background_hover_color)) { echo ' data-hbackground-color="' . $layer->background_hover_color . '"';}
							echo ' class="ksp-layer ksp-button-layer" >';

								echo wp_kses_post($layer->inner_html);
							echo '</a>';

						break;
						case 'image':
								
								echo '<img src="'.$layer->image_src.'"" alt="'.$layer->image_alt.'" width="'.$layer->width.'" height="'.$layer->height.'" class="ksp-layer ksp-image-layer">';
							
						break;
					}
					
					echo '</div>';
					$i ++;
				}
			}
			?>
		</div>
		
		
		<div class="ksp-layers-list">
			<?php
			if($edit && $layers != NULL) {
				foreach($layers as $layer) {
					switch($layer->type) {
						case 'text':
							echo '<div class="ksp-layer-settings ksp-text-layer-settings" style="display: none;">';
							ksp_admin_output_layer_text($layer);
							echo '</div>';
							break;
						case 'button':
							echo '<div class="ksp-layer-settings ksp-button-layer-settings" style="display: none;">';
							ksp_admin_output_layer_button($layer);
							echo '</div>';
						break;
						case 'image':
							echo '<div class="ksp-layer-settings ksp-image-layer-settings" style="display: none;">';
							ksp_admin_output_layer_image($layer);
							echo '</div>';
							break;
					}
				}
			}
			echo '<div class="ksp-void-layer-settings ksp-void-text-layer-settings ksp-layer-settings ksp-text-layer-settings">';
			ksp_admin_output_layer_text(false);
			echo '</div>';
			echo '<div class="ksp-void-layer-settings ksp-void-button-layer-settings ksp-layer-settings ksp-button-layer-settings">';
			ksp_admin_output_layer_button(false);
			echo '</div>';
			echo '<div class="ksp-void-layer-settings ksp-void-image-layer-settings ksp-layer-settings ksp-image-layer-settings">';
			ksp_admin_output_layer_image(false);
			echo '</div>';
			?>
		</div>

	</div>
<?php
}

