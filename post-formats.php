<?php
/**
 * Post Formats
 *
 * Functions relating to Post Formats
 *
 * @since	0.1.0
 *
 * @package mkdo\droplets
 */

/**
 * Remove Post Formats
 */
function mkdo_droplets_remove_post_formats() {
	remove_theme_support( 'post-formats' );
}
add_action( 'after_setup_theme', 'mkdo_droplets_remove_post_formats', 100 );
