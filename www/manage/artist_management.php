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
	
	$find_artistVideo = "SELECT * FROM mydna_musicplayer_video  WHERE artistid='".$artistID."' ORDER BY `order` ASC, `id` DESC";
	$result_artistVideo = mysql_query($find_artistVideo) or die(mysql_error());

	$find_artistContent = "SELECT * FROM mydna_musicplayer_content  WHERE artistid='".$artistID."' ORDER BY `order` ASC, `id` DESC";
	$result_artistContent = mysql_query($find_artistContent) or die(mysql_error());
	
	$find_artistProduct = "SELECT * FROM mydna_musicplayer_ecommerce_products  WHERE artistid='$artistID' AND sku != 'MADSONG' ORDER BY `order` ASC, `id` DESC";
	$result_artistProduct = mysql_query($find_artistProduct) or die(mysql_error());
	//echo "<pre />";
	//print_r($record_artistDetail);
	
	///Timthumb for profile images 
	
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
    
    $twitter = FALSE;
    $facebook = FALSE;
    if( $record_artistDetail['oauth_token'] && $record_artistDetail['oauth_secret'] && $record_artistDetail['twitter'] )
        $twitter = TRUE;
    
    if( $record_artistDetail['fb_access_token'] && $record_artistDetail['facebook'] )
        $facebook = TRUE;

    require_once 'header.php';
?>

<script type="text/javascript">

function setupSortableLists()
{
    $(function() {
        $("ul.playlist_sortable").sortable({opacity: 0.8, cursor: 'move', update: function() {
            //$("#response").html("Loading...");
                var order = $(this).sortable("serialize") + '&order=order&type=musicplayer_audio';
                $.post("/includes/ajax.php", order, function(theResponse){
                    //$("#response").html(theResponse);
                });
            }
        });
    });

    $(function() {
        $("ul.pages_sortable").sortable({opacity: 0.8, cursor: 'move', update: function() {
            //$("#response").html("Loading...");
                var order = $(this).sortable("serialize") + '&order=order&type=musicplayer_content';
                $.post("/includes/ajax.php", order, function(theResponse){
                    //$("#response").html(theResponse);
                });
            }
        });
    });

    $(function() {
        $("ul.videos_sortable").sortable({opacity: 0.8, cursor: 'move', update: function() {
            //$("#response").html("Loading...");
                var order = $(this).sortable("serialize") + '&order=order&type=musicplayer_video';
                $.post("/includes/ajax.php", order, function(theResponse){
                    //$("#response").html(theResponse);
                });
            }
        });
    });			

    $(function() {
        $("ul.products_sortable").sortable({opacity: 0.8, cursor: 'move', update: function() {
            //$("#response").html("Loading...");
                var order = $(this).sortable("serialize") + '&order=order&type=musicplayer_ecommerce_products';
                $.post("/includes/ajax.php", order, function(theResponse){
                    //$("#response").html(theResponse);
                });
            }
        });
    });
}

$(document).ready(setupSortableLists);

