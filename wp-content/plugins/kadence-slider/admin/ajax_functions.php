<?php
 
	/**
	 *  A method for inserting multiple rows into the specified table
	 *  Updated to include the ability to Update existing rows by primary key
	 *  
	 *  Usage Example for insert: 
	 *
	 *  $insert_arrays = array();
	 *  foreach($assets as $asset) {
	 *  $time = current_time( 'mysql' );
	 *  $insert_arrays[] = array(
	 *  'type' => "multiple_row_insert",
	 *  'status' => 1,
	 *  'name'=>$asset,
	 *  'added_date' => $time,
	 *  'last_update' => $time);
	 *
	 *  }
	 *
	 *
	 *  wp_insert_rows($insert_arrays, $wpdb->tablename);
	 *
	 *  Usage Example for update:
	 *
	 *  wp_insert_rows($insert_arrays, $wpdb->tablename, true, "primary_column");
	 *
	 *
	 * @param array $row_arrays
	 * @param string $wp_table_name
	 * @param boolean $update
	 * @param string $primary_key
	 * @return false|int
	 *
	 * @author	Ugur Mirza ZEYREK
	 * @contributor Travis Grenell
	 * @source http://stackoverflow.com/a/12374838/1194797
	 */
function ksp_insert_rows($row_arrays = array(), $wp_table_name, $update = false, $primary_key = null) {
	global $wpdb;
	$wp_table_name = esc_sql($wp_table_name);
	// Setup arrays for Actual Values, and Placeholders
	$values        = array();
	$place_holders = array();
	$query         = "";
	$query_columns = "";
	
	$query .= "INSERT INTO `{$wp_table_name}` (";
	foreach ($row_arrays as $count => $row_array) {
		foreach ($row_array as $key => $value) {
			if ($count == 0) {
				if ($query_columns) {
					$query_columns .= ", " . $key . "";
				} else {
					$query_columns .= "" . $key . "";
				}
			}
			
			$values[] = $value;
			
			$symbol = "%s";
			if (is_numeric($value)) {
				if (is_float($value)) {
					$symbol = "%f";
				} else {
					$symbol = "%d";
				}
			}
			if (isset($place_holders[$count])) {
				$place_holders[$count] .= ", '$symbol'";
			} else {
				$place_holders[$count] = "( '$symbol'";
			}
		}
		// mind closing the GAP
		$place_holders[$count] .= ")";
	}
	
	$query .= " $query_columns ) VALUES ";
	
	$query .= implode(', ', $place_holders);
	
	if ($update) {
		$update = " ON DUPLICATE KEY UPDATE $primary_key=VALUES( $primary_key ),";
		$cnt    = 0;
		foreach ($row_arrays[0] as $key => $value) {
			if ($cnt == 0) {
				$update .= "$key=VALUES($key)";
				$cnt = 1;
			} else {
				$update .= ", $key=VALUES($key)";
			}
		}
		$query .= $update;
	}
	
	$sql = $wpdb->prepare($query, $values);
	if ($wpdb->query($sql)) {
		return true;
	} else {
		return false;
	}
}

