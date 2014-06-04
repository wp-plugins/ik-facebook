<?php
/*
This file is part of The IK Facebook Plugin .

The IK Facebook Plugin  is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

The IK Facebook Plugin  is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with The IK Facebook Plugin.  If not, see <http://www.gnu.org/licenses/>.
*/

class ikFacebookOptions
{
	var $textdomain = '';

	function __construct(){
		//may be running in non WP mode (for example from a notification)
		if(function_exists('add_action')){
			//add a menu item
			add_action('admin_menu', array($this, 'add_admin_menu_item'));		
		}
	}
	
	function add_admin_menu_item(){
		if(get_option('ik_fb_unbranded') && is_valid_key(get_option('ik_fb_pro_key'))){
			$title = __('Social Settings', $this->textdomain);
		} else {
			$title = __('IK FB Settings', $this->textdomain);
		}
		
		if(get_option('ik_fb_unbranded') && is_valid_key(get_option('ik_fb_pro_key'))){
			$page_title = __('Social Plugin Settings', $this->textdomain);
		} else {
			$page_title = __('IK Facebook Plugin Settings', $this->textdomain);
		}
		
		//create new top-level menu
		add_menu_page($page_title, $title, 'administrator', __FILE__, array($this, 'settings_page'));

		//call register settings function
		add_action( 'admin_init', array($this, 'register_settings'));	
	}
	
	//function to produce tabs on admin screen
	function ik_fb_admin_tabs( $current = 'homepage' ) {
	
		$tabs = array( 'plugin_status' => __('Plugin Status &amp; Help', $this->textdomain), 'config_options' => __('Configuration Options', $this->textdomain), 'style_options' => __('Style Options', $this->textdomain), 'display_options' => __('Display Options', $this->textdomain), 'pro_options' => __('Pro Options', $this->textdomain) );
		echo '<div id="icon-themes" class="icon32"><br></div>';
		echo '<h2 class="nav-tab-wrapper">';
			foreach( $tabs as $tab => $name ){
				$class = ( $tab == $current ) ? ' nav-tab-active' : '';
				echo "<a class='nav-tab$class' href='?page=ik-facebook/include/ik_facebook_options.php&tab=$tab'>$name</a>";
			}
		echo '</h2>';
	}
	
	//register our settings
	function register_settings(){
		//register our config settings
		register_setting( 'ik-fb-config-settings-group', 'ik_fb_page_id' );
		register_setting( 'ik-fb-config-settings-group', 'ik_fb_app_id' );
		register_setting( 'ik-fb-config-settings-group', 'ik_fb_secret_key' );
		register_setting( 'ik-fb-config-settings-group', 'ik_fb_pro_key' );
		register_setting( 'ik-fb-config-settings-group', 'ik_fb_pro_url' );
		register_setting( 'ik-fb-config-settings-group', 'ik_fb_pro_email' );
		
		//register our style settings
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_custom_css' );
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_fix_feed_image_width' );
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_feed_image_width' );
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_fix_feed_image_height' );
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_feed_image_height' );
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_feed_theme' );		
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_header_bg_color' );
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_window_bg_color' );		
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_powered_by_font_color' );		
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_powered_by_font_size' );	
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_posted_by_font_color' );				
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_posted_by_font_size' );	
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_date_font_color' );				
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_date_font_size' );		
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_description_font_color' );		
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_description_font_size' );		
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_link_font_color' );		
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_link_font_size' );		
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_feed_window_height' );		
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_feed_window_width' );
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_font_color' );
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_font_size' );
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_sidebar_feed_window_height' );
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_sidebar_feed_window_width' );
		register_setting( 'ik-fb-style-settings-group', 'other_ik_fb_feed_window_width' );
		register_setting( 'ik-fb-style-settings-group', 'other_ik_fb_feed_image_width' );
		register_setting( 'ik-fb-style-settings-group', 'other_ik_fb_feed_image_height' );
		register_setting( 'ik-fb-style-settings-group', 'other_ik_fb_feed_window_height' );
		register_setting( 'ik-fb-style-settings-group', 'other_ik_fb_sidebar_feed_window_height' );
		register_setting( 'ik-fb-style-settings-group', 'other_ik_fb_sidebar_feed_window_width' );
		
		//register our display settings
		register_setting( 'ik-fb-display-settings-group', 'ik_fb_hide_feed_images' );
		register_setting( 'ik-fb-display-settings-group', 'ik_fb_show_like_button' );
		register_setting( 'ik-fb-display-settings-group', 'ik_fb_show_only_events' );
		register_setting( 'ik-fb-display-settings-group', 'ik_fb_show_profile_picture' );
		register_setting( 'ik-fb-display-settings-group', 'ik_fb_show_page_title' );
		register_setting( 'ik-fb-display-settings-group', 'ik_fb_show_posted_by' );
		register_setting( 'ik-fb-display-settings-group', 'ik_fb_show_date' );
		register_setting( 'ik-fb-display-settings-group', 'ik_fb_date_format' );
		register_setting( 'ik-fb-display-settings-group', 'ik_fb_use_human_timing' );
		register_setting( 'ik-fb-display-settings-group', 'ik_fb_feed_limit' );
		register_setting( 'ik-fb-display-settings-group', 'ik_fb_photo_feed_limit' );
		register_setting( 'ik-fb-display-settings-group', 'ik_fb_powered_by' );
		register_setting( 'ik-fb-display-settings-group', 'ik_fb_character_limit' );
		register_setting( 'ik-fb-display-settings-group', 'ik_fb_description_character_limit' );
		register_setting( 'ik-fb-display-settings-group', 'ik_fb_caption_character_limit' );
		register_setting( 'ik-fb-display-settings-group', 'ik_fb_link_photo_to_feed_item' );
		
		//register any pro settings
		if(function_exists("ik_fb_pro_register_settings")){
			ik_fb_pro_register_settings();
		}
	}

