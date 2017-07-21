<?php namespace BrownPaperTickets\Modules\EventFeed;

use \BrownPaperTickets\BptWordpress as Utilities;

require_once('event-feed-api.php');

use \BrownPaperTickets\Modules\EventFeed\Api;

/**
 * Ajax functions for the EventFeed module.
 */
class Ajax {

	/**
	 * Gets the feed events from the API class and returns JSON responses.
	 *
	 * @return string JSON containing either an error or events.
	 */
	public static function get_feed_events() {
		Utilities::check_nonce( $_GET['nonce'], 'event-feed' );

		if ( ! isset( $_REQUEST['id'] ) ) {
			wp_send_json_error( [ 'message' => 'No feed ID sent.' ] );
		}

		$manager = new EventFeedManager( $_REQUEST['id'] );
		$update = false;

		if ( isset( $_REQUEST['update'] ) ) {
			$update = true;
		}

		$events = $manager->get( $update );

		if ( is_wp_error( $events ) ) {
			wp_send_json_error( array( 'message' => $events->get_error_message() ) );
		}

		if ( ! $events ) {
			wp_send_json_error( array( 'message' => 'Could not load events.' ) );
		}

		wp_die( $events );
	}
}
