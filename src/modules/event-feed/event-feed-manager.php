<?php namespace BrownPaperTickets\Modules\EventFeed;

use BrownPaperTickets\BptWordpress as Utilities;

/**
 * The feed manager allows one stop access to feed.
 */
class EventFeedManager {
	/**
	 * Instance of the API class used to pull from BPT.
	 *
	 * @var BrownPaperTickets\Modules\Module\Api;
	 */
	public $api;

	/**
	 * The event feed's ID.
	 *
	 * @var integer
	 */
	public $id;

	/**
	 * How long the feed should stay living for. In Milliseconds.
	 *
	 * @var integer
	 */
	public $expiration = 43200;

	/**
	 * An existing JSON string of the event feed.
	 *
	 * @var string
	 */
	public $events = '';

	/**
	 * Initialize a feed manager with an ID and an optional expiration time (in ms)
	 *
	 * @param integer $id		 The BPT feed's id.
	 * @param integer $expiration How long the feed should stay alive for.
	 */
	public function __construct($id, $expiration = null) {
		$this->id = (integer) $id;

		if ( $expiration ) {
			$this->expiration = $expiration;
		}

		$this->api = new Api( $this->id );
	}

	/**
	 * Returns a string to be used for setting the transient.
	 *
	 * @return string
	 */
	public function transient_name() {
		return \BrownPaperTickets\PLUGIN_SLUG . '_event_feed_' . $this->id;
	}

	/**
	 * Get a feed from the transient. If it's expired or update is set to true,
	 * the feed will update and pull from BPT.
	 *
	 * @param  boolean $update Whether or not to update the feed.
	 * @return mixed	Either a JSON string of the events or a WP_Error object.
	 */
	public function get( $update = false ) {
		// If events are already loaded and we don't need to update, return them.
		if ( $this->events && ! $update ) {
			return $this->events;
		}
		// Get the existing feed from WordPress.
		$feed = get_transient( $this->transient_name() );

		// If there is no feed or update is forced.
		if ( ! $feed || $update ) {

			// Update the feed.
			$updated = $this->update();

			// If updated is a WP_Error, return it.
			if ( is_wp_error( $updated ) ) {
				return $updated;
			}

			// Feed was updated so let's grab the fresh copy.
			$feed = get_transient( $this->transient_name() );
		}

		return $feed;
	}

	/**
	 * Update the feed by pulling from BPT and saving the results as JSON in a WP
	 * transient.
	 *
	 * @return mixed Returns either a boolean with the results of the transient saving
	 * or a WP_Error if it wasn't able to successfully pull new events.
	 */
	public function update() {
		$feed = $this->pull();

		if ( is_wp_error( $feed ) || ! $feed ) {
			return $feed;
		}

		return set_transient( $this->transient_name(), wp_json_encode( $feed ), $this->expiration );
	}

	/**
	 * Delete the feed's transient.
	 *
	 * @return boolean Whether or not delete_Transient worked.
	 */
	public function delete() {
		return delete_transient( $this->transient_name() );
	}

	/**
	 * Pull events from the BPT feed.
	 *
	 * @return mixed WP_Error if wp_remote_get fails, SimpleXMLElement if successfull
	 * or false if the results from the feed could not be parsed.
	 */
	public function pull() {
		$events = $this->api->get_events( $this->id );
		return $this->parse_events( $events );
	}

	public function parse_events( $events ) {
        $events = $this->prune_events( $events );
        $events = $this->prune_dates( $events );
        $events = $this->sort_events( $events );

		foreach ( $events as &$event ) {
			$event['images'] = $this->api->get_images( $event['id'] );
		}

        return $events;
	}

    public function prune_events( $events ) {
        foreach ($events as $key => &$event ) {
            if ( ! $event['live'] ) {
                unset($events[$key]);
            }
        }

        return $events;
    }

    public function prune_dates( $events ) {
        foreach ( $events as &$event ) {
            if ( ! $event['dates'] ) {
                unset($event);
                continue;
            }

            foreach ($event['dates'] as $key => &$date) {
                if ( ! $date['live'] ) {
                    unset( $event['dates'][$key] );
                }
            }

            $event['dates']  = array_values( $event['dates'] );
        }

        return $events;
    }

    public function sort_events( $events ) {
        // foreach ($events as &$event) {
        //     usort( $event['dates'], [$this, 'sort_dates'] );
        // }

        usort( $events, function( $a, $b ) {
            if ($a['dates'][0]['dateStart'] < $b['dates'][0]['dateStart']) {
                return -1;
            }

            if ($a['dates'][0]['dateStart'] > $b['dates'][0]['dateStart']) {
                return 1;
            }

            return 0;
        });

        return $events;
    }
    //
    // public function sort_dates( $a, $b ) {
    //     if ( ! isset($a['start_date']) ||  ! isset( $b['start_date'] ) ) {
    //         return 0;
    //     }
    //
    //     if ( $a['start_date'] < $b['start_date'] ) {
    //         return -1;
    //     }
    //
    //     if ( $a['start_date'] > $b['start_date'] ) {
    //         return 1;
    //     }
    //
    //     return 0;
    // }
}
