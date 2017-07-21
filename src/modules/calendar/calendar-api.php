<?php

namespace BrownPaperTickets\Modules\Calendar;

use BrownPaperTickets\BptWordpress as Utilities;

require_once( Utilities::plugin_root_dir() . 'src/modules/bpt-module-api.php' );

class Api extends \BrownPaperTickets\Modules\ModuleApi {

	/**
	 * Returns an array of a specific producers events formatted for the CLNDR.
	 * @param  string  $client_id The Client ID of the producer you wish
	 *                            to get the events of.
	 * @param  boolean $dates     Get prices? Default is false.
	 * @param  boolean $prices    Get Prices? Default is false.
	 * @return json               The JSON string of the event Data.
	 */
	public function get_events( $client_id = null, $dates = true, $prices = false ) {

		$dev_id = get_option( '_bpt_dev_id' );
		$show_non_live_events = get_option( '_bpt_show_non_live_events' );

		if ( ! $this->dev_id ) {
			return array( 'success' => false, 'error' => 'Unable to fetch events.' );
		}

		if ( isset( $_POST['clientID'] ) &&  $_POST['clientID'] !== '' ) {
			$client_id = filter_var( $_POST['clientID'], FILTER_SANITIZE_NUMBER_INT );
		}

		$events = new \BrownPaperTickets\APIv2\EventInfo( $this->dev_id );

		$events = $events->getEvents( $client_id, null, $dates, $prices );
		$clndr_format = array();

		foreach ( $events as $event ) {

			if ( $event['live'] || $show_non_live_events === 'true' ) {
				foreach ( $event['dates'] as $date ) {
					if ( $date['live'] || $show_non_live_events === 'true' ) {

						$clndr_format[] = array(
							'eventID' => $event['id'],
							'dateID' => $date['id'],
							'date' => $date['dateStart'],
							'endDate' => $date['dateEnd'],
							'timeStart' => $date['timeStart'],
							'timeEnd' => $date['timeEnd'],
							'title' => $event['title'],
							'address1' => $event['address1'],
							'address2' => $event['address2'],
							'city' => $event['city'],
							'state' => $event['state'],
							'zip' => $event['zip'],
							'shortDescription' => $event['shortDescription'],
						);
					}
				}
			}
		}

		return $clndr_format;
	}
}
