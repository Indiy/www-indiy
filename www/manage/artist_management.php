<?php 

    if( $_REQUEST['session_id'] )
        session_id($_REQUEST['session_id']);

    require_once '../includes/config.php';
	require_once '../includes/functions.php';	
	if($_SESSION['sess_userId']=="")
	{
		header("Location: /index.php");
		exit();
	}
	$artistID = $_REQUEST['userId'];
    if( !$artistID )
    {
        if( $_SESSION['sess_userType'] == 'ARTIST' )
        {
            $artistID = $_SESSION['sess_userId'];
        }
        else
        {
            header("Location: dashboard.php");
            exit();
        }
    }
    setcookie('LOGIN_EMAIL',$_SESSION['sess_userEmail'], time() + 30*24*60*60,'/');
    
    $MAX_TABS = 5;
    
	$query_artistDetail = "SELECT * FROM mydna_musicplayer WHERE id='".$artistID."' ";
	$result_artistDetail = mysql_query($query_artistDetail) or die(mysql_error());
	$record_artistDetail = mysql_fetch_array($result_artistDetail);

	if(isset($_REQUEST['action']))
    {
		if(isset($_REQUEST['song_id']))
        {
            mysql_query("DELETE FROM mydna_musicplayer_audio WHERE id='".$_REQUEST['song_id']."' ");
        }
		elseif(isset($_REQUEST['video_id']))
        {
            mysql_query("DELETE FROM mydna_musicplayer_video WHERE id='".$_REQUEST['video_id']."' ");
        }
		elseif(isset($_REQUEST['content_id']))
        {
            mysql_query("DELETE FROM mydna_musicplayer_content WHERE id='".$_REQUEST['content_id']."' ");
        }
		elseif(isset($_REQUEST['prod_id']))
        {
            mysql_query("DELETE FROM mydna_musicplayer_ecommerce_products WHERE id='".$_REQUEST['prod_id']."' ");
        }
		elseif(isset($_REQUEST['photo_id']))
        {
            mysql_query("DELETE FROM photos WHERE id='".$_REQUEST['photo_id']."' ");
        }
	}
	
	$find_artistAudio = "SELECT * FROM mydna_musicplayer_audio  WHERE artistid='".$artistID."' AND `type`='0' ORDER BY `order` ASC, `id` DESC";
	$result_artistAudio = mysql_query($find_artistAudio) or die(mysql_error());
    $page_list = array();
    while( $row = mysql_fetch_array($result_artistAudio) )
    {
        $row['short_link'] = make_short_link($row['abbrev']);
        array_walk($row,cleanup_row_element);
        $row['download'] = $row['download'] == "0" ? FALSE : TRUE;
        $row['product_id'] = $row['product_id'] > 0 ? intval($row['product_id']) : FALSE;
        $image_path = "../artists/files/" . $row['image'];
        if( !empty($row['image']) && file_exists($image_path) )
            $row['image'] = $image_path;
        else
            $row['image'] = "images/photo_video_01.jpg";
        
        $page_list[] = $row;
    }
    $page_list_json = json_encode($page_list);
	
	$find_artistVideo = "SELECT * FROM mydna_musicplayer_video  WHERE artistid='".$artistID."' ORDER BY `order` ASC, `id` DESC";
	$result_artistVideo = mysql_query($find_artistVideo) or die(mysql_error());
    $video_list = array();
    while( $row = mysql_fetch_array($result_artistVideo) )
    {
        array_walk($row,cleanup_row_element);
        $image_path = "../artists/files/" . $row['image'];
        if( !empty($row['image']) && file_exists($image_path) )
            $row['image_url'] = $image_path;
        else
            $row['image_url'] = "images/photo_video_01.jpg";
        
        $video_list[] = $row;
    }
    $video_list_json = json_encode($video_list);
    
    $sql_photo = "SELECT * FROM photos  WHERE artist_id='$artistID' ORDER BY `order` ASC, `id` DESC";
	$q_photo = mysql_query($sql_photo) or die(mysql_error());
    $photo_list = array();
    while( $row = mysql_fetch_array($q_photo) )
    {
        array_walk($row,cleanup_row_element);
        $image_path = "../artists/files/" . $row['image'];
        if( !empty($row['image']) && file_exists($image_path) )
            $row['image_url'] = $image_path;
        else
            $row['image_url'] = "images/photo_video_01.jpg";
        
        $photo_list[] = $row;
    }
    $photo_list_json = json_encode($photo_list);

	$find_artistContent = "SELECT * FROM mydna_musicplayer_content  WHERE artistid='".$artistID."' ORDER BY `order` ASC, `id` DESC";
	$result_artistContent = mysql_query($find_artistContent) or die(mysql_error());
    $tab_list = array();
    while( $row = mysql_fetch_array($result_artistContent) )
    {
        array_walk($row,cleanup_row_element);
        $image_path = "../artists/files/" . $row['image'];
        if( !empty($row['image']) )
            $row['image_url'] = $image_path;
        else
            $row['image_url'] = "images/photo_video_01.jpg";
        
        $tab_list[] = $row;
    }
    $tab_list_json = json_encode($tab_list);
	
	$find_artistProduct = "SELECT * FROM mydna_musicplayer_ecommerce_products  WHERE artistid='$artistID' AND sku != 'MADSONG' ORDER BY `order` ASC, `id` DESC";
	$result_artistProduct = mysql_query($find_artistProduct) or die(mysql_error());
	$product_list = array();
    while( $row = mysql_fetch_array($result_artistProduct) )
    {
        $product_id = $row['id'];
        $row = get_product_data($product_id);
        $product_list[] = $row;
    }
    $product_list_json = json_encode($product_list);
    
    $sql = "SELECT * FROM artist_files WHERE artist_id='$artistID' AND upload_filename != '' ORDER BY id DESC";
    $files_q = mq($sql);
    $file_list = array();
    while( $file = mf($files_q) )
    {
        $id = $file['id'];
        $filename = $file['filename'];
        $upload_filename = $file['upload_filename'];
        $type = $file['type'];
        $item = array("id" => $id,
                      "filename" => $filename,
                      "upload_filename" => $upload_filename,
                      "type" => $type,
                      "is_uploading" => FALSE,
                      );
        $file_list[] = $item;
    }
    $file_list_json = json_encode($file_list);
	
	if($record_artistDetail['logo'] == '')
		$artist_img_logo = 'images/NoPhoto.jpg';
	else
		$artist_img_logo = '../artists/files/'.$record_artistDetail['logo'];

	$img_url = $artist_img_logo;

    $artist_url = str_replace("http://www.","http://".$record_artistDetail['url'].".",trueSiteUrl());

    $show_first_instruction = FALSE;
    if( $_SESSION['sess_userType'] == 'ARTIST' && $artistID == $_SESSION['sess_userId'] )
    {
        if(! $record_artistDetail['shown_first_instructions'] )
        {
            $show_first_instruction = TRUE;
            mysql_update('mydna_musicplayer',array("shown_first_instructions" => 1),'id',$artistID);
        }
    }
    
    $twitter = 'false';
    $facebook = 'false';
    if( $record_artistDetail['oauth_token'] && $record_artistDetail['oauth_secret'] && $record_artistDetail['twitter'] )
    {
        $twitter = 'true';
    }
    else
    {
        $record_artistDetail['twitter'] = FALSE;
    }
    if( $record_artistDetail['fb_access_token'] && $record_artistDetail['facebook'] )
    {
        $facebook = 'true';
    }
    else
    {
        $record_artistDetail['facebook'] = FALSE;
    }
    
    $artist_data = get_artist_data($artistID);
    $artist_data_json = json_encode($artist_data);
    
    $include_order = FALSE;
    $include_editor = TRUE;
    

    require_once 'header.php';
    
    include_once 'include/edit_page.html';
    include_once 'include/edit_video.html';
    include_once 'include/edit_photo.html';
    include_once 'include/edit_product.html';
    include_once 'include/edit_tab.html';
    include_once 'include/edit_social_config.html';
    include_once 'include/edit_profile.html';
    include_once 'include/edit_store.html';
    include_once 'include/invite_friends.html';
    include_once 'include/fan_connections.html';
    include_once 'include/first_instructions.html';
    include_once 'include/social_post.html';
    include_once 'include/account_limit.html';
    include_once 'include/artist_file.html';
    
    if( $_SESSION['sess_userType'] == 'SUPER_ADMIN' )
        include_once 'include/edit_account_settings.html';
    
