<?php

final class KBJ_UserShortcodesPlus_Shortcodes_UserMeta
{
    private $user_meta = array();

    public function __construct()
    {
        add_shortcode( 'user_meta', array( $this, 'do_shortcode' ) );
    }

    public function do_shortcode( $atts )
    {
        $atts = shortcode_atts( array(
            'id' => get_current_user_id(),
            'key' => '',
        ), $atts );

        if( ! $atts[ 'key' ] ) return '';

        return $this->get_user_meta( $atts[ 'id' ], $atts[ 'key' ] );
    }

    public function get_user_meta( $user_id, $key )
    {
        if( ! isset( $this->user_meta[ $user_id ][ $key ] ) ){
            $this->user_meta[ $user_id ][ $key ] = get_user_meta( $user_id, $key, TRUE );
        }
        return $this->user_meta[ $user_id ][ $key ];
    }

}
