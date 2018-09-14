<?php

final class KBJ_UserShortcodesPlus_Shortcodes_UserAvatar
{
    public function __construct()
    {
        add_shortcode( 'user_avatar',     array( $this, 'user_avatar'     ) );
        add_shortcode( 'user_avatar_url', array( $this, 'user_avatar_url' ) );
    }

    public function user_avatar( $atts )
    {
        $atts = shortcode_atts( array(
            'id' => get_current_user_id(),
        ), $atts );

        $userdata = get_userdata( $atts[ 'id' ] );

        return get_avatar( $userdata->user_email );
    }

    public function user_avatar_url( $atts )
    {
        $atts = shortcode_atts( array(
            'id' => get_current_user_id(),
            'size' => 500
        ), $atts );

        $userdata = get_userdata( $atts[ 'id' ] );

        return get_avatar_url( $userdata->user_email, array( 'size' => $atts[ 'size' ] ) );
    }

}
