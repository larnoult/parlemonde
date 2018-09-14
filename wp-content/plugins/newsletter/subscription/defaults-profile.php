<?php

// This file is used only on first installation!

$options = array();
$options['email'] = 'Email';
$options['email_error'] = __('Email address is not correct', 'newsletter');
$options['name'] = __('First name or full name', 'newsletter');
$options['name_error'] = __('Name is required', 'newsletter');
$options['name_status'] = 0;
$options['name_rules'] = 0;
$options['surname'] = __('Last name', 'newsletter');
$options['surname_error'] = __('Last name is required', 'newsletter');
$options['surname_status'] = 0;
$options['sex_status'] = 0;
$options['sex'] = __('I\'m', 'newsletter');

$options['privacy'] = __('By continuing, you accept the privacy policy', 'newsletter');
$options['privacy_error'] = 'You must accept the privacy policy';
$options['privacy_status'] = 0;
$options['privacy_url'] = '';
$options['privacy_use_wp_url'] = 0;

$options['subscribe'] = __('Subscribe', 'newsletter');

$options['title_female'] = __('Ms.', 'newsletter');
$options['title_male'] = __('Mr.', 'newsletter');
$options['title_none'] = __('Dear', 'newsletter');

$options['sex_male'] = 'Man';
$options['sex_female'] = 'Woman';
$options['sex_none'] = 'Not specified';

for ($i=1; $i<=NEWSLETTER_PROFILE_MAX; $i++) {
    $options['profile_' . $i . '_status'] = 0;
    $options['profile_' . $i] = '';
    $options['profile_' . $i . '_type'] = 'text';
    $options['profile_' . $i . '_placeholder'] = '';
    $options['profile_' . $i . '_rules'] = 0;
    $options['profile_' . $i . '_options'] = '';
}

