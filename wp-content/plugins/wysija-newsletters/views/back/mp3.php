<?php
defined('WYSIJA') or die('Restricted access');

class WYSIJA_view_back_mp3 extends WYSIJA_view_back{
    function __construct(){
        $this->skip_header =true;

    }

    function defaultDisplay($data){
        $model_config = WYSIJA::get('config','model');
        $time_install = $model_config->getValue('installed_time');

        $this->displayMP3();
    }

    function displayMP3() {
		?>

        <div class="wrap about-wrap mpoet-page">
<div class="changelog removeme">
	<h2 style="font-size: 25px; color: #626262; font-weight: 600;"><?php echo __("We're changing. So should you.", WYSIJA ); ?></h2>

<iframe src="https://player.vimeo.com/video/223581490" width="640" height="360" frameborder="0" webkitallowfullscreen="" mozallowfullscreen="" allowfullscreen=""></iframe>

<div class="feature-section">
<ul style="list-style: disc inside none">

<li><?php echo $this->replace_link_shortcode(__("[link]Read the FAQ[/link] on what's going to happen to this current version of MailPoet (version 2)", WYSIJA), 'http://www.mailpoet.com/faq-mailpoet-version-2/') ?></li>
<li><?php echo __('MailPoet version 3 is completely rewritten', WYSIJA); ?></li>
<li><?php echo __('New email designer', WYSIJA); ?></li>
<li><strong><?php echo __('Align images left or right of post excerpts, like in version 2 (new)', WYSIJA); ?></strong></li>
<li><?php echo __('Responsive templates', WYSIJA); ?></li>
<li><?php echo __('Fast user interface', WYSIJA); ?></li>
<li><?php echo __('Same easy configuration', WYSIJA); ?></li>
<li><?php echo __('Weekly releases', WYSIJA); ?></li>
<li><?php echo __('Version 2 and 3 can live side by side', WYSIJA); ?></li>
<li><a href="http://beta.docs.mailpoet.com/article/189-comparison-of-mailpoet-2-and-3?utm_source=mp2&amp;utm_medium=welcomeupdate&amp;utm_campaign=comparison"><?php echo __('Comparison table of both versions', WYSIJA); ?></a></li>
<li><?php echo $this->replace_link_shortcode(__('Try [link]the online demo[/link]', WYSIJA), 'http://demo3.mailpoet.com/launch/?utm_source=mp2&amp;utm_medium=updatewelcome&amp;utm_campaign=demo3'); ?></li>
<li><?php echo __('Multisite works, but not officially supported. Please test MailPoet 3 on a staging server', WYSIJA); ?></li>
<li><?php echo __('Right-to-left languages works, but can be improved', WYSIJA); ?></li>
<li><?php echo $this->replace_link_shortcode(
  $this->replace_link_shortcode(
    __('Get in touch in the [link]forums[/link] for further help. Customers can reach via our [link]support page[/link]', WYSIJA),
    'https://wordpress.org/support/plugin/wysija-newsletters'
  ),
  'https://www.mailpoet.com/support/'
); ?></li>
</ul>

<br>
<h3 style="font-size: 25px; color: #626262; font-weight: 600;"><strong><?php echo __('Comes with a 1-click migration tool:', WYSIJA); ?></strong></h3>
<ul style="list-style: disc inside none">
  <li><?php echo __('Your subscribers, lists, forms and settings will be migrated', WYSIJA); ?></li>
  <li><?php echo __('Automatic emails will not be migrated', WYSIJA); ?></li>
  <li><?php echo __('Archive of sent emails will not be migrated', WYSIJA); ?></li>
  <li><?php echo __('Your statistics will not be migrated', WYSIJA); ?></li>
</ul>
<a class="button-primary" href="plugin-install.php?s=mailpoet&tab=search&type=author"><?php echo __('Download MailPoet 3 now', WYSIJA); ?></a>
</div>
</div>
<?php
    }

    private function replace_link_shortcode($text, $url) {
      $count = 1;
      return preg_replace(
        '/\[\/link\]/',
        '</a>',
        preg_replace(
          '/\[link\]/',
          sprintf('<a href="%s">', $url),
          $text,
          $count
        ),
        $count
      );
    }
}
