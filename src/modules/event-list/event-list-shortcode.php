<?php namespace BrownPaperTickets\Modules\EventList;

use BrownPaperTickets\BPTPlugin as plugin;

class Shortcode {

	/**
	 * Shortcode stuff!
	 */
	public static function list_event_shortcode( $atts ) {

		global $post;

		if ( is_home() ||
				is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'list-events' ) ||
				is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'list_events' ) ||
				is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'list-event' ) ||
				is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'list_event' )
			) {

			$event_list_attributes = shortcode_atts(
				array(
					'event_id' => null,
					'client_id' => null,
					'event-id' => null,
					'client-id' => null,
				),
				$atts
			);


			$localized_variables = array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'bptNonce' => wp_create_nonce( 'bpt-event-list-nonce' ),
				'postID' => $post->ID,
			);

			$purchase_options = get_option( '_bpt_purchase_settings' );

			if ( isset( $purchase_options['enable_sales'] ) ) {
				$localized_variables['enableSales'] = $purchase_options['enable_sales'];
			}

			if ( $event_list_attributes['event_id'] ) {
				$localized_variables['eventID'] = $event_list_attributes['event_id'];
			}

			if ( $event_list_attributes['event-id'] ) {
				$localized_variables['eventID'] = $event_list_attributes['event-id'];
			}

			if ( $event_list_attributes['client-id'] ) {
				$localized_variables['clientID'] = $event_list_attributes['client-id'];
			}

			if ( $event_list_attributes['client_id'] ) {
				$localized_variables['clientID'] = $event_list_attributes['client_id'];
			}

			wp_enqueue_style( 'bpt_event_list_css', plugins_url( '/assets/css/bpt-event-list-shortcode.css', __FILE__ ), array(), \BrownPaperTickets\BPT_VERSION );

			wp_register_script(
				'event_feed_js_' . $post->ID,
				plugins_url( '/assets/js/event-feed.js', __FILE__ ),
				array( 'jquery', 'underscore', 'ractive_js', 'ractive_transitions_slide_js', 'moment_with_langs_min' ),
				null,
				true
			);

			wp_enqueue_script( 'event_feed_js_' . $post->ID );

			wp_localize_script(
				'event_feed_js_' . $post->ID,
				'bptEventFeedAjaxPost' . $post->ID,
				$localized_variables
			);
		}

		return require( __DIR__ . '/assets/templates/event-list.php');
	}
}
