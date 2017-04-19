<?php
/**
 * Yoast WPML Sitemap Per Trainslation
 *
 * If using WPML and Yoast SEO, create a sitemap per translation.
 *
 * @since	0.1.0
 *
 * @package mkdo\droplets
 */

/**
 * Yoast SEO Posts Join
 *
 * Create a sitemap per page in WPML.
 *
 * @param  mixed  $join false or a join string.
 * @param  string $type Post Type.
 * @return mixed        false or a join string
 */
function mkdo_droplets_yoast_wpml_sitemap_per_translation( $join, $type ) {
	global $wpdb, $sitepress;
	if ( isset( $sitepress ) ) {
		$lang = $sitepress->get_current_language();
		return ' JOIN ' . $wpdb->prefix . "icl_translations ON element_id = ID AND element_type = 'post_$type' AND language_code = '$lang'";
	}
	return $join;
}
add_filter( 'wpseo_posts_join', 'mkdo_droplets_yoast_wpml_sitemap_per_translation', 10, 2 );
