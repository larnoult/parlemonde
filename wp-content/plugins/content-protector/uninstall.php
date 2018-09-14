<?php

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit();

if ( !defined( 'CONTENT_PROTECTOR_HANDLE' ) )
    define( "CONTENT_PROTECTOR_HANDLE", "content_protector" );

$default_options = array( 'form_instructions',
    'form_instructions_font_size',
    'form_instructions_font_weight',
    'form_instructions_color',
    'error_message',
    'error_message_font_size',
    'error_message_font_weight',
    'error_message_color',
    'form_submit_label',
    'form_submit_label_color',
    'form_submit_button_color',
    'border_style',
    'border_color',
    'border_radius',
    'border_width',
    'padding',
    'background_color',
    'form_css',
    'encryption_algorithm',
    'content_filters',
    'other_content_filters',
    'share_auth',
    'share_auth_duration',
    'store_encrypted_passwords',
    'delete_options_on_uninstall',
    'password_field_type',
    'password_field_placeholder',
    'password_field_length');

$prefix = CONTENT_PROTECTOR_HANDLE . '_';
if ( "1" === get_option( $prefix . 'delete_options_on_uninstall' ) ) {
    foreach ( $default_options as $option ) {
        delete_option( $prefix . $option );
    }
}
?>