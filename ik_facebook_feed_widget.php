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

Shout out to http://www.makeuseof.com/tag/how-to-create-wordpress-widgets/ for the help
*/

class ikFacebookFeedWidget extends WP_Widget
{
	function ikFacebookFeedWidget(){
		$widget_ops = array('classname' => 'ikFacebookWidget', 'description' => 'Displays the Facebook Feed' );
		$this->WP_Widget('ikFacebookWidget', 'IK Facebook Feed', $widget_ops);
	}

	function form($instance){
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'use_thumb' => true, 'image_width' => '', 'colorscheme' => false ) );
		$title = $instance['title'];
		$colorscheme = $instance['colorscheme'];
		?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('colorscheme'); ?>">Color Scheme (light or dark): <input class="widefat" id="<?php echo $this->get_field_id('colorscheme'); ?>" name="<?php echo $this->get_field_name('colorscheme'); ?>" type="text" value="<?php echo attribute_escape($colorscheme); ?>" /></label></p>
		<?php
	}

	function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['colorscheme'] = strip_tags( $new_instance['colorscheme'] );
		return $instance;
	}

	function widget($args, $instance){
		extract($args, EXTR_SKIP);

		echo $before_widget;
		$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
		$colorscheme = empty($instance['colorscheme']) ? 'light' : $instance['colorscheme'];

		if (!empty($title))
			echo $before_title . $title . $after_title;;

		if (!isset($ik_fb)){
			$ik_fb = new ikFacebook();
		}

		//$colorscheme = "light", $use_thumb = true, $width = "", $is_sidebar_widget = false
		echo $ik_fb->ik_fb_output_feed($colorscheme,true,'',true);

		echo $after_widget;
	} 
}
?>