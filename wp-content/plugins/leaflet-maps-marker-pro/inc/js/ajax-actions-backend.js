jQuery(document).ready(function($) {
	var current_page = lmm_ajax_vars.lmm_ajax_current_page;
	var admin_url = lmm_ajax_vars.lmm_ajax_admin_url;
	var site_url = lmm_ajax_vars.lmm_ajax_site_url;
	var lmm_slug = lmm_ajax_vars.lmm_ajax_lmm_slug;
	var leaflet_plugin_url = lmm_ajax_vars.lmm_ajax_leaflet_plugin_url;
	var shortcode = lmm_ajax_vars.lmm_ajax_shortcode;
	var defaults_marker_icon_url = lmm_ajax_vars.lmm_defaults_marker_icon_url;
	window.group_for_clustering = L.layerGroup();
	//info: js for marker edit page
	if (current_page === 'leafletmapsmarker_marker') {
		if(!$('#id').val()) {
			$('.wpml-markertranslatelink').hide();
		}
		//info: get popuptext
		function lmm_get_tinymce_content() {
			if ($('#wp-popuptext-wrap').hasClass('tmce-active')) {
				return tinyMCE.activeEditor.getContent();
			} else {
				return jQuery('#popuptext').val();
			}
		}

		//info: submit buttons clickable after load only -> moved to TinyMCE event LoadContent with v2.8

		/************************************/
		//info: 1 submit function for add & edit
		$('#marker-add-edit').submit(function() {

			$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').hide();
			$('#lmm_ajax_loading_top, #lmm_ajax_loading_bottom').show();
			$('#submit_top, #duplicate_button_top, #delete_button_top, #submit_bottom, #duplicate_button_bottom, #delete_button_bottom').attr('disabled', true);


			//info: get values for checkboxes
			if (document.getElementById('openpopup').checked) { var openpopup_prepare = '1'; } else { var openpopup_prepare = '0'; }
			if (document.getElementById('panel').checked) { var panel_prepare = '1'; } else { var panel_prepare = '0'; }
			if (document.getElementById('wms')) { if (document.getElementById('wms').checked) { var wms_prepare = '1'; } else { var wms_prepare = '0'; } } else { var wms_prepare = '0'; }
			if (document.getElementById('wms2')) { if (document.getElementById('wms2').checked) { var wms2_prepare = '1'; } else { var wms2_prepare = '0'; } } else { var wms2_prepare = '0'; }
			if (document.getElementById('wms3')) { if (document.getElementById('wms3').checked) { var wms3_prepare = '1'; } else { var wms3_prepare = '0'; } } else { var wms3_prepare = '0'; }
			if (document.getElementById('wms4')) { if (document.getElementById('wms4').checked) { var wms4_prepare = '1'; } else { var wms4_prepare = '0'; } } else { var wms4_prepare = '0'; }
			if (document.getElementById('wms5')) { if (document.getElementById('wms5').checked) { var wms5_prepare = '1'; } else { var wms5_prepare = '0'; } } else { var wms5_prepare = '0'; }
			if (document.getElementById('wms6')) { if (document.getElementById('wms6').checked) { var wms6_prepare = '1'; } else { var wms6_prepare = '0'; } } else { var wms6_prepare = '0'; }
			if (document.getElementById('wms7')) { if (document.getElementById('wms7').checked) { var wms7_prepare = '1'; } else { var wms7_prepare = '0'; } } else { var wms7_prepare = '0'; }
			if (document.getElementById('wms8')) { if (document.getElementById('wms8').checked) { var wms8_prepare = '1'; } else { var wms8_prepare = '0'; } } else { var wms8_prepare = '0'; }
			if (document.getElementById('wms9')) { if (document.getElementById('wms9').checked) { var wms9_prepare = '1'; } else { var wms9_prepare = '0'; } } else { var wms9_prepare = '0'; }
			if (document.getElementById('wms10')) { if (document.getElementById('wms10').checked) { var wms10_prepare = '1'; } else { var wms10_prepare = '0'; } } else { var wms10_prepare = '0'; }
			if (document.getElementById('gpx_panel').checked) { var gpx_panel_prepare = '1'; } else { var gpx_panel_prepare = '0'; }

			if ($('#action-marker-add-edit').val() === 'add') { var lmm_ajax_subaction_prepare = 'marker-add'; } else { var lmm_ajax_subaction_prepare = 'marker-edit'; }

			var data = {
				action: 'mapsmarker_ajax_actions_backend',
				lmm_ajax_subaction: lmm_ajax_subaction_prepare,
				lmm_ajax_nonce: lmm_ajax_vars.lmm_ajax_nonce,
				id: $('#id').val(),
				markername: $('#markername').val(),
				basemap: $('#basemap').val(),
				layer: $('#layer').val(),
				lat: $('#lat').val(),
				lon: $('#lon').val(),
				icon_hidden: $('#icon_hidden').val(),
				popuptext: lmm_get_tinymce_content(),
				zoom: $('#zoom').val(),
				openpopup: openpopup_prepare,
				mapwidth: $('#mapwidth').val(),
				mapwidthunit: $('input[name=mapwidthunit]:checked', '#marker-add-edit').val(),
				mapheight: $('#mapheight').val(),
				panel: panel_prepare,
				createdby: $('#createdby').val(),
				createdon: $('#createdon').val(),
				updatedby: $('#updatedby_next').val(),
				updatedon: $('#updatedon_next').val(),
				controlbox: $('input[name=controlbox]:checked', '#marker-add-edit').val(),
				overlays_custom: $('#overlays_custom').val(),
				overlays_custom2: $('#overlays_custom2').val(),
				overlays_custom3: $('#overlays_custom3').val(),
				overlays_custom4: $('#overlays_custom4').val(),
				wms: wms_prepare,
				wms2: wms2_prepare,
				wms3: wms3_prepare,
				wms4: wms4_prepare,
				wms5: wms5_prepare,
				wms6: wms6_prepare,
				wms7: wms7_prepare,
				wms8: wms8_prepare,
				wms9: wms9_prepare,
				wms10: wms10_prepare,
				kml_timestamp: $('#kml_timestamp').val(),
				address: $('#address').val(),
				gpx_url: $('#gpx_url').val(),
				gpx_panel: gpx_panel_prepare
			};

			$.post(ajaxurl, data, function (response) {
				var results = response.replace(/^\s*[\r\n]/gm, '');
				var results = results.match(/!!LMM-AJAX-START!!(.*[\s\S]*)!!LMM-AJAX-END!!/)[1];
				var results = JSON.parse(results);

				$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').attr('class',results['status-class']);
				$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').html(results['status-text']);
				$('#lmm_ajax_loading_top, #lmm_ajax_loading_bottom').hide();
				$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').show();
				$('#submit_top, #duplicate_button_top, #delete_button_top, #submit_bottom, #duplicate_button_bottom, #delete_button_bottom').attr('disabled', false);

				if(typeof results['newmarkerid'] != 'undefined'){
					var markerid = results['newmarkerid'];
				}else{
					var markerid = results['markerid'];
				}
				$('.wpml-markertranslatelink').show();
				if ($('#defaults_wpml_status').val() === 'wpml') {
					$('.wpml-markername a').attr( "href",  lmm_ajax_vars.lmm_ajax_admin_url + 'admin.php?page=wpml-string-translation%2Fmenu%2Fstring-translation.php&context=Maps+Marker+Pro&search=' + escape($('#markername').val().replace(/'/g, '\\\'').replace(/"/g, '\\\'')));
					$('.wpml-markeraddress a').attr( "href",  lmm_ajax_vars.lmm_ajax_admin_url + 'admin.php?page=wpml-string-translation%2Fmenu%2Fstring-translation.php&context=Maps+Marker+Pro&search=' + escape($('#address').val().replace(/'/g, '\\\'').replace(/"/g, '\\"')));
					$('.wpml-markerpopuptext a').attr( "href",  lmm_ajax_vars.lmm_ajax_admin_url + 'admin.php?page=wpml-string-translation%2Fmenu%2Fstring-translation.php&context=Maps+Marker+Pro&search=' + escape(lmm_get_tinymce_content().replace(/'/g, '\\\'').replace(/"/g, '\\"')));
				} else if ($('#defaults_wpml_status').val() === 'pll') {
					$('.wpml-markername a').attr( "href",  lmm_ajax_vars.lmm_ajax_admin_url + 'admin.php?page=mlang_strings&s=' + escape($('#markername').val().replace(/'/g, '\\\'').replace(/"/g, '\\\'')) + '&group=Maps+Marker+Pro');
					$('.wpml-markeraddress a').attr( "href",  lmm_ajax_vars.lmm_ajax_admin_url + 'admin.php?page=mlang_strings&s=' + escape($('#address').val().replace(/'/g, '\\\'').replace(/"/g, '\\"')) + '&group=Maps+Marker+Pro');
					$('.wpml-markerpopuptext a').attr( "href",  lmm_ajax_vars.lmm_ajax_admin_url + 'admin.php?page=mlang_strings&s=' + escape(lmm_get_tinymce_content().replace(/'/g, '\\\'').replace(/"/g, '\\"')) + '&group=Maps+Marker+Pro');
				} else {
					$('.wpml-markertranslatelink a').attr( "href", 'https://www.mapsmarker.com/multilingual');
				}
				//info: update direction links
				if ($('#defaults_directions_directions_provider').val() === 'googlemaps') {
					if ( $('#address').val() === '') {
						var google_from = $('#lat').val()+','+$('#lon').val();
					} else {
						var google_from = encodeURIComponent($('#address').val());
					}
					$('#popup-directions, #panel-link-directions').attr('href', 'https://'+$('#defaults_directions_gmaps_base_domain_directions').val()+'/maps?daddr='+google_from+'&t='+$('#defaults_directions_directions_googlemaps_map_type').val()+'&layer='+$('#defaults_directions_directions_googlemaps_traffic').val()+'&doflg='+$('#defaults_directions_directions_googlemaps_distance_units').val()+$('#defaults_directions_google_avoidhighways').val()+$('#defaults_directions_google_avoidtolls').val()+$('#defaults_directions_google_publictransport').val()+$('#defaults_directions_google_walking').val()+$('#defaults_directions_google_language').val()+'&om='+$('#defaults_directions_directions_directions_googlemaps_overview_map').val());
				} else if ($('#defaults_directions_directions_provider').val() === 'yours') {
					$('#popup-directions, #panel-link-directions').attr('href', 'http://www.yournavigation.org/?tlat='+$('#lat').val()+'&tlon='+$('#lon').val()+'&v='+$('#defaults_directions_directions_yours_type_of_transport').val()+'&fast='+$('#defaults_directions_directions_yours_route_type').val()+'&layer='+$('#defaults_directions_directions_yours_layer').val());
				} else if ($('#defaults_directions_directions_provider').val() === 'ors') {
					$('#popup-directions, #panel-link-directions').attr('href', 'http://www.openrouteservice.org/?pos='+$('#lon').val()+','+$('#lat').val()+'&wp='+$('#lon').val()+','+$('#lat').val()+'&zoom='+$('#zoom').val()+'&routeWeigh='+$('#defaults_directions_directions_ors_routeWeigh').val()+'&routeOpt='+$('#defaults_directions_directions_ors_routeOpt').val()+'&layer='+$('#defaults_directions_directions_ors_layer').val());
				} else if ($('#defaults_directions_directions_provider').val() === 'bingmaps') {
					if ( $('#address').val() === '') {
						var bing_to = '';
					} else {
						var bing_to = '_'+encodeURIComponent($('#address').val());
					}
					$('#popup-directions, #panel-link-directions').attr('href', 'http://www.bing.com/maps/default.aspx?v=2&rtp=pos___e_~pos.'+$('#lat').val()+'_'+$('#lon').val()+bing_to);
				}

				if ($('#action-marker-add-edit').val() === 'add') {
					if (results['status-class'] === 'notice notice-success') {
						if (history.pushState) { //info: not supported in IE8+9
							window.history.pushState(null, null, 'admin.php?page=leafletmapsmarker_marker&id='+results['newmarkerid']);
						}
						$('#lmm-header-button2').removeClass('button-primary lmm-nav-primary');
						$('#lmm-header-button2').addClass('button-secondary lmm-nav-secondary');
						$('#marker-heading').html(results['markername']+' (ID '+results['newmarkerid']+')');
						$('#duplicate_span_top, #delete_span_top, #duplicate_span_bottom, #delete_span_bottom').show();
						$('#id').val(results['newmarkerid']);
						$('#submit_top, #submit_bottom').val($('#defaults_texts_update').val());
						$('#action-marker-add-edit').val('edit');
						$('#tr-shortcode').show();
						$('#shortcode').val('['+shortcode+' marker="'+results['newmarkerid']+'"]');
						$('#shortcode-link-kml, #panel-link-kml').attr('href', site_url+lmm_slug+'/kml/marker/'+results['newmarkerid']+'/?markername='+lmm_ajax_vars.lmm_ajax_misc_kml);
						$('#shortcode-link-fullscreen, #panel-link-fullscreen').attr('href', site_url+lmm_slug+'/fullscreen/marker/'+results['newmarkerid']+'/');
						$('#shortcode-link-qr, #panel-link-qr').attr('href', site_url+lmm_slug+'/qr/marker/'+results['newmarkerid']+'/');
						$('#shortcode-link-geojson, #panel-link-geojson').attr('href', site_url+lmm_slug+'/geojson/marker/'+results['newmarkerid']+'/?callback=jsonp&full=yes&full_icon_url=yes');
						$('#shortcode-link-georss, #panel-link-georss').attr('href', site_url+lmm_slug+'/georss/marker/'+results['newmarkerid']+'/');
						$('#shortcode-link-wikitude, #panel-link-wikitude').attr('href', site_url+lmm_slug+'/wikitude/marker/'+results['newmarkerid']+'/');
						if (results['layerid'] != '0') {
							$('#layereditlink').show();
							$('#layereditlink-href').hide();
							$('#multilayeredit').html('');

							var layers = results['layerid'].split(',');
							var layers_length = layers.length;
							if(layers_length > 0){
								$('.layereditlink_wrap').show();
							}
							$.each(layers,function(index, value) {
								$('#multilayeredit').append('<a id="layereditlink-href" href="' + admin_url + 'admin.php?page=leafletmapsmarker_layer&id=' + value + '">'  + ' <span id="layereditlink-id">' + value + '</span></a>');
								if (index != layers_length - 1) {
									$('#multilayeredit').append(', ');
								}
							});
						} else {
							$('#layereditlink').hide();
						}
						if ($('#markername').val() === '') {
							$('#lmm-panel-text').html('&nbsp;');
						}
					}
				} else if ($('#action-marker-add-edit').val() === 'edit') {
					if (results['status-class'] === 'notice notice-success') {
						$('#marker-heading').html(results['markername']+' (ID '+results['markerid']+')');
						if (results['layerid'] != '0') {
							$('#layereditlink').show();
							$('#layereditlink-href').hide();
							$('#multilayeredit').html('');

							var layers = results['layerid'].split(',');
							var layers_length = layers.length;
							if(layers_length > 0){
								$('.layereditlink_wrap').show();
							}
							$.each(layers,function(index, value) {
								$('#multilayeredit').append('<a id="layereditlink-href" href="' + admin_url + 'admin.php?page=leafletmapsmarker_layer&id=' + value + '">'  + ' <span id="layereditlink-id">' + value + '</span></a>');
								if (index != layers_length - 1) {
									$('#multilayeredit').append(', ');
								}
							});
						} else {
							$('#layereditlink').hide();
						}
						$('#updatedby').val(results['updatedby_saved']);
						$('#updatedon').val(results['updatedon_saved']);
						$('#audit_visibility').show();
						$('#updatedby_next').val(results['updatedby_next']);
						$('#updatedon_next').val(results['updatedon_next']);
					}
				}
			});
			return false;
		});

		//info: marker delete
		$('#delete_button_top, #delete_button_bottom').click(function(e) {
			if (confirm(lmm_ajax_vars.lmm_ajax_confirm_delete)) {
				$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').hide();
				$('#lmm_ajax_loading_top, #lmm_ajax_loading_bottom').show();
				$('#submit_top, #duplicate_button_top, #delete_button_top, #submit_bottom, #duplicate_button_bottom, #delete_button_bottom').attr('disabled', true);

				var data = {
					action: 'mapsmarker_ajax_actions_backend',
					lmm_ajax_subaction: 'marker-delete',
					lmm_ajax_nonce: lmm_ajax_vars.lmm_ajax_nonce,
					id: $('#id').val()
				};

				$.post(ajaxurl, data, function (response) {
					var results = response.replace(/^\s*[\r\n]/gm, '');
					var results = results.match(/!!LMM-AJAX-START!!(.*[\s\S]*)!!LMM-AJAX-END!!/)[1];
					var results = JSON.parse(results);

					$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').attr('class',results['status-class']);
					$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').html(results['status-text']);
					$('#lmm_ajax_loading_top, #lmm_ajax_loading_bottom').hide();
					$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').show();
					$('#submit_top, #duplicate_button_top, #delete_button_top, #submit_bottom, #duplicate_button_bottom, #delete_button_bottom').attr('disabled', false);

					if (results['status-class'] === 'notice notice-success') {
						if (history.pushState) { //info: not supported in IE8+9
							window.history.pushState(null, null, 'admin.php?page=leafletmapsmarker_marker');
						}
						$('#div-marker-editor-hide-on-ajax-delete').hide();
						$('#duplicate_span_top, #delete_span_top, #duplicate_span_bottom, #delete_span_bottom').hide();
					}
				});
				return false;
			}
			return false;
		});

		//info: marker duplicate
		$('#duplicate_button_top, #duplicate_button_bottom').click(function(e) {
			$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').hide();
			$('#lmm_ajax_loading_top, #lmm_ajax_loading_bottom').show();
			$('#submit_top, #duplicate_button_top, #delete_button_top, #submit_bottom, #duplicate_button_bottom, #delete_button_bottom').attr('disabled', true);

			var data = {
				action: 'mapsmarker_ajax_actions_backend',
				lmm_ajax_subaction: 'marker-duplicate',
				lmm_ajax_nonce: lmm_ajax_vars.lmm_ajax_nonce,
				id: $('#id').val()
			};

			$.post(ajaxurl, data, function (response) {
				var results = response.replace(/^\s*[\r\n]/gm, '');
				var results = results.match(/!!LMM-AJAX-START!!(.*[\s\S]*)!!LMM-AJAX-END!!/)[1];
				var results = JSON.parse(results);

				$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').attr('class',results['status-class']);
				$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').html(results['status-text']);
				$('#lmm_ajax_loading_top, #lmm_ajax_loading_bottom').hide();
				$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').show();
				$('#submit_top, #duplicate_button_top, #delete_button_top, #submit_bottom, #duplicate_button_bottom, #delete_button_bottom').attr('disabled', false);

				if (results['status-class'] === 'notice notice-success') {
					if (history.pushState) { //info: not supported in IE8+9
						window.history.pushState(null, null, 'admin.php?page=leafletmapsmarker_marker&id='+results['newmarkerid']);
					}
					$('#marker-heading').html(results['markername']+' (ID '+results['newmarkerid']+')');
					$('#id').val(results['newmarkerid']);
					$('#shortcode').val('['+shortcode+' marker="'+results['newmarkerid']+'"]');
					$('#shortcode-link-kml, #panel-link-kml').attr('href', site_url+lmm_slug+'/kml/marker/'+results['newmarkerid']+'/?markername='+lmm_ajax_vars.lmm_ajax_misc_kml);
					$('#shortcode-link-fullscreen, #panel-link-fullscreen').attr('href', site_url+lmm_slug+'/fullscreen/marker/'+results['newmarkerid']+'/');
					$('#shortcode-link-qr, #panel-link-qr').attr('href', site_url+lmm_slug+'/qr/marker/'+results['newmarkerid']+'/');
					$('#shortcode-link-geojson, #panel-link-geojson').attr('href', site_url+lmm_slug+'/geojson/marker/'+results['newmarkerid']+'/?callback=jsonp&full=yes&full_icon_url=yes');
					$('#shortcode-link-georss, #panel-link-georss').attr('href', site_url+lmm_slug+'/georss/marker/'+results['newmarkerid']+'/');
					$('#shortcode-link-wikitude, #panel-link-wikitude').attr('href', site_url+lmm_slug+'/wikitude/marker/'+results['newmarkerid']+'/');
					$('#tr-usedincontent').hide();
				}
			});
			return false;
		});

		//info: marker editor switch link 1/2
		$('#switch-link-visible').click(function(e) {
			$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').hide();
			$('#lmm_ajax_loading_top, #lmm_ajax_loading_bottom').show();
			$('#switch-link-visible').toggle();
			$('#switch-link-hidden').toggle();
			var active_editor = $('#active_editor').val();
			if (active_editor == 'advanced') {
				$('#active_editor').val('simplified');
			} else {
				$('#active_editor').val('advanced');
			}
			$('#apilinkstext').show();
			$('#apilinks').hide();
			$('#toggle-google-settings').toggle();
			$('#toggle-popup-directions-settings').toggle();
			$('#toggle-coordinates').toggle();
			$('#toogle-global-maximum-zoom-level').toggle();
			$('#toggle-controlbox-panel-kmltimestamp-backlinks-minimaps').toggle();
			$('#mapiconscollection').toggle();
			$('#popup-image-css-info').toggle();
			$('#toogle-icons-simplified').toggle();
			$('#toogle-icons-advanced').toggle();
			$('#toggle-advanced-settings').toggle();
			$('#toggle-audit').toggle();

			var data = {
				action: 'mapsmarker_ajax_actions_backend',
				lmm_ajax_subaction: 'editor-switchlink',
				lmm_ajax_nonce: lmm_ajax_vars.lmm_ajax_nonce,
				active_editor: $('#active_editor').val()
			};

			$.post(ajaxurl, data, function (response) {
				var results = response.replace(/^\s*[\r\n]/gm, '');
				var results = results.match(/!!LMM-AJAX-START!!(.*[\s\S]*)!!LMM-AJAX-END!!/)[1];
				var results = JSON.parse(results);

				$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').attr('class',results['status-class']);
				$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').html(results['status-text']);
				$('#lmm_ajax_loading_top, #lmm_ajax_loading_bottom').hide();
				$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').show();
			});
			return false;
		});

		//info: marker editor switch link 2/2
		$('#switch-link-hidden').click(function(e) {
			$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').hide();
			$('#lmm_ajax_loading_top, #lmm_ajax_loading_bottom').show();
			$('#switch-link-visible').toggle();
			$('#switch-link-hidden').toggle();
			var active_editor = $('#active_editor').val();
			if (active_editor == 'advanced') {
				$('#active_editor').val('simplified');
			} else {
				$('#active_editor').val('advanced');
			}
			$('#apilinkstext').hide();
			$('#apilinks').show();
			$('#toggle-google-settings').toggle();
			$('#toggle-popup-directions-settings').toggle();
			$('#toggle-coordinates').toggle();
			$('#toogle-global-maximum-zoom-level').toggle();
			$('#toggle-controlbox-panel-kmltimestamp-backlinks-minimaps').toggle();
			$('#mapiconscollection').toggle();
			$('#popup-image-css-info').toggle();
			$('#toogle-icons-simplified').toggle();
			$('#toogle-icons-advanced').toggle();
			$('#toggle-advanced-settings').toggle();
			$('#toggle-audit').toggle();

			var data = {
				action: 'mapsmarker_ajax_actions_backend',
				lmm_ajax_subaction: 'editor-switchlink',
				lmm_ajax_nonce: lmm_ajax_vars.lmm_ajax_nonce,
				active_editor: $('#active_editor').val()
			};

			$.post(ajaxurl, data, function (response) {
				var results = response.replace(/^\s*[\r\n]/gm, '');
				var results = results.match(/!!LMM-AJAX-START!!(.*[\s\S]*)!!LMM-AJAX-END!!/)[1];
				var results = JSON.parse(results);

				$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').attr('class',results['status-class']);
				$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').html(results['status-text']);
				$('#lmm_ajax_loading_top, #lmm_ajax_loading_bottom').hide();
				$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').show();
			});
			return false;
		});

		//info: add new marker actions
		$('.menu-top.toplevel_page_leafletmapsmarker_markers.menu-top-last ul.wp-submenu.wp-submenu-wrap li.current a.current, #lmm-header-button2, #wp-admin-bar-lmm-add-marker').click(function(e) {
			if (history.pushState) { //info: not supported in IE8+9
				window.history.pushState(null, null, 'admin.php?page=leafletmapsmarker_marker');
			}
			if ($('#lmm-header-button2').hasClass('button-secondary')) {
				$('#lmm-header-button2').removeClass('button-secondary lmm-nav-secondary');
				$('#lmm-header-button2').addClass('button-primary lmm-nav-primary');
			}

			//info @since 2.5 hide preview layers link
			$('#preview_layers').hide();
			$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').hide();
			$('#div-marker-editor-hide-on-ajax-delete').show();
			$('#marker-heading').html($('#defaults_texts_add_new_marker').val());
			$('#submit_top, #submit_bottom').val($('#defaults_texts_publish').val());
			$('#duplicate_span_top, #delete_span_top, #duplicate_span_bottom, #delete_span_bottom').hide();
			$('#action-marker-add-edit').val('add');
			$('#tr-shortcode').hide();
			$('#tr-usedincontent').hide();
			if ( $('#defaults_wpml_status').val() === false) {
				$('.wpml-markertranslatelink').show();
				$('.wpml-markertranslatelink a').attr( "href", 'https://www.mapsmarker.com/multilingual');
			} else {
				$('.wpml-markertranslatelink').hide(); //info: dont show if not saved yet
			}
			//info: set form values
			$('#id').val('');
			$('#markername').val('');
			$('#lmm-panel-text').html($('#defaults_texts_panel_text').val());
			//info: unresolved $('#basemap').val($('#defaults_basemap').val());
			if($('#defaults_layer').val() == "0"){
				$('#layer').select2('val', '');
			}else{
				var defaults_layers =  $('#defaults_layer').val();
				defaults_layers = defaults_layers.split(',');
				$('#layer').select2('val', defaults_layers);
			}
			$('#layereditlink').hide();
			$('.layereditlink_wrap').hide();
			$('#layeraddlink').show();
			$('#lat').val($('#defaults_lat').val());
			$('#lon').val($('#defaults_lon').val());
			$('.div-marker-icon').css('background','none');
			$('.div-marker-icon').css('opacity','0.4');
			marker.setIcon(new L.Icon({iconUrl: $('#defaults_marker_icon_url').val(),iconSize: [$('#defaults_marker_icon_iconsize_x').val()+','+$('#defaults_marker_icon_iconsize_y').val()],iconAnchor: [$('#defaults_marker_icon_iconanchor_x').val()+','+$('#defaults_marker_icon_iconanchor_y').val()],popupAnchor: [$('#defaults_marker_icon_popupanchor_x').val()+','+$('#defaults_marker_icon_popupanchor_y').val()],shadowUrl: $('#defaults_marker_icon_shadow_url').val(),shadowSize: [$('#defaults_marker_icon_shadowsize_x').val()+','+$('#defaults_marker_icon_shadowsize_y').val()],shadowAnchor: [$('#defaults_marker_icon_shadowanchor_x').val()+','+$('#defaults_marker_icon_shadowanchor_y').val()],className: $('#defaults_icon_className').val()}));
			marker.setLatLng( new L.LatLng($('#defaults_lat').val(), $('#defaults_lon').val()));
			$('.div-marker-icon-default').css('opacity','1');
			$('.div-marker-icon-default').css('background','#5e5d5d');
			$('#icon_hidden').val($('#defaults_icon').val());
			if ($('#wp-popuptext-wrap').hasClass('tmce-active')) {
				tinymce.get('popuptext').setContent('');
			} else {
				$('#popuptext').val('');
			}
 			$('html, body').animate({ scrollTop: 0 }, 'fast'); //info: workaround for tinyMCE focus
			$('#selectlayer-popuptext-hidden').val('');
			$('#zoom').val($('#defaults_zoom').val());
			if ($('#defaults_openpopup').val() === '0') { $('input:checkbox[name=openpopup]').attr('checked',false); } else { $('input:checkbox[name=openpopup]').attr('checked',true); }
			$('#mapwidth').val($('#defaults_mapwidth').val());
			if ($('#defaults_mapwidthunit').val() === 'px') { $('input:radio[id=mapwidthunit_px]')[0].checked = true; } else { $('input:radio[id=mapwidthunit_percent]')[0].checked = true; }
			$('#mapheight').val($('#defaults_mapheight').val());
			if ($('#defaults_panel').val() === '0') { $('input:checkbox[name=panel]').attr('checked',false); } else { $('input:checkbox[name=panel]').attr('checked',true); }
			$('#createdby').val($('#updatedby_next').val());
			$('#createdon').val($('#updatedon_next').val());
			$('#audit_visibility').hide();
			$('#updatedby').val($('#updatedby_next').val());
			$('#updatedon').val($('#updatedon_next').val());
			if ($('#defaults_controlbox').val() === '0') {
				$('input:radio[id=controlbox_hidden]')[0].checked = true;
			} else if ($('#defaults_controlbox').val() === '1') {
				$('input:radio[id=controlbox_collapsed]')[0].checked = true;
			} else if ($('#defaults_controlbox').val() === '2') {
				$('input:radio[id=controlbox_expanded]')[0].checked = true;
			}
			$('#overlays_custom').val($('#defaults_overlays_custom').val());
			$('#overlays_custom2').val($('#defaults_overlays_custom2').val());
			$('#overlays_custom3').val($('#defaults_overlays_custom3').val());
			$('#overlays_custom4').val($('#defaults_overlays_custom4').val());
			if ($('#defaults_wms').val() === '0') { $('input:checkbox[name=wms]').attr('checked',false); } else { $('input:checkbox[name=wms]').attr('checked',true); }
			if ($('#defaults_wms2').val() === '0') { $('input:checkbox[name=wms2]').attr('checked',false); } else { $('input:checkbox[name=wms2]').attr('checked',true); }
			if ($('#defaults_wms3').val() === '0') { $('input:checkbox[name=wms3]').attr('checked',false); } else { $('input:checkbox[name=wms3]').attr('checked',true); }
			if ($('#defaults_wms4').val() === '0') { $('input:checkbox[name=wms4]').attr('checked',false); } else { $('input:checkbox[name=wms4]').attr('checked',true); }
			if ($('#defaults_wms5').val() === '0') { $('input:checkbox[name=wms5]').attr('checked',false); } else { $('input:checkbox[name=wms5]').attr('checked',true); }
			if ($('#defaults_wms6').val() === '0') { $('input:checkbox[name=wms6]').attr('checked',false); } else { $('input:checkbox[name=wms6]').attr('checked',true); }
			if ($('#defaults_wms7').val() === '0') { $('input:checkbox[name=wms7]').attr('checked',false); } else { $('input:checkbox[name=wms7]').attr('checked',true); }
			if ($('#defaults_wms8').val() === '0') { $('input:checkbox[name=wms8]').attr('checked',false); } else { $('input:checkbox[name=wms8]').attr('checked',true); }
			if ($('#defaults_wms9').val() === '0') { $('input:checkbox[name=wms9]').attr('checked',false); } else { $('input:checkbox[name=wms9]').attr('checked',true); }
			if ($('#defaults_wms10').val() === '0') { $('input:checkbox[name=wms10]').attr('checked',false); } else { $('input:checkbox[name=wms10]').attr('checked',true); }
			$('#address').val('');
			$('#popup-address').html($('#defaults_texts_directions_link_new_marker').val());
			marker.setPopupContent($('#defaults_texts_directions_link_new_marker').val());
			if ($('#gpx_url').val() !== '') {
				$('#gpx_url').val('');
				//info: workaround as removeLayer did not work
				$('.leaflet-overlay-pane').html('');
				$('.lmm_gpx_icons').hide();
			}
			$('#gpx_fitbounds_link').hide();
			$('#gpx-panel-selectlayer').hide();
			$('input:checkbox[name=gpx_panel]').attr('checked',false);
			//info: reset leaflet map; do not change to default basemap due to unresolved issues :-/
			$('#lmm').css('width',$('#defaults_mapwidth').val()+$('#defaults_mapwidthunit').val());
			$('#selectlayer').css('height',$('#defaults_mapheight').val());
			selectlayer.invalidateSize();
			selectlayer.setView(new L.LatLng($('#defaults_lat').val(), $('#defaults_lon').val()), $('#defaults_zoom').val());
			if ($('#defaults_controlbox').val() === '0') {
				$('.leaflet-control-layers').hide();
			} else if ($('#defaults_controlbox').val() === '1') {
				$('.leaflet-control-layers').show();
				layersControl._collapse();
			} else if ($('#defaults_controlbox').val() === '2') {
				$('.leaflet-control-layers').show();
				layersControl._expand();
			}
			if ($('#defaults_panel').val() === '0') { $('#lmm-panel').css('display','none'); } else { $('#lmm-panel').css('display','block'); }
			if ($('#defaults_openpopup').val() === '0') { marker.closePopup(); } else { marker.openPopup(); }
			//info: reset wms
			if (selectlayer.hasLayer(wms)) { selectlayer.removeLayer(wms); }
			if (selectlayer.hasLayer(wms2)) { selectlayer.removeLayer(wms2); }
			if (selectlayer.hasLayer(wms3)) { selectlayer.removeLayer(wms3); }
			if (selectlayer.hasLayer(wms4)) { selectlayer.removeLayer(wms4); }
			if (selectlayer.hasLayer(wms5)) { selectlayer.removeLayer(wms5); }
			if (selectlayer.hasLayer(wms6)) { selectlayer.removeLayer(wms6); }
			if (selectlayer.hasLayer(wms7)) { selectlayer.removeLayer(wms7); }
			if (selectlayer.hasLayer(wms8)) { selectlayer.removeLayer(wms8); }
			if (selectlayer.hasLayer(wms9)) { selectlayer.removeLayer(wms9); }
			if (selectlayer.hasLayer(wms10)) { selectlayer.removeLayer(wms10); }
			$('#kml_timestamp').val('');
			//info: set default icon
			$('.div-marker-icon').css('background','none');
			$('.div-marker-icon').css('opacity','0.4');
			marker.setIcon(new L.Icon({iconUrl: $('#defaults_marker_icon_url').val(),iconSize: [$('#defaults_marker_icon_iconsize_x').val()+','+$('#defaults_marker_icon_iconsize_y').val()],iconAnchor: [$('#defaults_marker_icon_iconanchor_x').val()+','+$('#defaults_marker_icon_iconanchor_y').val()],popupAnchor: [$('#defaults_marker_icon_popupanchor_x').val()+','+$('#defaults_marker_icon_popupanchor_y').val()],shadowUrl: $('#defaults_marker_icon_shadow_url').val(),shadowSize: [$('#defaults_marker_icon_shadowsize_x').val()+','+$('#defaults_marker_icon_shadowsize_y').val()],shadowAnchor: [$('#defaults_marker_icon_shadowanchor_x').val()+','+$('#defaults_marker_icon_shadowanchor_y').val()],className: $('#defaults_icon_className').val()}));
			var icon_opacity_selector = $('#defaults_icon_opacity_selector').val();
			$(icon_opacity_selector).css('opacity','1');
			$(icon_opacity_selector).css('background','#5e5d5d');
			//info: reset panel api links
			$('#popup-directions, #panel-link-directions').attr('href', 'https://maps.google.com/maps?daddr='+$('#defaults_lat').val()+','+$('#defaults_lon').val()+'&t=m&layer=1&doflg=ptk&om=0');
			$('#panel-link-kml').attr('href', site_url+lmm_slug+'/kml/marker/?markername='+lmm_ajax_vars.lmm_ajax_misc_kml);
			$('#panel-link-fullscreen').attr('href', site_url+lmm_slug+'/fullscreen/marker/');
			$('#panel-link-qr').attr('href', site_url+lmm_slug+'/qr/marker/');
			$('#panel-link-geojson').attr('href', site_url+lmm_slug+'/geojson/marker/?callback=jsonp&full=yes&full_icon_url=yes');
			$('#panel-link-georss').attr('href', site_url+lmm_slug+'/georss/marker/');
			$('#panel-link-wikitude').attr('href', site_url+lmm_slug+'/wikitude/marker/');
			//info: clear markers from layer preview
			$.each(selectlayer._layers, function (e,i) {
				if(selectlayer._layers[e]._icon){
					selectlayer.removeLayer(selectlayer._layers[e]);
				}
			});
			marker.addTo(selectlayer);
		});
		//info: "preview all marker" link on marker & layer edit pages
		var toggle_preview = false;
		$("#preview_layers").on("click", function(e){

			if(toggle_preview == true){
				//info: clear markers from layer preview
				$.each(selectlayer._layers, function (e,i) {
					if(selectlayer._layers[e]._icon){
						selectlayer.removeLayer(selectlayer._layers[e]);
					}
				});
				marker.addTo(selectlayer);
				$('#preview_layers_text').html($('#defaults_texts_show_preview').val());
				$('#preview_layers_icon').attr('src', $('#defaults_texts_hide_preview_icon').val());
				toggle_preview = false;
			}else{
				var values = $('#layer').select2("val");
				if(values.length > 0){
					if(e.val !== "0"){
						//info: clear markers from layer preview
						$.each(selectlayer._layers, function (e,i) {
							if(selectlayer._layers[e]._icon){
								selectlayer.removeLayer(selectlayer._layers[e]);
							}
						});
						marker.addTo(selectlayer);
						//info: prepare icon vars for loop (not working by directly fetching values via $(...).val();
						var prepare_default_marker_icon_iconsize_x = $('#defaults_marker_icon_iconsize_x').val();
						var prepare_default_marker_icon_iconsize_y = $('#defaults_marker_icon_iconsize_y').val();
						var prepare_defaults_marker_icon_iconanchor_x = $('#defaults_marker_icon_iconanchor_x').val();
						var prepare_defaults_marker_icon_iconanchor_y = $('#defaults_marker_icon_iconanchor_y').val();
						var prepare_defaults_marker_icon_popupanchor_x = $('#defaults_marker_icon_popupanchor_x').val();
						var prepare_defaults_marker_icon_popupanchor_y = $('#defaults_marker_icon_popupanchor_y').val();
						var prepare_defaults_marker_icon_shadow_url = $('#defaults_marker_icon_shadow_url').val();
						var prepare_defaults_marker_icon_shadowsize_x = $('#defaults_marker_icon_shadowsize_x').val();
						var prepare_defaults_marker_icon_shadowsize_y = $('#defaults_marker_icon_shadowsize_y').val();
						var prepare_defaults_marker_icon_shadowanchor_x = $('#defaults_marker_icon_shadowanchor_x').val();
						var prepare_defaults_marker_icon_shadowanchor_y = $('#defaults_marker_icon_shadowanchor_y').val();
						//info: prepare popup+default vars for loop (not working by directly fetching values via $(...).val();
						var prepare_defaults_marker_popups_maxWidth = $('#defaults_marker_popups_maxWidth').val();
						var prepare_defaults_marker_popups_minWidth = $('#defaults_marker_popups_minWidth').val();
						var prepare_defaults_marker_popups_maxHeight = $('#defaults_marker_popups_maxHeight').val();
						var prepare_defaults_marker_popups_autoPan = $('#defaults_marker_popups_autoPan').val();
						var prepare_defaults_marker_popups_closeButton = $('#defaults_marker_popups_closeButton').val();
						var prepare_defaults_marker_popups_autopanpadding_x = $('#defaults_marker_popups_autopanpadding_x').val();
						var prepare_defaults_marker_popups_autopanpadding_y = $('#defaults_marker_popups_autopanpadding_y').val();
						var prepare_defaults_marker_popups_add_markername = $('#defaults_marker_popups_add_markername').val();
						var prepare_defaults_directions_popuptext_panel = $('#defaults_directions_popuptext_panel').val();


						var xhReq = new XMLHttpRequest();
						xhReq.open("GET", site_url+lmm_slug+'/geojson/layer/'+values.join(',')+'/', true);
						xhReq.onreadystatechange = function (e) { if (xhReq.readyState === 4) { if (xhReq.status === 200) { //info: async 1/2
							//info: check if WP DEBUG or other additional on-screen warnings or errors brake GeoJSON array validity
							if (xhReq.responseText.indexOf('{"type"') != 0) {
								var position = xhReq.responseText.indexOf('{"type"');
								var response = JSON.parse(xhReq.responseText.slice(position));
							} else {
								var response = JSON.parse(xhReq.responseText);
							}
							if(response.features){
								$.each(response.features, function(id, marker_item){

									var mlm_marker = new L.Marker( new L.LatLng(marker_item.geometry.coordinates[1], marker_item.geometry.coordinates[0]),
									{
										markerid: marker_item.properties.markerid,
										title: marker_item.properties.markername,
										interactive: true,
										draggable: false,
										zIndexOffset: 1,
										opacity: 0.5
									});

									if (prepare_defaults_marker_popups_add_markername === 'true') {
										if (marker_item.properties.markername != "") {
											var divmarkername1 = '<div class="popup-markername" style="border-bottom:1px solid #f0f0e7;padding-bottom:5px;margin-bottom:6px;">';
											var divmarkername2 = '</div>';
											var prepare_popups_add_markername =  marker_item.properties.markername;
										} else {
											var divmarkername1 = '';
											var divmarkername2 = '';
											var prepare_popups_add_markername =  '';
										}
									} else {
										var divmarkername1 = '';
										var divmarkername2 = '';
										var prepare_popups_add_markername =  '';
									}
									if (prepare_defaults_directions_popuptext_panel === 'yes') {
										if (marker_item.properties.text != '') { var css = 'border-top:1px solid #f0f0e7;padding-top:5px;margin-top:5px;clear:both;'; } else { var css = ''; }
										var prepare_direction_popuptext_panel = '<div class="popup-directions" style="'+css+'">'+marker_item.properties.address+' (<a href="'+ marker_item.properties.dlink +'" target="_blank" title="'+ lmm_ajax_vars.lmm_get_directions_text +'">'+ lmm_ajax_vars.lmm_directions_text +'</a>)</div>';
									} else {
										var prepare_direction_popuptext_panel = '';
									}
									mlm_marker.bindPopup("<img id=\"popup-loading-"+marker_item.properties.markerid+"\" style=\"display: none; margin: 20px auto;\" src=\""+lmm_ajax_vars.lmm_ajax_leaflet_plugin_url+"inc/img/paging-ajax-loader.gif\" /><div id=\"popup-content-"+marker_item.properties.markerid+"\">"+divmarkername1+prepare_popups_add_markername+divmarkername2+marker_item.properties.text+prepare_direction_popuptext_panel+"</div>",
									{
										maxWidth: prepare_defaults_marker_popups_maxWidth,
										minWidth: prepare_defaults_marker_popups_minWidth,
										maxHeight: prepare_defaults_marker_popups_maxHeight,
										autoPan: prepare_defaults_marker_popups_autoPan,
										closeButton: prepare_defaults_marker_popups_closeButton,
										autoPanPadding: new L.Point(prepare_defaults_marker_popups_autopanpadding_x+','+prepare_defaults_marker_popups_autopanpadding_y)
									});

									mlm_marker.options.icon = new L.Icon(
									{
										iconUrl: (marker_item.properties.icon != '') ? defaults_marker_icon_url+'/'+ marker_item.properties.icon : leaflet_plugin_url+'leaflet-dist/images/marker.png',
										iconSize: [prepare_default_marker_icon_iconsize_x,prepare_default_marker_icon_iconsize_y],
										iconAnchor: [prepare_defaults_marker_icon_iconanchor_x,prepare_defaults_marker_icon_iconanchor_y],
										popupAnchor: [prepare_defaults_marker_icon_popupanchor_x,prepare_defaults_marker_icon_popupanchor_y],
										shadowUrl: prepare_defaults_marker_icon_shadow_url,
										shadowSize: [prepare_defaults_marker_icon_shadowsize_x,prepare_defaults_marker_icon_shadowsize_y],
										shadowAnchor: [prepare_defaults_marker_icon_shadowanchor_x,prepare_defaults_marker_icon_shadowanchor_y],
										className: (marker_item.properties.icon != '') ? 'lmm_marker_icon_'+marker_item.properties.icon.substr(0,-4) : 'lmm_marker_icon_default'
									});
									mlm_marker.addTo(selectlayer);
								});
							}
						} else { if (window.console) { console.error(xhReq.statusText); } } } }; xhReq.onerror = function (e) { if (window.console) { console.error(xhReq.statusText); } }; xhReq.send(null); //info: async 2/2
					}
				}else{
					$.each(selectlayer._layers, function (e,i) {
						if(selectlayer._layers[e]._icon){
							selectlayer.removeLayer(selectlayer._layers[e]);
						}
					});
					marker.addTo(selectlayer);
				}
				$('#preview_layers_text').html($('#defaults_texts_hide_preview').val());
				$('#preview_layers_icon').attr('src', $('#defaults_texts_show_preview_icon').val());
				toggle_preview = true;
			}
		});
	 //info: end "current_page === 'leafletmapsmarker_layer'"

	//info: js for layer edit page
	} else if (current_page === 'leafletmapsmarker_layer') {

		if(!$('#id').val()) {
			$('.hide_on_new').hide(); //info: hide buttons
			$('.wpml-layertranslatelink').hide();
		}

		//info: disable "name" and "icon"
		var filter_name_value = [];
		var filter_icon_value = [];
		var filter_status_value = [];
		$("input[type=checkbox][name^=mlm]").each(function(){
			if(jQuery(this).attr('id')!='mlm-all'){
				var layer_id = $(this).attr('id').replace('mlm-','');
				filter_name_value[layer_id] = $('#mlm_filter_name_' + layer_id).val();
				filter_icon_value[layer_id] = $('#mlm_filter_icon_' + layer_id).val();
				filter_status_value[layer_id] = $('#mlm_filter_status_' + layer_id).val();
			}
		});
		$('.mlm_filter_status').change(function(e){
			var layer_id = $(this).attr('data-layerid');
			if($(this).val() == '0'){
				$('#mlm_filter_name_' + layer_id).attr('disabled', 'disabled');
				$('#mlm_filter_icon_' + layer_id).attr('disabled', 'disabled');
				filter_name_value[layer_id] = $('#mlm_filter_name_' + layer_id).val();
				filter_icon_value[layer_id] = $('#mlm_filter_icon_' + layer_id).val();
				$('#mlm_filter_name_' + layer_id).val('');
				$('#mlm_filter_icon_' + layer_id).val('');
			}else{
				$('#mlm_filter_name_' + layer_id).removeAttr('disabled');
				$('#mlm_filter_icon_' + layer_id).removeAttr('disabled');
				if(filter_status_value[layer_id] != '0'){
					filter_name_value[layer_id] = $('#mlm_filter_name_' + layer_id).val();
					filter_icon_value[layer_id] = $('#mlm_filter_icon_' + layer_id).val();
				}
				$('#mlm_filter_name_' + layer_id).val(filter_name_value[layer_id]);
				$('#mlm_filter_icon_' + layer_id).val(filter_icon_value[layer_id]);
				if(filter_name_value[layer_id] == ''){
					$('#mlm_filter_name_' + layer_id).val( $('#mlm_filter_name_' + layer_id).attr('data-default') );
				}
				if($(this).val() == '1'){
					$('#mlm-' + layer_id).attr('checked', 'checked');
				}else if($(this).val() == '2'){
					$('#mlm-' + layer_id).removeAttr('checked');
				}
			}
			filter_status_value[layer_id] = $(this).val();
		});
		$('.mlm_filter_status').trigger('change');
		/************************************/
		//info: 1 submit function for layer add & edit
		$('#layer-add-edit').submit(function() {

			$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').hide();
			$('#lmm_ajax_loading_top, #lmm_ajax_loading_bottom').show();
			$('#submit_top, #duplicate_button_top, #delete_button_top, #submit_bottom, #duplicate_button_bottom, #delete_button_bottom').attr('disabled', true);

			//info: get values for checkboxes
			if (document.getElementById('listmarkers').checked) { var listmarkers = '1'; } else { var listmarkers = '0'; }
			if (document.getElementById('clustering').checked) { var clustering = '1'; } else { var clustering = '0'; }
			if (document.getElementById('panel').checked) { var panel = '1'; } else { var panel = '0'; }
			if (document.getElementById('multi_layer_map').checked) { var multi_layer_map = '1'; } else { var multi_layer_map = '0'; }
			if (document.getElementById('mlm-all').checked) { var mlmall = '1'; } else { var mlmall = '0'; }

			/** Multi-Layers **/
			var layers = '';
			var filters_details = {};
			$("input[type=checkbox][name^=mlm]").each(function(){
				if(jQuery(this).attr('id')!='mlm-all'){
					var layer_id = $(this).attr('id').replace('mlm-','');
					if(document.getElementById(jQuery(this).attr('id')).checked) {
						layers += jQuery(this).attr('id')+',';
					}
					filters_details['mlm_filter_status_' + layer_id] = $('#mlm_filter_status_' + layer_id).val();
					filters_details['mlm_filter_name_'   + layer_id] = ($('#mlm_filter_name_' + layer_id).val()!='')?$('#mlm_filter_name_' + layer_id).val():$('#mlm_filter_name_' + layer_id).attr('data-default');
					filters_details['mlm_filter_icon_'   + layer_id] = $('#mlm_filter_icon_' + layer_id).val();
				}
			});

			if (document.getElementById('wms')) { if (document.getElementById('wms').checked) { var wms_prepare = '1'; } else { var wms_prepare = '0'; } } else { var wms_prepare = '0'; }
			if (document.getElementById('wms2')) { if (document.getElementById('wms2').checked) { var wms2_prepare = '1'; } else { var wms2_prepare = '0'; } } else { var wms2_prepare = '0'; }
			if (document.getElementById('wms3')) { if (document.getElementById('wms3').checked) { var wms3_prepare = '1'; } else { var wms3_prepare = '0'; } } else { var wms3_prepare = '0'; }
			if (document.getElementById('wms4')) { if (document.getElementById('wms4').checked) { var wms4_prepare = '1'; } else { var wms4_prepare = '0'; } } else { var wms4_prepare = '0'; }
			if (document.getElementById('wms5')) { if (document.getElementById('wms5').checked) { var wms5_prepare = '1'; } else { var wms5_prepare = '0'; } } else { var wms5_prepare = '0'; }
			if (document.getElementById('wms6')) { if (document.getElementById('wms6').checked) { var wms6_prepare = '1'; } else { var wms6_prepare = '0'; } } else { var wms6_prepare = '0'; }
			if (document.getElementById('wms7')) { if (document.getElementById('wms7').checked) { var wms7_prepare = '1'; } else { var wms7_prepare = '0'; } } else { var wms7_prepare = '0'; }
			if (document.getElementById('wms8')) { if (document.getElementById('wms8').checked) { var wms8_prepare = '1'; } else { var wms8_prepare = '0'; } } else { var wms8_prepare = '0'; }
			if (document.getElementById('wms9')) { if (document.getElementById('wms9').checked) { var wms9_prepare = '1'; } else { var wms9_prepare = '0'; } } else { var wms9_prepare = '0'; }
			if (document.getElementById('wms10')) { if (document.getElementById('wms10').checked) { var wms10_prepare = '1'; } else { var wms10_prepare = '0'; } } else { var wms10_prepare = '0'; }
			if (document.getElementById('gpx_panel').checked) { var gpx_panel_prepare = '1'; } else { var gpx_panel_prepare = '0'; }

			if ($('#action-layer-add-edit').val() === 'add') { var lmm_ajax_subaction_prepare = 'layer-add'; } else { var lmm_ajax_subaction_prepare = 'layer-edit'; }

			var data = {
				action: 'mapsmarker_ajax_actions_backend',
				lmm_ajax_subaction: lmm_ajax_subaction_prepare,
				lmm_ajax_nonce: lmm_ajax_vars.lmm_ajax_nonce,
				id: $('#id').val(),
				name: $('#layername').val(),
				basemap: $('#basemap').val(),
				layerviewlon: $('#layerviewlon').val(),
				layerviewlat: $('#layerviewlat').val(),
				layerzoom: $('#layerzoom').val(),
				mapwidth: $('#mapwidth').val(),
				mapwidthunit: $('input[name=mapwidthunit]:checked', '#layer-add-edit').val(),
				mapheight: $('#mapheight').val(),
				panel: panel,
				createdby: $('#createdby').val(),
				createdon: $('#createdon').val(),
				updatedby: $('#updatedby_next').val(),
				updatedon: $('#updatedon_next').val(),
				controlbox: $('input[name=controlbox]:checked', '#layer-add-edit').val(),
				overlays_custom: $('#overlays_custom').val(),
				overlays_custom2: $('#overlays_custom2').val(),
				overlays_custom3: $('#overlays_custom3').val(),
				overlays_custom4: $('#overlays_custom4').val(),
				wms: wms_prepare,
				wms2: wms2_prepare,
				wms3: wms3_prepare,
				wms4: wms4_prepare,
				wms5: wms5_prepare,
				wms6: wms6_prepare,
				wms7: wms7_prepare,
				wms8: wms8_prepare,
				wms9: wms9_prepare,
				wms10: wms10_prepare,
				kml_timestamp: $('#kml_timestamp').val(),
				address: $('#address').val(),
				gpx_url: $('#gpx_url').val(),
				gpx_panel: gpx_panel_prepare,
				listmarkers:listmarkers,
				clustering:clustering,
				panel:panel,
				multi_layer_map:multi_layer_map,
				mlmall:mlmall,
				mlmlayers:layers,
				controlbox_mlm_filter: $('input[name=controlbox_mlm_filter]:checked').val()
			};

			var data = $.extend(data, filters_details);
			$.post(ajaxurl, data, function (response) {
				var results = response.replace(/^\s*[\r\n]/gm, '');
				var results = results.match(/!!LMM-AJAX-START!!(.*[\s\S]*)!!LMM-AJAX-END!!/)[1];
				var results = JSON.parse(results);

				$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').attr('class',results['status-class']);
				$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').html(results['status-text']);
				$('#lmm_ajax_loading_top, #lmm_ajax_loading_bottom').hide();
				$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').show();
				$('#submit_top, #duplicate_button_top, #delete_button_top, #submit_bottom, #duplicate_button_bottom, #delete_button_bottom').attr('disabled', false);
				$('.hide_on_new').show();
				$('.advanced-layer-edit-button').hide();
				var new_layer_id = ($('#action-layer-add-edit').val() === 'add')?results['newlayerid']:results['layerid'];
				$('.addmarker_link').attr('href',admin_url + 'admin.php?page=leafletmapsmarker_marker&addtoLayer=' + new_layer_id + '&lat=' + $('#layerviewlat').val() + '&lon=' + $('#layerviewlon').val() + '&zoom=' + $('#layerzoom').val());
				$('.btns_layer_id').val(new_layer_id);
				$('.wpml-layertranslatelink').show();
				if ($('#defaults_wpml_status').val() === 'wpml') {
					$('.wpml-layername a').attr( "href",  lmm_ajax_vars.lmm_ajax_admin_url + 'admin.php?page=wpml-string-translation%2Fmenu%2Fstring-translation.php&context=Maps+Marker+Pro&search=' + escape($('#layername').val().replace(/'/g, '\\\'').replace(/"/g, '\\\'')));
					$('.wpml-layeraddress a').attr( "href",  lmm_ajax_vars.lmm_ajax_admin_url + 'admin.php?page=wpml-string-translation%2Fmenu%2Fstring-translation.php&context=Maps+Marker+Pro&search=' + escape($('#address').val().replace(/'/g, '\\\'').replace(/"/g, '\\"')));
				} else if ($('#defaults_wpml_status').val() === 'pll') {
					$('.wpml-layername a').attr( "href",  lmm_ajax_vars.lmm_ajax_admin_url + 'admin.php?page=mlang_strings&s=' + escape($('#layername').val().replace(/'/g, '\\\'').replace(/"/g, '\\\'')) + '&group=Maps+Marker+Pro');
					$('.wpml-layeraddress a').attr( "href",  lmm_ajax_vars.lmm_ajax_admin_url + 'admin.php?page=mlang_strings&s=' + escape($('#address').val().replace(/'/g, '\\\'').replace(/"/g, '\\"')) + '&group=Maps+Marker+Pro');
				} else {
					$('.wpml-markertranslatelink a').attr( "href", 'https://www.mapsmarker.com/multilingual');
				}
				//info: update direction links
				if ($('#defaults_directions_directions_provider').val() === 'googlemaps') {
					if ( $('#address').val() === '') {
						var google_from = $('#lat').val()+','+$('#lon').val();
					} else {
						var google_from = encodeURIComponent($('#address').val());
					}
					$('#popup-directions, #panel-link-directions').attr('href', 'https://'+$('#defaults_directions_gmaps_base_domain_directions').val()+'/maps?daddr='+google_from+'&t='+$('#defaults_directions_directions_googlemaps_map_type').val()+'&layer='+$('#defaults_directions_directions_googlemaps_traffic').val()+'&doflg='+$('#defaults_directions_directions_googlemaps_distance_units').val()+$('#defaults_directions_google_avoidhighways').val()+$('#defaults_directions_google_avoidtolls').val()+$('#defaults_directions_google_publictransport').val()+$('#defaults_directions_google_walking').val()+$('#defaults_directions_google_language').val()+'&om='+$('#defaults_directions_directions_directions_googlemaps_overview_map').val());
				} else if ($('#defaults_directions_directions_provider').val() === 'yours') {
					$('#popup-directions, #panel-link-directions').attr('href', 'http://www.yournavigation.org/?tlat='+$('#lat').val()+'&tlon='+$('#lon').val()+'&v='+$('#defaults_directions_directions_yours_type_of_transport').val()+'&fast='+$('#defaults_directions_directions_yours_route_type').val()+'&layer='+$('#defaults_directions_directions_yours_layer').val());
				} else if ($('#defaults_directions_directions_provider').val() === 'ors') {
					$('#popup-directions, #panel-link-directions').attr('href', 'http://www.openrouteservice.org/?pos='+$('#lon').val()+','+$('#lat').val()+'&wp='+$('#lon').val()+','+$('#lat').val()+'&zoom='+$('#zoom').val()+'&routeWeigh='+$('#defaults_directions_directions_ors_routeWeigh').val()+'&routeOpt='+$('#defaults_directions_directions_ors_routeOpt').val()+'&layer='+$('#defaults_directions_directions_ors_layer').val());
				} else if ($('#defaults_directions_directions_provider').val() === 'bingmaps') {
					if ( $('#address').val() === '') {
						var bing_to = '';
					} else {
						var bing_to = '_'+encodeURIComponent($('#address').val());
					}
					$('#popup-directions, #panel-link-directions').attr('href', 'http://www.bing.com/maps/default.aspx?v=2&rtp=pos___e_~pos.'+$('#lat').val()+'_'+$('#lon').val()+bing_to);
				}
				if($('input:checkbox[name=multi_layer_map]').is(':checked')) {
					$('.button-add-new-marker-to-this-layer, .addmarker_link').hide();
				} else {
					$('.button-add-new-marker-to-this-layer, .addmarker_link').show();
				}

				if ($('#action-layer-add-edit').val() === 'add') {
					if (results['status-class'] === 'notice notice-success') {
						if (history.pushState) { //info: not supported in IE8+9
							window.history.pushState(null, null, 'admin.php?page=leafletmapsmarker_layer&id='+results['newlayerid']);
						}
						$('#lmm-header-button4').removeClass('button-primary lmm-nav-primary');
						$('#lmm-header-button4').addClass('button-secondary lmm-nav-secondary');
						$('#layer-heading').html(results['layername']+' (ID '+results['newlayerid']+')');
						$('#duplicate_span_top, #delete_span_top, #duplicate_span_bottom, #delete_span_bottom').show();
						$('#id').val(results['newlayerid']);
						$('#oid').val(results['newlayerid']);
						$('#submit_top, #submit_bottom').val($('#defaults_texts_update').val());
						$('#action-layer-add-edit').val('edit');

						$('#tr-shortcode').show();
						$('#shortcode').val('['+shortcode+' layer="'+results['newlayerid']+'"]');
						$('#shortcode-link-kml, #panel-link-kml').attr('href', site_url+lmm_slug+'/kml/layer/'+results['newlayerid']+'/?markername='+lmm_ajax_vars.lmm_ajax_misc_kml);
						$('#shortcode-link-fullscreen, #panel-link-fullscreen').attr('href', site_url+lmm_slug+'/fullscreen/layer/'+results['newlayerid']+'/');
						$('#shortcode-link-qr, #panel-link-qr').attr('href', site_url+lmm_slug+'/qr/layer/'+results['newlayerid']+'/');
						$('#shortcode-link-geojson, #panel-link-geojson').attr('href', site_url+lmm_slug+'/geojson/layer/'+results['newlayerid']+'/?callback=jsonp&full=yes&full_icon_url=yes');
						$('#shortcode-link-georss, #panel-link-georss').attr('href', site_url+lmm_slug+'/georss/layer/'+results['newlayerid']+'/');
						$('#shortcode-link-wikitude, #panel-link-wikitude').attr('href', site_url+lmm_slug+'/wikitude/layer/'+results['newlayerid']+'/');
						$('#panel-link-kml').attr('href', site_url+lmm_slug+'/kml/layer/'+results['newlayerid']+'/?markername='+lmm_ajax_vars.lmm_ajax_misc_kml);
						$('#panel-link-fullscreen').attr('href', site_url+lmm_slug+'/fullscreen/layer/'+results['newlayerid']+'/');
						$('#panel-link-qr').attr('href', site_url+lmm_slug+'/qr/layer/'+results['newlayerid']+'/');
						$('#panel-link-geojson').attr('href', site_url+lmm_slug+'/geojson/layer/'+results['newlayerid']+'/?callback=jsonp&full=yes&full_icon_url=yes');
						$('#panel-link-georss').attr('href', site_url+lmm_slug+'/georss/layer/'+results['newlayerid']+'/');
						$('#panel-link-wikitude').attr('href', site_url+lmm_slug+'/wikitude/layer/'+results['newlayerid']+'/');
						if ($('#layername').val() === '') {
							$('#lmm-panel-text').html('&nbsp;');
						}
					}
				} else if ($('#action-layer-add-edit').val() === 'edit') {
					if (results['status-class'] === 'notice notice-success') {
						$('#layer-heading').html(results['layername']+' (ID '+results['layerid']+')');
						$('#listmarker-table-heading').html(results['listmarker-table-heading']);
						$('#updatedby').val(results['updatedby_saved']);
						$('#updatedon').val(results['updatedon_saved']);
						$('#audit_visibility').show();
						$('#updatedby_next').val(results['updatedby_next']);
						$('#updatedon_next').val(results['updatedon_next']);
					}
				}
			});
			return false;
		});

		//info: layer duplicate
		$('#duplicate_button_top, #duplicate_button_bottom').click(function(e) {
			$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').hide();
			$('#lmm_ajax_loading_top, #lmm_ajax_loading_bottom').show();
			$('#submit_top, #duplicate_button_top, #delete_button_top, #submit_bottom, #duplicate_button_bottom, #delete_button_bottom').attr('disabled', true);

			var data = {
				action: 'mapsmarker_ajax_actions_backend',
				lmm_ajax_subaction: 'layer-duplicate',
				lmm_ajax_nonce: lmm_ajax_vars.lmm_ajax_nonce,
				id: $('#id').val()
			};

			$.post(ajaxurl, data, function (response) {
				var results = response.replace(/^\s*[\r\n]/gm, '');
				var results = results.match(/!!LMM-AJAX-START!!(.*[\s\S]*)!!LMM-AJAX-END!!/)[1];
				var results = JSON.parse(results);

				$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').attr('class',results['status-class']);
				$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').html(results['status-text']);
				$('#lmm_ajax_loading_top, #lmm_ajax_loading_bottom').hide();
				$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').show();
				$('#submit_top, #duplicate_button_top, #delete_button_top, #submit_bottom, #duplicate_button_bottom, #delete_button_bottom').attr('disabled', false);

				if (results['status-class'] === 'notice notice-success') {
					if (history.pushState) { //info: not supported in IE8+9
						window.history.pushState(null, null, 'admin.php?page=leafletmapsmarker_layer&id='+results['newlayerid']);
					}
					$('#layer-heading').html(results['layername']+' (ID '+results['newlayerid']+')');
					$('#listmarker-table-heading').html(results['listmarker-table-heading']);
					$('#id').val(results['newlayerid']);
					$('#oid').val(results['newlayerid']);
					$('#shortcode').val('['+shortcode+' layer="'+results['newlayerid']+'"]');
					$('#shortcode-link-kml, #panel-link-kml').attr('href', site_url+lmm_slug+'/kml/layer/'+results['newlayerid']+'/?markername='+lmm_ajax_vars.lmm_ajax_misc_kml);
					$('#shortcode-link-fullscreen, #panel-link-fullscreen').attr('href', site_url+lmm_slug+'/fullscreen/layer/'+results['newlayerid']+'/');
					$('#shortcode-link-qr, #panel-link-qr').attr('href', site_url+lmm_slug+'/qr/layer/'+results['newlayerid']+'/');
					$('#shortcode-link-geojson, #panel-link-geojson').attr('href', site_url+lmm_slug+'/geojson/layer/'+results['newlayerid']+'/?callback=jsonp&full=yes&full_icon_url=yes');
					$('#shortcode-link-georss, #panel-link-georss').attr('href', site_url+lmm_slug+'/georss/layer/'+results['newlayerid']+'/');
					$('#shortcode-link-wikitude, #panel-link-wikitude').attr('href', site_url+lmm_slug+'/wikitude/layer/'+results['newlayerid']+'/');
					$('#panel-link-kml').attr('href', site_url+lmm_slug+'/kml/layer/'+results['newlayerid']+'/?markername='+lmm_ajax_vars.lmm_ajax_misc_kml);
					$('#panel-link-fullscreen').attr('href', site_url+lmm_slug+'/fullscreen/layer/'+results['newlayerid']+'/');
					$('#panel-link-qr').attr('href', site_url+lmm_slug+'/qr/layer/'+results['newlayerid']+'/');
					$('#panel-link-geojson').attr('href', site_url+lmm_slug+'/geojson/layer/'+results['newlayerid']+'/?callback=jsonp&full=yes&full_icon_url=yes');
					$('#panel-link-georss').attr('href', site_url+lmm_slug+'/georss/layer/'+results['newlayerid']+'/');
					$('#panel-link-wikitude').attr('href', site_url+lmm_slug+'/wikitude/layer/'+results['newlayerid']+'/');
					$('#markercount').html('0');
					$('#assigned-markers-table').hide(); //info: reset marker table at bottom
					$('.addmarker_link').attr('href', admin_url + 'admin.php?page=leafletmapsmarker_marker&addtoLayer='+ results['newlayerid'] +'&lat=' + results['layerviewlat'] + '&lon='+results['layerviewlon']+'&zoom='+results['layerzoom']);
					$('#tr-usedincontent').hide();
					$('#multi_layer_map').prop('disabled', false);
					$('#lmm-check-mlm-text').hide();
					//info: reset list of markers table below layer maps
					$('#lmm_listmarkers_table_admin tr').remove();
					var defaults_texts_list_markers = $('#defaults_texts_list_markers').val();
					$('#lmm_listmarkers_table_admin').append('<tr><td style="border-style:none;width:35px;"><img src="'+leaflet_plugin_url+'leaflet-dist/images/marker.png" /></td><td style="border-style:none;"><div style="float:right;"><img src="'+leaflet_plugin_url+'inc/img/icon-car.png" width="14" height="14" class="lmm-panel-api-images" />&nbsp;<img src="'+leaflet_plugin_url+'inc/img/icon-fullscreen.png" width="14" height="14" class="lmm-panel-api-images" />&nbsp;<img src="'+leaflet_plugin_url+'inc/img/icon-kml.png" width="14" height="14" class="lmm-panel-api-images" /></div><strong>'+defaults_texts_list_markers+'</strong></td></tr>');

				}
			});
			return false;
		});
		////////// end layer-duplicate

		//info: layer delete
		$('#delete_button_top, #delete_button_bottom').click(function(e) {
			if (confirm(lmm_ajax_vars.lmm_ajax_confirm_delete)) {
				$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').hide();
				$('#lmm_ajax_loading_top, #lmm_ajax_loading_bottom').show();
				$('#submit_top, #duplicate_button_top, #delete_button_top, #submit_bottom, #duplicate_button_bottom, #delete_button_bottom').attr('disabled', true);

				var data = {
					action: 'mapsmarker_ajax_actions_backend',
					lmm_ajax_subaction: 'layer-delete',
					lmm_ajax_nonce: lmm_ajax_vars.lmm_ajax_nonce,
					id: $('#id').val()
				};

				$.post(ajaxurl, data, function (response) {
					var results = response.replace(/^\s*[\r\n]/gm, '');
					var results = results.match(/!!LMM-AJAX-START!!(.*[\s\S]*)!!LMM-AJAX-END!!/)[1];
					var results = JSON.parse(results);

					$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').attr('class',results['status-class']);
					$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').html(results['status-text']);
					$('#lmm_ajax_loading_top, #lmm_ajax_loading_bottom').hide();
					$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').show();
					$('#submit_top, #duplicate_button_top, #delete_button_top, #submit_bottom, #duplicate_button_bottom, #delete_button_bottom').attr('disabled', false);

					if (results['status-class'] === 'notice notice-success') {
						if (history.pushState) { //info: not supported in IE8+9
							window.history.pushState(null, null, 'admin.php?page=leafletmapsmarker_layers');
						}
						$('#div-layer-editor-hide-on-ajax-delete').hide();
						$('#duplicate_span_top, #delete_span_top, #duplicate_span_bottom, #delete_span_bottom').hide();
					}
				});
				return false;
			}
			return false;
		});
		///////// end layer-delete

		//info: add new layer actions
		$('.menu-top.toplevel_page_leafletmapsmarker_markers.menu-top-last ul.wp-submenu.wp-submenu-wrap li.current a.current, #lmm-header-button4, #wp-admin-bar-lmm-add-layers').click(function(e) {
			e.preventDefault();
			if (history.pushState) { //info: not supported in IE8+9
				window.history.pushState(null, null, 'admin.php?page=leafletmapsmarker_layer');
			}
			if ($('#lmm-header-button4').hasClass('button-secondary')) {
				$('#lmm-header-button4').removeClass('button-secondary lmm-nav-secondary');
				$('#lmm-header-button4').addClass('button-primary lmm-nav-primary');
			}
			$('.hide_on_new').hide();
			if ( $('#defaults_wpml_status').val() === false) {
				$('.wpml-layertranslatelink').show();
				$('.wpml-layertranslatelink a').attr( "href", 'https://www.mapsmarker.com/multilingual');
			} else {
				$('.wpml-layertranslatelink').hide(); //info: dont show if not saved yet
			}
			$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').hide();
			$('#div-layer-editor-hide-on-ajax-delete').show();
			$('#layer-heading').html($('#defaults_texts_add_new_layer').val());
			$('#submit_top, #submit_bottom').val($('#defaults_texts_publish').val());
			$('#duplicate_span_top, #delete_span_top, #duplicate_span_bottom, #delete_span_bottom').hide();
			$('#action-layer-add-edit').val('add');
			$('#tr-shortcode').hide();
			$('#tr-usedincontent').hide();
			//info: set form values
			$('#id').val('');
			$('#layername').val('');
			$('#lmm-panel-text').html($('#defaults_texts_panel_text').val());
			//info: unresolved $('#basemap').val($('#defaults_basemap').val());
			$('#lat').val($('#defaults_lat').val());
			$('#lon').val($('#defaults_lon').val());
			$('.div-layer-icon').css('background','none');
			$('.div-layer-icon').css('opacity','0.4');
			$('.div-layer-icon-default').css('opacity','1');
			$('.div-layer-icon-default').css('background','#5e5d5d');
			$('#icon_hidden').val($('#defaults_icon').val());
			if ($('#wp-popuptext-wrap').hasClass('tmce-active')) {
				tinymce.get('popuptext').setContent('');
			} else {
				$('#popuptext').val('');
			}
 			$('html, body').animate({ scrollTop: 0 }, 'fast'); //info: workaround for tinyMCE focus
			$('#selectlayer-popuptext-hidden').val('');
			$('#zoom').val($('#defaults_zoom').val());
			if ($('#defaults_openpopup').val() === '0') { $('input:checkbox[name=openpopup]').attr('checked',false); } else { $('input:checkbox[name=openpopup]').attr('checked',true); }
			$('#mapwidth').val($('#defaults_mapwidth').val());
			if ($('#defaults_mapwidthunit').val() === 'px') { $('input:radio[id=mapwidthunit_px]')[0].checked = true; } else { $('input:radio[id=mapwidthunit_percent]')[0].checked = true; }
			$('#mapheight').val($('#defaults_mapheight').val());
			if ($('#defaults_panel').val() === '0') { $('input:checkbox[name=panel]').attr('checked',false); } else { $('input:checkbox[name=panel]').attr('checked',true); }
			if ($('#defaults_clustering').val() === '0') { $('input:checkbox[name=clustering]').attr('checked',false); } else { $('input:checkbox[name=clustering]').attr('checked',true); }
			$('#createdby').val($('#updatedby_next').val());
			$('#createdon').val($('#updatedon_next').val());
			$('#audit_visibility').hide();
			$('#updatedby').val($('#updatedby_next').val());
			$('#updatedon').val($('#updatedon_next').val());
			if ($('#defaults_controlbox').val() === '0') {
				$('input:radio[id=controlbox_hidden]')[0].checked = true;
			} else if ($('#defaults_controlbox').val() === '1') {
				$('input:radio[id=controlbox_collapsed]')[0].checked = true;
			} else if ($('#defaults_controlbox').val() === '2') {
				$('input:radio[id=controlbox_expanded]')[0].checked = true;
			}
			$('#overlays_custom').val($('#defaults_overlays_custom').val());
			$('#overlays_custom2').val($('#defaults_overlays_custom2').val());
			$('#overlays_custom3').val($('#defaults_overlays_custom3').val());
			$('#overlays_custom4').val($('#defaults_overlays_custom4').val());
			if ($('#defaults_wms').val() === '0') { $('input:checkbox[name=wms]').attr('checked',false); } else { $('input:checkbox[name=wms]').attr('checked',true); }
			if ($('#defaults_wms2').val() === '0') { $('input:checkbox[name=wms2]').attr('checked',false); } else { $('input:checkbox[name=wms2]').attr('checked',true); }
			if ($('#defaults_wms3').val() === '0') { $('input:checkbox[name=wms3]').attr('checked',false); } else { $('input:checkbox[name=wms3]').attr('checked',true); }
			if ($('#defaults_wms4').val() === '0') { $('input:checkbox[name=wms4]').attr('checked',false); } else { $('input:checkbox[name=wms4]').attr('checked',true); }
			if ($('#defaults_wms5').val() === '0') { $('input:checkbox[name=wms5]').attr('checked',false); } else { $('input:checkbox[name=wms5]').attr('checked',true); }
			if ($('#defaults_wms6').val() === '0') { $('input:checkbox[name=wms6]').attr('checked',false); } else { $('input:checkbox[name=wms6]').attr('checked',true); }
			if ($('#defaults_wms7').val() === '0') { $('input:checkbox[name=wms7]').attr('checked',false); } else { $('input:checkbox[name=wms7]').attr('checked',true); }
			if ($('#defaults_wms8').val() === '0') { $('input:checkbox[name=wms8]').attr('checked',false); } else { $('input:checkbox[name=wms8]').attr('checked',true); }
			if ($('#defaults_wms9').val() === '0') { $('input:checkbox[name=wms9]').attr('checked',false); } else { $('input:checkbox[name=wms9]').attr('checked',true); }
			if ($('#defaults_wms10').val() === '0') { $('input:checkbox[name=wms10]').attr('checked',false); } else { $('input:checkbox[name=wms10]').attr('checked',true); }
			$('#address').val('');
			$('#popup-address').html($('#defaults_texts_directions_link_new_layer').val());

			if ($('#gpx_url').val() !== '') {
				$('#gpx_url').val('');
				//info: workaround as removeLayer did not work
				$('.leaflet-overlay-pane').html('');
				$('.lmm_gpx_icons').hide();
			}
			$('#gpx_fitbounds_link').hide();
			$('#gpx-panel-selectlayer').hide();
			$('input:checkbox[name=gpx_panel]').attr('checked',false);
			//info: reset leaflet map; do not change to default basemap due to unresolved issues :-/
			$('#lmm').css('width',$('#defaults_mapwidth').val()+$('#defaults_mapwidthunit').val());
			$('#selectlayer').css('height',$('#defaults_mapheight').val());
			selectlayer.invalidateSize();
			selectlayer.setView(new L.LatLng($('#defaults_lat').val(), $('#defaults_lon').val()), $('#defaults_zoom').val());
			if ($('#defaults_controlbox').val() === '0') {
				$('.leaflet-control-layers').hide();
			} else if ($('#defaults_controlbox').val() === '1') {
				$('.leaflet-control-layers').show();
				layersControl._collapse();
			} else if ($('#defaults_controlbox').val() === '2') {
				$('.leaflet-control-layers').show();
				layersControl._expand();
			}
			if ($('#defaults_panel').val() === '0') { $('#lmm-panel').css('display','none'); } else { $('#lmm-panel').css('display','block'); }

			//info: reset wms
			if (selectlayer.hasLayer(wms)) { selectlayer.removeLayer(wms); }
			if (selectlayer.hasLayer(wms2)) { selectlayer.removeLayer(wms2); }
			if (selectlayer.hasLayer(wms3)) { selectlayer.removeLayer(wms3); }
			if (selectlayer.hasLayer(wms4)) { selectlayer.removeLayer(wms4); }
			if (selectlayer.hasLayer(wms5)) { selectlayer.removeLayer(wms5); }
			if (selectlayer.hasLayer(wms6)) { selectlayer.removeLayer(wms6); }
			if (selectlayer.hasLayer(wms7)) { selectlayer.removeLayer(wms7); }
			if (selectlayer.hasLayer(wms8)) { selectlayer.removeLayer(wms8); }
			if (selectlayer.hasLayer(wms9)) { selectlayer.removeLayer(wms9); }
			if (selectlayer.hasLayer(wms10)) { selectlayer.removeLayer(wms10); }
			$('#kml_timestamp').val('');
			//info: set default icon
			$('.div-layer-icon').css('background','none');
			$('.div-layer-icon').css('opacity','0.4');

			var icon_opacity_selector = $('#defaults_icon_opacity_selector').val();
			$(icon_opacity_selector).css('opacity','1');
			$(icon_opacity_selector).css('background','#5e5d5d');
			//info: reset panel api links
			$('#popup-directions, #panel-link-directions').attr('href', 'https://maps.google.com/maps?daddr='+$('#defaults_lat').val()+','+$('#defaults_lon').val()+'&t=m&layer=1&doflg=ptk&om=0');
			$('#panel-link-kml').attr('href', site_url+lmm_slug+'/kml/layer/?markername='+lmm_ajax_vars.lmm_ajax_misc_kml);
			$('#panel-link-fullscreen').attr('href', site_url+lmm_slug+'/fullscreen/layer/');
			$('#panel-link-qr').attr('href', site_url+lmm_slug+'/qr/layer/');
			$('#panel-link-geojson').attr('href', site_url+lmm_slug+'/geojson/layer/?callback=jsonp&full=yes&full_icon_url=yes');
			$('#panel-link-georss').attr('href', site_url+lmm_slug+'/georss/layer/');
			$('#panel-link-wikitude').attr('href', site_url+lmm_slug+'/wikitude/layer/');
			//info: reset list of markers table below layer maps
			$('#lmm_listmarkers_table_admin tr').remove();
			var defaults_texts_list_markers = $('#defaults_texts_list_markers').val();
			$('#lmm_listmarkers_table_admin').append('<tr><td style="border-style:none;width:35px;"><img src="'+leaflet_plugin_url+'leaflet-dist/images/marker.png" /></td><td style="border-style:none;"><div style="float:right;"><img src="'+leaflet_plugin_url+'inc/img/icon-car.png" width="14" height="14" class="lmm-panel-api-images" />&nbsp;<img src="'+leaflet_plugin_url+'inc/img/icon-fullscreen.png" width="14" height="14" class="lmm-panel-api-images" />&nbsp;<img src="'+leaflet_plugin_url+'inc/img/icon-kml.png" width="14" height="14" class="lmm-panel-api-images" /></div><strong>'+defaults_texts_list_markers+'</strong></td></tr>');
			//info: clear the map / remove layers
			$.each(selectlayer._layers, function (e,i) {
				if(selectlayer._layers[e]._icon){
					selectlayer.removeLayer(selectlayer._layers[e]);
				}
			});
			mapcentermarker.addTo(selectlayer);
			//info: clear multi-layer-map-checkboxes
			$('#multi_layer_map').prop('disabled', false);
			$('#lmm-check-mlm-text').hide();
			$('#multi_layer_map').prop('checked', false);
			$('#lmm-multi_layer_map').hide();
			$("input[type=checkbox][name^=mlm]").each(function(){
				$(this).prop('checked', false);
			});
			//info: prevent reusage of markers for new layer when clicking on clustering checkbox
			delete(geojsonObj);
			//info: reset marker table at bottom
			$('#assigned-markers-table').hide();
		});
		///////// end add new layer actions

		//info: multi-layer-map checkboxes
		$('#mlm-all').click(function(){
			if(document.getElementById('mlm-all').checked){
					$("input[type=checkbox][name^=mlm]").each(function(){
						if($(this).attr('id')!='mlm-all'){
							$(this).prop('checked', true);
						}
					});
			}else{
					$("input[type=checkbox][name^=mlm]").each(function(){
						if($(this).attr('id')!='mlm-all'){
							$(this).prop('checked', false);
						}
					});
			}
		});

		//info: dynamic preview of checked multi-layer-map layers
		var mlmlayers = '';
		$("input[type=checkbox][name^=mlm]").click(function(){
				var target = $(this).attr('id');
				var layer_id = $(this).attr('id').replace('mlm-','');
				if(document.getElementById(target).checked){
					$('#mlm_filter_status_' + layer_id).val('1');
					$('#mlm_filter_name_' + layer_id).val($('#mlm_filter_name_' + layer_id).attr('data-default') );
					$('#mlm_filter_name_' + layer_id).removeAttr('disabled');
					$('#mlm_filter_icon_' + layer_id).removeAttr('disabled');
				}else{
					$('#mlm_filter_status_' + layer_id).val('0');
					$('#mlm_filter_name_' + layer_id).attr('disabled', 'disabled');
					$('#mlm_filter_name_' + layer_id).val('');
					$('#mlm_filter_icon_' + layer_id).attr('disabled', 'disabled');
					$('#mlm_filter_icon_' + layer_id).val('');

				}
				$("input[type=checkbox][name^=mlm]").each(function(){
					if(document.getElementById($(this).attr('id')).checked) {
						mlmlayers += $(this).attr('id').replace('mlm-', '')+',';
					}else{
						if(target!= 'mlm-all'){
							$('#mlm-all').prop('checked', false);
						}
					}
				});
				//info: clear the map first
				$.each(selectlayer._layers, function (e,i) {
					if(selectlayer._layers[e]._icon){
						selectlayer.removeLayer(selectlayer._layers[e]);
					}
				});
				mapcentermarker.addTo(selectlayer);
				if(mlmlayers != ''){

					//info: prepare icon vars for loop (not working by directly fetching values via $(...).val();
					var prepare_default_marker_icon_iconsize_x = $('#defaults_marker_icon_iconsize_x').val();
					var prepare_default_marker_icon_iconsize_y = $('#defaults_marker_icon_iconsize_y').val();
					var prepare_defaults_marker_icon_iconanchor_x = $('#defaults_marker_icon_iconanchor_x').val();
					var prepare_defaults_marker_icon_iconanchor_y = $('#defaults_marker_icon_iconanchor_y').val();
					var prepare_defaults_marker_icon_popupanchor_x = $('#defaults_marker_icon_popupanchor_x').val();
					var prepare_defaults_marker_icon_popupanchor_y = $('#defaults_marker_icon_popupanchor_y').val();
					var prepare_defaults_marker_icon_shadow_url = $('#defaults_marker_icon_shadow_url').val();
					var prepare_defaults_marker_icon_shadowsize_x = $('#defaults_marker_icon_shadowsize_x').val();
					var prepare_defaults_marker_icon_shadowsize_y = $('#defaults_marker_icon_shadowsize_y').val();
					var prepare_defaults_marker_icon_shadowanchor_x = $('#defaults_marker_icon_shadowanchor_x').val();
					var prepare_defaults_marker_icon_shadowanchor_y = $('#defaults_marker_icon_shadowanchor_y').val();
					//info: prepare popup+default vars for loop (not working by directly fetching values via $(...).val();
					var prepare_defaults_marker_popups_maxWidth = $('#defaults_marker_popups_maxWidth').val();
					var prepare_defaults_marker_popups_minWidth = $('#defaults_marker_popups_minWidth').val();
					var prepare_defaults_marker_popups_maxHeight = $('#defaults_marker_popups_maxHeight').val();
					var prepare_defaults_marker_popups_autoPan = $('#defaults_marker_popups_autoPan').val();
					var prepare_defaults_marker_popups_closeButton = $('#defaults_marker_popups_closeButton').val();
					var prepare_defaults_marker_popups_autopanpadding_x = $('#defaults_marker_popups_autopanpadding_x').val();
					var prepare_defaults_marker_popups_autopanpadding_y = $('#defaults_marker_popups_autopanpadding_y').val();
					var prepare_defaults_marker_popups_add_markername = $('#defaults_marker_popups_add_markername').val();
					var prepare_defaults_directions_popuptext_panel = $('#defaults_directions_popuptext_panel').val();

					var xhReq = new XMLHttpRequest();
					xhReq.open("GET", site_url+lmm_slug+'/geojson/layer/'+mlmlayers+'/', true);
					xhReq.onreadystatechange = function (e) { if (xhReq.readyState === 4) { if (xhReq.status === 200) { //info: async 1/2
						//info: check if WP DEBUG or other additional on-screen warnings or errors brake GeoJSON array validity
						if (xhReq.responseText.indexOf('{"type"') != 0) {
							var position = xhReq.responseText.indexOf('{"type"');
							var response = JSON.parse(xhReq.responseText.slice(position));
						} else {
							var response = JSON.parse(xhReq.responseText);
						}
						window.group_for_clustering = L.layerGroup();
						if(response.features){
							$.each(response.features, function(id, marker){

								var mlm_marker = new L.Marker( new L.LatLng(marker.geometry.coordinates[1], marker.geometry.coordinates[0]),
								{
									markerid: marker.properties.markerid,
									title: marker.properties.markername,
									interactive: true,
									draggable: false,
									zIndexOffset: 1000,
									opacity: 1.0
								});
								window.group_for_clustering.addLayer(mlm_marker);
								if (prepare_defaults_marker_popups_add_markername === 'true') {
									if (marker.properties.markername != "") {
										var divmarkername1 = '<div class="popup-markername" style="border-bottom:1px solid #f0f0e7;padding-bottom:5px;margin-bottom:6px;">';
										var divmarkername2 = '</div>';
										var prepare_popups_add_markername =  marker.properties.markername;
									} else {
										var divmarkername1 = '';
										var divmarkername2 = '';
										var prepare_popups_add_markername =  '';
									}
								} else {
									var divmarkername1 = '';
									var divmarkername2 = '';
									var prepare_popups_add_markername =  '';
								}
								if (prepare_defaults_directions_popuptext_panel === 'yes') {
									if (marker.properties.text != '') { var css = 'border-top:1px solid #f0f0e7;padding-top:5px;margin-top:5px;clear:both;'; } else { var css = ''; }
									var prepare_direction_popuptext_panel = '<div class="popup-directions" style="'+css+'">'+marker.properties.address+' (<a href="'+ marker.properties.dlink +'" target="_blank" title="'+ lmm_ajax_vars.lmm_get_directions_text +'">'+ lmm_ajax_vars.lmm_directions_text +'</a>)</div>';
								} else {
									var prepare_direction_popuptext_panel = '';
								}
								mlm_marker.bindPopup("<img id=\"popup-loading-"+marker.properties.markerid+"\" style=\"display: none; margin: 20px auto;\" src=\""+lmm_ajax_vars.lmm_ajax_leaflet_plugin_url+"inc/img/paging-ajax-loader.gif\" /><div id=\"popup-content-"+marker.properties.markerid+"\">"+divmarkername1+prepare_popups_add_markername+divmarkername2+marker.properties.text+prepare_direction_popuptext_panel+"<div>",
								{
									maxWidth: prepare_defaults_marker_popups_maxWidth,
									minWidth: prepare_defaults_marker_popups_minWidth,
									maxHeight: prepare_defaults_marker_popups_maxHeight,
									autoPan: prepare_defaults_marker_popups_autoPan,
									closeButton: prepare_defaults_marker_popups_closeButton,
									autoPanPadding: new L.Point(prepare_defaults_marker_popups_autopanpadding_x+','+prepare_defaults_marker_popups_autopanpadding_y)
								});

								mlm_marker.options.icon = new L.Icon(
								{
									iconUrl: (marker.properties.icon != '') ? defaults_marker_icon_url+'/'+ marker.properties.icon : leaflet_plugin_url+'leaflet-dist/images/marker.png',
									iconSize: [prepare_default_marker_icon_iconsize_x,prepare_default_marker_icon_iconsize_y],
									iconAnchor: [prepare_defaults_marker_icon_iconanchor_x,prepare_defaults_marker_icon_iconanchor_y],
									popupAnchor: [prepare_defaults_marker_icon_popupanchor_x,prepare_defaults_marker_icon_popupanchor_y],
									shadowUrl: prepare_defaults_marker_icon_shadow_url,
									shadowSize: [prepare_defaults_marker_icon_shadowsize_x,prepare_defaults_marker_icon_shadowsize_y],
									shadowAnchor: [prepare_defaults_marker_icon_shadowanchor_x,prepare_defaults_marker_icon_shadowanchor_y],
									className: (marker.properties.icon != '') ? 'lmm_marker_icon_'+marker.properties.icon.substr(0,-4) : 'lmm_marker_icon_default'
								});
								if(!$('input:checkbox[name=clustering]').is(':checked')) {
									mlm_marker.addTo(selectlayer);
								}
								//info: add info about reloading list of markers
								if (document.getElementById('listmarkers').checked) {
									if (!document.getElementById('listmarkers-ajax-info')) {
										var defaults_texts_list_markers_ajax_info = $('#defaults_texts_list_markers_ajax_info').val();
										$('#lmm_listmarkers_table_admin').prepend('<tr id="listmarkers-ajax-info"><td colspan="2" style="background-color:#ffcc33;text-align:center;font-weight:bold;">'+defaults_texts_list_markers_ajax_info+'</td></tr>');
									}
								}
							});
								//info: 2.6 fix clustering
								 window.markercluster = new L.MarkerClusterGroup({ zoomToBoundsOnClick: true, showCoverageOnHover: true, spiderfyOnMaxZoom: true, animateAddingMarkers: false, disableClusteringAtZoom: 0, maxClusterRadius: 80, polygonOptions: {stroke: true, color: '#03f', weight: 5, opacity: 0.5, fillColor: '#03f', fillOpacity: 0.2, interactive: true}, singleMarkerMode: false, spiderfyDistanceMultiplier: 1, chunkedLoading: true, chunkProgress: null });
								if($('input:checkbox[name=clustering]').is(':checked')) {
									//info: clear the map first
									$.each(selectlayer._layers, function (e,i) {
										if(selectlayer._layers[e]._icon){
											selectlayer.removeLayer(selectlayer._layers[e]);
										}
									});
									selectlayer.removeLayer(window.group_for_clustering);
									window.group_for_clustering.addTo(window.markercluster);
									selectlayer.addLayer(window.markercluster);
									mapcentermarker.addTo(selectlayer);
								} else {

									window.markercluster.clearLayers();
									window.group_for_clustering.addTo(selectlayer);
								}
						}
					} else { if (window.console) { console.error(xhReq.statusText); } } } }; xhReq.onerror = function (e) { if (window.console) { console.error(xhReq.statusText); } }; xhReq.send(null); //info: async 2/2
				}
				mlmlayers = '';
		});

		//info: layer editor switch link 1/2
		$('#switch-link-visible').click(function(e) {
			$('#switch-link-visible').toggle();
			$('#switch-link-hidden').toggle();
			var active_editor = $('#active_editor').val();
			if (active_editor == 'advanced') {
				$('#active_editor').val('simplified');
			} else {
				$('#active_editor').val('advanced');
			}
			$('#apilinkstext').show();
			$('#apilinks').hide();
			$('#toggle-google-settings').toggle();
			$('#toggle-coordinates').toggle();
			$('#toogle-global-maximum-zoom-level').toggle();
			$('#toggle-controlbox-panel-kmltimestamp-backlinks-minimaps').toggle();
			$('#toggle-listofmarkerssettings').toggle();
			$('#toggle-clustersettings').toggle();
			$('#toggle-mlm-filters-settings').toggle();
			$('#toggle-advanced-settings').toggle();
			$('#toggle-audit').toggle();

			var data = {
				action: 'mapsmarker_ajax_actions_backend',
				lmm_ajax_subaction: 'editor-switchlink',
				lmm_ajax_nonce: lmm_ajax_vars.lmm_ajax_nonce,
				active_editor: $('#active_editor').val()
			};

			$.post(ajaxurl, data, function (response) {
				var results = response.replace(/^\s*[\r\n]/gm, '');
				var results = results.match(/!!LMM-AJAX-START!!(.*[\s\S]*)!!LMM-AJAX-END!!/)[1];
				var results = JSON.parse(results);

				$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').attr('class',results['status-class']);
				$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').html(results['status-text']);
				$('#lmm_ajax_loading_top, #lmm_ajax_loading_bottom').hide();
				$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').show();
			});
			return false;
		});

		//info: layer editor switch link 2/2
		$('#switch-link-hidden').click(function(e) {
			$('#switch-link-visible').toggle();
			$('#switch-link-hidden').toggle();
			var active_editor = $('#active_editor').val();
			if (active_editor == 'advanced') {
				$('#active_editor').val('simplified');
			} else {
				$('#active_editor').val('advanced');
			}
			$('#apilinkstext').hide();
			$('#apilinks').show();
			$('#toggle-google-settings').toggle();
			$('#toggle-coordinates').toggle();
			$('#toogle-global-maximum-zoom-level').toggle();
			$('#toggle-controlbox-panel-kmltimestamp-backlinks-minimaps').toggle();
			$('#toggle-listofmarkerssettings').toggle();
			$('#toggle-clustersettings').toggle();
			$('#toggle-mlm-filters-settings').toggle();
			$('#toggle-advanced-settings').toggle();
			$('#toggle-audit').toggle();

			var data = {
				action: 'mapsmarker_ajax_actions_backend',
				lmm_ajax_subaction: 'editor-switchlink',
				lmm_ajax_nonce: lmm_ajax_vars.lmm_ajax_nonce,
				active_editor: $('#active_editor').val()
			};

			$.post(ajaxurl, data, function (response) {
				var results = response.replace(/^\s*[\r\n]/gm, '');
				var results = results.match(/!!LMM-AJAX-START!!(.*[\s\S]*)!!LMM-AJAX-END!!/)[1];
				var results = JSON.parse(results);

				$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').attr('class',results['status-class']);
				$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').html(results['status-text']);
				$('#lmm_ajax_loading_top, #lmm_ajax_loading_bottom').hide();
				$('#lmm_ajax_results_top, #lmm_ajax_results_bottom').show();
			});
			return false;
		});
		var old_results =  $('#the-list').html();
		var old_mcount =  $('#totalmarkers').html();
		$('input[name=searchsubmit]').click(function(e){
			e.preventDefault();
			var val = $('#searchtext').val();
			lmm_get_search_markers_result(val);
		});
		$('#searchtext').keyup(function(){
			var val = $('#searchtext').val();
			lmm_get_search_markers_result(val);
		});
		$('.tablenav-pages.backend').on('click','a.first-page-backend',function(e){
			e.preventDefault();
			var old_tablenav_pages = $('.tablenav-pages').html();
			var page_number = $(this).html();
			$('.current-page').removeClass('current-page');
			var page_link_element = this;
			$.ajax({
				url:ajaxurl,
				data: {
					action: 'mapsmarker_ajax_actions_backend',
					lmm_ajax_subaction: 'lmm_list_markers_for_edit_page',
					lmm_ajax_nonce: lmm_ajax_vars.lmm_ajax_nonce,
					paged: page_number,
					orderby: $('input[name=orderby]').val(),
					order: $('input[name=order]').val(),
					layer_id: $('input[name=layer_id]').val(),
					multi_layer_map_list: $('input[name=multi_layer_map_list]').val(),
					totalmarkers: $('input[name=totalmarkers]').val()
				},
				beforeSend: function(){
					$('.tablenav-pages').html('<img src="'+   lmm_ajax_vars.lmm_ajax_leaflet_plugin_url	+'inc/img/paging-ajax-loader.gif"/>');
				},
				method:'POST',
				success: function(response){
					var results = response.replace(/^\s*[\r\n]/gm, '');
					var results = results.match(/!!LMM-AJAX-START!!(.*[\s\S]*)!!LMM-AJAX-END!!/)[1];
					var res = JSON.parse(results);
					$('#list-markers #the-list').html(res.rows);
					$('#totalmarkers').html(res.mcount);
					$('.tablenav-pages').html(res.pager);
					$(page_link_element).addClass('current-page');
				}
			});

		});
		function lmm_get_search_markers_result(val){
			//info only if user wrote a word more than 2 letters
			if(val.length > 2){
				$.ajax({
					url:ajaxurl,
					data: {
						action: 'mapsmarker_ajax_actions_backend',
						lmm_ajax_subaction: 'lmm_list_markers_search',
						lmm_ajax_nonce: lmm_ajax_vars.lmm_ajax_nonce,
						'searchtext': val.trim()
					},
					beforeSend: function(){
						$('#searchtext').addClass('searchtext_loading');
					},
					method:'POST',
					success: function(response){
						var results = response.replace(/^\s*[\r\n]/gm, '');
						var results = results.match(/!!LMM-AJAX-START!!(.*[\s\S]*)!!LMM-AJAX-END!!/);
						if(results !== null){
							results = results[1];
							var res = JSON.parse(results);
							var no_matches_found = $('#defaults_texts_no_search_results').val();
							if(res.mcount == 0){
								$('#the-list').html('<tr><td colspan="7">'+no_matches_found+'</td></tr>');
							}else{
								$('#the-list').html(res.rows);
							}
							$('#totalmarkers').html(res.mcount);
							$('#searchtext').removeClass('searchtext_loading');
						}


					}
				});
			}else{
				$('#the-list').html(old_results);
				$('#totalmarkers').html(old_mcount);
			}
		}
	} //info: end "current_page === 'leafletmapsmarker_layer'"
	
	//info: show loading indicator if popup contains images and update popup after images have loaded to prevent broken popups
	selectlayer.on('popupopen', function(e) {
		var popup_markerid = e.popup._source.options.markerid;
		var popup_images = jQuery('.leaflet-popup-content-wrapper #popup-content-'+popup_markerid+' img');
		if (popup_images.length > 0) {
			var image_counter = 0;
			jQuery('#popup-content-'+popup_markerid).css('display', 'none');
			jQuery('#popup-loading-'+popup_markerid).css('display', 'block');
			jQuery(popup_images).each(function() {
				jQuery(this).on('load', function() {
					image_counter++;
					if (image_counter == popup_images.length) {
						jQuery('#popup-loading-'+popup_markerid).css('display', 'none');
						jQuery('#popup-content-'+popup_markerid).css('display', 'block');
						e.popup.update();
					}
				});
			});
		}
	});
});
