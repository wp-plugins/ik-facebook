<?php
/*
Plugin Name: IK Facebook Plugin
Plugin URI: http://goldplugins.com/documentation/wp-social-pro-documentation/the-ik-facebook-plugin/
Description: IK Facebook Plugin - A Facebook Solution for WordPress
Author: Illuminati Karate, Inc.
Version: 2.6.3.4
Author URI: http://illuminatikarate.com

This file is part of the IK Facebook Plugin.

The IK Facebook Plugin is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

The IK Facebook Plugin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with the IK Facebook Plugin .  If not, see <http://www.gnu.org/licenses/>.
*/
include('include/widgets/ik_facebook_feed_widget.php');
include('include/widgets/ik_facebook_like_button_widget.php');
include('include/ik_facebook_options.php');
include('include/lib/CachedCurl.php');
include('include/lib/lib.php');
include('include/lib/ik_social_pro.php');
$ik_fb_options = new ikFacebookOptions();

//use this to track if css/powered by have been output
global $ikfb_footer_css_output;
global $ikfb_footer_poweredby_output;

class ikFacebook
{
	var $authToken;
	var $textdomain = "iksocialpro";

	function __construct(){
		//create shortcodes
		add_shortcode('ik_fb_feed', array($this, 'ik_fb_output_feed_shortcode'));
		add_shortcode('ik_fb_gallery', array($this, 'ik_fb_output_gallery_shortcode'));
		add_shortcode('ik_fb_like_button', array($this, 'ik_fb_output_like_button'));

		//add CSS
		add_action( 'wp_enqueue_scripts', array($this, 'ik_fb_setup_css'));
		add_action( 'wp_head', array($this, 'ik_fb_setup_custom_css'));
		add_action( 'wp_enqueue_scripts', array($this, 'ik_fb_setup_custom_theme_css'));

		//register sidebar widgets
		add_action( 'widgets_init', array($this, 'ik_fb_register_widgets' ));

		//display "powered by"
		add_action('wp_footer', array($this, 'ik_fb_show_powered_by' ));
		
		add_action( 'admin_init', array($this, 'ikfb_admin_init') );
	}
	
    function ikfb_admin_init() {
        wp_enqueue_style( 'farbtastic' );
		wp_enqueue_script( 'farbtastic' );
		wp_enqueue_script( 'ik_fb_pro_options', plugins_url('include/js/js.js', __FILE__), array( 'farbtastic', 'jquery' ) );
    }

	//register any widgets here
	function ik_fb_register_widgets() {
		register_widget( 'ikFacebookFeedWidget' );
		register_widget( 'ikFacebookLikeButtonWidget' );
	}
	
	//add Basic CSS
	function ik_fb_setup_css() {						
		$ikfb_themes = array(
			'ik_facebook_style' => 'include/css/style.css',
			'ik_facebook_dark_style' => 'include/css/dark_style.css',
			'ik_facebook_light_style' => 'include/css/light_style.css',
			'ik_facebook_blue_style' => 'include/css/blue_style.css',
			'ik_facebook_no_style' => 'include/css/no_style.css',
			'ik_facebook_gallery_style' => 'include/css/gallery.css',
		);
		
		if(is_valid_key(get_option('ik_fb_pro_key'))){
			$ikfb_themes['ik_facebook_cobalt_style'] = 'include/css/cobalt_style.css';
			$ikfb_themes['ik_facebook_green_gray_style'] = 'include/css/green_gray_style.css';
			$ikfb_themes['ik_facebook_halloween_style'] = 'include/css/halloween_style.css';
			$ikfb_themes['ik_facebook_indigo_style'] = 'include/css/indigo_style.css';
			$ikfb_themes['ik_facebook_orange_style'] = 'include/css/orange_style.css';			
		}
	
		foreach($ikfb_themes as $name => $path){
			wp_register_style( $name, plugins_url($path, __FILE__) );
		}
		
		wp_enqueue_style( 'ik_facebook_' . get_option('ik_fb_feed_theme'));
		wp_enqueue_style( 'ik_facebook_gallery_style' );
	}

	//add Custom CSS
	function ik_fb_setup_custom_css() {
		//use this to track if css has been output
		global $ikfb_footer_css_output;
		
		if($ikfb_footer_css_output){
			return;
		} else {
			echo '<!--IKFB CSS--> <style type="text/css" media="screen">' . get_option('ik_fb_custom_css') . "</style>";
			$ikfb_footer_css_output = true;
		}
	}
	
	//add Custom CSS from Theme
	function ik_fb_setup_custom_theme_css() {
		//only enqueue CSS if it's there
		if(file_exists(get_stylesheet_directory() . '/ik_fb_custom_style.css' )){
			wp_register_style( 'ik_facebook_custom_style', get_stylesheet_directory_uri() . '/ik_fb_custom_style.css' );
			wp_enqueue_style( 'ik_facebook_custom_style' );
		}
	}
	
	//generates the like button HTML
	function ik_fb_like_button($url, $height = "45", $colorscheme = "light"){
		return '<iframe id="like_button" src="//www.facebook.com/plugins/like.php?href='.htmlentities(urlencode($url).'&layout=standard&show_faces=false&action=like&colorscheme='.$colorscheme.'&height='.$height).'"></iframe>';//add facebook like button
	}
	
	//output the like button
	function ik_fb_output_like_button($atts){		
		//load shortcode attributes into an array
		extract( shortcode_atts( array(
			'url' => site_url(),
			'height' => '45',
			'colorscheme' => 'light'
		), $atts ) );
		
		return $this->ik_fb_like_button($url,$height,$colorscheme);
	}
	
