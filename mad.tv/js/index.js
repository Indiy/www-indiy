

var g_videoList = [ {
   video_file: 'http://www.madd3v.com/__MASTER.mp4',
   poster: 'http://www.madd3v.com/images/mad_poster.png',
   name: 'Test Video',
   artist: 'MyArtistDNA',
   logo: 'http://www.madd3v.com/artists/images/199_52582_madbold.png'
}];

$(document).ready(setupVideoPlayer)

function setupVideoPlayer()
{
    showVideo(0);
}

function showVideo(n)
{
    $('#video_player').show();

    var h = $('#video_player').height();
    $('#player_body').css('height',h-60);

    var video = g_videoList[n];
    var video_file = video.video_file;
    var video_file_ogv = video_file.replace(".mp4",".ogv");
    var poster = video.poster;

    $('#video_title').text(video.name);
    $('#artist_name').text(video.artist);
    $('#logo_img').attr('src',video.logo);

    var html = '';
    html += '<div class="video-js-box mad_video_css" style="width:100%; height: 100%;">';
    html += '<video id="mad_video_1" class="video-js" width="100%" height="100%" controls="controls" preload="auto" poster="' + poster + '">';
    html += '<source src="' + video_file + '" type="video/mp4" />';
    html += '<source src="' + video_file_ogv + '" type="video/ogg" />';
    html += '<object id="flash_fallback_1" class="vjs-flash-fallback" width="640" height="264" type="application/x-shockwave-flash" data="http://releases.flowplayer.org/swf/flowplayer-3.2.1.swf">';
    
    html += '<param name="movie" value="http://releases.flowplayer.org/swf/flowplayer-3.2.1.swf" />';    
    html += '<param name="allowfullscreen" value="true" />';
    html += '<param name="flashvars" value=\'config={"playlist":["' + poster +'", {"url": "' + video_file + '","autoPlay":false,"autoBuffering":true}]}\' />';
    html += '<img src="' + poster + '" width="853" height="480" alt="Poster Image" title="No video playback capabilities." />';
    html += '</object>';
    html += '</video>';
    html += '<p class="vjs-no-video">';
    html += '</p>';
    html += '</div>';

    $('#player_body').html(html);
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
function closeVideo()
{
    if( g_videoPlayer )
        g_videoPlayer.pause();
    $('#video_player').fadeOut(300);
}
