<?php
/**
 * Remove Admin Bar
 *
 * Prevent non-administrators from viewing the admin bar.
 *
 * @since	0.1.0
 *
 * @package mkdo\droplets
 */

/**
 * Prevent non-administrators from viewing the admin bar.
 */
function mkdo_droplets_remove_admin_bar() {
	if ( ! current_user_can( 'administrator' ) && !is_admin() ) {
		show_admin_bar( false );
	}
}

add_action( 'after_setup_theme', 'mkdo_droplets_remove_admin_bar' );
