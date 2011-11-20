<?php
// Sets the proper content type for javascript
header("Content-type: application/javascript");
include('../includes/config.php');
include('../includes/functions.php');
//require_once ('fb_redirect.php');
//require_once ('fb_helper.php');
$jsStr = '';
$user_id = $_GET['id'];

$xml = '';
$xml .= '<?xml version="1.0" encoding="UTF-8"?>';
$loadPlayList = mq("select * from mydna_musicplayer_video where artistid='{$user_id}' ORDER BY id DESC");


$getArtistImage = mf(mq("SELECT facebook,username, profile_image_url, oauth_uid, logo,oauth_provider FROM mydna_musicplayer WHERE id='{$user_id}'"));
$logo_url = '/timthumb.php?w=90&amp;h=60&amp;src=/artists/images/' . $getArtistImage['logo'];
$xml .= '<VIDLIBDATA avatar_img="' . $logo_url . '" playlist_id="0" vidnum_selected="2">';

$cnt = 0;	
while ($list = mf($loadPlayList)) 
{
	$video_arr = explode(".",$list['video']);
	$songArr = explode('.',$list['video']);

	if( $list['name'] == '' )
		$name_data = "Noname";
	else
		$name_data = $list['name'];

    $xml .= '<VIDEO><TITLE>'.$name_data.'</TITLE><DESCRIPTION></DESCRIPTION><PATH>vid/'.$list['video'].'</PATH><EMBED>Re-Nutriv EMBED CODE</EMBED></VIDEO>';

    $cnt++;
}

$xml .= '</VIDLIBDATA>';

$xml = str_replace("'","\'",$xml);
$xml = str_replace("\\\\'","\'",$xml);


// run query and get play list
//$jsStr .= '<script>';
$jsStr .= 'var selected_playlist = "";'."\n\n";
$jsStr .= 'var tmp_playlist_xml = null;'."\n\n";

$jsStr .= 'function pullPlaylist(n){'."\n"; 

$jsStr .= "var playlist0 = '".$xml."';\n\n";
//$jsStr .= "var playlist1 = '".$xml."';\n\n";
//$jsStr .= "var playlist2 = '".$xml."';\n\n";

$jsStr .= "var playlist = '';"."\n";
$jsStr .= 'if(n === 0){'."\n";
//$jsStr .= 'alert(n);';
$jsStr .= 'playlist = playlist0;'."\n";
$jsStr .= '}'."\n";
$jsStr .= 'if(n === 1){'."\n";
$jsStr .= 'playlist = playlist1;'."\n";
$jsStr .= '}'."\n";
$jsStr .= 'if(n === 2){'."\n";
$jsStr .= 'playlist = playlist2;'."\n";
$jsStr .= '}'."\n";
$jsStr .= 'if(n === 3){'."\n";
$jsStr .= 'playlist = playlist3;'."\n";
$jsStr .= '}'."\n";
$jsStr .= 'return playlist;'."\n";
$jsStr .= '}'."\n";

echo $jsStr;
?>

function echoData(){
		return selected_playlist;
}
//
//#################### jQuery onPageLoad Handler ####################
$(function(){
	$("#vid_lib li").each(function(index) {
		assignBoxMousing($(this));
	});
	//
	$("#close_btn").bind('click', function(){
		pullVideoDisplay();
	});
	//
	$(window).resize(function(){
		resizePlayerContainer();
	});
	//
	$("#player_hldr").hide();
	$("#close_btn").hide();
});
//
function assignBoxMousing(obj){
	$(obj).bind('click', function(){
        var vid_num = $(obj).attr('id').substring(6);
		var list_num = $(obj).parent().attr('id').substring(9);
		//
		tmp_playlist_xml = null;
		selected_playlist = "";
		//
		tmp_playlist_xml = $.parseXML(pullPlaylist(list_num*1));
		$(tmp_playlist_xml).find('VIDLIBDATA').attr('vidnum_selected', vid_num);
		//selected_playlist = new XMLSerializer().serializeToString(tmp_playlist_xml);
		selected_playlist = getXMLNodeSerialisation(tmp_playlist_xml);
		//
		loadPlayerPage();
	});
}
//
function getXMLNodeSerialisation(xmlNode) {
     var text = false;
     try {
         // Gecko-based browsers, Safari, Opera.
         var serializer = new XMLSerializer();
         text = serializer.serializeToString(xmlNode);
     }
     catch (e) {
		try {
			// Internet Explorer.
			text = xmlNode.xml;
		}
		catch(e){
			//
		}
	}
	return text;
}
//
function resizePlayerContainer(){
	var w = $(window).width();
	var h = $(window).height()-50;
	 $("#player_hldr").css('width',w);
	$("#player_hldr").css('height',h);
}
//
function loadPlayerPage(){
	resizePlayerContainer();
	$("#player_hldr").show();
	$("#close_btn").show();
	$('#player_hldr').load('player.html', writeMediaTag);
}
function writeMediaTag(){
	//
	$("#app_cntrl").hide();
	//
	var vid_elem = $(tmp_playlist_xml).find('VIDLIBDATA').attr('vidnum_selected');
	var path_node = $(tmp_playlist_xml).find('VIDLIBDATA').find('VIDEO PATH')[vid_elem];
	//var path_str = $(path_node).contents().empty().end().text()+'.mp4';
    var path_str = $(path_node).contents().empty().end().text();
	var title_node = $(tmp_playlist_xml).find('VIDLIBDATA').find('VIDEO TITLE')[vid_elem];
	var title_str = $(title_node).contents().empty().end().text();
	var desc_node = $(tmp_playlist_xml).find('VIDLIBDATA').find('VIDEO DESCRIPTION')[vid_elem];
	var desc_str = $(desc_node).contents().empty().end().text();
	//
	var avatar_image = $(tmp_playlist_xml).find('VIDLIBDATA').attr('avatar_img');
	//
	var playerVersion = swfobject.getFlashPlayerVersion(); 
	var majorVersion = playerVersion.major;
	var minorVersion = playerVersion.minor;
	//
	if(majorVersion < 10){
		//
		// WRITE OUT HTML5 VIDEO TAG
		$("#vid_player").html('<video id="html5vid" class="html5_video" src="'+path_str+'" type="video/mp4" controls autoplay></video><div id="html5vid_ovrly" class="html5video_overlay"></div><div id="html5vid_ttl" class="html5video_title">'+title_str+'</div><div id="html5vid_topkey" class="html5video_topkey"></div><div id="html5vid_desc" class="html5video_desc">'+desc_str+'</div><div id="html5vid_avatar_img" class="html5video_avatar_image"><img src="'+avatar_image+'" /></div>');
		//
		$("#html5vid").bind("ended", function(){
			pullVideoDisplay();
		});
	}else{
		//
		// WRITE OUT FLASH VIDEO PLAYER
		loadPlayerFlashApplication();
	}
}
function loadPlayerFlashApplication(){
var flashvars = {

};
	var params = {allowscriptaccess:"always", allowFullScreen:true};
	var attributes = {};
	swfobject.embedSWF("swf/integrated_player.swf", "vid_player", "100%", "100%", "10.1", "swf/expressInstall.swf", flashvars, params, attributes);

}
function pullVideoDisplay(){
	swfobject.removeSWF("vid_player");
	$("#vid_player").html('');
	$('#player_hldr').hide();
	$("#app_cntrl").show();
	$("#close_btn").hide();
}