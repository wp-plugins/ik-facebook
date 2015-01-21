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
		
		// create the BikeShed object now, so that BikeShed can add its hooks
        $this->shed = new GoldPlugins_BikeShed();
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
		//add_submenu_page( $top_level_menu_slug, 'Shortcode Generator', 'Shortcode Generator', 'manage_options', 'ikfb_shortcode_generator', array($this, 'shortcode_generator_page') ); 
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
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_powered_by_font_style' );
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_powered_by_font_family' );
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_posted_by_font_color' );
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_posted_by_font_size' );
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_posted_by_font_style' );
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_posted_by_font_family' );
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_date_font_color' );
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_date_font_size' );
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_date_font_style' );
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_date_font_family' );
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_description_font_color' );
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_description_font_size' );
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_description_font_style' );
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_description_font_family' );
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_link_font_color' );
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_link_font_size' );
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_link_font_style' );
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_link_font_family' );
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_feed_window_height' );
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_feed_window_width' );
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_font_color' );
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_font_size' );
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_font_family' );
		register_setting( 'ik-fb-style-settings-group', 'ik_fb_font_style' );
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
		global $current_user;
		get_currentuserinfo();
		?>
			<script type="text/javascript">
				jQuery(function () {
					if (typeof(gold_plugins_init_mailchimp_form) == 'function') {
						gold_plugins_init_mailchimp_form();
					}
				});
			</script>
			<?php if(is_valid_key()): ?>	
			<div class="wrap ikfb_settings gold_plugins_settings">
			<?php else: ?>
			<div class="wrap ikfb_settings not-pro gold_plugins_settings">			
			<?php endif; ?>
			
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
	
				<?php if(!is_valid_key()): ?>	
				<p class="plugin_is_not_registered">&#x2718; Your plugin is not registered and activated. You will not be able to use the PRO features until you upgrade. <a class="button" href="http://goldplugins.com/our-plugins/wp-social-pro/upgrade-to-wp-social-pro/?utm_source=api_key_reminder" target="_blank">Click here to upgrade now!</a></p>
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
	}
	
	/*
	 * Outputs the Basic Configuration page
	 */
	function configuration_options_page()
	{
		$this->start_settings_page(true);
		settings_fields( 'ik-fb-config-settings-group' );
		include('registration_options.php');		
		?>
			<h3><?php _e("Facebook API Settings");?></h3>
			<p><?php _e("These options tell the plugin how to access your Facebook Page.");?></p>
			<?php 
			$needs_app_id = (get_option('ik_fb_app_id', '') == '');
			$needs_secret = (get_option('ik_fb_secret_key', '') == '');
			if ( $needs_app_id ):
			?>
			<p><?php _e("<strong>Important:</strong> You'll need to <a href=\"http://goldplugins.com/documentation/wp-social-pro-documentation/how-to-get-an-app-id-and-secret-key-from-facebook/\">create a free Facebook app</a> so that your plugin can access your feed. Don't worry - it only takes 2 minutes, and we've even got <a href=\"http://goldplugins.com/documentation/wp-social-pro-documentation/how-to-get-an-app-id-and-secret-key-from-facebook/\">a video explaining the process</a>.");?></p>
			<?php endif; ?>
			<table class="form-table">
			
			<?php
				// Facebook Page ID
				$this->shed->text( array('name' => 'ik_fb_page_id', 'label' =>'Facebook Page ID', 'value' => get_option('ik_fb_page_id'), 'description' => 'Your Facebook Username or Page ID. This can be a username (like IlluminatiKarate) or a number (like 189090822).<br />Tip: You can find it by visiting your Facebook profile and copying the entire URL into the box above.') );

				// Facebook App ID
				$desc = 'This is the App ID you acquired when you <a href="http://goldplugins.com/documentation/wp-social-pro-documentation/how-to-get-an-app-id-and-secret-key-from-facebook/" target="_blank" title="How To Get An App ID and Secret Key From Facebook">setup your Facebook app</a>.';
				$desc = $needs_app_id ? '<div class="app_id_callout">' . $desc . '</div>' : $desc;
				$this->shed->text( array('name' => 'ik_fb_app_id', 'label' =>'Facebook App ID', 'value' => get_option('ik_fb_app_id'), 'description' => $desc) );

				// Facebook Secret Key
				$desc = 'This is the App Secret you acquired when you <a href="http://goldplugins.com/documentation/wp-social-pro-documentation/how-to-get-an-app-id-and-secret-key-from-facebook/" target="_blank" title="How To Get An App ID and Secret Key From Facebook">setup your Facebook app</a>.';
				$desc = $needs_secret ? '<div class="app_id_callout">' . $desc . '</div>' : $desc;
				$this->shed->text( array('name' => 'ik_fb_secret_key', 'label' =>'Facebook Secret Key', 'value' => get_option('ik_fb_secret_key'), 'description' => $desc) );
			?>
			
			</table>
		<?php		
		$this->end_settings_page();		
	}
	
	/*
	 * Outputs the Style Options page
	 */
	function style_options_page()
	{
		$this->start_settings_page();
		$ikfb_themes = array(
							'style' => 'Default Theme',
							'dark_style' => 'Dark Theme',
							'light_style' => 'Light Theme',
							'blue_style' => 'Blue Theme',
							'no_style' => 'No Theme',
						);
						
		if(is_valid_key(get_option('ik_fb_pro_key'))){
			$ikfb_themes['cobalt_style'] = 'Cobalt Theme';
			$ikfb_themes['green_gray_style'] = 'Green Gray Theme';
			$ikfb_themes['halloween_style'] = 'Halloween Theme';
			$ikfb_themes['indigo_style'] = 'Indigo Theme';
			$ikfb_themes['orange_style'] = 'Orange Theme';			
		}
		?>
		<?php settings_fields( 'ik-fb-style-settings-group' ); ?>
			
			<h3><?php _e('Style Options');?></h3>
			<p><?php _e('These options control the style of the Facebook Feed displayed on your website. You can change fonts, colors, image sizes, and even add your own custom CSS.');?></p>
		
			<table class="form-table">
			<?php 
				$desc = 'Select which theme you want to use.  If \'No Theme\' is selected, only your own theme\'s CSS, and any Custom CSS you\'ve added, will be used.  The settings below will override the defaults set in your selected theme.';
				if (!is_valid_key(get_option('ik_fb_pro_key'))) {
					$desc .= '<br /><br /><a href="http://goldplugins.com/our-plugins/wp-social-pro/upgrade-to-wp-social-pro/?utm_source=plugin&utm_campaign=unlock_more_themes">Tip: Upgrade to WP Social Pro to unlock more themes!</a>';
				}
				$this->shed->select( array('name' => 'ik_fb_feed_theme', 'options' => $ikfb_themes, 'label' =>'Feed Theme', 'value' => get_option('ik_fb_feed_theme'), 'description' => $desc) );
			?>				
				<?php $this->shed->textarea( array('name' => 'ik_fb_custom_css', 'label' =>'Custom CSS', 'value' => get_option('ik_fb_custom_css'), 'description' => 'Input any Custom CSS you want to use here.  You can also include a file in your theme\'s folder called \'ik_fb_custom_style.css\' - any styles in that file will be loaded with the plugin.  The plugin will work without you placing anything here - this is useful in case you need to edit any styles for it to work with your theme, though.') ); ?>
				
				<tr><td colspan=2><h4><?php _e('Feed Images');?></h4></td></tr>
				
				<?php
					$checked = (get_option('ik_fb_fix_feed_image_width') == '1');
					$this->shed->checkbox( array('name' => 'ik_fb_fix_feed_image_width', 'label' =>'Fix Feed Image Width', 'value' => 1, 'checked' => $checked, 'description' => 'If checked, images inside the feed will all be displayed at the width set below. If both this and \'Fix Feed Image Height\' are unchecked, feed will display image thumbnails.', 'inline_label' => 'Display images at the width selected below') ); 
				?>
				
				<?php
					$radio_options = array(
						'100%' => '100%',
						'OTHER' => sprintf('Other Pixel Value {{text|other_ik_fb_feed_image_width|%s}}', get_option('other_ik_fb_feed_image_width')),
					);				
					$this->shed->radio( array('name' => 'ik_fb_feed_image_width', 'value' => get_option('ik_fb_feed_image_width'), 'options' => $radio_options, 'label' =>'Feed Image Width', 'description' => "If 'Fix Feed Image Width' is checked, the images will be set to this width.  Choose '100%' or 'Other' and type in an integer number of pixels.  The effect of this setting may vary, based upon your theme's CSS.") );
				?>

				<?php
					$checked = (get_option('ik_fb_fix_feed_image_height') == '1');
					$this->shed->checkbox( array('name' => 'ik_fb_fix_feed_image_height', 'label' =>'Fix Feed Image Height', 'value' => 1, 'checked' => $checked, 'description' => 'If checked, images inside the feed will all be displayed at the height set below.  If both this and \'Fix Feed Image Width\' are unchecked, feed will display image thumbnails.', 'inline_label' => 'Display images at the height selected below') ); 
				?>
				<?php
					$radio_options = array(
						'100%' => '100%',
						'OTHER' => sprintf('Other Pixel Value {{text|other_ik_fb_feed_image_height|%s}}', get_option('other_ik_fb_feed_image_height')),
					);				
					$this->shed->radio( array('name' => 'ik_fb_feed_image_height', 'value' => get_option('ik_fb_feed_image_height'), 'options' => $radio_options, 'label' =>'Feed Image Height', 'description' => "If 'Fix Feed Image Height' is checked, the images will be set to this height.  Choose '100%' or 'Other' and type in an integer number of pixels.  The effect of this setting may vary, based upon your theme's CSS.") );
				?>
				
				<tr><td colspan=2><h4><?php _e('Feed Window Color and Dimensions');?></h4></td></tr>
				
				<?php $this->shed->color( array('name' => 'ik_fb_header_bg_color', 'label' =>'Feed Header Background Color', 'value' => get_option('ik_fb_header_bg_color'), 'description' => 'Input your hex color code, by clicking and using the Colorpicker or typing it in.  Erase the contents of this field to use the default color.') ); ?>				
				<?php $this->shed->color( array('name' => 'ik_fb_window_bg_color', 'label' =>'Feed Window Background Color', 'value' => get_option('ik_fb_window_bg_color'), 'description' => 'Input your hex color code, by clicking and using the Colorpicker or typing it in.  Erase the contents of this field to use the default color.') ); ?>
				
				<?php
					$radio_options = array(
						'' => 'Default',
						'auto' => 'Auto',
						'100%' => '100%',
						'OTHER' => sprintf('Other Pixel Value {{text|other_ik_fb_feed_window_height|%s}}', get_option('other_ik_fb_feed_window_height')),
					);				
					$this->shed->radio( array('name' => 'ik_fb_feed_window_height', 'value' => get_option('ik_fb_feed_window_height'), 'options' => $radio_options, 'label' =>'Feed Window Height', 'description' => "Choose 'Auto', '100%', or 'Other' and type in an integer number of pixels. The effect of this setting may vary, based upon your theme's CSS. This option does not apply to the sidebar widget.") );
				?>
				
				<?php
					$radio_options = array(
						'' => 'Default',
						'auto' => 'Auto',
						'100%' => '100%',
						'OTHER' => sprintf('Other Pixel Value {{text|other_ik_fb_feed_window_width|%s}}', get_option('other_ik_fb_feed_window_width')),
					);				
					$this->shed->radio( array('name' => 'ik_fb_feed_window_width', 'value' => get_option('ik_fb_feed_window_width'), 'options' => $radio_options, 'label' =>'Feed Window Width', 'description' => "Choose 'Auto', '100%', or 'Other' and type in an integer number of pixels. The effect of this setting may vary, based upon your theme's CSS. This option does not apply to the sidebar widget.") );
				?>
				
				<?php
					$radio_options = array(
						'' => 'Default',
						'auto' => 'Auto',
						'100%' => '100%',
						'OTHER' => sprintf('Other Pixel Value {{text|other_ik_fb_sidebar_feed_window_height|%s}}', get_option('other_ik_fb_sidebar_feed_window_height')),
					);				
					$this->shed->radio( array('name' => 'ik_fb_sidebar_feed_window_height', 'value' => get_option('ik_fb_sidebar_feed_window_height'), 'options' => $radio_options, 'label' =>'Sidebar Feed Window Height', 'description' => "Choose 'Auto', '100%', or 'Other' and type in an integer number of pixels. The effect of this setting may vary, based upon your theme's CSS. This option does not apply to the sidebar widget.") );
				?>
				
				<?php
					$radio_options = array(
						'' => 'Default',
						'auto' => 'Auto',
						'100%' => '100%',
						'OTHER' => sprintf('Other Pixel Value {{text|other_ik_fb_sidebar_feed_window_width|%s}}', get_option('other_ik_fb_sidebar_feed_window_width')),
					);				
					$this->shed->radio( array('name' => 'ik_fb_sidebar_feed_window_width', 'value' => get_option('ik_fb_sidebar_feed_window_width'), 'options' => $radio_options, 'label' =>'Sidebar Feed Window Width', 'description' => "Choose 'Auto', '100%', or 'Other' and type in an integer number of pixels. The effect of this setting may vary, based upon your theme's CSS. This option does not apply to the sidebar widget.") );
				?>
								
				<tr>
					<td colspan="2">
						<h4><?php _e('Font Styling');?></h4>
						<p class="section_intro"><strong>Tip:</strong> try out the <a href="http://www.google.com/fonts/" target="_blank">Google Web Fonts</a> for more exotic font options!</p>
					</td>
				</tr>
			
				<?php
					$values = array(
								'font_size' => get_option('ik_fb_description_font_size'),
								'font_family' => get_option('ik_fb_description_font_family'),
								'font_style' => get_option('ik_fb_description_font_style'),
								'font_color' => get_option('ik_fb_description_font_color'),
							);
					$this->shed->typography( array('name' => 'ik_fb_description_*', 'label' =>'Description Font', 'description' => 'Choose a font size, family, style, and color.', 'google_fonts' => true, 'default_color' => '#878787', 'values' => $values) );
				?>

				<?php
					$values = array(
								'font_size' => get_option('ik_fb_font_size'),
								'font_family' => get_option('ik_fb_font_family'),
								'font_style' => get_option('ik_fb_font_style'),
								'font_color' => get_option('ik_fb_font_color'),
							);
					$this->shed->typography( array('name' => 'ik_fb_*', 'label' =>'Message Font', 'description' => 'Choose a font size, family, style, and color.', 'google_fonts' => true, 'default_color' => '#878787', 'values' => $values) );
				?>

				<?php
					$values = array(
								'font_size' => get_option('ik_fb_link_font_size'),
								'font_family' => get_option('ik_fb_link_font_family'),
								'font_style' => get_option('ik_fb_link_font_style'),
								'font_color' => get_option('ik_fb_link_font_color'),
							);
					$this->shed->typography( array('name' => 'ik_fb_link_*', 'label' =>'Link Font', 'description' => 'Choose a font size, family, style, and color.', 'google_fonts' => true, 'default_color' => '#878787', 'values' => $values) );
				?>
			
				<?php
					$values = array(
								'font_size' => get_option('ik_fb_posted_by_font_size'),
								'font_family' => get_option('ik_fb_posted_by_font_family'),
								'font_style' => get_option('ik_fb_posted_by_font_style'),
								'font_color' => get_option('ik_fb_posted_by_font_color'),
							);
					$this->shed->typography( array('name' => 'ik_fb_posted_by_*', 'label' =>'Posted By Font', 'description' => 'Choose a font size, family, style, and color.', 'google_fonts' => true, 'default_color' => '#878787', 'values' => $values) );
				?>

				<?php
					$values = array(
								'font_size' => get_option('ik_fb_date_font_size'),
								'font_family' => get_option('ik_fb_date_font_family'),
								'font_style' => get_option('ik_fb_date_font_style'),
								'font_color' => get_option('ik_fb_date_font_color'),
							);
					$this->shed->typography( array('name' => 'ik_fb_date_*', 'label' =>'Date Font', 'description' => 'Choose a font size, family, style, and color.', 'google_fonts' => true, 'default_color' => '#878787', 'values' => $values) );
				?>
			
				<?php
					$values = array(
								'font_size' => get_option('ik_fb_powered_by_font_size'),
								'font_family' => get_option('ik_fb_powered_by_font_family'),
								'font_style' => get_option('ik_fb_powered_by_font_style'),
								'font_color' => get_option('ik_fb_powered_by_font_color'),
							);
					$this->shed->typography( array('name' => 'ik_fb_powered_by_*', 'label' =>'Powered By Font', 'description' => 'Choose a font size, family, style, and color.', 'google_fonts' => true, 'default_color' => '#878787', 'values' => $values) );
				?>
			
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
			<?php
				// Show Only Events (checkbox)
				$checked = (get_option('ik_fb_show_only_events') == '1');
				$this->shed->checkbox( array('name' => 'ik_fb_show_only_events', 'label' =>'Show Only Events', 'value' => 1, 'checked' => $checked, 'description' => 'If checked, only Events will be shown in your Feed.', 'inline_label' => 'Only Show Events In My Feed') ); 
				
				// Link Photo To Feed Item (checkbox)
				$checked = (get_option('ik_fb_link_photo_to_feed_item') == '1');
				$this->shed->checkbox( array('name' => 'ik_fb_link_photo_to_feed_item', 'label' =>'Link Photo to Feed Item', 'value' => 1, 'checked' => $checked, 'description' => 'If checked, the Photos in the Feed will link to the same location that the Read More text does.  If unchecked, the Photos in the Feed will link to the Full Sized version of themselves.', 'inline_label' => 'Link Photos to \'Read More\'') ); 

				// Limit the number of photos in the feed (number)
				$this->shed->text( array('name' => 'ik_fb_photo_feed_limit', 'label' =>'Number of Photo Feed Items', 'value' => get_option('ik_fb_photo_feed_limit'), 'description' => 'The default number of items displayed is 25 - set higher numbers to display more.  If set, the photo feed will be limited to this number of items.  This can be overridden via the shortcode.') );

				// Limit the total number of posts in the feed (number)
				$this->shed->text( array('name' => 'ik_fb_feed_limit', 'label' =>'Number of Feed Items', 'value' => get_option('ik_fb_feed_limit'), 'description' => 'The default number of items displayed is 25 - set higher numbers to display more.  If set, the feed will be limited to this number of items.  This can be overridden via the shortcode.') );

				// Feed Item Message Character limit (number)
				$this->shed->text( array('name' => 'ik_fb_character_limit', 'label' =>'Feed Item Message Character Limit', 'value' => get_option('ik_fb_character_limit'), 'description' => 'If set, the feed item will be limited to this number of characters.  If a feed item is shortened, a Read More link will be displayed.') );

				// Feed Item Description Character Limit (number)
				$this->shed->text( array('name' => 'ik_fb_description_character_limit', 'label' =>'Feed Item Description Character Limit', 'value' => get_option('ik_fb_description_character_limit'), 'description' => 'If set, the feed item will be limited to this number of characters.  If a feed item is shortened, a Read More link will be displayed.') );
			
				// Hide Images in Feed (checkbox)
				$checked = (get_option('ik_fb_hide_feed_images') == '1');
				$this->shed->checkbox( array('name' => 'ik_fb_hide_feed_images', 'label' =>'Hide Feed Images', 'value' => 1, 'checked' => $checked, 'description' => 'If checked, images will be hidden from your feed.', 'inline_label' => 'Hide All Images In My Feed') ); 

				// Show the Like Button (checkbox)
				$checked = (get_option('ik_fb_show_like_button') == '1');
				$this->shed->checkbox( array('name' => 'ik_fb_show_like_button', 'label' =>'Show Like Button', 'value' => 1, 'checked' => $checked, 'description' => 'If checked, the Like Button and number of people who like your page will be displayed above the Feed.', 'inline_label' => 'Show the Like Button above my feed') ); 

				// Show Profile Photo (checkbox)
				$checked = (get_option('ik_fb_show_profile_picture') == '1');
				$this->shed->checkbox( array('name' => 'ik_fb_show_profile_picture', 'label' =>'Show Profile Picture', 'value' => 1, 'checked' => $checked, 'description' => 'If checked, the Profile Picture will be shown next to the Title of the feed.', 'inline_label' => 'Show my Profile Picture above my feed ') );

				// Show Page Title (checkbox)
				$checked = (get_option('ik_fb_show_page_title') == '1');
				$this->shed->checkbox( array('name' => 'ik_fb_show_page_title', 'label' =>'Show Page Title', 'value' => 1, 'checked' => $checked, 'description' => 'If checked, the Title of the feed will be shown.', 'inline_label' => 'Show my Page Title above my feed') );

				// Show 'Posted By' text (checkbox)
				$checked = (get_option('ik_fb_show_posted_by') == '1');
				$this->shed->checkbox( array('name' => 'ik_fb_show_posted_by', 'label' =>'Show Posted By', 'value' => 1, 'checked' => $checked, 'description' => 'If checked, the text Posted By PosterName will be displayed in the feed.', 'inline_label' => 'Show \'Posted by PosterName\' for each item') );

				// Show Posted Date (checkbox)
				$checked = (get_option('ik_fb_show_date') == '1');
				$this->shed->checkbox( array('name' => 'ik_fb_show_date', 'label' =>'Show Posted Date', 'value' => 1, 'checked' => $checked, 'description' => 'If checked, the date of the post will be displayed in the Feed.', 'inline_label' => 'Show the date posted for each item') );

				// Disable "Human Timing" (checkbox)
				$checked = (get_option('ik_fb_use_human_timing') == '1');
				$this->shed->checkbox( array('name' => 'ik_fb_use_human_timing', 'label' =>'Disable "Human Timing" For Timestamps', 'value' => 1, 'checked' => $checked, 'description' => 'Check this box to always show normal timestamps, instead of "XX hours ago"', 'inline_label' => 'Disable "Human Timing" for Timestamps') );

				// Date Format (text)
				$this->shed->text( array('name' => 'ik_fb_date_format', 'label' =>'Date Format', 'value' => get_option('ik_fb_date_format'), 'description' => 'The format string to be used for the Post Date.  This follows the standard used for PHP strfrtime().  Warning: this is an advanced feature - do not change this value if you do not know what you are doing! The default setting is %B %d') );

				// Show Powered By link (checkbox)
				$checked = (get_option('ik_fb_powered_by') == '1');
				$this->shed->checkbox( array('name' => 'ik_fb_powered_by', 'label' =>'Show Powered By IK Facebook', 'value' => 1, 'checked' => $checked, 'description' => 'Love this plugin but are unable to donate?  Show your love by displaying our inconspicuous "Powered By IK Facebook" link in the footer of your site.', 'inline_label' => 'Add a "Powered By IK Facebook" link to my website\'s footer') );
			?>
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
		$ik_social_pro_options->output_settings($this->shed);
		$this->end_settings_page();		
	}
	
	/*
	 * Outputs the Shortcode Generator page
	 */
	function shortcode_generator_page()
	{
		wp_enqueue_script( 'gp_shortcode_generator');
		wp_enqueue_script( 'ikfb-admin');
		echo '<div id="shortcode_generator">';
		echo '<h3>Shortcode Generator</h3>';		
		echo '<table class="form-table">';
			echo '<tbody>';
			// Facebook Page ID
			$this->shed->text( array('name' => 'profile_id', 'label' =>'Facebook Page ID', 'value' => get_option('ik_fb_page_id'), 'description' => 'Your Facebook Username or Page ID. This can be a username (like IlluminatiKarate) or a number (like 189090822).') );

			// Generate button
			echo '<th scope="row"><label>&nbsp;</label></th><td><p class="submit"><input id="generate" type="submit" value="Generate My Shortcode" class="button-primary"></p></td>';
			
			// shortcode output
			$this->shed->textarea( array('name' => 'shortcode', 'label' =>'Your Shortcode') );
			echo '</tbody>';
		echo '</table>';
		echo '<form>';
		echo '</form>';
		echo '</div>';
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
		_e('<p>To output the custom Facebook Feed, place the following shortcode in the body of any page or post:</p>');
		_e('<p><input class="gp_code_to_copy" type="text" value="[ik_fb_feed]" /></p>');
		_e('<p>To further customize the feed via the shortcode, available attributes include: <code>colorscheme="light" use_thumb="true" width="250" num_posts="5" id="123456789"</code>.</p>');
		_e('<p><em>Valid choices for "colorscheme" are "light" and "dark". If "use_thumb" is set to true, the value of "width" will be ignored.  If "use_thumb" or "width" are not set, the values from the Options page will be used.  If id is not set, the shortcode will use the Page ID from your Settings page.</em></p>');
		_e('<p>To output the Like Button, place the following shortcode in the body of any page or post:</p>');
		_e('<p><input class="gp_code_to_copy" type="text" value=\'[ik_fb_like_button url="http://www.facebook.com"]\' /></p>');
		_e('Valid attributes include: <code>url="" height="" colorscheme="light"</code>.</p>');
		_e('<p><em>Valid options for colorscheme are "light" and "dark".  Valid values for height are integers.  URL must be a valid website URL.</em></p>');
		_e('<p>To output a Photo Gallery, place the following shortcode in the body of any page or post:</p>');
		_e('<p><input class="gp_code_to_copy" type="text" value=\'[ik_fb_gallery id="539627829386059" num_photos="25" size="130x73" title="Hello World!"]\' /></p>');
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
			_e('<li><a href="http://goldplugins.com/our-plugins/wp-social-pro/upgrade-to-wp-social-pro/?utm_source=help_from_a_human">Upgrade to WP Social Pro</a>, and get support directly from the developers.</li>');
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

		// Test #2: See if we can connect to the Graph API and generate an Access Token
		$access_token = $this->root->generateAccessToken();
		
		if ( empty($access_token)){
			$results['keys_work'] = false;
		} else {
			$results['keys_work'] = true;
		}
		
		// Test #3: See if we can load the demo profile
		$demo_feed = $this->root->loadFacebook('IlluminatiKarate');		
		if ( empty($demo_feed['feed']) ) {
			$results['loaded_demo_profile'] = false;
			$results['loaded_own_profile'] = false;
			return $results;
		} else {
			$results['loaded_demo_profile'] = true;
		}
				  
		// Test #4: See if we can load the owner's profile
		$own_feed = $this->root->loadFacebook($page_id);		
		if ( empty($own_feed['feed']) ) {
			//echo "<pre>";
			
			//print_r($own_feed);
			
			//echo "</pre>";
			
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
				<td><img src="<?php echo plugins_url('/img/check-button.png', __FILE__); ?>" alt="SUCCESS" /></td>
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
				<td><img src="<?php echo plugins_url('/img/check-button.png', __FILE__); ?>" alt="SUCCESS" /></td>
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
		$this->root->notifications->outputNotifications();
	}
	
	
	/*
	 * Outputs a Mailchimp signup form
	 */
	function output_newsletter_signup_form()
	{
		global $current_user;
		get_currentuserinfo();
?>
			<!-- Begin MailChimp Signup Form -->
			<div id="signup_wrapper">
				<div class="topper">
					<h3>Save 20% on WP Social Pro!</h3>
					<p class="pitch">Submit your name and email and we’ll send you a coupon for 20% off your upgrade to the Pro version.</p>
				</div>
				<div id="mc_embed_signup">
					<form action="http://illuminatikarate.us2.list-manage1.com/subscribe/post?u=403e206455845b3b4bd0c08dc&amp;id=3e22ddb309" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
						<label for="mce-EMAIL">Your Name:</label>
						<input type="email" value="<?php echo (!empty($current_user->display_name) ? $current_user->display_name : ''); ?>" name="NAME" class="email" id="mce-EMAIL" placeholder="Your Name" required>
						<label for="mce-EMAIL">Your Email:</label>
						<input type="email" value="<?php echo (!empty($current_user->user_email) ? $current_user->user_email : ''); ?>" name="EMAIL" class="email" id="mce-EMAIL" placeholder="Your Email" required>
						<!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
						<div style="position: absolute; left: -5000px;"><input type="text" name="b_403e206455845b3b4bd0c08dc_6ad78db648" tabindex="-1" value=""></div>
						<div class="clear"><input type="submit" value="Send Me The Coupon Now!" name="subscribe" id="mc-embedded-subscribe" class="button"></div>
						<p class="secure"><img src="<?php echo plugins_url( 'img/lock.png', __FILE__ ); ?>" alt="Lock" width="16px" height="16px" />We respect your privacy.</p>
						<input type="hidden" id="mc-upgrade-plugin-name" value="WP Social Pro" />
						<input type="hidden" id="mc-upgrade-link-per" value="http://goldplugins.com/purchase/wp-social-pro/single?promo=newsub20" />
						<input type="hidden" id="mc-upgrade-link-biz" value="http://goldplugins.com/purchase/wp-social-pro/business?promo=newsub20" />
						<input type="hidden" id="mc-upgrade-link-dev" value="http://goldplugins.com/purchase/wp-social-pro/developer?promo=newsub20" />
						<p class="customer_testimonial">
							"It's easy to use, it works, and with excellent support from it's developers - there is no reason to use any other plugin."
							<br /><span class="author">&dash; Jake Wheat, Author &amp; Artist</span>
						</p>
					</form>
				</div>
				<p class="u_to_p"><a href="http://goldplugins.com/our-plugins/wp-social-pro/upgrade-to-wp-social-pro/#buy_now"><?php _e("Upgrade to WP Social Pro now</a> to remove banners like this one.", $this->textdomain); ?></p>
			</div>
		<?php
	} // end output_newsletter_signup_form function
} // end class
?>