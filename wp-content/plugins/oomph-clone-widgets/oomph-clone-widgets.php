<?php
/*
Plugin Name: Oomph Clone Widgets
Plugin URI: http://www.thinkoomph.com/plugins-modules/oomph-clone-widgets/
Description: Add a "+" button on Widgets that will copy them along with all of their settings into a new widget.
Author: Ben Doherty @ Oomph, Inc.
Version: 2.0.0
Author URI: http://www.thinkoomph.com/thinking/author/ben-doherty/
License: GPLv2 or later

		Copyright © 2013 Oomph, Inc. <http://oomphinc.com>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/ 

/**
 * @package Oomph Clone Widgets
 */
class Oomph_Clone_Widgets {
	function __construct() {
		add_filter( 'admin_head', array( $this, 'clone_script'  )  );
	}

	function clone_script() {
		global $pagenow;

		if( $pagenow != 'widgets.php' ) 
			return;
?>
<style>
	.oomph-cloneable .clone-widget-action { float: left; }
	.oomph-cloneable .widget-title h4 { padding-left: 0; }
	.oomph-cloneable a.clone-widget::after { content: "\f132"; }
	.oomph-cloneable:hover a.clone-widget { margin-left: 0; }
</style>
<script>
(function($) {
	if(!window.Oomph) window.Oomph = {};

	Oomph.CloneWidgets = {
		init: function() {
			$('#widgets-right').on('click', 'a.clone-widget', Oomph.CloneWidgets.Clone);
			Oomph.CloneWidgets.Bind();
		},

		Bind: function() {
			$('#widgets-right').off('DOMSubtreeModified', Oomph.CloneWidgets.Bind);
			$('#widgets-right .widget:not(.oomph-cloneable)').each(function() {
				var $widget = $(this);

				$widget.addClass('oomph-cloneable')
					.find('.widget-top')
					.prepend('<div class="widget-title-action clone-widget-action"><a href="javascript:void(0);" class="clone-widget widget-action" title="Clone this Widget"></a></div>');
				$widget.addClass('oomph-cloneable');
			});
			$('#widgets-right').on('DOMSubtreeModified', Oomph.CloneWidgets.Bind);
		},

		Clone: function(ev) {
			var $original = $(this).parents('.widget');
			var $widget = $original.clone();
			
			// Find this widget's ID base. Find its number, duplicate.
			var idbase = $widget.find('input[name="id_base"]').val();
			var number = $widget.find('input[name="widget_number"]').val();
			var mnumber = $widget.find('input[name="multi_number"]').val();
			var highest = 0;

			$('input.widget-id[value|="' + idbase + '"]').each(function() {
				var match = this.value.match(/-(\d+)$/);
				if(match && parseInt(match[1]) > highest)
					highest = parseInt(match[1]);
			});

			var newnum = highest + 1;	

			$widget.find('.widget-content').find('input,select,textarea').each(function() {
				if($(this).attr('name'))
					$(this).attr('name', $(this).attr('name').replace(number, newnum));
			});
			
			// assign a unique id to this widget:
			var highest = 0;
			$('.widget').each(function() {
				var match = this.id.match(/^widget-(\d+)/);

				if(match && parseInt(match[1]) > highest)
					highest = parseInt(match[1]);
			});
			var newid = highest + 1;

			// Figure out the value of add_new from the source widget:
			var add = $('#widget-list .id_base[value="' + idbase + '"]').siblings('.add_new').val();	
			$widget[0].id = 'widget-' + newid + '_' + idbase + '-' + newnum;
			$widget.find('input.widget-id').val(idbase+'-'+newnum);
			$widget.find('input.widget_number').val(newnum);
			$widget.hide();
			$original.after($widget);
			$widget.fadeIn();

			// Not exactly sure what multi_number is used for.
			$widget.find('.multi_number').val(newnum);

			wpWidgets.save($widget, 0, 0, 1);

			ev.stopPropagation();
			ev.preventDefault();
		}
	}

	$(Oomph.CloneWidgets.init);
})(jQuery);

</script>
<?php
	}
}

$GLOBALS['Oomph_Clone_Widgets'] = new Oomph_Clone_Widgets();
