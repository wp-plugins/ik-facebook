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
	var $root = false;

	function __construct($root = false){
		//may be running in non WP mode (for example from a notification)
		if(function_exists('add_action')){
			//add a menu item
			add_action('admin_menu', array($this, 'add_admin_menu_item'));		
		}
		if ($root) {
			$this->root = $root;
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
		
		// TODO: Any reason not to change this to ik-facebook or something? see note on http://codex.wordpress.org/Function_Reference/add_submenu_page:
		// 		 "For $menu_slug please don't use __FILE__ it makes for an ugly URL, and is a minor security nuisance. "
		// $top_level_menu_slug = __FILE__;
		$top_level_menu_slug = 'ikfb_configuration_options';
		
		//create new top-level menu
		add_menu_page($page_title, $title, 'administrator', $top_level_menu_slug, array($this, 'configuration_options_page'));

		//create sub menus for each tab
		add_submenu_page( $top_level_menu_slug, 'Basic Configuration', 'Basic Configuration', 'manage_options', $top_level_menu_slug, array($this, 'configuration_options_page') ); 
		add_submenu_page( $top_level_menu_slug, 'Style Options', 'Style Options', 'manage_options', 'ikfb_style_options', array($this, 'style_options_page') ); 
		add_submenu_page( $top_level_menu_slug, 'Display Options', 'Display Options', 'manage_options', 'ikfb_display_options', array($this, 'display_options_page') ); 
		add_submenu_page( $top_level_menu_slug, 'Pro Options', 'Pro Options', 'manage_options', 'ikfb_pro_options', array($this, 'pro_options_page') ); 
		add_submenu_page( $top_level_menu_slug, 'Plugin Status &amp; Help', 'Plugin Status &amp; Help', 'manage_options', 'ikfb_plugin_status', array($this, 'plugin_status_page') ); 

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
		register_setting( 'ik-fb-config-settings-group', 'ik_fb_page_id', array($this, 'extract_facebook_id') );
		register_setting( 'ik-fb-config-settings-group', 'ik_fb_app_id' );
		register_setting( 'ik-fb-config-settings-group', 'ik_fb_secret_key' );
		register_setting( 'ik-fb-config-settings-group', 'ik_fb_pro_key' );
		register_setting( 'ik-fb-config-settings-group', 'ik_fb_pro_url' );
		register_setting( 'ik-fb-config-settings-group', 'ik_fb_pro_email' );
		
		// register pro config settings		
		register_setting( 'ik-fb-config-settings-group', 'wp_social_pro_registered_email' );
		register_setting( 'ik-fb-config-settings-group', 'wp_social_registered_url' );
		register_setting( 'ik-fb-config-settings-group', 'wp_social_pro_registered_key' );		
		
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
	
	function extract_facebook_id($input)
	{
		if (strpos($input, 'facebook.com/') !== FALSE) {
			$pieces = explode('/', $input); // divides the string in pieces where '/' is found
			return end($pieces); //takes the last piece
		}
		return $input;
	}

	function start_settings_page($wrap_with_form = true, $show_newsletter_form = true, $before_title = '' )
	{
		global $pagenow;
		
		if(get_option('ik_fb_unbranded') && is_valid_key(get_option('ik_fb_pro_key'))){
			$title = __("Facebook Settings", $this->textdomain);
			$message = __("Facebook Settings Updated.", $this->textdomain);
		} else {
			$title = __("IK Facebook Plugin Settings", $this->textdomain);
			$message = __("IK Facebook Plugin Settings Updated.", $this->textdomain);
		}
		?>
			<div class="wrap ikfb_settings">
			
				<?php echo $before_title; ?>
				<?php if ( get_option('ik_fb_app_id', '') == '' ): ?>
				<div class="app_id_callout">
					<p><?php _e("<strong>Important:</strong> You'll need to <a href=\"http://goldplugins.com/documentation/wp-social-pro-documentation/how-to-get-an-app-id-and-secret-key-from-facebook/\">create a free Facebook app</a> so that your plugin can access your feed. Don't worry - it only takes 2 minutes, and we've even got <a href=\"http://goldplugins.com/documentation/wp-social-pro-documentation/how-to-get-an-app-id-and-secret-key-from-facebook/\">a video tutorial</a>.");?></p>
				</div>
				<?php endif; ?>
				
				<h2><?php echo $title; ?></h2>		
				
				<?php if( !is_valid_key(get_option('ik_fb_pro_key') ) && $show_newsletter_form ): ?>
				<?php $this->output_newsletter_signup_form(); ?>
				<?php endif; ?>
			
				<?php if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') : ?>
				<div id="message" class="updated fade"><p><?php echo $message; ?></p></div>
				<?php endif; ?>	
	
				<?php if($wrap_with_form): ?>
				<form method="post" action="options.php" class="options_form">
				<?php endif; ?>
		<?php
	}
	
	function end_settings_page($wrap_with_form = true)
	{
		//don't output the save button on the status screen
		if($wrap_with_form):
		?>			
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>		
		<?php endif; ?>
		<?php if($wrap_with_form): ?></form><?php endif; ?>
		<?php
		if(!is_valid_key(get_option('ik_fb_pro_key'))):	
			$this->output_upgrade_teaser();
		endif;
	}
	
	/*
	 * Outputs the Basic Configuration page
	 */
	function configuration_options_page()
	{
		$this->start_settings_page(true);
		settings_fields( 'ik-fb-config-settings-group' );
		?>
			<h3><?php _e("Configuration Options");?></h3>
			<p><?php _e("These options tell the plugin how to access your Facebook Page.");?></p>
			<?php 
			$needs_app_id = (get_option('ik_fb_app_id', '') == '');
			if ( $needs_app_id ):
			?>
			<p><?php _e("<strong>Important:</strong> You'll need to <a href=\"http://goldplugins.com/documentation/wp-social-pro-documentation/how-to-get-an-app-id-and-secret-key-from-facebook/\">create a free Facebook app</a> so that your plugin can access your feed. Don't worry - it only takes 2 minutes, and we've even got <a href=\"http://goldplugins.com/documentation/wp-social-pro-documentation/how-to-get-an-app-id-and-secret-key-from-facebook/\">a video explaining the process</a>.");?></p>
			<?php endif; ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="ik_fb_page_id"><?php _e("Facebook Page ID");?></label></th>
					<td><input type="text" name="ik_fb_page_id" id="ik_fb_page_id" value="<?php echo get_option('ik_fb_page_id'); ?>"  style="width: 250px" />
					<p class="description"><?php _e("Your Facebook Username or Page ID. This can be a username (like IlluminatiKarate) or a number (like 189090822).<br />Tip: You can find it by visiting your Facebook profile and copying the entire URL into the box above.");?></p>
					</td>
				</tr>
			</table>

			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="ik_fb_app_id"><?php _e("Facebook App ID");?></label></th>
					<td><input type="text" name="ik_fb_app_id" id="ik_fb_app_id" value="<?php echo get_option('ik_fb_app_id'); ?>" style="width: 250px" />
					<p class="description <?php echo ($needs_app_id ? 'app_id_callout' : '');?>"><?php _e('This is the App ID you acquired when you <a href="http://goldplugins.com/documentation/wp-social-pro-documentation/how-to-get-an-app-id-and-secret-key-from-facebook/" target="_blank" title="How To Get An App ID and Secret Key From Facebook">setup your Facebook app</a>.');?></p></td>
				</tr>
			</table>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="ik_fb_secret_key"><?php _e("Facebook Secret Key");?></label></th>
					<td><input type="text" name="ik_fb_secret_key" id="ik_fb_secret_key" value="<?php echo get_option('ik_fb_secret_key'); ?>" style="width: 250px" />
					<p class="description <?php echo ($needs_app_id ? 'app_id_callout' : '');?>"><?php _e('This is the App Secret you acquired when you <a href="http://goldplugins.com/documentation/wp-social-pro-documentation/how-to-get-an-app-id-and-secret-key-from-facebook/" target="_blank" title="How To Get An App ID and Secret Key From Facebook">setup your Facebook app</a>.');?></p></td>
				</tr>
			</table>	
		<?php		
		include('registration_options.php');
		$this->end_settings_page();		
	}
	
	/*
	 * Outputs the Style Options page
	 */
	function style_options_page()
	{
		$this->start_settings_page();
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
			<p><?php _e('These options control the style of the Facebook Feed displayed on your website. You can change fonts, colors, image sizes, and even add your own custom CSS.');?></p>
		
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
		$this->end_settings_page();		
	}
	
	/*
	 * Outputs the Basic Configuration page
	 */
	function display_options_page()
	{
		$this->start_settings_page();
		?>
		<?php settings_fields( 'ik-fb-display-settings-group' ); ?>
			
			<h3><?php _e('Display Options');?></h3>
			<p><?php _e('These options control the type and amount of content that is displayed in your Facebook Feed.');?></p>
			
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
		$this->end_settings_page();		
	}
	
	/*
	 * Outputs the Basic Configuration page
	 */
	function pro_options_page()
	{
		$this->start_settings_page();
		global $ik_social_pro_options;		
		$ik_social_pro_options->output_settings();	
		$this->end_settings_page();		
	}
	
	/*
	 * Outputs the Basic Configuration page
	 */
	function plugin_status_page()
	{
		$diagnostics_results = $this->run_diagnostics();
		$graph_api_warning = '';
		
		// if their API Key and Secret work, but we can't load their profile, 
		// let the user know that they need to make their profile public
		if ($diagnostics_results['loaded_demo_profile'] && !$diagnostics_results['loaded_own_profile']) {
			$graph_api_warning = '<p class="alert_important"><strong>Your Facebook page (' . get_option("ik_fb_page_id") .') cannot be accessed via the Graph API</strong>Please verify that your Facebook page is Public, that it is not a Personal account, and that it has no Country, Age or other restrictions enabled.</p>';
		}

		// start the settings page, outputting a notice about the Graph API if needed
		$this->start_settings_page(false, false, $graph_api_warning);
			
		// output the Status Widget with the results of our diagnostics
		echo '<h3>';
		_e('Plugin Status');
		echo'</h3>';
		echo '<p>';
		_e('We\'re running some quick tests, to help you troubleshoot any issues you might be running into while setting up your Facebook feed.');
		echo '</p>';
		$this->output_status_box($diagnostics_results);
				
		// show some example shortcodes				
		_e("<h3>Example Shortcodes</h3>");
		_e('<p>To output the custom Facebook Feed, place <code>[ik_fb_feed]</code> in the body of a post.  To further customize the feed via the shortcode, available attributes include: <code>colorscheme="light" use_thumb="true" width="250" num_posts="5" id="123456789"</code>.</p>');
		_e('<p><em>Valid choices for "colorscheme" are "light" and "dark". If "use_thumb" is set to true, the value of "width" will be ignored.  If "use_thumb" or "width" are not set, the values from the Options page will be used.  If id is not set, the shortcode will use the Page ID from your Settings page.</em></p>');
		_e('<p>To output the Like Button, place <code>[ik_fb_like_button url="http://www.facebook.com"]</code> in the body of a post.  Valid attributes include: <code>url="" height="" colorscheme="light"</code>.</p>');
		_e('<p><em>Valid options for colorscheme are "light" and "dark".  Valid values for height are integers.  URL must be a valid website URL.</em></p>');
		_e('<p>To output a Photo Gallery, place <code>[ik_fb_gallery id="539627829386059" num_photos="25" size="130x73" title="Hello World!"]</code> in the body of a post.</p>');
		_e('<p><em>If no size is passed, it will default to 320 x 180.  Size options are 2048x1152, 960x540, 720x405, 600x337, 480x270, 320x180, and 130x73.  If num_photos is not passed, the Gallery will default to the amount set on the Dashboard - if no amount is set there, it will display up to 25 photos.  The ID number is found by looking at the URL of the link to the Album on Facebook - you can read more on our FAQs <a href="http://goldplugins.com/documentation/wp-social-pro-documentation/frequently-asked-questions/">here</a>.</em></p>');

		// output the current configuration settings (e.g., Page ID, API Key, and Secret)
		_e("<h3>Configuration Settings</h3>");
		_e("<p>If you need to contact us for help, please be sure to include these settings in your message, as well as a functional description of how you have the feed implemented on your site.</p>");
		echo "<table><tbody>";
		_e("<tr><td align='right'>Page ID:</td><td>" . get_option("ik_fb_page_id") . "</td></tr>");
		_e("<tr><td align='right'>App ID:</td><td>" . get_option("ik_fb_app_id") . "</td></tr>");
		_e("<tr><td align='right'>Secret Key:</td><td>" . get_option("ik_fb_secret_key") . "</td></tr>");
		echo "</tbody></table>";
				
		// show a message about where they can get help from a human
		_e("<h3>Get Help From A Human</h3>");
		_e("<p>Still having trouble? Sometimes talking to another person is the best way to get moving again.</p>");
		_e("<p>There are two great ways to get help from another human being:</p>");
		echo "<ol>";
		_e('<li><a href="https://wordpress.org/support/plugin/ik-facebook">Leave a message on the WordPress Support Forums</a>, and see if another member of the community can help.</li>');
		if (!is_valid_key()) {
			_e('<li><a href="http://goldplugins.com/our-plugins/wp-social-pro/?utm_source=help_from_a_human">Upgrade to WP Social Pro</a>, and get support directly from the developers.</li>');
		} else {
			_e('<li><a href="http://goldplugins.com/contact/">Contact Gold Plugins Support</a>. Please include the email address you used to purchase, and the address of this website.</li>');
		}
		echo "</ol>";
		_e("<p>If nothing else works, you might also try taking a 15 minute break. You'd be surprised how well it can work!</p>");
		
		
		$this->end_settings_page(false);		
	}
	
	function run_diagnostics()
	{
		// required settings for Graph API		
		$app_id = get_option('ik_fb_page_id', '');
		$secret_key = get_option('ik_fb_page_id', '');
		$page_id = get_option('ik_fb_page_id', '');
		
		// default all flags to false
		$results = array( 'keys_present' => false,
						  'keys_work' => false,
						  'loaded_demo_profile' => false,
						  'loaded_own_profile' => false,
					);
					
		/* run tests! */
		
		// Test #1: make sure the keys are present
		if ( empty($app_id) || empty($secret_key)  || empty($page_id) ) {
			$results['keys_present'] = false;
			return $results;
		} else {
			$results['keys_present'] = true;
		}
				  
		// Test #2: See if we can load the demo profile
		$demo_feed = $this->root->loadFacebook('IlluminatiKarate');		
		if ( empty($demo_feed['feed']) ) {
			$results['keys_work'] = false;
			$results['loaded_demo_profile'] = false;
			$results['loaded_own_profile'] = false;
			return $results;
		} else {
			$results['keys_work'] = true;
			$results['loaded_demo_profile'] = true;
		}
				  
		// Test #3: See if we can load the owner's profile
		$own_feed = $this->root->loadFacebook($page_id);		
		if ( empty($own_feed['feed']) ) {
			$results['loaded_own_profile'] = false;
		} else {
			$results['loaded_own_profile'] = true;
		}
				  
		return $results;
		
	}
	
	/*
	 * Outputs a plugin status box
	 */
	function output_status_box($diagnostics_results)
	{		
?>
	
	
	<table class="table" id="plugin_status_table" cellpadding="0" cellspacing="0">
		<tbody>
			<!-- Page ID -->
			<?php if ( $diagnostics_results['keys_present'] ): ?>
			<tr class="success">
				<td><img src="<?php echo plugins_url('/img/check-button.png', __FILE__); ?>" alt="SUCCESS" /></td>
				<td>API Key and Secret Key Present</td>
			</tr>
			<?php else: ?>
			<tr class="fail">
				<td><img src="<?php echo plugins_url('/img/x-button.png', __FILE__); ?>" alt="FAIL" /></td>
				<td>API Key and Secret Key Present</td>
			</tr>
			<?php endif; ?>
			
			<!-- Connected To Graph API -->
			<?php if ( $diagnostics_results['keys_work'] ): ?>
			<tr class="success">
				<td><img src="<?php echo plugins_url('/img/check-button.png', __FILE__); ?>" alt="SUCCESS" /></td>
				<td>Connected To Facebook Graph API</td>
			</tr>
			<?php else: ?>
			<tr class="fail">
				<td><img src="<?php echo plugins_url('/img/x-button.png', __FILE__); ?>" alt="FAIL" /></td>
				<td>Connected To Facebook Graph API</td>
			</tr>
			<?php endif; ?>
			
			<!-- Load Their Page Data -->
			<?php if ( $diagnostics_results['loaded_own_profile'] ): ?>
			<tr class="success">
				<td><img src="<?php echo plugins_url('/img/check-button.png', __FILE__); ?>" alt="FAIL" /></td>
				<td>Loaded Your Profile</td>
			</tr>			
			<?php else: ?>
			<tr class="fail">
				<td><img src="<?php echo plugins_url('/img/x-button.png', __FILE__); ?>" alt="FAIL" /></td>
				<td>Loaded Your Profile</td>
			</tr>
			<?php endif; ?>
			
			<!-- Load Their Page Data -->
			<?php if ( $diagnostics_results['loaded_demo_profile'] ): ?>
			<tr class="success">
				<td><img src="<?php echo plugins_url('/img/check-button.png', __FILE__); ?>" alt="FAIL" /></td>
				<td>Loaded Test Profile</td>
			</tr>			
			<?php else: ?>
			<tr class="fail">
				<td><img src="<?php echo plugins_url('/img/x-button.png', __FILE__); ?>" alt="FAIL" /></td>
				<td>Loaded Test Profile</td>
			</tr>
			<?php endif; ?>
			
			<!-- PRO Version Activated -->
			<?php if (is_valid_key()): ?>
			<tr class="success">
				<td><img src="<?php echo plugins_url('/img/check-button.png', __FILE__); ?>" alt="SUCCESS" /></td>
				<td>PRO Features Activated</td>
			</tr>
			<?php else: ?>
			<tr class="fail">
				<td><img src="<?php echo plugins_url('/img/x-button.png', __FILE__); ?>" alt="FAIL" /></td>
				<td>PRO Features Unlocked</td>
			</tr>
			<?php endif; ?>
		</tbody>
	</table>
<?php	
	}
	
	
	/*
	 * Outputs a Mailchimp signup form
	 */
	function output_newsletter_signup_form()
	{
?>
			<!-- Begin MailChimp Signup Form -->
			<div id="signup_wrapper">
				<div id="mc_embed_signup">
					<form action="http://illuminatikarate.us2.list-manage1.com/subscribe/post?u=403e206455845b3b4bd0c08dc&amp;id=3e22ddb309" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
						<p class="special-offer green_bg">Special Offer: Get $30 OFF WP Social Pro</p>
						<h3><?php _e("Subscribe to our newsletter, and save $30 on any version of WP Social Pro!", $this->textdomain); ?></h3>
						<p class="explain"><?php _e("Once you've confirmed your email address, you'll receive a coupon code for $30 off any version of WP Social Pro. After that, you'll receive around one email from us each month, jam-packed with tips, tricks, and special offers for getting more out of WordPress.", $this->textdomain); ?></p>
						<label for="mce-EMAIL">Your Email:</label>
						<input type="email" value="" name="EMAIL" class="email" id="mce-EMAIL" placeholder="email address" required>
						<!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
						<div style="position: absolute; left: -5000px;"><input type="text" name="b_403e206455845b3b4bd0c08dc_6ad78db648" tabindex="-1" value=""></div>
						<div class="clear"><input type="submit" value="Subscribe Now" name="subscribe" id="mc-embedded-subscribe" class="button"></div>
						<p class="respect"><em>We respect your privacy.</em></p>
					</form>
				</div>
				<p class="u_to_p"><a href="http://goldplugins.com/our-plugins/wp-social-pro/#buy_now"><?php _e("Upgrade to WP Social Pro now</a> to remove banners like this one.", $this->textdomain); ?></p>
			</div>
		<?php
	} // end output_newsletter_signup_form function
	
	function output_upgrade_teaser()
	{
		?>
		<style>
		#upgrade_teaser {
		    border: 1px solid gray;
			padding-top: 50px;
			position: relative;
			margin-top: 30px;
		}
		#upgrade_teaser h2
		{
			color: white;
			font-size: 18px;
			left: 0;
			padding: 10px 12px;
			position: absolute;
			right: 0;
			top: 0;
		}
		#upgrade_teaser h2 span {
			font-weight: bold;
		}
		#upgrade_teaser p.up
		{
			display: block;
			font-size: 16px;
			margin-bottom: 18px;		
		}
		#upgrade_teaser ul
		{
			list-style: disc outside none;
			padding-bottom: 2px;
			padding-left: 30px;		
		}
		#upgrade_teaser .button
		{
			background: #6db3f2; /* Old browsers */
			background: -moz-linear-gradient(top,  #6db3f2 0%, #1e69de 100%); /* FF3.6+ */
			background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#6db3f2), color-stop(100%,#1e69de)); /* Chrome,Safari4+ */
			background: -webkit-linear-gradient(top,  #6db3f2 0%,#1e69de 100%); /* Chrome10+,Safari5.1+ */
			background: -o-linear-gradient(top,  #6db3f2 0%,#1e69de 100%); /* Opera 11.10+ */
			background: -ms-linear-gradient(top,  #6db3f2 0%,#1e69de 100%); /* IE10+ */
			background: linear-gradient(to bottom,  #6db3f2 0%,#1e69de 100%); /* W3C */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#6db3f2', endColorstr='#1e69de',GradientType=0 ); /* IE6-9 */
			-webkit-border-radius: 5;
			-moz-border-radius: 5;
			border-radius: 5px;
			font-family: Arial;
			color: #ffffff;
			font-size: 30px;
			font-weight: bold;
			padding: 10px 20px 10px 20px;
			line-height: 1.5;
			height: auto;
			margin-top: 7px;
			margin-bottom: 17px;
			text-decoration: none;
			text-shadow: 0 0 3px darkblue;
			border: 3px solid #289AFD;
			box-shadow: 0 2px 3px cadetblue;
		}
		</style>
		<div class="updated" id="upgrade_teaser">
			<h2 class="green_bg"><?php _e('Want More Features? Upgrade to WP Social Pro'); ?></h2>
			<p class="up"><a href="http://goldplugins.com/our-plugins/wp-social-pro/"><?php _e('Upgrade to WP Social Pro now and get tons of new features and customization options. Click here!'); ?></a> </p>
			<a href="http://goldplugins.com/our-plugins/wp-social-pro/?utm_source=plugin_dash" target="_blank" title="<?php _e('Learn More About WP Social Pro');?>"><img src="<?php echo plugins_url('/img/wp_social_pro_banner.png', __FILE__); ?>" alt="WP Social Pro" /><p class="description"><?php _e('Click Here To Learn About WP Social Pro');?></p></a>
			<h3><?php _e('Pro Features Include:');?></h3>
			<ul>
				<li><strong><?php _e('Unbranded Admin screens:</strong> Remove all IK FB branding from your Wordpress admin.');?></li>
				<li><strong><?php _e('Hide non-page-owner posts from your feed:</strong> With this option, your feed will only show the posts from your own account.');?></li>
				<li><strong><?php _e("Custom HTML Output:</strong> Use any HTML tags you want for the feed. You'll be able to specify a custom HTML template for your feed.");?></li>
				<li><strong><?php _e("Hand Crafted Themes:</strong> Use any of our hand crafted themes to style your output!  Our Support Staff will also help you customize your CSS or styles, too!");?></li>
				<li><strong><?php _e("Fanatical Support:</strong> We're here to help!  Purchase WP Social Pro and receive prompt, responsive, and professional support.");?></li>
			</ul>
				
			<p><?php _e('And more to come! WP Social Pro plugin owners get new updates automatically by email. New features land in the Pro version first, so be sure to upgrade today.');?></p>
			<div style="max-width: 1000px; text-align: center; padding: 25px 0 20px;"><a class="button" href="http://goldplugins.com/our-plugins/wp-social-pro/?utm_source=plugin_dash" target="_blank" title="<?php _e('Upgrade To WP Social Pro');?>"><?php _e('Upgrade Now');?></a></div>
						
		</div>
		<?php	
	}
		
	
} // end class
?>