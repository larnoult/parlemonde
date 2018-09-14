jQuery(document).ready(function($) {

    $(function() {
	$(".datepicker").datepicker();
    });

    $("#bue_src_key_id").change(function() {

	console.log("bue_src_key_id changed ");
	var optionSelected = $("option:selected", this);
	var valueSelected = this.value;

	// alert( "Handler for .change() called with "+ valueSelected);
	$(".bue_data_source").hide("slow", function() {
	    var idToShow = "#bue_" + valueSelected + "_data_source";
	    console.log("show " + idToShow);
	    $(idToShow).show();
	});

    });

    $("#gf_form_key_id").change(function() {

	console.log("gf_form_key_id changed ");
	var optionSelected = $("option:selected", this);
	var valueSelected = this.value;
	console.log("gf changed " + valueSelected);

	data = {
	    action : 'bue_get_gf_form_fields',
	    form_id : valueSelected,

	};

	swal({
	    title : 'Working...',
	    text : 'retrieving GF fields',
	    type : "info",
	    showCloseButton : true,
	    showCancelButton : false,

	});

	$.post(ajaxurl, data, function(response) {
	    console.log(response);

	    var optionsAsString = "";
	    Object.keys(response).forEach(function(key) {

		optionsAsString += "<option value='" + key + "'>" + response[key] + "</option>";
	    });

	    $('select[class="gf_form_field_key_id"]').empty().append(optionsAsString);

	    $title = "Done!";
	    $type = "success"
	    var responseText = 'Form fields retrieved';
	    swal({
		title : $title,
		type : $type,
		text : responseText,
	    });

	});
	return false;
    });

    $('#bue-submit-post').click(function() {
	var formId = $(this).parents('form');
	console.log(formId);
	var fields = $(formId).serializeArray();
	console.log(fields);
	var actionCat = fields[0].value;
	var destCat = fields[1].value;
	var srcCat = fields[2].value;

	var start_date = fields[3].value;
	var end_date = fields[4].value;

	var boxText = actionCat + ' category "' + destCat + '" to all posts with category "' + srcCat;

	var confText = "Yes, update all!";

	if (start_date.length > 0 && end_date.length > 0) {
	    boxText = boxText + ', created between ' + start_date + ' and ' + end_date;
	}
	boxText = boxText + '" ?';
	console.log(boxText);

	var boxTitle = 'Operation not reversible!';
	swal({
	    title : boxTitle,
	    text : boxText,
	    type : "warning",
	    showCancelButton : true,
	    confirmButtonColor : '#3085d6',
	    cancelButtonColor : '#d33',
	    confirmButtonText : confText,

	}).then(function() {

	    data = {
		action : 'bue_modify_categories',
		catAction : actionCat,
		catDest : fields[1].value,
		postType : fields[2].value,
		catSrc : fields[3].value,
		start_date : fields[4].value,
		end_date : fields[5].value,
	    }
	    $.post(ajaxurl, data, function(response) {

		$title = "Done!";
		$type = "success"
		swal({
		    title : $title,
		    type : $type,
		    text : response,
		});

	    });
	    return false;

	});

	return false;

    });

    $('#bue-submit-gf').click(function() {
	// swal('clicked!');
	var formId = $(this).parents('form');
	console.log(formId);
	var fields = $(formId).serializeArray();
	console.log(fields);

	boxText = 'Sure ?';
	console.log(boxText);
	confText = 'Yes update!';
	var boxTitle = 'Operation not reversible!';
	swal({
	    title : boxTitle,
	    text : boxText,
	    type : "warning",
	    showCancelButton : true,
	    confirmButtonColor : '#3085d6',
	    cancelButtonColor : '#d33',
	    confirmButtonText : confText,

	}).then(function() {

	    swal({
		title : 'Working...',
		text : 'please wait (can be long)',
		type : "info",
		showCloseButton : true,
		showCancelButton : true,

	    });

	    data = {
		action : 'bue_modify_gf_fields',
	    }
	    Object.keys(fields).forEach(function(key) {
		data[fields[key].name] = fields[key].value;

	    });

	    console.log(data);

	    $.post(ajaxurl, data, function(response) {

		$title = "Done!";
		$type = "success"
		swal({
		    title : $title,
		    type : $type,
		    text : response,
		});

	    });
	    return false;

	});

	return false;

    });

    $('#bue-submit').click(function() {
	var formId = $(this).parents('form');
	console.log(formId);
	var fields = $(formId).serializeArray();
	console.log(fields);

	boxText = 'Sure ?';
	console.log(boxText);
	confText = 'Yes update!';
	var boxTitle = 'Operation not reversible!';
	swal({
	    title : boxTitle,
	    text : boxText,
	    type : "warning",
	    showCancelButton : true,
	    confirmButtonColor : '#3085d6',
	    cancelButtonColor : '#d33',
	    confirmButtonText : confText,

	}).then(function() {

	    swal({
		title : 'Working...',
		text : 'please wait (can be long)',
		type : "info",
		showCloseButton : true,
		showCancelButton : true,

	    });

	    data = {
		action : 'bue_modify_meta',
	    }
	    Object.keys(fields).forEach(function(key) {
		data[fields[key].name] = fields[key].value;

	    });

	    console.log(data);

	    $.post(ajaxurl, data, function(response) {

		$title = "Done!";
		$type = "success"
		swal({
		    title : $title,
		    type : $type,
		    text : response,
		});

	    });
	    return false;

	});

	return false;

    });

});