<? if( $show_first_instruction ) {?>

function showFirstInstruction()
{
    $.facebox.loading(true);
    $.get('/manage/first_instructions.php',function(data) { $.facebox.reveal(data, "bolder"); });
}

$(document).ready(showFirstInstruction);

<? } ?>

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
            <figure><img src="<?=$img_url;?>" alt="<?php echo $record_artistDetail['artist']; ?>" title="<?php echo $record_artistDetail['artist']; ?>" /></figure>
            
            <h6>Manage Profile</h6>
            <ul>
                <li><a href="register.php?artist_id=<?=$artistID?>" rel="facebox[.bolder]">Edit Profile</a></li>
                <li><a href="social_config.php?artist_id=<?=$artistID?>" rel="facebox[.bolder]">Social Connections</a></li>
                <li><a href="invite_friends.php?artist_id=<?=$artistID?>" rel="facebox[.bolder]">Invite Friends</a></li>
                <li><a style="font-weight: bold;" href="<?=$artist_url;?>">View Site</a></li>
            </ul>
            <h6>Platform</h6>
            <ul>
                <li><a href="addmusic.php?artist_id=<?=$artistID?>" rel="facebox[.bolder]">Add Music + Photo</a></li>
                <li><a href="addvideo.php?artist_id=<?=$artistID?>" rel="facebox[.bolder]">Add Video</a></li>
                <li><a href="addcontent.php?artist_id=<?=$artistID?>" rel="facebox[.bolder]">Add Tab</a></li>
                <li><a href="stats.php?userId=<?=$artistID;?>">View Analytics</a></li>
                <li><a href="fan_connections.php?artist_id=<?=$artistID;?>" rel="facebox[.bolder]">Fan Connections</a></li>
            </ul>
            
            <h6>Ecommerce</h6>
            <ul>
                <li><a href="store_settings.php?artist_id=<?=$artistID?>" rel="facebox[.bolder]">Edit Settings</a></li>
                <li><a href="addproduct.php?artist_id=<?=$artistID?>" rel="facebox[.bolder]">Add Product</a></li>
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
            <div class="buttonadd"><a href="addmusic.php?artist_id=<?=$artistID?>" rel="facebox[.bolder]">Add Music + Photo</a></div>
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
            <ul class="playlist_sortable">
            
            <?php
				$count = 1;
				while($record_artistAudio = mysql_fetch_array($result_artistAudio))
				{
                    $song_id = $record_artistAudio['id'];
					$class = (( $count%2) == 0) ? '' : 'odd';
                    $short_link = make_short_link($record_artistAudio['abbrev']);
                    $link_name = "madna.co/" . $record_artistAudio['abbrev'];
					
					echo "<li id='arrayorder_$song_id' class='playlist_sortable $class' >\n";
                    echo "<span class='title'>\n";
                    echo "<a href='addmusic.php?artist_id=".$artistID."&id=".$record_artistAudio['id']."' rel='facebox[.bolder]'>";
                    echo $record_artistAudio['name'];
                    echo "</a>\n";
                    echo "</span>\n";
                    echo "<span class='share'>";
                    echo "<a href='$short_link' target='_blank'>Link</a>";
                    echo "</span>\n";
					
                    /*
					if(!empty($record_artistAudio['audio']))
						echo	"<span class='preview'><a href='play_music.php?song=".$record_artistAudio['audio']."' rel='facebox[.bolder]' >Play</a></span>";
					else
						echo	"<span class='preview'>N/A</span>";
                    */
                    echo "<span class='socialize'>";
                    if( $facebook )
                    {
                        echo "<a href='socialize.php?artist_id=".$artistID."&song_id=".$record_artistAudio['id']."' rel='facebox[.bolder]'>";
                        echo "<img class='social_icon' src='/images/fb_icon_color.png'/>";
                        echo "</a>\n";
                    }
                    else
                    {
                        echo "<a href='social_config.php?artist_id=$artistID' rel='facebox[.bolder]'>";
                        echo "<img class='social_icon' src='/images/fb_icon_grey.png'/>";
                        echo "</a>\n";
                    }
                    if( $twitter )
                    {
                        echo "<a href='socialize.php?artist_id=".$artistID."&song_id=".$record_artistAudio['id']."' rel='facebox[.bolder]'>";
                        echo "<img class='social_icon' src='/images/tw_icon_color.png'/>";
                        echo "</a>\n";
                    }
                    else
                    {
                        echo "<a href='social_config.php?artist_id=$artistID' rel='facebox[.bolder]'>";
                        echo "<img class='social_icon' src='/images/tw_icon_grey.png'/>";
                        echo "</a>\n";
                    }
                    echo "</span>";

					echo 	"<span class='delete'><a href='#' onclick='if(confirm(\"Are you sure you want delete this item?\"))location.href=\"artist_management.php?userId=$userId&action=1&song_id=".$record_artistAudio['id']."\";' ></a></span>";
                    echo "</li>\n";
						 
					$count++;
				}
				
			?>                      

            </ul>
            </div>
        </div>
        
        <div class="products">
        
            <div class="heading">
            <h5>MONETIZE</h5>
            <div class="buttonadd"><a href="addproduct.php?artist_id=<?=$artistID?>" rel="facebox[.bolder]">Add Product</a></div>
            </div>
        
            <div class="list" style='display: none;'>
            <ul class="products_sortable">
           <?php
				$count = 1;
				while($record_artistProduct = mysql_fetch_array($result_artistProduct))
				{
                    $product_id = $record_artistProduct['id'];
                
					if(!empty($record_artistProduct['image']) && file_exists("../artists/products/".$record_artistProduct['image'])){
						$image = "../artists/products/".$record_artistProduct['image'];
					}else{
						$image = "images/photo_video_01.jpg";
					}
					?>
			<li id="arrayorder_<?=$product_id;?>"class="products_sortable">
            <figure><span class="close"><a href='#' onclick='if(confirm("Are you sure you want delete this item?"))location.href="artist_management.php?userId=<?=$userId?>&action=1&prod_id=<?=$record_artistProduct['id']?>";'></a></span>
           <a href="addproduct.php?artist_id=<?=$artistID?>&id=<?=$record_artistProduct['id']?>" rel="facebox[.bolder]"><img src="<?=$image?>" width="207" height="130" alt=""></a></figure>
            <span><a href="addproduct.php?artist_id=<?=$artistID?>&id=<?=$record_artistProduct['id']?>" rel="facebox[.bolder]"><?=$record_artistProduct['name']?></a></span><br>$<?=$record_artistProduct['price']?>
            </li>
				<?}?>
           
            </ul>
            </div>
        </div>
        
        <div class="videolist">
            <div class="heading">
            <h5>Videos</h5>
            <div class="buttonadd"><a href="addvideo.php?artist_id=<?=$artistID?>" rel="facebox[.bolder]">Add Video</a></div>
            </div>
        
            <div class="list" style='display: none;'>
            <ul class="videos_sortable">
            <?php
				$count = 1;
				while($record_artistVideo = mysql_fetch_array($result_artistVideo))
				{
                    $video_id = $record_artistVideo['id'];
					if(!empty($record_artistVideo['image']) && file_exists("../artists/images/".$record_artistVideo['image'])){
						$image = "../artists/images/".$record_artistVideo['image'];
					}else{
						$image = "images/photo_video_01.jpg";
					}
                ?>
			<li id="arrayorder_<?=$video_id;?>" class="videos_sortable">
            <figure>
				<span class="close">
					<a href='#' onclick='if(confirm("Are you sure you want delete this item?"))location.href="artist_management.php?userId=<?=$userId?>&action=1&video_id=<?=$record_artistVideo['id']?>";'></a>
				</span>
				<a href="addvideo.php?artist_id=<?=$artistID?>&id=<?=$record_artistVideo['id']?>" rel="facebox[.bolder]">
                    <img src="<?=$image?>" width="210" height="132" alt="">
                </a>
            </figure>
            <span>
                <a href="addvideo.php?artist_id=<?=$artistID?>&id=<?=$record_artistVideo['id']?>" rel="facebox[.bolder]">
                    <?=stripslashes($record_artistVideo['name'])?>
                </a>
            </span>
            <br>
            </li>
			<?}?>
            </ul>
            </div>
        </div>
        
        <div class="pages">
            <div class="heading">
            <h5>TABS</h5>
            <div class="buttonadd"><a href="addcontent.php?artist_id=<?=$artistID?>" rel="facebox[.bolder]">Add Tab</a></div>
        	</div> 
            
            <div class="list" style='display: none;'>
            <ul>
            <li class="listheading">
            <span class="title">Title</span>
           <!--  <span class="preview">Preview</span> -->
            <span class="delete">Delete</span>
            </li>
            </ul>
            <ul class="pages_sortable">
            <?php
				$count = 1;
				while($record_artistContent = mysql_fetch_array($result_artistContent))
				{
                    $content_id = $record_artistContent['id'];
                
					$class = (( $count%2) == 0) ? '' : 'odd';
					?>
            <li id="arrayorder_<?=$content_id;?>" class="pages_sortable <?=$class?>">
            <span class="title"><a href="addcontent.php?artist_id=<?=$artistID?>&id=<?=$record_artistContent['id']?>" rel="facebox[.bolder]"><?=$record_artistContent['name']?></a></span>
            <!-- <span class="preview"><a href="#">Preview</a></span> -->
            <span class="delete"><a  href='#' onclick='if(confirm("Are you sure you want delete this item?"))location.href="artist_management.php?userId=<?=$userId?>&action=1&content_id=<?=$record_artistContent['id']?>";'></a></span>
            </li>
            <?}?>
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