<?php 

    require_once '../includes/config.php';
	require_once '../includes/functions.php';

    session_start();
    session_write_close();
	if( $_SESSION['sess_userId'] == "" )
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
	
	$sql = "SELECT mydna_musicplayer_audio.*, artist_files.extra_json AS image_extra_json";
    $sql .= " FROM mydna_musicplayer_audio ";
    $sql .= " LEFT JOIN artist_files ON mydna_musicplayer_audio.image = artist_files.filename";
    $sql .= " WHERE mydna_musicplayer_audio.artistid='$artistID'";
    $sql .= " ORDER BY mydna_musicplayer_audio.order ASC, mydna_musicplayer_audio.id DESC";
	$result_artistAudio = mq($sql) or die(mysql_error());
    $page_list = array();
    while( $row = mf($result_artistAudio) )
    {
        $image_extra = json_decode($row['image_extra_json'],TRUE);
        $row['short_link'] = make_short_link($row['abbrev']);
        $row['image_extra'] = $image_extra;
        array_walk($row,cleanup_row_element);
        $row['download'] = $row['download'] == "0" ? FALSE : TRUE;
        $row['product_id'] = $row['product_id'] > 0 ? intval($row['product_id']) : FALSE;
        $page_list[] = $row;
    }
    $page_list_json = json_encode($page_list);
	
	$sql = "SELECT mydna_musicplayer_video.*, artist_files.extra_json AS image_extra_json";
    $sql .= " FROM mydna_musicplayer_video ";
    $sql .= " LEFT JOIN artist_files ON mydna_musicplayer_video.image = artist_files.filename";
    $sql .= " WHERE mydna_musicplayer_video.artistid = '$artistID'";
    $sql .= " ORDER BY mydna_musicplayer_video.order ASC, mydna_musicplayer_video.id DESC";
	$result_artistVideo = mq($sql) or die(mysql_error());
    $video_list = array();
    while( $row = mf($result_artistVideo) )
    {
        $image_extra = json_decode($row['image_extra_json'],TRUE);
        array_walk($row,cleanup_row_element);
        $row['image_extra'] = $image_extra;
        if( !empty($row['image']) )
            $row['image_url'] = artist_file_url($row['image']);
        else
            $row['image_url'] = "images/photo_video_01.jpg";
        
        $video_list[] = $row;
    }
    $video_list_json = json_encode($video_list);
    
	$sql = "SELECT photos.*, artist_files.extra_json AS image_extra_json";
    $sql .= " FROM photos ";
    $sql .= " LEFT JOIN artist_files ON photos.image = artist_files.filename";
    $sql .= " WHERE photos.artist_id = '$artistID'";
    $sql .= " ORDER BY photos.order ASC, photos.id DESC";
	$q_photo = mq($sql) or die(mysql_error());
    $photo_list = array();
    while( $row = mf($q_photo) )
    {
        $image_extra = json_decode($row['image_extra_json'],TRUE);
        array_walk($row,cleanup_row_element);
        $row['image_extra'] = $image_extra;
        if( !empty($row['image']) )
            $row['image_url'] = artist_file_url($row['image']);
        else
            $row['image_url'] = "images/photo_video_01.jpg";
        
        $photo_list[] = $row;
    }
    $photo_list_json = json_encode($photo_list);

	$sql = "SELECT * FROM mydna_musicplayer_content  WHERE artistid='$artistID' ORDER BY `order` ASC, `id` DESC";
	$result_artistContent = mq($sql) or die(mysql_error());
    $tab_list = array();
    while( $row = mf($result_artistContent) )
    {
        array_walk($row,cleanup_row_element);
        if( !empty($row['image']) )
            $row['image_url'] = artist_file_url($row['image']);
        else
            $row['image_url'] = "images/photo_video_01.jpg";
        
        $tab_list[] = $row;
    }
    $tab_list_json = json_encode($tab_list);
	
	$sql = "SELECT mydna_musicplayer_ecommerce_products.*, artist_files.extra_json AS image_extra_json";
    $sql .= " FROM mydna_musicplayer_ecommerce_products ";
    $sql .= " LEFT JOIN artist_files ON mydna_musicplayer_ecommerce_products.image = artist_files.filename";
    $sql .= " WHERE mydna_musicplayer_ecommerce_products.artistid = '$artistID'";
    $sql .= " ORDER BY mydna_musicplayer_ecommerce_products.order ASC, mydna_musicplayer_ecommerce_products.id DESC";
	$result_artistProduct = mq($sql) or die(mysql_error());
	$product_list = array();
    while( $row = mf($result_artistProduct) )
    {
        $image_extra = json_decode($row['image_extra_json'],TRUE);
        $product_id = $row['id'];
        $row = get_product_data($product_id);
        $row['image_extra'] = $image_extra;
        $product_list[] = $row;
    }
    $product_list_json = json_encode($product_list);
    
    $sql = "SELECT * FROM artist_files WHERE artist_id='$artistID' AND upload_filename != '' AND deleted = 0 ORDER BY id DESC";
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
                      "error" => $file['error'],
                      );
        $file_list[] = $item;
    }
    $file_list_json = json_encode($file_list);
	
	if($record_artistDetail['logo'] == '')
		$artist_img_logo = "/manage/images/artist_need_image.jpg";
	else
		$artist_img_logo = artist_file_url($record_artistDetail['logo']);

	$img_url = $artist_img_logo;

    $is_published = TRUE;
    $artist_url = str_replace("http://www.","http://".$record_artistDetail['url'].".",trueSiteUrl());
    if( strlen($record_artistDetail['preview_key']) > 0 )
    {
        $preview_key = $record_artistDetail['preview_key'];
        $is_published = FALSE;
        $artist_url .= "?preview_key=$preview_key";
    }

    $show_first_instruction = FALSE;
    if( $_SESSION['sess_userType'] == 'ARTIST' )
    {
        if( !$record_artistDetail['shown_first_instructions'] )
        {
            $show_first_instruction = TRUE;
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
    
    
    $template_list = array();
    $sql = "SELECT * FROM templates WHERE artist_id='$artistID'";
    $q = mq($sql);
    while( $t = mf($q) )
    {
        $params_json = $t['params_json'];
        $params = json_decode($params_json,TRUE);
        
        $t['params'] = $params;
        
        $template_list[] = $t;
    }
    $template_list_json = json_encode($template_list);
    

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
    include_once 'include/edit_template.html';
    
    if( $_SESSION['sess_userType'] == 'SUPER_ADMIN' )
        include_once 'include/edit_account_settings.html';
    
?>

<script src="js/artist_management.js" type="text/javascript"></script>
<script type="text/javascript">

    var g_artistId = <?=$artistID?>;
    var g_facebook = <?=$facebook;?>;
    var g_twitter = <?=$twitter;?>;
    var g_artistData = <?=$artist_data_json;?>;

    var g_artistPageUrl = "<?=$artist_url;?>";

    var g_pageList = <?=$page_list_json;?>;
    var g_photoList = <?=$photo_list_json;?>;
    var g_videoList = <?=$video_list_json;?>;
    var g_tabList = <?=$tab_list_json;?>;
    var g_productList = <?=$product_list_json;?>;
    var g_fileList = <?=$file_list_json;?>;
    var g_templateList = <?=$template_list_json;?>;

    var g_playerUrl = "<?=playerUrl();?>";
    var g_artistFileBaseUrl = "<?=artist_file_base_url();?>";

    var g_isPublished = <?=json_encode($is_published);?>;

    var g_shouldShowFirstInstruction = <?=json_encode($show_first_instruction);?>

</script>

<section id="wrapper">
<section id="content" class='thin'>
	
    <div id="admin">
        <div class='name_publish'>
            <div class='name'>
                <a class='artist_page_url' id='profile_name_anchor' href="<?=$artist_url;?>" target="_blank">
                    <?=$record_artistDetail['artist'];?>
                </a>
            </div>
            <div class='publish'>
                <div class='published'>You page is published and live at the URL below.</div>
                <div class='not_published'>You page is not published, you can use the link below to preview your site.</div>
                <div class='link_edit'>
                    <div class='link'>Site URL: <a class='artist_page_url' href="<?=$artist_url;?>" target="_blank"><?=$artist_url;?></a></div>
                    <div class='sep'></div>
                    <div class='edit' onclick='showEditProfile();'>Edit</div>
                </div>
                <div class='buttons'>
                    <div id='publish_button' class='button' onclick='publishSite();'>
                        <div class='label'>Publish</div>
                        <div class='icon'></div>
                    </div>
                    <div id='unpublish_button' class='button' onclick='confirmUnpublishSite();'>
                        <div class='label'>Unpublish</div>
                        <div class='icon'></div>
                    </div>
                </div>
            </div>
        </div>
        <div style="clear: both;"></div>
        
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
                    <a id='view_site_anchor' class='artist_page_url no_underline' href="<?=$artist_url;?>" target="_blank" title='View your site'>
                        <div class='block_button'>
                            <div class='icon'></div>
                            <div class='label'>View Site</div>
                        </div>
                    </a>
                </li>
            </ul>
            <h6>Platform</h6>
            <ul>
                <li><a onclick='showPagePopup(false);' title='Add audio to your site'>Add Audio</a></li>
                <li><a onclick='showVideoPopup(false);' title='Add a video to your site'>Add Video</a></li>
                <li><a onclick='showPhotoPopup(false);' title='Add a photo to your site'>Add Photo</a></li>
                <li id='add_tab_list_item'><a onclick='showTabPopup(false);' title='Add a tab to your site'>Add Tab</a></li>
                <li><a href="stats.php?userId=<?=$artistID;?>" title='View website analytics for you site'>View Analytics</a></li>
                <li><a onclick='showFanConnections();' title='Get a list of Fans of your site'>Fan Connections</a></li>
            </ul>
            
            <h6>Store</h6>
            <ul>
                <li><a onclick='showProductPopup(false);' title='Add a product to your store'>Add Product</a></li>
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
                <div class='title'>MEDIA LIBRARY</div>
                <a onclick='showAddArtistFilePopup(false);' title='Add files for your site'>
                    <div class="buttonadd">
                        <div class='plus'></div>
                        <div class='label'>Add Files</div>
                    </div>
                </a>
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
                <h5>AUDIO</h5>
                <a onclick='showPagePopup(false);' title='Add a song to your site'>
                    <div class="buttonadd">
                        <div class='plus'></div>
                        <div class='label'>Add Audio</div>
                    </div>
                </a>
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
                <a onclick='showPhotoPopup(false);' title='Add a photo to your site'>
                    <div class="buttonadd">
                        <div class='plus'></div>
                        <div class='label'>Add Photo</div>
                    </div>
                </a>
            </div>
        
            <div class="list" style='display: none;'>
            <ul id="photo_list_ul" class="photos_sortable">
            </ul>
            </div>
        </div>
        
        <div class="videolist">
            <div class="heading">
                <h5>Videos</h5>
                <a onclick='showVideoPopup(false);' title='Add a video to your site'>
                    <div class="buttonadd">
                        <div class='plus'></div>
                        <div class='label'>Add Video</div>
                    </div>
                </a>
            </div>
        
            <div class="list" style='display: none;'>
            <ul id="video_list_ul" class="videos_sortable">
            </ul>
            </div>
        </div>
        
        <div class="products">
        
            <div class="heading">
                <h5>Store</h5>
                <a onclick='showProductPopup(false);' title='Add a product to your store'>
                    <div class="buttonadd">
                        <div class='plus'></div>
                        <div class='label'>Add Product</div>
                    </div>
                </a>
            </div>
        
            <div class="list" style='display: none;'>
            <ul id="product_list_ul" class="products_sortable">
            </ul>
            </div>
        </div>

        <div class="pages">
            <div class="heading">
                <h5>TABS</h5>
                <a id='add_tab_link' onclick='showTabPopup(false);' title='Add a tab to your site'>
                    <div class="buttonadd">
                        <div class='plus'></div>
                        <div class='label'>Add Tab</div>
                    </div>
                </a>
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

        <!--
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
        -->
        

        </div>
    </div>
    
    </div><!-- admin -->
    
    
</section><!-- content -->
</section><!-- wrapper -->

<div id='drop_file_overlay'>
    <div class='text'>Drop files anywhere to upload&hellip;</div>
</div>

<?php
	include('footer.php');
?>