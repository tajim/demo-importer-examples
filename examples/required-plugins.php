<?php
/**
 * Return list of all plugins required by your theme
 *
 * @link https://wordpress.org/plugins/demo-importer/
 * @package demo-importer-example
 */

/**
 * Filter function
 *
 * @param array $plugins Plugins required by others (usually empty array).
 */
function mytheme_required_plugins( $plugins ) {

	return array_merge(
		$plugins,
		array(
			'contact-form-7',
			'advanced-custom-fields',
			'w3-total-cache',
		)
	);
}

add_filter( 'wdpdi_required_plugins', 'mytheme_required_plugins' );
