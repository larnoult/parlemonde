<?php
/**
 * Options Page For Default Images
 *
 * @package   Quick_Featured_Images_Defaults
 * @author    Martin Stehle <m.stehle@gmx.de>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins/quick-featured-images/
 * @copyright 2014 
 */
#dambedei( $this->selected_rules );
// define some variables
$no_thumb_url = includes_url() . 'images/blank.gif';

// store recurring translations only once for more performance
$matches_label      = esc_html__( 'matches', 'quick-featured-images' );
$number_label       = esc_html__( 'No.', 'quick-featured-images' );

// WP core strings
$text = 'Save Changes';
$button_label		= __( $text ); // will be escaped by submin_button()
$text = 'Choose Image';
$choose_image_label	= esc_attr__( $text );
$text = 'Taxonomy:';
$taxonomy_label		= esc_html__( $text );
$text = 'Action';
$action_label		= esc_html__( $text );
$text = 'Description';
$description_label  = esc_html__( $text );
$text = 'Image';
$image_label		= esc_html__( $text );
$text = 'Value';
$value_label		= esc_html__( $text );
$text = 'Author';
$user_label		= esc_html__( $text );
$text = '&mdash; Select &mdash;';
$first_option_label = esc_html__( $text );
$text = 'Featured Image';
$feat_img_label 	= esc_attr( _x( $text, 'post' ) );
$text = 'Category';
$category_label 	= esc_html( _x( $text, 'taxonomy singular name' ) );
$text = 'Tag';
$tag_label 			= esc_html( _x( $text, 'taxonomy singular name' ) );
$text = 'Post';
$post_label		= esc_html( _x( $text, 'post type singular name' ) );
$text = 'Page';
$page_label		= esc_html( _x( $text, 'post type singular name' ) );

// set parameters for term queries
$args = array( 
	'orderby'       => 'name', 
	'order'         => 'ASC',
	'hide_empty'    => false, 
	'hierarchical'  => true, 
);

// set options fields
$optionfields = array(
	'post_type' => __( 'Post Type', 'quick-featured-images' ),
	'category' => $category_label,
	'post_tag' => $tag_label,
	'user' => $user_label,
);

// get stored tags
$tags = get_tags( $args );

// get stored categories
$categories = get_categories( $args );

// get authors: Return List all blog editors, return limited fields in resulting row objects:
$user_query = new WP_User_Query( array( 
	'who' => 'authors', 
	'fields' => array( 'ID', 'user_nicename', 'display_name' ),
	'order' => 'ASC',
    'orderby' => 'display_name'
) );
$user_data = $user_query->get_results();
// make selection box entries
$users = array();
if ( 0 < count( $user_data ) ) {
	// loop through each author
	foreach ( $user_data as $user ) {
		$users[] = array( 'id' => $user->ID, 'name' => sprintf( '%s (%s)', $user->display_name, $user->user_nicename ) );
	}
}

// get stored post types
$custom_post_types = $this->get_custom_post_types_labels();

// get stored taxonomies
$custom_taxonomies = $this->get_custom_taxonomies_labels();
$custom_taxonomies_terms = array();
if ( $custom_taxonomies ) {
	foreach ( $custom_taxonomies as $key => $label ) {
		$options = array();
		$terms = get_terms( $key, $args );
		if ( is_wp_error( $terms ) ) {
			printf( '<p>%s<p>', esc_html( $terms->get_error_message() ) );
			continue;
		}
		if ( 0 < count( $terms ) ) {
			foreach ( $terms as $term ) {
				$custom_taxonomies_terms[ $key ][ $term->term_id ] = $term->name;
			}
			if ( isset( $this->selected_custom_taxonomies[ $key ] ) ) {
				$selected_tax = $this->selected_custom_taxonomies[ $key ];
			} else {
				$selected_tax = '';
			}
		}
	}
}

