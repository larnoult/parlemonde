<?php

namespace threewp_broadcast\traits;

/**
	@brief		Methods related to terms and taxonomies.
	@since		2014-10-19 15:44:39
**/
trait terms_and_taxonomies
{
	/**
		@brief		Collects the post type's taxonomies into the broadcasting data object.
		@details

		The taxonomies are places in $bcd->parent_blog_taxonomies.
		Requires only that $bcd->post->post_type be filled in.
		If $bcd->post->ID exists, only the terms used in the post will be collected, else all the terms will be inserted into $bcd->parent_post_taxonomies[ taxonomy ].

		@see		threewp_broadcast_collect_post_type_taxonomies
		@since		2014-04-08 13:40:44
	**/
	public function collect_post_type_taxonomies( $bcd )
	{
		$action = $this->new_action( 'collect_post_type_taxonomies' );
		$action->broadcasting_data = $bcd;
		$action->execute();
	}

	public function get_current_blog_taxonomy_terms( $taxonomy )
	{
		$terms = get_terms( $taxonomy, array(
			'hide_empty' => false,
		) );
		$terms = (array) $terms;
		$terms = $this->array_rekey( $terms, 'term_id' );
		return $terms;
	}

	/**
		@brief		Return all of the terms and their parents.
		@param		object	$taxonomy		The taxonomy object.
		@param		array	$terms			The terms array.
		@since		2015-01-14 22:18:39
	**/
	public function get_parent_terms( $o )
	{
		// Wanted terms = those which are referenced as parents which we don't know about.
		$wanted_terms = [];
		foreach( $o->terms as $term_id => $term )
		{
			$term = (object)$term;
			$parent_term_id = $term->parent;
			if ( $parent_term_id < 1 )
				continue;
			if ( ! isset( $o->terms[ $parent_term_id ] ) )
				$wanted_terms[ $parent_term_id ] = true;
		}

		if ( count( $wanted_terms ) < 1 )
			return;

		// Fetch them and then try to find their parents.
		$new_terms = get_terms( $o->taxonomy->name, [
			'include' => array_keys( $wanted_terms ),
		] );
		foreach( $new_terms as $new_term )
		{
			unset( $wanted_terms[ $new_term->term_id ] );
			$o->terms[ $new_term->term_id ] = $new_term;
		}

		if ( count( $wanted_terms ) > 0 )
		{
			$this->debug( 'Warning! Wanted these extra terms, but get_terms could not supply them. So ignoring them.' );
			return;
		}

		// And since we have added new terms, they might have parents themselves.
		$this->get_parent_terms( $o );
	}

	/**
	 * Recursively adds the missing ancestors of the given source term at the
	 * target blog.
	 *
	 * @param array $source_post_term           The term to add ancestors for
	 * @param array $source_post_taxonomy       The taxonomy we're working with
	 * @param array $target_blog_terms          The existing terms at the target
	 * @param array $parent_blog_taxonomy_terms The existing terms at the source
	 * @return int The ID of the target parent term
	 */
	public function insert_term_ancestors( $bcd, $source_post_term, $source_post_taxonomy, $target_blog_terms, $parent_blog_taxonomy_terms )
	{
		// Fetch the parent of the current term among the source terms
		foreach ( $parent_blog_taxonomy_terms as $term )
			if ( $term->term_id == $source_post_term->parent )
				$source_parent = $term;

		if ( ! isset( $source_parent ) )
			// Sanity check, the source term's parent doesn't exist! Orphan!
			return 0;

		// Check if the parent already exists at the target
		foreach ( $target_blog_terms as $term )
			if ( $term->slug === $source_parent->slug )
				// The parent already exists, return its ID
				return $term->term_id;

		// Does the parent also have a parent, and if so, should we create the parent?
		$target_grandparent_id = 0;
		if ( 0 != $source_parent->parent )
			// Recursively insert ancestors, and get the newly inserted parent's ID
			$target_grandparent_id = $this->insert_term_ancestors( $bcd, $source_parent, $source_post_taxonomy, $target_blog_terms, $parent_blog_taxonomy_terms );

		// Check if the parent exists at the target grandparent
		$term_id = term_exists( $source_parent->name, $source_post_taxonomy, $target_grandparent_id );

		if ( is_null( $term_id ) || $term_id == 0 )
		{
			// The target parent does not exist, we need to create it
			$new_term = $source_parent;
			$new_term->parent = $target_grandparent_id;
			$action = $this->new_action( 'wp_insert_term' );
			$action->broadcasting_data = $bcd;
			$action->taxonomy = $source_post_taxonomy;
			$action->term = $new_term;
			$action->execute();
			if ( $action->new_term )
				$term_id = $action->new_term->term_id;
		}
		elseif ( is_array( $term_id ) )
			// The target parent exists and we got an array as response, extract parent id
			$term_id = $term_id[ 'term_id' ];

		return $term_id;
	}

