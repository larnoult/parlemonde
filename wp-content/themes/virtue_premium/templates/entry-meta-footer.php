<?php 
global $virtue_premium;
echo '<meta itemprop="dateModified" content="'.esc_attr(get_the_modified_date('c')).'">';
echo '<meta itemscope itemprop="mainEntityOfPage" content="'.esc_url(get_the_permalink()).'" itemType="https://schema.org/WebPage" itemid="'.esc_url(get_the_permalink()).'">';
echo '<div itemprop="publisher" itemscope itemtype="https://schema.org/Organization">';
    if (!empty($virtue_premium['x1_virtue_logo_upload']['url'])) {  
    echo '<div itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">';
    echo '<meta itemprop="url" content="'.esc_attr($virtue_premium['x1_virtue_logo_upload']['url']).'">';
    echo '<meta itemprop="width" content="'.esc_attr($virtue_premium['x1_virtue_logo_upload']['width']).'">';
    echo '<meta itemprop="height" content="'.esc_attr($virtue_premium['x1_virtue_logo_upload']['height']).'">';
    echo '</div>';
    }
    echo '<meta itemprop="name" content="'.esc_attr(get_bloginfo('name')).'">';
echo '</div>';