?>

<script src="js/artist_management.js" type="text/javascript"></script>
<script type="text/javascript">

var g_artistId = <?=$artistID?>;
var g_facebook = <?=$facebook;?>;
var g_twitter = <?=$twitter;?>;
var g_paypalEmail = "<?=$paypalEmail;?>";
var g_artistData = <?=$artist_data_json;?>;

var g_pageList = <?=$page_list_json;?>;
var g_photoList = <?=$photo_list_json;?>;
var g_videoList = <?=$video_list_json;?>;
var g_tabList = <?=$tab_list_json;?>;
var g_productList = <?=$product_list_json;?>;
var g_fileList = <?=$file_list_json;?>;

var g_playerUrl = "<?=playerUrl();?>";

<? if( $show_first_instruction ): ?>

$(document).ready(showFirstInstructions);

<? endif; ?>

</script>

<section id="wrapper">
<section id="content">
	
    <div id="admin">
    <h2><a id='profile_name_anchor' href="<?=$artist_url;?>" target="_blank"><?php echo $record_artistDetail['artist']; ?></a></h2>
        
    <div id="adminblock">
    	<div class="column1">
            <figure id='profile_figure'>
                <a onclick='showEditProfile();'>
                    <img src="<?=$img_url;?>" />
                </a>
            </figure>
            
            <h6>Manage Profile</h6>
            <ul>
                <li><a onclick='showEditProfile();' title='Edit basic information about your profile'>Edit Profile</a></li>
                <li><a onclick='showSocialConfigPopup();' title='Add Facebook and Twitter account information'>Social Connections</a></li>
                <li><a onclick='showInvitePopup();' title='Invite your friends to MyArtistDNA'>Invite Friends</a></li>
                <li>
                    <a id='view_site_anchor' class='no_underline' href="<?=$artist_url;?>" target="_blank" title='View your site'>
                        <div class='block_button'>
                            <div class='icon'></div>
                            <div class='label'>View Site</div>
                        </div>
                    </a>
                </li>
            </ul>
            <h6>Platform</h6>
            <ul>
                <li><a onclick='showPagePopup(false);' title='Add a song to your site'>Add Song</a></li>
                <li><a onclick='showVideoPopup(false);' title='Add a video to your site'>Add Video</a></li>
                <li><a onclick='showPhotoPopup(false);' title='Add a photo to your site'>Add Photo</a></li>
                <li id='add_tab_list_item'><a onclick='showTabPopup(false);' title='Add a tab to your site'>Add Tab</a></li>
                <li><a href="stats.php?userId=<?=$artistID;?>" title='View website analytics for you site'>View Analytics</a></li>
                <li><a onclick='showFanConnections();' title='Get a list of Fans of your site'>Fan Connections</a></li>
            </ul>
            
            <h6>Store</h6>
            <ul>
                <li><a onclick='showProductPopup();' title='Add a product to your store'>Add Product</a></li>
                <li><a href="order_list.php?artist_id=<?=$artistID;?>" title='List of Customer Orders'>Order List</a></li>
                <li><a href="artist_statement.php?artist_id=<?=$artistID;?>" title="Account Statement">Account Statement</a></li>
            </ul>
            <h6>Misc</h6>
            <ul>
                <li><a onclick='deleteAccount(<?=$artistID;?>);' title='Delete your account'>Delete Account</a></li>
            </ul>
            <? if( $_SESSION['sess_userType'] == 'SUPER_ADMIN' ): ?>
                <h6>Super Admin</h6>
                <ul>
                    <li><a onclick='showAccountSettings();'>Account Settings</a></li>
                    <li><a href="all_account_summary.php" title="Summary of Accounts">Summary of Accounts</a></li>
                </ul>
            <? endif; ?>
        </div>
        
        <div class="column2">
        
        <div class="filelist">
        	<div class="heading">
                <div class='title'>ARTIST FILES</div>
                <div class="buttonadd">
                    <a onclick='showAddArtistFilePopup(false);' title='Add files for your site'>Add Files</a>
                </div>
            </div>
            <div class="list" style='display: none;'>
                <div class='file_heading'>
                    <div class='button active'>All</div>
                    <div class='button'>Images</div>
                    <div class='button'>Music</div>
                    <div class='button'>Video</div>
                    <div class='button'>Misc</div>
                </div>
                <div class='tip'>You can drag and drop files for easy upload.</div>
                <div id='file_list' class='file_list'>
                </div>
            </div>
        </div>
        <div class="playlist">
        	<div class="heading">
                <h5>SONGS</h5>
                <div class="buttonadd">
                    <a onclick='showPagePopup(false);' title='Add a song to your site'>Add Song</a>
                </div>
            </div>
            <div class="list" style='display: none;'>
                <ul>
                    <li class="listheading">
                        <span class="title">Title</span>
                        <span class="share">Share</span>
                        <span class="socialize">Socialize</span>
                        <span class="delete">Delete</span>
                    </li>
                </ul>
                <ul id='page_list_ul' class="playlist_sortable">
                </ul>
            </div>
        </div>
        
        <div class="photolist">
            <div class="heading">
            <h5>Photos</h5>
            <div class="buttonadd"><a onclick='showPhotoPopup(false);' title='Add a photo to your site'>Add Photo</a></div>
            </div>
        
            <div class="list" style='display: none;'>
            <ul id="photo_list_ul" class="photos_sortable">
            </ul>
            </div>
        </div>
        
        <div class="videolist">
            <div class="heading">
            <h5>Videos</h5>
            <div class="buttonadd"><a onclick='showVideoPopup(false);' title='Add a video to your site'>Add Video</a></div>
            </div>
        
            <div class="list" style='display: none;'>
            <ul id="video_list_ul" class="videos_sortable">
            </ul>
            </div>
        </div>
        
        <div class="products">
        
            <div class="heading">
            <h5>Store</h5>
            <? if( strlen($paypalEmail) == 0 ): ?>
                <div class="buttonadd"><a onclick='showStoreSettings();' title='Edit your store settings'>Store Settings</a></div>
            <? else: ?>
                <div class="buttonadd"><a onclick='showProductPopup(false);' title='Add a product to your store'>Add Product</a></div>
            <? endif ?>

            </div>
        
            <div class="list" style='display: none;'>
            <ul id="product_list_ul" class="products_sortable">
            </ul>
            </div>
        </div>

        <div class="pages">
            <div class="heading">
                <h5>TABS</h5>
                <div class="buttonadd">
                    <a id='add_tab_link' onclick='showTabPopup(false);' title='Add a tab to your site'>Add Tab</a>
                </div>
        	</div> 
            
            <div class="list" style='display: none;'>
            <ul>
            <li class="listheading">
            <span class="title">Title</span>
            <span class="delete">Delete</span>
            </li>
            </ul>
            <ul id='tab_list_ul' class="pages_sortable">
            </ul>
            </div>
        </div>

        <div class="branding_tips">
            <div class="heading">
                <h5>BRANDING TIPS</h5>
        	</div> 
            
            <div class="list">
                <ul class="branding_tips">
                    <li class="branding_tips odd">
                        <span class='branding_left'>Tip #1 - </span>
                        <span class='branding_right'>Pages - Be creative! This is your art collection, your magazine, your radio station. Become a Producer of your Brand.</span>
                    </li>
                    <li class="branding_tips">
                        <span class='branding_left'>Tip #2 - </span>
                        <span class='branding_right'>Monetize - Image is everything. People buy things that look good. Make your image as good as possible to increase sales.</span>
                    </li>
                    <li class="branding_tips odd">
                        <span class='branding_left'>Tip #3 - </span>
                        <span class='branding_right'>Videos - Quality, Quality, Quality. We suggest using Canon camera products. Great quality and cost.</span>
                    </li>
                    <li class="branding_tips">
                        <span class='branding_left'>Tip #4 - </span>
                        <span class='branding_right'>Tabs - The more info in the form of text you add about yourself the better chance your account will be found. Learn the term "Metadata". 
                        </span>
                    </li>
                </ul>
            </div>
        </div>
        

        </div>
    </div>
    
    </div><!-- admin -->
    
    
</section><!-- content -->
</section><!-- wrapper -->

<?php
	include('footer.php');
?>