	/**
		@brief		Syncs the terms of a taxonomy from the parent blog in the BCD to the current blog.
		@details	If $bcd->add_new_taxonomies is set, new taxonomies will be created, else they are ignored.

		Upon syncing, the broadcasting data will contain an array of the equivalent source terms <-> child terms in:
		$bcd->parent_blog_taxonomies[ $taxonomy ][ 'equivalent_terms' ][ $blog_id ]

		@param		broadcasting_data		$bcd			The broadcasting data.
		@param		string					$taxonomy		The taxonomy to sync.
		@since		20131004
	**/
	public function sync_terms( $bcd, $taxonomy )
	{
		if ( ! isset( $bcd->parent_blog_taxonomies[ $taxonomy ] ) )
			return;

		$source_terms = $bcd->parent_blog_taxonomies[ $taxonomy ][ 'terms' ];

		if ( ! isset( $bcd->parent_blog_taxonomies[ $taxonomy ][ 'equivalent_terms' ] ) )
			$bcd->parent_blog_taxonomies[ $taxonomy ][ 'equivalent_terms' ] = [];

		// Select only those terms that exist in the blog. We select them by slugs.
		$needed_slugs = [];
		foreach( $source_terms as $source_term )
			$needed_slugs[ $source_term->slug ] = true;
		$target_terms = get_terms( $taxonomy, [
			'slug' => array_keys( $needed_slugs ),
			'hide_empty' => false,
		] );
		$target_terms = $this->array_rekey( $target_terms, 'term_id' );

		$this->debug( 'Target terms: %s', $target_terms );

		$refresh_cache = false;

		// Keep track of which terms we've found.
		$found_targets = [];
		$found_sources = [];

		// Also keep track of which sources we haven't found on the target blog.
		$unfound_sources = $source_terms;

		// Rekey the terms in order to find them faster.
		$source_slugs = [];
		foreach( $source_terms as $source_term_id => $source_term )
			$source_slugs[ $source_term->slug ] = $source_term_id;
		$target_slugs = [];
		foreach( $target_terms as $target_term )
			$target_slugs[ $target_term->slug ] = $target_term->term_id;

		// Step 1.
		$this->debug( 'Find out which of the source terms exist on the target blog.' );
		foreach( $source_slugs as $source_slug => $source_term_id )
		{
			if ( ! isset( $target_slugs[ $source_slug ] ) )
				continue;
			$target_term_id = $target_slugs[ $source_slug ];
			$this->debug( 'Found source term %s. Source ID: %s. Target ID: %s.', $source_slug, $source_term_id, $target_term_id );
			$found_targets[ $target_term_id ] = $source_term_id;
			$found_sources[ $source_term_id ] = $target_term_id;
			unset( $unfound_sources[ $source_term_id ] );
		}

		// These sources were not found. Add them.
		if ( isset( $bcd->add_new_taxonomies ) && $bcd->add_new_taxonomies )
		{
			$this->debug( '%s taxonomies are missing on this blog.', count( $unfound_sources ) );
			foreach( $unfound_sources as $unfound_source_id => $unfound_source )
			{
				// We need to clone because we will be modifying the source.
				$unfound_source = clone( $unfound_source );

				if ( $unfound_source->parent > 0 )
				{
					$this->debug( 'The unfound source needs a parent.' );
					$parent_of_equivalent_source_term = $unfound_source->parent;
					$unfound_source->parent = 0;
					// Does the parent of the source have an equivalent target?
					if ( isset( $found_sources[ $parent_of_equivalent_source_term ] ) )
						$unfound_source->parent = $found_sources[ $parent_of_equivalent_source_term ];

					// Recursively insert ancestors if needed, and get the target term's parent's ID
					if ( $unfound_source->parent == 0 )
					{
						$this->debug( 'Inserting parent term for %s', $unfound_source->slug );
						$unfound_source->parent = $this->insert_term_ancestors(
							$bcd,
							$unfound_source,
							$taxonomy,
							$found_targets,
							$bcd->parent_blog_taxonomies[ $taxonomy ][ 'terms' ]
						);
					}
				}

				$action = $this->new_action( 'wp_insert_term' );
				$action->broadcasting_data = $bcd;
				$action->taxonomy = $taxonomy;
				$action->term = $unfound_source;
				$action->execute();

				if ( $action->new_term )
				{
					$new_term = $action->new_term;
					$new_term_id = $new_term->term_id;
					$target_terms[ $new_term_id ] = $new_term;
					$found_sources[ $unfound_source_id ] = $new_term_id;
					$found_targets[ $new_term_id ] = $unfound_source_id;
					$refresh_cache = true;
				}
			}
		}

		// Now we know which of the terms on our target blog exist on the source blog.
		// Next step: see if the parents are the same on the target as they are on the source.
		// "Same" meaning pointing to the same slug.

		$this->debug( 'About to update taxonomy terms.' );
		foreach( $found_targets as $target_term_id => $source_term_id)
		{
			$source_term = (object)$source_terms[ $source_term_id ];
			$target_term = (object)$target_terms[ $target_term_id ];

			$action = $this->new_action( 'wp_update_term' );
			$action->broadcasting_data = $bcd;
			$action->taxonomy = $taxonomy;

			// The old term is the target term, since it contains the old values.
			$action->set_old_term( $target_term );
			// The new term is the source term, since it has the newer data.
			$action->set_new_term( $source_term );

			// ... but the IDs have to be switched around, since the target term has the new ID.
			$action->switch_data();

			// Does the source term even have a parent?
			if ( $source_term->parent > 0 )
			{
				$parent_of_equivalent_source_term = $source_term->parent;
				$this->debug( 'Parent of equivalent source term: %s', $parent_of_equivalent_source_term );
				// Does the parent of the source have an equivalent target?
				if ( isset( $found_sources[ $parent_of_equivalent_source_term ] ) )
					$new_parent = $found_sources[ $parent_of_equivalent_source_term ];
			}
			else
				$new_parent = 0;

			$action->switch_data( 'parent' );
			$action->new_term->parent = $new_parent;

			$action->execute();
			$refresh_cache |= $action->updated;
		}

		// Save the equivalent sources for later use.
		$blog_id = get_current_blog_id();
		$bcd->parent_blog_taxonomies[ $taxonomy ][ 'equivalent_terms' ][ $blog_id ] = $found_sources;

		// wp_update_category alone won't work. The "cache" needs to be cleared.
		// see: http://wordpress.org/support/topic/category_children-how-to-recalculate?replies=4
		if ( $refresh_cache )
			delete_option( 'category_children' );

		// Tell everyone we've just synced this taxonomy.
		$action = $this->new_action( 'synced_taxonomy' );
		$action->broadcasting_data = $bcd;
		$action->taxonomy = $taxonomy;
		$action->execute();
	}

