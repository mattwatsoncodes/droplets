<?php
/**
 * Post Content Clean
 *
 * Automatically cleans up content on post save, to get rid of any non-html nasties.
 * Lovingly borrowed the idea from https://en-gb.wordpress.org/plugins/safe-paste/.
 *
 * @since	0.1.0
 *
 * @package mkdo\droplets
 */

/**
 * Clean post data on save
 *
 * @param  array $data    An array of slashed post data.
 * @param  array $postarr An array of sanitized, but otherwise unmodified post data.
 * @return array          The modified post data
 */
function mkdo_droplets_sanitize_post_content( $data, $postarr ) {

	// Clean up the post content.
	$data['post_content'] = mkdo_droplets_sanitize_html( $data['post_content'] );

	return $data;
}
add_filter( 'wp_insert_post_data' , 'mkdo_droplets_sanitize_post_content', 99, 2 );

/**
 * Clean post meta on save
 *
 * @param  int $post_id The post ID.
 */
function mkdo_droplets_sanitize_post_meta( $post_id ) {

	// Remove this hook, otherwise we will cause an infinate loop.
	remove_action( 'wp_insert_post', 'mkdo_droplets_sanitize_post_meta' );

	/**
	 * Example 1: Clean Meta
	 *
	 * To clean meta we need to get it, clean it and save it again.
	 *
	 * // Get the post meta.
 	 * $meta_example = get_post_meta( $post_id, 'meta_key', true );
 	 *
 	 * // Sanitize the items HTML key.
 	 * $meta_example = mkdo_droplets_sanitize_html( $meta_example );
 	 *
 	 * // Save the meta.
 	 * update_post_meta( $post_id, 'meta_key', $meta_example );
 	 *
 	 * END - Example 1.
	 */

	/**
	 * Example 2: Clean Array of Meta
	 *
	 * Sometmes meta is stored as an array, we will need to get the array
	 * and loop through the key that contains the HTML content.
	 *
	 * // Get the post meta (this should be an array).
 	 * $meta_array_example = get_post_meta( $post_id, 'meta_array_key', true );
 	 *
 	 * // Make sure this is an array.
 	 * if ( ! is_array( $meta_array_example ) ) {
 	 *     $meta_array_example = array();
 	 * }
 	 *
 	 * // Loop through the array, and sanitize it.
 	 * foreach ( $meta_array_example as &$item ) {
 	 *    // Sanitize the items HTML key.
 	 *    $item['content'] = mkdo_droplets_sanitize_html( $item['content'] );
 	 * }
 	 *
 	 * // After the clean update the post meta.
 	 * update_post_meta( $post_id, 'meta_array_key', $meta_array_example );
 	 *
 	 * END - Example 2.
	 */

	// Add the hook back in.
	add_action( 'wp_insert_post', 'mkdo_droplets_sanitize_post_meta' );
}
add_action( 'wp_insert_post', 'mkdo_droplets_sanitize_post_meta' );

/**
 * HTML Clean
 *
 * Function to clean up HTML.
 *
 * @param  string $content Content to be cleaned.
 * @return string          Cleaned content.
 */
function mkdo_droplets_sanitize_html( $content ) {

	// The tags and attributes that are allowed.
	$allowed_tags = array(
		'p' => array(),
		'a' => array(
			'href'   => array(),
			'title'  => array(),
			'target' => array(),
			'rel'    => array(),
		),
		'img' => array(
			'src'    => array(),
			'alt'    => array(),
			'class'  => array(),
			'width'  => array(),
			'height' => array(),
		),
		'h2'         => array(),
		'h3'         => array(),
		'h4'         => array(),
		'h5'         => array(),
		'h6'         => array(),
		'hr'         => array(),
		'blockquote' => array(),
		'cite'       => array(),
		'ol'         => array(),
		'ul'         => array(),
		'li'         => array(),
		'em'         => array(),
		'strong'     => array(),
		'del'        => array(),
		'div'        => array(
			'id'    => array(),
			'class' => array(),
		),
	);

	// Allowed protocols.
	$allowed_protocols = array( 'http', 'https', 'mailto', 'tel' );

	// Clean up the HTML tags.
	$content = wp_kses( $content, $allowed_tags, $allowed_protocols );

	// Replace &nbsp; with real spaces.
	$content = preg_replace( '/&nbsp;/i', ' ', $content );

	return $content;
}

/**
 * Remove empty <p> tags.
 *
 * @see https://gist.github.com/Fantikerz/5557617
 *
 * @param  string $content Content to be cleaned.
 * @return string
 */
function mkdo_droplets_remove_empty_p( $content ) {
	$content = force_balance_tags( $content );
	$return = preg_replace( '#<p>\s*+(<br\s*/*>)?\s*</p>#i', '', $content );
	$return = preg_replace( '~\s?<p>(\s|&nbsp;)+</p>\s?~', '', $return );

	return $return;
}
add_filter( 'the_content', 'mkdo_droplets_remove_empty_p', 20, 1 );

/**
 * Remove <p> tags around images.
 *
 * @param  string $content Content to be cleaned.
 * @return string
 */
function mkdo_droplets_remove_image_ptags( $content ) {
	return preg_replace( '/<p>\s*(<a .*>)?\s*(<img .* \/>|<iframe .*>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content );
}
add_filter( 'the_content' , 'mkdo_droplets_remove_image_ptags' );
