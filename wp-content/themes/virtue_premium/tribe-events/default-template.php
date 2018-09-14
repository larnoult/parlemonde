<?php
/**
 * Default Events Template
 * This file is the basic wrapper template for all the views if 'Default Events Template'
 * is selected in Events -> Settings -> Template -> Events Template.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/default-template.php
 *
 * @package TribeEventsCalendar
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

add_filter('kadence_display_sidebar', 'kt_tribe_sidebar');

function kt_tribe_sidebar($sidebar) {
  if (tribe_is_event_query()) {
    return false;
  }
  return $sidebar;
}
// wrapper handles this get_header();
?>
<!-- kt-event-code -->
<div id="content" class="container">
   	<div class="row">
     	<div class="main <?php echo virtue_main_class(); ?>" id="ktmain" role="main">
<!-- stop kt-event-code -->
<div id="tribe-events-pg-template">
	<?php tribe_events_before_html(); ?>
	<?php tribe_get_view(); ?>
	<?php tribe_events_after_html(); ?>
</div> <!-- #tribe-events-pg-template -->

<!-- kt-event-code -->
</div>
<!-- stop kt-event-code -->
<?php
// wrappery handles this get_footer();