// Add slider
add_action('wp_ajax_ksp_addSlider', 'ksp_addSlider_callback');
function ksp_addSlider_callback() {
	global $wpdb;
	$options = wp_unslash($_POST['datas']);

	$output = ksp_addSlider_insert($options);
	
	// Returning
	$output = json_encode($wpdb->insert_id);

	if(is_array($output)) {
		print_r($output);
	} else{
		echo $output;
	}

	die();
}
function ksp_addSlider_insert($options) {
	global $wpdb;
	if(isset($options['name'])){
		$name = sanitize_text_field($options['name']);
	} else {
		$name = '';
	}
	if(isset($options['maxHeight'])){
		$maxHeight = absint($options['maxHeight']);
	} else {
		$maxHeight = '';
	}
	if(isset($options['maxWidth'])){
		$maxWidth = absint($options['maxWidth']);
	} else {
		$maxWidth = '';
	}
	if(isset($options['fullHeight'])){
		$fullHeight = absint($options['fullHeight']);
	} else {
		$fullHeight = '';
	}
	if(isset($options['fullWidth'])){
		$fullWidth = absint($options['fullWidth']);
	} else {
		$fullWidth = '';
	}
	if(isset($options['full_offset'])){
		$full_offset = $options['full_offset'];
	} else {
		$full_offset = '';
	}
	if(isset($options['responsive'])){
		$responsive = absint($options['responsive']);
	} else {
		$responsive = '';
	}
	if(isset($options['autoPlay'])){
		$autoPlay = absint($options['autoPlay']);
	} else {
		$autoPlay = '';
	}
	if(isset($options['pauseTime'])){
		$pauseTime = absint($options['pauseTime']);
	} else {
		$pauseTime = '';
	}
	if(isset($options['enableParallax'])){
		$enableParallax = absint($options['enableParallax']);
	} else {
		$enableParallax = '';
	}
	if(isset($options['singleSlide'])){
		$singleSlide = absint($options['singleSlide']);
	} else {
		$singleSlide = '';
	}
	if(isset($options['minHeight'])){
		$minHeight = absint($options['minHeight']);
	} else {
		$minHeight = '';
	}
	if(isset($options['pauseonHover'])){
		$pauseonHover = absint($options['pauseonHover']);
	} else {
		$pauseonHover = '';
	}
	return $wpdb->insert(
		$wpdb->prefix . 'ksp_sliders',
		array(
			'name' => $name,
			'maxHeight' => $maxHeight,
			'maxWidth' => $maxWidth,
			'fullHeight' => $fullHeight,
			'fullWidth' => $fullWidth,
			'full_offset' => $full_offset,
			'responsive' => $responsive,
			'autoPlay' => $autoPlay,
			'pauseTime' => $pauseTime,
			'enableParallax' => $enableParallax,
			'singleSlide' => $singleSlide,
			'minHeight' => $minHeight,
			'pauseonHover' => $pauseonHover,
		),
		array(
			'%s',
			'%d',
			'%d',
			'%d',
			'%d',
			'%s',
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
		)
	);
}

// Edit slider
add_action('wp_ajax_ksp_editSlider', 'ksp_editSlider_callback');
function ksp_editSlider_callback() {
	global $wpdb;
	$options = wp_unslash($_POST['datas']);
	if(isset($options['name'])){
		$name = sanitize_text_field($options['name']);
	} else {
		$name = '';
	}
	if(isset($options['maxHeight'])){
		$maxHeight = absint($options['maxHeight']);
	} else {
		$maxHeight = '';
	}
	if(isset($options['maxWidth'])){
		$maxWidth = absint($options['maxWidth']);
	} else {
		$maxWidth = '';
	}
	if(isset($options['fullHeight'])){
		$fullHeight = absint($options['fullHeight']);
	} else {
		$fullHeight = '';
	}
	if(isset($options['fullWidth'])){
		$fullWidth = absint($options['fullWidth']);
	} else {
		$fullWidth = '';
	}
	if(isset($options['full_offset'])){
		$full_offset = sanitize_text_field($options['full_offset']);
	} else {
		$full_offset = '';
	}
	if(isset($options['responsive'])){
		$responsive = absint($options['responsive']);
	} else {
		$responsive = '';
	}
	if(isset($options['autoPlay'])){
		$autoPlay = absint($options['autoPlay']);
	} else {
		$autoPlay = '';
	}
	if(isset($options['pauseTime'])){
		$pauseTime = absint($options['pauseTime']);
	} else {
		$pauseTime = '';
	}
	if(isset($options['enableParallax'])){
		$enableParallax = absint($options['enableParallax']);
	} else {
		$enableParallax = '';
	}
	if(isset($options['singleSlide'])){
		$singleSlide = absint($options['singleSlide']);
	} else {
		$singleSlide = '';
	}
	if(isset($options['minHeight'])){
		$minHeight = absint($options['minHeight']);
	} else {
		$minHeight = '';
	}
	if(isset($options['pauseonHover'])){
		$pauseonHover = absint($options['pauseonHover']);
	} else {
		$pauseonHover = '';
	}
	$table_name = $wpdb->prefix . 'ksp_sliders';
		
	$output = $wpdb->update(
		$table_name,
		array(
			'name' => $name,
			'maxHeight' => $maxHeight,
			'maxWidth' => $maxWidth,
			'fullHeight' => $fullHeight,
			'fullWidth' => $fullWidth,
			'full_offset' => $full_offset,
			'responsive' => $responsive,
			'autoPlay' => $autoPlay,
			'pauseTime' => $pauseTime,
			'enableParallax' => $enableParallax,
			'singleSlide' => $singleSlide,
			'minHeight' => $minHeight,
			'pauseonHover' => $pauseonHover,
		),
		array('id' => $options['id']), 
		array(
			'%s',
			'%d',
			'%d',
			'%d',
			'%d',
			'%s',
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
		),
		array('%d')
	);
	
	// Returning
	$output = json_encode($output);
	if(is_array($output)) print_r($output);
	else echo $output;
	
	die();
}

