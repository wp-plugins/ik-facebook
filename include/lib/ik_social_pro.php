<?php
global $ik_social_pro;
global $ik_social_pro_options;
global $ik_fb;

include('ik_social_pro_options.php');

class ikSocialPro
{
	var $feed_options;
	
	function __construct(){		
		//some init yang
		add_action( 'admin_init', array($this, 'ikfb_pro_admin_init') );
	}
	
    function ikfb_pro_admin_init() {		
		//register our pro settings
		$ik_social_options = new ikSocialProOptions();		
		return $ik_social_options->register_settings();
    }
	
	//returns true if current item is written by the page owner
	function is_page_owner($item,$page_data){
		//only hide items if the option is toggled
		if($this->feed_options->get_option('ik_fb_only_show_page_owner') && is_valid_key($this->feed_options->get_option('ik_fb_pro_key'))){
			if($item->from->id == $page_data->id){
				return true;
			}
			return false;
		} else {
			return true;
		}
	}
	
	//inserts avatars into the message content, if option is enabled
	function pro_user_avatars($content = "", $item = array()){
		global $ik_fb;
		
		
		if(is_valid_key($this->feed_options->get_option('ik_fb_pro_key')) && $this->feed_options->get_option('ik_fb_show_avatars') && isset($item->from->id)){	
			if(!isset($ik_fb->authToken)){
				$app_id = $this->feed_options->get_option('ik_fb_app_id');
				$app_secret = $this->feed_options->get_option('ik_fb_secret_key');
		
				$ik_fb->authToken = $ik_fb->fetchUrl("https://graph.facebook.com/oauth/access_token?type=client_cred&client_id={$app_id}&client_secret={$app_secret}");
			}	
			
			$content .= '<img src="https://graph.facebook.com/' . $item->from->id .'/picture?' . $ik_fb->authToken . '" class="ikfb_user_avatar" alt="avatar"/>';
		}
		
		return $content;
	}
	
	//insert comment info into feed, if enabled
	function pro_comments($item, $the_link){
		global $ik_fb;
	
		$comment_output = "";

		if(is_valid_key($this->feed_options->get_option('ik_fb_pro_key'))){	
		
			if($this->feed_options->get_option('ik_fb_show_reply_counts')){	
				if(!isset($ik_fb->authToken)){
					$app_id = $this->feed_options->get_option('ik_fb_app_id');
					$app_secret = $this->feed_options->get_option('ik_fb_secret_key');
			
					$ik_fb->authToken = $ik_fb->fetchUrl("https://graph.facebook.com/oauth/access_token?type=client_cred&client_id={$app_id}&client_secret={$app_secret}");
				}		
			
				$request = "https://graph.facebook.com/{$item->id}/comments?summary=1&{$ik_fb->authToken}";
							
				$data = $ik_fb->fetchUrl($request, true);
				
				$num_comments = 0;
				
				if(isset($data->summary->total_count)){
					$num_comments = $data->summary->total_count;
				}	
				
				if($num_comments > 0){				
					$comment_string = "comment";
					
					if($num_comments > 1){
						$comment_string = "comments";
					}
					
					$comment_output = '<a href="'.$the_link.'" target="_blank" class="ikfb_comments" title="Click To Read On Facebook">' . $num_comments . ' ' . $comment_string . '</a>';
				}
			}

			if($this->feed_options->get_option('ik_fb_show_replies')){	
				$has_comments = false;
				
				if(isset($item->comments)){
					$comment_list = '<ul class="ikfb_comment_list">';
					
					//list of comment groupss per feed item
					foreach($item->comments as $comments){
						//each comment group has multiple comments - conversations per feed item
						foreach($comments as $comment){
							if(isset($comment->message)){
								$comment_list .= '<li class="ikfb_comment">';
								//show avatars, if enabled
								if($this->feed_options->get_option('ik_fb_show_avatars')){
									if(!isset($ik_fb->authToken)){
										$app_id = $this->feed_options->get_option('ik_fb_app_id');
										$app_secret = $this->feed_options->get_option('ik_fb_secret_key');
								
										$ik_fb->authToken = $ik_fb->fetchUrl("https://graph.facebook.com/oauth/access_token?type=client_cred&client_id={$app_id}&client_secret={$app_secret}");
									}	
									
									$comment_avatar = '<img src="https://graph.facebook.com/' . $comment->from->id .'/picture?' . $ik_fb->authToken . '" class="ikfb_user_comment_avatar" alt="avatar"/>';									
									
									$comment_list .= $comment_avatar;
								}
								$comment_list .= '<p class="ikfb_comment_message"><span class="ikfb_comment_author">' . $comment->from->name . ' says:</span> ';
								$comment_list .= nl2br(htmlentities($comment->message),true) . '</p>';
								
								//output date, if option to display it is enabled
								if($this->feed_options->get_option('ik_fb_show_date')){
									if(strtotime($comment->created_time) >= strtotime('-1 day')){
										$date = $ik_fb->humanTiming(strtotime($comment->created_time)). " ago";
									}else{
										$date = date('F jS', strtotime($comment->created_time));
									}
								
									if(strlen($date)>2){
										$comment_list .= '<p class="ikfb_comment_date">' . $date . '</p>';
									}
								}	
								
								//ouput number of likes, if option to show them are enabled
								if($this->feed_options->get_option('ik_fb_show_likes')){	
									if($comment->like_count > 0){
										$like_string = "person likes";
										if($comment->like_count > 1){
											$like_string = "people like";
										}
										$comment_list .= '<p class="ikfb_comment_likes">' . $comment->like_count . ' ' . $like_string . ' this.</p>';
									}
								}						
								
								$comment_list .= '<span class="ikfb_clear"></span>';
								
								$comment_list .= '</li>';
								
								$has_comments = true;
							}
						}
					}
					
					$comment_list .= '</ul>';
				}
				
				if($has_comments){
					$comment_output .= $comment_list;
				}
			}
		}
		
		return $comment_output;
	}
	
	//insert like info into feed, if enabled
	function pro_likes($item, $the_link){
		global $ik_fb;
		
		$likes = "";
		if($this->feed_options->get_option('ik_fb_show_likes')){
		
			if(!isset($ik_fb->authToken)){
				$app_id = $this->feed_options->get_option('ik_fb_app_id');
				$app_secret = $this->feed_options->get_option('ik_fb_secret_key');
		
				$ik_fb->authToken = $ik_fb->fetchUrl("https://graph.facebook.com/oauth/access_token?type=client_cred&client_id={$app_id}&client_secret={$app_secret}");
			}		
		
			$request = "https://graph.facebook.com/{$item->id}/likes?summary=1&{$ik_fb->authToken}";
						
			$data = $ik_fb->fetchUrl($request, true);
			
			$num_likes = 0;
			
			if(isset($data->summary->total_count)){
				$num_likes = $data->summary->total_count;
			}	
			
			if($num_likes > 0){				
				$like_string = "like";
				
				if($num_likes > 1){
					$like_string = "likes";
				}
				
				$likes = '<a href="'.$the_link.'" target="_blank" class="ikfb_likes">' . $num_likes . ' ' . $like_string . '</a> ';
			}
		}
		
		return $likes;
	}
}

if (!isset($ik_social_pro)){
	$ik_social_pro = new ikSocialPro();
}

if (!isset($ik_social_pro_options)){
	$ik_social_pro_options = new ikSocialProOptions();
}