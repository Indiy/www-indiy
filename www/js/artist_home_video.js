
function showVideo(n)
{
    var video = g_videoList[n];
    var w = $(window).width();
    var h = $(window).height()-50;
    $("#player_hldr").css('width',w);
    $("#player_hldr").css('height',h);
    $("#player_hldr").show();
    $("#close_btn").show();
    $("#player_bg").fadeIn(300);

    //$(".close_button").fadeIn(300);
    //$(".player_holder").fadeIn(300);


    var video_file = video.video_file;
    var poster = video.image_file;

    var html = '';
    
    html += '<div class="video-js-box vim-css" style="width:100%; height: 100%;">';
    html += '<video id="mad_video_1" class="video-js" width="100%" height="100%" controls="controls" preload="auto" poster="' + poster + '">';
    html += '<source src="' + video_file + '" type="video/mp4" />';
    html += '<object id="flash_fallback_1" class="vjs-flash-fallback" width="640" height="264" type="application/x-shockwave-flash" data="http://releases.flowplayer.org/swf/flowplayer-3.2.1.swf">';
    
    html += '<param name="movie" value="http://releases.flowplayer.org/swf/flowplayer-3.2.1.swf" />';    
    html += '<param name="allowfullscreen" value="true" />';
    html += '<param name="flashvars" value=\'config={"playlist":["/images/mad_poster.png", {"url": "' + video_file + '","autoPlay":false,"autoBuffering":true}]}\' />';
    html += '<img src="/images/mad_poster.png" width="853" height="480" alt="Poster Image" title="No video playback capabilities." />';
    html += '</object>';
    html += '</video>';
    html += '<p class="vjs-no-video">';
    html += '</p>';
    html += '</div>';

    $('#player_hldr').html(html);
    window.setTimeout(setupVideoJS,10);
}

var g_videoPlayer = false;

function setupVideoJS()
{
    g_videoPlayer = VideoJS.setup("mad_video_1",{
                                  controlsBelow: false,
                                  controlsHiding: true,
                                  defaultVolume: 0.85,
                                  flashVersion: 9,
                                  linksHiding: true
                                  });
}
