=== Bookertools Integration ===
Contributors: CodeFairies
Donate link: http://www.bookertools.com/
Tags: bookertools
Requires PHP: 5.2.4
Requires at least: 3.0.1
Tested up to: 6.0.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin adds a widget and shortcode [bookertools_shows] which displays your announced Bookertools shows.

== Description ==

This plugin only works for users of the [Bookertools](https://app.bookertools.com) software.
Use a unique code to connect your Bookertools data with your WordPress website.
When connected you can use :

*	A "Bookertools shows" widget to display the upcoming shows in the sidebar
*	A [bookertools_shows] shortcode to render a table with all upcoming shows
*	A [bookertools_shows band="bandname"] shortcode to render a table with all shows for a specific band
*	A [bookertools_shows venue="venuename"] shortcode to render a table with all shows in a specific venue
*	A [bookertools_shows city="cityname"] shortcode to render a table with all shows in a specific city
*	A [bookertools_shows country="countryname"] shortcode to render a table with all shows in a specific country
*	A "Bookertools tours" widget to display the upcoming tours in the sidebar
*	A [bookertools_tours] shortcode to render a table with all upcoming shows
*	A [bookertools_tours band="bandname"] shortcode to render a table with all tours for a specific band

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings->Plugin Name screen to configure the plugin
4. Create and copy a unique application key from [this page](https://app.bookertools.com/team/applications) and enter it on the settings page


== Frequently Asked Questions ==

= What if we have any questions =

Please contact info@bookertools.com

== Changelog ==
= 1.0 =
* First version on wordpress.org

== Upgrade Notice ==
= 1.5.2 =
Bugfix - Compatibility with php 8+

= 1.5.1 =
added ticket and facebook event link options

= 1.5.0 =
added shortcode filters to retrieve all shows for tour

= 1.4.9 =
added shortcode filters for city and country

= 1.4.8 =
added soldout notice

= 1.4.7 =
Bugfix - php8 compatibility

= 1.4.6 =
Bugfix - changes for new api

= 1.4.5 =
Bugfix - default limit 1000 shows

= 1.4.4 =
Added ticket links

= 1.4.3 =
Added 'show tourname if available' and 'group shows on same date' functionality 

= 1.4.2 =
Editable 'no shows/tours found' text

= 1.4.1 =
Bugfix for loading shows for bands & venues

= 1.4 =
Added tour shortcode and widget

= 1.3.5 =
Added limit to shortcode



