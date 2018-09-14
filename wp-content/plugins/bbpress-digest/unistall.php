<?php

/**
 * bbPress Digest Unistall
 *
 * Code used when the plugin is deleted.
 *
 * @package bbPress Digest
 * @subpackage Unistall
 */

/* Exit if accessed directly or not in unistall */
if ( ! defined( 'ABSPATH' ) || ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;


/*
 * Remove options on uninstallation of plugin.
 *
 * Based on delete_post_meta_by_key()
 *
 * @since 1.0
 *
 * @uses delete_metadata() To delete all users meta data.
 * @uses delete_option()   To delete all site settings.
 */

/* Remove users settings */
delete_metadata( 'user', null, 'bbp_digest_time',   '', true );
delete_metadata( 'user', null, 'bbp_digest_day',    '', true );
delete_metadata( 'user', null, 'bbp_digest_forums', '', true );

/* Remove site's settings */
delete_option( '_bbp_digest_show_one_click' );
delete_option( '_bbp_digest_enable_weekly' );