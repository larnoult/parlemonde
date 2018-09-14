<?php

namespace threewp_broadcast\broadcast_data;

use threewp_broadcast\BroadcastData;

/**
	@brief		Cache for broadcast data
	@since		20131009
**/
class cache
	extends \threewp_broadcast\cache\posts_cache
{

	/**
		@brief		Store an empty broadcast data object.
		@since		20131010
	**/
	public function cache_no_data( $blog_id, $post_id )
	{
		$key = $this->key( $blog_id, $post_id );
		$this->set( $key, new BroadcastData );
	}

	/**
		@brief		Gets the broadcast data of a blog+post combo.
		@details 	Will always return a broadcast_data object.
		@return		broadcast_data		Broadcast data object.
		@since		20131010
	**/
	public function get_for( $blog_id, $post_id )
	{
		$key = $this->key( $blog_id, $post_id );

		if ( ! $this->has( $key ) )
		{
			// Retrieve the post data for this solitary post.
			$results = $this->lookup( $blog_id, $post_id );
			if ( count( $results ) > 0 )
			{
				// We keep the first result. The rest have to go.
				$result = array_shift( $results );
				$bcd = $result[ 'data' ];

				$bc = ThreeWP_Broadcast();

				foreach( $results as $result )
				{
					$bc->debug( 'Deleting duplicate BCD %d for blog %d post %d', $result[ 'id' ], $blog_id, $post_id );
					$bc->sql_delete_broadcast_data( $result[ 'id' ] );
				}
			}
			else
				$bcd = new BroadcastData;
			$this->set_for( $blog_id, $post_id, $bcd );
		}
		return $this->get( $key );
	}

	/**
		@brief		Asks ThreeWP_Broadcast to look up some broadcast datas.
		@since		20131010
	**/
	public function lookup( $blog_id, $post_ids )
	{
		return \threewp_broadcast\ThreeWP_Broadcast::instance()->sql_get_broadcast_datas( $blog_id, $post_ids );
	}
}
