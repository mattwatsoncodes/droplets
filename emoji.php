<?php
/**
 * Emoji
 *
 * Functions relating to Emoji
 *
 * @since	0.1.0
 *
 * @package mkdo\droplets
 */

/**
 *  Disable WP Emoji
 */
function mkdo_droplets_disable_emoji() {

	// Remove emoji scripts.
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );

	// Remove emoji styles.
	remove_action( 'wp_print_styles', 'print_emoji_styles' );

	// Remove emoji from email.
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

	// Remove emoji from feeds.
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );

	// Remove TinyMCE emojis.
	add_filter( 'tiny_mce_plugins', 'mkdo_droplets_disable_emoji_tinymce' );
}
add_action( 'init', 'mkdo_droplets_disable_emoji' );

/**
 * Remove TinyMCE Emoji
 *
 * @param array $plugins Array of TinyMCE plugins.
 * @return array         The modified plugin array.
 */
function mkdo_droplets_disable_emoji_tinymce( $plugins ) {

	// Make sure that the array is an array.
	if ( ! is_array( $plugins ) ) {
		$plugins = array();
	}

	// Remove the emoji plugin from the array.
	return array_diff( $plugins, array( 'wpemoji' ) );
}
