=== Eventz Lite ===
Contributors: onebytenz
Donate link: https://plugin.onebyte.nz/donate
Tags: event, events, eventz, eventfinda, eventfinder, event finda, event finder, concerts, gig guide, exhibitions, festivals, lifestyle, performing arts, sports, outdoors, workshops, seminars, conferences, classes
Requires at least: 4.6.1
Tested up to: 4.7.3
Stable tag: 4.6
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html

Easily display events from the Eventfinda API on your Wordpress site with a simple shortcode.

== Description ==

This plugin will request event listings from the Eventfinda API servers and display them in your pages or posts.

**Features**

* Easy Setup.
* Responsive design.
* Configurable shortcode that can be used in multiple pages & posts to display different result sets.

Upgrade to [Eventz Pro](https://plugin.onebyte.nz/eventz-pro) to get more features:

* Search Panel for users to enable searching within the result sets.
* Location filter for users to further refine the result sets.
* Data & Image caching to speed up page loading times.
* Adjustable cache expiry times.
* Dynamic Shortcode Builder - no need to manually create shortcodes.
* Support with installation, setup etc...
* More info at http://plugin.onebyte.nz/eventz-pro/features/

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the plugin files to the `/wp-content/plugins/eventz-lite` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Visit http://www.eventfinda.co.nz/api/v2/index to request a user name & Eventfinda API key.
3. Use the Settings->Eventz Lite screen to configure the plugin.
4. Enter your Eventfinda API username & password in the General Setup section.
5. You must add this shortcode [eventz-lite] in a page or post.

== Frequently Asked Questions ==

= How do I use the shortcode? =

* Place the shortcode [eventz-lite] in any page or post.

= How do I refine the result sets? =

Visit http://www.eventfinda.co.nz/api/v2/events and navigate to the "Parameters" section to see the available query parameters that can be used to retrieve the result sets.

= I have events listed with Eventfinda, how do I display just those listings? =

[eventz-lite params="username=myeventfindausername"]


The Location Slug:

To find your location slug just visit Eventfinda Austria, Australia, New Zealand or Singapore and navigate to the location you would like to display listings for.

The location slug is the last string in the url:

http://www.eventfinda.co.nz/whatson/events/auckland-central

The location slug for the url above is "auckland-central".

[eventz-lite params="location_slug=auckland-central"]

The location slug can also be used for venues.

Visit Eventfinda and search for your venue, example “Henderson RSA”.

Events for this venue will be displayed with a link to the venue, for the above example the link is:

https://www.eventfinda.co.nz/venue/henderson-rsa-auckland-west

The location slug to enter for the above example is “henderson-rsa-auckland-west”.


The Category Slug:

To find the category slugs for your country visit your local Eventfinda site and click “Find Events” at the top of the page.

When the “Upcoming Events” page has loaded you will see the event categories listed underneath the locations.

Click the category you would like to display and get the category slug from the url:

http://www.eventfinda.co.nz/concerts-gig-guide/events/new-zealand

The category slug for the url above is "concerts-gig-guide".

[eventz-lite params="category_slug=concerts-gig-guide"] 


Example Shortcodes:

Auckland Events: [eventz-lite params="location_slug=auckland"]

Auckland Gig Guide: [eventz-lite params="location_slug=auckland&category_slug=concerts-gig-guide"]

The paramaters for “rows” & “offset” are taken care of by the plugin (results per page in the admin setup).


* More information on querying the Eventfinda API at http://www.eventfinda.co.nz/api/v2/events
* https://plugin.onebyte.nz/eventz-lite/docs/

== Screenshots ==

1. Event listings - Auckland Gig Guide
2. Admin - General Setup
3. Edit Post - Auckland Gig Guide shortcode

== Changelog ==

= 1.0.3 =
* Added configuration options for front end event display.
* Enable / disable: Event Location / Venue.
* Enable / disable: Event Date.
* Enable / disable: Event Category.
* Select excerpt length: 50 - 300 characters.
* Styling adjustments for front end event display.
* readme.txt - extra info for displaying events for a particular venue.

= 1.0.2 =
* Added code to admin class to increment version number after plugin update.

= 1.0.1 =
* Added dynamic links to request API username & password from Eventfinda based on country selection.
* Logos changed for plugin site.

= 1.0.0 =
* Eventz Lite Version 1.0.0 released Feb 2017
* Eventz Pro Version 2.0.0 released Feb 2017

== Upgrade Notice ==
= 1.0.3 =
= 1.0.2 =
= 1.0.1 =
= 1.0.0 =

