<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'inv_manager' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         'rIk:U77-tdT*]HxBcy$In>j[?#mpNb~x%3zulCtome~eoFJ4TXrS#zRG[jJ62Q~,' );
define( 'SECURE_AUTH_KEY',  'umvkk`blxC d.BX{e$GvP%Ido9R-!@P;()?OzVx}mV7@bUL2c3$^Y$Xt#o4(&Lm.' );
define( 'LOGGED_IN_KEY',    'U~(^Xoo#Eo|[B}#Sv;@Hl,pOCZF$+avrs/u:Hz&:ehDhA EnemwD&b|I 8n5~d&a' );
define( 'NONCE_KEY',        'e#YwZOh1- if+[d3I=FThPEnc4eIK[=RR218S&5`HhL`J8O>:5qv$S;xjkjW jIs' );
define( 'AUTH_SALT',        'Z2uM-GyY%2PE%g4GXxeJ!iCh(<Xu0&cGQQ(_}uL;j!H]`f?prqWzOpMdO?.aS94H' );
define( 'SECURE_AUTH_SALT', 'nx!>!5sUZJ}iGpHC{y=ztZ~Zi`eJY]SlaY]xhnKTc2}Oe;CidJKQ,9[Xsf^c0^J1' );
define( 'LOGGED_IN_SALT',   '8Z8|x_!i_EBd3<{i5F}:e2eJP781WHbNQ/p :BY.W*@srJc?D`QYqy+Rif#|4!$y' );
define( 'NONCE_SALT',       '/|N/,?!wbTVS:6lq}UZf)x1${.o0BEAa/2.PR>ZhH(lInFdXv4x]W5zD+!#q^7QC' );

/**#@-*/

/**
 * WordPress database table prefix.
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
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', false );
define( 'WP_DEBUG_DISPLAY', true );
@ini_set( 'display_errors', 1 );

/* Add any custom values between this line and the "stop editing" line. */


// set_error_handler(function() {
//     error_log(print_r(debug_backtrace(), true));
//     return true;
// }, E_WARNING);

//define( 'WP_DEBUG', true );
// define( 'WP_DEBUG_LOG', true );
// define( 'WP_DEBUG_DISPLAY', false );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
