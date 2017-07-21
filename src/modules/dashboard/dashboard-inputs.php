<?php

/**
 * Brown Paper Tickets Account Settings Fields HTML
 *
 * Here lies the callbacks for the add_settings_fields() function.
 */
namespace BrownPaperTickets\Modules\Dashboard;

require_once( plugin_dir_path( __FILE__ ).'../../../src/brown-paper-tickets-plugin.php');

use BrownPaperTickets\BPTPlugin as plugin;


class Inputs {

	public static function section() {
		?>
			<h1>Account Settings</h1>
			<p>In order to use the plugin, you'll need to add your Brown Paper Tickets developer ID.</p>
			<p class="description">You need to have Developer Tools active on your Brown Paper Tickets account. See the help tab on the top right for more information.</p>
		<?php
	}

	public static function developer_id() {
		?>
		<input id="dev-id" name="_bpt_dev_id" value="<?php esc_attr_e( get_option( '_bpt_dev_id' ) );?>" type="text" placeholder="<?php esc_attr_e( 'Developer ID' ); ?>">
		<p class="description">
			You can find your developer ID on the <a href="https://www.brownpapertickets.com/developer/index.html" target="_blank">Developer Status</a> page.
		</p>
		<?php
	}

	public static function client_id() {
		?>
		<input id="client-id" name="_bpt_client_id" value="<?php esc_attr_e( get_option( '_bpt_client_id' ) );?>" type="text" placeholder="<?php esc_attr_e( 'Client ID' ); ?>">
		<p class="description">Your Brown Paper Tickets <strong>username</strong>.</p>
		<?php
	}
}
