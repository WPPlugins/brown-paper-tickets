<?php

namespace BrownPaperTickets\Modules\Calendar;

require_once( 'calendar-api.php' );

use BrownPaperTickets\BptWordpress as Utilities;

class Ajax {

	private static $nonce_title = 'bpt-calendar-widget-nonce';
	/**
	 * Returns an array of a specific producers events formatted for the CLNDR.
	 * @param  string  $client_id The Client ID of the producer you wish
	 *                            to get the events of.
	 * @param  boolean $dates     Get prices? Default is false.
	 * @param  boolean $prices    Get Prices? Default is false.
	 * @return json               The JSON string of the event Data.
	 */
	public function get_events() {
		$get = filter_input_array( INPUT_GET, FILTER_SANITIZE_ENCODED );
		$nonce           = $get['nonce'];
		$widget_instance = $get['widgetID'];

		Utilities::check_nonce( $nonce, 'bpt-calendar-widget-nonce' );

		if ( isset( $get['clientID'] ) ) {
			$client_id = $get['clientID'];
		} else {
			$client_id = get_option( '_bpt_client_id' );
		}

		if ( ! $client_id ) {
			wp_send_json( array( 'success' => false, 'error' => 'No client ID.' ) );
		}

		if ( ! $widget_instance ) {
			wp_send_json( array( 'success' => false, 'error' => 'No widget ID.' ) );
		}

		if ( ! Utilities::cache_enabled() ) {

			$events = new Api;

			wp_send_json( $events->get_events( $client_id ) );
		}

		if ( get_transient( '_bpt_calendar_events_' . $widget_instance ) === false && Utilities::cache_enabled() ) {

			$events = new Api;

			set_transient( '_bpt_calendar_events_' . $widget_instance, $events->get_events( $client_id ), Utilities::cache_time() );

		}

		wp_send_json( get_transient( '_bpt_calendar_events_' . $widget_instance ) );
	}
}
