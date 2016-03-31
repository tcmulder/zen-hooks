<?php
/**
 * Base configurations for servers other than live
 */
$hostname = $_SERVER['HTTP_HOST'];
$proj_name = 'PROJECT_NAME';
$db_pass = 'DATABASE_PASSWORD';

switch ($hostname) {
    //host: port 8888
    case (strstr($hostname, '8888') == true):
        define('DB_NAME', "l1_$proj_name");
        define('DB_USER', 'root');
        define('DB_PASSWORD', 'root');
        define('DB_HOST', 'localhost');
        break;
    //host: localhost
    case 'localhost':
        define('DB_NAME', "l1_$proj_name");
        define('DB_USER', 'root');
        define('DB_PASSWORD', 'root');
        define('DB_HOST', 'localhost');
        break;
    //host: dev1
    case 'YOUR_SERVER_ADDRESS':
        define('DB_NAME', "d1_$proj_name");
        define('DB_USER', "d1_$proj_name");
        define('DB_PASSWORD', "$db_pass");
        define('DB_HOST', 'localhost');
        break;
    //host: test1
    case 'YOUR_SERVER_ADDRESS':
        define('DB_NAME', "t1_$proj_name");
        define('DB_USER', "t1_$proj_name");
        define('DB_PASSWORD', "$db_pass");
        define('DB_HOST', 'localhost');
        break;
    //host: stage1
    case 'YOUR_SERVER_ADDRESS':
        define('DB_NAME', "s1_$proj_name");
        define('DB_USER', "s1_$proj_name");
        define('DB_PASSWORD', "$db_pass");
        define('DB_HOST', 'localhost');
        break;
    //host: preview1
    case 'preview1.zenman.com':
        define('DB_NAME', "p1_$proj_name");
        define('DB_USER', "p1_$proj_name");
        define('DB_PASSWORD', "$db_pass");
        define('DB_HOST', 'localhost');
        break;
    //fallback error
    default:
        define('DB_NAME', 'wp_fail');
        define('DB_USER', 'userfail');
        define('DB_PASSWORD', 'passwordfail');
        define('DB_HOST', 'hostfail');
        break;
}

define('DB_CHARSET', 'utf8');

define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the
 * {@link https://api.wordpress.org/secret-key/1.1/salt/
 * WordPress.org secret-key service}
 * You can change these at any point in time to invalidate
 * all existing cookies. This will force all users to
 * have to log in again.
 *
 * @since 2.6.0
 */
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
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/**
 * Disable automatic updates
 */
define( 'WP_AUTO_UPDATE_CORE', false );

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
    define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');