// Edit slides. Receives an array with all the slides options. Delete al the old slides then recreate them
add_action('wp_ajax_ksp_editSlides', 'ksp_editSlides_callback');
function ksp_editSlides_callback() {
	global $wpdb;
	$options = $_POST['datas'];
	$table_name = $wpdb->prefix . 'ksp_slides';
	if(!ksp_sliderexists((esc_sql($options['slider_parent'])))) {
		echo json_encode(false);
		return;
	}
	// if(!ksp_slider_new_column_exists((esc_sql($options['slider_parent'])))) {
	// 	echo json_encode(false);
	// 	return;
	// }
	if(count($options['options']) == 0) {
		echo json_encode(false);
		return;
	}
	$output = true;
	// Remove all the old slides else you end with copies.
	$output = $wpdb->delete($table_name, array('slider_parent' => $options['slider_parent']), array('%d'));
	if($output === false) {
		echo json_encode(false);
	} else {
		// It's impossible to have 0 slides (jQuery checks it)
		$output = ksp_insert_rows($options['options'], $table_name);
		// Returning
		$output = json_encode($output);

		if(is_array($output)) {
			print_r($output);
		} else { 
			echo $output;
		}
	}
	
	die();
}
function ksp_slider_new_column_exists($id) {
	global $wpdb;
	$slider = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'ksp_slides WHERE slider_parent = %d', esc_sql($id)));
	if( ! property_exists( $slider ) ) {
		return true;
	} else if( property_exists( $slider->background_link_new_tab ) ) {
		return true;
	}
	return false;
}
function ksp_sliderexists($id) {
	global $wpdb;
	$slider = $wpdb->get_row($wpdb->prepare('SELECT id FROM ' . $wpdb->prefix . 'ksp_sliders WHERE id = %d', esc_sql($id)));
	if($slider != NULL) {
		return true;
	}
	return false;
}
add_action('wp_ajax_ksp_editLayers', 'ksp_editLayers_callback');
function ksp_editLayers_callback() {
	global $wpdb;
	$options = $_POST['datas'];
	$table_name = $wpdb->prefix . 'ksp_layers';
	
	$output = true;	
	
	$output = $wpdb->delete($table_name, array('slider_parent' => $options['slider_parent']), array('%d'));

	if($output === false) {
		echo json_encode(false);
	} else {
		$option_temp = json_decode(stripslashes($options['options']));
		if(empty($option_temp)) {
			echo json_encode(true);
		} else {	
			// Insert row per row
			$options_array = json_decode(stripslashes($options['options']));
			$output = ksp_insert_rows($options_array, $table_name);

			// Returning
			$output = json_encode($output);

			if(is_array($output)) {
				print_r($output);
			} else {
				echo $output;
			}
		}
	}
	
	die();
}

// Delete slider and its content
add_action('wp_ajax_ksp_deleteSlider', 'ksp_deleteSlider_callback');
function ksp_deleteSlider_callback() {
	global $wpdb;
	$options = $_POST['datas'];
	
	$real_output = true;
	
	// Delete slider
	$table_name = $wpdb->prefix . 'ksp_sliders';		
	$output = $wpdb->delete($table_name, array('id' => $options['id']), array('%d'));
	if($output === false) {
		$real_output = false;
	}
	
	// Delete slides
	$table_name = $wpdb->prefix . 'ksp_slides';		
	$output = $wpdb->delete($table_name, array('slider_parent' => $options['id']), array('%d'));
	if($output === false) {
		$real_output = false;
	}
	
	// Delete Layers
	$table_name = $wpdb->prefix . 'ksp_layers';		
	$output = $wpdb->delete($table_name, array('slider_parent' => $options['id']), array('%d'));
	if($output === false) {
		$real_output = false;
	}
	
	// Returning
	$real_output = json_encode($real_output);
	if(is_array($real_output)) print_r($real_output);
	else echo $real_output;
	
	die();
}

