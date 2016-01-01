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
define('DB_NAME', 'database_name_here');

/** MySQL database username */
define('DB_USER', 'username_here');

/** MySQL database password */
define('DB_PASSWORD', 'password_here');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
// define('AUTH_KEY',         'nh8#%ECF2ey$ZZ%^oN~4@m#zGMFp|1PV_TozoSk+LUoUiSvlE7~&[ia_k$!j!e-|');
// define('SECURE_AUTH_KEY',  'X3/&^?c,Lc#oE]kWu(1r.Uclm@GS}qm>btKmOB<@-ylt{B[4<>LZ#X]{-.WxS/LE');
// define('LOGGED_IN_KEY',    'HglJ$=;}WPp#{RBTf-j}dh3t>ROeg?XTN`+I+a:FR>lLpM/X>[FTVdrz-%BH}<bm');
// define('NONCE_KEY',        'fR$3E;%09@q93!BJ;FK{s4D?nPWLm6$mi TwqNHTMPC|jV1DMaK@@>-Ty<kbw/CN');
// define('AUTH_SALT',        '<i)SM;s~BI-P(;ez4|I<I9qZ{*9EDe[DDK-*2W{I~OLC`|t5`X!KR{+4>N=6 g0U');
// define('SECURE_AUTH_SALT', '#JPeC}r+|NfF9Yj2<|o>+!Vl`GaXa3p+c!29Zf9p$B;Z(zhEoFquft@{FH5JQ1cR');
// define('LOGGED_IN_SALT',   'en>dD8.;Ml3M]sdI:}-q8qj)AW|<,,o&o+oGM}h5`G%{<MKPH=s-OF/v4<Iz go1');
// define('NONCE_SALT',       'QK9W|5elS+i$Cw-{#9sHfH?J+ PV+lR%?/,]@oq1nXEe+gnB,QtH8Th&?zph5X.=');

define('AUTH_KEY',         'put your unique phrase here');
define('SECURE_AUTH_KEY',  'put your unique phrase here');
define('LOGGED_IN_KEY',    'put your unique phrase here');
define('NONCE_KEY',        'put your unique phrase here');
define('AUTH_SALT',        'put your unique phrase here');
define('SECURE_AUTH_SALT', 'put your unique phrase here');
define('LOGGED_IN_SALT',   'put your unique phrase here');
define('NONCE_SALT',       'put your unique phrase here');

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
