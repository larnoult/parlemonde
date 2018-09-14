<?php
/*
Plugin Name: Dashboard Widget Sidebar
Plugin URI: http://www.iosoftgame.com/
Description: Enable regulare widgets to be used as Dashboard Widgets in admin
Version: 1.2.3
Author: IO SoftGame
Author URI: http://www.iosoftgame.com
License: GPLv2 or later
*/
?>
<?php
	// Function that outputs the contents of the dashboard widget
	function dws_dashboard_widget_function($post, $metabox) {
		
		//Get global variables
		global $wp_registered_sidebars, $wp_registered_widgets;
		
		//Get sidebars
		$sidebars = wp_get_sidebars_widgets();
		//Get widgets
		$dws_widgets = $sidebars["dws-sidebar"];
		
		//Get current widget
		$id = $metabox["args"]["id"];
		
		//Get the sidebar
		$sidebar = $wp_registered_sidebars["dws-sidebar"];
		
		//Gets widgets unique number
		$widgetnumber = $wp_registered_widgets[$id]["params"][0]["number"];
		
		//Check if the required data is set
		if( isset($wp_registered_widgets[$id]) && isset($wp_registered_widgets[$id]["callback"]) && isset($wp_registered_widgets[$id]["callback"][0]) && $wp_registered_widgets[$id]["params"][0]["number"] == $widgetnumber)
		{
			/* Code borrowed from widget.php in the WordPress core */
			$params = array_merge(
			                array( array_merge( $sidebar, array('widget_id' => $id, 'widget_name' => $wp_registered_widgets[$id]['name']) ) ),
			                (array) $wp_registered_widgets[$id]['params']
			        );

	        // Substitute HTML id and class attributes into before_widget
	        $classname_ = '';
	        foreach ( (array) $wp_registered_widgets[$id]['classname'] as $cn ) {
	                if ( is_string($cn) )
	                        $classname_ .= '_' . $cn;
	                elseif ( is_object($cn) )
	                        $classname_ .= '_' . get_class($cn);
	        }
	        $classname_ = ltrim($classname_, '_');
	        $params[0]['before_widget'] = sprintf($params[0]['before_widget'], $id, $classname_);

	        $params = apply_filters( 'dynamic_sidebar_params', $params );

	        $callback = $wp_registered_widgets[$id]['callback'];

	        do_action( 'dynamic_sidebar', $wp_registered_widgets[$id] );
			
			if ( is_callable($callback) ) {
				//Call the function, that outputs the widget content
				call_user_func_array($callback, $params);
	        }
			
			/* ---------------------------------------------------- */
		}
		
	}

	// Function used in the action hook
	function dws_add_dashboard_widgets() {
		
		global $wp_registered_sidebars, $wp_registered_widgets;
		
		//Get current settings
		$widgetSettings = get_option('dws_widget_settings', array());
		
		//Get sidebars
		$sidebars = wp_get_sidebars_widgets();
		
		//Get widgets from the sidebar
		$dws_widgets = $sidebars["dws-sidebar"];
		
		//Add each widget to the dashboard
		if(is_array($dws_widgets) && count($dws_widgets) > 0) {
			foreach($dws_widgets as $id)
			{
				if(!isset($wp_registered_widgets[$id]))
					continue;
				//Gets widgets unique number
				$widgetnumber = $wp_registered_widgets[$id]["params"][0]["number"];
				
				//Check if the required data is set
				if( isset($wp_registered_widgets[$id]) && isset($wp_registered_widgets[$id]["callback"]) && isset($wp_registered_widgets[$id]["callback"][0]) && $wp_registered_widgets[$id]["params"][0]["number"] == $widgetnumber)
				{
					//Get widgets settings
					$widget = $wp_registered_widgets[$id]["callback"][0]->get_settings();

					//Set title
					if(trim($widget[$widgetnumber]["title"]) == "") {
						$title = '&nbsp;';
					} else {
						$title = $widget[$widgetnumber]["title"];
					}					
					
					//Settings - default
					if(!isset($widgetSettings[$id])) {
						$widgetSettings[$id] = array(
							'priority' => 'default',
							'context' => 'normal'
						);
					}
					
					//Add the widget to dashboard
					add_meta_box( 
						'dws_dashboard_widget_' . $id, 					//ID
						$title, 										//Title
						'dws_dashboard_widget_function', 				//Callback function
						'dashboard', 									//Where?
						$widgetSettings[$id]['context'], 				//Context
						$widgetSettings[$id]['priority'], 				//Priority
						array('id' => $id)								//Meta data
					);
				}
			}
		}
	}

	// Register the new dashboard widget with the 'wp_dashboard_setup' action
	add_action('wp_dashboard_setup', 'dws_add_dashboard_widgets' );
	
	//Register the widget sidebar
	register_sidebar(array(
		'name' => __( 'Dashboard Widget Sidebar' ),
		'id' => 'dws-sidebar',
		'description' => __( 'Widgets in this area will be shown on the dashboard in admin.' ),
		'before_title' => '<div style="display: none;">',
		'after_title' => '</div>',
		'before_widget' => '',
		'after_widget' => ''
	));
	
	//Regsiter admin script
	function dws_enqueue_script($hook) {
   		if( 'widgets.php' != $hook )
	        return;
			
	    wp_enqueue_script( 'dws_admin_script', plugins_url('/dashboard-widget-sidebar.js', __FILE__) );
	}
	add_action( 'admin_enqueue_scripts', 'dws_enqueue_script' );
	
	//Register admin ajax
	function dws_ajax_update() {
		//Get widget ID
		$widgetID = $_POST['widget_id'];
		
		//Get current settings
		$widgetSettings = get_option('dws_widget_settings', array());
		
		//Settings
		$widgetSettings[$widgetID]['priority'] = strtolower($_POST['priority']);
		$widgetSettings[$widgetID]['context'] = strtolower($_POST['context']);
		
		//Update settings
		update_option('dws_widget_settings', $widgetSettings);
		
		//Return 1 to the client
	    echo '1';

		die(); // this is required to return a proper result
	}
	add_action('wp_ajax_dws_ajax_update', 'dws_ajax_update');
	
	//Admin head
	function dws_admin_head(){		
		/* Style */
		echo '<style>.dws-settings label {display: block;}</style>';
		
		/* Settings */
		echo '<script type="text/javascript">
			var dwsWidgetSettings= new Array();';
		
		//Get current settings
		$widgets = get_option('dws_widget_settings', array());
		
		//Echo setting to be used in Javascript
		foreach($widgets as $widgetID=>$widgetSettings) {
			echo 'dwsWidgetSettings["' . $widgetID . '"] = ["' . $widgetSettings['priority'] . '", "' . $widgetSettings['context'] . '"];';
		}
		
		echo '</script>';
	}
	add_action('admin_head', 'dws_admin_head');
	
	/**
	 * Add-Ons
	 */
	 
	//Contact Form 7
	if(defined('WPCF7_PLUGIN_DIR') && $_SERVER["SCRIPT_NAME"] == '/wp-admin/index.php') {
		require_once WPCF7_PLUGIN_DIR . '/includes/controller.php';
		add_action( 'admin_enqueue_scripts', 'wpcf7_do_enqueue_scripts' );
	}
?>