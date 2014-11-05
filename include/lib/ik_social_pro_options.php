<?php
class ikSocialProOptions
{	
	//register the additional settings that come with pro
	function register_settings(){	
		register_setting( 'ik-fb-branding-settings-group', 'ik_fb_only_show_page_owner' );
		register_setting( 'ik-fb-branding-settings-group', 'ik_fb_unbranded' );
		
		register_setting( 'ik-fb-pro-display-settings-group', 'ik_fb_show_avatars' );
		register_setting( 'ik-fb-pro-display-settings-group', 'ik_fb_show_replies' );
		register_setting( 'ik-fb-pro-display-settings-group', 'ik_fb_show_reply_counts' );
		register_setting( 'ik-fb-pro-display-settings-group', 'ik_fb_show_likes' );
		
		register_setting( 'ik-fb-pro-event-settings-group', 'ik_fb_reverse_events' );
		register_setting( 'ik-fb-pro-event-settings-group', 'ik_fb_start_date_format' );
		register_setting( 'ik-fb-pro-event-settings-group', 'ik_fb_end_date_format' );
		register_setting( 'ik-fb-pro-event-settings-group', 'ik_fb_event_image_size' );
		register_setting( 'ik-fb-pro-event-settings-group', 'ik_fb_event_range_start_date' );
		register_setting( 'ik-fb-pro-event-settings-group', 'ik_fb_event_range_end_date' );
		
		register_setting( 'ik-fb-html-settings-group', 'ik_fb_feed_item_html' );
		register_setting( 'ik-fb-html-settings-group', 'ik_fb_message_html' );
		register_setting( 'ik-fb-html-settings-group', 'ik_fb_image_html' );
		register_setting( 'ik-fb-html-settings-group', 'ik_fb_description_html' );
		register_setting( 'ik-fb-html-settings-group', 'ik_fb_caption_html' );
		register_setting( 'ik-fb-html-settings-group', 'ik_fb_feed_html' );
		register_setting( 'ik-fb-html-settings-group', 'ik_fb_use_custom_html' );
		register_setting( 'ik-fb-html-settings-group', 'ik_fb_show_picture_before_message' );
	}
	
	//function to produce tabs on admin screen
	function ikfb_admin_tabs( $current = 'display_options' ) {
	
		$tabs = array( 'display_options' => 'Display Options', 'html_options' => 'Custom HTML Options', 'branding_options' => 'Branding Options', 'event_options' => 'Event Options' );
		echo '<div id="icon-themes" class="icon32"><br></div>';
		echo '<h2 class="nav-tab-wrapper">';
			foreach( $tabs as $tab => $name ){
				$class = ( $tab == $current ) ? ' nav-tab-active' : '';
				echo "<a class='nav-tab$class' href='?page=ikfb_pro_options&pro_tab=$tab'>$name</a>";				
			}
		echo '</h2>';
	}
	