// Duplicate slider and its content
add_action('wp_ajax_ksp_duplicateSlider', 'ksp_duplicateSlider_callback');
function ksp_duplicateSlider_callback() {
	global $wpdb;
	$options = $_POST['datas'];
	
	$output = true;
	$real_output = true;
	
	$slider_id = $options['id'];
	
	$cloned_slider_name = '';
	
	$sliders = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'ksp_sliders WHERE id = \'' . $slider_id . '\'', ARRAY_A);
	foreach($sliders as $slider) {
		$cloned_slider_name = $slider['name'] = $slider['name'] . '_' . __('Copy', 'ksp');
		$output = ksp_addSlider_insert($slider);
	}
	
	if($output === false) {
		$real_output = false;
	} else {
		$cloned_slider_id = $wpdb->insert_id;
		
		// Clone slides
		$slides = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'ksp_slides WHERE slider_parent = ' . $slider_id . ' ORDER BY position', ARRAY_A);
		if(empty($slides)) {
			$output = true;
		}
		else {
			foreach($slides as $key => $slide) {
				unset($slides[$key]['id']);
				$slides[$key]['slider_parent'] = $cloned_slider_id;
			}
			$temp = ksp_insert_rows($slides, $wpdb->prefix . 'ksp_slides');
			if($temp === false) {
				$output = false;
			}
		}
		
		// Clone Layers
		$layers = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'ksp_layers WHERE slider_parent = ' . $slider_id, ARRAY_A);
		if(empty($layers)) {
			$output = true;
		}
		else {
			foreach($layers as $key => $element) {
				unset($layers[$key]['id']);
				$layers[$key]['slider_parent'] = $cloned_slider_id;
			}
			$temp = ksp_insert_rows($layers, $wpdb->prefix . 'ksp_layers');
			if($temp === false) {
				$output = false;
			}
			
			if($output === false) {
				$real_output = false;
			}
		}
	}
	
	if($real_output === true) {
		$real_output = array(
			'response' => true,
			'cloned_slider_id' => $cloned_slider_id,
			'cloned_slider_name' => $cloned_slider_name,
		);
	} else {
		$real_output = array(
			'response' => false,
			'cloned_slider_id' => false,
			'cloned_slider_name' => false,
		);
	}
	
	// Returning
	$real_output = json_encode($real_output);
	if(is_array($real_output)) print_r($real_output);
	else echo $real_output;
	
	die();
}

