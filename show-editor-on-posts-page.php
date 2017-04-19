<?php
/**
 * Show Editor on Posts Page
 *
 * In WordPress 4.2 the editor was removed on whichever page was assigned to
 * show Latest Posts. The following function below will re-add the editor
 * and remove the notification:
 *
 * @see https://wordpress.stackexchange.com/questions/193755/show-default-editor-on-blog-page-administration-panel
 * @since	0.1.0
 *
 * @package mkdo\droplets
 */

/**
 * Add the wp-editor back into WordPress after it was removed in 4.2.2.
 *
 * @param object $post The post object.
 */
function mkdo_droplets_show_editor_on_posts_page( $post ) {

	if ( isset( $post ) && $post->ID !== get_option('page_for_posts') ) {
		return;
	}

	remove_action( 'edit_form_after_title', '_wp_posts_page_notice' );
	add_post_type_support( 'page', 'editor' );
}
add_action( 'edit_form_after_title', 'mkdo_droplets_show_editor_on_posts_page', 0 );
