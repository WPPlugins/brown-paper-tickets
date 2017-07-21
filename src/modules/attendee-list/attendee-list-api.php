<?php

namespace BrownPaperTickets\Modules\AttendeeList;

use BrownPaperTickets\BptWordpress as Utilities;

require_once( Utilities::plugin_root_dir() . 'src/modules/bpt-module-api.php' );

class Api extends \BrownPaperTickets\Modules\ModuleApi {

	public function get_events()
	{
		$api = new \BrownPaperTickets\APIv2\EventInfo( $this->dev_id );

		return $api->getEvents( $this->client_id, null, true, true );
	}

	public function get_attendees($event = null) {
		$api = new \BrownPaperTickets\APIv2\SalesInfo( $this->dev_id );

		return $api->getOrders( $this->client_id, $event );
	}
}
