<?php
/**
 * Yoast WPML Alternate Links to Site Map
 *
 * If using WPML and Yoast SEO, add alternate links to the sitemap.
 *
 * @since	0.1.0
 *
 * @package mkdo\droplets
 */

/**
 * Filter URL entry before it gets added to the sitemap.
 *
 * We are using this to calculate the xhtml:link sitemap parts for later use
 *
 * @param array  $url  Array of URL parts.
 * @param string $type URL type.
 * @param object $post Post Object.
 * @return array       The modified URL parts
 */
function mkdo_droplets_yoast_wpml_alternate_links_to_site_map_setup( $url, $type, $post ) {

	$url['id']   = 0;
	$url['type'] = 'post';

	if ( 'post' === $type ) {
		$url['id']    = $post->ID;
		$url['type']  = $post->post_type;
	} else {
		$url['id']    = $post->term_id;
		$url['type']  = $post->taxonomy;
	}

	// If the relevent WPML function exists.
	if ( function_exists( 'icl_object_id' ) ) {
		$url['wpml']               = array();
		$url['wpml']['xhtml:link'] = array();
		$langs                     = icl_get_languages(); // Already checked WPML so assume this exists.

		// Loop through the site langauges.
		foreach ( $langs as $lang ) {

			// Get the langauge ID.
			$id = icl_object_id( $url['id'], $url['type'], false, $lang['code'] );

			// If there is a translation.
			if ( ! empty( $id ) ) {
				$url['wpml'][ $lang['code'] ]['id'] = icl_object_id( $url['id'], $url['type'], false, $lang['code'] );

				if ( (int) $id === (int) $url['id'] ) {
					// If the translation is the current post.
					$url['wpml'][ $lang['code'] ]['current_lang'] = true;
				} else {

					$link = get_the_permalink( $id );

					if ( 'post' !== $type ) {
						$link = get_term_link( $id );
					}

					// The translation is an alternative, create the xhtml:link.
					$xhtml_link  = '<xhtml:link ';
					$xhtml_link .= 'rel="alternate" ' . "\n\t\t\t";
					$xhtml_link .= 'hreflang="' . $lang['code'] . '" ' . "\n\t\t\t";
					$xhtml_link .= 'href="' . $link . '" ' . "\n\t\t";
					$xhtml_link .= '/>';

					$url['wpml']['xhtml:link'][] = $xhtml_link;
				}
			}
		}
	}
	return $url;
}
add_filter( 'wpseo_sitemap_entry', 'mkdo_droplets_yoast_wpml_alternate_links_to_site_map_setup', 100, 3 );

/**
 * Hook into the sitemap links
 *
 * We use the xhtml:link param we defined earlier to add this to the sitemap.
 *
 * @param string $output The sitemap URL.
 * @param array  $url    Array of URL parts.
 * @return string        The transformed sitemap URL
 */
function mkdo_droplets_yoast_wpml_alternate_links_to_site_map( $output, $url ) {

	// If we have some xhtml:link's.
	if (
		isset( $url['wpml'] ) &&
		isset( $url['wpml']['xhtml:link'] ) &&
		is_array( $url['wpml']['xhtml:link'] ) &&
		! empty( $url['wpml']['xhtml:link'] )
	) {
		$insert = '';

		// Loop through the links and append them to the $insert string.
		foreach ( $url['wpml']['xhtml:link'] as $key => $xhtml_link ) {
			if ( 0 === $key ) {
				$insert .= "\t" . $xhtml_link;
			} else {
				$insert .= "\n\t\t" . $xhtml_link;
			}
		}

		// Replace the last part of the URL with the new links.
		$output = str_replace( '</url>', $insert . "\n\t" . '</url>', $output );
	}
	return $output;
}
add_filter( 'wpseo_sitemap_url', 'mkdo_droplets_yoast_wpml_alternate_links_to_site_map', 100, 2 );
