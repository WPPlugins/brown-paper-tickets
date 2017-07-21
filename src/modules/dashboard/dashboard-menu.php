<?php

// require_once( plugin_dir_path( __FILE__ ).'../src/brown-paper-tickets-plugin.php');

use BrownPaperTickets\BPTFeed;
use BrownPaperTickets\BPTPlugin;

?>
<h1>
	<img src="<?php echo esc_url( plugins_url( 'public/assets/img/bpt.png', $this->plugin_root() ) )?>">
</h1>

<div class="wrap">
	<div class="welcome-panel">
		<div class="welcome-panel-content">
			<div id="greeting"></div>
			<p class="about-description">Please be aware that this plugin is a beta release. You may encounter errors and bugs.</p>
			<div class="welcome-panel-column-container">
				<div class="welcome-panel-column">
					<h3>Account Issues</h3>
					<p>
						If you are having issues with your Client or Developer ID, please email <a href="mailto:support@brownpapertickets.com?subject=WordPress Plugin Client ID">support@brownpapertickets.com</a>.
					</p>
					<p>
						<a
							class="button button-primary button-large load-customize"
							target="_blank"
							href="mailto:support@brownpapertickets.com?subject=WordPress Plugin Client ID"
						>
						 <span class="bpt-icon dashicons dashicons-email"></span> Email Support
						</a>
					</p>
				</div>
				<div class="welcome-panel-column">
					<h3>Bug Reports and Feature Requests</h3>
					<p>
						<strong>Bug reports</strong> should be reported on the GitHub issue tracker page. This allows us to keep development of the plugin and issue reporting tightly synced.
					</p>
					<p>
						However, if it is urget (the plugin breaks your site or throws exceptions), you may email <a href="labs@brownpapertickets.com?subject=Plugin Broken">labs@brownpapertickets.com</a>.
					</p>
					<p>
						<strong>Feature Requests</strong> are welcome. If the request is possible and reasonable, it will most likely be added in a future release.
					</p>
					<p>
						<a
							class="button button-primary button-large load-customize"
							target="_blank"
							href="https://github.com/BrownPaperTickets/brown-paper-tickets-wordpress/issues/new?title=<?php esc_html_e( 'v' . $this->version ); ?> - Your Bug Here&labels=bug"
						>
							<span class="bpt-icon dashicons dashicons-info"></span> Submit Bug
						</a>
						<a
							class="button button-primary button-large load-customize"
							target="_blank"
							href="https://github.com/BrownPaperTickets/brown-paper-tickets-wordpress/issues/new?&labels=feature request"
						>
							Request Feature
						</a>
					</p>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/ractive" id="bpt-welcome-panel-template">
	{{ #account }}
	<h2>Hi, {{ firstName }}</h2><h3>Thanks for using Brown Paper Tickets.</h3>
	{{ /account}}
	{{ ^account }}
	<h2>Thanks for using Brown Paper Tickets.</h2>
	{{ /account }}
	<div class="bpt-status-box">

	</div>
	{{ #request }}
		{{ #message }}
			<div class="bpt-message-box">
				<p class="{{ result === false ? 'bpt-error-message' : 'bpt-success-message' }} ">{{ message }} </p>
			</div>
		{{ /message}}
	{{ /request }}
</script>
