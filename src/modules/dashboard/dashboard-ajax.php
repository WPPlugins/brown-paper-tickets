<?php

namespace BrownPaperTickets\Modules\Dashboard;

require( 'dashboard-api.php' );

use BrownPaperTickets\BptWordpress as Utilities;
use BrownPaperTickets\BPTFeed;

class Ajax
{
	private $nonce_title = 'bpt-dashboard';

	public function get_account() {
		$get = filter_input_array( INPUT_GET, FILTER_SANITIZE_ENCODED );
		Utilities::check_nonce( $get['nonce'], $this->nonce_title );

		$account_info = false;

		if ( Utilities::cache_enabled() ) {

			if ( ! get_transient( '_bpt_user_account_info' ) ) {
				$account = new Api;
				$account_info = $account->get_account();
				set_transient( '_bpt_user_account_info', $account_info, 0 );
			}

			$account_info = get_transient( '_bpt_user_account_info' );

		} else {
			$account = new Api;
			$account_info = $account->get_account();
		}

		wp_send_json( $account_info );
	}

	public function get_all_options() {
		$get = filter_input_array( INPUT_GET, FILTER_SANITIZE_ENCODED );

		Utilities::check_nonce( $get['nonce'], $this->nonce_title );

		if ( ! Utilities::is_user_an_admin() ) {
			wp_send_json( array( 'success' => false, 'message' => 'Not authorized.' ) );
		}

		global $wpdb;

		$options = $wpdb->get_results(
			'SELECT *
			FROM `wp_options`
			WHERE `option_name` LIKE \'%_bpt_%\'',
			OBJECT
		);

		$results = array();

		foreach ( $options as &$option ) {
			$option_name = str_replace( '_bpt_', '', $option->option_name );
			$value = $option->option_value;

			if ( is_serialized( $value ) ) {
				$value = unserialize( $value );
			}

			$results[ $option_name ] = $value;
		}

		wp_send_json( $results );
	}
}
