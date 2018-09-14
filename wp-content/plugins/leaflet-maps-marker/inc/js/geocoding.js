/**
* MMP Geocoding class
*@since 2.8.0
**/

var MMP_Geocoding = function( provider , options, is_edit, typinginterval, min_chars_start_search_as_you_type){
	this.provider = provider;
	this.is_edit = is_edit;
	this.options = options;
	this.typingInterval = typinginterval;
	this.min_chars_start_search_as_you_type = min_chars_start_search_as_you_type;
	this.query = jQuery('#address').val();
	var typingTimer;
	var footer_tips = jQuery('#defaults_texts_geocoding_footer_tips').html();

	//Object which has all the providers data. 
	this.providers = {
		mapzen_search: {
			footer: '<div class="ap-footer">' + footer_tips + '<div style="float:right;margin:4px 0 0 10px;"><a href="https://www.mapsmarker.com/mapzen-search" target="_blank">Powered by Mapzen Search</a></div><a href="https://www.mapsmarker.com/mapzen-search" target="_blank"><img src="'+ lmm_ajax_vars.lmm_ajax_leaflet_plugin_url +'inc/img/geocoding/mapzen-search.png" width="20" height="20" /></a></div>',
			results_key: 'features',
			geo_key: 'geometry.coordinates',
			get_url: function(){
				var url = 'https://search.mapzen.com/v1/autocomplete?text={query}';
				if(parent.options[parent.provider].api_key){
					url += '&api_key=' + parent.options[parent.provider].api_key;
				}
				if(parent.options[parent.provider].focuspointlat!='none'){
					url += '&focus.point.lat=' + parent.options[parent.provider].focuspointlat;
				}
				if(parent.options[parent.provider].focuspointlon!='none'){
					url += '&focus.point.lon=' + parent.options[parent.provider].focuspointlon;
				}
				if(parent.options[parent.provider].sources){
					url += '&sources=' + parent.options[parent.provider].sources;
				}					
				if(parent.options[parent.provider].layers!='none'){
					url += '&layers=' + parent.options[parent.provider].layers;
				}
				if(parent.options[parent.provider].country){
					url += '&boundary.country=' + parent.options[parent.provider].country;
				}
				if(parent.options[parent.provider].narrow_search == 'rectangle'){
					if(parent.options[parent.provider].rect_lat_min){
						url +='&boundary.rect.min_lat=' + parent.options[parent.provider].rect_lat_min;
					}
					if(parent.options[parent.provider].rect_lon_min){
						url +='&boundary.rect.min_lon=' + parent.options[parent.provider].rect_lon_min;
					}
					if(parent.options[parent.provider].rect_lat_max){
						url +='&boundary.rect.max_lat=' + parent.options[parent.provider].rect_lat_max;
					}	
					if(parent.options[parent.provider].rect_lon_max){
						url +='&boundary.rect.max_lon=' + parent.options[parent.provider].rect_lon_max;
					}									
				}else if(parent.options[parent.provider].narrow_search == 'circle'){
					if(parent.options[parent.provider].circle_lat){
						url +='&boundary.circle.lat=' + parent.options[parent.provider].circle_lat;
					}
					if(parent.options[parent.provider].circle_lon){
						url +='&boundary.circle.lon=' + parent.options[parent.provider].circle_lon;
					}
					if(parent.options[parent.provider].circle_radius){
						url +='&boundary.circle.radius=' + parent.options[parent.provider].circle_radius;
					}											
				}
				return url;
			},
			format: function(options, text){
				var tags = options.properties.layer;
			    var types = ['country', 'venue', 'address'];
				var type = parent.get_unified_type(types, tags);
				var type_title = type.charAt(0).toUpperCase() + type.slice(1);
		        //noinspection UnnecessaryLocalVariableJS
				var out = ('<span class="ap-suggestion-icon ap-'+ type +'" title="'+ type_title +'">' + '' + '</span><span class="ap-name">'+ options.properties.label.replace(text, '<em>'+ text +'</em>') +'\n\n</span>').replace(/\s*\n\s*/g, ' ');
		        return out;
			},
			update_limit: function(xhrObj){
				var x_cache = xhrObj.getResponseHeader('X-Cache');
				if(x_cache == 'MISS'){
					var rate_limit = (parent.options[parent.provider].api_key)?30000:1000;
					var remaining_limit = xhrObj.getResponseHeader('X-ApiaxleProxy-Qpd-Left');
					jQuery('#mapzen-rate-limit-static').css('display','none');
					jQuery('#mapzen-rate-limit-dynamic').css('display','inline');
					jQuery('#mapzen-rate-limit-consumed').html(rate_limit - remaining_limit);
				}
			},
			displayError: function(response, xhrObj){
				var error_type = (typeof response.results != 'undefined')?response.results.error.type:'';
                var error_message = (typeof response.results != 'undefined')?response.results.error.message:'';
				if(xhrObj.status != 200){
					if ( (response.meta.status_code == '429') || (response.meta.status_code == '401') ) {
						if(!parent.options[parent.provider].api_key){
							jQuery('#geocoding-provider-status').html(jQuery('#defaults_texts_geocoding_mapzen_api_key_needed').html());
						} else { //info: not sure if needed, added as fallback
							jQuery('#geocoding-provider-status').html('Mapzen Search failed: "' + error_type + ' - ' + error_message + '"');
						}
					} else if (response.meta.status_code != '200') {
						jQuery('#geocoding-provider-status').html('Mapzen Search failed: "' + error_type + ' - ' + error_message + '"');
					}
				}
			}
		},
		algolia_places: {
			footer: '<div class="ap-footer">' + footer_tips + 'Built by <a href="https://www.mapsmarker.com/algolia-places" target="_blank" title="Search by Algolia" class="ap-footer-algolia"></a> using <a href="https://community.algolia.com/places/documentation.html#license" class="ap-footer-osm" target="_blank" title="Algolia Places data &copy; OpenStreetMap contributors"> <span>data</span></a></div>',
			results_key: 'hits',
			geo_key: '_geoloc',
			get_url: function(){
				var url = 'https://places-dsn.algolia.net/1/places/query?query={query}&hitsPerPage=10';
				if(parent.options[parent.provider].appId){
					url += '&x-algolia-application-id=' + parent.options[parent.provider].appId;
				}
				if(parent.options[parent.provider].apiKey){
					url += '&x-algolia-api-key=' + parent.options[parent.provider].apiKey;
				}
				url += '&language='+ parent.options[parent.provider].language;
				if(parent.options[parent.provider].countries){
					url += '&countries='+ parent.options[parent.provider].countries;
				}
				url += '&aroundLatLngViaIP=' + parent.options[parent.provider].aroundLatLngViaIP;
				if(parent.options[parent.provider].aroundLatLng){
					url += '&aroundLatLng='+ parent.options[parent.provider].aroundLatLng;
				}
				return url;
			},			
			format: function(options){
				var language = parent.options[parent.provider].language;
				var administrative = options.administrative;
		        var city = options.city;
		        var country = options.country;
		        var hit = options;
		        if(typeof hit._highlightResult.locale_names[0]!='undefined'){
		        	var name = hit._highlightResult.locale_names[0].value;
		        }else if(typeof hit._highlightResult.locale_names[language][0] != 'undefined'){
		        	var name = hit._highlightResult.locale_names[language][0].value;
		        }else{
		        	var name = '';
		        }
		        city = (city) ? hit._highlightResult.city[0].value : undefined;
		        administrative = (administrative && typeof hit._highlightResult.administrative != 'undefined')? hit._highlightResult.administrative[0].value : undefined;
		        country = (country)? hit._highlightResult.country.value : undefined;
		        var tags = options._tags;
			    var types = ['country', 'city', 'address'];
				var type = parent.get_unified_type(types, tags);
				var type_title = type.charAt(0).toUpperCase() + type.slice(1);
		        //noinspection UnnecessaryLocalVariableJS
				var out = ('<span class="ap-suggestion-icon ap-'+ type +'" title="'+ type_title +'">' + '</span>\n<span class="ap-name">' + name + '</span>\n<span class="ap-address">\n  ' + '\n  ' + (administrative ? administrative + ',' : '') + '\n  ' + (country ? '' + country : '') + '\n</span>').replace(/\s*\n\s*/g, ' ');
		        return out;
			},
			beforeSend: function(request){
				if(parent.options[parent.provider].appId && parent.options[parent.provider].apiKey){
                	request.setRequestHeader("X-Algolia-Application-Id", parent.options[parent.provider].appId);
                	request.setRequestHeader("X-Algolia-API-Key", parent.options[parent.provider].apiKey);
                }
			},
			displayError: function(response, xhrObj){
				if(xhrObj.status != 200){
					jQuery('#address').css('background', '#ffffcc');
					jQuery('#geocoding-provider-status').html('Algolia places error '+response.status+': ' + response.message);
				}
			}
		},
		photon: {
			footer: '<div class="ap-footer">' + footer_tips + '<div style="float:right;"><a href="https://www.mapsmarker.com/photon/" target="_blank"><img src="'+ lmm_ajax_vars.lmm_ajax_leaflet_plugin_url +'inc/img/geocoding/photon-mapsmarker-small.png" width="144" height="23"/></a></div><div style="float:right;margin:4px 5px 0 0;"><a href="https://www.mapsmarker.com/photon/" target="_blank">Powered by </a></div></div>',
			results_key: 'features',
			geo_key: 'geometry.coordinates',
			get_url: function(){
				var url = 'https://photon.mapsmarker.com/pro/api?q={query}&limit=10';
				if(parent.options[parent.provider].language){
					url += '&lang=' + parent.options[parent.provider].language;
				}
				if(parent.options[parent.provider].locationbiaslat!='none'){
					url += '&lat=' + parent.options[parent.provider].locationbiaslat;
				}
				if(parent.options[parent.provider].locationbiaslon!='none'){
					url += '&lon=' + parent.options[parent.provider].locationbiaslon;
				}
				if(parent.options[parent.provider].filter!='none'){
					url += '&osm_tag=' + parent.options[parent.provider].filter;
				}									
				return url;
			},
			format: function(options, text){
				var country = options.properties.country;
				var city = options.properties.city;
				var postcode = options.properties.postcode;
				var state = options.properties.state;
				var name = (typeof options.properties.name != 'undefined')?options.properties.name.replace(text, '<em>'+ text +'</em>'): '';
				var address = (state ? state + ',' : '') + '\n  ' + (country ? '' + country : '');
				address = address.replace(text, '<em>'+ text +'</em>');
				var tags = options.properties.osm_value;
			    var types = ['country', 'city', 'address'];
				var type = parent.get_unified_type(types, tags);
				var type_title = type.charAt(0).toUpperCase() + type.slice(1);
		        //noinspection UnnecessaryLocalVariableJS
				var out = ('<span class="ap-suggestion-icon ap-'+ type +'" title="'+ type_title +'">' + '</span>\n<span class="ap-name">' + name + '</span>\n<span class="ap-address">\n  ' + '\n  ' + address + '\n</span>').replace(/\s*\n\s*/g, ' ');
		        return out;
			},
			update_limit: function(xhrObj){
				var rate_limit = xhrObj.getResponseHeader('X-RateLimit-Limit-day');
				var remaining_limit = xhrObj.getResponseHeader('X-RateLimit-Remaining-day');
				jQuery('#photon-rate-limit-static').css('display','none');
				jQuery('#photon-rate-limit-dynamic').css('display','inline');
				jQuery('#photon-rate-limit').html(rate_limit);
				jQuery('#photon-rate-limit-consumed').html(rate_limit - remaining_limit);
			},
			displayError: function(response, xhrObj){
				var responseJSON = JSON.parse(xhrObj.responseText);
				if(xhrObj.status != 200){
					jQuery('#address').css('background', '#ffffcc');
					jQuery('#geocoding-provider-status').html('Photon@MapsMarker error: ' + responseJSON.message );
				}
			}
		},
		mapquest_geocoding: {
			footer: '<div class="ap-footer">' + footer_tips + '<div style="float:right;"><a href="https://www.mapsmarker.com/mapquest-geocoding" target="_blank"><img src="'+ lmm_ajax_vars.lmm_ajax_leaflet_plugin_url +'inc/img/geocoding/mapquest-logo-small.png" width="144" height="26"/></a></div><div style="float:right;margin:6px 5px 0 0;"><a href="https://www.mapsmarker.com/mapquest-geocoding" target="_blank">Powered by </a></div></div>',
			results_key: 'results.locations',
			geo_key: 'latLng',
			get_url: function(){
				var url = 'https://www.mapquestapi.com/geocoding/v1/address?location={query}&maxResults=10';
				if(parent.options[parent.provider].api_key){
					url += '&key=' + parent.options[parent.provider].api_key;
				}
				if(parent.options[parent.provider].boundingBox == 'enabled'){
					url += '&boundingBox=' + parent.options[parent.provider].lat1 +','+ parent.options[parent.provider].lon1 +','+ parent.options[parent.provider].lat2 +','+ parent.options[parent.provider].lon2;
				}
				return url;
			},
			format: function(options, text){
				var address = '';
				address += (typeof options.street != 'undefined' && options.street != '')?options.street+', ':'';
				address += (typeof options.adminArea5 != 'undefined' && options.adminArea5 != '')?options.adminArea5+', ':'';
				address += (typeof options.adminArea4 != 'undefined' && options.adminArea4 != '')?options.adminArea4+', ':'';
				address += (typeof options.adminArea3 != 'undefined' && options.adminArea3 != '')?options.adminArea3+', ':'';
				address += (typeof options.adminArea2 != 'undefined' && options.adminArea2 != '')?options.adminArea2+', ':'';
				address += (typeof options.adminArea1 != 'undefined' && options.adminArea1 != '')?options.adminArea1:'';
		
				var tags = options.geocodeQuality.toLowerCase();
			    var types = ['country', 'city', 'address'];
				var type = parent.get_unified_type(types, tags);
				var type_title = type.charAt(0).toUpperCase() + type.slice(1);
		        //noinspection UnnecessaryLocalVariableJS
				var out = ('<span class="ap-suggestion-icon ap-'+ type +'" title="'+ type_title +'">' + '' + '</span><span class="ap-name">'+ address.replace(text, '<em>'+ text +'</em>') +'\n\n</span>').replace(/\s*\n\s*/g, ' ');
		        return out;
			},
			displayError: function(response, xhrObj){
				if(xhrObj.status != 200){
					if(typeof options.street != 'undefined'){ //info: needed as long as CORS is not enabled
						if(response.info.statuscode != 0){
							jQuery('#geocoding-provider-status').html('Error: '+response.info.messages.join(', '));
						}
					} else {
						jQuery('#geocoding-provider-status').html(jQuery('#defaults_texts_mapquest_key_issue').html()+' ('+xhrObj.responseText+')');
					}
				}
			}
		},
		google_geocoding: {
			footer: '<div class="ap-footer">' + footer_tips + '<a href="https://www.mapsmarker.com/google-geocoding" target="_blank"><img src="'+ lmm_ajax_vars.lmm_ajax_leaflet_plugin_url +'inc/img/geocoding/powered-by-google.png" width="144" height="18" /></a></div>',
			results_key: 'results',
			geo_key: 'geometry.location',
			get_url: function(){
				var url = lmm_ajax_vars.lmm_ajax_admin_url.replace('wp-admin/', '') + 'mmp-api/google_places/autocomplete/?mmp_address={query}';
				if(parent.options[parent.provider].nonce){
					url += '&_wpnonce=' + parent.options[parent.provider].nonce;
				}
				return url;
			},
			format: function(options, text){
				var tags = options.types;
				var types = ['country', 'locality', 'street_address'];
				var type = parent.get_unified_type(types, tags);
				var type_title = type.charAt(0).toUpperCase() + type.slice(1);
				//noinspection UnnecessaryLocalVariableJS
				var out = ('<span class="ap-suggestion-icon ap-'+ type +'" title="'+ type_title +'">' + '</span><span class="ap-name">'+ options.formatted_address.replace(text, '<em>'+ text +'</em>') +'\n\n</span>').replace(/\s*\n\s*/g, ' ');
				return out;
			},
			displayError: function(response){
				if(response.status != 'OK' && response.status != 'ZERO_RESULTS'){
					jQuery('#address').css('background', '#ffffcc');
					jQuery('#geocoding-provider-status').html('Google Geocoding error: '+ response.status );
				}
			}
		}
	};

	var parent = this;

	/**
	* Initializing the places text field.
	**/
	this.init = function(){
		jQuery('#address').places_autocomplete({minLength:min_chars_start_search_as_you_type, hint: false, autoselect:true, openOnFocus:false, debug:false}, [{
	            source: function(q, cb) {
	            	if (typingTimer) clearTimeout(typingTimer); 
	            	typingTimer = setTimeout(function(){
						parent.query = jQuery('#address').val();
						if(parent.query.length >= parent.min_chars_start_search_as_you_type){
				            var ajaxObj = jQuery.ajax(
				              {
				                url: parent.providers[parent.provider].get_url().replace('{query}',parent.query),
				                type: 'GET',
				                beforeSend: function(request){
		                			jQuery('#address').addClass('results-loading');
		                			if(typeof parent.providers[parent.provider].beforeSend == 'function'){
			                			parent.providers[parent.provider].beforeSend(request);
		                			}
				                }
				              }
				              ).done(function(response, statusText, xhrObj) {
									if (response['status'] == 'GOOGLE-ERROR') {
										jQuery('#geocoding-provider-status').html('Google Geocoding error: '+ response['results']['status'] + ': ' + response['results']['error_message'] );
										//info: Performing fallback for Google Geocoding only (as original Google STATUS is OK on error too)
										jQuery('#address').places_autocomplete('destroy');
										parent.provider = parent.options.fallback;
										parent.init();
										jQuery('#address').focus();
										jQuery('#address').removeClass('results-loading');
										jQuery('#address').css('background', '#ffffcc');
										jQuery('#s2id_geocoding-provider-select').select2('val', parent.provider);
										jQuery('#geocoding-rate-limit-details').html(jQuery('#defaults_texts_geocoding_fallback_info').val());
									} else if (response['status'] == 'ZERO_RESULTS') {
										jQuery('#address').removeClass('results-loading');
								  	} else {
										if(parent.provider == 'mapquest_geocoding' && response['results'][0]['locations'].length != 0){
											jQuery.each(response['results'][0]['locations'], function(i,suggestion){
												response['results'][0]['locations'][i].name = jQuery(parent.providers[parent.provider].format(suggestion, parent.query)).text().trim();
											});
											cb(response['results'][0]['locations'], response);
										}else if(response[parent.providers[parent.provider].results_key].length != 0){
											var results_key = parent.providers[parent.provider].results_key;
											jQuery.each(response[results_key], function(i,suggestion){
												response[results_key][i].name = jQuery(parent.providers[parent.provider].format(suggestion, parent.query)).text().trim();
											});
											cb(response[results_key], response); //info: add .slice(0, 5) for limiting search results preview for Mapzen
										}
										jQuery('#address').removeClass('results-loading'); 
										//info: update rate limits
										if(typeof parent.providers[parent.provider].update_limit == 'function'){
											parent.providers[parent.provider].update_limit(xhrObj);
										}
										if(typeof parent.providers[parent.provider].displayError == 'function'){
											parent.providers[parent.provider].displayError(response, xhrObj);
										}
							  		}
				             }).fail(function(e){
				              		//display previous provider error
				              		if(typeof parent.providers[parent.provider].displayError == 'function'){
					            		parent.providers[parent.provider].displayError(e.responseJSON, e);
					            	}
					            	//info: save the address old value
					            	var address_value = jQuery('#address').val();
				              		//info: Performing fallbacks
				              		jQuery('#address').places_autocomplete('destroy');
				              		parent.provider = parent.options.fallback;
				              		parent.init();
				              		jQuery('#address').focus();
									var e = jQuery.Event("keydown");
									e.which = 50;
									jQuery("#address").trigger(e);
									jQuery('#address').val( address_value );
		                			jQuery('#address').removeClass('results-loading');
                                    jQuery('#address').css('background', '#ffffcc');
                                    jQuery('#s2id_geocoding-provider-select').select2('val', parent.provider);
                                    jQuery('#geocoding-rate-limit-details').html(jQuery('#defaults_texts_geocoding_fallback_info').val());
									setTimeout(function(){
										jQuery('#address').places_autocomplete('open');
									},500);
				              });
				          	}
			            }, parent.typingInterval);
	            },
	            displayKey: 'name',
	            templates: {
	              suggestion: function(suggestion) {
	            	var text = jQuery('#address').val();
	                return parent.providers[parent.provider].format(suggestion, text);
	              },
	              footer: parent.providers[parent.provider].footer,
				  header: '<div id="geocoding-results-header-div">' + jQuery("#defaults_texts_geocoding_results_header").val() + '</div> <div id="geocoding-results-header-image-div"><img src="'+ lmm_ajax_vars.lmm_ajax_leaflet_plugin_url +'inc/img/geocoding/icon-key-enter.png" width="" height="" alt="enter key icon" /></div><div style="clear:both;border-bottom:1px solid #ccc;"></div>'
	            } 
			}]).on('autocomplete:selected', function(event, suggestion){
				if(parent.provider != 'google_geocoding'){
					var geo_key = parent.providers[parent.provider].geo_key;
					if(geo_key.indexOf('.') != -1){
						geo_key = geo_key.split('.');
						if(parent.provider == 'mapzen_search' || parent.provider == 'photon'){
							parent.selected(suggestion[geo_key[0]][geo_key[1]][1], suggestion[geo_key[0]][geo_key[1]][0]);
						}else{
							parent.selected(suggestion[geo_key[0]][geo_key[1]].lat, suggestion[geo_key[0]][geo_key[1]].lng);
						}
					}else{
						parent.selected(suggestion[geo_key].lat, suggestion[geo_key].lng);
					}
				}else{
					//get lat\lng data from local Google Places endpoint.
					parent.google_places_selected(suggestion.place_id);
				}
		});
	};

	/*
	* Executed when a location selected
	* Updating the map and location lat and lng.
	*/
	this.selected = function(lat, lng){
		var map = selectlayer;
		var markerLocation = new L.LatLng(lat, lng);
		jQuery('.addmarker_link').attr('href',lmm_ajax_vars.lmm_ajax_admin_url +'admin.php?page=leafletmapsmarker_marker&addtoLayer='+ document.getElementById('id').value+'&lat='+lat.toFixed(6)+'&lon='+lng.toFixed(6));
		//info: layer page
		if(typeof mapcentermarker != 'undefined'){
			mapcentermarker.setLatLng(markerLocation);
			if(this.is_edit){
				mapcentermarker.bindPopup('<a class="addmarker_link" target="_blank" href="'+ lmm_ajax_vars.admin_url +'admin.php?page=leafletmapsmarker_marker&addtoLayer='+ document.getElementById('id').value+'&lat=' + lat.toFixed(6) + '&lon=' + lng.toFixed(6) + '&zoom=' + selectlayer.getZoom() +'" style="text-decoration:none;">' + document.getElementById('defaults_texts_add_new_marker_here').value + '</a>',
				{
					autoPan: true,
					closeButton: true,
					autoPanPadding: new L.Point(5,5)
				});
			}
		}
		//info: marker page
		if(typeof marker != 'undefined'){
			marker.setLatLng(markerLocation);
			document.getElementById('lat').value = lat.toFixed(6);
			document.getElementById('lon').value = lng.toFixed(6);
			document.getElementById('popup-address').innerHTML = document.getElementById('address').value;
		
			if (document.getElementById("openpopup").checked) { var openpopup_js = ".openPopup()"; } else { var openpopup_js = ""; };
			if(jQuery('#defaults_directions_popuptext_panel').val() == 'yes'){
				if (document.getElementById("popuptext").value != undefined) { var popup_panel_css_google = ""; } else { var popup_panel_css_google = "border-top:1px solid #f0f0e7;padding-top:5px;margin-top:5px;clear:both;"; }
				marker.bindPopup(document.getElementById("selectlayer-popuptext-hidden").innerHTML+"<div style=\""+popup_panel_css_google+"\">"+document.getElementById("address").value+" <a href=\""+ jQuery('#defaults_directions_link').val() +"\" target=\"_blank\" title=\"Get directions\">(Directions)</a>"+ jQuery('#directions_settings_link').html() +"</div>",{maxWidth: jQuery('#defaults_marker_popups_maxWidth').val(), minWidth: jQuery('#defaults_marker_popups_minWidth').val(), maxHeight: jQuery('#defaults_marker_popups_maxHeight').val(), autoPan: jQuery('#defaults_marker_popups_autoPan').val(), closeButton: jQuery('#defaults_marker_popups_closeButton').val(), autoPanPadding: new L.Point(jQuery('#defaults_marker_popups_autopanpadding_x').val(), jQuery('#defaults_marker_popups_autopanpadding_y').val())})+openpopup_js;
			}else{
				marker.bindPopup(document.getElementById("selectlayer-popuptext-hidden").innerHTML,{maxWidth: jQuery('#defaults_marker_popups_maxWidth').val(), minWidth: jQuery('#defaults_marker_popups_minWidth').val(), maxHeight: jQuery('#defaults_marker_popups_maxHeight').val(), autoPan: jQuery('#defaults_marker_popups_autoPan').val(), closeButton: jQuery('#defaults_marker_popups_closeButton').val(), autoPanPadding: new L.Point(jQuery('#defaults_marker_popups_autopanpadding_x').val(), jQuery('#defaults_marker_popups_autopanpadding_y').val())})+openpopup_js;
			}
		}
		map.setView(markerLocation, selectlayer.getZoom());
		jQuery('#layerviewlat').val(lat);
		jQuery('#layerviewlon').val(lng);
	};

	this.google_places_selected = function( place_id ){
		var details_endpoint = lmm_ajax_vars.lmm_ajax_admin_url.replace('wp-admin/', '') + 'mmp-api/google_places/details/?mmp_place_id={place_id}&_wpnonce={wpnonce}';
		jQuery.ajax(
          {
            url: details_endpoint.replace('{place_id}', place_id).replace('{wpnonce}', parent.options[parent.provider].nonce),
            type: 'GET',
            beforeSend: function(request){
    			jQuery('#address').addClass('results-loading');
    			if(typeof parent.providers[parent.provider].beforeSend == 'function'){
        			parent.providers[parent.provider].beforeSend(request);
    			}
            }
          }
        ).done(function(response, statusText, xhrObj){
    		jQuery('#address').removeClass('results-loading');
    		if(response.status == 'OK'){
	    		parent.selected(response.results.geometry.location.lat, response.results.geometry.location.lng);
    		}
        });
	};

	this.get_unified_type = function(types, tags){
		for (var typeIndex = 0; typeIndex < types.length; typeIndex++) {
		  var type = types[typeIndex];
		  if (tags.indexOf(type) !== -1) {
		    break;
		  }
		}
		switch(type){
			case 'address':
				return 'address';
				break;
			case 'street_address':
				return 'address';
				break;
			case 'city':
				return 'city';
				break;
			case 'locality':
				return 'city';
				break;
			case 'venue':
				return 'city';				
			case 'country':
				return 'country';
				break;
			default:
				return 'address';
		}
	}
};