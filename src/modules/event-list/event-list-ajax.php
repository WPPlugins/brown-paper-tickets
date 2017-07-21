<?php

namespace BrownPaperTickets\Modules\EventList;

require( 'event-list-api.php' );

use \BrownPaperTickets\BptWordpress as Utilities;

class Ajax {

	private $nonce_title = 'bpt-event-list-nonce';

	/**
	 * Get the Events
	 */
	public function get_events( $get = null ) {

		if ( ! $get ) {
			$get = filter_input_array( INPUT_GET, FILTER_SANITIZE_ENCODED );
		}

		$nonce	 = $get['nonce'];
		$post_id   = null;
		$client_id = null;
		$event_id  = null;
		$widget_id = null;

		if ( isset( $get['postID'] ) ) {
			$post_id = $get['postID'];
		}

		if ( isset( $get['clientID'] ) ) {
			$client_id = $get['clientID'];
		}

		if ( isset( $get['eventID'] ) ) {
			$event_id = $get['eventID'];
		}

		if ( isset( $get['widgetID'] ) ) {
			$widget_id = $post['widgetID'];
		}

		Utilities::check_nonce( $nonce, $this->nonce_title );

		$events = new Api;

		if ( ! Utilities::cache_enabled() ) {
			$events = $events->get_events( $client_id, $event_id );
			$events = $this->sort_events( $events );
			$events = $this->apply_price_options( $events );

			wp_send_json( $events, true );
		}

		if ( ! get_transient( '_bpt_event_list_events' . $post_id ) ) {
			set_transient(
				'_bpt_event_list_events' . $post_id,
				$events->get_events( $client_id, $event_id ),
				Utilities::cache_time()
			);
		}

		$events = get_transient( '_bpt_event_list_events' . $post_id );

		if ( $event_id ) {
			$single_event = $this->get_single_event( $event_id, $events );

			if ( ! $single_event ) {
				wp_send_json( array( 'success' => false, 'error' => 'Could not find event.' ) );
			}

			$events = array( $single_event );
		}

		$events = $this->sort_events( $events );
		$events = $this->apply_price_options( $events );

		wp_send_json( $events );
	}

	/**
	 * Gets a specific event from an array of events.
	 * @param  integer $eventId The event ID.
	 * @param  mixed $events Either a json string or an array of events.
	 * @return mixed			Returns the single event array or false if no event.
	 */
	private function get_single_event( $event_id, $events ) {
		if ( is_string( $events ) ) {
			$events = json_decode( $events, true );
		}

		$single_event = false;

		foreach ( $events as $event ) {
			if ( $event['id'] === (integer) $event_id ) {
				$single_event = $event;
			}
		}

		return $single_event;
	}

	public function hide_prices() {
		$post = filter_input_array(INPUT_POST, FILTER_SANITIZE_ENCODED);
		$response = array();
		$nonce = $post['nonce'];

		if ( isset( $post['admin'] ) && $post['admin'] ) {
			$nonceTitle = 'bpt-admin-nonce';
		} else {
			$nonceTitle = 'bpt-event-list-nonce';
		}

		Utilities::check_nonce( $nonce, $nonceTitle );

		if ( Utilities::is_user_an_admin() ) {

			$hidden_prices = get_option( '_bpt_hidden_prices' );

			if ( $hidden_prices === '' || ! $hidden_prices ) {
				$hidden_prices = array();
			}

			if ( ! is_array( $post['prices'] ) ) {
				wp_send_json_error( 'No prices were sent.' );
			}

			$prices = $post['prices'];

			$response['prices'] = $prices;

			foreach ( $prices as $price ) {

				if ( array_key_exists( $price['priceId'], $hidden_prices ) ) {
					continue;
				}

				if ( empty( $price['eventTitle'] ) ) {
					$response['error'] = 'Event title is required.';
				}


				if ( empty( $price['eventId'] ) ) {
					$response['error'] = 'Event ID is required.';
				}

				if ( empty( $price['priceId'] ) ) {
					$response['error'] = 'Price ID is required.';
				}

				if ( empty( $price['priceName'] ) ) {
					$response['error'] = 'Price name is required.';
				}

				$id = $price['priceId'];

				$hidden_prices[ $id ] = $price;

			}

			$response['hiddenPrices'] = $hidden_prices;

			$update_option = update_option( '_bpt_hidden_prices', $hidden_prices );

			if ( $update_option ) {

				$response['success'] = 'Price has been hidden';
				$response['priceID'] = $price['priceId'];

			} else {

				$response['error'] = 'Could not hide price.';
				$response['priceID'] = $price['priceId'];
			}
		} else {

			$response = array(
				'error' => 'Not authorized.',
			);
		}

		wp_send_json( $response );
	}

