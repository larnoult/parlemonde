<?php

/*
*  FieldMaster Admin Field Groups Class
*
*  All the logic for editing a list of field groups
*
*  @class 		fieldmaster_admin_field_groups
*  @package		FieldMaster
*  @subpackage	Admin
*/

if( ! class_exists('fieldmaster_admin_field_groups') ) :

class fieldmaster_admin_field_groups {

	// vars
	var $url = 'edit.php?post_type=fm-field-group',
		$sync = array();


	/*
	*  __construct
	*
	*  This function will setup the class functionality
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/

	function __construct() {

		// actions
		add_action('current_screen',		array($this, 'current_screen'));
		add_action('trashed_post',			array($this, 'trashed_post'));
		add_action('untrashed_post',		array($this, 'untrashed_post'));
		add_action('deleted_post',			array($this, 'deleted_post'));

	}


	/*
	*  current_screen
	*
	*  This function is fired when loading the admin page before HTML has been rendered.
	*
	*  @type	action (current_screen)
	*  @date	21/07/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/

	function current_screen() {

		// validate screen
		if( !fieldmaster_is_screen('edit-fm-field-group') ) {

			return;

		}


		// customize post_status
		global $wp_post_statuses;


		// modify publish post status
		$wp_post_statuses['publish']->label_count = _n_noop( 'Active <span class="count">(%s)</span>', 'Active <span class="count">(%s)</span>', 'fieldmaster' );


		// reorder trash to end
		$wp_post_statuses['trash'] = fieldmaster_extract_var( $wp_post_statuses, 'trash' );


		// check stuff
		$this->check_duplicate();
		$this->check_sync();


		// actions
		add_action('admin_enqueue_scripts',							array($this, 'admin_enqueue_scripts'));
		add_action('admin_footer',									array($this, 'admin_footer'));


		// columns
		add_filter('manage_edit-fm-field-group_columns',			array($this, 'field_group_columns'), 10, 1);
		add_action('manage_fm-field-group_posts_custom_column',	array($this, 'field_group_columns_html'), 10, 2);

	}


	/*
	*  admin_enqueue_scripts
	*
	*  This function will add the already registered css
	*
	*  @type	function
	*  @date	28/09/13
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/

	function admin_enqueue_scripts() {

		wp_enqueue_script('fieldmaster-input');

	}


	/*
	*  check_duplicate
	*
	*  This function will check for any $_GET data to duplicate
	*
	*  @type	function
	*  @date	17/10/13
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/

	function check_duplicate() {

		// message
		if( $ids = fieldmaster_maybe_get($_GET, 'fieldmasterduplicatecomplete') ) {

			// explode
			$ids = explode(',', $ids);
			$total = count($ids);

			if( $total == 1 ) {

				fieldmaster_add_admin_notice( sprintf(__('Field group duplicated. %s', 'fieldmaster'), '<a href="' . get_edit_post_link($ids[0]) . '">' . get_the_title($ids[0]) . '</a>') );

			} else {

				fieldmaster_add_admin_notice( sprintf(_n( '%s field group duplicated.', '%s field groups duplicated.', $total, 'fieldmaster' ), $total) );

			}

		}


		// vars
		$ids = array();


		// check single
		if( $id = fieldmaster_maybe_get($_GET, 'fieldmasterduplicate') ) {

			$ids[] = $id;

		// check multiple
		} elseif( fieldmaster_maybe_get($_GET, 'action2') === 'fieldmasterduplicate' ) {

			$ids = fieldmaster_maybe_get($_GET, 'post');

		}


		// sync
		if( !empty($ids) ) {

			// validate
			check_admin_referer('bulk-posts');


			// vars
			$new_ids = array();


			// loop
			foreach( $ids as $id ) {

				// duplicate
				$field_group = fieldmaster_duplicate_field_group( $id );


				// increase counter
				$new_ids[] = $field_group['ID'];

			}


			// redirect
			wp_redirect( admin_url( $this->url . '&fieldmasterduplicatecomplete=' . implode(',', $new_ids)) );
			exit;

		}

	}


	/*
	*  check_sync
	*
	*  This function will check for any $_GET data to sync
	*
	*  @type	function
	*  @date	9/12/2014
	*  @since	5.1.5
	*
	*  @param	n/a
	*  @return	n/a
	*/