// print jQuery for pulldowns
?>
<script type="text/javascript">
jQuery( document ).ready( function( $ ){

/*
 * build arrays of options
 */
var options = new Array();
<?php
// build post type options
$key = 'post_type';
printf( 'options[ \'%s\' ] = new Array();', $key );
print "\n";
printf( 'options[ \'%s\' ].push( \'<option value="">%s</option>\' );', $key, $first_option_label );
print "\n";
printf( 'options[ \'%s\' ].push( \'<option value="%s">%s</option>\' );', $key, 'post', $post_label );
print "\n";
printf( 'options[ \'%s\' ].push( \'<option value="%s">%s</option>\' );', $key, 'page', $page_label );
print "\n";
foreach ( $custom_post_types as $name => $label ) {
	printf( 'options[ \'%s\' ].push( \'<option value="%s">%s</option>\' );', $key, esc_attr( $name ), esc_html( $label ) );
	print "\n";
}

// build tag options
$key = 'post_tag';
printf( 'options[ \'%s\' ] = new Array();', $key );
print "\n";
printf( 'options[ \'%s\' ].push( \'<option value="">%s</option>\' );', $key, $first_option_label ); 
print "\n";
foreach ( $tags as $tag ) {
	printf( 'options[ \'%s\' ].push( \'<option value="%d">%s</option>\' );', $key, absint( $tag->term_id ), esc_html( $tag->name ) );
	print "\n";
}

// build category options
$key = 'category';
printf( 'options[ \'%s\' ] = new Array();', $key );
print "\n";
printf( 'options[ \'%s\' ].push( \'<option value="">%s</option>\' );', $key, $first_option_label );
print "\n";
foreach ( $categories as $category ) {
	printf( 'options[ \'%s\' ].push( \'<option value="%d">%s</option>\' );', $key, absint( $category->term_id ), esc_html( $category->name ) );
	print "\n";
}

// build custom taxonomy options
if ( $custom_taxonomies_terms ) {
	foreach ( array_keys( $custom_taxonomies_terms ) as $key ) {
		printf( 'options[ \'%s\' ] = new Array();', $key );
		print "\n";
		printf( 'options[ \'%s\' ].push( \'<option value="">%s</option>\' );', $key, $first_option_label );
		print "\n";
 		foreach ( $custom_taxonomies_terms[ $key ] as $term_id => $term_name ) {
			printf( 'options[ \'%s\' ].push( \'<option value="%d">%s</option>\' );', $key, absint( $term_id ), esc_html( $term_name ) );
			print "\n";
		}
	}
} // if ( custom_taxonomies_terms )

// build user options
$key = 'user';
printf( 'options[ \'%s\' ] = new Array();', $key );
print "\n";
printf( 'options[ \'%s\' ].push( \'<option value="">%s</option>\' );', $key, $first_option_label );
print "\n";
foreach ( $users as $user ) {
	printf( 'options[ \'%s\' ].push( \'<option value="%d">%s</option>\' );', $key, absint( $user[ 'id' ] ), esc_html( $user[ 'name' ] ) );
	print "\n";
}
?>
	 /*
	 * Options changes
	 */
	 $( '.selection_rules' ).live( 'change', function() {
		// get number of row
		var row_number = this.id.match( /[0-9]+/ );
		// set selector names
		var selector_taxonomy = '#taxonomy_' + row_number;
		var selector_matchterm = '#matchterm_' + row_number;
		// change 'value' selection on change of 'taxonomy' selection
		$( selector_taxonomy + ' option:selected' ).each( function() {
			$( selector_matchterm ).html( options[ $( this ).val() ].join( '' ));
		} );
	} )
} )
</script>

<h2 class="no-bottom"><?php esc_html_e( 'Default featured images for future posts', 'quick-featured-images' ); ?></h2>
<div class="qfi_page_description">
	<p><?php esc_html_e( 'Define the rules to use images as default featured images automatically every time a post is saved.', 'quick-featured-images' ); ?></p>
	<p><?php esc_html_e( 'To use a rule choose the image and set both the taxonomy and the value. A rule which is defined only partially will be ignored.', 'quick-featured-images' ); ?></p>
</div>

<?php 
if ( ! current_theme_supports( 'post-thumbnails' ) ) {
?>
<h2 class="no-bottom"><?php esc_html_e( 'Notice', 'quick-featured-images' ); ?></h2>
<div class="qfi-failure">
	<p><?php esc_html_e( 'The current theme does not support featured images. Anyway you can use this plugin. The effects are stored and will be visible in a theme which supports featured images.', 'quick-featured-images' ); ?></p>
</div>
<?php 
}
?>

