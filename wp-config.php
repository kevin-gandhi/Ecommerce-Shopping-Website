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
define( 'DB_NAME', 'amazebox' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         's0e!&$/.F`b|yy9cdHZVXT#lVQxshIvTj[uxRLm<i OJ|61W3bwHVXONWB7J;nXi' );
define( 'SECURE_AUTH_KEY',  '/AW1,IRMH_$,h`F#<wT.ndFqRNC{643SpKBdm0SFU18PH92~Vq#G-jmh.h=<ex7K' );
define( 'LOGGED_IN_KEY',    'mZHufvUf>`s^M1*~Y<NBQ}9f$<G|d!J06=$yGh<889CeWJJ?b%|4`4zR_?cD?`CF' );
define( 'NONCE_KEY',        's[Y%XW)FLvl?|ld(msK0hNx?U`nDy@!+EH{: __%9xB=U3DCOfbW*&YRn5n:nT*x' );
define( 'AUTH_SALT',        'INpWFG<W KuzdIa5^vi^mu2uw;p)&(Yi~`J9!/?)VzLVQkg}r;yruHj-3PnnKt+/' );
define( 'SECURE_AUTH_SALT', 'E#Vc~DTHK#iL3$~G)U<c!|l)PQo+@P)>QO&#q2jGWJ:h+h.Rg~y`(L $+Lz~d:a/' );
define( 'LOGGED_IN_SALT',   'v;ed;BIryEF%3n[k{AslMcealTwR`+r0j?21:CJsiCLVO19+^4|2MI[@sZ}6j: I' );
define( 'NONCE_SALT',       '<0=uTJ0q~Yg]{ikG<{n_IS2)xuF7MJ6P&$@|v&pmia^g]m&ahD%Ds[It`p)?t|$U' );

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