	function check_sync() {

		// message
		if( $ids = fieldmaster_maybe_get($_GET, 'fieldmastersynccomplete') ) {

			// explode
			$ids = explode(',', $ids);
			$total = count($ids);

			if( $total == 1 ) {

				fieldmaster_add_admin_notice( sprintf(__('Field group synchronised. %s', 'fieldmaster'), '<a href="' . get_edit_post_link($ids[0]) . '">' . get_the_title($ids[0]) . '</a>') );

			} else {

				fieldmaster_add_admin_notice( sprintf(_n( '%s field group synchronised.', '%s field groups synchronised.', $total, 'fieldmaster' ), $total) );

			}

		}


		// vars
		$groups = fieldmaster_get_field_groups();


		// bail early if no field groups
		if( empty($groups) ) return;


		// find JSON field groups which have not yet been imported
		foreach( $groups as $group ) {

			// vars
			$local = fieldmaster_maybe_get($group, 'local', false);
			$modified = fieldmaster_maybe_get($group, 'modified', 0);
			$private = fieldmaster_maybe_get($group, 'private', false);


			// ignore DB / PHP / private field groups
			if( $local !== 'json' || $private ) {

				// do nothing

			} elseif( !$group['ID'] ) {

				$this->sync[ $group['key'] ] = $group;

			} elseif( $modified && $modified > get_post_modified_time('U', true, $group['ID'], true) ) {

				$this->sync[ $group['key'] ]  = $group;

			}

		}


		// bail if no sync needed
		if( empty($this->sync) ) return;


		// maybe sync
		$sync_keys = array();


		// check single
		if( $key = fieldmaster_maybe_get($_GET, 'fieldmastersync') ) {

			$sync_keys[] = $key;

		// check multiple
		} elseif( fieldmaster_maybe_get($_GET, 'action2') === 'fieldmastersync' ) {

			$sync_keys = fieldmaster_maybe_get($_GET, 'post');

		}


		// sync
		if( !empty($sync_keys) ) {

			// validate
			check_admin_referer('bulk-posts');


			// disable filters to ensure FieldMaster loads raw data from DB
			fieldmaster_disable_filters();
			fieldmaster_enable_filter('local');


			// disable JSON
			// - this prevents a new JSON file being created and causing a 'change' to theme files - solves git anoyance
			fieldmaster_update_setting('json', false);


			// vars
			$new_ids = array();


			// loop
			foreach( $sync_keys as $key ) {

				// append fields
				if( fieldmaster_have_local_fields($key) ) {

					$this->sync[ $key ]['fields'] = fieldmaster_get_local_fields( $key );

				}


				// import
				$field_group = fieldmaster_import_field_group( $this->sync[ $key ] );


				// append
				$new_ids[] = $field_group['ID'];

			}


			// redirect
			wp_redirect( admin_url( $this->url . '&fieldmastersynccomplete=' . implode(',', $new_ids)) );
			exit;

		}


		// filters
		add_filter('views_edit-fm-field-group', array($this, 'list_table_views'));

	}


	/*
	*  list_table_views
	*
	*  This function will add an extra link for JSON in the field group list table
	*
	*  @type	function
	*  @date	3/12/2014
	*  @since	5.1.5
	*
	*  @param	$views (array)
	*  @return	$views
	*/

	function list_table_views( $views ) {

		// vars
		$class = '';
		$total = count($this->sync);

		// active
		if( fieldmaster_maybe_get($_GET, 'post_status') === 'sync' ) {

			// actions
			add_action('admin_footer', array($this, 'sync_admin_footer'), 5);


			// set active class
			$class = ' class="current"';


			// global
			global $wp_list_table;


			// update pagination
			$wp_list_table->set_pagination_args( array(
				'total_items' => $total,
				'total_pages' => 1,
				'per_page' => $total
			));

		}


		// add view
		$views['json'] = '<a' . $class . ' href="' . admin_url($this->url . '&post_status=sync') . '">' . __('Sync available', 'fieldmaster') . ' <span class="count">(' . $total . ')</span></a>';


		// return
		return $views;

	}


