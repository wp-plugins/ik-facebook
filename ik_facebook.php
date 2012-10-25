<?php
/*
Plugin Name: IK Facebook
Plugin URI: http://illuminatikarate.com/ik-facebook-plugin
Description: IK Facebook - A Facebook Solution for WordPress
Author: Illuminati Karate, Inc.
Version: 1.0.5
Author URI: http://illuminatikarate.com

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
include('ik_facebook_widget.php');
include('ik_facebook_options.php');
$ik_fb_options = new ikFacebookOptions();

class ikFacebook
{
	function __construct(){
		//create shortcodes
		add_shortcode('ik_fb_feed', array($this, 'ik_fb_output_feed'));

		//add CSS
		add_action( 'wp_head', array($this, 'ik_fb_setup_css'));
		add_action( 'wp_head', array($this, 'ik_fb_setup_custom_css'));

		//register sidebar widgets
		add_action( 'widgets_init', array($this, 'ik_fb_register_widgets' ));
	}

	//register any widgets here
	function ik_fb_register_widgets() {
		register_widget( 'ikFacebookWidget' );
	}
	
	//add Basic CSS to header
	function ik_fb_setup_css() {
		wp_register_style( 'ik_facebook_style', plugins_url('style.css', __FILE__) );
		wp_enqueue_style( 'ik_facebook_style' );
	}

	//add Custom CSS to header
	//load theme options
	function ik_fb_setup_custom_css() {
		echo '<style type="text/css" media="screen">' . get_option('ik_fb_custom_css') . "</style>";
	}

	//facebook feed
	public function ik_fb_output_feed(){			
		//load shortcode attributes into an array
		extract( shortcode_atts( array(
		), $atts ) );
		
		//load facebook data
		$fbData = $this->loadFacebook();
		
		$feed = $fbData['feed'];
		$page_data = $fbData['page_data'];
		
		//something went wrong!
		if(count($feed)<1){
			$output = "<p class='ik_fb_error'>IK FB: Please check your settings.</p>";
			return $output;
		}
		
		$output = '<div id="ik_fb_widget">';
		
		//outputs the profile picture and Like button for the profile
		$output .= '<div class="ik_fb_profile_picture">
						<img src="//graph.facebook.com/'.$page_data->username.'/picture" height="50" width="50" />
						<a target="_blank" href="'.$page_data->link.'"><span class="ik_fb_name">'.$page_data->name.'</span> on Facebook</a>
					</div>';			

		$output .= '<iframe src="//www.facebook.com/plugins/like.php?href='.urlencode($page_data->link).'&amp;layout=standard&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;height=45" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:99%; height:45px; margin-left:4px;" allowTransparency="true"></iframe>';//add facebook like button
		$output .= '<ul class="ik_fb_feed_window">';//start of the feed		

		if(count($feed)>0){//check to see if feed data is set
			foreach($feed as $item){//$item is the feed object
				$output .= '<li class="ik_fb_feed_item">';
				
				if(isset($item->message)){ //output the item message
					$output .= '<p>'.$item->message.'</p>';
				}				

				if(isset($item->picture)){ //output the item photo
				
					$output .= '<p class="ik_fb_facebook_image"><img src="'.$item->picture.'" /></p>';	
					if(isset($item->description)){//adds the text for photo description
						$output .= '<p class="ik_fb_facebook_description">'.$item->description.'</p>';
					}
				}		

				if(isset($item->link)){ //output the item link
					if(isset($item->caption)){
						$link_text = $item->caption; //some items have a caption
					} else {
						$link_text = $item->name;  //others might just have a name
					}

					$output .= '<p class="ik_fb_facebook_link"><a href="'.$item->link.'" target="_blank">'.$link_text.'</a></p>';	
				}				
				
				$output .= '</li>';//end li.feed_item
			}
		}			

		$output .= '</ul>';//end ul.feed_window
		
		$output .= '</div>';//end div#ik_fb_widget
		
		return $output;		
	}
	
	//fetches an URL
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
			
			$authToken = $this->fetchUrl("https://graph.facebook.com/oauth/access_token?type=client_cred&client_id={$app_id}&client_secret={$app_secret}");
			$feed = $this->fetchUrl("https://graph.facebook.com/{$profile_id}/feed?{$authToken}", true);//the feed data
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

if (!isset($ik_fb)){
	$ik_fb = new ikFacebook();
}
?>