	function ik_fb_output_feed_shortcode($atts){			
		//load shortcode attributes into an array
		extract( shortcode_atts( array(
			'colorscheme' => 'light',
			'width' => get_option('ik_fb_feed_image_width'),
			'height' => get_option('ik_fb_feed_image_height'),
			'use_thumb' => !get_option('ik_fb_fix_feed_image_width') && !get_option('ik_fb_fix_feed_image_height'),
			'num_posts' => null,
			'id' => false,
			'show_errors' => false
		), $atts ) );
		
		return $this->ik_fb_output_feed($colorscheme, $use_thumb, $width, false, $height, $num_posts, $id, $show_errors);				
	}
	
	function ik_fb_output_gallery_shortcode($atts){			
		//load shortcode attributes into an array
		extract( shortcode_atts( array(
			'id' => '',
			'size' => '320x180',
			'show_name' => true,
			'title' => null,
			'num_photos' => false
		), $atts ) );
		
		return $this->ik_fb_output_gallery($id, $size, $show_name, $title, $num_photos);				
	}
	
	public function ik_fb_output_gallery($id = '', $size = '320x180', $show_name = true, $the_title = null, $num_photos = false){
		$output = '';
		
		$app_id = get_option('ik_fb_app_id');
		$app_secret = get_option('ik_fb_secret_key');
		
		$size_array = array(
			'2048x1152' => 0,
			'960x540' => 1,
			'720x405' => 2,
			'600x337' => 3,
			'480x270' => 4,
			'320x180' => 5,
			'130x73' => 7
		);
		
		$width_array = array (
			'2048x1152' => '1152px',
			'960x540' => '540px',
			'720x405' => '405px',
			'600x337' => '337px',
			'480x270' => '270px',
			'320x180' => '180px',
			'130x73' => '73px'
		);
		
		$height_array = array (
			'2048x1152' => '2048px',
			'960x540' => '960px',
			'720x405' => '720px',
			'600x337' => '600px',
			'480x270' => '480px',
			'320x180' => '320px',
			'130x73' => '130px'
		);
		
		$position = $size_array[$size];
		
		if(!isset($this->authToken)){
			$this->authToken = $this->fetchUrl("https://graph.facebook.com/oauth/access_token?type=client_cred&client_id={$app_id}&client_secret={$app_secret}");
		}
			
		//see if a limit is set in the options, if one wasn't passed via shortcode
		if(!$num_photos){
			$limit = get_option('ik_fb_photo_feed_limit');
	
		} else {
			$limit = $num_photos;
		}
		
		//make sure its really a number, otherwise we default to 25
		if(!is_numeric($limit)){				
			$limit = 25;
		}
		
		$gallery = $this->fetchUrl("https://graph.facebook.com/{$id}/photos?limit={$limit}&summary=1&{$this->authToken}", true);//the gallery data
		
		ob_start();
		
		echo '<div class="ik_fb_gallery_standard">';
		
		if(isset($the_title)){
			echo '<span class="ik_fb_gallery_standard_title">' . $the_title . '</span>';
		}
		
		if(isset($gallery->data)){
			foreach($gallery->data as $gallery_item){
				if(isset($gallery_item->images[$position]->source)){	
					//	echo '<div class="ik_fb_gallery_item" style="width:'.$width_array[$size].';height:'.$height_array[$size].';">';			
					echo '<div class="ik_fb_gallery_item ik_fb_gallery_'.$size.'">';
					
					$name = "";
					if(isset($gallery_item->name)){
						$name = $gallery_item->name;
					}
				
					echo '<a href="'.htmlentities($gallery_item->source).'" target="_blank" title="'. __('Click to View Full Sized Photo', $this->textdomain) . '"><img class="ik_fb_standard_image" src="'.$gallery_item->images[$position]->source.'" alt="' . htmlentities($name) . '" /></a>';
				
					if($show_name){
						echo '<p class="ik_fb_standard_image_name">' . $name . '</p>';
					}
					
					echo '</div>';
				}
				
			}
		} else {
			echo '<p class="ik_fb_error">'.__('IK FB: Unable to load photos.', $this->textdomain).'</p>';
		}
		
		echo '</div>';
		
		$output = ob_get_contents();
		
		ob_end_clean();
		
		return $output;
	}
	