	/*
	*  trashed_post
	*
	*  This function is run when a post object is sent to the trash
	*
	*  @type	action (trashed_post)
	*  @date	8/01/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	n/a
	*/

	function trashed_post( $post_id ) {

		// validate post type
		if( get_post_type($post_id) != 'fm-field-group' ) {

			return;

		}


		// trash field group
		fieldmaster_trash_field_group( $post_id );

	}


	/*
	*  untrashed_post
	*
	*  This function is run when a post object is restored from the trash
	*
	*  @type	action (untrashed_post)
	*  @date	8/01/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	n/a
	*/

	function untrashed_post( $post_id ) {

		// validate post type
		if( get_post_type($post_id) != 'fm-field-group' ) {

			return;

		}


		// trash field group
		fieldmaster_untrash_field_group( $post_id );

	}


	/*
	*  deleted_post
	*
	*  This function is run when a post object is deleted from the trash
	*
	*  @type	action (deleted_post)
	*  @date	8/01/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	n/a
	*/

	function deleted_post( $post_id ) {

		// validate post type
		if( get_post_type($post_id) != 'fm-field-group' ) {

			return;

		}


		// trash field group
		fieldmaster_delete_field_group( $post_id );

	}


	/*
	*  field_group_columns
	*
	*  This function will customize the columns for the field group table
	*
	*  @type	filter (manage_edit-fm-field-group_columns)
	*  @date	28/09/13
	*  @since	5.0.0
	*
	*  @param	$columns (array)
	*  @return	$columns (array)
	*/

	function field_group_columns( $columns ) {

		return array(
			'cb'	 				=> '<input type="checkbox" />',
			'title' 				=> __('Title', 'fieldmaster'),
			'fieldmaster-fg-description'	=> __('Description', 'fieldmaster'),
			'fieldmaster-fg-status' 		=> '<i class="fieldmaster-icon -dot-3 small fieldmaster-js-tooltip" title="' . __('Status', 'fieldmaster') . '"></i>',
			'fieldmaster-fg-count' 			=> __('Fields', 'fieldmaster'),
		);

	}


	/*
	*  field_group_columns_html
	*
	*  This function will render the HTML for each table cell
	*
	*  @type	action (manage_fm-field-group_posts_custom_column)
	*  @date	28/09/13
	*  @since	5.0.0
	*
	*  @param	$column (string)
	*  @param	$post_id (int)
	*  @return	n/a
	*/

	function field_group_columns_html( $column, $post_id ) {

		// vars
		$field_group = fieldmaster_get_field_group( $post_id );


		// render
		$this->render_column( $column, $field_group );

	}

	function render_column( $column, $field_group ) {

		// description
		if( $column == 'fieldmaster-fg-description' ) {

			if( $field_group['description'] ) {

				echo '<span class="fieldmaster-description">' . $field_group['description'] . '</span>';

			}

        // status
	    } elseif( $column == 'fieldmaster-fg-status' ) {

			if( isset($this->sync[ $field_group['key'] ]) ) {

				echo '<i class="fieldmaster-icon -sync grey small fieldmaster-js-tooltip" title="' . __('Sync available', 'fieldmaster') .'"></i> ';

			}

			if( $field_group['active'] ) {

				//echo '<i class="fieldmaster-icon -check small fieldmaster-js-tooltip" title="' . __('Active', 'fieldmaster') .'"></i> ';

			} else {

				echo '<i class="fieldmaster-icon -minus yellow small fieldmaster-js-tooltip" title="' . __('Inactive', 'fieldmaster') . '"></i> ';

			}

        // fields
	    } elseif( $column == 'fieldmaster-fg-count' ) {

			echo fieldmaster_get_field_count( $field_group );

        }

	}


	/*
	*  admin_footer
	*
	*  This function will render extra HTML onto the page
	*
	*  @type	action (admin_footer)
	*  @date	23/06/12
	*  @since	3.1.8
	*
	*  @param	n/a
	*  @return	n/a
	*/

