<?php
/*
Plugin Name:    Widget Logic
Plugin URI:     http://wordpress.org/extend/plugins/widget-logic/
Description:    Control widgets with WP's conditional tags is_home etc
Version:        5.9.0
Author:         wpchefgadget, alanft

Text Domain:   widget-logic
Domain Path:   /languages/
*/

DEFINE( 'WIDGET_LOGIC_VERSION', '5.7.0' );

register_activation_hook( __FILE__, 'widget_logic_activate' );

function widget_logic_activate()
{
	$alert = (array)get_option( 'wpchefgadget_alert', array() );
	if ( get_option('widget_logic_version') !=  WIDGET_LOGIC_VERSION && !empty( $alert['limit-login-attempts'] ) )
	{
		unset( $alert['limit-login-attempts'] );
		add_option( 'wpchefgadget_alert', $alert, '', 'no' );
		update_option( 'wpchefgadget_alert', $alert );
	}
	add_option( 'widget_logic_version', WIDGET_LOGIC_VERSION, '', 'no' );
	update_option( 'widget_logic_version', WIDGET_LOGIC_VERSION );
}

$plugin_dir = basename(dirname(__FILE__));
global $wl_options, $wl_in_customizer;

$wl_in_customizer = false;

add_action( 'init', 'widget_logic_init' );
function widget_logic_init()
{
    load_plugin_textdomain( 'widget-logic', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	/*
	if ( is_admin() )
	{
		if ( get_option('widget_logic_version') != WIDGET_LOGIC_VERSION )
			widget_logic_activate();

		global $wp_version;
		if ( version_compare( $wp_version, '4.2', '>=' ) && !file_exists(WP_PLUGIN_DIR.'/limit-login-attempts-reloaded') && current_user_can('install_plugins')  )
		{
			$alert = (array)get_option( 'wpchefgadget_alert', array() );
			if ( empty( $alert['limit-login-attempts'] ) )
			{
				add_action( 'admin_notices', 'widget_logic_alert');
				add_action( 'network_admin_notices', 'widget_logic_alert');
				add_action( 'wp_ajax_wpchefgadget_dismiss_alert', 'widget_logic_dismiss_alert' );
				add_action( 'admin_enqueue_scripts', 'widget_logic_alert_scripts' );
			}
			//enqueue admin/js/updates.js
		}
	}
	*/
}

if((!$wl_options = get_option('widget_logic')) || !is_array($wl_options) )
	$wl_options = array();

if (is_admin())
{
	add_filter( 'in_widget_form', 'widget_logic_in_widget_form', 10, 3 );
	add_filter( 'widget_update_callback', 'widget_logic_update_callback', 10, 4);

	add_action( 'sidebar_admin_setup', 'widget_logic_expand_control');
	// before any HTML output save widget changes and add controls to each widget on the widget admin page
	add_action( 'sidebar_admin_page', 'widget_logic_options_control');
	// add Widget Logic specific options on the widget admin page
	add_filter( 'plugin_action_links', 'wl_charity', 10, 2);// add my justgiving page link to the plugin admin page

	add_action( 'widgets_init', 'widget_logic_add_controls', 999 );
}
else
{
	$loadpoint = isset($wl_options['widget_logic-options-load_point']) ? (string)@$wl_options['widget_logic-options-load_point'] : '';
	if ( 'plugins_loaded' == $loadpoint )
		widget_logic_sidebars_widgets_filter_add();
	else
	{
		if ( !in_array( $loadpoint, array( 'after_setup_theme', 'wp_loaded', 'wp_head' ) ) )
			$loadpoint = 'parse_query';

		add_action( $loadpoint, 'widget_logic_sidebars_widgets_filter_add' );
	}

	if ( !empty($wl_options['widget_logic-options-filter']) )
		add_filter( 'dynamic_sidebar_params', 'widget_logic_widget_display_callback', 10);
		// redirect the widget callback so the output can be buffered and filtered
}


function widget_logic_in_customizer()
{
	global $wl_in_customizer;
	$wl_in_customizer = true;

	//add_filter( 'widget_display_callback', 'widget_logic_customizer_display_callback', 10, 3 );
	add_action( 'dynamic_sidebar', 'widget_logic_customizer_dynamic_sidebar_callback' );
}
add_action( 'customize_preview_init', 'widget_logic_in_customizer' );


function widget_logic_sidebars_widgets_filter_add()
{
	// actually remove the widgets from the front end depending on widget logic provided
	add_filter( 'sidebars_widgets', 'widget_logic_filter_sidebars_widgets', 10);
}
// wp-admin/widgets.php explicitly checks current_user_can('edit_theme_options')
// which is enough security, I believe. If you think otherwise please contact me


// CALLED VIA 'widget_update_callback' FILTER (ajax update of a widget)
function widget_logic_update_callback( $instance, $new_instance, $old_instance, $this_widget )
{
	if ( isset( $new_instance['widget_logic'] ) )
		$instance['widget_logic'] = $new_instance['widget_logic'];

	return $instance;
}


// CALLED VIA 'sidebar_admin_setup' ACTION
// adds in the admin control per widget, but also processes import/export
function widget_logic_expand_control()
{	global $wp_registered_widgets, $wp_registered_widget_controls, $wl_options;


	// EXPORT ALL OPTIONS
	if (isset($_GET['wl-options-export']))
	{
		header("Content-Disposition: attachment; filename=widget_logic_options.txt");
		header('Content-Type: text/plain; charset=utf-8');

		echo "[START=WIDGET LOGIC OPTIONS]\n";
		foreach ($wl_options as $id => $text)
			echo "$id\t".json_encode($text)."\n";
		echo "[STOP=WIDGET LOGIC OPTIONS]";
		exit;
	}


	// IMPORT ALL OPTIONS
	if ( isset($_POST['wl-options-import']))
	{	if ($_FILES['wl-options-import-file']['tmp_name'])
		{	$import=explode("\n",file_get_contents($_FILES['wl-options-import-file']['tmp_name'], false));
			if (array_shift($import)=="[START=WIDGET LOGIC OPTIONS]" && array_pop($import)=="[STOP=WIDGET LOGIC OPTIONS]")
			{	foreach ($import as $import_option)
				{	list($key, $value)=explode("\t",$import_option);
					$wl_options[$key]=json_decode($value);
				}
				$wl_options['msg']= __('Success! Options file imported','widget-logic');
			}
			else
			{	$wl_options['msg']= __('Invalid options file','widget-logic');
			}

		}
		else
			$wl_options['msg']= __('No options file provided','widget-logic');

		update_option('widget_logic', $wl_options);
		wp_redirect( admin_url('widgets.php') );
		exit;
	}

	// UPDATE OTHER WIDGET LOGIC OPTIONS
	// must update this to use http://codex.wordpress.org/Settings_API
	if ( isset($_POST['widget_logic-options-submit']) )
	{
		$wl_options['widget_logic-options-filter'] = !empty($_POST['widget_logic-options-filter']);
		$wl_options['widget_logic-options-wp_reset_query'] = !empty($_POST['widget_logic-options-wp_reset_query']);
		$wl_options['widget_logic-options-show_errors'] = !empty($_POST['widget_logic-options-show_errors']);
		$wl_options['widget_logic-options-load_point']=$_POST['widget_logic-options-load_point'];
	}


	update_option('widget_logic', $wl_options);

}




// CALLED VIA 'sidebar_admin_page' ACTION
// output extra HTML
// to update using http://codex.wordpress.org/Settings_API asap
function widget_logic_options_control()
{	global $wp_registered_widget_controls, $wl_options;

	if ( isset($wl_options['msg']))
	{	if (substr($wl_options['msg'],0,2)=="OK")
			echo '<div id="message" class="updated">';
		else
			echo '<div id="message" class="error">';
		echo '<p>Widget Logic – '.$wl_options['msg'].'</p></div>';
		unset($wl_options['msg']);
		update_option('widget_logic', $wl_options);
	}


	?><div class="wrap">

		<h2><?php _e('Widget Logic options', 'widget-logic'); ?></h2>
		<form method="POST" style="float:left; width:45%">
			<ul>
				<li><label for="widget_logic-options-filter" title="<?php _e('Adds a new WP filter you can use in your own code. Not needed for main Widget Logic functionality.', 'widget-logic'); ?>">
					<input id="widget_logic-options-filter" name="widget_logic-options-filter" type="checkbox" value="checked" class="checkbox" <?php if (!empty($wl_options['widget_logic-options-filter'])) echo "checked" ?>/>
					<?php _e('Add \'widget_content\' filter', 'widget-logic'); ?>
					</label>
				</li>
				<li><label for="widget_logic-options-wp_reset_query" title="<?php _e('Resets a theme\'s custom queries before your Widget Logic is checked', 'widget-logic'); ?>">
					<input id="widget_logic-options-wp_reset_query" name="widget_logic-options-wp_reset_query" type="checkbox" value="checked" class="checkbox" <?php if (!empty($wl_options['widget_logic-options-wp_reset_query'])) echo "checked" ?> />
					<?php _e('Use \'wp_reset_query\' fix', 'widget-logic'); ?>
					</label>
				</li>
				<li><label for="widget_logic-options-load_point" title="<?php _e('Delays widget logic code being evaluated til various points in the WP loading process', 'widget-logic'); ?>"><?php _e('Load logic', 'widget-logic'); ?>
					<select id="widget_logic-options-load_point" name="widget_logic-options-load_point" ><?php
						$wl_load_points = array(
							'parse_query'    =>	__( 'after query variables set (default)', 'widget-logic' ),
							'plugins_loaded'    =>	__( 'when plugin starts', 'widget-logic' ),
							'after_setup_theme' =>	__( 'after theme loads', 'widget-logic' ),
							'wp_loaded'         =>	__( 'when all PHP loaded', 'widget-logic' ),
							'wp_head'           =>	__( 'during page header', 'widget-logic' )
						);
						foreach($wl_load_points as $action => $action_desc)
						{	echo "<option value='".$action."'";
							if (isset($wl_options['widget_logic-options-load_point']) && $action==$wl_options['widget_logic-options-load_point'])
								echo " selected ";
							echo ">".$action_desc."</option>"; //
						}
						?>
					</select>
					</label>
				</li>
				<li>
					<label for="widget_logic-options-show_errors">
					<input id="widget_logic-show_errors" name="widget_logic-options-show_errors" type="checkbox" value="1" class="checkbox" <?php if (!empty($wl_options['widget_logic-options-show_errors'])) echo "checked" ?> />
					<?php esc_html_e('Display logic errors to admin', 'widget-logic'); ?>
					</label>
			</ul>
			<?php submit_button( __( 'Save WL options', 'widget-logic' ), 'button-primary', 'widget_logic-options-submit', false ); ?>

		</form>
		<form method="POST" enctype="multipart/form-data" style="float:left; width:45%">
			<a class="submit button" href="?wl-options-export" title="<?php _e('Save all WL options to a plain text config file', 'widget-logic'); ?>"><?php _e('Export options', 'widget-logic'); ?></a><p>
			<?php submit_button( __( 'Import options', 'widget-logic' ), 'button', 'wl-options-import', false, array('title'=> __( 'Load all WL options from a plain text config file', 'widget-logic' ) ) ); ?>
			<input type="file" name="wl-options-import-file" id="wl-options-import-file" title="<?php _e('Select file for importing', 'widget-logic'); ?>" /></p>
		</form>

	</div>

	<?php
}

function widget_logic_add_controls()
{
	global $wp_registered_widget_controls, $wp_registered_widgets, $wp_registered_widget_updates;

	foreach ( $wp_registered_widgets as $id => $widget )
	{
		if ( preg_match( '/^(.+)-(\d+)$/', $id) )
			continue;

		if ( !isset( $wp_registered_widget_controls[ $id ] ) )
		{
			wp_register_widget_control( $id, $id, 'widget_logic_extra_control', array(), $id, null );
			continue;
		}

		if ( @$wp_registered_widget_controls[ $id ]['callback'] != 'widget_logic_extra_control' )
		{
			$wp_registered_widget_controls[$id]['params'][] = $id;
			$wp_registered_widget_controls[$id]['params'][] = @$wp_registered_widget_controls[$id]['callback'];
			$wp_registered_widget_controls[$id]['callback'] = 'widget_logic_extra_control';

			$wp_registered_widget_updates[$id]['params'][] = $id;
			$wp_registered_widget_updates[$id]['params'][] = @$wp_registered_widget_updates[$id]['callback'];
			$wp_registered_widget_updates[$id]['callback'] = 'widget_logic_extra_control';
		}
	}
}

function widget_logic_in_widget_form( $widget, $return, $instance )
{
	$logic = isset( $instance['widget_logic'] ) ? $instance['widget_logic'] : widget_logic_by_id( $widget->id );

	?>
		<p>
			<label for="<?php echo $widget->get_field_id('widget_logic'); ?>">
				<?php esc_html_e('Widget logic:','widget-logic') ?>
			</label>
			<textarea class="widefat" name="<?php echo $widget->get_field_name('widget_logic'); ?>" id="<?php echo $widget->get_field_id('widget_logic'); ?>"><?php echo esc_textarea( $logic ) ?></textarea>
		</p>
	<?php
	return;
}

// added to widget functionality in 'widget_logic_expand_control' (above)
function widget_logic_extra_control()
{
	global $wp_customize;
	$args = func_get_args();

	$callback = array_pop( $args );
	$widget_id = array_pop( $args );

	if ( is_callable($callback) )
		call_user_func_array( $callback, $args );

	if ( isset( $_POST["widget-$widget_id"]['widget_logic'] ) )
	{
		$logic = stripslashes( $_POST["widget-$widget_id"]['widget_logic'] );
		widget_logic_save( $widget_id, $logic );
	}
	else
		$logic = widget_logic_by_id( $widget_id );

	$input_id = "widget-$widget_id-widget_logic";
	$input_name = "widget-{$widget_id}[widget_logic]";
	?>
		<p>
			<label for="<?php echo $input_id ?>">
				<?php esc_html_e('Widget logic:','widget-logic') ?>
			</label>
			<?php if ( !empty($wp_customize) && $wp_customize->is_preview() ): ?>
			<textarea class="widefat" id="<?php echo $input_id ?>" readonly><?php echo esc_textarea( $logic ) ?></textarea>
			<br>
			<span class="description"><?php printf( esc_html__('This is a "wp register sidebar widget" and is different from regular widgets. Hence it can only be edited from the %s page.', 'widget-logic'), sprintf( '<a href="%s" target="_blank">%s</a>', esc_attr(admin_url('widgets.php')), __('widgets') ) ) ?></span>
			<?php else: ?>
			<textarea class="widefat" name="<?php echo $input_name ?>" id="<?php echo $input_id ?>"><?php echo esc_textarea( $logic ) ?></textarea>
			<?php endif ?>
		</p>
	<?php
	return true;
}

// CALLED ON 'plugin_action_links' ACTION
function wl_charity($links, $file)
{	if ($file == plugin_basename(__FILE__))
		array_push($links, '<a href="http://www.justgiving.com/widgetlogic_cancerresearchuk/">'.esc_html__('Charity Donation', 'widget-logic').'</a>');
	return $links;
}



// FRONT END FUNCTIONS...

function widget_logic_by_id( $widget_id )
{
	global $wl_options;

	if ( preg_match( '/^(.+)-(\d+)$/', $widget_id, $m ) )
	{
		$widget_class = $m[1];
		$widget_i = $m[2];

		$info = get_option( 'widget_'.$widget_class );
		if ( empty( $info[ $widget_i ] ) )
			return '';

		$info = $info[ $widget_i ];
	}
	else
		$info = (array)get_option( 'widget_'.$widget_id, array() );

	if ( isset( $info['widget_logic'] ) )
		$logic = $info['widget_logic'];

	elseif ( isset( $wl_options[ $widget_id ] ) )
	{
		$logic = stripslashes( $wl_options[ $widget_id ] );
		widget_logic_save( $widget_id, $logic );

		unset( $wl_options[ $widget_id ] );
		update_option( 'widget_logic', $wl_options );
	}

	else
		$logic = '';

	return $logic;
}

function widget_logic_save( $widget_id, $logic )
{
	global $wl_options;

	if ( preg_match( '/^(.+)-(\d+)$/', $widget_id, $m ) )
	{
		$widget_class = $m[1];
		$widget_i = $m[2];

		$info = get_option( 'widget_'.$widget_class );
		if ( !is_array( $info[ $widget_i ] ) )
			$info[ $widget_i ] = array();

		$info[ $widget_i ]['widget_logic'] = $logic;
		update_option( 'widget_'.$widget_class, $info );
	}
	else
	{
		$info = (array)get_option( 'widget_'.$widget_id, array() );
		$info['widget_logic'] = $logic;
		update_option( 'widget_'.$widget_id, $info );
	}
}

// CALLED ON 'sidebars_widgets' FILTER
function widget_logic_filter_sidebars_widgets( $sidebars_widgets )
{
	global $wl_options, $wl_in_customizer;

	if ( $wl_in_customizer )
		return $sidebars_widgets;

	// reset any database queries done now that we're about to make decisions based on the context given in the WP query for the page
	if ( !empty( $wl_options['widget_logic-options-wp_reset_query'] ) )
		wp_reset_query();

	// loop through every widget in every sidebar (barring 'wp_inactive_widgets') checking WL for each one
	foreach($sidebars_widgets as $widget_area => $widget_list)
	{
		if ($widget_area=='wp_inactive_widgets' || empty($widget_list))
			continue;

		foreach($widget_list as $pos => $widget_id)
		{
			$logic = widget_logic_by_id( $widget_id );

			if ( !widget_logic_check_logic( $logic ) )
				unset($sidebars_widgets[$widget_area][$pos]);
		}
	}
	return $sidebars_widgets;
}


function widget_logic_check_logic( $logic )
{
	$logic = @trim( (string)$logic );
	$logic = apply_filters( "widget_logic_eval_override", $logic );

	if ( is_bool( $logic ) )
		return $logic;

	if ( $logic === '' )
		return true;

	if ( stristr( $logic, "return" ) === false )
		$logic = "return ( $logic );";

	set_error_handler( 'widget_logic_error_handler' );

	try {
		$show_widget = eval($logic);
	}
	catch ( Error $e ) {
		trigger_error( $e->getMessage(), E_USER_WARNING );

		$show_widget = false;
	}

	restore_error_handler();

	return $show_widget;
}

function widget_logic_error_handler( $errno , $errstr )
{
	global $wl_options;
	$show_errors = !empty($wl_options['widget_logic-options-show_errors']) && current_user_can('manage_options');

	if ( $show_errors )
		echo 'Invalid Widget Logic: '.$errstr;

	return true;
}

function widget_logic_customizer_dynamic_sidebar_callback( $widget )
{
	widget_logic_customizer_display( $widget['id'] );
}

function widget_logic_customizer_display( $widget_id )
{
	if ( !preg_match( '/^(.+)-(\d+)$/', $widget_id) )
		return;

	$logic = widget_logic_by_id( $widget_id );

	global $wl_options;
	$show_errors = !empty($wl_options['widget_logic-options-show_errors']) && current_user_can('manage_options');

	ob_start();
	$show_widget = widget_logic_check_logic( $logic );
	$error = ob_get_clean();

	if ( $show_errors && $error ) :
		?><script>jQuery(function($){$('#<?php echo $widget_id?>').append( $('<p class="widget-logic-error">').html(<?php echo json_encode($error)?>) );})</script><?php
	endif;
	if ( !$show_widget ):
		?><script>jQuery(function($){$('#<?php echo $widget_id?>').children().not('.widget-logic-error').css('opacity', '0.2');})</script><?php
	endif;
}

// CALLED ON 'dynamic_sidebar_params' FILTER - this is called during 'dynamic_sidebar' just before each callback is run
// swap out the original call back and replace it with our own
function widget_logic_widget_display_callback($params)
{	global $wp_registered_widgets;
	$id=$params[0]['widget_id'];
	$wp_registered_widgets[$id]['callback_wl_redirect']=$wp_registered_widgets[$id]['callback'];
	$wp_registered_widgets[$id]['callback']='widget_logic_redirected_callback';
	return $params;
}


// the redirection comes here
function widget_logic_redirected_callback()
{	global $wp_registered_widgets;

	// replace the original callback data
	$params=func_get_args();
	$id=$params[0]['widget_id'];
	$callback=$wp_registered_widgets[$id]['callback_wl_redirect'];
	$wp_registered_widgets[$id]['callback']=$callback;

	// run the callback but capture and filter the output using PHP output buffering
	if ( is_callable($callback) )
	{	ob_start();
		call_user_func_array($callback, $params);
		$widget_content = ob_get_contents();
		ob_end_clean();
		echo apply_filters( 'widget_content', $widget_content, $id);
	}
}


function widget_logic_alert()
{
	if ( $old = get_option('wpchefgadget_promo') )
	{
		delete_option('wpchefgadget_promo');
		if ( $old['limit-login-attempts'] )
		{
			$alert = (array)get_option( 'wpchefgadget_alert', array() );
			$alert['limit-login-attempts'] = $old['limit-login-attempts'];
			update_option( 'wpchefgadget_alert', $alert );
			return;
		}
	}

	$screen = get_current_screen();

	?>
	<div class="notice notice-info is-dismissible" id="wpchefgadget_alert_lla">
		<p class="plugin-card-limit-login-attempts-reloaded"<?php if ( $screen->id != 'plugin-install' ) echo ' id="plugin-filter"' ?>>
			<b>Widget Logic team security recommendation only!</b> If your site is currently not protected (check with your admin) against login attacks (the most common reason admin login gets compromised) we highly recommend installing <a href="<?php echo network_admin_url('plugin-install.php?tab=plugin-information')?>&amp;plugin=limit-login-attempts-reloaded&amp;TB_iframe=true&amp;width=600&amp;height=550" class="thickbox open-plugin-details-modal" aria-label="More information about Limit Login Attempts Reloaded" data-title="Limit Login Attempts Reloaded">Limit Login Attempts Reloaded</a> plugin to immediately have the protection in place.
			<a href="<?php echo network_admin_url('plugin-install.php?tab=plugin-information')?>&amp;plugin=limit-login-attempts-reloaded&amp;TB_iframe=true&amp;width=600&amp;height=550" class="thickbox open-plugin-details-modal button" aria-label="More information about Limit Login Attempts Reloaded" data-title="Limit Login Attempts Reloaded" id="wpchef_alert_install_button">Install</a>
			<a class="install-now button" data-slug="limit-login-attempts-reloaded" href="<?php echo network_admin_url('update.php?action=install-plugin')?>&amp;plugin=limit-login-attempts-reloaded&amp;_wpnonce=<?php echo wp_create_nonce('install-plugin_limit-login-attempts-reloaded') ?>" aria-label="Install Limit Login Attempts Reloaded now" data-name="Limit Login Attempts Reloaded" style="display:none">Install Now</a>
		</p>
	</div>
	<script>
	jQuery('#wpchefgadget_alert_lla .open-plugin-details-modal').on('click', function(){
		jQuery('#wpchef_alert_install_button').hide().next().show();
		return true;
	});
	jQuery(function($){
		var alert = $('#wpchefgadget_alert_lla');
		alert.on('click', '.notice-dismiss', function(e){
			//e.preventDefault
			$.post( ajaxurl, {
				action: 'wpchefgadget_dismiss_alert',
				alert: 'limit-login-attempts',
				sec: <?php echo json_encode( wp_create_nonce('wpchefgadget_dissmiss_alert') ) ?>
			} );
		});

		<?php if ( $screen->id == 'plugin-install' ): ?>
		$('#plugin-filter').prepend( alert.css('margin-bottom','10px').addClass('inline') );
		<?php endif ?>

		$(document).on('tb_unload', function(){
			if ( jQuery('#wpchef_alert_install_button').next().hasClass('updating-message') )
				return;

			jQuery('#wpchef_alert_install_button').show().next().hide();
		});
		$(document).on('credential-modal-cancel', function(){
			jQuery('#wpchef_alert_install_button').show().next().hide();
		});
	});
	</script>
	<?php
	wp_print_request_filesystem_credentials_modal();
}

function widget_logic_dismiss_alert()
{
	check_ajax_referer( 'wpchefgadget_dissmiss_alert', 'sec' );

	$alert = (array)get_option( 'wpchefgadget_alert', array() );
	$alert[ $_POST['alert'] ] = 1;

	add_option( 'wpchefgadget_alert', $alert, '', 'no' );
	update_option( 'wpchefgadget_alert', $alert );

	exit;
}

function widget_logic_alert_scripts()
{
	wp_enqueue_script( 'plugin-install' );
	add_thickbox();
	wp_enqueue_script( 'updates' );
}

?>