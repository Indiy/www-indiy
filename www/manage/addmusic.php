<?php  require_once('../includes/config.php');
	if($_SESSION['sess_userId']=="")
	{
		header("location: index.php");
		exit();
	}
	include("../includes/functions.php");

	$database = "[p]musicplayer_audio";
    $_SESSION['tabOpen']='playlist';

	if ($_POST["WriteTags"] != "") {
		
		if ($_POST["id"] != "") {
			$row = mf(mq("select * from {$database} where id='{$_POST[id]}'"));
			
			$old_logo = $row["image"];
			$old_sound = $row["audio"];
            $old_product_id = $row["product_id"];
		}
		
		extract($_REQUEST);

		$audio_name = my($_POST["name"]);
		$audio_download = $_POST["download"];
		$audio_bgcolor = $_POST["bgcolor"];
		$audio_bgposition = $_POST["bgposition"];
		$audio_bgrepeat = $_POST["bgrepeat"];
		$audio_amazon = $_POST["amazon"];
		$audio_itunes = $_POST["itunes"];
        $mad_store = $_POST["mad_store"] == 'true';
        $remove_image = $_POST["remove_image"] == 'true';
        $remove_song = $_POST["remove_song"] == 'true';
		
		// Upload Image
		if(!empty($_FILES["logo"]["name"])){
			if (is_uploaded_file($_FILES["logo"]["tmp_name"])) {
				$audio_logo = $artistid."_".strtolower(rand(11111,99999)."_".basename(cleanup($_FILES["logo"]["name"])));
				@move_uploaded_file($_FILES['logo']['tmp_name'], '../artists/images/' . $audio_logo);
			} else {
				if ($old_logo != $audio_logo) {
					$audio_logo = $old_logo;
				}
			}
		}else{
			$audio_logo = $old_logo;
		}
		//echo "{'img':'<img src=artists/images/$audio_logo>'}";
		
		// Upload Audio
		if(!empty($_FILES["audio"]["name"]))
		{
			if (is_uploaded_file($_FILES["audio"]["tmp_name"])) {
				$ext = explode(".",$_FILES["audio"]["name"]);
				
				$filename = $artistid."_".strtolower(rand(11111,99999)."_song.");
				$audio_sound = $filename.$ext[count($ext)-1];
				$audio_sound_ogg = $filename."ogg";

				@move_uploaded_file($_FILES['audio']['tmp_name'], '../artists/audio/' . $audio_sound);
				@system('/usr/local/bin/ffmpeg -i /home/madcom/public_html/artists/audio/'.$audio_sound.'  -acodec libvorbis /home/madcom/public_html/artists/audio/'.$audio_sound_ogg);

				//$audio_sound = $audio_sound_ogg;
			} else {
				if ($old_sound != $audio_sound) {
					$audio_sound = $old_sound;
				}
			}
		}else{
				$audio_sound = $old_sound;
		}
        if( $mad_store )
        {
            if( isset($old_product_id) && $old_product_id > 0 )
            {
                $product_id = $old_product_id;
            }
            else
            {
                $columns = "artistid|name|description|image|price|sku";
                $values = "$artistid|$audio_name|Single|$audio_logo|0.99|MADSONG";
                insert('mydna_musicplayer_ecommerce_products',$columns,$values);
                $product_id = mysql_insert_id();
            }
        }
        else
        {
            if( isset($old_product_id) )
            {
                $sql = "DELETE FROM mydna_musicplayer_ecommerce_products WHERE id = '$old_product_id'";
                mq($sql);
            }
            $product_id = NULL;
        }
        if( $remove_song )
            $audio_sound = '';
        if( $remove_image )
            $audio_logo = '';
		
		//INSERTING THE DATA
		$tables = "artistid|name|image|bgcolor|bgposition|bgrepeat|audio|download|amazon|itunes|product_id";
		$values = "{$artistid}|{$audio_name}|{$audio_logo}|{$audio_bgcolor}|{$audio_bgposition}|{$audio_bgrepeat}|{$audio_sound}|{$audio_download}|{$audio_amazon}|{$audio_itunes}|{$product_id} ";
		
		if ($_POST["id"] != "") {
			update($database,$tables,$values,"id",$_POST["id"]);
		} else {
			insert($database,$tables,$values);
            $new_song_id = mysql_insert_id();
		}
		
		$successMessage = "<div id='notify'>Success! You are being redirected...</div>";
		
		//showing the post value after the upload //	
		$postedValues['imageSource'] = "../artists/images/".$audio_logo;
		$postedValues['audio_sound'] = "../artists/audio/".$audio_sound;
		$postedValues['success'] = "1";
		
		$postedValues['postedValues'] = $_REQUEST;

		//echo '{"Name":"'.$audio_name.'","imageSource":"artists/images/'.$audio_logo.'","":"","audio_sound":"artists/audio/'.$audio_sound.'","success":1}';
		echo json_encode($postedValues);
        
        require_once 'include/utils.php';
        @create_abbrevs();
        
        if( $new_song_id )
        {
            $song = mf(mq("SELECT * FROM mydna_musicplayer_audio WHERE id = '$new_song_id'"));
            $short_link = make_short_link($song['abbrev']);
            $update_text = "Check out my new song: $short_link";

            $artist = mf(mq("SELECT * FROM mydna_musicplayer WHERE id = '$artistid'"));
            if( $artist['fb_setting'] == 'AUTO' )
            {
                send_fb_update($artist,$update_text);
            }
            if( $artist['tw_setting'] == 'AUTO' )
            {
                send_tweet($artist,$update_text);
            }
        }
        
		exit;		

		refresh("1","?p=home");
	}
	
	if ($_GET["id"] != "") {
		$artistid=$_REQUEST['artist_id'];
		$row = mf(mq("select * from {$database} where id='{$_GET["id"]}' and artistid='{$artistid}'"));
		$audio_id = $row["id"];
		$audio_name = $row["name"];
		$audio_logo = $row["image"];
		$audio_bgcolor = $row["bgcolor"];
		$audio_bgrepeat = $row["bgrepeat"];
		$audio_bgposition = $row["bgposition"];
		$audio_sound = $row["audio"];
		$audio_download = $row["download"];
		$audio_amazon = $row["amazon"];
		$audio_itunes = $row["itunes"];
		$head_title = "Edit";
        $mad_store = $row["product_id"];
	}else{
		$head_title = "Add";
	}
	
    $image_html = '';
	if( $audio_logo != "" )
    {
		$image_html .= "<img src='../artists/images/$audio_logo' style='margin-top: 0px; height: 25px;' />";
        $image_html .= "<button onclick='return onImageRemove();'></button>";
	}
    $audio_html = '';
    if( $audio_sound != '' )
    {
        $audio_html .= $audio_sound;
        $audio_html .= "<button onclick='return onSongRemove();'></button>";
    }
	
	if ($audio_download == "1") { $yesDownload = " checked"; } else { $noDownload = " checked"; }
	$audio_name = stripslashes($audio_name);
	