<form method="post" action="">
	<table class="widefat">
		<thead>
			<tr>
				<th class="num"><?php echo $number_label; ?></th>
				<th><?php echo $image_label; ?></th>
				<th><?php echo $description_label; ?></th>
				<th><?php echo $action_label; ?></th>
			</tr>
		</thead>
		<tbody>
			<tr id="row_1" class="alternate">
				<td class="num">1</td>
				<td>
					<?php printf( '<img src="%s" alt="%s" width="80" height="80" />', plugins_url( 'assets/images/overwrite-image.jpg' , dirname( __FILE__ ) ), esc_attr__( 'An image overwrites an existing image', 'quick-featured-images' ) ); ?><br />
				</td>
				<td>
					<p>
						<label><input type="checkbox" name="overwrite_automatically" value="1"<?php checked( isset( $this->selected_rules[ 'overwrite_automatically' ] ), '1' ); ?>><?php esc_html_e( 'Activate to automatically overwrite an existing featured image while saving a post', 'quick-featured-images' ); ?></label>
					</p>
					<p class="description"><?php esc_html_e( 'If activated the rule is used automatically while saving a post to overwrite an existing featured image with the new one based on the following rules. Do not use this if you want to keep manually set featured images.', 'quick-featured-images' ); ?></p>
				</td>
				<td></td>
			</tr>
			<tr id="row_2">
				<td class="num">2</td>
				<td>
					<?php printf( '<img src="%s" alt="%s" width="80" height="80" />', plugins_url( 'assets/images/first-content-image.gif' , dirname( __FILE__ ) ), esc_attr__( 'Text with images in WordPress editor', 'quick-featured-images' ) ); ?><br />
				</td>
				<td>
					<p>
						<label><input type="checkbox" value="1" name="use_first_image_as_default"<?php  checked( isset( $this->selected_rules[ 'use_first_image_as_default' ] ), '1' ); ?>><?php esc_html_e( 'Activate to automatically use the first content image if available in the media library as featured image while saving a post', 'quick-featured-images' ); ?></label>
					</p>
					<p class="description"><?php esc_html_e( 'If activated the rule is used automatically while saving a post to set the first content image - if available in the media library - as the featured image of the post. If the post has no content images the next rules will be applied.', 'quick-featured-images' ); ?></p>
					<p><?php esc_html_e( 'For which post types should this rule be applied?', 'quick-featured-images' ); ?></p>
					<p>
<?php
// backward compatibility: set "all post types" if no setting available
if ( empty( $this->selected_rules[ 'post_types_1st_image' ] ) ) {
	$this->selected_rules[ 'post_types_1st_image' ][] = 'post';
	$this->selected_rules[ 'post_types_1st_image' ][] = 'page';
	foreach( array_keys( $custom_post_types ) as $key ) {
		$this->selected_rules[ 'post_types_1st_image' ][] = $key;
	}
}
?>
						<label><input type="checkbox" name="post_types_1st_image[]" value="post"<?php checked( in_array( 'post', $this->selected_rules[ 'post_types_1st_image' ] ) ); ?>><?php echo $post_label; ?></label><br>
						<label><input type="checkbox" name="post_types_1st_image[]" value="page"<?php checked( in_array( 'page', $this->selected_rules[ 'post_types_1st_image' ] ) ); ?>><?php echo $page_label; ?></label><?php
$c = count( $custom_post_types );
$i = 0;
foreach ( $custom_post_types as $key => $label ) {
	if ( $i < $c ) {
		print "<br>\n";
	}
	$i++;
?>
						<label><input type="checkbox" name="post_types_1st_image[]" value="<?php echo esc_attr( $key ); ?>"<?php checked( in_array( $key, $this->selected_rules[ 'post_types_1st_image' ] ) ); ?>><?php esc_html_e( $label ); ?></label>
<?php
}
?>
					</p>
					<p class="description"><?php esc_html_e( 'Select at least one post type, otherwise all post types will be considered.', 'quick-featured-images' ); ?></p>
				</td>
				<td></td>
			</tr>
