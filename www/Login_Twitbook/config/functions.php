<?php 

require_once 'dbconfig.php';
require_once '../../includes/functions.php';

class User {

    function checkUser($uid, $oauth_provider, $username,$user_info=null,$music_str=null,$autoincrement_id=null) 
	{
		if($autoincrement_id > 0 ) {
			$query = mysql_query("SELECT * FROM mydna_musicplayer WHERE id = '$autoincrement_id'") or die(mysql_error());
		    $result = mysql_fetch_array($query);
		}
		else {
			if($oauth_provider == 'twitter') {
			$query = mysql_query("SELECT * FROM mydna_musicplayer WHERE oauth_uid_twitter = '$uid' ") or die(mysql_error());
			}elseif($oauth_provider == 'facebook') {
					$query = mysql_query("SELECT * FROM mydna_musicplayer WHERE oauth_uid = '$uid' ") or die(mysql_error());
			}
		    $result = mysql_fetch_array($query);
		}
		
		
        if (!empty($result)) ################## UPDATE ########################
		{           
		   if($oauth_provider=='facebook') :				

				$query = "UPDATE mydna_musicplayer SET 
							facebook='".$user_info["username"]."',
							gender='".$user_info["gender"]."',
							music_likes='".$music_str."',
							location='".$user_info["location"]["name"]."',
							oauth_uid='".$uid."'
						    WHERE id = '$result[id]'";
			    mysql_query($query);

		   elseif($oauth_provider=='twitter') : // TWITTER UPDATE

				$query="UPDATE mydna_musicplayer 
					SET  twitter='".$user_info->screen_name."',
					twitter_screen_name ='".$user_info->screen_name."',
					oauth_uid_twitter ='".$uid."'
					WHERE id = '$result[id]'";
				mysql_query($query);
				
		   endif;
		  
        } 
		else ################## INSERT THE VALUES ########################
		{
		
			################### USER NOT PRESENT. INSERT A NEW RECORD ########################
				
				if($oauth_provider == 'twitter')
                {

					$name_arr = explode(" ",$user_info->name);

                    mysql_insert('mydna_musicplayer',array("username" => $user_info->screen_name,
                                                           "artist" => $user_info->name,
                                                           "twitter" => $user_info->screen_name,
                                                           "first_name" => $name_arr[0],
                                                           "last_name" => $name_arr[2],
                                                           "linkToProfile" => $user_info->url,
                                                           "location" => $user_info->location,
                                                           "profile_image_url" => $user_info->profile_image_url,
                                                           "languages" => $user_info->lang,
                                                           "website" => $user_info->url,
                                                           "oauth_uid" => $uid,
                                                           "oauth_uid_twitter" => $uid,
                                                           "oauth_token" => $_SESSION['oauth_token'],
                                                           "oauth_secret" => $_SESSION['oauth_token_secret'],
                                                           "oauth_provider" => $oauth_provider,
                                                           "twitter_screen_name" => $user_info->screen_name,
                                                           ));
                }
			    else
                {
				
					$languages_str = "";
					if(count($user_info[languages]) > 0 ):
						foreach($user_info[languages] as $languages):				
							$languages_str .= $languages["name"].", ";
						endforeach;
					endif;
					
					 $query="INSERT INTO mydna_musicplayer 
								SET  username ='".mysql_real_escape_string($user_info["username"])."',
								artist='".$user_info["first_name"]." ".$user_info["last_name"]."',
								facebook='".$user_info["username"]."',
								first_name='".$user_info["first_name"]."',
								last_name='".$user_info["last_name"]."',
								linkToProfile='".$user_info["link"]."',
								location='".$user_info["location"]["name"]."',
								gender='".$user_info["gender"]."',		
								languages='".$languages_str."',
								music_likes='".$music_str."',
								oauth_uid='".$uid."',
								oauth_provider='".$oauth_provider."',
								created_at=now()";
					mysql_query($query);
						
                }

			 $query_data = mysql_query("SELECT * FROM mydna_musicplayer WHERE oauth_uid = '$uid' and oauth_provider = '$oauth_provider'");
			 $result_data = mysql_fetch_array($query_data);
			 return $result_data;
        }
        return $result;
    }

    

}

?>