?>
	
    <link rel="stylesheet" media="screen" type="text/css" href="includes/css/layout.css" />

<script type="text/javascript">

$(document).ready(setupQuestionTolltips);

var g_removeSong = false;
var g_removeImage = false;

function onSongRemove()
{
    var result = window.confirm("Remove song from page?");
    if( result )
    {
        g_removeSong = true;
        $('.filename').hide();
    }
    return false;
}

function onImageRemove()
{
    var result = window.confirm("Remove image from page?");
    if( result )
    {
        g_removeImage = true;
        $('.image_image').hide();
    }
    return false;
}
    
</script>

<div id="popup">
    <?=$successMessage;?>
    <div class='top_bar'>
        <h2><?=$head_title;?> Page</h2>
        <button onclick='$.facebox.close();'>CLOSE</button>
    </div>

    <div class='top_blue_bar'></div>
    <div class='top_sep'></div>

    <form id="ajax_from" method="post" enctype="multipart/form-data" action="addmusic.php" onsubmit='return onAddMusicSubmit();'>
        <input id='artist_id' type='hidden' value="<?=$_REQUEST['artist_id']?>" name="artistid">
        <input id='song_id' type='hidden' value="<?=$_REQUEST['id']?>" name="id">
        
        <div class='input_container'>
            <div class='line_label'>Name</div>
            <input id='song_name' type="text" name="name" value="<?=$audio_name;?>" class="line_text" />
        </div>
        <div class='input_container' style='height: 50px;'>
            <div class='left_label'>MP3 File <span id='tip_mp3' class='tooltip'>(?)</span></div>
            <div class='right_file_filename'>
                <input id='song_audio' type="file" name="audio" />
                <div class='filename'><?=$audio_html;?></div>
            </div>
        </div>
        <div class='input_container' style='height: 50px;'>
            <div class='left_image_label'>
                <div class='image_label'>Image <span id='tip_image' class='tooltip'>(?)</span></div>
                <div class='image_image'><?=$image_html;?></div>
            </div>
            <input id='song_image' type="file" name="logo" class='right_file' />
        </div>
        <div class='input_container'>
            <div class='left_label'>Background Color</div>
            <input id='song_bgcolor' type="text" name="bgcolor" maxlength="6" size="6" class='color' value="<?=$audio_bgcolor;?>" />
        </div>
        <div class='input_container'>
            <div class='left_label'>Background Position</div>
            <select id='song_bgposition' name="bgposition" class='right_drop'>
            <option value="">Select</option>
            <?
                $positions = array("top left","top center","top right","center left","center center","center right","bottom left","bottom center","bottom right");
                foreach ($positions as $position) {
                    if ($audio_bgposition == $position) {
                        $selected = " selected";
                    } else {
                        $selected = "";
                    }
                    echo "<option value='{$position}'{$selected}>".ucfirst($position)."</option>\n";
                }
            ?>
            </select>
        </div>
        <div class='input_container'>
            <div class='left_label'>Background Repeat</div>
            <select id='song_bgrepeat' name="bgrepeat" class='right_drop'>
            <option value="">Select </option>
            <option value=""></option>
            <?
                $colors = array("repeat","no-repeat","stretch");
                foreach ($colors as $color) {
                    if ($audio_bgrepeat == $color) {
                        $selected = " selected";
                    } else {
                        $selected = "";
                    }
                    echo "<option value='{$color}'{$selected}>".ucfirst($color)."</option>\n";
                }
            ?>
            </select>
        </div>
        <div class='input_container'>
            <div class='left_label'>Free Download</div>
            <div class='right_box'>
                <input type="radio" name="download" value="1" class="radio"<?=$yesDownload;?> /> Yes
                <input type="radio" name="download" value="0" class="radio"<?=$noDownload;?> /> No
            </div>
        </div>
        <div class='input_container'>
            <div class='line_label'>Amazon MP3 URL</div>
            <input id='amazon_url' type="text" name="amazon" value="<?=$audio_amazon;?>" class='line_text' />
        </div>
        <div class='input_container'>
            <div class='line_label'>iTunes URL</div>
            <input id='itunes_url' type="text" name="itunes" value="<?=$audio_itunes;?>" class='line_text' />
        </div>
        <div class='input_container'>
            <div class='left_label'>MyArtistDNA Store <span id='tip_store' class='tooltip'>(?)</span></div>
            <input id='mad_store' type="checkbox" name="mad_store" <? if($mad_store) echo 'checked'; ?> class='right_box'/>
        </div>
        <div class='submit_branding_container'>
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
