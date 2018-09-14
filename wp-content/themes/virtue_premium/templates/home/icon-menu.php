<?php 
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
    global $virtue_premium; 
    
    $icons = $virtue_premium['icon_menu']; 
    if(!empty($virtue_premium['home_icon_menu_column'])) {
        $columnsize = $virtue_premium['home_icon_menu_column'];
    } else {
        $columnsize = 3;
    }
    if ($columnsize == '2') {
        $itemsize = 'tcol-lg-6 tcol-md-6 tcol-sm-6 tcol-xs-12 tcol-ss-12'; 
    } else if ($columnsize == '3'){
        $itemsize = 'tcol-lg-4 tcol-md-4 tcol-sm-4 tcol-xs-6 tcol-ss-12';
    } else if ($columnsize == '6'){
        $itemsize = 'tcol-lg-2 tcol-md-2 tcol-sm-3 tcol-xs-4 tcol-ss-6';
    } else if ($columnsize == '5'){
        $itemsize = 'tcol-lg-25 tcol-md-25 tcol-sm-3 tcol-xs-4 tcol-ss-6';
    } else {
        $itemsize = 'tcol-lg-3 tcol-md-3 tcol-sm-6 tcol-xs-6 tcol-ss-12';
    }             
    ?>
                <div class="home-margin home-padding kt-home-icon-menu">
                	<div class="rowtight homepromo clearfix kt-home-iconmenu-container" data-equal-height="1">
                    <?php $counter = 1;?>
                        <?php foreach ($icons as $icon) : ?>
                        <?php if(!empty($icon['target']) && $icon['target'] == 1) {$target = '_blank';} else {$target = '_self';} ?>
                            <div class="<?php echo esc_attr($itemsize);?> home-iconmenu kad-animation <?php echo 'homeitemcount'.esc_attr($counter);?>" data-animation="fade-in" data-delay="<?php echo esc_attr($counter*150);?>">
                                <?php if(!empty($icon['link'])) {?> 
	                            <a href="<?php echo esc_url($icon['link']); ?>" target="<?php echo esc_attr($target); ?>"  title="<?php echo strip_tags(esc_attr($icon['title'])); ?>" class="home-icon-item">
                               <?php } else { ?>
                                <div class="home-icon-item">
                                <?php } ?>
	                            <?php if(!empty($icon['url'])) echo '<img src="'.esc_url($icon['url']).'" alt="'.esc_attr($icon['title']).'" />' ; else echo '<i class="'.esc_attr($icon['icon_o']).'"></i>'; ?>
	                            <?php if ($icon['title'] != '') echo '<h4>'.$icon['title'].'</h4>'; ?>
                                <?php if ($icon['description'] != '') echo '<p>'.$icon['description'].'</p>'; ?>
                                <?php if(!empty($icon['link'])) {?> 
	                            </a>
                                 <?php } else { ?>
                                </div>
                                <?php } ?>
                            </div>
                             <?php $counter ++; ?>
                        <?php endforeach; ?>

                    </div> <!--homepromo -->
                </div>