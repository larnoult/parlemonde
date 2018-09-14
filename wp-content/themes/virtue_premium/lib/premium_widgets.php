<?php
/**
 * Virtue Premium Widgets
 *
 * @package Virtue Theme
 */

class kad_infobox_widget extends WP_Widget{

private static $instance = 0;
    public function __construct() {
        $widget_ops = array('classname' => 'virtue_infobox_widget', 'description' => __('Adds a info box with icon options', 'virtue'));
        parent::__construct('virtue_infobox_widget', __('Virtue: Info Box', 'virtue'), $widget_ops);
    }

       public function widget($args, $instance){ 
        extract( $args );
        //title
        $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        if(!empty($title)) { $title = '<h4>'.$title.'</h4>';} else {$title = '';}
        //description & link
        if(!empty($instance['description'])) { $description = $instance['description'];} else {$description = '';}
        if(!empty($instance['link'])) {$link = $instance["link"];} else {$link = '';}
        if(!empty($description)) {$description = '<p>'.$description.'</p>';} else {$description = '';}
        if(!empty($link)) { $link = 'link='.$link; } else {$link = '';}

        if(!empty($instance['image_uri'])) {$imglink = esc_url($instance['image_uri']);} else {$imglink = '';}
        if(!empty($instance["info_icon"])) {$icon = 'icon='.$instance["info_icon"];} else {$icon = '';}
        if(!empty($instance["background"])) {$info_background = 'background='.$instance["background"];} else {$info_background = '';}
        if(!empty($instance["iconbackground"])) {$icon_background = 'iconbackground='.$instance["iconbackground"];} else {$icon_background = '';}
        if(!empty($instance["size"])) {$info_size = 'size='.$instance["size"];} else {$info_size = 'size=48';}
        if(!empty($instance["style"])) { $style = 'style='.$instance["style"]; } else {$style = '';}
        if(!empty($instance["color"])) { $color = 'color='.$instance["color"]; } else {$color = '';}
        if(!empty($instance["tcolor"])) { $tcolor = 'tcolor='.$instance["tcolor"]; } else {$tcolor = '';}
        if(!empty($instance["target"])) { $target = 'target='.$instance["target"]; } else {$target = '';}
        if(!empty($imglink)) {$info_icon = 'image='.$imglink;} else {$info_icon = $icon;}
        if(!empty($instance['image_id'])) {
          $alt = 'alt="'.esc_attr( get_post_meta($instance['image_id'], '_wp_attachment_image_alt', true) ).'"';
        } else {
          $alt = '';
        }

            ?>


          <?php echo $before_widget;
           echo do_shortcode('[infobox '.$link.' '.$target.' '.$info_icon.' '.$tcolor.' '.$info_size.' '.$info_background.' '.$alt.' '.$style .' '.$icon_background.' '.$color.'] '.$title.' '. $description.'[/infobox]');
           echo $after_widget;?>

    <?php }

    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['info_icon'] = $new_instance['info_icon'];
        $instance['image_uri'] = strip_tags( $new_instance['image_uri'] );
        $instance['background'] = strip_tags( $new_instance['background'] );
        $instance['iconbackground'] = strip_tags($new_instance['iconbackground'] );
        $instance['color'] = strip_tags( $new_instance['color'] );
        $instance['tcolor'] = strip_tags( $new_instance['tcolor'] );
        $instance['size'] = (int) $new_instance['size']; 
        $instance['style'] = $new_instance['style'];
        $instance['target'] = $new_instance['target'];
        $instance['description'] = $new_instance['description'];
        $instance['title'] = $new_instance['title'];
        $instance['image_id'] = $new_instance['image_id'];
        $instance['link'] = $new_instance['link'];
        if ( function_exists( 'icl_register_string' ) ) {
            icl_register_string( 'Widgets', 'info_box_description_' . $this->id_base, $instance['description'] );
            icl_register_string( 'Widgets', 'info_box_link_' . $this->id_base, $instance['link'] ) ;
        }
        return $instance;
    }

  public function form($instance){ 
    $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
    $link = isset($instance['link']) ? esc_attr($instance['link']) : '';
    $background = isset($instance['background']) ? esc_attr($instance['background']) : '';
    $iconbackground = isset($instance['iconbackground']) ? esc_attr($instance['iconbackground']) : '';
    $color = isset($instance['color']) ? esc_attr($instance['color']) : '';
    $tcolor = isset($instance['tcolor']) ? esc_attr($instance['tcolor']) : '';
    $size = isset($instance['size']) ? esc_attr($instance['size']) : '';
    if (isset($instance['target'])) { $target = esc_attr($instance['target']); } else {$target = '_self';}
    if (isset($instance['info_icon'])) { $info_icon = esc_attr($instance['info_icon']); } else {$info_icon = '';}
    $image_uri = isset($instance['image_uri']) ? esc_attr($instance['image_uri']) : '';
    $image_id = isset($instance['image_id']) ? esc_attr($instance['image_id']) : '';
    if (isset($instance['style'])) { $style = esc_attr($instance['style']); } else {$style = 'none';}
    $icon_style_array = array();
    $icon_array = array();
    $target_options = array(array("slug" => "_self", "name" => __('Self', 'virtue')), array("slug" => "_blank", "name" => __('New Window', 'virtue')));
    foreach ($target_options as $target_option) {
      if ($target == $target_option['slug']) { $selected=' selected="selected"';} else { $selected=""; }
      $target_array[] = '<option value="' . $target_option['slug'] .'"' . $selected . '>' . $target_option['name'] . '</option>';
    }
    $icon_style_options = array(array("slug" => "none", "name" => __('None', 'virtue')), array("slug" => "kad-circle-iconclass", "name" => __('Circle', 'virtue')), array("slug" => "kad-square-iconclass", "name" => __('Square', 'virtue')));
    $icons = kad_icon_list();
    foreach ($icons as $icon) {
      if ($info_icon == $icon) { $selected=' selected="selected"';} else { $selected=""; }
      $icon_array[] = '<option value="' . $icon .'"' . $selected . '>' . $icon . '</option>';
    }
    foreach ($icon_style_options as $icon_style_option) {
      if ($style == $icon_style_option['slug']) { $selected=' selected="selected"';} else { $selected=""; }
      $icon_style_array[] = '<option value="' . $icon_style_option['slug'] .'"' . $selected . '>' . $icon_style_option['name'] . '</option>';
    }
    ?>  

    <div id="virtue_infobox_widget<?php echo esc_attr($this->get_field_id('container')); ?>" class="kad_img_upload_widget kad_infobox_widget">
            <p>
                <label for="<?php echo $this->get_field_id('info_icon'); ?>"><?php _e('Choose an Icon', 'virtue'); ?></label><br />
                <select id="<?php echo $this->get_field_id('info_icon'); ?>" class="kad_icomoon" name="<?php echo $this->get_field_name('info_icon'); ?>"><?php echo implode('', $icon_array);?></select>
            </p>
            <p>
            <img class="kad_custom_media_image" src="<?php if(!empty($instance['image_uri'])){echo $instance['image_uri'];} ?>" style="margin:0;padding:0;max-width:100px;display:block" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('image_uri'); ?>"><?php _e('Or upload a custom icon', 'virtue'); ?></label><br />
                <input type="text" class="widefat kad_custom_media_url" name="<?php echo $this->get_field_name('image_uri'); ?>" id="<?php echo $this->get_field_id('image_uri'); ?>" value="<?php echo $image_uri; ?>">
                <input type="hidden" value="<?php echo $image_id; ?>" class="kad_custom_media_id" name="<?php echo $this->get_field_name('image_id'); ?>" id="<?php echo $this->get_field_id('image_id'); ?>" />
                <input type="button" value="<?php _e('Upload', 'virtue'); ?>" class="button kad_custom_media_upload" id="kad_custom_image_uploader" />
            </p>
            <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'virtue'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
            </p>
             <p>
              <label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Description', 'virtue'); ?></label><br />
              <textarea name="<?php echo $this->get_field_name('description'); ?>" style="min-height: 100px;" id="<?php echo $this->get_field_id('description'); ?>" class="widefat" ><?php if(!empty($instance['description'])) echo $instance['description']; ?></textarea>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('background'); ?>"><?php _e('Box Background Color (e.g. = #f2f2f2)', 'virtue'); ?></label><br />
                <input type="text" class="widefat kad-widget-colorpicker" style="width: 70px;"  name="<?php echo $this->get_field_name('background'); ?>" id="<?php echo $this->get_field_id('background'); ?>" value="<?php echo $background; ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('tcolor'); ?>"><?php _e('Text Color (e.g. = #444444)', 'virtue'); ?></label><br />
                <input type="text" class="widefat kad-widget-colorpicker" style="width: 70px;"  name="<?php echo $this->get_field_name('tcolor'); ?>" id="<?php echo $this->get_field_id('tcolor'); ?>" value="<?php echo $tcolor; ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('size'); ?>"><?php _e('Icon Size (e.g. = 48)', 'virtue'); ?></label><br />
                <input type="number" class="widefat kad_img_widget_link" name="<?php echo $this->get_field_name('size'); ?>" id="<?php echo $this->get_field_id('size'); ?>" value="<?php echo $size; ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('style'); ?>"><?php _e('Icon Style', 'virtue'); ?></label><br />
                <select id="<?php echo $this->get_field_id('style'); ?>" style="width:100%; max-width:230px;" name="<?php echo $this->get_field_name('style'); ?>"><?php echo implode('', $icon_style_array);?></select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('iconbackground'); ?>"><?php _e('Icon Background Color (e.g. = #444444)', 'virtue'); ?></label><br />
                <input type="text" class="widefat kad-widget-colorpicker" style="width: 70px;"  name="<?php echo $this->get_field_name('iconbackground'); ?>" id="<?php echo $this->get_field_id('iconbackground'); ?>" value="<?php echo $iconbackground; ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('color'); ?>"><?php _e('Icon Color (e.g. = #f2f2f2)', 'virtue'); ?></label><br />
                <input type="text" class="widefat kad-widget-colorpicker" style="width: 70px;"  name="<?php echo $this->get_field_name('color'); ?>" id="<?php echo $this->get_field_id('color'); ?>" value="<?php echo $color; ?>">
            </p>
            <p>
            <label for="<?php echo $this->get_field_id('link'); ?>"><?php _e('Link:', 'virtue'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('link'); ?>" name="<?php echo $this->get_field_name('link'); ?>" type="text" value="<?php echo $link; ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('target'); ?>"><?php _e('Link Target', 'virtue'); ?></label><br />
                <select id="<?php echo $this->get_field_id('target'); ?>" style="width:100%; max-width:230px;" name="<?php echo $this->get_field_name('target'); ?>"><?php echo implode('', $target_array);?></select>
            </p>

    </div>

<?php } }


