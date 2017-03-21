<?php
/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function resonance_body_classes( $classes ) {
	global $post;

	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	if ( is_singular() ) {
		$classes[] = 'slug-' . $post->post_name;
	}

	return $classes;
}
add_filter( 'body_class', 'resonance_body_classes' );

/**
 * Remove empty <p> tags.
 * @return string
 * @see https://gist.github.com/Fantikerz/5557617
 */
function remove_empty_p( $content ) {
	$content = force_balance_tags( $content );
	$return = preg_replace( '#<p>\s*+(<br\s*/*>)?\s*</p>#i', '', $content );
	$return = preg_replace( '~\s?<p>(\s|&nbsp;)+</p>\s?~', '', $return );

	return $return;
}
add_filter( 'the_content', 'remove_empty_p', 20, 1 );

/**
 * Remove <p> tags around images.
 *
 * @param string $content Page/post content.
 * @return string
 */
function resonance_remove_image_ptags( $content ) {
	return preg_replace( '/<p>\s*(<a .*>)?\s*(<img .* \/>|<iframe .*>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content );
}
add_filter( 'the_content' , 'resonance_remove_image_ptags' );

/**
 * Wrap iFrames in a responsive embed wrapper.
 * @param string $content Page/post content.
 * @return string
 */
function resonance_post_embed_wrapper( $content ) {

	$pattern = '~<iframe.*</iframe>|<embed.*</embed>~';
	preg_match_all( $pattern, $content, $matches );

	foreach ( $matches[0] as $match ) {
		$wrapped_frame = '<div class="responsive-embed">' . $match . '</div>';

		$content = str_replace( $match, $wrapped_frame, $content );
	}

	return $content;
}
add_filter( 'the_content', 'resonance_post_embed_wrapper' );
