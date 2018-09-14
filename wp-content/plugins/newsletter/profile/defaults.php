<?php

$options = array();

// Profile page
$options['text'] = '{profile_form}' . __('
    <p>If you change your email address, a confirmation email will be sent to activate it.</p>
    <p><a href="{unsubscription_confirm_url}">Cancel your subscription</a></p>', 'newsletter');

// Profile page messages
$options['email_changed'] = __("Your email has been changed, an activation email has been sent with instructions.", 'newsletter');
$options['error'] = __("Your email is not valid or already in use.", 'newsletter');
$options['save_label'] = __('Save', 'newsletter');
$options['privacy_label'] = __('Read our privacy note', 'newsletter');
$options['saved'] = __('Profile saved.', 'newsletter');
$options['export_newsletters'] = 0;
$options['url'] = '';