class kad_icon_flip_box_widget extends WP_Widget{
private static $instance = 0;
    public function __construct() {
        $widget_ops = array('classname' => 'virtue_icon_flip_box_widget', 'description' => __('Adds a box that flips to show more content.', 'virtue'));
        parent::__construct('virtue_icon_flip_box_widget', __('Virtue: Icon Flip Box', 'virtue'), $widget_ops);
    }

       public function widget($args, $instance){ 
        extract( $args ); 
        if(!empty($instance["title"])) {$title = 'title="'.$instance["title"].'"';} else {$title = '';}
        if(!empty($instance["description"])) {$description = 'description="'. str_replace('"', '&quot;', $instance["description"] ). '"';} else {$description = '';}
        if(!empty($instance['icon'])) {$icon = 'icon="'.$instance['icon'].'"';} else {$icon = '';}
        if(!empty($instance['iconcolor'])) {$iconcolor = 'iconcolor="'.$instance['iconcolor'].'"';} else {$iconcolor = '';}
        if(!empty($instance['titlecolor'])) {$titlecolor = 'titlecolor="'.$instance['titlecolor'].'"';} else {$titlecolor = '';}
        if(!empty($instance['fcolor'])) {$fcolor = 'fcolor="'.$instance['fcolor'].'"';} else {$fcolor = '';}
        if(!empty($instance['titlesize'])) {$titlesize = 'titlesize="'.$instance['titlesize'].'px"';} else {$titlesize = '';}
        if(!empty($instance['image'])) {$image = 'image="'.$instance['image'].'"';} else {$image = '';}
        if(!empty($instance['height'])) {$height = 'height="'.$instance['height'].'px"';} else {$height = '';}
        if(!empty($instance["iconsize"])) { $iconsize = 'iconsize="'.$instance["iconsize"].'px" ';} else {$iconsize = '';}
        if(!empty($instance["flip_content"])) { $flip_content = 'flip_content="' . str_replace('"', '&quot;', $instance['flip_content'] ) . '" ';} else {$flip_content = '';}
        if(!empty($instance["fbtn_text"])) { $fbtn_text = 'fbtn_text="'.$instance["fbtn_text"].'" ';} else {$fbtn_text = '';}
        if(!empty($instance["fbtn_link"])) { $fbtn_link = 'fbtn_link="'.$instance["fbtn_link"].'" ';} else {$fbtn_link = '';}
        if(!empty($instance["fbtn_color"])) { $fbtn_color = 'fbtn_color="'.$instance["fbtn_color"].'"';} else {$fbtn_color = '';}
        if(!empty($instance["fbtn_icon"])) { $fbtn_icon = 'fbtn_icon="'.$instance["fbtn_icon"].'"';} else {$fbtn_icon = '';}
        if(!empty($instance["fbtn_background"])) { $fbtn_background = 'fbtn_background="'.$instance["fbtn_background"].'"';} else {$fbtn_background = '';}
        if(!empty($instance["fbtn_border"])) { $fbtn_border = 'fbtn_border="'.$instance["fbtn_border"].'"';} else {$fbtn_border = '';}
        if(!empty($instance["fbtn_border_radius"])) { $fbtn_border_radius = 'fbtn_border_radius="'.$instance["fbtn_border_radius"].'px"';} else {$fbtn_border_radius = '';}
        if(!empty($instance["background"])) { $background = 'background="'.$instance["background"].'"';} else {$background = '';}
        if(!empty($instance["bcolor"])) { $bcolor = 'bcolor="'.$instance["bcolor"].'"';} else {$bcolor = '';}
        if(!empty($instance["bbackground"])) { $bbackground = 'bbackground="'.$instance["bbackground"].'"';} else {$bbackground = '';}
        if(!empty($instance["fbtn_target"])) { $fbtn_target = 'fbtn_target="'.$instance["fbtn_target"].'"';} else {$fbtn_target = '';}
            ?>

                <?php echo $before_widget; ?>
                <?php $output = '[kt_flip_box '.$icon.' '.$height.' '.$iconsize.' '.$iconcolor.' '.$titlecolor.' '.$fcolor.' '.$title.' '.$description.' '.$titlesize.' '.$image.' '.$flip_content.' '.$fbtn_text.' '.$fbtn_color.' '.$fbtn_icon.' '.$fbtn_background.' '.$fbtn_border.' '.$fbtn_border_radius.' '.$background.' '.$bcolor.' '.$bbackground.' '.$fbtn_target.' '.$fbtn_link.']';
                echo do_shortcode($output); ?>

                <?php echo $after_widget;?>

    <?php }

    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        $instance['description'] = $new_instance['description'];
        $instance['icon'] = $new_instance['icon'];
        $instance['iconcolor'] = $new_instance['iconcolor'];
        $instance['titlecolor'] = $new_instance['titlecolor'];
        $instance['fcolor'] = $new_instance['fcolor'];
        $instance['image'] = $new_instance['image'];
        $instance['flip_content'] = $new_instance['flip_content'];
        $instance['fbtn_text'] = $new_instance['fbtn_text'];

