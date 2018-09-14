<?php 
/**
 * @version 1.0
 * @package Secure Downloads 
 * @subpackage Core Functions
 * @category Functions
 * 
 * @author wpdevelop
 * @link http://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 29.09.2015
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

////////////////////////////////////////////////////////////////////////////////
//  Internal plugin action hooks system      ///////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
global $opsd_action, $opsd_filter;


function add_opsd_filter($filter_type, $filter) {
    global $opsd_filter;

    $args = array();
    if ( is_array($filter) && 1 == count($filter) && is_object($filter[0]) ) // array(&$this)
        $args[] =& $filter[0];
    else
        $args[] = $filter;
    for ( $a = 2; $a < func_num_args(); $a++ )
        $args[] = func_get_arg($a);

    if ( is_array($opsd_filter) )

        if ( isset($opsd_filter[$filter_type]) ) {
            if ( is_array($opsd_filter[$filter_type]) )
                $opsd_filter[$filter_type][]= $args;
            else
                $opsd_filter[$filter_type]= array($args);
        } else
            $opsd_filter[$filter_type]= array($args);
    else
        $opsd_filter = array( $filter_type => array( $args ) ) ;
}

function remove_opsd_filter($filter_type, $filter) {
    global $opsd_filter;

    if ( isset($opsd_filter[$filter_type]) ) {
        for ($i = 0; $i < count($opsd_filter[$filter_type]); $i++) {
            if ( $opsd_filter[$filter_type][$i][0] == $filter ) {
                $opsd_filter[$filter_type][$i] = null;
                return;
            }
        }
    }
}

function apply_opsd_filter($filter_type) {
    global $opsd_filter;


    $args = array();
    for ( $a = 1; $a < func_num_args(); $a++ )
        $args[] = func_get_arg($a);

    if ( count($args) > 0 )
        $value = $args[0];
    else
        $value = false;

    if ( is_array($opsd_filter) )
        if ( isset($opsd_filter[$filter_type]) )
            foreach ($opsd_filter[$filter_type] as $filter) {
                $filter_func = array_shift($filter);
                $parameter = $args;
                $value =  call_user_func_array($filter_func,$parameter );
            }
    return $value;
}


function make_opsd_action($action_type) {
    global $opsd_action;


    $args = array();
    for ( $a = 1; $a < func_num_args(); $a++ )
        $args[] = func_get_arg($a);

    if ( is_array($opsd_action) )
        if ( isset($opsd_action[$action_type]) )
            foreach ($opsd_action[$action_type] as $action) {
                $action_func = array_shift($action);
                $parameter = $action;
                call_user_func_array($action_func,$args );
            }
}

function add_opsd_action($action_type, $action) {
    global $opsd_action;

    $args = array();
    if ( is_array($action) && 1 == count($action) && is_object($action[0]) ) // array(&$this)
        $args[] =& $action[0];
    else
        $args[] = $action;
    for ( $a = 2; $a < func_num_args(); $a++ )
        $args[] = func_get_arg($a);

    if ( is_array($opsd_action) )
        if ( isset($opsd_action[$action_type]) ) {
            if ( is_array($opsd_action[$action_type]) )
                $opsd_action[$action_type][]= $args;
            else
                $opsd_action[$action_type]= array($args);
        } else
                $opsd_action[$action_type]= array($args);

    else
        $opsd_action = array( $action_type => array( $args ) ) ;
}

function remove_opsd_action($action_type, $action) {
    global $opsd_action;

    if ( isset($opsd_action[$action_type]) ) {
        for ($i = 0; $i < count($opsd_action[$action_type]); $i++) {
            if ( $opsd_action[$action_type][$i][0] == $action ) {
                $opsd_action[$action_type][$i] = null;
                return;
            }
        }
    }
}


function get_opsd_option( $option, $default = false ) {

    $u_value = apply_opsd_filter('opsd_get_option', 'no-values'  , $option, $default );
    if ( $u_value !== 'no-values' ) return $u_value;

    return get_option( $option, $default  );
}

function update_opsd_option ( $option, $newvalue ) {

    $u_value = apply_opsd_filter('opsd_update_option', 'no-values'  , $option, $newvalue );
    if ( $u_value !== 'no-values' ) return $u_value;

    return update_option($option, $newvalue);
}

function delete_opsd_option ( $option ) {

    $u_value = apply_opsd_filter('opsd_delete_option', 'no-values'  , $option );
    if ( $u_value !== 'no-values' ) return $u_value;

    return delete_option($option );
}

function add_opsd_option( $option, $value = '', $deprecated = '', $autoload = 'yes' ) {

    $u_value = apply_opsd_filter('opsd_add_option', 'no-values'  , $option, $value, $deprecated,  $autoload );
    if ( $u_value !== 'no-values' ) return $u_value;

    return add_option( $option, $value  , $deprecated  , $autoload   );
}


