<?php 

    require_once '../includes/config.php';
	require_once '../includes/functions.php';
	if( $_SESSION['sess_userId'] == "" )
	{
		header("Location: /index.php");
		exit();
	}
	$database = "[p]musicplayer_video";
    $_SESSION['tabOpen']='videolist';
	if ($_POST["name"] != "") 
    {
		if ($_POST["id"] != "") 
        {
			$row = mf(mq("select `id`,`image`,`video` from `{$database}` where `id`='{$_POST["id"]}'"));
			$old_logo = $row["image"];
			$old_sound = $row["video"];
		}
		
		$video_name = my($_POST["name"]);
		
		// Upload Image
		if(!empty($_FILES["logo"]["name"]))
        {
			if (is_uploaded_file($_FILES["logo"]["tmp_name"]))
            {
				$video_logo = $artistid."_".strtolower(rand(11111,99999)."_".basename(cleanup($_FILES["logo"]["name"])));
				@move_uploaded_file($_FILES['logo']['tmp_name'], '../artists/images/' . $video_logo);
			} 
            else 
            {
				if ($old_logo != "") 
                {
					$video_logo = $old_logo;
				}
			}
		}
        else
        {
            $video_logo = $old_logo;
		}
		
		// Upload video
		if(!empty($_FILES["video"]["name"]))
        {
			if (is_uploaded_file($_FILES["video"]["tmp_name"]))
            {
                $tmp_file = $_FILES['video']['tmp_name'];
				$ext = explode(".",$tmp_file);
                $upload_ext = strtolower($ext[count($ext)-1]);

				$video_sound_mp4 = $artistid . '_' . strtolower( rand(11111,99999) . '_video.mp4' );
				$dest_file = '../vid/' . $video_sound_mp4;

                $args = "-i_qfactor 0.71 -qcomp 0.6 -qmin 10 -qmax 63 -qdiff 4 -trellis 0 -vcodec libx264 -s 640x360 -vb 300k -ab 64k -ar 44100 -threads 4";
				if( $upload_ext == "mp4" )
                {
					@system("/usr/local/bin/ffmpeg -i $tmp_file $args $dest_file");
                }
				else if( $upload_ext == "mov" )
                {
					@system("/usr/local/bin/ffmpeg -i $tmp_file $args $dest_file");
				}
                else
                {
					@system("/usr/local/bin/ffmpeg -i $tmp_file $args $dest_file");
                }
                @unlink($_FILES['video']['tmp_name']);
                @system("/usr/bin/qafaststart $dest_file");
				$video_sound = $video_sound_mp4;
			} 
            else 
            {
				if ($old_sound != "") 
                {
					$video_sound = $old_sound;
				}
			}
		}
        else
        {
            $video_sound = $old_sound;
		}
		
		$tables = "artistid|name|image|video";
		$values = "{$artistid}|{$video_name}|{$video_logo}|{$video_sound}";
		
		if ($_POST["id"] != "") 
        {
			update($database,$tables,$values,"id",$_POST["id"]);
		} 
        else 
        {
			insert($database,$tables,$values);
		}

		$successMessage = "<div id='notify'>Success! You are being redirected...</div>";

		$postedValues['imageSource'] = "../artists/images/".$video_logo;
		$postedValues['video_sound'] = "../artists/video/".$video_sound;
		$postedValues['success'] = "1";

		$postedValues['postedValues'] = $_REQUEST;

		echo json_encode($postedValues);
		exit();
	}
	
	if ($_GET["id"] != "") 
    {
		$artistid=$_REQUEST['artist_id'];

		$row = mf(mq("select * from `{$database}` where `id`='{$_GET["id"]}' and `artistid`='{$artistid}'"));
		$video_id = $row["id"];
		$video_name = $row["name"];
		$video_logo = $row["image"];
		$video_sound = $row["video"];

		$head_title	=	"Edit";
	}
    else
    {
		$head_title	=	"Add";
	}
	
	if ($video_logo != "")
    {
		$video_logo = '<img src="../artists/images/'.$video_logo.'" style="height: 25px;" />';
	}
	
	if ($video_download == "1") 
    { 
        $yesDownload = " checked"; 
    } 
    else 
    { 
        $noDownload = " checked"; 
    }
	$video_name = stripslashes($video_name);
    
    $image_html = '';
	if( $video_logo != "" )
    {
		$image_html .= "<img src='../artists/images/$video_logo' style='height: 25px;' />";
        $image_html .= "<button onclick='return onVideoImageRemove();'></button>";
	}
    $video_html = '';
    if( $video_sound != '' )
    {
        $video_html .= $video_sound;
        $video_html .= "<button onclick='return onVideoRemove();'></button>";
    }

?>

<div id="popup">
    <?=$successMessage;?>
    <div class='top_bar'>
        <h2><?=$head_title?> Video</h2>
        <button onclick='$.facebox.close();'>CLOSE</button>
    </div>

    <div class='top_blue_bar'></div>
    <div class='top_sep'></div>
    <form id="ajax_from" method="post" enctype="multipart/form-data" action="addvideo.php" onsubmit='return onAddVideoSubmit();'>
        <input id='artist_id' type='hidden' value="<?=$_REQUEST['artist_id']?>" name="artistid"/>
        <input id='song_id' type='hidden' value="<?=$_REQUEST['id']?>" name="id"/>

        <div class='input_container'>
            <div class='left_label'>Name</div>
            <input id='video_name' type="text" name="name" value="<?=$video_name;?>" class='right_text' />
        </div>
        <div class='input_container' style='height: 50px;'>
            <div class='left_image_label'>
                <div class='image_label'>Image></div>
                <div class='image_image'><?=$image_html;?></div>
            </div>
            <input id='video_image_file' type="file" name="logo" class='right_file' />
        </div>
        <div class='input_container' style='height: 50px;'>
            <div class='left_label'>Video (flv or mp4)</div>
            <div class='right_file_filename'>
                <input id='video_file' type="file" name="audio" />
                <div class='filename'><?=$video_html;?></div>
            </div>
        </div>
        <div class='submit_branding_container' style="padding-top: 25px;">
            <input type="submit" name="WriteTags" value="submit" class='left_submit' />
            <div class='branding_tip'>Branding Tip: Lorem ipsum dolor sit amet, consectetur adipisicing elit.</div>
        </div>
    </form>
    <div id='status' class='form_status' style='display: none;'></div>
    <div id='upload_bar' style='display: none;'></div>
    <div id='spinner' style='display: none;'>
        <img src='/images/ajax-loader-white.gif'/>
    </div>

    <div class='bottom_sep'></div>
    <div class='bottom_blue_bar'></div>
</div>
