<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

if( is_plugin_active( 'user-groups/user-groups.php' ) ){
	add_filter( 'acui_restricted_fields', 'acui_ug_restricted_fields', 10, 1 );
	add_action( 'acui_documentation_after_plugins_activated', 'acui_ug_documentation_after_plugins_activated' );
	add_action( 'post_acui_import_single_user', 'acui_ug_post_import_single_user', 10, 3 );
}

function acui_ug_restricted_fields( $acui_restricted_fields ){
	return array_merge( $acui_restricted_fields, array( 'user_group' ) );
}

function acui_ug_documentation_after_plugins_activated(){
	?>
	<tr valign="top">
		<th scope="row"><?php _e( "WP Users Group is activated", 'import-users-from-csv-with-meta' ); ?></th>
		<td>
			<?php _e( "You can import user groups and assign them to the users using the next format", 'import-users-from-csv-with-meta' ); ?>.
			<ul style="list-style:disc outside none; margin-left:2em;">
				<li><?php _e( "user_group as the column title", 'import-users-from-csv-with-meta' ); ?></li>
				<li><?php _e( "The value of each cell will be the name of the user group (do not use slugs)", 'import-users-from-csv-with-meta' ); ?></li>
				<li><?php _e( "If you want to import multiple values, you can use a list using commas to separate items", 'import-users-from-csv-with-meta' ); ?></li>
			</ul>
		</td>
	</tr>
	<?php
}

function acui_ug_post_import_single_user( $headers, $row, $user_id ){
	$pos = array_search( 'user_group', $headers );

	if( $pos === FALSE )
		return;

	$user_groups = explode( ',', $row[ $pos ] );
	$user_groups = array_filter( $user_groups, function( $value ){ return $value !== ''; } );

	$taxonomy = 'user-group';
	$terms = array();

	foreach ( $user_groups as $user_group ) {
		$term = get_term_by( 'name', $user_group , $taxonomy );
		
		if( $term == false ){
		    $term = wp_insert_term( $user_group, $taxonomy);
		    $terms[] = $term['term_id'];
		}else{
			$terms[] = $term->term_id;
		}
	}

	wp_set_object_terms( $user_id, $terms, $taxonomy, false );
	clean_object_term_cache( $user_id, $taxonomy );
}	