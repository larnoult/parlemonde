/*
 * search, order & delete actions + MMPAPI select2 activation
 */
jQuery(document).ready(function($){
	
	var current_page = lmm_ajax_vars.lmm_ajax_current_page;
	if ( (current_page === 'leafletmapsmarker_markers') || (current_page === 'leafletmapsmarker_layers') ){
	
		var old_results =  $('#the-list').html();
		var old_mcount =  $('#totalmarkers').html();
		$('input[name=searchsubmit]').click(function(e){
			e.preventDefault();
			var layer = $('#layerfilter').val();
			var val = $('#searchtext').val();
			lmm_get_search_markers_result(val, layer);
		});
		$('#searchtext').keyup(function(){
			var layer = $('#layerfilter').val();
			var val = $('#searchtext').val();
			lmm_get_search_markers_result(val, layer);
		});
		$('#layerfilter').change(function(){
			var layer = $('#layerfilter').val();
			var val = $('#searchtext').val();
			lmm_get_search_markers_result(val, layer);
		});
		$('.tablenav-pages').on('click','a.first-page',function(e){
			e.preventDefault();
			var old_tablenav_pages = $('.tablenav-pages').html();
			var page_number = $(this).html();
			$('.current-page').removeClass('current-page');
			var page_link_element = this;
			$.ajax({
				url:ajaxurl,
				data: {
					action: 'mapsmarker_ajax_actions_backend',
					lmm_ajax_subaction: $('#lmm_list_maps_subaction').val(),
					lmm_ajax_nonce: lmm_ajax_vars.lmm_ajax_nonce,
					paged: page_number,
					orderby: $('input[name=orderby]').val(),
					order: $('input[name=order]').val(),
					'layerfilter': $('#layerfilter').val(),
					'searchtext': $('#searchtext').val().trim()
				},
				beforeSend: function(){
					$('.tablenav-pages').html('<img src="'+   lmm_ajax_vars.lmm_ajax_leaflet_plugin_url	+'inc/img/paging-ajax-loader.gif"/>');
				},
				method:'POST',
				success: function(response){
					var results = response.replace(/^\s*[\r\n]/gm, '');
					var results = results.match(/!!LMM-AJAX-START!!(.*[\s\S]*)!!LMM-AJAX-END!!/)[1];
					var res = JSON.parse(results);
					$('#the-list').html(res.rows);
					$('#totalmarkers').html(res.mcount);
					$('.tablenav-pages').html(res.pager);
					$(page_link_element).addClass('current-page');
				}
			});
	
		});
		function lmm_get_search_markers_result(val, layer){
			//info only if user wrote a word more than 2 letters
			//if(val.length > 2){
				$.ajax({
					url:ajaxurl,
					data: {
						action: 'mapsmarker_ajax_actions_backend',
						lmm_ajax_subaction: $('#lmm_list_maps_subaction').val() + '_search',
						lmm_ajax_nonce: lmm_ajax_vars.lmm_ajax_nonce,
						'layerfilter': layer,
						'searchtext': val.trim()
					},
					beforeSend: function(){
						$('.lmm-search-markers').addClass('searchtext_loading');
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
							$('#search_title').html($('#defaults_texts_search_result').val());
							$('#totalmarkers').html(res.mcount);
							$('#search_reset_link').css('display','inline');
							$('.tablenav-pages').html(res.pager);
							$('#searchtext').removeClass('searchtext_loading');
						}
	
	
					}
				});
			/*}else{
				$('#the-list').html(old_results);
				$('#totalmarkers').html(old_mcount);
			}*/
		}

		//info: reset search & filter results
		jQuery('#search_reset_link').click(function(e){
			$.ajax({
				url:ajaxurl,
				data: {
					action: 'mapsmarker_ajax_actions_backend',
					lmm_ajax_subaction: $('#lmm_list_maps_subaction').val() + '_search',
					lmm_ajax_nonce: lmm_ajax_vars.lmm_ajax_nonce,
					'layerfilter': 0,
					'searchtext': ''
				},
				beforeSend: function(){
					$('.lmm-search-markers').addClass('searchtext_loading');
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
						$('#search_title').html($('#defaults_texts_total').val());
						$('#searchtext').val('');
						$('#totalmarkers').html(res.mcount);
						$('#search_reset_link').css('display','none');
						$('.tablenav-pages').html(res.pager);
						$('#searchtext').removeClass('searchtext_loading');
						if (current_page === 'leafletmapsmarker_markers') {
							$('#layerfilter').val(0)
						}
					}
				}
			});
		});

		// Check multi-layer-maps for bulk actions
		jQuery('#bulk-actions-btn').click(function(e){
			if(jQuery('#duplicatelayerandmarkers').is(':checked') === true){
				jQuery('input[name="checkedlayers[]"]').each(function(i, layer){
					if(jQuery(layer).is(':checked') === true){
						if(jQuery(layer).attr('data-layertype') == 'mlm'){
							e.preventDefault();
							var mlm_validation = $('#defaults_texts_mlm_validation').val();
							alert(mlm_validation);
						}
					}
				});
			}
		});
	} else if (current_page === 'leafletmapsmarker_apis') {
		//info: initiating select2 to select users in the api keys page.
		jQuery('#mmp_select_user').select2();
	} 
});

//info: delete action
function lmm_delete_layer( layer_id ){
	if ( confirm( 'Do you really want to delete layer ID ' + layer_id + '? (markers assigned to this layer will be unassigned but not be deleted)' ) ) {
		jQuery.ajax({
				url:ajaxurl,
				data: {
					action: 'mapsmarker_ajax_actions_backend',
					lmm_ajax_subaction: 'layer-delete',
					lmm_ajax_nonce: lmm_ajax_vars.lmm_ajax_nonce,
					'id': layer_id
				},
				method:'POST',
				success: function(response){
					var results = response.replace(/^\s*[\r\n]/gm, '');
					var results = results.match(/!!LMM-AJAX-START!!(.*[\s\S]*)!!LMM-AJAX-END!!/);
					if(results !== null){
						results = results[1];
						var res = JSON.parse(results);
						if(res['status-class'] == 'notice notice-success'){
							jQuery('#link-' + layer_id).css('background', '#EA7C7C');
							jQuery('#link-' + layer_id).hide('slow');
						}
					}
				}
			}
		);
	}
} 
