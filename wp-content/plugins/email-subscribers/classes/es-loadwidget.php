<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

class es_cls_widget {
	public static function load_subscription( $arr ) {

		if ( ! is_array( $arr ) ) {
			return '';
		}

		$instance['es_name']  = trim($arr['es_name']);
		$instance['es_desc']  = trim($arr['es_desc']);
		$instance['es_group'] = trim($arr['es_group']);
		$instance['es_pre'] = 'shortcode';
		return es_cls_registerhook::es_get_form($instance);

	}
}

function es_shortcode( $atts ) {

	if ( ! is_array( $atts ) ) {
		return '';
	}

	$es_name = isset($atts['namefield']) ? $atts['namefield'] : 'YES';
	$es_desc = isset($atts['desc']) ? $atts['desc'] : '';
	$es_group = isset($atts['group']) ? $atts['group'] : '';

	$arr = array();
	$arr["es_title"] 	= "";
	$arr["es_desc"] 	= $es_desc;
	$arr["es_name"] 	= $es_name;
	$arr["es_group"] 	= $es_group;
	return es_cls_widget::load_subscription($arr);

}

function es_subbox( $namefield = "YES", $desc = "", $group = "" ) {

	$arr = array();
	$arr["es_title"] 	= "";
	$arr["es_desc"] 	= $desc;
	$arr["es_name"] 	= $namefield;
	$arr["es_group"] 	= $group;
	echo es_cls_widget::load_subscription($arr);

}