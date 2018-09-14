<?php

class WPMDBPro_Multisite_Tools_CLI extends WPMDBPro_Multisite_Tools {

	function __construct( $plugin_file_path ) {
		parent::__construct( $plugin_file_path );

		// Add support for extra CLI args with a lower priority so that it can check media options.
		add_filter( 'wpmdb_cli_filter_get_extra_args', array( $this, 'filter_extra_args' ), 10, 1 );
		add_filter( 'wpmdb_cli_filter_get_profile_data_from_args', array( $this, 'add_extra_cli_args' ), 20, 3 );
		add_filter( 'wpmdb_cli_default_find_and_replace', array( $this, 'filter_cli_default_find_and_replace' ), 20, 2 );

		// Filter verify connection to let it know tables can be pre-filtered.
		add_filter( 'wpmdb_cli_verify_connection_to_remote_site_args', array( $this, 'filter_cli_verify_connection_to_remote_site_args' ), 10, 2 );
		add_filter( 'wpmdb_verify_connection_to_remote_site_args', array( $this, 'filter_cli_verify_connection_to_remote_site_args' ), 10, 2 );
	}

	/**
	 * Add extra CLI args used by this plugin.
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public function filter_extra_args( $args = array() ) {
		$args[] = 'subsite';
		$args[] = 'prefix';

		return $args;
	}

	/**
	 * Add support for extra CLI args.
	 *
	 * @param array $profile
	 * @param array $args
	 * @param array $assoc_args
	 *
	 * @return array
	 */
	public function add_extra_cli_args( $profile, $args, $assoc_args ) {
		if ( ! is_array( $profile ) ) {
			return $profile;
		}

		global $wpmdbpro_cli;

		// --subsite=<blog-id|subsite-url>
		$mst_select_subsite   = false;
		$mst_selected_subsite = 0;
		if ( isset( $assoc_args['subsite'] ) ) {
			if ( ! is_multisite() && 'savefile' === $profile['action'] ) {
				return $wpmdbpro_cli->cli_error( __( 'The installation must be a Multisite network to make use of the export subsite option', 'wp-migrate-db-pro-multisite-tools' ) );
			}
			if ( empty( $assoc_args['subsite'] ) ) {
				return $wpmdbpro_cli->cli_error( __( 'A valid Blog ID or Subsite URL must be supplied to make use of the subsite option', 'wp-migrate-db-pro-multisite-tools' ) );
			}
			if ( is_multisite() ) {
				$mst_selected_subsite = $this->get_subsite_id( $assoc_args['subsite'] );

				if ( false === $mst_selected_subsite ) {
					return $wpmdbpro_cli->cli_error( __( 'A valid Blog ID or Subsite URL must be supplied to make use of the subsite option', 'wp-migrate-db-pro-multisite-tools' ) );
				}
			} else {
				$mst_selected_subsite = $assoc_args['subsite'];
			}

			$mst_select_subsite = true;
		}

		// --prefix=<new-table-prefix>
		global $wpdb;
		$new_prefix = $wpdb->base_prefix;
		if ( isset( $assoc_args['prefix'] ) ) {
			if ( false === $mst_select_subsite ) {
				return $wpmdbpro_cli->cli_error( __( 'A new table name prefix may only be specified for subsite exports.', 'wp-migrate-db-pro-multisite-tools' ) );
			}
			if ( empty( $assoc_args['prefix'] ) ) {
				return $wpmdbpro_cli->cli_error( __( 'A valid prefix must be supplied to make use of the prefix option', 'wp-migrate-db-pro-multisite-tools' ) );
			}
			$new_prefix = trim( $assoc_args['prefix'] );

			if ( sanitize_key( $new_prefix ) !== $new_prefix ) {
				return $wpmdbpro_cli->cli_error( $this->get_string( 'new_prefix_contents' ) );
			}
		}

		// Disable Media Files Select Subsites if using Subsite Migration.
		if ( $mst_select_subsite && ! empty( $profile['mf_select_subsites'] ) && ! empty( $profile['mf_selected_subsites'] ) ) {
			unset( $profile['mf_select_subsites'], $profile['mf_selected_subsites'] );
		}

		$filtered_profile = compact(
			'mst_select_subsite',
			'mst_selected_subsite',
			'new_prefix'
		);

		return array_merge( $profile, $filtered_profile );
	}

	/**
	 * Ensure CLI has appropriate default find and replace values when doing MST.
	 *
	 * @param array $profile
	 * @param array $post_data
	 *
	 * @return array
	 *
	 * TODO: Update for multisite <=> multisite (blog_ids)
	 */
	public function filter_cli_default_find_and_replace( $profile, $post_data ) {
		if ( is_wp_error( $profile ) ) {
			return $profile;
		}

		if ( empty( $profile['mst_select_subsite'] ) || empty( $profile['mst_selected_subsite'] ) ) {
			return $profile;
		}

		$source = ( 'pull' === $post_data['intent'] ) ? $post_data['site_details']['remote'] : $post_data['site_details']['local'];
		$target = ( 'pull' === $post_data['intent'] ) ? $post_data['site_details']['local'] : $post_data['site_details']['remote'];

		$blog_id = false;

		if ( 'true' === $source['is_multisite'] && ! empty( $source['subsites'] ) ) {
			$blog_id = $this->get_subsite_id( $profile['mst_selected_subsite'], $source['subsites'] );
		} elseif ( 'true' === $target['is_multisite'] && ! empty( $target['subsites'] ) ) {
			$blog_id = $this->get_subsite_id( $profile['mst_selected_subsite'], $target['subsites'] );
		}

		if ( false === $blog_id ) {
			return $profile;
		}

		if ( 'true' === $source['is_multisite'] && ! empty( $source['subsites_info'][ $blog_id ]['site_url'] ) ) {
			$profile['replace_old'][1] = '//' . untrailingslashit( $this->scheme_less_url( $source['subsites_info'][ $blog_id ]['site_url'] ) );
		}

		if ( 'true' === $target['is_multisite'] && ! empty( $target['subsites_info'][ $blog_id ]['site_url'] ) ) {
			$profile['replace_new'][1] = '//' . untrailingslashit( $this->scheme_less_url( $target['subsites_info'][ $blog_id ]['site_url'] ) );
		}

		return $profile;
	}

	/**
	 * If doing MST add selected subsite details to verify connection args.
	 *
	 * @param array $args
	 * @param array $profile
	 *
	 * @return mixed
	 */
	public function filter_cli_verify_connection_to_remote_site_args( $args, $profile ) {
		if ( is_wp_error( $args ) ||
		     empty( $profile['mst_select_subsite'] ) ||
		     empty( $profile['mst_selected_subsite'] )
		) {
			return $args;
		}

		$args['mst_select_subsite']   = $profile['mst_select_subsite'];
		$args['mst_selected_subsite'] = $profile['mst_selected_subsite'];

		return $args;
	}
}