	function admin_footer() {

		// vars
		$url_home = 'https://goldhat.ca';
		$url_support = 'https://goldhat.ca/support';
		$url_docs = $url_home . '/resources/';


?>
<script type="text/html" id="tmpl-fieldmaster-column-2">
<div class="fieldmaster-column-2">
	<div class="fieldmaster-box">
		<div class="inner">
			<h2><?php echo fieldmaster_get_setting('name'); ?></h2>

			<h3><?php _e("Changelog",'fieldmaster'); ?></h3>
			<p><?php printf(__('See what\'s new in <a href="%s">version %s</a>.','fieldmaster'), admin_url('edit.php?post_type=fm-field-group&page=fieldmaster-settings-info&tab=changelog'), fieldmaster_get_setting('version')); ?></p>

			<h3><?php _e("Resources",'fieldmaster'); ?></h3>
			<ul>
				<li><a href="<?php echo $url_docs; ?>" target="_blank"><?php _e("Documentation",'fieldmaster'); ?></a></li>

				<li><a href="<?php echo $url_docs; ?>#getting-started" target="_blank"><?php _e("Getting Started",'fieldmaster'); ?></a></li>
				<li><a href="<?php echo $url_docs; ?>#field-types" target="_blank"><?php _e("Field Types",'fieldmaster'); ?></a></li>
				<li><a href="<?php echo $url_docs; ?>#functions" target="_blank"><?php _e("Functions",'fieldmaster'); ?></a></li>
				<li><a href="<?php echo $url_docs; ?>#actions" target="_blank"><?php _e("Actions",'fieldmaster'); ?></a></li>
				<li><a href="<?php echo $url_docs; ?>#filters" target="_blank"><?php _e("Filters",'fieldmaster'); ?></a></li>
				<li><a href="<?php echo $url_docs; ?>#features" target="_blank"><?php _e("Features",'fieldmaster'); ?></a></li>
				<li><a href="<?php echo $url_docs; ?>#how-to" target="_blank"><?php _e("How to",'fieldmaster'); ?></a></li>
				<li><a href="<?php echo $url_docs; ?>#tutorials" target="_blank"><?php _e("Tutorials",'fieldmaster'); ?></a></li>
				<li><a href="<?php echo $url_docs; ?>#faq" target="_blank"><?php _e("FAQ",'fieldmaster'); ?></a></li>
				<li><a href="<?php echo $url_support; ?>" target="_blank"><?php _e("Support",'fieldmaster'); ?></a></li>
			</ul>
		</div>
		<div class="footer -blue">
			<p><?php echo sprintf( __('Thank you for creating with <a href="%s">FieldMaster</a>.','fieldmaster'), $url_home ); ?></p>
		</div>
	</div>
</div>
<div class="fieldmaster-clear"></div>
</script>
<script type="text/javascript">
(function($){

	// wrap
	$('#wpbody .wrap').attr('id', 'fm-field-group-wrap');


	// wrap form
	$('#posts-filter').wrap('<div class="fieldmaster-columns-2" />');


	// add column main
	$('#posts-filter').addClass('fieldmaster-column-1');


	// add column side
	$('#posts-filter').after( $('#tmpl-fieldmaster-column-2').html() );


	// modify row actions
	$('#the-list tr').each(function(){

		// vars
		var $tr = $(this),
			id = $tr.attr('id'),
			description = $tr.find('.column-fieldmaster-fg-description').html();


		// replace Quick Edit with Duplicate (sync page has no id attribute)
		if( id ) {

			// vars
			var post_id	= id.replace('post-', '');


			// create el
			var $span = $('<span class="fieldmaster-duplicate-field-group"><a title="<?php _e('Duplicate this item', 'fieldmaster'); ?>" href="<?php echo admin_url($this->url . '&fieldmasterduplicate='); ?>' + post_id + '&_wpnonce=<?php echo wp_create_nonce('bulk-posts'); ?>"><?php _e('Duplicate', 'fieldmaster'); ?></a> | </span>');


			// replace
			$tr.find('.column-title .row-actions .inline').replaceWith( $span );

		}


		// add description to title
		$tr.find('.column-title .row-title').after( description );

	});


	// modify bulk actions
	$('#bulk-action-selector-bottom option[value="edit"]').attr('value','fieldmasterduplicate').text('<?php _e( 'Duplicate', 'fieldmaster' ); ?>');


	// clean up table
	$('#adv-settings label[for="fieldmaster-fg-description-hide"]').remove();


	// mobile compatibility
	var status = $('.fieldmaster-icon.-dot-3').first().attr('title');
	$('td.column-fieldmaster-fg-status').attr('data-colname', status);


	// no field groups found
	$('#the-list tr.no-items td').attr('colspan', 4);


	// search
	$('.subsubsub').append(' | <li><a href="#" class="fieldmaster-toggle-search"><?php _e('Search', 'fieldmaster'); ?></a></li>');


	// events
	$(document).on('click', '.fieldmaster-toggle-search', function( e ){

		// prevent default
		e.preventDefault();


		// toggle
		$('.search-box').slideToggle();

	});

})(jQuery);
</script>
<?php

	}


