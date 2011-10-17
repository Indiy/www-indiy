<?php require_once('../includes/config.php');
include_once('../includes/functions.php');	
include_once('../includes/functions.php');	
?>
<?php
$videoID = $_REQUEST['videoID']; 

$find_artistVideo = "SELECT * FROM mydna_musicplayer_video  WHERE id='".$videoID."' ";
$result_artistVideo = mysql_query($find_artistVideo) or die(mysql_error());
$record_artistVideo = mysql_fetch_array($result_artistVideo);

$image = $record_artistVideo['image'];
$video = $record_artistVideo['video'];
?>
<script type="text/javascript" src="jw_mediaplayer/swfobject.js"></script>
<script type="text/javascript" src="jw_mediaplayer/jwplayer.js"></script>
<!-- START OF THE PLAYER EMBEDDING TO COPY-PASTE -->
<div id="mediaplayer">JW Player goes here</div>

<script type="text/javascript">
jwplayer("mediaplayer").setup({
	flashplayer: "jw_mediaplayer/player.swf",
	file: "../vid/<?=$video?>",
	image: "../artists/images/<?=$image?>"
});
</script>
<!-- END OF THE PLAYER EMBEDDING -->