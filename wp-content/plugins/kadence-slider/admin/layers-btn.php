<?php 

function ksp_admin_output_layer_button($layer) {
	$void = !$layer ? true : false;
	
	$animations = array(
		'fadeIn' => __('fadeIn', 'kadence-slider'),
		'fadeInDown' => __('fadeInDown', 'kadence-slider'),
		'fadeInDownBig' => __('fadeInDownBig', 'kadence-slider'),
		'fadeInLeft' => __('fadeInLeft', 'kadence-slider'),
		'fadeInLeftBig' => __('fadeInLeftBig', 'kadence-slider'),
		'fadeInRight' => __('fadeInRight', 'kadence-slider'),
		'fadeInRightBig' => __('fadeInRightBig', 'kadence-slider'),
		'fadeInUp' => __('fadeInUp', 'kadence-slider'),
		'fadeInUpBig' => __('fadeInUpBig', 'kadence-slider'),
		'bounceIn' => __('bounceIn', 'kadence-slider'),
		'bounceInDown' => __('bounceInDown', 'kadence-slider'),
		'bounceInLeft' => __('bounceInLeft', 'kadence-slider'),
		'bounceInRight' => __('bounceInRight', 'kadence-slider'),
		'bounceInUp' => __('bounceInUp', 'kadence-slider'),
		'rotateIn' => __('rotateIn', 'kadence-slider'),
		'rotateInDownLeft' => __('rotateInDownLeft', 'kadence-slider'),
		'rotateInDownRight' => __('rotateInDownRight', 'kadence-slider'),
		'rotateInUpLeft' => __('rotateInUpLeft', 'kadence-slider'),
		'rotateInUpRight' => __('rotateInUpRight', 'kadence-slider'),
		'slideInDown' => __('slideInDown', 'kadence-slider'),
		'slideInLeft' => __('slideInLeft', 'kadence-slider'),
		'slideInRight' => __('slideInRight', 'kadence-slider'),
		'slideInUp' => __('slideInUp', 'kadence-slider'),
		'rollIn' => __('rollIn', 'kadence-slider'),
	);
	$animations_out = array(
		'fadeOut' => __('fadeOut', 'kadence-slider'),
		'fadeOutDown' => __('fadeOutDown', 'kadence-slider'),
		'fadeOutDownBig' => __('fadeOutDownBig', 'kadence-slider'),
		'fadeOutLeft' => __('fadeOutLeft', 'kadence-slider'),
		'fadeOutLeftBig' => __('fadeOutLeftBig', 'kadence-slider'),
		'fadeOutRight' => __('fadeOutRight', 'kadence-slider'),
		'fadeOutRightBig' => __('fadeOutRightBig', 'kadence-slider'),
		'fadeOutUp' => __('fadeOutUp', 'kadence-slider'),
		'fadeOutUpBig' => __('fadeOutUpBig', 'kadence-slider'),
		'bounceOut' => __('bounceOut', 'kadence-slider'),
		'bounceOutDown' => __('bounceOutDown', 'kadence-slider'),
		'bounceOutLeft' => __('bounceOutLeft', 'kadence-slider'),
		'bounceOutRight' => __('bounceOutRight', 'kadence-slider'),
		'bounceOutUp' => __('bounceOutUp', 'kadence-slider'),
		'rotateOut' => __('rotateOut', 'kadence-slider'),
		'rotateOutDownLeft' => __('rotateOutDownLeft', 'kadence-slider'),
		'rotateOutDownRight' => __('rotateOutDownRight', 'kadence-slider'),
		'rotateOutUpLeft' => __('rotateOutUpLeft', 'kadence-slider'),
		'rotateOutUpRight' => __('rotateOutUpRight', 'kadence-slider'),
		'slideOutDown' => __('slideOutDown', 'kadence-slider'),
		'slideOutLeft' => __('slideOutLeft', 'kadence-slider'),
		'slideOutRight' => __('slideOutRight', 'kadence-slider'),
		'slideOutUp' => __('slideOutUp', 'kadence-slider'),
		'rollOut' => __('rollOut', 'kadence-slider'),
	);
	$animation_time = array(
		'0' => __('0ms', 'kadence-slider'),
		'300' => __('300ms', 'kadence-slider'),
		'600' => __('600ms', 'kadence-slider'),
		'900' => __('900ms', 'kadence-slider'),
		'1200' => __('1200ms', 'kadence-slider'),
		'1500' => __('1500ms', 'kadence-slider'),
		'1800' => __('1800ms', 'kadence-slider'),
		'2100' => __('2100ms', 'kadence-slider'),
		'2400' => __('2400ms', 'kadence-slider'),
		'2700' => __('2700ms', 'kadence-slider'),
		'3000' => __('3000ms', 'kadence-slider'),
	);
	$animation_delay = array(
		'0' => __('0ms', 'kadence-slider'),
		'300' => __('300ms', 'kadence-slider'),
		'600' => __('600ms', 'kadence-slider'),
		'900' => __('900ms', 'kadence-slider'),
		'1200' => __('1200ms', 'kadence-slider'),
		'1500' => __('1500ms', 'kadence-slider'),
		'1800' => __('1800ms', 'kadence-slider'),
		'2100' => __('2100ms', 'kadence-slider'),
		'2400' => __('2400ms', 'kadence-slider'),
		'2700' => __('2700ms', 'kadence-slider'),
		'3000' => __('3000ms', 'kadence-slider'),
		'3300' => __('3300ms', 'kadence-slider'),
		'3600' => __('3600ms', 'kadence-slider'),
		'3900' => __('3900ms', 'kadence-slider'),
		'4200' => __('4200ms', 'kadence-slider'),
		'4500' => __('4500ms', 'kadence-slider'),
		'4800' => __('4800ms', 'kadence-slider'),
		'5100' => __('5100ms', 'kadence-slider'),
		'5400' => __('5400ms', 'kadence-slider'),
		'5700' => __('5700ms', 'kadence-slider'),
		'6000' => __('6000ms', 'kadence-slider'),
	);
	
	?>
	<div class="ksp-layer-settings-list ksp-button-layer-settings-list ksp-table">
		<div class="ksp-list-title">
				<?php _e('Button Layer Options', 'kadence-slider'); ?>
		</div>
		
		<div class="ksp-row layer-settings-contain">
			<div class="ksp-column ksp-full">
				<div class="ksp-content">
					<div class="ksp-inner-row ksp-btn-options ksp-clearfix">
					<div class="ksp-border-wrap ksp-clearfix">
						<div class="ksp-column-outer ksp-btn-text">
							<div class="ksp-column ksp-odd">
									<span class="ksp-inner-row-label">
										<?php _e('Text', 'kadence-slider'); ?>
									</span>
									<?php					
									if($void){
										 echo '<textarea class="ksp-layer-inner_html">' . __('Button Layer', 'kadence-slider') . '</textarea>';
									} else {
										echo '<textarea class="ksp-layer-inner_html">' . $layer->inner_html . '</textarea>';
									} ?>
							</div>
							<div class="ksp-column ksp-even ksp-btn-font">
									<span class="ksp-inner-row-label">
										<?php _e('Font', 'kadence-slider'); ?>
									</span>
									<?php
									$data = get_option('kadence_slider');
									if($void){
										$selected = 'font_option_one';
									} else {
										$selected = $layer->font;
									}

									echo ksp_font_list_select($selected );
									?>
							</div>
						</div>
						<div class="ksp-column-outer ksp-odd ksp-btn-colors">
							<div class="ksp-column ksp-even">
									<span class="ksp-inner-row-label">
										<?php _e('Font Color', 'kadence-slider'); ?>
									</span>
									<?php if($void) { ?>
										<input class="ksp-layer-font_color ksp-button ksp-btn-color" type="text" value="#ffffff" />
									<?php }else { ?>
										<input class="ksp-layer-font_color ksp-button ksp-bcolor-wp-color-picker ksp-btn-color" type="text" value="<?php echo $layer->font_color; ?>" />
									<?php } ?>	
							</div>
							<div class="ksp-column ksp-odd">
									<span class="ksp-inner-row-label">
										<?php _e('Background Color', 'kadence-slider'); ?>
									</span>
									<?php if($void) { ?>
										<input class="ksp-layer-background_color ksp-button ksp-btn-color" type="text" value="#000000" />
									<?php }else { ?>
										<input class="ksp-layer-background_color ksp-button ksp-background-wp-color-picker ksp-btn-color" type="text" value="<?php echo $layer->background_color; ?>" />
									<?php } ?>	
							</div>
							<div class="ksp-column ksp-even">
									<span class="ksp-inner-row-label">
										<?php _e('Border Color', 'kadence-slider'); ?>
									</span>
									<?php if($void) { ?>
										<input class="ksp-layer-border_color ksp-button ksp-btn-color" type="text" value="#000000" />
									<?php }else { ?>
										<input class="ksp-layer-border_color ksp-button ksp-border-wp-color-picker ksp-btn-color" type="text" value="<?php echo $layer->border_color; ?>" />
									<?php } ?>	
							</div>

							<div class="ksp-clearfix">
								<div class="ksp-column ksp-odd">
										<span class="ksp-inner-row-label">
											<?php _e('Font Hover Color', 'kadence-slider'); ?>
										</span>
										<?php if($void) { ?>
											<input class="ksp-layer-font_hover_color ksp-button ksp-btn-color" type="text" value="#ffffff" />
										<?php }else { ?>
											<input class="ksp-layer-font_hover_color ksp-button ksp-hcolor-wp-color-picker ksp-btn-color" type="text" value="<?php echo $layer->font_hover_color; ?>" />
										<?php } ?>	
								</div>
								<div class="ksp-column ksp-even">
										<span class="ksp-inner-row-label">
											<?php _e('Background Hover Color', 'kadence-slider'); ?>
										</span>
										<?php if($void) { ?>
											<input class="ksp-layer-background_hover_color ksp-button ksp-btn-color" type="text" value="#444444" />
										<?php }else { ?>
											<input class="ksp-layer-background_hover_color ksp-button ksp-hbackground-wp-color-picker ksp-btn-color" type="text" value="<?php echo $layer->background_hover_color; ?>" />
										<?php } ?>	
								</div>
								<div class="ksp-column ksp-odd">
										<span class="ksp-inner-row-label">
											<?php _e('Border Hover Color', 'kadence-slider'); ?>
										</span>
										<?php if($void) { ?>
											<input class="ksp-layer-border_hover_color ksp-button ksp-btn-color" type="text" value="#444444" />
										<?php }else { ?>
											<input class="ksp-layer-border_hover_color ksp-button ksp-hborder-wp-color-picker ksp-btn-color" type="text" value="<?php echo $layer->border_hover_color; ?>" />
										<?php } ?>	
								</div>
							</div>
						</div>

						<div class="ksp-clearfix ksp_fonts_container">
							
							<div class="ksp-odd ksp-btn-fontstyling">
								<div class="ksp-column ksp-odd">
									<span class="ksp-inner-row-label">
										<?php _e('Font Size', 'kadence-slider'); ?>
									</span>
									<?php
									if($void) { 
										echo '<input class="ksp-layer-font_size kt_small_input" type="number" value="16" />';
									} else {
										echo '<input class="ksp-layer-font_size kt_small_input" type="number" value="' . $layer->font_size .'" />';
									} ?>
									px
								</div>
								<div class="ksp-column ksp-even">
										<span class="ksp-inner-row-label">
											<?php _e('Line height', 'kadence-slider'); ?>
										</span>
										<?php
										if($void){
											echo '<input class="ksp-layer-line_height kt_small_input" type="number" value="30" />';
										} else {
											echo '<input class="ksp-layer-line_height kt_small_input" type="number" value="' . $layer->line_height .'" />';
										}
										?>
										px
								</div>
								<div class="ksp-column ksp-odd">
										<span class="ksp-inner-row-label">
											<?php _e('Letter Spacing', 'kadence-slider'); ?>
										</span>
										<?php
										if($void){
											echo '<input class="ksp-layer-letter_spacing kt_small_input" type="number" value="0" />';
										} else {
											echo '<input class="ksp-layer-letter_spacing kt_small_input" type="number" value="' . $layer->letter_spacing .'" />';
										}
										?>
										px
								</div>
								<div class="ksp-column ksp-even">
										<span class="ksp-inner-row-label">
											<?php _e('Border Width', 'kadence-slider'); ?>
										</span>
										<?php
										if($void){
											echo '<input class="ksp-layer-border_width kt_small_input" type="number" value="2" />';
										} else {
											echo '<input class="ksp-layer-border_width kt_small_input" type="number" value="' . $layer->border_width .'" />';
										}
										?>
										px
								</div>
								<div class="ksp-column ksp-odd">
										<span class="ksp-inner-row-label">
											<?php _e('Border Radius', 'kadence-slider'); ?>
										</span>
										<?php
										if($void){
											echo '<input class="ksp-layer-border_radius kt_small_input" type="number" value="4" />';
										} else {
											echo '<input class="ksp-layer-border_radius kt_small_input" type="number" value="' . $layer->border_radius .'" />';
										}
										?>
										px
								</div>
								<div class="ksp-column ksp-even">
										<span class="ksp-inner-row-label">
											<?php _e('Left and Right Padding', 'kadence-slider'); ?>
										</span>
										<?php
										if($void){
											echo '<input class="ksp-layer-padding kt_small_input" type="number" value="12" />';
										} else {
											echo '<input class="ksp-layer-padding kt_small_input" type="number" value="' . $layer->padding .'" />';
										}
										?>
										px
								</div>
							</div>
						</div>
					</div>
					</div>
				
					<div class="ksp-inner-title">
						<span>
							<?php _e('Position Options', 'kadence-slider'); ?>
						</span>
					</div>
					<div class="ksp-inner-row ksp-position-options ksp-clearfix">
						<div class="ksp-border-wrap ksp-clearfix">
							<div class="ksp-column ksp-odd">
									<span class="ksp-inner-row-label">
										<?php _e('Layer Position Horizontal', 'kadence-slider'); ?>
									</span>
									<?php
									if($void) { 
										echo '<input class="ksp-layer-data_x kt_small_input" type="number" value="0" />';
									} else {
										echo '<input class="ksp-layer-data_x kt_small_input" type="number" value="' . $layer->data_x .'" />';
									} ?>
									px
							</div>
							<div class="ksp-column ksp-even">
								<a class="ksp-layer-center-x ksp-align-btn">Align Horizontally Center</a>
							</div>
							<div class="ksp-column ksp-odd">
								<a class="ksp-layer-center-y ksp-align-btn">Align Vertially Center</a>
							</div>
							<div class="ksp-column ksp-even">
									<span class="ksp-inner-row-label">
										<?php _e('Layer Position Vertical', 'kadence-slider'); ?>
									</span>
									<?php
									if($void){
										echo '<input class="ksp-layer-data_y kt_small_input" type="number" value="0" />';
									} else {
										echo '<input class="ksp-layer-data_y kt_small_input" type="number" value="' . $layer->data_y .'" />';
									}
									?>
									px
							</div>		
						</div>
					</div>			
					<div class="ksp-inner-title">
							<span>
								<?php _e('Animation Options', 'kadence-slider'); ?>
							</span>
					</div>
					<div class="ksp-inner-row ksp-animation-options ksp-clearfix">
						<div class="ksp-border-wrap ksp-clearfix">
							<div class="ksp-column ksp-odd">
									<span class="ksp-inner-row-label">
										<?php _e('Delay', 'kadence-slider'); ?>
									</span>
									<select class="ksp-layer-data_delay">
												<?php
												foreach($animation_delay as $key => $value) {
													echo '<option value="' . $key . '"';
													if(($void && $key == '0ms') || (!$void && $layer->data_delay == $key)) {
														echo ' selected';
													}
													echo '>' . $value . '</option>';
												}
												?>
									</select>
							</div>
							<div class="ksp-column ksp-even">
									<span class="ksp-inner-row-label">
										<?php _e('Animation speed', 'kadence-slider'); ?>
									</span>
									<select class="ksp-layer-data_ease">
												<?php
												foreach($animation_time as $key => $value) {
													echo '<option value="' . $key . '"';
													if(($void && $key == '900ms') || (!$void && $layer->data_ease == $key)) {
														echo ' selected';
													}
													echo '>' . $value . '</option>';
												}
												?>
									</select>
							</div>
							<div class="ksp-column ksp-odd">
										<span class="ksp-inner-row-label">
										<?php _e('Animation in', 'kadence-slider'); ?>
										</span>
										<select class="ksp-layer-data_in">
											<?php
											foreach($animations as $key => $value) {
												echo '<option value="' . $key . '"';
												if(($void && $key == 'fadeIn') || (!$void && $layer->data_in == $key)) {
													echo ' selected';
												}
												echo '>' . $value . '</option>';
											}
											?>
										</select>
							</div>
							<div class="ksp-column ksp-even">
								<span class="ksp-inner-row-label">
									<?php _e('Animation out', 'kadence-slider'); ?>
								</span>
								<select class="ksp-layer-data_out">
									<?php
									foreach($animations_out as $key => $value) {
										echo '<option value="' . $key . '"';
										if(($void && $key == 'fadeOut') || (!$void && $layer->data_out == $key)) {
											echo ' selected';
										}
										echo '>' . $value . '</option>';
									}
									?>
								</select>
							</div>
						</div>
					</div>
					<div class="ksp-inner-title">
							<span>
								<?php _e('Layer link and z-index', 'kadence-slider'); ?>
							</span>
					</div>
					<div class="ksp-inner-row ksp-link-options ksp-clearfix">
						<div class="ksp-border-wrap ksp-clearfix">
							<div class="ksp-column ksp-odd">
								<span class="ksp-inner-row-label">
									<?php _e('Link', 'kadence-slider'); ?>
									</span>
								<?php
								if($void){
									echo '<input class="ksp-layer-link" type="text" value="#" />';
								} else {
									echo '<input class="ksp-layer-link" type="text" value="' . stripslashes($layer->link) .'" />';
								} 
								if($void) {
									echo '<input class="ksp-layer-link_new_tab" type="checkbox" />' . __('Open link in a new tab', 'kadence-slider');
								} else {
									if($layer->link_new_tab) {
										echo '<input class="ksp-layer-link_new_tab" type="checkbox" checked />' . __('Open link in a new tab', 'kadence-slider');
									} else {
										echo '<input class="ksp-layer-link_new_tab" type="checkbox" />' . __('Open link in a new tab', 'kadence-slider');
									}
								}
								?>
							</div>
							<div class="ksp-column ksp-even">
									<span class="ksp-inner-row-label">
										<?php _e('z-index', 'kadence-slider'); ?>
									</span>
									<?php
									if($void) {
										echo '<input class="ksp-layer-z_index kt_small_input" type="text" value="1" />';
									} else {
										echo '<input class="ksp-layer-z_index kt_small_input" type="text" value="' . $layer->z_index .'" />';
									}
									?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}