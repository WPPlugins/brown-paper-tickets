<?php

namespace BrownPaperTickets\Modules\Dashboard;

class Help
{
	public function debug()
	{
		echo '<p>If you encounter issues, you may need to provide the following information.</p>';
		echo '<strong>PHP Version: </strong>' . phpversion() . '<br />';
		echo '<strong>OS: </strong>' . php_uname() . '<br />';
		$curl_version = curl_version();
		echo '<strong>cURL Version: </strong>' . $curl_version['version'];
	}

	public function setup_wizard()
	{
		echo '<p>Go To <a href="'. get_admin_url( null, 'admin.php' ) . '?page=brown_paper_tickets_settings_setup_wizard">Setup Wizard</a></p>';
	}

	public function saved_options() {
		?>
		<p>
			<a id="get-all-options" href="#get-debug">Get all options saved by the plugin</a>
			<div id="all-options-results">
			</div>
		</p>
		<script type="text/html" id="all-options-template">
			{{ #if options}}
			<textarea id="all-options">{{ options }}</textarea>
			{{ /if }}
		</script>
		<?php
	}

	public function credits() {
		?>
		<p>This plugin makes use of Free Software</p>
		<div>
			<ul>
				<li><a href="http://www.jquery.com" target="blank">jQuery</a></li>
				<li><a href="http://underscorejs.org/" target="_blank">Underscore</a></li>
				<li><a href="http://kylestetz.github.io/CLNDR/" target="_blank">CLNDR.js</a></li>
				<li><a href="http://www.ractivejs.org/" target="_blank">Ractive.js</a></li>
				<li><a href="http://momentjs.com/" target="_blank">Moment.js</a></li>
			</ul>
		</div>
		<?php
	}
}
