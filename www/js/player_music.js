
var MUSIC_IMAGE_PRELOAD_TIMEOUT = 3000;

var g_musicIsPlaying = false;

$(document).ready(musicOnReady);

function musicOnReady()
{

    $('#jquery_jplayer').jPlayer({
        ready: jplayerReady,
        solution: "html, flash",
        supplied: "mp3, oga",
        swfPath: "/js/Jplayer.swf",
        verticalVolume: true,
        wmode: "window"
    })
    .bind($.jPlayer.event.ended,jplayerEnded)
    .bind($.jPlayer.event.timeupdate,jplayerTimeUpdate)
    .bind($.jPlayer.event.play,jplayerPlay)
    .bind($.jPlayer.event.pause,jplayerPause)
    .bind($.jPlayer.event.volumechange,jplayerVolume);
     
    window.setTimeout(musicPreloadImages,MUSIC_IMAGE_PRELOAD_TIMEOUT);
    $(window).resize(musicResizeBackgrounds);
}

function musicHide()
{
    $('#jquery_jplayer').jPlayer("stop");
}

function jplayerTimeUpdate(event)
{
    //var percent = event.jPlayer.status.currentPercentAbsolute;
    var total_time = event.jPlayer.status.duration;
    var curr_time = event.jPlayer.status.currentTime;
    
    playerProgress(curr_time,total_time);
}
var g_musicIsPlaying = false;
function jplayerPlay()
{
    g_musicIsPlaying = true;
    playerSetPlaying();
}
function jplayerPause()
{
    g_musicIsPlaying = false;    
    playerSetPaused();
}
function jplayerEnded()
{
    g_musicIsPlaying = false;
    playerSetPaused();
    musicNext();
}
function jplayerVolume(event)
{
    var vol = event.jPlayer.options.volume;
    //$('#player .volume .current').css('height',vol * 100 + "%");
}

function musicPlayPause()
{
    if( g_musicIsPlaying )
        $('#jquery_jplayer').jPlayer("pause");
    else
        $('#jquery_jplayer').jPlayer("play");
}

function jplayerReady() 
{
    musicChange(0);
}

var g_songsPlayed = 0;
var g_currentSongId = 0;
var g_currentSongIndex = 0;

function musicChange( index ) 
{
    setPlayerMode("music");

    g_songsPlayed++;
    if( g_songsPlayed == 3 )
        maybeAskForEmail();
    
    g_currentSongIndex = index;
    var song = g_musicList[index];
    //$("#playlist .song_list_item").removeClass("current");
    //$("#playlist #song_list_item_" + song.id).addClass("current");
    
    var media = {
        mp3: song.mp3,
        oga: song.mp3.replace(".mp3",".ogg")
    };
    if( song.mp3.endsWith("mp3") )
    {
        $('#jquery_jplayer').jPlayer("setMedia", media);
        $('#jquery_jplayer').jPlayer("play");
    }
    else
    {
        $('#jquery_jplayer').jPlayer("stop");
    }
    
    g_currentSongId = song.id;
    window.location.hash = '#song_id=' + g_currentSongId; 
    
    playerTrackInfo(song.name,song.listens);
    
    if( musicUpdateListens(song.id,index) )
    {
        g_totalPageViews++;
        playerUpdateTotalViewCount();
    }
}

function musicNext()
{
    var index = g_currentSongIndex + 1;
    if( index == g_musicList.length )
        index = 0;
    
    musicChange(index);
}


var g_musicListenUpdated = {};
function musicUpdateListens(song_id,index)
{
    if( song_id in g_musicListenUpdated )
        return false;

    g_musicListenUpdated[song_id] = true;

    var url = "/data/element_views.php?song_id=" + song_id;
    jQuery.ajax(
    {
        type: 'POST',
        url: url,
        dataType: 'json',
        success: function(data) 
        {
            g_totalPageViews = data['total_views'];
            var track_listens = data['element_views'];
            g_musicList[index].listens = track_listens;
            playerUpdateTotalViewCount();
            playerTrackInfo(false,track_listens);
        },
        error: function()
        {
            //alert('Failed to get listens!');
        }
    });
    return true;
}

function musicPreloadImages()
{
    for( var k in g_songPlayList )
    {
        var song = g_musicList[k];
        musicLoadImage(song,k);
    }
}

function musicLoadImage(song,index)
{
    var image = song.image;
    var color = song.bgcolor;
    var bg_style = song.bg_style;
    
    if( !song.loaded )
    {
        song.loaded = true;
        var holder = $('#image_holder_' + index);
        
        holder.css("background-color", "#" + color);
        if( bg_style == 'STRETCH' )
        {
            var img_url = "/timthumb.php?src=" + image + "&w=" + getWindowWidth() + "&zc=0&q=100";
            var html = "<div style='height: " + getWindowHeight() + "px;'>";
            html += "<img src='" + img_url + "' />";
            html += "</div>"
            holder.html(html);
            holder.css("background-image","none");
            holder.css("background-repeat","no-repeat");
            holder.css("background-position","center center");
        }
        else if( bg_style == 'CENTER' )
        {
            holder.css("background-image","url(" + image + ")");
            holder.css("background-repeat","no-repeat");
            holder.css("background-position","center center");
            var html = "<div style='width: 100%; height: " + getWindowHeight() + "px;'></div>";
            holder.html(html);
        }
        else if( bg_style == 'TILE' )
        {
            holder.css("background-image","url(" + image + ")");
            holder.css("background-repeat","repeat");
            holder.css("background-position","center center");
            var html = "<div style='width: 100%; height: " + getWindowHeight() + "px;'></div>";
            holder.html(html);
        }
    }            
    musicResizeBackgrounds();
}
function musicResizeBackgrounds()
{
    for( var i = 0 ; i < g_musicList.length ; ++i )
    {
        var song = g_musicList[i];
        var bg_style = song.bg_style;
        if( bg_style == 'STRETCH' )
        {
            var win_height = getWindowHeight();
            var win_width = getWindowWidth();
            var win_ratio = win_width / win_height;
            
            var holder = $('#image_holder_' + i + ' div');
            var image = $('#image_holder_' + i + ' div img');
            var img_width = song.image_data.width;
            var img_height = song.image_data.height;
            if( img_height > 0 && img_width > 0 )
            {
                var img_ratio = img_width/img_height;
                
                holder.height(win_height);
                holder.width(win_width);
                if( win_ratio < img_ratio )
                {
                    var height = win_height;
                    var width = height * img_ratio;
                    image.width(width);
                    image.height(height);
                    holder.scrollLeft((width - win_width)/2);
                }
                else
                {
                    var width = win_width;
                    var height = width / img_ratio;
                    image.width(width);
                    image.height(height);
                    holder.scrollTop((height - win_height)/2);
                }
            }
        }
    }
}



