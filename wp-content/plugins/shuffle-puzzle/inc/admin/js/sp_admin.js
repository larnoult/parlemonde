jQuery(document).ready(function($){
	var targetfield = '';
	var targetimg = '';
	var targetinput = '';
	var msp_send_to_editor = window.send_to_editor;
	var til_m = '';
	var imgurl = '';

	$("h2.title_m").click(function() {
		$(this).toggleClass('hide-block')
	});

	$("#msp_option_name").bind('keyup mouseup change', function () {
		$("input.msp_addbutton").prop('disabled', !(/^[a-zA-Z_]+$/).test($(this).val()));
	});

	$(document).on('focusout', '.code > .container textarea', function (argument) {
		var $temp_ = $(this).parents(".syntaxhighlighter").parent();
		var $temp_t = $temp_.next();
		$temp_t.val( $('.container textarea').val() );
		$temp_.remove();
		$temp_t.before("<pre class='brush: css;'></pre>");
		$temp_t.prev().text($temp_t.val());
		SyntaxHighlighter.highlight();
	});

	$( '#m_fild input, #m_fild_sh .spsl2' ).keypress(function(event) {
		var theEvent = event || window.event;
		var key = theEvent.keyCode || theEvent.which;
		key = String.fromCharCode(key);
		var regex = /[0-9]|\./;
		if( !regex.test(key) ) {
			theEvent.returnValue = false;
			if(theEvent.preventDefault) theEvent.preventDefault();
		}
	});

	function sp_reset_names() {
		$( '#sortable li input' ).each( function( index ) {
			var str = ($(this).attr('name')).split('[');
			$(this).attr( 'name', str[0]+'['+str[1].replace(/[\d\.]+/g, Math.floor(index/2)+1) );
			$(this).parent().find('.ui-num').text(Math.floor(index/2)+1);
			if(index>=38){
				$('#add-img').hide();
			}else if(index>=40){
				$(this).remove();				
			}else if(index<38){
				$('#add-img').show();
			}
		});
	}

	function sp_reset_m_tile() {
		var m_til = '';
		$( '#sortable_msh li' ).each( function( index ) {
			m_til+= $(this).find('span').text() + ',';
			if(index>=14){
				$('#add-menu-tiles').hide();
			}else if(index>=15){
				$(this).remove();				
			}else{
				$('#add-menu-tiles').show();
			}
		});
		$('.m_g').val(m_til.substr(0, m_til.length-1))
	}

	$("#sortable").sortable({
		axis: "y",
		stop: sp_reset_names,
		start: function(e, ui){
			ui.placeholder.height(Math.floor(ui.item.height()));
		}
	});

	$('#add-img').click(function(event) {
		event.preventDefault();
		var clone = $('.reserve').html();
		$('#sortable').append(clone);

		//alert('Now press the button "Save Settings"');
		sp_reset_names();
	});

	$('#sortable').on('click', '.remove-image', function(event) {
		event.preventDefault();
		//var message = 'Are you sure you want to remove this image?'
		//if (confirm(message)) {
		var $parent = $(this).parent();
		$('._poof').css({
			top: $parent.position().top
		});
		$('._poof').addClass('_poof_actions');

		$parent.css('visibility', 'hidden');
		setTimeout(function () {
			$('._poof').removeClass('_poof_actions');
			$parent.remove();
			sp_reset_names();
		}, 500);
		//sp_reset_names();
		//}
	});

	$('#sortable').on('click', '.add-image', function(event) {
		event.preventDefault();
		targetimg = $(this).nextAll('.img_min');
		targetinput = $(this).parents('.ui-sort-bg').children('input.widefat');

		tb_show('', 'media-upload.php?type=image&TB_iframe=true');
		window.send_to_editor = function(html) {
			imgurl = $(html).attr('src');
			if (!imgurl) {
				imgurl = $('img', html)[0].src;
			}
			$(targetimg).attr('src', imgurl);
			$(targetinput).val(imgurl);
			$(targetimg).parent().removeClass("add")
			tb_remove();
		}
	});

	$("#sortable_msh").sortable({
		stop: sp_reset_m_tile
	});

	$('#add-menu-tiles').click(function(event) {
		event.preventDefault();
		var clone = $('.reserve_til').html();
		$('#sortable_msh').append(clone);
		sp_reset_m_tile();
	});

	$('#sortable_msh').on('click', '.remove-tile', function(event) {
		event.preventDefault();
		$(this).parent().remove();
		sp_reset_m_tile();
	});
	
	$('#sortable_msh').on("click", function(event) {
		if($(event.target).is("span") ){
			til_m = $(event.target);
			var options = {
				"my": "center center",
				"at": "right center",
				"of": til_m
			};
			var tile_arr = til_m.text().split('x');
			$('.spl1').val(tile_arr[0]);
			$('.spl2').val(tile_arr[1]);
			$('#m_fild').show().position(options);
		}
	});

	$('.ok_img').on("click", function() {
		til_m.text($('.spl1').val()+'x'+$('.spl2').val());
		$('#m_fild').hide(100);
		sp_reset_m_tile();
	});

	$('.ok_img_').on("click", function() {
		til_m.text($('.spsl1').val()+":"+$('.spsl2').val());
		$('#m_fild_sh').hide(100);
		sp_reset_m_shu();
	});

	$("#sortable_mshuff").sortable({
		stop: sp_reset_m_shu
	});

	function sp_reset_m_shu() {
		var m_til = '';
		$( '#sortable_mshuff li' ).each( function( index ) {
			m_til+= $(this).find('span').text() + ',';
			if(index>=14){
				$('#add-menu-shuffle').hide();
			}else if(index>=15){
				$(this).remove();				
			}else{
				$('#add-menu-shuffle').show();
			}
		});
		$('.m_sh').val(m_til.substr(0, m_til.length-1))
	}
	
	$('#add-menu-shuffle').click(function(event) {
		event.preventDefault();
		var clone = $('.reserve_shu').html();
		$('#sortable_mshuff').append(clone);
		sp_reset_m_shu();
	});
	$('#sortable_mshuff').on('click', '.remove-shu', function(event) {
		event.preventDefault();
		$(this).parent().remove();
		sp_reset_m_shu();
	});

	$('#sortable_mshuff').on("click", function(event) {
		if($(event.target).is("span") ){
			til_m = $(event.target);
			var options = {
				"my": "center center",
				"at": "right center",
				"of": til_m
			};
			var tile_arr = til_m.text().split(':');
			$('.spsl1').val(tile_arr[0]);
			$('.spsl2').val(tile_arr[1]);
			$('#m_fild_sh').show().position(options);
		}
	});
	
	/*-------------------------------------------*/

	if ($("#msp_editwrap").length) {
		$("#msp_editwrap .msp_bgColor").farbtastic("#msp_bgColor");
	}
	$('html').click(function() {
		$("#msp_editwrap .farbtastic").fadeOut('fast');
	});
	$("#msp_editwrap .msp_colwrap").click(function(event) {
		$("#msp_editwrap .farbtastic").hide();
		$(this).find(".farbtastic").fadeIn('fast');
		event.stopPropagation();
	});
});

function msp_delconfirm(msp_item) {
	msp_item = " '" + msp_item + "' ";
	var msp_agree = confirm('Are you sure you want to delete ' + msp_item + '?');
	return msp_agree;
}