<?php
/**
 * WPML Bing Compatible Header
 *
 * Add Bing Compatible language links
 *
 * @since	0.1.0
 *
 * @package mkdo\droplets
 */

/**
 * Add Bing Compatible language links
 */
function mkdo_droplets_wpml_bing_compatible_header() {
	global $post;

	if ( function_exists( 'icl_get_languages' ) ) {
		$langs = icl_get_languages();
		foreach ( $langs as $lang ) {
			// Get the langauge ID.
			$id = icl_object_id( $post->ID, $post->post_type, false, $lang['code'] );

			// If there is a translation.
			if ( ! empty( $id ) && (int) $id === (int) $post->ID ) {
				echo '<meta http-equiv="content-language" content="' . esc_attr( $lang['code'] ) . '"/>' . "\n";
			}
		}
	}
}
add_filter( 'wp_head', 'mkdo_droplets_wpml_bing_compatible_header', 0 );
