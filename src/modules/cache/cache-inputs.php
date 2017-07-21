<?php

namespace BrownPaperTickets\Modules\Cache;

require_once( plugin_dir_path( __FILE__ ).'../../../src/brown-paper-tickets-plugin.php');

use BrownPaperTickets\BPTPlugin as plugin;
use BrownPaperTickets\bptWordpress as Utilities;

class Inputs {

	public function cache_time() {
		$increment  = 1;
		$cache_time = get_option( '_bpt_cache_time' );
		$cache_unit = get_option( '_bpt_cache_unit' );

		?>
			<div class="cache-time-wrapper">
				<label for="cache-time-increment">Cache Time</label>
				<select id="cache-time-increment" name="_bpt_cache_time">
					<option value="false" <?php esc_attr_e( selected( $cache_time, '0' ) );?>>Do Not Cache</option>
					<option value="0" <?php esc_attr_e( selected( $cache_time, '0' ) );?>>Cache Indefinitely</option>
		<?php
		while ( $increment < 50 ) {
						echo '<option value="' . esc_attr( $increment ) . '"' . esc_attr( selected( $cache_time, $increment ) ) . '>' . esc_attr( $increment ) . '</option>';
						$increment++;
					}
		?>
				</select>
				<label for="cache-time-unit">Cache Unit</label>
				<select id="cache-time-unit" name="_bpt_cache_unit">
					<option value="minutes" <?php selected( $cache_unit, 'minutes' ); ?>>Minutes</option>
					<option value="hours" <?php selected( $cache_unit, 'hours' ); ?>>Hours</option>
					<option value="days" <?php selected( $cache_unit, 'days' ); ?>>Days</option>
				</select>
				<button class="button-large button" id="delete-cache"><span id="message">Delete Cache</span></button>
			</div>
		<?php
	}
}
