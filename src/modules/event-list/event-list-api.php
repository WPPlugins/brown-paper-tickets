<?php

namespace BrownPaperTickets\Modules\EventList;

use BrownPaperTickets\BptWordpress as Utilities;

require_once( Utilities::plugin_root_dir() . 'src/modules/bpt-module-api.php' );

class Api extends \BrownPaperTickets\Modules\ModuleApi {

	public function get_events( $client_id = null, $event_id = null ) {

		if ( ! $client_id ) {
			$client_id = $this->client_id;
		}

		if ( ! $event_id ) {
			$event_id = null;
		}

		/**
		 * Get Event List Setting Options
		 */
		$show_non_live_events = get_option( '_bpt_show_non_live_events' );
		$show_dates           = get_option( '_bpt_show_dates' );
		$show_prices          = get_option( '_bpt_show_prices' );
		$show_past_dates      = get_option( '_bpt_show_past_dates' );
		$show_sold_out_dates  = get_option( '_bpt_show_sold_out_dates' );
		$show_sold_out_prices = get_option( '_bpt_show_sold_out_prices' );

		$event_info = new \BrownPaperTickets\APIv2\EventInfo( $this->dev_id );

		if ( $event_id ) {
			$client_id = null;
			$event_id  = explode( ' ', $event_id );
			$events    = array();

			foreach ( $event_id as $id ) {
				$events[] = $event_info->getEvents( $client_id, $id, $show_dates, $show_prices );
			}

			foreach ( $events as $event ) {
				$event_list[] = $event[0];
			}
		}

		if ( ! $event_id ) {
			$event_list = $event_info->getEvents( $client_id, $event_id, $show_dates, $show_prices );
		}


		if ( isset( $event_list['error'] ) ) {
			$event_list;
		}

		if ( ! $event_id && $show_non_live_events === 'false' ) {
			$event_list = Utilities::remove_bad_events( $event_list );
		}

		$event_list = Utilities::unescape_description( $event_list );

		$event_list = Utilities::sort_prices( $event_list );

		if ( $show_dates === 'true' ) {

			$remove_past = true;

			if ( $show_past_dates === 'false' ) {
				$remove_past = false;
			}
			if ( ! $show_non_live_events ) {
				$event_list = Utilities::remove_bad_dates( $event_list, true, $remove_past );
			}
		}

		if ( $show_prices === 'true' && $show_sold_out_prices === 'false' ) {
			$event_list = Utilities::remove_bad_prices( $event_list );
		}
		return $event_list;
	}
}
