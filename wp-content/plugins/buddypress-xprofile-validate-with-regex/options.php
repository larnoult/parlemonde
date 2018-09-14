<?php

/*
BuddyPress XProfile Validate with RegEx
Copyright (C) 2014  Tomasz "Tometzky" Ostrowski

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// Exit if accessed directly   
if ( !defined( 'ABSPATH' ) ) exit;

$meta_keys_descriptions = array(
	'validate_with_regex' => 'Validate with regular expression (PCRE):',
	'validate_with_regex_error_message' => 'Error message to show when validation fails:'
);

function buddypress_xprofile_validate_with_regex_options($field)
{
	global $meta_keys_descriptions;
	echo "<p>\n";
	foreach( $meta_keys_descriptions as $meta_key => $meta_description ) {
		$meta_value = bp_xprofile_get_meta( $field->id, 'field', $meta_key );
		printf('
			%1$s <input type="text"
				id="buddypress_xprofile_%2$s"
				name="buddypress_xprofile_%2$s"
				value="%3$s"><br />
			', $meta_description, $meta_key, esc_attr($meta_value)
		);
	}
	echo "</p>\n";
}
add_action('xprofile_field_additional_options','buddypress_xprofile_validate_with_regex_options');

function buddypress_xprofile_validate_with_regex_save($field)
{
	global $wpdb, $bp, $meta_keys_descriptions;
	
	$field_id = $field->id;
	if ( !isset($field_id) ) {
		$field_id = $wpdb->insert_id;
		// There could be another insert after field - a record with type set to 'option'
		// we need to use its 'parent_id' instead
		$sql = $wpdb->prepare(
			"SELECT parent_id from {$bp->profile->table_name_fields} WHERE id = %d",
			$field_id
		);
		$parent_id = $wpdb->get_var($sql);
		if ( isset($parent_id) ) {
			$field_id = $parent_id;
		}
	}
	
	foreach( array_keys($meta_keys_descriptions) as $meta_key ) {
		if ( isset($_POST["buddypress_xprofile_$meta_key"]) )
		{
			bp_xprofile_update_meta(
				$field_id,
				'field',
				$meta_key,
				$_POST["buddypress_xprofile_$meta_key"]
			);
		}
	}
}
add_action('xprofile_field_after_save','buddypress_xprofile_validate_with_regex_save');

?>
