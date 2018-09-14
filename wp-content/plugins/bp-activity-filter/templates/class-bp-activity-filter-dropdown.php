<?php
/**
 * Defining class for Filter dropdown option for public setting
 */

if (!class_exists('WbCom_BP_Activity_Filter_Public_Setting')) {
	class WbCom_BP_Activity_Filter_Public_Setting {

		/**
		 * Constructor
		 */

		public function __construct() {
			/**
			 * Showing selected filters in dropdown
			 */
			add_filter('bp_get_activity_show_filters', array($this, 'getting_all_filters_function'), 999, 3);

		/* Clearing cookie for correct result */
			$past = time() - 3600;
			if (isset($_COOKIE['bp-activity-filter']))
				setcookie('bp-activity-filter', ' ', $past, '/');
		}
		/**
		 * Populating dropdown with selected filter on front-end
		 * @param html $output
		 * @param array $filters
		 * @param array $context
		 * @return string
		 */

		public function getting_all_filters_function($output, $filters, $context) {

			// Build the options output.
			$output = '';
			$filters_db = bp_get_option('bp-hidden-filters-name');
			foreach ($filters as  $key => $value) {
				if (in_array($key, $filters_db))
					unset($filters[$key]);
			}

			if (!empty($filters)) {
				$defult_activity_stream = bp_get_option('bp-default-filter-name');
				foreach ($filters as $value => $filter) {
					if ($value == $defult_activity_stream)
						$output .= '<option value="' . esc_attr($value) . '" selected=selected>' . esc_html($filter) . '</option>' . "\n";
					else $output .= '<option value="' . esc_attr($value) . '">' . esc_html($filter) . '</option>' . "\n";
				}
			}
			return $output;
		}
	}
}
if (class_exists('WbCom_BP_Activity_Filter_Public_Setting')) {
	$filter_obj = new WbCom_BP_Activity_Filter_Public_Setting();
}