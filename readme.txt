=== Plugin Name ===
Contributors: richardgabriel
Tags: facebook, facebook feed, facebook embed
Requires at least: 3.0.1
Tested up to: 3.5
Stable tag: 1.2.1
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

IK Facebook is a simple plugin for adding bits of Facebook to a WordPress site.

== Description ==

IK Facebook is an easy-to-use plugin that allows users to add a Facebook Feed to the sidebar, as a widget, or to embed the Feed into a Page or Post using the shortcode.  IK Facebook also allows you to insert a Like Button into the Page, Post, or theme

IK Facebook includes options to set the Title of the Widget, whether or not to show the Like Button above the Feed, and whether or not to show the Profile Picture.  IK Facebook supports both the Light and Dark color schemes for the Like Button.

IK Facebook includes the option to set your own custom CSS for styling purposes or, if you prefer, IK Facebook allows you to include a custom style sheet in your theme directory. Gone are the days of fighting with the Facebook Social Plugin!

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the contents of `/ik-facebook/` to the `/wp-content/plugins/` directory
2. Activate IK Facebook through the 'Plugins' menu in WordPress
3. Visit this address for information on how to configure the plugin: https://illuminatikarate.com/ik-facebook-plugin/

= Outputting the Feed =
* To output the Feed, place `[ik_fb_feed colorscheme="light or dark" use_thumb="true or false" width="250"]` in the body of a post, or use the Appearance section to add the IK Facebook Widget to your Widgets area.  If 'use_thumb' is set to true, the value of 'width' will be ignored.  If 'use_thumb' or 'width' are not set, the values from the Options page will be used.
* You can also use the function `ik_fb_display_feed($colorscheme,$use_thumb,$width)` to display the feed in your theme.

= Outputting the Like Button = 
* To output the Like Button, place `[ik_fb_like_button url="http://some_url" height"desired_iframe_height" colorscheme="light or dark"]` in the body of a post.
* You can also use the function `ik_fb_display_like_button($url_to_like,$height_of_iframe,$colorscheme)` to output a like button in your theme.

== Frequently Asked Questions ==

= Help!  I need an App ID/Secret Key! =

OK!  We have a great blog post with some helpful information here: https://illuminatikarate.com/blog/how-to-create-a-simple-facebook-app/

Follow the information on that page to Create A Simple Facebook App - you'll be guided along the way to get your App ID, Secret Key, and any other info you may need.

= Ack!  All I see is 'IK FB: Please check your settings.' - what do I do? =

It's all good!  This just means there is no feed data - this could be due to bad settings, including a bad Page ID, App ID, or Secret Key, or it could be due to some other error such as not having cUrl installed.  Check the plugin instructions for help (or send us a message if you think it's an error.)

= I've set an image width on the options page, but it isn't working! =

No worries!  Did you include any non-integer characters?  Be sure the width is just something like "250" (ignore the quotes) - you don't need to include "px".

== Screenshots ==

1. This is the Settings page.

== Changelog ==

= 1.2.1 =
* Fix: Fix issue that was causing Profile Photo to not load for some Facebook Profiles.

= 1.2 =
* Feature: Adds option to set width of images displayed in feed.  Will use high-res images if this option is enabled, otherwise outputs thumbnails.
* Feature: Adds ability to output the Dark or Light color scheme for the Like Button, both in the Feed, Widget and the standalone Like Button.

= 1.1.2 =
* Only load custom style sheet if one exists.

= 1.1.1 =
* Adds Option to Show / Hide Profile Picture.

= 1.1 = 
* Feature: Adds ability to embed Like Button, without Feed.
* Feature: Adds functions to output Feed and / or Like Button in theme files.
* Clarifies installation instructions.
* Simplifies setup process.

= 1.0.9 =
* CSS compatibility adjustment.

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

== Upgrade Notice ==

= 1.2.1 =
Bug fix available!