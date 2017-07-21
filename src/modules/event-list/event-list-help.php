<?php

namespace BrownPaperTickets\Modules\EventList;

class Help {
	public static function shipping_options()
	{
		?>
<p>
	<h4>This plugin has no way to determine which shipping options are available for your events.</h3>
</p>
<p>
	<h4>You must ensure that the options you select here are actually enabled on your event</h3>
</p>
<p>
	Select the shipping methods you wish to display for your events.
	<ul>
		<li>Print at Home - This method allows ticket buyers to print their tickets at home. No Fee</li>
		<li>Will Call - This method allows the ticket buyer to pick up their tickets at the box office prior to the show. No fee</li>
		<li>Physical - This method will allow physical tickets to be shipped to the ticket buyer, fulfilled by Brown Paper Tickets. Fee. </li>
		<li>Mobile - This method will send the user a text message with their ticket purchase allowing producers who use the Brown Paper Tickets Mobile Scanner App to scan tickets at the door.</li>
	</ul>
</p>
		<?php
	}
}
