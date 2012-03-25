

var g_videoMaxRows = 0;

$(document).ready(setupVideoPlayer);

function setupVideoPlayer()
{
    // Calculate the max number of video overlay rows
    $(".videos .row-button").each( function() {
                                  ++g_videoMaxRows;
                                  });
    
    // JS code for the pagination buttons
    $(".videos .row-button").click( function() {
                                   var row_num = $(this).children('span').html()
                                   showVideoRow(row_num);
                                   });
    
    // JS code for the arrows of pagination
    $(".videos .nav-arrows div").click( function() {
                                       var new_page = $(this).children('span').html();
                                       if( new_page ) {
                                       showVideoRow(new_page);
                                       }
                                       });
    
    // Function that switches video overlay pages
    showVideoRow("1");
}

function showVideoRow(page) 
{
    $(".videos .row-button").css("background-position", "center top");
    $(".videos .row-button-" + page).css("background-position", "center bottom");
    $(".videos .video-row").hide();
    $(".videos .video-row-" + page).show();
    $(".videos .nav-arrows div").addClass('active');
    $(".videos .left-arrow").children('span').html( page*1-1 );
    $(".videos .right-arrow").children('span').html( page*1+1 );
    if( page == 1 ) {
        $(".videos .left-arrow").removeClass('active');
        $(".videos .left-arrow").children('span').html('');
    }
    if( page == g_videoMaxRows ) {
        $(".videos .right-arrow").removeClass('active');
        $(".videos .right-arrow").children('span').html('');
    }
}


function showVideo(n)
{
    var video = g_videoList[n];

    var hash = '#song_id={0}&video_id={1}'.format(g_currentSongId,video.id);
    window.location.hash = hash;

    $('#jquery_jplayer').jPlayer("pause");
    $('#video_player').show();

    var h = $('#video_player').height();
    $('#player_body').css('height',h-60);

    var video_file = video.video_file;
    var video_file_ogv = video_file.replace(".mp4",".ogv");
    var poster = video.image_file;

    $('#video_title').text(video.name);

    var width = getWindowWidth();
    var height = getWindowHeight() - 60;

    var html = '';
    html += '<div class="video-js-box mad_video_css" style="width:100%; height: 100%;">';
    html += '<video id="mad_video_1" class="video-js" controls="controls" preload="auto" poster="' + poster + '">';
    html += '<source src="' + video_file + '" type="video/mp4" />';
    html += '<source src="' + video_file_ogv + '" type="video/ogg" />';
    html += '<object id="flash_fallback_1" class="vjs-flash-fallback" width="' + width + '" height="' + height + '" type="application/x-shockwave-flash" data="http://releases.flowplayer.org/swf/flowplayer-3.2.1.swf">';
    
    html += '<param name="movie" value="http://releases.flowplayer.org/swf/flowplayer-3.2.1.swf" />';    
    html += '<param name="allowfullscreen" value="true" />';
    html += '<param name="flashvars" value=\'config={"playlist":["/images/mad_poster.png", {"url": "' + video_file + '","autoPlay":false,"autoBuffering":true}]}\' />';
    html += '<img src="/images/mad_poster.png" title="No video playback capabilities." />';
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
    $('#video_player').fadeOut(300);
    var hash = '#song_id={0}'.format(g_currentSongId);
    window.location.hash = hash;
    try 
    {
        if( g_videoPlayer )
            g_videoPlayer.pause();
    }
    catch(e)
    {}
}
