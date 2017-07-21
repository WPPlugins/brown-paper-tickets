<?php

namespace BrownPaperTickets\Modules\Account;

use BrownPaperTickets\BptWordpress as Utilities;

require_once( Utilities::plugin_root_dir() . 'src/modules/bpt-module-api.php' );

class Api extends \BrownPaperTickets\Modules\ModuleApi {

	public function get_account() {

		$account_info = new \BrownPaperTickets\APIv2\AccountInfo( $this->dev_id );

		return $account_info->getAccount( $this->client_id );
	}


	/**
	 * Simple Get Account Call for testing that the settings are correct.
	 * $dev_id and $client_id must be passed to the function.
	 */

	public function test_account( $dev_id, $client_id ) {

		$account_info = new \BrownPaperTickets\APIv2\AccountInfo( $dev_id );
		$event_list   = new \BrownPaperTickets\APIv2\EventInfo( $dev_id );

		$response = array(
			'account' => $account_info->getAccount( $client_id ),
			'events'  => $event_list->getEvents( $client_id ),
		);

		if ( ! $response['events'] ) {
			$response['events'] = array();
		}

		return $response;
	}
}
