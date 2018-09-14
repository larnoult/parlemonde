<?php

namespace threewp_broadcast\broadcasting_data;

/**
	@brief		Convenience class for handling taxonomies.
	@since		2017-07-10 17:01:02
**/
class Taxonomies
{
	/**
		@brief		The broadcasting data object.
		@since		2015-06-06 09:02:08
	**/
	public $broadcasting_data;

	/**
		@brief		Constructor.
		@since		2015-06-06 09:01:58
	**/
	public function __construct( $broadcasting_data )
	{
		$this->broadcasting_data = $broadcasting_data;

		// Convenience.
		$bcd = $this->broadcasting_data;

		if ( ! is_object( $bcd->taxonomy_data ) )
			$bcd->taxonomy_data = ThreeWP_Broadcast()->collection();

		// If the blacklist isn't set, then nothing is.
		if ( ! $bcd->taxonomy_data->has( 'taxonomy_term_meta' ) )
		{
			// Set up the blacklist and protectlist.
			foreach( [ 'blacklist', 'protectlist' ] as $list_type )
			{
				// Get the option.
				$key = 'taxonomy_term_' . $list_type;
				$option_value = ThreeWP_Broadcast()->get_site_option( $key );
				// Convert the option to collections.
				$lines = explode( "\n", $option_value );
				$lines = array_filter( $lines );
				foreach( $lines as $line )
				{
					$line = trim( $line );
					$columns = explode( ' ', $line );
					$columns = array_filter( $columns );

					// Each line MUST have 1 taxonomy, 1 term and at least 1 field.
					if ( count( $columns ) < 3 )
						continue;
					$taxonomy = array_shift( $columns );
					$term = array_shift( $columns );

					$meta_key_collection = $bcd->taxonomy_data
							->collection( 'taxonomy_term_meta' )
							->collection( $list_type )
							->collection( $taxonomy )
							->collection( $term );

					foreach( $columns as $column )
							$meta_key_collection->append( $column );
				}
			}
		}
	}

	/**
		@brief		Convenience method to also sync this taxonomy.
		@details	Used best during broadcasting_started.
		@since		2017-11-08 12:38:59
	**/
	public function also_sync( $post_type, $taxonomy )
	{
		ThreeWP_Broadcast()->debug( 'Also syncing taxonomy <em>%s</em> for post type <em>%s</em>.', $taxonomy, $post_type );

		// We need to store the taxonomy + terms of the post type.
		// Fake a post
		$post = (object)[
			'ID' => 0,
			'post_type' => $post_type,
			'post_status' => 'publish',
		];

		// And now collect the taxonomy info for the post type.
		$post_bcd = new \threewp_broadcast\broadcasting_data( [
			'parent_post_id' => -1,
			'post' => $post,
		] );
		$post_bcd->add_new_taxonomies = true;
		unset( $post_bcd->post->ID );		// This is so that collect_post_type_taxonomies returns ALL the terms, not just those from the non-existent post.
		ThreeWP_Broadcast()->collect_post_type_taxonomies( $post_bcd );

		// Copy the collected taxonomy data.
		$this->broadcasting_data->parent_blog_taxonomies[ $taxonomy ] = $post_bcd->parent_blog_taxonomies[ $taxonomy ];
	}

	/**
		@brief		Checks whether a taxonomy + term + meta_key combo exist in the blacklist.
		@since		2017-07-10 17:13:49
	**/
	public function blacklist_has( $taxonomy_slug, $term_slug, $meta_key )
	{
		return $this->list_has( 'blacklist', $taxonomy_slug, $term_slug, $meta_key  );
	}

	/**
		@brief		Checks whether a taxonomy + term combo exist in the *list.
		@since		2017-07-10 17:13:49
	**/
	public function list_has( $list_type, $taxonomy_slug, $term_slug, $meta_key )
	{
		// Extract the list type.
		$ttm = $this->broadcasting_data
			->taxonomy_data
			->collection( 'taxonomy_term_meta' )
			->collection( $list_type );

		foreach( $ttm->to_array() as $ttm_taxonomy_slug => $ttm_terms )
		{
			// Does this slug match?
			if ( ! static::matches( $taxonomy_slug, $ttm_taxonomy_slug ) )
				continue;
			// Go through the terms and see if they match.
			foreach( $ttm_terms as $ttm_term_slug => $meta_keys )
			{
				// Does this slug match?
				if ( ! static::matches( $term_slug, $ttm_term_slug ) )
					continue;
				// And now look for a match in the metya keys.
				foreach( $meta_keys as $ttm_meta_key )
					if ( static::matches( $meta_key, $ttm_meta_key ) )
						return true;
			}
		}
		return false;
	}

	/**
		@brief		Does this needle exist in the haystack?
		@since		2017-07-12 06:57:23
	**/
	public static function matches( $haystack, $needle )
	{
		// No wildcard = straight match
		if ( strpos( $needle, '*' ) === false )
		{
			if ( $needle == $haystack )
				return true;
		}
		else
		{
			$preg = str_replace( '*', '.*', $needle );
			$preg = sprintf( '/%s/', $preg );
			preg_match( $preg, $haystack, $matches );
			if ( ( count( $matches ) == 1 ) && $matches[ 0 ] == $haystack )
				return true;
		}
		return false;
	}

	/**
		@brief		Checks whether a taxonomy + term + meta_key combo exist in the protectlist.
		@since		2017-07-10 17:13:49
	**/
	public function protectlist_has( $taxonomy_slug, $term_slug, $meta_key )
	{
		return $this->list_has( 'protectlist', $taxonomy_slug, $term_slug, $meta_key );
	}
}
