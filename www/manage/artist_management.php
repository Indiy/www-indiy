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

	if(isset($_REQUEST['action'])){
		if(isset($_REQUEST['song_id']))
			$type = "audio";
		if(isset($_REQUEST['video_id']))
			$type = "video";
		if(isset($_REQUEST['content_id']))
			$type = "content";
		if(isset($_REQUEST['prod_id']))
			$type = "product";
		switch($type){
			case 'audio':
				mysql_query("delete FROM mydna_musicplayer_audio  WHERE id='".$_REQUEST['song_id']."' ");
				break;
			case 'video':
				mysql_query("delete FROM mydna_musicplayer_video  WHERE id='".$_REQUEST['video_id']."' ");
				break;
			case 'content':
				mysql_query("delete FROM mydna_musicplayer_content  WHERE id='".$_REQUEST['content_id']."' ");
				break;
			case 'product':
				mysql_query("delete FROM mydna_musicplayer_ecommerce_products  WHERE id='".$_REQUEST['prod_id']."' ");
				break;
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
        $image_path = "../artists/images/" . $row['image'];
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
        $image_path = "../artists/images/" . $row['image'];
        if( !empty($row['image']) && file_exists($image_path) )
            $row['image_url'] = $image_path;
        else
            $row['image_url'] = "images/photo_video_01.jpg";
        
        $video_list[] = $row;
    }
    $video_list_json = json_encode($video_list);

	$find_artistContent = "SELECT * FROM mydna_musicplayer_content  WHERE artistid='".$artistID."' ORDER BY `order` ASC, `id` DESC";
	$result_artistContent = mysql_query($find_artistContent) or die(mysql_error());
    $tab_list = array();
    while( $row = mysql_fetch_array($result_artistContent) )
    {
        array_walk($row,cleanup_row_element);
        $image_path = "../artists/images/" . $row['image'];
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
        array_walk($row,cleanup_row_element);
        $image_path = "../artists/products/" . $row['image'];
        if( !empty($row['image']) && file_exists($image_path) )
            $row['image'] = $image_path;
        else
            $row['image'] = "images/photo_video_01.jpg";
        $product_list[] = $row;
    }
    $product_list_json = json_encode($product_list);
     
	
	if($record_artistDetail['logo'] == '')
		$artist_img_logo = 'images/NoPhoto.jpg';
	else
		$artist_img_logo = '../artists/images/'.$record_artistDetail['logo'];

	$img_url = "timthumb.php?src=$artist_img_logo&w=220&zc=1&q=100";

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
        array_walk($record_artistDetail,cleanup_row_element);
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
    
    $store_check = mf(mq("SELECT * FROM `[p]musicplayer_ecommerce` WHERE `userid`='$artistID' LIMIT 1"));
    $paypalEmail = $store_check["paypal"];
    $record_artistDetail['paypal_email'] = $paypalEmail;

    array_walk($record_artistDetail,cleanup_row_element);
    $artist_data_json = json_encode($record_artistDetail);


    require_once 'header.php';
    
    include_once 'include/edit_page.html';
    include_once 'include/edit_product.html';
    include_once 'include/edit_video.html';
    include_once 'include/edit_tab.html';
    include_once 'include/edit_social_config.html';
    
    include_once 'include/popup_messages2.html';
?>

<script type="text/javascript">

var g_artistId = <?=$artistID?>;
var g_facebook = <?=$facebook;?>;
var g_twitter = <?=$twitter;?>;
var g_paypalEmail = "<?=$paypalEmail;?>";
var g_artistData = <?=$artist_data_json;?>;

var g_pageList = <?=$page_list_json;?>;
var g_videoList = <?=$video_list_json;?>;
var g_tabList = <?=$tab_list_json;?>;
var g_productList = <?=$product_list_json;?>;

<? if( $show_first_instruction ): ?>

function showFirstInstruction()
{
    $.facebox.loading(true);
    $.get('/manage/first_instructions.php',function(data) { $.facebox.reveal(data, "bolder"); });
}

$(document).ready(showFirstInstruction);

<? endif; ?>

</script>

<section id="wrapper">
<section id="content">
	
    <div id="admin">
    <h2><a href="<?=$artist_url;?>"><?php echo $record_artistDetail['artist']; ?></a></h2>
    
    <!--
    <div class="search">
    <fieldset>
    <input name="" value="SEARCH" type="text" class="input" />
    <input name="" type="image" src="images/icon_search.gif" class="button">
    </fieldset>
    </div>
    -->
    
    <div id="adminblock">
    	<div class="column1">
            <figure>
                <a href="register.php?artist_id=<?=$artistID?>" rel="facebox[.bolder]">
                    <img src="<?=$img_url;?>" alt="<?php echo $record_artistDetail['artist']; ?>" title="<?php echo $record_artistDetail['artist']; ?>" />
                </a>
            </figure>
            
            <h6>Manage Profile</h6>
            <ul>
                <li><a href="register.php?artist_id=<?=$artistID?>" rel="facebox[.bolder]">Edit Profile</a></li>
                <li><a href="social_config.php?artist_id=<?=$artistID?>" rel="facebox[.bolder]">Social Connections</a></li>
                <li><a href="invite_friends.php?artist_id=<?=$artistID?>" rel="facebox[.bolder]">Invite Friends</a></li>
                <li><a class='view_site' href="<?=$artist_url;?>" target="_blank">View Site</a></li>
            </ul>
            <h6>Platform</h6>
            <ul>
                <li><a onclick='showPagePopup(false);'>Add Audio + Photo</a></li>
                <li><a onclick='showVideoPopup(false);'>Add Video</a></li>
                <li id='add_tab_list_item'><a onclick='showTabPopup(false);'>Add Tab</a></li>
                <li><a href="stats.php?userId=<?=$artistID;?>">View Analytics</a></li>
                <li><a href="fan_connections.php?artist_id=<?=$artistID;?>" rel="facebox[.bolder]">Fan Connections</a></li>
            </ul>
            
            <h6>Store</h6>
            <ul>
                <li><a href="store_settings.php?artist_id=<?=$artistID?>" rel="facebox[.bolder]">Edit Settings</a></li>
                <? if( strlen($paypalEmail) == 0 ): ?>
                    <li><a href="store_settings.php?artist_id=<?=$artistID?>" rel="facebox[.bolder]">Add Product</a></li>
                <? else: ?>
                    <li><a onclick='showProductPopup(false);'>Add Product</a></li>
                <? endif ?>
            </ul>
            <h6>Misc</h6>
            <ul>
                <li><a onclick='deleteAccount(<?=$artistID;?>);'>Delete Account</a></li>
            </ul>
            <? if( $_SESSION['sess_userType'] == 'SUPER_ADMIN' ): ?>
                <h6>Super Admin</h6>
                <ul>
                <li><a href="account_settings.php?artist_id=<?=$artistID?>" rel="facebox[.bolder]">Account Settings</a></li>
                </ul>
            <? endif; ?>
        </div>
        
        <div class="column2">
        
        <div class="playlist">
        	<div class="heading">
                <h5>PAGES</h5>
                <div class="buttonadd">
                    <a onclick='showPagePopup(false);'>Add Audio + Photo</a>
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
        
        <div class="products">
        
            <div class="heading">
            <h5>Store</h5>
            <? if( strlen($paypalEmail) == 0 ): ?>
                <div class="buttonadd"><a href="store_settings.php?artist_id=<?=$artistID?>" rel="facebox[.bolder]">Store Settings</a></div>
            <? else: ?>
                <div class="buttonadd"><a onclick='showProductPopup(false);'>Add Product</a></div>
            <? endif ?>

            </div>
        
            <div class="list" style='display: none;'>
            <ul id="product_list_ul" class="products_sortable">
            </ul>
            </div>
        </div>
        
        <div class="videolist">
            <div class="heading">
            <h5>Videos</h5>
            <div class="buttonadd"><a onclick='showVideoPopup(false);'>Add Video</a></div>
            </div>
        
            <div class="list" style='display: none;'>
            <ul id="video_list_ul" class="videos_sortable">
            </ul>
            </div>
        </div>
        
        <div class="pages">
            <div class="heading">
                <h5>TABS</h5>
                <div class="buttonadd">
                    <a id='add_tab_link' onclick='showTabPopup(false);'>Add Tab</a>
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