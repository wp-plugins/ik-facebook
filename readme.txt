=== IK Facebook Plugin ===
Contributors: richardgabriel
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=V7HR8DP4EJSYN
Tags: facebook, facebook feed, facebook embed, facebook feed widget, facebook feed embed, like button widget
Requires at least: 3.0.1
Tested up to: 3.6
Stable tag: 2.1.1
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

The IK Facebook Plugin is an easy-to-use plugin for adding a Custom Facebook Feed to a WordPress site, with a shortcode or widget.

== Description ==

The IK Facebook Plugin is an **easy-to-use** plugin that allows users to add a **custom Facebook Feed widget** to the sidebar, as a widget, or to embed the custom Facebook Feed widget into a Page or Post using the shortcode.  The IK Facebook Plugin also allows you to insert a Like Button widget into the Page, Post, or theme.

= The IK Facebook Plugin is a great plugin for many uses, including: =

* Adding SEO friendly content to your website
* Powering your blog with your Facebook Feed - avoid the need to maintain content in multiple locations!
* Embed a Custom Facebook Feed Widget in your Sidebar or Footer
* Styling a Custom Facebook Feed
* Adding a Facebook Like Button to your website, anywhere!
* and our quality documentation won't leave you hanging!

The IK Facebook Plugin includes options to set the Title of the custom Facebook Feed widget, whether or not to show the Like Button above the custom Facebook Feed widget, and whether or not to show the Profile Picture.  The IK Facebook Plugin supports both the Light and Dark color schemes for the Like Button widget.

