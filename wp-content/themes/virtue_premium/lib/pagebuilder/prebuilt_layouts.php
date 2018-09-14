<?php 

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function virtue_prebuilt_page_layouts($layouts){
    $layouts['parallax-page'] = array (
        'name' => __('Parallax Example', 'virtue'),
        'screenshot' => 'https://s3.amazonaws.com/ktdemocontent/layouts/kt_parallax_screenshot-min.jpg',
        'description' => 'A parallax page example,  Great for the landing page template.',
        'widgets' =>
        array(
            0 =>
              array(
                'text' => '<h1 style="color:#fff; font-size:70px; line-height:80px; text-align:center;">Parallax Backgrounds</h1>
    <p style="color:#fff; text-align:center; max-width:600px; margin:0 auto;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse et leo nec justo accumsan ullamcorper a ut felis. Nunc auctor tristique enim, ut mattis mi ultricies vitae. Duis hendrerit dictum urna vitae molestie. Sed elementum enim tempus, interdum urna non, placerat massa. Maecenas ullamcorper pellentesque ornare. Nunc luctus tincidunt enim, quis molestie erat varius sit amet. Praesent nec pulvinar nibh.</p>',
                'info' =>
                array(
                  'class' => 'WP_Widget_Text',
                  'id' => '1',
                  'grid' => '0',
                  'cell' => '0',
                ),
              ),
            1 => array(
                'text' => '<div style="text-align:center">[btn text="View more" border="4px" borderradius="6px" link="#" tcolor="#ffffff" bcolor="transparent" bordercolor="#ffffff" thovercolor="#000000" bhovercolor="#ffffff" borderhovercolor="#ffffff" size="large" font="h1-family" icon="icon-arrow-right"]</div>',
                'info' => array(
                        'class' => 'WP_Widget_Text',
                        'id' => '2',
                        'grid' => '0',
                        'cell' => '0',
                    ),
            ),
            3 => array(
                    'title' => __('Evening Sky Photo Series', 'virtue'),
                    'image_url' => 'https://s3.amazonaws.com/ktdemocontent/layouts/kt_split_content_01-min.jpg',
                    'img_align' => 'left',
                    'height' => '400',
                    'description' => 'Nullam luctus urna ac ultrices tristique. Aliquam id dolor in turpis dictum posuere at ac mauris. Pellentesque posuere eget nisi eu vestibulum. Maecenas ex leo, viverra at iaculis quis, mollis sit amet est. Phasellus efficitur, urna non bibendum venenatis, tortor nunc sodales nulla, id scelerisque justo metus in risus. Quisque finibus elit eu posuere ornare. Pellentesque posuere eget nisi eu vestibulum. Maecenas ex leo, viverra at iaculis quis, mollis sit amet est. Aliquam id dolor in turpis dictum posuere at ac mauris.

    [btn text="View Gallery" border="2px" borderradius="4px" link="#" tcolor="#333333" bcolor="transparent" bordercolor="#333333" thovercolor="#ffffff" bhovercolor="#333333" borderhovercolor="#333333"]',
                    'filter' => '1',
                    'info' =>  array(
                                'class' => 'kad_split_content_widget',
                                'id' => '3',
                                'grid' => '1',
                                'cell' => '0',
                            ),
            ),
            4 => array(
                    'title' => __('Night Sky Photo Series', 'virtue'),
                    'image_url' => 'https://s3.amazonaws.com/ktdemocontent/layouts/kt_split_content_02-min.jpg',
                    'img_align' => 'right',
                    'height' => '400',
                    'description' => 'Nullam luctus urna ac ultrices tristique. Aliquam id dolor in turpis dictum posuere at ac mauris. Pellentesque posuere eget nisi eu vestibulum. Maecenas ex leo, viverra at iaculis quis, mollis sit amet est. Phasellus efficitur, urna non bibendum venenatis, tortor nunc sodales nulla, id scelerisque justo metus in risus. Quisque finibus elit eu posuere ornare. Pellentesque posuere eget nisi eu vestibulum. Maecenas ex leo, viverra at iaculis quis, mollis sit amet est. Aliquam id dolor in turpis dictum posuere at ac mauris.

    [btn text="View Gallery" border="2px" borderradius="4px" link="#" tcolor="#333333" bcolor="transparent" bordercolor="#333333" thovercolor="#ffffff" bhovercolor="#333333" borderhovercolor="#333333"]',
                    'filter' => '1',
                    'info' =>  array(
                            'class' => 'kad_split_content_widget',
                            'id' => '4',
                            'grid' => '2',
                            'cell' => '0',
                        ),
            ),
            5 => array(
                'title' => 'Easy to Customize',
                'description' => "Vestibulum pharetra pellentesque elit. Donec massa magna, semper nec tincidunt eu, condimentum non arcu. In hac habitasse platea dictumst. Integer ut risus imperdiet, hendrerit nunc nec, viverra velit. Duis ullamcorper sit amet diam in hendrerit. Nunc laoreet tincidunt consequat. Fusce vel odio ut magna vestibulum volutpat luctus a ante. Donec tincidunt ultrices sollicitudin. Phasellus scelerisque congue suscipit.",
                'info_icon' => 'icon-pencil2',
                'image_uri' => '',
                'size' => '20',
                'style' => 'kad-circle-iconclass',
                'color' => '#ffffff',
                'iconbackground' => '#444444',
                'background' => '',
                'info' => array(
                            'class' => 'kad_infobox_widget',
                            'id' => '5',
                            'grid' => '3',
                            'cell' => '0',
                        ),
            ),
            6 => array(
                'title' => 'Beautiful Layouts',
                'description' => "Vestibulum pharetra pellentesque elit. Donec massa magna, semper nec tincidunt eu, condimentum non arcu. In hac habitasse platea dictumst. Integer ut risus imperdiet, hendrerit nunc nec, viverra velit. Duis ullamcorper sit amet diam in hendrerit. Nunc laoreet tincidunt consequat. Fusce vel odio ut magna vestibulum volutpat luctus a ante. Donec tincidunt ultrices sollicitudin. Phasellus scelerisque congue suscipit.",
                'info_icon' => 'icon-laptop',
                'image_uri' => '',
                'size' => '20',
                'style' => 'kad-circle-iconclass',
                'color' => '#ffffff',
                'iconbackground' => '#444444',
                'background' => '',
                'info' => array(
                      'class' => 'kad_infobox_widget',
                      'id' => '6',
                      'grid' => '3',
                      'cell' => '1',
                    ),
            ),
            7 => array(
                'title' => 'Tons of Extras',
                'description' => "Vestibulum pharetra pellentesque elit. Donec massa magna, semper nec tincidunt eu, condimentum non arcu. In hac habitasse platea dictumst. Integer ut risus imperdiet, hendrerit nunc nec, viverra velit. Duis ullamcorper sit amet diam in hendrerit. Nunc laoreet tincidunt consequat. Fusce vel odio ut magna vestibulum volutpat luctus a ante. Donec tincidunt ultrices sollicitudin. Phasellus scelerisque congue suscipit.",
                'info_icon' => 'icon-basket',
                'image_uri' => '',
                'size' => '20',
                'style' => 'kad-circle-iconclass',
                'color' => '#ffffff',
                'iconbackground' => '#444444',
                'background' => '',
                'info' => array(
                        'class' => 'kad_infobox_widget',
                        'id' => '7',
                        'grid' => '3',
                        'cell' => '2',
                    ),
            ),
        ),
        'grids' => array(
            0 => array(
                'cells' => '1',
                'style' => array(
                    'row_stretch'               => 'full',
                    'background_image_url'      => 'https://s3.amazonaws.com/ktdemocontent/layouts/kt_parallax_scroll_02-min.jpg',
                    'background_image_style'    => 'parallax',
                    'padding_top'               => '180px',
                    'padding_bottom'            => '180px',
                    'bottom_margin'             => '0px',
                ),
            ),
            1 => array(
                'cells' => '1',
                'style' => array(
                    'padding_top'     => '80px',
                ),
            ),
            2 => array(
                'cells' => '1',
            ),
            3 => array(
                'cells' => '3',
                'style' => array(
                    'padding_top' => '30px', 
                    'padding_bottom' => '0px',
                ),
            ),
        ),
        'grid_cells' => array(
            0 => array(
                'weight' => '1',
                'grid' => '0',
            ),
            1 => array(
                'weight' => '1',
                'grid' => '1',
            ),
            2 => array(
                'weight' => '1',
                'grid' => '2',
            ),
            3 => array(
                'weight' => '0.3333333333333333',
                'grid' => '3',
            ),
            4 => array(
                'weight' => '0.3333333333333333',
                'grid' => '3',
            ),
            5 => array(
                'weight' => '0.3333333333333333',
                'grid' => '3',
            ),
        ),
    );
$layouts['example-icon-boxes'] = array (
    'name' => __('Icon Boxes Example', 'virtue'),
    'screenshot' => 'https://s3.amazonaws.com/ktdemocontent/layouts/kt_icon_screenshot.jpg',
        'description' => 'A quick way to get started with different icon boxes.',
    'widgets' =>
    array(
      0 =>
      array(
        'text' => '[iconbox icon="icon-mobile" iconsize="48px" link="#" color="#444444" background="trasparent" hcolor="#ffffff" hbackground="#00aeff"]<h4>Responsive</h4><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse sapien tortor, feugiat non odio quis, volutpat pretium odio. </p>[/iconbox]',
        'info' =>
        array(
          'class' => 'WP_Widget_Text',
          'id' => '1',
          'grid' => '0',
          'cell' => '0',
        ),
      ),
      1 =>
       array(
        'text' => '[iconbox icon="icon-equalizer2" iconsize="48px" link="#" color="#444444" background="trasparent" hcolor="#ffffff" hbackground="#da4b54"]<h4>Tons of Options</h4><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse sapien tortor, feugiat non odio quis, volutpat pretium odio. </p>[/iconbox]',
        'info' =>
        array(
          'class' => 'WP_Widget_Text',
          'id' => '2',
          'grid' => '0',
          'cell' => '1',
        ),
      ),
      2 =>
       array(
        'text' => '[iconbox icon="icon-pencil" iconsize="48px" link="#" color="#444444" background="trasparent" hcolor="#ffffff" hbackground="#F76A0C"]<h4>Clean Design</h4><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse sapien tortor, feugiat non odio quis, volutpat pretium odio. </p>[/iconbox]',
        'info' =>
        array(
          'class' => 'WP_Widget_Text',
          'id' => '3',
          'grid' => '0',
          'cell' => '2',
        ),
      ),
      3 =>
       array(
        'text' => '[iconbox icon="icon-basket" iconsize="48px" link="#" color="#444444" background="trasparent" hcolor="#ffffff" hbackground="#3E6617"]<h4>eCommerce</h4><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse sapien tortor, feugiat non odio quis, volutpat pretium odio. </p>[/iconbox]',
        'info' =>
        array(
          'class' => 'WP_Widget_Text',
          'id' => '4',
          'grid' => '0',
          'cell' => '3',
        ),
      ),
        4 => array(
            'title' => 'Hover Over This',
            'description' => 'Watch as the box flips and reveals new content.',
            'icon' => 'icon-lab',
            'iconcolor' => '#444444',
            'titlecolor' => '#444444',
            'fcolor' => '#444444',
            'titlesize' => '24',
            'iconsize' => '48',
            'height' => '230',
            'flip_content' => 'This backside can have unique content or the same depending on what you are wanting.',
            'fbtn_text' => 'Custom Button',
            'fbtn_link' => '#',
            'fbtn_color' => '#ffffff',
            'fbtn_background' => 'trasparent',
            'fbtn_border' => '2px solid #ffffff',
            'fbtn_border_radius' => '0',
            'background' => '#f2f2f2',
            'bcolor' => '#ffffff',
            'bbackground' => '#444444',
            'info' => array(
                'class' => 'kad_icon_flip_box_widget',
                'id' => '5',
                'grid' => '1',
                'cell' => '0',
            ),
        ),
        5 => array(
            'title' => 'Customize the colors',
            'description' => 'Watch as the box flips and reveals new content.',
            'icon' => 'icon-palette',
            'iconcolor' => '#ffffff',
            'titlecolor' => '#ffffff',
            'fcolor' => '#ffffff',
            'titlesize' => '24',
            'iconsize' => '48',
            'height' => '230',
            'flip_content' => 'This backside can have unique content or the same depending on what you are wanting.',
            'fbtn_text' => 'Custom Button',
            'fbtn_link' => '#',
            'fbtn_color' => '#ffffff',
            'fbtn_background' => 'trasparent',
            'fbtn_border' => '2px solid #ffffff',
            'fbtn_border_radius' => '0',
            'background' => '#2d5c88',
            'bcolor' => '#ffffff',
            'bbackground' => '#f3690e',
            'info' => array(
                'class' => 'kad_icon_flip_box_widget',
                'id' => '6',
                'grid' => '1',
                'cell' => '1',
            ),
        ),
        6 => array(
            'title' => 'Create Something Unique',
            'description' => 'Watch as the box flips and reveals new content.',
            'icon' => 'icon-rocket',
            'iconcolor' => '#ffffff',
            'titlecolor' => '#ffffff',
            'fcolor' => '#ffffff',
            'titlesize' => '24',
            'iconsize' => '48',
            'height' => '230',
            'flip_content' => 'This backside can have unique content or the same depending on what you are wanting.',
            'fbtn_text' => 'Custom Button',
            'fbtn_link' => '#',
            'fbtn_color' => '#ffffff',
            'fbtn_background' => '#444444',
            'fbtn_border' => '2px solid #444444',
            'fbtn_border_radius' => '0',
            'background' => '#444444',
            'bcolor' => '#444444',
            'bbackground' => '#f2f2f2',
            'info' => array(
                'class' => 'kad_icon_flip_box_widget',
                'id' => '7',
                'grid' => '1',
                'cell' => '2',
            ),
        ),
    ),
    'grids' =>
    array(
      0 =>
      array(
        'cells' => '4',
        'style' => '',
      ),
      1 =>
      array(
        'cells' => '3',
        'style' => '',
      ),
    ),
    'grid_cells' =>
    array(
      0 =>
      array(
        'weight' => '0.25',
        'grid' => '0',
      ),
      1 =>
      array(
        'weight' => '0.25',
        'grid' => '0',
      ),
      2 =>
      array(
        'weight' => '0.25',
        'grid' => '0',
      ),
      3 =>
      array(
        'weight' => '0.25',
        'grid' => '0',
      ),
      4 =>
      array(
        'weight' => '0.3333333333333333',
        'grid' => '1',
      ),
      5 =>
      array(
        'weight' => '0.3333333333333333',
        'grid' => '1',
      ),
      6 =>
      array(
        'weight' => '0.3333333333333333',
        'grid' => '1',
      ),
    ),
  );

  return $layouts;
}
add_filter('siteorigin_panels_prebuilt_layouts', 'virtue_prebuilt_page_layouts');