<?php
$c = 3;
if ( isset( $this->selected_rules[ 'rules' ] ) ) {
	foreach ( $this->selected_rules[ 'rules' ] as $rule ) {
		// only consider valid values
		if ( '0' == $rule[ 'id' ] ) continue;
		if ( '' == $rule[ 'taxonomy' ] ) continue;
		if ( '' == $rule[ 'matchterm' ] ) continue;
		// alternate row color
		if( 0 != $c % 2 ) { // if c is odd
			$row_classes = ' class="alternate"';
		} else {
			$row_classes = '';
		}
		$r_id = absint( $rule[ 'id' ] );
?>
			<tr id="row_<?php echo $c; ?>"<?php echo $row_classes; ?>>
				<td class="num"><?php echo $c; ?></td>
				<td>
					<input type="hidden" value="<?php echo $r_id; ?>" name="rules[<?php echo $c; ?>][id]" id="image_id_<?php echo $c; ?>">
					<img src="<?php echo wp_get_attachment_thumb_url( $r_id ); ?>" alt="<?php echo $feat_img_label; ?>" id="selected_image_<?php echo $c; ?>" class="attachment-thumbnail qfi_preset_image">
				</td>
				<td>
					<input type="button" name="upload_image_<?php echo $c; ?>" value="<?php echo $choose_image_label; ?>" class="button imageupload" id="upload_image_<?php echo $c; ?>"><br />
					<label for="taxonomy_<?php echo $c; ?>"><?php echo $taxonomy_label; ?></label><br />
					<select name="rules[<?php echo $c; ?>][taxonomy]" id="taxonomy_<?php echo $c; ?>" class="selection_rules">
						<option value=""><?php echo $first_option_label; ?></option>
<?php
		$key = $rule[ 'taxonomy' ];
		foreach ( $optionfields as $value => $label ) {
?>
						<option value="<?php echo $value; ?>"<?php selected( $value == $key, true ); ?>><?php echo $label; ?></option>
<?php
		} // foreach ( $optionfields )
		if ( $custom_taxonomies_terms ) {
			foreach ( $custom_taxonomies as $custom_key => $label ) {
				if ( $custom_key and $label ) { // ommit empty or false values
?>
						<option value="<?php echo esc_attr( $custom_key ); ?>"<?php selected( $custom_key == $rule[ 'taxonomy' ], true ); ?>><?php echo esc_html( $label ); ?></option>
<?php
				}
			}
		} // if ( $custom_taxonomies_terms )
?>
					</select><br />
					<?php echo $matches_label; ?>:<br />
					<label for="matchterm_<?php echo $c; ?>"><?php echo $value_label; ?></label><br />
					<select name="rules[<?php echo $c; ?>][matchterm]" id="matchterm_<?php echo $c; ?>">
<?php
		switch( $rule[ 'taxonomy' ] ) {
			case 'post_type':
?>
						<option value=""><?php echo $first_option_label; ?></option>
						<option value="post"<?php selected( 'post' == $rule[ 'matchterm' ], true ); ?>><?php echo $post_label; ?></option>
						<option value="page"<?php selected( 'page' == $rule[ 'matchterm' ], true ); ?>><?php echo $page_label; ?></option>
<?php
				foreach ( $custom_post_types as $key => $label ) {
?>
						<option value="<?php echo esc_attr( $key ); ?>"<?php selected( $key == $rule[ 'matchterm' ], true); ?>><?php echo esc_html( $label ); ?></option>
<?php
				}
				break;
			case 'post_tag':
?>
						<option value=""><?php echo $first_option_label; ?></option>
<?php
				foreach ( $tags as $tag ) {
?>
						<option value="<?php echo absint( $tag->term_id ); ?>"<?php selected( $tag->term_id == $rule[ 'matchterm' ], true ); ?>><?php echo esc_html( $tag->name ); ?></option>
<?php
				}
				break;
			case 'category':
?>
						<option value=""><?php echo $first_option_label; ?></option>
<?php
				foreach ( $categories as $category ) {
?>
						<option value="<?php echo absint( $category->term_id ); ?>"<?php selected( $category->term_id == $rule[ 'matchterm' ], true ); ?>><?php echo esc_html( $category->name ); ?></option>
<?php
				}
				break;
			case 'user':
?>
						<option value=""><?php echo $first_option_label; ?></option>
<?php
				foreach ( $users as $user ) {
?>
						<option value="<?php echo absint( $user[ 'id' ] ); ?>"<?php selected( $user[ 'id' ] == $rule[ 'matchterm' ], true ); ?>><?php echo esc_html( $user[ 'name' ] ); ?></option>
<?php
				}
				break;
			default: // custom taxonomy
?>
						<option value=""><?php echo $first_option_label; ?></option>
<?php
				if ( $custom_taxonomies_terms ) {
					foreach ( $custom_taxonomies_terms[ $rule[ 'taxonomy' ] ] as $term_id => $term_name ) {
?>
						<option value="<?php echo absint( $term_id ); ?>"<?php selected( $term_id == $rule[ 'matchterm' ] ); ?>><?php echo esc_html( $term_name ); ?></option>
<?php
					}
				}
		} // switch()
?>
					</select>
				</td>
				<td><input type="button" name="remove_rule_<?php echo $c; ?>" value="X" class="button remove_rule" id="remove_rule_<?php echo $c; ?>"></td>
			</tr>
<?php
		$c = $c + 1;
	} // foreach()
} else {
	// show default taxonomy rule row
?>
			<tr id="row_<?php echo $c; ?>" class="alternate">
				<td class="num"><?php echo $c; ?></td>
				<td>
					<input type="hidden" value="0" name="rules[<?php echo $c; ?>][id]" id="image_id_<?php echo $c; ?>">
					<img src="<?php echo $no_thumb_url; ?>" alt="<?php echo $feat_img_label; ?>" id="selected_image_<?php echo $c; ?>" />
				</td>
				<td>
					<input type="button" name="upload_image_<?php echo $c; ?>" value="<?php echo $choose_image_label; ?>" class="button imageupload" id="upload_image_<?php echo $c; ?>" /><br />
					<label for="taxonomy_<?php echo $c; ?>"><?php echo $taxonomy_label; ?></label><br />
					<select name="rules[<?php echo $c; ?>][taxonomy]" id="taxonomy_<?php echo $c; ?>" class="selection_rules">
						<option value=""><?php echo $first_option_label; ?></option>
<?php
		foreach ( $optionfields as $value => $label ) {
?>
						<option value="<?php echo $value; ?>"><?php echo $label; ?></option>
<?php
		} // foreach ( $optionfields )
		if ( $custom_taxonomies_terms ) {
			foreach ( $custom_taxonomies as $key => $label ) {
				if ( $key and $label ) { // ommit empty or false values
?>
						<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></option>
<?php
				}
			}
		} // if ( $custom_taxonomies_terms )
?>
					</select><br />
					<?php echo $matches_label; ?>:<br />
					<label for="matchterm_<?php echo $c; ?>"><?php echo $value_label; ?></label><br />
					<select name="rules[<?php echo $c; ?>][matchterm]" id="matchterm_<?php echo $c; ?>">
						<option value=""><?php echo $first_option_label; ?></option>
					</select>
				</td>
				<td><input type="button" name="remove_rule_<?php echo $c; ?>" value="X" class="button remove_rule" id="remove_rule_<?php echo $c; ?>"></td>
			</tr>
<?php
} // if( rules )
?>
			<tr id="template_row">
				<td class="num">XX</td>
				<td>
					<input type="hidden" value="0" name="rules[XX][id]" id="image_id_XX">
					<img src="<?php echo $no_thumb_url; ?>" alt="<?php echo $feat_img_label; ?>" id="selected_image_XX">
				</td>
				<td>
					<input type="button" name="upload_image_XX" value="<?php echo $choose_image_label; ?>" class="button imageupload" id="upload_image_XX"><br />
					<label for="taxonomy_XX"><?php echo $taxonomy_label; ?></label><br />
					<select name="rules[XX][taxonomy]" id="taxonomy_XX" class="selection_rules">
						<option value=""><?php echo $first_option_label; ?></option>
