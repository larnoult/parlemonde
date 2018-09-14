<?php $icons = array(
    array('icon_o' => 'icon-pencil', 'link' => '', 'title' => 'Clean Design', 'url' => '', 'description' => ''),
    array('icon_o' => 'icon-mobile-phone', 'link' => '', 'title' => 'Responsive', 'url' => '', 'description' => ''),
    array('icon_o' => 'icon-cogs', 'link' => '', 'title' => 'Tons of Options', 'url' => '', 'description' => ''),
    array('icon_o' => 'icon-shopping-cart', 'link' => '', 'title' => 'Ecommerce', 'url' => '', 'description' => ''),
    ); 
    $itemsize = 'tcol-lg-3 tcol-md-3 tcol-sm-4 tcol-xs-6 tcol-ss-12';                   
    ?>
                <div class="home-margin home-padding">
                    <div class="rowtight homepromo">
                    <?php $counter = 1;?>
                        <?php foreach ($icons as $icon) : ?>
                            <div class="<?php echo esc_attr($itemsize);?> home-iconmenu <?php echo 'homeitemcount'.esc_attr($counter);?>">
                                <a href="<?php echo esc_url($icon['link']); ?>" title="<?php echo esc_attr($icon['title']); ?>">
                                <?php if(!empty($icon['url'])) {
                                    echo '<img src="'.esc_url($icon['url']).'"/>'; 
                                } else {
                                    echo '<i class="'.esc_attr($icon['icon_o']).'"></i>';
                                } 
                                if (!empty($icon['title'])) {
                                    echo '<h4>'.esc_html($icon['title']).'</h4>';
                                }
                                if (!empty($icon['description'])) {
                                    echo '<p>'.esc_html($icon['description']).'</p>';
                                } ?>
                                </a>
                            </div>
                             <?php $counter ++ ?>
                        <?php endforeach; ?>
                    </div> <!--homepromo -->
                </div>