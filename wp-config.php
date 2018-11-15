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
define('DB_NAME', 'amp');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'mysql');

/** MySQL hostname */
define('DB_HOST', '127.0.0.1');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

define("OTGS_DISABLE_AUTO_UPDATES", true);
define( 'WP_MEMORY_LIMIT', '256M' );

ini_set('display_errors','Off');
ini_set('error_reporting', E_ALL );
define('WP_DEBUG_DISPLAY', false);

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '?}7)%1r-:Rspxd,1j SjDm-IZU-B%TNpN!`M}G[GtR/iVl]2Bpe Mp0g@-IAgS1r');
define('SECURE_AUTH_KEY',  'R;@),(2-5p;UMRY&),xx Tn#Qj((7xhh/a>viRZrS-NEKtF~B|@9F5V]?wH80;;V');
define('LOGGED_IN_KEY',    '-,NLZ]E7Ax6pt<kq8w?SJe2 UGXET/|[%oJ>%f&l+wRRP5Cw!vnk<}3^X*61OdY ');
define('NONCE_KEY',        'g3`3uQ1C>2A0iK{f?b#1<NZ&z<e,|_yp+w<8 u3|9K0uwlAmr,0kV}jIY3T*xZ6{');
define('AUTH_SALT',        'n0dK#${{=FD44bk#T^:zr^i8;wG1LpTq7:}<KFlz9;fFi1+r`(zR(FI[3IVukX^W');
define('SECURE_AUTH_SALT', 'yUdp+InGgjsp]`cH*})ptoPTNu wH!nqiF0rY5?T{nbimz1Pp<)CHm#P|ocY]BtT');
define('LOGGED_IN_SALT',   'qFa!dmw;-#mOm1NzJG%i/C}PXRr80,~HlE7Q@Zg_PY(]x1FA[GXf91DxuD&=h^-e');
define('NONCE_SALT',       'XK9=lL+.oMhuDiI_W`ZXC-ndV9^jnd<T;VINujJuBG?o5+$@>c L,({x2(jZ`Py;');

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
define('WP_DEBUG', 'false');
define('WPLANG', 'id_ID');



/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