// Exports the slider in xml
add_action('wp_ajax_ksp_exportSlider', 'ksp_exportSlider_callback');
function ksp_exportSlider_callback() {
	global $wpdb;
	
	// Clear the temp folder
	array_map('unlink', glob(KADENCE_SLIDER_PATH . '/temp/*'));
	
	$options = $_POST['datas'];
	
	$real_output = true;
	
	$result = array();
	
	// Get the slider
	$sliders = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'ksp_sliders WHERE id = \'' . $options['id'] . '\'', ARRAY_A);
	if(empty($sliders)) {
		$real_output = false;
	} else {
		foreach($sliders as $key => $temp) {
			unset($sliders[$key]['id']);
		}
		$result['sliders'] = $sliders;
	}
	$slider_slug = strtolower(preg_replace('/[^a-zA-Z0-9-_\.]/','', $sliders[0]['name']));
	$zip = new ZipArchive();
	$filename = 'ksp-' . $slider_slug . '.zip';
	if($zip->open(KADENCE_SLIDER_PATH . '/temp/' . $filename, ZipArchive::CREATE) !== TRUE) {
		echo false;
		die();
	}
	
	// Get the slides
	$slides = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'ksp_slides WHERE slider_parent = ' . $options['id'] . ' ORDER BY position', ARRAY_A);
	if(! empty($slides)) {
		foreach($slides as $key => $temp) {
			unset($slides[$key]['id']);
			unset($slides[$key]['slider_parent']);
			
			// Add images to zip and remove media directory URLs
			if($slides[$key]['background_type_image'] != 'none' && $slides[$key]['background_type_image'] != 'undefined') {
				$img = $slides[$key]['background_type_image'];
				$zip->addFromString(basename($img), file_get_contents($img));
				$slides[$key]['background_type_image'] = basename($img);
			}
			if(!empty($slides[$key]['background_type_video_mp4']) && $slides[$key]['background_type_video_mp4'] != 'none' && $slides[$key]['background_type_video_mp4'] != 'undefined') {
					$mp4 = $slides[$key]['background_type_video_mp4'];
					$zip->addFromString(basename($mp4), file_get_contents($mp4));
					$slides[$key]['background_type_video_mp4'] = basename($mp4);
			}
			if(!empty($slides[$key]['background_type_video_webm']) && $slides[$key]['background_type_video_webm'] != 'none' && $slides[$key]['background_type_video_webm'] != 'undefined') {
					$webm = $slides[$key]['background_type_video_webm'];
					$zip->addFromString(basename($webm), file_get_contents($webm));
					$slides[$key]['background_type_video_webm'] = basename($webm);
			}
		}
		$result['slides'] = $slides;
	}
	
	// Get the layers
	$layers = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'ksp_layers WHERE slider_parent = ' . $options['id'], ARRAY_A);
	if(! empty($layers)) {
		foreach($layers as $key => $temp) {
			unset($layers[$key]['id']);
			unset($layers[$key]['slider_parent']);
			
			// Add images to zip and remove media directory URLs
			if($layers[$key]['type'] == 'image') {
				$img = $layers[$key]['image_src'];
				$zip->addFromString(basename($img), file_get_contents($img));
				$layers[$key]['image_src'] = basename($img);
			}
		}
		$result['layers'] = $layers;
	}
	
	$json = json_encode($result);	
	$zip->addFromString("slider.json", $json);
	
	$zip->close();
	
	if($real_output === true) {
		$real_output = array(
			'response' => true,
			'url' => KADENCE_SLIDER_URL . 'temp/' . $filename,
		);
	} else {
		$real_output = array(
			'response' => false,
			'url' => false,
		);
	}
	
	// Returning
	$real_output = json_encode($real_output);
	if(is_array($real_output)) print_r($real_output);
	else echo $real_output;
	
	die();
}

