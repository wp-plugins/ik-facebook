<h3>Pro Registration</h3>			
<?php if(!is_valid_key()): ?><p>Enter your Email Address and API Key here to activate additional features such as Custom HTML, Unbranded Admin Screens, Comments, Avatars, and more!</p><?php endif; ?>
<?php if(is_valid_key()): ?>	
<p class="plugin_is_registered">Your plugin is succesfully registered and activated!</p>
<?php else: ?>
<p class="plugin_is_not_registered">Your plugin has not been successfully registered and activated. <a href="http://goldplugins.com/our-plugins/wp-social-pro/?utm_source=api_key_reminder" target="_blank">Click here</a> to upgrade now!</p>
<?php endif; ?>	
<style type="text/css">
.plugin_is_registered {
    background-color: #90EE90;
    font-weight: bold;
    padding: 20px;
    width: 860px;
}
.plugin_is_not_registered {
	background-color: #FF8C00;
    font-weight: bold;
    padding: 20px;
    min-width: 860px;
}
</style>
<?php if(!wpsp_is_valid_multisite_key()): ?>
<table class="form-table">
	<tr valign="top">
		<th scope="row"><label for="wp_social_pro_registered_email">Email Address</label></th>
		<td><input type="text" name="wp_social_pro_registered_email" id="wp_social_pro_registered_email" value="<?php echo get_option('wp_social_pro_registered_email'); ?>"  style="width: 250px" />
		<p class="description">This is the e-mail address that you used when you registered the plugin.</p>
		</td>
	</tr>
</table>
	
<table class="form-table">
	<tr valign="top">
		<th scope="row"><label for="wp_social_pro_registered_key">API Key</label></th>
		<td><input type="text" name="wp_social_pro_registered_key" id="wp_social_pro_registered_key" value="<?php echo get_option('wp_social_pro_registered_key'); ?>"  style="width: 250px" />
		<p class="description">This is the API Key that you received after registering the plugin.</p>
		</td>
	</tr>
</table>
<?php endif; ?>