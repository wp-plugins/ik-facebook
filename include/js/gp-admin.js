var gold_plugins_init_mailchimp_form = function () {
	var $form = jQuery('#mc-embedded-subscribe-form');
	if ($form.length > 0) {
	
		// bind to form's submit action to reveal codes/links
		$form.bind('submit', function () {
			var coupon_html = '<div id="mc-show-coupon-codes"> <h3>Redeem Your Discount Now:</h3> <p class="thx">Thanks for subscribing! Please use the links below to save 20% on @plugin_name.</p> <div class="upgrade_links"> <div class="upgrade_link"> <div class="package"> <a href="@personal_url" target="_blank">Personal License - <strike>$59</strike> $47.20</a> </div> <div class="desc"> <a href="@personal_url" target="_blank">Use it on a single website</a> </div> </div> <div class="upgrade_link"> <div class="package"> <a href="@biz_url" target="_blank">Business License - <strike>$99</strike> $79.20</a> </div> <div class="desc"> <a href="@biz_url" target="_blank">Use it on any 3 websites!</a> </div> </div> <div class="upgrade_link"> <div class="package"> <a href="@dev_url" target="_blank">Developer License - <strike>$199</strike> $159.20</a> </div> <div class="desc"> <a href="@dev_url" target="_blank">Use it on unlimited websites!</a> </div> </div> </div> <p class="tip">Tip: you can also use the coupon code <strong>newsub20</strong> to save 20% of *any* product on our website, <a href="http://goldplugins.com/?utm_source=plugin&utm_campaign=save20_sitewide">GoldPlugins.com</a>!</p> </div>';
			
			// replace links in the HTML before inserting it
			$plugin_name = jQuery('#mc-upgrade-plugin-name').val();
			$personal_url = jQuery('#mc-upgrade-link-per').val();
			$biz_url = jQuery('#mc-upgrade-link-biz').val();
			$dev_url = jQuery('#mc-upgrade-link-dev').val();
			coupon_html = coupon_html.replace(/@plugin_name/g, $plugin_name);
			coupon_html = coupon_html.replace(/@personal_url/g, $personal_url);
			coupon_html = coupon_html.replace(/@biz_url/g, $biz_url);
			coupon_html = coupon_html.replace(/@dev_url/g, $dev_url);						
			var coupon_div = jQuery(coupon_html);

			// make the whole button clickable
			coupon_div.on('click', function (e) {
				if( !jQuery("a").is(e.target) ) {
					$href = jQuery(this).find('a:first').attr('href');
					// try to open in a new tab
					window.open(
					  $href,
					  '_blank'
					);
					return false;
					
				}
				return true;
			});				
			
			// replace the form with the coupons!
			$form.after(coupon_div);
			$form.css('display', 'none');
			
			return true;
		});
	
	
	
	}
};