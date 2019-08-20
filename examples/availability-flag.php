<?php
/**
 * Tell the "Demo Importer" plugin that your theme contain demo data to be imported
 *
 * @link https://wordpress.org/plugins/demo-importer/
 * @package demo-importer-example
 */

add_filter( 'wdpdi_demo_data_available', '__return_true' );
