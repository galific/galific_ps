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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'blog' );

/** MySQL database username */
define( 'DB_USER', 'galific' );

/** MySQL database password */
define( 'DB_PASSWORD', 'galific@2019' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

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
define( 'AUTH_KEY',         'WXBc5_|}/Gx]NlftcwIdV*Kj7kEyK%da|9RiT.C =-Ny4XM.}&HP`~/vVgv_V=c(' );
define( 'SECURE_AUTH_KEY',  'PZaz:nq4ljzK0FQ@F*2L5i$Gi>02&pZ%~^Qst^TW/}bF%W&#5d@_4`Az+VZPr(V9' );
define( 'LOGGED_IN_KEY',    '=OI*!s]#y(w8juoz40+F@73`0eD~a^j+geT9bUeQ<;V4.9xMW7^YKF=Mv|~og!#U' );
define( 'NONCE_KEY',        'U/4vmH{j6zK Gsgyz:MSrM*Hr,|:p4lU6#96}E<<eDZM$9Y7sD2z&0mB=}91,ZuU' );
define( 'AUTH_SALT',        'Iih20=lo[H5rB;yt)x@cKD^Y^cF^WgXMPvG~7TOVoO/F.%609tVD3EJ&9Dch@Yl~' );
define( 'SECURE_AUTH_SALT', '&AV?Srro82!!{djcfI6Z0y!.btwf/zgO]OO&2(P51miboniB&aOebcdk-53NjF4C' );
define( 'LOGGED_IN_SALT',   '+h*bX#)E5x^P7KOSEP%L!75A/m_<Bj}k7`z3G~Fl?.c/s?)LW*(suv.uNxCX5{j2' );
define( 'NONCE_SALT',       'bDaMH?|Er:iBVAVMad)`N5>&DUm!j+7&y}#x@4ZsAsR;)@[A6FU1y,F6MtU>qP;.' );

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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