	/**
		@brief		Convert all terms of a taxonomy into a tree.
		@since		2015-07-11 22:22:04
	**/
	public function taxonomy_terms_to_tree( $taxonomy )
	{
		$terms = get_terms( $taxonomy, [
			'hide_empty' => false,
		] );
		$tree = new \plainview\sdk_broadcast\tree\tree();
		// Add root node 0, so that the terms can attach themselves to it.
		foreach( $terms as $term )
		{
			$parent = ( $term->parent > 0 ? absint( $term->parent ) : null );
			$tree->add( intval( $term->term_id ), $term, $parent );
		}
		return $tree;
	}

	/**
		@brief		Init this trait.
		@since		2017-04-10 20:36:19
	**/
	public function terms_and_taxonomies_init()
	{
		$this->add_action( 'threewp_broadcast_collect_post_type_taxonomies', 5 );
		$this->add_action( 'threewp_broadcast_wp_insert_term', 5 );
		$this->add_action( 'threewp_broadcast_wp_update_term', 5 );
	}

	/**
		@brief		Collects the post type's taxonomies into the broadcasting data object.
		@details

		The taxonomies are places in $bcd->parent_blog_taxonomies.
		Requires only that $bcd->post->post_type be filled in.
		If $bcd->post->ID exists, only the terms used in the post will be collected, else all the terms will be inserted into $bcd->parent_post_taxonomies[ taxonomy ].

		@see		collect_post_type_taxonomies
		@since		2016-07-19 20:45:02
	**/
	public function threewp_broadcast_collect_post_type_taxonomies( $action )
	{
		$bcd = $action->broadcasting_data;

		// Syncing taxonomies doesn't go through the proper custom_fields setup (in order to retrieve blacklists and what not), so we have to do it, just in case.
		$bcd->prepare_custom_fields();

		$bcd->parent_blog_taxonomies = get_object_taxonomies( [ 'object_type' => $bcd->post->post_type ], 'array' );
		$bcd->parent_post_taxonomies = [];

		if ( ! isset( $bcd->taxonomy_term_meta ) OR ! is_object( $bcd->taxonomy_term_meta ) )
			$bcd->taxonomy_term_meta = ThreeWP_Broadcast()->collection();

		foreach( $bcd->parent_blog_taxonomies as $parent_blog_taxonomy => $taxonomy )
		{
			if ( isset( $bcd->post->ID ) )
				$taxonomy_terms = get_the_terms( $bcd->post->ID, $parent_blog_taxonomy );
			else
				$taxonomy_terms = get_terms( [ $parent_blog_taxonomy ], [
					'hide_empty' => false,
				] );

			// No terms = empty = false.
			if ( ! $taxonomy_terms )
				$taxonomy_terms = [];

			$bcd->parent_post_taxonomies[ $parent_blog_taxonomy ] = $this->array_rekey( $taxonomy_terms, 'term_id' );

			// Parent blog taxonomy terms are used for creating missing target term ancestors
			$o = (object)[];
			$o->taxonomy = $taxonomy;
			$o->terms = $bcd->parent_post_taxonomies[ $parent_blog_taxonomy ];
			$this->get_parent_terms( $o );

			$bcd->parent_blog_taxonomies[ $parent_blog_taxonomy ] =
			[
				'taxonomy' => $taxonomy,
				'terms'    => $o->terms,
			];

			// Store the term meta.
			foreach( $o->terms as $term )
			{
				$meta = get_term_meta( $term->term_id );
				if ( ! is_array( $meta ) )
					$meta = [];
				// We store the meta array as a collection for easier handling later.
				$meta = ThreeWP_Broadcast()->collection( $meta );

				// Cull blacklisted meta keys.
				foreach( $meta as $key => $value )
				{
					if ( $bcd->taxonomies()->blacklist_has( $taxonomy->name, $term->slug, $key ) )
					{
						$this->debug( 'Taxonomy term meta key %s / %s / %s is blacklisted. Not storing.', $taxonomy->name, $term->slug, $key );
						unset( $meta[ $key ] );
					}
				}
				$bcd->taxonomy_term_meta
					->collection( $bcd->parent_blog_id )
					->collection( 'terms' )
					->set( $term->term_id, $meta );
			}
		}
	}

