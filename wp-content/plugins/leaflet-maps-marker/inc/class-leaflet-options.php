<?php
/**
 * Leaflet Maps Marker Plugin - settings class
 * based on class by Alison Barrett, http://alisothegeek.com/2011/01/wordpress-settings-api-tutorial-1/
*/
//info prevent file from being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'class-leaflet-options.php') { die ("Please do not access this file directly. Thanks!<br/><a href='https://www.mapsmarker.com/go'>www.mapsmarker.com</a>"); }
class Class_leaflet_options {
	private $panes;
	private $sections;
	private $checkboxes;
	private $_settings = array();

	public function __get( $name ) {
		if ( 'settings' === $name ) {
			$this->get_settings();
			return $this->_settings;
		}
		return null;
	}

	public function __isset( $name ) {
		if ( 'settings' === $name ) {
			$this->get_settings();
			return ! empty( $this->_settings );
		}
		return false;
	}

	 /**
	 *
	 * Construct
	 *
	 */
	public function __construct() {
		//info:  This will keep track of the checkbox options for the validate_settings function.
		$this->checkboxes = array();

		$this->panes['mapdefaults'] 	= esc_attr__('Map Defaults','lmm');
		$this->panes['geocoding'] 		= esc_attr__('Geocoding','lmm');
		$this->panes['basemaps'] 		= esc_attr__('Basemaps','lmm');
		$this->panes['overlays']		= esc_attr__('Overlays','lmm');
		$this->panes['wms']				= esc_attr__('WMS','lmm');
		$this->panes['directions']		= esc_attr__('Directions','lmm');
		$this->panes['ar']				= esc_attr__('Augmented-Reality','lmm');
		$this->panes['misc']			= esc_attr__('Misc','lmm');
		$this->panes['reset']			= esc_attr__('Reset','lmm');

		$this->sections['mapdefaults-marker_map_defaults']		= esc_attr__('Default values for new marker maps','lmm');
		$this->sections['mapdefaults-marker_icons']		= esc_attr__('Default values for marker icons','lmm');
		$this->sections['mapdefaults-marker_popups']		= esc_attr__('Default values for marker popups','lmm') . '</li><li>&nbsp;';

		$this->sections['mapdefaults-layer_map_defaults']		= esc_attr__('Default values for new layer maps','lmm');
		$this->sections['mapdefaults-list_of_markers']		= esc_attr__('List of markers settings','lmm');
		$this->sections['mapdefaults-filtering']	= esc_attr__('Filter settings for multi-layer-maps','lmm');
		$this->sections['mapdefaults-clustering']	= esc_attr__('Marker clustering settings','lmm') . '</li><li>&nbsp;';

		$this->sections['mapdefaults-interaction']		= esc_attr__('Interaction options','lmm');
		$this->sections['mapdefaults-control']	= esc_attr__('Control options','lmm');
		$this->sections['mapdefaults-minimap']	= esc_attr__('Minimap settings','lmm');
		$this->sections['mapdefaults-gpx']	= esc_attr__('GPX tracks settings','lmm');
		$this->sections['mapdefaults-geolocate']	= esc_attr__('Geolocate settings','lmm');
		$this->sections['mapdefaults-mobile_web_app']	= esc_attr__('Mobile web app settings','lmm');
		$this->sections['mapdefaults-qr_code']			= esc_attr__('QR code settings','lmm');
		$this->sections['mapdefaults-marker_tooltip']			= esc_attr__('Marker tooltip settings','lmm');

		$this->sections['geocoding-general_settings']		= esc_attr__('Geocoding provider','lmm') . '</li><li>&nbsp;';

		$this->sections['geocoding-algolia']		= esc_attr__('Algolia Places settings','lmm');
		$this->sections['geocoding-photon']		= esc_attr__('Photon@MapsMarker settings','lmm') . '</li><li>&nbsp;';

        $this->sections['geocoding-mapzen']		= '<span style="float:left;margin-right:5px;margin-top:-2px;"><img src="'. LEAFLET_PLUGIN_URL .'inc/img/icon-good-mark.png" width="23" height="23" /></span>' . esc_attr__('Mapzen Search settings','lmm');
		$this->sections['geocoding-mapquest']		= esc_attr__('MapQuest Geocoding settings','lmm');
		$this->sections['geocoding-google']		= esc_attr__('Google Geocoding settings','lmm');

		$this->sections['basemaps-default_basemap']		= esc_attr__('Default basemap for new markers/layers','lmm');
		$this->sections['basemaps-available_basemaps_controlbox']		= esc_attr__('Available basemaps in control box','lmm');
		$this->sections['basemaps-basemap_names']		= esc_attr__('Names for basemaps in control box','lmm');
		$this->sections['basemaps-basemap_global_settings']		= esc_attr__('Global basemap settings','lmm') . '</li><li>&nbsp;';

		$this->sections['basemaps-openstreetmap']		= 'OpenStreetMap';
		$this->sections['basemaps-stamen_maps']		= esc_attr__( 'Stamen Maps', 'lmm' );
		$this->sections['basemaps-mapquest']		= 'MapQuest</li><li>&nbsp;';

		$this->sections['basemaps-google_js_api']		= esc_attr__('Google Maps JavaScript API','lmm');
		$this->sections['basemaps-google_localization']		= esc_attr__('Google language localization','lmm');
		$this->sections['basemaps-google_base_domain']		= esc_attr__('Google Maps base domain','lmm');
		$this->sections['basemaps-google_styling']		= esc_attr__('Google Maps styling','lmm') . '</li><li>&nbsp;';

		$this->sections['basemaps-bing']		= esc_attr__('Bing Maps','lmm');
		$this->sections['basemaps-basemap_at']		= 'basemap.at';
		$this->sections['basemaps-mapbox1']		= 'MapBox 1';
		$this->sections['basemaps-mapbox2']		= 'MapBox 2';
		$this->sections['basemaps-mapbox3']		= 'MapBox 3' . '</li><li>&nbsp;';

		$this->sections['basemaps-custom_basemap1']		= sprintf(esc_attr__('Custom basemap %1$s','lmm'), '1');
		$this->sections['basemaps-custom_basemap2']		= sprintf(esc_attr__('Custom basemap %1$s','lmm'), '2');
		$this->sections['basemaps-custom_basemap3']		= sprintf(esc_attr__('Custom basemap %1$s','lmm'), '3');
		$this->sections['basemaps-empty_basemap']		= esc_attr__('Empty basemap','lmm');

		$this->sections['overlays-available_overlays']		= esc_attr__('Available overlays for new markers/layers','lmm') . '</li><li>&nbsp;';
		$this->sections['overlays-custom_overlay1']		= esc_attr__('Custom overlay settings','lmm');
		$this->sections['overlays-custom_overlay2']		= esc_attr__('Custom overlay 2 settings','lmm');
		$this->sections['overlays-custom_overlay3']		= esc_attr__('Custom overlay 3 settings','lmm');
		$this->sections['overlays-custom_overlay4']		= esc_attr__('Custom overlay 4 settings','lmm');

		$this->sections['wms-available_wms']			= esc_attr__('Available WMS layers for new markers/layers','lmm') . '</li><li>&nbsp;';
		$this->sections['wms-wms1']			= esc_attr__('WMS layer 1 settings','lmm');
		$this->sections['wms-wms2']			= esc_attr__('WMS layer 2 settings','lmm');
		$this->sections['wms-wms3']			= esc_attr__('WMS layer 3 settings','lmm');
		$this->sections['wms-wms4']			= esc_attr__('WMS layer 4 settings','lmm');
		$this->sections['wms-wms5']			= esc_attr__('WMS layer 5 settings','lmm');
		$this->sections['wms-wms6']			= esc_attr__('WMS layer 6 settings','lmm');
		$this->sections['wms-wms7']			= esc_attr__('WMS layer 7 settings','lmm');
		$this->sections['wms-wms8']			= esc_attr__('WMS layer 8 settings','lmm');
		$this->sections['wms-wms9']			= esc_attr__('WMS layer 9 settings','lmm');
		$this->sections['wms-wms10']			= esc_attr__('WMS layer 10 settings','lmm');

		$this->sections['directions-general_settings']		= esc_attr__('General directions settings','lmm') . '</li><li>&nbsp;';
		$this->sections['directions-google_maps']		= esc_attr__('Google Maps directions','lmm');
		$this->sections['directions-yournavigation']		= 'yournavigation.org';
		$this->sections['directions-openrouteservice']		= 'openrouteservice.org';

		$this->sections['ar-wikitude']				= esc_attr__('Wikitude settings','lmm');

		$this->sections['misc-general_settings']			= esc_attr__('General settings','lmm');
		$this->sections['misc-interface_language']			= esc_attr__('Interface language settings','lmm');
		$this->sections['misc-list_all_markers']			= esc_attr__('"List all markers" page settings','lmm');
		$this->sections['misc-list_all_layers']			= esc_attr__('"List all layers" page settings','lmm');
		$this->sections['misc-web_api']			= esc_attr__('Web API settings','lmm');
		$this->sections['misc-permissions']			= esc_attr__('Permission settings','lmm');
		$this->sections['misc-xml_sitemap']			= esc_attr__('XML sitemaps integration','lmm');
		$this->sections['misc-compatibility']			= esc_attr__('Compatibility settings','lmm');
		$this->sections['misc-wordpress_integration']			= esc_attr__('WordPress integration settings','lmm');

		$this->sections['reset-reset_settings']			= esc_attr__('Reset Settings','lmm');

		add_action( 'admin_init', array( &$this, 'register_settings' ) );

		if ( ! get_option( 'leafletmapsmarker_options' ) )
			$this->initialize_settings();
	}
	/**
	 * Create settings field
	 *
	 * @since 1.0
	 */
	public function create_setting( $args = array() ) {

		$defaults = array(
			'id'      => 'default_field',
			'version' => '',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-section1',
			'title'   => __( 'Default Field','lmm' ),
			'desc'    => __( 'This is a default description.','lmm' ),
			'std'     => '',
			'type'    => 'text',
			'choices' => array(),
			'class'   => ''
		);

		extract( wp_parse_args( $args, $defaults ) );

		$field_args = array(
			'type'      => $type,
			'id'        => $id,
			'desc'      => $desc,
			'std'       => $std,
			'choices'   => $choices,
			'label_for' => $id,
			'class'     => $class
		);

		if ( $type == 'checkbox' )
			$this->checkboxes[] = $id;

		add_settings_field( $id, $title, array( $this, 'display_setting' ), 'leafletmapsmarker_settings', $section, $field_args );
	}

	/**
	 * Display options page
	 *
	 * @since 1.0
	 */
	public function display_page() {

		include(LEAFLET_PLUGIN_DIR . 'inc' . DIRECTORY_SEPARATOR . 'admin-header.php');
		if ( isset( $_GET['settings-updated'] ) )
			echo '<div style="margin:15px 0;clear:both;" class="notice notice-success is-dismissible"><p>' . __( 'Settings have been successfully updated!','lmm' ) . '</p></div>';

		echo '<h2 style="padding-top:15px;float:left;">'.__('Settings','lmm').'</h2><div class="wrap lmmsettings" style="clear:both;">';
		echo '<form action="options.php" method="post">';
		settings_fields( 'leafletmapsmarker_options' );
		echo '<div class="lmm-ui-tabs tabs-top tabbable tabs-top">
			<ul class="lmm-ui-tabs-nav-top ul-outter tabs" id="lmm-admin-top-tabs">';

		$section_index = 0;
		foreach ( $this->panes as $pane_slug => $pane ) {
			$li_class = 0 === $section_index ? ' class="active"' : '';
			$section_index++;

			echo '<li' . $li_class . '><a href="#' . $pane_slug . '" data-toggle="tab">' . $pane . '</a></li>';
		}

		echo '</ul>';
		echo '<div class="tab-content">';

		$panel_index = 0;
        foreach($this->panes as $pane_slug => $pane){
	        $li_class = 0 === $panel_index ? ' in active' : '';
	        $panel_index++;

            echo '<div id = '.$pane_slug.' class="lmm-ui-tabs tabs-left tab-pane lmm-fade' . $li_class . '">';
	        echo '<div class="tabbable tabs-left">';
            echo '<ul class="lmm-ui-tabs-nav lmm-ui-tabs-navleft tabs lmm-admin-navleft-tabs">';
            $sections = array();
	        $sub_panel_index = 0;
            foreach ( $this->sections as $key => $row ){
	            $k = explode("-",$key);
	            if ( $k[0] == $pane_slug ) {
		            $li_class = 0 === $sub_panel_index ? ' class="active"' : '';
		            $sub_panel_index ++;
		            echo '<li' . $li_class . '><a href="#' . $key . '" data-toggle="tab">' . $row . '</a></li>';
		            $sections[] = $key;
	            }
            }
            echo '</ul>';
	        echo '<div class="tab-content">';

	        $sub_panel_index = 0;
                foreach($sections as $slug => $section){
	                $li_class = 0 === $sub_panel_index ? ' in active' : '';
	                $sub_panel_index++;
                    echo '<div class="section tab-pane lmm-fade' . $li_class . '" id="' . $section. '">';
                    echo "<h3 class='h3-lmm-settings'>".strip_tags($this->sections[$section])."</h3>"; //info: strip_tags for removing <br/> dividers
                    if (function_exists('display_'.$pane_slug.'_section')) { //info: Phalanger fix
                    	@call_user_func(array(&$this, 'display_'.$pane_slug.'_section'), array());
                    }
                    echo '<table class="form-table">';
                        do_settings_fields( $_GET['page'], $section );
                    echo '</table>';
                    echo '</div>';
                }

            echo '</div>'; // tab-content
            echo '</div>'; // tabs-left
            echo '</div>'; // tab-pane
        }
		echo '</div>'; // tab-content
		echo '</div>'; // tabs-top
		echo '<p class="submit"><input id="submit" name="Submit" type="submit" class="button-primary" value="' . __( 'Save Changes','lmm' ) . '" /></p>

	</form>';

	include(LEAFLET_PLUGIN_DIR . 'inc' . DIRECTORY_SEPARATOR . 'admin-footer.php');

	echo '<script type="text/javascript">
		jQuery(document).ready(function($) {
			var panes = [];';

			foreach ( $this->sections as $pane_slug => $pane )
				echo "panes['$pane'] = '$pane_slug';";
			echo '
			$("input[type=text], textarea").each(function() {
				if ($(this).val() == $(this).attr("placeholder") || $(this).val() == "")
					$(this).css("color", "#999");
			});

			$("input[type=text], textarea").focus(function() {
				if ($(this).val() == $(this).attr("placeholder") || $(this).val() == "") {
					//$(this).val("");
					$(this).css("color", "#000");
				}
			}).blur(function() {
				if ($(this).val() == "" || $(this).val() == $(this).attr("placeholder")) {
					$(this).val($(this).attr("placeholder"));
					$(this).css("color", "#999");
				}
			});

			$(".lmmsettings h3, .lmmsettings table").show();

			//info:  This will make the "warning" checkbox class really stand out when checked.
			$(".warning").change(function() {
				if ($(this).is(":checked"))
					$(this).parent().css("background", "#c00").css("color", "#fff").css("fontWeight", "bold");
				else
					$(this).parent().css("background", "none").css("color", "inherit").css("fontWeight", "normal");
			});

			//info:  Browser compatibility
			if ($.browser.mozilla)
			         $("form").attr("autocomplete", "off");

			//info: warn on unsaved changes when leaving page
			var unsaved = false;
			$(":input").change(function(){
				unsaved = true;
			});
			$("#submit, #s2id_lmm-select-search-tabs").click(function() {
				unsaved = false;
			});
			function unloadPage(){
				if(unsaved){
					return "' . esc_attr__('You have unsaved changes on this page. Do you want to leave this page and discard your changes or stay on this page?','lmm') . '";
				}
			}
			window.onbeforeunload = unloadPage;
		});
	</script></div>';
		?>
	<?php
	}

	/**
	 * HTML output for text field
	 */
	public function display_setting( $args = array() ) {

		extract( $args );

		$options = get_option( 'leafletmapsmarker_options' );

		if ( ! isset( $options[$id] ) && $type != 'checkbox' )
			$options[$id] = $std;
		elseif ( ! isset( $options[$id] ) )
			$options[$id] = 0;

		$field_class = '';
		if ( $class != '' )
			$field_class = ' ' . $class;

		switch ( $type ) {

			case 'heading':
				echo '</td></tr><tr valign="top"><td colspan="2" rowspan="2"><h4 class="h4-lmm-settings">' . $desc . '</h4>';
				break;

			case 'helptext':
				echo '</td></tr><tr valign="top"><td colspan="2">' . $desc . '';
				break;

			case 'helptext-twocolumn':
				echo $desc;
				break;

			case 'checkbox':
				echo '<input class="checkbox' . $field_class . '" type="checkbox" id="' . $id . '" name="leafletmapsmarker_options[' . $id . ']" value="1" ' . checked( $options[$id], 1, false ) . ' /> <label for="' . $id . '">' . $desc . '</label>';
				break;

			case 'checkbox-pro':
				echo '<input class="checkbox' . $field_class . '" type="checkbox" id="' . $id . '" name="leafletmapsmarker_options[' . $id . ']" value="1" ' . checked( $options[$id], 1, false ) . ' disabled="disabled" /> <label for="' . $id . '">' . $desc . '</label>';
				break;

			case 'checkbox-readonly':
				echo '<input class="checkbox' . $field_class . '" type="checkbox" id="' . $id . '" name="leafletmapsmarker_options[' . $id . ']" value="1" ' . checked( $options[$id], 1, false ) . ' disabled="disabled" /> <label for="' . $id . '">' . $desc . '</label>';
				break;

			case 'select':
				echo '<select class="select' . $field_class . '" name="leafletmapsmarker_options[' . $id . ']">';
				foreach ( $choices as $value => $label )
					echo '<option value="' . esc_attr( $value ) . '"' . selected( $options[$id], $value, false ) . '>' . $label . '</option>';
				echo '</select>';
				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';
				break;

			case 'select-pro':
				echo '<select class="select' . $field_class . '" name="leafletmapsmarker_options[' . $id . ']">';
				foreach ( $choices as $value => $label )
					echo '<option value="' . esc_attr( $value ) . '"' . selected( $options[$id], $value, false ) . ' disabled="disabled">' . $label . '</option>';
				echo '</select>';
				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';
				break;

			case 'radio':
				$i = 0;
				echo '<ul>';
				foreach ( $choices as $value => $label ) {
					echo '<li><input class="radio' . $field_class . '" type="radio" name="leafletmapsmarker_options[' . $id . ']" id="' . $id . $i . '" value="' . esc_attr( $value ) . '" ' . checked( $options[$id], $value, false ) . '> <label for="' . $id . $i . '">' . $label . '</label></li>';
					$i++;
				}
				if ( $desc != '' )
					echo '<span class="description">' . $desc . '</span>';
				break;

			case 'radio-reverse':
				if ( $desc != '' )
					echo '<span class="description">' . $desc . '</span><br/>';
				$i = 0;
				echo '<ul>';
				foreach ( $choices as $value => $label ) {
					echo '<li><input class="radio' . $field_class . '" type="radio" name="leafletmapsmarker_options[' . $id . ']" id="' . $id . $i . '" value="' . esc_attr( $value ) . '" ' . checked( $options[$id], $value, false ) . '> <label for="' . $id . $i . '">' . $label . '</label></li>';
					$i++;
				}
				echo '</ul>';
				break;

			case 'radio-pro':
				$i = 0;
				echo '<ul>';
				foreach ( $choices as $value => $label ) {
					echo '<li><input class="radio' . $field_class . '" type="radio" name="leafletmapsmarker_options[' . $id . ']" id="' . $id . $i . '" value="' . esc_attr( $value ) . '" ' . checked( $options[$id], $value, false ) . ' disabled="disabled"> <label for="' . $id . $i . '">' . $label . '</label></li>';
					$i++;
				}
				echo '</ul>';
				if ( $desc != '' )
					echo '<span class="description">' . $desc . '</span>';
				break;

			case 'radio-reverse-pro':
				if ( $desc != '' )
					echo '<span class="description">' . $desc . '</span><br/>';
				$i = 0;
				echo '<ul>';
				foreach ( $choices as $value => $label ) {
					echo '<li><input class="radio' . $field_class . '" type="radio" name="leafletmapsmarker_options[' . $id . ']" id="' . $id . $i . '" value="' . esc_attr( $value ) . '" ' . checked( $options[$id], $value, false ) . ' disabled="disabled"> <label for="' . $id . $i . '">' . $label . '</label></li>';
					$i++;
				}
				echo '</ul>';
				break;

			case 'textarea':
				echo '<textarea class="' . $field_class . '" id="' . $id . '" name="leafletmapsmarker_options[' . $id . ']" placeholder="' . $std . '" rows="5" cols="30">' . wp_htmledit_pre( $options[$id] ) . '</textarea>';

				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';
				break;

			case 'textarea-pro':
				echo '<textarea class="' . $field_class . '" id="' . $id . '" name="leafletmapsmarker_options[' . $id . ']" placeholder="' . $std . '" rows="5" cols="30" disabled="disabled">' . wp_htmledit_pre( $options[$id] ) . '</textarea>';

				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';
				break;

			case 'password':
				echo '<input class="regular-text' . $field_class . '" type="password" id="' . $id . '" name="leafletmapsmarker_options[' . $id . ']" value="' . esc_attr( $options[$id] ) . '" />';
				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';
				break;

			case 'text':
			default:
		 		echo '<input class="regular-text' . $field_class . '" style="width:30em;" type="text" id="' . $id . '" name="leafletmapsmarker_options[' . $id . ']" placeholder="' . $std . '" value="' . esc_attr( $options[$id] ) . '" />';
		 		if ( $desc != '' )
		 			echo '<br /><span class="description">' . $desc . '</span>';
		 		break;

			case 'text-reverse':
		 		if ( $desc != '' )
		 			echo '<span class="description">' . $desc . '</span><br />';
				echo '<input class="regular-text' . $field_class . '" style="width:30em;" type="text" id="' . $id . '" name="leafletmapsmarker_options[' . $id . ']" placeholder="' . $std . '" value="' . esc_attr( $options[$id] ) . '" />';
		 		break;

			case 'text-pro':
		 		echo '<input class="regular-text' . $field_class . '" style="width:30em;" type="text" id="' . $id . '" name="leafletmapsmarker_options[' . $id . ']" placeholder="' . $std . '" value="' . esc_attr( $options[$id] ) . '" disabled="disabled" />';
		 		if ( $desc != '' )
		 			echo '<br /><span class="description">' . $desc . '</span>';
		 		break;

			case 'text-reverse-pro':
		 		if ( $desc != '' )
		 			echo '<span class="description">' . $desc . '</span><br/>';
		 		echo '<input class="regular-text' . $field_class . '" style="width:30em;" type="text" id="' . $id . '" name="leafletmapsmarker_options[' . $id . ']" placeholder="' . $std . '" value="' . esc_attr( $options[$id] ) . '" disabled="disabled" />';
		 		break;

			case 'text-readonly':
		 		echo '<input readonly="readonly" class="regular-text' . $field_class . '" style="width:60em;" type="text" id="' . $id . '" name="leafletmapsmarker_options[' . $id . ']" placeholder="' . $std . '" value="' . esc_attr( $options[$id] ) . '" />';
	 		if ( $desc != '' )
		 			echo '<br /><span class="description">' . $desc . '</span>';
		 		break;

			case 'text-deletable':
		 		echo '<input class="regular-text' . $field_class . '" style="width:60em;" type="text" id="' . $id . '" name="leafletmapsmarker_options[' . $id . ']" value="' . esc_attr( $options[$id] ) . '" />';
	 		if ( $desc != '' )
		 			echo '<br /><span class="description">' . $desc . '</span>';
		 		break;
		}
	}

	/**
	 * Settings and defaults
	 */
	public function get_settings() {
		if ( ! empty( $this->_settings ) )
			return;

		$pro_button_link = '<br/><a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade" title="' . esc_attr__('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-pro-option.png" width="65" height="15" /></a>';
		$pro_button_link_inline = ' <a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade" title="' . esc_attr__('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-pro-option.png" width="65" height="15" style="display:inline;" /></a>';
		$pro_feature_link = ' <a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade" title="' . esc_attr__('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-pro-feature.png" width="70" height="15" /></a>';

		/*===========================================
		*
		*
		* pane basemaps
		*
		*
		===========================================*/
		/*
		* Default values for new marker maps
		*/
		$this->_settings['defaults_marker_helptext1'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'std'     => '',
			'title'   => '',
			'desc'    => __( 'Will be used when creating a new marker. All values can be changed afterwards on each marker.', 'lmm') . '<br/>' . __('The following screenshot was taken with the advanced editor enabled:','lmm') . '<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-marker-defaults.jpg" width="650" height="593" />',
			'type'    => 'helptext'
		);
		$this->_settings['defaults_marker_lat'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => __( 'Latitude', 'lmm' ),
			'desc'    => '',
			'std'     => '48.216038',
			'type'    => 'text'
		);
		$this->_settings['defaults_marker_lon'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => __( 'Longitude', 'lmm' ),
			'desc'    => '',
			'std'     => '16.378984',
			'type'    => 'text'
		);
		$this->_settings['defaults_marker_zoom'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => __( 'Zoom', 'lmm' ),
			'desc'    => '',
			'std'     => '11',
			'type'    => 'text'
		);
		$this->_settings['defaults_marker_mapwidth'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => __( 'Map width', 'lmm' ),
			'desc'    => '',
			'std'     => '640',
			'type'    => 'text'
		);
		$this->_settings['defaults_marker_mapwidthunit'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => __('Map width unit','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'px',
			'choices' => array(
				'px' => 'px',
				'%' => '%'
			)
		);
		$this->_settings['defaults_marker_mapheight'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => __( 'Map height', 'lmm' ) . ' (px)',
			'desc'    => '',
			'std'     => '480',
			'type'    => 'text'
		);
		$this->_settings['defaults_marker_default_layer'] = array(
			'version' => '3.4',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => __( 'Default layer ID', 'lmm' ),
			'desc'    => __('Set to 0 if you do not want to assign new markers to a layer','lmm'),
			'std'     => '0',
			'type'    => 'text'
		);
		$this->_settings['defaults_marker_openpopup'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => __('Open popup by default','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '0',
			'choices' => array(
				'0' => __('disabled','lmm'),
				'1' => __('enabled','lmm')
			)
		);
		$this->_settings['defaults_marker_controlbox'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => __('Controlbox for basemaps/overlays','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '1',
			'choices' => array(
				'0' => __('hidden','lmm'),
				'1' => __('collapsed','lmm'),
				'2' => __('expanded','lmm')
			)
		);
		// defaults_marker - which overlays are active by default?
		$this->_settings['defaults_marker_overlays_custom_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'    => __('Checked overlays in control box','lmm'),
			'desc'    => __('Custom overlay','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_marker_overlays_custom2_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => '',
			'desc'    => __('Custom overlay 2','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);

		$this->_settings['defaults_marker_overlays_custom3_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => '',
			'desc'    => __('Custom overlay 3','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_marker_overlays_custom4_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => '',
			'desc'    => __('Custom overlay 4','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_marker_panel'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => __('Panel for displaying marker name and API URLs on top of map','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '1',
			'choices' => array(
				'1' => __('show','lmm'),
				'0' => __('hide','lmm'),
			)
		);
		// defaults_marker - active API links in panel
		$this->_settings['defaults_marker_panel_directions'] = array(
			'version' => '1.4',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'    => __('Visible API links in panel','lmm'),
			'desc'    => __('Directions','lmm') .  ' <img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-car.png" width="14" height="14" />',
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['defaults_marker_panel_kml'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'    => '',
			'desc'    => 'KML <img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-kml.png" width="14" height="14" />',
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['defaults_marker_panel_fullscreen'] = array(
			'version' => '1.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'    => '',
			'desc'    => __('Fullscreen','lmm') .  ' <img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-fullscreen.png" width="14" height="14" />',
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['defaults_marker_panel_qr_code'] = array(
			'version' => '1.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'    => '',
			'desc'    => __('QR code','lmm') .  ' <img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-qr-code.png" width="14" height="14" />',
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['defaults_marker_panel_geojson'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => '',
			'desc'    => 'GeoJSON <img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-json.png" width="14" height="14" />',
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['defaults_marker_panel_georss'] = array(
			'version' => '1.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => '',
			'desc'    => 'GeoRSS <img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-georss.png" width="14" height="14" />',
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['defaults_marker_panel_wikitude'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => '',
			'desc'    => 'Wikitude <img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-wikitude.png" width="14" height="14" />',
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_marker_panel_background_color'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => __( 'Panel background color', 'lmm' ),
			'desc'    => __('Please use hexadecimal color values','lmm'),
			'std'     => '#efefef',
			'type'    => 'text'
		);
		$this->_settings['defaults_marker_panel_paneltext_css'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => __( 'Panel text css', 'lmm' ),
			'desc'    => '',
			'std'     => 'font-weight:bold;color:#373737;',
			'type'    => 'text'
		);
		// defaults_marker - which WMS layers are active by default?
		$this->_settings['defaults_marker_wms_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'    => __('Checked WMS layers','lmm'),
			'desc'    => __('WMS 1','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_marker_wms2_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => '',
			'desc'    => __('WMS 2','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_marker_wms3_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => '',
			'desc'    => __('WMS 3','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_marker_wms4_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => '',
			'desc'    => __('WMS 4','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_marker_wms5_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => '',
			'desc'    => __('WMS 5','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_marker_wms6_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => '',
			'desc'    => __('WMS 6','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_marker_wms7_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => '',
			'desc'    => __('WMS 7','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_marker_wms8_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => '',
			'desc'    => __('WMS 8','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_marker_wms9_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => '',
			'desc'    => __('WMS 9','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_marker_wms10_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => '',
			'desc'    => __('WMS 10','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		/* Default values for markers added directly */
		$this->_settings['defaults_marker_shortcode_helptext'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'std'     => '',
			'title'   => '<h4 class="h4-lmm-settings">' . __('Default values for markers added directly','lmm') . '</h4>',
			'desc'    => __( 'You can also add markers directly to posts or pages without having to save them to your database previously. You just have to use the shortcode with the attributes mlat and mlon (e.g. <strong>[mapsmarker mlat="48.216038" mlon="16.378984"]</strong>).', 'lmm') . ' ' . sprintf(__('For more information please visit the Shortcode API docs at %1$s','lmm'), '<a href="https://www.mapsmarker.com/shortcode-api" target="_blank">https://www.mapsmarker.com/shortcode-api</a>') . '<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-marker-direct.jpg" width="408" height="80" /><br/><br/>' . __('Defaults values for markers added directly:','lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['defaults_marker_shortcode_basemap'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => __('Default basemap','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'osm_mapnik',
			'choices' => array(
				'osm_mapnik' => 'OpenStreetMap',
				'mapquest_osm' => 'MapQuest (OSM) - <strong>' . __('API key required!','lmm') . '</strong> <a href="https://www.mapsmarker.com/mapquest-api-key" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
				'mapquest_aerial' => 'MapQuest (Aerial) - <strong>' . __('API key required!','lmm') . '</strong> <a href="https://www.mapsmarker.com/mapquest-api-key" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
				'mapquest_hybrid' => 'MapQuest (Hybrid) - <strong>' . __('API key required!','lmm') . '</strong> <a href="https://www.mapsmarker.com/mapquest-api-key" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
				'googleLayer_roadmap' => __('Google Maps (Roadmap)','lmm')  . ' - <strong>' . __('API key required!','lmm') . '</strong> <a href="https://www.mapsmarker.com/google-maps-javascript-api" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
				'googleLayer_satellite' => __('Google Maps (Satellite)','lmm')  . ' - <strong>' . __('API key required!','lmm') . '</strong> <a href="https://www.mapsmarker.com/google-maps-javascript-api" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
				'googleLayer_hybrid' => __('Google Maps (Hybrid)','lmm')  . ' - <strong>' . __('API key required!','lmm') . '</strong> <a href="https://www.mapsmarker.com/google-maps-javascript-api" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
				'googleLayer_terrain' => __('Google Maps (Terrain)','lmm')  . ' - <strong>' . __('API key required!','lmm') . '</strong> <a href="https://www.mapsmarker.com/google-maps-javascript-api" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
				'bingaerial' => __('Bing Maps (Aerial)','lmm') . ' - <strong>' . __('API key required!','lmm') . '</strong> <a href="https://www.mapsmarker.com/bing-maps" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
				'bingaerialwithlabels' => __('Bing Maps (Aerial+Labels)','lmm') . ' - <strong>' . __('API key required!','lmm'). '</strong> <a href="https://www.mapsmarker.com/bing-maps" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
				'bingroad' => __('Bing Maps (Road)','lmm') . ' - <strong>' . __('API key required!','lmm'). '</strong> <a href="https://www.mapsmarker.com/bing-maps" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
				'ogdwien_basemap' => 'basemap.at',
				'ogdwien_satellite' => __('basemap.at (satellite)','lmm'),
				'custom_basemap' => __('Custom basemap','lmm'),
				'custom_basemap2' => __('Custom basemap 2','lmm'),
				'custom_basemap3' => __('Custom basemap 3','lmm')
			)
		);
		$this->_settings['defaults_marker_shortcode_zoom'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => __( 'Zoom', 'lmm' ),
			'desc'    => '',
			'std'     => '11',
			'type'    => 'text'
		);
		$this->_settings['defaults_marker_shortcode_mapwidth'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => __( 'Map width', 'lmm' ),
			'desc'    => '',
			'std'     => '640',
			'type'    => 'text'
		);
		$this->_settings['defaults_marker_shortcode_mapwidthunit'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => __('Map width unit','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'px',
			'choices' => array(
				'px' => 'px',
				'%' => '%'
			)
		);
		$this->_settings['defaults_marker_shortcode_mapheight'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => __( 'Map height', 'lmm' ) . ' (px)',
			'desc'    => '',
			'std'     => '480',
			'type'    => 'text'
		);
		$this->_settings['defaults_marker_shortcode_controlbox'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => __('Controlbox for basemaps/overlays','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '1',
			'choices' => array(
				'0' => __('hidden','lmm'),
				'1' => __('collapsed','lmm'),
				'2' => __('expanded','lmm')
			)
		);
		// defaults_marker - which overlays are active by default?
		$this->_settings['defaults_marker_shortcode_overlays_custom_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'    => __('Checked overlays in control box','lmm'),
			'desc'    => __('Custom overlay','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_marker_shortcode_overlays_custom2_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => '',
			'desc'    => __('Custom overlay 2','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);

		$this->_settings['defaults_marker_shortcode_overlays_custom3_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => '',
			'desc'    => __('Custom overlay 3','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);

		$this->_settings['defaults_marker_shortcode_overlays_custom4_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => '',
			'desc'    => __('Custom overlay 4','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		// defaults_marker shortcode - which WMS layers are active by default?
		$this->_settings['defaults_marker_shortcode_wms_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'    => __('Checked WMS layers','lmm'),
			'desc'    => __('WMS 1','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_marker_shortcode_wms2_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => '',
			'desc'    => __('WMS 2','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_marker_shortcode_wms3_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => '',
			'desc'    => __('WMS 3','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_marker_shortcode_wms4_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => '',
			'desc'    => __('WMS 4','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_marker_shortcode_wms5_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => '',
			'desc'    => __('WMS 5','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_marker_shortcode_wms6_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => '',
			'desc'    => __('WMS 6','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_marker_shortcode_wms7_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => '',
			'desc'    => __('WMS 7','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_marker_shortcode_wms8_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => '',
			'desc'    => __('WMS 8','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_marker_shortcode_wms9_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => '',
			'desc'    => __('WMS 9','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_marker_shortcode_wms10_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_map_defaults',
			'title'   => '',
			'desc'    => __('WMS 10','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);

		/*
		* Default values for marker icons
		*/
		$this->_settings['defaults_marker_icon_helptext1'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_icons',
			'std'     => '',
			'title'   => '',
			'desc'    => '',
			'type'    => 'helptext'
		);
		$this->_settings['defaults_marker_custom_icon_url_dir'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_icons',
			'title'   => __('Use custom icon URL and directory','lmm') . $pro_button_link,
			'desc'    => __('If set to yes, please be aware that the pro settings below have to be changed when you move your WordPress installation to another server for example!','lmm') . '<a style="background:#f99755;display:block;padding:3px;text-decoration:none;color:#2702c6;width:635px;margin:10px 0;" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade">' . __('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm') . '</a>',
			'type'    => 'radio-pro',
			'std'     => 'no',
			'choices' => array(
				'no' => __('no','lmm'),
				'yes' => __('yes','lmm'),
			)
		);
		$this->_settings['defaults_marker_icon_url'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_icons',
			'title'   => __( 'Custom icons URL', 'lmm' ) . $pro_button_link,
			'desc'    => __( 'If the option above is set to yes, icons will automatically be loaded from this URL. If the option above is set to no, the following marker icons url will be used:','lmm') . '<br/><strong>' . LEAFLET_PLUGIN_ICONS_URL . '</strong>',
			'std'     => __('Custom directories can be set in the pro version only!','lmm'),
			'type'    => 'text-pro'
		);
		$this->_settings['defaults_marker_icon_dir'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_icons',
			'title'   => __( 'Custom icons directory', 'lmm' ) . $pro_button_link,
			'desc'    => __( 'If option above is set to yes, the directory on server where icons are stored will be used (needed for backend only). If the option above is set to no, the following marker icons directory will be used:','lmm') . '<br/><strong>' . LEAFLET_PLUGIN_ICONS_DIR . '</strong>',
			'std'     => __('Custom directories can be set in the pro version only!','lmm'),
			'type'    => 'text-pro'
		);
		$this->_settings['defaults_marker_icon'] = array(
			'version' => '1.8',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_icons',
			'title'   => __( 'Default icon', 'lmm' ),
			'desc'    => sprintf(__( 'If you want to use another icon than the blue pin (%s), please enter the file name of the icon in the form field - e.g. smiley_happy.png', 'lmm' ),'<img src="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png" width="32" height="37" />'),
			'std'     => '',
			'type'    => 'text'
		);
		$this->_settings['defaults_marker_icon_shadow_url_status'] = array(
			'version' => '3.5.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_icons',
			'title'   => '<br/>' . __('Marker shadow','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'default',
			'choices' => array(
				'default' => __('use default shadow','lmm') . ' (' . LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker-shadow.png, ' . __('preview','lmm') . ': <img src="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker-shadow.png" width="51" height="37" />)',
				'custom' => __('use custom shadow (please enter URL below)','lmm')
			)
		);
		$this->_settings['defaults_marker_icon_shadow_url'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_icons',
			'title'   => __( 'Custom marker shadow URL', 'lmm' ),
			'desc'    => __( 'The URL to the custom icons shadow image. If empty, no shadow image will be used (even if the option above is set to default).', 'lmm' ) . '',
			'std'     => LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker-shadow.png',
			'type'    => 'text-deletable'
		);
		$this->_settings['defaults_marker_icon_title'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_icons',
			'title'   => __('Native marker tooltip','lmm') . '<br/><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/help-marker-title.jpg" width="79" height="40" />',
			'desc'    => __('Show marker name for the browser tooltip that appear on marker hover (tooltip is always hidden if marker name is empty).','lmm'),
			'type'    => 'radio',
			'std'     => 'show',
			'choices' => array(
				'show' => __('show','lmm'),
				'hide' => __('hide','lmm')
			)
		);
		$this->_settings['defaults_marker_icon_opacity'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_icons',
			'title'   => __( 'Opacity', 'lmm' ),
			'desc'    => __( 'The opacity of the markers.', 'lmm' ),
			'std'     => '1.0',
			'type'    => 'text'
		);
		$this->_settings['defaults_marker_icon_helptext2'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_icons',
			'std'     => '',
			'title'   => '',
			'desc'    => '<strong>' . sprintf(__('Only change the values below if you are not using marker or shadow icons from the <a href="%1$s" target="_blank">Map Icons Collection</a>!','lmm'), 'https://mapicons.mapsmarker.com') . '</strong>',
			'type'    => 'helptext'
		);
		$this->_settings['defaults_marker_icon_iconsize_x'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_icons',
			'title'   => __( 'Icon size', 'lmm' ) . ' (x)',
			'desc'    => __( 'Width of the icons in pixel', 'lmm' ),
			'std'     => '32',
			'type'    => 'text'
		);
		$this->_settings['defaults_marker_icon_iconsize_y'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_icons',
			'title'   => __( 'Icon size', 'lmm' ) . ' (y)',
			'desc'    => __( 'Height of the icons in pixel', 'lmm' ),
			'std'     => '37',
			'type'    => 'text'
		);
		$this->_settings['defaults_marker_icon_iconanchor_x'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_icons',
			'title'   => __( 'Icon anchor', 'lmm' ) . ' (x)',
			'desc'    => __( 'The x-coordinates of the "tip" of the icons (relative to its top left corner).', 'lmm' ),
			'std'     => '17',
			'type'    => 'text'
		);
		$this->_settings['defaults_marker_icon_iconanchor_y'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_icons',
			'title'   => __( 'Icon anchor', 'lmm' ) . ' (y)',
			'desc'    => __( 'The y-coordinates of the "tip" of the icons (relative to its top left corner).', 'lmm' ),
			'std'     => '36',
			'type'    => 'text'
		);
		$this->_settings['defaults_marker_icon_popupanchor_x'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_icons',
			'title'   => __( 'Popup anchor', 'lmm' ) . ' (x)',
			'desc'    => __( 'The x-coordinates of the popup anchor (relative to its top left corner)', 'lmm' ),
			'std'     => '-1',
			'type'    => 'text'
		);
		$this->_settings['defaults_marker_icon_popupanchor_y'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_icons',
			'title'   => __( 'Popup anchor', 'lmm' ) . ' (y)',
			'desc'    => __( 'The y-coordinates of the popup anchor (relative to its top left corner)', 'lmm' ),
			'std'     => '-32',
			'type'    => 'text'
		);
		$this->_settings['defaults_marker_icon_shadowsize_x'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_icons',
			'title'   => __( 'Shadow size', 'lmm' ) . ' (x)',
			'desc'    => __( 'Width of the shadow icon in pixel', 'lmm' ),
			'std'     => '41',
			'type'    => 'text'
		);
		$this->_settings['defaults_marker_icon_shadowsize_y'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_icons',
			'title'   => __( 'Shadow size', 'lmm' ) . ' (y)',
			'desc'    => __( 'Height of the shadow icon in pixel', 'lmm' ),
			'std'     => '41',
			'type'    => 'text'
		);
		$this->_settings['defaults_marker_icon_shadowanchor_x'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_icons',
			'title'   => __( 'Shadow anchor', 'lmm' ) . ' (x)',
			'desc'    => __( 'The x-coordinates of the "tip" of the shadow icon (relative to its top left corner)', 'lmm' ),
			'std'     => '16',
			'type'    => 'text'
		);
		$this->_settings['defaults_marker_icon_shadowanchor_y'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_icons',
			'title'   => __( 'Shadow anchor', 'lmm' ) . ' (y)',
			'desc'    => __( 'The y-coordinates of the "tip" of the shadow icon (relative to its top left corner)', 'lmm' ),
			'std'     => '43',
			'type'    => 'text'
		);

		/*
		* Default values for marker popups
		*/
		$this->_settings['defaults_marker_popups_helptext1'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_popups',
			'std'     => '',
			'title'   => '',
			'desc'    => '<img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-popup.jpg" width="342" height="168" />',
			'type'    => 'helptext'
		);
		$this->_settings['defaults_marker_popups_maxwidth'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_popups',
			'title'   => 'maxWidth (px)',
			'desc'    => __( 'Maximum width of popups in pixel', 'lmm' ),
			'std'     => '300',
			'type'    => 'text'
		);
		$this->_settings['defaults_marker_popups_minwidth'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_popups',
			'title'   => 'minWidth (px)',
			'desc'    => __( 'Minimum width of popups in pixel', 'lmm' ),
			'std'     => '250',
			'type'    => 'text'
		);
		$this->_settings['defaults_marker_popups_maxheight'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_popups',
			'title'   => 'maxHeight (px)',
			'desc'    => __( 'If set, creates a scrollable container of the given height in pixel inside popups if its content exceeds it.', 'lmm' ),
			'std'     => '160',
			'type'    => 'text-deletable'
		);
		$this->_settings['defaults_marker_popups_image_css'] = array(
			'version' => '3.8.7',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_popups',
			'title'   => __('CSS for images in popups','lmm'),
			'desc'    => __( 'Gets added to .leaflet-popup-content img {...} - use max-width to reduce the image width in popups automatically to the given value in pixel (only if it is wider). The height of the images gets reduced by the according ratio automatically.', 'lmm' ),
			'std'     => 'max-width:234px !important; height:auto; width:auto !important;',
			'type'    => 'text-deletable'
		);
		$this->_settings['defaults_marker_popups_autopan'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_popups',
			'title'   => 'autoPan',
			'desc'    => __('Set it to false if you do not want the map to do panning animation to fit the opened popup.','lmm'),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);
		$this->_settings['defaults_marker_popups_closebutton'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_popups',
			'title'   => 'closeButton',
			'desc'    => __('Controls the presence of a close button in popups.','lmm'),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);
		$this->_settings['defaults_marker_popups_add_markername'] = array(
			'version' => 'p1.5.8',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_popups',
			'title'   => __('add markername to popup','lmm') . $pro_button_link,
			'desc'    => __('If set to true, the marker name gets added to the top of the popup automatically','lmm'),
			'type'    => 'radio-pro',
			'std'     => 'false',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);
		$this->_settings['defaults_marker_popups_autopanpadding_x'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_popups',
			'title'   => 'autoPanPadding (x)',
			'desc'    => __( 'The x-coordinates of the margin between popups and the edges of the map view after autopanning was performed.', 'lmm' ),
			'std'     => '5',
			'type'    => 'text'
		);
		$this->_settings['defaults_marker_popups_autopanpadding_y'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_popups',
			'title'   => 'autoPanPadding (y)',
			'desc'    => __( 'The y-coordinates of the margin between popups and the edges of the map view after autopanning was performed.', 'lmm' ),
			'std'     => '5',
			'type'    => 'text'
		);
		$this->_settings['defaults_marker_popups_center_map'] = array(
			'version' => 'p2.7',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_popups',
			'title'   => __('center map on popup','lmm') . $pro_button_link,
			'desc'    => __('If set to true, the map centers on the popup center instead of the marker when a popup is opened','lmm'),
			'type'    => 'radio-pro',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);
		$this->_settings['defaults_marker_popups_rise_on_hover'] = array(
			'version' => 'p3.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_popups',
			'title'   => __('Open popups on mouse hover instead of mouse click','lmm') . $pro_button_link,
			'desc'    => __('If set to true, popups will open on mouse hover.','lmm'),
			'type'    => 'radio-pro',
			'std'     => 'false',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);

		/*
		* Default values for new layer maps
		*/
		$this->_settings['defaults_layer_helptext1'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-layer_map_defaults',
			'std'     => '',
			'title'   => '',
			'desc'    => __( 'Will be used when creating a new layer. All values can be changed afterwards on each layer.', 'lmm') . '<br/>' . __('The following screenshot was taken with the advanced editor enabled:','lmm') . '<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-layer-defaults.jpg" width="650" height="466" />',
			'type'    => 'helptext'
		);
		$this->_settings['defaults_layer_lat'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-layer_map_defaults',
			'title'   => __( 'Latitude', 'lmm' ),
			'desc'    => '',
			'std'     => '48.216038',
			'type'    => 'text'
		);
		$this->_settings['defaults_layer_lon'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-layer_map_defaults',
			'title'   => __( 'Longitude', 'lmm' ),
			'desc'    => '',
			'std'     => '16.378984',
			'type'    => 'text'
		);
		$this->_settings['defaults_layer_zoom'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-layer_map_defaults',
			'title'   => __( 'Zoom', 'lmm' ),
			'desc'    => '',
			'std'     => '11',
			'type'    => 'text'
		);
		$this->_settings['defaults_layer_mapwidth'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-layer_map_defaults',
			'title'   => __( 'Map width', 'lmm' ),
			'desc'    => '',
			'std'     => '640',
			'type'    => 'text'
		);
		$this->_settings['defaults_layer_mapwidthunit'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-layer_map_defaults',
			'title'   => __('Map width unit','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'px',
			'choices' => array(
				'px' => 'px',
				'%' => '%'
			)
		);
		$this->_settings['defaults_layer_mapheight'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-layer_map_defaults',
			'title'   => __( 'Map height', 'lmm' ) . ' (px)',
			'desc'    => '',
			'std'     => '480',
			'type'    => 'text'
		);
		$this->_settings['defaults_layer_controlbox'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-layer_map_defaults',
			'title'   => __('Controlbox for basemaps/overlays','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '1',
			'choices' => array(
				'0' => __('hidden','lmm'),
				'1' => __('collapsed','lmm'),
				'2' => __('expanded','lmm')
			)
		);
		// defaults_layer - which overlays are active by default?
		$this->_settings['defaults_layer_overlays_custom_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-layer_map_defaults',
			'title'    => __('Checked overlays in control box','lmm'),
			'desc'    => __('Custom overlay','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_layer_overlays_custom2_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-layer_map_defaults',
			'title'   => '',
			'desc'    => __('Custom overlay 2','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_layer_overlays_custom3_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-layer_map_defaults',
			'title'   => '',
			'desc'    => __('Custom overlay 3','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_layer_overlays_custom4_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-layer_map_defaults',
			'title'   => '',
			'desc'    => __('Custom overlay 4','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_layer_panel'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-layer_map_defaults',
			'title'   => __('Panel for displaying layer name and API URLs on top of map','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '1',
			'choices' => array(
				'1' => __('show','lmm'),
				'0' => __('hide','lmm'),
			)
		);
		// defaults_layer_clustering
		$this->_settings['defaults_layer_clustering'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-layer_map_defaults',
			'title'   => __('Marker clustering','lmm') . $pro_button_link,
			'desc'    => '',
			'type'    => 'radio-pro',
			'std'     => 'disabled',
			'choices' => array(
				'enabled' => __('enabled','lmm'),
				'disabled' => __('disabled','lmm'),
			)
		);
		// defaults_layer - active API links in panel
		$this->_settings['defaults_layer_panel_kml'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-layer_map_defaults',
			'title'    => __('Visible API links in panel','lmm'),
			'desc'    => 'KML <img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-kml.png" width="14" height="14" />',
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['defaults_layer_panel_fullscreen'] = array(
			'version' => '1.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-layer_map_defaults',
			'title'    => '',
			'desc'    => __('Fullscreen','lmm') .  ' <img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-fullscreen.png" width="14" height="14" />',
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['defaults_layer_panel_qr_code'] = array(
			'version' => '1.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-layer_map_defaults',
			'title'    => '',
			'desc'    => __('QR code','lmm') .  ' <img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-qr-code.png" width="14" height="14" />',
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['defaults_layer_panel_geojson'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-layer_map_defaults',
			'title'   => '',
			'desc'    => 'GeoJSON <img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-json.png" width="14" height="14" /> (' . __('not available on multi layer maps','lmm') . ')',
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['defaults_layer_panel_georss'] = array(
			'version' => '1.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-layer_map_defaults',
			'title'   => '',
			'desc'    => 'GeoRSS <img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-georss.png" width="14" height="14" />',
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['defaults_layer_panel_wikitude'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-layer_map_defaults',
			'title'   => '',
			'desc'    => 'Wikitude <img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-wikitude.png" width="14" height="14" />',
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_layer_panel_background_color'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-layer_map_defaults',
			'title'   => __( 'Panel background color', 'lmm' ),
			'desc'    => __('Please use hexadecimal color values','lmm'),
			'std'     => '#efefef',
			'type'    => 'text'
		);
		$this->_settings['defaults_layer_panel_paneltext_css'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-layer_map_defaults',
			'title'   => __( 'Panel text css', 'lmm' ),
			'desc'    => '',
			'std'     => 'font-weight:bold;color:#373737;',
			'type'    => 'text'
		);
		// defaults_layer - which WMS layers are active by default?
		$this->_settings['defaults_layer_wms_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-layer_map_defaults',
			'title'    => __('Checked WMS layers','lmm'),
			'desc'    => __('WMS 1','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_layer_wms2_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-layer_map_defaults',
			'title'   => '',
			'desc'    => __('WMS 2','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_layer_wms3_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-layer_map_defaults',
			'title'   => '',
			'desc'    => __('WMS 3','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_layer_wms4_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-layer_map_defaults',
			'title'   => '',
			'desc'    => __('WMS 4','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_layer_wms5_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-layer_map_defaults',
			'title'   => '',
			'desc'    => __('WMS 5','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_layer_wms6_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-layer_map_defaults',
			'title'   => '',
			'desc'    => __('WMS 6','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_layer_wms7_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-layer_map_defaults',
			'title'   => '',
			'desc'    => __('WMS 7','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_layer_wms8_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-layer_map_defaults',
			'title'   => '',
			'desc'    => __('WMS 8','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_layer_wms9_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-layer_map_defaults',
			'title'   => '',
			'desc'    => __('WMS 9','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_layer_wms10_active'] = array(
			'version' => '1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-layer_map_defaults',
			'title'   => '',
			'desc'    => __('WMS 10','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		// defaults_layer - filter controlbox
		$this->_settings['defaults_layer_mlm_filter_controlbox'] = array(
			'version' => 'p2.7',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-layer_map_defaults',
			'title'   => __('Controlbox for multi-layer-map filter','lmm') . $pro_button_link,
			'desc'    => '',
			'type'    => 'radio-pro',
			'std'     => '1',
			'choices' => array(
				'0' => __('hidden','lmm'),
				'1' => __('collapsed','lmm'),
				'2' => __('expanded','lmm')
			)
		);

		/*
		* List of markers settings
		*/
		$this->_settings['defaults_layer_listmarkers_helptext'] = array(
			'version' => '3.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-list_of_markers',
			'std'     => '',
			'title'   => '',
			'desc'    => '<img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-list-markers.png" width="601" height="360" />',
			'type'    => 'helptext'
		);
		$this->_settings['defaults_layer_listmarkers'] = array(
			'version' => '1.5',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-list_of_markers',
			'title'   => __('Display a list of markers under layer maps','lmm'),
			'desc'    => __('Can be changed for each layer map','lmm'),
			'type'    => 'radio',
			'std'     => '1',
			'choices' => array(
				'0' => __('no','lmm'),
				'1' => __('yes','lmm')
			)
		);
		$this->_settings['defaults_layer_listmarkers_show_icon'] = array(
			'version' => '2.6',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-list_of_markers',
			'title'    => __('Marker attributes to display in list','lmm'),
			'desc'    => __('Icon','lmm'),
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['defaults_layer_listmarkers_show_markername'] = array(
			'version' => '2.6',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-list_of_markers',
			'title'    => '',
			'desc'    => __('Marker name','lmm'),
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['defaults_layer_listmarkers_show_popuptext'] = array(
			'version' => '2.6',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-list_of_markers',
			'title'    => '',
			'desc'    => __('Popup text','lmm'),
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['defaults_layer_listmarkers_show_address'] = array(
			'version' => '3.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-list_of_markers',
			'title'    => '',
			'desc'    => __('Address','lmm'),
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['defaults_layer_listmarkers_show_distance'] = array(
			'version' => 'p2.7',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-list_of_markers',
			'title'   => '',
			'desc'    => __('Distance','lmm')  . $pro_button_link_inline . '<br/><span class="description">' . __('The actual distance value is only shown if sort by "distance from layer center" or "distance from current position" is selected','lmm') . '</span>',
			'type'    => 'checkbox-pro',
			'std'     => 1
		);
		$this->_settings['defaults_layer_listmarkers_show_distance_unit'] = array(
			'version' => 'p2.7',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-list_of_markers',
			'title'   => __('Distance unit in list','lmm') . $pro_button_link,
			'desc'    => '',
			'type'    => 'radio-pro',
			'std'     => 'km',
			'choices' => array(
				'km' => __('metric (km)','lmm'),
				'mile' => __('imperial (miles)','lmm')
			)
		);
		$this->_settings['defaults_layer_listmarkers_show_distance_precision'] = array(
			'version' => 'p2.6',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-list_of_markers',
			'title'   => __( 'Distance precision', 'lmm' ) . $pro_button_link_inline,
			'desc'    => __( 'Number of digits to show after the decimal point', 'lmm' ),
			'std'     => '1',
			'type'    => 'text-pro'
		);
		$this->_settings['defaults_layer_listmarkers_order_by'] = array(
			'version' => '1.5',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-list_of_markers',
			'title'   => __('Order list of markers by','lmm'),
			'desc'    => __('The options "order by distance from current position" and "order by distance from layer center" are only available in the pro version!','lmm') . $pro_button_link_inline,
			'type'    => 'radio',
			'std'     => 'm.id',
			'choices' => array(
				'm.id' => __('marker ID','lmm'),
				'm.markername' => __('marker name','lmm'),
				'm.popuptext' => __('popuptext','lmm'),
				'm.icon' => __('icon','lmm'),
				'm.createdby' => __('created by','lmm'),
				'm.createdon' => __('created on','lmm'),
				'm.updatedby' => __('updated by','lmm'),
				'm.updatedon' => __('updated on','lmm'),
				'm.layer' => __('layer ID','lmm'),
				'm.address' => __('address','lmm'),
				'm.kml_timestamp' => __('KML timestamp','lmm')
			)
		);
		$this->_settings['defaults_layer_listmarkers_sort_order'] = array(
			'version' => '1.5',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-list_of_markers',
			'title'   => __('Sort order','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'ASC',
			'choices' => array(
				'ASC' => __('ascending','lmm'),
				'DESC' => __('descending','lmm')
			)
		);
		$this->_settings['defaults_layer_listmarkers_limit'] = array(
			'version' => '1.7',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-list_of_markers',
			'title'   => __( 'Limit', 'lmm' ),
			'desc'    => __( 'maximum number of markers to display in the list', 'lmm' ),
			'std'     => '10',
			'type'    => 'text'
		);
		// defaults_layer - active API links in markers list
		$this->_settings['defaults_layer_listmarkers_api_directions'] = array(
			'version' => '2.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-list_of_markers',
			'title'    => __('Visible API links for each marker','lmm'),
			'desc'    => __('Directions','lmm') .  ' <img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-car.png" width="14" height="14" />',
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['defaults_layer_listmarkers_api_kml'] = array(
			'version' => '2.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-list_of_markers',
			'title'    => '',
			'desc'    => 'KML <img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-kml.png" width="14" height="14" />',
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['defaults_layer_listmarkers_api_fullscreen'] = array(
			'version' => '2.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-list_of_markers',
			'title'    => '',
			'desc'    => __('Fullscreen','lmm') .  ' <img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-fullscreen.png" width="14" height="14" />',
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['defaults_layer_listmarkers_api_qr_code'] = array(
			'version' => '2.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-list_of_markers',
			'title'    => '',
			'desc'    => __('QR code','lmm') .  ' <img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-qr-code.png" width="14" height="14" />',
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_layer_listmarkers_api_geojson'] = array(
			'version' => '2.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-list_of_markers',
			'title'   => '',
			'desc'    => 'GeoJSON <img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-json.png" width="14" height="14" />',
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_layer_listmarkers_api_georss'] = array(
			'version' => '2.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-list_of_markers',
			'title'   => '',
			'desc'    => 'GeoRSS <img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-georss.png" width="14" height="14" />',
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_layer_listmarkers_api_wikitude'] = array(
			'version' => '2.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-list_of_markers',
			'title'   => '',
			'desc'    => 'Wikitude <img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-wikitude.png" width="14" height="14" />',
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['defaults_layer_listmarkers_link_action'] = array(
			'version' => 'p1.8',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-list_of_markers',
			'title'   => __('Default action for clicking on icons or marker names','lmm') . $pro_button_link,
			'desc'    => '',
			'type'    => 'radio-pro',
			'std'     => 'disabled',
			'choices' => array(
				'setview-open' => __('set map center on marker position and open popup','lmm'),
				'setview-only' => __('set map center on marker position only','lmm'),
				'disabled' => __('no action (and hide links)','lmm')
			)
		);
		$this->_settings['defaults_layer_listmarkers_link_action_zoom'] = array(
			'version' => 'p2.7',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-list_of_markers',
			'title'   => __('Zoom level to use for centering marker','lmm') . $pro_button_link,
			'desc'    => __('If the marker name or icon in the list of markers is clicked, the specified zoom level will be used','lmm'),
			'type'    => 'radio-pro',
			'std'     => 'marker-zoom',
			'choices' => array(
				'layer-zoom' => __('use layer zoom level for all markers','lmm'),
				'marker-zoom' => __('use distinct marker zoom levels','lmm')
			)
		);
		$this->_settings['defaults_layer_listmarkers_helptext2'] = array(
			'version' => 'p2.7',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-list_of_markers',
			'std'     => '',
			'title'   => '<h4 class="h4-lmm-settings">' . __('Action bar settings','lmm') . '</h4>',
			'desc'    => __('Option to add a search field and sort order feature for the list of markers','lmm') . ':<br/><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-list-markers-action-bar.png" width="500" height="60" />',
			'type'    => 'helptext'
		);
		$this->_settings['defaults_layer_listmarkers_action_bar'] = array(
			'version' => 'p2.7',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-list_of_markers',
			'title'   => __('Action bar visibility','lmm') . $pro_button_link,
			'desc'    => '',
			'type'    => 'radio-pro',
			'std'     => 'show-full',
			'choices' => array(
				'show-full' => __('show full action bar','lmm'),
				'show-search-field-only' => __('show search field only','lmm'),
				'show-sort-order-selection-only' => __('show sort order selection only','lmm'),
				'hide' => __('hide action bar','lmm')
			)
		);
		$this->_settings['defaults_layer_listmarkers_searchtext'] = array(
			'version' => 'p2.7',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-list_of_markers',
			'title'   => __( 'Custom text for search field', 'lmm' ) . $pro_button_link,
			'desc'    => __('leave empty to use default search text (Search markers)','lmm'),
			'std'     => '',
			'type'    => 'text-pro'
		);
		$this->_settings['defaults_layer_listmarkers_searchtext_hover'] = array(
			'version' => 'p2.7',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-list_of_markers',
			'title'   => __( 'Custom hover text for search field', 'lmm' ) . $pro_button_link,
			'desc'    => __('leave empty to use default search hover text (start typing to find marker entries based on markername or popuptext)','lmm'),
			'std'     => '',
			'type'    => 'text-pro'
		);
		$this->_settings['defaults_layer_listmarkers_sort_id'] = array(
			'version' => 'p2.7',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-list_of_markers',
			'title'   => __('Available sort orders in action bar dropdown field','lmm') . $pro_button_link,
			'desc'    => __('marker ID','lmm'),
			'type'    => 'checkbox-pro',
			'std'     => 0
		);
		$this->_settings['defaults_layer_listmarkers_sort_markername'] = array(
			'version' => 'p2.7',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-list_of_markers',
			'title'   => '',
			'desc'    => __('marker name','lmm'),
			'type'    => 'checkbox-pro',
			'std'     => 1
		);
		$this->_settings['defaults_layer_listmarkers_sort_popuptext'] = array(
			'version' => 'p2.7',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-list_of_markers',
			'title'   => '',
			'desc'    => __('popuptext','lmm'),
			'type'    => 'checkbox-pro',
			'std'     => 0
		);
		$this->_settings['defaults_layer_listmarkers_sort_icon'] = array(
			'version' => 'p2.7',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-list_of_markers',
			'title'   => '',
			'desc'    => __('icon','lmm'),
			'type'    => 'checkbox-pro',
			'std'     => 0
		);
		$this->_settings['defaults_layer_listmarkers_sort_created_by'] = array(
			'version' => 'p2.7',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-list_of_markers',
			'title'   => '',
			'desc'    => __('created by','lmm'),
			'type'    => 'checkbox-pro',
			'std'     => 0
		);
		$this->_settings['defaults_layer_listmarkers_sort_created_on'] = array(
			'version' => 'p2.7',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-list_of_markers',
			'title'   => '',
			'desc'    => __('created on','lmm'),
			'type'    => 'checkbox-pro',
			'std'     => 1
		);
		$this->_settings['defaults_layer_listmarkers_sort_updated_by'] = array(
			'version' => 'p2.7',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-list_of_markers',
			'title'   => '',
			'desc'    => __('updated by','lmm'),
			'type'    => 'checkbox-pro',
			'std'     => 0
		);
		$this->_settings['defaults_layer_listmarkers_sort_updated_on'] = array(
			'version' => 'p2.7',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-list_of_markers',
			'title'   => '',
			'desc'    => __('updated on','lmm'),
			'type'    => 'checkbox-pro',
			'std'     => 0
		);
		$this->_settings['defaults_layer_listmarkers_sort_layer_id'] = array(
			'version' => 'p2.7',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-list_of_markers',
			'title'   => '',
			'desc'    => __('layer ID','lmm'),
			'type'    => 'checkbox-pro',
			'std'     => 0
		);
		$this->_settings['defaults_layer_listmarkers_sort_address'] = array(
			'version' => 'p2.7',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-list_of_markers',
			'title'   => '',
			'desc'    => __('address','lmm'),
			'type'    => 'checkbox-pro',
			'std'     => 0
		);
		$this->_settings['defaults_layer_listmarkers_sort_kml_timestamp'] = array(
			'version' => 'p2.7',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-list_of_markers',
			'title'   => '',
			'desc'    => __('KML timestamp','lmm'),
			'type'    => 'checkbox-pro',
			'std'     => 0
		);
		$this->_settings['defaults_layer_listmarkers_sort_distance_layer_center'] = array(
			'version' => 'p2.7',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-list_of_markers',
			'title'   => '',
			'desc'    => __('distance from layer center','lmm'),
			'type'    => 'checkbox-pro',
			'std'     => 0
		);
		$this->_settings['defaults_layer_listmarkers_sort_distance_current_pos'] = array(
			'version' => 'p2.7',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-list_of_markers',
			'title'   => '',
			'desc'    => __('distance from current position','lmm'),
			'type'    => 'checkbox-pro',
			'std'     => 1
		);

		/*
		* Filter settings for multi-layer-maps
		*/
		$this->_settings['mlm_filter_helptext'] = array(
			'version' => 'p2.7',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-filtering',
			'std'     => '',
			'title'   => '',
			'desc'    => '<a style="background:#f99755;display:block;padding:3px;text-decoration:none;color:#2702c6;width:635px;margin:10px 0;" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade">' . __('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm') . '</a>' . __('Add a geolocate button to all maps which allows to show and follow your current location:','lmm') . '<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-mlm-filter.jpg" width="400" height="222" />',
			'type'    => 'helptext'
		);
		$this->_settings['mlm_filter_controlbox_position'] = array(
			'version' => 'p2.7',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-filtering',
			'title'   => __('Filter controlbox position','lmm') . $pro_button_link,
			'desc'    => __('The position of the filter controlbox for all maps.','lmm'),
			'type'    => 'radio-pro',
			'std'     => 'topright',
			'choices' => array(
				'topleft' => __('Top left of the map','lmm'),
				'topright' => __('Top right of the map','lmm'),
				'bottomleft' => __('Bottom left of the map','lmm'),
				'bottomright' => __('Bottom right of the map','lmm')
			)
		);
		// mlm_filter - controlbox attributes
		$this->_settings['mlm_filter_controlbox_name'] = array(
			'version' => 'p2.7',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-filtering',
			'title'    => __('Attributes to display for each layer in controlbox','lmm') . $pro_button_link,
			'desc'    => __('Layer name','lmm'),
			'type'    => 'checkbox-pro',
			'std'     => 1
		);
		$this->_settings['mlm_filter_controlbox_icon'] = array(
			'version' => 'p2.7',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-filtering',
			'title'   => '',
			'desc'    => __('Icon','lmm'),
			'type'    => 'checkbox-pro',
			'std'     => 1
		);
		$this->_settings['mlm_filter_controlbox_markercount'] = array(
			'version' => 'p2.7',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-filtering',
			'title'   => '',
			'desc'    => __('Marker count','lmm'),
			'type'    => 'checkbox-pro',
			'std'     => 1
		);
		// mlm_filter sort options
		$this->_settings['mlm_filter_active_orderby'] = array(
			'version' => 'p2.7',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-filtering',
			'title'   => __('Order active layers in controlbox by','lmm') . $pro_button_link,
			'desc'    => '',
			'type'    => 'radio-pro',
			'std'     => 'markercount',
			'choices' => array(
				'id' => __('ID','lmm'),
				'name' => __('Layer name','lmm'),
				'markercount' => __('marker count')
			)
		);
		$this->_settings['mlm_filter_active_sort_order'] = array(
			'version' => 'p2.7',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-filtering',
			'title'   => __('Sort order for active layers in controlbox','lmm') . $pro_button_link,
			'desc'    => '',
			'type'    => 'radio-pro',
			'std'     => 'DESC',
			'choices' => array(
				'ASC' => __('ascending','lmm'),
				'DESC' => __('descending','lmm')
			)
		);
		$this->_settings['mlm_filter_inactive_orderby'] = array(
			'version' => 'p2.7',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-filtering',
			'title'   => __('Order inactive layers in controlbox by','lmm') . $pro_button_link,
			'desc'    => '',
			'type'    => 'radio-pro',
			'std'     => 'markercount',
			'choices' => array(
				'id' => __('ID','lmm'),
				'name' => __('Layer name','lmm'),
				'markercount' => __('marker count')
			)
		);
		$this->_settings['mlm_filter_inactive_sort_order'] = array(
			'version' => 'p2.7',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-filtering',
			'title'   => __('Sort order for inactive layers in controlbox','lmm') . $pro_button_link,
			'desc'    => '',
			'type'    => 'radio-pro',
			'std'     => 'DESC',
			'choices' => array(
				'ASC' => __('ascending','lmm'),
				'DESC' => __('descending','lmm')
			)
		);

		/*
		* Marker clustering settings
		*/
		$this->_settings['clustering_helptext'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-clustering',
			'std'     => '',
			'title'   => '',
			'desc'    => '<a style="background:#f99755;display:block;padding:3px;text-decoration:none;color:#2702c6;width:635px;margin:10px 0;" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade">' . __('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm') . '</a>' . __( 'Clustering can be enabled/disabled for each layer separately on the layer edit page. Below you will find the global settings which are valid for all layer maps with clustering enabled.', 'lmm') . '<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-clustering.jpg" width="500" height="204" />',
			'type'    => 'helptext'
		);
		$this->_settings['clustering_zoomToBoundsOnClick'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-clustering',
			'title'   => 'zoomToBoundsOnClick' . $pro_button_link,
			'desc'    => __('When you click a cluster it zooms to its bounds','lmm'),
			'type'    => 'radio-pro',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);
		$this->_settings['clustering_showCoverageOnHover'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-clustering',
			'title'   => 'showCoverageOnHover' . $pro_button_link,
			'desc'    => __('When you mouse over a cluster it shows the bounds of its markers:','lmm') . '<br/><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-clustering-coverage.jpg" width="135" height="94" />',
			'type'    => 'radio-pro',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);
		$this->_settings['clustering_spiderfyOnMaxZoom'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-clustering',
			'title'   => 'spiderfyOnMaxZoom' . $pro_button_link,
			'desc'    => __('When you click a cluster at the bottom zoom level it spiderfies it so you can see all of its markers:','lmm') . '<br/><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-clustering-spiderify.jpg" width="140" height="100" />',
			'type'    => 'radio-pro',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);
		$this->_settings['clustering_disableClusteringAtZoom'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-clustering',
			'title'   =>'disableClusteringAtZoom' . $pro_button_link,
			'desc'    => __('If set (1 to 18 repectively 19 if supported), at this zoom level and below markers will not be clustered.','lmm'),
			'std'     => '',
			'type'    => 'text-pro'
		);
		$this->_settings['clustering_maxClusterRadius'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-clustering',
			'title'   =>'maxClusterRadius' . $pro_button_link,
			'desc'    => __('The maximum radius that a cluster will cover from the central marker (in pixels). Decreasing will make more smaller clusters.','lmm'),
			'std'     => '80',
			'type'    => 'text-pro'
		);
$this->_settings['clustering_helptext2'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-clustering',
			'std'     => '',
			'title'   =>'polygonOptions' . $pro_button_link,
			'desc'    => sprintf(__('Options to pass when creating the L.Polygon for styling (<a href="%1s" target="_blank">more details</a>)','lmm'), 'http://leafletjs.com/reference.html#path-options') . '<br/><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-clustering-polygon-options.jpg" width="141" height="91" />',
			'type'    => 'helptext-twocolumn'
		);
		$this->_settings['clustering_polygonOptions_stroke'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-clustering',
			'title'   => '',
			'desc'    => __('stroke (whether to draw stroke along the path. Set it to false to disable borders on polygons or circles)','lmm'),
			'type'    => 'radio-reverse-pro',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);
		$this->_settings['clustering_polygonOptions_color'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-clustering',
			'title'   =>'',
			'desc'    => __('color (stroke color)','lmm') . ' - ' . __('example','lmm') . ': ff0000',
			'std'     => '03f',
			'type'    => 'text-reverse-pro'
		);
		$this->_settings['clustering_polygonOptions_weight'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-clustering',
			'title'   =>'',
			'desc'    => __('weight (stroke width in pixel)','lmm') . ' - ' . __('example','lmm') . ': 5',
			'std'     => '5',
			'type'    => 'text-reverse-pro'
		);
		$this->_settings['clustering_polygonOptions_opacity'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-clustering',
			'title'   =>'',
			'desc'    => __('opacity (stroke opacity)','lmm') . ' - ' . __('example','lmm') . ': 0.5',
			'std'     => '0.5',
			'type'    => 'text-reverse-pro'
		);
		$this->_settings['clustering_polygonOptions_fill'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-clustering',
			'title'   => '',
			'desc'    => __('fill (whether to fill the path with color. Set it to false to disable filling on polygons or circles)','lmm'),
			'type'    => 'radio-reverse-pro',
			'std'     => 'auto',
			'choices' => array(
				'auto' => __('automatic','lmm'),
				'false' => __('false','lmm')
			)
		);
		$this->_settings['clustering_polygonOptions_fillColor'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-clustering',
			'title'   =>'',
			'desc'    => __('fillColor (fill color)','lmm') . ' - ' . __('example','lmm') . ': ff0000',
			'std'     => '03f',
			'type'    => 'text-reverse-pro'
		);
		$this->_settings['clustering_polygonOptions_fillopacity'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-clustering',
			'title'   => '',
			'desc'    => __('fillOpacity (fill opacity)','lmm') . ' - ' . __('example','lmm') . ': 0.2',
			'std'     => '0.2',
			'type'    => 'text-reverse-pro'
		);
		$this->_settings['clustering_polygonOptions_clickable'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-clustering',
			'title'   => '',
			'desc'    => __('clickable (if false, the vector will not emit mouse events and will act as a part of the underlying map)','lmm'),
			'type'    => 'radio-reverse-pro',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);
		$this->_settings['clustering_helptext3'] = array(
			'version' => 'p1.3',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-clustering',
			'std'     => '',
			'title'   => __('Cluster colors','lmm') . '' . $pro_button_link,
			'desc'    => __('Options to set the colors of the cluster circles','lmm') . ' - <a href="https://www.mapsmarker.com/colorpicker" target="_blank">https://www.mapsmarker.com/colorpicker</a><br/><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-clustering-colors.jpg" width="400" height="98" />',
			'type'    => 'helptext-twocolumn'
		);
		$this->_settings['clustering_color_small_text'] = array(
			'version' => 'p1.3.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-clustering',
			'title'   =>'',
			'desc'    => __('Cluster small (text color)','lmm'),
			'std'     => '#000000',
			'type'    => 'text-reverse-pro'
		);
		$this->_settings['clustering_color_small'] = array(
			'version' => 'p1.3',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-clustering',
			'title'   =>'',
			'desc'    => __('Cluster small (outer)','lmm'),
			'std'     => 'rgba(181, 226, 140, 0.6)',
			'type'    => 'text-reverse-pro'
		);
		$this->_settings['clustering_color_small_inner'] = array(
			'version' => 'p1.3',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-clustering',
			'title'   =>'',
			'desc'    => __('Cluster small (inner)','lmm'),
			'std'     => 'rgba(110, 204, 57, 0.6)',
			'type'    => 'text-reverse-pro'
		);
		$this->_settings['clustering_color_medium'] = array(
			'version' => 'p1.3',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-clustering',
			'title'   =>'',
			'desc'    => __('Cluster medium (outer)','lmm'),
			'std'     => 'rgba(241, 211, 87, 0.6)',
			'type'    => 'text-reverse-pro'
		);
		$this->_settings['clustering_color_medium_inner'] = array(
			'version' => 'p1.3',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-clustering',
			'title'   =>'',
			'desc'    => __('Cluster medium (inner)','lmm'),
			'std'     => 'rgba(240, 194, 12, 0.6)',
			'type'    => 'text-reverse-pro'
		);
		$this->_settings['clustering_color_medium_text'] = array(
			'version' => 'p1.3.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-clustering',
			'title'   =>'',
			'desc'    => __('Cluster medium (text color)','lmm'),
			'std'     => '#000000',
			'type'    => 'text-reverse-pro'
		);
		$this->_settings['clustering_color_large'] = array(
			'version' => 'p1.3',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-clustering',
			'title'   =>'',
			'desc'    => __('Cluster large (outer)','lmm'),
			'std'     => 'rgba(253, 156, 115, 0.6)',
			'type'    => 'text-reverse-pro'
		);
		$this->_settings['clustering_color_large_inner'] = array(
			'version' => 'p1.3',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-clustering',
			'title'   =>'',
			'desc'    => __('Cluster large (inner)','lmm'),
			'std'     => 'rgba(241, 128, 23, 0.6)',
			'type'    => 'text-reverse-pro'
		);
		$this->_settings['clustering_color_large_text'] = array(
			'version' => 'p1.3.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-clustering',
			'title'   =>'',
			'desc'    => __('Cluster large (text color)','lmm'),
			'std'     => '#000000',
			'type'    => 'text-reverse-pro'
		);
		$this->_settings['clustering_singleMarkerMode'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-clustering',
			'title'   => 'singleMarkerMode' . $pro_button_link,
			'desc'    => __('If set to true, overrides the icon for all added markers to make them appear as a 1 size cluster:','lmm') . '<br/><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-clustering-singlemarkermode.jpg" width="123" height="100" />',
			'type'    => 'radio-pro',
			'std'     => 'false',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);
		$this->_settings['clustering_spiderfyDistanceMultiplier'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-clustering',
			'title'   =>'spiderfyDistanceMultiplier' . $pro_button_link,
			'desc'    => __('Increase from 1 to increase the distance away from the center that spiderfied markers are placed. Use if you are using big marker icons:','lmm') . '<br/><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-clustering-spiderify-distance.jpg" width="117" height="100" />',
			'std'     => '1',
			'type'    => 'text-pro'
		);
		$this->_settings['clustering_helptext4'] = array(
			'version' => 'p2.6',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-clustering',
			'std'     => '',
			'title'   =>'spiderLegPolylineOptions' . $pro_button_link,
			'desc'    => sprintf(__('Allows you to specify <a href="%1$s" target="_blank">PolylineOptions</a> to style spider legs.','lmm'), 'http://leafletjs.com/reference.html#polyline-options'),
			'type'    => 'helptext-twocolumn'
		);
		$this->_settings['clustering_spiderLegPolylineOptions_color'] = array(
			'version' => 'p2.6',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-clustering',
			'title'   =>'',
			'desc'    => __('color (stroke color)','lmm') . ' - ' . __('example','lmm') . ': 222',
			'std'     => '222',
			'type'    => 'text-reverse-pro'
		);
		$this->_settings['clustering_spiderLegPolylineOptions_weight'] = array(
			'version' => 'p2.6',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-clustering',
			'title'   =>'',
			'desc'    => __('weight (stroke width in pixel)','lmm') . ' - ' . __('example','lmm') . ': 1.5',
			'std'     => '1.5',
			'type'    => 'text-reverse-pro'
		);
		$this->_settings['clustering_spiderLegPolylineOptions_opacity'] = array(
			'version' => 'p2.6',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-clustering',
			'title'   =>'',
			'desc'    => __('opacity (stroke opacity)','lmm') . ' - ' . __('example','lmm') . ': 0.5',
			'std'     => '0.5',
			'type'    => 'text-reverse-pro'
		);
		$this->_settings['clustering_animateAddingMarkers'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-clustering',
			'title'   => 'animateAddingMarkers' . $pro_button_link,
			'desc'    => __('If set to true then adding individual markers to the MarkerClusterGroup after it has been added to the map will add the marker and animate it in to the cluster. Defaults to false as this gives better performance when bulk adding markers.','lmm'),
			'type'    => 'radio-pro',
			'std'     => 'false',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);
		$this->_settings['clustering_animate'] = array(
			'version' => 'p2.8',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-clustering',
			'title'   => 'animate' . $pro_button_link,
			'desc'    => __('Smoothly split / merge cluster children when zooming and spiderfying.','lmm'),
			'type'    => 'radio-pro',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);

		/*
		* Interaction options
		* formerly "General map settings" and moved to "Basemaps" from "Misc" tab
		*/
		$this->_settings['map_interaction_options_helptext'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-interaction',
			'std'     => '',
			'title'   => '',
			'desc'    => __( 'The following settings will be used for all marker and layer maps', 'lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['misc_map_dragging'] = array(
			'version' => '2.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-interaction',
			'title'   => 'dragging',
			'desc'    => __('Whether the map be draggable with mouse/touch or not.','lmm'),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);
		$this->_settings['misc_map_touchzoom'] = array(
			'version' => '2.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-interaction',
			'title'   => 'touchZoom',
			'desc'    => __('Whether the map can be zoomed by touch-dragging with two fingers.','lmm'),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);
		$this->_settings['misc_map_scrollwheelzoom'] = array(
			'version' => '2.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-interaction',
			'title'   => 'scrollWheelZoom',
			'desc'    => __('Whether the map can be zoomed by using the mouse wheel.','lmm'),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);
		$this->_settings['misc_map_doubleclickzoom'] = array(
			'version' => '2.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-interaction',
			'title'   => 'doubleClickZoom',
			'desc'    => __('Whether the map can be zoomed in by double clicking on it.','lmm'),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);
		$this->_settings['map_interaction_options_boxzoom'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-interaction',
			'title'   => 'boxzoom',
			'desc'    => __('Whether the map can be zoomed to a rectangular area specified by dragging the mouse while pressing shift.','lmm'),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);
		$this->_settings['misc_map_trackresize'] = array(
			'version' => '2.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-interaction',
			'title'   => 'trackResize',
			'desc'    => __('Whether the map automatically handles browser window resize to update itself.','lmm'),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);
		$this->_settings['map_interaction_options_worldcopyjump'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-interaction',
			'title'   => 'worldCopyJump',
			'desc'    => __('With this option enabled, the map tracks when you pan to another "copy" of the world and moves all overlays like markers and vector layers there.','lmm'),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);
		$this->_settings['misc_map_closepopuponclick'] = array(
			'version' => '2.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-interaction',
			'title'   => 'closePopupOnClick',
			'desc'    => __('Set it to false if you do not want popups to close when user clicks the map.','lmm'),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);
		$this->_settings['misc_map_osm_editlink'] = array(
			'version' => '3.3',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-interaction',
			'title'   => __('OpenStreetMap edit link','lmm'),
			'desc'    => __('Appends an edit link to the OpenStreetMap attribution text which allows direct edits on www.openstreetmap.org (free account required)','lmm') . '<br/><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-osmeditlink.jpg" width="521" height="78" />',
			'type'    => 'radio',
			'std'     => 'show',
			'choices' => array(
				'show' => __('show','lmm'),
				'hide' => __('hide','lmm')
			)
		);
		$this->_settings['misc_map_osm_editlink_editor'] = array(
			'version' => '3.6',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-interaction',
			'title'   => __('OpenStreetMap edit link editor','lmm'),
			'desc'    => __('Editor used to edit maps','lmm'),
			'type'    => 'radio',
			'std'     => 'id',
			'choices' => array(
				'id' => '<a href="http://ideditor.com/" target="_blank">iD</a>',
				'potlatch2' => '<a href="http://wiki.openstreetmap.org/wiki/Potlatch_2" target="_blank">Potlatch 2</a>',
				'remote' => __('remote editor','lmm') . ' (<a href="http://wiki.openstreetmap.org/wiki/JOSM" target="_blank">JOSM</a> / <a href="http://wiki.openstreetmap.org/wiki/Merkaartor" target="_blank">Merkaartor</a>)'
			)
		);
		$this->_settings['leaflet_hash_status'] = array(
			'version' => 'p2.6',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-interaction',
			'title'   => __('URL hashes','lmm') . $pro_button_link,
			'desc'    => sprintf(__('add dynamic URL hashes to web pages with maps, allowing users to easily link to specific map views. Example: %1$s','lmm'), 'https://domain/link-to-map/#11/48.2073/16.3792'),
			'type'    => 'radio-pro',
			'std'     => 'enabled',
			'choices' => array(
				'enabled' => __('enabled','lmm'),
				'disabled' => __('disabled','lmm')
			)
		);
		$this->_settings['map_interaction_options_tap'] = array(
			'version' => 'p2.7',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-interaction',
			'title'   => 'tap' . $pro_button_link,
			'desc'    => __('Enables mobile hacks for supporting instant taps (fixing 200ms click delay on iOS/Android) and touch holds (fired as contextmenu events).','lmm'),
			'type'    => 'radio-pro',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);
		$this->_settings['map_interaction_options_taptolerance'] = array(
			'version' => 'p2.7',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-interaction',
			'title'   => 'tapTolerance' . $pro_button_link,
			'desc'    => __('The max number of pixels a user can shift his finger during touch for it to be considered a valid tap.','lmm'),
			'std'     => '15',
			'type'    => 'text-pro'
		);
		$this->_settings['map_interaction_options_bounceatzoomlimits'] = array(
			'version' => 'p2.7',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-interaction',
			'title'   => 'bounceAtZoomLimits' . $pro_button_link,
			'desc'    => __('Set it to false if you do not want the map to zoom beyond min/max zoom and then bounce back when pinch-zooming.','lmm'),
			'type'    => 'radio-pro',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);
		/* Keyboard navigation options */
		$this->_settings['map_keyboard_navigation_options_helptext'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-interaction',
			'std'     => '',
			'title'   => '<h4 class="h4-lmm-settings">' . __('Keyboard navigation options','lmm') . '</h4>',
			'desc'    => __( 'The following settings will be used for all marker and layer maps', 'lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['map_keyboard_navigation_options_keyboard'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-interaction',
			'title'   => 'keyboard',
			'desc'    => __('Makes the map focusable and allows users to navigate the map with keyboard arrows and +/- keys','lmm'),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);
		$this->_settings['map_keyboard_navigation_options_keyboardpanoffset'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-interaction',
			'title'   => 'keyboardPanOffset',
			'desc'    => __('Amount of pixels to pan when pressing an arrow key','lmm'),
			'std'     => '80',
			'type'    => 'text'
		);
		$this->_settings['map_keyboard_navigation_options_keyboardzoomoffset'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-interaction',
			'title'   => 'keyboardZoomOffset',
			'desc'    => __( 'Number of zoom levels to change when pressing + or - key.', 'lmm' ),
			'std'     => '1',
			'type'    => 'text'
		);
		/* Panning inertia options */
		$this->_settings['map_panning_inertia_options_helptext'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-interaction',
			'std'     => '',
			'title'   => '<h4 class="h4-lmm-settings">' . __('Panning inertia options','lmm') . '</h4>',
			'desc'    => __( 'The following settings will be used for all marker and layer maps', 'lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['map_panning_inertia_options_inertia'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-interaction',
			'title'   => 'inertia',
			'desc'    => __('If enabled, panning of the map will have an inertia effect where the map builds momentum while dragging and continues moving in the same direction for some time. Feels especially nice on touch devices.','lmm'),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);
		$this->_settings['map_panning_inertia_options_inertiadeceleration'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-interaction',
			'title'   => 'inertiaDeceleration',
			'desc'    => __('The rate with which the inertial movement slows down, in pixels/second','lmm'),
			'std'     => '3000',
			'type'    => 'text'
		);
		$this->_settings['map_panning_inertia_options_inertiamaxspeed'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-interaction',
			'title'   => 'inertiaMaxSpeed',
			'desc'    => __('Max speed of the inertial movement, in pixels/second.','lmm'),
			'std'     => '1500',
			'type'    => 'text'
		);

		/*
		* Control options
		*/
		$this->_settings['map_control_options_helptext'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-control',
			'title'   => '',
			'desc'    => __( 'The following settings will be used for all marker and layer maps', 'lmm'),
			'std'     => '',
			'type'    => 'helptext'
		);
		$this->_settings['misc_map_zoomcontrol'] = array(
			'version' => '2.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-control',
			'title'   => 'zoomControl',
			'desc'    => __('Whether the zoom control is added to the map by default.','lmm') . '<br/><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-zoomcontrol.png" width="124" height="77" />',
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);
		$this->_settings['map_fullscreen_button'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-control',
			'title'   => __('Fullscreen button','lmm') . $pro_button_link,
			'desc'    => __('Whether to add a button for displaying maps in fullscreen via HTML5','lmm') . '<br/><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-fullscreen.png" width="87" height="99" /><a style="background:#f99755;display:block;padding:3px;text-decoration:none;color:#2702c6;width:635px;margin:10px 0;" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade">' . __('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm') . '</a>',
			'type'    => 'radio-pro',
			'std'     => 'false',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm'),
			)
		);
		$this->_settings['map_fullscreen_button_position'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-control',
			'title'   => __('Fullscreen button position','lmm') . $pro_button_link,
			'desc'    => __('The position of the fullscreen button (one of the map corners).','lmm'),
			'type'    => 'radio-pro',
			'std'     => 'topleft',
			'choices' => array(
				'bottomleft' => __('Bottom left of the map','lmm'),
				'bottomright' => __('Bottom right of the map','lmm'),
				'topright' => __('Top right of the map','lmm'),
				'topleft' => __('Top left of the map','lmm')
			)
		);
		$this->_settings['map_home_button'] = array(
			'version' => 'p2.7',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-control',
			'title'   => __('Home button','lmm') . $pro_button_link,
			'desc'    => __('Whether to add a home button to reset map view','lmm') . '<br/><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-home-button.png" width="52" height="45" />',
			'type'    => 'radio-pro',
			'std'     => 'true-ondemand',
			'choices' => array(
				'true-always' => __('true','lmm'),
				'true-ondemand' => __('true (only add on demand if map view is changed)','lmm'),
				'false' => __('false','lmm'),
			)
		);
		$this->_settings['map_home_button_position'] = array(
			'version' => 'p2.7',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-control',
			'title'   => __('Home button position','lmm') . $pro_button_link,
			'desc'    => __('The position of the home button (one of the map corners).','lmm'),
			'type'    => 'radio-pro',
			'std'     => 'topleft',
			'choices' => array(
				'bottomleft' => __('Bottom left of the map','lmm'),
				'bottomright' => __('Bottom right of the map','lmm'),
				'topright' => __('Top right of the map','lmm'),
				'topleft' => __('Top left of the map','lmm')
			)
		);
		/* Scale control options */
		$this->_settings['map_scale_control_helptext'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-control',
			'std'     => '',
			'title'   => '<h4 class="h4-lmm-settings">' . __('Scale control','lmm') . '</h4>',
			'desc'    => __( 'A simple scale control that shows the scale of the current center of screen in metric (m/km) and/or imperial (mi/ft) systems. The following settings will be used for all marker and layer maps.', 'lmm').'<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-basemap-scale-control.jpg" width="645" height="43" />',
			'type'    => 'helptext'
		);
		$this->_settings['map_scale_control'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-control',
			'title'   => __('Scale Control','lmm'),
			'desc'    => __('Whether the scale control is added to the map by default.','lmm'),
			'type'    => 'radio',
			'std'     => 'enabled',
			'choices' => array(
				'enabled' => __('enabled','lmm'),
				'disabled' => __('disabled','lmm')
			)
		);
		$this->_settings['map_scale_control_position'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-control',
			'title'   => __('Position','lmm'),
			'desc'    => __('The position of the control (one of the map corners).','lmm'),
			'type'    => 'radio',
			'std'     => 'bottomleft',
			'choices' => array(
				'bottomleft' => __('Bottom left of the map','lmm'),
				'bottomright' => __('Bottom right of the map','lmm'),
				'topright' => __('Top right of the map','lmm'),
				'topleft' => __('Top left of the map','lmm')
			)
		);
		$this->_settings['map_scale_control_maxwidth'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-control',
			'title'   => 'maxWidth',
			'desc'    => __('Maximum width of the control in pixels. The width is set dynamically to show round values (e.g. 100, 200, 500).','lmm'),
			'std'     => '100',
			'type'    => 'text'
		);
		$this->_settings['map_scale_control_metric'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-control',
			'title'   => 'metric',
			'desc'    => __('Whether to show the metric scale line (m/km).','lmm'),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);
		$this->_settings['map_scale_control_imperial'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-control',
			'title'   => 'imperial',
			'desc'    => __('Whether to show the imperial scale line (mi/ft).','lmm'),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);
		$this->_settings['map_scale_control_updatewhenidle'] = array(
			'version' => '2.7.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-control',
			'title'   => 'updateWhenIdle',
			'desc'    => __('If true, the control is updated on moveend, otherwise it is always up-to-date (updated on move).','lmm'),
			'type'    => 'radio',
			'std'     => 'false',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);
		/* KML Settings */
		$this->_settings['misc_kml_helptext'] = array(
			'version' => '1.8',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-control',
			'std'     => '',
			'title'   => '<h4 class="h4-lmm-settings">' . __('KML settings','lmm') . '</h4>',
			'desc'    => '',
			'type'    => 'helptext'
		);
		$this->_settings['misc_kml'] = array(
			'version' => '1.8',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-control',
			'title'   => __( 'Marker names in KML', 'lmm' ),
			'desc'    => __( 'Choose how marker names should be displayed in KML files', 'lmm') . ' <a href="https://www.mapsmarker.com/kml-names" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
			'type'    => 'radio',
			'std'     => 'show',
			'choices' => array(
				'show' => __('show', 'lmm'),
				'hide' => __('hide', 'lmm'),
				'popup' => __('put in front of popup-text', 'lmm')
			)
		);

		/*
		* Minimap settings
		*/
		$this->_settings['minimap_helptext'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-minimap',
			'std'     => '',
			'title'   => '',
			'desc'    => '<a style="background:#f99755;display:block;padding:3px;text-decoration:none;color:#2702c6;width:635px;margin:10px 0;" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade">' . __('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm') . '</a>' . __( 'Add an expandable minimap to your maps which shows the same as the main map with a set zoom offset', 'lmm') . '<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-minimap.jpg" width="400" height="183" />',
			'type'    => 'helptext'
		);
		$this->_settings['minimap_status'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-minimap',
			'title'   => __('Status','lmm') . $pro_button_link,
			'desc'    => '',
			'type'    => 'radio-pro',
			'std'     => 'hidden',
			'choices' => array(
				'collapsed' => __('collapsed','lmm'),
				'expanded' => __('expanded','lmm'),
				'hidden' => __('hidden','lmm')
			)
		);
		$this->_settings['minimap_basemap'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-minimap',
			'title'   => __('Basemap','lmm') . $pro_button_link,
			'desc'    => __('Please select basemap which should be used for minimaps','lmm'),
			'type'    => 'radio-pro',
			'std'     => 'automatic',
			'choices' => array(
				'automatic' => __('automatic (use basemap from main map and OpenStreetMap as fallback if unsupported)','lmm'),
				'osm_mapnik_minimap' => 'OpenStreetMap',
				'mapquest_osm_minimap' => 'MapQuest (OSM) - <strong>' . __('API key required!','lmm') . '</strong> <a href="https://www.mapsmarker.com/mapquest-api-key" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
				'mapquest_aerial_minimap' => __('MapQuest (Aerial)','lmm') . ' - <strong>' . __('API key required!','lmm') . '</strong> <a href="https://www.mapsmarker.com/mapquest-api-key" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
				'mapquest_hybrid_minimap' => __('MapQuest (Aerial)','lmm') . ' - <strong>' . __('API key required!','lmm') . '</strong> <a href="https://www.mapsmarker.com/mapquest-api-key" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
				'googleLayer_roadmap_minimap' => __('Google Maps (Roadmap)','lmm')  . ' - <strong>' . __('API key required!','lmm') . '</strong> <a href="https://www.mapsmarker.com/google-maps-javascript-api" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
				'googleLayer_satellite_minimap' => __('Google Maps (Satellite)','lmm')  . ' - <strong>' . __('API key required!','lmm') . '</strong> <a href="https://www.mapsmarker.com/google-maps-javascript-api" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
				'googleLayer_hybrid_minimap' => __('Google Maps (Hybrid)','lmm')  . ' - <strong>' . __('API key required!','lmm') . '</strong> <a href="https://www.mapsmarker.com/google-maps-javascript-api" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
				'googleLayer_terrain_minimap' => __('Google Maps (Terrain)','lmm')  . ' - <strong>' . __('API key required!','lmm') . '</strong> <a href="https://www.mapsmarker.com/google-maps-javascript-api" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
				'bingaerial_minimap' => __('Bing Maps (Aerial)','lmm') . ' - <strong>' . __('API key required!','lmm') . '</strong>',
				'bingaerialwithlabels_minimap' => __('Bing Maps (Aerial+Labels)','lmm') . ' - <strong>' . __('API key required!','lmm') . '</strong>',
				'bingroad_minimap' => __('Bing Maps (Road)','lmm') . ' - <strong>' . __('API key required!','lmm') . '</strong>'
				)
		);
		$this->_settings['minimap_position'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-minimap',
			'title'   => __('Position','lmm') . $pro_button_link,
			'desc'    => '',
			'type'    => 'radio-pro',
			'std'     => 'bottomright',
			'choices' => array(
				'bottomleft' => __('Bottom left of the map','lmm'),
				'bottomright' => __('Bottom right of the map','lmm'),
				'topright' => __('Top right of the map','lmm'),
				'topleft' => __('Top left of the map','lmm')
			)
		);
		$this->_settings['minimap_width'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-minimap',
			'title'   => __('Width','lmm') . $pro_button_link,
			'desc'    => __('The width of the minimap in pixels.','lmm'),
			'std'     => '150',
			'type'    => 'text-pro'
		);
		$this->_settings['minimap_height'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-minimap',
			'title'   => __('Height','lmm') . $pro_button_link,
			'desc'    => __(' The height of the minimap in pixels.','lmm'),
			'std'     => '150',
			'type'    => 'text-pro'
		);
		$this->_settings['minimap_collapsedWidth'] = array(
			'version' => 'p2.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-minimap',
			'title'   => 'collapsedWidth' . $pro_button_link,
			'desc'    => __('The width of the toggle marker and the minimap when collapsed, in pixels.','lmm'),
			'std'     => '19',
			'type'    => 'text-pro'
		);
		$this->_settings['minimap_collapsedHeight'] = array(
			'version' => 'p2.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-minimap',
			'title'   => 'collapsedHeight' . $pro_button_link,
			'desc'    => __('The height of the toggle marker and the minimap when collapsed, in pixels.','lmm'),
			'std'     => '19',
			'type'    => 'text-pro'
		);
		$this->_settings['minimap_zoomLevelOffset'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-minimap',
			'title'   => 'zoomLevelOffset' . $pro_button_link,
			'desc'    => __('The offset applied to the zoom in the minimap compared to the zoom of the main map. Can be positive or negative.','lmm'),
			'std'     => '-5',
			'type'    => 'text-pro'
		);
		$this->_settings['minimap_zoomLevelFixed'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-minimap',
			'title'   =>'zoomLevelFixed' . $pro_button_link,
			'desc'    => __('Overrides the offset to apply a fixed zoom level to the minimap regardless of the main map zoom. Set it to any valid zoom level, if unset zoomLevelOffset is used instead.','lmm'),
			'std'     => '',
			'type'    => 'text-pro'
		);
		$this->_settings['minimap_zoomAnimation'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-minimap',
			'title'   => 'zoomAnimation' . $pro_button_link,
			'desc'    => __('Sets whether the minimap should have an animated zoom. (Will cause it to lag a bit after the movement of the main map.)','lmm'),
			'type'    => 'radio-pro',
			'std'     => 'false',
			'choices' => array(
				'false' => __('false','lmm'),
				'true' => __('true','lmm')
			)
		);
		$this->_settings['minimap_toggleDisplay'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-minimap',
			'title'   => 'toggleDisplay' . $pro_button_link,
			'desc'    => __('Sets whether the minimap should have a button to minimise it','lmm'),
			'type'    => 'radio-pro',
			'std'     => 'true',
			'choices' => array(
				'false' => __('false','lmm'),
				'true' => __('true','lmm')
			)
		);
		$this->_settings['minimap_autoToggleDisplay'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-minimap',
			'title'   => 'autoToggleDisplay' . $pro_button_link,
			'desc'    => __('Sets whether the minimap should hide automatically if the parent map bounds does not fit within the minimap bounds. Especially useful when zoomLevelFixed is set.','lmm'),
			'type'    => 'radio-pro',
			'std'     => 'false',
			'choices' => array(
				'false' => __('false','lmm'),
				'true' => __('true','lmm')
			)
		);

		/*
		* GPX tracks settings
		*/
		$this->_settings['gpx_helptext'] = array(
			'version' => 'p1.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-gpx',
			'std'     => '',
			'title'   => '',
			'desc'    => '<a style="background:#f99755;display:block;padding:3px;text-decoration:none;color:#2702c6;width:635px;margin:10px 0;" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade">' . __('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm') . '</a><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-gpx.jpg" width="449" height="263" /><br/><br/>' . __( 'Settings below will be applied to all GPX tracks added to marker or layer maps.', 'lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['gpx_track_color'] = array(
			'version' => 'p1.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-gpx',
			'title'   => __('Polygon options','lmm'). $pro_button_link,
			'desc'    => __('Track color','lmm') . ' - ' . sprintf(__('Please enter the hex value of the color you would like to use. For help please visit <a href="%1s" target="_blank">%2s</a>.','lmm'), 'https://www.mapsmarker.com/colorpicker', 'mapsmarker.com/colorpicker'),
			'std'     => '#0000FF',
			'type'    => 'text-pro'
		);
		$this->_settings['gpx_track_weight'] = array(
			'version' => 'p1.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-gpx',
			'title'   => '',
			'desc'    => __('Track weight','lmm') . ' - ' . __('Stroke width in pixels','lmm'),
			'std'     => '5',
			'type'    => 'text-pro'
		);
		$this->_settings['gpx_track_opacity'] = array(
			'version' => 'p1.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-gpx',
			'title'   => '',
			'desc'    => __('Stroke opacity','lmm'),
			'std'     => '0.5',
			'type'    => 'text-pro'
		);
		$this->_settings['gpx_track_clickable'] = array(
			'version' => 'p1.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-gpx',
			'title'   => '',
			'desc'    => __('Clickable','lmm') . ' - ' . __('If true, the vector will emit mouse events and will act as a part of the underlying map.','lmm'),
			'type'    => 'radio-pro',
			'std'     => 'false',
			'choices' => array(
				'false' => __('false','lmm'),
				'true' => __('true','lmm')
			)
		);
		$this->_settings['gpx_track_noClip'] = array(
			'version' => 'p1.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-gpx',
			'title'   => '',
			'desc'    => 'noClip' . ' - ' . __('polyline clipping','lmm'),
			'type'    => 'radio-pro',
			'std'     => 'false',
			'choices' => array(
				'false' => __('false','lmm'),
				'true' => __('true','lmm')
			)
		);
		$this->_settings['gpx_track_smoothFactor'] = array(
			'version' => 'p1.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-gpx',
			'title'   => '',
			'desc'    => __('Track smoothFactor','lmm') . ' - ' . __('How much to simplify the polyline on each zoom level. More means better performance and smoother look, and less means more accurate representation.','lmm'),
			'std'     => '1.0',
			'type'    => 'text-pro'
		);
		$this->_settings['gpx_metadata_units'] = array(
			'version' => 'p1.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-gpx',
			'title'   => __('GPX metadata settings','lmm'). $pro_button_link,
			'desc'    => __('Distance units','lmm'),
			'type'    => 'radio-pro',
			'std'     => 'metric',
			'choices' => array(
				'metric' => __('metric (km)','lmm'),
				'imperial' => __('imperial (miles)','lmm')
			)
		);
		$this->_settings['gpx_metadata_name'] = array(
			'version' => 'p1.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-gpx',
			'title'   => '',
			'desc'    => __( 'Track name', 'lmm' ),
			'type'    => 'checkbox-pro',
			'std'     => 1
		);
		$this->_settings['gpx_metadata_start'] = array(
			'version' => 'p1.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-gpx',
			'title'   => '',
			'desc'    => __( 'Starting time', 'lmm' ),
			'type'    => 'checkbox-pro',
			'std'     => 1
		);
		$this->_settings['gpx_metadata_end'] = array(
			'version' => 'p1.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-gpx',
			'title'   => '',
			'desc'    => __( 'End time', 'lmm' ),
			'type'    => 'checkbox-pro',
			'std'     => 0
		);
		$this->_settings['gpx_metadata_distance'] = array(
			'version' => 'p1.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-gpx',
			'title'   => '',
			'desc'    => __( 'Total track distance', 'lmm' ),
			'type'    => 'checkbox-pro',
			'std'     => 1
		);
		$this->_settings['gpx_metadata_duration_total'] = array(
			'version' => 'p1.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-gpx',
			'title'   => '',
			'desc'    => __( 'Duration', 'lmm' ),
			'type'    => 'checkbox-pro',
			'std'     => 1
		);
		$this->_settings['gpx_metadata_duration_moving'] = array(
			'version' => 'p1.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-gpx',
			'title'   => '',
			'desc'    => __( 'Moving time', 'lmm' ),
			'type'    => 'checkbox-pro',
			'std'     => 0
		);
		$this->_settings['gpx_metadata_avpace'] = array(
			'version' => 'p1.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-gpx',
			'title'   => '',
			'desc'    => __( 'Average moving pace', 'lmm' ),
			'type'    => 'checkbox-pro',
			'std'     => 1
		);
		$this->_settings['gpx_metadata_avhr'] = array(
			'version' => 'p1.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-gpx',
			'title'   => '',
			'desc'    => __( 'Average heart rate', 'lmm' ),
			'type'    => 'checkbox-pro',
			'std'     => 0
		);
		$this->_settings['gpx_metadata_hr_full'] = array(
			'version' => 'p1.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-gpx',
			'title'   => '',
			'desc'    => __( 'Full heart rate data', 'lmm' ),
			'type'    => 'checkbox-pro',
			'std'     => 0
		);
		$this->_settings['gpx_metadata_elev_gain'] = array(
			'version' => 'p1.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-gpx',
			'title'   => '',
			'desc'    => __( 'Elevation gain', 'lmm' ),
			'type'    => 'checkbox-pro',
			'std'     => 1
		);
		$this->_settings['gpx_metadata_elev_loss'] = array(
			'version' => 'p1.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-gpx',
			'title'   => '',
			'desc'    => __( 'Elevation loss', 'lmm' ),
			'type'    => 'checkbox-pro',
			'std'     => 1
		);
		$this->_settings['gpx_metadata_elev_net'] = array(
			'version' => 'p1.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-gpx',
			'title'   => '',
			'desc'    => __( 'Elevation net', 'lmm' ),
			'type'    => 'checkbox-pro',
			'std'     => 1
		);
		$this->_settings['gpx_metadata_elev_full'] = array(
			'version' => 'p1.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-gpx',
			'title'   => '',
			'desc'    => __( 'Full elevation data', 'lmm' ),
			'type'    => 'checkbox-pro',
			'std'     => 0
		);
		$this->_settings['gpx_metadata_gpx_download'] = array(
			'version' => 'p2.4',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-gpx',
			'title'   => '',
			'desc'    => __( 'download GPX file', 'lmm' ),
			'type'    => 'checkbox-pro',
			'std'     => 1
		);
		$this->_settings['gpx_max_point_interval'] = array(
			'version' => 'p1.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-gpx',
			'title'   => __('Maximum interval between points','lmm'). $pro_button_link,
			'desc'    => __('GPX parsing will automatically handle pauses in the track with a default tolerance interval of 15 seconds between points.','lmm'),
			'std'     => '15000',
			'type'    => 'text-pro'
		);
		$this->_settings['gpx_icons_status'] = array(
			'version' => 'p2.6',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-gpx',
			'title'   => __('GPX icon settings','lmm'). $pro_button_link,
			'desc'    => '',
			'type'    => 'radio-pro',
			'std'     => 'show',
			'choices' => array(
				'show' => __('show default start and end icons','lmm'),
				'hide' => __('hide default start and end icons','lmm')
			)
		);
		$this->_settings['gpx_startIconUrl'] = array(
			'version' => 'p1.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-gpx',
			'title'   => '',
			'desc'    => __('Start icon','lmm') . ' - ' . sprintf(__('Leave empty to use the <a href="%1s" target="_blank">default icon</a>. To use a custom icon, please enter the file name of the icon within your marker icon directory (%2s)','lmm'), LEAFLET_PLUGIN_URL . 'leaflet-dist/images/gpx-icon-start.png', LEAFLET_PLUGIN_ICONS_URL),
			'std'     => '',
			'type'    => 'text-pro'
		);
		$this->_settings['gpx_endIconUrl'] = array(
			'version' => 'p1.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-gpx',
			'title'   => '',
			'desc'    => __('End icon','lmm') . ' - ' . sprintf(__('Leave empty to use the <a href="%1s" target="_blank">default icon</a>. To use a custom icon, please enter the file name of the icon within your marker icon directory (%2s)','lmm'), LEAFLET_PLUGIN_URL . 'leaflet-dist/images/gpx-icon-end.png', LEAFLET_PLUGIN_ICONS_URL),
			'std'     => '',
			'type'    => 'text-pro'
		);
		$this->_settings['gpx_shadowUrl'] = array(
			'version' => 'p1.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-gpx',
			'title'   => '',
			'desc'    => __('Shadow icon','lmm') . ' - ' . sprintf(__('Leave empty to use the <a href="%1s" target="_blank">default icon</a>. To use a custom icon, please enter the file name of the icon within your marker icon directory (%2s)','lmm'), LEAFLET_PLUGIN_URL . 'leaflet-dist/images/gpx-icon-shadow.png', LEAFLET_PLUGIN_ICONS_URL),
			'std'     => '',
			'type'    => 'text-pro'
		);
		$this->_settings['gpx_iconSize_x'] = array(
			'version' => 'p1.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-gpx',
			'title'   => '',
			'desc'    => __( 'Icon size', 'lmm' ) . ' (x) - ' . __( 'Width of the icons in pixel', 'lmm' ),
			'std'     => '33',
			'type'    => 'text-pro'
		);
		$this->_settings['gpx_iconSize_y'] = array(
			'version' => 'p1.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-gpx',
			'title'   => '',
			'desc'    => __( 'Icon size', 'lmm' ) . ' (y) - ' . __( 'Width of the icons in pixel', 'lmm' ),
			'std'     => '50',
			'type'    => 'text-pro'
		);
		$this->_settings['gpx_shadowSize_x'] = array(
			'version' => 'p1.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-gpx',
			'title'   => '',
			'desc'    => __( 'Shadow size', 'lmm' ) . ' (x) - ' . __( 'Width of the shadow in pixel', 'lmm' ),
			'std'     => '50',
			'type'    => 'text-pro'
		);
		$this->_settings['gpx_shadowSize_y'] = array(
			'version' => 'p1.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-gpx',
			'title'   => '',
			'desc'    => __( 'Shadow size', 'lmm' ) . ' (y) - ' . __( 'Height of the shadow in pixel', 'lmm' ),
			'std'     => '50',
			'type'    => 'text-pro'
		);
		$this->_settings['gpx_iconAnchor_x'] = array(
			'version' => 'p1.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-gpx',
			'title'   => '',
			'desc'    => __( 'Icon anchor', 'lmm' ) . ' (x) - ' . __( 'The x-coordinates of the icons in pixel', 'lmm' ),
			'std'     => '16',
			'type'    => 'text-pro'
		);
		$this->_settings['gpx_iconAnchor_y'] = array(
			'version' => 'p1.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-gpx',
			'title'   => '',
			'desc'    => __( 'Icon anchor', 'lmm' ) . ' (y) - ' . __( 'The y-coordinates of the icons in pixel', 'lmm' ),
			'std'     => '45',
			'type'    => 'text-pro'
		);
		$this->_settings['gpx_shadowAnchor_x'] = array(
			'version' => 'p1.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-gpx',
			'title'   => '',
			'desc'    => __( 'Shadow anchor', 'lmm' ) . ' (x) - ' . __( 'The x-coordinates of the shadow in pixel', 'lmm' ),
			'std'     => '16',
			'type'    => 'text-pro'
		);
		$this->_settings['gpx_shadowAnchor_y'] = array(
			'version' => 'p1.2',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-gpx',
			'title'   => '',
			'desc'    => __( 'Shadow anchor', 'lmm' ) . ' (y) - ' . __( 'The x-coordinates of the shadow in pixel', 'lmm' ),
			'std'     => '47',
			'type'    => 'text-pro'
		);

		/*
		* Geolocate settings
		*/
		$this->_settings['geolocate_helptext'] = array(
			'version' => 'p1.9',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-geolocate',
			'std'     => '',
			'title'   => '',
			'desc'    => '<a style="background:#f99755;display:block;padding:3px;text-decoration:none;color:#2702c6;width:635px;margin:10px 0;" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade">' . __('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm') . '</a>' . __('Add a geolocate button to all maps which allows to show and follow your current location:','lmm') . '<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-geolocation.jpg" width="399" height="186" />',
			'type'    => 'helptext'
		);
		$this->_settings['geolocate_status'] = array(
			'version' => 'p1.9',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-geolocate',
			'title'   => __('Add a geolocate button to each map','lmm') . $pro_button_link,
			'desc'    => sprintf(__('Recommendation: only enable this feature if your maps are accessible via https, which is required by the following browsers: %1$s. For more details, <a href="%2$s" target="_blank">please click here</a>','lmm'), 'Chrome 50+, Safari 10+', 'http://mapsmarker.com/geolocation-https-only'),
			'type'    => 'radio-pro',
			'std'     => 'false',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);
		$this->_settings['geolocate_autostart'] = array(
			'version' => 'p2.3',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-geolocate',
			'title'   => __('Autostart','lmm') . $pro_button_link,
			'desc'    => sprintf(__('If set to true, geolocation will start automatically on each map (backend and frontend).<br/>For infos on how to start geolocation automatically for selected maps only, please see <a href="%1$S" target="_blank">this changelog</a>.','lmm'), 'https://www.mapsmarker.com/v2.3p'),
			'type'    => 'radio-pro',
			'std'     => 'false',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);
		$this->_settings['geolocate_icon'] = array(
			'version' => 'p1.9',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-geolocate',
			'title'   => __('icon','lmm') . $pro_button_link,
			'desc'    => '',
			'type'    => 'radio-pro',
			'std'     => 'icon-cross-hairs',
			'choices' => array(
				'icon-cross-hairs' => 'cross hairs <img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-icon-cross-hairs.png" width="20" height="20" />',
				'icon-pin' => 'pin <img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-icon-pin.png" width="16" height="17" />',
				'icon-arrow' => 'arrow <img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-icon-arrow.png" width="17" height="16" />'
			)
		);
		$this->_settings['geolocate_position'] = array(
			'version' => 'p1.9',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-geolocate',
			'title'   => __('Geolocate button position','lmm') . $pro_button_link,
			'desc'    => __('The position of the geolocate button (one of the map corners).','lmm'),
			'type'    => 'radio-pro',
			'std'     => 'topleft',
			'choices' => array(
				'topleft' => __('Top left of the map','lmm'),
				'topright' => __('Top right of the map','lmm'),
				'bottomleft' => __('Bottom left of the map','lmm'),
				'bottomright' => __('Bottom right of the map','lmm')
			)
		);
		$this->_settings['geolocate_drawCircle'] = array(
			'version' => 'p1.9',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-geolocate',
			'title'   => 'drawCircle' . $pro_button_link,
			'desc'    => __('controls whether a circle is drawn that shows the uncertainty about the location','lmm'),
			'type'    => 'radio-pro',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);
		$this->_settings['geolocate_drawMarker'] = array(
			'version' => 'p2.9',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-geolocate',
			'title'   => 'drawMarker' . $pro_button_link,
			'desc'    => esc_attr__('If set, the marker at the user´s location is drawn.','lmm'),
			'type'    => 'radio-pro',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);		
		$this->_settings['geolocate_setView'] = array(
			'version' => 'p2.9',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-geolocate',
			'title'   => 'setView' . $pro_button_link,
			'desc'    => esc_attr__('set the map view (zoom and pan) to the user´s location as it updates.','lmm'),
			'type'    => 'radio-pro',
			'std'     => 'untilPan',
			'choices' => array(
				'untilPan' => 'untilPan',
				'once' => __('once','lmm'),
				'always' => __('always','lmm'),
				'false' => __('false','lmm')
			)
		);
		$this->_settings['geolocate_keepCurrentZoomLevel'] = array(
			'version' => 'p1.9',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-geolocate',
			'title'   => 'keepCurrentZoomLevel' . $pro_button_link,
			'desc'    => esc_attr__('keep the current map zoom level when displaying the location of the user','lmm'),
			'type'    => 'radio-pro',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false (use maximum zoom level)','lmm')
			)
		);
		$this->_settings['geolocate_clickBehavior_inView'] = array(
			'version' => 'p2.9',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-geolocate',
			'title'   => 'clickBehavior inView' . $pro_button_link,
			'desc'    => sprintf(esc_attr__('What to do when the user clicks on the control (%1$s)','lmm'),'inView'),
			'type'    => 'radio-pro',
			'std'     => 'stop',
			'choices' => array(
				'stop' => __('stop','lmm'),
				'setView' => 'setView'
			)
		);
		$this->_settings['geolocate_clickBehavior_outOfView'] = array(
			'version' => 'p2.9',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-geolocate',
			'title'   => 'clickBehavior outOfView' . $pro_button_link,
			'desc'    => sprintf(esc_attr__('What to do when the user clicks on the control (%1$s)','lmm'),'outOfView'),
			'type'    => 'radio-pro',
			'std'     => 'setView',
			'choices' => array(
				'stop' => __('stop','lmm'),
				'setView' => 'setView'
			)
		);
		$this->_settings['geolocate_showPopup'] = array(
			'version' => 'p1.9',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-geolocate',
			'title'   => 'showPopup' . $pro_button_link,
			'desc'    => esc_attr__('display a popup when the user click on the inner marker','lmm'),
			'type'    => 'radio-pro',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);
		$this->_settings['geolocate_units'] = array(
			'version' => 'p1.9',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-geolocate',
			'title'   => __('Distance units','lmm') . $pro_button_link,
			'desc'    => '',
			'type'    => 'radio-pro',
			'std'     => 'true',
			'choices' => array(
				'true' => __('metric (km)','lmm'),
				'false' => __('imperial (miles)','lmm')
			)
		);
		$this->_settings['geolocate_circlePadding'] = array(
			'version' => 'p1.9',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-geolocate',
			'title'   => 'circlePadding' . $pro_button_link,
			'desc'    => __('padding around accuracy circle, value is passed to setBounds','lmm'),
			'std'     => '[0,0]',
			'type'    => 'text-pro'
		);
		$this->_settings['geolocate_circleStyle'] = array(
			'version' => 'p1.9',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-geolocate',
			'title'   => 'circleStyle' . $pro_button_link,
			'desc'    => sprintf(esc_attr__('change the style of the circle around the location of the user, example: %1$s','lmm'), "<br/>'color':'#136AEC','fillColor':'#136AEC','fillOpacity':'0.15','weight':'2','opacity':'0.5'"),
			'std'     => '',
			'type'    => 'text-pro'
		);
		$this->_settings['geolocate_markerStyle'] = array(
			'version' => 'p1.9',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-geolocate',
			'title'   => 'markerStyle' . $pro_button_link,
			'desc'    => sprintf(esc_attr__('change the style of the marker of the location of the user, example: %1$s','lmm'), "<br/>'color':'#136AEC','fillColor':'#136AEC','fillOpacity':'0.15','weight':'2','opacity':'0.5','radius':'5'"),
			'std'     => '',
			'type'    => 'text-pro'
		);
		$this->_settings['geolocate_followCircleStyle'] = array(
			'version' => 'p1.9',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-geolocate',
			'title'   => 'followCircleStyle' . $pro_button_link,
			'desc'    => sprintf(esc_attr__('change the style of the circle around the location of the user while following, example: %1$s','lmm'), "<br/>'color':'#136AEC','fillColor':'#136AEC','fillOpacity':'0.15','weight':'2','opacity':'0.5'"),
			'std'     => '',
			'type'    => 'text-pro'
		);
		$this->_settings['geolocate_followMarkerStyle'] = array(
			'version' => 'p1.9',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-geolocate',
			'title'   => 'followMarkerStyle' . $pro_button_link,
			'desc'    => sprintf(esc_attr__('change the style of the marker of the location of the user while following, example: %1$s','lmm'), "<br/>'color':'#136AEC','fillColor':'#136AEC','fillOpacity':'0.15','weight':'4','opacity':'0.5','radius':'5'"),
			'std'     => '',
			'type'    => 'text-pro'
		);
		$this->_settings['geolocate_locateOptions'] = array(
			'version' => 'p1.9',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-geolocate',
			'title'   => 'locateOptions' . $pro_button_link,
			'desc'    => sprintf(__('define additional location options e.g enableHighAccuracy: true, maxZoom: 10<br/>reference: %1$s','lmm'), '<a href="http://leafletjs.com/reference.html#map-locate-options" target="_blank">http://leafletjs.com/reference.html#map-locate-options</a>'),
			'std'     => 'watch: true',
			'type'    => 'text-pro'
		);

		/*
		* Mobile web app settings
		*/
		$this->_settings['map_webapp_helptext'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-mobile_web_app',
			'std'     => '',
			'title'   => '',
			'desc'    => __( 'Fullscreen maps are automatically optimized for mobile devices - this means that the viewport of the map automatically adjusts to the width of the device used.<br/><br/>For iOS devices (iphone, ipad, ipod) it is also possible to add maps with a custom icon to the homescreen and open them as web apps with a custom launch image and without the address bar of the browser.', 'lmm') . '<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-webapp.jpg" width="551" height="213" />'  . '<a style="background:#f99755;display:block;padding:3px;text-decoration:none;color:#2702c6;width:635px;margin:10px 0;" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade">' . __('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm') . '</a>',
			'type'    => 'helptext'
		);
		$this->_settings['map_webapp_images'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-mobile_web_app',
			'title'   => __('Images to use','lmm') . $pro_button_link,
			'desc'    => __('If you want to use custom images, please enter the URL to app icons and launch images below:','lmm'),
			'type'    => 'radio-pro',
			'std'     => 'default',
			'choices' => array(
				'none' => __('disable images','lmm'),
				'default' => __('use default images','lmm'),
				'custom' => __('use custom images','lmm')
			)
		);
		$this->_settings['map_webapp_icon57'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-mobile_web_app',
			'title'   => __('App icon URL','lmm') . $pro_button_link,
			'desc'    => __('Size for iPhone and iPod touch','lmm') . ' (57x57px)',
			'std'     => '',
			'type'    => 'text-pro'
		);
		$this->_settings['map_webapp_icon114'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-mobile_web_app',
			'title'   => __('App icon URL','lmm') . $pro_button_link,
			'desc'    => __('Size for high-resolution iPhone and iPod touch','lmm') . ' (114x114px)',
			'std'     => '',
			'type'    => 'text-pro'
		);
		$this->_settings['map_webapp_icon72'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-mobile_web_app',
			'title'   => __('App icon URL','lmm') . $pro_button_link,
			'desc'    => __('Size for iPad','lmm') . ' (72x72px)',
			'std'     => '',
			'type'    => 'text-pro'
		);
		$this->_settings['map_webapp_icon144'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-mobile_web_app',
			'title'   => __('App icon URL','lmm') . $pro_button_link,
			'desc'    => __('Size for high-resolution iPad','lmm') . ' (144x144px)',
			'std'     => '',
			'type'    => 'text-pro'
		);
		$this->_settings['map_webapp_launch1024'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-mobile_web_app',
			'title'   => __('Launch image URL','lmm') . $pro_button_link,
			'desc'    => __('Size for iPad','lmm') . ' (1024x748px)',
			'std'     => '',
			'type'    => 'text-pro'
		);
		$this->_settings['map_webapp_launch2048'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-mobile_web_app',
			'title'   => __('Launch image URL','lmm') . $pro_button_link,
			'desc'    => __('Size for high-resolution iPad','lmm') . ' (2048x1496px)',
			'std'     => '',
			'type'    => 'text-pro'
		);
		$this->_settings['map_webapp_launch768'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-mobile_web_app',
			'title'   => __('Launch image URL','lmm') . $pro_button_link,
			'desc'    => __('Size for iPad','lmm') . ' (768x1004px)',
			'std'     => '',
			'type'    => 'text-pro'
		);
		$this->_settings['map_webapp_launch1536'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-mobile_web_app',
			'title'   => __('Launch image URL','lmm') . $pro_button_link,
			'desc'    => __('Size for high-resolution iPad','lmm') . ' (1536x2008px)',
			'std'     => '',
			'type'    => 'text-pro'
		);
		$this->_settings['map_webapp_launch320'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-mobile_web_app',
			'title'   => __('Launch image URL','lmm') . $pro_button_link,
			'desc'    => __('Size for iPhone and iPod touch','lmm') . ' (320x460px)',
			'std'     => '',
			'type'    => 'text-pro'
		);
		$this->_settings['map_webapp_launch640'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-mobile_web_app',
			'title'   => __('Launch image URL','lmm') . $pro_button_link,
			'desc'    => __('Size for high-resolution iPhone and iPod touch','lmm') . ' (640x920px)',
			'std'     => '',
			'type'    => 'text-pro'
		);
		$this->_settings['map_webapp_launch640_1096'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-mobile_web_app',
			'title'   => __('Launch image URL','lmm') . $pro_button_link,
			'desc'    => __('Size for iPhone 5 and iPod 5','lmm') . ' (640x1096px)',
			'std'     => '',
			'type'    => 'text-pro'
		);

		/*
		* QR Code settings
		*/
		$this->_settings['qrcode_provider_helptext'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-qr_code',
			'std'     => '',
			'title'   => '',
			'desc'    => '',
			'type'    => 'helptext'
		);
		$this->_settings['qrcode_provider'] = array(
			'version' => '3.6',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-qr_code',
			'title'   => __('QR code provider','lmm'),
			'desc'    => __('Service provider used for creating QR codes for links to full screen maps','lmm'),
			'type'    => 'radio',
			'std'     => 'visualead',
			'choices' => array(
				'visualead' => 'Visualead',
				'google' => 'Google'
			)
		);
		$this->_settings['qrcode_visualead_helptext'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-qr_code',
			'std'     => '',
			'title'   => '<h4 class="h4-lmm-settings">' . __('Visualead settings','lmm') . '</h4>',
			'desc'    =>  '<img src="' . LEAFLET_PLUGIN_URL . 'inc/img/help-visualead.png" width="200" height="200" /><a style="background:#f99755;display:block;padding:3px;text-decoration:none;color:#2702c6;width:635px;margin:10px 0;" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade">' . __('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm') . '</a>',
			'type'    => 'helptext'
		);
		$this->_settings['qrcode_visualead_caching'] = array(
			'version' => 'p1.5',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-qr_code',
			'title'   => __('QR code caching','lmm') . $pro_button_link,
			'desc'    => __('Save generated QR codes in the following directory for higher performance and for saving API calls:','lmm') . ' n/a<br/>' . __('Cached QR images will automatically be deleted if the according marker or layer map gets deleted!','lmm'),
			'type'    => 'radio-pro',
			'std'     => 'disabled',
			'choices' => array(
				'enabled' => __('enabled','lmm'),
				'disabled' => __('disabled','lmm')
			)
		);
		$this->_settings['qrcode_visualead_api_key'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-qr_code',
			'title'   => __( 'API key', 'lmm' ) . $pro_button_link,
			'desc'    => __('If empty, the default API key from MapsMarker.com will be used','lmm') . ' (' . __('valid for default image only!','lmm') . ')',
			'std'     => '',
			'type'    => 'text-pro'
		);
		$this->_settings['qrcode_visualead_image_url'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-qr_code',
			'title'   => __( 'Image URL', 'lmm' ) . $pro_button_link,
			'desc'    => sprintf(__('A custom image can only be used if you sign up for a custom visualead API key! Please visit %s for more information.','lmm'), '<a href="https://www.mapsmarker.com/pro-feature-qrcode" target="_blank">mapsmarker.com/visualead</a>'),
			'std'     => '',
			'type'    => 'text-pro'
		);
		$this->_settings['qrcode_visualead_project_id'] = array(
			'version' => 'p1.5',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-qr_code',
			'title'   => 'project_id' . $pro_button_link,
			'desc'    => sprintf(__('If a visualead project_id is given, the generate_from_project method will be used instead of the generate method for creating QR codes with custom images (please note that the image url from above will also be ignored if a project_id is set - the image from the project_id is used instead). To create a new project, please log into the <a href="%s" target="_blank">visualead admin dashboard</a>.','lmm'), 'http://api.visualead.com/v3'),
			'std'     => '',
			'type'    => 'text-pro'
		);
		$this->_settings['qrcode_visualead_qr_size'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-qr_code',
			'title'   => __( 'QR size', 'lmm' ) . $pro_button_link,
			'desc'    => __('The width/height of the Visual QR Code (minimum: 124 pixel)','lmm'),
			'std'     => '124',
			'type'    => 'text-pro'
		);
		$this->_settings['qrcode_visualead_qr_cell_size'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-qr_code',
			'title'   => __( 'QR cell size', 'lmm' ) . $pro_button_link,
			'desc'    => esc_attr__('Force a specific size of the QR Code cell (Measured in pixels). Once this parameter is used then the "QR size" parameter is ignored. If the resulting QR Code size is bigger than the image, then the image is extended.','lmm'),
			'std'     => '',
			'type'    => 'text-pro'
		);
		$this->_settings['qrcode_visualead_qr_x'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-qr_code',
			'title'   => 'QR x' . $pro_button_link,
			'desc'    => __('The top left x position of the QR Code. (Measured in pixels)','lmm'),
			'std'     => '4',
			'type'    => 'text-pro'
		);
		$this->_settings['qrcode_visualead_qr_y'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-qr_code',
			'title'   => 'QR y' . $pro_button_link,
			'desc'    => __('The top left y position of the QR Code. (Measured in pixels)','lmm'),
			'std'     => '5',
			'type'    => 'text-pro'
		);
		$this->_settings['qrcode_visualead_qr_gravity'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-qr_code',
			'title'   => __( 'QR gravity', 'lmm' ) . $pro_button_link,
			'desc'    => esc_attr__('center/N/S/W/E (or common combinations such as NW or Scenter) - will position the QR Code accordingly. Once this parameter is used then the "qr_x and "qr_y" parameters are ignored.','lmm'),
			'std'     => '',
			'type'    => 'text-pro'
		);
		$this->_settings['qrcode_visualead_qr_rotation'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-qr_code',
			'title'   => __( 'QR rotation', 'lmm' ) . $pro_button_link,
			'desc'    => __('The angle of the QR Code rotation. Allowed angles: 0/90/180/270.','lmm'),
			'std'     => '0',
			'type'    => 'text-pro'
		);
		$this->_settings['qrcode_visualead_output_image_width'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-qr_code',
			'title'   => __( 'Output image width', 'lmm' ) . $pro_button_link,
			'desc'    => __('The desired image width returned in the response. If empty, the width of the image from image url will be used.','lmm'),
			'std'     => '',
			'type'    => 'text-pro'
		);
		$this->_settings['qrcode_visualead_cells_type'] = array(
			'version' => 'p1.5',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-qr_code',
			'title'   => 'cells_type' . $pro_button_link,
			'desc'    => '',
			'type'    => 'radio-pro',
			'std'     => '1',
			'choices' => array(
				'1' => __('squared','lmm'),
				'2' => __('rounded','lmm')
			)
		);
		$this->_settings['qrcode_visualead_markers_type'] = array(
			'version' => 'p1.5',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-qr_code',
			'title'   => 'markers_type' . $pro_button_link,
			'desc'    => '',
			'type'    => 'radio-pro',
			'std'     => '1',
			'choices' => array(
				'1' => __('squared','lmm'),
				'2' => __('rounded','lmm')
			)
		);
		$this->_settings['qrcode_google_helptext'] = array(
			'version' => 'p1.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-qr_code',
			'std'     => '',
			'title'   => '<h4 class="h4-lmm-settings">' . __('Google QR settings','lmm') . '</h4><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/help-google-qr.png" width="122" height="126" />',
			'desc'    => '',
			'type'    => 'helptext'
		);
		$this->_settings['misc_qrcode_size'] = array(
			'version' => '1.1',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-qr_code',
			'title'   => __( 'QR code image size', 'lmm' ),
			'desc'    => __( 'Width and height in pixel of QR code image for marker/layer standalone fullscreen map links', 'lmm' ),
			'std'     => '150',
			'type'    => 'text'
		);

		/*
		* Marker tooltip settings
		*/
		$this->_settings['marker_tooltip_helptext'] = array(
			'version' => 'p3.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_tooltip',
			'std'     => '',
			'title'   => '',
			'desc'    => __( 'Display the marker name as small texts on top of marker icons:', 'lmm') . '<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-marker-tooltip.jpg" width="201" height="81" />',
			'type'    => 'helptext'
		);
       $this->_settings['marker_tooltip_status'] = array(
            'version' => 'p3.0',
            'pane'    => 'mapdefaults',
            'section' => 'mapdefaults-marker_tooltip',
            'title'   => __('Marker tooltip status','lmm') . $pro_button_link,
            'desc'    => '',
            'type'    => 'radio-pro',
            'std'     => 'disabled',
            'choices' => array(
                'enabled' => __('enabled','lmm'),
                'disabled' => __('disabled','lmm')
            )
        );
		$this->_settings['marker_tooltip_permanent'] = array(
			'version' => 'p3.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_tooltip',
			'title'   => 'permanent' . $pro_button_link,
			'desc'    => __('Whether to show the tooltip permanently or only on mouseover','lmm'),
			'type'    => 'radio-pro',
			'std'     => 'false',
			'choices' => array(
				'false' => __('false (show tooltip only on mouseover)','lmm'),
				'true' => __('true','lmm')
			)
		);
		$this->_settings['marker_tooltip_sticky'] = array(
			'version' => 'p3.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_tooltip',
			'title'   => 'sticky' . $pro_button_link,
			'desc'    => __('If true, the tooltip will follow the mouse instead of being fixed at the set direction','lmm'),
			'type'    => 'radio-pro',
			'std'     => 'false',
			'choices' => array(
				'false' => __('false','lmm'),
				'true' => __('true','lmm')
			)
		);
		$this->_settings['marker_tooltip_direction'] = array(
			'version' => 'p3.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_tooltip',
			'title'   => __('Direction','lmm') . $pro_button_link,
			'desc'    => sprintf(__('Direction where to open the tooltip. %1$s will dynamicaly switch between right and left according to the tooltip position on the map.','lmm'), 'auto'),
			'type'    => 'radio-pro',
			'std'     => 'auto',
			'choices' => array(
				'auto' => 'auto - ' . sprintf(__('recommended offset (x/y): %1$s','lmm'), '0/0'),
				'right' => 'right - ' . sprintf(__('recommended offset (x/y): %1$s or %2$s','lmm'), '0/0', '11/-20'),
				'left' => 'left - ' . sprintf(__('recommended offset (x/y): %1$s or %2$s','lmm'), '0/0', '-13/-20'),
				'top' => 'top - ' . sprintf(__('recommended offset (x/y): %1$s','lmm'), '-2/-32'),
				'bottom' => 'bottom - ' . sprintf(__('recommended offset (x/y): %1$s','lmm'), '0/-1'),
				'center' => 'center - ' . sprintf(__('recommended offset (x/y): %1$s','lmm'), '0/20', '/')
			)
		);
        $this->_settings['marker_tooltip_offset_x'] = array(
            'version' => 'p3.0',
            'pane'    => 'mapdefaults',
            'section' => 'mapdefaults-marker_tooltip',
            'title'   => __( 'Offset (x)', 'lmm' ) . $pro_button_link,
            'desc'    => __('Optional offset value of the tooltip position.','lmm'),
            'std'     => '0',
            'type'    => 'text-pro'
        );		
        $this->_settings['marker_tooltip_offset_y'] = array(
            'version' => 'p3.0',
            'pane'    => 'mapdefaults',
            'section' => 'mapdefaults-marker_tooltip',
            'title'   => __( 'Offset (y)', 'lmm' ) . $pro_button_link,
            'desc'    => __('Optional offset value of the tooltip position.','lmm'),
            'std'     => '0',
            'type'    => 'text-pro'
        );		
		$this->_settings['marker_tooltip_interactive'] = array(
			'version' => 'p3.0',
			'pane'    => 'mapdefaults',
			'section' => 'mapdefaults-marker_tooltip',
			'title'   => 'interactive' . $pro_button_link,
			'desc'    => __('If true, the tooltip will listen to the feature events','lmm'),
			'type'    => 'radio-pro',
			'std'     => 'false',
			'choices' => array(
				'false' => __('false','lmm'),
				'true' => __('true','lmm')
			)
		);
        $this->_settings['marker_tooltip_opacity'] = array(
            'version' => 'p3.0',
            'pane'    => 'mapdefaults',
            'section' => 'mapdefaults-marker_tooltip',
            'title'   => __( 'Opacity', 'lmm' ) . $pro_button_link,
            'desc'    => __('Tooltip container opacity','lmm'),
            'std'     => '0.9',
            'type'    => 'text-pro'
        );

		/*===========================================
		*
		*
		* pane geocoding
		*
		*
		===========================================*/
		/*
		* Geocoding providers settings
		*/
		$this->_settings['geocoding_provider_helptext'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-general_settings',
			'std'     => '',
			'title'   => '',
			'desc'    => __('Geocoding is the process of transforming a description of a location - like an address, name or place - to a location on the earth&#39;s surface.','lmm') . '<br/>' . sprintf(__('%1$s allows you to choose from different geocoding providers which enables you to get the best results according to your needs.','lmm'), 'Maps Marker Pro') . '<br/>' . sprintf(__('For a comparision of supported geocoding providers please visit %1$s','lmm'), '<a href="https://www.mapsmarker.com/geocoding" target="_blank">https://www.mapsmarker.com/geocoding</a>'),
			'type'    => 'helptext'
		);
		$this->_settings['geocoding_provider'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-general_settings',
			'title'   => __('Main geocoding provider','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'algolia-places',
			'choices' => array(
                'algolia-places' => 'Algolia Places',
				'photon' => 'Photon@MapsMarker',
                 'mapzen-search' => '<span style="float:left;margin-right:5px;"><img src="'. LEAFLET_PLUGIN_URL .'inc/img/icon-good-mark.png" width="23" height="23" /></span> Mapzen Search <strong>(' . __('recommended','lmm') . ' - <a href="https://www.mapsmarker.com/mapzen-search" target="_blank">' . __('API key required','lmm') . '</a>)</strong>',
				'mapquest-geocoding' => 'MapQuest Geocoding <strong>(<a href="https://www.mapsmarker.com/mapquest-geocoding" target="_blank">' . __('API key required','lmm') . '</a>)</strong>',
				'google-geocoding' => 'Google Geocoding <strong>(<a href="https://www.mapsmarker.com/google-geocoding" target="_blank">' . __('API key required','lmm') . ')</a></strong>',
			)
		);
		$this->_settings['geocoding_provider_fallback'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-general_settings',
			'title'   => __('Fallback geocoding provider','lmm'),
			'desc'    => __('The fallback geocoding provider is used automatically if the main geocoding provider is unavailable','lmm'),
			'type'    => 'radio',
			'std'     => 'photon',
			'choices' => array(
				'algolia-places' => 'Algolia Places',
				'photon' => 'Photon@MapsMarker',
				'mapzen-search' => 'Mapzen Search <strong>(<a href="https://www.mapsmarker.com/mapzen-search" target="_blank">' . __('API key required','lmm') . '</a>)</strong>',
                'mapquest-geocoding' => 'MapQuest Geocoding <strong>(<a href="https://www.mapsmarker.com/mapquest-geocoding" target="_blank">' . __('API key required','lmm') . '</a>)</strong>',
                'google-geocoding' => 'Google Geocoding <strong>(<a href="https://www.mapsmarker.com/google-geocoding" target="_blank">' . __('API key required','lmm') . ')</a></strong>',
			)
		);
		$this->_settings['geocoding_provider_helptext2'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-general_settings',
			'std'     => '',
			'title'   => '<h4 class="h4-lmm-settings">' . __('Rate limit savings and performance settings','lmm') . '</h4>',
			'desc'    => '',
			'type'    => 'helptext'
		);
		$this->_settings['geocoding_typing_delay'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-general_settings',
			'title'   => __('Typing interval delay','lmm'),
			'desc'    => __( 'Delay in milliseconds after last character input in the search field on marker and layer edit pages before a request to the geocoding provider is sent.', 'lmm' ),
			'std'     => '400',
			'type'    => 'text'
		);
		$this->_settings['geocoding_min_chars_search_autostart'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-general_settings',
			'title'   => __('Minimum characters needed to start typeahead suggestions','lmm'),
			'desc'    => __( 'Typeahead suggestions will only show if you type at least the specified number of characters above into the location search field on marker and layer edit pages.', 'lmm' ),
			'std'     => '3',
			'type'    => 'text'
		);

		/*
		* Algolia Places settings
		*/
		$this->_settings['geocoding_algolia_helptext'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-algolia',
			'std'     => '',
			'title'   => '',
			'desc'    => '<a href="https://www.mapsmarker.com/algolia-places/" target="_blank"><img src="'. LEAFLET_PLUGIN_URL .'inc/img/geocoding/algolia-places.png" width="674" height="160" /></a><br/><br/>' . sprintf(__('<a href="%1$s" target="_blank">%2$s</a> allows up to %3$s requests/domain/day and a maximum of %4$s requests/%5$s without registration - just select "%6$s" as preferred "Geocoding provider" in the according tab on the left to start using the service.','lmm'), 'https://www.mapsmarker.com/algolia-places/', 'Algolia Places', '1.000', '15', __('second','lmm'), 'Algolia Places'),
			'type'    => 'helptext'
		);
		$this->_settings['geocoding_algolia_helptext2'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-algolia',
			'std'     => '',
			'title'   => '<h4 class="h4-lmm-settings">' . __('Authentication settings','lmm') . '</h4>',
			'desc'    =>  sprintf(__('With <a href="%1$s" target="_blank">free authentication</a> up to %2$s request per domain/month are allowed. <a href="%3$s" target="_blank">Paid plans with even higher limits are available upon request</a>.','lmm'), 'https://www.algolia.com/users/sign_up/places', '100.000', 'https://community.algolia.com/places/contact.html'),
			'type'    => 'helptext'
		);
		$this->_settings['geocoding_algolia_appId'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-algolia',
			'title'   => 'appId',
			'desc'    => __('If using the authenticated API, the Application ID to use.','lmm'),
			'std'     => '',
			'type'    => 'text'
		);
		$this->_settings['geocoding_algolia_apiKey'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-algolia',
			'title'   => 'apiKey',
			'desc'    => __('If using the authenticated API, the API key to use.','lmm'),
			'std'     => '',
			'type'    => 'text'
		);
		$this->_settings['geocoding_algolia_helptext3'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-algolia',
			'std'     => '',
			'title'   => '<h4 class="h4-lmm-settings">' . __('Advanced settings','lmm') . '</h4>',
			'desc'    => '',
			'type'    => 'helptext'
		);
		$this->_settings['geocoding_algolia_aroundLatLng'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-algolia',
			'title'   => 'aroundLatLng',
			'desc'    => __('Force to first search around a specific latitude longitude. The option value must be provided as a string: latitude,longitude like 12.232,23.1. The default is to search around the location of the user determined via his IP address (geoip).','lmm'),
			'std'     => '',
			'type'    => 'text'
		);
		$this->_settings['geocoding_algolia_aroundLatLngViaIP'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-algolia',
			'title'   => 'aroundLatLngViaIP',
			'desc'    => __( 'Whether or not to first search around the geolocation of the user found via his IP address.', 'lmm' ),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'false' => __('false', 'lmm'),
				'true' => __('true', 'lmm')
			)
		);
		$this->_settings['geocoding_algolia_language'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-algolia',
			'title'   => __('language','lmm'),
			'desc'    => sprintf(__('Change the default language of the results. You can pass two letters country codes (<a href="%1$s" target="_blank">ISO 639-1</a>).','lmm'), 'https://en.wikipedia.org/wiki/ISO_3166-1#Officially_assigned_code_elements') . '<br/>' . sprintf(__('By default the language will be retrieved from the WordPress global variable $locale = %1$s (with a fallback to %2$s if not supported)','lmm'), '<strong>' . get_locale() . '</strong>', 'en'),
			'std'     => '',
			'type'    => 'text'
		);
		$this->_settings['geocoding_algolia_countries'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-algolia',
			'title'   => __('countries','lmm'),
			'desc'    => sprintf(__('Change the countries to search in. You must pass an array of two letters country codes. You can pass two letters country codes (<a href="%1$s" target="_blank">ISO 639-1</a>). Default: Search on the whole planet.','lmm'), 'https://en.wikipedia.org/wiki/ISO_3166-1#Officially_assigned_code_elements'),
			'std'     => '',
			'type'    => 'text'
		);

		/*
		* Photon@MapsMarker settings
		*/
		$this->_settings['geocoding_photon_helptext'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-photon',
			'std'     => '',
			'title'   => '',
			'desc'    => '<a href="https://www.mapsmarker.com/photon/" target="_blank"><img src="'. LEAFLET_PLUGIN_URL .'inc/img/geocoding/photon-mapsmarker.png" width="486" height="78" /></a><br/><br/>' . sprintf(__('<a href="%1$s" target="_blank">%2$s</a> allows up to %3$s requests/domain/day and a maximum of %4$s requests/%5$s without registration - just select "%6$s" as preferred "Geocoding provider" in the according tab on the left to start using the service.','lmm'), 'https://www.mapsmarker.com/photon/', 'Photon@MapsMarker', '2.500', '10', __('second','lmm'), 'Photon@MapsMarker'),
			'type'    => 'helptext'
		);
		$this->_settings['geocoding_photon_language'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-photon',
			'title'   => __('Language','lmm'),
			'desc'    => sprintf(__('By default the language will be retrieved from the WordPress global variable $locale = %1$s (with a fallback to %2$s if not supported)','lmm'), '<strong>' . get_locale() . '</strong>', __('English','lmm')),
			'type'    => 'radio',
			'std'     => 'automatic',
			'choices' => array(
				'automatic' => __('automatic','lmm'),
				'en' => __('English','lmm'),
				'de' => __('German','lmm'),
				'fr' => __('French','lmm'),
				'it' => __('Italian','lmm')
			)
		);
		$this->_settings['geocoding_photon_helptext2'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-photon',
			'std'     => '',
			'title'   => '<h4 class="h4-lmm-settings">' . __('Location Biasing','lmm') . '</h4>',
			'desc'    => __('To focus your search based upon a geographical area, please provide latitude and longitude values.','lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['geocoding_photon_location_bias_lat'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-photon',
			'title'   => __('Latitude','lmm'),
			'desc'    => '',
			'std'     => '',
			'type'    => 'text'
		);
		$this->_settings['geocoding_photon_location_bias_lon'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-photon',
			'title'   => __('Longitude','lmm'),
			'desc'    => '',
			'std'     => '',
			'type'    => 'text'
		);
		$this->_settings['geocoding_photon_helptext3'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-photon',
			'std'     => '',
			'title'   => '<h4 class="h4-lmm-settings">' . __('Filter results by tags and values','lmm') . '</h4>',
			'desc'    => '',
			'type'    => 'helptext'
		);
		$this->_settings['geocoding_photon_filter_results'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-photon',
			'title'   => __('Filter to use','lmm'),
			'desc'    => sprintf(__('For a detailed documentation <a href="%1$s" target="_blank">please click here</a>','lmm'), 'https://github.com/komoot/photon#filter-results-by-tags-and-values'),
			'std'     => '',
			'type'    => 'text'
		);

		/*
		* Mapzen Search settings
		*/
		$this->_settings['geocoding_mapzen_search_helptext'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-mapzen',
			'std'     => '',
			'title'   => '',
			'desc'    => '<a href="https://www.mapsmarker.com/mapzen-search/" target="_blank"><img src="'. LEAFLET_PLUGIN_URL .'inc/img/geocoding/mapzen-search-large.png" width="293" height="108" alt="pro option" /></a><br/><br/>' . sprintf(__('<a href="%1$s" target="_blank">Mapzen Search</a> is a search engine for places worldwide, powered by open data and as result our recommended geolocation provider.','lmm'), 'https://www.mapsmarker.com/mapzen-search/'),
			'type'    => 'helptext'
		);
		$this->_settings['geocoding_mapzen_search_helptext2'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-mapzen',
			'std'     => '',
			'title'   => '<h4 class="h4-lmm-settings">' . __('Authentication settings','lmm') . '</h4>',
			'desc'    =>  sprintf(__('With a <a href="%1$s" target="_blank">free API key</a> up to %2$s requests/%3$s and a maximum of %4$s requests/%5$s are allowed. <a href="%6$s" target="_blank">Even higher limits are available upon request</a>.','lmm'), 'https://mapzen.com/developers/sign_in', '30.000', __('day','lmm'), '6', __('second','lmm'), 'mailto:hello@mapzen.com?subject=Maps Marker Pro - request for higher API limits'),
			'type'    => 'helptext'
		);
		$this->_settings['geocoding_mapzen_search_api_key'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-mapzen',
			'title'   => __('API key','lmm') . ' (' . __('required','lmm') . ')',
			'desc'    => sprintf(__('Get your free API key at %1$s','lmm'),'<a href="https://mapzen.com/developers/" target="_blank">https://mapzen.com/developers/</a>'),
			'std'     => '',
			'type'    => 'text'
		);
		$this->_settings['geocoding_mapzen_search_helptext3'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-mapzen',
			'std'     => '',
			'title'   => '<h2 class="h3-lmm-settings">' . __('Advanced settings','lmm') . '</h2><h4 class="h4-lmm-settings">' . __('Local focus point','lmm') . '</h4>',
			'desc'    => __('To focus your search based upon a geographical area, please provide latitude and longitude values.','lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['geocoding_mapzen_search_focus_lat'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-mapzen',
			'title'   => __('Latitude','lmm'),
			'desc'    => '',
			'std'     => '',
			'type'    => 'text'
		);
		$this->_settings['geocoding_mapzen_search_focus_lon'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-mapzen',
			'title'   => __('Longitude','lmm'),
			'desc'    => '',
			'std'     => '',
			'type'    => 'text'
		);
		$this->_settings['geocoding_mapzen_search_helptext4'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-mapzen',
			'std'     => '',
			'title'   => '<h4 class="h4-lmm-settings">' . __('Search within a particular country','lmm') . '</h4>',
			'desc'    => '',
			'type'    => 'helptext'
		);


		$this->_settings['geocoding_mapzen_search_country'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-mapzen',
			'title'   => __('Country code','lmm'),
			'desc'    => sprintf(__( 'Restrict search results be from a particular country. You can pass alpha-2 or alpha-3 <a href="%1$s" target="_blank">ISO-3166 country codes</a>.', 'lmm' ), 'https://en.wikipedia.org/wiki/ISO_3166-1'),
			'std'     => '',
			'type'    => 'text'
		);
		$this->_settings['geocoding_mapzen_search_helptext5'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-mapzen',
			'std'     => '',
			'title'   => '<h4 class="h4-lmm-settings">' . __('Narrow search','lmm') . '</h4>',
			'desc'    => sprintf(__('Limit your search to a specific <a href="%1$s" target="_blank">rectangle</a> or <a href="%2$s" target="_blank">circle</a>.','lmm'),'https://mapzen.com/documentation/search/search/#search-within-a-rectangular-region','https://mapzen.com/documentation/search/search/#search-within-a-circular-region'),
			'type'    => 'helptext'
		);
		$this->_settings['geocoding_mapzen_search_narrow_search'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-mapzen',
			'title'   => __('Narrow search status','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'disabled',
			'choices' => array(
				'disabled' => __('disabled','lmm'),
				'rectangle' => __('use rectangle','lmm'),
				'circle' => __('use circle','lmm')
			)
		);
		$this->_settings['geocoding_mapzen_search_narrow_rect_lat_min'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-mapzen',
			'title'   => __( 'Latitude', 'lmm' ) . ' min (' . __('rectangle','lmm') . ')',
			'desc'    => '',
			'std'     => '48.326583',
			'type'    => 'text'
		);
		$this->_settings['geocoding_mapzen_search_narrow_rect_lon_min'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-mapzen',
			'title'   => __( 'Longitude', 'lmm' ) . ' min (' . __('rectangle','lmm') . ')',
			'desc'    => '',
			'std'     => '16.55056',
			'type'    => 'text'
		);
		$this->_settings['geocoding_mapzen_search_narrow_rect_lat_max'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-mapzen',
			'title'   => __( 'Latitude', 'lmm' ) . ' max (' . __('rectangle','lmm') . ')',
			'desc'    => '',
			'std'     => '48.114308',
			'type'    => 'text'
		);
		$this->_settings['geocoding_mapzen_search_narrow_rect_lon_max'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-mapzen',
			'title'   => __( 'Longitude', 'lmm' ) . ' max (' . __('rectangle','lmm') . ')',
			'desc'    => '',
			'std'     => '16.187325',
			'type'    => 'text'
		);
		$this->_settings['geocoding_mapzen_search_narrow_circle_lat'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-mapzen',
			'title'   => __( 'Latitude', 'lmm' ) . ' (' . __('circle','lmm') . ')',
			'desc'    => '',
			'std'     => '48.326583',
			'type'    => 'text'
		);
		$this->_settings['geocoding_mapzen_search_narrow_circle_lon'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-mapzen',
			'title'   => __( 'Longitude', 'lmm' ) . ' (' . __('circle','lmm') . ')',
			'desc'    => '',
			'std'     => '16.55056',
			'type'    => 'text'
		);
		$this->_settings['geocoding_mapzen_search_narrow_circle_radius'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-mapzen',
			'title'   => __( 'Radius', 'lmm' ) . ' (' . __('circle','lmm') . ')',
			'desc'    => __( 'Radius in kilometer (km)', 'lmm' ),
			'std'     => '100',
			'type'    => 'text'
		);
		$this->_settings['geocoding_mapzen_search_helptext6'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-mapzen',
			'std'     => '',
			'title'   => '<h4 class="h4-lmm-settings">' . __('Sources','lmm') . '</h4>',
			'desc'    => __('Specify from which data sources you would like to receive results:','lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['geocoding_mapzen_search_sources_osm'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-mapzen',
			'title'    => 'OpenStreetmap',
			'desc'    => '',
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['geocoding_mapzen_search_sources_oa'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-mapzen',
			'title'    => 'OpenAddresses',
			'desc'    => '',
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['geocoding_mapzen_search_sources_geonames'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-mapzen',
			'title'    => 'Geonames',
			'desc'    => '',
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['geocoding_mapzen_search_sources_wof'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-mapzen',
			'title'    => 'Who&#39;s On First',
			'desc'    => '',
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['geocoding_mapzen_search_helptext7'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-mapzen',
			'std'     => '',
			'title'   => '<h4 class="h4-lmm-settings">' . __('Layers','lmm') . '</h4>',
			'desc'    => __('The type of record is referred to as its layer. All records are indexed into the layers.','lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['geocoding_mapzen_search_layer'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-mapzen',
			'title'   => __('Specific layer to use','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'none',
			'choices' => array(
				'none' => __('none (default setting, recommended)','lmm'),
				'venue' => 'venue',
				'address' => 'address',
				'street' => 'street<br/><br/>',
				'coarse' => __('all the administrative hierarchy layers below','lmm'),
				'microhood' => 'microhood',
				'neighbourhood' => 'neighbourhood',
				'macrohood' => 'macrohood',
				'county' => 'county',
				'macrocounty' => 'macrocounty',
				'localadmin' => 'localadmin',
				'locality' => 'locality',
				'borough' => 'borough',
				'region' => 'region',
				'country' => 'country'
			)
		);

        /*
        * MapQuest Geocoding settings
        */
        $this->_settings['geocoding_mapquest_geocoding_helptext'] = array(
            'version' => '3.11',
            'pane'    => 'geocoding',
            'section' => 'geocoding-mapquest',
            'std'     => '',
            'title'   => '',
            'desc'    => '<a href="https://www.mapsmarker.com/mapquest-geocoding/" target="_blank"><img src="'. LEAFLET_PLUGIN_URL .'inc/img/geocoding/mapquest-logo.png" width="581" height="113" /></a><br/><br/>' . sprintf(__('MapQuest Geocoding API allows up to %1$s transactions/month and a maximum of %2$s requests/%3$s with a free API key. Higher quotas are available on demand - <a href="%4$s" target="_blank">click here for more details</a>.','lmm'), '15.000', '10', __('second','lmm'), 'https://developer.mapquest.com/plans') . '<br/><br/><span style="font-weight:bold;color:red;">' . __('Please note that MapQuest basemaps should also be used if MapQuest geocoding is selected!','lmm') . '</span><br/>' . sprintf(__('<a href="%1$s" target="_blank">Click here</a> for a tutorial on how to activate MapQuest basemaps.','lmm'), 'https://www.mapsmarker.com/mapquest-api-key'),
            'type'    => 'helptext'
        );
        $this->_settings['geocoding_mapquest_geocoding_api_key'] = array(
            'version' => '3.11',
            'pane'    => 'geocoding',
            'section' => 'geocoding-mapquest',
            'title'   => __('MapQuest Geocoding API key (required)','lmm'),
            'desc'    => sprintf(__('For a tutorial on how to get your free MapQuest Geocoding API key, please <a href="%1$s" target="_blank">click here</a>','lmm'),'https://www.mapsmarker.com/mapquest-geocoding'),
            'std'     => '',
            'type'    => 'text'
        );
		$this->_settings['geocoding_mapquest_geocoding_helptext2'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-mapquest',
			'std'     => '',
			'title'   => '',
			'desc'    => '<h4 class="h4-lmm-settings">' . __('Location Biasing','lmm') . '</h4>',
			'type'    => 'helptext'
		);
        $this->_settings['geocoding_mapquest_geocoding_bounds_status'] = array(
            'version' => '3.11',
            'pane'    => 'geocoding',
            'section' => 'geocoding-mapquest',
            'title'   => __('MapQuest Geocoding bounds','lmm'),
            'desc'    => __( 'When using batch geocoding or when ambiguous results are returned, any results within the provided bounding box will be moved to the top of the results list. Below you find an example for Vienna/Austria:', 'lmm') . '<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'inc/img/geocoding/bounds-example.jpg" width="425" height="334" />',
            'type'    => 'radio',
            'std'     => 'disabled',
            'choices' => array(
                'enabled' => __('enabled','lmm'),
                'disabled' => __('disabled','lmm')
            )
        );
        $this->_settings['geocoding_mapquest_geocoding_bounds_lat1'] = array(
            'version' => '3.11',
            'pane'    => 'geocoding',
            'section' => 'geocoding-mapquest',
            'title'   => __( 'Latitude', 'lmm' ) . ' 1',
            'desc'    => '',
            'std'     => '48.326583',
            'type'    => 'text'
        );
        $this->_settings['geocoding_mapquest_geocoding_bounds_lon1'] = array(
            'version' => '3.11',
            'pane'    => 'geocoding',
            'section' => 'geocoding-mapquest',
            'title'   => __( 'Longitude', 'lmm' ) . ' 1',
            'desc'    => '',
            'std'     => '16.55056',
            'type'    => 'text'
        );
        $this->_settings['geocoding_mapquest_geocoding_bounds_lat2'] = array(
            'version' => '3.11',
            'pane'    => 'geocoding',
            'section' => 'geocoding-mapquest',
            'title'   => __( 'Latitude', 'lmm' ) . ' 2',
            'desc'    => '',
            'std'     => '48.114308',
            'type'    => 'text'
        );
        $this->_settings['geocoding_mapquest_geocoding_bounds_lon2'] = array(
            'version' => '3.11',
            'pane'    => 'geocoding',
            'section' => 'geocoding-mapquest',
            'title'   => __( 'Longitude', 'lmm' ) . ' 2',
            'desc'    => '',
            'std'     => '16.187325',
            'type'    => 'text'
        );

		/*
		* Google Geocoding settings
		*/
		$this->_settings['geocoding_google_geocoding_helptext'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-google',
			'std'     => '',
			'title'   => '',
			'desc'    => sprintf(__('If you enable Google as geocoding provider by filling in your credentials below, "<a href="%1$s" target="_blank">Google Places API Web Service</a>" (allowing up to %2$s free requests/day) will be available for manual location search.','lmm'), 'https://developers.google.com/places/web-service/', '1.000') . ' ' . sprintf(__('If you enable pay-as-you-go billing by verifying your identity to Google with a credit card, up to %1$s free requests/%2$s-hour-period for manual location search are allowed - <a href="%3$s" target="_blank">click here for more details</a>. If you need an even higher quota, you need to purchase a <a href="%4$s" target="_blank">Google Maps APIs Premium Plan license</a>.','lmm'), '150.000', '24', 'https://developers.google.com/maps/documentation/geocoding/usage-limits', 'https://developers.google.com/places/web-service/usage#purchase-m4w') . '<br/><br/>' . sprintf(__('For the <a href="%1$s">import feature</a> and the <a href="%2$s">Maps Marker APIs</a>, "<a href="%3$s" target="_blank">Google Maps Geocoding API</a>" (allowing up to %4$s free requests/day and a maximum of %5$s requests/%6$s) can be used. Once billing at Google has been enabled, you can exceed %7$s requests/day and you will be billed by Google at %8$s USD/%9$s additional requests, up to %10$s daily. If you need an even higher quota, you need to purchase a <a href="%11$s" target="_blank">Google Maps APIs Premium Plan license</a>.','lmm'), LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_import_export', LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_apis', 'https://developers.google.com/maps/documentation/geocoding/', '2.500', '50', __('second','lmm'), '2.500', '0.50', '1.000', '100.000', 'https://developers.google.com/maps/premium/overview'),
			'type'    => 'helptext'
		);
		$this->_settings['geocoding_google_geocoding_auth_method'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-google',
			'title'   => __('Authentication method','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'api-key',
			'choices' => array(
				'api-key' => __('Google API server key','lmm'),
				'clientid-signature' => 'client ID + signature (' . __('Google Maps APIs Premium Plan customers only','lmm') . ')'
			)
		);
		$this->_settings['geocoding_google_geocoding_api_key'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-google',
			'title'   => __('Google API server key','lmm') . ' (' . __('required','lmm') . ')',
			'desc'    => sprintf(__('For a tutorial on how to register a free Google API server key for "Google Places API Web Service" and "Google Maps Geocoding API", please <a href="%1$s" target="_blank">click here</a>','lmm'),'https://www.mapsmarker.com/google-geocoding'),
			'std'     => '',
			'type'    => 'text'
		);
		$this->_settings['geocoding_google_geocoding_helptext2'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-google',
			'std'     => '',
			'title'   => '',
			'desc'    => '<h4 class="h4-lmm-settings">' . __('Authentication for Google Maps APIs Premium Plan customers','lmm') . '</h4><br/>' . sprintf(__('If you are a <a href="%1$s" target="_blank">Google Maps APIs Premium Plan</a> customer, please change the authentication method above to %2$s and fill in the credentials below which you received in the welcome email for "Google Maps APIs Premium Plan"-customers from Google.','lmm'), 'https://developers.google.com/maps/premium/overview', '"client ID + signature"'),
			'type'    => 'helptext'
		);
		$this->_settings['geocoding_google_geocoding_premium_client'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-google',
			'title'   => 'client ID (' . __('required','lmm') . ')',
			'desc'    => '',
			'std'     => '',
			'type'    => 'text'
		);
		$this->_settings['geocoding_google_geocoding_premium_signature'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-google',
			'title'   => 'signature (' . __('required','lmm') . ')',
			'desc'    => '',
			'std'     => '',
			'type'    => 'text'
		);
		$this->_settings['geocoding_google_geocoding_premium_channel'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-google',
			'title'   => 'channel (' . __('optional','lmm') . ')',
			'desc'    => '',
			'std'     => '',
			'type'    => 'text'
		);
		$this->_settings['geocoding_google_geocoding_helptext3'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-google',
			'std'     => '',
			'title'   => '<h4 class="h4-lmm-settings">' . __('Location Biasing','lmm') . '</h4>',
			'desc'    => __('You may bias results to a specified circle by passing a location and a radius parameter. This instructs the Place Autocomplete service to prefer showing results within that circle. Results outside of the defined area may still be displayed. You can use the components parameter to filter results to show only those places within a specified country.','lmm') . ' ' . __('If you would prefer to have no location bias, set the location to 0,0 and radius to 20000000 (20 thousand kilometers), to encompass the entire world.','lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['geocoding_google_geocoding_location'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-google',
			'title'   => 'location',
			'desc'    => sprintf(__('The point around which you wish to retrieve place information. Must be specified as latitude,longitude (e.g. %1$s). Can be retrieved for any location by switching to advanced editor on backend for example.','lmm'), '48.216038,16.378984'),
			'std'     => '0,0',
			'type'    => 'text'
		);
		$this->_settings['geocoding_google_geocoding_radius'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-google',
			'title'   => 'radius',
			'desc'    => __('The distance (in meters) within which to return place results. Note that setting a radius biases results to the indicated area, but may not fully restrict results to the specified area.','lmm'),
			'std'     => '20000000',
			'type'    => 'text'
		);
		$this->_settings['geocoding_google_geocoding_helptext4'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-google',
			'std'     => '',
			'title'   => '<h4 class="h4-lmm-settings">' . __('Advanced settings','lmm') . '</h4>',
			'desc'    => '',
			'type'    => 'helptext'
		);
		$this->_settings['geocoding_google_geocoding_language'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-google',
			'title'   => __('language','lmm'),
			'desc'    => sprintf(__('The language in which to return results. <a href="%1$s" target="_blank">See the list of supported domain languages</a>. If you set a specific language at Settings / Google / "Google language localization", that language will also be used for Google Geocoder. If no language is set, the current WordPress locale (%2$s) will be used.','lmm'), 'https://developers.google.com/maps/faq#languagesupport', substr(get_locale(),0,2)),
			'std'     => '',
			'type'    => 'text'
		);
		$this->_settings['geocoding_google_geocoding_region'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-google',
			'title'   => __('region','lmm'),
			'desc'    => sprintf(__('Optional region code, specified as a ccTLD ("top-level domain") two-character value. This parameter will only influence, not fully restrict, results from the geocoder. For more information see <a href="%1$s" target="_blank">Region Biasing</a>.','lmm'), 'https://developers.google.com/maps/documentation/geocoding/intro#RegionCodes'),
			'std'     => '',
			'type'    => 'text'
		);
		$this->_settings['geocoding_google_geocoding_components'] = array(
			'version' => '3.11',
			'pane'    => 'geocoding',
			'section' => 'geocoding-google',
			'title'   => __('components','lmm'),
			'desc'    => sprintf(__('Optional component filters, separated by a pipe (|). Each component filter consists of a component:value pair and will fully restrict the results from the geocoder. For more information see <a href="%1$s" target="_blank">Component Filtering</a>.','lmm'), 'https://developers.google.com/maps/documentation/geocoding/intro#ComponentFiltering'),
			'std'     => '',
			'type'    => 'text'
		);

		/*===========================================
		*
		*
		* pane basemaps
		*
		*
		===========================================*/
		/*
		* Default basemap for new markers/layers
		*/
		$this->_settings['default_basemap_helptext1'] = array(
			'version' => '1.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-default_basemap',
			'title'   => '',
			'desc'    => __( 'Please select the basemap which should be pre-selected as default for new markers and layers. Can be changed afterwards on each marker/layer.', 'lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['standard_basemap'] = array(
			'version' => '3.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-default_basemap',
			'title'   => __('Default basemap','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'osm_mapnik',
			'choices' => array(
				'osm_mapnik' => 'OpenStreetMap',
				'stamen_terrain' => __('Stamen Terrain','lmm'),
				'stamen_toner' => __('Stamen Toner','lmm'),
				'stamen_watercolor' => __('Stamen Watercolor','lmm'),
				'mapquest_osm' => 'MapQuest (OSM) - <strong>' . __('API key required!','lmm') . '</strong> <a href="https://www.mapsmarker.com/mapquest-api-key" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
				'mapquest_aerial' => 'MapQuest (Aerial) - <strong>' . __('API key required!','lmm') . '</strong> <a href="https://www.mapsmarker.com/mapquest-api-key" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
				'mapquest_hybrid' => 'MapQuest (Hybrid) - <strong>' . __('API key required!','lmm') . '</strong> <a href="https://www.mapsmarker.com/mapquest-api-key" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
				'googleLayer_roadmap' => __('Google Maps (Roadmap)','lmm')  . ' - <strong>' . __('API key required!','lmm') . '</strong> <a href="https://www.mapsmarker.com/google-maps-javascript-api" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
				'googleLayer_satellite' => __('Google Maps (Satellite)','lmm')  . ' - <strong>' . __('API key required!','lmm') . '</strong> <a href="https://www.mapsmarker.com/google-maps-javascript-api" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
				'googleLayer_hybrid' => __('Google Maps (Hybrid)','lmm')  . ' - <strong>' . __('API key required!','lmm') . '</strong> <a href="https://www.mapsmarker.com/google-maps-javascript-api" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
				'googleLayer_terrain' => __('Google Maps (Terrain)','lmm')  . ' - <strong>' . __('API key required!','lmm') . '</strong> <a href="https://www.mapsmarker.com/google-maps-javascript-api" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
				'bingaerial' => __('Bing Maps (Aerial)','lmm') . ' - <strong>' . __('API key required!','lmm') . '</strong> <a href="https://www.mapsmarker.com/bing-maps" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
				'bingaerialwithlabels' => __('Bing Maps (Aerial+Labels)','lmm') . ' - <strong>' . __('API key required!','lmm'). '</strong> <a href="https://www.mapsmarker.com/bing-maps" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
				'bingroad' => __('Bing Maps (Road)','lmm') . ' - <strong>' . __('API key required!','lmm'). '</strong> <a href="https://www.mapsmarker.com/bing-maps" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
				'ogdwien_basemap' => 'basemap.at',
				'ogdwien_satellite' => __('basemap.at satellite','lmm'),
				'mapbox' => 'MapBox 1',
				'mapbox2' => 'MapBox 2',
				'mapbox3' => 'MapBox 3',
				'custom_basemap' => __('Custom basemap','lmm'),
				'custom_basemap2' => __('Custom basemap 2','lmm'),
				'custom_basemap3' => __('Custom basemap 3','lmm'),
				'empty_basemap' => __('empty basemap','lmm')
			)
		);

		/*
		* Available basemaps in controlbox
		*/
		$this->_settings['default_basemap_helptext3'] = array(
			'version' => '1.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-available_basemaps_controlbox',
			'std'     => '',
			'title'    => '',
			'desc'    => __( 'Please select the basemaps which should be available in the control box.', 'lmm').'<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-default-basemap-available-basemaps.jpg" width="386" height="290" />',
			'type'    => 'helptext'
		);
		$this->_settings['controlbox_osm_mapnik'] = array(
			'version' => '1.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-available_basemaps_controlbox',
			'title'   => __( 'Available basemaps in control box', 'lmm' ),
			'desc'    => 'OpenStreetMap',
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['controlbox_stamen_terrain'] = array(
			'version' => '3.11',
			'pane'    => 'basemaps',
			'section' => 'basemaps-available_basemaps_controlbox',
			'title'   => '',
			'desc'    => __( 'Stamen Terrain', 'lmm' ),
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['controlbox_stamen_toner'] = array(
			'version' => '3.11',
			'pane'    => 'basemaps',
			'section' => 'basemaps-available_basemaps_controlbox',
			'title'   => '',
			'desc'    => __( 'Stamen Toner', 'lmm' ),
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['controlbox_stamen_watercolor'] = array(
			'version' => '3.11',
			'pane'    => 'basemaps',
			'section' => 'basemaps-available_basemaps_controlbox',
			'title'   => '',
			'desc'    => __( 'Stamen Watercolor', 'lmm' ),
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['controlbox_mapquest_osm'] = array(
			'version' => '3.11',
			'pane'    => 'basemaps',
			'section' => 'basemaps-available_basemaps_controlbox',
			'title'   => '',
			'desc'    => __('MapQuest (OSM)','lmm')  . ' - <strong>' . __('API key required!','lmm') . '</strong> <a href="https://www.mapsmarker.com/mapquest-api-key" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['controlbox_mapquest_aerial'] = array(
			'version' => '3.11',
			'pane'    => 'basemaps',
			'section' => 'basemaps-available_basemaps_controlbox',
			'title'   => '',
			'desc'    => __('MapQuest (Aerial)','lmm')  . ' - <strong>' . __('API key required!','lmm') . '</strong> <a href="https://www.mapsmarker.com/mapquest-api-key" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['controlbox_mapquest_hybrid'] = array(
			'version' => '3.11',
			'pane'    => 'basemaps',
			'section' => 'basemaps-available_basemaps_controlbox',
			'title'   => '',
			'desc'    => __('MapQuest (Hybrid)','lmm')  . ' - <strong>' . __('API key required!','lmm') . '</strong> <a href="https://www.mapsmarker.com/mapquest-api-key" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['controlbox_googleLayer_roadmap'] = array(
			'version' => '2.5',
			'pane'    => 'basemaps',
			'section' => 'basemaps-available_basemaps_controlbox',
			'title'   => '',
			'desc'    => __('Google Maps (Roadmap)','lmm')  . ' - <strong>' . __('API key required!','lmm') . '</strong> <a href="https://www.mapsmarker.com/google-maps-javascript-api" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['controlbox_googleLayer_satellite'] = array(
			'version' => '2.5',
			'pane'    => 'basemaps',
			'section' => 'basemaps-available_basemaps_controlbox',
			'title'   => '',
			'desc'    => __('Google Maps (Satellite)','lmm')  . ' - <strong>' . __('API key required!','lmm') . '</strong> <a href="https://www.mapsmarker.com/google-maps-javascript-api" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['controlbox_googleLayer_hybrid'] = array(
			'version' => '2.5',
			'pane'    => 'basemaps',
			'section' => 'basemaps-available_basemaps_controlbox',
			'title'   => '',
			'desc'    => __('Google Maps (Hybrid)','lmm')  . ' - <strong>' . __('API key required!','lmm') . '</strong> <a href="https://www.mapsmarker.com/google-maps-javascript-api" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['controlbox_googleLayer_terrain'] = array(
			'version' => '2.6',
			'pane'    => 'basemaps',
			'section' => 'basemaps-available_basemaps_controlbox',
			'title'   => '',
			'desc'    => __('Google Maps (Terrain)','lmm')  . ' - <strong>' . __('API key required!','lmm') . '</strong> <a href="https://www.mapsmarker.com/google-maps-javascript-api" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['controlbox_bingaerial'] = array(
			'version' => '2.6',
			'pane'    => 'basemaps',
			'section' => 'basemaps-available_basemaps_controlbox',
			'title'   => '',
			'desc'    => __('Bing Maps (Aerial)','lmm') . ' - <strong>' . __('API key required!','lmm'). '</strong> <a href="https://www.mapsmarker.com/bing-maps" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['controlbox_bingaerialwithlabels'] = array(
			'version' => '2.6',
			'pane'    => 'basemaps',
			'section' => 'basemaps-available_basemaps_controlbox',
			'title'   => '',
			'desc'    => __('Bing Maps (Aerial+Labels)','lmm') . ' - <strong>' . __('API key required!','lmm'). '</strong> <a href="https://www.mapsmarker.com/bing-maps" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['controlbox_bingroad'] = array(
			'version' => '2.6',
			'pane'    => 'basemaps',
			'section' => 'basemaps-available_basemaps_controlbox',
			'title'   => '',
			'desc'    => __('Bing Maps (Road)','lmm') . ' - <strong>' . __('API key required!','lmm'). '</strong> <a href="https://www.mapsmarker.com/bing-maps" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['controlbox_ogdwien_basemap'] = array(
			'version' => '3.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-available_basemaps_controlbox',
			'title'   => '',
			'desc'    => 'basemap.at - <strong>' . __('coverage: Austria only','lmm') . '</strong>',
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['controlbox_ogdwien_satellite'] = array(
			'version' => '3.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-available_basemaps_controlbox',
			'title'   => '',
			'desc'    => __('basemap.at satellite','lmm') . ' - <strong>' . __('coverage: Austria only','lmm') . '</strong>',
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['controlbox_mapbox'] = array(
			'version' => '2.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-available_basemaps_controlbox',
			'title'   => '',
			'desc'    => 'MapBox - <strong>' . __('API key required!','lmm'). '</strong> <a href="https://www.mapsmarker.com/mapbox" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['controlbox_mapbox2'] = array(
			'version' => '2.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-available_basemaps_controlbox',
			'title'   => '',
			'desc'    => 'MapBox 2 - <strong>' . __('API key required!','lmm'). '</strong> <a href="https://www.mapsmarker.com/mapbox" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['controlbox_mapbox3'] = array(
			'version' => '2.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-available_basemaps_controlbox',
			'title'   => '',
			'desc'    => 'MapBox 3 - <strong>' . __('API key required!','lmm'). '</strong> <a href="https://www.mapsmarker.com/mapbox" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['controlbox_custom_basemap'] = array(
			'version' => '1.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-available_basemaps_controlbox',
			'title'   => '',
			'desc'    => __('Custom basemap','lmm'),
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['controlbox_custom_basemap2'] = array(
			'version' => '1.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-available_basemaps_controlbox',
			'title'   => '',
			'desc'    => __('Custom basemap 2','lmm'),
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['controlbox_custom_basemap3'] = array(
			'version' => '1.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-available_basemaps_controlbox',
			'title'   => '',
			'desc'    => __('Custom basemap 3','lmm'),
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['controlbox_empty_basemap'] = array(
			'version' => '3.3',
			'pane'    => 'basemaps',
			'section' => 'basemaps-available_basemaps_controlbox',
			'title'   => '',
			'desc'    => __('empty basemap','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		/*
		* Names for basemaps in controlbox
		*/
		$this->_settings['default_basemap_helptext2'] = array(
			'version' => '1.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-basemap_names',
			'std'     => '',
			'title'   => '',
			'desc'    => __( 'Optionally you can also change the name of the predefined basemaps in the controlbox.', 'lmm').'<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-default-basemap-names.jpg" width="386" height="290" />',
			'type'    => 'helptext'
		);
		$this->_settings['default_basemap_name_osm_mapnik'] = array(
			'version' => '3.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-basemap_names',
			'title'   => 'OpenStreetMap',
			'desc'    => '',
			'std'     => 'OpenStreetMap',
			'type'    => 'text'
		);
		$this->_settings['default_basemap_name_stamen_terrain'] = array(
			'version' => '3.11',
			'pane'    => 'basemaps',
			'section' => 'basemaps-basemap_names',
			'title'   => __('Stamen Terrain','lmm'),
			'desc'    => '',
			'std'     => 'Stamen Terrain',
			'type'    => 'text'
		);
		$this->_settings['default_basemap_name_stamen_toner'] = array(
			'version' => '3.11',
			'pane'    => 'basemaps',
			'section' => 'basemaps-basemap_names',
			'title'   => __('Stamen Toner','lmm'),
			'desc'    => '',
			'std'     => 'Stamen Toner',
			'type'    => 'text'
		);
		$this->_settings['default_basemap_name_stamen_watercolor'] = array(
			'version' => '3.11',
			'pane'    => 'basemaps',
			'section' => 'basemaps-basemap_names',
			'title'   => __('Stamen Watercolor','lmm'),
			'desc'    => '',
			'std'     => 'Stamen Watercolor',
			'type'    => 'text'
		);
		$this->_settings['default_basemap_name_mapquest_osm'] = array(
			'version' => '3.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-basemap_names',
			'title'   => 'MapQuest',
			'desc'    => '',
			'std'     => 'MapQuest (OSM)',
			'type'    => 'text'
		);
		$this->_settings['default_basemap_name_mapquest_aerial'] = array(
			'version' => '1.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-basemap_names',
			'title'   => __('MapQuest (Aerial)','lmm'),
			'desc'    => '',
			'std'     => __('MapQuest (Aerial)','lmm'),
			'type'    => 'text'
		);
		$this->_settings['default_basemap_name_mapquest_hybrid'] = array(
			'version' => '3.11',
			'pane'    => 'basemaps',
			'section' => 'basemaps-basemap_names',
			'title'   => __('MapQuest (Hybrid)','lmm'),
			'desc'    => '',
			'std'     => __('MapQuest (Hybrid)','lmm'),
			'type'    => 'text'
		);
		$this->_settings['default_basemap_name_googleLayer_roadmap'] = array(
			'version' => '2.5',
			'pane'    => 'basemaps',
			'section' => 'basemaps-basemap_names',
			'title'   => __('Google Maps (Roadmap)','lmm'),
			'desc'    => '',
			'std'   => __('Google Maps (Roadmap)','lmm'),
			'type'    => 'text'
		);
		$this->_settings['default_basemap_name_googleLayer_satellite'] = array(
			'version' => '2.5',
			'pane'    => 'basemaps',
			'section' => 'basemaps-basemap_names',
			'title'   => __('Google Maps (Satellite)','lmm'),
			'desc'    => '',
			'std'   => __('Google Maps (Satellite)','lmm'),
			'type'    => 'text'
		);
		$this->_settings['default_basemap_name_googleLayer_hybrid'] = array(
			'version' => '2.5',
			'pane'    => 'basemaps',
			'section' => 'basemaps-basemap_names',
			'title'   => __('Google Maps (Hybrid)','lmm'),
			'desc'    => '',
			'std'   => __('Google Maps (Hybrid)','lmm'),
			'type'    => 'text'
		);
		$this->_settings['default_basemap_name_googleLayer_terrain'] = array(
			'version' => '2.6',
			'pane'    => 'basemaps',
			'section' => 'basemaps-basemap_names',
			'title'   => __('Google Maps (Terrain)','lmm'),
			'desc'    => '',
			'std'   => __('Google Maps (Terrain)','lmm'),
			'type'    => 'text'
		);
		$this->_settings['default_basemap_name_bingaerial'] = array(
			'version' => '2.6',
			'pane'    => 'basemaps',
			'section' => 'basemaps-basemap_names',
			'title'   => __('Bing Maps (Aerial)','lmm'),
			'desc'    => '',
			'std'   => __('Bing Maps (Aerial)','lmm'),
			'type'    => 'text'
		);
		$this->_settings['default_basemap_name_bingaerialwithlabels'] = array(
			'version' => '2.6',
			'pane'    => 'basemaps',
			'section' => 'basemaps-basemap_names',
			'title'   => __('Bing Maps (Aerial+Labels)','lmm'),
			'desc'    => '',
			'std'   => __('Bing Maps (Aerial+Labels)','lmm'),
			'type'    => 'text'
		);
		$this->_settings['default_basemap_name_bingroad'] = array(
			'version' => '2.6',
			'pane'    => 'basemaps',
			'section' => 'basemaps-basemap_names',
			'title'   => __('Bing Maps (Road)','lmm'),
			'desc'    => '',
			'std'   => __('Bing Maps (Road)','lmm'),
			'type'    => 'text'
		);
		$this->_settings['default_basemap_name_ogdwien_basemap'] = array(
			'version' => '1.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-basemap_names',
			'title'   => 'basemap.at',
			'desc'    => '',
			'std'     => 'basemap.at',
			'type'    => 'text'
		);
		$this->_settings['default_basemap_name_ogdwien_satellite'] = array(
			'version' => '1.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-basemap_names',
			'title'   => __('basemap.at satellite','lmm'),
			'desc'    => '',
			'std'     => __('basemap.at satellite','lmm'),
			'type'    => 'text'
		);
		$this->_settings['mapbox_name'] = array(
			'version' => '2.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-basemap_names',
			'title'   => 'MapBox',
			'desc'    => '',
			'std'     => 'Blue Marble Topography',
			'type'    => 'text'
		);
		$this->_settings['mapbox2_name'] = array(
			'version' => '2.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-basemap_names',
			'title'   => 'MapBox 2',
			'desc'    => '',
			'std'     => 'Geography Class',
			'type'    => 'text',
		);
		$this->_settings['mapbox3_name'] = array(
			'version' => '2.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-basemap_names',
			'title'   => 'MapBox 3',
			'desc'    => '',
			'std'     => 'Natural Earth I',
			'type'    => 'text'
		);
		$this->_settings['custom_basemap_name'] = array(
			'version' => '1.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-basemap_names',
			'title'   => __( 'Custom Basemap', 'lmm' ),
			'desc'    => '',
			'std'     => 'OpenMapSurfer',
			'type'    => 'text'
		);
		$this->_settings['custom_basemap2_name'] = array(
			'version' => '1.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-basemap_names',
			'title'   => __( 'Custom Basemap 2', 'lmm' ),
			'desc'    => '',
			'std'     => 'OpenTopoMap',
			'type'    => 'text'
		);
		$this->_settings['custom_basemap3_name'] = array(
			'version' => '1.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-basemap_names',
			'title'   => __( 'Custom Basemap 3', 'lmm' ),
			'desc'    => '',
			'std'     => 'Hydda',
			'type'    => 'text'
		);
		$this->_settings['empty_basemap_name'] = array(
			'version' => '3.3',
			'pane'    => 'basemaps',
			'section' => 'basemaps-basemap_names',
			'title'   => __( 'empty basemap', 'lmm' ),
			'desc'    => '',
			'std'     => 'empty basemap',
			'type'    => 'text'
		);
		/*
		* Global basemap settings
		*/
		$this->_settings['global_basemap_settings_helptext'] = array(
			'version' => 'p3.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-basemap_global_settings',
			'std'     => '',
			'title'   => '',
			'desc'    => __( 'The following settings will be used for all marker and layer maps', 'lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['global_maxzoom_level'] = array(
			'version' => 'p1.5',
			'pane'    => 'basemaps',
			'section' => 'basemaps-basemap_global_settings',
			'title'   => __('Global maximum zoom level','lmm') . $pro_button_link,
			'desc'    => __('If the native maximum zoom level of a basemap is lower, tiles will be upscaled automatically.','lmm'),
			'std'     => '21',
			'type'    => 'text-pro'
		);
		$this->_settings['edgeBufferTiles'] = array(
			'version' => 'p3.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-basemap_global_settings',
			'title'   => 'edgeBufferTiles' . $pro_button_link,
			'desc'    => __('The number of tiles that should be pre-loaded beyond the edge of the visible map. This makes it less likely to see the blank background behind tile images when panning a map. This may also be a fractional number, set to 0 for disabling this feature.','lmm'),
			'std'     => '2',
			'type'    => 'text-pro'
		);
		$this->_settings['basemaps_nowrap_enabled'] = array(
			'version' => 'p3.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-basemap_global_settings',
			'title'   => __('Enable nowrap?','lmm') . $pro_button_link,
			'desc'    => __('If set to true, the tiles just will not load outside the world width (-180 to 180 longitude) instead of repeating.','lmm'),
			'type'    => 'radio-pro',
			'std'     => 'false',
			'choices' => array(
				'false' => __('false','lmm'),
				'true' => __('true','lmm')
			)
		);
		/* Retina display detection */
		$this->_settings['map_retina_detection'] = array(
			'version' => '2.7.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-basemap_global_settings',
			'title'   => 'detectRetina',
			'desc'    => __('If true and user is on a retina display, four tiles of half the specified size and a bigger zoom level in place of one will be requested to utilize the high resolution.','lmm'),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);		
		$this->_settings['misc_projections'] = array(
			'version' => '1.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-basemap_global_settings',
			'title'   => __( 'Coordinate Reference System', 'lmm' ),
			'desc'    => __( 'Used for created maps - do not change this if you are not sure what it means!', 'lmm') . '<div style="height:200px;"></div>',
			'type'    => 'radio',
			'std'     => 'L.CRS.EPSG3857',
			'choices' => array(
				'L.CRS.EPSG3857' => __('EPSG:3857 (Spherical Mercator), used by most of commercial map providers (CloudMade, Google, Yahoo, Bing, etc.)', 'lmm'),
				'L.CRS.EPSG4326' => __('EPSG:4326 (Plate Carree), very popular among GIS enthusiasts', 'lmm'),
				'L.CRS.EPSG3395' => __('EPSG:4326 (Mercator), used by some map providers.', 'lmm')
			)
		);
				
		/*
		* OpenStreetMap section
		*/
		$this->_settings['openstreetmap_helptext'] = array(
			'version' => '3.11',
			'pane'    => 'basemaps',
			'section' => 'basemaps-openstreetmap',
			'std'     => '',
			'title'   => '',
			'desc'    => sprintf(__( '<a href="%1$s" target="_blank">OpenStreetMap</a> is a map of the world, created by people like you and free to use under an open license. OpenStreetMap powers map data on thousands of web sites, mobile apps, and hardware devices. It is built by a community of mappers that contribute and maintain data about roads, trails, cafés, railway stations, and much more, all over the world.', 'lmm'),'https://www.openstreetmap.org') . '<br/><br/>' . __('OpenStreetmap can be used out-of-the box for your maps, no API keys is required. You can choose between the following variants:','lmm') . '<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-osm-variants.jpg" width="649" height="470" alt="OSM variants"/>',
			'type'    => 'helptext'
		);
		$this->_settings['openstreetmap_variants'] = array(
			'version' => '3.11',
			'pane'    => 'basemaps',
			'section' => 'basemaps-openstreetmap',
			'title'   => __('Variant to use','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'osm-mapnik',
			'choices' => array(
				'osm-mapnik' => 'Mapnik',
				'osm-blackandwhite' => __('Black and White','lmm'),
				'osm-de' => 'DE (<a href="https://hotosm.org/" target="_blank">' . __('OSM Germany','lmm') . '</a>)',
				'osm-france' => __('France','lmm') . ' (<a href="https://www.openstreetmap.fr/" target="_blank">' . __('OSM France','lmm') . '</a>)',
				'osm-hot' => 'HOT (<a href="https://hotosm.org/" target="_blank">' . __('Humanitarian OpenStreetMap Team','lmm') . '</a>)'
				)
		);
		$this->_settings['openstreetmap_helptext2'] = array(
			'version' => '3.11',
			'pane'    => 'basemaps',
			'section' => 'basemaps-openstreetmap',
			'std'     => '',
			'title'   => '',
			'desc'    => '<h4 class="h4-lmm-settings">' . __('Why we recommend OpenStreetMap','lmm') . '</h4><br/><strong>' . __('Local knowledge','lmm') . '</strong><br/>' . __('OpenStreetMap emphasizes local knowledge. Contributors use aerial imagery, GPS devices, and low-tech field maps to verify that OSM is accurate and up to date.','lmm') . '<br/><br/><strong>' . __('Community driven','lmm') . '</strong><br/>' . sprintf(__('The community of OpenStreetMap is diverse, passionate, and growing every day. Its contributors include enthusiast mappers, GIS professionals, engineers running the OSM servers, humanitarians mapping disaster-affected areas, and many more. To learn more about the community, see the <a href="%1$s" target="_blank">user diaries</a>, <a href="%2$s" target="_blank">community blogs</a>, and the <a href="%3$s" target="_blank">OSM Foundation</a> website.','lmm'), 'https://www.openstreetmap.org/diary','http://blogs.openstreetmap.org/','http://www.osmfoundation.org/') . '<br/><br/><strong>' . __('Open Data','lmm') . '</strong><br/>' . sprintf(__('OpenStreetMap is open data: you are free to use it for any purpose as long as you credit OpenStreetMap and its contributors. See the <a href="%1$s" target="_blank">Copyright and License page</a> for details.','lmm'), 'https://www.openstreetmap.org/copyright') . '<br/><br/><strong>' . __('Legal','lmm') . '</strong><br/>' . sprintf(__('<a href="%1$s" target="_blank">OpenStreetMap.org</a> and many other related services are formally operated by the <a href="%2$s" target="_blank">OpenStreetMap Foundation</a> (OSMF) on behalf of the community. Use of all OSMF operated services is subject to the <a href="%3$s" target="_blank">Acceptable Use Policies</a> and <a href="%4$s" target="_blank">Privacy Policy</a>. Please <a href="%5$s" target="_blank">contact the OSMF</a> if you have licensing, copyright or other legal questions and issues.','lmm'),'https://www.openstreetmap.org','http://osmfoundation.org/','http://wiki.openstreetmap.org/wiki/Acceptable_Use_Policy','http://wiki.osmfoundation.org/wiki/Privacy_Policy','http://osmfoundation.org/Contact') . '<br/><br/><strong>' . __('Partners','lmm') . '</strong><br/>' . sprintf(__('OpenStreetMap.org hosting is supported by the <a href="%1$s" target="_blank">UCL VR Centre</a>, <a href="%2$s" target="_blank">Imperial College London</a> and <a href="%3$s" target="_blank">Bytemark Hosting</a>, and <a href="%4$s" target="_blank">other partners</a>.','lmm'),'http://www.bartlett.ucl.ac.uk/','http://www.imperial.ac.uk/','http://www.bytemark.co.uk/','http://wiki.openstreetmap.org/wiki/Partners') . '<br/><br/><h4 class="h4-lmm-settings">' . __('How to support OpenStreetMap','lmm'). '</h4><br/>' . sprintf(__('In order for OpenStreetMap to keep the service free and open for everyone, we encourage you to support OpenStreetMap <a href="%1$s" target="_blank">by making a donation</a>.','lmm'),'https://donate.openstreetmap.org/'),
			'type'    => 'helptext'
		);
		/*
		* Stamen Map settings
		*/
		$this->_settings['stamen_maps_helptext'] = array(
			'version' => '3.11',
			'pane'    => 'basemaps',
			'section' => 'basemaps-stamen_maps',
			'std'     => '',
			'title'   => '',
			'desc'    => sprintf(__( 'For over a decade, <a href="%1$s" target="_blank">Stamen</a> has been exploring cartography with <a href="%2$s" target="_blank">their clients</a> and in <a href="%3$s" target="_blank">research</a>.','lmm'), 'https://www.stamen.com', 'https://stamen.com/work', 'http://stamen.com/projects') . '<br/>' . sprintf(__('Their map tiles are made available as part of the <a href="%1$s" target="_blank">CityTracking project</a>, funded by the <a href="%2$s" target="_blank">Knight Foundation</a>, in which Stamen is building web services and <a href="%3$s" target="_blank">open source tools</a> to display public data in easy-to-understand, highly visual ways.', 'lmm'), 'http://citytracking.org/', 'http://www.knightfoundation.org/', 'https://github.com/Citytracking') . '<br/><br/>' . sprintf(__('<a href="%1$s" target="_blank">maps.stamen.com</a> remains free (and ad-free), serves upwards of %2$s million tiles a month, and takes them hundreds of hours, and thousands of dollars, to sustain.','lmm'), 'http://maps.stamen.com', '600') . '<br/>' . sprintf(__('If you find any value or joy in these maps, or if you rely on them for your work or play, <a href="%1$s" target="_blank">please consider a donation in any amount</a>.','lmm'),'https://stamen.com/opensource/#donate'),
			'type'    => 'helptext'
		);
		/* Stamen terrain settings*/
		$this->_settings['stamen_terrain_helptext'] = array(
			'version' => '3.11',
			'pane'    => 'basemaps',
			'section' => 'basemaps-stamen_maps',
			'std'     => '',
			'title'   => '<h4 class="h4-lmm-settings">' . __('Stamen Terrain map settings','lmm') . '</h4>',
			'desc'    => sprintf(__( 'Orient yourself with terrain maps, featuring hill shading and natural vegetation colors. These maps showcase advanced labeling and linework generalization of dual-carriageway roads. Terrain was developed in collaboration with Gem Spear and Nelson Minar. Available in four flavors:', 'lmm'), '') . '<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-stamen-terrain.jpg" width="649" height="470" alt="Stamen Terrain Flavors"/>',
			'type'    => 'helptext'
		);
		$this->_settings['stamen_terrain_flavor'] = array(
			'version' => '3.11',
			'pane'    => 'basemaps',
			'section' => 'basemaps-stamen_maps',
			'title'   => __('Flavor to use','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'terrain',
			'choices' => array(
				'terrain' => 'terrain',
				'terrain-background' => 'terrain-background',
				'terrain-classic' => 'terrain-classic',
				'terrain-labels' => 'terrain-labels',
				'terrain-lines' => 'terrain-lines'
				)
		);
		/* Stamen toner settings */
		$this->_settings['stamen_toner_helptext'] = array(
			'version' => '3.11',
			'pane'    => 'basemaps',
			'section' => 'basemaps-stamen_maps',
			'std'     => '',
			'title'   => '<h4 class="h4-lmm-settings">' . __('Stamen Toner map settings','lmm') . '</h4>',
			'desc'    => sprintf(__( 'These high-contrast black and white maps were first featured in the <a href="%1$s" target="_blank">Dotspotting project</a>. They are perfect for data mashups and exploring river meanders and coastal zones. Available in six flavors:', 'lmm'), 'http://dotspotting.org/') . '<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-stamen-toner.jpg" width="649" height="470" alt="Stamen Toner Flavors"/>',
			'type'    => 'helptext'
		);
		$this->_settings['stamen_toner_flavor'] = array(
			'version' => '3.11',
			'pane'    => 'basemaps',
			'section' => 'basemaps-stamen_maps',
			'title'   => __('Flavor to use','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'toner',
			'choices' => array(
				'toner' => 'toner',
				'toner-background' => 'toner-background',
				'toner-hybrid' => 'toner-hybrid',
				'toner-labels' => 'toner-labels',
				'toner-lines' => 'toner-lines',
				'toner-lite' => 'toner-lite',
				)
		);
		/* Stamen watercolor settings */
		$this->_settings['stamen_watercolor_helptext'] = array(
			'version' => '3.11',
			'pane'    => 'basemaps',
			'section' => 'basemaps-stamen_maps',
			'std'     => '',
			'title'   => '<h4 class="h4-lmm-settings">' . __('Stamen Watercolor map','lmm') . '</h4>',
			'desc'    => sprintf(__( 'Reminiscent of hand drawn maps, the Stamen watercolor maps apply raster effect area washes and organic edges over a paper texture to add warm pop to any map. Watercolor was inspired by the <a href="%1$s" target="_blank">Bicycle Portraits project</a>. Thanks to <a href="%2$s" target="_blank">Cassidy Curtis</a> for his early advice. There are no related settings available - this map can be used out of the box.', 'lmm'), 'https://www.kickstarter.com/projects/bicycleportraits/bicycle-portraits-a-photographic-book-part-iii-fin', 'http://otherthings.com/blog/') . '<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-stamen-watercolor.jpg" width="185" height="185" alt="Stamen Watercolor"/>',
			'type'    => 'helptext'
		);
		/*
		* MapQuest settings
		*/
		$this->_settings['mapquest_helptext'] = array(
			'version' => '3.11',
			'pane'    => 'basemaps',
			'section' => 'basemaps-mapquest',
			'std'     => '',
			'title'   => '',
			'desc'    => sprintf(__( 'Since July 11 2016, MapQuest requires to <a href="%1$s" target="_blank">sign up for a plan on their Developer Network</a> if you want to use their maps on your site.','lmm'), 'https://developer.mapquest.com/plans') . '<br/><br/>' . sprintf(__( 'For a tutorial on how to signup for a free plan with %1$s monthly transactions included, <a href="%2$s" target="_blank">please click here</a>', 'lmm'), '15.000', 'https://www.mapsmarker.com/mapquest-api-key'),
			'type'    => 'helptext'
		);
		$this->_settings['mapquest_api_key'] = array(
			'version' => '3.11',
			'pane'    => 'basemaps',
			'section' => 'basemaps-mapquest',
			'title'   => __( 'API key', 'lmm' ),
			'desc'    => __( 'Please enter your MapQuest API key here', 'lmm' ),
			'std'     => '',
			'type'    => 'text'
		);
		$this->_settings['mapquest_helptext2'] = array(
			'version' => '3.11',
			'pane'    => 'basemaps',
			'section' => 'basemaps-mapquest',
			'std'     => '',
			'title'   => '',
			'desc'    => __( 'Once you entered your API key above, you can activate the following basemaps by ticking the related checkbox at "Available basemaps in control box":','lmm') . '<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-mapquest-tiles.jpg" width="649" height="229" alt="MapQuest tiles" /><div style="height:180px;"></div>',
			'type'    => 'helptext'
		);
		/*
		* Google Maps JavaScript API Key
		*/
		$this->_settings['google_maps_api_key_helptext'] = array(
			'version' => '2.6',
			'pane'    => 'basemaps',
			'section' => 'basemaps-google_js_api',
			'std'     => '',
			'title'   => '',
			'desc'    => sprintf(__( 'Since June 22nd 2016 <a href="%1$s" target="_blank">Google requires a Google Maps API key</a> when using any Google Map service on your website.','lmm'), 'https://googlegeodevelopers.blogspot.co.at/2016/06/building-for-scale-updates-to-google.html') . '<br/>' . sprintf(__('Your personal API key can be obtained from the <a href="%1$s" target="_blank">Google API Console</a>.', 'lmm'), 'https://console.developers.google.com/apis/') . '<br/>' . sprintf(__('For a tutorial including screenshots on how to register a Google API key <a href="%1$s" target="_blank">please click here</a>.', 'lmm'), 'https://www.mapsmarker.com/google-maps-javascript-api'),
			'type'    => 'helptext'
		);
		$this->_settings['google_maps_api_status'] = array(
			'version' => '3.11', //info: was p1.6
			'pane'    => 'basemaps',
			'section' => 'basemaps-google_js_api',
			'title'   => __('Google Maps JavaScript API status','lmm'),
			'desc'    => __('If disabled, existing Google maps will automatically use OpenStreetMap as basemap. In addition enabled Google basemaps in the layer controlbox will also be hidden automatically.','lmm'),
			'type'    => 'radio',
			'std'     => 'disabled', //info: re-enabled via install-and-updates.php for upgrades only
			'choices' => array(
				'enabled' => __('enabled','lmm'),
				'disabled' => __('disabled','lmm')
			)
		);
		$this->_settings['google_maps_api_key'] = array(
			'version' => '2.6',
			'pane'    => 'basemaps',
			'section' => 'basemaps-google_js_api',
			'title'   => __( 'Google Maps API key', 'lmm'),
			'desc'    => __( 'Please enter your Google Maps JavaScript API key here', 'lmm' ),
			'std'     => '',
			'type'    => 'text'
		);
		$this->_settings['google_maps_plugin'] = array(
			'version' => 'p3.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-google_js_api',
			'title'   => __('Google Maps leaflet implementation','lmm') . $pro_button_link,
			'desc'    => sprintf(__('GoogleMutant plugin is recommended although not supported on Internet Explorer 10 or lower and several older browsers versions (maps will automatically switch to OpenStreetMap for those users).<br/>Current browser market share for affected browsers: about %1$s and declining steadily. If you do not want Google basemaps to automatically switch to OpenStreetMap for those outdated browsers, please activate the legacy plugin.','lmm'), '2% (02/2017)'),
			'type'    => 'radio-pro',
			'std'     => 'google_legacy',
			'choices' => array(
				'google_mutant' => __('GoogleMutant plugin (higher performance, active development)','lmm'),
				'google_legacy' => __('legacy plugin (slower performance, no active development)','lmm')
			)
		);
		$this->_settings['google_maps_api_deferred_loading'] = array(
			'version' => 'p2.6.2',
			'pane'    => 'basemaps',
			'section' => 'basemaps-google_js_api',
			'title'   => __('Deferred loading','lmm') . $pro_button_link,
			'desc'    => __('If enabled, Google Maps API scripts will only be loaded on demand as this significantly decreases the loadtime for all OpenStreetMap based maps. Disabling this feature is only recommended if you are experiencing compatibility issues with other plugins or themes.','lmm'),
			'type'    => 'radio-pro',
			'std'     => 'disabled',
			'choices' => array(
				'enabled' => __('enabled','lmm'),
				'disabled' => __('disabled','lmm')
			)
		);
		$this->_settings['google_api_deregister_scripts'] = array(
			'version' => '3.10.6',
			'pane'    => 'basemaps',
			'section' => 'basemaps-google_js_api',
			'title'   => __('Dequeue Google Maps API scripts by third parties','lmm'),
			'desc'    => __('Only enable this compatibility option if you see the admin notice that another plugin or theme also embedds the Google Maps API (which can cause maps and address search to break if that implementation does not properly send the Google API key which is mandatory since June 22nd 2016!)','lmm') . '<br/><br/><div style="height:40px;"></div>',
			'type'    => 'radio',
			'std'     => 'disabled',
			'choices' => array(
				'enabled' => __('enabled','lmm'),
				'disabled' => __('disabled','lmm')
			)
		);
		/*
		* Google language localization
		* https://spreadsheets.google.com/spreadsheet/pub?key=0Ah0xU81penP1cDlwZHdzYWkyaERNc0xrWHNvTTA1S1E&gid=1
		*/
		$this->_settings['google_maps_language_localization_helptext'] = array(
			'version' => '2.7.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-google_localization',
			'std'     => '',
			'title'   => '',
			'desc'    => __( 'Language used when displaying textual information such as the names for controls, copyright notices, driving directions and labels on Google maps, direction links and autocomplete for address search.', 'lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['google_maps_language_localization'] = array(
			'version' => '2.7.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-google_localization',
			'title'   => __('Default language','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'browser_setting',
			'choices' => array(
				'browser_setting' => __('automatic 1 (distinct language for each user - detects the users browser language setting, preferred method by Google)','lmm'),
				'wordpress_setting' => sprintf(__('automatic 2 (same language for each user - using the global variable $locale = %s)','lmm'), '<strong>' . substr(get_locale(),0,2) . '</strong>'),
				'ar' => __('Arabic','lmm') . ' (' . __('language code','lmm') . ': ar)',
				'bg' => __('Bulgarian','lmm') . ' (' . __('language code','lmm') . ': bg)',
				'ca' => __('Catalan','lmm') . ' (' . __('language code','lmm') . ': ca)',
				'cs' => __('Czech','lmm') . ' (' . __('language code','lmm') . ': cs)',
				'da' => __('Danish','lmm') . ' (' . __('language code','lmm') . ': da)',
				'de' => __('German','lmm') . ' (' . __('language code','lmm') . ': de)',
				'el' => __('Greek','lmm') . ' (' . __('language code','lmm') . ': el)',
				'en' => __('English','lmm') . ' (' . __('language code','lmm') . ': en)',
				'en-AU' => __('English (Australian)','lmm') . ' (' . __('language code','lmm') . ': en-AU)',
				'en-GB' => __('English (Great Britain)','lmm') . ' (' . __('language code','lmm') . ': en-GB)',
				'es' => __('Spanish','lmm') . ' (' . __('language code','lmm') . ': es)',
				'eu' => __('Basque','lmm') . ' (' . __('language code','lmm') . ': eu)',
				'fa' => __('Farsi','lmm') . ' (' . __('language code','lmm') . ': fa)',
				'fi' => __('Finnish','lmm') . ' (' . __('language code','lmm') . ': fi)',
				'fil' => __('Filipino','lmm') . ' (' . __('language code','lmm') . ': fil)',
				'fr' => __('French','lmm') . ' (' . __('language code','lmm') . ': fr)',
				'gl' => __('Galician','lmm') . ' (' . __('language code','lmm') . ': gl)',
				'gu' => __('Gujarati','lmm') . ' (' . __('language code','lmm') . ': gu)',
				'hi' => __('Hindi','lmm') . ' (' . __('language code','lmm') . ': hi)',
				'hr' => __('Croatian','lmm') . ' (' . __('language code','lmm') . ': hr)',
				'hu' => __('Hungarian','lmm') . ' (' . __('language code','lmm') . ': hu)',
				'id' => __('Indonesian','lmm') . ' (' . __('language code','lmm') . ': id)',
				'it' => __('Italian','lmm') . ' (' . __('language code','lmm') . ': it)',
				'iw' => __('Hebrew','lmm') . ' (' . __('language code','lmm') . ': iw)',
				'ja' => __('Japanese','lmm') . ' (' . __('language code','lmm') . ': ja)',
				'kn' => __('Kannada','lmm') . ' (' . __('language code','lmm') . ': kn)',
				'ko' => __('Korean','lmm') . ' (' . __('language code','lmm') . ': ko)',
				'lt' => __('Lithuanian','lmm') . ' (' . __('language code','lmm') . ': lt)',
				'lv' => __('Latvian','lmm') . ' (' . __('language code','lmm') . ': lv)',
				'ml' => __('Malayalam','lmm') . ' (' . __('language code','lmm') . ': ml)',
				'mr' => __('Marathi','lmm') . ' (' . __('language code','lmm') . ': mr)',
				'nl' => __('Dutch','lmm') . ' (' . __('language code','lmm') . ': nl)',
				'no' => __('Norwegian','lmm') . ' (' . __('language code','lmm') . ': no)',
				'pl' => __('Polish','lmm') . ' (' . __('language code','lmm') . ': pl)',
				'pt' => __('Portuguese','lmm') . ' (' . __('language code','lmm') . ': pt)',
				'pt-BR' => __('Portuguese (Brazil)','lmm') . ' (' . __('language code','lmm') . ': pt-BR)',
				'pt-PT' => __('Portuguese (Portugal)','lmm') . ' (' . __('language code','lmm') . ': pt-PT)',
				'ro' => __('Romanian','lmm') . ' (' . __('language code','lmm') . ': ro)',
				'ru' => __('Russian','lmm') . ' (' . __('language code','lmm') . ': ru)',
				'sk' => __('Slovak','lmm') . ' (' . __('language code','lmm') . ': sk)',
				'sl' => __('Slovenian','lmm') . ' (' . __('language code','lmm') . ': sl)',
				'sr' => __('Serbian','lmm') . ' (' . __('language code','lmm') . ': sr)',
				'sv' => __('Swedish','lmm') . ' (' . __('language code','lmm') . ': sv)',
				'tl' => __('Tagalog','lmm') . ' (' . __('language code','lmm') . ': tl)',
				'ta' => __('Tamil','lmm') . ' (' . __('language code','lmm') . ': ta)',
				'te' => __('Telugu','lmm') . ' (' . __('language code','lmm') . ': te)',
				'th' => __('Thai','lmm') . ' (' . __('language code','lmm') . ': th)',
				'uk' => __('Ukrainian','lmm') . ' (' . __('language code','lmm') . ': uk)',
				'vi' => __('Vietnamese','lmm') . ' (' . __('language code','lmm') . ': vi)',
				'zh-CN' => __('Chinese (simplified)','lmm') . ' (' . __('language code','lmm') . ': zh-CN)',
				'zh-TW' => __('Chinese (traditional)','lmm') . ' (' . __('language code','lmm') . ': zh-TW)',
			)
		);
		/*
		* Google Maps base domain
		*/
		$this->_settings['google_maps_base_domain_helptext'] = array(
			'version' => '2.7.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-google_base_domain',
			'std'     => '',
			'title'   => '',
			'desc'    => __( 'The base domain from which to load the Google Maps API. If you want to change the language of the Google Maps interface (buttons etc) only, please change the option "Google language localization" above.', 'lmm') . ' ' . __('This base domain is also used for Google Maps Directions.','lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['google_maps_base_domain'] = array(
			'version' => '2.7.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-google_base_domain',
			'title'   => __('Google Maps base domain','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'maps.google.com',
			'choices' => array(
				'maps.google.com' => 'maps.google.com',
				'maps.google.at' => 'maps.google.at',
				'maps.google.com.au' => 'maps.google.com.au',
				'maps.google.com.ba' => 'maps.google.com.ba',
				'maps.google.be' => 'maps.google.be',
				'maps.google.bg' => 'maps.google.bg',
				'maps.google.com.br' => 'maps.google.com.br',
				'maps.google.ca' => 'maps.google.ca',
				'maps.google.ch' => 'maps.google.ch',
				'maps.google.cm' => 'maps.google.cm',
				'ditu.google.cn' => 'ditu.google.cn',
				'maps.google.cz' => 'maps.google.cz',
				'maps.google.de' => 'maps.google.de',
				'maps.google.dk' => 'maps.google.dk',
				'maps.google.es' => 'maps.google.es',
				'maps.google.fi' => 'maps.google.fi',
				'maps.google.fr' => 'maps.google.fr',
				'maps.google.it' => 'maps.google.it',
				'maps.google.lk' => 'maps.google.lk',
				'maps.google.jp' => 'maps.google.jp',
				'maps.google.nl' => 'maps.google.nl',
				'maps.google.no' => 'maps.google.no',
				'maps.google.co.nz' => 'maps.google.co.nz',
				'maps.google.pl' => 'maps.google.pl',
				'maps.google.ru' => 'maps.google.ru',
				'maps.google.se' => 'maps.google.se',
				'maps.google.tw' => 'maps.google.tw',
				'maps.google.co.uk' => 'maps.google.co.uk',
				'maps.google.co.ve' => 'maps.google.co.ve'
			)
		);
		$this->_settings['google_maps_base_domain_custom'] = array(
			'version' => '2.7.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-google_base_domain',
			'title'   => __( 'Custom base domain', 'lmm'),
			'desc'    => __( 'If your localized Google Maps basedomain is not available in the list above, please enter the domain name here (without http://, for example maps.google.com). If a domain name is entered, the setting "Google Maps base domain" from above gets overwritten.', 'lmm' ),
			'std'     => '',
			'type'    => 'text'
		);
		/*
		* Google Maps styling
		*/
		$this->_settings['google_styling_helptext1'] = array(
			'version' => 'p1.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-google_styling',
			'std'     => '',
			'title'   => '',
			'desc'    => __( 'Styled maps allow you to customize the presentation of the standard Google base maps, changing the visual display of such elements as roads, parks, and built-up areas.', 'lmm') . '<br/><a href="http://googlemaps.github.io/js-samples/styledmaps/examplestyles.html" target="_blank" title="' . esc_attr__('show examples','lmm') . '"><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-google-styling-preview.jpg" width="650" height="401" /></a><a style="background:#f99755;display:block;padding:3px;text-decoration:none;color:#2702c6;width:635px;margin:10px 0;" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade">' . __('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm') . '</a>',
			'type'    => 'helptext'
		);
		$this->_settings['google_styling_json'] = array(
			'version' => 'p1.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-google_styling',
			'title'   => 'JSON' . $pro_button_link,
			'desc'    => sprintf(__('Please enter the custom JSON array to style your Google maps (you can use the <a href="%1s" target="_blank">Google Styled Maps Wizard</a> to create custom styles easily). Example for hiding roads:','lmm'), 'https://mapstyle.withgoogle.com/') . ' <br/><strong>[ { &#39;featureType&#39;: &#39;road.highway&#39;, &#39;elementType&#39;: &#39;geometry&#39;, &#39;stylers&#39;: [ { &#39;visibility&#39;: &#39;off&#39; } ] },{ &#39;featureType&#39;: &#39;road.arterial&#39;, &#39;stylers&#39;: [ { &#39;visibility&#39;: &#39;off&#39; } ] },{ &#39;featureType&#39;: &#39;road.local&#39;, &#39;stylers&#39;: [ { &#39;visibility&#39;: &#39;off&#39; } ] } ]</strong><div style="height:90px;"></div>',
			'std'     => '',
			'type'    => 'text-pro'
		);
		/*
		/* Bing Maps API Key
		*/
		$this->_settings['bingmaps_api_key_helptext'] = array(
			'version' => '2.6',
			'pane'    => 'basemaps',
			'section' => 'basemaps-bing',
			'std'     => '',
			'title'   => '',
			'desc'    => __( 'An API key is required if you want to use Bing Maps as basemap for marker or layer maps. Please click on the question mark for more info on how to get your API key.', 'lmm') . ' <a href="https://www.mapsmarker.com/bing-maps" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
			'type'    => 'helptext'
		);
		$this->_settings['bingmaps_api_key'] = array(
			'version' => '2.6',
			'pane'    => 'basemaps',
			'section' => 'basemaps-bing',
			'title'   => __( 'Bing Maps API key', 'lmm' ),
			'desc'    => '',
			'std'     => '',
			'type'    => 'text'
		);
		/* Bing culture parameter - http://msdn.microsoft.com/en-us/library/hh441729.aspx  */
		$this->_settings['bingmaps_culture_helptext'] = array(
			'version' => '2.9',
			'pane'    => 'basemaps',
			'section' => 'basemaps-bing',
			'std'     => '',
			'title'   => '<h4 class="h4-lmm-settings">' . __('Bing Culture Parameter','lmm') . '</h4>',
			'desc'    => __( 'The culture parameter allows you to select the language of the culture for geographic entities, place names and map labels on bing map images. For supported cultures, street names are localized to the local culture. For example, if you request a location in France, the street names are localized in French. For other localized data such as country names, the level of localization will vary for each culture. For example, there may not be a localized name for the "United States" for every culture code. See <a href="http://msdn.microsoft.com/en-us/library/hh441729.aspx" target="_blank">this page</a> for more details.', 'lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['bingmaps_culture'] = array(
			'version' => '2.9',
			'pane'    => 'basemaps',
			'section' => 'basemaps-bing',
			'title'   => __('Default culture','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'automatic',
			'choices' => array(
				'automatic' => sprintf(__('automatic (using the global variable $locale = %s - fallback to en-US if not supported by bing)','lmm'),'<strong>' . str_replace("_","-", get_locale()) . '</strong>'),
				'af' => __('Afrikaans','lmm') . ' (' . __('culture code','lmm') . ': af)',
				'am' => __('Amharic','lmm') . ' (' . __('culture code','lmm') . ': am)',
				'ar-sa' => __('Arabic (Saudi Arabia)','lmm') . ' (' . __('culture code','lmm') . ': ar-sa)',
				'as' => __('Assamese','lmm') . ' (' . __('culture code','lmm') . ': as)',
				'az-Latn' => __('Azerbaijani (Latin)','lmm') . ' (' . __('culture code','lmm') . ': az-Latn)',
				'be' => __('Belarusian','lmm') . ' (' . __('culture code','lmm') . ': be)',
				'bg' => __('Bulgarian','lmm') . ' (' . __('culture code','lmm') . ': bg)',
				'bn-BD' => __('Bangla (Bangladesh)','lmm') . ' (' . __('culture code','lmm') . ': bn-BD)',
				'bn-IN' => __('Bangla (India)','lmm') . ' (' . __('culture code','lmm') . ': bn-IN)',
				'bs' => __('Bosnian (Latin)','lmm') . ' (' . __('culture code','lmm') . ': bs)',
				'ca' => __('Catalan Spanish','lmm') . ' (' . __('culture code','lmm') . ': ca)',
				'ca-ES-valencia' => __('Valencian','lmm') . ' (' . __('culture code','lmm') . ': ca-ES-valencia)',
				'cs' => __('Czech','lmm') . ' (' . __('culture code','lmm') . ': cs)',
				'cy' => __('Welsh','lmm') . ' (' . __('culture code','lmm') . ': cy)',
				'da' => __('Danish','lmm') . ' (' . __('culture code','lmm') . ': da)',
				'de' => __('German (Germany)','lmm') . ' (' . __('culture code','lmm') . ': de)',
				'de-de' => __('German (Germany)','lmm') . ' (' . __('culture code','lmm') . ': de-de)',
				'el' => __('Greek','lmm') . ' (' . __('culture code','lmm') . ': el)',
				'en-GB' => __('English (United Kingdom)','lmm') . ' (' . __('culture code','lmm') . ': en-GB)',
				'en-US' => __('English (United States)','lmm') . ' (' . __('culture code','lmm') . ': en-US)',
				'es' => __('Spanish (Spain)','lmm') . ' (' . __('culture code','lmm') . ': es)',
				'es-ES' => __('Spanish (Spain)','lmm') . ' (' . __('culture code','lmm') . ': es-ES)',
				'es-US' => __('Spanish (United States)','lmm') . ' (' . __('culture code','lmm') . ': es-US)',
				'es-MX' => __('Spanish (Mexico)','lmm') . ' (' . __('culture code','lmm') . ': es-MX)',
				'et' => __('Estonian','lmm') . ' (' . __('culture code','lmm') . ': et)',
				'eu' => __('Basque','lmm') . ' (' . __('culture code','lmm') . ': eu)',
				'fa' => __('Persian','lmm') . ' (' . __('culture code','lmm') . ': fa)',
				'fi' => __('Finnish','lmm') . ' (' . __('culture code','lmm') . ': fi)',
				'fil-Latn' => __('Filipino','lmm') . ' (' . __('culture code','lmm') . ': fil-Latn)',
				'fr' => __('French (France)','lmm') . ' (' . __('culture code','lmm') . ': fr)',
				'fr-FR' => __('French (France)','lmm') . ' (' . __('culture code','lmm') . ': fr-FR)',
				'fr-CA' => __('French (Canada)','lmm') . ' (' . __('culture code','lmm') . ': fr-CA)',
				'ga' => __('Irish','lmm') . ' (' . __('culture code','lmm') . ': ga)',
				'gd-Latn' => __('Scottish Gaelic','lmm') . ' (' . __('culture code','lmm') . ': gd-Latn)',
				'gl' => __('Galician','lmm') . ' (' . __('culture code','lmm') . ': gl)',
				'gu' => __('Gujarati','lmm') . ' (' . __('culture code','lmm') . ': gu)',
				'ha-Latn' => __('Hausa (Latin)','lmm') . ' (' . __('culture code','lmm') . ': ha-Latn)',
				'he' => __('Hebrew','lmm') . ' (' . __('culture code','lmm') . ': he)',
				'hi' => __('Hindi','lmm') . ' (' . __('culture code','lmm') . ': hi)',
				'hr' => __('Croatian','lmm') . ' (' . __('culture code','lmm') . ': hr)',
				'hu' => __('Hungarian','lmm') . ' (' . __('culture code','lmm') . ': hu)',
				'hy' => __('Armenian','lmm') . ' (' . __('culture code','lmm') . ': hy)',
				'id' => __('Indonesian','lmm') . ' (' . __('culture code','lmm') . ': id)',
				'ig-Latn' => __('Igbo','lmm') . ' (' . __('culture code','lmm') . ': ig-Latn)',
				'is' => __('Icelandic','lmm') . ' (' . __('culture code','lmm') . ': )',
				'it' => __('Italian (Italy)','lmm') . ' (' . __('culture code','lmm') . ': it)',
				'it-it' => __('Italian (Italy)','lmm') . ' (' . __('culture code','lmm') . ': it-it)',
				'ja' => __('Japanese','lmm') . ' (' . __('culture code','lmm') . ': ja)',
				'ka' => __('Georgian','lmm') . ' (' . __('culture code','lmm') . ': ka)',
				'kk' => __('Kazakh','lmm') . ' (' . __('culture code','lmm') . ': kk)',
				'km' => __('Khmer','lmm') . ' (' . __('culture code','lmm') . ': km)',
				'kn' => __('Kannada','lmm') . ' (' . __('culture code','lmm') . ': kn)',
				'ko' => __('Korean','lmm') . ' (' . __('culture code','lmm') . ': ko)',
				'kok' => __('Konkani','lmm') . ' (' . __('culture code','lmm') . ': kok)',
				'ku-Arab' => __('Central Curdish','lmm') . ' (' . __('culture code','lmm') . ': ku-Arab)',
				'ky-Cyrl' => __('Kyrgyz','lmm') . ' (' . __('culture code','lmm') . ': ky-Cyrl)',
				'lb' => __('Luxembourgish','lmm') . ' (' . __('culture code','lmm') . ': lb)',
				'lt' => __('Lithuanian','lmm') . ' (' . __('culture code','lmm') . ': lt)',
				'lv' => __('Latvian','lmm') . ' (' . __('culture code','lmm') . ': lv)',
				'mi-Latn' => __('Maori','lmm') . ' (' . __('culture code','lmm') . ': mi-Latn)',
				'mk' => __('Macedonian','lmm') . ' (' . __('culture code','lmm') . ': mk)',
				'ml' => __('Malayalam','lmm') . ' (' . __('culture code','lmm') . ': ml)',
				'mn-Cyrl' => __('Mongolian (Cyrillic)','lmm') . ' (' . __('culture code','lmm') . ': mn-Cyrl)',
				'mr' => __('Marathi','lmm') . ' (' . __('culture code','lmm') . ': mr)',
				'ms' => __('Malay (Malaysia)','lmm') . ' (' . __('culture code','lmm') . ': ms)',
				'mt' => __('Maltese','lmm') . ' (' . __('culture code','lmm') . ': mt)',
				'nb' => __('Norwegian (Bokmal)','lmm') . ' (' . __('culture code','lmm') . ': nb)',
				'ne' => __('Nepali (Nepal)','lmm') . ' (' . __('culture code','lmm') . ': ne)',
				'nl' => __('Dutch (Netherlands)','lmm') . ' (' . __('culture code','lmm') . ': nl)',
				'nl-BE' => __('Dutch (Netherlands)','lmm') . ' (' . __('culture code','lmm') . ': nl-BE)',
				'nn' => __('Norwegian (Nynorsk)','lmm') . ' (' . __('culture code','lmm') . ': nn)',
				'nso' => __('Sesotho sa Leboa','lmm') . ' (' . __('culture code','lmm') . ': nso)',
				'or' => __('Odia','lmm') . ' (' . __('culture code','lmm') . ': or)',
				'pa' => __('Punjabi (Gurmukhi)','lmm') . ' (' . __('culture code','lmm') . ': pa)',
				'pa-Arab' => __('Punjabi (Arabic)','lmm') . ' (' . __('culture code','lmm') . ': pa-Arab)',
				'pl' => __('Polish','lmm') . ' (' . __('culture code','lmm') . ': pl)',
				'prs-Arab' => __('Dari','lmm') . ' (' . __('culture code','lmm') . ': prs-Arab)',
				'pt-BR' => __('Portuguese (Brazil)','lmm') . ' (' . __('culture code','lmm') . ': pt-BR)',
				'pt-PT' => __('Portuguese (Portugal)','lmm') . ' (' . __('culture code','lmm') . ': pt-PT)',
				'qut-Latn' => __('Kiche','lmm') . ' (' . __('culture code','lmm') . ': qut-Latn)',
				'quz' => __('Quechua (Peru)','lmm') . ' (' . __('culture code','lmm') . ': quz)',
				'ro' => __('Romanian (Romania)','lmm') . ' (' . __('culture code','lmm') . ': ro)',
				'ru' => __('Russian','lmm') . ' (' . __('culture code','lmm') . ': ru)',
				'rw' => __('Kinyarwanda','lmm') . ' (' . __('culture code','lmm') . ': rw)',
				'sd-Arab' => __('Sindhi (Arabic)','lmm') . ' (' . __('culture code','lmm') . ': sd-Arab)',
				'si' => __('Sinhala','lmm') . ' (' . __('culture code','lmm') . ': si)',
				'sk' => __('Slovak','lmm') . ' (' . __('culture code','lmm') . ': sk)',
				'sl' => __('Slovenian','lmm') . ' (' . __('culture code','lmm') . ': sl)',
				'sq' => __('Albanian','lmm') . ' (' . __('culture code','lmm') . ': sq)',
				'sr-Cyrl-BA' => __('Serbian (Cyrillic, Bosnia and Herzegovina)','lmm') . ' (' . __('culture code','lmm') . ': sr-Cyrl-BA)',
				'sr-Cyrl-RS' => __('Serbian (Cyrillic, Serbia)','lmm') . ' (' . __('culture code','lmm') . ': sr-Cyrl-RS)',
				'sr-Latn-RS' => __('Serbian (Latin, Serbia)','lmm') . ' (' . __('culture code','lmm') . ': sr-Latn-RS)',
				'sv' => __('Swedish (Sweden)','lmm') . ' (' . __('culture code','lmm') . ': sv)',
				'sw' => __('Kiswahili','lmm') . ' (' . __('culture code','lmm') . ': sw)',
				'ta' => __('Tamil','lmm') . ' (' . __('culture code','lmm') . ': ta)',
				'te' => __('Telugu','lmm') . ' (' . __('culture code','lmm') . ': te)',
				'tg-Cyrl' => __('Tajik (Cyrillic)','lmm') . ' (' . __('culture code','lmm') . ': tg-Cyrl)',
				'th' => __('Thai','lmm') . ' (' . __('culture code','lmm') . ': th)',
				'ti' => __('Tigrinya','lmm') . ' (' . __('culture code','lmm') . ': ti)',
				'tk-Latn' => __('Turkmen (Latin)','lmm') . ' (' . __('culture code','lmm') . ': tk-Latn)',
				'tn' => __('Setswana','lmm') . ' (' . __('culture code','lmm') . ': tn)',
				'tr' => __('Turkish','lmm') . ' (' . __('culture code','lmm') . ': tr)',
				'tt-Cyrl' => __('Tatar (Cyrillic)','lmm') . ' (' . __('culture code','lmm') . ': tt-Cyrl)',
				'ug-Arab' => __('Uyghur','lmm') . ' (' . __('culture code','lmm') . ': ug-Arab)',
				'uk' => __('Ukrainian','lmm') . ' (' . __('culture code','lmm') . ': uk)',
				'ur' => __('Urdu','lmm') . ' (' . __('culture code','lmm') . ': ur)',
				'uz-Latn' => __('Uzbek (Latin)','lmm') . ' (' . __('culture code','lmm') . ': uz-Latn)',
				'vi' => __('Vietnamese','lmm') . ' (' . __('culture code','lmm') . ': vi)',
				'wo' => __('Wolof','lmm') . ' (' . __('culture code','lmm') . ': wo)',
				'xh' => __('isiXhosa','lmm') . ' (' . __('culture code','lmm') . ': xh)',
				'yo-Latn' => __('Yoruba','lmm') . ' (' . __('culture code','lmm') . ': yo-Latn)',
				'zh-Hans' => __('Chinese (Simplified)','lmm') . ' (' . __('culture code','lmm') . ': zh-Hans)',
				'zh-Hant' => __('Chinese (Traditional)','lmm') . ' (' . __('culture code','lmm') . ': zh-Hant)',
				'zu' => __('isiZulu','lmm') . ' (' . __('culture code','lmm') . ': zu)'
			)
		);
		/*
		* basemap.at settings
		*/
		$this->_settings['basemap_at_helptext'] = array(
			'version' => '3.11',
			'pane'    => 'basemaps',
			'section' => 'basemaps-basemap_at',
			'std'     => '',
			'title'   => '',
			'desc'    => __( 'The development of Maps Marker Pro respectively Leaflet Maps Marker was originally initiated at an Open Data Hackathon in Vienna/Austria in 2011. The idea was to provide an easy way to share locations using the map provided by the City of Vienna.', 'lmm') . '<br/><br/>' . sprintf(__( 'This is the reason, why the successor map <a href="%1$s" target="_blank">basemap.at</a> - which covers the whole of Austria and which is also available as Open Data - can be used within this plugin.', 'lmm'), 'https://www.basemap.at') . '<br/><br/>' . __('The basemap.at integration is disabled by default - if you enable those basemaps via "Available basemaps in controlbox", please be aware that only the area of Austria is covered!','lmm') . '<div style="height:535px;"></div>',
			'type'    => 'helptext'
		);
		/*
		* MapBox settings
		*/
		$this->_settings['mapbox_helptext'] = array(
			'version' => '2.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-mapbox1',
			'std'     => '',
			'title'   => '',
			'desc'    => '<span style="background:#f99755;display:block;padding:3px;text-decoration:none;width:635px;">' . sprintf(__('Warning: as Mapbox now requires to use a custom API access token, custom Mapbox basemaps will not work anymore if you registered your Mapbox account before January 2015.<br/>In case your Mapbox maps are broken, please switch to another basemap like OpenStreetMap or <a href="%1$s">upgrade to Maps Marker Pro</a>, which enables you to continue using custom Mapbox basemaps - even with accounts created after January 2015 (please also note that Mapbox might discontinue the usage of their old API for existing users in the long run too!).','lmm'), LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade') . '</span><br/>' . sprintf(__('A tutorial on how to configure Mapbox basemaps can be found at %1$s','lmm'), '<a href="https://www.mapsmarker.com/mapbox" target="_blank">https://www.mapsmarker.com/mapbox</a>'),
			'type'    => 'helptext'
		);
		$this->_settings['mapbox_user'] = array(
			'version' => '2.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-mapbox1',
			'title'   => __( 'User', 'lmm' ),
			'desc'    => __('extracted from Map ID within mapbox editor, e.g.','lmm') . '<strong>YourUsername</strong>.kpf3blj3',
			'std'     => 'mapbox',
			'type'    => 'text'
		);
		$this->_settings['mapbox_map'] = array(
			'version' => '2.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-mapbox1',
			'title'   => __('Style','lmm'),
			'desc'    => __('extracted from Map ID within mapbox editor, e.g.','lmm') . 'YourUsername.<strong>kpf3blj3</strong>',
			'std'     => 'blue-marble-topo-jul',
			'type'    => 'text'
		);
		$this->_settings['mapbox_access_token'] = array(
			'version' => 'p2.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-mapbox1',
			'title'   => __('API access token','lmm') . $pro_button_link,
			'desc'    => __('Access to Mapbox web services requires an access token. The access token is used to associate requests to API resources with your account.','lmm'),
			'std'     => '',
			'type'    => 'text-pro'
		);
		$this->_settings['mapbox_minzoom'] = array(
			'version' => '2.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-mapbox1',
			'title'   => __('Minimum zoom level','lmm'),
			'desc'    => '',
			'std'     => '0',
			'type'    => 'text'
		);
		$this->_settings['mapbox_maxzoom'] = array(
			'version' => '2.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-mapbox1',
			'title'   => __('Maximum zoom level','lmm'),
			'desc'    => '',
			'std'     => '8',
			'type'    => 'text'
		);
		$this->_settings['mapbox_attribution'] = array(
			'version' => '2.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-mapbox1',
			'title'   => __('Attribution','lmm'),
			'desc'    => __("For example","lmm"). ": Copyright ".date('Y')." &lt;a href=&quot;http://xy.com&quot;&gt;Provider X&lt;/a&gt;<div style=\"height:180px;\"></div>",
			'std'     => "MapBox/NASA, <a href=&quot;http://www.mapbox.com&quot; target=&quot;_blank&quot;>http://www.mapbox.com</a>",
			'type'    => 'text'
		);

		/*
		* MapBox 2 settings
		*/
		$this->_settings['mapbox2_helptext'] = array(
			'version' => '2.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-mapbox2',
			'std'     => '',
			'title'   => '',
			'desc'    => '<span style="background:#f99755;display:block;padding:3px;text-decoration:none;width:635px;">' . sprintf(__('Warning: as Mapbox now requires to use a custom API access token, custom Mapbox basemaps will not work anymore if you registered your Mapbox account before January 2015.<br/>In case your Mapbox maps are broken, please switch to another basemap like OpenStreetMap or <a href="%1$s">upgrade to Maps Marker Pro</a>, which enables you to continue using custom Mapbox basemaps - even with accounts created after January 2015 (please also note that Mapbox might discontinue the usage of their old API for existing users in the long run too!).','lmm'), LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade') . '</span><br/>' . sprintf(__('A tutorial on how to configure Mapbox basemaps can be found at %1$s','lmm'), '<a href="https://www.mapsmarker.com/mapbox" target="_blank">https://www.mapsmarker.com/mapbox</a>'),
			'type'    => 'helptext'
		);
		$this->_settings['mapbox2_user'] = array(
			'version' => '2.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-mapbox2',
			'title'   => __( 'User', 'lmm' ),
			'desc'    => __('extracted from Map ID within mapbox editor, e.g.','lmm') . '<strong>YourUsername</strong>.kpf3blj3',
			'std'     => 'mapbox',
			'type'    => 'text'
		);
		$this->_settings['mapbox2_map'] = array(
			'version' => '2.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-mapbox2',
			'title'   => __('Style','lmm'),
			'desc'    => __('extracted from Map ID within mapbox editor, e.g.','lmm') . 'YourUsername.<strong>kpf3blj3</strong>',
			'std'     => 'geography-class',
			'type'    => 'text'
		);
		$this->_settings['mapbox2_access_token'] = array(
			'version' => 'p2.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-mapbox2',
			'title'   => __('API access token','lmm') . $pro_button_link,
			'desc'    => __('Access to Mapbox web services requires an access token. The access token is used to associate requests to API resources with your account.','lmm'),
			'std'     => '',
			'type'    => 'text-pro'
		);
		$this->_settings['mapbox2_minzoom'] = array(
			'version' => '2.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-mapbox2',
			'title'   => __('Minimum zoom level','lmm'),
			'desc'    => '',
			'std'     => '0',
			'type'    => 'text'
		);
		$this->_settings['mapbox2_maxzoom'] = array(
			'version' => '2.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-mapbox2',
			'title'   => __('Maximum zoom level','lmm'),
			'desc'    => '',
			'std'     => '8',
			'type'    => 'text'
		);
		$this->_settings['mapbox2_attribution'] = array(
			'version' => '2.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-mapbox2',
			'title'   => __('Attribution','lmm'),
			'desc'    => __("For example","lmm"). ": Copyright ".date('Y')." &lt;a href=&quot;http://xy.com&quot;&gt;Provider X&lt;/a&gt;<div style=\"height:180px;\"></div>",
			'std'     => "MapBox, <a href=&quot;http://www.mapbox.com&quot; target=&quot;_blank&quot;>http://www.mapbox.com</a>",
			'type'    => 'text'
		);

		/*
		* MapBox 3 settings
		*/
		$this->_settings['mapbox3_helptext'] = array(
			'version' => '2.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-mapbox3',
			'std'     => '',
			'title'   => '',
			'desc'    => '<span style="background:#f99755;display:block;padding:3px;text-decoration:none;width:635px;">' . sprintf(__('Warning: as Mapbox now requires to use a custom API access token, custom Mapbox basemaps will not work anymore if you registered your Mapbox account before January 2015.<br/>In case your Mapbox maps are broken, please switch to another basemap like OpenStreetMap or <a href="%1$s">upgrade to Maps Marker Pro</a>, which enables you to continue using custom Mapbox basemaps - even with accounts created after January 2015 (please also note that Mapbox might discontinue the usage of their old API for existing users in the long run too!).','lmm'), LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade') . '</span><br/>' . sprintf(__('A tutorial on how to configure Mapbox basemaps can be found at %1$s','lmm'), '<a href="https://www.mapsmarker.com/mapbox" target="_blank">https://www.mapsmarker.com/mapbox</a>'),
			'type'    => 'helptext'
		);
		$this->_settings['mapbox3_user'] = array(
			'version' => '2.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-mapbox3',
			'title'   => __( 'User', 'lmm' ),
			'desc'    => __('extracted from Map ID within mapbox editor, e.g.','lmm') . '<strong>YourUsername</strong>.kpf3blj3',
			'std'     => 'mapbox',
			'type'    => 'text'
		);
		$this->_settings['mapbox3_map'] = array(
			'version' => '2.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-mapbox3',
			'title'   => __('Style','lmm'),
			'desc'    => __('extracted from Map ID within mapbox editor, e.g.','lmm') . 'YourUsername.<strong>kpf3blj3</strong>',
			'std'     => 'natural-earth-1',
			'type'    => 'text'
		);
		$this->_settings['mapbox3_access_token'] = array(
			'version' => 'p2.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-mapbox3',
			'title'   => __('API access token','lmm') . $pro_button_link,
			'desc'    => __('Access to Mapbox web services requires an access token. The access token is used to associate requests to API resources with your account.','lmm'),
			'std'     => '',
			'type'    => 'text-pro'
		);
		$this->_settings['mapbox3_minzoom'] = array(
			'version' => '2.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-mapbox3',
			'title'   => __('Minimum zoom level','lmm'),
			'desc'    => '',
			'std'     => '0',
			'type'    => 'text'
		);
		$this->_settings['mapbox3_maxzoom'] = array(
			'version' => '2.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-mapbox3',
			'title'   => __('Maximum zoom level','lmm'),
			'desc'    => '',
			'std'     => '6',
			'type'    => 'text'
		);
		$this->_settings['mapbox3_attribution'] = array(
			'version' => '2.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-mapbox3',
			'title'   => __('Attribution','lmm'),
			'desc'    => __("For example","lmm"). ": Copyright ".date('Y')." &lt;a href=&quot;http://xy.com&quot;&gt;Provider X&lt;/a&gt;<div style=\"height:180px;\"></div>",
			'std'     => "MapBox, <a href=&quot;http://www.mapbox.com&quot; target=&quot;_blank&quot;>http://www.mapbox.com</a>",
			'type'    => 'text'
		);

		/*
		* Custom basemap 1 settings
		*/
		$this->_settings['custom_basemap_helptext'] = array(
			'version' => '1.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-custom_basemap1',
			'std'     => '',
			'title'   => '',
			'desc'    =>  sprintf(__('For a community-curated list of custom basemaps, please visit %1$s','lmm'),'<a href="https://www.mapsmarker.com/custom-basemaps" target="_blank">mapsmarker.com/custom-basemaps</a>') . '<br/><br/>' . __( 'If you want to use a custom basemap, please enter the related settings below:','lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['custom_basemap_tileurl'] = array(
			'version' => '1.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-custom_basemap1',
			'title'   => __( 'Tiles URL', 'lmm' ),
			'desc'    => __("For example","lmm"). ": http://korona.geog.uni-heidelberg.de/tiles/adminb/x={x}&y={y}&z={z}",
			'std'     => 'http://korona.geog.uni-heidelberg.de/tiles/adminb/x={x}&y={y}&z={z}',
			'type'    => 'text'
		);
		$this->_settings['custom_basemap_attribution'] = array(
			'version' => '1.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-custom_basemap1',
			'title'   => __( 'Attribution', 'lmm' ),
			'desc'    => __("For example","lmm"). ": Map data &copy; &lt;a href=&quot;http://openstreetmap.org/&quot;&gt;OpenStreetMap contributors&lt;/a&gt;, tiles: &lt;a href=&quot;http://giscience.uni-hd.de/&quot; target=&quot;_blank&quot;&lt;GIScience Research Group @ University of Heidelberg&lt;/a&gt;, cartography Maxim Rylov",
			'std'	  => "Map data &copy; <a href=&quot;http://openstreetmap.org/&quot; target=&quot;_blank&quot;>OpenStreetMap contributors</a>, tiles: <a href=&quot;http://giscience.uni-hd.de/&quot; target=&quot;_blank&quot;>GIScience Research Group @ University of Heidelberg</a>, cartography Maxim Rylov",
			'type'    => 'text'
		);
		$this->_settings['custom_basemap_minzoom'] = array(
			'version' => '1.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-custom_basemap1',
			'title'   => __( 'Minimum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '4',
			'type'    => 'text'
		);
		$this->_settings['custom_basemap_maxzoom'] = array(
			'version' => '1.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-custom_basemap1',
			'title'   => __( 'Maximum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '18',
			'type'    => 'text'
		);
		$this->_settings['custom_basemap_tms'] = array(
			'version' => '2.7.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-custom_basemap1',
			'title'   => 'tms',
			'desc'    => __('If true, inverses Y axis numbering for tiles (turn this on for TMS services).','lmm'),
			'type'    => 'radio',
			'std'     => 'false',
			'choices' => array(
				'false' => __('false','lmm'),
				'true' => __('true','lmm')
			)
		);
		$this->_settings['custom_basemap_subdomains_enabled'] = array(
			'version' => '1.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-custom_basemap1',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from tiles url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'no',
			'choices' => array(
				'yes' => __('Yes (please enter subdomains in next form field)','lmm'),
				'no' => __('No','lmm')
			)
		);
		$this->_settings['custom_basemap_subdomains_names'] = array(
			'version' => '1.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-custom_basemap1',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;a&quot;, &quot;b&quot;, &quot;c&quot;",
			'std'     => '&quot;a&quot;, &quot;b&quot;, &quot;c&quot;',
			'type'    => 'text'
		);
		$this->_settings['custom_basemap_continuousworld_enabled'] = array(
			'version' => '2.7.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-custom_basemap1',
			'title'   => __('Enable continuousWorld?','lmm'),
			'desc'    => __('If set to true, the tile coordinates will not be wrapped by world width (-180 to 180 longitude) or clamped to lie within world height (-90 to 90). Use this if you use Leaflet for maps that do not reflect the real world (e.g. game, indoor or photo maps).','lmm'),
			'type'    => 'radio',
			'std'     => 'false',
			'choices' => array(
				'false' => __('false','lmm'),
				'true' => __('true','lmm')
			)
		);
		$this->_settings['custom_basemap_nowrap_enabled'] = array(
			'version' => '2.7.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-custom_basemap1',
			'title'   => __('Enable nowrap?','lmm'),
			'desc'    => __('If set to true, the tiles just will not load outside the world width (-180 to 180 longitude) instead of repeating.','lmm'),
			'type'    => 'radio',
			'std'     => 'false',
			'choices' => array(
				'false' => __('false','lmm'),
				'true' => __('true','lmm')
			)
		);
		$this->_settings['custom_basemap_errortileurl'] = array(
			'version' => '3.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-custom_basemap1',
			'title'   => __('Show errorTile-images if map could not be loaded?','lmm'),
			'desc'    => __('Set to false if you want to use basemaps produced with maptiler for example','lmm'),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);

		/*
		* Custom basemap 2 settings
		*/
		$this->_settings['custom_basemap2_helptext'] = array(
			'version' => '1.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-custom_basemap2',
			'std'     => '',
			'title'   => '',
			'desc'    =>  sprintf(__('For a community-curated list of custom basemaps, please visit %1$s','lmm'),'<a href="https://www.mapsmarker.com/custom-basemaps" target="_blank">mapsmarker.com/custom-basemaps</a>') . '<br/><br/>' . __( 'If you want to use a custom basemap, please enter the related settings below:','lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['custom_basemap2_tileurl'] = array(
			'version' => '1.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-custom_basemap2',
			'title'   => __( 'Tiles URL', 'lmm' ),
			'desc'    => __("For example","lmm"). ": https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png",
			'std'     => 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png',
			'type'    => 'text'
		);
		$this->_settings['custom_basemap2_attribution'] = array(
			'version' => '1.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-custom_basemap2',
			'title'   => __( 'Attribution', 'lmm' ),
			'desc'    => __("For example","lmm"). ":  Map: &copy; &lt;a href=&quot;https://www.openstreetmap.org/copyright&quot;&gt;OpenStreetMap contributors&lt;/a&gt;, &lt;a href=&quot;http://viewfinderpanoramas.org&quot;&gt;SRTM&lt;/a&gt; | Map style: &copy; &lt;a href=&quot;https://opentopomap.org&quot;&gt;OpenTopoMap&lt;/a&gt; (&lt;a href=&quot;https://creativecommons.org/licenses/by-sa/3.0/&quot;&gt;CC BY SA&lt;/a&gt;)",
			'std'     => "Map: &copy; <a href=&quot;https://www.openstreetmap.org/copyright&quot; target=&quot;_blank&quot;>OpenStreetMap contributors</a>, <a href=&quot;http://viewfinderpanoramas.org&quot; target=&quot;_blank&quot;>SRTM</a> | Map style: <a href=&quot;https://opentopomap.org&quot; target=&quot;_blank&quot;>OpenTopoMap</a> (<a href=&quot;http://creativecommons.org/licenses/by-sa/3.0&quot; target=&quot;_blank&quot;>CC BY SA</a>)",
			'type'    => 'text'
		);
		$this->_settings['custom_basemap2_minzoom'] = array(
			'version' => '1.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-custom_basemap2',
			'title'   => __( 'Minimum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '1',
			'type'    => 'text'
		);
		$this->_settings['custom_basemap2_maxzoom'] = array(
			'version' => '1.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-custom_basemap2',
			'title'   => __( 'Maximum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '17',
			'type'    => 'text'
		);
		$this->_settings['custom_basemap2_tms'] = array(
			'version' => '2.7.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-custom_basemap2',
			'title'   => 'tms',
			'desc'    => __('If true, inverses Y axis numbering for tiles (turn this on for TMS services).','lmm'),
			'type'    => 'radio',
			'std'     => 'false',
			'choices' => array(
				'false' => __('false','lmm'),
				'true' => __('true','lmm')
			)
		);
		$this->_settings['custom_basemap2_subdomains_enabled'] = array(
			'version' => '1.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-custom_basemap2',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from tiles url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes (please enter subdomains in next form field)','lmm'),
				'no' => __('No','lmm')
			)
		);
		$this->_settings['custom_basemap2_subdomains_names'] = array(
			'version' => '1.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-custom_basemap2',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;a&quot;, &quot;b&quot;, &quot;c&quot;",
			'std'     => '&quot;a&quot;, &quot;b&quot;, &quot;c&quot;',
			'type'    => 'text'
		);
		$this->_settings['custom_basemap2_continuousworld_enabled'] = array(
			'version' => '2.7.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-custom_basemap2',
			'title'   => __('Enable continuousWorld?','lmm'),
			'desc'    => __('If set to true, the tile coordinates will not be wrapped by world width (-180 to 180 longitude) or clamped to lie within world height (-90 to 90). Use this if you use Leaflet for maps that do not reflect the real world (e.g. game, indoor or photo maps).','lmm'),
			'type'    => 'radio',
			'std'     => 'false',
			'choices' => array(
				'false' => __('false','lmm'),
				'true' => __('true','lmm')
			)
		);
		$this->_settings['custom_basemap2_nowrap_enabled'] = array(
			'version' => '2.7.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-custom_basemap2',
			'title'   => __('Enable nowrap?','lmm'),
			'desc'    => __('If set to true, the tiles just will not load outside the world width (-180 to 180 longitude) instead of repeating.','lmm'),
			'type'    => 'radio',
			'std'     => 'false',
			'choices' => array(
				'false' => __('false','lmm'),
				'true' => __('true','lmm')
			)
		);
		$this->_settings['custom_basemap2_errortileurl'] = array(
			'version' => '3.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-custom_basemap2',
			'title'   => __('Show errorTile-images if map could not be loaded?','lmm'),
			'desc'    => __('Set to false if you want to use basemaps produced with maptiler for example','lmm'),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);

		/*
		* Custom basemap 3 settings
		*/
		$this->_settings['custom_basemap3_helptext'] = array(
			'version' => '1.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-custom_basemap3',
			'std'     => '',
			'title'   => '',
			'desc'    =>  sprintf(__('For a community-curated list of custom basemaps, please visit %1$s','lmm'),'<a href="https://www.mapsmarker.com/custom-basemaps" target="_blank">mapsmarker.com/custom-basemaps</a>') . '<br/><br/>' . __( 'If you want to use a custom basemap, please enter the related settings below:','lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['custom_basemap3_tileurl'] = array(
			'version' => '1.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-custom_basemap3',
			'title'   => __( 'Tiles URL', 'lmm' ),
			'desc'    => __("For example","lmm"). ": https://{s}.tile.openstreetmap.se/hydda/full/{z}/{x}/{y}.png",
			'std'     => 'https://{s}.tile.openstreetmap.se/hydda/full/{z}/{x}/{y}.png',
			'type'    => 'text'
		);
		$this->_settings['custom_basemap3_attribution'] = array(
			'version' => '1.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-custom_basemap3',
			'title'   => __( 'Attribution', 'lmm' ),
			'desc'    => __("For example","lmm"). ": Tiles courtesy of &lt;a href=&quot;http://openstreetmap.se/&quot; target=&quot;_blank&quot;&gt;OpenStreetMap Sweden&lt;/a&gt;, Map: &copy; &lt;a href=&quot;https://www.openstreetmap.org/copyright&quot;&gt;OpenStreetMap contributors&lt;/a&gt;",
			'std'     => "Tiles courtesy of <a href=&quot;http://openstreetmap.se/&quot; target=&quot;_blank&quot;>OpenStreetMap Sweden</a>;, Map: &copy; <a href=&quot;https://www.openstreetmap.org/copyright&quot; target=&quot;_blank&quot;>OpenStreetMap contributors</a>",
			'type'    => 'text'
		);
		$this->_settings['custom_basemap3_minzoom'] = array(
			'version' => '1.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-custom_basemap3',
			'title'   => __( 'Minimum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '1',
			'type'    => 'text'
		);
		$this->_settings['custom_basemap3_maxzoom'] = array(
			'version' => '1.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-custom_basemap3',
			'title'   => __( 'Maximum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '18',
			'type'    => 'text'
		);
		$this->_settings['custom_basemap3_tms'] = array(
			'version' => '2.7.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-custom_basemap3',
			'title'   => 'tms',
			'desc'    => __('If true, inverses Y axis numbering for tiles (turn this on for TMS services).','lmm'),
			'type'    => 'radio',
			'std'     => 'false',
			'choices' => array(
				'false' => __('false','lmm'),
				'true' => __('true','lmm')
			)
		);
		$this->_settings['custom_basemap3_subdomains_enabled'] = array(
			'version' => '1.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-custom_basemap3',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from tiles url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes (please enter subdomains in next form field)','lmm'),
				'no' => __('No','lmm')
			)
		);
		$this->_settings['custom_basemap3_subdomains_names'] = array(
			'version' => '1.0',
			'pane'    => 'basemaps',
			'section' => 'basemaps-custom_basemap3',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;a&quot;, &quot;b&quot;, &quot;c&quot;",
			'std'     => '&quot;a&quot;, &quot;b&quot;, &quot;c&quot;',
			'type'    => 'text'
		);
		$this->_settings['custom_basemap3_continuousworld_enabled'] = array(
			'version' => '2.7.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-custom_basemap3',
			'title'   => __('Enable continuousWorld?','lmm'),
			'desc'    => __('If set to true, the tile coordinates will not be wrapped by world width (-180 to 180 longitude) or clamped to lie within world height (-90 to 90). Use this if you use Leaflet for maps that do not reflect the real world (e.g. game, indoor or photo maps).','lmm'),
			'type'    => 'radio',
			'std'     => 'false',
			'choices' => array(
				'false' => __('false','lmm'),
				'true' => __('true','lmm')
			)
		);
		$this->_settings['custom_basemap3_nowrap_enabled'] = array(
			'version' => '2.7.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-custom_basemap3',
			'title'   => __('Enable nowrap?','lmm'),
			'desc'    => __('If set to true, the tiles just will not load outside the world width (-180 to 180 longitude) instead of repeating.','lmm'),
			'type'    => 'radio',
			'std'     => 'false',
			'choices' => array(
				'false' => __('false','lmm'),
				'true' => __('true','lmm')
			)
		);
		$this->_settings['custom_basemap3_errortileurl'] = array(
			'version' => '3.1',
			'pane'    => 'basemaps',
			'section' => 'basemaps-custom_basemap3',
			'title'   => __('Show errorTile-images if map could not be loaded?','lmm'),
			'desc'    => __('Set to false if you want to use basemaps produced with maptiler for example','lmm'),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);
		/*
		* emtpy basemap settings
		*/
		$this->_settings['empty_basemap_helptext'] = array(
			'version' => '3.11',
			'pane'    => 'basemaps',
			'section' => 'basemaps-empty_basemap',
			'std'     => '',
			'title'   => '',
			'desc'    => __( 'For special usescases like adding overlays only you can use an empty basemap as background.','lmm') . '<br/><br/>' . __('There are no related settings available - the empty basemap can be used out of the box if the related checkbox at "Available basemaps in control box" has been checked.', 'lmm') . '<br/><br/><div style="height:575px;"></div>',
			'type'    => 'helptext'
		);

		/*===========================================
		*
		*
		* pane overlays
		*
		*
		===========================================*/
		/*
		* Available overlays for new markers/layers
		*/
		$this->_settings['overlays_available_helptext'] = array(
			'version' => '1.0',
			'pane'    => 'overlays',
			'section' => 'overlays-available_overlays',
			'std'     => '',
			'title'   => '',
			'desc'    => __( 'Please select the overlays which should be available in the control box.', 'lmm').'<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-overlays-custom.jpg" width="500" height="162" />',
			'type'    => 'helptext'
		);
		$this->_settings['overlays_custom'] = array(
			'version' => '3.1',
			'pane'    => 'overlays',
			'section' => 'overlays-available_overlays',
			'title'    => __('Available overlays in control box','lmm'),
			'desc'    => __('Custom overlay','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['overlays_custom2'] = array(
			'version' => '1.0',
			'pane'    => 'overlays',
			'section' => 'overlays-available_overlays',
			'title'   => '',
			'desc'    => __('Custom overlay 2','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);

		$this->_settings['overlays_custom3'] = array(
			'version' => '1.0',
			'pane'    => 'overlays',
			'section' => 'overlays-available_overlays',
			'title'   => '',
			'desc'    => __('Custom overlay 3','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);

		$this->_settings['overlays_custom4'] = array(
			'version' => '1.0',
			'pane'    => 'overlays',
			'section' => 'overlays-available_overlays',
			'title'   => '',
			'desc'    => __('Custom overlay 4','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);

		/*
		* Custom overlay settings
		*/
		$this->_settings['overlays_custom_helptext'] = array(
			'version' => '1.0',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay1',
			'std'     => '',
			'title'   => '',
			'desc'    => '<img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-overlays-custom.jpg" width="500" height="162" /><br/><br/>' . sprintf(__('For a community-curated list of custom overlays, please visit %1$s','lmm'),'<a href="https://www.mapsmarker.com/custom-overlays" target="_blank">mapsmarker.com/custom-overlays</a>') . '<br/><br/>' . __( 'If you want to use a custom overlay, please enter the related settings below:','lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['overlays_custom_name'] = array(
			'version' => '1.0',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay1',
			'title'   => __( 'Name', 'lmm' ),
			'desc'   => __( 'Will be displayed in controlbox if selected', 'lmm' ),
			'std'     => 'Waymarked Trails - ' . __('Hiking','lmm'),
			'type'    => 'text'
		);

		$this->_settings['overlays_custom_tileurl'] = array(
			'version' => '1.0',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay1',
			'title'   => __( 'Tiles URL', 'lmm' ),
			'desc'    => __('For example','lmm'). ": http://tile.lonvia.de/hiking/{z}/{x}/{y}.png",
			'std'     => 'http://tile.lonvia.de/hiking/{z}/{x}/{y}.png',
			'type'    => 'text'
		);
		$this->_settings['overlays_custom_attribution'] = array(
			'version' => '1.1',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay1',
			'title'   => __( 'Attribution', 'lmm' ),
			'desc'    => '',
			'std'     => 'Overlay: <a href=&quot;http://waymarkedtrails.org/&quot; target=&quot;_blank&quot;>Lonvias Waymarked Trails</a>',
			'type'    => 'text'
		);
		$this->_settings['overlays_custom_minzoom'] = array(
			'version' => '1.0',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay1',
			'title'   => __( 'Minimum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '1',
			'type'    => 'text'
		);
		$this->_settings['overlays_custom_maxzoom'] = array(
			'version' => '1.0',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay1',
			'title'   => __( 'Maximum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '18',
			'type'    => 'text'
		);
		$this->_settings['overlays_custom_opacity'] = array(
			'version' => '2.7.1',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay1',
			'title'   => __( 'Opacity', 'lmm' ),
			'desc'    => __('The opacity of the tile layer.','lmm'),
			'std'     => '1.0',
			'type'    => 'text'
		);
		$this->_settings['overlays_custom_tms'] = array(
			'version' => '3.1',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay1',
			'title'   => 'tms',
			'desc'    => __('If true, inverses Y axis numbering for tiles (turn this on for TMS services).','lmm'),
			'type'    => 'radio',
			'std'     => 'false',
			'choices' => array(
				'false' => __('false','lmm'),
				'true' => __('true','lmm')
			)
		);
		$this->_settings['overlays_custom_subdomains_enabled'] = array(
			'version' => '1.0',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay1',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from tiles url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes (please enter subdomains in next form field)','lmm'),
				'no' => __('No','lmm')
			)
		);
		$this->_settings['overlays_custom_subdomains_names'] = array(
			'version' => '1.0',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay1',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;a&quot;, &quot;b&quot;, &quot;c&quot;",
			'std'     => '&quot;a&quot;, &quot;b&quot;, &quot;c&quot;',
			'type'    => 'text'
		);
		$this->_settings['overlays_custom_errortileurl'] = array(
			'version' => '3.2',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay1',
			'title'   => __('Show errorTile-images if map could not be loaded?','lmm'),
			'desc'    => __('Set to false if you want to use overlays produced with maptiler for example','lmm'),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);

		/*
		* Custom overlay 2 settings
		*/
		$this->_settings['overlays_custom2_helptext'] = array(
			'version' => '1.0',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay2',
			'std'     => '',
			'title'   => '',
			'desc'    => '<img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-overlays-custom.jpg" width="500" height="162" /><br/><br/>' . sprintf(__('For a community-curated list of custom overlays, please visit %1$s','lmm'),'<a href="https://www.mapsmarker.com/custom-overlays" target="_blank">mapsmarker.com/custom-overlays</a>') . '<br/><br/>' . __( 'If you want to use a custom overlay, please enter the related settings below:','lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['overlays_custom2_name'] = array(
			'version' => '1.0',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay2',
			'title'   => __( 'Name', 'lmm' ),
			'desc'   => __( 'Will be displayed in controlbox if selected', 'lmm' ),
			'std'     => 'Waymarked Trails - ' . __('Cycling','lmm'),
			'type'    => 'text'
		);

		$this->_settings['overlays_custom2_tileurl'] = array(
			'version' => '1.0',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay2',
			'title'   => __( 'Tiles URL', 'lmm' ),
			'desc'    => __('For example','lmm'). ": http://tile.lonvia.de/cycling/{z}/{x}/{y}.png",
			'std'     => 'http://tile.lonvia.de/cycling/{z}/{x}/{y}.png',
			'type'    => 'text'
		);
		$this->_settings['overlays_custom2_attribution'] = array(
			'version' => '1.1',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay2',
			'title'   => __( 'Attribution', 'lmm' ),
			'desc'    => '',
			'std'     => 'Overlay: <a href=&quot;http://waymarkedtrails.org/&quot; target=&quot;_blank&quot;>Lonvias Waymarked Trails</a>',
			'type'    => 'text'
		);
		$this->_settings['overlays_custom2_minzoom'] = array(
			'version' => '1.0',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay2',
			'title'   => __( 'Minimum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '1',
			'type'    => 'text'
		);
		$this->_settings['overlays_custom2_maxzoom'] = array(
			'version' => '1.0',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay2',
			'title'   => __( 'Maximum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '18',
			'type'    => 'text'
		);
		$this->_settings['overlays_custom2_opacity'] = array(
			'version' => '2.7.1',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay2',
			'title'   => __( 'Opacity', 'lmm' ),
			'desc'    => __('The opacity of the tile layer.','lmm'),
			'std'     => '1.0',
			'type'    => 'text'
		);
		$this->_settings['overlays_custom2_tms'] = array(
			'version' => '3.1',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay2',
			'title'   => 'tms',
			'desc'    => __('If true, inverses Y axis numbering for tiles (turn this on for TMS services).','lmm'),
			'type'    => 'radio',
			'std'     => 'false',
			'choices' => array(
				'false' => __('false','lmm'),
				'true' => __('true','lmm')
			)
		);
		$this->_settings['overlays_custom2_subdomains_enabled'] = array(
			'version' => '1.0',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay2',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from tiles url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes (please enter subdomains in next form field)','lmm'),
				'no' => __('No','lmm')
			)
		);
		$this->_settings['overlays_custom2_subdomains_names'] = array(
			'version' => '1.0',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay2',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;a&quot;, &quot;b&quot;, &quot;c&quot;",
			'std'     => '&quot;a&quot;, &quot;b&quot;, &quot;c&quot;',
			'type'    => 'text'
		);
		$this->_settings['overlays_custom2_errortileurl'] = array(
			'version' => '3.2',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay2',
			'title'   => __('Show errorTile-images if map could not be loaded?','lmm'),
			'desc'    => __('Set to false if you want to use overlays produced with maptiler for example','lmm'),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);

		/*
		* Custom overlay 3 settings
		*/
		$this->_settings['overlays_custom3_helptext'] = array(
			'version' => '1.0',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay3',
			'std'     => '',
			'title'   => '',
			'desc'    => '<img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-overlays-custom.jpg" width="500" height="162" /><br/><br/>' . sprintf(__('For a community-curated list of custom overlays, please visit %1$s','lmm'),'<a href="https://www.mapsmarker.com/custom-overlays" target="_blank">mapsmarker.com/custom-overlays</a>') . '<br/><br/>' . __( 'If you want to use a custom overlay, please enter the related settings below:','lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['overlays_custom3_name'] = array(
			'version' => '1.0',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay3',
			'title'   => __( 'Name', 'lmm' ),
			'desc'   => __( 'Will be displayed in controlbox if selected', 'lmm' ),
			'std'     => 'OpenWeatherMap - ' . __('Rain','lmm'),
			'type'    => 'text'
		);
		$this->_settings['overlays_custom3_tileurl'] = array(
			'version' => '1.0',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay3',
			'title'   => __( 'Tiles URL', 'lmm' ),
			'desc'    => __("For example","lmm"). ": http://{s}.tile.openweathermap.org/map/rain/{z}/{x}/{y}.png",
			'std'     => 'http://{s}.tile.openweathermap.org/map/rain/{z}/{x}/{y}.png',
			'type'    => 'text'
		);
		$this->_settings['overlays_custom3_attribution'] = array(
			'version' => '1.1',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay3',
			'title'   => __( 'Attribution', 'lmm' ),
			'desc'    => '',
			'std'     => 'Map data: <a href=&quot;http://openweathermap.org&quot; target=&quot;_blank&quot;>OpenWeatherMap</a>',
			'type'    => 'text'
		);
		$this->_settings['overlays_custom3_minzoom'] = array(
			'version' => '1.0',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay3',
			'title'   => __( 'Minimum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '1',
			'type'    => 'text'
		);
		$this->_settings['overlays_custom3_maxzoom'] = array(
			'version' => '1.0',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay3',
			'title'   => __( 'Maximum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '18',
			'type'    => 'text'
		);
		$this->_settings['overlays_custom3_opacity'] = array(
			'version' => '2.7.1',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay3',
			'title'   => __( 'Opacity', 'lmm' ),
			'desc'    => __('The opacity of the tile layer.','lmm'),
			'std'     => '1.0',
			'type'    => 'text'
		);
		$this->_settings['overlays_custom3_tms'] = array(
			'version' => '3.1',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay3',
			'title'   => 'tms',
			'desc'    => __('If true, inverses Y axis numbering for tiles (turn this on for TMS services).','lmm'),
			'type'    => 'radio',
			'std'     => 'false',
			'choices' => array(
				'false' => __('false','lmm'),
				'true' => __('true','lmm')
			)
		);
		$this->_settings['overlays_custom3_subdomains_enabled'] = array(
			'version' => '1.0',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay3',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from tiles url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes (please enter subdomains in next form field)','lmm'),
				'no' => __('No','lmm')
			)
		);
		$this->_settings['overlays_custom3_subdomains_names'] = array(
			'version' => '1.0',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay3',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;a&quot;, &quot;b&quot;, &quot;c&quot;",
			'std'     => '&quot;a&quot;, &quot;b&quot;, &quot;c&quot;',
			'type'    => 'text'
		);
		$this->_settings['overlays_custom3_errortileurl'] = array(
			'version' => '3.2',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay3',
			'title'   => __('Show errorTile-images if map could not be loaded?','lmm'),
			'desc'    => __('Set to false if you want to use overlays produced with maptiler for example','lmm'),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);

		/*
		* Custom overlay 4 settings
		*/
		$this->_settings['overlays_custom4_helptext'] = array(
			'version' => '1.0',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay4',
			'std'     => '',
			'title'   => '',
			'desc'    => '<img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-overlays-custom.jpg" width="500" height="162" /><br/><br/>' . sprintf(__('For a community-curated list of custom overlays, please visit %1$s','lmm'),'<a href="https://www.mapsmarker.com/custom-overlays" target="_blank">mapsmarker.com/custom-overlays</a>') . '<br/><br/>' . __( 'If you want to use a custom overlay, please enter the related settings below:','lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['overlays_custom4_name'] = array(
			'version' => '1.0',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay4',
			'title'   => __( 'Name', 'lmm' ),
			'desc'   => __( 'Will be displayed in controlbox if selected', 'lmm' ),
			'std'     => 'OpenWeatherMap - ' . __('Temperature','lmm'),
			'type'    => 'text'
		);

		$this->_settings['overlays_custom4_tileurl'] = array(
			'version' => '1.0',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay4',
			'title'   => __( 'Tiles URL', 'lmm' ),
			'desc'    => __("For example","lmm"). ": http://{s}.tile.openweathermap.org/map/temp/{z}/{x}/{y}.png",
			'std'     => 'http://{s}.tile.openweathermap.org/map/temp/{z}/{x}/{y}.png',
			'type'    => 'text'
		);
		$this->_settings['overlays_custom4_attribution'] = array(
			'version' => '1.1',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay4',
			'title'   => __( 'Attribution', 'lmm' ),
			'desc'    => '',
			'std'     => 'Map data: <a href=&quot;http://openweathermap.org&quot; target=&quot;_blank&quot;>OpenWeatherMap</a>',
			'type'    => 'text'
		);
		$this->_settings['overlays_custom4_minzoom'] = array(
			'version' => '1.0',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay4',
			'title'   => __( 'Minimum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '1',
			'type'    => 'text'
		);
		$this->_settings['overlays_custom4_maxzoom'] = array(
			'version' => '1.0',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay4',
			'title'   => __( 'Maximum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '18',
			'type'    => 'text'
		);
		$this->_settings['overlays_custom4_opacity'] = array(
			'version' => '2.7.1',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay4',
			'title'   => __( 'Opacity', 'lmm' ),
			'desc'    => __('The opacity of the tile layer.','lmm'),
			'std'     => '1.0',
			'type'    => 'text'
		);
		$this->_settings['overlays_custom4_tms'] = array(
			'version' => '3.1',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay4',
			'title'   => 'tms',
			'desc'    => __('If true, inverses Y axis numbering for tiles (turn this on for TMS services).','lmm'),
			'type'    => 'radio',
			'std'     => 'false',
			'choices' => array(
				'false' => __('false','lmm'),
				'true' => __('true','lmm')
			)
		);
		$this->_settings['overlays_custom4_subdomains_enabled'] = array(
			'version' => '1.0',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay4',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from tiles url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes (please enter subdomains in next form field)','lmm'),
				'no' => __('No','lmm')
			)
		);
		$this->_settings['overlays_custom4_subdomains_names'] = array(
			'version' => '1.0',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay4',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;a&quot;, &quot;b&quot;, &quot;c&quot;",
			'std'     => '&quot;a&quot;, &quot;b&quot;, &quot;c&quot;',
			'type'    => 'text'
		);
		$this->_settings['overlays_custom4_errortileurl'] = array(
			'version' => '3.2',
			'pane'    => 'overlays',
			'section' => 'overlays-custom_overlay4',
			'title'   => __('Show errorTile-images if map could not be loaded?','lmm'),
			'desc'    => __('Set to false if you want to use overlays produced with maptiler for example','lmm'),
			'type'    => 'radio',
			'std'     => 'true',
			'choices' => array(
				'true' => __('true','lmm'),
				'false' => __('false','lmm')
			)
		);

		/*===========================================
		*
		*
		* pane wms
		*
		*
		===========================================*/
		/*
		* Available WMS layers for new markers/layers
		*/
		$this->_settings['wms_available_helptext2'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-available_wms',
			'std'     => '',
			'title'   => '',
			'desc'    => __('WMS stands for <a href="http://www.opengeospatial.org/standards/wms" target="_blank">Web Map Service</a> and is a standard protocol for serving georeferenced map images over the Internet that are generated by a map server using data from a GIS database.<br/>With Leaflet Maps Marker you can configure up to 10 WMS layers which can be enabled for each map. As default, 10 WMS layers from <a href="http://data.wien.gv.at" target="_blank">OGD Vienna</a> and from the <a href="http://www.eea.europa.eu/code/gis" target="_blank">European Environment Agency</a> have been predefined for you.<br/>A selection of further possible WMS layers can be found at <a href="http://www.mapsmarker.com/wms" target="_blank">http://www.mapsmarker.com/wms</a>', 'lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['wms_available_helptext'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-available_wms',
			'std'     => '',
			'title'   => '',
			'desc'    => __( 'Please select the WMS layers which should be available when creating new markers/layers', 'lmm').'<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-wms-available-wms-layers.jpg" width="724" height="245" />',
			'type'    => 'helptext'
		);
		$this->_settings['wms_wms_available'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-available_wms',
			'title'    => __('Available WMS layers','lmm'),
			'desc'    => 'WMS 1',
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['wms_wms2_available'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-available_wms',
			'title'    => '',
			'desc'    => 'WMS 2',
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['wms_wms3_available'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-available_wms',
			'title'    => '',
			'desc'    => 'WMS 3',
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['wms_wms4_available'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-available_wms',
			'title'    => '',
			'desc'    => 'WMS 4',
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['wms_wms5_available'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-available_wms',
			'title'    => '',
			'desc'    => 'WMS 5',
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['wms_wms6_available'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-available_wms',
			'title'    => '',
			'desc'    => 'WMS 6',
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['wms_wms7_available'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-available_wms',
			'title'    => '',
			'desc'    => 'WMS 7',
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['wms_wms8_available'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-available_wms',
			'title'    => '',
			'desc'    => 'WMS 8',
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['wms_wms9_available'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-available_wms',
			'title'    => '',
			'desc'    => 'WMS 9',
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['wms_wms10_available'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-available_wms',
			'title'    => '',
			'desc'    => 'WMS 10',
			'type'    => 'checkbox',
			'std'     => 1
		);

		/*
		* WMS layer settings
		*/
		$this->_settings['wms_wms_helptext'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms1',
			'std'     => '',
			'title'   => '',
			'desc'    => '', //empty for not breaking settings layout
			'type'    => 'helptext'
		);
		$this->_settings['wms_wms_name'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms1',
			'title'    => __('Name','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => '<a href=&quot;https://open.wien.at&quot; target=&quot;_blank&quot;>OGD Vienna - Public Toilets</a>'
		);
		$this->_settings['wms_wms_baseurl'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms1',
			'title'    => __('baseURL','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'https://data.wien.gv.at/daten/wms'
		);
		$this->_settings['wms_wms_layers'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms1',
			'title'    => __('Layers','lmm'),
			'desc'    => __('(required) Comma-separated list of WMS layers to show','lmm'),
			'type'    => 'text',
			'std'     => 'WCANLAGEOGD'
		);
		$this->_settings['wms_wms_styles'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms1',
			'title'    => __('Styles','lmm'),
			'desc'    => __('Comma-separated list of WMS styles','lmm'),
			'type'    => 'text',
			'std'     => ''
		);
		$this->_settings['wms_wms_format'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms1',
			'title'    => __('Format','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'image/gif'
		);
		$this->_settings['wms_wms_transparent'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms1',
			'title'   => __('Transparent','lmm'),
			'desc'    => __('If yes, the WMS service will return images with transparency','lmm'),
			'type'    => 'radio',
			'std'     => 'TRUE',
			'choices' => array(
				'TRUE' => __('true','lmm'),
				'FALSE' => __('false','lmm')
			)
		);
		$this->_settings['wms_wms_version'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms1',
			'title'    => __('Version','lmm'),
			'desc'    => __('Version of the WMS service to use','lmm'),
			'type'    => 'text',
			'std'     => '1.1.1'
		);
		$this->_settings['wms_wms_attribution'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms1',
			'title'    => __('Attribution','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'WMS: City of Vienna (<a href=&quot;https://open.wien.at&quot; target=&quot;_blank&quot;>open.wien.gv.at</a>)'
		);
		$this->_settings['wms_wms_legend_enabled'] = array(
			'version' => '1.1',
			'pane'    => 'wms',
			'section' => 'wms-wms1',
			'title'   => __('Display legend?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes','lmm'),
				'no' => __('No','lmm')
			)
		);
		$this->_settings['wms_wms_legend'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms1',
			'title'    => __('Legend','lmm'),
			'desc'    => __('URL of image which gets show when hovering the text "(Legend)" next to WMS attribution text','lmm'),
			'type'    => 'text',
			'std'     => ''
		);
		$this->_settings['wms_wms_subdomains_enabled'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms1',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from base url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'no',
			'choices' => array(
				'no' => __('No','lmm'),
				'yes' => __('Yes (please enter subdomains in next form field)','lmm')
			)
		);
		$this->_settings['wms_wms_subdomains_names'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms1',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;",
			'std'     => '&quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;',
			'type'    => 'text'
		);
		$this->_settings['wms_wms_kml_helptext'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms1',
			'std'     => '',
			'title'   => '<h4 class="h4-lmm-settings">' . __('KML settings','lmm') . '</h4>',
			'desc'    => __('If the WMS server supports KML output of the WMS layer, the settings below will be used when a marker or layer map with this active WMS layer is exported as KML.','lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['wms_wms_kml_support'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms1',
			'title'   => __('Does the WMS server support KML output?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('yes','lmm'),
				'no' => __('no','lmm')
			)
		);
		$this->_settings['wms_wms_kml_href'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms1',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#href" target="_blank">href</a>',
			'desc'    => __('http-address of the KML-webservice of the WMS layer','lmm'),
			'type'    => 'text',
			'std'     => 'https://data.wien.gv.at/daten/geo?version=1.3.0&service=WMS&request=GetMap&crs=EPSG:4326&bbox=48.10,16.16,48.34,16.59&width=1&height=1&layers=ogdwien:WCANLAGEOGD&styles=&format=application/vnd.google-earth.kml+xml'
		);
		$this->_settings['wms_wms_kml_refreshMode'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms1',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshmode" target="_blank">refreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'onChange',
			'choices' => array(
				'onChange' => __('onChange (refresh when the file is loaded and whenever the Link parameters change)','lmm'),
				'onInterval' => __('onInterval (refresh every n seconds (specified in refreshInterval)','lmm'),
				'onExpire' => __('onExpire (refresh the file when the expiration time is reached)','lmm'),
				'onStop' => __('onStop (after camera movement stops)','lmm')
			)
		);
		$this->_settings['wms_wms_kml_refreshInterval'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms1',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshinterval" target="_blank">refreshInterval</a>',
			'desc'    => __('Indicates to refresh the file every n seconds','lmm'),
			'type'    => 'text',
			'std'     => '30'
		);
		$this->_settings['wms_wms_kml_viewRefreshMode'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms1',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshmode" target="_blank">viewrefreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'never',
			'choices' => array(
				'never' => __('never (ignore changes in the view)','lmm'),
				'onStop' => __('onStop (refresh the file n seconds after movement stops, where n is specified in viewRefreshTime)','lmm'),
				'onRequest' => __('onRequest (refresh the file only when the user explicitly requests it)','lmm')
			)
		);
		$this->_settings['wms_wms_kml_viewRefreshTime'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms1',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshtime" target="_blank">viewRefreshTime</a>',
			'desc'    => __('After camera movement stops, specifies the number of seconds to wait before refreshing the view (is used when viewRefreshMode is set to onStop)','lmm'),
			'type'    => 'text',
			'std'     => '1'
		);

		/*
		* WMS layer 2 settings
		*/
		$this->_settings['wms_wms2_helptext'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms2',
			'std'     => '',
			'title'   => '',
			'desc'    => '', //empty for not breaking settings layout
			'type'    => 'helptext'
		);
		$this->_settings['wms_wms2_name'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms2',
			'title'    => __('Name','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => '<a href=&quot;https://open.wien.at&quot; target=&quot;_blank&quot;>OGD Vienna - Elevators at stations</a>'
		);
		$this->_settings['wms_wms2_baseurl'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms2',
			'title'    => __('baseURL','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'https://data.wien.gv.at/daten/wms'
		);
		$this->_settings['wms_wms2_layers'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms2',
			'title'    => __('Layers','lmm'),
			'desc'    => __('(required) Comma-separated list of WMS layers to show','lmm'),
			'type'    => 'text',
			'std'     => 'AUFZUGOGD'
		);
		$this->_settings['wms_wms2_styles'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms2',
			'title'    => __('Styles','lmm'),
			'desc'    => __('Comma-separated list of WMS styles','lmm'),
			'type'    => 'text',
			'std'     => ''
		);
		$this->_settings['wms_wms2_format'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms2',
			'title'    => __('Format','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'image/gif'
		);
		$this->_settings['wms_wms2_transparent'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms2',
			'title'   => __('Transparent','lmm'),
			'desc'    => __('If yes, the WMS service will return images with transparency','lmm'),
			'type'    => 'radio',
			'std'     => 'TRUE',
			'choices' => array(
				'TRUE' => __('true','lmm'),
				'FALSE' => __('false','lmm')
			)
		);
		$this->_settings['wms_wms2_version'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms2',
			'title'    => __('Version','lmm'),
			'desc'    => __('Version of the WMS service to use','lmm'),
			'type'    => 'text',
			'std'     => '1.1.1'
		);
		$this->_settings['wms_wms2_attribution'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms2',
			'title'    => __('Attribution','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'WMS: City of Vienna (<a href=&quot;https://open.wien.at&quot; target=&quot;_blank&quot;>http://open.wien.at</a>)'
		);
		$this->_settings['wms_wms2_legend_enabled'] = array(
			'version' => '1.1',
			'pane'    => 'wms',
			'section' => 'wms-wms2',
			'title'   => __('Display legend?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes','lmm'),
				'no' => __('No','lmm')
			)
		);
		$this->_settings['wms_wms2_legend'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms2',
			'title'    => __('Legend','lmm'),
			'desc'    => __('URL of image which gets show when hovering the text "(Legend)" next to WMS attribution text','lmm'),
			'type'    => 'text',
			'std'     => ''
		);
		$this->_settings['wms_wms2_subdomains_enabled'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms2',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from base url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'no',
			'choices' => array(
				'no' => __('No','lmm'),
				'yes' => __('Yes (please enter subdomains in next form field)','lmm')
			)
		);
		$this->_settings['wms_wms2_subdomains_names'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms2',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;",
			'std'     => '&quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;',
			'type'    => 'text'
		);
		$this->_settings['wms_wms2_kml_helptext'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms2',
			'std'     => '',
			'title'   => '<h4 class="h4-lmm-settings">' . __('KML settings','lmm') . '</h4>',
			'desc'    => __('If the WMS server supports KML output of the WMS layer, the settings below will be used when a marker or layer map with this active WMS layer is exported as KML.','lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['wms_wms2_kml_support'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms2',
			'title'   => __('Does the WMS server support KML output?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('yes','lmm'),
				'no' => __('no','lmm')
			)
		);
		$this->_settings['wms_wms2_kml_href'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms2',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#href" target="_blank">href</a>',
			'desc'    => __('http-address of the KML-webservice of the WMS layer','lmm'),
			'type'    => 'text',
			'std'     => 'https://data.wien.gv.at/daten/geo?version=1.3.0&service=WMS&request=GetMap&crs=EPSG:4326&bbox=48.10,16.16,48.34,16.59&width=1&height=1&layers=ogdwien:AUFZUGOGD&styles=&format=application/vnd.google-earth.kml+xml'
		);
		$this->_settings['wms_wms2_kml_refreshMode'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms2',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshmode" target="_blank">refreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'onChange',
			'choices' => array(
				'onChange' => __('onChange (refresh when the file is loaded and whenever the Link parameters change)','lmm'),
				'onInterval' => __('onInterval (refresh every n seconds (specified in refreshInterval)','lmm'),
				'onExpire' => __('onExpire (refresh the file when the expiration time is reached)','lmm'),
				'onStop' => __('onStop (after camera movement stops)','lmm')
			)
		);
		$this->_settings['wms_wms2_kml_refreshInterval'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms2',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshinterval" target="_blank">refreshInterval</a>',
			'desc'    => __('Indicates to refresh the file every n seconds','lmm'),
			'type'    => 'text',
			'std'     => '30'
		);
		$this->_settings['wms_wms2_kml_viewRefreshMode'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms2',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshmode" target="_blank">viewrefreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'never',
			'choices' => array(
				'never' => __('never (ignore changes in the view)','lmm'),
				'onStop' => __('onStop (refresh the file n seconds after movement stops, where n is specified in viewRefreshTime)','lmm'),
				'onRequest' => __('onRequest (refresh the file only when the user explicitly requests it)','lmm')
			)
		);
		$this->_settings['wms_wms2_kml_viewRefreshTime'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms2',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshtime" target="_blank">viewRefreshTime</a>',
			'desc'    => __('After camera movement stops, specifies the number of seconds to wait before refreshing the view (is used when viewRefreshMode is set to onStop)','lmm'),
			'type'    => 'text',
			'std'     => '1'
		);

		/*
		* WMS layer 3 settings
		*/
		$this->_settings['wms_wms3_helptext'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms3',
			'std'     => '',
			'title'   => '',
			'desc'    => '', //empty for not breaking settings layout
			'type'    => 'helptext'
		);
		$this->_settings['wms_wms3_name'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms3',
			'title'    => __('Name','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => '<a href=&quot;http://discomap.eea.europa.eu/ArcGIS/rest/services/Air/EPRTRDiffuseAir_Dyna_WGS84/MapServer/7&quot; target=&quot;_blank&quot;>EEA - CO emissions from road transport</a>'
		);
		$this->_settings['wms_wms3_baseurl'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms3',
			'title'    => __('baseURL','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Air/EPRTRDiffuseAir_Dyna_WGS84/MapServer/WMSServer'
		);
		$this->_settings['wms_wms3_layers'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms3',
			'title'    => __('Layers','lmm'),
			'desc'    => __('(required) Comma-separated list of WMS layers to show','lmm'),
			'type'    => 'text',
			'std'     => '24'
		);
		$this->_settings['wms_wms3_styles'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms3',
			'title'    => __('Styles','lmm'),
			'desc'    => __('Comma-separated list of WMS styles','lmm'),
			'type'    => 'text',
			'std'     => ''
		);
		$this->_settings['wms_wms3_format'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms3',
			'title'    => __('Format','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'image/png'
		);
		$this->_settings['wms_wms3_transparent'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms3',
			'title'   => __('Transparent','lmm'),
			'desc'    => __('If yes, the WMS service will return images with transparency','lmm'),
			'type'    => 'radio',
			'std'     => 'TRUE',
			'choices' => array(
				'TRUE' => __('true','lmm'),
				'FALSE' => __('false','lmm')
			)
		);
		$this->_settings['wms_wms3_attribution'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms3',
			'title'    => __('Attribution','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'WMS: <a href=&quot;http://www.eea.europa.eu/code/gis&quot; target=&quot;_blank&quot;>European Environment Agency</a>'
		);
		$this->_settings['wms_wms3_legend_enabled'] = array(
			'version' => '1.1',
			'pane'    => 'wms',
			'section' => 'wms-wms3',
			'title'   => __('Display legend?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes','lmm'),
				'no' => __('No','lmm')
			)
		);
		$this->_settings['wms_wms3_legend'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms3',
			'title'    => __('Legend','lmm'),
			'desc'    => __('URL of image which gets show when hovering the text "(Legend)" next to WMS attribution text','lmm'),
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Air/EPRTRDiffuseAir_Dyna_WGS84/MapServer/WMSServer?request=GetLegendGraphic%26version=1.3.0%26format=image/png%26layer=1'
		);
		$this->_settings['wms_wms3_version'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms3',
			'title'    => __('Version','lmm'),
			'desc'    => __('Version of the WMS service to use','lmm'),
			'type'    => 'text',
			'std'     => '1.3.0'
		);
		$this->_settings['wms_wms3_subdomains_enabled'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms3',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from base url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'no',
			'choices' => array(
				'no' => __('No','lmm'),
				'yes' => __('Yes (please enter subdomains in next form field)','lmm')
			)
		);
		$this->_settings['wms_wms3_subdomains_names'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms3',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;",
			'std'     => '&quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;',
			'type'    => 'text'
		);
		$this->_settings['wms_wms3_kml_helptext'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms3',
			'std'     => '',
			'title'   => '<h4 class="h4-lmm-settings">' . __('KML settings','lmm') . '</h4>',
			'desc'    => __('If the WMS server supports KML output of the WMS layer, the settings below will be used when a marker or layer map with this active WMS layer is exported as KML.','lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['wms_wms3_kml_support'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms3',
			'title'   => __('Does the WMS server support KML output?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('yes','lmm'),
				'no' => __('no','lmm')
			)
		);
		$this->_settings['wms_wms3_kml_href'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms3',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#href" target="_blank">href</a>',
			'desc'    => __('http-address of the KML-webservice of the WMS layer','lmm'),
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/rest/services/Air/EPRTRDiffuseAir_Dyna_WGS84/MapServer/generatekml?docName=&l%3A7=on&layers=7&layerOptions=nonComposite'
		);
		$this->_settings['wms_wms3_kml_refreshMode'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms3',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshmode" target="_blank">refreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'onChange',
			'choices' => array(
				'onChange' => __('onChange (refresh when the file is loaded and whenever the Link parameters change)','lmm'),
				'onInterval' => __('onInterval (refresh every n seconds (specified in refreshInterval)','lmm'),
				'onExpire' => __('onExpire (refresh the file when the expiration time is reached)','lmm'),
				'onStop' => __('onStop (after camera movement stops)','lmm')
			)
		);
		$this->_settings['wms_wms3_kml_refreshInterval'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms3',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshinterval" target="_blank">refreshInterval</a>',
			'desc'    => __('Indicates to refresh the file every n seconds','lmm'),
			'type'    => 'text',
			'std'     => '30'
		);
		$this->_settings['wms_wms3_kml_viewRefreshMode'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms3',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshmode" target="_blank">viewrefreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'never',
			'choices' => array(
				'never' => __('never (ignore changes in the view)','lmm'),
				'onStop' => __('onStop (refresh the file n seconds after movement stops, where n is specified in viewRefreshTime)','lmm'),
				'onRequest' => __('onRequest (refresh the file only when the user explicitly requests it)','lmm')
			)
		);
		$this->_settings['wms_wms3_kml_viewRefreshTime'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms3',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshtime" target="_blank">viewRefreshTime</a>',
			'desc'    => __('After camera movement stops, specifies the number of seconds to wait before refreshing the view (is used when viewRefreshMode is set to onStop)','lmm'),
			'type'    => 'text',
			'std'     => '1'
		);

		/*
		* WMS layer 4 settings
		*/
		$this->_settings['wms_wms4_helptext'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms4',
			'std'     => '',
			'title'   => '',
			'desc'    => '', //empty for not breaking settings layout
			'type'    => 'helptext'
		);
		$this->_settings['wms_wms4_name'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms4',
			'title'    => __('Name','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => '<a href=&quot;http://discomap.eea.europa.eu/ArcGIS/rest/services/Land/CLC2006_Dyna_WM/MapServer&quot; target=&quot;_blank&quot;>EEA - Agricultural areas</a>'
		);
		$this->_settings['wms_wms4_baseurl'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms4',
			'title'    => __('baseURL','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Land/CLC2006_Dyna_WM/MapServer/WMSServer'
		);
		$this->_settings['wms_wms4_layers'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms4',
			'title'    => __('Layers','lmm'),
			'desc'    => __('(required) Comma-separated list of WMS layers to show','lmm'),
			'type'    => 'text',
			'std'     => '10'
		);
		$this->_settings['wms_wms4_styles'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms4',
			'title'    => __('Styles','lmm'),
			'desc'    => __('Comma-separated list of WMS styles','lmm'),
			'type'    => 'text',
			'std'     => ''
		);
		$this->_settings['wms_wms4_format'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms4',
			'title'    => __('Format','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'image/png'
		);
		$this->_settings['wms_wms4_transparent'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms4',
			'title'   => __('Transparent','lmm'),
			'desc'    => __('If yes, the WMS service will return images with transparency','lmm'),
			'type'    => 'radio',
			'std'     => 'TRUE',
			'choices' => array(
				'TRUE' => __('true','lmm'),
				'FALSE' => __('false','lmm')
			)
		);
		$this->_settings['wms_wms4_version'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms4',
			'title'    => __('Version','lmm'),
			'desc'    => __('Version of the WMS service to use','lmm'),
			'type'    => 'text',
			'std'     => '1.3.0'
		);
		$this->_settings['wms_wms4_attribution'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms4',
			'title'    => __('Attribution','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'WMS: <a href=&quot;http://www.eea.europa.eu/code/gis&quot; target=&quot;_blank&quot;>European Environment Agency</a>'
		);
		$this->_settings['wms_wms4_legend_enabled'] = array(
			'version' => '1.1',
			'pane'    => 'wms',
			'section' => 'wms-wms4',
			'title'   => __('Display legend?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes','lmm'),
				'no' => __('No','lmm')
			)
		);
		$this->_settings['wms_wms4_legend'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms4',
			'title'    => __('Legend','lmm'),
			'desc'    => __('URL of image which gets show when hovering the text "(Legend)" next to WMS attribution text','lmm'),
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Land/CLC2000_Cach_WM/MapServer/WMSServer?request=GetLegendGraphic%26version=1.3.0%26format=image/png%26layer=11'
		);
		$this->_settings['wms_wms4_subdomains_enabled'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms4',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from base url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'no',
			'choices' => array(
				'no' => __('No','lmm'),
				'yes' => __('Yes (please enter subdomains in next form field)','lmm')
			)
		);
		$this->_settings['wms_wms4_subdomains_names'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms4',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;",
			'std'     => '&quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;',
			'type'    => 'text'
		);
		$this->_settings['wms_wms4_kml_helptext'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms4',
			'std'     => '',
			'title'   => '<h4 class="h4-lmm-settings">' . __('KML settings','lmm') . '</h4>',
			'desc'    => __('If the WMS server supports KML output of the WMS layer, the settings below will be used when a marker or layer map with this active WMS layer is exported as KML.','lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['wms_wms4_kml_support'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms4',
			'title'   => __('Does the WMS server support KML output?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('yes','lmm'),
				'no' => __('no','lmm')
			)
		);
		$this->_settings['wms_wms4_kml_href'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms4',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#href" target="_blank">href</a>',
			'desc'    => __('http-address of the KML-webservice of the WMS layer','lmm'),
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/rest/services/Land/CLC2006_Dyna_WM/MapServer/generatekml?docName=&l%3A5=on&layers=5&layerOptions=nonComposite'
		);
		$this->_settings['wms_wms4_kml_refreshMode'] = array(
			'version' => '1.4.3',


			'pane'    => 'wms',
			'section' => 'wms-wms4',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshmode" target="_blank">refreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'onChange',
			'choices' => array(
				'onChange' => __('onChange (refresh when the file is loaded and whenever the Link parameters change)','lmm'),
				'onInterval' => __('onInterval (refresh every n seconds (specified in refreshInterval)','lmm'),
				'onExpire' => __('onExpire (refresh the file when the expiration time is reached)','lmm'),
				'onStop' => __('onStop (after camera movement stops)','lmm')
			)
		);
		$this->_settings['wms_wms4_kml_refreshInterval'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms4',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshinterval" target="_blank">refreshInterval</a>',
			'desc'    => __('Indicates to refresh the file every n seconds','lmm'),
			'type'    => 'text',
			'std'     => '30'
		);
		$this->_settings['wms_wms4_kml_viewRefreshMode'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms4',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshmode" target="_blank">viewrefreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'never',
			'choices' => array(
				'never' => __('never (ignore changes in the view)','lmm'),
				'onStop' => __('onStop (refresh the file n seconds after movement stops, where n is specified in viewRefreshTime)','lmm'),
				'onRequest' => __('onRequest (refresh the file only when the user explicitly requests it)','lmm')
			)
		);
		$this->_settings['wms_wms4_kml_viewRefreshTime'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms4',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshtime" target="_blank">viewRefreshTime</a>',
			'desc'    => __('After camera movement stops, specifies the number of seconds to wait before refreshing the view (is used when viewRefreshMode is set to onStop)','lmm'),
			'type'    => 'text',
			'std'     => '1'
		);

		/*
		* WMS layer 5 settings
		*/
		$this->_settings['wms_wms5_helptext'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms5',
			'std'     => '',
			'title'   => '',
			'desc'    => '', //empty for not breaking settings layout
			'type'    => 'helptext'
		);
		$this->_settings['wms_wms5_name'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms5',
			'title'    => __('Name','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => '<a href=&quot;http://discomap.eea.europa.eu/arcgis/rest/services/Land/LandscapeFragmentation_LAEA/MapServer&quot; target=&quot;_blank&quot;>EEA - Landscape fragmentation</a>'
		);
		$this->_settings['wms_wms5_baseurl'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms5',
			'title'    => __('baseURL','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/arcgis/services/Land/LandscapeFragmentation_LAEA/MapServer/WmsServer?'
		);
		$this->_settings['wms_wms5_layers'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms5',
			'title'    => __('Layers','lmm'),
			'desc'    => __('(required) Comma-separated list of WMS layers to show','lmm'),
			'type'    => 'text',
			'std'     => '0'
		);
		$this->_settings['wms_wms5_styles'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms5',
			'title'    => __('Styles','lmm'),
			'desc'    => __('Comma-separated list of WMS styles','lmm'),
			'type'    => 'text',
			'std'     => ''
		);
		$this->_settings['wms_wms5_format'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms5',
			'title'    => __('Format','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'image/png'
		);
		$this->_settings['wms_wms5_transparent'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms5',
			'title'   => __('Transparent','lmm'),
			'desc'    => __('If yes, the WMS service will return images with transparency','lmm'),
			'type'    => 'radio',
			'std'     => 'TRUE',
			'choices' => array(
				'TRUE' => __('true','lmm'),
				'FALSE' => __('false','lmm')
			)
		);
		$this->_settings['wms_wms5_version'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms5',
			'title'    => __('Version','lmm'),
			'desc'    => __('Version of the WMS service to use','lmm'),
			'type'    => 'text',
			'std'     => '1.3.0'
		);
		$this->_settings['wms_wms5_attribution'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms5',
			'title'    => __('Attribution','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'WMS: <a href=&quot;http://www.eea.europa.eu/code/gis&quot; target=&quot;_blank&quot;>European Environment Agency</a>'
		);
		$this->_settings['wms_wms5_legend_enabled'] = array(
			'version' => '1.1',
			'pane'    => 'wms',
			'section' => 'wms-wms5',
			'title'   => __('Display legend?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes','lmm'),
				'no' => __('No','lmm')
			)
		);
		$this->_settings['wms_wms5_legend'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms5',
			'title'    => __('Legend','lmm'),
			'desc'    => __('URL of image which gets show when hovering the text "(Legend)" next to WMS attribution text','lmm'),
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/arcgis/services/Land/LandscapeFragmentation_LAEA/MapServer/WmsServer?request=GetLegendGraphic%26version=1.3.0%26format=image/png%26layer=0'
		);
		$this->_settings['wms_wms5_subdomains_enabled'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms5',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from base url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'no',
			'choices' => array(
				'no' => __('No','lmm'),
				'yes' => __('Yes (please enter subdomains in next form field)','lmm')
			)
		);
		$this->_settings['wms_wms5_subdomains_names'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms5',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;",
			'std'     => '&quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;',
			'type'    => 'text'
		);
		$this->_settings['wms_wms5_kml_helptext'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms5',
			'std'     => '',
			'title'   => '<h4 class="h4-lmm-settings">' . __('KML settings','lmm') . '</h4>',
			'desc'    => __('If the WMS server supports KML output of the WMS layer, the settings below will be used when a marker or layer map with this active WMS layer is exported as KML.','lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['wms_wms5_kml_support'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms5',

			'title'   => __('Does the WMS server support KML output?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'no',
			'choices' => array(
				'yes' => __('yes','lmm'),
				'no' => __('no','lmm')
			)
		);
		$this->_settings['wms_wms5_kml_href'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms5',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#href" target="_blank">href</a>',
			'desc'    => __('http-address of the KML-webservice of the WMS layer','lmm'),
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/rest/services/Noise/Noise_Dyna_LAEA/MapServer/generatekml?docName=&l%3A460=on&layers=460&layerOptions=nonComposite'
		);
		$this->_settings['wms_wms5_kml_refreshMode'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms5',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshmode" target="_blank">refreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'onChange',
			'choices' => array(
				'onChange' => __('onChange (refresh when the file is loaded and whenever the Link parameters change)','lmm'),
				'onInterval' => __('onInterval (refresh every n seconds (specified in refreshInterval)','lmm'),
				'onExpire' => __('onExpire (refresh the file when the expiration time is reached)','lmm'),
				'onStop' => __('onStop (after camera movement stops)','lmm')
			)
		);
		$this->_settings['wms_wms5_kml_refreshInterval'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms5',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshinterval" target="_blank">refreshInterval</a>',
			'desc'    => __('Indicates to refresh the file every n seconds','lmm'),
			'type'    => 'text',
			'std'     => '30'
		);
		$this->_settings['wms_wms5_kml_viewRefreshMode'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms5',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshmode" target="_blank">viewrefreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'never',
			'choices' => array(
				'never' => __('never (ignore changes in the view)','lmm'),
				'onStop' => __('onStop (refresh the file n seconds after movement stops, where n is specified in viewRefreshTime)','lmm'),
				'onRequest' => __('onRequest (refresh the file only when the user explicitly requests it)','lmm')
			)
		);
		$this->_settings['wms_wms5_kml_viewRefreshTime'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms5',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshtime" target="_blank">viewRefreshTime</a>',
			'desc'    => __('After camera movement stops, specifies the number of seconds to wait before refreshing the view (is used when viewRefreshMode is set to onStop)','lmm'),
			'type'    => 'text',
			'std'     => '1'
		);

		/*
		* WMS layer 6 settings
		*/
		$this->_settings['wms_wms6_helptext'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms6',
			'std'     => '',
			'title'   => '',
			'desc'    => '', //empty for not breaking settings layout
			'type'    => 'helptext'
		);
		$this->_settings['wms_wms6_name'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms6',
			'title'    => __('Name','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => '<a href=&quot;http://discomap.eea.europa.eu/ArcGIS/rest/services/Land/CLC2006_Dyna_WM/MapServer&quot; target=&quot;_blank&quot;>EEA - WaterBodies</a>'
		);
		$this->_settings['wms_wms6_baseurl'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms6',
			'title'    => __('baseURL','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Land/CLC2006_Dyna_WM/MapServer/WMSServer'
		);
		$this->_settings['wms_wms6_layers'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms6',
			'title'    => __('Layers','lmm'),
			'desc'    => __('(required) Comma-separated list of WMS layers to show','lmm'),
			'type'    => 'text',
			'std'     => '2'
		);
		$this->_settings['wms_wms6_styles'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms6',
			'title'    => __('Styles','lmm'),
			'desc'    => __('Comma-separated list of WMS styles','lmm'),
			'type'    => 'text',
			'std'     => ''
		);
		$this->_settings['wms_wms6_format'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms6',
			'title'    => __('Format','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'image/png'
		);
		$this->_settings['wms_wms6_transparent'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms6',
			'title'   => __('Transparent','lmm'),
			'desc'    => __('If yes, the WMS service will return images with transparency','lmm'),
			'type'    => 'radio',
			'std'     => 'TRUE',
			'choices' => array(
				'TRUE' => __('true','lmm'),
				'FALSE' => __('false','lmm')
			)
		);
		$this->_settings['wms_wms6_version'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms6',
			'title'    => __('Version','lmm'),
			'desc'    => __('Version of the WMS service to use','lmm'),
			'type'    => 'text',
			'std'     => '1.3.0'
		);
		$this->_settings['wms_wms6_attribution'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms6',
			'title'    => __('Attribution','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'WMS: <a href=&quot;http://www.eea.europa.eu/code/gis&quot; target=&quot;_blank&quot;>European Environment Agency</a>'
		);
		$this->_settings['wms_wms6_legend_enabled'] = array(
			'version' => '1.1',
			'pane'    => 'wms',
			'section' => 'wms-wms6',
			'title'   => __('Display legend?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes','lmm'),
				'no' => __('No','lmm')
			)
		);
		$this->_settings['wms_wms6_legend'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms6',
			'title'    => __('Legend','lmm'),
			'desc'    => __('URL of image which gets show when hovering the text "(Legend)" next to WMS attribution text','lmm'),
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Land/CLC2006_Dyna_WM/MapServer/WMSServer?request=GetLegendGraphic%26version=1.3.0%26format=image/png%26layer=2'
		);
		$this->_settings['wms_wms6_subdomains_enabled'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms6',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from base url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'no',
			'choices' => array(
				'no' => __('No','lmm'),
				'yes' => __('Yes (please enter subdomains in next form field)','lmm')
			)
		);
		$this->_settings['wms_wms6_subdomains_names'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms6',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;",
			'std'     => '&quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;',
			'type'    => 'text'
		);
		$this->_settings['wms_wms6_kml_helptext'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms6',
			'std'     => '',
			'title'   => '<h4 class="h4-lmm-settings">' . __('KML settings','lmm') . '</h4>',
			'desc'    => __('If the WMS server supports KML output of the WMS layer, the settings below will be used when a marker or layer map with this active WMS layer is exported as KML.','lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['wms_wms6_kml_support'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms6',
			'title'   => __('Does the WMS server support KML output?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('yes','lmm'),
				'no' => __('no','lmm')
			)
		);
		$this->_settings['wms_wms6_kml_href'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms6',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#href" target="_blank">href</a>',
			'desc'    => __('http-address of the KML-webservice of the WMS layer','lmm'),
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/rest/services/Land/CLC2006_Dyna_WM/MapServer/generatekml?docName=&l%3A14=on&layers=14&layerOptions=nonComposite'
		);
		$this->_settings['wms_wms6_kml_refreshMode'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms6',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshmode" target="_blank">refreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'onChange',
			'choices' => array(
				'onChange' => __('onChange (refresh when the file is loaded and whenever the Link parameters change)','lmm'),
				'onInterval' => __('onInterval (refresh every n seconds (specified in refreshInterval)','lmm'),
				'onExpire' => __('onExpire (refresh the file when the expiration time is reached)','lmm'),
				'onStop' => __('onStop (after camera movement stops)','lmm')
			)
		);
		$this->_settings['wms_wms6_kml_refreshInterval'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms6',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshinterval" target="_blank">refreshInterval</a>',
			'desc'    => __('Indicates to refresh the file every n seconds','lmm'),
			'type'    => 'text',
			'std'     => '30'
		);
		$this->_settings['wms_wms6_kml_viewRefreshMode'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms6',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshmode" target="_blank">viewrefreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'never',
			'choices' => array(
				'never' => __('never (ignore changes in the view)','lmm'),
				'onStop' => __('onStop (refresh the file n seconds after movement stops, where n is specified in viewRefreshTime)','lmm'),
				'onRequest' => __('onRequest (refresh the file only when the user explicitly requests it)','lmm')
			)
		);
		$this->_settings['wms_wms6_kml_viewRefreshTime'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms6',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshtime" target="_blank">viewRefreshTime</a>',
			'desc'    => __('After camera movement stops, specifies the number of seconds to wait before refreshing the view (is used when viewRefreshMode is set to onStop)','lmm'),
			'type'    => 'text',
			'std'     => '1'
		);

		/*
		* WMS layer 7 settings
		*/
		$this->_settings['wms_wms7_helptext'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms7',
			'std'     => '',
			'title'   => '',
			'desc'    => '', //empty for not breaking settings layout
			'type'    => 'helptext'
		);
		$this->_settings['wms_wms7_name'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms7',
			'title'    => __('Name','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => '<a href=&quot;http://discomap.eea.europa.eu/ArcGIS/rest/services/Water/RiverAndLakes_Dyna_WM/MapServer&quot; target=&quot;_blank&quot;>EEA - Mean annual nitrates in rivers 2008</a>'
		);
		$this->_settings['wms_wms7_baseurl'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms7',
			'title'    => __('baseURL','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Water/RiverAndLakes_Dyna_WM/MapServer/WMSServer'
		);
		$this->_settings['wms_wms7_layers'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms7',
			'title'    => __('Layers','lmm'),
			'desc'    => __('(required) Comma-separated list of WMS layers to show','lmm'),
			'type'    => 'text',
			'std'     => '14'
		);
		$this->_settings['wms_wms7_styles'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms7',
			'title'    => __('Styles','lmm'),
			'desc'    => __('Comma-separated list of WMS styles','lmm'),
			'type'    => 'text',
			'std'     => ''
		);
		$this->_settings['wms_wms7_format'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms7',
			'title'    => __('Format','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'image/png'
		);
		$this->_settings['wms_wms7_transparent'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms7',
			'title'   => __('Transparent','lmm'),
			'desc'    => __('If yes, the WMS service will return images with transparency','lmm'),
			'type'    => 'radio',
			'std'     => 'TRUE',
			'choices' => array(
				'TRUE' => __('true','lmm'),
				'FALSE' => __('false','lmm')
			)
		);
		$this->_settings['wms_wms7_version'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms7',
			'title'    => __('Version','lmm'),
			'desc'    => __('Version of the WMS service to use','lmm'),
			'type'    => 'text',
			'std'     => '1.3.0'
		);
		$this->_settings['wms_wms7_attribution'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms7',
			'title'    => __('Attribution','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'WMS: <a href=&quot;http://www.eea.europa.eu/code/gis&quot; target=&quot;_blank&quot;>European Environment Agency</a>'
		);
		$this->_settings['wms_wms7_legend_enabled'] = array(
			'version' => '1.1',
			'pane'    => 'wms',
			'section' => 'wms-wms7',
			'title'   => __('Display legend?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes','lmm'),
				'no' => __('No','lmm')
			)
		);
		$this->_settings['wms_wms7_legend'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms7',
			'title'    => __('Legend','lmm'),
			'desc'    => __('URL of image which gets show when hovering the text "(Legend)" next to WMS attribution text','lmm'),
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Water/RiverAndLakes_Dyna_WM/MapServer/WMSServer?request=GetLegendGraphic%26version=1.3.0%26format=image/png%26layer=14'
		);
		$this->_settings['wms_wms7_subdomains_enabled'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms7',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from base url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'no',
			'choices' => array(
				'no' => __('No','lmm'),
				'yes' => __('Yes (please enter subdomains in next form field)','lmm')
			)
		);
		$this->_settings['wms_wms7_subdomains_names'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms7',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;",
			'std'     => '&quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;',
			'type'    => 'text'
		);
		$this->_settings['wms_wms7_kml_helptext'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms7',
			'std'     => '',
			'title'   => '<h4 class="h4-lmm-settings">' . __('KML settings','lmm') . '</h4>',
			'desc'    => __('If the WMS server supports KML output of the WMS layer, the settings below will be used when a marker or layer map with this active WMS layer is exported as KML.','lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['wms_wms7_kml_support'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms7',
			'title'   => __('Does the WMS server support KML output?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('yes','lmm'),
				'no' => __('no','lmm')
			)
		);
		$this->_settings['wms_wms7_kml_href'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms7',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#href" target="_blank">href</a>',
			'desc'    => __('http-address of the KML-webservice of the WMS layer','lmm'),
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/rest/services/Water/RiverAndLakes_Dyna_WM/MapServer/generatekml?docName=&l%3A9=on&layers=9&layerOptions=nonComposite'
		);
		$this->_settings['wms_wms7_kml_refreshMode'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms7',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshmode" target="_blank">refreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'onChange',
			'choices' => array(
				'onChange' => __('onChange (refresh when the file is loaded and whenever the Link parameters change)','lmm'),
				'onInterval' => __('onInterval (refresh every n seconds (specified in refreshInterval)','lmm'),
				'onExpire' => __('onExpire (refresh the file when the expiration time is reached)','lmm'),
				'onStop' => __('onStop (after camera movement stops)','lmm')
			)
		);
		$this->_settings['wms_wms7_kml_refreshInterval'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms7',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshinterval" target="_blank">refreshInterval</a>',
			'desc'    => __('Indicates to refresh the file every n seconds','lmm'),
			'type'    => 'text',
			'std'     => '30'
		);
		$this->_settings['wms_wms7_kml_viewRefreshMode'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms7',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshmode" target="_blank">viewrefreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'never',
			'choices' => array(
				'never' => __('never (ignore changes in the view)','lmm'),
				'onStop' => __('onStop (refresh the file n seconds after movement stops, where n is specified in viewRefreshTime)','lmm'),
				'onRequest' => __('onRequest (refresh the file only when the user explicitly requests it)','lmm')
			)
		);
		$this->_settings['wms_wms7_kml_viewRefreshTime'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms7',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshtime" target="_blank">viewRefreshTime</a>',
			'desc'    => __('After camera movement stops, specifies the number of seconds to wait before refreshing the view (is used when viewRefreshMode is set to onStop)','lmm'),
			'type'    => 'text',
			'std'     => '1'
		);

		/*
		* WMS layer 8 settings
		*/
		$this->_settings['wms_wms8_helptext'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms8',
			'std'     => '',
			'title'   => '',
			'desc'    => '', //empty for not breaking settings layout
			'type'    => 'helptext'
		);
		$this->_settings['wms_wms8_name'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms8',
			'title'    => __('Name','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => '<a href=&quot;http://discomap.eea.europa.eu/ArcGIS/rest/services/Reports2010/Reports2008_Dyna_WGS84/MapServer&quot; target=&quot;_blank&quot;>EEA - Temperature Change</a>'
		);
		$this->_settings['wms_wms8_baseurl'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms8',
			'title'    => __('baseURL','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Reports2010/Reports2008_Dyna_WGS84/MapServer/WMSServer'
		);
		$this->_settings['wms_wms8_layers'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms8',
			'title'    => __('Layers','lmm'),
			'desc'    => __('(required) Comma-separated list of WMS layers to show','lmm'),
			'type'    => 'text',
			'std'     => '5'
		);
		$this->_settings['wms_wms8_styles'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms8',
			'title'    => __('Styles','lmm'),
			'desc'    => __('Comma-separated list of WMS styles','lmm'),
			'type'    => 'text',
			'std'     => ''
		);
		$this->_settings['wms_wms8_format'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms8',
			'title'    => __('Format','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'image/png'
		);
		$this->_settings['wms_wms8_transparent'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms8',
			'title'   => __('Transparent','lmm'),
			'desc'    => __('If yes, the WMS service will return images with transparency','lmm'),
			'type'    => 'radio',
			'std'     => 'TRUE',
			'choices' => array(
				'TRUE' => __('true','lmm'),
				'FALSE' => __('false','lmm')
			)
		);
		$this->_settings['wms_wms8_version'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms8',
			'title'    => __('Version','lmm'),
			'desc'    => __('Version of the WMS service to use','lmm'),
			'type'    => 'text',
			'std'     => '1.3.0'
		);
		$this->_settings['wms_wms8_attribution'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms8',
			'title'    => __('Attribution','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'WMS: <a href=&quot;http://www.eea.europa.eu/code/gis&quot; target=&quot;_blank&quot;>European Environment Agency</a>'
		);
		$this->_settings['wms_wms8_legend_enabled'] = array(
			'version' => '1.1',
			'pane'    => 'wms',
			'section' => 'wms-wms8',
			'title'   => __('Display legend?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes','lmm'),
				'no' => __('No','lmm')
			)
		);
		$this->_settings['wms_wms8_legend'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms8',
			'title'    => __('Legend','lmm'),
			'desc'    => __('URL of image which gets show when hovering the text "(Legend)" next to WMS attribution text','lmm'),
			'type'    => 'text',
			'std'     => 'http://cow6/ArcGIS/services/Reports2010/Reports2008_Dyna_WGS84/MapServer/WMSServer?request=GetLegendGraphic%26version=1.3.0%26format=image/png%26layer=5'
		);
		$this->_settings['wms_wms8_subdomains_enabled'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms8',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from base url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'no',
			'choices' => array(
				'no' => __('No','lmm'),
				'yes' => __('Yes (please enter subdomains in next form field)','lmm')
			)
		);
		$this->_settings['wms_wms8_subdomains_names'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms8',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;",
			'std'     => '&quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;',
			'type'    => 'text'
		);
		$this->_settings['wms_wms8_kml_helptext'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms8',
			'std'     => '',
			'title'   => '<h4 class="h4-lmm-settings">' . __('KML settings','lmm') . '</h4>',
			'desc'    => __('If the WMS server supports KML output of the WMS layer, the settings below will be used when a marker or layer map with this active WMS layer is exported as KML.','lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['wms_wms8_kml_support'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms8',
			'title'   => __('Does the WMS server support KML output?','lmm'),
			'desc'    => '',
			'type'    => 'radio',

			'std'     => 'yes',
			'choices' => array(
				'yes' => __('yes','lmm'),
				'no' => __('no','lmm')
			)
		);
		$this->_settings['wms_wms8_kml_href'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms8',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#href" target="_blank">href</a>',
			'desc'    => __('http-address of the KML-webservice of the WMS layer','lmm'),
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/rest/services/Reports2010/Reports2008_Dyna_WGS84/MapServer/generatekml?docName=&l%3A26=on&layers=26&layerOptions=nonComposite'
		);
		$this->_settings['wms_wms8_kml_refreshMode'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms8',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshmode" target="_blank">refreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'onChange',
			'choices' => array(
				'onChange' => __('onChange (refresh when the file is loaded and whenever the Link parameters change)','lmm'),
				'onInterval' => __('onInterval (refresh every n seconds (specified in refreshInterval)','lmm'),
				'onExpire' => __('onExpire (refresh the file when the expiration time is reached)','lmm'),
				'onStop' => __('onStop (after camera movement stops)','lmm')
			)
		);
		$this->_settings['wms_wms8_kml_refreshInterval'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms8',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshinterval" target="_blank">refreshInterval</a>',
			'desc'    => __('Indicates to refresh the file every n seconds','lmm'),
			'type'    => 'text',
			'std'     => '30'
		);
		$this->_settings['wms_wms8_kml_viewRefreshMode'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms8',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshmode" target="_blank">viewrefreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'never',
			'choices' => array(
				'never' => __('never (ignore changes in the view)','lmm'),
				'onStop' => __('onStop (refresh the file n seconds after movement stops, where n is specified in viewRefreshTime)','lmm'),
				'onRequest' => __('onRequest (refresh the file only when the user explicitly requests it)','lmm')
			)
		);
		$this->_settings['wms_wms8_kml_viewRefreshTime'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms8',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshtime" target="_blank">viewRefreshTime</a>',
			'desc'    => __('After camera movement stops, specifies the number of seconds to wait before refreshing the view (is used when viewRefreshMode is set to onStop)','lmm'),
			'type'    => 'text',
			'std'     => '1'
		);

		/*
		* WMS layer 9 settings
		*/
		$this->_settings['wms_wms9_helptext'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms9',
			'std'     => '',
			'title'   => '',
			'desc'    => '', //empty for not breaking settings layout
			'type'    => 'helptext'
		);
		$this->_settings['wms_wms9_name'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms9',
			'title'    => __('Name','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => '<a href=&quot;http://discomap.eea.europa.eu/arcgis/rest/services/Land/UrbanAtlas_LAEA/MapServer&quot; target=&quot;_blank&quot;>EEA - Urban Outlines</a>'
		);
		$this->_settings['wms_wms9_baseurl'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms9',
			'title'    => __('baseURL','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/arcgis/services/Land/UrbanAtlas_LAEA/MapServer/WmsServer?'
		);
		$this->_settings['wms_wms9_layers'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms9',
			'title'    => __('Layers','lmm'),
			'desc'    => __('(required) Comma-separated list of WMS layers to show','lmm'),
			'type'    => 'text',
			'std'     => '4'
		);
		$this->_settings['wms_wms9_styles'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms9',
			'title'    => __('Styles','lmm'),
			'desc'    => __('Comma-separated list of WMS styles','lmm'),
			'type'    => 'text',
			'std'     => ''
		);
		$this->_settings['wms_wms9_format'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms9',
			'title'    => __('Format','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'image/png'
		);
		$this->_settings['wms_wms9_transparent'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms9',
			'title'   => __('Transparent','lmm'),
			'desc'    => __('If yes, the WMS service will return images with transparency','lmm'),
			'type'    => 'radio',
			'std'     => 'TRUE',
			'choices' => array(
				'TRUE' => __('true','lmm'),
				'FALSE' => __('false','lmm')
			)
		);
		$this->_settings['wms_wms9_version'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms9',
			'title'    => __('Version','lmm'),
			'desc'    => __('Version of the WMS service to use','lmm'),
			'type'    => 'text',
			'std'     => '1.3.0'
		);
		$this->_settings['wms_wms9_attribution'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms9',
			'title'    => __('Attribution','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'WMS: <a href=&quot;http://www.eea.europa.eu/code/gis&quot; target=&quot;_blank&quot;>European Environment Agency</a>'
		);
		$this->_settings['wms_wms9_legend_enabled'] = array(
			'version' => '1.1',
			'pane'    => 'wms',
			'section' => 'wms-wms9',
			'title'   => __('Display legend?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'no',
			'choices' => array(
				'yes' => __('Yes','lmm'),
				'no' => __('No','lmm')
			)
		);
		$this->_settings['wms_wms9_legend'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms9',
			'title'    => __('Legend','lmm'),
			'desc'    => __('URL of image which gets show when hovering the text "(Legend)" next to WMS attribution text','lmm'),
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/arcgis/services/Land/UrbanAtlas_LAEA/MapServer/WmsServer?request=GetLegendGraphic%26version=1.3.0%26format=image/png%26layer=4'
		);
		$this->_settings['wms_wms9_subdomains_enabled'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms9',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from base url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'no',
			'choices' => array(
				'no' => __('No','lmm'),
				'yes' => __('Yes (please enter subdomains in next form field)','lmm')
			)
		);
		$this->_settings['wms_wms9_subdomains_names'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms9',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;",
			'std'     => '&quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;',
			'type'    => 'text'
		);
		$this->_settings['wms_wms9_kml_helptext'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms9',
			'std'     => '',
			'title'   => '<h4 class="h4-lmm-settings">' . __('KML settings','lmm') . '</h4>',
			'desc'    => __('If the WMS server supports KML output of the WMS layer, the settings below will be used when a marker or layer map with this active WMS layer is exported as KML.','lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['wms_wms9_kml_support'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms9',
			'title'   => __('Does the WMS server support KML output?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'no',
			'choices' => array(
				'yes' => __('yes','lmm'),
				'no' => __('no','lmm')
			)
		);
		$this->_settings['wms_wms9_kml_href'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms9',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#href" target="_blank">href</a>',
			'desc'    => __('http-address of the KML-webservice of the WMS layer','lmm'),
			'type'    => 'text',
			'std'     => ''
		);
		$this->_settings['wms_wms9_kml_refreshMode'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms9',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshmode" target="_blank">refreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'onChange',
			'choices' => array(
				'onChange' => __('onChange (refresh when the file is loaded and whenever the Link parameters change)','lmm'),
				'onInterval' => __('onInterval (refresh every n seconds (specified in refreshInterval)','lmm'),
				'onExpire' => __('onExpire (refresh the file when the expiration time is reached)','lmm'),
				'onStop' => __('onStop (after camera movement stops)','lmm')
			)
		);
		$this->_settings['wms_wms9_kml_refreshInterval'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms9',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshinterval" target="_blank">refreshInterval</a>',
			'desc'    => __('Indicates to refresh the file every n seconds','lmm'),
			'type'    => 'text',
			'std'     => '30'
		);
		$this->_settings['wms_wms9_kml_viewRefreshMode'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms9',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshmode" target="_blank">viewrefreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'never',
			'choices' => array(
				'never' => __('never (ignore changes in the view)','lmm'),
				'onStop' => __('onStop (refresh the file n seconds after movement stops, where n is specified in viewRefreshTime)','lmm'),
				'onRequest' => __('onRequest (refresh the file only when the user explicitly requests it)','lmm')
			)
		);
		$this->_settings['wms_wms9_kml_viewRefreshTime'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms9',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshtime" target="_blank">viewRefreshTime</a>',
			'desc'    => __('After camera movement stops, specifies the number of seconds to wait before refreshing the view (is used when viewRefreshMode is set to onStop)','lmm'),
			'type'    => 'text',
			'std'     => '1'
		);

		/*
		* WMS layer 10 settings
		*/
		$this->_settings['wms_wms10_helptext'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms10',
			'std'     => '',
			'title'   => '',
			'desc'    => '', //empty for not breaking settings layout
			'type'    => 'helptext'
		);
		$this->_settings['wms_wms10_name'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms10',
			'title'    => __('Name','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => '<a href=&quot;http://discomap.eea.europa.eu/arcgis/rest/services/Bio/Article17_Dyna_WM/MapServer&quot; target=&quot;_blank&quot;>EEA - Bieogeographical Regions</a>'
		);
		$this->_settings['wms_wms10_baseurl'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms10',
			'title'    => __('baseURL','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/arcgis/services/Bio/Article17_Dyna_WM/MapServer/WmsServer?'
		);
		$this->_settings['wms_wms10_layers'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms10',
			'title'    => __('Layers','lmm'),
			'desc'    => __('(required) Comma-separated list of WMS layers to show','lmm'),
			'type'    => 'text',
			'std'     => '1'
		);
		$this->_settings['wms_wms10_styles'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms10',
			'title'    => __('Styles','lmm'),
			'desc'    => __('Comma-separated list of WMS styles','lmm'),
			'type'    => 'text',
			'std'     => ''
		);
		$this->_settings['wms_wms10_format'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms10',
			'title'    => __('Format','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'image/png'
		);
		$this->_settings['wms_wms10_transparent'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms10',
			'title'   => __('Transparent','lmm'),
			'desc'    => __('If yes, the WMS service will return images with transparency','lmm'),
			'type'    => 'radio',
			'std'     => 'TRUE',
			'choices' => array(
				'TRUE' => __('true','lmm'),
				'FALSE' => __('false','lmm')
			)
		);
		$this->_settings['wms_wms10_version'] = array(

			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms10',
			'title'    => __('Version','lmm'),
			'desc'    => __('Version of the WMS service to use','lmm'),
			'type'    => 'text',
			'std'     => '1.3.0'
		);
		$this->_settings['wms_wms10_attribution'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms10',
			'title'    => __('Attribution','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'WMS: <a href=&quot;http://www.eea.europa.eu/code/gis&quot; target=&quot;_blank&quot;>European Environment Agency</a>'
		);
		$this->_settings['wms_wms10_legend_enabled'] = array(
			'version' => '1.1',
			'pane'    => 'wms',
			'section' => 'wms-wms10',
			'title'   => __('Display legend?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes','lmm'),
				'no' => __('No','lmm')
			)
		);
		$this->_settings['wms_wms10_legend'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms10',
			'title'    => __('Legend','lmm'),
			'desc'    => __('URL of image which gets show when hovering the text "(Legend)" next to WMS attribution text','lmm'),
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/arcgis/services/Bio/Article17_Dyna_WM/MapServer/WmsServer?request=GetLegendGraphic%26version=1.3.0%26format=image/png%26layer=2'
		);
		$this->_settings['wms_wms10_subdomains_enabled'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms10',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from base url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'no',
			'choices' => array(
				'no' => __('No','lmm'),
				'yes' => __('Yes (please enter subdomains in next form field)','lmm')
			)
		);
		$this->_settings['wms_wms10_subdomains_names'] = array(
			'version' => '1.0',
			'pane'    => 'wms',
			'section' => 'wms-wms10',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;",
			'std'     => '&quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;',
			'type'    => 'text'
		);
		$this->_settings['wms_wms10_kml_helptext'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms10',
			'std'     => '',
			'title'   => '<h4 class="h4-lmm-settings">' . __('KML settings','lmm') . '</h4>',
			'desc'    => __('If the WMS server supports KML output of the WMS layer, the settings below will be used when a marker or layer map with this active WMS layer is exported as KML.','lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['wms_wms10_kml_support'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms10',
			'title'   => __('Does the WMS server support KML output?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'no',
			'choices' => array(
				'yes' => __('yes','lmm'),
				'no' => __('no','lmm')
			)
		);
		$this->_settings['wms_wms10_kml_href'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms10',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#href" target="_blank">href</a>',
			'desc'    => __('http-address of the KML-webservice of the WMS layer','lmm'),
			'type'    => 'text',
			'std'     => ''
		);
		$this->_settings['wms_wms10_kml_refreshMode'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms10',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshmode" target="_blank">refreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'onChange',
			'choices' => array(
				'onChange' => __('onChange (refresh when the file is loaded and whenever the Link parameters change)','lmm'),
				'onInterval' => __('onInterval (refresh every n seconds (specified in refreshInterval)','lmm'),
				'onExpire' => __('onExpire (refresh the file when the expiration time is reached)','lmm'),
				'onStop' => __('onStop (after camera movement stops)','lmm')
			)
		);
		$this->_settings['wms_wms10_kml_refreshInterval'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms10',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#refreshinterval" target="_blank">refreshInterval</a>',
			'desc'    => __('Indicates to refresh the file every n seconds','lmm'),
			'type'    => 'text',
			'std'     => '30'
		);
		$this->_settings['wms_wms10_kml_viewRefreshMode'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms10',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshmode" target="_blank">viewrefreshMode</a>',
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'never',
			'choices' => array(
				'never' => __('never (ignore changes in the view)','lmm'),
				'onStop' => __('onStop (refresh the file n seconds after movement stops, where n is specified in viewRefreshTime)','lmm'),
				'onRequest' => __('onRequest (refresh the file only when the user explicitly requests it)','lmm')
			)
		);
		$this->_settings['wms_wms10_kml_viewRefreshTime'] = array(
			'version' => '1.4.3',
			'pane'    => 'wms',
			'section' => 'wms-wms10',
			'title'   => '<a href="http://code.google.com/apis/kml/documentation/kmlreference.html#viewrefreshtime" target="_blank">viewRefreshTime</a>',
			'desc'    => __('After camera movement stops, specifies the number of seconds to wait before refreshing the view (is used when viewRefreshMode is set to onStop)','lmm'),
			'type'    => 'text',
			'std'     => '1'
		);

		/*===========================================
		*
		*
		* pane Directions
		*
		*
		===========================================*/
		/*
		* Directions General
		*/
		$this->_settings['directions_general_helptext1'] = array(
			'version' => '1.4',
			'pane'    => 'directions',
			'section' => 'directions-general_settings',
			'std'     => '',
			'title'   => '',
			'desc'    => __( 'Please select your prefered directions provider. This setting will be used for the directions link in the panel on top of marker maps and for the action panel which gets attached to the popup text on each marker if enabled.', 'lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['directions_provider'] = array(
			'version' => '1.4',
			'pane'    => 'directions',
			'section' => 'directions-general_settings',
			'title'   => __('Use the following directions provider','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'googlemaps',
			'choices' => array(
				'googlemaps' => __('Google Maps (worldwide)','lmm') . ' - <a href="https://maps.google.com/maps?saddr=Vienna&daddr=Linz&hl=de&sll=37.0625,-95.677068&sspn=59.986788,135.263672&geocode=FS6Z3wIdO9j5ACmfyjZRngdtRzFGW6JRiuXC_Q%3BFfwa4QIdBvzZAClNhZn6lZVzRzHEdXlXLClTfA&vpsrc=0&mra=ls&t=m&z=9&layer=t" style="text-decoration:none;" target="_blank">Demo</a>',
				'yours' => __('yournavigation.org (based on OpenStreetMap, worldwide)','lmm') . ' - <a href="http://www.yournavigation.org/?flat=52.215636&flon=6.963946&tlat=52.2573&tlon=6.1799&v=motorcar&fast=1&layer=mapnik" style="text-decoration:none;" target="_blank">Demo</a>',
				'ors' => __('openrouteservice.org (based on OpenStreetMap, Europe only)','lmm') . ' - <a href="http://openrouteservice.org/?pos=7.0892567,50.7265543&wp=7.0892567,50.7265543&zoom=15&routeWeigh=Fastest&routeOpt=Bicycle&layer=B000FTTTTTTTTTT" style="text-decoration:none;" target="_blank">Demo</a>',
				'bingmaps' => __('Bing Maps (worldwide)','lmm') . ' - <a href="http://www.bing.com/maps/default.aspx?v=2&rtp=pos.48.208614_16.370541___e_~pos.48.207321_16.330513" style="text-decoration:none;" target="_blank">Demo</a>'
			)
		);
		$this->_settings['directions_popuptext_panel'] = array(
			'version' => '1.4',
			'pane'    => 'directions',
			'section' => 'directions-general_settings',
			'title'   => __('Attach directions panel with address to popup text on each marker','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('yes','lmm'),
				'no' => __('no','lmm')			)
		);
		$this->_settings['directions_general_helptext2'] = array(
			'version' => '1.4',
			'pane'    => 'directions',
			'section' => 'directions-general_settings',
			'std'     => '',
			'title'   => '',
			'desc'    => '<img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-directions-popuptext-panel.jpg" width="450" height="218" />',
			'type'    => 'helptext'
		);

		/*
		* Directions Google Maps
		*/
		$this->_settings['directions_googlemaps_helptext1'] = array(
			'version' => '1.4',
			'pane'    => 'directions',
			'section' => 'directions-google_maps',
			'title'   => '',
			'desc'    => '',
			'type'    => 'helptext',
			'std'     => ''
		);
		$this->_settings['directions_googlemaps_map_type'] = array(
			'version' => '1.4',
			'pane'    => 'directions',
			'section' => 'directions-google_maps',
			'title'   => __('Map type','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'm',
			'choices' => array(
				'm' => __('Map','lmm'),
				'k' => __('Satellite','lmm'),
				'h' => __('Hybrid','lmm'),
				'p' => __('Terrain','lmm')
			)
		);
		$this->_settings['directions_googlemaps_traffic'] = array(
			'version' => '1.4',
			'pane'    => 'directions',
			'section' => 'directions-google_maps',
			'title'   => __('Show traffic layer?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '1',
			'choices' => array(
				'1' => __('yes','lmm'),
				'0' => __('no','lmm')
			)
		);
		$this->_settings['directions_googlemaps_distance_units'] = array(
			'version' => '1.4',
			'pane'    => 'directions',
			'section' => 'directions-google_maps',
			'title'   => __('Distance units','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'ptk',
			'choices' => array(
				'ptk' => __('metric (km)','lmm'),
				'ptm' => __('imperial (miles)','lmm')
			)
		);
		$this->_settings['directions_googlemaps_route_type_highways'] = array(
			'version' => '1.0',
			'pane'    => 'directions',
			'section' => 'directions-google_maps',
			'title'    => __('Route type','lmm'),
			'desc'    => __('Avoid highways','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['directions_googlemaps_route_type_tolls'] = array(
			'version' => '1.0',
			'pane'    => 'directions',
			'section' => 'directions-google_maps',
			'title'    => '',
			'desc'    => __('Avoid tolls','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['directions_googlemaps_route_type_public_transport'] = array(
			'version' => '1.0',
			'pane'    => 'directions',
			'section' => 'directions-google_maps',
			'title'    => '',
			'desc'    => __('Public transport (works only in some areas)','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['directions_googlemaps_route_type_walking'] = array(
			'version' => '1.0',
			'pane'    => 'directions',
			'section' => 'directions-google_maps',
			'title'    => '',
			'desc'    => __('Walking directions','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['directions_googlemaps_overview_map'] = array(
			'version' => '1.4',
			'pane'    => 'directions',
			'section' => 'directions-google_maps',
			'title'   => __('Overview map','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '0',
			'choices' => array(
				'0' => __('hidden','lmm'),
				'1' => __('visible','lmm')
			)
		);

		/*
		* yournavigation.org
		*/
		$this->_settings['directions_yours_helptext1'] = array(
			'version' => '1.4',
			'pane'    => 'directions',
			'section' => 'directions-yournavigation',
			'std'     => '',
			'title'   => '',
			'desc'    => '',
			'type'    => 'helptext'
		);
		$this->_settings['directions_yours_type_of_transport'] = array(
			'version' => '1.4',
			'pane'    => 'directions',
			'section' => 'directions-yournavigation',
			'title'   => __('Type of transport','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'motorcar',
			'choices' => array(
				'motorcar' => __('Motorcar','lmm'),
				'bicycle' => __('Bicycle','lmm'),
				'foot' => __('Foot','lmm')
			)
		);
		$this->_settings['directions_yours_route_type'] = array(
			'version' => '1.4',
			'pane'    => 'directions',
			'section' => 'directions-yournavigation',
			'title'   => __('Route type','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '1',
			'choices' => array(
				'0' => __('fastest route','lmm'),
				'1' => __('shortest route','lmm')
			)
		);
		$this->_settings['directions_yours_layer'] = array(
			'version' => '1.4',
			'pane'    => 'directions',
			'section' => 'directions-yournavigation',
			'title'   => __('Gosmore instance to calculate the route','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'mapnik',
			'choices' => array(
				'mapnik' => __('mapnik (for normal routing using car, bicycle or foot)','lmm'),
				'cn' => __('cn (for using bicycle routing using cycle route networks only)','lmm')
			)
		);

		/*
		* openrouteservice.org
		*/
		$this->_settings['directions_ors_helptext1'] = array(
			'version' => '1.4',
			'pane'    => 'directions',
			'section' => 'directions-openrouteservice',
			'std'     => '',
			'title'   => '',
			'desc'    => '',
			'type'    => 'helptext'
		);
		$this->_settings['directions_ors_routeWeigh'] = array(
			'version' => '3.10.1',
			'pane'    => 'directions',
			'section' => 'directions-openrouteservice',
			'title'   => 'routeWeigh',
			'desc'    => __('weighting method of routing','lmm'),
			'type'    => 'radio',
			'std'     => 'Fastest',
			'choices' => array(
				'Fastest' => __('Fastest','lmm'),
				'Shortest' => __('Shortest','lmm'),
				'Recommended' => __('Recommended','lmm')
			)
		);
		$this->_settings['directions_ors_routeOpt'] = array(
			'version' => '3.10.1',
			'pane'    => 'directions',
			'section' => 'directions-openrouteservice',
			'title'   => 'routeOpt',
			'desc'    => __('preferred route profile','lmm'),
			'type'    => 'radio',
			'std'     => 'Car',
			'choices' => array(
				'Car' => __('Car','lmm'),
				'Bicycle' => __('Bicycle','lmm'),
				'Pedestrian' => __('Pedestrian','lmm'),
				'HeavyVehicle' => __('HeavyVehicle','lmm')
			)
		);
		$this->_settings['directions_ors_layer'] = array(
			'version' => '3.11', //was 3.10.1
			'pane'    => 'directions',
			'section' => 'directions-openrouteservice',
			'title'   => __('default background layer','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'B000',
			'choices' => array(
				'B000' => 'OpenMapsurfer',
				'0B000' => 'OSM-WMS worldwide',
				'00B00' => 'OSM Mapnik',
				'0000B' => 'Stamen Map',
				'000B0' => 'OpenCycleMap'
			)
		);

		/*===========================================
		*
		*
		* pane Augmented-Reality
		*
		*
		===========================================*/
		/*
		* AR General
		*/
		$this->_settings['ar_general_helptext1'] = array(
			'version' => '1.0',
			'pane'    => 'ar',
			'section' => 'ar-wikitude',
			'std'     => '',
			'title'   => '',
			'desc'    => sprintf(__( 'Markers can also be displayed via <a href="%1$s" target="_blank">Augmented-Reality technology</a> powered by <a href="%2$s" target="_blank">Wikitude</a> on mobile devices. Sample screenshots:', 'lmm'), 'https://en.wikipedia.org/wiki/Augmented_reality', 'http://www.wikitude.com') . '<br/><br/><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-wikitude.jpg" width="500" height="699" />',
			'type'    => 'helptext'
		);

		/*
		* AR Wikitude
		*/
		$this->_settings['ar_wikitude_helptext'] = array(
			'version' => '1.0',
			'pane'    => 'ar',
			'section' => 'ar-wikitude',
			'std'     => '',
			'title'   => '',
			'desc'    => __( 'Please visit <a href="http://www.mapsmarker.com/wikitude" target="_blank">http://www.mapsmarker.com/wikitude</a> for instructions how to submit your marker or layer maps to Wikitude.', 'lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['ar_wikitude_provider_name'] = array(
			'version' => '1.0',
			'pane'    => 'ar',
			'section' => 'ar-wikitude',
			'title'   => __( 'Provider name', 'lmm' ),
			'desc'    => '<strong>' . __( 'Identifies the content provider or content channel, no spaces/special characters', 'lmm' ) . '</strong>',
			'std'     => 'www_mapsmarker_com',
			'type'    => 'text'
		);
		$this->_settings['ar_wikitude_shortname'] = array(
			'version' => '3.3',
			'pane'    => 'ar',
			'section' => 'ar-wikitude',
			'title'   => __( 'Short name', 'lmm' ),
			'desc'    => __( 'A short name for your World, should only be up to 20 characters, to be used when there is not enough space.', 'lmm' ),
			'std'     => 'www.mapsmarker.com',
			'type'    => 'text-deletable'
		);
		$this->_settings['ar_wikitude_description'] = array(
			'version' => 'p1.0',
			'pane'    => 'ar',
			'section' => 'ar-wikitude',
			'title'   => __( 'Description', 'lmm' ) . $pro_button_link,
			'desc'    => __( 'Description of the content provider that provides additional information about the content displayed.', 'lmm' ) . '<a style="background:#f99755;display:block;padding:3px;text-decoration:none;color:#2702c6;width:635px;margin:10px 0;" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade">' . __('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm') . '</a>',
			'std'     => __('Wikitude API powered by www.mapsmarker.com','lmm'),
			'type'    => 'text-pro'
		);
		$this->_settings['ar_wikitude_promotiontext'] = array(
			'version' => '3.3',
			'pane'    => 'ar',
			'section' => 'ar-wikitude',
			'title'   => __( 'Promotion text', 'lmm' ),
			'desc'    => __( 'A promotion text describing your World in more details.', 'lmm' ),
			'std'     => 'Wikitude API powered by www.mapsmarker.com',
			'type'    => 'text-deletable'
		);
		$this->_settings['ar_wikitude_promotiongraphic'] = array(
			'version' => '3.3',
			'pane'    => 'ar',
			'section' => 'ar-wikitude',
			'title'   => __( 'Promotion graphic', 'lmm' ),
			'desc'    => __( 'A graphic advertising your World. Format: 180x120 pixel, transparent PNG', 'lmm' ),
			'std'     => LEAFLET_PLUGIN_URL . 'inc/img/wikitude-promotiongraphic-180x200.png',
			'type'    => 'text-deletable'
		);
		$this->_settings['ar_wikitude_optout'] = array(
			'version' => '3.3',
			'pane'    => 'ar',
			'section' => 'ar-wikitude',
			'title'   => __( 'No promotion', 'lmm' ),
			'desc'    => __( 'Opt-out from being promoted by Wikitude.', 'lmm' ),
			'type'    => 'radio',
			'std'     => 'false',
			'choices' => array(
				'false' => __('false', 'lmm'),
				'true' => __('true', 'lmm')
			)
		);
		$this->_settings['ar_wikitude_featuregraphic'] = array(
			'version' => '3.4',
			'pane'    => 'ar',
			'section' => 'ar-wikitude',
			'title'   => __( 'Feature graphic', 'lmm' ),
			'desc'    => __( 'A graphic spotlighting your World in promotions. Format: 1024x500 pixel, transparent PNG', 'lmm' ),
			'std'     => LEAFLET_PLUGIN_URL . 'inc/img/wikitude-featuregraphic-1024x500.png',
			'type'    => 'text-deletable'
		);
		$this->_settings['ar_wikitude_provider_url'] = array(
			'version' => '1.0',
			'pane'    => 'ar',
			'section' => 'ar-wikitude',
			'title'   => __( 'Provider URL', 'lmm' ),
			'desc'    => __( 'Link to content provider', 'lmm' ),
			'std'     => 'http://www.mapsmarker.com',
			'type'    => 'text-deletable'
		);
		$this->_settings['ar_wikitude_logo'] = array(
			'version' => '3.3',
			'pane'    => 'ar',
			'section' => 'ar-wikitude',
			'title'   => __( 'Logo', 'lmm' ),
			'desc'    => __( 'The logo is displayed on the left bottom corner on Wikitude when an icon is selected - 96x96 pixel, transparent PNG', 'lmm' ),
			'std'     => LEAFLET_PLUGIN_URL . 'inc/img/wikitude-logo-96x96.png',
			'type'    => 'text-deletable'
		);
		$this->_settings['ar_wikitude_icon'] = array(
			'version' => '3.3',
			'pane'    => 'ar',
			'section' => 'ar-wikitude',
			'title'   => __( 'Icon', 'lmm' ),
			'desc'    => __( 'The icon is displayed in the cam view of Wikitude to indicate a marker - 32x32 pixel, transparent PNG', 'lmm' ),
			'std'     => LEAFLET_PLUGIN_URL . 'inc/img/wikitude-icon-32x32.png',
			'type'    => 'text-deletable'
		);
		$this->_settings['ar_wikitude_hiresicon'] = array(
			'version' => '3.3',
			'pane'    => 'ar',
			'section' => 'ar-wikitude',
			'title'   => __( 'High Resolution Icon', 'lmm' ),
			'desc'    => __( 'A high resolution icon for your World which can be included in features. Format: 512x512 pixel, transparent PNG', 'lmm' ),
			'std'     => '',
			'type'    => 'text-deletable'
		);
		$this->_settings['ar_wikitude_tags'] = array(
			'version' => '3.3',
			'pane'    => 'ar',
			'section' => 'ar-wikitude',
			'title'   => __( 'Tags', 'lmm' ),
			'desc'    => __( 'Comma separated list of keywords that characterize the content provider. When users search for content in Wikitude the tags will be searched as well. A match in the tags is higher ranked than in the description.', 'lmm' ),
			'std'     => 'mapsmarker',
			'type'    => 'text-deletable'
		);
		$this->_settings['ar_wikitude_email'] = array(
			'version' => '1.0',
			'pane'    => 'ar',
			'section' => 'ar-wikitude',
			'title'   => __( 'E-Mail', 'lmm' ),
			'desc'    => __( 'Optional: displayed on each marker; used for sending an email directly from Wikitude', 'lmm' ),
			'std'     => '',
			'type'    => 'text-deletable'
		);
		$this->_settings['ar_wikitude_phone'] = array(
			'version' => '1.0',
			'pane'    => 'ar',
			'section' => 'ar-wikitude',
			'title'   => __( 'Phone', 'lmm' ),
			'desc'    => __( 'Optional: example: +4312345 - when a phone number is given, Wikitude displays a "call me" button in the bubble; used for every marker.', 'lmm' ),
			'std'     => '',
			'type'    => 'text-deletable'
		);
		$this->_settings['ar_wikitude_attachment'] = array(
			'version' => '1.0',
			'pane'    => 'ar',
			'section' => 'ar-wikitude',
			'title'   => __( 'Attachment', 'lmm' ),
			'desc'    => __( 'Optional: displayed on each marker; can be a link to a resource (image, PDF file...). You could use this to issue coupons or vouchers for potential clients that found you via Wikitude.', 'lmm' ),
			'std'     => '',
			'type'    => 'text-deletable'
		);
		$this->_settings['ar_wikitude_radius'] = array(
			'version' => '1.0',
			'pane'    => 'ar',
			'section' => 'ar-wikitude',
			'title'   => __( 'Search radius (in meter)', 'lmm' ),
			'desc'    => __( 'Retrieve POIs (Points of Interests) from database within this search radius in meters from the current location of the Wikitude user', 'lmm' ),
			'std'     => '100000',
			'type'    => 'text'
		);
		$this->_settings['ar_wikitude_maxnumberpois'] = array(
			'version' => '1.0',
			'pane'    => 'ar',
			'section' => 'ar-wikitude',
			'title'   => __( 'Maximum number of POIs', 'lmm' ),
			'desc'    => __( 'Used if Wikitude does not pass the variable maxNumberofPois - 50 is the maximum recommended', 'lmm' ),
			'std'     => '50',
			'type'    => 'text'
		);

		/*===========================================
		*
		*
		* pane miscellaneous
		*
		*
		===========================================*/
		/*
		 * General settings
		*/
		$this->_settings['misc_general_helptext'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'misc-general_settings',
			'std'     => '',
			'title'   => '',
			'desc'    => '', //empty for not breaking settings layout
			'type'    => 'helptext'
		);
		$this->_settings['affiliate_id'] = array(
			'version' => '3.6.4',
			'pane'    => 'misc',
			'section' => 'misc-general_settings',
			'title'   => __( 'Affiliate ID', 'lmm' ),
			'desc'    => __( 'Enter your affiliate ID to replace the default MapsMarker.com-backlink on all maps with your personal affiliate link - enabling you to receive commissions up to 50% from sales of the pro version.', 'lmm' ) . '<br/><a href="https://www.mapsmarker.com/affiliateid" target="_blank">' . __('Click here for more infos on the Maps Marker affiliate program and how to get your affiliate ID','lmm') . '</a>',
			'std'     => '',
			'type'    => 'text'
		);
		$this->_settings['misc_betatest'] = array(
			'version' => 'p1.1',
			'pane'    => 'misc',
			'section' => 'misc-general_settings',
			'title'   => __('Beta testing','lmm'). $pro_button_link,
			'desc'    => __('Set to enabled if you want to easily upgrade to beta releases.','lmm') . ' <span style="font-weight:bold;color:red;">' . __('Warning: not recommended on production sites - use on your own risk!','lmm') . '</span>',
			'type'    => 'radio-pro',
			'std'     => 'disabled',
			'choices' => array(
				'disabled' => __('disabled','lmm'),
				'enabled' => __('enabled','lmm')
			)
		);
		$this->_settings['misc_general_helptext2'] = array(
			'version' => '3.11',
			'pane'    => 'misc',
			'section' => 'misc-general_settings',
			'std'     => '',
			'title'   => '',
			'desc'    => '<h4 class="h4-lmm-settings">' . __('Whitelabel settings','lmm') . '</h4>',
			'type'    => 'helptext'
		);
		$this->_settings['misc_whitelabel_backend'] = array(
			'version' => 'p1.2',
			'pane'    => 'misc',
			'section' => 'misc-general_settings',
			'title'   => __('Whitelabel backend','lmm'). $pro_button_link,
			'desc'    => __('Set to enabled if you want to remove all backlinks and logos on backend as well as making the pages and menu entries for Tools, Settings, Support, License visible to admins only (user capability activate_plugins).','lmm'),
			'type'    => 'radio-pro',
			'std'     => 'disabled',
			'choices' => array(
				'disabled' => __('disabled','lmm'),
				'enabled' => __('enabled','lmm')
			)
		);
		$this->_settings['misc_backlink'] = array(
			'version' => 'p1.0',
			'pane'    => 'misc',
			'section' => 'misc-general_settings',
			'title'   => __('MapsMarker.com backlinks','lmm') . $pro_button_link,
			'desc'    => __('Option to hide backlinks to Mapsmarker.com on maps and screen overlays in KML files.','lmm') . '<a style="background:#f99755;display:block;padding:3px;text-decoration:none;color:#2702c6;width:635px;margin:10px 0;" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_pro_upgrade">' . __('This feature is available in the pro version only! Click here to find out how you can start a free 30-day-trial easily','lmm') . '</a><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-backlink.jpg" width="642" height="45" /><br/><img src="'. LEAFLET_PLUGIN_URL .'inc/img/help-backlink-kml.jpg" width="471" height="71" />',
			'type'    => 'radio-pro',
			'std'     => 'show',
			'choices' => array(
				'show' => __('show','lmm'),
				'hide' => __('hide','lmm')
			)
		);

		/*
		* Interface language settings
		*/
		$this->_settings['misc_language_helptext'] = array(
			'version' => '2.4',
			'pane'    => 'misc',
			'section' => 'misc-interface_language',
			'std'     => '',
			'title'   => '',
			'desc'    => __('The interface language to use on backend and/or on maps on frontend. Please note that the language for Google Maps and Bing maps can be set separately via the according basemap settings section.','lmm') . '<br/><br/>' . sprintf(__('If your language is missing or not fully translated yet, you are invited to help on the <a href="%1s" target="_blank">web-based translation plattform</a>.','lmm'), 'https://translate.mapsmarker.com/projects/lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['misc_plugin_language'] = array(
			'version' => '2.4',
			'pane'    => 'misc',
			'section' => 'misc-interface_language',
			'title'   => __('Default language','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'automatic',
			'choices' => array(
				'automatic' => __('automatic (use WordPress default)','lmm'),
				'ar' => __('Arabic','lmm') . ' (ar)',
				'af' => __('Afrikaans','lmm') . ' (af)',
				'bn_BD' => __('Bengali','lmm') . ' (bn_BD)',
				'bs_BA' => __('Bosnian','lmm') . ' (bs_BA)',
				'bg_BG' => __('Bulgarian','lmm') . ' (bg_BG)',
				'ca' => __('Catalan','lmm') . ' (ca)',
				'zh_CN' => __('Chinese','lmm') . ' (zh_CN)',
				'zh_TW' => __('Chinese','lmm') . ' (zh_TW)',
				'hr' => __('Croatian','lmm') . ' (hr)',
				'cs_CZ' => __('Czech','lmm') . ' (cs_CZ)',
				'da_DK' => __('Danish','lmm') . ' (da_DK)',
				'nl_NL' => __('Dutch','lmm') . ' (nl_NL)',
				'en_US' => __('English','lmm') . ' (en_US)',
				'fi_FI' => __('Finnish','lmm') . ' (fi_FI)',
				'fr_FR' => __('French','lmm') . ' (fr_FR)',
				'gl_ES' => __('Galician','lmm') . ' (gl_ES)',
				'de_DE' => __('German','lmm') . ' (de_DE)',
				'el' => __('Greek','lmm') . ' (el)',
				'he_IL' => __('Hebrew','lmm') . ' (he_IL)',
				'hi_IN' => __('Hindi','lmm') . ' (hi_IN)',
				'hu_HU' => __('Hungarian','lmm') . ' (hu_HU)',
				'id_ID' => __('Indonesian','lmm') . ' (id_ID)',
				'it_IT' => __('Italian','lmm') . ' (it_IT)',
				'ja' => __('Japanese','lmm') . ' (ja)',
				'ko_KR' => __('Korean','lmm') . ' (ko_KR)',
				'lv' => __('Latvian','lmm') . ' (lv)',
				'lt_LT' => __('Lithuanian','lmm') . ' (lt_LT)',
				'ms_MY' => __('Malawy','lmm') . ' (ms_MY)',
				'nb_NO' => __('Norwegian (Bokmål)','lmm') . ' (nb_NO)',
				'pl_PL' => __('Polish','lmm') . ' (pl_PL)',
				'pt_BR' => __('Portuguese','lmm') . ' - ' . __('Brazil','lmm') . ' (pt_BR)',
				'pt_PT' => __('Portuguese','lmm') . ' - ' . __('Portugal','lmm') . ' (pt_PT)',
				'ro_RO' => __('Romanian','lmm') . ' (ro_RO)',
				'ru_RU' => __('Russian','lmm') . ' (ru_RU)',
				'sk_SK' => __('Slovak','lmm') . ' (sk_SK)',
				'sl_SL' => __('Slovenian','lmm') . ' (sl_SL)',
				'sv_SE' => __('Swedish','lmm') . ' (sv_SE)',
				'es_ES' => __('Spanish','lmm') . ' (es_ES)',
				'es_MX' => __('Spanish','lmm') . ' (es_MX)',
				'th' => __('Thai','lmm') . ' (th)',
				'tr_TR' => __('Turkish','lmm') . ' (tr_TR)',
				'ug' => __('Uighur','lmm') . ' (ug)',
				'uk_UK' => __('Ukrainian','lmm') . ' (uk_UK)',
				'vi' => __('Vietnamese','lmm') . ' (vi)',
				'yi' => __('Yiddish','lmm') . ' (yi)'
			)
		);
		$this->_settings['misc_plugin_language_area'] = array(
			'version' => '2.4',
			'pane'    => 'misc',
			'section' => 'misc-interface_language',
			'title'   => __('Where to change the default language','lmm'),
			'desc'    => __('This setting will only be used when the plugin language is not selected automatically','lmm'),
			'type'    => 'radio',
			'std'     => 'backend',
			'choices' => array(
				'backend' => __('WordPress admin area only','lmm'),
				'frontend' => __('WordPress frontend only','lmm'),
				'both' => __('WordPress admin area and frontend','lmm')
			)
		);

		/*
		* "List all markers" page settings
		*/
		$this->_settings['misc_marker_listing_columns_helptext'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_markers',
			'std'     => '',
			'title'   => '',
			'desc'    => '',
			'type'    => 'helptext'
		);
		$this->_settings['markers_per_page'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_markers',
			'title'   => __( 'Markers per page', 'lmm' ),
			'desc'    => __( 'The number of markers per page that should be listed in tables on the "list all markers"- and "layer edit"-pages on backend', 'lmm' ),
			'std'     => '30',
			'type'    => 'text'
		);
		$this->_settings['misc_marker_listing_columns_helptext2'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_markers',
			'std'     => '',
			'title'   => '',
			'desc'    => '<h4 class="h4-lmm-settings">' . __('Available columns','lmm') . '</h4><br/>' . __( 'Please select the columns which should be available on the page "List all markers"', 'lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['misc_marker_listing_columns_id'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_markers',
			'title'    => __('Columns to show','lmm'),
			'desc'    => 'ID',
			'type'    => 'checkbox-readonly',
			'std'     => 1
		);
		$this->_settings['misc_marker_listing_columns_icon'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_markers',
			'title'    => '',
			'desc'    => __('Icon','lmm'),
			'type'    => 'checkbox-readonly',
			'std'     => 1
		);
		$this->_settings['misc_marker_listing_columns_markername'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_markers',
			'title'    => '',
			'desc'    => __('Marker name','lmm'),
			'type'    => 'checkbox-readonly',
			'std'     => 1
		);
		$this->_settings['misc_marker_listing_columns_address'] = array(
			'version' => '3.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_markers',
			'title'    => '',
			'desc'    => __('Address','lmm'),
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['misc_marker_listing_columns_popuptext'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_markers',
			'title'    => '',
			'desc'    => __('Popup text','lmm'),
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['misc_marker_listing_columns_layername'] = array(
			'version' => '2.7.1',
			'pane'    => 'misc',
			'section' => 'misc-list_all_markers',
			'title'    => '',
			'desc'    => __('Layer name','lmm') . ' ' . __('(for marker listings below multi-layer maps only)','lmm'),
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['misc_marker_listing_columns_basemap'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_markers',
			'title'    => '',
			'desc'    => __('Basemap','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['misc_marker_listing_columns_layer'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_markers',
			'title'    => '',
			'desc'    => __('Layer','lmm'),
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['misc_marker_listing_columns_coordinates'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_markers',
			'title'    => '',
			'desc'    => __('Coordinates','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['misc_marker_listing_columns_zoom'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_markers',
			'title'    => '',
			'desc'    => __('Zoom','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['misc_marker_listing_columns_openpopup'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_markers',
			'title'    => '',
			'desc'    => __('Popup status','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['misc_marker_listing_columns_panelstatus'] = array(
			'version' => '1.4',
			'pane'    => 'misc',
			'section' => 'misc-list_all_markers',
			'title'    => '',
			'desc'    => __('Panel status','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['misc_marker_listing_columns_mapsize'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_markers',
			'title'    => '',
			'desc'    => __('Map size','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['misc_marker_listing_columns_createdby'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_markers',
			'title'    => '',
			'desc'    => __('Created by','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['misc_marker_listing_columns_createdon'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_markers',
			'title'    => '',
			'desc'    => __('Created on','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['misc_marker_listing_columns_updatedby'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_markers',
			'title'    => '',
			'desc'    => __('Updated by','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['misc_marker_listing_columns_updatedon'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_markers',
			'title'    => '',
			'desc'    => __('Updated on','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['misc_marker_listing_columns_controlbox'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_markers',
			'title'    => '',
			'desc'    => __('Controlbox status','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['misc_marker_listing_columns_used_in_content'] = array(
			'version' => '3.10.1',
			'pane'    => 'misc',
			'section' => 'misc-list_all_markers',
			'title'    => '',
			'desc'    => __('Used in content','lmm') . $pro_button_link_inline,
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['misc_marker_listing_columns_shortcode'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_markers',
			'title'    => '',
			'desc'    => __('Shortcode','lmm'),
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['misc_marker_listing_columns_kml'] = array(
			'version' => '3.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_markers',
			'title'    => '',
			'desc'    => 'KML',
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['misc_marker_listing_columns_fullscreen'] = array(
			'version' => '3.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_markers',
			'title'    => '',
			'desc'    => __('Fullscreen','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['misc_marker_listing_columns_qr_code'] = array(
			'version' => '3.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_markers',
			'title'    => '',
			'desc'    => __('QR code','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['misc_marker_listing_columns_geojson'] = array(
			'version' => '3.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_markers',
			'title'    => '',
			'desc'    => 'GeoJSON',
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['misc_marker_listing_columns_georss'] = array(
			'version' => '3.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_markers',
			'title'    => '',
			'desc'    => 'GeoRSS',
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['misc_marker_listing_columns_wikitude'] = array(
			'version' => '3.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_markers',
			'title'    => '',
			'desc'    => 'Wikitude',
			'type'    => 'checkbox',
			'std'     => 0
		);
		/* Sort order for marker listing page */
		$this->_settings['misc_marker_listing_sort_helptext'] = array(
			'version' => '2.3',
			'pane'    => 'misc',
			'section' => 'misc-list_all_markers',
			'std'     => '',
			'title'   => '<h4 class="h4-lmm-settings">' . __('Sort order','lmm') . '</h4>',
			'desc'    => __( 'Please select order by and sort order for "List all markers" page', 'lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['misc_marker_listing_sort_order_by'] = array(
			'version' => '2.3',
			'pane'    => 'misc',
			'section' => 'misc-list_all_markers',
			'title'   => __('Order list of markers by','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'm.id',
			'choices' => array(
				'm.id' => 'ID',
				'm.markername' => __('marker name','lmm'),
				'm.layer' => __('assigned layer','lmm') . '(ID)',
				'm.createdon' => __('created on','lmm'),
				'm.createdby' => __('created by','lmm'),
				'm.updatedon' => __('updated on','lmm'),
				'm.updatedby' => __('updated by','lmm')
			)
		);
		$this->_settings['misc_marker_listing_sort_sort_order'] = array(
			'version' => '2.3',
			'pane'    => 'misc',
			'section' => 'misc-list_all_markers',
			'title'   => __('Sort order','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'ASC',
			'choices' => array(
				'ASC' => __('ascending','lmm'),
				'DESC' => __('descending','lmm')
			)
		);

		/*
		* "List all layers" page settings
		*/
		$this->_settings['misc_layer_listing_columns_helptext'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_layers',
			'std'     => '',
			'title'   => '',
			'desc'    => '',
			'type'    => 'helptext'
		);
		$this->_settings['layers_per_page'] = array(
			'version' => 'p2.8',
			'pane'    => 'misc',
			'section' => 'misc-list_all_layers',
			'title'   => __( 'Layers per page', 'lmm' ) . $pro_button_link,
			'desc'    => __( 'The number of layer per page that should be listed in the table on the "list all layers"-page on backend', 'lmm' ),
			'std'     => '30',
			'type'    => 'text-pro'
		);
		$this->_settings['misc_layer_listing_columns_helptext2'] = array(
			'version' => 'p2.8',
			'pane'    => 'misc',
			'section' => 'misc-list_all_layers',
			'std'     => '',
			'title'   => '',
			'desc'    => '<h4 class="h4-lmm-settings">' . __('Available columns','lmm') . '</h4>' . __( 'Please select the columns which should be available on the page "List all layers"', 'lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['misc_layer_listing_columns_id'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_layers',
			'title'    => __('Columns to show','lmm'),
			'desc'    => 'ID',
			'type'    => 'checkbox-readonly',
			'std'     => 1
		);
		$this->_settings['misc_layer_listing_columns_type'] = array(
			'version' => '1.7',
			'pane'    => 'misc',
			'section' => 'misc-list_all_layers',
			'title'    => '',
			'desc'    => __('Type','lmm'),
			'type'    => 'checkbox-readonly',
			'std'     => 1
		);
		$this->_settings['misc_layer_listing_columns_layername'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_layers',
			'title'    => '',
			'desc'    => __('Layer name','lmm'),
			'type'    => 'checkbox-readonly',
			'std'     => 1
		);
		$this->_settings['misc_layer_listing_columns_address'] = array(
			'version' => '3.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_layers',
			'title'    => '',
			'desc'    => __('Address','lmm'),
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['misc_layer_listing_columns_markercount'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_layers',
			'title'    => '',
			'desc'    => __('Number of markers','lmm'),
			'type'    => 'checkbox-readonly',
			'std'     => 1
		);
		$this->_settings['misc_layer_listing_columns_basemap'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_layers',
			'title'    => '',
			'desc'    => __('Basemap','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['misc_layer_listing_columns_layercenter'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_layers',
			'title'    => '',
			'desc'    => __('Layer center','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['misc_layer_listing_columns_zoom'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_layers',
			'title'    => '',
			'desc'    => __('Zoom','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['misc_layer_listing_columns_mapsize'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_layers',
			'title'    => '',
			'desc'    => __('Map size','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['misc_layer_listing_columns_panelstatus'] = array(
			'version' => '1.4',
			'pane'    => 'misc',
			'section' => 'misc-list_all_layers',
			'title'    => '',
			'desc'    => __('Panel status','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['misc_layer_listing_columns_createdby'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_layers',
			'title'    => '',
			'desc'    => __('Created by','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['misc_layer_listing_columns_createdon'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_layers',
			'title'    => '',
			'desc'    => __('Created on','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['misc_layer_listing_columns_updatedby'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_layers',
			'title'    => '',
			'desc'    => __('Updated by','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['misc_layer_listing_columns_updatedon'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_layers',
			'title'    => '',
			'desc'    => __('Updated on','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['misc_layer_listing_columns_controlbox'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_layers',
			'title'    => '',
			'desc'    => __('Controlbox status','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['misc_layer_listing_columns_used_in_content'] = array(
			'version' => '3.10.1',
			'pane'    => 'misc',
			'section' => 'misc-list_all_layers',
			'title'    => '',
			'desc'    => __('Used in content','lmm') . $pro_button_link_inline,
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['misc_layer_listing_columns_shortcode'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_layers',
			'title'    => '',
			'desc'    => __('Shortcode','lmm'),
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['misc_layer_listing_columns_kml'] = array(
			'version' => '3.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_layers',
			'title'    => '',
			'desc'    => 'KML',
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['misc_layer_listing_columns_fullscreen'] = array(
			'version' => '3.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_layers',
			'title'    => '',
			'desc'    => __('Fullscreen','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['misc_layer_listing_columns_qr_code'] = array(
			'version' => '3.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_layers',
			'title'    => '',
			'desc'    => __('QR code','lmm'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['misc_layer_listing_columns_geojson'] = array(
			'version' => '3.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_layers',
			'title'    => '',
			'desc'    => 'GeoJSON',
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['misc_layer_listing_columns_georss'] = array(
			'version' => '3.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_layers',
			'title'    => '',
			'desc'    => 'GeoRSS',
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->_settings['misc_layer_listing_columns_wikitude'] = array(
			'version' => '3.0',
			'pane'    => 'misc',
			'section' => 'misc-list_all_layers',
			'title'    => '',
			'desc'    => 'Wikitude',
			'type'    => 'checkbox',
			'std'     => 0
		);
		/* Sort order for layer listing page */
		$this->_settings['misc_layer_listing_sort_helptext'] = array(
			'version' => '2.3',
			'pane'    => 'misc',
			'section' => 'misc-list_all_layers',
			'std'     => '',
			'title'   => '<h4 class="h4-lmm-settings">' . __('Sort order','lmm') . '</h4>',
			'desc'    => __( 'Please select order by and sort order for "List all layers" page', 'lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['misc_layer_listing_sort_order_by'] = array(
			'version' => '2.3',
			'pane'    => 'misc',
			'section' => 'misc-list_all_layers',
			'title'   => __('Order list of markers by','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'id',
			'choices' => array(
				'id' => 'ID',
				'name' => __('layer name','lmm'),
				'createdon' => __('created on','lmm'),
				'createdby' => __('created by','lmm'),
				'updatedon' => __('updated on','lmm'),
				'updatedby' => __('updated by','lmm')
			)
		);
		$this->_settings['misc_layer_listing_sort_sort_order'] = array(
			'version' => '2.3',
			'pane'    => 'misc',
			'section' => 'misc-list_all_layers',
			'title'   => __('Sort order','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'ASC',
			'choices' => array(
				'ASC' => __('ascending','lmm'),
				'DESC' => __('descending','lmm')
			)
		);

		/*
		* Web API settings
		*/
		$this->_settings['api_helptext'] = array(
			'version' => '3.6',
			'pane'    => 'misc',
			'section' => 'misc-web_api',
			'std'     => '',
			'title'   => '',
			'desc'    => __('Use the Web API to access a Maps Marker Pro install either from JavaScript in a plugin or theme, or from an external client such as a desktop, mobile or web app.','lmm') . '<br/>' . sprintf(__('For more information on how to use the MapsMarker API, <a href="%1s" target="_blank">please visit the API docs on mapsmarker.com</a>','lmm'), 'https://www.mapsmarker.com/mapsmarker-api') . '<br/><br/>' . sprintf(__('For more information on all available APIs in Maps Marker Pro <a href="%1$s">please click here</a>.','lmm'), LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_apis'),
			'type'    => 'helptext'
		);
		$this->_settings['api_status'] = array(
			'version' => '3.6',
			'pane'    => 'misc',
			'section' => 'misc-web_api',
			'title'   => __('API status','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'disabled',
			'choices' => array(
				'enabled' => __('enabled','lmm'),
				'disabled' => __('disabled','lmm')
			)
		);
		$this->_settings['api_default_format'] = array(
			'version' => '3.6',
			'pane'    => 'misc',
			'section' => 'misc-web_api',
			'title'   => __('API default format','lmm'),
			'desc'    => __('Default output format (can be overwritten by parameter format on each API request)','lmm'),
			'type'    => 'radio',
			'std'     => 'json',
			'choices' => array(
				'json' => 'JSON',
				'xml' => 'XML'
			)
		);
		$this->_settings['api_json_callback'] = array(
			'version' => '3.6',
			'pane'    => 'misc',
			'section' => 'misc-web_api',
			'title'   => __('JSONP callback function name', 'lmm'),
			'desc'    => sprintf(__('Used for JSON format only, allows to overcome the <a href="%1s" target="_blank">same origin policy</a> (can be overwritten by parameter callback on each API request)','lmm'), 'http://en.wikipedia.org/wiki/JSONP'),
			'std'     => 'jsonp',
			'type'    => 'text'
		);
		$this->_settings['api_helptext4'] = array(
			'version' => '3.8.6',
			'pane'    => 'misc',
			'section' => 'misc-web_api',
			'std'     => '',
			'title'   => '<h4 class="h4-lmm-settings">' . __('Authentication','lmm') . '</h4>',
			'desc'    =>  sprintf(__('You will find a <a href="%1$s">API URL generator</a> and <a href="%2$s">API URL tester</a> in the tools section. Please see the API docs for more information on authentication.','lmm'), LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools#api-url-generator', LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools#api-url-tester'),
			'type'    => 'helptext'
		);
		$this->_settings['api_key'] = array(
			'version' => '3.6',
			'pane'    => 'misc',
			'section' => 'misc-web_api',
			'title'   => __('Public API key', 'lmm'),
			'desc'    => __('Both public and private API keys are needed for API calls!','lmm'),
			'std'     => '',
			'type'    => 'text'
		);
		$this->_settings['api_key_private'] = array(
			'version' => '3.8.6',
			'pane'    => 'misc',
			'section' => 'misc-web_api',
			'title'   => __('Private API key', 'lmm'),
			'desc'    => __('Both public and private API keys are needed for API calls!','lmm'),
			'std'     => '',
			'type'    => 'text'
		);
		$this->_settings['api_helptext3'] = array(
			'version' => '3.6',
			'pane'    => 'misc',
			'section' => 'misc-web_api',
			'std'     => '',
			'title'   => '<h4 class="h4-lmm-settings">' . __('Security options','lmm') . '</h4>',
			'desc'    =>  '',
			'type'    => 'helptext'
		);
		$this->_settings['api_permissions_view'] = array(
			'version' => '3.6',
			'pane'    => 'misc',
			'section' => 'misc-web_api',
			'title'   => __('Allowed API actions','lmm'),
			'desc'    => __('view existing markers/layers','lmm'),
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['api_permissions_add'] = array(
			'version' => '3.6',
			'pane'    => 'misc',
			'section' => 'misc-web_api',
			'title'   => '',
			'desc'    => __('add new markers/layers','lmm'),
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['api_permissions_update'] = array(
			'version' => 'p1.0',
			'pane'    => 'misc',
			'section' => 'misc-web_api',
			'title'   => '',
			'desc'    => __('update existing markers/layers','lmm') . $pro_feature_link,
			'type'    => 'checkbox-pro',
			'std'     => 0
		);
		$this->_settings['api_permissions_delete'] = array(
			'version' => 'p1.0',
			'pane'    => 'misc',
			'section' => 'misc-web_api',
			'title'   => '',
			'desc'    => __('delete existing markers/layers','lmm') . $pro_feature_link,
			'type'    => 'checkbox-pro',
			'std'     => 0
		);
		$this->_settings['api_permissions_search'] = array(
			'version' => 'p1.5.7',
			'pane'    => 'misc',
			'section' => 'misc-web_api',
			'title'   => '',
			'desc'    => __('search existing markers/layers','lmm') . $pro_feature_link,
			'type'    => 'checkbox-pro',
			'std'     => 0
		);
		$this->_settings['api_allowed_ip'] = array(
			'version' => '3.6',
			'pane'    => 'misc',
			'section' => 'misc-web_api',
			'title'   => __('IP access restriction', 'lmm'),
			'desc'    => __('If an IP address or range is entered above (like 12.34.56.*), only API calls from this IP address or range are allowed.','lmm'),
			'std'     => '',
			'type'    => 'text'
		);
		$this->_settings['api_allowed_referer'] = array(
			'version' => '3.6',
			'pane'    => 'misc',
			'section' => 'misc-web_api',
			'title'   => __('Allowed referer', 'lmm'),
			'desc'    => __('If set (like http://www.your-domain.com/form.php), only API calls with this referer are allowed.','lmm'),
			'std'     => '',
			'type'    => 'text'
		);
		$this->_settings['api_request_type_get'] = array(
			'version' => '3.6',
			'pane'    => 'misc',
			'section' => 'misc-web_api',
			'title'   => __('Allowed API request methods','lmm'),
			'desc'    => 'GET',
			'type'    => 'checkbox',
			'std'     => 1
		);
		$this->_settings['api_request_type_post'] = array(
			'version' => '3.6',
			'pane'    => 'misc',
			'section' => 'misc-web_api',
			'title'   => '',
			'desc'    => 'POST',
			'type'    => 'checkbox',
			'std'     => 1
		);

		/*
		* Permission settings
		*/
		$this->_settings['capabilities_helptext1'] = array(
			'version' => '3.10.1',
			'pane'    => 'misc',
			'section' => 'misc-permissions',
			'std'     => '',
			'title'   => '',
			'desc'    => '',
			'type'    => 'helptext'
		);
		$this->_settings['capabilities_view_others'] = array(
			'version' => 'p2.5',
			'pane'    => 'misc',
			'section' => 'misc-permissions',
			'title'   => __( 'User role needed for viewing markers/layers from other users (applies only to backend access)', 'lmm' ) . $pro_button_link,
			'desc'    => '',
			'type'    => 'radio-pro',
			'std'     => 'edit_posts',
			'choices' => array(
				'activate_plugins' => __('Administrator (Capability activate_plugins)', 'lmm'),
				'moderate_comments' => __('Editor (Capability moderate_comments)', 'lmm'),
				'edit_published_posts' => __('Author (Capability edit_published_posts)', 'lmm'),
				'edit_posts' => __('Contributor (Capability edit_posts)', 'lmm'),
				'read' => __('Subscriber (Capability read)', 'lmm')
			)
		);
		$this->_settings['capabilities_edit'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'misc-permissions',
			'title'   => __( 'User role needed for adding and editing markers/layers', 'lmm' ),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'edit_posts',
			'choices' => array(
				'activate_plugins' => __('Administrator (Capability activate_plugins)', 'lmm'),
				'moderate_comments' => __('Editor (Capability moderate_comments)', 'lmm'),
				'edit_published_posts' => __('Author (Capability edit_published_posts)', 'lmm'),
				'edit_posts' => __('Contributor (Capability edit_posts)', 'lmm'),
				'read' => __('Subscriber (Capability read)', 'lmm')
			)
		);
		$this->_settings['capabilities_edit_others'] = array(
			'version' => 'p1.2',
			'pane'    => 'misc',
			'section' => 'misc-permissions',
			'title'   => __( 'User role needed for editing markers/layers from other users', 'lmm' ) . $pro_button_link,
			'desc'    => '',
			'type'    => 'radio-pro',
			'std'     => 'edit_posts',
			'choices' => array(
				'activate_plugins' => __('Administrator (Capability activate_plugins)', 'lmm'),
				'moderate_comments' => __('Editor (Capability moderate_comments)', 'lmm'),
				'edit_published_posts' => __('Author (Capability edit_published_posts)', 'lmm'),
				'edit_posts' => __('Contributor (Capability edit_posts)', 'lmm'),
				'read' => __('Subscriber (Capability read)', 'lmm')
			)
		);
		$this->_settings['capabilities_delete'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'misc-permissions',
			'title'   => __( 'User role needed for deleting markers/layers', 'lmm' ),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'edit_posts',
			'choices' => array(
				'activate_plugins' => __('Administrator (Capability activate_plugins)', 'lmm'),
				'moderate_comments' => __('Editor (Capability moderate_comments)', 'lmm'),
				'edit_published_posts' => __('Author (Capability edit_published_posts)', 'lmm'),
				'edit_posts' => __('Contributor (Capability edit_posts)', 'lmm'),
				'read' => __('Subscriber (Capability read)', 'lmm')
			)
		);
		$this->_settings['capabilities_delete_others'] = array(
			'version' => 'p1.2',
			'pane'    => 'misc',
			'section' => 'misc-permissions',
			'title'   => __( 'User role needed for deleting markers/layers from other users', 'lmm' ) . $pro_button_link,
			'desc'    => '',
			'type'    => 'radio-pro',
			'std'     => 'edit_posts',
			'choices' => array(
				'activate_plugins' => __('Administrator (Capability activate_plugins)', 'lmm'),
				'moderate_comments' => __('Editor (Capability moderate_comments)', 'lmm'),
				'edit_published_posts' => __('Author (Capability edit_published_posts)', 'lmm'),
				'edit_posts' => __('Contributor (Capability edit_posts)', 'lmm'),
				'read' => __('Subscriber (Capability read)', 'lmm')
			)
		);

		/*
		* XML sitemaps integration
		*/
		$this->_settings['xml_sitemaps_helptext1'] = array(
			'version' => 'p2.6',
			'pane'    => 'misc',
			'section' => 'misc-xml_sitemap',
			'std'     => '',
			'title'   => '',
			'desc'    => sprintf(__( 'XML sitemaps help search engines like Google, Bing, Yahoo and Ask.com to better index your blog. With such a sitemap, it is much easier for the crawlers to see the complete structure of your site and retrieve it more efficiently. Geolocation information can also be added to sitemaps in order to improve your local SEO value for services like Google Places.<br/><br/>In order to automatically add links to your KML maps to your XML sitemaps, please install and activate the plugin %1$s. If you do not want to use that plugin, you can manually register <a href="%2$s" target="_blank">your geositemap</a> by following <a href="%3$s" target="_blank">this tutorial</a>.', 'lmm'), '<a href="https://wordpress.org/plugins/google-sitemap-generator/" target="_blank">Google XML Sitemaps</a>', LEAFLET_PLUGIN_URL . 'leaflet-geositemap.php', 'https://www.mapsmarker.com/geo-sitemap'),
			'type'    => 'helptext'
		);
		$this->_settings['xml_sitemaps_status'] = array(
			'version' => 'p2.6',
			'pane'    => 'misc',
			'section' => 'misc-xml_sitemap',
			'title'   => __('Automatic Google XML sitemap integration','lmm') . $pro_button_link,
			'desc'    => sprintf(__('If enabled is selected and the plugin %1$s is active, KML links will automatically be added to your XML sitemap','lmm'), '<a href="https://wordpress.org/plugins/google-sitemap-generator/" target="_blank">Google XML Sitemaps</a>'),
			'type'    => 'radio-pro',
			'std'     => 'disabled',
			'choices' => array(
				'enabled' => __('enabled','lmm'),
				'disabled' => __('disabled','lmm')
			)
		);
		$this->_settings['xml_sitemaps_include'] = array(
			'version' => 'p2.6',
			'pane'    => 'misc',
			'section' => 'misc-xml_sitemap',
			'title'   => __('Content to add to XML sitemap','lmm') . $pro_button_link,
			'desc'    => '',
			'type'    => 'radio-pro',
			'std'     => 'both',
			'choices' => array(
				'both' => __('marker and layer maps','lmm'),
				'markers' => __('marker maps only','lmm'),
				'layers' => __('layer maps only','lmm')
			)
		);
		$this->_settings['xml_sitemaps_priority_markers'] = array(
			'version' => 'p2.6',
			'pane'    => 'misc',
			'section' => 'misc-xml_sitemap',
			'title'   => __('Priority for marker maps','lmm') . $pro_button_link,
			'desc'    => __('The priority of maps relative to other URLs on your site','lmm'),
			'type'    => 'select-pro',
			'std'     => '0.5',
			'choices' => array(
				'0' => '0',
				'0.1' => '0.1',
				'0.2' => '0.2',
				'0.3' => '0.3',
				'0.4' => '0.4',
				'0.5' => '0.5',
				'0.6' => '0.6',
				'0.7' => '0.7',
				'0.8' => '0.8',
				'0.9' => '0.9',
				'1' => '1'
			)
		);
		$this->_settings['xml_sitemaps_priority_layers'] = array(
			'version' => 'p2.6',
			'pane'    => 'misc',
			'section' => 'misc-xml_sitemap',
			'title'   => __('Priority for layer maps','lmm') . $pro_button_link,
			'desc'    => __('The priority of maps relative to other URLs on your site','lmm'),
			'type'    => 'select-pro',
			'std'     => '0.5',
			'choices' => array(
				'0' => '0',
				'0.1' => '0.1',
				'0.2' => '0.2',
				'0.3' => '0.3',
				'0.4' => '0.4',
				'0.5' => '0.5',
				'0.6' => '0.6',
				'0.7' => '0.7',
				'0.8' => '0.8',
				'0.9' => '0.9',
				'1' => '1'
			)
		);
		$this->_settings['xml_sitemaps_change_frequency_markers'] = array(
			'version' => 'p2.6',
			'pane'    => 'misc',
			'section' => 'misc-xml_sitemap',
			'title'   => __('Change frequency for marker maps','lmm') . $pro_button_link,
			'desc'    => sprintf(__('How frequently the maps are likely to change. This value provides general information to search engines and may not correlate exactly to how often they crawl the page. Additional information available at %1$s','lmm'), '<a href="http://www.sitemaps.org/protocol.html" target="_blank">sitemaps.org</a>'),
			'type'    => 'select-pro',
			'std'     => 'monthly',

			'choices' => array(
				'always' => __('Always','lmm'),
				'hourly' => __('Hourly','lmm'),
				'daily' => __('Daily','lmm'),
				'weekly' => __('Weekly','lmm'),
				'monthly' => __('Monthly','lmm'),
				'yearly' => __('Yearly','lmm'),
				'never' => __('Never','lmm')
			)
		);
		$this->_settings['xml_sitemaps_change_frequency_layers'] = array(
			'version' => 'p2.6',
			'pane'    => 'misc',
			'section' => 'misc-xml_sitemap',
			'title'   => __('Change frequency for layer maps','lmm') . $pro_button_link,
			'desc'    => sprintf(__('How frequently the maps are likely to change. This value provides general information to search engines and may not correlate exactly to how often they crawl the page. Additional information available at %1$s','lmm'), '<a href="http://www.sitemaps.org/protocol.html" target="_blank">sitemaps.org</a>'),
			'type'    => 'select-pro',
			'std'     => 'monthly',
			'choices' => array(
				'always' => __('Always','lmm'),
				'hourly' => __('Hourly','lmm'),
				'daily' => __('Daily','lmm'),
				'weekly' => __('Weekly','lmm'),
				'monthly' => __('Monthly','lmm'),
				'yearly' => __('Yearly','lmm'),
				'never' => __('Never','lmm')
			)
		);

		/*
		* Compatibility settings
		*/
		$this->_settings['misc_compat_settings_helptext1'] = array(
			'version' => '3.11',
			'pane'    => 'misc',
			'section' => 'misc-compatibility',
			'std'     => '',
			'title'   => '',
			'desc'    => __( 'The following settings only need to be changed if you experience any compatibility issues with your theme or other plugins.', 'lmm'),
			'type'    => 'helptext'
		);
		$this->_settings['misc_javascript_header_footer'] = array(
			'version' => '3.1',
			'pane'    => 'misc',
			'section' => 'misc-compatibility',
			'title'   => __('Where to insert Javascript files on frontend?','lmm'),
			'desc'    => __('Footer is recommended for better performance. If you are using WordPress lesser than v3.3, Javascript files automatically get inserted into the header of your site and the javascript needed for each maps inline within the content.','lmm') . ' ' . __('If you choose footer, javascripts will also only be loaded when a shortcode is used and not on all pages.','lmm') . ' ' . __('Setting this option to header+inline-javascript is required, if maps should be displayed withing a jQuery mobile framework.','lmm'),
			'type'    => 'radio',
			'std'     => 'footer',
			'choices' => array(
				'header' => __('header (+ inline javascript)','lmm'),
				'footer' => __('footer','lmm')
			)
		);
		$this->_settings['misc_responsive_support'] = array(
			'version' => '3.2',
			'pane'    => 'misc',
			'section' => 'misc-compatibility',
			'title'   => __('Support for responsive designs','lmm'),
			'desc'    => __('If enabled, maps will automatically be resized to width=100% if map width unit is set to px and the div parent element is smaller than the width of the map.','lmm'),
			'type'    => 'radio',
			'std'     => 'enabled',
			'choices' => array(
				'enabled' => __('enabled','lmm'),
				'disabled' => __('disabled','lmm')
			)
		);
		$this->_settings['misc_conditional_css_loading'] = array(
			'version' => '3.2.2',
			'pane'    => 'misc',
			'section' => 'misc-compatibility',
			'title'   => __('Support for conditional css loading','lmm'),
			'desc'    => __('If enabled, css files will only be loaded if a shortcode is used.','lmm'),
			'type'    => 'radio',
			'std'     => 'enabled',
			'choices' => array(
				'enabled' => __('enabled','lmm'),
				'disabled' => __('disabled','lmm')
			)
		);
		$this->_settings['misc_tab_hidden_div_compatibility'] = array(
			'version' => 'p2.7.2',
			'pane'    => 'misc',
			'section' => 'misc-compatibility',
			'title'   => __('Tab/hidden div compatibility','lmm') . $pro_button_link,
			'desc'    => __('Please enable this setting only if you are experiencing issues with maps in proprietary tab solutions or hidden divs which are displayed on demand.','lmm'),
			'type'    => 'radio-pro',
			'std'     => 'disabled',
			'choices' => array(
				'enabled' => __('enabled','lmm'),
				'disabled' => __('disabled','lmm')
			)
		);
		$this->_settings['async_geojson_loading'] = array(
			'version' => 'p1.6',
			'pane'    => 'misc',
			'section' => 'misc-compatibility',
			'title'   => __('Async GeoJSON loading for layer maps','lmm') . $pro_button_link,
			'desc'    => __('Disabling async GeoJSON loading will increase page loadtime and is only needed if multiple instances of a layer map should be displayed on one page.','lmm'),
			'type'    => 'radio-pro',
			'std'     => 'disabled',
			'choices' => array(
				'enabled' => __('enabled','lmm'),
				'disabled' => __('disabled','lmm')
			)
		);
		$this->_settings['maxzoom_compatibility_mode'] = array(
			'version' => '3.11.1',
			'pane'    => 'misc',
			'section' => 'misc-compatibility',
			'title'   => __('maxZoom theme compatibility','lmm'),
			'desc'    => sprintf(__('Please only enable this setting if (Google) maps do not show markers but the following error in the browser console instead: %1$s','lmm'), '"Uncaught Map has no maxZoom specified"'),
			'type'    => 'radio',
			'std'     => 'disabled',
			'choices' => array(
				'enabled' => __('enabled','lmm'),
				'disabled' => __('disabled','lmm')
			)
		);
		$this->_settings['rewrite_baseurl'] = array(
			'version' => 'p3.0.1',
			'pane'    => 'misc',
			'section' => 'misc-compatibility',
			'title'   => __('Permalinks base URL','lmm') . $pro_button_link,
			'desc'    => __('Needed for creating pretty links to fullscreen maps or API endpoints.','lmm') . '<br/>' . __('Only set this option to the URL of your WordPress folder if you are experiencing issues or recommended so by support!','lmm') . '<br/>' . sprintf(esc_attr__('If empty, "WordPress Address (URL)" - %1$s - will be used.','lmm'), get_site_url()),
			'type'    => 'text-pro',
			'std'     => ''
		);
		$this->_settings['wp_kses_status'] = array(
			'version' => '3.12.1',
			'pane'    => 'misc',
			'section' => 'misc-compatibility',
			'title'   => __('HTML filter for popuptexts','lmm') . ' (wp_kses)',
			'desc'    => sprintf(__('If enabled, unsupported code tags are stripped from popuptexts to prevent injection of malicious code (<a href="%1$s" target="_blank">reference of supported code tags</a>).','lmm'), 'https://core.trac.wordpress.org/browser/tags/4.7.3/src/wp-includes/kses.php#L53') . '<br/>' . __('Disabling this option allows you to display unfiltered popuptexts and is only recommended if special HTML tags are needed.','lmm'),
			'type'    => 'radio',
			'std'     => 'enabled',
			'choices' => array(
				'enabled' => __('enabled','lmm'),
				'disabled' => __('disabled','lmm')
			)
		);

		/*
		* WordPress integration settings
		*/
		$this->_settings['misc_wp_integr_helptext1'] = array(
			'version' => '3.11',
			'pane'    => 'misc',
			'section' => 'misc-wordpress_integration',
			'std'     => '',
			'title'   => '',
			'desc'    => '',
			'type'    => 'helptext'
		);
		$this->_settings['shortcode'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'misc-wordpress_integration',
			'title'   => __( 'Shortcode', 'lmm' ),
			'desc'    => __( 'Shortcode to add markers or layers into articles or pages  - Example: [<strong>mapsmarker</strong> marker="1"].<br/> Attention: if you change the shortcode after having embedded shortcodes into posts/Pages, the shortcode on these specific articles/pages has to be changed also manually - otherwise these markers/layers will not be show on frontend!', 'lmm' ),
			'std'     => 'mapsmarker',
			'type'    => 'text'
		);
		$this->_settings['misc_tinymce_button'] = array(
			'version' => '1.9',
			'pane'    => 'misc',
			'section' => 'misc-wordpress_integration',
			'title'   => __('TinyMCE button','lmm'),
			'desc'    => __('if enabled, an "Insert map" button gets added above the TinyMCE editor on post and page edit screens for easily searching and inserting maps','lmm'),
			'type'    => 'radio',
			'std'     => 'enabled',
			'choices' => array(
				'enabled' => __('enabled','lmm'),
				'disabled' => __('disabled','lmm')
			)
		);
		$this->_settings['misc_add_georss_to_head'] = array(
			'version' => '1.5',
			'pane'    => 'misc',
			'section' => 'misc-wordpress_integration',
			'title'   => __('Add GeoRSS feed to &lt;head&gt;','lmm'),
			'desc'    => __('if enabled, a GeoRSS feed for all markers will be added to the &lt;head&gt;-section of the website, allowing users to subscribe to your markers','lmm'),
			'type'    => 'radio',
			'std'     => 'enabled',
			'choices' => array(
				'enabled' => __('enabled','lmm'),
				'disabled' => __('disabled','lmm')
			)
		);
		$this->_settings['admin_bar_integration'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'misc-wordpress_integration',
			'title'   => __('WordPress Admin Bar integration','lmm'),
			'desc'    => __('show or hide drop down menu in Wordpress Admin Bar','lmm'),
			'type'    => 'radio',
			'std'     => 'enabled',
			'choices' => array(
				'enabled' => __('enabled','lmm'),
				'disabled' => __('disabled','lmm')
			)
		);
		$this->_settings['misc_admin_dashboard_widget'] = array(
			'version' => '2.5',
			'pane'    => 'misc',
			'section' => 'misc-wordpress_integration',
			'title'   => __('WordPress admin dashboard widget','lmm'),
			'desc'    => __('shows a widget on the admin dashboard which displays latest markers and blog posts from mapsmarker.com','lmm'),
			'type'    => 'radio',
			'std'     => 'enabled',
			'choices' => array(
				'enabled' => __('enabled','lmm'),
				'disabled' => __('disabled','lmm')
			)
		);
		$this->_settings['misc_pointers'] = array(
			'version' => '2.8',
			'pane'    => 'misc',
			'section' => 'misc-wordpress_integration',
			'title'   => __('WordPress Pointers','lmm'),
			'desc'    => __('display WordPress pointers on plugin updates','lmm'),
			'type'    => 'radio',
			'std'     => 'enabled',
			'choices' => array(
				'enabled' => __('enabled','lmm'),
				'disabled' => __('disabled','lmm')
			)
		);
		$this->_settings['misc_global_admin_notices'] = array(
			'version' => '3.5',
			'pane'    => 'misc',
			'section' => 'misc-wordpress_integration',
			'title'   => __('Admin notices','lmm'),
			'desc'    => __('Option for global admin notices in backend (showing infos about plugin incompatibilities or invalid marker icon directory settings for example). Please be aware that hiding them results in not being informed about plugin incompatibilites discovered in future releases too!','lmm'),
			'type'    => 'radio',
			'std'     => 'show',
			'choices' => array(
				'show' => __('show','lmm'),
				'hide' => __('hide','lmm')
			)
		);
		$this->_settings['multilingual_integration_status'] = array(
			'version' => 'p3.0',
			'pane'    => 'misc',
			'section' => 'misc-wordpress_integration',
			'title'   => __('WPML/Polylang integration','lmm') . $pro_button_link,
			'desc'    => sprintf(__('if enabled and WPML or Polylang translation plugin is active, map texts can be translated. Fore more details please see %1s','lmm'), '<a href="https://www.mapsmarker.com/multilingual" target="_blank">mapsmarker.com/multilingual</a>'),
			'type'    => 'radio-pro',
			'std'     => 'disabled',
			'choices' => array(
				'enabled' => __('enabled','lmm'),
				'disabled' => __('disabled','lmm')
			)
		);
		$this->_settings['rewrite_slug'] = array(
			'version' => 'p3.0',
			'pane'    => 'misc',
			'section' => 'misc-wordpress_integration',
			'title'   => __('Permalinks slug','lmm') . $pro_button_link,
			'desc'    => sprintf(__('Used to create pretty links to fullscreen maps or API endpoints. Default value: %1$s','lmm'), '<strong>maps</strong>') . '<br/>' . sprintf(__('Example link to fullscreen marker map ID 1: %1$s','lmm'), get_site_url() . '/<strong>maps</strong>/fullscreen/marker/1'),
			'type'    => 'text-pro',
			'std'     => 'maps'
		);

		/*===========================================
		*
		*
		* pane reset
		*
		*
		===========================================*/
		$this->_settings['reset_helptext'] = array(
			'version' => 'p1.0',
			'pane'    => 'reset',
			'section' => 'reset-reset_settings',
			'std'     => '',
			'title'   => '',
			'desc'    => $pro_feature_link . ' ' . sprintf(__('You can backup your current settings on the <a href="%1$s">tools page</a> before resetting all options to their default values.','lmm'), LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools'),
			'type'    => 'helptext'
		);
		$this->_settings['reset_settings'] = array(
			'version' => '1.0',
			'pane'    => 'misc',
			'section' => 'reset-reset_settings',
			'title'   => __('Warning - cannot be undone!','lmm'),
			'type'    => 'checkbox',
			'std'     => 0,
			'class'   => 'warning', // Custom class for CSS
			'desc'    => __( 'Check this box and click "Save Changes" below to reset plugin options to their defaults.','lmm' )
		);
	}

	/**
	 * Initialize settings to their default values
	 */
	public function initialize_settings() {
		$default_settings = array();
		foreach ( $this->settings as $id => $setting ) {
			if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['type'] != 'helptext-twocolumn' && $setting['type'] != 'checkbox-pro' && $setting['type'] != 'select-pro' && $setting['type'] != 'radio-pro' && $setting['type'] != 'radio-reverse-pro' && $setting['type'] != 'textarea-pro' && $setting['type'] != 'text-pro' && $setting['type'] != 'text-reverse-pro' ) {
				$default_settings[$id] = $setting['std'];
				}
		}
		update_option( 'leafletmapsmarker_options', $default_settings );
	}
	/**
	* Register settings
	*/

	public function register_settings() {
		global $pagenow;
		$current_page = (isset($_REQUEST['page'])) ? $_REQUEST['page'] : '';
		if ( ('options.php' !== $pagenow) && ! in_array( $current_page, array( 'leafletmapsmarker_settings' ) ) )
			return;

		register_setting( 'leafletmapsmarker_options', 'leafletmapsmarker_options', array ( &$this, 'validate_settings' ) );

		$this->get_settings();

		    foreach ( $this->settings as $id => $setting ) {
			    $setting['id'] = $id;
			    $this->create_setting( $setting );  // ----setttings
        }
	}
	/**
	 * save defaults for new options after plugin updates but keep values of old settings
	 */
	public function save_defaults_for_new_options() {
		//info:  set defaults for options introduced in v1.1
		if (version_compare(get_option('leafletmapsmarker_version'),'1.0','='))
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting )
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['type'] != 'helptext-twocolumn' && $setting['type'] != 'checkbox-pro' && $setting['type'] != 'select-pro' && $setting['type'] != 'radio-pro' && $setting['type'] != 'radio-reverse-pro' && $setting['type'] != 'textarea-pro' && $setting['type'] != 'text-pro' && $setting['type'] != 'text-reverse-pro' && $setting['version'] == '1.1')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v1.2
		if (version_compare(get_option('leafletmapsmarker_version'),'1.1','='))
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting )
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['type'] != 'helptext-twocolumn' && $setting['type'] != 'checkbox-pro' && $setting['type'] != 'select-pro' && $setting['type'] != 'radio-pro' && $setting['type'] != 'radio-reverse-pro' && $setting['type'] != 'textarea-pro' && $setting['type'] != 'text-pro' && $setting['type'] != 'text-reverse-pro' && $setting['version'] == '1.2')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v1.4
		if (version_compare(get_option('leafletmapsmarker_version'),'1.3','='))
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting )
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['type'] != 'helptext-twocolumn' && $setting['type'] != 'checkbox-pro' && $setting['type'] != 'select-pro' && $setting['type'] != 'radio-pro' && $setting['type'] != 'radio-reverse-pro' && $setting['type'] != 'textarea-pro' && $setting['type'] != 'text-pro' && $setting['type'] != 'text-reverse-pro' && $setting['version'] == '1.4')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v1.4.3
		if (version_compare(get_option('leafletmapsmarker_version'),'1.4.2','='))
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting )
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['type'] != 'helptext-twocolumn' && $setting['type'] != 'checkbox-pro' && $setting['type'] != 'select-pro' && $setting['type'] != 'radio-pro' && $setting['type'] != 'radio-reverse-pro' && $setting['type'] != 'textarea-pro' && $setting['type'] != 'text-pro' && $setting['type'] != 'text-reverse-pro' && $setting['version'] == '1.4.3')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v1.5
		if (version_compare(get_option('leafletmapsmarker_version'),'1.4.3','='))
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting )
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['type'] != 'helptext-twocolumn' && $setting['type'] != 'checkbox-pro' && $setting['type'] != 'select-pro' && $setting['type'] != 'radio-pro' && $setting['type'] != 'radio-reverse-pro' && $setting['type'] != 'textarea-pro' && $setting['type'] != 'text-pro' && $setting['type'] != 'text-reverse-pro' && $setting['version'] == '1.5')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v1.6
		if (version_compare(get_option('leafletmapsmarker_version'),'1.5.1','='))
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting )
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['type'] != 'helptext-twocolumn' && $setting['type'] != 'checkbox-pro' && $setting['type'] != 'select-pro' && $setting['type'] != 'radio-pro' && $setting['type'] != 'radio-reverse-pro' && $setting['type'] != 'textarea-pro' && $setting['type'] != 'text-pro' && $setting['type'] != 'text-reverse-pro' && $setting['version'] == '1.6')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v1.7
		if (version_compare(get_option('leafletmapsmarker_version'),'1.6','='))
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting )
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['type'] != 'helptext-twocolumn' && $setting['type'] != 'checkbox-pro' && $setting['type'] != 'select-pro' && $setting['type'] != 'radio-pro' && $setting['type'] != 'radio-reverse-pro' && $setting['type'] != 'textarea-pro' && $setting['type'] != 'text-pro' && $setting['type'] != 'text-reverse-pro' && $setting['version'] == '1.7')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v1.8
		if (version_compare(get_option('leafletmapsmarker_version'),'1.7','='))
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting )
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['type'] != 'helptext-twocolumn' && $setting['type'] != 'checkbox-pro' && $setting['type'] != 'select-pro' && $setting['type'] != 'radio-pro' && $setting['type'] != 'radio-reverse-pro' && $setting['type'] != 'textarea-pro' && $setting['type'] != 'text-pro' && $setting['type'] != 'text-reverse-pro' && $setting['version'] == '1.8')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v1.9
		if (version_compare(get_option('leafletmapsmarker_version'),'1.8','='))
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting )
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['type'] != 'helptext-twocolumn' && $setting['type'] != 'checkbox-pro' && $setting['type'] != 'select-pro' && $setting['type'] != 'radio-pro' && $setting['type'] != 'radio-reverse-pro' && $setting['type'] != 'textarea-pro' && $setting['type'] != 'text-pro' && $setting['type'] != 'text-reverse-pro' && $setting['version'] == '1.9')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v2.1
		if (version_compare(get_option('leafletmapsmarker_version'),'2.0','='))
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting )
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['type'] != 'helptext-twocolumn' && $setting['type'] != 'checkbox-pro' && $setting['type'] != 'select-pro' && $setting['type'] != 'radio-pro' && $setting['type'] != 'radio-reverse-pro' && $setting['type'] != 'textarea-pro' && $setting['type'] != 'text-pro' && $setting['type'] != 'text-reverse-pro' && $setting['version'] == '2.1')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v2.2
		if (version_compare(get_option('leafletmapsmarker_version'),'2.1','='))
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting )
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['type'] != 'helptext-twocolumn' && $setting['type'] != 'checkbox-pro' && $setting['type'] != 'select-pro' && $setting['type'] != 'radio-pro' && $setting['type'] != 'radio-reverse-pro' && $setting['type'] != 'textarea-pro' && $setting['type'] != 'text-pro' && $setting['type'] != 'text-reverse-pro' && $setting['version'] == '2.2')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v2.3
		if (version_compare(get_option('leafletmapsmarker_version'),'2.2','='))
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting )
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['type'] != 'helptext-twocolumn' && $setting['type'] != 'checkbox-pro' && $setting['type'] != 'select-pro' && $setting['type'] != 'radio-pro' && $setting['type'] != 'radio-reverse-pro' && $setting['type'] != 'textarea-pro' && $setting['type'] != 'text-pro' && $setting['type'] != 'text-reverse-pro' && $setting['version'] == '2.3')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v2.4
		if (version_compare(get_option('leafletmapsmarker_version'),'2.3','='))
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting )
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['type'] != 'helptext-twocolumn' && $setting['type'] != 'checkbox-pro' && $setting['type'] != 'select-pro' && $setting['type'] != 'radio-pro' && $setting['type'] != 'radio-reverse-pro' && $setting['type'] != 'textarea-pro' && $setting['type'] != 'text-pro' && $setting['type'] != 'text-reverse-pro' && $setting['version'] == '2.4')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v2.5
		if (version_compare(get_option('leafletmapsmarker_version'),'2.4','='))
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting )
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['type'] != 'helptext-twocolumn' && $setting['type'] != 'checkbox-pro' && $setting['type'] != 'select-pro' && $setting['type'] != 'radio-pro' && $setting['type'] != 'radio-reverse-pro' && $setting['type'] != 'textarea-pro' && $setting['type'] != 'text-pro' && $setting['type'] != 'text-reverse-pro' && $setting['version'] == '2.5')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v2.6
		if (version_compare(get_option('leafletmapsmarker_version'),'2.5','='))
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting )
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['type'] != 'helptext-twocolumn' && $setting['type'] != 'checkbox-pro' && $setting['type'] != 'select-pro' && $setting['type'] != 'radio-pro' && $setting['type'] != 'radio-reverse-pro' && $setting['type'] != 'textarea-pro' && $setting['type'] != 'text-pro' && $setting['type'] != 'text-reverse-pro' && $setting['version'] == '2.6')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v2.7.1
		if (version_compare(get_option('leafletmapsmarker_version'),'2.7','='))
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting )
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['type'] != 'helptext-twocolumn' && $setting['type'] != 'checkbox-pro' && $setting['type'] != 'select-pro' && $setting['type'] != 'radio-pro' && $setting['type'] != 'radio-reverse-pro' && $setting['type'] != 'textarea-pro' && $setting['type'] != 'text-pro' && $setting['type'] != 'text-reverse-pro' && $setting['version'] == '2.7.1')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v2.8
		if (version_compare(get_option('leafletmapsmarker_version'),'2.7.1','='))
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting )
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['type'] != 'helptext-twocolumn' && $setting['type'] != 'checkbox-pro' && $setting['type'] != 'select-pro' && $setting['type'] != 'radio-pro' && $setting['type'] != 'radio-reverse-pro' && $setting['type'] != 'textarea-pro' && $setting['type'] != 'text-pro' && $setting['type'] != 'text-reverse-pro' && $setting['version'] == '2.8')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v2.9
		if (version_compare(get_option('leafletmapsmarker_version'),'2.8.2','='))
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting )
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['type'] != 'helptext-twocolumn' && $setting['type'] != 'checkbox-pro' && $setting['type'] != 'select-pro' && $setting['type'] != 'radio-pro' && $setting['type'] != 'radio-reverse-pro' && $setting['type'] != 'textarea-pro' && $setting['type'] != 'text-pro' && $setting['type'] != 'text-reverse-pro' && $setting['version'] == '2.9')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v3.0
		if (version_compare(get_option('leafletmapsmarker_version'),'2.9.2','='))
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting )
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['type'] != 'helptext-twocolumn' && $setting['type'] != 'checkbox-pro' && $setting['type'] != 'select-pro' && $setting['type'] != 'radio-pro' && $setting['type'] != 'radio-reverse-pro' && $setting['type'] != 'textarea-pro' && $setting['type'] != 'text-pro' && $setting['type'] != 'text-reverse-pro' && $setting['version'] == '3.0')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v3.1
		if (version_compare(get_option('leafletmapsmarker_version'),'3.0','='))
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting )
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['type'] != 'helptext-twocolumn' && $setting['type'] != 'checkbox-pro' && $setting['type'] != 'select-pro' && $setting['type'] != 'radio-pro' && $setting['type'] != 'radio-reverse-pro' && $setting['type'] != 'textarea-pro' && $setting['type'] != 'text-pro' && $setting['type'] != 'text-reverse-pro' && $setting['version'] == '3.1')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v3.2
		if (version_compare(get_option('leafletmapsmarker_version'),'3.1','='))
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting )
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['type'] != 'helptext-twocolumn' && $setting['type'] != 'checkbox-pro' && $setting['type'] != 'select-pro' && $setting['type'] != 'radio-pro' && $setting['type'] != 'radio-reverse-pro' && $setting['type'] != 'textarea-pro' && $setting['type'] != 'text-pro' && $setting['type'] != 'text-reverse-pro' && $setting['version'] == '3.2')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v3.2.2
		if (version_compare(get_option('leafletmapsmarker_version'),'3.2.1','='))
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting )
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['type'] != 'helptext-twocolumn' && $setting['type'] != 'checkbox-pro' && $setting['type'] != 'select-pro' && $setting['type'] != 'radio-pro' && $setting['type'] != 'radio-reverse-pro' && $setting['type'] != 'textarea-pro' && $setting['type'] != 'text-pro' && $setting['type'] != 'text-reverse-pro' && $setting['version'] == '3.2.2')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v3.3
		if (version_compare(get_option('leafletmapsmarker_version'),'3.2.5','='))
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting )
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['type'] != 'helptext-twocolumn' && $setting['type'] != 'checkbox-pro' && $setting['type'] != 'select-pro' && $setting['type'] != 'radio-pro' && $setting['type'] != 'radio-reverse-pro' && $setting['type'] != 'textarea-pro' && $setting['type'] != 'text-pro' && $setting['type'] != 'text-reverse-pro' && $setting['version'] == '3.3')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v3.4
		if (version_compare(get_option('leafletmapsmarker_version'),'3.3','='))
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting )
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['type'] != 'helptext-twocolumn' && $setting['type'] != 'checkbox-pro' && $setting['type'] != 'select-pro' && $setting['type'] != 'radio-pro' && $setting['type'] != 'radio-reverse-pro' && $setting['type'] != 'textarea-pro' && $setting['type'] != 'text-pro' && $setting['type'] != 'text-reverse-pro' && $setting['version'] == '3.4')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v3.5
		if (version_compare(get_option('leafletmapsmarker_version'),'3.4.3','='))
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting )
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['type'] != 'helptext-twocolumn' && $setting['type'] != 'checkbox-pro' && $setting['type'] != 'select-pro' && $setting['type'] != 'radio-pro' && $setting['type'] != 'radio-reverse-pro' && $setting['type'] != 'textarea-pro' && $setting['type'] != 'text-pro' && $setting['type'] != 'text-reverse-pro' && $setting['version'] == '3.5')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v3.5.2
		if (version_compare(get_option('leafletmapsmarker_version'),'3.5.1','='))
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting )
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['type'] != 'helptext-twocolumn' && $setting['type'] != 'checkbox-pro' && $setting['type'] != 'select-pro' && $setting['type'] != 'radio-pro' && $setting['type'] != 'radio-reverse-pro' && $setting['type'] != 'textarea-pro' && $setting['type'] != 'text-pro' && $setting['type'] != 'text-reverse-pro' && $setting['version'] == '3.5.2')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v3.6
		if (version_compare(get_option('leafletmapsmarker_version'),'3.5.4','='))
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting )
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['type'] != 'helptext-twocolumn' && $setting['type'] != 'checkbox-pro' && $setting['type'] != 'select-pro' && $setting['type'] != 'radio-pro' && $setting['type'] != 'radio-reverse-pro' && $setting['type'] != 'textarea-pro' && $setting['type'] != 'text-pro' && $setting['type'] != 'text-reverse-pro' && $setting['version'] == '3.6')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v3.4
		if (version_compare(get_option('leafletmapsmarker_version'),'3.6.3','='))
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting )
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['type'] != 'helptext-twocolumn' && $setting['type'] != 'checkbox-pro' && $setting['type'] != 'select-pro' && $setting['type'] != 'radio-pro' && $setting['type'] != 'radio-reverse-pro' && $setting['type'] != 'textarea-pro' && $setting['type'] != 'text-pro' && $setting['type'] != 'text-reverse-pro' && $setting['version'] == '3.6.4')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v3.8.6
		if (version_compare(get_option('leafletmapsmarker_version'),'3.8.5','='))
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting )
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['type'] != 'helptext-twocolumn' && $setting['type'] != 'checkbox-pro' && $setting['type'] != 'select-pro' && $setting['type'] != 'radio-pro' && $setting['type'] != 'radio-reverse-pro' && $setting['type'] != 'textarea-pro' && $setting['type'] != 'text-pro' && $setting['type'] != 'text-reverse-pro' && $setting['version'] == '3.8.6')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v3.8.7
		if (version_compare(get_option('leafletmapsmarker_version'),'3.8.6','='))
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting )
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['type'] != 'helptext-twocolumn' && $setting['type'] != 'checkbox-pro' && $setting['type'] != 'select-pro' && $setting['type'] != 'radio-pro' && $setting['type'] != 'radio-reverse-pro' && $setting['type'] != 'textarea-pro' && $setting['type'] != 'text-pro' && $setting['type'] != 'text-reverse-pro' && $setting['version'] == '3.8.7')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v3.10.1
		if (version_compare(get_option('leafletmapsmarker_version'),'3.10','='))
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting )
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['type'] != 'helptext-twocolumn' && $setting['type'] != 'checkbox-pro' && $setting['type'] != 'select-pro' && $setting['type'] != 'radio-pro' && $setting['type'] != 'radio-reverse-pro' && $setting['type'] != 'textarea-pro' && $setting['type'] != 'text-pro' && $setting['type'] != 'text-reverse-pro' && $setting['version'] == '3.10.1')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v3.10.6
		if (version_compare(get_option('leafletmapsmarker_version'),'3.10.5','='))
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting )
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['type'] != 'helptext-twocolumn' && $setting['type'] != 'checkbox-pro' && $setting['type'] != 'select-pro' && $setting['type'] != 'radio-pro' && $setting['type'] != 'radio-reverse-pro' && $setting['type'] != 'textarea-pro' && $setting['type'] != 'text-pro' && $setting['type'] != 'text-reverse-pro' && $setting['version'] == '3.10.6')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v3.11
		if (version_compare(get_option('leafletmapsmarker_version'),'3.10.6','='))
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting )
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['type'] != 'helptext-twocolumn' && $setting['type'] != 'checkbox-pro' && $setting['type'] != 'select-pro' && $setting['type'] != 'radio-pro' && $setting['type'] != 'radio-reverse-pro' && $setting['type'] != 'textarea-pro' && $setting['type'] != 'text-pro' && $setting['type'] != 'text-reverse-pro' && $setting['version'] == '3.11')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v3.11.1
		if (version_compare(get_option('leafletmapsmarker_version'),'3.11','='))
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting )
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['type'] != 'helptext-twocolumn' && $setting['type'] != 'checkbox-pro' && $setting['type'] != 'select-pro' && $setting['type'] != 'radio-pro' && $setting['type'] != 'radio-reverse-pro' && $setting['type'] != 'textarea-pro' && $setting['type'] != 'text-pro' && $setting['type'] != 'text-reverse-pro' && $setting['version'] == '3.11.1')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v3.12.1
		if (version_compare(get_option('leafletmapsmarker_version'),'3.12','='))
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting )
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['type'] != 'helptext-twocolumn' && $setting['type'] != 'checkbox-pro' && $setting['type'] != 'select-pro' && $setting['type'] != 'radio-pro' && $setting['type'] != 'radio-reverse-pro' && $setting['type'] != 'textarea-pro' && $setting['type'] != 'text-pro' && $setting['type'] != 'text-reverse-pro' && $setting['version'] == '3.12.1')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		/* template for plugin updates
		//info:  set defaults for options introduced in v3.13
		if (version_compare(get_option('leafletmapsmarker_version'),'3.12.1','='))
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting )
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['type'] != 'helptext-twocolumn' && $setting['type'] != 'checkbox-pro' && $setting['type'] != 'select-pro' && $setting['type'] != 'radio-pro' && $setting['type'] != 'radio-reverse-pro' && $setting['type'] != 'textarea-pro' && $setting['type'] != 'text-pro' && $setting['type'] != 'text-reverse-pro' && $setting['version'] == '3.13')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		*/
	}

	/**
	* Validate settings
	*/
	public function validate_settings( $input ) {

		if ( ! isset( $input['reset_settings'] ) ) {
			$options = get_option( 'leafletmapsmarker_options' );

			foreach ( $this->checkboxes as $id ) {
				if ( isset( $options[$id] ) && ! isset( $input[$id] ) )
					unset( $options[$id] );
			}
			return $input;
		}
		return false;
	}
}