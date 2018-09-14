<?php 
global $virtue;
echo '<meta itemscope itemprop="mainEntityOfPage" content="'.esc_url(get_the_permalink()).'" itemType="https://schema.org/WebPage" itemid="'.esc_url(get_the_permalink()).'">';
echo '<meta itemprop="dateModified" content="'.esc_attr(get_the_modified_date('c')).'">';
echo '<div itemprop="publisher" itemscope itemtype="https://schema.org/Organization">';
$site_icon_id = get_option( 'site_icon' );
    if (!empty($virtue['x1_virtue_logo_upload']['url'])) {  
	    echo '<div itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">';
	    echo '<meta itemprop="url" content="'.esc_attr($virtue['x1_virtue_logo_upload']['url']).'">';
	    echo '<meta itemprop="width" content="'.esc_attr($virtue['x1_virtue_logo_upload']['width']).'">';
	    echo '<meta itemprop="height" content="'.esc_attr($virtue['x1_virtue_logo_upload']['height']).'">';
	    echo '</div>';
    } else if(!empty( $site_icon_id ) ) {
    	$image = wp_get_attachment_image_src($site_icon_id, 'full');
    	  	echo '<div itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">';
		    echo '<meta itemprop="url" content="'.esc_attr($image[0]).'">';
		    echo '<meta itemprop="width" content="'.esc_attr($image[1]).'">';
		    echo '<meta itemprop="height" content="'.esc_attr($image[2]).'">';
		    echo '</div>';
    }
    echo '<meta itemprop="name" content="'.esc_attr(get_bloginfo('name')).'">';
echo '</div>';