	/**
		@brief		Allows Broadcast plugins to update the term with their own info.
		@since		2014-04-08 15:12:05
	**/
	public function threewp_broadcast_wp_insert_term( $action )
	{
		if ( $action->is_finished() )
			return;

		if ( ! isset( $action->term->parent ) )
			$action->term->parent = 0;

		$term = wp_insert_term(
			$action->term->name,
			$action->taxonomy,
			[
				'description' => $action->term->description,
				'parent' => $action->term->parent,
				'slug' => $action->term->slug,
			]
		);

		// Sometimes the search didn't find the term because it's SIMILAR and not exact.
		// WP will complain and give us the term tax id.
		if ( is_wp_error( $term ) )
		{
			$wp_error = $term;
			$this->debug( 'Error creating the term: %s. Error was: %s', $action->term->name, serialize( $wp_error->error_data ) );
			if ( isset( $wp_error->error_data[ 'term_exists' ] ) )
			{
				$term_id = $wp_error->error_data[ 'term_exists' ];
				$this->debug( 'Term exists already with the term ID: %s', $term_id );
				$term = get_term_by( 'id', $term_id, $action->taxonomy, ARRAY_A );
			}
			else
			{
				throw new \Exception( 'Unable to create a new term.' );
			}
		}

		$term = (object)$term;
		$term_taxonomy_id = $term->term_taxonomy_id;

		$this->debug( 'Created the new term %s with the term taxonomy ID of %s.', $action->term->name, $term_taxonomy_id );

		$action->new_term = get_term_by( 'term_taxonomy_id', $term_taxonomy_id, $action->taxonomy );

		$action->finish();
	}

