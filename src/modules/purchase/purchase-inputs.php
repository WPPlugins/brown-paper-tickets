<?php

/**
 * Brown Paper Tickets Account Settings Fields HTML
 *
 * Here lies the callbacks for the add_settings_fields() function.
 */
namespace BrownPaperTickets\Modules\Purchase;

require_once( plugin_dir_path( __FILE__ ).'../../../src/brown-paper-tickets-plugin.php');

use BrownPaperTickets\BPTPlugin;

class Inputs {

	public static function section() {
		if ( ! is_ssl() ) {
			?>
				<h3 class="error">Sorry, you must have SSL (HTTPS) enabled in order to use this option.</h3>
				<p>
					Without SSL on your site, you would be enabling your ticket buyers to submit their Credit Card without any sort of security.
				</p>
				<p>
					You'll want to contact your web host or your web person in order to get SSL set up.
				</p>
			<?php
		} else {
			?>
				<h3>Enable users to purchase tickets without leaving your site.</h3>
			<?php
		}
	}

	public static function enable_sales() {
		if ( ! is_ssl() ) { echo 'SSL must be enabled.'; return; }

		$options = get_option( '_bpt_purchase_settings' );

		$enable_sales = ( isset( $options['enable_sales'] ) ? true : false );
		?>
			<p>Not yet implemented!</p>
			<!-- <div class="purchase-settings-wrapper">
				<label for="bpt-purchase-enable-sales">Enable Sales on Event List</label>
				<input id="bpt-purchase-enable-sales" class="checkbox" name="_bpt_purchase_settings[enable_sales]" type="checkbox" value="true" <?php esc_attr_e( ($enable_sales ? 'checked' : '' ) ); ?> >
				<div class="<?php esc_attr_e( BPTPlugin::get_menu_slug() ); ?>_help">
					<span>?</span>
					<div>
						<p>
							If you would like to use allow customers to purchase tickets from your site, check this.
						</p>
					</div>
				</div>
			</div> -->
		<?php
	}
}