<?php

use BrownPaperTickets\Modules\Account\Inputs as AccountInputs;
use BrownPaperTickets\Modules\EventList\Inputs as EventListInputs;
use BrownPaperTickets\Modules\Calendar\Inputs as CalendarInputs;
use BrownPaperTickets\Modules\General\Inputs as GeneralInputs;

use BrownPaperTickets\BPTPlugin;

$menu_slug = $this->menu_slug;

?>

<form id="bpt-setup-wizard-form" method="post" action="options.php">
<?php settings_fields( $menu_slug . '_api' ); ?>
<div class="bpt-setup-wizard-wrapper wrap">
<h1>
	<img src="<?php echo esc_url( plugins_url( 'public/assets/img/bpt.png', $this->plugin_root() ) ); ?>">
</h1>
	<div class="bpt-setup-wizard bpt-intro">
		<h1>Thanks for installing the Brown Paper Tickets Wordpress Plugin.</h1>
		<p>In order to make use of this plugin, you'll need to set up a few options.</p>
		<div class="bpt-setup-wizard-button-container">
			<button class="bpt-setup-wizard-prev-step button-secondary button-large" disabled >&laquo; Previous</button>
			<button class="bpt-setup-wizard-next-step button-primary button-large">Next &raquo;</button>
		</div>
	</div>
	<div class="bpt-setup-wizard bpt-step-1">
		<h1>Account Setup</h1>
		<h3>
			First, you'll need your Brown Paper Tickets Developer ID.
		</h3>
		<?php AccountInputs::developer_id(); ?>
		<p>
			If you don't already have one, you'll need to do the following to obtain one.
		</p>
		<ol>
			<li>Log in to Brown Paper Tickets</li>
			<li>Go to <a href="https://www.brownpapertickets.com/user/functions.html" target="_blank">Account Functions</a></li>
			<li>Click on "Developer Tools"</li>
			<li>Click on "Add Developer Tools"</li>
			<li>
				You'll see a new <a href="https://www.brownpapertickets.com/developer/index.html" target="_blank">Developer</a>
				link in the navigation menu.</li>
			<li>Go <a href="https://www.brownpapertickets.com/developer/index.html" target="_blank">there</a> to find your Developer ID.</li>
			<li>Enter your Developer ID EXACTLY as it appears into the box above</li>
		</ol>
		<div class="bpt-setup-wizard-button-container">
			<button class="bpt-setup-wizard-prev-step button-secondary button-large">&laquo; Previous</button>
			<button class="bpt-setup-wizard-next-step button-primary button-large">Next &raquo;</button>
		</div>
	</div>
	<div class="bpt-setup-wizard bpt-step-2">
		<h1>Account Setup</h1>
		<h3>
			Next you'll need your Client ID.
		</h3>
		<?php AccountInputs::client_id(); ?>
		<p>
			Your Client ID is the Brown Paper Tickets <em>username</em>
			of your (or the producer whose events you want to list) account.
		</p>
		<p>
			Brown Paper Tickets will allow you to log in using either
			your username or the email address associated with your account.
		<p>
			Sometimes they are the same, sometimes they are not.
		<p>
			If you are having issues determining what your username is,
			please contact Client Services at 1.800.838.3006 or send them
			an <a href="mailto:support@brownpapertickets.com">email</a>.
		</p>
		<div class="bpt-setup-wizard-button-container">
			<button class="bpt-setup-wizard-prev-step button-secondary button-large">&laquo; Previous</button>
			<button class="bpt-setup-wizard-next-step button-primary button-large">Next &raquo;</button>
		</div>
	</div>
	<div class="bpt-setup-wizard bpt-step-3">
		<h1>Account Test</h1>
		<h3>
			Time to test your Developer ID and Client ID.
		</h3>
		<p>
			If you are having issues determining what your username is,
			please contact Client Services at 1.800.838.3006 or send them
			an <a href="mailto:support@brownpapertickets.com">email</a>.
		</p>

		<div>
			<button class="bpt-setup-wizard-test-account button-secondary">Test Account</button><img class="bpt-loading hidden" src="<?php echo esc_url( plugins_url( 'public/assets/img/loading.gif', $this->plugin_root() ) ); ?>">
			<div id="bpt-setup-wizard-response"></div>
		</div>
	</div>
	<div class="bpt-setup-wizard bpt-step-4">
		<h1>Setup is complete.</h1>

		<h2>However, you'll want to take a look at some of the other settings and options available under <a href="<?php menu_page_url( $menu_slug ); ?>">BPT Settings</a>. You can find it at the bottom of the admin screen.</h2>
			<div class="bpt-setup-wizard-bpt-setup-wizard-button-container">
				<button class="bpt-setup-wizard-prev-step button-secondary button-large">&laquo; Previous</button>
				<button class="button-primary button-large bpt-setup-wizard-save">Save</button>
			</div>
		</div>
	</div>
</div>
</form>

<script type="text/html" id="bpt-setup-wizard-template">
	{{ #eventError }}
		<div>
			<h2>Sorry, something went wrong with the Developer ID </h2>
			<h3>{{ explainError(code, 'events') }}</h3>
			<h4 class="last-name"></h4>
			<span class="total-events"></span>
		</div>
	{{ /eventError }}

	{{ #accountError }}
		<div>
			<h2>Sorry, something went wrong with the Client ID.</h2>
			<h3>{{ explainError(code, 'account') }}</h3>
		</div>
	{{ /accountError }}

	{{ #unknownError }}
		<div>
		<h2>Sorry, an unknown error has occured.</h2>
		<pre>
			{{ responseText }}
		</pre>
		</div>
		<div class="bpt-setup-wizard-button-container">
			<button class="bpt-setup-wizard-prev-step button-secondary button-large">Previous</button>
			<button class="bpt-setup-wizard-next-step button-primary button-large bpt-setup-wizard-save" disabled>Save and Continue</button>
		</div>
	{{ /unknownError}}
	{{ #account }}
		<div>
			<h2>Hello {{ firstName }}.</h2>
		</div>
	{{ /account }}

	<div class="bpt-no-events {{ events == false ? '' : 'hidden' }}">
		<p>You're account doesn't seem to have any active events. When your events go live, the plugin will be able to access them.</p>
		<div class="bpt-setup-wizard-button-container">
			<button class="bpt-setup-wizard-prev-step button-secondary button-large">Previous</button>
			<button class="bpt-setup-wizard-next-step button-primary button-large bpt-setup-wizard-save" disabled="{{ .eventError || .accountError ? 'disabled' : '' }}">Save and Continue</button>
		</div>
	</div>

	<div class="{{ .events.length > 0 ? '' : 'hidden' }}">
		<h3>You currently have these {{ .liveEvents( .events ) }} events active on Brown Paper Tickets</h3>
		<ul class="bpt-setup-wizard-event-list">
		{{ #events }}
			<li>{{ title }}</li>
		{{ /events}}
		</ul>

		<p>If this is correct, please click Save and Continue.</p>
		<div class="bpt-setup-wizard-button-container">
			<button class="bpt-setup-wizard-prev-step button-secondary button-large">Previous</button>
			<button class="bpt-setup-wizard-next-step button-primary button-large bpt-setup-wizard-save" disabled="{{ .eventError || .accountError ? 'disabled' : '' }}">Save and Continue</button>
		</div>
	</div>
</script>
