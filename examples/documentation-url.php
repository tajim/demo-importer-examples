<?php
/**
 * Tell the "Demo Importer" plugin that your theme offer an online documentation
 * - link to this documentation will be presented to your user at the end of import process
 *
 * @link https://wordpress.org/plugins/demo-importer/
 * @package demo-importer-example
 */

/**
 * Filter function
 */
function mytheme_documentation_url() {
	return 'https://docs.example.com/my-theme-documentation/';
}

add_filter( 'wdpdi_documentation_url', 'mytheme_documentation_url' );
