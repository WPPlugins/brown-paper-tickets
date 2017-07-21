<?php

namespace BrownPaperTickets\Modules\Calendar;

require_once( plugin_dir_path( __FILE__ ).'../../../src/brown-paper-tickets-plugin.php');

use BrownPaperTickets\BPTPlugin as plugin;
use BrownPaperTickets\BptWordpress as Utilities;

class Inputs {

	public function show_upcoming_events() {
		?>
		<div class="show-upcoming-dates-calendar-wrapper">
			<input id="show-upcoming-dates-calendar-true" name="_bpt_show_upcoming_events_calendar" <?php esc_attr_e( Utilities::is_selected( 'true', '_bpt_show_upcoming_events_calendar', 'checked' ) );?> value="true" type="radio" />
			<label for="show-upcoming-events-calendar-true">Yes</label>
			<input id="show-upcoming-events-calendar-false" name="_bpt_show_upcoming_events_calendar" <?php esc_attr_e( Utilities::is_selected( 'false', '_bpt_show_upcoming_events_calendar', 'checked' ) ); ?> value="false" type="radio" />
			<label for="show-upcoming-events-calendar-false">No</label>
			<p class="description">
				If set to "yes", the calendar will display the closest upcoming events if there aren't any in the current month.
			</p>
		</div>
		<?php
	}

	public function date_text() {
		?>
		<div class="date-text-calendar-wrapper">
			<input id="date-text-calendar" name="_bpt_date_text_calendar" value="<?php esc_attr_e( get_option( '_bpt_date_text_calendar' ) ); ?>" type="text">
		</div>
		<?php
	}

	public function purchase_text() {
		?>
		<div class="purchase-text-calendar-wrapper">
			<input id="purchase-text-calendar" name="_bpt_purchase_text_calendar" value="<?php esc_attr_e( get_option( '_bpt_purchase_text_calendar' ) ); ?>" type="text">
		</div>
		<?php
	}
}
