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
define('DB_NAME', 'blog');

/** MySQL database username */
define('DB_USER', 'galific');

/** MySQL database password */
define('DB_PASSWORD', 'galific@2019');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

define('WP_SITEURL','https://www.galific.com/blog');
define('WP_HOME','https://www.galific.com/blog');
/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'iXPW.rg5:R bpNaQr2*+9!xG$=5VQ)}73CpL>YQ4Uw0Hu|I>5o<6@ln[+aChthM|');
define('SECURE_AUTH_KEY',  'iFb#g`q{l:4Sh+[fw#>d:FuF1vY9U|s_-H!g);XU4$ky1r:|:Qm!wt^eYicg|u.(');
define('LOGGED_IN_KEY',    '+y*0Kk56/y0tA!J~fN}}szngh9PI.md&[/h9shluiL(L}!{~WR-/o#gm^s2gc-)+');
define('NONCE_KEY',        '9^k+].zpjN!^rU_+M~;jcH1lRUJ+`t2On);Y]~S[m8z8ZA:=B3F>z5yrpP?`}[<G');
define('AUTH_SALT',        'OSy{-OYG}zTTq&wK>Que*1uUZA2ACNH/I-@O]X wIh]+KetV/^7Zf~E7Zlw~xb1I');
define('SECURE_AUTH_SALT', 'pjbly[uxu-M@r7+W-kQcznPR[I]jkOdK>j>To7j4L-BccZ,;ttZ-n&(.E$2FERpX');
define('LOGGED_IN_SALT',   ' ;J>`PJb ][hY !}F#;|_goB|-.LK(l6-E>|!Fa)e%CwW5)?`0OX|Hg-?~.XuRVV');
define('NONCE_SALT',       '4%y1+|c,{ISk=P:)l:s~(r!l@H+#m~w8&KU+,,2cW*cwY8UOzO!~un(o*-VVMeBh');


/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
