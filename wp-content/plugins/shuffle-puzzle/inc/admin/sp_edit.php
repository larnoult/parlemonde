<?php
if (isset($_REQUEST['settings-updated'])) {
	$msp_message = '<div class="msp_updated" id="msp_message">Settings Saved</div>';
}


//-- Edit ShufflePuzzle (Function)
//-------------------------------
function edit_shufflepuzzle(){
	/*global $wpdb;
	$table_name = $wpdb->prefix . "shufflepuzzle";
	$column_name = 'option_name';
	
	//foreach ( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
		//echo '<small>'.$wpdb->get_var("show tables like '$table_name'").'</small><br>';
	$msp_data = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id");
	foreach ($msp_data as $data) {
		echo '<small>'.$data->option_name.'</small><br>';
		
	}*/

	global $msp_message;
	//global $option3;

	$option=$_GET['edit']; ?>

    <?php
    if(get_option('sp_load_highlighter') && get_option('sp_load_highlighter') == 'yes'){
        echo '<script type="text/javascript">SyntaxHighlighter.all();</script>';
    }
    ?>
        
	<div id="msp_editwrap">
		<?php _e($msp_message) ?>
		<h2 style="display: block; position: relative;">
			<input readonly type="text" onClick="this.setSelectionRange(0, this.value.length)" name="sp-shortcode" value="[sp name='<?php _e($option); ?>']">

		</h2>
		<form class="msp_editform" method="post" action="options.php">
			<?php
				settings_fields('options');
				//wp_nonce_field('update-options');
				if($_GET["edit"]){
					$option=$_GET['edit'];
				}else{
					$option='sp_main';
				}
				$options = get_option($option);

				//print_r($options);
				//echo '<pre>'.print_r($options).'</pre>';

				$sp_images = array();

				foreach($options as $key=> $value){
					if(substr($key,0,6) == 'image_' && $key!='image_999'){
						array_push($sp_images, $value);
					}
				}

				$m_til = preg_split ('/,/', $options['menu_grid']);
				$m_shu = preg_split ('/,/', $options['menu_shuffle']);
			?>

			<div class="setting_block">
				<h2 class="title_m">Main Settings</h2>
				<div class="sliding">
					<table style="width: 100%" class="msp_edittbl">
						<tbody>
						<tr>
							<td>Width/Height:</td>
							<td>
								<input class="w_img" type="text" name="<?php echo $option; ?>[w_img]" value="<?php echo $options['w_img']; ?>" size="5" maxlength="4" />
								<input class="h_img" type="text" name="<?php echo $option; ?>[h_img]" value="<?php echo $options['h_img']; ?>" size="5" maxlength="4" />
							</td>
						</tr>
						<tr>
							<td>Responsive:</td>
							<td>
								<input type="checkbox" name="<?php echo $option; ?>[auto_size]" value="true"<?php if($options['auto_size'] == 'true'){ echo 'checked="checked"';} ?>>
							</td>
						</tr>
						<tr>
							<td>Tiles horizontal/vertical:</td>
							<td>
								<input type="text" name="<?php echo $option; ?>[tiles_h]" value="<?php echo $options['tiles_h']; ?>" size="5"  />
								<input type="text" name="<?php echo $option; ?>[tiles_v]" value="<?php echo $options['tiles_v']; ?>" size="5" />
							</td>
						</tr>
						<tr>
							<td>Gap between tiles:</td>
							<td>
								<input type="checkbox" name="<?php echo $option; ?>[gap]" value="true"<?php if($options['gap'] == 'true'){ echo 'checked="checked"';} ?>>
							</td>
						</tr>
						<tr>
							<td>Duration of moving:</td>
							<td><input type="text" name="<?php echo $option; ?>[duration]" value="<?php echo $options['duration']; ?>" size="5"  />&nbsp;<small>(<?php _e('in milliseconds','msp') ?>)</small></td>
						</tr>
						<tr>
							<td>Background Color:</td>
							<td><div class="msp_colwrap"><input type="text" id="msp_bgColor" class="msp_color_inp" value="<?php if($options['bgColor']) echo $options['bgColor']; ?>" name="<?php echo $option; ?>[bgColor]" /><div class="msp_colsel msp_bgColor"></div></div>
						<tr>
						<tr>
							<td>Background opacity:</td>
							<td><input type="text" name="<?php echo $option; ?>[bgOpacity]" value="<?php echo $options['bgOpacity']; ?>" size="5" />&nbsp;<small>(<?php _e('from 0 till 1','msp') ?>)</small></td>
						</tr>
						<tr>
							<td>Image opacity:</td>
							<td><input type="text" name="<?php echo $option; ?>[imgBgOpacity]" value="<?php echo $options['imgBgOpacity']; ?>" size="5" />&nbsp;<small>(<?php _e('from 0 till 1','msp') ?>)</small></td>
						</tr>
						<tr>
							<td>Shuffle number:</td>
							<td><input type="text" name="<?php echo $option; ?>[shuffleNum]" value="<?php echo $options['shuffleNum']; ?>" size="5" /></td>
						</tr>
						<tr>
							<td>Shuffling on the start:</td>
							<td>
								<input type="checkbox" name="<?php echo $option; ?>[showStart]" value="true"<?php if($options['showStart'] == 'true'){ echo 'checked="checked"';} ?>>
							</td>
						</tr>

						<tr>
							<td>Stop after render:</td>
							<td>
								<input type="checkbox" name="<?php echo $option; ?>[sh_stop]" value="true"<?php if($options['sh_stop'] == 'true'){ echo 'checked="checked"';} ?>>
							</td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>

			<div class="setting_block">
				<h2 class="title_m">Images</h2>

				<div class="sliding">
					<ul id="sortable">
						<?php if ( ! empty( $sp_images ) ) {
								foreach ($sp_images as $key => $image) {?>
									<li class="ui-sort-bg">
										<!--span class="ui-drag"></span-->
										<div class="img_sp">
											<a href="#" class="add-image dashicons-before dashicons-dashboard"></a>
											<span class="ui-num"><?php echo ($key+1) ?></span>
											<img class="img_min" src="<?php echo $options['image_'.($key+1)] ?>"/>
										</div>
										<input type="text" name="<?php echo $option.'[name_'.($key+1).']'?>" value="<?php echo $options['name_'.($key+1)] ?>" size="15" maxlength="15"/>
										<input type="hidden" name="<?php echo $option.'[image_'.($key+1).']'?>" value="<?php echo $options['image_'.($key+1)] ?>" class="widefat"/>
										<a href="#" class="remove-image dashicons-before dashicons-dashboard"></a>
									</li>
									<?php
								}
							}
						?>
					</ul>
					<a href="#" id="add-img" name="<?php echo $option; ?>" class="button">Add Image</a>

					<div class="reserve_til">
						<li class="ui-state-default">
							<span>4x4</span>
							<a href="#" class="remove-tile">x</a>
						</li>
					</div>
					<div class="reserve_shu">
						<li class="ui-shuffle">
							<span>Other:20</span>
							<a href="#" class="remove-shu">x</a>
						</li>
					</div>

					<div class="reserve">
						<li class="ui-sort-bg">
							<div class="img_sp add">
								<a href="#" class="add-image dashicons-before dashicons-dashboard"></a>
								<span class="ui-num">99</span>
								<img class="img_min" src="" />
							</div>
							<input type="text" name="<?php echo $option; ?>[name_999]" value="Name" size="15" maxlength="15"/>
							<input type="hidden" name="<?php echo $option; ?>[image_999]" value="" class="widefat"/>
							<a href="#" class="remove-image dashicons-before dashicons-dashboard"></a>
						</li>
					</div>
					<div class="_poof"></div>
				</div>
			</div>

			<div class="setting_block">
				<h2 class="title_m">Menu</h2>
				<div class="sliding">
					<table style="width: 100%" class="msp_edittbl">
							<tbody>
							<tr>
								<td>Menu visible:</td>
								<td>
                                    <input type="checkbox" name="<?php echo $option; ?>[menuVisible]" value="true"<?php if($options['menuVisible'] == 'true'){ echo 'checked="checked"';} ?>>
								</td>
							</tr>
							<tr>
								<td>Shuffle button:</td>
								<td><input type="text" name="<?php echo $option; ?>[menuNameShuffle]" value="<?php echo $options['menuNameShuffle']; ?>" size="5" /></td>
							</tr>
							<tr>
								<td>Grid button:</td>
								<td><input type="text" name="<?php echo $option; ?>[menuNameGrid]" value="<?php echo $options['menuNameGrid']; ?>" size="5" /></td>
							</tr>
							<tr>
								<td>Image button:</td>
								<td><input type="text" name="<?php echo $option; ?>[menuNameImage]" value="<?php echo $options['menuNameImage']; ?>" size="5" /></td>
							</tr>
							</tbody>
						</table>

					<br><b>Array of pieces:</b><br>
					<ul id="sortable_msh">
						<input class ="m_g" type="hidden" name="<?php echo $option; ?>[menu_grid]" value="<?php echo $options['menu_grid']; ?>"/>
						<?php if ( ! empty( $m_til ) ) { foreach($m_til as $value){?>
							<li class="ui-state-default">
								<span><?php echo $value ?></span>
								<a href="#" class="remove-tile">x</a>
							</li>
						<?php }} ?>
					</ul>
					<a href="#" id="add-menu-tiles" name="<?php echo $option; ?>" class="button">Add</a>
					<br><b>Names of submenu and number of shuffle:</b><br>
					<ul id="sortable_mshuff">
						<input class ="m_sh" type="hidden" name="<?php echo $option; ?>[menu_shuffle]" value="<?php echo $options['menu_shuffle']; ?>"/>
						<?php if ( ! empty( $m_shu ) ) { foreach($m_shu as $value){?>
							<li class="ui-shuffle">
								<span><?php echo $value ?></span>
								<a href="#" class="remove-shu">x</a>
							</li>
						<?php }} ?>
					</ul>
					<a href="#" id="add-menu-shuffle" name="<?php echo $option; ?>" class="button">Add</a>
					<br>
					<div id="m_fild">
							<input type="text" name="mt1" value="" size="1" maxlength="2" class="spl1" />x<input type="text" name="mt2" value="" size="1" maxlength="2" class="spl2" /><img class="ok_img" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAMAAAAoLQ9TAAABBVBMVEVKqQVBmwFNqwhNrQhKqAVOrQlNqwc/mgBDngJJpAlcsR5MqghQrA1BjApFpABBnABEngJcsR9WrhU+mABttjZotjAAAAAhTQJMrwRDnQNMqQd2ukRlsis/mgAZOQJFnwRLqwYIEgFHoQdarh1dsSBKpApDnQIAAAA8mABCnQEAAAAAAABBmwBAmgBDngJAnABBnAAAAAAAAAA9mAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABkyBpYwQxdxhFYwwtaww1axQ1TugphyRVhxRhZwg1awA9hxxdZwwxixhh80D5dwRSJ01GY2WZ4zjij3nWn3n2S2F6q4YGt4oWp4X2h3XOuoJgPAAAAPXRSTlPHMv3kfvGmECha9rCtmBEDP6uyAuLpGECDUODz4CF9P6VET9KOVygRASkHEBkBJDcPDggPDAkFBgQDAgEAECniJQAAAJVJREFUeNpdzlUOQjEURdHi7u7uzrMa7u7MfygkfQm0nM+V3NwN6N8E6PVFqJa6AmRS7woPUvj5KPMQuN5e6foPHKfzPVLsMMi57dTmPFyCEnvbzh9HVmpe7V0hvaOR3E0XBtMc+L5h8fHMuLZ4udLEZjvxC+mxpYcSogNWh3Itmi20BrKiQUwAJRipCmxqECFM2Im4DyVuNf8V/b/HAAAAAElFTkSuQmCC">
						</div>

					<div id="m_fild_sh">
						<input type="text" name="mt1" value="" size="10" maxlength="20" class="spsl1" />:<input type="text" name="mt2" value="" size="2" maxlength="3" class="spsl2" /><img class="ok_img_" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAMAAAAoLQ9TAAABBVBMVEVKqQVBmwFNqwhNrQhKqAVOrQlNqwc/mgBDngJJpAlcsR5MqghQrA1BjApFpABBnABEngJcsR9WrhU+mABttjZotjAAAAAhTQJMrwRDnQNMqQd2ukRlsis/mgAZOQJFnwRLqwYIEgFHoQdarh1dsSBKpApDnQIAAAA8mABCnQEAAAAAAABBmwBAmgBDngJAnABBnAAAAAAAAAA9mAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABkyBpYwQxdxhFYwwtaww1axQ1TugphyRVhxRhZwg1awA9hxxdZwwxixhh80D5dwRSJ01GY2WZ4zjij3nWn3n2S2F6q4YGt4oWp4X2h3XOuoJgPAAAAPXRSTlPHMv3kfvGmECha9rCtmBEDP6uyAuLpGECDUODz4CF9P6VET9KOVygRASkHEBkBJDcPDggPDAkFBgQDAgEAECniJQAAAJVJREFUeNpdzlUOQjEURdHi7u7uzrMa7u7MfygkfQm0nM+V3NwN6N8E6PVFqJa6AmRS7woPUvj5KPMQuN5e6foPHKfzPVLsMMi57dTmPFyCEnvbzh9HVmpe7V0hvaOR3E0XBtMc+L5h8fHMuLZ4udLEZjvxC+mxpYcSogNWh3Itmi20BrKiQUwAJRipCmxqECFM2Im4DyVuNf8V/b/HAAAAAElFTkSuQmCC">
					</div>
				</div>
			</div>

			<h2 class="title_m">Additional Settings (for advanced users)</h2>

            
            <?php
            $class_text = '';
            $pre_display = "none";
            if(get_option('sp_load_highlighter') && get_option('sp_load_highlighter') == 'yes'){
                $class_text = 'class="text_hide"';
                $pre_display = "block";
            }
            ?>
            
			<div class="sliding">
				<br><h2>JS Global Variables </h2>
				<pre style="display: <?php echo $pre_display ?>" class="brush: js"><?php echo str_replace("\xc2\xa0",' ',$options['gl_var']); ?></pre>
				<textarea <?php echo $class_text ?> type="text" name="<?php echo $option; ?>[gl_var]"><?php echo str_replace("\xc2\xa0",' ',$options['gl_var']); ?></textarea>
				<br><h2>firstStart</h2>
				<pre style="display: <?php echo $pre_display ?>" class="brush: js"><?php echo str_replace("\xc2\xa0",' ',$options['firstStart']); ?></pre>
				<textarea <?php echo $class_text ?> type="text" name="<?php echo $option; ?>[firstStart]"><?php echo str_replace("\xc2\xa0",' ',$options['firstStart']); ?></textarea>
				<br><h2>onStart</h2>
				<pre style="display: <?php echo $pre_display ?>" class="brush: js"><?php echo str_replace("\xc2\xa0",' ',$options['onStart']); ?></pre>
				<textarea <?php echo $class_text ?> type="text" name="<?php echo $option; ?>[onStart]"><?php echo str_replace("\xc2\xa0",' ',$options['onStart']); ?></textarea>
				<br><h2>afterCreate</h2>
				<pre style="display: <?php echo $pre_display ?>" class="brush: js"><?php echo str_replace("\xc2\xa0",' ',$options['afterCreate']); ?></pre>
				<textarea <?php echo $class_text ?> type="text" name="<?php echo $option; ?>[afterCreate]"><?php echo str_replace("\xc2\xa0",' ',$options['afterCreate']); ?></textarea>
				<br><h2>onChange</h2>
				<pre style="display: <?php echo $pre_display ?>" class="brush: js"><?php echo str_replace("\xc2\xa0",' ',$options['onChange']); ?></pre>
				<textarea <?php echo $class_text ?> type="text" name="<?php echo $option; ?>[onChange]"><?php echo str_replace("\xc2\xa0",' ',$options['onChange']); ?></textarea>
				<br><h2>onCompleted</h2>
				<pre style="display: <?php echo $pre_display ?>" class="brush: js"><?php echo str_replace("\xc2\xa0",' ',$options['onCompleted']); ?></pre>
				<textarea <?php echo $class_text ?> type="text" name="<?php echo $option; ?>[onCompleted]"><?php echo str_replace("\xc2\xa0",' ',$options['onCompleted']); ?></textarea>
				<br><h2>CSS for Puzzle</h2>
				<pre style="display: <?php echo $pre_display ?>" class="brush: css;"><?php echo str_replace("\xc2\xa0",' ',$options['puzzle_style']); ?></pre>
				<textarea <?php echo $class_text ?> type="text" style="width:100%; height:200px; resize:vertical;" name="<?php echo $option; ?>[puzzle_style]"><?php echo str_replace("\xc2\xa0",' ',$options['puzzle_style']); ?></textarea>
			</div>

            <input type="hidden" name="action" value="update"/>
			<input type="hidden" name="page_options" value="<?php echo $option; ?>" />

			<p class="sp_save_button">
				<input type="submit" class="button button-primary" name="msp_updated" value="<?php _e('Save Settings','msp') ?>" />
			</p>
		</form>
	</div>
<?php
}
?>