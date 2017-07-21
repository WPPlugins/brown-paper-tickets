<?php

namespace BrownPaperTickets\Modules\Calendar;

use BrownPaperTickets\BPTPlugin as plugin;

class Widget extends \WP_Widget {

	protected static $plugin_version;

	public function __construct( ) {

		self::$plugin_version = \BrownPaperTickets\BPT_VERSION;

		parent::__construct(
			'_bpt_widget_calendar',
			__( 'Brown Paper Tickets Calendar Widget', 'brown-paper-tickets-locale' ),
			array( 'description', __( 'Simple widget to display events in a calendar.', 'brown-paper-tickets-locale' ) )
		);


		if ( is_active_widget( false, false, $this->id_base, true ) ) {

		}
	}


	public function form( $instance ) {

		$title           = self::get_title( $instance );
		$display_type    = self::get_display_type( $instance );
		$client_id       = self::get_client_id( $instance );
		$upcoming_events = self::get_upcoming_events( $instance );

		?>

			<p>
				<label for="<?php esc_attr_e( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:' ); ?></label>
				<input class="widefat" id="<?php esc_attr_e( $this->get_field_id( 'title' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">

				<label for="<?php esc_attr_e( $this->get_field_id( 'display_type' ) ); ?>"><?php _e( 'Display:' ); ?></label>
				<select class="widefat" id="<?php esc_attr_e( $this->get_field_id( 'display_type' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'display_type' ) ); ?>">
					<option value="<?php esc_attr_e( 'all_events' ); ?>" <?php selected( $display_type, 'all_events' ); ?>><?php  _e( 'Your Events' ); ?></option>
					<option value="<?php esc_attr_e( 'producers_events' ); ?>" <?php selected( $display_type, 'producers_event' ); ?>><?php  _e( 'Another Producer\'s Events' ); ?></option>
				</select>

				<label for="<?php esc_attr_e( $this->get_field_id( 'client_id' ) ); ?>"><?php _e( 'Client ID:' ); ?></label>
				<input class="widefat" id="<?php esc_attr_e( $this->get_field_id( 'client_id' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'client_id' ) ); ?>" type="text" value="<?php echo esc_attr( $client_id ); ?>">

				<input type="checkbox" value="true" id="<?php esc_attr_e( $this->get_field_id( 'upcoming_events' ) ); ?>_true" class="checkbox" name="<?php esc_attr_e( $this->get_field_name( 'upcoming_events' ) ); ?>" <?php esc_attr_e( ( $upcoming_events ? 'checked' : '') ); ?>>
				<label for="<?php esc_attr_e( $this->get_field_id( 'upcoming_events' ) ); ?>_true">Display Upcoming Events?</label>
			</p>
		<?php
	}

