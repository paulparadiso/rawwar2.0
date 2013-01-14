<?php
/** 
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information by
 * visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
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
define('DB_NAME', '374821_rawwardev');

/** MySQL database username */
define('DB_USER', '374821_rawwardev');

/** MySQL database password */
define('DB_PASSWORD', 'oeI3jf4aLlkj23');

/** MySQL hostname */
define('DB_HOST', 'rawwardev.paradisoprojects.com');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');
error_reporting(E_ERROR | E_WARNING | E_PARSE);
/**#@+
 * Authentication Unique Keys.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',        'Z?-H,0oV&E:C}oW6GnMfZ5 I5Jp<0-1kzld&5S>|]>$f!+./-PhLwCqoHRw+WBBP');
define('SECURE_AUTH_KEY', 'F<rXI O8K6xz0?=H%MUy*StxD70H%vcan4QJLNW1%K|td`R$RnY71l_D_d`b`waB');
define('LOGGED_IN_KEY',   'Ysh{pML~U*j)DH zPv%+([CM7W)-`.|J|lAlJ]?dJ6x o+}C:vDV0olp$lKai%in');
define('NONCE_KEY',       '-R,RyG(O&6!1[91iU-fP>q6RA?F|7U-Pax&T,_5mc))m[l[#smV8YV}S;q_=(KS?');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'rw_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress.  A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de.mo to wp-content/languages and set WPLANG to 'de' to enable German
 * language support.
 */
define ('WPLANG', '');

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
