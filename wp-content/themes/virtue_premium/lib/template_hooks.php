<?php

// Footer

function kt_sitewide_shortcode_output() {
  global $virtue_premium;

  if(isset($virtue_premium['sitewide_footer_shortcode_input']) && !empty($virtue_premium['sitewide_footer_shortcode_input'])) {
    echo '<div class="clearfix kt_footer_sitewide_shortcode">';
    echo do_shortcode($virtue_premium['sitewide_footer_shortcode_input']);
    echo '</div>';
  }
}
add_action('kt_before_footer', 'kt_sitewide_shortcode_output', 10 );

function kt_sitewide_calltoaction_output() {
  global $virtue_premium;

  if(isset($virtue_premium['sitewide_calltoaction']) && $virtue_premium['sitewide_calltoaction'] == 1) { 
    get_template_part('templates/sitewide', 'calltoaction'); 
  }
}
add_action('kt_before_footer', 'kt_sitewide_calltoaction_output', 20 );