The IK Facebook Plugin is a free version of [IK Social Pro](http://iksocialpro.com/ "IK Social Pro") - IK Social Pro is a professionally developed WordPress plugin that integrates your Facebook Feed into your WordPress website as a custom widget.  With the IK Facebook Plugin, you can easily add **Search Engine Optimization friendly** content to your website without extra effort -- no iframe means the content exists on your site and is crawlable by search engines like Google!  IK Social Pro supports additional facebook feed content to display in the widget, including Comments, Avatars, Number of Likes, and more!

The IK Facebook Plugin includes the option to set your own custom CSS for styling purposes or, if you prefer, the IK Facebook Plugin allows you to include a custom style sheet in your theme directory  -- either method is great for displaying a custom Facebok Feed widget. The IK Facebook Plugin also allows the user to select from a few pre-made Feed Themes, to help generate their custom Facebook Feed widget.  *Gone are the days of fighting with the Facebook Social Plugin!*

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the contents of `/ik-facebook/` to the `/wp-content/plugins/` directory
2. Activate the IK Facebook Plugin through the 'Plugins' menu in WordPress
3. [Click here](http://iksocialpro.com/installation-usage-instructions/configuration-options-and-instructions/ "Configuration Options and Instructions") for information on how to configure the plugin.

= Outputting the Feed =
* To output the custome Facebook Feed, place `[ik_fb_feed colorscheme="light or dark" use_thumb="true or false" width="250"]` in the body of a post, or use the Appearance section to add the The IK Facebook Plugin Widget to your Widgets area.  If 'use_thumb' is set to true, the value of 'width' will be ignored.  If 'use_thumb' or 'width' are not set, the values from the Options page will be used.
* You can also use the function `ik_fb_display_feed($colorscheme,$use_thumb,$width)` to display the custom facebook feed in your theme.

= Outputting the Like Button = 
* To output the Like Button, place `[ik_fb_like_button url="http://some_url" height"desired_iframe_height" colorscheme="light or dark"]` in the body of a post.
* You can also use the function `ik_fb_display_like_button($url_to_like,$height_of_iframe,$colorscheme)` to output a like button in your theme.

== Frequently Asked Questions ==

= Help!  I need an App ID/Secret Key! =

OK!  We have a great page with some helpful information [here](http://iksocialpro.com/installation-usage-instructions/how-to-get-an-app-id-and-secret-key-from-facebook/ "Configuration Options and Instructions").

Follow the information on that page to Create A Simple Facebook App - you'll be guided along the way to get your App ID, Secret Key, and any other info you may need.

= Ack!  All I see is 'IK FB: Please check your settings.' - what do I do? =

It's all good!  This just means there is no feed data - this could be due to bad settings, including a bad Page ID, App ID, or Secret Key, or it could be due to some other error such as not having cUrl installed.  Be sure to check your Facebook Page's Privacy Settings, too!  Check the plugin instructions for help (or send us a message if you think it's an error.)

= So what's up with this 'Publicly Accessible Page' thing? =

OK, so here's the deal:

Your Facebook Feed needs to come from a Publicly Accessible Facebook page.  If your page is Private, if its a Personal Profile and not a Page, or if you have an Age Limit set that thus requires the user to login, the plugin won't be able to display the feed data (it will instead just display the page title, like button, and profile pic - even that can be dependent upon your settings.)

Here's how to test if your page is Public or Private:

Logout of Facebook and then try to visit the Facebook Page in question.  If Facebook wants you to login to be able to view the Feed, then this page is not Publicly Accessible.  You just need to update the Page's relevant settings so that it is.

= I've set an image width on the options page, but it isn't working! =

No worries!  Did you include any non-integer characters?  Be sure the width is just something like "250" (ignore the quotes) - you don't need to include "px".

= Instead of a Like Button, all I see is "Error"! =

That probably means the URL you've given the Like Button is invalid.  Sometimes this happens in the feed widget, if the URL isn't a valid Facebook Page.

= Other people's posts are showing up on my wall!  How do I stop it? =

If you are using the Free version of the plugin, you'll need to [purchase IK Social Pro](http://iksocialpro.com/purchase-ik-social-pro/ "Purchase IK Social Pro") first.  Once installed, look for the option titled "Only Show Page Owner's Posts".  When that is checked, these posts will be hidden from view.

= Hey!  I need more control over the styling of my feed, but I don't know CSS! =

If you are using the Free version of the plugin, you'll need to [purchase IK Social Pro](http://iksocialpro.com/purchase-ik-social-pro/ "Purchase IK Social Pro") first.  Once installed, look for the long list of options under the heading "Display Options".  You will be able to use these to control font size and color for all of the different text elements, feed width and height for the in page and sidebar versions each, and more being added all the time!

= I see this plugin uses caching. Do I need to do anything for this? =

Nope!  Thanks to the WordPress Transient API, all you have to do is sit back and relax and we'll do the rest!

= I Want All Of My Posts Visible -- Not Contained In A Box! =

Try using the "No Style" theme -- this will output everything in a list.  You can also turn off the Like Button, Feed Title, and Profile Pic to have it look more like a list of posts.

== Screenshots ==

1. This is the Configuration Options Settings page.
2. This is the Style Options Settings page.  These settings help you control the visible appearance of your custom facebook feed.
3. This is the Display Options Settings page.  These settings help you control what content appears in your custom facebook feed.

== Changelog ==

= 2.1.1 =
* Minor Fix: addresses a spacing issue in the number of likes for comments.

= 2.1 =
* Feature: Adds ability to display comments in the feed.  Comments also support other IK Facebook options, such as Posted on Date, Avatars, and Number of Likes.

= 2.0 =
* Update: Upgraded feature set and interface to integrate IK Social Pro and IK Facebook.
* Feature: Adds ability to display user avatars in the feed.
* Feature: Adds ability to show number of comments in the feed.
* Feature: Adds ability to show number of likes in the feed.
* Minor Feature: Adds ability to disable pro styling options

= 1.9.1 =
* Update: Compatible with 3.6
* Update: Adjust Settings Output for New Pro Options

= 1.9 =
* Update: cleans up the layout of the Settings screen
* Update: reposition "Read More" text to a logical place

= 1.8.8 =
* Interface Updates
* Feature: Images in Feed Link to Full Sized Versions
* Feature: Updates Themes to include No Style and Default Style.  The No Style Theme includes no CSS at all -- everything fully inherits from your website.  The Default Theme includes basic styles to adjust the layout of the items of the feed - no colors are applied.
* Fix: No longer upsize thumbnails in the feed to larger than their starting size, when the options to fix the Feed Image height or width are set.

= 1.8.7 =
* Minor Updates

= 1.8.6 =
* Minor Change: Update some wording and buttons on the Settings screen.

= 1.8.5 =
* Feature: Adds Option to display date of post in feed.

= 1.8.4 =
* Feature: Option to Limit The Number Of Displayed Characters in Parts of Feed.  Read More is Displayed if Post is Shortened.

= 1.8.3 =
* Update: change default Feed Title link to go to Feed's FB Page

= 1.8.2 =
* Fix: addressed issue where some URLs were being output relative to the site URL, leading to some broken links.

= 1.8.1 =
* Feature: Adds option to hide Facebook Page Title from the top of the feed.

= 1.8 =
* Feature: Adds Request Caching using the WordPress Transient API, significantly decreasing page load time.
* Update some language on the settings screen, readme file, and screenshot.

= 1.7.1 =
* Feature: Adds option to set the Height of images that are displayed in the Feed.
* Fix: Change Page Title of Settings Page to 'Social Plugin Settings' instead of 'IK Facebook Plugin Settings', when unbranded option is enabled.
* Fix: Address typo on Settings page.

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

= 2.1 =
* New Pro Features Available