	//output the additional options that come with pro
	function output_settings(){
		global $pagenow;
	
		if(get_option('ik_fb_unbranded') && is_valid_key(get_option('ik_fb_pro_key'))){
			$title = "Additional Settings";
		} else {
			$title = "WP Social Pro Settings";
		}			
		
		?>
		<h3><?php echo $title; ?></h3>
		<p><?php _e('These additional settings provide even more control over the output of your Facebook feed.');?></p>
		<?php if(!is_valid_key(get_option('ik_fb_pro_key'))): ?>
			<p><strong>These settings require WP Social Pro, the Pro version of IK Facebook. </strong><a href="http://goldplugins.com/our-plugins/wp-social-pro/"><?php _e('Upgrade to WP Social Pro now');?></a> <?php _e('to instantly unlock these features and more.');?> </p>
		<?php endif; ?>
		
		<?php if ( isset ( $_GET['pro_tab'] ) ) $this->ikfb_admin_tabs($_GET['pro_tab']); else $this->ikfb_admin_tabs('display_options'); ?>
			
		<?php 
			if ( $pagenow == 'admin.php' && $_GET['page'] == 'ikfb_pro_options' ){
				if ( isset ( $_GET['pro_tab'] ) ) $tab = $_GET['pro_tab'];
				else $tab = 'display_options';
			}	
		
			if(!is_valid_key(get_option('ik_fb_pro_key'))): ?><style>div.disabled,div.disabled label,div.disabled .description{color:#999999;}</style><?php endif;
		
			switch ( $tab ){
				case 'display_options' :	
			?>
			<?php settings_fields( 'ik-fb-pro-display-settings-group' ); ?>
		
			<?php if(!is_valid_key(get_option('ik_fb_pro_key'))): ?><div class="disabled"><?php endif; ?>
		
			<h3><?php _e('Display Options');?></h3>
			<?php echo $this->pro_upgrade_link(); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="ik_fb_show_avatars"><?php _e('Show Avatars');?></label></th>
					<td><input <?php if(!is_valid_key(get_option('ik_fb_pro_key'))): ?>disabled="disabled"<?php endif; ?>  type="checkbox" name="ik_fb_show_avatars" id="ik_fb_show_avatars" value="1" <?php if(get_option('ik_fb_show_avatars')){ ?> checked="CHECKED" <?php } ?>/>
					<p class="description"><?php _e('If checked, user avatars will be shown in the feed.');?></p>
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><label for="ik_fb_show_reply_counts"><?php _e('Show Comment Counts');?></label></th>
					<td><input <?php if(!is_valid_key(get_option('ik_fb_pro_key'))): ?>disabled="disabled"<?php endif; ?>  type="checkbox" name="ik_fb_show_reply_counts" id="ik_fb_show_reply_counts" value="1" <?php if(get_option('ik_fb_show_reply_counts')){ ?> checked="CHECKED" <?php } ?>/>
					<p class="description"><?php _e('If checked, user comment counts will be shown in the feed, with a link to the Facebook page.');?></p>
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><label for="ik_fb_show_replies"><?php _e('Show Comments');?></label></th>
					<td><input <?php if(!is_valid_key(get_option('ik_fb_pro_key'))): ?>disabled="disabled"<?php endif; ?>  type="checkbox" name="ik_fb_show_replies" id="ik_fb_show_replies" value="1" <?php if(get_option('ik_fb_show_replies')){ ?> checked="CHECKED" <?php } ?>/>
					<p class="description"><?php _e('If checked, user comments will be shown in the feed.  If Show Avatars is also checked, user avatars will be shown in the replies.  If Show Date is is also checked, the comment date will be shown in the replies. If Show Likes is also checked, the number of likes for each comment will be displayed.');?></p>
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><label for="ik_fb_show_likes"><?php _e('Show Likes');?></label></th>
					<td><input <?php if(!is_valid_key(get_option('ik_fb_pro_key'))): ?>disabled="disabled"<?php endif; ?>  type="checkbox" name="ik_fb_show_likes" id="ik_fb_show_likes" value="1" <?php if(get_option('ik_fb_show_likes')){ ?> checked="CHECKED" <?php } ?>/>
					<p class="description"><?php _e('If checked, user like counts will be shown in the feed, with a link to the Facebook page.');?></p>
					</td>
				</tr>
			</table>
			
			<?php if(!is_valid_key(get_option('ik_fb_pro_key'))): ?></div><?php endif; ?>
			
			<?php
				break;
				case 'html_options' :	
			?>
			<?php settings_fields( 'ik-fb-html-settings-group' ); ?>			
			
			<?php if(!is_valid_key(get_option('ik_fb_pro_key'))): ?><div class="disabled"><?php endif; ?>
			
			<h3><?php _e('Custom HTML');?></h3>
			<?php echo $this->pro_upgrade_link(); ?>
			<table class="form-table">
				
				<tr valign="top">
					<th scope="row"><label for="ik_fb_use_custom_html"><?php _e('Use Custom HTML');?></label></th>
					<td><input <?php if(!is_valid_key(get_option('ik_fb_pro_key'))): ?>disabled="disabled"<?php endif; ?>  type="checkbox" name="ik_fb_use_custom_html" id="ik_fb_use_custom_html" value="1" <?php if(get_option('ik_fb_use_custom_html')){ ?> checked="CHECKED" <?php } ?>/>
					<p class="description"><?php _e('If checked, this will disable the Style Options in the first tab and will instead use the HTML from below.');?></p>
					</td>
				</tr>	
				
				<tr valign="top">
					<th scope="row"><label for="ik_fb_show_picture_before_message"><?php _e('Show Picture Before Message');?></label></th>
					<td><input <?php if(!is_valid_key(get_option('ik_fb_pro_key'))): ?>disabled="disabled"<?php endif; ?>  type="checkbox" name="ik_fb_show_picture_before_message" id="ik_fb_show_picture_before_message" value="1" <?php if(get_option('ik_fb_show_picture_before_message')){ ?> checked="CHECKED" <?php } ?>/>
					<p class="description"><?php _e('If checked, the Picture HMTL will be output before the Message HTML.');?></p>
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><label for="ik_fb_feed_item_html"><?php _e('Custom Feed Item Wrapper HTML');?></a></th>
					<td><textarea <?php if(!is_valid_key(get_option('ik_fb_pro_key'))): ?>disabled="disabled"<?php endif; ?> name="ik_fb_feed_item_html" id="ik_fb_feed_item_html" style="width: 250px; height: 250px;"><?php echo get_option('ik_fb_feed_item_html'); ?></textarea>
					<p class="description"><?php _e('Input any Custom Feed Item HTML you want to use here.  The plugin will work without you placing anything here - this is useful in case you need to edit any styles for it to work with your theme, though. Accepts the following shortcodes: {ikfb:feed_item}');?></p>
					<p class="description"><?php _e('Example:');?> <code><?php echo htmlentities('<li class="ik_fb_feed_item">{ikfb:feed_item}</li>'); ?></code></p></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="ik_fb_message_html"><?php _e('Custom Feed Message HTML');?></a></th>
					<td><textarea <?php if(!is_valid_key(get_option('ik_fb_pro_key'))): ?>disabled="disabled"<?php endif; ?> name="ik_fb_message_html" id="ik_fb_message_html" style="width: 250px; height: 250px;"><?php echo get_option('ik_fb_message_html'); ?></textarea>
					<p class="description"><?php _e('Input any Custom Feed Item HTML you want to use here.  The plugin will work without you placing anything here - this is useful in case you need to edit any styles for it to work with your theme, though. Accepts the following shortcodes: {ikfb:feed_item:message}');?></p>
					<p class="description"><?php _e('Example:');?> <code><?php echo htmlentities('<p>{ikfb:feed_item:message}</p>'); ?></code></p></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="ik_fb_image_html"><?php _e('Custom Feed Image HTML');?></a></th>
					<td><textarea <?php if(!is_valid_key(get_option('ik_fb_pro_key'))): ?>disabled="disabled"<?php endif; ?> name="ik_fb_image_html" id="ik_fb_image_html" style="width: 250px; height: 250px;"><?php echo get_option('ik_fb_image_html'); ?></textarea>
					<p class="description"><?php _e('Input any Custom Feed Item HTML you want to use here.  The plugin will work without you placing anything here - this is useful in case you need to edit any styles for it to work with your theme, though. Accepts the following shortcodes: {ikfb:feed_item:image}');?></p>
					<p class="description"><?php _e('Example:');?> <code><?php echo htmlentities('<p class="ik_fb_facebook_image">{ikfb:feed_item:image}</p>'); ?></code></p></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="ik_fb_description_html"><?php _e('Custom Feed Description HTML');?></a></th>
					<td><textarea <?php if(!is_valid_key(get_option('ik_fb_pro_key'))): ?>disabled="disabled"<?php endif; ?> name="ik_fb_description_html" id="ik_fb_description_html" style="width: 250px; height: 250px;"><?php echo get_option('ik_fb_description_html'); ?></textarea>
					<p class="description"><?php _e('Input any Custom Feed Item HTML you want to use here.  The plugin will work without you placing anything here - this is useful in case you need to edit any styles for it to work with your theme, though. Accepts the following shortcodes: {ikfb:feed_item:description}');?></p>
					<p class="description"><?php _e('Example:');?> <code><?php echo htmlentities('<p class="ik_fb_facebook_description">{ikfb:feed_item:description}</p>'); ?></code></p></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="ik_fb_caption_html"><?php _e('Custom Feed Caption HTML');?></a></th>
					<td><textarea <?php if(!is_valid_key(get_option('ik_fb_pro_key'))): ?>disabled="disabled"<?php endif; ?> name="ik_fb_caption_html" id="ik_fb_caption_html" style="width: 250px; height: 250px;"><?php echo get_option('ik_fb_caption_html'); ?></textarea>
					<p class="description"><?php _e('Input any Custom Feed Item HTML you want to use here.  The plugin will work without you placing anything here - this is useful in case you need to edit any styles for it to work with your theme, though. Accepts the following shortcodes: {ikfb:feed_item:link}');?></p>					
					<p class="description"><?php _e('Example:');?> <code><?php echo htmlentities('<p class="ik_fb_facebook_link">{ikfb:feed_item:link}</p>'); ?></code></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="ik_fb_feed_html"><?php _e('Custom Feed Wrapper HTML');?></a></th>
					<td><textarea <?php if(!is_valid_key(get_option('ik_fb_pro_key'))): ?>disabled="disabled"<?php endif; ?> name="ik_fb_feed_html" id="ik_fb_feed_html" style="width: 250px; height: 250px;"><?php echo get_option('ik_fb_feed_html'); ?></textarea>
					<p class="description"><?php _e('Input any Custom Feed Item HTML you want to use here.  The plugin will work without you placing anything here - this is useful in case you need to edit any styles for it to work with your theme, though.  Accepts the following shortcodes: {ikfb:image},{ikfb:link},{ikfb:like_button}, and {ikfb:feed}.');?></p>
					<p class="description"><?php _e('Example:');?> <code><?php echo htmlentities('<div id="ik_fb_widget"><div class="ik_fb_profile_picture">{ikfb:image}{ikfb:link}</div>{ikfb:like_button}<ul class="ik_fb_feed_window">{ikfb:feed}</ul></div>'); ?></code></p>
				</tr>
			</table>
			
			<?php if(!is_valid_key(get_option('ik_fb_pro_key'))): ?></div><?php endif; ?>
			
			<?php
				break;
				case 'branding_options' :	
			?>
			<?php settings_fields( 'ik-fb-branding-settings-group' ); ?>
			
			<?php if(!is_valid_key(get_option('ik_fb_pro_key'))): ?><div class="disabled"><?php endif; ?>
			
			<h3><?php _e('Branding Options');?></h3>
			<?php echo $this->pro_upgrade_link(); ?>
			<table class="form-table">	
			
				<tr valign="top">
					<th scope="row"><label for="ik_fb_only_show_page_owner"><?php _e("Only Show Page Owner's Posts");?></label></th>
					<td><input <?php if(!is_valid_key(get_option('ik_fb_pro_key'))): ?>disabled="disabled"<?php endif; ?>  type="checkbox" name="ik_fb_only_show_page_owner" id="ik_fb_only_show_page_owner" value="1" <?php if(get_option('ik_fb_only_show_page_owner')){ ?> checked="CHECKED" <?php } ?>/>
					<p class="description"><?php _e('If checked, the only posts shown will be those made by the Page Owner.  This is a good way to prevent random users from posting things to your FB Wall that will then show up on your website.');?></p>
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><label for="ik_fb_unbranded"><?php _e('Hide Branding');?></label></th>
					<td><input <?php if(!is_valid_key(get_option('ik_fb_pro_key'))): ?>disabled="disabled"<?php endif; ?>  type="checkbox" name="ik_fb_unbranded" id="ik_fb_unbranded" value="1" <?php if(get_option('ik_fb_unbranded')){ ?> checked="CHECKED" <?php } ?>/>
					<p class="description"><?php _e('If checked, our branding will be hidden from the Dashboard.');?></p>
					</td>
				</tr>	
				
			</table>
			
			<?php if(!is_valid_key(get_option('ik_fb_pro_key'))): ?></div><?php endif; ?>
			
			<?php 
				break;
				case 'event_options' :	
			?>
			<?php settings_fields( 'ik-fb-pro-event-settings-group' ); ?>
			
			<?php if(!is_valid_key(get_option('ik_fb_pro_key'))): ?><div class="disabled"><?php endif; ?>
			
			<h3><?php _e('Event Options');?></h3>
			<?php echo $this->pro_upgrade_link(); ?>
			<table class="form-table">	
			
				<tr valign="top">
					<th scope="row"><label for="ik_fb_reverse_events"><?php _e("Reverse Event Feed Order");?></label></th>
					<td><input <?php if(!is_valid_key(get_option('ik_fb_pro_key'))): ?>disabled="disabled"<?php endif; ?>  type="checkbox" name="ik_fb_reverse_events" id="ik_fb_reverse_events" value="1" <?php if(get_option('ik_fb_reverse_events')){ ?> checked="CHECKED" <?php } ?>/>
					<p class="description"><?php _e('If checked, the order of the events feed will be reversed.');?></p>
					</td>
				</tr>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="ik_fb_start_date_format"><?php _e('Start Date Format');?></label></th>
					<td><input type="text" name="ik_fb_start_date_format" id="ik_fb_start_date_format" value="<?php echo get_option('ik_fb_start_date_format', 'l, F jS, Y h:i:s a'); ?>" style="width: 250px" />
					<p class="description"><?php _e('The format string to be used for the Event Start Date.  This follows the standard used for PHP date.  Warning: this is an advanced feature - do not change this value if you do not know what you are doing! The default setting is l, F jS, Y h:i:s a');?></p></td>
				</tr>
			</table>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="ik_fb_end_date_format"><?php _e('End Date Format');?></label></th>
					<td><input type="text" name="ik_fb_end_date_format" id="ik_fb_end_date_format" value="<?php echo get_option('ik_fb_end_date_format', 'l, F jS, Y h:i:s a'); ?>" style="width: 250px" />
					<p class="description"><?php _e('The format string to be used for the Event End Date.  This follows the standard used for PHP date.  Warning: this is an advanced feature - do not change this value if you do not know what you are doing! The default setting is l, F jS, Y h:i:s a');?></p></td>
				</tr>
			</table>	
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="ik_fb_event_range_start_date"><?php _e('Event Range Start Date');?></label></th>
					<td><input type="text" name="ik_fb_event_range_start_date" id="ik_fb_event_range_start_date" class="datepicker" value="<?php echo get_option('ik_fb_event_range_start_date', ''); ?>" style="width: 250px" />
					<p class="description"><?php _e('The Start Date of Events you want shown.  Events that start before this date will not be shown in the feed - even if their End Date is after this date.');?></p></td>
				</tr>
			</table>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="ik_fb_event_range_end_date"><?php _e('Event Range End Date');?></label></th>
					<td><input type="text" name="ik_fb_event_range_end_date" id="ik_fb_event_range_end_date" class="datepicker" value="<?php echo get_option('ik_fb_event_range_end_date', ''); ?>" style="width: 250px" />
					<p class="description"><?php _e('The End Date of Events you want shown.  Events that end after this date will not be shown in the feed - even if their Start Date is before this date.');?></p></td>
				</tr>
			</table>		
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="ik_fb_event_image_size"><?php _e('Event Feed Image Size');?></a></th>
					<td>
						<select name="ik_fb_event_image_size" id="ik_fb_event_image_size">	
							<?php 
								$ikfb_event_image_size = array(
									'normal' => 'Normal',
									'small' => 'Small',
									'large' => 'Large',
									'square' => 'Square'
								);
							?>
							<?php foreach($ikfb_event_image_size as $value => $name): ?>
							<option value="<?php echo $value; ?>" <?php if(get_option('ik_fb_event_image_size', 'small') == $value): echo 'selected="SELECTED"'; endif; ?>><?php echo $name; ?></option>
							<?php endforeach; ?>
						</select>
						<p class="description"><?php _e("Select which size of image to display with Events in your Feed.");?></p>
					</td>
				</tr>
			</table>	
			
			<?php if(!is_valid_key(get_option('ik_fb_pro_key'))): ?></div><?php endif; ?>
			
			<?php break;?>
		<?php }//end switch ?>
	<?php } // end ik_fb_pro_settings function
	
	function pro_upgrade_link($text = 'Upgrade To WP Social Pro To Unlock These Features')
	{
		if(!is_valid_key(get_option('ik_fb_pro_key'))) {
			return '<strong><a class="upgrade_link" href="http://goldplugins.com/our-plugins/wp-social-pro/?utm_source=pkugin_dash&utm_campaign=upgrade_to_unlock" target="_blank" />' . $text . '</a></strong>';
		} else {
			return '';
		}
	}
	
} // end class