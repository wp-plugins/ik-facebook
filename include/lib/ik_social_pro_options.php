<?php
class ikSocialProOptions
{	
	var $shed;
	
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
	function output_settings(&$shed){
		global $pagenow;
		$this->shed = $shed;
		$is_pro = is_valid_key(get_option('ik_fb_pro_key'));
		
		
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
		
			if(!is_valid_key(get_option('ik_fb_pro_key'))): ?><style>div.disabled,div.disabled th,div.disabled label,div.disabled .description{color:#999999;}</style><?php endif;
		
			switch ( $tab ){
				case 'display_options' :	
			?>
			<?php settings_fields( 'ik-fb-pro-display-settings-group' ); ?>
		
			<?php if(!is_valid_key(get_option('ik_fb_pro_key'))): ?><div class="disabled"><?php endif; ?>
		
			<h3><?php _e('Display Options');?></h3>
			<?php echo $this->pro_upgrade_link(); ?>
			<table class="form-table">
			<?php
				// Use Custom HTML (checkbox)
				$checked = (get_option('ik_fb_show_avatars') == '1');
				$this->shed->checkbox( array('name' => 'ik_fb_show_avatars', 'label' => 'Show Avatars', 'value' => 1, 'checked' => $checked, 'description' => 'If checked, user avatars will be shown in the feed.', 'inline_label' => 'Show user avatars in my feed', 'disabled' => !$is_pro) );
				
				// Show Comment Count (checkbox)
				$checked = (get_option('ik_fb_show_reply_counts') == '1');
				$this->shed->checkbox( array('name' => 'ik_fb_show_reply_counts', 'label' => 'Show Comment Counts', 'value' => 1, 'checked' => $checked, 'description' => 'If checked, user comment counts will be shown in the feed, with a link to the Facebook page.', 'inline_label' => 'Show Comment Counts', 'disabled' => !$is_pro) );

				// Show Comments (checkbox)
				$checked = (get_option('ik_fb_show_replies') == '1');
				$this->shed->checkbox( array('name' => 'ik_fb_show_replies', 'label' => 'Show Comments', 'value' => 1, 'checked' => $checked, 'description' => 'If checked, user comments will be shown in the feed.  If Show Avatars is also checked, user avatars will be shown in the replies.  If Show Date is is also checked, the comment date will be shown in the replies. If Show Likes is also checked, the number of likes for each comment will be displayed.', 'inline_label' => 'Show Comments', 'disabled' => !$is_pro) );

				// Show Likes (checkbox)
				$checked = (get_option('ik_fb_show_likes') == '1');
				$this->shed->checkbox( array('name' => 'ik_fb_show_likes', 'label' => 'Show Likes', 'value' => 1, 'checked' => $checked, 'description' => 'If checked, user like counts will be shown in the feed, with a link to the Facebook page.', 'inline_label' => 'Show Likes', 'disabled' => !$is_pro) );
			?>
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
			<?php
				// Use Custom HTML (checkbox)
				$checked = (get_option('ik_fb_use_custom_html') == '1');
				$this->shed->checkbox( array('name' => 'ik_fb_use_custom_html', 'label' => 'Use Custom HTML', 'value' => 1, 'checked' => $checked, 'description' => 'If checked, this will disable the Style Options in the first tab and will instead use the HTML from below.', 'inline_label' => 'Use Custom HTML', 'disabled' => !$is_pro) );

				// Hide Branding (checkbox)
				$checked = (get_option('ik_fb_show_picture_before_message') == '1');
				$this->shed->checkbox( array('name' => 'ik_fb_show_picture_before_message', 'label' => 'Show Picture Before Message', 'value' => 1, 'checked' => $checked, 'description' => 'If checked, the Picture HMTL will be output before the Message HTML.', 'inline_label' => 'Output the Picture HTML before the Message HTML', 'disabled' => !$is_pro) );
				
				// Custom Feed Item Wrapper HTML (textarea)
				$desc = 'Input any Custom Feed Item HTML you want to use here.  The plugin will work without you placing anything here - this is useful in case you need to edit any styles for it to work with your theme, though. Accepts the following shortcodes: {ikfb:feed_item}';
				$desc .= '<br />Example: <code>' . htmlentities('<li class="ik_fb_feed_item">{ikfb:feed_item}</li>') . '</code>';
				$this->shed->textarea( array('name' => 'ik_fb_feed_item_html', 'label' => 'Custom Feed Item Wrapper HTML', 'value' => get_option('ik_fb_feed_item_html'), 'description' => $desc, 'disabled' => !$is_pro) );
				
				// Custom Feed Message HTML (textarea)
				$desc = 'Input any Custom Feed Item HTML you want to use here.  The plugin will work without you placing anything here - this is useful in case you need to edit any styles for it to work with your theme, though. Accepts the following shortcodes: {ikfb:feed_item:message}';
				$desc .= '<br />Example: <code>' . htmlentities('<p>{ikfb:feed_item:message}</p>') . '</code>';
				$this->shed->textarea( array('name' => 'ik_fb_message_html', 'label' => 'Custom Feed Message HTML', 'value' => get_option('ik_fb_message_html'), 'description' => $desc, 'disabled' => !$is_pro) );
				
				// Custom Feed Image HTML (textarea)
				$desc = 'Input any Custom Feed Item HTML you want to use here.  The plugin will work without you placing anything here - this is useful in case you need to edit any styles for it to work with your theme, though. Accepts the following shortcodes: {ikfb:feed_item:image}';
				$desc .= '<br />Example: <code>' . htmlentities('<p class="ik_fb_facebook_image">{ikfb:feed_item:image}</p>') . '</code>';
				$this->shed->textarea( array('name' => 'ik_fb_image_html', 'label' => 'Custom Feed Image HTML', 'value' => get_option('ik_fb_image_html'), 'description' => $desc, 'disabled' => !$is_pro) );
				
				// Custom Feed Description HTML (textarea)
				$desc = 'Input any Custom Feed Item HTML you want to use here.  The plugin will work without you placing anything here - this is useful in case you need to edit any styles for it to work with your theme, though. Accepts the following shortcodes: {ikfb:feed_item:description}';
				$desc .= '<br />Example: <code>' . htmlentities('<p class="ik_fb_facebook_description">{ikfb:feed_item:description}</p>') . '</code>';
				$this->shed->textarea( array('name' => 'ik_fb_description_html', 'label' => 'Custom Feed Description HTML', 'value' => get_option('ik_fb_description_html'), 'description' => $desc, 'disabled' => !$is_pro) );
				
				// Custom Feed Caption HTML (textarea)
				$desc = 'Input any Custom Feed Item HTML you want to use here.  The plugin will work without you placing anything here - this is useful in case you need to edit any styles for it to work with your theme, though. Accepts the following shortcodes: {ikfb:feed_item:link}';
				$desc .= '<br />Example: <code>' . htmlentities('<p class="ik_fb_facebook_link">{ikfb:feed_item:link}</p>') . '</code>';
				$this->shed->textarea( array('name' => 'ik_fb_caption_html', 'label' => 'Custom Feed Caption HTML', 'value' => get_option('ik_fb_caption_html'), 'description' => $desc, 'disabled' => !$is_pro) );
				
				// Custom Feed Wrapper HTML (textarea)
				$desc = 'Input any Custom Feed Item HTML you want to use here.  The plugin will work without you placing anything here - this is useful in case you need to edit any styles for it to work with your theme, though.  Accepts the following shortcodes: {ikfb:image},{ikfb:link},{ikfb:like_button}, and {ikfb:feed}.';
				$desc .= '<br />Example: <code>' . htmlentities('<div id="ik_fb_widget"><div class="ik_fb_profile_picture">{ikfb:image}{ikfb:link}</div>{ikfb:like_button}<ul class="ik_fb_feed_window">{ikfb:feed}</ul></div>') . '</code>';
				$this->shed->textarea( array('name' => 'ik_fb_feed_html', 'label' => 'Custom Feed Wrapper HTML', 'value' => get_option('ik_fb_feed_html'), 'description' => $desc, 'disabled' => !$is_pro) );
			?>
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
			<?php
				// Only Show Page Owner's Posts (checkbox)
				$checked = (get_option('ik_fb_only_show_page_owner') == '1');
				$this->shed->checkbox( array('name' => 'ik_fb_only_show_page_owner', 'label' =>'Only Show Page Owner\'s Posts', 'value' => 1, 'checked' => $checked, 'description' => 'If checked, the only posts shown will be those made by the Page Owner.  This is a good way to prevent random users from posting things to your FB Wall that will then show up on your website.', 'inline_label' => 'Only show posts made by the page owner', 'disabled' => !$is_pro) );

				// Hide Branding (checkbox)
				$checked = (get_option('ik_fb_unbranded') == '1');
				$this->shed->checkbox( array('name' => 'ik_fb_unbranded', 'label' =>'Hide Branding', 'value' => 1, 'checked' => $checked, 'description' => 'If checked, our branding will be hidden from the Dashboard.', 'inline_label' => 'Hide WP Social Branding', 'disabled' => !$is_pro) );
			?>
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
			<?php			
				// Reverse Event Feed Order (checkbox)
				$checked = (get_option('ik_fb_reverse_events') == '1');
				$this->shed->checkbox( array('name' => 'ik_fb_reverse_events', 'label' =>'Reverse Event Feed Order', 'value' => 1, 'checked' => $checked, 'description' => 'If checked, the order of the events feed will be reversed.', 'inline_label' => 'Reverse the order of the events feed', 'disabled' => !$is_pro) );
			
				// Start Date Format (text)
				$value = get_option('ik_fb_start_date_format', 'l, F jS, Y h:i:s a');
				$this->shed->text( array('name' => 'ik_fb_start_date_format', 'label' =>'Start Date Format', 'value' => $value, 'description' => 'The format string to be used for the Event Start Date.  This follows the standard used for PHP date.  Warning: this is an advanced feature - do not change this value if you do not know what you are doing! The default setting is l, F jS, Y h:i:s a', 'disabled' => !$is_pro) );

				// End Date Format (text)
				$value = get_option('ik_fb_end_date_format', 'l, F jS, Y h:i:s a');
				$this->shed->text( array('name' => 'ik_fb_end_date_format', 'label' =>'End Date Format', 'value' => $value, 'description' => 'The format string to be used for the Event End Date.  This follows the standard used for PHP date.  Warning: this is an advanced feature - do not change this value if you do not know what you are doing! The default setting is l, F jS, Y h:i:s a', 'disabled' => !$is_pro) );

				// Event Range - Start Date (text / datepicker)
				$this->shed->text( array('name' => 'ik_fb_event_range_start_date', 'label' =>'Event Range Start Date', 'value' => get_option('ik_fb_event_range_start_date'), 'description' => 'The Start Date of Events you want shown.  Events that start before this date will not be shown in the feed - even if their End Date is after this date.', 'class' => 'datepicker', 'disabled' => !$is_pro) );
			
				// Event Range - End Date (text / datepicker)
				$this->shed->text( array('name' => 'ik_fb_event_range_end_date', 'label' =>'Event Range End Date', 'value' => get_option('ik_fb_event_range_end_date'), 'description' => 'The End Date of Events you want shown.  Events that end after this date will not be shown in the feed - even if their Start Date is before this date.', 'class' => 'datepicker', 'disabled' => !$is_pro) );
			
			?>
			<?php
				$ikfb_event_image_sizes = array(
					'normal' => 'Normal',
					'small' => 'Small',
					'large' => 'Large',
					'square' => 'Square'
				);			
				$this->shed->select( array('name' => 'ik_fb_event_image_size', 'options' => $ikfb_event_image_sizes, 'label' =>'Event Feed Image Size', 'value' => get_option('ik_fb_event_image_size'), 'description' => 'Select which size of image to display with Events in your Feed.', 'disabled' => !$is_pro) );
			?>
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