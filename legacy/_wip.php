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

/**
 *  Disable WP Emoji Icons.
 */
function resonance_disable_wp_emoji() {

	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );

	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );

	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );

	// Remove TinyMCE emojis.
	add_filter( 'tiny_mce_plugins', 'disable_emojicons_tinymce' );
}
add_action( 'init', 'resonance_disable_wp_emoji' );

function disable_emojicons_tinymce( $plugins ) {
	if ( is_array( $plugins ) ) {
		return array_diff( $plugins, array( 'wpemoji' ) );
	} else {
		return array();
	}
}

<?php
/**
 * Accessibility (WCAG) improvements.
 *
 * @package Hartley Botanic
 */

/**
 * Makes iFrames in page/post content more WCAG compliant.
 * @param  string $content Page/post content.
 * @return string
 */
function hartley_botanic_iframe_wcag_compliance( $content ) {

	// Find all iFrames in the content.
	$pattern = '~<iframe.*</iframe>~';
	preg_match_all( $pattern, $content, $matches );

	// Loop through our matches.
	foreach ( $matches[0] as $match ) {

		// Match the name and src attributes.
		$newPattern = "/name=['\"](?'name'.+?)['\"]|src=['\"](?'src'.+?)['\"]/";
		preg_match_all( $newPattern, $match, $newMatches );

		// Filter empty strings from the array and assign variables to the named keys.
		$attribs = array_filter( array_map( 'array_filter', $newMatches ) );
		$src     = $attribs['src'];
		$name    = ( in_array( 'name', $attribs ) ? $attribs['name'] : null );

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
add_filter( 'the_content', 'hartley_botanic_iframe_wcag_compliance', 20 );

function tpc_tiny_mce_remove_unused_formats( $init ) {
	// Add block format elements you want to show in dropdown
	$init['block_formats'] = 'Paragraph=p;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6;Address=address;Pre=pre';
	return $init;
}
add_filter( 'tiny_mce_before_init', 'tpc_tiny_mce_remove_unused_formats' );


function fb_mce_editor_buttons( $buttons ) {

    array_unshift( $buttons, 'styleselect' );
    return $buttons;
}
add_filter( 'mce_buttons_2', 'fb_mce_editor_buttons' );

/*
Plugin Name: Safe Paste by Make Do
Plugin URI: http://www.makedo.in
Description: Removes a lot of HTML tags from post and page content before inserting it to database. Preventing users to paste undesired HTML tags to post content.
Author: Make Do based on original by Samuel Aguilera (http://samuelaguilera.com)
Version: 0.1
Author URI: http://makedo.in
License: GPL2
*/


function mkdo_safe_paste( $data, $postarr ) {

	global $post;

	$clean_tags = array(
		'p' => array(),
		'a' => array( 'href' => array(), 'title' => array(), 'target' => array() ),
		'img' => array( 'src' => array(), 'alt' => array(), 'class' => array(), 'width' => array(), 'height' => array() ),
		//'h1' => array(),
		'h2' => array(),
		'h3' => array(),
		'h4' => array(),
		'h5' => array(),
		'h6' => array(),
		'hr' => array(),
		'blockquote' => array(),
		'cite' => array(),
		'ol' => array(),
		'ul' => array(),
		'li' => array(),
		'em' => array(),
		'strong' => array(),
		'del' => array(),
		'div' => array( 'id' => array(), 'class' => array() )
	);

	$allowed_protocols = array( 'http', 'https' , 'mailto' );

	// Doing the clean for HTML tags...
	$data['post_content'] = wp_kses( $data['post_content'], $clean_tags, $allowed_protocols );

	// Removes only &nbsp;
	$data['post_content'] = preg_replace( '/&nbsp;/i', '', $data['post_content'] );

	return $data; // return data to WP
}

add_filter( 'wp_insert_post_data' , 'mkdo_safe_paste', 99, 2 );

function mkdo_safe_paste_meta( $post_id ) {

  remove_action( 'wp_insert_post', 'mkdo_safe_paste_meta' );
  $cleanPost_tags = array(
    'p' => array(),
    'a' => array( 'href' => array(), 'title' => array() ),
    'img' => array( 'src' => array(), 'alt' => array(), 'class' => array(), 'width' => array(), 'height' => array() ),
    'h1' => array(),
    'h2' => array(),
    'h3' => array(),
    'h4' => array(),
    'h5' => array(),
    'h6' => array(),
    'blockquote' => array(),
    'cite' => array(),
    'ol' => array(),
    'ul' => array(),
    'li' => array(),
    'em' => array(),
    'strong' => array(),
    'del' => array() );

  $allowed_protocols = array( 'http', 'https' , 'mailto' );

  $carousel_items      =    get_post_meta( $post_id, '_mkdo_meta_carousel_group', FALSE );

  delete_post_meta( $post_id, '_mkdo_meta_carousel_group' );

  foreach ( $carousel_items as &$item ) {

    $item['_mkdo_meta_content'] = wp_kses( $item['_mkdo_meta_content'], $cleanPost_tags, $allowed_protocols );
    $item['_mkdo_meta_content'] = preg_replace( "/&nbsp;/i", "", $item['_mkdo_meta_content'] );

    add_post_meta( $post_id, '_mkdo_meta_carousel_group', $item, FALSE );
  }

  add_action( 'wp_insert_post', 'mkdo_safe_paste_meta' );
}

add_action( 'wp_insert_post', 'mkdo_safe_paste_meta' );
