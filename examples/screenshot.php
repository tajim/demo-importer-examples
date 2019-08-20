<?php
/**
 * Tell the "Demo Importer" plugin that you want to use custom screenshot
 * of your theme in importer popup.
 *
 * @link https://wordpress.org/plugins/demo-importer/
 * @package demo-importer-example
 */

/**
 * Filter function
 */
function mytheme_screenshot_url() {
	return get_template_directory_uri() . '/screenshot.jpg';
}

add_filter( 'wdpdi_screenshot', 'mytheme_screenshot_url' );