	//facebook feed
	public function ik_fb_output_feed($colorscheme = "light", $use_thumb = true, $width = "", $is_sidebar_widget = false, $height = "", $num_posts = null, $id = false, $show_errors = false){			
		$show_only_events = get_option('ik_fb_show_only_events');
		$show_only_events = ($show_only_events) ? 1 : 0;
		
		$content_type = ($show_only_events) ? "events" : "";
		
		//load facebook data
		$fbData = $this->loadFacebook($id, $num_posts, $content_type);
				
		$feed = isset($fbData['feed']) ? $fbData['feed'] : array();
		
		$page_data = $fbData['page_data'];
		
		//check and see if there is a start time - this will indicate whether or not this is an event!
		if(isset($page_data->start_time)){
			$is_event = true;
		} else {
			$is_event = false;
		}
		
		$ik_fb_header_bg_color = strlen(get_option('ik_fb_header_bg_color')) > 2 && !get_option('ik_fb_use_custom_html') ? get_option('ik_fb_header_bg_color') : '';
		$ik_fb_window_bg_color = strlen(get_option('ik_fb_window_bg_color')) > 2 && !get_option('ik_fb_use_custom_html') ? get_option('ik_fb_window_bg_color') : '';
		
		//use different heigh/width styling options, if this is the sidebar widget
		if(!$is_sidebar_widget){
			$ik_fb_feed_height = strlen(get_option('ik_fb_feed_window_height')) > 0 && !get_option('ik_fb_use_custom_html') ? get_option('ik_fb_feed_window_height') : '';
			$ik_fb_feed_width = strlen(get_option('ik_fb_feed_window_width')) > 0 && !get_option('ik_fb_use_custom_html') ? get_option('ik_fb_feed_window_width') : '';
				
			if($ik_fb_feed_width == "OTHER"){
				$ik_fb_feed_width = str_replace("px", "", get_option('other_ik_fb_feed_window_width')) . "px";
			}
			
			if($ik_fb_feed_height == "OTHER"){
				$ik_fb_feed_height = str_replace("px", "", get_option('other_ik_fb_feed_window_height')) . "px";
			}
		} else {
			$ik_fb_feed_height = strlen(get_option('ik_fb_sidebar_feed_window_height')) > 0 && !get_option('ik_fb_use_custom_html') ? get_option('ik_fb_sidebar_feed_window_height') : '';
			$ik_fb_feed_width = strlen(get_option('ik_fb_sidebar_feed_window_width')) > 0 && !get_option('ik_fb_use_custom_html') ? get_option('ik_fb_sidebar_feed_window_width') : '';
				
			if($ik_fb_feed_width == "OTHER"){
				$ik_fb_feed_width = str_replace("px", "", get_option('other_ik_fb_sidebar_feed_window_width')) . "px";
			}			
			
			if($ik_fb_feed_height == "OTHER"){
				$ik_fb_feed_height = str_replace("px", "", get_option('other_ik_fb_sidebar_feed_window_height')) . "px";
			}
		}
		
		//feed window width
		$custom_styling_1 = ' style="';
		if(strlen($ik_fb_feed_width)>0){
			$custom_styling_1 .= "width: {$ik_fb_feed_width};";
		}	
		if(strlen($ik_fb_feed_height)>0){		
			$custom_styling_1 .= "height: auto; ";
		}
		$custom_styling_1 .= '"';
		
		//feed window height, feed window bg color
		$custom_styling_2 = ' style="';
		if(strlen($ik_fb_feed_height)>0){		
			$custom_styling_2 .= "height: {$ik_fb_feed_height}; ";
		}
		if(strlen($ik_fb_window_bg_color)>0){
			$custom_styling_2 .= " background-color: {$ik_fb_window_bg_color};";
		}	
		
		$custom_styling_2 .= '"';
		
		//feed heading bg color
		$custom_styling_3 = ' style="';
		if(strlen($ik_fb_header_bg_color)>0){
			$custom_styling_3 .= "background-color: {$ik_fb_header_bg_color};";
		}
		$custom_styling_3 .= '"';
		
		$default_html = '<div id="ik_fb_widget" {custom_styling_1} ><div id="ik_fb_widget_top" {custom_styling_3} ><div class="ik_fb_profile_picture">{ikfb:image}{ikfb:link}</div>{ikfb:like_button}</div><ul class="ik_fb_feed_window" {custom_styling_2} >{ikfb:feed}</ul></div>';
		
		//load custom HTML structure from Pro Plugin, if available and enabled
		$output = strlen(get_option('ik_fb_feed_html')) > 2 && get_option('ik_fb_use_custom_html') ? get_option('ik_fb_feed_html') : $default_html;		
		
		//only display photo if option is set
		if(get_option('ik_fb_show_profile_picture')){
			//use the username if available, otherwise fallback to page ID
			if(isset($page_data->username)){
				$replace = '<img src="//graph.facebook.com/'.$page_data->username.'/picture" alt="profile picture"/>';
				$output = str_replace('{ikfb:image}', $replace, $output);
			} else if(isset($page_data->id)){
				$replace = '<img src="//graph.facebook.com/'.$page_data->id.'/picture" alt="profile picture"/>';
				$output = str_replace('{ikfb:image}', $replace, $output);
			} else { //bad ID has been input, lets try not to crap out
				$output = str_replace('{ikfb:image}', '', $output);
			}
		} else {
			$output = str_replace('{ikfb:image}', '', $output);
		}
		
		
		//only display title if option is set
		if(get_option('ik_fb_show_page_title') && isset($page_data->name)){
			$the_link = "https://www.facebook.com/pages/".urlencode($page_data->name)."/".urlencode($page_data->id);
			
			$replace = '<a target="_blank" href="'.$the_link.'"><span class="ik_fb_name">'.$page_data->name.'</span></a>';	
			$output = str_replace('{ikfb:link}', $replace, $output);	
		} else if(isset($page_data->name)) {
			$the_link = "https://www.facebook.com/pages/".urlencode($page_data->name)."/".urlencode($page_data->id);
			
			$output = str_replace('{ikfb:link}', '', $output);	
		} else { //bad ID has been input, lets try not to crap out
			$output = str_replace('{ikfb:link}', '', $output);
		}

		//events don't have a like button, so display datetime and location
		if(!$is_event){
			//only show like button if enabled in settings
			if(get_option('ik_fb_show_like_button')){
				$replace = $this->ik_fb_like_button($the_link, "45", $colorscheme);
				$output = str_replace('{ikfb:like_button}', $replace, $output);		
			} else {
				$output = str_replace('{ikfb:like_button}', '', $output);		
			}
		} else {
			//TBD: allow the Date Formatting to be controlled by user
			$replace = '<p class="ikfb_event_meta">' . $page_data->location . ', ' . date('M d, Y',strtotime($page_data->start_time)) . '<br/>' . $page_data->venue->street . ', ' . $page_data->venue->city . ', ' . $page_data->venue->country . '</p>';
			$output = str_replace('{ikfb:like_button}', $replace, $output);	
		}
		
		//build line items to replace with
		$replace = '';
		
		if(count($feed)>0){//check to see if feed data is set			
			foreach($feed as $item){//$item is the feed object	
				$replace .= $this->buildFeedLineItem($item, $use_thumb, $width, $page_data, $height, $the_link, $page_data->id);
			}
		} else {
			//something went wrong!
			if($show_errors){
				$replace = "<p class='ik_fb_error'>" . __('IK FB: Unable to load feed.', $this->textdomain) . "</p>";
			} else {
				//hide the feed window, there was an error and we don't want a big blank space messing up websites
				$custom_styling_2 = 'style="display:none;"';
			}
		}			
		
		$output = str_replace('{ikfb:feed}', $replace, $output);
		
		//last step, replace all the custom styling, if it's present
		$output = str_replace('{custom_styling_1}', $custom_styling_1, $output);
		$output = str_replace('{custom_styling_2}', $custom_styling_2, $output);
		$output = str_replace('{custom_styling_3}', $custom_styling_3, $output);
		
		return $output;		
	}
	
