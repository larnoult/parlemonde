var WPMUURM_JS = (function($) {
	'use strict';

	/**
	 * The wrapper for the content of the thickbox
	 *
	 * @type String
	 */
	var wpmuurmSelector = '.wpmuurm-wrapper';

	/**
	 * Select for holding all the notifications
	 *
	 * @var object
	 */
	var notificationsSelector = '.notifications';

	/**
	 * Holds all the templates loaded. Stops us
	 * retrieving htem more than once.
	 *
	 * @var object
	 */
	var templates = {};

	/**
	 * Our construct function that is run when the
	 * class is first initialized
	 *
	 * @return null
	 */
	var initialize = function() {
		// Run on document ready
		$(function() {
			setupSelectors();
		});
	};

	/**
	 * Checks the current page for any selectors we're looking for
	 * and if they're present triggers the corresponding function
	 * @return null
	 */
	var setupSelectors = function() {
		// Setup editable
		$.fn.editable.defaults.mode = 'inline';

		// Setup user rows in the table
		if ( $( 'a.wpmuurm-launch' ).length ) {
			$( 'a.wpmuurm-launch' ).on( 'click', function(e) {
				e.preventDefault();
				doUserRolesTable();
			});
		}

		// Setup remove user form blog button
		setupRemoveUserFromBlog();

		setupCancelRemoveUserFromBlog();

		setupConfirmRemoveUserFromBlog();

		setupLoadBlogRolesSelect();

		setupAddUseToBlogSubmit();
	};

	/**
	 * When trying to remove a user from a blog this shows the
	 * confirm and re-attribute posts row.
	 *
	 * @return null
	 */
	var setupRemoveUserFromBlog = function() {
		// Removes a user from a blog
		$( wpmuurmSelector ).on( 'click', '.remove-blog', function(e) {
			e.preventDefault();

			var row = $( this ).closest( 'tr' );
			var blog_id = $( row ).data( 'blog-id' );

			// Highlight the row
			row.addClass( 'confirm-remove' );

			// Setup spinner
			$(this).after( getTemplate( 'ajax-spinner' ) );
			$( row ).find( '.spinner' ).css( 'visibility', 'visible' );

			wp.ajax.send( 'reassign_user_blog_posts', {
				data : {
					'blog_id' : blog_id,
					'user_id' : wpmuurm.user_id,
					'nonce'   : wpmuurm.nonce
				},

				// Clear old notifications
				beforeSend : doClearNotifications,

				// If error fire notifications
				error : doNotifications,

				// Add the remove user from site confirm dialog box
				success : function( data ) {
					var rowTemplate = getTemplate( 'user-blog-remove-confirm' );
					row.after( rowTemplate( data ) );

					$( row ).find( '.spinner' ).remove();
				}

			});

		});
	};

	/**
	 * When the confirm button on the remove user from blog screen
	 * is pressed remove the user
	 *
	 * @return null
	 */
	var setupConfirmRemoveUserFromBlog = function() {
		// Removes a site from a user
		$( wpmuurmSelector ).on( 'click', '.confirm-user-blog-removal', function(e) {
			e.preventDefault();

			var row = $( this ).closest( 'tr' );
			var blog_id = $( row ).data( 'blog-id' );

			// Setup spinner
			$( this ).after( getTemplate( 'ajax-spinner' ) );
			$( row ).find( '.spinner' ).css( 'visibility', 'visible' );

			var reassign_id = false;
			if ( $( 'select.ressaign_to', row ).length ){
				reassign_id = $( 'select.ressaign_to', row ).val();
			}

			wp.ajax.send( 'remove_user_from_blog', {
				data : {
					'blog_id'     : blog_id,
					'reassign_id' : reassign_id,
					'user_id'     : wpmuurm.user_id,
					'nonce'       : wpmuurm.nonce
				},

				// Clear old notifications
				beforeSend : doClearNotifications,

				// If error fire notifications
				error : doNotifications,

				// remove the row and confirm dialog and re-do
				// the add to blog select
				success : function( data ) {
					row.prev( 'tr.blog-' + blog_id ).remove();
					row.remove();

					// Reload the addToBlogSelect
					doAddToBlogsSelect();
				}

			});

		});
	};


	/**
	 * When the cancel button on the remove user from blog screen
	 * is pressed clear the row
	 *
	 * @return null
	 */
	var setupCancelRemoveUserFromBlog = function() {
		// Removes a site from a user
		$( wpmuurmSelector ).on( 'click', '.cancel-user-blog-removal', function(e) {
			e.preventDefault();

			var parentRow = $( this ).closest( 'tr' );

			// Remove the highlight colour from the actual row
			$( 'tr.blog-' + parentRow.data( 'blog-id' ) ).removeClass( 'confirm-remove' );

			// Remove confirm select
			parentRow.remove();

		});
	};

	/**
	 * When a new blog is selected retrive all the possible
	 * roles for it
	 *
	 * @return null
	 */
	var setupLoadBlogRolesSelect = function() {
		$( '.add-user-to-blog-wrapper', wpmuurmSelector ).on( 'change', 'select.add-to-blog-blogs', function() {
			var roleSelect = $( '.add-user-to-blog-wrapper select.add-to-blog-roles' );
			var submitSelect = $( '.add-user-to-blog-wrapper a.add-to-blog-submit' );
			$( roleSelect, submitSelect ).attr( 'disabled', true );

			// No blog value, stop the function, kill the spinner
			if ( ! $( this ).val().length ) {
				roleSelect.next( '.spinner' ).remove();
				return;
			}

			roleSelect.after( getTemplate( 'ajax-spinner' ) );
			roleSelect.next( '.spinner' ).css( { 'visibility' : 'visible', 'float' : 'none' } );

			wp.ajax.send( 'get_blog_roles', {
				data : {
					'blog_id' : $( this ).val(),
					'nonce'   : wpmuurm.nonce
				},

				// Clear old notifications
				beforeSend : doClearNotifications,

				// If error fire notifications
				error : doNotifications,

				// Add all available roles for the chosen blog
				success : function( data ) {
					var rolesTemplate = getTemplate( 'blog-roles-options' );
					$( roleSelect ).html( rolesTemplate( data ) );
					$( roleSelect, submitSelect ).attr( 'disabled', false );
					roleSelect.next( '.spinner' ).remove();
				}

			});

		});
	};

	/**
	 * Adds a user to a blog and reload the table with the new data
	 *
	 * @return null
	 */
	var setupAddUseToBlogSubmit = function() {
		$( '.add-user-to-blog-wrapper', wpmuurmSelector ).on( 'click', 'a.add-to-blog-submit', function(e) {
			e.preventDefault();
			var blogsSelect = $( '.add-user-to-blog-wrapper select.add-to-blog-blogs' );
			var rolesSelect = $( '.add-user-to-blog-wrapper select.add-to-blog-roles' );

			$( blogsSelect ).attr( 'disabled', true );
			$( rolesSelect ).attr( 'disabled', true );

			$( this ).after( getTemplate( 'ajax-spinner' ) );
			$( this ).next( '.spinner' ).css( { 'visibility' : 'visible', 'float' : 'none' } );

			wp.ajax.send( 'add_user_to_blog', {
				data : {
					'blog_id'     : blogsSelect.val(),
					'role'        : rolesSelect.val(),
					'user_id'     : wpmuurm.user_id,
					'nonce'       : wpmuurm.nonce
				},

				// Clear old notifications
				beforeSend : doClearNotifications,

				// If error fire notifications
				error : doNotifications,

				// Reload the table with the new blog added
				success : function( data ) {
					doUserRolesTable();
					doAddToBlogsSelect();
				}

			});

		});
	};

	/**
	 * When the MU roles table is clicked and loaded
	 * this function kicks everything off and populates it.
	 *
	 * @return null
	 */
	var doUserRolesTable = function() {
		// Get all the user's blogs
		wp.ajax.send( 'get_user_blogs', {
			data : {
				'user_id' : wpmuurm.user_id,
				'nonce'   : wpmuurm.nonce
			},

			// Clear old notifications
			beforeSend : doClearNotifications,

			// If error fire notifications
			error : doNotifications,

			// Now we've got the blogs use the WP template helper to
			// create the rows and setup select2 for each input
			success : function( data ) {
				var tBody = $( wpmuurmSelector + ' table tbody' );
				var rowTemplate = getTemplate( 'user-blog-roles-row' );

				tBody.html('');

				for ( var i = 0; i < data.length; i++ ) {

					tBody.append( rowTemplate( data[i] ) );

					// Setup the fancy select
					$( '[roles-editable]:last', tBody ).editable({
						url    : '/wp-admin/admin-ajax.php',
						value  : data[ i ].user_roles[0],
						type   : 'checklist',
						pk     : 1,
						title  : 'Select roles',
						source : data[ i ].blog_roles,
						params : {
							'action'  : 'set_user_blog_roles',
							'user_id' : wpmuurm.user_id,
							'blog_id' : data[ i ].blog.userblog_id,
							'nonce'   : wpmuurm.nonce
						}
					});
				}

				doAddToBlogsSelect();

			}

		});

	};

	/**
	 * Generates the html for the add to site select
	 *
	 * @return null
	 */
	var doAddToBlogsSelect = function() {
		var addUserToBlogWrapper = $( '.add-user-to-blog-wrapper', wpmuurmSelector );
		addUserToBlogWrapper.html( getTemplate( 'ajax-spinner' ) );
		$( '.spinner', addUserToBlogWrapper ).css( { 'visibility' : 'visible', 'float' : 'left' } );

		wp.ajax.send( 'get_blogs_wo_user', {
			data : {
				'user_id' : wpmuurm.user_id,
				'nonce'   : wpmuurm.nonce
			},

			// Clear old notifications
			beforeSend : doClearNotifications,

			// If error fire notifications
			error : doNotifications,

			// Add the blog to the table
			success : function( data ) {
				var template = getTemplate( 'add-user-to-blog' );
				$( addUserToBlogWrapper ).html( template( data ) );
			}

		});

	};

	/**
	 * Handles and shows any error messages
	 * @param  array errors
	 * @return null
	 */
	var doNotifications = function( data ) {
		var notificationTemplate = getTemplate( 'notification' );
		_.each( data, function( notification ){
			$( notificationsSelector ).append( notificationTemplate( notification ) );
		});
	};

	/**
	 * Hides and currently displayed errors.
	 *
	 * @return null
	 */
	var doClearNotifications = function() {
		$( notificationsSelector ).html('');
	};

	/**
	 * Helper function to get templates with caching
	 *
	 * @param  string templateName
	 * @return object
	 */
	var getTemplate = function( templateName ) {
		if ( typeof templates[ templateName ] === 'undefined' ) {
			templates[ templateName ] = wp.template( templateName );
		}

		return templates[ templateName ];
	};

	return initialize();

})( jQuery );
