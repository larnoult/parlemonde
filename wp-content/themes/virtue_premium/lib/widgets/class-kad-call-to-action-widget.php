<?php 
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class kad_calltoaction_widget extends WP_Widget{

private static $instance = 0;
    public function __construct() {
        $widget_ops = array('classname' => 'kadence_calltoaction_widget', 'description' => __('Adds a simple call to action', 'virtue'));
        parent::__construct('kadence_calltoaction_widget', __('Virtue: Call to Action', 'virtue'), $widget_ops);
    }

       public function widget($args, $instance){ 
        extract( $args );
        //title
        if(!empty($instance["title"])) { $title = $instance['title'];} else {$title = '';}
        //description & btn_link
        if(!empty($instance["abovetitle"])) { $abovetitle = $instance['abovetitle'];} else {$abovetitle = '';}
        if(!empty($instance["subtitle"])) { $subtitle = $instance['subtitle'];} else {$subtitle = '';}
        if(!empty($instance["btn_link"])) {$btn_link = $instance["btn_link"];} else {$btn_link = '';}
        if(!empty($instance["btn_text"])) {$btn_text = $instance["btn_text"];} else {$btn_text = '';}
        if(!empty($instance["btn_target"])) {$btn_target = $instance["btn_target"];} else {$btn_target = 'false';}
        if(!empty($instance["btn_size"])) {$btn_size = $instance["btn_size"];} else {$btn_size = 'large';}
        if(!empty($instance["tsize"])) {$tsize = $instance["tsize"];} else {$tsize = '40';}
        if(!empty($instance["tweight"])) {$tweight = $instance["tweight"];} else {$tweight = 'default';}
        if(!empty($instance["sweight"])) {$sweight = $instance["sweight"];} else {$sweight = 'default';}
        if(!empty($instance["atweight"])) {$atweight = $instance["atweight"];} else {$atweight = 'default';}
        if(!empty($instance["atsize"])) {$atsize = $instance["atsize"];} else {$atsize = '16';}
        if(!empty($instance["ssize"])) {$ssize = $instance["ssize"];} else {$ssize = '20';}
        if(!empty($instance["tsmallsize"])) {$tsmallsize = $instance["tsmallsize"];} else {$tsmallsize = $tsize;}
        if(!empty($instance["atsmallsize"])) {$atsmallsize = $instance["atsmallsize"];} else {$atsmallsize = $atsize;}
        if(!empty($instance["ssmallsize"])) {$ssmallsize = $instance["ssmallsize"];} else {$ssmallsize = $ssize;}
        if(!empty($instance["align"])) { $align = $instance["align"];} else {$align = 'center';}
        if(!empty($instance["atcolor"])) { $atcolor = 'color:'.$instance["atcolor"].';'; } else {$atcolor = '';}
        if(!empty($instance["tcolor"])) { $tcolor = 'color:'.$instance["tcolor"].';'; } else {$tcolor = '';}
        if(!empty($instance["scolor"])) { $scolor = 'color:'.$instance["scolor"].';'; } else {$scolor = '';}
        if(!empty($instance["btn_color"])) { $btn_color = 'tcolor="'.$instance["btn_color"].'"'; } else {$btn_color = '';}
        if(!empty($instance["btn_background"])) { $btn_background = 'bcolor="'.$instance["btn_background"].'"'; } else {$btn_background = '';}
        if(!empty($instance["btn_border_color"])) { $btn_border_color = 'bordercolor="'.$instance["btn_border_color"].'"'; } else {$btn_border_color = '';}
        if(!empty($instance["btn_hover_color"])) { $btn_hover_color = 'thovercolor="'.$instance["btn_hover_color"].'"'; } else {$btn_hover_color = '';}
        if(!empty($instance["btn_hover_background"])) { $btn_hover_background = 'bhovercolor="'.$instance["btn_hover_background"].'"'; } else {$btn_hover_background = '';}
        if(!empty($instance["btn_hover_border_color"])) { $btn_hover_border_color = 'borderhovercolor="'.$instance["btn_hover_border_color"].'"'; } else {$btn_hover_border_color = '';}
        if(!empty($instance["btn_border"])) { $btn_border = 'border="'.$instance["btn_border"].'"'; } else {$btn_border = '';}
        if(!empty($instance["btn_border_radius"])) { $btn_border_radius = 'borderradius="'.$instance["btn_border_radius"].'"'; } else {$btn_border_radius = '';}
        if(!empty($instance["title_html_tag"])) { $title_html_tag = $instance["title_html_tag"]; } else {$title_html_tag = 'h1';}
        if(!empty($tweight) && $tweight != 'default') {
        	$tweight_tag = 'font-weight:'.$tweight.';';
        } else {
        	$tweight_tag = '';
        }
        if(!empty($sweight) && $sweight != 'default') {
        	$sweight_tag = 'font-weight:'.$sweight.';';
        } else {
        	$sweight_tag = '';
        }
        if(!empty($atweight) && $atweight != 'default') {
        	$atweight_tag = 'font-weight:'.$atweight.';';
        } else {
        	$atweight_tag = '';
        }


            ?>


        <?php 
        echo $before_widget;
          	echo '<div class="kt-ctaw clearfix">';
          		if(!empty($abovetitle)){
            		echo '<h5 class="kt-call-to-action-abovetitle" style="'.esc_attr($atcolor).' font-size:'.esc_attr($atsize).'px; line-height:1.1; text-align:'.esc_attr($align).'; '.$atweight_tag.'"  data-max-size="'.esc_attr($atsize).'" data-min-size="'.esc_attr($atsmallsize).'">'.wp_kses_post($abovetitle).'</h5>';
            	}
          		if(!empty($title)){
            		echo '<'.esc_attr($title_html_tag).' class="kt-call-to-action-title" style="'.esc_attr($tcolor).' font-size:'.esc_attr($tsize).'px; line-height:1.1; text-align:'.esc_attr($align).'; '.$tweight_tag.'"  data-max-size="'.esc_attr($tsize).'" data-min-size="'.esc_attr($tsmallsize).'">'.wp_kses_post($title).'</'.esc_attr($title_html_tag).'>';
            	}
            	if(!empty($subtitle)) { echo '<h5 class="kt-call-to-action-subtitle" style="'.esc_attr($scolor).' font-size:'.esc_attr($ssize).'px; line-height:1.1; text-align:'.esc_attr($align).'; '.$sweight_tag.'"  data-max-size="'.esc_attr($ssize).'" data-min-size="'.esc_attr($ssmallsize).'">'.wp_kses_post($subtitle).'</h5>'; }
            	if(!empty($btn_link)) {
	            	echo '<div style="text-align:'.esc_attr($align).'">';
	            		echo do_shortcode('[btn text="'.$btn_text.'" '.$btn_color.' '.$btn_background.' '.$btn_border_color.' '.$btn_hover_color.' '.$btn_hover_background.' '.$btn_hover_border_color.' '.$btn_border.' '.$btn_border_radius.' link="'.esc_attr($btn_link).'" size="'.esc_attr($btn_size).'" target="'.esc_attr($btn_target).'"]');
	            	echo '</div>';
	            }
            echo '</div>';
        echo $after_widget;?>

    <?php }

    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['align'] = sanitize_text_field($new_instance['align']);
        $instance['btn_target'] = sanitize_text_field($new_instance['btn_target']);
        $instance['btn_link'] = esc_url_raw( $new_instance['btn_link'] );
        $instance['btn_text'] = sanitize_text_field( $new_instance['btn_text'] );
        $instance['btn_color'] = sanitize_text_field( $new_instance['btn_color'] );
        $instance['btn_size'] = sanitize_text_field( $new_instance['btn_size'] );
        $instance['btn_background'] = sanitize_text_field( $new_instance['btn_background'] );
        $instance['btn_border_color'] = sanitize_text_field( $new_instance['btn_border_color'] );
        $instance['btn_border'] = sanitize_text_field( $new_instance['btn_border'] );
        $instance['btn_border_radius'] = sanitize_text_field( $new_instance['btn_border_radius'] );
        $instance['btn_hover_border_color'] = sanitize_text_field( $new_instance['btn_hover_border_color'] );
        $instance['btn_hover_color'] = sanitize_text_field( $new_instance['btn_hover_color'] );
        $instance['btn_hover_background'] = sanitize_text_field( $new_instance['btn_hover_background'] );
        $instance['atcolor'] = sanitize_text_field( $new_instance['atcolor'] );
        $instance['tcolor'] = sanitize_text_field( $new_instance['tcolor'] );
        $instance['scolor'] = sanitize_text_field( $new_instance['scolor'] );
        $instance['tweight'] = sanitize_text_field( $new_instance['tweight'] );
        $instance['sweight'] = sanitize_text_field( $new_instance['sweight'] );
        $instance['atweight'] = sanitize_text_field( $new_instance['atweight'] );
        $instance['atsize'] = (int) $new_instance['atsize'];
        $instance['tsize'] = (int) $new_instance['tsize'];
        $instance['ssize'] = (int) $new_instance['ssize']; 
        $instance['atsmallsize'] = (int) $new_instance['atsmallsize'];
        $instance['tsmallsize'] = (int) $new_instance['tsmallsize'];
        $instance['ssmallsize'] = (int) $new_instance['ssmallsize']; 
        $instance['abovetitle'] = wp_kses_post( $new_instance['abovetitle'] );
        $instance['title'] = wp_kses_post( $new_instance['title'] );
        $instance['subtitle'] = wp_kses_post( $new_instance['subtitle'] );
        $instance['title_html_tag'] = sanitize_text_field( $new_instance['title_html_tag'] );
        return $instance;
    }

  public function form($instance){ 
    $title = isset($instance['title']) ? esc_textarea($instance['title']) : '';
    $abovetitle = isset($instance['abovetitle']) ? esc_textarea($instance['abovetitle']) : '';
    $subtitle = isset($instance['subtitle']) ? esc_textarea($instance['subtitle']) : '';
    $atcolor = isset($instance['atcolor']) ? esc_attr($instance['atcolor']) : '';
    $tcolor = isset($instance['tcolor']) ? esc_attr($instance['tcolor']) : '';
    $scolor = isset($instance['scolor']) ? esc_attr($instance['scolor']) : '';
    $atsize = isset($instance['atsize']) ? esc_attr($instance['atsize']) : '16';
    $tsize = isset($instance['tsize']) ? esc_attr($instance['tsize']) : '60';
    $ssize = isset($instance['ssize']) ? esc_attr($instance['ssize']) : '30';
    $atsmallsize = isset($instance['atsmallsize']) ? esc_attr($instance['atsmallsize']) : '';
    $tsmallsize = isset($instance['tsmallsize']) ? esc_attr($instance['tsmallsize']) : '';
    $ssmallsize = isset($instance['ssmallsize']) ? esc_attr($instance['ssmallsize']) : '';
    $btn_link = isset($instance['btn_link']) ? esc_attr($instance['btn_link']) : '';
    $btn_text = isset($instance['btn_text']) ? esc_attr($instance['btn_text']) : '';
    $btn_color = isset($instance['btn_color']) ? esc_attr($instance['btn_color']) : '';
    $btn_size = isset($instance['btn_size']) ? esc_attr($instance['btn_size']) : 'large';
    $tweight = isset($instance['tweight']) ? esc_attr($instance['tweight']) : 'default';
    $atweight = isset($instance['atweight']) ? esc_attr($instance['atweight']) : 'default';
    $sweight = isset($instance['sweight']) ? esc_attr($instance['sweight']) : 'default';
    $btn_background = isset($instance['btn_background']) ? esc_attr($instance['btn_background']) : '';
    $btn_border = isset($instance['btn_border']) ? esc_attr($instance['btn_border']) : '';
    $btn_border_radius = isset($instance['btn_border_radius']) ? esc_attr($instance['btn_border_radius']) : '';
    $btn_border_color = isset($instance['btn_border_color']) ? esc_attr($instance['btn_border_color']) : '';
    $btn_hover_color = isset($instance['btn_hover_color']) ? esc_attr($instance['btn_hover_color']) : '';
    $btn_hover_background = isset($instance['btn_hover_background']) ? esc_attr($instance['btn_hover_background']) : '';
    $btn_hover_border_color = isset($instance['btn_hover_border_color']) ? esc_attr($instance['btn_hover_border_color']) : '';
    $title_html_tag = isset($instance['title_html_tag']) ? esc_attr($instance['title_html_tag']) : 'h2';
    if (isset($instance['align'])) { $align = esc_attr($instance['align']); } else {$align = 'center';}
    if (isset($instance['btn_target'])) { $btn_target = esc_attr($instance['btn_target']); } else {$btn_target = 'false';}
    $align_array = array();
    $btn_target_array = array();
    $html_tag_array = array();
    $tweight_array = array();
    $atweight_array = array();
    $sweight_array = array();
    $btn_size_array = array();
    $btn_size_options = array(array("slug" => "large", "name" => __('Large', 'virtue')), array("slug" => "normal", "name" => __('Normal', 'virtue')), array("slug" => "small", "name" => __('Small', 'virtue')));
    $weight_options = array(array("slug" => "default", "name" => __('Default', 'virtue')), array("slug" => "300", "name" => __('Lighter', 'virtue')), array("slug" => "400", "name" => __('Normal', 'virtue')), array("slug" => "600", "name" => __('Bold', 'virtue')), array("slug" => "800", "name" => __('Bolder', 'virtue')));
    $html_tag_options = array(array("slug" => "h1", "name" => __('h1', 'virtue')), array("slug" => "h2", "name" => __('h2', 'virtue')), array("slug" => "h3", "name" => __('h3', 'virtue')), array("slug" => "div", "name" => __('div', 'virtue')));
    $align_options = array(array("slug" => "center", "name" => __('Center', 'virtue')), array("slug" => "left", "name" => __('Left', 'virtue')), array("slug" => "right", "name" => __('Right', 'virtue')));
    $btn_target_options = array(array("slug" => "false", "name" => __('Self', 'virtue')), array("slug" => "true", "name" => __('New Window', 'virtue')));
    foreach ($weight_options as $weight_option) {
      if ($tweight == $weight_option['slug']) { $selected=' selected="selected"';} else { $selected=""; }
      $tweight_array[] = '<option value="' . $weight_option['slug'] .'"' . $selected . '>' . $weight_option['name'] . '</option>';
    }
    foreach ($btn_size_options as $btn_size_option) {
      if ($btn_size == $btn_size_option['slug']) { $selected=' selected="selected"';} else { $selected=""; }
      $btn_size_array[] = '<option value="' . $btn_size_option['slug'] .'"' . $selected . '>' . $btn_size_option['name'] . '</option>';
    }
     foreach ($weight_options as $weight_option) {
      if ($sweight == $weight_option['slug']) { $selected=' selected="selected"';} else { $selected=""; }
      $sweight_array[] = '<option value="' . $weight_option['slug'] .'"' . $selected . '>' . $weight_option['name'] . '</option>';
    }
    foreach ($weight_options as $weight_option) {
      if ($atweight == $weight_option['slug']) { $selected=' selected="selected"';} else { $selected=""; }
      $atweight_array[] = '<option value="' . $weight_option['slug'] .'"' . $selected . '>' . $weight_option['name'] . '</option>';
    }
    foreach ($html_tag_options as $html_tag_option) {
      if ($title_html_tag == $html_tag_option['slug']) { $selected=' selected="selected"';} else { $selected=""; }
      $html_tag_array[] = '<option value="' . $html_tag_option['slug'] .'"' . $selected . '>' . $html_tag_option['name'] . '</option>';
    }
    foreach ($align_options as $align_option) {
      if ($align == $align_option['slug']) { $selected=' selected="selected"';} else { $selected=""; }
      $align_array[] = '<option value="' . $align_option['slug'] .'"' . $selected . '>' . $align_option['name'] . '</option>';
    }
    foreach ($btn_target_options as $btn_target_option) {
      if ($btn_target == $btn_target_option['slug']) { $selected=' selected="selected"';} else { $selected=""; }
      $btn_target_array[] = '<option value="' . $btn_target_option['slug'] .'"' . $selected . '>' . $btn_target_option['name'] . '</option>';
    }
    ?>  

    <div id="kadence_calltoaction_widget<?php echo esc_attr($this->get_field_id('container')); ?>" class="kad_calltoaction_widget kad-colorpick">
    		<p>
              <label for="<?php echo $this->get_field_id('abovetitle'); ?>"><?php _e('Above Title', 'virtue'); ?></label><br />
               <input class="widefat" id="<?php echo $this->get_field_id('abovetitle'); ?>" name="<?php echo $this->get_field_name('abovetitle'); ?>" type="text" value="<?php echo $abovetitle; ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('atsize'); ?>"><?php _e('Above Title Size (e.g. = 18)', 'virtue'); ?></label><br />
                <input type="number" class="widefat kad_img_widget_link" name="<?php echo $this->get_field_name('atsize'); ?>" id="<?php echo $this->get_field_id('atsize'); ?>" style="width: 70px;" value="<?php echo $atsize; ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('atsmallsize'); ?>"><?php _e('Smaller Device - Title Font Size (e.g. = 14)', 'virtue'); ?></label><br />
                <input type="number" class="widefat kad_img_widget_link" name="<?php echo $this->get_field_name('atsmallsize'); ?>" id="<?php echo $this->get_field_id('atsmallsize'); ?>" style="width: 70px;" value="<?php echo $atsmallsize; ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('atcolor'); ?>"><?php _e('Above Title Color (e.g. = #f2f2f2)', 'virtue'); ?></label><br />
                <input type="text" class="widefat kad-widget-colorpicker" name="<?php echo $this->get_field_name('atcolor'); ?>" id="<?php echo $this->get_field_id('atcolor'); ?>" value="<?php echo $atcolor; ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('atweight'); ?>"><?php _e('Above Title font Weight', 'virtue'); ?></label><br />
                <select id="<?php echo $this->get_field_id('atweight'); ?>" style="width:100%; max-width:230px;" name="<?php echo $this->get_field_name('atweight'); ?>"><?php echo implode('', $atweight_array);?></select>
            </p>
            <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'virtue'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('tsize'); ?>"><?php _e('Title Size (e.g. = 60)', 'virtue'); ?></label><br />
                <input type="number" class="widefat kad_img_widget_link" name="<?php echo $this->get_field_name('tsize'); ?>" id="<?php echo $this->get_field_id('tsize'); ?>" style="width: 70px;" value="<?php echo $tsize; ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('tsmallsize'); ?>"><?php _e('Smaller Device - Title Font Size (e.g. = 30)', 'virtue'); ?></label><br />
                <input type="number" class="widefat kad_img_widget_link" name="<?php echo $this->get_field_name('tsmallsize'); ?>" id="<?php echo $this->get_field_id('tsmallsize'); ?>" style="width: 70px;" value="<?php echo $tsmallsize; ?>">
            </p>
             <p>
                <label for="<?php echo $this->get_field_id('tcolor'); ?>"><?php _e('Title Color (e.g. = #f2f2f2)', 'virtue'); ?></label><br />
                <input type="text" class="kad-widget-colorpicker" name="<?php echo $this->get_field_name('tcolor'); ?>" id="<?php echo $this->get_field_id('tcolor'); ?>" style="width: 70px;" value="<?php echo $tcolor; ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('title_html_tag'); ?>"><?php _e('Title html Tag (e.g. = h2)', 'virtue'); ?></label><br />
                 <select id="<?php echo $this->get_field_id('title_html_tag'); ?>" style="width:100%; max-width:230px;" name="<?php echo $this->get_field_name('title_html_tag'); ?>"><?php echo implode('', $html_tag_array);?></select>
            </p>
             <p>
                <label for="<?php echo $this->get_field_id('tweight'); ?>"><?php _e('Title font Weight', 'virtue'); ?></label><br />
                <select id="<?php echo $this->get_field_id('tweight'); ?>" style="width:100%; max-width:230px;" name="<?php echo $this->get_field_name('tweight'); ?>"><?php echo implode('', $tweight_array);?></select>
            </p>
             <p>
              <label for="<?php echo $this->get_field_id('subtitle'); ?>"><?php _e('Subtitle', 'virtue'); ?></label><br />
              <textarea name="<?php echo $this->get_field_name('subtitle'); ?>" style="min-height: 50px;" id="<?php echo $this->get_field_id('subtitle'); ?>" class="widefat" ><?php echo $subtitle; ?></textarea>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('ssize'); ?>"><?php _e('Subtitle Size (e.g. = 50)', 'virtue'); ?></label><br />
                <input type="number" class="widefat kad_img_widget_link" name="<?php echo $this->get_field_name('ssize'); ?>" id="<?php echo $this->get_field_id('ssize'); ?>" style="width: 70px;" value="<?php echo $ssize; ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('ssmallsize'); ?>"><?php _e('Smaller Device - Title Font Size (e.g. = 20)', 'virtue'); ?></label><br />
                <input type="number" class="widefat kad_img_widget_link" name="<?php echo $this->get_field_name('ssmallsize'); ?>" id="<?php echo $this->get_field_id('ssmallsize'); ?>" style="width: 70px;" value="<?php echo $ssmallsize; ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('scolor'); ?>"><?php _e('Subtitle Color (e.g. = #f2f2f2)', 'virtue'); ?></label><br />
                <input type="text" class="widefat kad-widget-colorpicker" name="<?php echo $this->get_field_name('scolor'); ?>" id="<?php echo $this->get_field_id('scolor'); ?>" value="<?php echo $scolor; ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('sweight'); ?>"><?php _e('Subtitle font Weight', 'virtue'); ?></label><br />
                <select id="<?php echo $this->get_field_id('sweight'); ?>" style="width:100%; max-width:230px;" name="<?php echo $this->get_field_name('sweight'); ?>"><?php echo implode('', $sweight_array);?></select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('align'); ?>"><?php _e('Align', 'virtue'); ?></label><br />
                <select id="<?php echo $this->get_field_id('align'); ?>" style="width:100%; max-width:230px;" name="<?php echo $this->get_field_name('align'); ?>"><?php echo implode('', $align_array);?></select>
            </p>
            <p>
            <label for="<?php echo $this->get_field_id('btn_text'); ?>"><?php _e('Button Text:', 'virtue'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('btn_text'); ?>" name="<?php echo $this->get_field_name('btn_text'); ?>" type="text" value="<?php echo $btn_text; ?>" />
            </p>
            <p>
            <label for="<?php echo $this->get_field_id('btn_link'); ?>"><?php _e('Button Link:', 'virtue'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('btn_link'); ?>" name="<?php echo $this->get_field_name('btn_link'); ?>" type="text" value="<?php echo $btn_link; ?>" />
            </p>
             <p>
                <label for="<?php echo $this->get_field_id('btn_target'); ?>"><?php _e('Link Target', 'virtue'); ?></label><br />
                <select id="<?php echo $this->get_field_id('btn_target'); ?>" style="width:100%; max-width:230px;" name="<?php echo $this->get_field_name('btn_target'); ?>"><?php echo implode('', $btn_target_array);?></select>
            </p>
             <p>
                <label for="<?php echo $this->get_field_id('btn_color'); ?>"><?php _e('Button Color', 'virtue'); ?></label><br />
                <input type="text" class="kad-widget-colorpicker" name="<?php echo $this->get_field_name('btn_color'); ?>" id="<?php echo $this->get_field_id('btn_color'); ?>" style="width: 70px;" value="<?php echo $btn_color; ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('btn_background'); ?>"><?php _e('Button Background', 'virtue'); ?></label><br />
                <input type="text" class="kad-widget-colorpicker" name="<?php echo $this->get_field_name('btn_background'); ?>" id="<?php echo $this->get_field_id('btn_background'); ?>" style="width: 70px;" value="<?php echo $btn_background; ?>">
            </p>
            <p>
            <label for="<?php echo $this->get_field_id('btn_border'); ?>"><?php _e('Button Border Size (e.g. = 2px)', 'virtue'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('btn_border'); ?>" name="<?php echo $this->get_field_name('btn_border'); ?>" type="text" value="<?php echo $btn_border; ?>" />
            </p>
            <p>
            <label for="<?php echo $this->get_field_id('btn_border_radius'); ?>"><?php _e('Button Border Radius (e.g. = 6px)', 'virtue'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('btn_border_radius'); ?>" name="<?php echo $this->get_field_name('btn_border_radius'); ?>" type="text" value="<?php echo $btn_border_radius; ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('btn_border_color'); ?>"><?php _e('Button Border Color', 'virtue'); ?></label><br />
                <input type="text" class="kad-widget-colorpicker" name="<?php echo $this->get_field_name('btn_border_color'); ?>" id="<?php echo $this->get_field_id('btn_border_color'); ?>" style="width: 70px;" value="<?php echo $btn_border_color; ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('btn_hover_color'); ?>"><?php _e('Button Hover Color', 'virtue'); ?></label><br />
                <input type="text" class="kad-widget-colorpicker" name="<?php echo $this->get_field_name('btn_hover_color'); ?>" id="<?php echo $this->get_field_id('btn_hover_color'); ?>" style="width: 70px;" value="<?php echo $btn_hover_color; ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('btn_hover_background'); ?>"><?php _e('Button Hover Background', 'virtue'); ?></label><br />
                <input type="text" class="kad-widget-colorpicker" name="<?php echo $this->get_field_name('btn_hover_background'); ?>" id="<?php echo $this->get_field_id('btn_hover_background'); ?>" style="width: 70px;" value="<?php echo $btn_hover_background; ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('btn_hover_border_color'); ?>"><?php _e('Button Hover Border Color', 'virtue'); ?></label><br />
                <input type="text" class="kad-widget-colorpicker" name="<?php echo $this->get_field_name('btn_hover_border_color'); ?>" id="<?php echo $this->get_field_id('btn_hover_border_color'); ?>" style="width: 70px;" value="<?php echo $btn_hover_border_color; ?>">
            </p>
             <p>
                <label for="<?php echo $this->get_field_id('btn_size'); ?>"><?php _e('Button Size', 'virtue'); ?></label><br />
                <select id="<?php echo $this->get_field_id('btn_size'); ?>" style="width:100%; max-width:230px;" name="<?php echo $this->get_field_name('btn_size'); ?>"><?php echo implode('', $btn_size_array);?></select>
            </p>
    </div>

<?php } }