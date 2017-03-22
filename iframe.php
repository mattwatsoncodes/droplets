<?php
/**
 * iFrame
 *
 * Functions relating to iFrames
 *
 * @since	0.1.0
 *
 * @package mkdo\droplets
 */

/**
 * Make iFrames in page/post content more WCAG compliant
 *
 * @param  string $content Page/post content.
 * @return string
 */
function mkdo_droplets_iframe_wcag_compliance( $content ) {

	// Find all iFrames in the content.
	$pattern = '~<iframe.*</iframe>~';
	preg_match_all( $pattern, $content, $matches );

	// Loop through our matches.
	foreach ( $matches[0] as $match ) {

		// Match the name and src attributes.
		$pattern_new = "/name=['\"](?'name'.+?)['\"]|src=['\"](?'src'.+?)['\"]/";
		preg_match_all( $pattern_new, $match, $matches_new );

		// Filter empty strings from the array and assign variables to the named keys.
		$attribs = array_filter( array_map( 'array_filter', $matches_new ) );
		$src     = $attribs['src'];
		$name    = ( in_array( 'name', $attribs, true ) ? $attribs['name'] : null );

		// Re-index the 'src' array.
		if ( $src ) {
			$src = array_values( $src );

			// If the src is empty or contains about:blank, set it to false.
			if ( '' === $src[0] || 'about:blank' === $src[0] ) {
				$src = false;
			}
		}

		// Re-index the 'name' array.
		if ( $name ) {
			$name = array_values( $name );
		}

		if ( $src ) {
			// If we have the src, fetch the domain and use it.
			$src_pattern = '/^.*\/\/(.+?)[\/]/';
			preg_match( $src_pattern, $src[0], $src_match );

			$domain = $src_match[1];
			$title = "title='Embedded content from " . $domain . "'";
		} else {
			// Failing that, we'll just use the name for the title.
			$title = "title='" . $name[0] . "'";
		}

		// Add our title attribute - not having a title is against WCAG.
		$title_pattern  = '/<iframe/';
		$title_modified = preg_replace( $title_pattern, '<iframe ' . $title, $match );

		// Strip frameborder attribute - this is not valid as per WCAG.
		$frameborder_pattern  = "/(frameborder=['|\"].+?['|\"])/";
		$frameborder_modified = preg_replace( $frameborder_pattern, '', $title_modified );

		// Remove width attribute - this is not valid as per WCAG.
		$width_pattern  = "/(width=['|\"](.+?)['|\"])/";
		$width_modified = preg_replace( $width_pattern, '', $frameborder_modified );

		// Remove height attribute - this is not valid as per WCAG.
		$height_pattern  = "/(height=['|\"](.+?)['|\"])/";
		$height_modified = preg_replace( $height_pattern, '', $width_modified );

		// Update the content.
		$content  = str_replace( $match, $height_modified, $content );
	}

	return $content;
}
add_filter( 'the_content', 'mkdo_droplets_iframe_wcag_compliance', 20 );