// Inport the slider from a json string
add_action('wp_ajax_ksp_importSlider', 'ksp_importSlider_callback');
function ksp_importSlider_callback() {
	global $wpdb;
	
	// Clear the temp folder
	array_map('unlink', glob(KADENCE_SLIDER_PATH . 'temp/*'));
	
	foreach($_FILES as $file) {		
		$output = true;
		$real_output = true;
		
		$zip = new ZipArchive();
		if($zip->open($file['tmp_name']) !== TRUE) {
			echo false;
			die();
		}
		
		$zip->extractTo(KADENCE_SLIDER_PATH . 'temp/');
		
		$imported_array = json_decode(file_get_contents(KADENCE_SLIDER_PATH . 'temp/slider.json'));
		
		$sliders = $imported_array->sliders;
		foreach($sliders as $slider) {
			$output = ksp_addSlider_insert((array) $slider);
		}
		
		if($output === false) {
			$real_output = false;
		} else {
			$imported_slider_id = $wpdb->insert_id;
			
			// Import slides
			$slides = $imported_array->slides;
			if(empty($slides)) {
				$output = true;
			} else {
				foreach($slides as $key => $slide) {
					$slides[$key]->slider_parent = $imported_slider_id;
					
					// Set background images
					if($slides[$key]->background_type_image != 'undefined' && $slides[$key]->background_type_image != 'none') {
						$upload = media_sideload_image(KADENCE_SLIDER_URL . 'temp/' . $slides[$key]->background_type_image, 0, null, 'src');
						$slides[$key]->background_type_image = $upload;
					}
					// Set html5 videos
					if(!empty($slides[$key]->background_type_video_mp4) && $slides[$key]->background_type_video_mp4 != 'undefined' && $slides[$key]->background_type_video_mp4 != 'none') {
						$ext = substr($slides[$key]->background_type_video_mp4, strrpos($slides[$key]->background_type_video_mp4, "."));
						if($ext == '.mp4') {
							$file = array(
								'name'     => $slides[$key]->background_type_video_mp4,
								'type'     => 'video/mp4',
								'tmp_name' => KADENCE_SLIDER_PATH . 'temp/' . $slides[$key]->background_type_video_mp4,
								'error'    => 0,
								'size'     => filesize(KADENCE_SLIDER_PATH . 'temp/' . $slides[$key]->background_type_video_mp4),
							);
							$overrides = array(
								'test_form' => false,
								'test_size' => true,
							);

							$upload = wp_handle_sideload( $file, $overrides );
							if ( empty( $upload['error'] ) ) {
								$slides[$key]->background_type_video_mp4 = $upload['url'];
							}
						}
					}
					if(!empty($slides[$key]->background_type_video_webm) && $slides[$key]->background_type_video_webm != 'undefined' && $slides[$key]->background_type_video_webm != 'none') {
						$ext = substr($slides[$key]->background_type_video_webm, strrpos($slides[$key]->background_type_video_webm, "."));
						if($ext == '.webm') {
							$file = array(
								'name'     => $slides[$key]->background_type_video_webm,
								'type'     => 'video/webm',
								'tmp_name' => KADENCE_SLIDER_PATH . 'temp/' . $slides[$key]->background_type_video_webm,
								'error'    => 0,
								'size'     => filesize(KADENCE_SLIDER_PATH . 'temp/' . $slides[$key]->background_type_video_webm),
							);
							$overrides = array(
								'test_form' => false,
								'test_size' => true,
							);

							$upload = wp_handle_sideload( $file, $overrides );
							if ( empty( $upload['error'] ) ) {
								$slides[$key]->background_type_video_webm = $upload['url'];
							}
						}
					}
				}
				$temp = ksp_insert_rows($slides, $wpdb->prefix . 'ksp_slides');
				if($temp === false) {
					$output = false;
				}
			}
			
			// Import layers
			$layers = (array) $imported_array->layers;
			if(empty($layers)) {
				$output = true;
			} else {
				foreach($layers as $key => $element) {
					$layers[$key]->slider_parent = $imported_slider_id;
					
					// Set images
					if($layers[$key]->type == 'image') {
						$upload = media_sideload_image(KADENCE_SLIDER_URL . 'temp/' . $layers[$key]->image_src, 0, null, 'src');
						$layers[$key]->image_src = $upload;
					}
				}
				$temp = ksp_insert_rows($layers, $wpdb->prefix . 'ksp_layers');
				if($temp === false) {
					$output = false;
				}
				
				if($output === false) {
					$real_output = false;
				}
			}
		}
		
		if($real_output === true) {
			$real_output = array(
				'response' => true,
				'imported_slider_id' => $imported_slider_id,
				'imported_slider_name' => $imported_array->sliders[0]->name,
			);
		} else {
			$real_output = array(
				'response' => false,
				'imported_slider_id' => false,
				'imported_slider_name' => false,
			);
		}
		
		// Returning
		$real_output = json_encode($real_output);
		if(is_array($real_output)) print_r($real_output);
		else echo $real_output;
		
		die();
	}
}

