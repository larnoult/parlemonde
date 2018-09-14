jQuery(document).ready( function($) {
	$('a.neg-cap').attr('title',cmeAdmin.negationCaption);
	$('a.neg-type-caps').attr('title',cmeAdmin.typeCapsNegationCaption);
	$('td.cap-unreg').attr('title',cmeAdmin.typeCapUnregistered);
	$('a.normal-cap').attr('title',cmeAdmin.switchableCaption);
	$('span.cap-x').attr('title',cmeAdmin.capNegated);
	$('table.cme-checklist input[class!="cme-check-all"]').attr('title',cmeAdmin.chkCaption);
	
	$('table.cme-checklist a.neg-cap').click( function(e) {
		$(this).closest('td').removeClass('cap-yes').removeClass('cap-no').addClass('cap-neg');

		var cap_name_attr = $(this).parent().find('input[type="checkbox"]').attr('name');
		$(this).after('<input type="hidden" class="cme-negation-input" name="'+cap_name_attr+'" value="" />');
		
		return false;
	});
	
	//$('table.cme-typecaps span.cap-x,table.cme-checklist span.cap-x,table.cme-checklist td.cap-neg span').live( 'click', function(e) {
	$(document).on( 'click', 'table.cme-typecaps span.cap-x,table.cme-checklist span.cap-x,table.cme-checklist td.cap-neg span', function(e) {
		$(this).closest('td').removeClass('cap-neg').removeClass('cap-yes').addClass('cap-no');
		$(this).parent().find('input[type="checkbox"]').prop('checked',false);
		$(this).parent().find('input.cme-negation-input').remove();
		
		return false;
	});
	
	$('input.cme-check-all').click( function(e) {
		$(this).closest('table').find('input[type="checkbox"][disabled!="disabled"]:visible').prop('checked', $(this).is(":checked") );
	});
	
	$('a.cme-neg-all').click( function(e) {
		$(this).closest('table').find('a.neg-cap:visible').click();
		return false;
	});
	
	$('a.cme-switch-all').click( function(e) {
		$(this).closest('table').find('td.cap-neg span').click();
		return false;
	});
	
	$('table.cme-typecaps a.neg-type-caps').click( function(e) {
		$(this).closest('tr').find('td[class!="cap-neg"]').filter('td[class!="cap-unreg"]').each( function(e) {
			$(this).addClass('cap-neg');

			var cap_name_attr = $(this).parent().find('input[type="checkbox"]').attr('name');
			$(this).append('<input type="hidden" class="cme-negation-input" name="'+cap_name_attr+'" value="" />');
		});
		
		return false;
	});
	
	//http://stackoverflow.com/users/803925/nbrooks
	$('table.cme-typecaps th').click(function(){
		var columnNo = $(this).index();
		
		var check_val = ! $(this).prop('checked_all');
		
		if ( $(this).hasClass('term-cap') )
			var class_sel = '[class*="term-cap"]';
		else
			var class_sel = '[class*="post-cap"]';
		
		$(this).closest("table")
			.find("tr td" + class_sel + ":nth-child(" + (columnNo+1) + ') input[type="checkbox"]:visible')
			.prop("checked", check_val);
		
		$(this).prop('checked_all',check_val);
	});
});