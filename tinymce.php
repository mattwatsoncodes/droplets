<?php
/**
 * TinyMCE
 *
 * Functions to clean up TinyMCE.
 *
 * @since	0.1.0
 *
 * @package mkdo\droplets
 */

/**
 * Define the elements that can be selected via the TinyMCE dropdown.
 *
 * @param  array $settings An array of TinyMCE settings.
 * @return array           The modified TinyMCE settings
 */
function mkdo_droplets_tiny_mce_block_formats( $settings ) {

	// Add only the block format elements you want to show in dropdown.
	$settings['block_formats'] = 'Paragraph=p;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6;Address=address;Pre=pre';

	return $settings;
}
add_filter( 'tiny_mce_before_init', 'mkdo_droplets_tiny_mce_block_formats' );

/**
 * Remove the Style Select dropdown from TinyMCE
 *
 * Styles are part of the theme, so remove this to stop things getting messy.
 *
 * @param  array $buttons An array of TinyMCE buttons.
 * @return array          The modified TinyMCE buttons
 */
function mkdo_droplets_tiny_mce_editor_buttons( $buttons ) {

	// Remove the style dropdown from TinyMCE.
	array_unshift( $buttons, 'styleselect' );

	return $buttons;
}
add_filter( 'mce_buttons_2', 'mkdo_droplets_tiny_mce_editor_buttons' );
