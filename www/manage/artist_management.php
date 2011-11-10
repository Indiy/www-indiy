<?php 

    if( $_REQUEST['session_id'] )
        session_id($_REQUEST['session_id']);

    require_once '../includes/config.php';
	require_once '../includes/functions.php';	
	if($_SESSION['sess_userId']=="")
	{
		header("location: index.php");
		exit();
	}
	$artistID = $_REQUEST['userId']; 
	
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
	
	$find_artistProduct = "SELECT * FROM mydna_musicplayer_ecommerce_products  WHERE artistid='".$artistID."' ORDER BY `order` ASC, `id` DESC";
	$result_artistProduct = mysql_query($find_artistProduct) or die(mysql_error());
	//echo "<pre />";
	//print_r($record_artistDetail);
	
	///Timthumb for profile images 
	
	if($record_artistDetail['logo'] == '')
		$artist_img_logo = 'images/NoPhoto.jpg';
	else
		$artist_img_logo = '../artists/images/'.$record_artistDetail['logo'];

	$img_url = "timthumb.php?src=".$artist_img_logo.'&amp;w=220&amp;h=248&amp;zc=1&amp;q=100';

    $artist_url = str_replace("http://www.","http://".$record_artistDetail['url'].".",trueSiteUrl());

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

</script>

<section id="wrapper">
<section id="content">
	
    <div id="admin">
    <h2><?php echo $record_artistDetail['artist']; ?></h2>
    
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
        <figure><img src="<?php echo $img_url; ?>" alt="<?php echo $record_artistDetail['artist']; ?>" title="<?php echo $record_artistDetail['artist']; ?>" /></figure>
        
        <h6>Music Player</h6>
        <ul>
        <li><a href="<?=$artist_url;?>">View Site</a></li>
        <li><a href="stats.php?userId=<?=$artistID;?>">Analytics</a></li>
        <li><a href="#">Newsletter</a></li>
        <li><a href="addmusic.php?artist_id=<?=$artistID?>" rel="facebox[.bolder]">Add Music</a></li>
        <li><a href="addvideo.php?artist_id=<?=$artistID?>" rel="facebox[.bolder]">Add Video</a></li>
        <li><a href="addcontent.php?artist_id=<?=$artistID?>" rel="facebox[.bolder]">Add Pages</a></li>
        </ul>
        
        <h6>Ecommerce</h6>
        <ul>
        <li><a href="store_settings.php?artist_id=<?=$artistID?>" rel="facebox[.bolder]">Settings</a></li>
        <li><a href="addproduct.php?artist_id=<?=$artistID?>" rel="facebox[.bolder]">Add Product</a></li>
        </ul>
		 <h6>Manage Profile</h6>
		<ul>
        <li><a href="register.php?artist_id=<?=$artistID?>" rel="facebox[.bolder]">Settings</a></li>
		</ul>
        <h6>Add Social Channel</h6>
        <ul>
        <li><a href="#">Add your Facebook Account</a></li>
        </ul>
        </div>
        
        <div class="column2">
        <div class="embedcode">
            <label>EMBED CODE</label>
            <textarea name="" cols="" rows="" class="textarea"><iframe src="<?=playerUrl().$record_artistDetail[url]?>&embed=true" border="0" width="400" height="600" frameborder="0" name="<?=$record_artistDetail['url']?>"></iframe></textarea>
        </div>
        
        <div class="playlist">
        	<div class="heading">
            <h5>PLAYLIST</h5>
            <div class="buttonadd"><a href="addmusic.php?artist_id=<?=$artistID?>" rel="facebox[.bolder]">Add Song</a></div>
            </div>
            
            <div class="list">
            <ul>
            <li class="listheading">
            <span class="title">Title</span>
            <span class="duration">Duration</span>
            <span class="preview">Preview</span>
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
					
					echo "<li id='arrayorder_$song_id' class='playlist_sortable $class' >
							<span class='title'><a href='addmusic.php?artist_id=".$artistID."&id=".$record_artistAudio['id']."' rel='facebox[.bolder]'>".$record_artistAudio['name']."</a></span>
							<span class='duration'>".$record_artistAudio['audio_duration']."</span>";
					
					if(!empty($record_artistAudio['audio']))
						echo	"<span class='preview'><a href='play_music.php?song=".$record_artistAudio['audio']."' rel='facebox[.bolder]' >Play</a></span>";
					else
						echo	"<span class='preview'>N/A</span>";

					echo 	"<span class='delete'><a href='#' onclick='if(confirm(\"Are you sure you want delete this item?\"))location.href=\"artist_management.php?userId=$userId&action=1&song_id=".$record_artistAudio['id']."\";' ></a></span>
						 </li>";
						 
					$count++;
				}
				
			?>                      

            </ul>
            </div>
        </div>
        
        <div class="videolist">
            <div class="heading">
            <h5>Videos</h5>
            <div class="buttonadd"><a href="addvideo.php?artist_id=<?=$artistID?>" rel="facebox[.bolder]">Add Video</a></div>
            </div>
        
            <div class="list">
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
           <?php
			if(!empty($record_artistVideo['video'])){?>
				<a href='play_video.php?videoID=<?=$record_artistVideo['id']?>' rel='facebox[.bolder]' ><img src="<?=$image?>" width="210" height="132" alt=""></a></figure>
		        <span><a href="addvideo.php?artist_id=<?=$artistID?>&id=<?=$record_artistVideo['id']?>" rel="facebox[.bolder]"><?=stripslashes($record_artistVideo['name'])?></a></span><br><!-- 3:35 -->
			<?}else{?>
				<img src="<?=$image?>" width="210" height="132" alt=""></figure>
				<span><?=stripslashes($record_artistVideo['name'])?></span><br><!-- 3:35 -->
			<?}?>
            </li>
			<?}?>
            </ul>
            </div>
        </div>
        
        <div class="pages">
            <div class="heading">
            <h5>PAGES</h5>
            <div class="buttonadd"><a href="addcontent.php?artist_id=<?=$artistID?>" rel="facebox[.bolder]">Add Page</a></div>
        	</div> 
            
            <div class="list">
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
        
        <div class="products">
        
            <div class="heading">
            <h5>PRODUCTS</h5>
            <div class="buttonadd"><a href="addproduct.php?artist_id=<?=$artistID?>" rel="facebox[.bolder]">Add Product</a></div>
            </div>
        
            <div class="list">
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
        
        </div>
    </div>
    
    </div><!-- admin -->
    
    
</section><!-- content -->
</section><!-- wrapper -->


<script src="ajaxupload/file.js" type="text/javascript"></script>
<script src="ajaxupload/jquery.iframe-post-form.js" type="text/javascript"></script>

<?php
	include('footer.php');
?>