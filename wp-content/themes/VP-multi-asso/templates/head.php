<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <?php global $virtue; ?>
  <title><?php wp_title( '|', true, 'right' ); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php if (!empty($virtue['virtue_custom_favicon']['url'])) {?>
  	<link rel="shortcut icon" type="image/x-icon" href="<?php echo $virtue['virtue_custom_favicon']['url']; ?>" />
  	<?php } ?>
  <?php wp_head(); ?>
  <?php /*if ( !is_user_logged_in() ) {
	get_template_part('templates/header', 'topbar');
	} */?>
</head>
