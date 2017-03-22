<?php
/**
 * Debug
 *
 * Enable debug on local and staging environments.
 *
 * @since	0.1.0
 *
 * @package mkdo\droplets
 */

// Debug.
if ( 'dev' !== MKDO_DROPLETS_TLD ) {

	// Display PHP errors.
	@ini_set( 'display_errors', 1 );

	// Enable WP Debugging.
	if ( ! defined( 'WP_DEBUG' ) ) {
		define( 'WP_DEBUG', true );
	}

	// Enable WP Error Log.
	if ( ! defined( 'WP_DEBUG_LOG' ) ) {
		define( 'WP_DEBUG_LOG', true );
	}

	// Display Debug Errors.
	if ( ! defined( 'WP_DEBUG_DISPLAY' ) ) {
		define( 'WP_DEBUG_DISPLAY', true );
	}

	// Use dev core scripts.
	if ( ! defined( 'SCRIPT_DEBUG' ) ) {
		define( 'SCRIPT_DEBUG', true );
	}

	// Save SQL Querys (view using $wpdb->queries).
	if ( ! defined( 'SAVEQUERIES' ) ) {
		define( 'SAVEQUERIES', true );
	}
}
