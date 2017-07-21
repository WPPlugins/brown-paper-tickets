<?php

namespace BrownPaperTickets\Modules\Calendar;

require( 'calendar-widget.php' );

class Shortcode {
	public static function calendar( $atts ) {

		$calendar_attributes = shortcode_atts(
			array(
				'client_id' => '',
				
				'title' => '',
			),
			$atts
		);

		$calendar_instance = array();
		$title = $calendar_attributes['title'];
		$calendar_instance['title'] = $title;

		if ( $calendar_attributes['client_id'] ) {

			$client_id = $calendar_attributes['client_id'];
			$calendar_instance = array(
				'client_id' => $client_id,
				'display_type' => 'producers_events',
				'title' => $title,
			);
		}

		$calendar_args = array(
			'widget_id' => 'shortcode',
		);

		ob_start();

		the_widget( 'BrownPaperTickets\Modules\Calendar\Widget', $calendar_instance, $calendar_args );

		return ob_get_clean();
	}
}