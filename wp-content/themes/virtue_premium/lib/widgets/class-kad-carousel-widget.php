<?php 
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class kad_carousel_widget extends WP_Widget{

private static $instance = 0;
    public function __construct() {
        $widget_ops = array('classname' => 'virtue_carousel_widget', 'description' => __('Adds a carousel to any widget area', 'virtue'));
        parent::__construct('virtue_carousel_widget', __('Virtue: Carousel', 'virtue'), $widget_ops);
    }

       public function widget($args, $instance){ 
        extract( $args ); 
        $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        if(!empty($instance["type"])) {$c_type = $instance["type"];} else {$c_type = 'post';}
        if(!empty($instance["c_order"])) {$c_order = 'orderby='.$instance["c_order"];} else {$c_order = '';}
        if(!empty($instance["autoplay"])) {$autoplay = 'autoplay='.$instance["autoplay"];} else {$autoplay = '';}
        if(!empty($instance["c_items"])) {$c_items = 'items='.$instance["c_items"];} else {$c_items = 'items=6';}
        if(!empty($instance["c_speed"])) {$c_speed = 'speed='.$instance["c_speed"];} else {$c_speed = '';}
         if($c_type == "cat-products" || $c_type == "sale-products") {
            if(!empty($instance["productcat"])) {$c_cat = 'cat='.$instance["productcat"];} else {$c_cat = '';}
        } else if ($c_type == "portfolio") {
            if(!empty($instance["portfoliocat"])) {$c_cat = 'cat='.$instance["portfoliocat"];} else {$c_cat = '';}
        } else {
            if(!empty($instance["postcat"])) {$c_cat = 'cat='.$instance["postcat"];} else {$c_cat = '';}
        }
        if(!empty($instance["c_columns"])) { $c_columns = $instance["c_columns"]; } else {$c_columns = '1';}
        if(!empty($instance["c_scroll"])) { $c_scroll = $instance["c_scroll"]; } else {$c_scroll = '1';}

            ?>


          <?php echo $before_widget;
            if ( $title ) echo $before_title . $title . $after_title; 
           echo do_shortcode('[carousel type='.$c_type.' '.$c_items.' '.$autoplay.' '.$c_order.' columns='.$c_columns.' '.$c_speed.' '.$c_cat.' scroll='.$c_scroll.']');
           echo $after_widget;?>

    <?php }

    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['type'] = $new_instance['type'];
        $instance['c_items'] = (int) $new_instance['c_items']; 
        $instance['c_columns'] = $new_instance['c_columns'];
        $instance['autoplay'] = $new_instance['autoplay'];
        $instance['c_order'] = $new_instance['c_order'];
        $instance['c_scroll'] = $new_instance['c_scroll'];
        $instance['postcat'] = $new_instance['postcat'];
        $instance['portfoliocat'] = $new_instance['portfoliocat'];
        $instance['productcat'] = $new_instance['productcat'];
        $instance['c_speed'] = (int) $new_instance['c_speed'];
        $instance['title'] = strip_tags( $new_instance['title'] );
        return $instance;
    }

  public function form($instance){ 
    $c_items = isset($instance['c_items']) ? esc_attr($instance['c_items']) : '';
    $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
    $c_speed = isset($instance['c_speed']) ? esc_attr($instance['c_speed']) : '';
    $autoplay = isset($instance['autoplay']) ? esc_attr($instance['autoplay']) : 'true';
    if (isset($instance['type'])) { $c_type = esc_attr($instance['type']); } else {$c_type = 'post';}
    if (isset($instance['c_scroll'])) { $c_scroll = esc_attr($instance['c_scroll']); } else {$c_scroll = '1';}
    if (isset($instance['c_order'])) { $c_order = esc_attr($instance['c_order']); } else {$c_order = 'menu_order';}
    if (isset($instance['c_columns'])) { $c_columns = esc_attr($instance['c_columns']); } else {$c_columns = '1';}
    $carousel_type_array = array();
    $carousel_scroll_array = array();
    $carousel_columns_array = array();
    $carousel_order_array = array();
    $carousel_types = array(
        'post' => array("slug" => "post", "name" => __('Blog Posts', 'virtue')), 
        'portfolio' => array("slug" => "portfolio", "name" => __('Portfolio Posts', 'virtue')), 
        'featured-products' => array( "slug" => "featured-products", "name" => __('Featured Products', 'virtue')), 
        'sale-products' => array( "slug" => "sale-products", "name" => __('Sale Products', 'virtue')), 
        'best-products' => array( "slug" => "best-products", "name" => __('Best Products', 'virtue')),
        'cat-products' => array( "slug" => "cat-products", "name" => __('Category of Products', 'virtue')),
        );
    
    $carousel_types = apply_filters('kadence_widget_carousel_types', $carousel_types);
    $carousel_columns_options = array(array("slug" => "1", "name" => __('1 Column', 'virtue')), array("slug" => "2", "name" => __('2 Columns', 'virtue')), array("slug" => "3", "name" => __('3 Columns', 'virtue')), array("slug" => "4", "name" => __('4 Columns', 'virtue')), array("slug" => "5", "name" => __('5 Columns', 'virtue')));
    $carousel_scroll_options = array(array("slug" => "1", "name" => __('1 item', 'virtue')), array("slug" => "all", "name" => __('All Visible', 'virtue')));
    $carousel_autoplay = array(array("slug" => "true", "name" => __('True', 'virtue')), array("slug" => "false", "name" => __('False', 'virtue')));
    $carousel_order_options = array(array("slug" => "menu_order", "name" => __('Menu Order', 'virtue')), array("slug" => "date", "name" => __('Date', 'virtue')), array("slug" => "rand", "name" => __('Random', 'virtue')));

    if (isset($instance['postcat'])) { $postcat = esc_attr($instance['postcat']); } else {$postcat = '';}
    if (isset($instance['portfoliocat'])) { $portfoliocat = esc_attr($instance['portfoliocat']); } else {$portfoliocat = '';}
    if (isset($instance['productcat'])) { $productcat = esc_attr($instance['productcat']); } else {$productcat = '';}

     $types= get_terms('portfolio-type');
     $type_options = array();
    $type_options[] = '<option value="">All</option>';
    if(!is_wp_error($types) ) {
        foreach ($types as $type) {
          if ($portfoliocat==$type->slug) { $selected=' selected="selected"';} else { $selected=""; }
          $type_options[] = '<option value="' . $type->slug .'"' . $selected . '>' . $type->name . '</option>';
        }
    }
     $categories= get_categories();
    $cat_options = array();
    $cat_options[] = '<option value="">All</option>';
    foreach ($categories as $cat) {
      if ($postcat==$cat->slug) { $selected=' selected="selected"';} else { $selected=""; }
      $cat_options[] = '<option value="' . $cat->slug .'"' . $selected . '>' . $cat->name . '</option>';
    }

    $product_options = array();
    $product_options[] = '<option value="">All</option>';
    if (class_exists('woocommerce')) { 
        $product_categories= get_terms('product_cat');
        foreach ($product_categories as $pcat) {
          if ($productcat==$pcat->slug) { $selected=' selected="selected"';} else { $selected=""; }
          $product_options[] = '<option value="' . $pcat->slug .'"' . $selected . '>' . $pcat->name . '</option>';
        }
    }
    $autoplay_options = array();
    foreach ($carousel_autoplay as $auto) {
        if ($autoplay == $auto['slug']) { $selected=' selected="selected"';} else { $selected=""; }
            $autoplay_options[] = '<option value="' . $auto['slug'] .'"' . $selected . '>' . $auto['name'] . '</option>';
        }


    foreach ($carousel_types as $carousel_type) {
      if ($c_type == $carousel_type['slug']) { $selected=' selected="selected"';} else { $selected=""; }
      $carousel_type_array[] = '<option value="' . $carousel_type['slug'] .'"' . $selected . '>' . $carousel_type['name'] . '</option>';
    }
    foreach ($carousel_scroll_options as $carousel_scroll_option) {
      if ($c_scroll == $carousel_scroll_option['slug']) { $selected=' selected="selected"';} else { $selected=""; }
      $carousel_scroll_array[] = '<option value="' . $carousel_scroll_option['slug'] .'"' . $selected . '>' . $carousel_scroll_option['name'] . '</option>';
    }
    foreach ($carousel_columns_options as $carousel_column_option) {
      if ($c_columns == $carousel_column_option['slug']) { $selected=' selected="selected"';} else { $selected=""; }
      $carousel_columns_array[] = '<option value="' . $carousel_column_option['slug'] .'"' . $selected . '>' . $carousel_column_option['name'] . '</option>';
    }
    foreach ($carousel_order_options as $carousel_order_option) {
      if ($c_order == $carousel_order_option['slug']) { $selected=' selected="selected"';} else { $selected=""; }
      $carousel_order_array[] = '<option value="' . $carousel_order_option['slug'] .'"' . $selected . '>' . $carousel_order_option['name'] . '</option>';
    }?>  

    <div id="virtue_carousel_widget<?php echo esc_attr($this->get_field_id('container')); ?>" class="kad_widget_carousel">
          <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'virtue'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('type'); ?>"><?php _e('Carousel Type', 'virtue'); ?></label><br />
                <select id="<?php echo $this->get_field_id('type'); ?>" style="width:100%; max-width:230px" name="<?php echo $this->get_field_name('type'); ?>"><?php echo implode('', $carousel_type_array);?></select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('c_columns'); ?>"><?php _e('Carousel Columns', 'virtue'); ?></label><br />
                <select id="<?php echo $this->get_field_id('c_columns'); ?>" style="width:100%; max-width:230px;" name="<?php echo $this->get_field_name('c_columns'); ?>"><?php echo implode('', $carousel_columns_array);?></select>
            </p>
             <p>
                <label for="<?php echo $this->get_field_id('c_scroll'); ?>"><?php _e('Scroll Setting', 'virtue'); ?></label><br />
                <select id="<?php echo $this->get_field_id('c_scroll'); ?>" style="width:100%; max-width:230px;" name="<?php echo $this->get_field_name('c_scroll'); ?>"><?php echo implode('', $carousel_scroll_array);?></select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('c_items'); ?>"><?php _e('Items (e.g. = 8)', 'virtue'); ?></label><br />
                <input type="text" class="widefat kad_img_widget_link" name="<?php echo $this->get_field_name('c_items'); ?>" id="<?php echo $this->get_field_id('c_items'); ?>" value="<?php echo $c_items; ?>">
            </p>
             <p>
                <label for="<?php echo $this->get_field_id('c_order'); ?>"><?php _e('Order by', 'virtue'); ?></label><br />
                <select id="<?php echo $this->get_field_id('c_order'); ?>" style="width:100%; max-width:230px;" name="<?php echo $this->get_field_name('c_order'); ?>"><?php echo implode('', $carousel_order_array);?></select>
            </p>
             <p>
                <label for="<?php echo $this->get_field_id('postcat'); ?>"><?php _e('Blog Post Category', 'virtue'); ?></label><br />
                <select id="<?php echo $this->get_field_id('postcat'); ?>" style="width:100%; max-width:230px;" name="<?php echo $this->get_field_name('postcat'); ?>"><?php echo implode('', $cat_options);?></select>
            </p>
             <p>
                <label for="<?php echo $this->get_field_id('portfoliocat'); ?>"><?php _e('Portfolio Category', 'virtue'); ?></label><br />
                <select id="<?php echo $this->get_field_id('portfoliocat'); ?>" style="width:100%; max-width:230px;" name="<?php echo $this->get_field_name('portfoliocat'); ?>"><?php echo implode('', $type_options);?></select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('productcat'); ?>"><?php _e('Product Category', 'virtue'); ?></label><br />
                <select id="<?php echo $this->get_field_id('productcat'); ?>" style="width:100%; max-width:230px;" name="<?php echo $this->get_field_name('productcat'); ?>"><?php echo implode('', $product_options);?></select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('c_speed'); ?>"><?php _e('Carousel Speed (e.g. = 7000)', 'virtue'); ?></label><br />
                <input type="text" class="widefat kad_img_widget_link" name="<?php echo $this->get_field_name('c_speed'); ?>" id="<?php echo $this->get_field_id('c_speed'); ?>" value="<?php echo $c_speed; ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('autoplay'); ?>"><?php _e('Auto Play?', 'virtue'); ?></label><br />
                <select id="<?php echo $this->get_field_id('autoplay'); ?>" style="width:100%; max-width:230px;" name="<?php echo $this->get_field_name('autoplay'); ?>"><?php echo implode('', $autoplay_options);?></select>
            </p>
    </div>

<?php } }