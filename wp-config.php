<?php
/** Enable W3 Total Cache **/

/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('WP_CACHE', true);
define( 'WPCACHEHOME', '/home/parlemon/www/wp-content/plugins/wp-super-cache/' );
define('DB_NAME', 'parlemonde-plm');

/** MySQL database username */
define('DB_USER', 'parlemonde-plm');

/** MySQL database password */
define('DB_PASSWORD', 'DbhFkkstesZ9');

/** MySQL hostname */
define('DB_HOST', 'parlemonde-plm.mysql.db');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');


define('WP_ALLOW_REPAIR', true);
/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'mYBU{|?:mm_torwuYji-x6iQu!l@in;a7^~+c]d]fsB$lRu]AwqOyGm(hn[G?jHU');
define('SECURE_AUTH_KEY',  '1;pbiJqV7F|ZH!8WYm?YU1n&$vjFi=}f.{(SG-6n,*izCgL?>t0%}D]r~M{eYV0M');
define('LOGGED_IN_KEY',    'K<+EX-A) e0S&~GI!)RuKmKDJG$S{i.:rRE}?o&4{{N2P*`%]qR!LH(IL?XP0rGc');
define('NONCE_KEY',        'XSj##xiM=M:O-3$<o)W}YGPA!#Yan6LJ!Z(/]U`X^HI2GQ0q.oqulO0hXie,)OcV');
define('AUTH_SALT',        'P-;g?wpnz4oTE-Zl2eEY6@?ROKI:x9T|A$SIoA+(h{{@o#hVyE]9X|C2{Ep<0~x|');
define('SECURE_AUTH_SALT', '%8M(([_,+,SoWMP?x;,ostw+Nh}GQ~lkByN++]dUPRGaltkfGr>Q1z)O1mw(6+*s');
define('LOGGED_IN_SALT',   '&jWI^:$zulzwG{N}`ahVLp`UXR`@4|--Oz5c>5>+ d!}ilUTt-F?r}8Vg-o0!|cr');
define('NONCE_SALT',       'D?th2&OE)+[e:3!uk0nq8#B.01`Dz$6dY:TiUpU=N_bvO`0czFw?I#Kb^H_yc.Qx');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', 'fr_FR');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */
/* Multisite */
define('WP_ALLOW_MULTISITE', true);
define('MULTISITE', true);
define('SUBDOMAIN_INSTALL', true);
define('DOMAIN_CURRENT_SITE', 'www.parlemonde.org');
define('PATH_CURRENT_SITE', '/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);
define( 'NOBLOGREDIRECT', 'http://www.parlemonde.org' );

/*To change BuddyPress in pelico.parlemonde.org */

define ( 'BP_ROOT_BLOG', 7);

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
