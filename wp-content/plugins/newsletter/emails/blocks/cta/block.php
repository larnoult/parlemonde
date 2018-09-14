<?php
/*
 * Name: Call To Action
 * Section: content
 * Description: Call to action button
 */

/* @var $options array */
/* @var $wpdb wpdb */

$default_options = array(
    'text' => 'Call to action',
    'background' => '#256F9C',
    'color' => '#ffffff',
    'url' => home_url(),
    'font_family' => $font_family,
    'font_size' => 16,
    'block_background'=>'#ffffff'
);

$options = array_merge($default_options, $options);
$options['block_padding_top'] = '15px';
$options['block_padding_bottom'] = '15px';
?>

<a href="<?php echo $options['url'] ?>" target="_blank" rel="noopener" style="line-height: normal; font-size: <?php echo $options['font_size'] ?>px; font-family: <?php echo $options['font_family'] ?>; font-weight: normal; color: <?php echo $options['color'] ?>; text-decoration: none; background-color: <?php echo $options['background'] ?>; border-top: 15px solid <?php echo $options['background'] ?>; border-bottom: 15px solid <?php echo $options['background'] ?>; border-left: 25px solid <?php echo $options['background'] ?>; border-right: 25px solid <?php echo $options['background'] ?>; border-radius: 3px; -webkit-border-radius: 3px; -moz-border-radius: 3px; display: inline-block;"><?php echo $options['text'] ?></a>

<div itemscope="" itemtype="http://schema.org/EmailMessage">
    <div itemprop="potentialAction" itemscope="" itemtype="http://schema.org/ViewAction">
        <meta itemprop="url" content="<?php echo esc_attr($options['url']) ?>" />
        <meta itemprop="name" content="<?php echo esc_attr($options['text']) ?>" />
    </div>
    <meta itemprop="description" content="<?php echo esc_attr($options['text']) ?>" />
</div>