        $instance['fbtn_link'] = $new_instance['fbtn_link'];
        $instance['fbtn_color'] = $new_instance['fbtn_color'];
        $instance['fbtn_icon'] = $new_instance['fbtn_icon'];
        $instance['fbtn_background'] = $new_instance['fbtn_background'];
        $instance['fbtn_border'] = $new_instance['fbtn_border'];
        $instance['background'] = $new_instance['background'];
        $instance['bcolor'] = $new_instance['bcolor'];
        $instance['bbackground'] = $new_instance['bbackground'];
        $instance['fbtn_target'] = $new_instance['fbtn_target'];

        $instance['height'] = (int) $new_instance['height'];
        $instance['titlesize'] = (int) $new_instance['titlesize'];
        $instance['iconsize'] = (int) $new_instance['iconsize'];
        $instance['fbtn_border_radius'] = (int) $new_instance['fbtn_border_radius'];

        return $instance;
    }

  public function form($instance){ 
    $title = isset($instance['title']) ? $instance['title'] : '';
    $description = isset($instance['description']) ? $instance['description'] : '';
    $icon = isset($instance['icon']) ? $instance['icon'] : '';
    $iconcolor = isset($instance['iconcolor']) ? $instance['iconcolor'] : '';
    $titlecolor = isset($instance['titlecolor']) ? $instance['titlecolor'] : '';
    $fcolor = isset($instance['fcolor']) ? $instance['fcolor'] : '';
    $image = isset($instance['image']) ? $instance['image'] : '';
    $flip_content = isset($instance['flip_content']) ? $instance['flip_content'] : '';
    $fbtn_text = isset($instance['fbtn_text']) ? $instance['fbtn_text'] : '';
    $fbtn_color = isset($instance['fbtn_color']) ? $instance['fbtn_color'] : '';
    $fbtn_border = isset($instance['fbtn_border']) ? $instance['fbtn_border'] : '2px solid #ffffff';
    $fbtn_icon = isset($instance['fbtn_icon']) ? $instance['fbtn_icon'] : '';
    $fbtn_background = isset($instance['fbtn_background']) ? $instance['fbtn_background'] : '';
    $background = isset($instance['background']) ? $instance['background'] : '';
    $bcolor = isset($instance['bcolor']) ? $instance['bcolor'] : '';
    $bbackground = isset($instance['bbackground']) ? $instance['bbackground'] : '';
    $iconsize = isset($instance['iconsize']) ? $instance['iconsize'] : '48';
    $titlesize = isset( $instance['titlesize'] ) ? $instance['titlesize'] : '24';
    $height = isset( $instance['height'] ) ? $instance['height'] : '';
    $fbtn_border_radius = isset( $instance['fbtn_border_radius'] ) ? $instance['fbtn_border_radius'] : '0';
   
    $image = isset($instance['image']) ? esc_url($instance['image']) : '';
    $fbtn_link = isset($instance['fbtn_link']) ? esc_url($instance['fbtn_link']) : '';
    $fbtn_target = isset($instance['fbtn_target']) ? esc_attr($instance['fbtn_target']) : '_self';
    $target_options = array(array("slug" => "_self", "name" => __('Self', 'virtue')), array("slug" => "_blank", "name" => __('New Window', 'virtue')));
    foreach ($target_options as $target_option) {
      if ($fbtn_target == $target_option['slug']) { $selected=' selected="selected"';} else { $selected=""; }
      $target_array[] = '<option value="' . $target_option['slug'] .'"' . $selected . '>' . $target_option['name'] . '</option>';
    }
    $icons = kad_icon_list();
    foreach ($icons as $ico) {
      if ($icon == $ico) { $selected=' selected="selected"';} else { $selected=""; }
      $icon_array[] = '<option value="' . $ico .'"' . $selected . '>' . $ico . '</option>';
    }
    $icon_btn_array[] = '<option value="">' . __('None', 'virtue') . '</option>';
    foreach ($icons as $ico) {
      if ($fbtn_icon == $ico) { $selected=' selected="selected"';} else { $selected=""; }
      $icon_btn_array[] = '<option value="' . $ico .'"' . $selected . '>' . $ico . '</option>';
    }
    ?>  

    <div id="virtue_icon_flip_box_widget<?php echo esc_attr($this->get_field_id('container')); ?>" class="kad_img_upload_widget kad_infobox_widget">
            <h4><?php _e('Front Side', 'virtue');?></h4>
            <p>
                <label for="<?php echo $this->get_field_id('icon'); ?>"><?php _e('Choose an Icon', 'virtue'); ?></label><br />
                <select id="<?php echo $this->get_field_id('icon'); ?>" class="kad_icomoon" name="<?php echo $this->get_field_name('icon'); ?>"><?php echo implode('', $icon_array);?></select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('iconsize'); ?>"><?php _e('Icon Size', 'virtue'); ?></label><br />
                <input type="number" class="widefat kad_img_widget_link" name="<?php echo $this->get_field_name('iconsize'); ?>" id="<?php echo $this->get_field_id('iconsize'); ?>" style="width: 70px;" value="<?php echo $iconsize; ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('iconcolor'); ?>"><?php _e('Icon Color', 'virtue'); ?></label><br />
                <input type="text" class="kad-widget-colorpicker" name="<?php echo $this->get_field_name('iconcolor'); ?>" id="<?php echo $this->get_field_id('iconcolor'); ?>" style="width: 70px;" value="<?php echo $iconcolor; ?>">
            </p>
            <p>
            <img class="kad_custom_media_image" src="<?php if(!empty($instance['image'])){echo $instance['image'];} ?>" style="margin:0;padding:0;max-width:100px;display:block" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('image'); ?>"><?php _e('Optional Image instead of icon', 'virtue'); ?></label><br />
                <input type="text" class="widefat kad_custom_media_url" name="<?php echo $this->get_field_name('image'); ?>" id="<?php echo $this->get_field_id('image'); ?>" value="<?php echo $image; ?>">
                <input type="button" value="<?php _e('Upload', 'virtue'); ?>" class="button kad_custom_media_upload" id="kad_custom_image_uploader" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'virtue'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('titlesize'); ?>"><?php _e('Title Size', 'virtue'); ?></label><br />
                <input type="number" class="widefat kad_img_widget_link" name="<?php echo $this->get_field_name('titlesize'); ?>" id="<?php echo $this->get_field_id('titlesize'); ?>" style="width: 70px;" value="<?php echo $titlesize; ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('titlecolor'); ?>"><?php _e('Title Color', 'virtue'); ?></label><br />
                <input type="text" class="kad-widget-colorpicker" name="<?php echo $this->get_field_name('titlecolor'); ?>" id="<?php echo $this->get_field_id('titlecolor'); ?>" style="width: 70px;" value="<?php echo $titlecolor; ?>">
            </p>
           <p>
              <label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Description', 'virtue'); ?></label><br />
              <textarea class="widefat" rows="6" cols="20" id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>" ><?php echo esc_textarea( $description ); ?></textarea>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('fcolor'); ?>"><?php _e('Description Color', 'virtue'); ?></label><br />
                <input type="text" class="kad-widget-colorpicker" name="<?php echo $this->get_field_name('fcolor'); ?>" id="<?php echo $this->get_field_id('fcolor'); ?>" style="width: 70px;" value="<?php echo $fcolor; ?>">
            </p>

            <p>
                <label for="<?php echo $this->get_field_id('background'); ?>"><?php _e('Front Side Background', 'virtue'); ?></label><br />
                <input type="text" class="kad-widget-colorpicker" name="<?php echo $this->get_field_name('background'); ?>" id="<?php echo $this->get_field_id('background'); ?>" style="width: 70px;" value="<?php echo $background; ?>">
            </p>
            <h4><?php _e('Back Side', 'virtue');?></h4>
            <p>
              <label for="<?php echo $this->get_field_id('flip_content'); ?>"><?php _e('Back Side Description', 'virtue'); ?></label><br />
              <textarea class="widefat" rows="6" cols="20" id="<?php echo $this->get_field_id('flip_content'); ?>" name="<?php echo $this->get_field_name('flip_content'); ?>" ><?php echo esc_textarea( $flip_content ); ?></textarea>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('bcolor'); ?>"><?php _e('Back Side Description Color', 'virtue'); ?></label><br />
                <input type="text" class="kad-widget-colorpicker" name="<?php echo $this->get_field_name('bcolor'); ?>" id="<?php echo $this->get_field_id('bcolor'); ?>" style="width: 70px;" value="<?php echo $bcolor; ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('fbtn_text'); ?>"><?php _e('Button Text', 'virtue'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('fbtn_text'); ?>" name="<?php echo $this->get_field_name('fbtn_text'); ?>" type="text" value="<?php echo $fbtn_text; ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('fbtn_link'); ?>"><?php _e('Button Link', 'virtue'); ?></label><br />
                <input type="text" class="widefat" name="<?php echo $this->get_field_name('fbtn_link'); ?>" id="<?php echo $this->get_field_id('fbtn_link'); ?>"value="<?php echo $fbtn_link; ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('fbtn_color'); ?>"><?php _e('Button Text Color', 'virtue'); ?></label><br />
                <input type="text" class="kad-widget-colorpicker" name="<?php echo $this->get_field_name('fbtn_color'); ?>" id="<?php echo $this->get_field_id('fbtn_color'); ?>" style="width: 70px;" value="<?php echo $fbtn_color; ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('fbtn_background'); ?>"><?php _e('Button Background Color', 'virtue'); ?></label><br />
                <input type="text" class="kad-widget-colorpicker" name="<?php echo $this->get_field_name('fbtn_background'); ?>" id="<?php echo $this->get_field_id('fbtn_background'); ?>" style="width: 70px;" value="<?php echo $fbtn_background; ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('fbtn_border'); ?>"><?php _e('Button Border (example: 2px solid #ffffff)', 'virtue'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('fbtn_border'); ?>" name="<?php echo $this->get_field_name('fbtn_border'); ?>" type="text" value="<?php echo $fbtn_border; ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('fbtn_border_radius'); ?>"><?php _e('Button Border Radius (example: 6)', 'virtue'); ?></label><br />
                <input type="number" class="widefat kad_img_widget_link" name="<?php echo $this->get_field_name('fbtn_border_radius'); ?>" id="<?php echo $this->get_field_id('fbtn_border_radius'); ?>" style="width: 70px;" value="<?php echo $fbtn_border_radius; ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('fbtn_icon'); ?>"><?php _e('Button Icon (optional)', 'virtue'); ?></label><br />
                <select id="<?php echo $this->get_field_id('fbtn_icon'); ?>" class="kad_icomoon" name="<?php echo $this->get_field_name('fbtn_icon'); ?>"><?php echo implode('', $icon_btn_array);?></select>
            </p>
             <p>
                <label for="<?php echo $this->get_field_id('fbtn_target'); ?>"><?php _e('Button Link Target', 'virtue'); ?></label><br />
                <select id="<?php echo $this->get_field_id('fbtn_target'); ?>" style="width:100%; max-width:230px;" name="<?php echo $this->get_field_name('fbtn_target'); ?>"><?php echo implode('', $target_array);?></select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('bbackground'); ?>"><?php _e('Back Side Background', 'virtue'); ?></label><br />
                <input type="text" class="kad-widget-colorpicker" name="<?php echo $this->get_field_name('bbackground'); ?>" id="<?php echo $this->get_field_id('bbackground'); ?>" style="width: 70px;" value="<?php echo $bbackground; ?>">
            </p>


            <h4><?php _e('Box Height', 'virtue');?></h4>
            <p>
                <label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Height (example: 280)', 'virtue'); ?></label><br />
                <input type="number" class="widefat kad_img_widget_link" name="<?php echo $this->get_field_name('height'); ?>" id="<?php echo $this->get_field_id('height'); ?>" style="width: 70px;" value="<?php echo $height; ?>">
            </p>
    </div>

<?php } }