<?php

namespace threewp_broadcast\maintenance\checks\broadcast_data\traits;

trait post_bcd_caching
{
	public function cache_post_bcd( $blog_id, $post_id, $bcd )
	{
		$key = $this->post_bcd_cache_key( $blog_id, $post_id );
		$this->data->post_bcd_cache->set( $key, $bcd );
	}

	public function lookup_post_bcd( $blog_id, $post_id )
	{
		$key = $this->post_bcd_cache_key( $blog_id, $post_id );
		if ( ! $this->data->post_bcd_cache->has( $key ) )
			return false;
		return $this->data->post_bcd_cache->get( $key );
	}

	public function post_bcd_cache_key( $blog_id, $post_id )
	{
		return sprintf( '%s_%s', $blog_id, $post_id );
	}
}
