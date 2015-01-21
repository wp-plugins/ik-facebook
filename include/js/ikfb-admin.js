var gold_plugins_init_selectable_code_boxes = function () {
	jQuery('input.gp_code_to_copy').bind('click', function () {
		jQuery(this).select();
		console.log('what');
	});
};

	
jQuery(function () {
	 gold_plugins_init_selectable_code_boxes();
	 scg = jQuery('#shortcode_generator');
	 if (typeof(GoldPlugins_ShortcodeGenerator) == 'function' && scg.length > 0)
	 {
		var frm = scg.find('form:first');
		var generator = new GoldPlugins_ShortcodeGenerator('ik_fb_feed', frm);
		generator.addTextAttribute('profile_id');
		generator.addNumberAttribute('post_count', '');
		generator.addOptionalAttribute('style');
		generator.addCustomAttribute('more_than_10', function (field_id, values) {
			var val = (parseInt(values.post_count) > 10) ? 'yes' : 'no';
			return field_id + '="' + val + '"';
		});
		generator.addCustomAttribute('before_or_after', function (field_id, values) {
			var selector = 'input[name=\'' + field_id + '\']:checked';
			var val = root.get_value_from_input(selector);
			return (val && val != 'none') ? (field_id + '="' + val + '"') : '';
		});
		generator.addCheckboxAttribute('checkboxes');


		jQuery('#generate').on('click', function () {
			var output  = generator.buildShortcode();
			jQuery('#shortcode').val(output);
			return false;
		});
	}
}); 