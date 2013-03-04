<?php

	require_once('../includes/config.php');
	include_once('../includes/functions.php');	
    include_once("include/page.inc.php");

    session_start();
    session_write_close();
	if( $_SESSION['sess_userId'] == "" )
	{
		header("Location: /index.php");
		exit();
	}
	$artistID = $_REQUEST['userId']; 
	
	$query_artistDetail = "SELECT * FROM mydna_musicplayer WHERE id='".$artistID."' ";
	$result_artistDetail = mysql_query($query_artistDetail) or die(mysql_error());
	$record_artistDetail = mysql_fetch_array($result_artistDetail);

	if(isset($_REQUEST['action'])){
		if(isset($_REQUEST['artist_id']))
			$type = "artist";
		if(isset($_REQUEST['song_id']))
			$type = "audio";
		if(isset($_REQUEST['video_id']))
			$type = "video";
		if(isset($_REQUEST['content_id']))
			$type = "content";
		if(isset($_REQUEST['prod_id']))
			$type = "product";
		switch($type){
			case 'artist':
				mysql_query("delete FROM mydna_musicplayer WHERE id='".$_REQUEST['artist_id']."' ");
				break;
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
	
	$find_artistAudio = "SELECT * FROM mydna_musicplayer_audio  WHERE artistid='".$artistID."' ";
	$result_artistAudio = mysql_query($find_artistAudio) or die(mysql_error());
	
	$find_artistVideo = "SELECT * FROM mydna_musicplayer_video  WHERE artistid='".$artistID."' ";
	$result_artistVideo = mysql_query($find_artistVideo) or die(mysql_error());

	$find_artistContent = "SELECT * FROM mydna_musicplayer_content  WHERE artistid='".$artistID."' ";
	$result_artistContent = mysql_query($find_artistContent) or die(mysql_error());
	
	$find_artistProduct = "SELECT * FROM mydna_musicplayer_ecommerce_products  WHERE artistid='".$artistID."' ";
	$result_artistProduct = mysql_query($find_artistProduct) or die(mysql_error());
	//echo "<pre />";
	//print_r($record_artistDetail);
	
	///Timthumb for profile images 
	
	if($record_artistDetail['logo'] == '')
		$artist_img_logo = 'images/NoPhoto.jpg';
	else
		$artist_img_logo = '../artists/files/'.$record_artistDetail['logo'];

	$img_url = "timthumb.php?src=".$artist_img_logo.'&amp;w=220&amp;h=248&amp;zc=1&amp;q=100';

    $label_name = $_SESSION['sess_userName'];

    $include_order = FALSE;
    $include_editor = TRUE;

	include_once('header.php');	
?>
<section id="wrapper">
<section id="content">
	<section id="adminlist">
        <!--
        <div class="search">
        <fieldset>
	        <input name="" value="search" type="text" class="input" />
		    <input name="" type="image" src="images/icon_search.gif" class="button">
        </fieldset>
        </div>
        -->
        <h2><?=$label_name;?></h2>
		<?php
		if( $_SESSION['sess_userType'] != 'ARTIST' ):
		?>
        <div class="sortby">
	        <p>SORT BY:<a href="dashboard.php?sort_by=DESC" <?php if($_GET['sort_by']=='DESC' || $_GET['sort_by']==''){ echo 'class="active"';}?>> NEWSEST FIRST</a> / <a  href="dashboard.php?sort_by=ASC" <?php if($_GET['sort_by']=='ASC'){ echo 'class="active"';}?>>OLDEST FIRST</a> / <a href="dashboard.php?sort_by=name" <?php if($_GET['sort_by']=='name'){ echo 'class="active"';}?>>A-Z</a></p>
        </div>
		<?php
		endif;
		?>

		<form name="index_searchFrm" method="post" action="">	
        <ul>
        	<?php				
				#### Artist Login ####
				$sqlArtistFilter = "";
				if( $_SESSION['sess_userType'] == 'ARTIST' )
                {
                    $id_filter = $_SESSION['sess_userId'];
					$sqlArtistFilter = " AND id = $id_filter ";
                }
                else if( $_SESSION['sess_userType'] == 'LABEL' )
                {
                    $label_filter = $_SESSION['sess_userId'];
					$sqlArtistFilter = " AND label_id = $label_filter ";
                }
				#### End Artist login ######

				####### To handle the sorting functionality #####
				$orderBy = " ORDER BY id DESC";
				if($_GET['sort_by']=='name')
					$orderBy = " ORDER BY artist ASC";
				elseif($_GET['sort_by']=='ASC')
					$orderBy = " ORDER BY id ASC";
				elseif($_GET['sort_by']=='DESC')
					$orderBy = " ORDER BY id DESC";
				########### End sorting ##############

				$sql = "SELECT id,artist,logo FROM mydna_musicplayer WHERE 1=1 ".$sqlArtistFilter.$orderBy;
				$query_find_artist = mysql_query($sql) or die(mysql_error() . "sql=$sql");

				### Paging Goes here ####
				$record_per_page="10";
				$scroll_select="5";
				$total_records = mysql_numrows($query_find_artist);

				if(isset($_REQUEST['rec_per_page']) && $_REQUEST['rec_per_page']!='')				
					 $record_per_page=$_REQUEST['rec_per_page'];				
				else						
					$_REQUEST['rec_per_page']=$record_per_page;
					
				$page=new Page(); 	
				$page->set_qry_string('rec_per_page='.$record_per_page.'&sort_by='.$_REQUEST['sort_by']);
				
				### Running the Query with paging 
				$page->set_page_data($_SERVER['PHP_SELF'],$total_records,$record_per_page,$scroll_select,True,false,false);
				######### End Paging #########

				$query_page_artist = mysql_query($page->get_limit_query($sql));	
				while($find_record = mysql_fetch_array($query_page_artist))
				{
						if($find_record['logo'] == '')
							$artist_img_logo = 'images/NoPhoto.jpg';
						else
							$artist_img_logo = '../artists/files/'.$find_record['logo'];

					$img_url = "timthumb.php?src=".$artist_img_logo.'&amp;w=89&amp;h=40&amp;zc=1&amp;q=100';

					echo "<li>
							<figure><a href='artist_management.php?userId=".$find_record['id']." '><img src='".$img_url."' alt='".$find_record['artist']."' title='".$find_record['artist']."'/></a></figure>

							<span class='title'><a href='artist_management.php?userId=".$find_record['id']."'>".$find_record['artist']."</a></span>

							<span class='visit'><a title='Visit ".$find_record['artist']."' href='artist_management.php?userId=".$find_record['id']." '>Visit</a></span>
							<span class='deleteArtist'><a title='Delete ".$find_record['artist']."' href='#' onclick='if(confirm(\"Are you sure you want delete ".$find_record['artist']."?\"))location.href=\"dashboard.php?userId=$userId&action=1&artist_id=".$find_record['id']."\";'></a></span>
						</li>"; 					
				}			
			?>			
			<input type="hidden" name="rec_per_page" value="<?php echo $_REQUEST['rec_per_page'] ?>">
			<input type="hidden" name="sort_by" value="<?php echo $_REQUEST['sort_by'] ?>">
        </ul>
        </form>

		<?php
		if( $_SESSION['sess_userType'] != 'ARTIST' ):
		?>	
			<div class="pagination2">
			   <?php $page->get_page_nav();?>
			</div>
		<?php endif;?>
    </section>    
</section><!-- content -->
</section><!-- wrapper -->

<?php 	include_once('footer.php'); ?>
