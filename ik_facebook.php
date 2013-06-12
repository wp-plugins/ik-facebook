<?php
/*
Plugin Name: IK Facebook Plugin
Plugin URI: http://illuminatikarate.com/ik-facebook-plugin
Description: IK Facebook Plugin - A Facebook Solution for WordPress
Author: Illuminati Karate, Inc.
Version: 1.7
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
include('ik_facebook_feed_widget.php');
include('ik_facebook_options.php');
$ik_fb_options = new ikFacebookOptions();

//use this to track if css/powered by have been output
global $ikfb_footer_css_output;
global $ikfb_footer_poweredby_output;

class ikFacebook
{
	var $authToken;

	function __construct(){
		//create shortcodes
		add_shortcode('ik_fb_feed', array($this, 'ik_fb_output_feed_shortcode'));
		add_shortcode('ik_fb_like_button', array($this, 'ik_fb_output_like_button'));

		//add CSS
		add_action( 'wp_enqueue_scripts', array($this, 'ik_fb_setup_css'));
		add_action( 'wp_footer', array($this, 'ik_fb_setup_custom_css'));
		add_action( 'wp_enqueue_scripts', array($this, 'ik_fb_setup_custom_theme_css'));

		//register sidebar widgets
		add_action( 'widgets_init', array($this, 'ik_fb_register_widgets' ));

		//display "powered by"
		add_action('wp_footer', array($this, 'ik_fb_show_powered_by' ));
	}

	//register any widgets here
	function ik_fb_register_widgets() {
		register_widget( 'ikFacebookFeedWidget' );
	}
	
	//add Basic CSS
	function ik_fb_setup_css() {
		wp_register_style( 'ik_facebook_style', plugins_url('style.css', __FILE__) );
		wp_register_style( 'ik_facebook_dark_style', plugins_url('dark_style.css', __FILE__) );
		wp_register_style( 'ik_facebook_light_style', plugins_url('light_style.css', __FILE__) );
		wp_register_style( 'ik_facebook_blue_style', plugins_url('blue_style.css', __FILE__) );
		
		switch(get_option('ik_fb_feed_theme')){
			case 'dark_style':
				wp_enqueue_style( 'ik_facebook_dark_style' );
				break;
			case 'light_style':
				wp_enqueue_style( 'ik_facebook_light_style' );
				break;
			case 'blue_style':
				wp_enqueue_style( 'ik_facebook_blue_style' );
				break;
			case 'no_style':
			default:
				wp_enqueue_style( 'ik_facebook_style' );
				break;
		}
	}

	//add Custom CSS
	function ik_fb_setup_custom_css() {
		//use this to track if css has been output
		global $ikfb_footer_css_output;
		
		if($ikfb_footer_css_output){
			return;
		} else {
			echo '<style type="text/css" media="screen">' . get_option('ik_fb_custom_css') . "</style>";
			$ikfb_footer_css_output = true;
		}
	}
	
	//add Custom CSS from Theme
	function ik_fb_setup_custom_theme_css() {
		//only enqueue CSS if it's there
		if(file_exists(get_stylesheet_directory() . '/ik_fb_custom_style.css' )){
			wp_register_style( 'ik_facebook_custom_style', get_stylesheet_directory() . '/ik_fb_custom_style.css' );
			wp_enqueue_style( 'ik_facebook_custom_style' );
		}
	}
	
	//generates the like button HTML
	function ik_fb_like_button($url, $height = "45", $colorscheme = "light"){
		return '<iframe id="like_button" src="//www.facebook.com/plugins/like.php?href='.urlencode($url).'&amp;layout=standard&amp;show_faces=false&amp;action=like&amp;colorscheme='.$colorscheme.'&amp;height='.$height.'" scrolling="no" frameborder="0" allowTransparency="true"></iframe>';//add facebook like button
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
			//TBD: make colorscheme available as a global option
			'colorscheme' => 'light',
			'width' => get_option('ik_fb_feed_image_width'),
			'use_thumb' => !get_option('ik_fb_fix_feed_image_width')
		), $atts ) );
		
		return $this->ik_fb_output_feed($colorscheme, $use_thumb, $width);				
	}
	
	//facebook feed
	public function ik_fb_output_feed($colorscheme = "light", $use_thumb = true, $width = "", $is_sidebar_widget = false){		
		//load facebook data
		$fbData = $this->loadFacebook();
		
		$feed = $fbData['feed'];
		$page_data = $fbData['page_data'];
		
		$ik_fb_header_bg_color = strlen(get_option('ik_fb_header_bg_color')) > 2 && !get_option('ik_fb_use_custom_html') ? get_option('ik_fb_header_bg_color') : '';
		$ik_fb_window_bg_color = strlen(get_option('ik_fb_window_bg_color')) > 2 && !get_option('ik_fb_use_custom_html') ? get_option('ik_fb_window_bg_color') : '';
		
		//use different heigh/width styling options, if this is the sidebar widget
		if(!$is_sidebar_widget){
			$ik_fb_feed_height = strlen(get_option('ik_fb_feed_window_height')) > 0 && !get_option('ik_fb_use_custom_html') ? get_option('ik_fb_feed_window_height') : '';
			$ik_fb_feed_width = strlen(get_option('ik_fb_feed_window_width')) > 0 && !get_option('ik_fb_use_custom_html') ? get_option('ik_fb_feed_window_width') : '';
		} else {
			$ik_fb_feed_height = strlen(get_option('ik_fb_sidebar_feed_window_height')) > 0 && !get_option('ik_fb_use_custom_html') ? get_option('ik_fb_sidebar_feed_window_height') : '';
			$ik_fb_feed_width = strlen(get_option('ik_fb_sidebar_feed_window_width')) > 0 && !get_option('ik_fb_use_custom_html') ? get_option('ik_fb_sidebar_feed_window_width') : '';
		}
		
		//something went wrong!
		if(count($feed)<1){
			$output = "<p class='ik_fb_error'>IK FB: Please check your settings.</p>";
			return $output;
		}
		
		//feed window width
		$custom_styling_1 = ' style="';
		if(function_exists("ik_fb_pro_is_page_owner")){
			if(strlen($ik_fb_feed_width)>0){
				$custom_styling_1 .= "width: {$ik_fb_feed_width}px;";
			}	
			if(strlen($ik_fb_feed_height)>0){		
				$custom_styling_1 .= "height: auto; ";
			}
		}
		$custom_styling_1 .= '"';
		
		//feed window height, feed window bg color
		$custom_styling_2 = ' style="';
		if(function_exists("ik_fb_pro_is_page_owner")){
			if(strlen($ik_fb_feed_height)>0){		
				$custom_styling_2 .= "height: {$ik_fb_feed_height}px; ";
			}
			if(strlen($ik_fb_window_bg_color)>0){
				$custom_styling_2 .= " background-color: {$ik_fb_window_bg_color};";
			}
		}		
		
		$custom_styling_2 .= '"';
		
		//feed heading bg color
		$custom_styling_3 = ' style="';
		if(function_exists("ik_fb_pro_is_page_owner")){
			if(strlen($ik_fb_header_bg_color)>0){
				$custom_styling_3 .= "background-color: {$ik_fb_header_bg_color};";
			}
		}
		$custom_styling_3 .= '"';
		
		$default_html = '<div id="ik_fb_widget" ' . $custom_styling_1 . ' ><div id="ik_fb_widget_top" ' . $custom_styling_3 . ' ><div class="ik_fb_profile_picture">{ikfb:image}{ikfb:link}</div>{ikfb:like_button}</div><ul class="ik_fb_feed_window" ' . $custom_styling_2 . ' >{ikfb:feed}</ul></div>';
		
		//load custom HTML structure from Pro Plugin, if available and enabled
		$output = strlen(get_option('ik_fb_feed_html')) > 2 && get_option('ik_fb_use_custom_html') ? get_option('ik_fb_feed_html') : $default_html;		
		
		//only display photo if option is set
		if(get_option('ik_fb_show_profile_picture')){
			//use the username if available, otherwise fallback to page ID
			if(isset($page_data->username)){
				$replace = '<img src="//graph.facebook.com/'.$page_data->username.'/picture" />';
				$output = str_replace('{ikfb:image}', $replace, $output);
			} else {
				$replace = '<img src="//graph.facebook.com/'.$page_data->id.'/picture" />';
				$output = str_replace('{ikfb:image}', $replace, $output);
			}
		} else {
			$output = str_replace('{ikfb:image}', '', $output);
		}
		
		//use the link if set, else fall back to /pages/name/id
		if(isset($page_data->link)){
			$the_link = $page_data->link;
		} else {
			$the_link = "https://www.facebook.com/pages/".$page_data->name."/".$page_data->id;
		}
		
		$replace = '<a target="_blank" href="'.$the_link.'"><span class="ik_fb_name">'.$page_data->name.'</span></a>';	
		$output = str_replace('{ikfb:link}', $replace, $output);		

		//only show like button if enabled in settings
		if(get_option('ik_fb_show_like_button')){
			$replace = $this->ik_fb_like_button($the_link, "45", $colorscheme);
			$output = str_replace('{ikfb:like_button}', $replace, $output);		
		} else {
			$output = str_replace('{ikfb:like_button}', '', $output);		
		}

		//build line items to replace with
		$replace = '';
		
		if(count($feed)>0){//check to see if feed data is set
			//see if a limit is set in the options
			$limit = get_option('ik_fb_feed_limit');
			$count = 0;
			
			if(!is_numeric($limit)){
				$limit = -1;
			}
			
			foreach($feed as $item){//$item is the feed object
				if($limit == -1 || $count < $limit){
					$replace .= $this->buildFeedLineItem($item, $use_thumb, $width, $page_data);
					$count ++;
				}
			}
		}			
		
		$output = str_replace('{ikfb:feed}', $replace, $output);		
		
		return $output;		
	}
	
	//passed a FB Feed Item, builds the appropriate HTML
	function buildFeedLineItem($item, $use_thumb, $width, $page_data){
		//build default HTML structure
		$default_feed_item_html = '<li class="ik_fb_feed_item">{ikfb:feed_item}</li>';		
		$default_message_html = '<p>{ikfb:feed_item:message}</p>';		
		$default_image_html = '<p class="ik_fb_facebook_image">{ikfb:feed_item:image}</p>';		
		$default_description_html = '<p class="ik_fb_facebook_description">{ikfb:feed_item:description}</p>';		
		$default_caption_html = '<p class="ik_fb_facebook_link">{ikfb:feed_item:link}</p>';	
		
		//load custom HTML structure from Pro Plugin, if available
		$feed_item_html = strlen(get_option('ik_fb_feed_item_html')) > 2 && get_option('ik_fb_use_custom_html') ? get_option('ik_fb_feed_item_html') : $default_feed_item_html;
		$message_html = strlen(get_option('ik_fb_message_html')) > 2 && get_option('ik_fb_use_custom_html') ? get_option('ik_fb_message_html') : $default_message_html;
		$image_html = strlen(get_option('ik_fb_image_html')) > 2 && get_option('ik_fb_use_custom_html') ? get_option('ik_fb_image_html') : $default_image_html;
		$description_html = strlen(get_option('ik_fb_description_html')) > 2 && get_option('ik_fb_use_custom_html') ? get_option('ik_fb_description_html') : $default_description_html;
		$caption_html = strlen(get_option('ik_fb_caption_html')) > 2 && get_option('ik_fb_use_custom_html') ? get_option('ik_fb_caption_html') : $default_caption_html;
		
		$output = '';
		
		$add_feed_item = false;
		
		if(IK_FACEBOOK_PRO){
			if(function_exists("ik_fb_pro_is_page_owner")){
				$add_feed_item = ik_fb_pro_is_page_owner($item,$page_data);
			} else {
				$add_feed_item = true;
			}
		} else {
			$add_feed_item = true;
		}
		
		$line_item = '';
		
		if($add_feed_item){
			//output the item message
			if(isset($item->message)){ 
				$replace = $item->message;
				
				//add custom message styling from pro options
				if(function_exists("ik_fb_pro_message_styling") && !get_option('ik_fb_use_custom_html')){		
					$message_html = ik_fb_pro_message_styling($message_html);
				}				
				
				$line_item .= str_replace('{ikfb:feed_item:message}', $replace, $message_html);		
			}				

			//output the item photo
			if(isset($item->picture)){ 						
				//output the images
				//if set, load the custom image width from the options page
				if(!$use_thumb){						
					//load fullsized image	
					//start with an authtoken, if needed
					if(!isset($this->authToken)){
						//TBD: Caching!!!
						$this->authToken = $this->fetchUrl("https://graph.facebook.com/oauth/access_token?type=client_cred&client_id={$app_id}&client_secret={$app_secret}");
					}
					
					//get the item id
					$item_id = $item->object_id;
					
					//load the photo from the open graph
					$photo = $this->fetchUrl("https://graph.facebook.com/{$item_id}/picture?{$this->authToken}&redirect=0", true);		
					
					if(isset($photo->data->url)){
						//if using custom width, output fullsized image
						$replace = '<img width="'.$width.'" src="'.$photo->data->url.'" />';
				
						//add custom image styling from pro options
						if(function_exists("ik_fb_pro_image_styling") && !get_option('ik_fb_use_custom_html')){		
							$image_html = ik_fb_pro_image_styling($image_html);
						}		
						
						$line_item .= str_replace('{ikfb:feed_item:image}', $replace, $image_html);	
					} else if(isset($item->picture)){
						$replace = '<img width="'.$width.'" src="'.$item->picture.'" />';
						
						//add custom image styling from pro options
						if(function_exists("ik_fb_pro_image_styling") && !get_option('ik_fb_use_custom_html')){		
							$image_html = ik_fb_pro_image_styling($image_html);
						}	
						
						$line_item .= str_replace('{ikfb:feed_item:image}', $replace, $image_html);	
					}
				} else {
					//otherwise, use thumbnail
					$replace = '<img src="'.$item->picture.'" />';
				
					//add custom image styling from pro options
					if(function_exists("ik_fb_pro_image_styling") && !get_option('ik_fb_use_custom_html')){		
						$image_html = ik_fb_pro_image_styling($image_html);
					}	
					
					$line_item .= str_replace('{ikfb:feed_item:image}', $replace, $image_html);	
				}

				//add the text for photo description
				if(isset($item->description)){
					$replace = $item->description;					
				
					//add custom image styling from pro options
					if(function_exists("ik_fb_pro_description_styling") && !get_option('ik_fb_use_custom_html')){		
						$description_html = ik_fb_pro_description_styling($description_html);
					}	
					
					$line_item .= str_replace('{ikfb:feed_item:description}', $replace, $description_html);	
				}
			}		

			if(isset($item->link)){ //output the item link
				if(isset($item->caption)){
					$link_text = $item->caption; //some items have a caption
				} else {
					$link_text = $item->name;  //others might just have a name
				}
				
				//don't add the line item if the link text isn't set
				if(strlen($link_text) > 1){
					$replace_front = '<a href="'.$item->link.'" target="_blank">';
					$replace_back = $link_text.'</a>';				
				
					//add custom link styling from pro options
					if(function_exists("ik_fb_pro_link_styling") && !get_option('ik_fb_use_custom_html')){		
						$replace_front = ik_fb_pro_link_styling($item->link);
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
							$posted_by_text = '<p class="ikfb_item_author">Posted By '.$from_text.'</p>';
				
							//add custom posted by styling from pro options
							if(function_exists("ik_fb_pro_posted_by_styling") && !get_option('ik_fb_use_custom_html')){		
								$posted_by_text = ik_fb_pro_posted_by_styling($posted_by_text);
							}			
							//TBD: make Custom HTML option for Posted By
							$line_item .= $posted_by_text;
						}
					}
				}
			
				$output = str_replace('{ikfb:feed_item}', $line_item, $feed_item_html);	
			}
		}
		
		return $output;
	}
	
	//checks settings and outputs Powered By link
	function ik_fb_show_powered_by() {
		//use this to track if powered by has been output
		global $ikfb_footer_poweredby_output;
				
		if(get_option('ik_fb_powered_by')){			
			if($ikfb_footer_poweredby_output){
				return;
			} else {			
				$content = '<a href="https://illuminatikarate.com/ik-facebook-plugin/" target="_blank" id="ikfb_powered_by">Powered By IK Facebook Plugin</a>';			
				
				//add custom powered by styling from pro options
				if(function_exists("ik_fb_pro_powered_by_styling") && !get_option('ik_fb_use_custom_html')){		
					$content = ik_fb_pro_powered_by_styling($content);
				}		
				
				echo $content;
				
				$ikfb_footer_poweredby_output = true;
			}
		}
	}
	
	//fetches an URL
	//TBD: replace this with a version that supports caching
	function fetchUrl($url,$decode=false){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$retData = curl_exec($ch);
		curl_close($ch);
		
		if($decode){
			$retData = json_decode($retData);
		}
		
		return $retData;
	}
	
	//loads facebook feed based on current id
	function loadFacebook(){
		$retData = array();
	
		$profile_id = get_option('ik_fb_page_id'); //id of the facebook page

		if(isset($profile_id) && strlen($profile_id)>0){
			$app_id = get_option('ik_fb_app_id');
			$app_secret = get_option('ik_fb_secret_key');
			
			if(!isset($this->authToken)){
				$this->authToken = $this->fetchUrl("https://graph.facebook.com/oauth/access_token?type=client_cred&client_id={$app_id}&client_secret={$app_secret}");
			}
			
			$feed = $this->fetchUrl("https://graph.facebook.com/{$profile_id}/feed?{$this->authToken}", true);//the feed data
			$page_data = $this->fetchUrl("https://graph.facebook.com/{$profile_id}", true);//the page data
			
			if(isset($feed->data)){//check to see if feed data is set				
				$retData['feed'] = $feed->data;
			}
			if(isset($page_data)){//check to see if page data is set
				$retData['page_data'] = $page_data;
			}
		}
		
		return $retData;
	}
}//end ikFacebook

//pubicly available functions

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