<?php
foreach ( $optionfields as $value => $label ) {
?>
						<option value="<?php echo $value; ?>"><?php echo $label; ?></option>
<?php
} // foreach ( $optionfields )

if ( $custom_taxonomies_terms ) {
	foreach ( $custom_taxonomies as $key => $label ) {
		if ( $key and $label ) { // ommit empty or false values
?>
						<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></option>
<?php
		}
	}
} // if ( $custom_taxonomies_terms )
?>
					</select><br />
					<?php echo $matches_label; ?>:<br />
					<label for="matchterm_XX"><?php echo $value_label; ?></label><br />
					<select name="rules[XX][matchterm]" id="matchterm_XX">
						<option value=""><?php echo $first_option_label; ?></option>
					</select>
				</td>
				<td><input type="button" name="remove_rule_XX" value="X" class="button remove_rule" id="remove_rule_XX"></td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<th class="num"><?php echo $number_label; ?></th>
				<th><?php echo $image_label; ?></th>
				<th><?php echo $description_label; ?></th>
				<th><?php echo $action_label; ?></th>
			</tr>
		</tfoot>
	</table>
<?php 
submit_button( __( 'Add rule', 'quick-featured-images' ), 'secondary', 'add_rule_button' );
submit_button( $button_label );
wp_nonce_field( $this->main_function_name, $this->nonce );
?>
	<input type="hidden" id="placeholder_url" name="placeholder_url" value="<?php echo $no_thumb_url; ?>" />
	<input type="hidden" id="confirmation_question" name="confirmation_question" value="<?php esc_attr_e( 'Are you sure to remove this rule?', 'quick-featured-images' ); ?>" />
