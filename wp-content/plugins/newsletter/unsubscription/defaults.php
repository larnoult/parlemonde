<?php
// Those default options are used ONLY on FIRST setup and on plugin updates but limited to
// new options that may have been added between your and new version.
//
// This is the main language file, too, which is always loaded by Newsletter. Other language
// files are loaded according the WPLANG constant defined in wp-config.php file. Those language
// specific files are "merged" with this one and the language specific configuration
// keys override the ones in this file.
//
// Language specific files only need to override configurations containing texts
// langiage dependant.

$options = array();

$options['unsubscribe_text'] = '<p>' . __('Please confirm you want to unsubscribe <a href="{unsubscription_confirm_url}">clicking here</a>.', 'newsletter') . '</p>';
$options['error_text'] = '<p>' . __("Subscriber not found, it probably has already been removed. No further actions are required.", 'newsletter') . '</p>';

// When you finally loosed your subscriber
$options['unsubscribed_text'] = "<p>" . __('Your subscription has been deleted. If that was an error you can <a href="{reactivate_url}">subscribe again here</a>.', 'newsletter') . "</p>";

$options['unsubscribed_subject'] = __("Goodbye", 'newsletter');

$options['unsubscribed_message'] = '<p>' . __('This message confirms that you have unsubscribed from our newsletter. Thank you.') . '</p>';

$options['reactivated_text'] = '<p>' . __('Your subscription has been reactivated.') . '</p>';