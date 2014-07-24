<?php
/*
This file is part of The IK Facebook Plugin .

The IK Facebook Plugin  is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

The IK Facebook Plugin  is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with The IK Facebook Plugin.  If not, see <http://www.gnu.org/licenses/>.
*/
	class CachedCurl
	{
		var $cache_time = 900; // 900 seconds [15 minutes]
		
		function __construct($cache_time = 900)
		{					
			$this->cache_time = $cache_time;
		}
				
		function load_url($url, $post_fields = false, $headers = false)
		{
			$cache_key = strlen($url) . md5($url);
			
			// check for a cached result
			$result = get_transient($cache_key);
			
			if ($result === false) {				
				$result = wp_remote_get($url);
				
				if(is_wp_error($result)){
					$result = $result->get_error_message();
				} else {
					$result = isset($result['body']) ? $result['body'] : '';
				
					if(strlen($result)>2){
						// store to cache
						set_transient($cache_key, $result, $this->cache_time);
					}
				}
				
				return $result;
			} else {
				return $result;
			}
		}
	}
?>