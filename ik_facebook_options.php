<?php
/*
This file is part of IK Facebook.

IK Facebook is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

IK Facebook is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with IK Facebook.  If not, see <http://www.gnu.org/licenses/>.
*/

class ikFacebookOptions
{
	function __construct(){
		//may be running in non WP mode (for example from a notification)
		if(function_exists('add_action')){
			//add a menu item
			add_action('admin_menu', array($this, 'add_admin_menu_item'));		
		}
	}
	
	function add_admin_menu_item(){
		//create new top-level menu
		add_menu_page('IK Facebook Plugin Settings', 'IK FB Settings', 'administrator', __FILE__, array($this, 'settings_page'));

		//call register settings function
		add_action( 'admin_init', array($this, 'register_settings'));	
	}


	function register_settings(){
		//register our settings
		register_setting( 'ik-fb-settings-group', 'ik_fb_page_id' );
		register_setting( 'ik-fb-settings-group', 'ik_fb_app_id' );
		register_setting( 'ik-fb-settings-group', 'ik_fb_secret_key' );
		register_setting( 'ik-fb-settings-group', 'ik_fb_custom_css' );
		register_setting( 'ik-fb-settings-group', 'ik_fb_show_like_button' );
	}

	function settings_page(){
	?>
	<div class="wrap">
		<h2>IK Facebook Settings</h2>
		
		<?php if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') : ?>
		<div id="message" class="updated fade"><p>IK Facebook settings updated</p></div>
		<?php endif; ?>	
		
		<form method="post" action="options.php">
			<?php settings_fields( 'ik-fb-settings-group' ); ?>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="ik_fb_page_id">Page ID</label></th>
					<td><input type="text" name="ik_fb_page_id" id="ik_fb_page_id" value="<?php echo get_option('ik_fb_page_id'); ?>"  style="width: 250px" />
					<p class="description">This is your Username or Page ID.  For example, ours is IlluminatiKarate (the end of our Facebook Page URL), but some people's maybe an Integer (such as 199789123).  It depends on if you have a username.</p>
					</td>
				</tr>
			</table>

			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="ik_fb_app_id">Facebook App ID</label></th>
					<td><input type="text" name="ik_fb_app_id" id="ik_fb_app_id" value="<?php echo get_option('ik_fb_app_id'); ?>" style="width: 250px" />
					<p class="description">This is the App ID you acquired when you <a href="https://illuminatikarate.com/blog/how-to-create-a-simple-facebook-app/" target="_blank" title="How To Create A Simple Facebook App">setup your Facebook app</a>.</p></td>
				</tr>
			</table>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="ik_fb_secret_key">Facebook Secret Key</label></th>
					<td><input type="text" name="ik_fb_secret_key" id="ik_fb_secret_key" value="<?php echo get_option('ik_fb_secret_key'); ?>" style="width: 250px" />
					<p class="description">This is the App Secret you acquired when you <a href="https://illuminatikarate.com/blog/how-to-create-a-simple-facebook-app/" target="_blank" title="How To Create A Simple Facebook App">setup your Facebook app</a>.</p></td>
				</tr>
			</table>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="ik_fb_custom_css">Custom CSS</a></th>
					<td><textarea name="ik_fb_custom_css" id="ik_fb_custom_css" style="width: 250px; height: 250px;"><?php echo get_option('ik_fb_custom_css'); ?></textarea>
					<p class="description">Input any Custom CSS you want to use here.  You can also include a file in your theme's folder called 'ik_fb_custom_style.css' - any styles in that file will be loaded with the plugin.  The plugin will work without you placing anything here - this is useful in case you need to edit any styles for it to work with your theme, though.</td>
				</tr>
			</table>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="ik_fb_show_like_button">Show Like Button</label></th>
					<td><input type="checkbox" name="ik_fb_show_like_button" id="ik_fb_show_like_button" value="1" <?php if(get_option('ik_fb_show_like_button')){ ?> checked="CHECKED" <?php } ?>/>
					<p class="description">If checked, the Like Button and number of people who like your page will be displayed above the Feed.</p></td>
				</tr>
			</table>
			
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div>
	<?php } // end settings_page function
	
} // end class
?>