<?php
/**
 * Ajax actions for the attendee list.
 * @package brown-paper-tickets
 */

namespace BrownPaperTickets\Modules\AttendeeList;

require_once( plugin_dir_path( __FILE__ ) . '../event-list/event-list-ajax.php' );
require_once( 'attendee-list-api.php' );

use \BrownPaperTickets\BptWordpress as Utilities;
use \BrownPaperTickets\Modules\EventList\Ajax as EventListAjax;

/**
 * All the ajax functions for the attendee list.
 */
class Ajax {

	/**
	 * The nonce title...
	 * @var string
	 */
	private $nonce_title = 'bpt-attendee-list-nonce';

	/**
	 * Title for the attendee list event transient.
	 * @var string
	 */
	private $event_transient_title = '_bpt_attendee_list_events';

	/**
	 * The title will be combined with the event id when saving the transient.
	 * @var string
	 */
	private $attendees_transient_title = '_bpt_attendee_list_attendees';

	/**
	 * Get all the events, cache it and spit it out and die as JSON.
	 */
	public function get_events() {
		$api = new Api();
		$events = get_transient( $this->event_transient_title );
		$success = false;
		$get = filter_input_array( INPUT_GET, FILTER_SANITIZE_ENCODED );

		Utilities::check_nonce( $get['nonce'], $this->nonce_title );

		if ( ! $events ) {
			$events = $api->get_events();

			set_transient(
				$this->event_transient_title,
				$events,
				Utilities::cache_time()
			);
		}

		if ( $events ) {
			$success = true;
		}

		wp_send_json(
			array(
				'events' => $events,
				'success' => $success,
			)
		);
	}

	/**
	 * Get a list of attendees for a given event and/or date, cache it and spit
	 * out as JSON and die.
	 */
	public function get_attendees() {
		$api = new Api();
		$get = filter_input_array( INPUT_GET, FILTER_SANITIZE_ENCODED );
		$success = false;
		$event = null;
		$attendees = null;

		Utilities::check_nonce( $get['nonce'], $this->nonce_title );

		if ( isset( $get['event'] ) ) {
			$event = $get['event'];
		}

		if ( $event ) {
			$attendees = get_transient(
				$this->attendees_transient_title . '_' . $event
			);

			if ( ! $attendees ) {
				$attendees = $api->get_attendees( $get['event'] );

				set_transient(
					$this->attendees_transient_title . '_' . $get['event'],
					$attendees,
					Utilities::cache_time()
				);
			}

			if ( $attendees ) {
				$success = true;
			}
		}

		wp_send_json( array(
			'attendees' => $attendees,
			'success' => $success,
		));
	}
}
