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
define( 'DB_NAME', 'wordpress-database' );

/** MySQL database username */
define( 'DB_USER', 'wordpress_user' );

/** MySQL database password */
define( 'DB_PASSWORD', 'iq7Sn6mhZMXmGtIH' );

/** MySQL hostname */
define( 'DB_HOST', 'wordpress-db.ctuvllfpf3pd.us-east-2.rds.amazonaws.com' );

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
define( 'AUTH_KEY',         '4Dyy@a]$XiU-w,z3NOT zN]oozV{qv:;SYi&RnoN%L9AF;n;Xt4(i6b*7;W.3gd@' );
define( 'SECURE_AUTH_KEY',  'HtnUr*ek:tJ%xG8`]9KD?nUv)u-E@Y}J<`z>drH:D~tiXVhA4k|;=)D X.bUg!?L' );
define( 'LOGGED_IN_KEY',    'Xe/U;uk;H]jjBq$cB(bd4Ijl1Fmcr6tMZQ>qL:_cE9HZ;P<YK w;33n,S==A-wy|' );
define( 'NONCE_KEY',        'e|VI9FC$.S]^l]LWh8|SUp(y?.>#v.>66*LK+q5-7#N]LLC]}ucf!DlLt*Lj>zhi' );
define( 'AUTH_SALT',        'v3JP]#RoYy0$linP)o9fqmph(]ky=Gz+k0R+WzYi<m}UipF.dl5335FXD-nz[.j~' );
define( 'SECURE_AUTH_SALT', 'W!!K@y3INo``HEYs:{k%#,mh@M^L/0HR*L/FZG[GhSM7Q^=x}xcDUo{&cO:iIT&D' );
define( 'LOGGED_IN_SALT',   ']lW.i3T) X$%ok~~Z(JVd`Y1iv:L`eo`Ae,]Q:Co1@Qf~]SfY~x4#`ns8k@e|/[;' );
define( 'NONCE_SALT',       'DNX$2,5UEK>A9ncAn<T}.lp*JW@[`KkgjG6tvJlFs2{RBmslms=2Zn?)_st,nM1o' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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