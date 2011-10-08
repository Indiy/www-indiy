var selected_playlist = "";
var tmp_playlist_xml = null;
//
function pullPlaylist(n){
	//
	var playlist0 = '<?xml version="1.0" encoding="UTF-8"?><VIDLIBDATA avatar_img="img/artistavatar_sample.png" playlist_id="0" vidnum_selected="2"><VIDEO><TITLE>Cheezi Puffs</TITLE><DESCRIPTION>some fun video full of cheezi Puff graphics</DESCRIPTION><PATH>vid/112_46051_iced-earth---alive-in-athens---travel-in-stygian----youtube</PATH><EMBED>Cheezi Puffs Video EMBED CODE</EMBED></VIDEO><VIDEO><TITLE>Estée Lauder Behind the Scenes</TITLE><DESCRIPTION>Take a tour backstage at Fashion Week 2010 with Estée Lauder and Tom Pechuex</DESCRIPTION><PATH>vid/el_behind_the_scenes</PATH><EMBED>Estée Lauder Behind the Scenes EMBED CODE</EMBED></VIDEO><VIDEO><TITLE>Q-Lab Desktop Software Tutorial</TITLE><DESCRIPTION>Some hands on training with Q-Lab – Performance Automation Software</DESCRIPTION><PATH>vid/qlab_desktop</PATH><EMBED>Q-Lab Desktop Software Tutorial EMBED CODE</EMBED></VIDEO><VIDEO><TITLE>Re-Nutriv</TITLE><DESCRIPTION>Aerin Lauder shares her thoughts on women’s beauty and Re-Nutirv</DESCRIPTION><PATH>vid/renutriv_aerin_lauder</PATH><EMBED>Re-Nutriv EMBED CODE</EMBED></VIDEO></VIDLIBDATA>';
	//
	var playlist1 = '<?xml version="1.0" encoding="UTF-8"?><VIDLIBDATA avatar_img="img/artistavatar_sample.png" playlist_id="0" vidnum_selected="2"><VIDEO><TITLE>Cheezi Puffs</TITLE><DESCRIPTION>some fun video full of cheezi Puff graphics</DESCRIPTION><PATH>vid/cheezipuffs</PATH><EMBED>Cheezi Puffs Video EMBED CODE</EMBED></VIDEO><VIDEO><TITLE>Estée Lauder Behind the Scenes</TITLE><DESCRIPTION>Take a tour backstage at Fashion Week 2010 with Estée Lauder and Tom Pechuex</DESCRIPTION><PATH>vid/el_behind_the_scenes</PATH><EMBED>Estée Lauder Behind the Scenes EMBED CODE</EMBED></VIDEO><VIDEO><TITLE>Q-Lab Desktop Software Tutorial</TITLE><DESCRIPTION>Some hands on training with Q-Lab – Performance Automation Software</DESCRIPTION><PATH>vid/qlab_desktop</PATH><EMBED>Q-Lab Desktop Software Tutorial EMBED CODE</EMBED></VIDEO><VIDEO><TITLE>Re-Nutriv</TITLE><DESCRIPTION>Aerin Lauder shares her thoughts on women’s beauty and Re-Nutirv</DESCRIPTION><PATH>vid/renutriv_aerin_lauder</PATH><EMBED>Re-Nutriv EMBED CODE</EMBED></VIDEO></VIDLIBDATA>';
	//
	var playlist2 = '<?xml version="1.0" encoding="UTF-8"?><VIDLIBDATA avatar_img="img/artistavatar_sample.png" playlist_id="0" vidnum_selected="2"><VIDEO><TITLE>Cheezi Puffs</TITLE><DESCRIPTION>some fun video full of cheezi Puff graphics</DESCRIPTION><PATH>vid/cheezipuffs</PATH><EMBED>Cheezi Puffs Video EMBED CODE</EMBED></VIDEO><VIDEO><TITLE>Estée Lauder Behind the Scenes</TITLE><DESCRIPTION>Take a tour backstage at Fashion Week 2010 with Estée Lauder and Tom Pechuex</DESCRIPTION><PATH>vid/el_behind_the_scenes</PATH><EMBED>Estée Lauder Behind the Scenes EMBED CODE</EMBED></VIDEO><VIDEO><TITLE>Q-Lab Desktop Software Tutorial</TITLE><DESCRIPTION>Some hands on training with Q-Lab – Performance Automation Software</DESCRIPTION><PATH>vid/qlab_desktop</PATH><EMBED>Q-Lab Desktop Software Tutorial EMBED CODE</EMBED></VIDEO><VIDEO><TITLE>Re-Nutriv</TITLE><DESCRIPTION>Aerin Lauder shares her thoughts on women’s beauty and Re-Nutirv</DESCRIPTION><PATH>vid/renutriv_aerin_lauder</PATH><EMBED>Re-Nutriv EMBED CODE</EMBED></VIDEO></VIDLIBDATA>';
	//
	var playlist = '';
	//
	if(n === 0){
		playlist = playlist0;
	}
	if(n === 1){
		playlist = playlist1;
	}
	if(n === 2){
		playlist = playlist2;
	}
	return playlist;
}
//
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
		var list_num = $(obj).parent().attr('id').substring(9);
		var vid_num = $(obj).attr('id').substring(6);
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
	var h = $(window).height()-150;
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
	var path_str = $(path_node).contents().empty().end().text()+'.mp4';
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
	var flashvars = {resource_path:"./", video_prefix:"../"};
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