	//thanks to Alix Axel, http://stackoverflow.com/questions/2762061/how-to-add-http-if-its-not-exists-in-the-url
	function addhttp($url) {
		if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
			$url = "http://" . $url;
		}
		return $url;
	}
	
	//passed a FB Feed Item, builds the appropriate HTML
	function buildFeedLineItem($item, $use_thumb, $width, $page_data, $height, $the_link = false, $page_id = null){
		global $ik_social_pro;
		
		//build default HTML structure
		$default_feed_item_html = '<li class="ik_fb_feed_item">{ikfb:feed_item}</li>';		
		$default_message_html = '<p>{ikfb:feed_item:message}</p>';		
		$default_image_html = '<p class="ik_fb_facebook_image">{ikfb:feed_item:image}</p>';		
		$default_description_html = '<p class="ik_fb_facebook_description">{ikfb:feed_item:description}</p>';		
		$default_caption_html = '<p class="ik_fb_facebook_link">{ikfb:feed_item:link}</p>';	
		
		$feed_item_html = strlen(get_option('ik_fb_feed_item_html')) > 2 && get_option('ik_fb_use_custom_html') ? get_option('ik_fb_feed_item_html') : $default_feed_item_html;
		$message_html = strlen(get_option('ik_fb_message_html')) > 2 && get_option('ik_fb_use_custom_html') ? get_option('ik_fb_message_html') : $default_message_html;
		$image_html = strlen(get_option('ik_fb_image_html')) > 2 && get_option('ik_fb_use_custom_html') ? get_option('ik_fb_image_html') : $default_image_html;
		$description_html = strlen(get_option('ik_fb_description_html')) > 2 && get_option('ik_fb_use_custom_html') ? get_option('ik_fb_description_html') : $default_description_html;
		$caption_html = strlen(get_option('ik_fb_caption_html')) > 2 && get_option('ik_fb_use_custom_html') ? get_option('ik_fb_caption_html') : $default_caption_html;
		
		$output = '';
		
		$add_feed_item = false;
		
		if(is_valid_key(get_option('ik_fb_pro_key'))){
			$add_feed_item = $ik_social_pro->is_page_owner($item,$page_data);
		} else {
			$add_feed_item = true;
		}
		
		//parse post date for output
		$date = "";
		
		if(isset($item->created_time)){
			$date = $item->created_time;
		}
		
		$line_item = '';
		$shortened = false;
		
		if($add_feed_item){
			$replace = "";
		
			//output the item message
			if(isset($item->message)){				
				//add avatar for pro users
				if(is_valid_key(get_option('ik_fb_pro_key'))){		
					$replace = $ik_social_pro->pro_user_avatars($replace, $item) . " ";
				}
				
				$replace = $replace . htmlspecialchars($item->message);
				
				//if a character limit is set, here is the logic to handle that
				$limit = get_option('ik_fb_character_limit');
				if(is_numeric($limit)){
					//only perform changes on posts longer than the character limit
					if(strlen($replace) > $limit){
						//remove characters beyond limit
						$replace = substr($replace, 0, $limit);
						$replace .= __('... ', $this->textdomain);
						
						$shortened = true;
					}
				}
				
				//add custom message styling from pro options
				if(!get_option('ik_fb_use_custom_html')){		
					$message_html = $this->ikfb_message_styling($message_html);
				}		
				
				$line_item .= str_replace('{ikfb:feed_item:message}', $replace, $message_html);			
			}				

			//output the item photo
			if(isset($item->picture)){ 		
				if(!isset($this->authToken)){
					$this->authToken = $this->fetchUrl("https://graph.facebook.com/oauth/access_token?type=client_cred&client_id={$app_id}&client_secret={$app_secret}");
				}	
				
				//need info about full sized photo for linking purposes
				//get the item id
				$item_id = isset($item->object_id) ? $item->object_id : -999;
								
				if($item_id != -999){
					$photo = $this->fetchUrl("https://graph.facebook.com/{$item_id}/picture?summary=1&{$this->authToken}&redirect=false", true);	
				}

				//load arguments into array for use below
				$parsed_url = parse_url($item->picture);
				
				if(isset($parsed_url['query'])){
					parse_str($parsed_url['query'], $params);               
				}
				
				if(isset($photo->data->url)){
					$photo_link = $photo->data->url;
					$photo_source = $photo->data->url;
				} else if(isset($params['url'])) {
					$photo_link = $params['url'];
					$photo_source = $params['url'];
				} else {
					$photo_link = $item->picture;
					$photo_source = $item->picture;
				}
				
				if(get_option('ik_fb_link_photo_to_feed_item')){
					$item_id = explode("_",$item->id);
					$photo_link = "https://www.facebook.com/permalink.php?id=".urlencode($page_id)."&story_fbid=".urlencode($item_id[1]);
				}
				
				//output the images
				//if set, load the custom image width from the options page
				if(!$use_thumb){								
					//if using custom width, output fullsized image
					$width = get_option('ik_fb_fix_feed_image_width') ? $width : '';
					$height = get_option('ik_fb_fix_feed_image_height') ? $height : '';	
					
					if($width == "OTHER"){
						$width = get_option('other_ik_fb_feed_image_width');
					}
					
					if($height == "OTHER"){
						$height = get_option('other_ik_fb_feed_image_height');
					}
					
					//source: tim morozzo
					if (isset($item->description) && strlen($item->description) >5){
						$title = $item->description;
					}elseif (isset($item->message))
					{
						$title = $item->message;
					}else{ 
						$title = __('Click for fullsize photo', $this->textdomain);
					}
					
					$limit = get_option('ik_fb_description_character_limit');
					
					if(is_numeric($limit)){
						if(strlen($limit) > $limit){
							//remove characters beyond limit
							$title = substr($limit, 0, $limit);
							$title .= __('... ', $this->textdomain);

							$shortened = true;
						}
					}
					
					//replace ampersands and equal signs, and whatever else
					$photo_link = str_replace("&","&amp;",$photo_link);
					
					$width = strlen($width)>0 ? 'width="'.$width.'"' : '';
					$height = strlen($height)>0 ? 'height="'.$height.'"' : '';
					
					$replace = '<a href="'.$photo_link.'" title="'.htmlspecialchars($title, ENT_QUOTES).'" target="_blank"><img '.$width.' '.$height.' src="'.$photo_source.'" alt="'.htmlspecialchars($title, ENT_QUOTES).'"/></a>';
					
					//if set, hide feed images	
					if(get_option('ik_fb_hide_feed_images')){
						$replace = '';
					}
						
					$line_item .= str_replace('{ikfb:feed_item:image}', $replace, $image_html);						
				} else {						
					//ampersands!
					$item->picture = str_replace("&","&amp;",$item->picture);
					
					//otherwise, use thumbnail
					$replace = '<a href="'.$photo_link.'" target="_blank"><img src="'.$item->picture.'" alt="Click for fullsize photo"/></a>';
					
					//if set, hide feed images
					if(get_option('ik_fb_hide_feed_images')){
						$replace = '';
					}
							
					$line_item .= str_replace('{ikfb:feed_item:image}', $replace, $image_html);	
				}

				//add the text for photo description
				if(isset($item->description)){
					$replace = $item->description;	

					//if a character limit is set, here is the logic to handle that
					$limit = get_option('ik_fb_description_character_limit');
					if(is_numeric($limit)){
						//only perform changes on posts longer than the character limit
						if(strlen($replace) > $limit){
							//remove characters beyond limit
							$replace = substr($replace, 0, $limit);
							$replace .= __('... ', $this->textdomain);
						
							$shortened = true;
						}
					}					
				
					//add custom image styling from pro options
					if(!get_option('ik_fb_use_custom_html')){		
						$description_html = $this->ikfb_description_styling($description_html);
					}	
					
					$line_item .= str_replace('{ikfb:feed_item:description}', $replace, $description_html);	
				}
			}			
			
			if($shortened){
				$item_id = explode("_",$item->id);
				$the_link = "https://www.facebook.com/permalink.php" . htmlentities("?id=".urlencode($page_id)."&story_fbid=".urlencode($item_id[1]));				
				$line_item .= ' <a href="'.$the_link.'" class="ikfb_read_more" target="_blank">'.__('Read More...', $this->textdomain).'</a>';
			}	

			if(isset($item->link)){ //output the item link				
				if(isset($item->caption) && isset($item->picture)){
					$link_text = $item->caption; //some items have a caption	
				} else if(isset($item->description)){
					$link_text = $item->description; //some items have a description	
				} else {
					$link_text = isset($item->name) ? $item->name : '';  //others might just have a name
				}
				
				//don't add the line item if the link text isn't set
				if(strlen($link_text) > 1){
					//prevent validation errors
					$item->link = str_replace("&","&amp;",$item->link);
					
					$replace_front = '<a href="'.htmlentities($item->link).'" target="_blank">';
					$replace_back = $link_text.'</a>';				
				
					//add custom link styling from pro options
					if(!get_option('ik_fb_use_custom_html')){		
						$replace_front = $this->ikfb_link_styling($item->link);
					}	
					
					$line_item .= str_replace('{ikfb:feed_item:link}', $replace_front.$replace_back, $caption_html);	
				}
			}	
			
			if(strlen($line_item)>2){
				
				//output Posted By... text, if option is set
				if(get_option('ik_fb_show_posted_by')){
					//only add the author if there is line item content to display
					if(isset($item->from)){ //output the author of the item
						if(isset($item->from->name)){
							$from_text = $item->from->name;
						}
						
						if(strlen($from_text) > 1){
							$posted_by_text = '<p class="ikfb_item_author">' . __('Posted By ', $this->textdomain) . $from_text . '</p>';
				
							//add custom posted by styling from pro options
							if(!get_option('ik_fb_use_custom_html')){		
								$posted_by_text = $this->ikfb_posted_by_styling($posted_by_text);
							}			
							//TBD: make Custom HTML option for Posted By
							$line_item .= $posted_by_text;
						}
					}
				}
				
				//output date, if option to display it is enabled
				//TBD: Allow user control over date formatting
				if(get_option('ik_fb_show_date')){
					setlocale(LC_TIME, WPLANG);
					$ik_fb_use_human_timing = get_option('ik_fb_use_human_timing');
					if(strtotime($date) >= strtotime('-1 day') && !$ik_fb_use_human_timing){
						$date = $this->humanTiming(strtotime($date)). __(' ago', $this->textdomain);
					}else{
						$ik_fb_date_format = get_option('ik_fb_date_format');
						$ik_fb_date_format = strlen($ik_fb_date_format) > 2 ? $ik_fb_date_format : "%B %d";
						$date = strftime($ik_fb_date_format, strtotime($date));
					}
				
					if(strlen($date)>2){
						$date = '<p class="date">' . $date . '</p>';
						
						//add custom date styling from  options
						if(!get_option('ik_fb_use_custom_html')){		
							$date = $this->ikfb_date_styling($date);
						}
					}
										
					$line_item .= $date;
				}
			
				//add likes, if pro and enabled
				if(is_valid_key(get_option('ik_fb_pro_key'))){		
					$line_item .= $ik_social_pro->pro_likes($item, $the_link);
				}
				
				//add comments, if pro and enabled
				if(is_valid_key(get_option('ik_fb_pro_key'))){		
					$line_item .= $ik_social_pro->pro_comments($item, $the_link);
				}	
			
				$output = str_replace('{ikfb:feed_item}', $line_item, $feed_item_html);	
			} else if((isset($item->link) && strpos($item->link,'http://www.facebook.com/events/') !== false) || get_option('ik_fb_show_only_events')){
				//some event parsing				
				$event_id = explode('/',$item->link);
				$event_id = $event_id[4];
				
				if(get_option('ik_fb_show_only_events')){
					$event_id = $item->id;
				}
				
				if($event_id){
					$app_id = get_option('ik_fb_app_id');
					$app_secret = get_option('ik_fb_secret_key');
					
					if(!isset($this->authToken)){
						$this->authToken = $this->fetchUrl("https://graph.facebook.com/oauth/access_token?type=client_cred&client_id={$app_id}&client_secret={$app_secret}");
					}
					
					$event_data = $this->fetchUrl("https://graph.facebook.com/{$event_id}?summary=1&{$this->authToken}", true);//the event data
					
					$replace = '';	
					
					//add avatar for pro users
					if(is_valid_key(get_option('ik_fb_pro_key'))){		
						$replace = $ik_social_pro->pro_user_avatars($replace, $item) . " ";
					}
					
					//load event image source
					$event_image = "http://graph.facebook.com/" . $event_id . "/picture";
					
					if(isset($event_data->name)){
						//event name
						$replace = '<p class="ikfb_event_title">' . $replace . $event_data->name . '</p>';
						
						$start_time = isset($event_data->start_time) ? $event_data->start_time : '';
						$end_time = isset($event_data->end_time) ? $event_data->end_time : '';			
						
						//TBD: Allow user control over date formatting
						$time_object = new DateTime($start_time);
						$start_time = $time_object->format('l, F jS, Y h:i:s a');	
						
						//TBD: Allow user control over date formatting
						if(strlen($end_time)>2){
							$time_object = new DateTime($end_time);
							$end_time = $time_object->format('l, F jS, Y h:i:s a');						
						}
						
						//event start time - event end time					
						$event_start_time = isset($event_data->start_time) ? $start_time : '';					
						$event_end_time = isset($event_data->end_time) ? $end_time : '';
						
						$replace .= '<p class="ikfb_event_date">';
						$event_had_start = false;
						if(strlen($event_start_time)>2){
							$replace .= $event_start_time;
							$event_had_start = true;
						}
						if($event_had_start){
							$replace .= ' - ';
						}
						if(strlen($event_end_time)>2){
							$replace .= $event_end_time; 
						}
						$replace .= '</p>';
						
						//event image					
						$replace .= '<img class="ikfb_event_image" src="' . $event_image . '" alt="Event Image"/>';					
						
						//event description
						if(isset($event_data->description)){
							$event_description = substr($event_data->description, 0, 250);
							$event_description .= __('... ', $this->textdomain);
								
							$replace .= '<p class="ikfb_event_description">' . $event_description . '</p>';
						}
						
						//event read more link
						$replace .= '<p class="ikfb_event_link"><a href="http://facebook.com/events/'.urlencode($event_id).'" title="Click Here To Read More" target="_blank">Read More...</a></p>';
					}
					
					$output = str_replace('{ikfb:feed_item}', $replace, $feed_item_html);	
				}
			}			
		}
		
		return $output;
	}
	
	//check to see time elapsed since given datetime
	//credit to http://stackoverflow.com/questions/2915864/php-how-to-find-the-time-elapsed-since-a-date-time
	function humanTiming ($time)	{
		$time = time() - $time; // to get the time since that moment
	
		$tokens = array (
			31536000 => __('year', $this->textdomain),
			2592000 => __('month', $this->textdomain),
			604800 => __('week', $this->textdomain),
			86400 => __('day', $this->textdomain),
			3600 => __('hour', $this->textdomain),
			60 => __('minute', $this->textdomain),
			1 => __('second', $this->textdomain)
		);

		foreach ($tokens as $unit => $text) {
			if ($time < $unit) continue;
			$numberOfUnits = floor($time / $unit);
			return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
		}

	}
	
	//checks settings and outputs Powered By link
	function ik_fb_show_powered_by() {
		//use this to track if powered by has been output
		global $ikfb_footer_poweredby_output;
				
		if(get_option('ik_fb_powered_by')){			
			if($ikfb_footer_poweredby_output){
				return;
			} else {			
				$content = '<a href="https://illuminatikarate.com/ik-facebook-plugin/" target="_blank" id="ikfb_powered_by">'.__('Powered By IK Facebook Plugin', $this->textdomain).'</a>';			
				
				//add custom powered by styling from pro options
				if(!get_option('ik_fb_use_custom_html')){		
					$content = $this->ikfb_powered_by_styling($content);
				}		
				
				echo $content;
				
				$ikfb_footer_poweredby_output = true;
			}
		}
	}
	
	//fetches an URL
	function fetchUrl($url,$decode=false){		
		//caching
		$ch = new CachedCurl();
		$retData = $ch->load_url($url);
		
		if($decode){
			$retData = json_decode($retData);
		}
		
		return $retData;
	}
	
	//loads facebook feed based on current id
	function loadFacebook($id = false, $num_posts = -1, $content_type = ''){
		$retData = array();
	
		if(!$id){
			$profile_id = get_option('ik_fb_page_id'); //id of the facebook page
		} else {
			$profile_id = $id;
		}
	
		if(isset($profile_id) && strlen($profile_id)>0){
			$app_id = get_option('ik_fb_app_id');
			$app_secret = get_option('ik_fb_secret_key');
			
			if(!isset($this->authToken)){
				$this->authToken = $this->fetchUrl("https://graph.facebook.com/oauth/access_token?type=client_cred&client_id={$app_id}&client_secret={$app_secret}");
			}
			
			//see if a limit is set in the options, if one wasn't passed via shortcode
			if(!$num_posts){
				$limit = get_option('ik_fb_feed_limit');
			} else {
				$limit = $num_posts;
			}
			
			//make sure its really a number, otherwise we default to 25
			if(!is_numeric($limit)){				
				$limit = 25;
			}
			
			//handle events
			if($content_type == "events") {
				$feed = $this->fetchUrl("https://graph.facebook.com/{$profile_id}/events?limit={$limit}&{$this->authToken}", true);//the feed data
			} else {
				$feed = $this->fetchUrl("https://graph.facebook.com/{$profile_id}/feed?limit={$limit}&{$this->authToken}", true);//the feed data
			}	
						
			$page_data = $this->fetchUrl("https://graph.facebook.com/{$profile_id}?{$this->authToken}", true);//the page data
			
			if(isset($feed->data)){//check to see if feed data is set				
				$retData['feed'] = $feed->data;
			}
			if(isset($page_data)){//check to see if page data is set
				$retData['page_data'] = $page_data;
			}
		}
		
		return $retData;
	}
	
	/* Styling Functions */
	
	
	//inserts any selected custom styling options into the feed's message html
	//load custom style options from Pro Plugin, if available
	function ikfb_message_styling($message_html = ""){
		$ik_fb_font_color = strlen(get_option('ik_fb_font_color')) > 2 ? get_option('ik_fb_font_color') : '';
		$ik_fb_font_size = strlen(get_option('ik_fb_font_size')) > 0 ? get_option('ik_fb_font_size') : '';

		//load our custom styling, to insert
		$insertion = ' style="';
		if(strlen($ik_fb_font_size)>0){
			$insertion .= "font-size: {$ik_fb_font_size}px; ";
		}
		if(strlen($ik_fb_font_color)>0){
			$insertion .= "color: {$ik_fb_font_color};";
		}
		$insertion .= '"';					
		//find the position of the replacement shortcode in the HTML
		$position = strpos($message_html,'{ikfb:feed_item:message}');
		//move back one character from that position, assuming a closing bracket to some HTML tag, and insert our custom styling
		$message_html = substr_replace($message_html, $insertion, $position-1, 0);
		
		return $message_html;
	}
	
	//inserts any selected custom styling options into the feed's link
	//$replace = <p class="ik_fb_facebook_link">{ikfb:feed_item:link}</p>
	function ikfb_link_styling($item_link = ""){	
		$ik_fb_link_font_color = strlen(get_option('ik_fb_link_font_color')) > 2 ? get_option('ik_fb_link_font_color') : '';
		$ik_fb_link_font_size = strlen(get_option('ik_fb_link_font_size')) > 0 ? get_option('ik_fb_link_font_size') : '';
		
		//load our custom styling, to insert
		$insertion = ' style="';
		if(strlen($ik_fb_link_font_size)>0){
			$insertion .= "font-size: {$ik_fb_link_font_size}px; ";
		}
		if(strlen($ik_fb_link_font_color)>0){
			$insertion .= "color: {$ik_fb_link_font_color};";
		}
		$insertion .= '"';
		
		$replace = '<a href="'.$item_link.'" target="_blank" '.$insertion.'>';	
		
		return $replace;
	}
	
	//inserts any selected custom styling options into the feed's posted by attribute
	//$line_item .= '<p class="ikfb_item_author">Posted By '.$from_text.'</p>';		
	function ikfb_posted_by_styling($line_item = ""){	
		$ik_fb_posted_by_font_color = strlen(get_option('ik_fb_posted_by_font_color')) > 2 ? get_option('ik_fb_posted_by_font_color') : '';
		$ik_fb_posted_by_font_size = strlen(get_option('ik_fb_posted_by_font_size')) > 0 ? get_option('ik_fb_posted_by_font_size') : '';
		
		//load our custom styling, to insert
		$insertion = ' style="';
		if(strlen($ik_fb_posted_by_font_size)>0){
			$insertion .= "font-size: {$ik_fb_posted_by_font_size}px; ";
		}
		if(strlen($ik_fb_posted_by_font_color)>0){
			$insertion .= "color: {$ik_fb_posted_by_font_color};";
		}
		$insertion .= '"';					
		//find the position of the replacement shortcode in the HTML
		$position = strpos($line_item,'Posted By');
		//move back one character from that position, assuming a closing bracket to some HTML tag, and insert our custom styling
		$line_item = substr_replace($line_item, $insertion, $position-1, 0);
		
		return $line_item;
	}
	
	//inserts any selected custom styling options into the feed's date attribute
	function ikfb_date_styling($line_item = ""){	
		$ik_fb_date_font_color = strlen(get_option('ik_fb_date_font_color')) > 2 ? get_option('ik_fb_date_font_color') : '';
		$ik_fb_date_font_size = strlen(get_option('ik_fb_date_font_size')) > 0 ? get_option('ik_fb_date_font_size') : '';
		
		//load our custom styling, to insert
		$insertion = ' style="';
		if(strlen($ik_fb_date_font_size)>0){
			$insertion .= "font-size: {$ik_fb_date_font_size}px; ";
		}
		if(strlen($ik_fb_date_font_color)>0){
			$insertion .= "color: {$ik_fb_date_font_color};";
		}
		$insertion .= '"';					
		//find the position of the replacement shortcode in the HTML
		$position = strpos($line_item,'class="date"');
		//move back one character from that position, assuming a closing bracket to some HTML tag, and insert our custom styling
		$line_item = substr_replace($line_item, $insertion, $position-1, 0);
		
		return $line_item;
	}
	
	//inserts any selected custom styling options into the feed's description
	//$replace = $item->description;				
	function ikfb_description_styling($replace = ""){	
		$ik_fb_description_font_color = strlen(get_option('ik_fb_description_font_color')) > 2 ? get_option('ik_fb_description_font_color') : '';
		$ik_fb_description_font_size = strlen(get_option('ik_fb_description_font_size')) > 0 ? get_option('ik_fb_description_font_size') : '';
		
		//load our custom styling, to insert
		$insertion = ' style="';
		if(strlen($ik_fb_description_font_size)>0){
			$insertion .= "font-size: {$ik_fb_description_font_size}px; ";
		}
		if(strlen($ik_fb_description_font_color)>0){
			$insertion .= "color: {$ik_fb_description_font_color};";
		}
		$insertion .= '"';					
		//find the position of the replacement shortcode in the HTML
		$position = strpos($replace,'{ikfb:feed_item:description}');
		//move back one character from that position, assuming a closing bracket to some HTML tag, and insert our custom styling
		$replace = substr_replace($replace, $insertion, $position-1, 0);
		
		return $replace;
	}
	
	//inserts any selected custom styling options into the feed's powered by attribute	
	//$content = '<a href="https://illuminatikarate.com/ik-facebook-plugin/" target="_blank" id="ikfb_powered_by">Powered By IK Facebook Plugin</a>';	
	function ikfb_powered_by_styling($content = ""){
		$ik_fb_powered_by_font_color = strlen(get_option('ik_fb_powered_by_font_color')) > 2 ? get_option('ik_fb_powered_by_font_color') : '';
		$ik_fb_powered_by_font_size = strlen(get_option('ik_fb_powered_by_font_size')) > 0 ? get_option('ik_fb_powered_by_font_size') : '';
		
		//load our custom styling, to insert
		$insertion = ' style="';
		if(strlen($ik_fb_powered_by_font_size)>0){
			$insertion .= "font-size: {$ik_fb_powered_by_font_size}px; ";
		}
		if(strlen($ik_fb_powered_by_font_color)>0){
			$insertion .= "color: {$ik_fb_powered_by_font_color};";
		}
		$insertion .= '"';					
		//find the position of the replacement shortcode in the HTML
		$position = strpos($content,'id="ikfb_powered_by"');
		//move back one character from that position, assuming a closing bracket to some HTML tag, and insert our custom styling
		$content = substr_replace($content, $insertion, $position-1, 0);
		
		return $content;
	}
}//end ikFacebook

//publicly available functions

//display feed
function ik_fb_display_feed(){
	$ik_fb = new ikFacebook();
	echo $ik_fb->ik_fb_output_feed();
}

//display like box
function ik_fb_display_like_button($url, $height = "45", $colorscheme = "light"){
	$ik_fb = new ikFacebook();
	echo $ik_fb->ik_fb_like_button($url,$height,$colorscheme);
}

if (!isset($ik_fb)){
	$ik_fb = new ikFacebook();
}