	/**
	 * This unhides the given prices. A events array needs to be posted to this.
	 */
	public function unhide_prices() {
		$post = filter_input_array( INPUT_POST, FILTER_SANITIZE_ENCODED );
		$response = array();
		$nonce = $post['nonce'];

		if ( isset( $post['admin'] ) ) {
			$nonceTitle = 'event-list-admin';
		} else {
			$nonceTitle = 'bpt-event-list-nonce';
		}

		Utilities::check_nonce( $nonce, $nonceTitle );

		if ( Utilities::is_user_an_admin() ) {

			$hidden_prices = get_option( '_bpt_hidden_prices' );

			if ( ! $hidden_prices ) {
				$response['error'] = 'No hidden prices';
			}

			$prices = $post['prices'];
			$response['prices'] = $prices;

			foreach ( $prices as $price ) {
				$id = $price['priceId'];
				unset( $hidden_prices[ $id ] );
			}

			$response['hiddenPrices'] = $hidden_prices;
			$update_option = update_option( '_bpt_hidden_prices', $hidden_prices );

			if ( $update_option ) {
				$response['success'] = 'Price is now visible.';
				$response['priceID'] = $price['priceId'];

			} else {
				$response['error'] = 'Could not unhide price.';
				$response['priceID'] = $price['priceId'];
			}
		} else {
			$response = array(
				'error' => 'Not authorized.',
			);
		}

		wp_send_json( $response );
	}

	/**
	 * This sets a prices max quantity to whatever is passed.
	 */
	public function set_price_max_quantity() {
		$post = filter_input_array( INPUT_POST, FILTER_SANITIZE_ENCODED );
		if ( isset( $post['maxQuantity'] ) ) {
			$max_quantity = get_option( '_bpt_price_max_quantity' );

			if ( ! $max_quantity ) {
				$max_quantity = array();
			}

			foreach ( $post['maxQuantity'] as $prices  ) {

				foreach ( $prices as $id => $max ) {

					$id = intval( $id );
					$max = intval( $max );

					if ( $max === 0 ) {
						unset( $max_quantity[ $id ] );
						continue;
					}

					$max_quantity[ $id ] = $max;
				}
			}

			if ( update_option( '_bpt_price_max_quantity', $max_quantity ) ) {
				wp_send_json( array( 'success' => true, 'message' => 'Updated price quantity.', 'maxQuantity' => $max_quantity ) );
			}
		}

		wp_send_json_error( 'Unable to update price quantity.' );
	}

	public function set_price_intervals() {
		$post = filter_input_array( INPUT_POST, FILTER_SANITIZE_ENCODED );

		if ( isset( $post['intervals'] ) ) {
			$price_intervals = get_option( '_bpt_price_intervals' );

			if ( ! $price_intervals ) {
				$price_intervals = array();
			}

			foreach ( $post['intervals'] as $prices ) {
				foreach ( $prices as $id => $interval ) {
					$id = intval( $id );
					$interval = intval( $interval );

					if ( $interval === 0 ) {
						unset( $price_intervals[ $id ] );
						continue;
					}

					$price_intervals[ $id ] = $interval;
				}
			}

			if ( update_option( '_bpt_price_intervals', $price_intervals ) ) {
				wp_send_json(
					array(
						'success' => true,
						'message' => 'Updated price interval.',
						'interval' => $price_intervals,
					)
				);
			}
		}

		wp_send_json_error( 'Unable to update price interval.' );
	}

	public function set_price_include_fee() {

		$post = filter_input_array( INPUT_POST, FILTER_SANITIZE_ENCODED );

		if ( isset( $post['includeFee'] ) ) {
			$price_include_fee = get_option( '_bpt_price_include_fee' );
			$existing_include = $price_include_fee;

			if ( ! $price_include_fee ) {
				$price_include_fee = array();
			}

			foreach ( $post['includeFee'] as &$prices ) {
				foreach ( $prices as $id => $include ) {
					$id = intval( $id );
					$include = ( $include === 'true' ? true : false );

					if ( ! $include ) {
						unset( $price_include_fee[ $id ] );
						continue;
					}

					$price_include_fee[ $id ] = true;
				}
			}

			if ( $existing_include === $price_include_fee ) {
				wp_send_json(
					array(
						'success' => true,
						'message' => 'Nothing to update.'
					)
				);
			}

			if ( update_option( '_bpt_price_include_fee', $price_include_fee ) ) {
				wp_send_json(
					array(
						'success' => true,
						'message' => 'Updated price include fee.',
						'includeFee' => $price_include_fee,
					)
				);
			}
		}

		wp_send_json_error( 'Unable to update price interval.' );
	}

