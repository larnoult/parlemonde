<?php

namespace threewp_broadcast\broadcasting_data;

/**
	@brief		Helper class that handles partial broadcast info.
	@detail		Sometimes one encounters a single broadcast that takes way too long: WooCommerce products with many variations.

				Using the queue in that case normally doesn't help since a product + variations is considered one broadcast. Here is where the partial broadcast objects comes in.

				Used like a collection, it tells the queue plugin that it should not delete the broadcasting data after broadcast, but rebroadcast it.

				$bcd->partial_broadcast()->set( 'my_unique_identifier', true );

				The identifier is any old string. The queue will check whether the partial broadcast collection is empty. If not, keep the data, rebroadcast.

				When you're done:

				$bcd->partial_broadcast()->forget( 'my_unique_identifier' );

				and the queue will delete the broadcasting data and continue with the next item in the queue.

	@since		2016-12-04 18:49:37
**/
class Partial_Broadcast
	extends \threewp_broadcast\collection
{
	/**
		@brief		Is the partial broadcast finished?
		@details	It is finished when there are no more add-ons registered.
		@since		2016-12-04 18:50:23
	**/
	public function finished()
	{
		return count( $this ) < 1;
	}
}
