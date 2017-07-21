=== Brown Paper Tickets ===
Contributors: Chandler Blum
Donate Link: N/A
Tags: bpt, brown paper tickets
Requires at least: 3.6
Tested up to: 4.6.1
Stable tag: 0.7.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The Brown Paper Tickets Plugin is a simple way to display your Brown Paper Tickets events in a WordPress post/page.

== Description ==

The Brown Paper Tickets plugin is a simple way to display events in a WordPress post/page. You can display a single event, a list of all events or all of your events in a calendar.

The Brown Paper Tickets plugin is Free Software, released under the [GNU GPL v2 or later](http://www.gnu.org/licenses/gpl-2.0.txt). Certain libraries used by the plugin (see About section) are licensed under the MIT License.

It's source code can be found on [Github](https://github.com/BrownPaperTickets/brown-paper-tickets-wordpress).

**There are some caveats to using this plugin. PLEASE READ!**

* The data returned by the [pricelist](http://www.brownpapertickets.com/apidocs/2.0/pricelist.html) API call does not make a distinction between password protected prices and regular prices. As a result, prices that are typically hidden by passwords on BPT will show up via the plugin. **DO NOT use this plugin if you intend to use the event list feature or want your password protected prices to stay hidden.** Calendar format should be OK as it does not make the price list API call.

**Please use the [Issues](https://github.com/BrownPaperTickets/brown-paper-tickets-wordpress/issues/new) page to submit bugs, feature requests, etc.**

== Installation ==

To install the plugin, download the zip, extract it and upload the extracted folder to your plugins directory on your webserver.

From there, activate the plugin as normal. The plugin should take you through a setup wizard. If for some reason it doesn't, on the bottom of your WordPress Admin menu you should see a "BPT Settings" link.

To obtain your developer ID, you must first have developer tools added to your Brown Paper Tickets account. First log into BPT, then go to [Account Functions](https://www.brownpapertickets.com/user/functions.html). Click Developer Tools and then add. You'll see a new link in the BPT navigation titled "Developer". Click that and you'll see your developer ID listed at the top.

Your client ID is typically whatever you use to log into Brown Paper Tickets. If you are having issues with your client ID, please contact [client services](mailto:support@brownpapertickets.com).

== Plugin Usage ==

To use the plugin place the shortcode ``` [list_event] ``` in the post
or page that you want the listing to appear.

= All Events =

[list_events]

= Single Event Listing =
Use the ```[list_events event_id="XXXXXX"]``` shortcode to display a single event (XXXXXX is the event ID).

**The default shipping options set by the plugin are Will-Call and Print at Home. If your events are using something different, go to the "BPT Settings" page in the WordPress Admin to set them.**

= Calendar Format =

Display a calendar listing all of your events:

    [event_calendar]

You can optionally pass in a ```client_id``` attribute to display another producers events in a calendar.

== About the Calendar ==

This plugin makes use of the following Free/Open Source Software:

- [CLNDR.js](http://kylestetz.github.io/CLNDR/)

- [Ractive.js](http://www.ractivejs.org/)

- [Moment.js](http://momentjs.com/)

== Frequently Asked Questions ==

= I've updated some of my events on Brown Paper Tickets but the changes are not showing up in the plugin. Why is that? =

You have most likely enabled the plugin's cache so it's not pulling in the new event data.

There are a few ways to solve this:
    - You could wait for the cache to expire.
    - You could delete the cache and force the plugin to refresh the data.
        - To Do that, simply go to the "General Settings" tab above and click "Delete Cache".

= I am 100% certain that my developer ID and client ID are correct. What is going on? =

It's possible that your client ID is not attached to your developer tools.

To add your account:

- Go to <a target="_blank" href="https://www.brownpapertickets.com/developer/accounts.html">Authorized Accounts</a>.

- If your account is listed under "Current Account", click "Edit" and then "Delete Account".

- On the next screen, under "Add a Client" enter in your username and password, select the permissions you need and hit "Add Client Account".

- Your account should now be authorized.

= My password protected prices are being displayed by the plugin, how do I prevent that? =

When you're logged into WordPress as an Administrator, go to the post/page where the event list is being displayed. You should see a green "HIDE PRICE" link under the prices. Clicking that will hide the price from any visitor to the site who is not logged in as an admin.

= How can I customize the look and feel of the event list or the calendar? =

Go to BPT Settings and click on the appearance tab. You can add custom CSS there.

== Upgrade Notice ==

No upgrade notes.

== Changelog ==

== v0.7.3 ==

** Improvements **

* Added option to display non-live events.

** Bug Fixes **

* Fixed issue with account test displaying error when everything is actually a-okay.

== v0.7.2 ==

**Improvements**

* Added ability to choose which credit card icons to display on the event list.

**Bug Fixes**

* Fixed issue with displaying mobile ticket option in event list.

= v0.7.1 =

**Bug Fixes**

* Fixed issue where upcoming events in the Calendar widget were not being ordered properly.
* Fixed various typos.
* Fixed issue where the setup wizard was not saving the account/client ID properly.

**v0.7.0**

**New Features**

* Added attendee lists

**Improvments**

* Admin UI refactoring. The BPT Settings sections have been split up into
individual pages and kept as native WordPress as possible. This should hopefully
improve use on mobile and in general.

**Bug Fixes**

* Fixed issue where plugin would redirect super admin to plugin settings page upon network activation.

**Other**

* Updated Ractive to v0.7.3
* Swapped icon of the BPT Settings menu.

= v0.6.3 =

**Bug Fixes**

* Fixed issue where the account_test would throw an invalid argument error if the API library doesn't find any events. Fixes #23

= v0.6.2 =

Merging in pull requests from [razordaze](https://github.com/razordaze):

**Bug Fixes**

* Added address fields to calendar event response and widget.
* Minor CSS fixes/improvements.
* Minor tool tip correction.
* Small Template Update to price list table headers.

= v0.6.1 =

* Updated BptAPI library to fix an issue that caused the API to reject requests.

= v0.6.0 =

**New Features**

* Added ability to set whether or not to include the service fee on an individual price.

**Bug Fixes**

* Fixed some input sanitization.
* Fixed bug where the price name was undefined in the hidden prices section of the event list options.

= v0.5.0 =

**New Features**

* Added ability to set a price's interval.

**Bug Fixes**

* Fixed bug that made the price's quantity wacky when changing the max quantity or the interval.

= v0.4.1 =

**Bug Fixes**

* Fixed bug where event list display options weren't being applied if the cache wasn't enabled. #fixes 10
* Added various empty index.php files to prevent directory listings on misconfigured servers.

= v0.4.0 =

**New Features**

* Added ability to change the text of the calendar's event list text.
* Added ability to change the text of the calendar's buy tickets links.

**Improvements**

* Updated FAQ.
* Added some debug information gathering to the help tab.
* Major reorganization of code base.

**Bug Fixes**

* Fixed link to the setup wizard on the help tab.
* Fixed bug where the Welcome message wasn't being displayed properly when the
data wasn't cached.
* Fixed bug where custom date format wasn't being displayed properly on the calendar.

= v0.3.1 =

* Fixed bug where events without dates would throw errors.

= v0.3.0 =

**New Features**

* Added ability to include service fee in price value.
* Added ability to set a max quantity sold per price.
* Added ability to sort events chronologically or reverse chronologically.

**Bug Fixes**

* Fixed issue where prices were not hidden if the data was not cached.

= v0.2.1 =

* Fixed bug with Ractive and the event listing.

= v0.2.0 =

**New Features**

* Users can now add custom CSS for the event listing and calendar
widget/shortcode rules by going to new "Appearance" tab in the
plugin settings.

* Users can now manually hide prices that they do not wish to make
public.
    * __Hiding Prices__: When logged into Wordpress as an admin,
    view the post that contains the event listing. You'll see a
    (HIDE PRICE) button.
    Clicking that will prevent the price from being displayed to
    anyone who isn't an admin.

    * __Showing Prices__: After hiding a price, the hide price link
    will become a (DISPLAY PRICE) link.
    You can also go to the plugin's options page and go to the
    "Password Price Settings" tab and choose to display them
    there.

**Bug Fix**

* Fixed issue with calendar not loading properly if using as a widget.

**Other**

* Updated Ractive to version 0.5.8

= v0.1.31 =

**Bug Fix**

* Fixed rogue console.log();

* Fixed issue where the default title "New Title" was being displayed
above shortcode calendars.

= v0.1.3 =
**Improvements**

* Refactored Calendar Javascript

**New Option in Settings**

* Added Calendar Options settings. You can now set the "Show upcoming
Events in Calendar" option. When enabled, this will show the next 5
events in the event listing if the clicked day does not have any events.
When switching months, it will also show all of the upcoming events in
that month.

**Bug Fixes**

* Fixed issue where shortcodes weren't being placed in the proper place.
* Fixed various typos and grammatical errors.

= v0.1.2 =

**Improvements**

* Added proper uninstall functions.

**Bug Fixes**

* Fixed issue where event calendar wasn't being displayed if a widget
wasn't in place.

* Fixed issue where the cache wasn't being deleted properly.

**Miscellaneous**

* Updated header in main plugin file.


= v0.1.1 =
**Improvements**

* Users can now list multiple events in the same shortcode event_id
attribute.

**Bug Fixes**

* Added 100% width to the pricing table on the default event list theme.

* Fixed issue with PHP versions below 5.3. Changed short array syntax
to array()

* Added proper checks for various shortcode spelling.

* Updated BptAPI library to latest version which fixes a bug where
API errors weren't being returned as an array.

* Fixed bug where event list is displayed only when there is no error.

* Fixed bug where using the event ID of an event not belonging to the
default producer would call the BPT API using the default client ID.

* Fixed issue with loading gif not displaying.

* Fixed issue where data from the API was returned too early.

**Miscellaneous**

* Updated Readme to reflect WP version requirement. has_shortcode()
was introduced in version 3.6.

= v0.1 =

* Initial Release
== Screenshots ==
1.
2.
3.
