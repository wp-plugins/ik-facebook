=== Plugin Name ===
Contributors: richardgabriel
Tags: facebook, facebook feed, facebook embed
Requires at least: 3.0.1
Tested up to: 3.4.2
Stable tag: 1.0.8
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

IK Facebook is a simple plugin for adding bits of Facebook to a WordPress site.

== Description ==

The IK Facebook Plugin is a easy-to-use plugin that allows users to add a Facebook Feed to the sidebar, as a widget, or to embed the Feed into a Page or Post using the shortcode. 

The plugin includes options to set the Title of the Widget, whether or not to show the Like Button above the Feed, and also includes the option to set your own custom CSS for styling purposes.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the contents of `/ik-facebook/` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Place `[ik_fb_feed]` in the body of a post, or use the Appearance section to add the IK Facebook Widget to your Widgets area.
4. For information on how to configure the plugin, see here: https://illuminatikarate.com/ik-facebook-plugin/#instructions

== Frequently Asked Questions ==

= Help!  I need an App ID/Secret Key! =

OK!  We have a great blog post with some helpful information here: https://illuminatikarate.com/blog/how-to-create-a-simple-facebook-app/

Follow the information on that page to Create A Simple Facebook App - you'll be guided along the way to get your App ID, Secret Key, and any other info you may need.

== Screenshots ==

1. This is the Settings page.

== Changelog ==

= 1.0.8 =
* Feature: can now create a CSS file in your theme directory titled 'ik_fb_custom_style.css' - these styles will automatically be loaded by the plugin.

= 1.0.7 =
* Bugfix: remove double output of wrapping widget tag.
* Feature: added option to show or hide the Like Button (hidden by default).

= 1.0.6 = 
* Cleanup Widget markup, using WordPress $before and $after markup.

= 1.0.5 =
* Adds Title Field to Widget.
* Allows multiple Widgets to be active on the site at once.

= 1.0.4 =
* Pull Page Link, Page Name, from Open Graph instead of user options.

= 1.0.3 =
* Readme Updated (again).

= 1.0.2 =
* Readme Updated.

= 1.0.1 =
* Typo fix.

= 1.0 =
* Released!