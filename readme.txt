=== Plugin Name ===
Contributors: richardgabriel
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=V7HR8DP4EJSYN
Tags: facebook, facebook feed, facebook embed
Requires at least: 3.0.1
Tested up to: 3.5.1
Stable tag: 1.7
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

The IK Facebook Plugin is a simple plugin for adding bits of Facebook to a WordPress site.

== Description ==

The IK Facebook Plugin is an easy-to-use plugin that allows users to add a Facebook Feed to the sidebar, as a widget, or to embed the Feed into a Page or Post using the shortcode.  The IK Facebook Plugin also allows you to insert a Like Button into the Page, Post, or theme

The IK Facebook Plugin includes options to set the Title of the Widget, whether or not to show the Like Button above the Feed, and whether or not to show the Profile Picture.  The IK Facebook Plugin supports both the Light and Dark color schemes for the Like Button.

The IK Facebook Plugin includes the option to set your own custom CSS for styling purposes or, if you prefer, the IK Facebook Plugin allows you to include a custom style sheet in your theme directory. The IK Facebook Plugin also allows the user to select from a few pre-made Feed Themes.  Gone are the days of fighting with the Facebook Social Plugin!

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the contents of `/ik-facebook/` to the `/wp-content/plugins/` directory
2. Activate the IK Facebook Plugin through the 'Plugins' menu in WordPress
3. Visit this address for information on how to configure the plugin: https://illuminatikarate.com/ik-facebook-plugin/

= Outputting the Feed =
* To output the Feed, place `[ik_fb_feed colorscheme="light or dark" use_thumb="true or false" width="250"]` in the body of a post, or use the Appearance section to add the The IK Facebook Plugin Widget to your Widgets area.  If 'use_thumb' is set to true, the value of 'width' will be ignored.  If 'use_thumb' or 'width' are not set, the values from the Options page will be used.
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

= Instead of a Like Button, all I see is "Error"! =

That probably means the URL you've given the Like Button is invalid.  Sometimes this happens in the feed widget, if the URL isn't a valid Facebook Page.

= Other people's posts are showing up on my wall!  How do I stop it? =

Visit our website to find out about premium options that are available: http://illuminatikarate.com/ik-facebook-plugin/

= Hey!  I need more control over the styling of my feed, but I don't know CSS! =

Visit our website to find out about premium options that are available: http://illuminatikarate.com/ik-facebook-plugin/

== Screenshots ==

1. This is the Settings page.

== Changelog ==

= 1.7 =
* Feature: Adds support for new Pro Features - Individual Font and Feed Styling Options.
* Feature: Adds option to display "Powered By" link.
* Fix: Adds logic to prevent outputting custom CSS multiple times in footer.

= 1.6.5 =
* Feature: Adds Option to Output "Posted By Author" in the feed.

= 1.6.4 =
* Feature: Adds Option to Limit Number Of Displayed Posts.

= 1.6.3 =
* Minor Update: Remove "on Facebook" hardcoded text from Feed Title.

= 1.6.2 =
* Fix: Properly Adds Blue Style.

= 1.6.1 =
* Feature: Adds Blue Style Pre-made Theme.

= 1.6 =
* Feature: Adds Optional Pre-made Feed Themes.

= 1.5.2 =
* Fix: fix order that Custom CSS is loaded to guarantee that the custom styles are used
* Fix: remove inline styling from Like Button and place it in CSS file, instead.

= 1.5.1 =
* Fix: fix issue where the graph data didn't contain a properly formed link, leading to an Error message being output instead of the Like Button.

= 1.5 = 
* Feature: Adds support for new Pro Feature - Custom HTML.
* Update: remove branding from Settings Updated message.
* Update: update method of building and outputting the feed.
* Update: Change some wording throughout.
* Fix: fix bad image output when URL to fullsized photo was not set.

= 1.4 = 
* Feature: Adds support for new Pro Feature - Unbranded Dashboard
* Update: Output options more efficiently

= 1.3.4 =
* Fix: output already started error.

= 1.3.3 =
* Fix: fix shortcode output always appearing at the top of the_content()

= 1.3.2 =
* Fix: Fix issue when The IK Facebook Plugin Pro package was deactivated.

= 1.3.1 =
* Readme Update

= 1.3 =
* Feature: Introduces support for the IK Social Plugin Pro, a premium enhancement plugin that provides additional useful features.
* Updates plugin to be compatible with WordPress 3.5.1.

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

= 1.7 =
New Features and Bug Fix Available