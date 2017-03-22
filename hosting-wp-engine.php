<?php
/**
 * Hosting - WP Engine
 *
 * Some specific WP Engine Overrides.
 *
 * @since	0.1.0
 *
 * @package mkdo\droplets
 */

/**
 * Change the name of WP Engine in the menu.
 *
 * Also removes sub pages if the user isn't the one defined in the MKDO_DROPLETS_PERMITTED_USERNAME
 * constant.
 */
function mkdo_droplets_change_wp_engine_name() {

	if ( is_admin() ) {
		global $menu, $submenu;

		if ( strpos( $_SERVER['HTTP_HOST'], '.staging' ) !== false ) {
			remove_menu_page( 'wpengine-common' );
		} else {

			$current_user = wp_get_current_user();
			$user_name    = $current_user->user_login;

			// Change menu name and icon.
			if ( is_array( $menu ) ) {
				foreach ( $menu as &$m ) {
					if ( 'WP Engine' === $m[0] ) {
						$m[0] = 'Hosting';
						$m[6] = 'dashicons-admin-site';
					}
				}
			}

			// Change submenu name.
			if ( is_array( $submenu ) && isset( $submenu['wpengine-common'] ) ) {
				foreach ( $submenu['wpengine-common'] as &$m ) {
					if ( 'WP Engine' === $m[0] ) {
						$m[0] = 'Hosting';
					}
				}
			}

			// Remove Sub Pages.
			if ( MKDO_DROPLETS_PERMITTED_USERNAME !== $user_name ) {
				remove_submenu_page( 'wpengine-common', 'wpe-user-portal' );
				remove_submenu_page( 'wpengine-common', 'wpe-support-portal' );
			}
		}
	}
}
add_action( 'admin_menu', 'mkdo_droplets_change_wp_engine_name', 9999 );

/**
 * Override robots.txt setting on save
 *
 * Dont allow the staging site to have robots.txt enabled.
 */
function mkdo_droplets_override_robots_txt_save() {

	$allow_robots = '1';

	if ( strpos( $_SERVER['HTTP_HOST'], '.staging' ) !== false ) {
		$allow_robots = '0';
	}

	return $allow_robots;
}
add_filter( 'pre_option_blog_public', 'mkdo_droplets_override_robots_txt_save' );
