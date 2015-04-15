<h3>WP Social Pro Registration</h3>			
<?php if(is_valid_key()): ?>
<p class="plugin_is_registered">&#x2713; WP Social Pro is registered and activated. Thank you!</p>
<?php else: ?>
<p class="plugin_is_not_registered">&#x2718; Pro features not available. Upgrade to WP Social Pro to unlock all features. <a class="button" href="http://goldplugins.com/our-plugins/wp-social-pro/upgrade-to-wp-social-pro/?utm_source=api_key_reminder" target="_blank">Click here to upgrade now!</a></p>
<p>Enter your Email Address and API Key here to activate additional features such as Custom HTML, Unbranded Admin Screens, Comments, Avatars, and more!</p>
<p><a class="button" href="http://goldplugins.com/our-plugins/wp-social-pro/upgrade-to-wp-social-pro/?utm_source=plugin&utm_campaign=api_key_reminder_2">Get An API Key</a></p>
<?php endif; ?>

<?php if(!wpsp_is_valid_multisite_key()): ?>
<table class="form-table">
	<?php
		// Registration Email
		$this->shed->text( array('name' => 'wp_social_pro_registered_email', 'label' =>'Email Address', 'value' => get_option('wp_social_pro_registered_email'), 'description' => 'This is the e-mail address that you used when you registered the plugin.') );

		// API Key
		$this->shed->text( array('name' => 'wp_social_pro_registered_key', 'label' =>'API Key', 'value' => get_option('wp_social_pro_registered_key'), 'description' => 'This is the API Key that you received after registering the plugin.') );
	?>
</table>	
<?php endif; ?>