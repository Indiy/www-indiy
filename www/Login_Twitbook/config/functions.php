<?php 

require_once 'dbconfig.php';
require_once '../includes/login_helper.php';

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
                    
                    $access_token = $_SESSION['access_token'];
                    $oauth_token = $access_token['oauth_token'];
                    $oauth_token_secret = $access_token['oauth_token_secret'];
                    $logo = NULL;
                    if( $user_info->profile_image_url )
                    {
                        $contents = file_get_contents($user_info->profile_image_url);
                        if( $contents )
                        {
                            $hash = hash("md5",$contents);
                            $new_logo = $artist_id . "_" . $hash . ".jpg";
                            $file_path = '../artists/files/' . $new_logo;
                            file_put_contents($file_path,$contents);
                            $logo = $new_logo;
                        }
                    }

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
                                                           "oauth_token" => $oauth_token,
                                                           "oauth_secret" => $oauth_token_secret,
                                                           "oauth_provider" => $oauth_provider,
                                                           "twitter_screen_name" => $user_info->screen_name,
                                                           "logo" => $logo,
                                                           ));
                }
			    else
                {
                    $fb_access_token = $_SESSION['fb_access_token'];
                    $username = $user_info['username'];
                    if( !$username )
                        $username = $user_info['name'];
                    $email = $user_info['email'];
                    $logo = NULL;
                    $artist_id = $userdata['id'];
                    if( $user_info['username'] )
                    {
                        $url = "http://graph.facebook.com/$username/picture";
                        $contents = file_get_contents($url);
                        if( $contents )
                        {
                            $hash = hash("md5",$contents);
                            $new_logo = $artist_id . "_" . $hash . ".jpg";
                            $file_path = '../artists/files/' . $new_logo;
                            file_put_contents($file_path,$contents);
                            $logo = $new_logo;
                        }
                    }
                    
                    mysql_insert('mydna_musicplayer',array("username" => $username,
                                                           "artist" => $user_info["name"],
                                                           "facebook" => $username,
                                                           "first_name" => $user_info["first_name"],
                                                           "last_name" => $user_info["last_name"],
                                                           "fb_uid" => $uid,
                                                           "fb_access_token" => $fb_access_token,
                                                           "oauth_provider" => $oauth_provider,
                                                           "oauth_uid" => $uid,
                                                           "email" => $email,
                                                           "logo" => $logo,
                                                           ));
                }

			 $query_data = mysql_query("SELECT * FROM mydna_musicplayer WHERE oauth_uid = '$uid' and oauth_provider = '$oauth_provider'");
			 $result_data = mysql_fetch_array($query_data);
             post_signup($result_data);
			 return $result_data;
        }
        return $result;
    }

    

}

?>
