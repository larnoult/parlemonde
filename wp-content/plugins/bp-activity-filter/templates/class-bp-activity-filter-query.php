<?php
/**
 * Defining class for Filtering activity stream
 */
if ( ! class_exists( 'WbCom_BP_Activity_Filter_Activity_Stream' ) ) {
	class WbCom_BP_Activity_Filter_Activity_Stream {
		/**
		 * Constructor
		 */
		public function __construct() {
			/**
			 * Filtering activity stream
			 */
				add_filter( 'bp_ajax_querystring', array( $this, 'filtering_activity_default' ), 999, 2 );
				$filter_key = 'has_activities';
				add_filter( 'bp_before_' . $filter_key . '_parse_args', array( $this, 'bpaf_filter_activity' ), 100, 1 );
		}

		public function bpaf_filter_activity( $args ) {
			if ( array_key_exists( 'scope', $args ) || array_key_exists( 'since', $args ) || array_key_exists( 'include', $args ) || array_key_exists( 'include', $args ) || array_key_exists( 'page', $args ) ) {
				global $bp;
				$count                  = 0;
				$action                 = '';
				$defult_activity_stream = bp_get_option( 'bp-default-filter-name' );
				$hidden_activity_stream = bp_get_option( 'bp-hidden-filters-name' );
				if ( empty( $hidden_activity_stream ) ) {
					$hidden_activity_stream = array();
				}
				$admin_setting_object = new WbCom_BP_Activity_Filter_Admin_Setting();
				$labels               = $admin_setting_object->bpaf_get_labels();
				if ( ! empty( $hidden_activity_stream ) ) {
					foreach ( $labels as $l_key => $l_value ) {
						if ( ! empty( $l_value ) ) {
							if ( in_array( $l_key, $hidden_activity_stream ) ) {

							} else {
								if ( $count == 0 ) {
									$action .= $l_key;
									$count++;
								} else {
									$action .= ',' . $l_key;
									$count++;
								}
							}
						}
					}
					$args['action']  = $action;
					$args['include'] = '';
				}
			}
			return $args;
		}

		/**
		 * Modyfying activity loop for default acitvity
		 *
		 * @param $retval
		 */
		public function filtering_activity_default( $query, $object ) {
			global $bp;
			if ( 'activity' != $object ) {
				return $query;
			}

			if ( empty( $query ) ) {
				$query = wp_parse_args( $query, array() );

				$count                  = 0;
				$action                 = '';
				$defult_activity_stream = bp_get_option( 'bp-default-filter-name' );
				$hidden_activity_stream = bp_get_option( 'bp-hidden-filters-name' );
				if ( empty( $hidden_activity_stream ) ) {
					$hidden_activity_stream = array();
				}
				$admin_setting_object = new WbCom_BP_Activity_Filter_Admin_Setting();
				$labels               = $admin_setting_object->bpaf_get_labels();
				foreach ( $labels as $l_key => $l_value ) {
					if ( ! empty( $l_value ) ) {
						if ( in_array( $l_key, $hidden_activity_stream ) ) {

						} else {
							if ( $count == 0 ) {
								$action .= $l_key;
								$count++;
							} else {
								$action .= ',' . $l_key;
								$count++;
							}
						}
					}
				}
				if ( $defult_activity_stream != -1 ) {
					$query = 'action=' . $defult_activity_stream;
				} else {
					$query = 'action=' . $action;
				}
			}
			return $query;
		}
	}
}
if ( class_exists( 'WbCom_BP_Activity_Filter_Activity_Stream' ) ) {
	$filter_query_obj = new WbCom_BP_Activity_Filter_Activity_Stream();
}
