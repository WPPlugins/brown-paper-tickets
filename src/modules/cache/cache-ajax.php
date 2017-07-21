<?php

namespace BrownPaperTickets\Modules\General;

use BrownPaperTickets\BptWordpress as Utilities;

class Ajax
{

	public static function delete_cache() {

		global $wpdb;

		$cached_data = $wpdb->get_results(
			'SELECT *
			FROM `wp_options`
			WHERE `option_name` LIKE \'%_transient__bpt_%\'',
			OBJECT
		);

		if ( empty( $cached_data ) ) {
			$result = array(
				'success' => false,
				'status' => 'success',
				'message' => 'No cached data to delete.',
			);

			wp_send_json( $result );
		}

		if ( ! empty( $cached_data ) ) {

			foreach ( $cached_data as $cache ) {

				$option_name = $cache->option_name;

				$option_name = str_replace( '_transient_', '', $option_name );

				delete_transient( $option_name );
			}
		}

		$result = array(
			'status' => 'success',
			'message' => 'Cached data has been deleted.',
		);

		wp_send_json( $result );
	}
}
