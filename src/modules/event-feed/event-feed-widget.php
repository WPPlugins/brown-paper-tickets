<?php namespace BrownPaperTickets\Modules\EventFeed;

require_once('event-feed-manager.php');
require_once(__DIR__ . '/../../../lib/bptWordpress.php');

use BrownPaperTickets\Modules\EventFeed\EventFeedManager as Manager;
use BrownPaperTickets\BptWordpress as Utilities;

class Widget extends \WP_Widget {

	public function __construct() {
		$widget_ops = array(
			'classname' => 'Event Feed',
			'description' => 'Display an event feed in any widget area.',
		);
		parent::__construct( 'event-feed-widget', 'Brown Paper Tickets Event Feed', $widget_ops );
	}

	public function widget($args, $instance) {
        $plugin_root_url = Utilities::plugin_root_url();
		$manager = new Manager( $instance[ 'feed_id' ] );
		$template = get_post_meta( $instance[ 'post_id' ], 'feed_template', true );
		if ( ! $template ) {
			$template = file_get_contents( __DIR__ . '/assets/templates/widget/default-template.php' );
		}

		wp_enqueue_script(
			\BrownPaperTickets\PLUGIN_SLUG . '_event_feed',
			plugin_dir_url( __FILE__ ) . 'assets/js/event-feed.js',
			array( 'ractive_js' ),
			\BrownPaperTickets\BPT_VERSION,
			true
		);

        wp_enqueue_style(
			\BrownPaperTickets\PLUGIN_SLUG . '_event_feed',
			plugin_dir_url( __FILE__ ) . 'assets/css/event-feed.css',
			array(),
			\BrownPaperTickets\BPT_VERSION
		);

		wp_localize_script(
			\BrownPaperTickets\PLUGIN_SLUG . '_event_feed',
			'bptEventFeed',
			array(
                'title' => $instance['title'],
				'feedId' => $instance['feed_id'],
				'containerId' => esc_attr( '#' . $this->get_field_id( 'container-id' ) ),
				'templateId' => esc_attr( '#' . $this->get_field_id( 'feed-template' ) ),
				'url' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce('event-feed'),
                'defaultImage' => $plugin_root_url . '/public/assets/img/bpt-circle-250.png',
			)
		);

		require 'assets/templates/widget/widget.php';
	}

	public function update($new, $old) {
		return array(
			'post_id' => ( ! empty( $new['feed_id'] ) ) ? strip_tags( $new['feed_id'] ) : '',
			'feed_id' => ( ! empty( $new['feed_id'] ) ) ? strip_tags( get_post_meta( $new['feed_id'], 'feed_id', true ) ) : '',
			'title' => ( ! empty( $new['title'] ) ) ? strip_tags( $new['title'] ) : '',
		);
	}

	public function form($instance) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'New title', \BrownPaperTickets\TEXT_DOMAIN );
		$feed_id = ! empty( $instance['feed_id'] ) ? $instance['feed_id'] : '';

		$feeds = new \WP_Query(array(
			'post_type' => 'bpt_event_feed',
		));

		require 'assets/templates/widget/form.php';
	}
}
