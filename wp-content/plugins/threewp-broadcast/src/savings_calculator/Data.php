<?php

namespace threewp_broadcast\savings_calculator;

class Data
	extends \threewp_broadcast\collection
{
	use \plainview\sdk_broadcast\wordpress\object_stores\Site_Option;

	/**
		@brief		Return an array of default values.
		@since		2015-10-28 10:34:09
	**/
	public static function get_defaults()
	{
		return [
			'new_post_basic_setup' => '90',
			'time_per_attachment' => '60',
			'updated_post_discount' => '10',
			'hourly_wage' => '10',
		];
	}

	/**
		@brief		Get the current value or the default value.
		@since		2015-10-27 22:16:01
	**/
	public function get_or_default( $key )
	{
		$defaults = static::get_defaults();
		return $this->get( $key, $defaults[ $key ] );
	}

	/**
		@brief		Return the table containing all of the savings data.
		@since		2015-10-27 21:53:38
	**/
	public function get_savings_table()
	{
		$table = static::store_container()->table();

		$row = $table->body()->row();
		// Since when is the time savings function monitoring
		$row->th()->text( __( 'Monitoring since', 'threewp-broadcast' ) );
		$since = $this->get( 'since', time() );
		$row->td()->text( date( 'Y-m-d', $since ) );

		$row = $table->body()->row();
		// How many posts have been created during time savings monitoring
		$row->th()->text( __( 'Posts created', 'threewp-broadcast' ) );
		$row->td()->text( $this->get( 'posts_created', 0 ) );

		$row = $table->body()->row();
		// How many posts have been updated during time savings monitoring
		$row->th()->text( __( 'Posts updated', 'threewp-broadcast' ) );
		$row->td()->text( $this->get( 'posts_updated', 0 ) );

		$row = $table->body()->row();
		// How much time has been saved during time savings monitoring
		$row->th()->text( __( 'Time saved (HH:MM:SS)', 'threewp-broadcast' ) );
		$time_saved = $this->get( 'time_saved', 0 );

		// This is complicated. To get a nice date we can use gmdate, but it won't give us days.
		$nice_date = gmdate("H:i:s", $time_saved);
		$parts = explode( ':', $nice_date );
		$hours = $parts[ 0 ];
		if ( $hours > 24 )
		{
			// Time to calculate days.
			$days = floor( $hours / 24 );
			$hours = $hours - ( $days * 24 );
			$nice_date = sprintf( '%s %s %s:%s:%s',
				$days,
				__( 'days', 'threewp-broadcast' ),
				$hours,
				$parts[ 1 ],
				$parts[ 2 ]
			);
		}
		$row->td()->text( $nice_date );

		$row = $table->body()->row();
		$row->th()->text( __( 'Money saved', 'threewp-broadcast' ) );
		$wage = $this->get( 'hourly_wage', 100 );
		$cost = $time_saved / 60 / 60 * $wage;
		$cost = floor( $cost );
		$row->td()->textf( '&sect; %s', $cost );

		return $table;
	}

	/**
		@brief		Reset the stats. Time saved and posts updated and what not.
		@since		2015-10-27 22:20:23
	**/
	public function reset_stats()
	{
		foreach( [
			'posts_created',
			'posts_updated',
			'time_saved',
			'since',
		] as $key )
			$this->forget( $key );

		$this->save();
	}

	public static function store_container()
	{
		return ThreeWP_Broadcast();
	}

	public static function store_key()
	{
		return 'savings_calculator_data';
	}

}
