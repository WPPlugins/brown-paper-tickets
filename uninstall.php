<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

$bpt_options = $wpdb->get_results(
	'SELECT *
	FROM `wp_options`
	WHERE `option_name` LIKE \'%_bpt_%\'',
	OBJECT
);

if ( ! empty( $bpt_options ) ) {

	foreach ( $bpt_options as $bpt_option ) {
		
		$option_name = $bpt_option->option_name;

		delete_option( $option_name );
	}
}