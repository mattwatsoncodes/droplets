<?php
/**
 * Add /blog to Post Slug
 *
 * Rename the post slug to include /blog
 *
 * @since	0.1.0
 *
 * @package mkdo\droplets
 */

/**
 * Remove the 'post' post type
 */
function mkdo_droplets_remove_default_post_type() {
	remove_menu_page( 'edit.php' );
}
add_action( 'admin_menu', 'mkdo_droplets_remove_default_post_type' );

/**
 * Re-add the 'post' post type
 */
function mkdo_droplets_add_blog_to_post_slug() {

	register_post_type(
		'post',
		array(
		    'labels' => array(
		        'name_admin_bar' => _x( 'Post', 'add new on admin bar' ),
		    ),
			'menu_position'   => 5,
		    'public'          => true,
		    '_builtin'        => false,
		    '_edit_link'      => 'post.php?post=%d',
		    'capability_type' => 'post',
		    'map_meta_cap'    => true,
		    'hierarchical'    => false,
		    'rewrite'         => array( 'slug' => 'blog' ),
		    'query_var'       => false,
		    'supports'        => array(
				'title',
				'editor',
				'author',
				'thumbnail',
				'excerpt',
				'trackbacks',
				'custom-fields',
				'comments',
				'revisions',
				'post-formats',
			),
		)
	);
}
add_action( 'init', 'mkdo_droplets_add_blog_to_post_slug', 1 );