	/**
		@brief		[Maybe] update a term.
		@details	The "old" term is the term on the child. The "new" term is the term from the parent.
		@since		2014-04-10 14:26:23
	**/
	public function threewp_broadcast_wp_update_term( $action )
	{
		$bcd = $action->broadcasting_data;
		$this->debug( 'wp_update_term: %s', $action->new_term );
		$update = true;

		// If we are given an old term, then we have a chance of checking to see if there should be an update called at all.
		if ( $action->has_old_term() )
		{
			// Assume they match.
			$update = false;
			foreach( [ 'name', 'description', 'parent' ] as $key )
				if ( $action->old_term->$key != $action->new_term->$key )
				{
					$this->debug( 'Will update the term because of %s', $key );
					$update = true;
				}
		}

		if ( $update )
		{
			$this->debug( 'Updating the term %s.', $action->new_term->name );
			wp_update_term( $action->new_term->term_id, $action->taxonomy, array(
				'description' => $action->new_term->description,
				'name' => $action->new_term->name,
				'parent' => $action->new_term->parent,
				'term_order' => $action->new_term->term_order,
			) );
			$action->updated = true;
		}
		else
			$this->debug( 'Will not update the term %s.', $action->new_term->name );

		if ( isset( $bcd->taxonomy_term_meta ) )
		{
			$old_term_id = $action->old_term->term_id;
			$new_term_id = $action->new_term->term_id;

			$old_meta = $bcd->taxonomy_term_meta
				->collection( $bcd->parent_blog_id )				// Extract the data from the parent blog
				->collection( 'terms' )								// And extract the data from the terms subcollection
				->get( $old_term_id );								// And get the collection for this old term ID.
			if ( is_object( $old_meta ) )
			{
				foreach( $old_meta as $key => $values )
				{
					$value = reset( $values );		// Wordpress likes reporting back values in an array, even though I've never seen anyone store several values under one key.

					// Is this term protected?
					if ( $bcd->taxonomies()->protectlist_has( $action->taxonomy, $action->new_term->slug, $key ) )
					{
						$current_value = get_term_meta( $new_term_id, $key, true);
						if ( count( $current_value ) > 0 )
						{
							$this->debug( 'Taxonomy term %s (%s) already has a %s term meta value. Skipping.', $action->new_term->slug, $action->new_term->term_id, $key );
							continue;
						}
					}

					$this->debug( 'Updating taxonomy term %s with key %s and value %s', $new_term_id, $key, $value );
					update_term_meta( $new_term_id, $key, $value );
				}
			}
		}
	}
}
