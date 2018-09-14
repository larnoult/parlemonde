<?php

final class KBJ_UserShortcodesPlus_Admin_TinyMCE_AddShortcodeButton
{
    function __construct()
    {
        // Add a tinyMCE button to our post and page editor
        add_filter( 'media_buttons_context', array( $this, 'insert_button' ) );
    }

    public function insert_button( $context )
    {
        global $pagenow;
        if( ! in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ){
            return $context;
        }

        add_thickbox();

        $data = array(
            'button_text' => __( 'Add User Shortcode', '' ),
            'users' => get_users(),
            'shortcodes' => KBJ_UserShortcodesPlus::config( 'Shortcodes' ),
        );

        return KBJ_UserShortcodesPlus::template( 'admin-tinymce-modal.html.php', $data );
    }

}