	function settings_page(){
		global $pagenow;
		
		if(get_option('ik_fb_unbranded') && is_valid_key(get_option('ik_fb_pro_key'))){
			$title = __("Facebook Settings", $this->textdomain);
			$message = __("Facebook Settings Updated.", $this->textdomain);
		} else {
			$title = __("IK Facebook Plugin Settings.", $this->textdomain);
			$message = __("IK Facebook Plugin Settings Updated.", $this->textdomain);
		}
		
	?>
	<div class="wrap">
		<h2><?php echo $title; ?></h2>		
		
		<?php if(!is_valid_key(get_option('ik_fb_pro_key') )): ?>			
				<!-- Begin MailChimp Signup Form -->
			<style type="text/css">
				/* MailChimp Form Embed Code - Slim - 08/17/2011 */
				#mc_embed_signup form {display:block; position:relative; text-align:left; padding:10px 0 10px 3%}
				#mc_embed_signup h2 {font-weight:bold; padding:0; margin:15px 0; font-size:1.4em;}
				#mc_embed_signup input {border:1px solid #999; -webkit-appearance:none;}
				#mc_embed_signup input[type=checkbox]{-webkit-appearance:checkbox;}
				#mc_embed_signup input[type=radio]{-webkit-appearance:radio;}
				#mc_embed_signup input:focus {border-color:#333;}
				#mc_embed_signup .button {clear:both; background-color: #aaa; border: 0 none; border-radius:4px; color: #FFFFFF; cursor: pointer; display: inline-block; font-size:15px; font-weight: bold; height: 32px; line-height: 32px; margin: 0 5px 10px 0; padding:0; text-align: center; text-decoration: none; vertical-align: top; white-space: nowrap; width: auto;}
				#mc_embed_signup .button:hover {background-color:#777;}
				#mc_embed_signup .small-meta {font-size: 11px;}
				#mc_embed_signup .nowrap {white-space:nowrap;}     
				#mc_embed_signup .clear {clear:none; display:inline;}

				#mc_embed_signup h3 { color: #008000; display:block; font-size:19px; padding-bottom:10px; font-weight:bold; margin: 0 0 10px;}
				#mc_embed_signup .explain {
					color: #808080;
					width: 600px;
				}
				#mc_embed_signup label {
					color: #000000;
					display: block;
					font-size: 15px;
					font-weight: bold;
					padding-bottom: 10px;
				}
				#mc_embed_signup input.email {display:block; padding:8px 0; margin:0 4% 10px 0; text-indent:5px; width:58%; min-width:130px;}

				#mc_embed_signup div#mce-responses {float:left; top:-1.4em; padding:0em .5em 0em .5em; overflow:hidden; width:90%;margin: 0 5%; clear: both;}
				#mc_embed_signup div.response {margin:1em 0; padding:1em .5em .5em 0; font-weight:bold; float:left; top:-1.5em; z-index:1; width:80%;}
				#mc_embed_signup #mce-error-response {display:none;}
				#mc_embed_signup #mce-success-response {color:#529214; display:none;}
				#mc_embed_signup label.error {display:block; float:none; width:auto; margin-left:1.05em; text-align:left; padding:.5em 0;}		
				#mc_embed_signup{background:#fff; clear:left; font:14px Helvetica,Arial,sans-serif; }
					#mc_embed_signup{    
							background-color: white;
							border: 1px solid #DCDCDC;
							clear: left;
							color: #008000;
							font: 14px Helvetica,Arial,sans-serif;
							margin-top: 10px;
							margin-bottom: 0px;
							max-width: 800px;
							padding: 5px 12px 0px;
				}
				#mc_embed_signup form{padding: 10px}

				#mc_embed_signup .special-offer {
					color: #808080;
					margin: 0;
					padding: 0 0 3px;
					text-transform: uppercase;
				}
				#mc_embed_signup .button {
				  background: #5dd934;
				  background-image: -webkit-linear-gradient(top, #5dd934, #549e18);
				  background-image: -moz-linear-gradient(top, #5dd934, #549e18);
				  background-image: -ms-linear-gradient(top, #5dd934, #549e18);
				  background-image: -o-linear-gradient(top, #5dd934, #549e18);
				  background-image: linear-gradient(to bottom, #5dd934, #549e18);
				  -webkit-border-radius: 5;
				  -moz-border-radius: 5;
				  border-radius: 5px;
				  font-family: Arial;
				  color: #ffffff;
				  font-size: 20px;
				  padding: 10px 20px 10px 20px;
				  line-height: 1.5;
				  height: auto;
				  margin-top: 7px;
				  text-decoration: none;
				}

				#mc_embed_signup .button:hover {
				  background: #65e831;
				  background-image: -webkit-linear-gradient(top, #65e831, #5dd934);
				  background-image: -moz-linear-gradient(top, #65e831, #5dd934);
				  background-image: -ms-linear-gradient(top, #65e831, #5dd934);
				  background-image: -o-linear-gradient(top, #65e831, #5dd934);
				  background-image: linear-gradient(to bottom, #65e831, #5dd934);
				  text-decoration: none;
				}
				#signup_wrapper {
					max-width: 800px;
					margin-bottom: 20px;
				}
				#signup_wrapper .u_to_p
				{
					font-size: 10px;
					margin: 0;
					padding: 2px 0 0 3px;				
				}
			</style>				
			<div id="signup_wrapper">
				<div id="mc_embed_signup">
					<form action="http://illuminatikarate.us2.list-manage1.com/subscribe/post?u=403e206455845b3b4bd0c08dc&amp;id=3e22ddb309" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
						<p class="special-offer">Special Offer:</p>
						<h3><?php _e("Sign-up for our mailing list now, and we'll give you a discount on WP Social Pro!", $this->textdomain); ?></h3>
						<label for="mce-EMAIL">Your Email:</label>
						<input type="email" value="" name="EMAIL" class="email" id="mce-EMAIL" placeholder="email address" required>
						<!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
						<div style="position: absolute; left: -5000px;"><input type="text" name="b_403e206455845b3b4bd0c08dc_6ad78db648" tabindex="-1" value=""></div>
						<div class="clear"><input type="submit" value="Subscribe Now" name="subscribe" id="mc-embedded-subscribe" class="button"></div>
						<p class="explain"><strong><?php _e("What To Expect:", $this->textdomain); ?></strong> <?php _e("You'll receive you around one email from us each month, jam-packed with special offers and tips for getting the most out of WordPress. Of course, you can unsubscribe at any time.", $this->textdomain); ?></p>
					</form>
				</div>
				<p class="u_to_p"><a href="http://goldplugins.com/our-plugins/wp-social-pro/#buy_now"><?php _e("Upgrade to WP Social Pro now</a> to remove banners like this one.", $this->textdomain); ?></p>
			</div>
			
		<?php endif; ?>
	
		<?php if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') : ?>
		<div id="message" class="updated fade"><p><?php echo $message; ?></p></div>
		<?php endif; ?>	
		
		<?php if ( isset ( $_GET['tab'] ) ) $this->ik_fb_admin_tabs($_GET['tab']); else $this->ik_fb_admin_tabs('config_options'); ?>
		<?php 
			if ( $pagenow == 'admin.php' && $_GET['page'] == 'ik-facebook/include/ik_facebook_options.php' ){
				if ( isset ( $_GET['tab'] ) ) $tab = $_GET['tab'];
				else $tab = 'config_options';
			} 
		?>
			<?php if($tab != 'plugin_status'): ?>
			<form method="post" action="options.php">
			<?php endif; ?>
				
			<?php 			
				switch ( $tab ){
					case 'plugin_status' :	
			?>
				<style>p{font-size:14px;}td iframe{height:45px;}ol{padding-top:10px;}em{font-size:12px;}.ik_fb_error{color:red;}</style>
				<h3><?php _e('Plugin Status &amp; Help');?></h3>
				<p><?php _e('This page is used to determine if your plugin and page are setup correctly.  Use the below items to help troubleshoot any issues you may have and to see example shortcodes.');?></p>
			<?php
				//example shortcodes
				
				_e("<h4>Example Shortcodes</h4>");
				_e('<p>To output the custom Facebook Feed, place <code>[ik_fb_feed]</code> in the body of a post.  To further customize the feed via the shortcode, available attributes include: <code>colorscheme="light" use_thumb="true" width="250" num_posts="5" id="123456789"</code>.</p>');
				_e('<p><em>Valid choices for "colorscheme" are "light" and "dark". If "use_thumb" is set to true, the value of "width" will be ignored.  If "use_thumb" or "width" are not set, the values from the Options page will be used.  If id is not set, the shortcode will use the Page ID from your Settings page.</em></p>');
				_e('<p>To output the Like Button, place <code>[ik_fb_like_button url="http://www.facebook.com"]</code> in the body of a post.  Valid attributes include: <code>url="" height="" colorscheme="light"</code>.</p>');
				_e('<p><em>Valid options for colorscheme are "light" and "dark".  Valid values for height are integers.  URL must be a valid website URL.</em></p>');
				_e('<p>To output a Photo Gallery, place <code>[ik_fb_gallery id="539627829386059" num_photos="25" size="130x73" title="Hello World!"]</code> in the body of a post.</p>');
				_e('<p><em>If no size is passed, it will default to 320 x 180.  Size options are 2048x1152, 960x540, 720x405, 600x337, 480x270, 320x180, and 130x73.  If num_photos is not passed, the Gallery will default to the amount set on the Dashboard - if no amount is set there, it will display up to 25 photos.  The ID number is found by looking at the URL of the link to the Album on Facebook - you can read more on our FAQs <a href="http://goldplugins.com/documentation/wp-social-pro-documentation/frequently-asked-questions/">here</a>.</em></p>');
								
				_e("<h4>Configuration Settings</h4>");
				_e("<p>If you need to contact us for help, please be sure to include these settings in your message, as well as a functional description of how you have the feed implemented on your site.</p>");
				echo "<table><tbody>";
				_e("<tr><td align='right'>Page ID:</td><td>" . get_option("ik_fb_page_id") . "</td></tr>");
				_e("<tr><td align='right'>App ID:</td><td>" . get_option("ik_fb_app_id") . "</td></tr>");
				_e("<tr><td align='right'>Secret Key:</td><td>" . get_option("ik_fb_secret_key") . "</td></tr>");
				echo "</tbody></table>";
				
				_e("<h3>Plugin Settings Test</h3>");
				_e("<p>Use the below feeds to determine if your settings are correct.</p>");
				_e("<strong>How to use:</strong>");
				echo "<ol>";
				_e("<li>If both feeds are showing up, everything is working!  Hooray!</li>");
				_e("<li>If neither feed is showing up then your App ID or Secret Key is incorrect.  Please verify you have entered the correct information.</li>");
				_e("<li>If our feed is showing up, but your feed is not, then either:");
					_e("<ol><li>Your Page ID is Incorrect.  Please verify you have entered the correct information.</li>");
					_e("<li>Your Page is not configured to be publically viewable.  Please verify that you are using a Facebook Page, not a Personal Profile, and that the page has no Country, Age, or other restrictions placed on it.</li></ol></li></ol>");
				
				echo "<table><tbody>";
				_e("<tr><td><h4>Our Feed</h4></td><td><h4>Your Feed</h4></td></tr>");
				echo "<tr><td valign='top'>" . do_shortcode('[ik_fb_feed show_errors="1" id="IlluminatiKarate" num_posts="2"]') . "</td><td valign='top'>" . do_shortcode('[ik_fb_feed show_errors="1" num_posts="2"]') . "</td></tr>";
				echo "</tbody></table>";
			?>
			<?php
					break;
					case 'config_options' :	
			?>
			<?php settings_fields( 'ik-fb-config-settings-group' ); ?>
			
			<h3><?php _e("Configuration Options");?></h3>
			<p><?php _e("The below options are used to configure your plugin to interact with your Facebook Page.");?></p>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="ik_fb_page_id"><?php _e("Page ID");?></label></th>
					<td><input type="text" name="ik_fb_page_id" id="ik_fb_page_id" value="<?php echo get_option('ik_fb_page_id'); ?>"  style="width: 250px" />
					<p class="description"><?php _e("This is your Username or Page ID.  For example, ours is IlluminatiKarate (the end of our Facebook Page URL), but some people's maybe an Integer (such as 199789123).  It depends on if you have a username.");?></p>
					</td>
				</tr>
			</table>

			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="ik_fb_app_id"><?php _e("Facebook App ID");?></label></th>
					<td><input type="text" name="ik_fb_app_id" id="ik_fb_app_id" value="<?php echo get_option('ik_fb_app_id'); ?>" style="width: 250px" />
					<p class="description"><?php _e('This is the App ID you acquired when you <a href="http://goldplugins.com/documentation/wp-social-pro-documentation/how-to-get-an-app-id-and-secret-key-from-facebook/" target="_blank" title="How To Get An App ID and Secret Key From Facebook">setup your Facebook app</a>.');?></p></td>
				</tr>
			</table>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="ik_fb_secret_key"><?php _e("Facebook Secret Key");?></label></th>
					<td><input type="text" name="ik_fb_secret_key" id="ik_fb_secret_key" value="<?php echo get_option('ik_fb_secret_key'); ?>" style="width: 250px" />
					<p class="description"><?php _e('This is the App Secret you acquired when you <a href="http://goldplugins.com/documentation/wp-social-pro-documentation/how-to-get-an-app-id-and-secret-key-from-facebook/" target="_blank" title="How To Get An App ID and Secret Key From Facebook">setup your Facebook app</a>.');?></p></td>
				</tr>
			</table>	
			
				<?php
					break;
					case 'style_options' :					
						$ikfb_themes = array(
							'style' => 'Default Style',
							'dark_style' => 'Dark Style',
							'light_style' => 'Light Style',
							'blue_style' => 'Blue Style',
							'no_style' => 'No Style',
						);
						
						if(is_valid_key(get_option('ik_fb_pro_key'))){
							$ikfb_themes['cobalt_style'] = 'Cobalt Style';
							$ikfb_themes['green_gray_style'] = 'Green Gray Style';
							$ikfb_themes['halloween_style'] = 'Halloween Style';
							$ikfb_themes['indigo_style'] = 'Indigo Style';
							$ikfb_themes['orange_style'] = 'Orange Style';			
						}
				?>
				<?php settings_fields( 'ik-fb-style-settings-group' ); ?>
			
			<h3><?php _e('Style Options');?></h3>
			<p><?php _e('The below options are used to modify, or fully change, the style of your Facebook Feed displayed on your website.');?></p>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="ik_fb_feed_theme"><?php _e('Feed Theme');?></a></th>
					<td>
						<select name="ik_fb_feed_theme" id="ik_fb_feed_theme">	
							<?php foreach($ikfb_themes as $value => $name): ?>
							<option value="<?php echo $value; ?>" <?php if(get_option('ik_fb_feed_theme') == $value): echo 'selected="SELECTED"'; endif; ?>><?php echo $name; ?></option>
							<?php endforeach; ?>
						</select>
						<p class="description"><?php _e("Select which theme you want to use.  If 'No Style' is selected, only your Theme's CSS, and any Custom CSS you've added, will be used.  The settings below will override the defaults set in your selected theme.");?></p>
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><label for="ik_fb_custom_css"><?php _e('Custom CSS');?></a></th>
					<td><textarea name="ik_fb_custom_css" id="ik_fb_custom_css" style="width: 100%; height: 250px;"><?php echo get_option('ik_fb_custom_css'); ?></textarea>
					<p class="description"><?php _e("Input any Custom CSS you want to use here.  You can also include a file in your theme's folder called 'ik_fb_custom_style.css' - any styles in that file will be loaded with the plugin.  The plugin will work without you placing anything here - this is useful in case you need to edit any styles for it to work with your theme, though.");?></p></td>
				</tr>
				
				<tr><td colspan=2><h4><?php _e('Feed Images');?></h4></td></tr>
				
				<tr valign="top">
					<th scope="row"><label for="ik_fb_fix_feed_image_width"><?php _e('Fix Feed Image Width');?></label></th>
					<td><input type="checkbox" name="ik_fb_fix_feed_image_width" id="ik_fb_fix_feed_image_width" value="1" <?php if(get_option('ik_fb_fix_feed_image_width')){ ?> checked="CHECKED" <?php } ?>/>
					<p class="description"><?php _e("If checked, images inside the feed will all be displayed at the width set below.  If both this and 'Fix Feed Image Height' are unchecked, feed will display image thumbnails.");?></p></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="ik_fb_feed_image_width"><?php _e('Feed Image Width');?></label></th>
					<td>
					
					<input type="radio" name="ik_fb_feed_image_width" id="ik_fb_feed_image_width" <?php if(get_option('ik_fb_feed_image_width')=="100%"){ ?> checked="CHECKED" <?php } ?> value="100%"> 100%<br>
					<input type="radio" name="ik_fb_feed_image_width" id="ik_fb_feed_image_width"  <?php if(get_option('ik_fb_feed_image_width')=="OTHER"){ ?> checked="CHECKED" <?php } ?> value="OTHER"> Other Pixel Value <input type="text" style="width: 250px" value="<?php echo get_option('other_ik_fb_feed_image_width'); ?>" name="other_ik_fb_feed_image_width" />
									
					<p class="description"><?php _e("If 'Fix Feed Image Width' is checked, the images will be set to this width.  Choose '100%' or 'Other' and type in an integer number of pixels.  The effect of this setting may vary, based upon your theme's CSS.");?></p></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="ik_fb_fix_feed_image_height"><?php _e('Fix Feed Image Height');?></label></th>
					<td><input type="checkbox" name="ik_fb_fix_feed_image_height" id="ik_fb_fix_feed_image_height" value="1" <?php if(get_option('ik_fb_fix_feed_image_height')){ ?> checked="CHECKED" <?php } ?>/>
					<p class="description"><?php _e("If checked, images inside the feed will all be displayed at the height set below.  If both this and 'Fix Feed Image Width' are unchecked, feed will display image thumbnails.");?></p></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="ik_fb_feed_image_height"><?php _e('Feed Image Height');?></label></th>
					<td>
					
					<input type="radio" name="ik_fb_feed_image_height" id="ik_fb_feed_image_height" <?php if(get_option('ik_fb_feed_image_height')=="100%"){ ?> checked="CHECKED" <?php } ?> value="100%"> 100%<br>
					<input type="radio" name="ik_fb_feed_image_height" id="ik_fb_feed_image_height"  <?php if(get_option('ik_fb_feed_image_height')=="OTHER"){ ?> checked="CHECKED" <?php } ?> value="OTHER"> Other Pixel Value <input type="text" style="width: 250px" value="<?php echo get_option('other_ik_fb_feed_image_height'); ?>" name="other_ik_fb_feed_image_height" />
					
					<p class="description"><?php _e("If 'Fix Feed Image Height' is checked, the images will be set to this width.  Choose '100%' or 'Other' and type in an integer number of pixels.  The effect of this setting may vary, based upon your theme's CSS.");?></p></td>
				</tr>
				
				<tr><td colspan=2><h4><?php _e('Feed Window Color and Dimensions');?></h4></td></tr>
				
				<tr valign="top">
					<th scope="row"><label for="ik_fb_header_bg_color"><?php _e('Feed Header Background Color');?></label></th>
					<td>
					<div class="color-picker" style="position: relative;">
						<input type="text" name="ik_fb_header_bg_color" id="ik_fb_header_bg_color" value="<?php echo strlen(get_option('ik_fb_header_bg_color'))>2 ? get_option('ik_fb_header_bg_color') : ' '; ?>" class="color" />
						<div style="z-index: 100; background: none repeat scroll 0% 0% rgb(238, 238, 238); border: 1px solid rgb(204, 204, 204); position: absolute;" class="colorpicker"></div>
					</div>		
					
					<p class="description"><?php _e('Input your hex color code, by clicking and using the Colorpicker or typing it in.  Erase the contents of this field to use the default color.');?></p></td>
				</tr>
				
				<tr valign="top">
					<div class="color-picker" style="position: relative;">					
					<th scope="row"><label for="ik_fb_window_bg_color"><?php _e('Feed Window Background Color');?></label></th>
					<td>				
					<div class="color-picker" style="position: relative;">
						<input type="text" name="ik_fb_window_bg_color" id="ik_fb_window_bg_color" value="<?php echo strlen(get_option('ik_fb_window_bg_color'))>2 ? get_option('ik_fb_window_bg_color') : ' '; ?>" class="color" />
						<div style="z-index: 100; background: none repeat scroll 0% 0% rgb(238, 238, 238); border: 1px solid rgb(204, 204, 204); position: absolute;" class="colorpicker"></div>
					</div>		
					<p class="description"><?php _e('Input your hex color code, by clicking and using the Colorpicker or typing it in.  Erase the contents of this field to use the default color.');?></p></td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><label for="ik_fb_feed_window_height"><?php _e('Feed Window Height');?></label></th>
					<td>
					
					<input type="radio" name="ik_fb_feed_window_height" id="ik_fb_feed_window_height" <?php if(get_option('ik_fb_feed_window_height')==""){ ?> checked="CHECKED" <?php } ?> value=""> Default<br>
					<input type="radio" name="ik_fb_feed_window_height" id="ik_fb_feed_window_height" <?php if(get_option('ik_fb_feed_window_height')=="auto"){ ?> checked="CHECKED" <?php } ?> value="auto"> Auto<br>
					<input type="radio" name="ik_fb_feed_window_height" id="ik_fb_feed_window_height" <?php if(get_option('ik_fb_feed_window_height')=="100%"){ ?> checked="CHECKED" <?php } ?> value="100%"> 100%<br>
					<input type="radio" name="ik_fb_feed_window_height" id="ik_fb_feed_window_height"  <?php if(get_option('ik_fb_feed_window_height')=="OTHER"){ ?> checked="CHECKED" <?php } ?> value="OTHER"> Other Pixel Value <input type="text" style="width: 250px" value="<?php echo get_option('other_ik_fb_feed_window_height'); ?>" name="other_ik_fb_feed_window_height" />
					
					<p class="description"><?php _e("Choose 'Auto', '100%', or 'Other' and type in an integer number of pixels. The effect of this setting may vary, based upon your theme's CSS. This option does not apply to the sidebar widget.");?></p></td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><label for="ik_fb_feed_window_width"><?php _e('Feed Window Width');?></label></th>
					<td>
					
					<input type="radio" name="ik_fb_feed_window_width" id="ik_fb_feed_window_width" <?php if(get_option('ik_fb_feed_window_width')==""){ ?> checked="CHECKED" <?php } ?> value=""> Default<br>
					<input type="radio" name="ik_fb_feed_window_width" id="ik_fb_feed_window_width" <?php if(get_option('ik_fb_feed_window_width')=="auto"){ ?> checked="CHECKED" <?php } ?> value="auto"> Auto<br>
					<input type="radio" name="ik_fb_feed_window_width" id="ik_fb_feed_window_width" <?php if(get_option('ik_fb_feed_window_width')=="100%"){ ?> checked="CHECKED" <?php } ?> value="100%"> 100%<br>
					<input type="radio" name="ik_fb_feed_window_width" id="ik_fb_feed_window_width"  <?php if(get_option('ik_fb_feed_window_width')=="OTHER"){ ?> checked="CHECKED" <?php } ?> value="OTHER"> Other Pixel Value <input type="text" style="width: 250px" value="<?php echo get_option('other_ik_fb_feed_window_width'); ?>" name="other_ik_fb_feed_window_width" />
					
					<p class="description"><?php _e("Choose 'Auto', '100%', or 'Other' and type in an integer number of pixels. The effect of this setting may vary, based upon your theme's CSS. This option does not apply to the sidebar widget.");?></p></td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><label for="ik_fb_sidebar_feed_window_height"><?php _e('Sidebar Feed Window Height');?></label></th>
					<td>
					
					<input type="radio" name="ik_fb_sidebar_feed_window_height" id="ik_fb_sidebar_feed_window_height" <?php if(get_option('ik_fb_sidebar_feed_window_height')==""){ ?> checked="CHECKED" <?php } ?> value=""> Default<br>
					<input type="radio" name="ik_fb_sidebar_feed_window_height" id="ik_fb_sidebar_feed_window_height" <?php if(get_option('ik_fb_sidebar_feed_window_height')=="auto"){ ?> checked="CHECKED" <?php } ?> value="auto"> Auto<br>
					<input type="radio" name="ik_fb_sidebar_feed_window_height" id="ik_fb_sidebar_feed_window_height" <?php if(get_option('ik_fb_sidebar_feed_window_height')=="100%"){ ?> checked="CHECKED" <?php } ?> value="100%"> 100%<br>
					<input type="radio" name="ik_fb_sidebar_feed_window_height" id="ik_fb_sidebar_feed_window_height"  <?php if(get_option('ik_fb_sidebar_feed_window_height')=="OTHER"){ ?> checked="CHECKED" <?php } ?> value="OTHER"> Other Pixel Value <input type="text" style="width: 250px" value="<?php echo get_option('other_ik_fb_sidebar_feed_window_height'); ?>" name="other_ik_fb_sidebar_feed_window_height" />
					
					<p class="description"><?php _e("Choose 'Auto', '100%', or 'Other' and type in an integer number of pixels. The effect of this setting may vary, based upon your theme's CSS.");?></p></td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><label for="ik_fb_sidebar_feed_window_width"><?php _e('Sidebar Feed Window Width');?></label></th>
					<td>
					
					<input type="radio" name="ik_fb_sidebar_feed_window_width" id="ik_fb_sidebar_feed_window_width" <?php if(get_option('ik_fb_sidebar_feed_window_width')==""){ ?> checked="CHECKED" <?php } ?> value=""> Default<br>
					<input type="radio" name="ik_fb_sidebar_feed_window_width" id="ik_fb_sidebar_feed_window_width" <?php if(get_option('ik_fb_sidebar_feed_window_width')=="auto"){ ?> checked="CHECKED" <?php } ?> value="auto"> Auto<br>
					<input type="radio" name="ik_fb_sidebar_feed_window_width" id="ik_fb_sidebar_feed_window_width" <?php if(get_option('ik_fb_sidebar_feed_window_width')=="100%"){ ?> checked="CHECKED" <?php } ?> value="100%"> 100%<br>
					<input type="radio" name="ik_fb_sidebar_feed_window_width" id="ik_fb_sidebar_feed_window_width"  <?php if(get_option('ik_fb_sidebar_feed_window_width')=="OTHER"){ ?> checked="CHECKED" <?php } ?> value="OTHER"> Other Pixel Value <input type="text" style="width: 250px" value="<?php echo get_option('other_ik_fb_sidebar_feed_window_width'); ?>" name="other_ik_fb_sidebar_feed_window_width" />
					
					<p class="description"><?php _e("Choose 'Auto', '100%', or 'Other' and type in an integer number of pixels. The effect of this setting may vary, based upon your theme's CSS.");?></p></td>
				</tr>
				
				<tr><td colspan=2><h4><?php _e('Font Styling');?></h4></td></tr>
			
				<tr valign="top">
					<th scope="row"><label for="ik_fb_description_font_color"><?php _e('Description Font Color');?></label></th>
					<td>
					<div class="color-picker" style="position: relative;">				
						<input type="text" name="ik_fb_description_font_color" id="ik_fb_description_font_color" value="<?php echo strlen(get_option('ik_fb_description_font_color'))>2 ? get_option('ik_fb_description_font_color') : ' '; ?>" class="color" />
						<div style="z-index: 100; background: none repeat scroll 0% 0% rgb(238, 238, 238); border: 1px solid rgb(204, 204, 204); position: absolute;" class="colorpicker"></div>
					</div>						
					<p class="description"><?php _e('Input your hex color code, by clicking and using the Colorpicker or typing it in.  Erase the contents of this field to use the default color.');?></p></td>
				</tr>
			
				<tr valign="top">
					<th scope="row"><label for="ik_fb_description_font_size"><?php _e('Description Font Size');?></label></th>
					<td><input type="text" name="ik_fb_description_font_size" id="ik_fb_description_font_size" value="<?php echo get_option('ik_fb_description_font_size'); ?>" style="width: 250px" />
					<p class="description"><?php _e('Input your font pixel size.');?></p></td>
				</tr>
			
				<tr valign="top">
					<th scope="row"><label for="ik_fb_font_color"><?php _e('Message Font Color');?></label></th>
					<td>
					<div class="color-picker" style="position: relative;">				
						<input type="text" name="ik_fb_font_color" id="ik_fb_font_color" value="<?php echo strlen(get_option('ik_fb_font_color'))>2 ? get_option('ik_fb_font_color') : ' '; ?>" class="color" />
						<div style="z-index: 100; background: none repeat scroll 0% 0% rgb(238, 238, 238); border: 1px solid rgb(204, 204, 204); position: absolute;" class="colorpicker"></div>
					</div>						
					<p class="description"><?php _e('Input your hex color code, by clicking and using the Colorpicker or typing it in.  Erase the contents of this field to use the default color.');?></p></td>
				</tr>
			
				<tr valign="top">
					<th scope="row"><label for="ik_fb_font_size"><?php _e('Message Font Size');?></label></th>
					<td><input type="text" name="ik_fb_font_size" id="ik_fb_font_size" value="<?php echo get_option('ik_fb_font_size'); ?>" style="width: 250px" />
					<p class="description"><?php _e('Input your font pixel size.');?></p></td>
				</tr>
			
				<tr valign="top">
					<th scope="row"><label for="ik_fb_link_font_color"><?php _e('Link Font Color');?></label></th>
					<td>
					<div class="color-picker" style="position: relative;">				
						<input type="text" name="ik_fb_link_font_color" id="ik_fb_link_font_color" value="<?php echo strlen(get_option('ik_fb_link_font_color'))>2 ? get_option('ik_fb_link_font_color') : ' '; ?>" class="color" />
						<div style="z-index: 100; background: none repeat scroll 0% 0% rgb(238, 238, 238); border: 1px solid rgb(204, 204, 204); position: absolute;" class="colorpicker"></div>
					</div>						
					<p class="description"><?php _e('Input your hex color code, by clicking and using the Colorpicker or typing it in.  Erase the contents of this field to use the default color.');?></p></td>
				</tr>
			
				<tr valign="top">
					<th scope="row"><label for="ik_fb_link_font_size"><?php _e('Link Font Size');?></label></th>
					<td><input type="text" name="ik_fb_link_font_size" id="ik_fb_link_font_size" value="<?php echo get_option('ik_fb_link_font_size'); ?>" style="width: 250px" />
					<p class="description"><?php _e('Input your font pixel size.');?></p></td>
				</tr>			
			
				<tr valign="top">
					<th scope="row"><label for="ik_fb_posted_by_font_color"><?php _e('Posted By Font Color');?></label></th>
					<td>
					<div class="color-picker" style="position: relative;">				
						<input type="text" name="ik_fb_posted_by_font_color" id="ik_fb_posted_by_font_color" value="<?php echo strlen(get_option('ik_fb_posted_by_font_color'))>2 ? get_option('ik_fb_posted_by_font_color') : ' '; ?>" class="color" />
						<div style="z-index: 100; background: none repeat scroll 0% 0% rgb(238, 238, 238); border: 1px solid rgb(204, 204, 204); position: absolute;" class="colorpicker"></div>
					</div>						
					<p class="description"><?php _e('Input your hex color code, by clicking and using the Colorpicker or typing it in.  Erase the contents of this field to use the default color.');?></p></td>
				</tr>
			
				<tr valign="top">
					<th scope="row"><label for="ik_fb_date_font_size"><?php _e('Date Font Size');?></label></th>
					<td><input type="text" name="ik_fb_date_font_size" id="ik_fb_date_font_size" value="<?php echo get_option('ik_fb_date_font_size'); ?>" style="width: 250px" />
					<p class="description"><?php _e('Input your font pixel size.');?></p></td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><label for="ik_fb_date_font_color"><?php _e('Date Font Color');?></label></th>
					<td>
					<div class="color-picker" style="position: relative;">				
						<input type="text" name="ik_fb_date_font_color" id="ik_fb_date_font_color" value="<?php echo strlen(get_option('ik_fb_date_font_color'))>2 ? get_option('ik_fb_date_font_color') : ' '; ?>" class="color" />
						<div style="z-index: 100; background: none repeat scroll 0% 0% rgb(238, 238, 238); border: 1px solid rgb(204, 204, 204); position: absolute;" class="colorpicker"></div>
					</div>						
					<p class="description"><?php _e('Input your hex color code, by clicking and using the Colorpicker or typing it in.  Erase the contents of this field to use the default color.');?></p></td>
				</tr>
			
				<tr valign="top">
					<th scope="row"><label for="ik_fb_posted_by_font_size"><?php _e('Posted By Font Size');?></label></th>
					<td><input type="text" name="ik_fb_posted_by_font_size" id="ik_fb_posted_by_font_size" value="<?php echo get_option('ik_fb_posted_by_font_size'); ?>" style="width: 250px" />
					<p class="description"><?php _e('Input your font pixel size.');?></p></td>
				</tr>
			
				<tr valign="top">
					<th scope="row"><label for="ik_fb_powered_by_font_color"><?php _e('Powered By Font Color');?></label></th>
					<td>
					<div class="color-picker" style="position: relative;">				
						<input type="text" name="ik_fb_powered_by_font_color" id="ik_fb_powered_by_font_color" value="<?php echo strlen(get_option('ik_fb_powered_by_font_color'))>2 ? get_option('ik_fb_powered_by_font_color') : ' '; ?>" class="color" />
						<div style="z-index: 100; background: none repeat scroll 0% 0% rgb(238, 238, 238); border: 1px solid rgb(204, 204, 204); position: absolute;" class="colorpicker"></div>
					</div>						
					<p class="description"><?php _e('Input your hex color code, by clicking and using the Colorpicker or typing it in.  Erase the contents of this field to use the default color.');?></p></td>
				</tr>
			
				<tr valign="top">
					<th scope="row"><label for="ik_fb_powered_by_font_size"><?php _e('Powered By Font Size');?></label></th>
					<td><input type="text" name="ik_fb_powered_by_font_size" id="ik_fb_powered_by_font_size" value="<?php echo get_option('ik_fb_powered_by_font_size'); ?>" style="width: 250px" />
					<p class="description"><?php _e('Input your font pixel size.');?></p></td>
				</tr>
			</table>
				<?php
					break;
					case 'display_options' :
				?>
				<?php settings_fields( 'ik-fb-display-settings-group' ); ?>
			
			<h3><?php _e('Display Options');?></h3>
			<p><?php _e('The below options are used to control the type and amount of content that is displayed in your Facebook Feed.');?></p>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="ik_fb_show_only_events"><?php _e('Show Only Events');?></label></th>
					<td><input type="checkbox" name="ik_fb_show_only_events" id="ik_fb_show_only_events" value="1" <?php if(get_option('ik_fb_show_only_events')){ ?> checked="CHECKED" <?php } ?>/>
					<p class="description"><?php _e('If checked, only Events will be shown in your Feed.');?></p></td>
				</tr>
			</table>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="ik_fb_link_photo_to_feed_item"><?php _e('Link Photo to Feed Item');?></label></th>
					<td><input type="checkbox" name="ik_fb_link_photo_to_feed_item" id="ik_fb_link_photo_to_feed_item" value="1" <?php if(get_option('ik_fb_link_photo_to_feed_item')){ ?> checked="CHECKED" <?php } ?>/>
					<p class="description"><?php _e('If checked, the Photos in the Feed will link to the same location that the Read More text does.  If unchecked, the Photos in the Feed will link to the Full Sized version of themselves.');?></p></td>
				</tr>
			</table>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="ik_fb_photo_feed_limit"><?php _e('Number of Photo Feed Items');?></label></th>
					<td><input type="text" name="ik_fb_photo_feed_limit" id="ik_fb_photo_feed_limit" value="<?php echo get_option('ik_fb_photo_feed_limit'); ?>" style="width: 250px" />
					<p class="description"><?php _e('The default number of items displayed is 25 - set higher numbers to display more.  If set, the photo feed will be limited to this number of items.  This can be overridden via the shortcode.');?></p></td>
				</tr>
			</table>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="ik_fb_feed_limit"><?php _e('Number of Feed Items');?></label></th>
					<td><input type="text" name="ik_fb_feed_limit" id="ik_fb_feed_limit" value="<?php echo get_option('ik_fb_feed_limit'); ?>" style="width: 250px" />
					<p class="description"><?php _e('The default number of items displayed is 25 - set higher numbers to display more.  If set, the feed will be limited to this number of items.  This can be overridden via the shortcode.');?></p></td>
				</tr>
			</table>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="ik_fb_character_limit"><?php _e('Feed Item Message Character Limit');?></label></th>
					<td><input type="text" name="ik_fb_character_limit" id="ik_fb_character_limit" value="<?php echo get_option('ik_fb_character_limit'); ?>" style="width: 250px" />
					<p class="description"><?php _e('If set, the feed item will be limited to this number of characters.  If a feed item is shortened, a Read More link will be displayed.');?></p></td>
				</tr>
			</table>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="ik_fb_description_character_limit"><?php _e('Feed Item Description Character Limit');?></label></th>
					<td><input type="text" name="ik_fb_description_character_limit" id="ik_fb_description_character_limit" value="<?php echo get_option('ik_fb_description_character_limit'); ?>" style="width: 250px" />
					<p class="description"><?php _e('If set, the feed item will be limited to this number of characters.  If a feed item is shortened, a Read More link will be displayed.');?></p></td>
				</tr>
			</table>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="ik_fb_hide_feed_images"><?php _e('Hide Feed Images');?></label></th>
					<td><input type="checkbox" name="ik_fb_hide_feed_images" id="ik_fb_show_feed_images" value="1" <?php if(get_option('ik_fb_hide_feed_images')){ ?> checked="CHECKED" <?php } ?>/>
					<p class="description"><?php _e('If checked, images will be hidden from your feed.');?></p></td>
				</tr>
			</table>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="ik_fb_show_like_button"><?php _e('Show Like Button');?></label></th>
					<td><input type="checkbox" name="ik_fb_show_like_button" id="ik_fb_show_like_button" value="1" <?php if(get_option('ik_fb_show_like_button')){ ?> checked="CHECKED" <?php } ?>/>
					<p class="description"><?php _e('If checked, the Like Button and number of people who like your page will be displayed above the Feed.');?></p></td>
				</tr>
			</table>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="ik_fb_show_profile_picture"><?php _e('Show Profile Picture');?></label></th>
					<td><input type="checkbox" name="ik_fb_show_profile_picture" id="ik_fb_show_profile_picture" value="1" <?php if(get_option('ik_fb_show_profile_picture')){ ?> checked="CHECKED" <?php } ?>/>
					<p class="description"><?php _e('If checked, the Profile Picture will be shown next to the Title of the feed.');?></p></td>
				</tr>
			</table>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="ik_fb_show_page_title"><?php _e('Show Page Title');?></label></th>
					<td><input type="checkbox" name="ik_fb_show_page_title" id="ik_fb_show_page_title" value="1" <?php if(get_option('ik_fb_show_page_title')){ ?> checked="CHECKED" <?php } ?>/>
					<p class="description"><?php _e('If checked, the Title of the feed will be shown.');?></p></td>
				</tr>
			</table>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="ik_fb_show_posted_by"><?php _e('Show Posted By');?></label></th>
					<td><input type="checkbox" name="ik_fb_show_posted_by" id="ik_fb_show_posted_by" value="1" <?php if(get_option('ik_fb_show_posted_by')){ ?> checked="CHECKED" <?php } ?>/>
					<p class="description"><?php _e('If checked, the text Posted By PosterName will be displayed in the feed.');?></p></td>
				</tr>
			</table>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="ik_fb_show_date"><?php _e('Show Posted Date');?></label></th>
					<td><input type="checkbox" name="ik_fb_show_date" id="ik_fb_show_date" value="1" <?php if(get_option('ik_fb_show_date')){ ?> checked="CHECKED" <?php } ?>/>
					<p class="description"><?php _e('If checked, the date of the post will be displayed in the Feed.');?></p></td>
				</tr>
			</table>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="ik_fb_use_human_timing"><?php _e('Do Not Use Special Formatting for Times within 24 hours');?></label></th>
					<td><input type="checkbox" name="ik_fb_use_human_timing" id="ik_fb_use_human_timing" value="1" <?php if(get_option('ik_fb_use_human_timing')){ ?> checked="CHECKED" <?php } ?>/>
					<p class="description"><?php _e('If checked, the date of the post will not be displayed using XX hours ago, when within 24 hours of now.');?></p></td>
				</tr>
			</table>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="ik_fb_date_format"><?php _e('Date Format');?></label></th>
					<td><input type="text" name="ik_fb_date_format" id="ik_fb_date_format" value="<?php echo get_option('ik_fb_date_format'); ?>" style="width: 250px" />
					<p class="description"><?php _e('The format string to be used for the Post Date.  This follows the standard used for PHP strfrtime().  Warning: this is an advanced feature - do not change this value if you do not know what you are doing! The default setting is %B %d');?></p></td>
				</tr>
			</table>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="ik_fb_powered_by"><?php _e('Show "Powered By IK Facebook"');?></label></th>
					<td><input type="checkbox" name="ik_fb_powered_by" id="ik_fb_powered_by" value="1" <?php if(get_option('ik_fb_powered_by')){ ?> checked="CHECKED" <?php } ?>/>
					<p class="description"><?php _e('Love this plugin but are unable to donate?  Show your love by displaying our inconspicuous "Powered By IK Facebook" link in the footer of your site.');?></p></td>
				</tr>
			</table>
				<?php
					break;
					case 'pro_options' :
						global $ik_social_pro_options;
						
						$ik_social_pro_options->output_settings();
					break;
					}//end switch
					
					//don't output the save button on the status screen
					if($tab != 'plugin_status'):
				?>			
					<p class="submit">
						<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
					</p>		
				<?php
					endif;
				?>
				<?php if($tab != 'plugin_status'): ?></form><?php endif; ?>
				<?php
					if(!is_valid_key(get_option('ik_fb_pro_key'))):					
				?>
					<div class="updated" id="message">
						<h2><?php _e('Want More Features?'); ?></h2>
						<p><a href="http://goldplugins.com/our-plugins/wp-social-pro/"><?php _e('Upgrade to WP Social Pro now;'); ?></a><?php _e(' and get tons of new features and settings.'); ?> </p>
						<h3><?php _e('Pro Features Include:');?></h3>
						<ul>
							<li><strong><?php _e('Unbranded Admin screens:</strong> Remove all IK FB branding from your Wordpress admin.');?></li>
							<li><strong><?php _e('Hide non-page-owner posts from your feed:</strong> With this option, your feed will only show the posts from your own account.');?></li>
							<li><strong><?php _e("Custom HTML Output:</strong> Use any HTML tags you want for the feed. You'll be able to specify a custom HTML template for your feed.");?></li>
							<li><strong><?php _e("Fanatical Support:</strong> We're here to help!  Purchase WP Social Pro and receive prompt, responsive, and professional support.");?></li>
							<li><strong><?php _e("Free Updates For A Year:</strong> Get WP Social Pro now, and you'll get free updates for a year!");?></li>
						</ul>
							
						<p><?php _e('More to come! WP Social Pro plugin owners get new updates automatically by email. New features land in the Pro version first, so be sure to upgrade today.');?></p>
									
						<a href="http://goldplugins.com/our-plugins/wp-social-pro/" target="_blank" title="<?php _e('Learn More About WP Social Pro');?>"><img src="<?php echo plugins_url('/img/ik_social_pro.jpg', __FILE__); ?>" alt="WP Social Pro" /><p class="description"><?php _e('Click Here To Learn About WP Social Pro');?></p></a>
					</div>
				<?php
					endif;
				?>
	</div>
	<?php } // end settings_page function
	
} // end class
?>