	/*
	*  sync_admin_footer
	*
	*  This function will render extra HTML onto the page
	*
	*  @type	action (admin_footer)
	*  @date	23/06/12
	*  @since	3.1.8
	*
	*  @param	n/a
	*  @return	n/a
	*/

	function sync_admin_footer() {

		// vars
		$i = -1;
		$columns = array(
			'fieldmaster-fg-description',
			'fieldmaster-fg-status',
			'fieldmaster-fg-count'
		);

?>
<script type="text/html" id="tmpl-fieldmaster-json-tbody">
<?php foreach( $this->sync as $field_group ): $i++; ?>
	<tr <?php if($i%2 == 0): ?>class="alternate"<?php endif; ?>>
		<th class="check-column" scope="row">
			<label for="cb-select-<?php echo $field_group['key']; ?>" class="screen-reader-text"><?php printf( __( 'Select %s', 'fieldmaster' ), $field_group['title'] ); ?></label>
			<input type="checkbox" value="<?php echo $field_group['key']; ?>" name="post[]" id="cb-select-<?php echo $field_group['key']; ?>">
		</th>
		<td class="post-title page-title column-title">
			<strong>
				<span class="row-title"><?php echo $field_group['title']; ?></span><span class="fieldmaster-description"><?php echo $field_group['key']; ?>.json</span>
			</strong>
			<div class="row-actions">
				<span class="import"><a title="<?php echo esc_attr( __('Synchronise field group', 'fieldmaster') ); ?>" href="<?php echo admin_url($this->url . '&post_status=sync&fieldmastersync=' . $field_group['key'] . '&_wpnonce=' . wp_create_nonce('bulk-posts')); ?>"><?php _e( 'Sync', 'fieldmaster' ); ?></a></span>
			</div>
		</td>
		<?php foreach( $columns as $column ): ?>
			<td class="column-<?php echo $column; ?>"><?php $this->render_column( $column, $field_group ); ?></td>
		<?php endforeach; ?>
	</tr>
<?php endforeach; ?>
</script>
<script type="text/html" id="tmpl-fieldmaster-bulk-actions">
	<?php // source: bulk_actions() wp-admin/includes/class-wp-list-table.php ?>
	<select name="action2" id="bulk-action-selector-bottom"></select>
	<?php submit_button( __( 'Apply' ), 'action', '', false, array( 'id' => "doaction2" ) ); ?>
</script>
<script type="text/javascript">
(function($){

	// update table HTML
	$('#the-list').html( $('#tmpl-fieldmaster-json-tbody').html() );


	// bulk may not exist if no field groups in DB
	if( !$('#bulk-action-selector-bottom').exists() ) {

		$('.tablenav.bottom .actions.alignleft').html( $('#tmpl-fieldmaster-bulk-actions').html() );

	}


	// set only options
	$('#bulk-action-selector-bottom').html('<option value="-1"><?php _e('Bulk Actions'); ?></option><option value="fieldmastersync"><?php _e('Sync', 'fieldmaster'); ?></option>');

})(jQuery);
</script>
<?php

	}

}

new fieldmaster_admin_field_groups();

endif;

?>
