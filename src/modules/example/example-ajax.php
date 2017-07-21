<?php

namespace BrownPaperTickets\Modules\Account;

require( 'account-api.php' );

use BrownPaperTickets\BptWordpress as Utilities;
use BrownPaperTickets\BPTFeed;

class Ajax
{
	private static $nonce_title = 'bpt-admin-nonce';

	public static function get_account() {
		$get = filter_input_array( INPUT_GET, FILTER_SANITIZE_ENCODED );
		Utilities::check_nonce( $get['nonce'], self::$nonce_title );

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


	/**
	 * Account Test Setup
	 */

	public static function test_account() {
		$post = filter_input_array( INPUT_POST, FILTER_SANITIZE_ENCODED );
		Utilities::check_nonce( $post['nonce'], 'bpt-account' );

		$dev_id    = ( isset( $post['devID'] ) ? htmlentities( $post['devID'] ) : false );
		$client_id = ( isset( $post['clientID'] ) ? htmlentities( $post['clientID'] ) : false );

		if ( ! $dev_id ) {
			wp_send_json( array( 'success' => false, 'error' => 'No developer ID.' ) );
		}

		if ( ! $client_id ) {
			wp_send_json( array( 'success' => false, 'error' => 'No client ID.' ) );
		}

		$account = new Api;

		wp_send_json( $account->test_account( $dev_id, $client_id ) );
	}

}
