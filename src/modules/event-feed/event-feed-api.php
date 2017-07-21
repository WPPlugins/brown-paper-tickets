<?php namespace BrownPaperTickets\Modules\EventFeed;

use BrownPaperTickets\APIv2\EventInfo;

/**
 * Class that grabs the event feed from BPT.
 */
class Api extends \BrownPaperTickets\Modules\ModuleApi {
	const URL = 'https://www.brownpapertickets.com/eventfeed/';

	/**
	 * Load events from the feed.
	 *
	 * @param  integer $id The feed's ID.
	 * @return mixed WP_Error upon failure, SimpleXMLElement if successfuly.
	 */
	public function get_events( $id ) {
		$url = esc_url_raw( self::URL . $id );
		$response = wp_remote_get( $url, array( 'timeout' => 10, 'decompress' => false ) );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		try {
            $events = array();
			$xml = new \SimpleXMLElement( $response['body'] );
            $api = new EventInfo( $this->dev_id );

            foreach( $xml->event as $event ) {
                $response = $api->getEvents( null, $event->event_id, true );
                if ( $response ) {
                    $events[] = $response[0];
                }
            }

		} catch (\Exception $e) {
			return false;
		}

		return $events;
	}

	public function get_images( $event_id ) {
		$bpt = new EventInfo( $this->dev_id );
		return $bpt->getImages( $event_id );
	}
}
