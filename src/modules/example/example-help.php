<?php

namespace BrownPaperTickets\Modules\Account;

class Help
{
	public static function add_developer_tools()
	{
		?>
<p>
	If you do not have access to the Developer Status page, you'll need to add Developer Tools to your account.
</p>
<ol>
	<li>Log in to Brown Paper Tickets.</li>
	<li>Go to Account Functions.</li>
	<li>Click on "Developer Tools".</li>
	<li>Click on "Add Developer Tools".</li>
	<li>You'll see a new Developer link in the navigation menu.</li>
	<li>Go there to find your Developer ID.</li>
</ol>
		<?php
	}

	public static function authorized_accounts()
	{
		?>
<p>
	Certain features require that a client account be associated with your developer ID. For example, the Attendee List requires that the client ID used below be associated with the given developer ID.
</p>
<ol>
	<li>Go to <a target="_blank" href="https://www.brownpapertickets.com/developer/accounts.html">Authorized Accounts</a>.</li>
	<li>If your account is listed under "Current Account", click "Edit" and then "Delete Account".</li>
	<li>On the next screen, under "Add a Client" enter in your username and password, select the permissions you need and hit "Add Client Account".</li>
	<li>Your account should now be authorized.</li>
</ol>

<p>If you are still having issues, send an email to <a href="mailto:support@brownpaperticekts.com">support@brownpapertickets.com</a></p>
		<?php
	}

	public static function client_id()
	{
		?>
<p>
You're able to log in to the Brown Paper Tickets website using either a username or your email address. Sometimes they are the same, sometimes they are not.
</p>
<p>If you are having issues determining your username, send an email to <a href="mailto:support@brownpaperticekts.com">support@brownpapertickets.com</a></p>
		<?php
	}
}