	public function widget( $args, $instance ) {

		$title         = apply_filters( 'widget_title', self::get_title( $instance ) );
		$display_type  = self::get_display_type( $instance );
		$client_id     = self::get_client_id( $instance );
		$widget_id     = $args['widget_id'];
		$date_format   = get_option( '_bpt_date_format' );
		$time_format   = get_option( '_bpt_time_format' );
		$date_text     = get_option( '_bpt_date_text_calendar' );
		$purchase_text = get_option( '_bpt_purchase_text_calendar' );

		if ( $date_format === 'custom' ) {
			$date_format = get_option( '_bpt_custom_date_format' );
		}

		if ( ! $purchase_text ) {
			$purchase_text = 'Buy Tickets';
		}

		if ( is_active_widget( false, false, $this->id_base, true ) || $args['widget_id'] === 'shortcode' ) {

			wp_enqueue_style( 'bpt_calendar_widget_css', plugins_url( 'assets/css/calendar-widget.css', __FILE__ ), false, self::$plugin_version );
			wp_enqueue_script(
				'bpt_calendar_widget_js',
				plugins_url( 'assets/js/calendar-widget.js', __FILE__ ),
				array(
					'jquery',
					'ractive_js',
					'ractive_transitions_slide_js',
					'ractive_transitions_fade_js',
					'moment_with_langs_min',
					'clndr_min_js',
				),
				false,
				true
			);

			if ( $widget_id === 'shortcode' ) {
				// Whether or not to show a list of upcoming events if there are no events in the month.
				// We pull this from the settings rather than set it in the shortcode.
				$show_upcoming_events = get_option( '_bpt_show_upcoming_events_calendar' );

				wp_localize_script(
					'bpt_calendar_widget_js', 'bptCalendarWidgetShortcodeAjax', array(
						'ajaxurl'     => admin_url( 'admin-ajax.php' ),
						'nonce'    => wp_create_nonce( 'bpt-calendar-widget-nonce' ),
						'clientID'    => $client_id,
						'widgetID'    => $widget_id,
						'showUpcoming' => $show_upcoming_events,
						'calendarContainer' => '.bpt-calendar-shortcode',
						'eventListContainer' => '#bpt-calendar-shortcode-event-view',
					)
				);

			} else {
				// Whether or not to show a list of upcoming events if there are no events in the month.
				// We pull this from the widget's instance rather than the option set.
				$show_upcoming_events    = self::get_upcoming_events( $instance );

				if ( $show_upcoming_events === '' ) {
					$show_upcoming_events = 'false';
				}

				wp_localize_script(
					'bpt_calendar_widget_js', 'bptCalendarWidgetAjax', array(
						'ajaxurl'     => admin_url( 'admin-ajax.php' ),
						'nonce'    => wp_create_nonce( 'bpt-calendar-widget-nonce' ),
						'widgetID'    => $widget_id,
						'showUpcoming' => $show_upcoming_events,
						'calendarContainer' => '.bpt-calendar-widget',
						'eventListContainer' => '#bpt-calendar-widget-event-view',
					)
				);
			}
		}

		echo wp_kses_post( $args['before_widget'] );

		if ( isset( $title ) ) {
			echo wp_kses_post( $args['before_title'] . $title . $args['after_title'] );
		}

		$calendar_style = get_option( '_bpt_calendar_style' );

		if ( $calendar_style ) {
			$use_style = ( isset( $calendar_style['use_style'] ) ? true : false );

			if ( $use_style ) {
				$css = '<style type="text/css">' . esc_html( $calendar_style['custom_css'] ) . '</style>';

			}

			if ( isset( $css ) ) {
				$allowed_html = array(
					'style' => array(
						'type' => array(),
					),
				);

				echo wp_kses( $css, $allowed_html );
			}
		}

		?>
			<div class="<?php esc_attr_e( ( $widget_id === 'shortcode' ? 'bpt-calendar-shortcode' : 'bpt-calendar-widget' ) ); ?>" class="bpt-calendar-<?php esc_attr_e( $widget_id ); ?>">

			</div>
			<div id="<?php esc_attr_e( ( $widget_id === 'shortcode' ? 'bpt-calendar-shortcode-event-view' : 'bpt-calendar-widget-event-view' ) ) ?>" class="bpt-calendar-event-list-<?php esc_attr_e( $widget_id ); ?>">

			</div>
			<script type="text/html" id="bpt-calendar-widget-calendar-template">
				<div class="bpt-calendar-widget-controls">
					<div class="bpt-calendar-widget-controls-button">
						<span class="bpt-calendar-widget-controls-previous-button">&laquo;</span>
					</div>
					<div class="bpt-calendar-widget-controls-month">
						<%= month %> <%= year %>
					</div>
					<div class="bpt-calendar-widget-controls-button">
						<span class="bpt-calendar-widget-controls-next-button">&raquo;</span>
					</div>
				</div>
				<table class="bpt-calendar-widget-table" border="0" cellspacing="0" cellpadding="0">
					<thead>
						<tr class="bpt-calendar-widget-header-days">
							<% for(var i = 0; i < daysOfTheWeek.length; i++ ) { %>
							<td class="bpt-calendar-widget-header-day">
								<%= daysOfTheWeek[i] %>
							</td>
							<% } %>
						</tr>
					</thead>
					<tbody>
						<% for(var i = 0; i < numberOfRows; i++){ %>
						<tr class="bpt-calendar-widget-week-row">
							<% for(var j = 0; j < 7; j++){ %>
								<% var d = j + i * 7; %>
							<td class="<%= days[d].classes %>">
								<div class="bpt-calendar-widget-day-contents">
									<%= days[d].day %>
								</div></td>
							<% } %>
						</tr>
						<% } %>
					</tbody>
				</table>
			</script>

			<script type="text/html" id="bpt-calendar-widget-event-view-template" class="bpt-calendar-widget-event-view">
				{{ #date }}
					<h1 intro="slide" outro="fade"><?php esc_html_e( $date_text );?> {{ formatDate( '<?php esc_attr_e( $date_format ); ?>', date ) }}</h1>
				{{ /date }}
				{{ #eventsThisMonth }}
					<h1 intro="slide" outro="fade">Events This Month</h1>
				{{ /eventsThisMonth }}

				{{ #showUpcoming}}
					<h1 intro="slide" outro="fade">Upcoming Events</h1>
				{{ /showUpcoming}}

				{{ #sortEvents(currentEvents) }}
				<div class="bpt-calendar-widget-event-view-event" intro="slide" outro="fade">
					<h2>{{{ unescapeHTML(title) }}}</h2>
					<div class="bpt-calendar-widget-event-view-location">
                        {{#address1}}
						 <div class="address1">{{ address1 }}</div>
                         {{/address1}}
                         {{#address2}}
						 <div class="address2">{{ address2 }}</div>
                         {{/address2}}
						 <div>{{#city}}<span class="city">{{ city }}</span>,{{/city}} {{#state}}<span class="state">{{ state }}</span>{{/state}}</div>
					</div>
					<div class="bpt-calendar-widget-event-view-date">
						{{ formatDate( '<?php esc_attr_e( $date_format ); ?>', date ) }}
					</div>
					<div class="bpt-calendar-widget-event-view-description">
						{{ shortDescription }}
					</div>
					<div class="bpt-calendar-widget-event-view-buy-tickets">
						<a href="http://www.brownpapertickets.com/event/{{ eventID }}?date={{ dateID }}" target="_blank"><?php esc_html_e( $purchase_text ); ?></a>
					</div>
				</div>
				{{ /currentEvents}}
			</script>
		<?php

		echo wp_kses_post( $args['after_widget'] );
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();

		$instance['title']        = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['display_type'] = ( ! empty( $new_instance['display_type'] ) ) ? strip_tags( $new_instance['display_type'] ) : '';
		$instance['client_id']    = ( ! empty( $new_instance['client_id'] ) ) ? strip_tags( $new_instance['client_id'] ) : '';
		$instance['upcoming_events']    = ( ! empty( $new_instance['upcoming_events'] ) ) ? strip_tags( $new_instance['upcoming_events'] ) : '';
		return $instance;
	}


	private static function get_title( $instance ) {
		if ( isset( $instance['title'] ) ) {

			$title = $instance['title'];

		} else {

			$title = __( 'New title', 'brown-paper-tickets-locale' );
		}

		return $title;
	}

	private static function get_display_type( $instance ) {

		if ( isset( $instance['display_type'] ) ) {

			$display_type = $instance['display_type'];

		} else {

			$display_type = 'all_events';

		}

		return $display_type;
	}

	private static function get_client_id( $instance ) {
		if ( isset( $instance['client_id'] ) ) {

			$client_id = $instance['client_id'];

		} else {

			$client_id = null;

		}

		return $client_id;
	}

	private static function get_upcoming_events( $instance ) {
		if ( isset( $instance['upcoming_events'] ) ) {

			$upcoming_events = $instance['upcoming_events'];

		} else {

			$upcoming_events = null;

		}

		return $upcoming_events;
	}
}
