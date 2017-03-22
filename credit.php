<?php
/**
 * Credit
 *
 * Add a hidden credit to the footer, so we know if the site is built using Droplets.
 *
 * @since	0.1.0
 *
 * @package mkdo\droplets
 */

/**
 * Add a credit to the footer.
 */
function mkdo_droplets_add_credit_to_footer() {

		echo '<!--';
		echo 'Built using Droplets by <a href="http://makedo.net" target="_blank">Make Do</a>.';
		echo '-->';
}
add_filter( 'wp_footer', 'mkdo_droplets_add_credit_to_footer', 9999 );