</form>

<h3><?php esc_html_e( 'How the rules work', 'quick-featured-images' ); ?></h3>
<p><?php esc_html_e( 'Every time you save a post the post get the featured image if one of the following rules match a property of the post. You can also set rules for pages and all other current post types which support featured images.', 'quick-featured-images' ); ?></p>
<p><?php esc_html_e( 'Regardless of the order in the list the rules are applied in the following order until a rule and a property of the post fit together:', 'quick-featured-images' ); ?></p>
<ol>
	<li><?php esc_html_e( 'found first content image. If not then...', 'quick-featured-images' ); ?></li>
	<li><?php esc_html_e( 'matched custom taxonomy. If not then...', 'quick-featured-images' ); ?></li>
	<li><?php esc_html_e( 'matched tag. If not then...', 'quick-featured-images' ); ?></li>
	<li><?php esc_html_e( 'matched category. If not then...', 'quick-featured-images' ); ?></li>
	<li><?php esc_html_e( 'matched author. If not then...', 'quick-featured-images' ); ?></li>
	<li><?php esc_html_e( 'matched post type. If not then...', 'quick-featured-images' ); ?></li>
	<li><?php esc_html_e( 'no featured image.', 'quick-featured-images' ); ?></li>
</ol>
<p><?php esc_html_e( 'Bear in mind that if two or more rules with the same taxonomy would fit to the post it is unforeseeable which image will become the featured image.', 'quick-featured-images' ); ?></p>
<h3><?php esc_html_e( 'Additional rules in the premium version', 'quick-featured-images' ); ?></h3>
<ol>
	<li><?php esc_html_e( 'Multiple images to set them randomly as featured image', 'quick-featured-images' ); ?></li>
	<li><?php esc_html_e( 'Random featured images at each page load', 'quick-featured-images' ); ?></li>
	<li><?php esc_html_e( 'The first content image can be also an image from an external server to set it as automated featured image', 'quick-featured-images' ); ?></li>
	<li><?php esc_html_e( 'Remove the first content image automatically after the featured image was set successfully', 'quick-featured-images' ); ?></li>
	<li><?php esc_html_e( 'Match with a search string in post title', 'quick-featured-images' ); ?></li>
	<li><?php esc_html_e( 'Match with a selected post format', 'quick-featured-images' ); ?></li>
	<li><?php esc_html_e( 'User defined order of rules', 'quick-featured-images' ); ?></li>
	<li><?php esc_html_e( 'Import your rules from this plugin into the premium plugin', 'quick-featured-images' ); ?></li>
</ol>
<p class="qfi_ad_for_pro"><?php esc_html_e( 'Get the premium version', 'quick-featured-images' ); ?> <a href="https://www.quickfeaturedimages.com<?php esc_html_e( '/', 'quick-featured-images' ); ?>">Quick Featured Images Pro</a>.</p>

