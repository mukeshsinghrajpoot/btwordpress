<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'btwordpress' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '123456' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'C1-c84.<+QVHS@er+Q4H3Sn$XZ0L}N9pKp.]*jkoZ4qnF3.V 1Q&8A+_hra^0AB=' );
define( 'SECURE_AUTH_KEY',  'PjDyi^T!tp}Je7sg{PAAga|&>nH#jM3AFZ#!<U#K,tM?R&U Zs2gF-57h/3YNkG6' );
define( 'LOGGED_IN_KEY',    'R$^7^nHRrw65ci{7>|N&8Nt<*Fx/gs|HLRy,]BiB$ljX&k05$}dY[6lpvT*)NpJ~' );
define( 'NONCE_KEY',        '!`0yCOwugC8P:qyJ{%8V~8AI6|&j^U/)-v/PbT0{CMK??KeoFLEa?F<&YO~BD=cj' );
define( 'AUTH_SALT',        'ISsbg`?$uB20=f:v|Kh@D(f@gFffV&)r Kt^qLj9!d]6K(mJQ0@gp?-/V%zu4hVv' );
define( 'SECURE_AUTH_SALT', 'ef5rO(qCkr@4j8la8_Be^UH5E=5tp>H pUmn;5P,g(OK&K3UPI.UqZT84y-EuP~>' );
define( 'LOGGED_IN_SALT',   'b94K+nGqIDPTEyi|g].s_6}5!STGZ%Kg1e|kgKAbAHvqLn-di7P0Ej[jv9f}Z=B,' );
define( 'NONCE_SALT',       'G3nw11}m;Dco.v,*&Q3`]@,y`sXh^t.n.E?[=&W>$=MEz!A@u.xQ*./,rN..,ZY9' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'btwordpress_';

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', true );
define('WP_DEBUG_LOG', true);
define('ALLOW_UNFILTERED_UPLOADS', true); 
/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
