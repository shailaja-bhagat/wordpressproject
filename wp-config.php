<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
if( $_SERVER == "localhost"){

	define( 'DB_NAME', 'wp-assignment' );

	/** MySQL database username */
	define( 'DB_USER', 'root' );

	/** MySQL database password */
	define( 'DB_PASSWORD', '' );

	/** MySQL hostname */
	define( 'DB_HOST', 'localhost' );

} else {

	define( 'DB_NAME', 'wordpress-assignment' );

	/** MySQL database username */
	define( 'DB_USER', 'wordpress_user' );

	/** MySQL database password */
	define( 'DB_PASSWORD', 'iq7Sn6mhZMXmGtIH' );

	/** MySQL hostname */
	define( 'DB_HOST', 'wordpress-db.ctuvllfpf3pd.us-east-2.rds.amazonaws.com' );
	
}

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'mG:]9biE,:Xsf6OPlL`v9oD}FQQ|@9yk975Qe~C/sNkN:P{N&+l6y!kP>}tby]7q' );
define( 'SECURE_AUTH_KEY',  'G=YgC<*Z[j:]3_@9xRNOR&@zfU.7FG1m?S|)K]2tNuWNAYQF&?+Sq}]zK-&N,ya[' );
define( 'LOGGED_IN_KEY',    'Y??24w>rDtp,yy:;4Ks@74D$;mJbbCN8DQ{X{bjj{AGon5au]1TD_S%sjwb%PR{-' );
define( 'NONCE_KEY',        '5+Fv},oM]DP!RUSN.XBC:;k|f`y$S1lCb;l+t>;4Et$cpMEnh%0KZKb(S]V #q,H' );
define( 'AUTH_SALT',        ']z0`e*UcTd=/pgENJ1ux<-^9[~B7l^&vrst(kFxh]ad]0{OR)~cY|SlHYHk3-Wym' );
define( 'SECURE_AUTH_SALT', '7g3%.C7*;yMMTlFhnh+4h`I5-1n,Kk]t/<L{F]~%CI;?N+vU>*#T1msP)kK7`]K2' );
define( 'LOGGED_IN_SALT',   'Ss_!W[wDwRf9i2KpD}oV7}NHNGQ,.z>7Yx[Epw~Z=LE;9D|W0|6H08ntU>N?Gf0]' );
define( 'NONCE_SALT',       '{:Ad<S<eYPW7C+dQ_alN##Z}?zKqWL]?8TeUz1xmABkjq2`7g ~&YF%(;t#*~#^~' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wa_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
define('FS_METHOD', 'direct');