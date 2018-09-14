<?php
function shufflePuzzle($option = 'sp_main'){
	global $wpdb;
	$table_name = $wpdb->prefix . "shufflepuzzle";
    $msp_data = $wpdb->get_results("SELECT * FROM $table_name WHERE option_name = '".$option."'",ARRAY_A);

   
	//echo $options['showStart'];
	
	if($msp_data[0]['active']){
		$options = get_option($option);

		//echo '<pre>'.print_r($options).'</pre>';
		//echo 'true';
		

		if(!isset($options['sh_stop'])){$options['sh_stop'] = 'false';}
		if(!isset($options['showStart'])){$options['showStart'] = 'false';}
		if(!isset($options['auto_size'])){$options['auto_size'] = 'false';}
		if(!isset($options['gap'])){$options['gap'] = 'false';}
		if(!isset($options['menuVisible'])){$options['menuVisible'] = 'false';}
		?>

		<!-- ShufflePuzzle Starts Here -->

        <?php echo '<style type="text/css">'.str_replace("\xc2\xa0",' ',$options['puzzle_style']).'</style>';?>
        <script type="text/javascript">
			jQuery(document).ready(function(){
				<?php if( $options['gl_var'] ){
					echo str_replace("\xc2\xa0",' ',$options['gl_var']);
				}?>var config = {
					width: <?php echo $options['w_img']; ?>,
					height: <?php echo $options['h_img']; ?>,
					tilesH: <?php echo $options['tiles_h']; ?>,
					tilesV: <?php echo $options['tiles_v']; ?>,
					gap: <?php echo $options['gap']; ?>,
					auto_size: <?php echo $options['auto_size']; ?>,
					showStart: <?php echo $options['showStart']; ?>,
					duration: <?php echo $options['duration']; ?>,
					bgColor: "<?php echo $options['bgColor']; ?>",
					bgOpacity: <?php echo $options['bgOpacity']; ?>,
					imgBgOpacity: <?php echo $options['imgBgOpacity']; ?>,
					shuffleNum: <?php echo $options['shuffleNum']; ?>,
					menuVisible: <?php echo $options['menuVisible']; ?>,
					menuNameShuffle: "<?php echo $options['menuNameShuffle']; ?>",
					menuNameGrid: "<?php echo $options['menuNameGrid']; ?>",
					menuNameImage: "<?php echo $options['menuNameImage']; ?>",
					menu_shuffle:{<?php echo "'".str_replace(",", ",'", str_replace(":", "':", $options['menu_shuffle']));?>},
					menu_grid: [<?php
					$arr = preg_split ('/,/', $options['menu_grid']);
					for($i = 0; $i < count($arr); ++$i) {
						$arr2[$i] = "'".$arr[$i]."'";
						if($i < count($arr) - 1){
							echo $arr2[$i].',';
						}else{
							echo $arr2[$i];
						}
					};
					?>],
					menu_image:{<?php 
						$sp_images = array();
						foreach($options as $key=> $value){
							if(substr($key,0,6) == 'image_' && $key!='image_999'){
								array_push($sp_images, $value);								
							}
						}
						$i = 0;
						foreach ($sp_images as $key => $image) {
							$i++;
							echo "'".$options['name_'.($key+1)]."':'".$options['image_'.($key+1)];
							if($i < count($sp_images)){
								echo "',";
							}else{
								echo "'";
							}
						}
					?>},
					onCompleted : <?php echo $options['onCompleted'] ? 'function(){'.str_replace("\xc2\xa0",' ',$options['onCompleted']).'}' : 'null';?>,
					onStart : <?php echo $options['onStart'] ? 'function(){'.str_replace("\xc2\xa0",' ',$options['onStart']).'}' : 'null';?>,
					onChange : <?php echo $options['onChange'] ? 'function(){'.str_replace("\xc2\xa0",' ',$options['onChange']).'}' : 'null';?>,
					afterCreate : <?php echo $options['afterCreate'] ? 'function(){'.str_replace("\xc2\xa0",' ',$options['afterCreate']).'}' : 'null';?>,
					firstStart : <?php echo $options['firstStart'] ? 'function(){'.str_replace("\xc2\xa0",' ',$options['firstStart']).'}' : 'null';?>,
					stop : <?php echo $options['sh_stop']; ?>
				};
				jQuery('#shufflepuzzle.<?php echo $option; ?>').shufflepuzzle(config);
			});
        </script>
        <div id="shufflepuzzle" class="<?php echo $option; ?>"></div>
		<!-- ShufflePuzzle Ends Here -->
		<?php
	}else{
		_e('This <b>'.$option.'</b> Plugin is deactivated, please activate it from Shuffle Puzzle admin panel.','msp');
	}
}

function msp_short_code($atts) {
	ob_start();
    extract(shortcode_atts(array("name" => ''), $atts));
	shufflePuzzle($name);
	$output = ob_get_clean();
	return $output;
}
add_shortcode('sp', 'msp_short_code');
?>