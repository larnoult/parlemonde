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
<?php if ( !is_user_logged_in() ) { ?> <div id="admin-like"> Bienvenue sur le site de l'association <span style="font-family:'littledays'; font-size:16px">Par Le Monde</span> ! <?php do_action('icl_language_selector'); ?> <span id="button-like"> <a href="http://www.parlemonde.org/wp-login.php">Connexion</a> </span> <a href="http://www.parlemonde.fr/" style="color: #fff;" target="_blank"><span id="PLM_fr">Site de l'aventure 2014-2015 : J'y vais ! </span> </a></div> <?php ;} ?> 
  <?php wp_head(); ?>
</head>
