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
define( 'DB_NAME', 'c6335webshop' );

/** MySQL database username */
define( 'DB_USER', 'c6335Ryan' );

/** MySQL database password */
define( 'DB_PASSWORD', '8113326' );

/** MySQL hostname */
define( 'DB_HOST', '127.0.0.1' );

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
define( 'AUTH_KEY',         'gG|m}<pc_P^l<V^3.5I<GtXK}{av7#Q{V/S eW7KINg5#*XxknCqf7$k|<&`XcY0' );
define( 'SECURE_AUTH_KEY',  '!3$T++Fxx[NHF!)]9dg`viU%E+JsFo#FQcg+QoD ZrG%~F/oy86+F[dT:Ssou$b[' );
define( 'LOGGED_IN_KEY',    'NQz@>&#E<3c}8|de+8Nt NmM!1xnAEm,!o2p.,U@22msP}*JQW{+CFoRqNzy*V5D' );
define( 'NONCE_KEY',        '7l EJB1=c#IXb@Ni@-[F^_<k2Wt{|mABjOHF32~@$PybgK;C_DF8d|v&lC, h%51' );
define( 'AUTH_SALT',        'bi9>$jPB<SXlt.vJ@O5s3P3y5+W-E)o;.`1FLkWq<Nzm8)FqY{`riy{9A_oGhVTt' );
define( 'SECURE_AUTH_SALT', '(/n$M3V|=.(,SxU}h>aPoa)S%Z$Q%l@g,C;}Y!WCk>b#yD>l~q9S,lfY2/P`N:>$' );
define( 'LOGGED_IN_SALT',   'f,EOk@E:J)7],$n~ZvbVrx>yv0>rTaB _eqGB4K*@4FdZnBbll/DvJI7,3*I?&o(' );
define( 'NONCE_SALT',       'GuvFc,?*8~SBw?0FnpPfRwT(W,s9rsYbz<)BJ1X6/cIlFN0QS99>/M~m!~7xW7Ti' );

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