	private function sort_events( $events ) {
		$sort = get_option( '_bpt_sort_events' );

		if ( $sort ) {

			if ( $sort === 'chrono' ) {
				usort( $events,
					function($a, $b) {

						if ( ! isset( $a['dates'] ) || ! isset( $b['dates'] ) ) {
							return 0;
						}

						if ( ! isset( $a['dates'][0] ) || ! isset( $b['dates'][0] ) ) {
							return 0;
						}

						if ( $a['dates'][0]['dateStart'] < $b['dates'][0]['dateStart'] ) {
							return -1;
						}
						if ( $a['dates'][0]['dateStart'] > $b['dates'][0]['dateStart'] ) {
							return 1;
						}
						return 0;
					}
				);
			}

			if ( $sort === 'reverse' ) {
				usort( $events,
					function($a, $b) {

						if ( ! isset( $a['dates'] ) || ! isset( $b['dates'] ) ) {
							return 0;
						}

						if ( ! isset( $a['dates'][0] ) || ! isset( $b['dates'][0] ) ) {
							return 0;
						}

						if ( $a['dates'][0]['dateStart'] > $b['dates'][0]['dateStart'] ) {
							return -1;
						}
						if ( $a['dates'][0]['dateStart'] < $b['dates'][0]['dateStart'] ) {
							return 1;
						}
						return 0;
					}
				);
			}
		}

		return $events;
	}

	private function apply_price_options( $events ) {

		$events = $this->filter_hidden_prices( $events );
		$events = $this->apply_max_quantity( $events );
		$events = $this->apply_intervals( $events );
		$events = $this->apply_include_fee( $events );
		return $events;
	}

	private function apply_max_quantity( $events ) {
		if ( $max_quantity = get_option( '_bpt_price_max_quantity' ) ) {

			foreach ( $events as &$event ) {
				foreach ( $event['dates'] as &$date ) {
					foreach ( $date['prices'] as &$price ) {
						foreach ( $max_quantity as $max ) {
							if ( key( $max_quantity ) === $price['id'] ) {
								$price['maxQuantity'] = $max;
							}
						}
					}
				}
			}
		}

		return $events;
	}

	private function apply_intervals( $events ) {
		if ( $intervals = get_option( '_bpt_price_intervals' ) ) {
			foreach ( $events as &$event ) {
				foreach ( $event['dates'] as &$date ) {
					foreach ( $date['prices'] as &$price ) {
						foreach ( $intervals as $interval ) {
							if ( key( $intervals ) === $price['id'] ) {
								$price['interval'] = (integer) $interval;
							}
						}
					}
				}
			}
		}

		return $events;
	}

	private function apply_include_fee( $events ) {

		$all_include_fees = ( get_option( '_bpt_include_service_fee' ) === 'true' ? true : false );
		$include_fee = get_option( '_bpt_price_include_fee' );

		foreach ( $events as &$event ) {
			foreach ( $event['dates'] as &$date ) {
				foreach ( $date['prices'] as &$price ) {

					if ( $all_include_fees ) {
						$price['includeFee'] = true;
						continue;
					}

					if ( ! empty( $include_fee ) ) {
						foreach ( $include_fee as $include ) {

							if ( array_key_exists( $price['id'], $include_fee ) ) {
								$price['includeFee'] = $include;
								continue;
							}

							$price['includeFee'] = false;
							continue;
						}
					}
				}
			}
		}

		return $events;
	}

	/**
	 * Include the service fee globally for all prices.
	 * @param  array $events An array of events with dates and prices.
	 * @return array The modified event array.
	 */
	private function include_service_fee( $events ) {

		if ( get_option( '_bpt_include_service_fee' ) === 'true' ) {
			foreach ( $events as &$event ) {

				if ( ! isset( $event['dates'] ) ) {
					continue;
				}

				foreach ( $event['dates'] as &$date ) {
					foreach ( $date['prices'] as &$price ) {
						$price['value']  = $price['value'] + $price['serviceFee'];
					}
				}
			}
		}

		return $events;
	}

	/**
	 * Filter Hidden Prices
	 * @param  mixed $events Either a json string or an array of events.
	 * @return array		 The modified/filtered array.
	 */
	private function filter_hidden_prices( $events ) {

		if ( is_string( $events ) ) {
			$events = json_decode( $events, true );
		}

		$hidden_prices = get_option( '_bpt_hidden_prices' );

		if ( $hidden_prices ) {

			// If the user is an admin, we'll just add a property "hidden" to the
			// price.
			if ( Utilities::is_user_an_admin() ) {

				foreach ( $events as &$event ) {

					foreach ( $event['dates'] as &$date ) {

						foreach ( $date['prices'] as &$price ) {

							if ( array_key_exists( $price['id'], $hidden_prices ) ) {

								$price['hidden'] = true;

							}
						}
					}
				}
			} else {
				// If the user is not an admin, we'll remove that price.
				foreach ( $events as &$event ) {

					foreach ( $event['dates'] as &$date ) {

						$i = 0;

						foreach ( $date['prices'] as &$price ) {

							if ( array_key_exists( $price['id'], $hidden_prices ) ) {

								unset( $date['prices'][ $i ] );

							}

							$i++;
						}

						$date['prices'] = array_values( $date['prices'] );
					}
				}
			}
		}

		return $events;
	}
}
