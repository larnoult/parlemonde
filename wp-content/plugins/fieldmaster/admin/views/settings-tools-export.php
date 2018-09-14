<?php 

// vars
$field_groups = fieldmaster_extract_var( $args, 'field_groups');


// replace
$str_replace = array(
	"  "			=> "\t",
	"'!!__(!!\'"	=> "__('",
	"!!\', !!\'"	=> "', '",
	"!!\')!!'"		=> "')"
);

$preg_replace = array(
	'/([\t\r\n]+?)array/'	=> 'array',
	'/[0-9]+ => array/'		=> 'array'
);

?>
<div class="wrap fieldmaster-settings-wrap">
	
	<h1><?php _e('Tools', 'fieldmaster'); ?></h1>
	
	<div class="fieldmaster-box">
		<div class="title">
			<h3><?php _e('Export Field Groups to PHP', 'fieldmaster'); ?></h3>
		</div>
		
		<div class="inner">
			<p><?php _e("The following code can be used to register a local version of the selected field group(s). A local field group can provide many benefits such as faster load times, version control & dynamic fields/settings. Simply copy and paste the following code to your theme's functions.php file or include it within an external file.", 'fieldmaster'); ?></p>
			
			<textarea class="pre" readonly="true"><?php
			
			echo "if( function_exists('fieldmaster_add_local_field_group') ):" . "\r\n" . "\r\n";
			
			foreach( $field_groups as $field_group ) {
						
				// code
				$code = var_export($field_group, true);
				
				
				// change double spaces to tabs
				$code = str_replace( array_keys($str_replace), array_values($str_replace), $code );
				
				
				// correctly formats "=> array("
				$code = preg_replace( array_keys($preg_replace), array_values($preg_replace), $code );
				
				
				// esc_textarea
				$code = esc_textarea( $code );
				
				
				// echo
				echo "fieldmaster_add_local_field_group({$code});" . "\r\n" . "\r\n";
			
			}
			
			echo "endif;";
			
			?></textarea>
            
		</div>
		
	</div>
	
</div>
<div class="fieldmaster-hidden">
	<style type="text/css">
		textarea.pre {
			width: 100%;
			padding: 15px;
			font-size: 14px;
			line-height: 1.5em;
			resize: none;
		}
	</style>
	<script type="text/javascript">
	(function($){
		
		var i = 0;
		
		$(document).on('click', 'textarea.pre', function(){
			
			if( i == 0 )
			{
				i++;
				
				$(this).focus().select();
				
				return false;
			}
					
		});
		
		$(document).on('keyup', 'textarea.pre', function(){
		
		    $(this).height( 0 );
		    $(this).height( this.scrollHeight );
		
		});
	
		$(document).ready(function(){
			
			$('textarea.pre').trigger('keyup');
	
		});
	
	})(jQuery);
	</script>
</div>