function ksp_import_direct($file) {
	global $wpdb;
	
	if(empty( $file ) ) {
		die();
	}
	// Clear the temp folder
	array_map('unlink', glob(KADENCE_SLIDER_PATH . 'temp/*'));
	

		$output = true;
		$real_output = true;
		
		$zip = new ZipArchive();
		if($zip->open($file) !== TRUE) {
			return 'false';
			die();
		}
		
		$zip->extractTo(KADENCE_SLIDER_PATH . 'temp/');
		
		$imported_array = json_decode(file_get_contents(KADENCE_SLIDER_PATH . 'temp/slider.json'));
		
		$sliders = $imported_array->sliders;
		foreach($sliders as $slider) {
			$output = ksp_addSlider_insert((array) $slider);
		}
		
		if($output === false) {
			$real_output = false;
		} else {
			$imported_slider_id = $wpdb->insert_id;
			
			// Import slides
			$slides = $imported_array->slides;
			if(empty($slides)) {
				$output = true;
			} else {
				foreach($slides as $key => $slide) {
					$slides[$key]->slider_parent = $imported_slider_id;
					
					// Set background images
					if($slides[$key]->background_type_image != 'undefined' && $slides[$key]->background_type_image != 'none') {
						$upload = media_sideload_image(KADENCE_SLIDER_URL . 'temp/' . $slides[$key]->background_type_image, 0, null, 'src');
						$slides[$key]->background_type_image = $upload;
					}
					// Set html5 videos
					if(!empty($slides[$key]->background_type_video_mp4) && $slides[$key]->background_type_video_mp4 != 'undefined' && $slides[$key]->background_type_video_mp4 != 'none') {
						$ext = substr($slides[$key]->background_type_video_mp4, strrpos($slides[$key]->background_type_video_mp4, "."));
						error_log($ext);
						if($ext == '.mp4') {
							$file = array(
								'name'     => $slides[$key]->background_type_video_mp4,
								'type'     => 'video/mp4',
								'tmp_name' => KADENCE_SLIDER_PATH . 'temp/' . $slides[$key]->background_type_video_mp4,
								'error'    => 0,
								'size'     => filesize(KADENCE_SLIDER_PATH . 'temp/' . $slides[$key]->background_type_video_mp4),
							);
							$overrides = array(
								'test_form' => false,
								'test_size' => true,
							);

							$upload = wp_handle_sideload( $file, $overrides );
							if ( empty( $upload['error'] ) ) {
								$slides[$key]->background_type_video_mp4 = $upload['url'];
							}
						}
					}
					if(!empty($slides[$key]->background_type_video_webm) && $slides[$key]->background_type_video_webm != 'undefined' && $slides[$key]->background_type_video_webm != 'none') {
						$ext = substr($slides[$key]->background_type_video_webm, strrpos($slides[$key]->background_type_video_webm, "."));
						if($ext == '.webm') {
							$file = array(
								'name'     => $slides[$key]->background_type_video_webm,
								'type'     => 'video/webm',
								'tmp_name' => KADENCE_SLIDER_PATH . 'temp/' . $slides[$key]->background_type_video_webm,
								'error'    => 0,
								'size'     => filesize(KADENCE_SLIDER_PATH . 'temp/' . $slides[$key]->background_type_video_webm),
							);
							$overrides = array(
								'test_form' => false,
								'test_size' => true,
							);

							$upload = wp_handle_sideload( $file, $overrides );
							if ( empty( $upload['error'] ) ) {
								$slides[$key]->background_type_video_webm = $upload['url'];
							}
						}
					}
				}
				$temp = ksp_insert_rows($slides, $wpdb->prefix . 'ksp_slides');
				if($temp === false) {
					$output = false;
				}
			}
			
			// Import layers
			$layers = (array) $imported_array->layers;
			if(empty($layers)) {
				$output = true;
			} else {
				foreach($layers as $key => $element) {
					$layers[$key]->slider_parent = $imported_slider_id;
					
					// Set images
					if($layers[$key]->type == 'image') {
						$upload = media_sideload_image(KADENCE_SLIDER_URL . 'temp/' . $layers[$key]->image_src, 0, null, 'src');
						$layers[$key]->image_src = $upload;
					}
				}
				$temp = ksp_insert_rows($layers, $wpdb->prefix . 'ksp_layers');
				if($temp === false) {
					$output = false;
				}
				
				if($output === false) {
					$real_output = false;
				}
			}
		}
		
		if($real_output === true) {
			$real_output = array(
				'response' => true,
				'imported_slider_id' => $imported_slider_id,
				'imported_slider_name' => $imported_array->sliders[0]->name,
			);
		} else {
			$real_output = array(
				'response' => false,
				'imported_slider_id' => false,
				'imported_slider_name' => false,
			);
		}
		
		// Returning
		$real_output = json_encode($real_output);
		return $real_output;
		
		die();
}
