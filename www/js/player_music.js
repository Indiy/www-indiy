
var MUSIC_IMAGE_PRELOAD_TIMEOUT = 3000;

var g_musicIsPlaying = false;

$(document).ready(musicOnReady);

function musicOnReady()
{
    if( g_musicList.length == 0 )
        return;

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
    $('#music_bg').hide();
    $('#jquery_jplayer').jPlayer("stop");
}

function musicShow()
{
    $('#music_bg').show();
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

    loveChangedMusic(song.id,song.name);
    
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
    musicLoadImage(song,index);
    $('#music_bg .image_holder').hide();
    $('#music_bg #image_holder_' + index).show();
    
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
function musicPrevious()
{
    var index = g_currentSongIndex - 1;
    if( index < 0 )
        index = g_musicList.length - 1;
    
    musicChange(index);
}

function musicPreloadImages()
{
    for( var i = 0 ; i < g_musicList.length ; ++i  )
    {
        var song = g_musicList[i];
        musicLoadImage(song,i);
    }
}

function musicLoadImage(song,index)
{
    imageLoadItem(song,index,'#music_bg');
    return;

    var image = song.image;
    var color = song.bgcolor;
    var bg_style = song.bg_style;
    
    if( !song.loaded )
    {
        song.loaded = true;
        var holder = $('#music_bg #image_holder_' + index);
        
        var win_height = $('#music_bg').height();
        var win_width = $('#music_bg').width();
        
        holder.css("background-color", "#" + color);
        if( bg_style == 'STRETCH' )
        {
            var image_params = musicGetBackgroundParams(song);
            
            var img_style = "width: {0}px; height: {1}px;".format(image_params.width,image_params.height);
            var img_url = "/timthumb.php?src={0}&w={1}&zc=0&q=100".format(image,win_width);

            var div_holder_style = "";
            div_holder_style += "height: {0}px; ".format(win_height);
            div_holder_style += "width: {0}px; ".format(win_width);
            div_holder_style += "margin-top: {0}px; ".format(image_params.margin_top);
            div_holder_style += "margin-left: {0}px; ".format(image_params.margin_left);
            div_holder_style += "padding-bottom: {0}px; ".format(-image_params.margin_top);
            div_holder_style += "padding-right: {0}px; ".format(-image_params.margin_left);

            var html = "";
            html += "<div style='{0}'>".format(div_holder_style);
            html += "<img src='{0}' style='{1}' />".format(img_url,img_style);
            html += "</div>"
            holder.html(html);
            
            holder.css("background-image","none");
            holder.css("background-repeat","no-repeat");
            holder.css("background-position","center center");
        }
        else if( bg_style == 'LETTERBOX' )
        {
            var image_params = imageGetLetterboxParams(song,'#music_bg');

            var img_style = "width: {0}px; height: {1}px;".format(image_params.width,image_params.height);
            
            var div_holder_style = "";
            div_holder_style += "height: {0}px; ".format(win_height);
            div_holder_style += "width: {0}px; ".format(win_width);
            div_holder_style += "margin-top: {0}px; ".format(image_params.margin_top);
            div_holder_style += "margin-left: {0}px; ".format(image_params.margin_left);
            div_holder_style += "padding-bottom: {0}px; ".format(-image_params.margin_top);
            div_holder_style += "padding-right: {0}px; ".format(-image_params.margin_left);
            
            var html = "";
            html += "<div style='{0}'>".format(div_holder_style);
            html += "<img src='{0}' style='{1}' />".format(image,img_style);
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
            var html = "<div style='width: 100%; height: {0}px;'></div>".format(win_height);
            holder.html(html);
        }
        else if( bg_style == 'TILE' )
        {
            holder.css("background-image","url(" + image + ")");
            holder.css("background-repeat","repeat");
            holder.css("background-position","center center");
            var html = "<div style='width: 100%; height: {0}px;'></div>".format(win_height);
            holder.html(html);
        }
    }            
}

function musicResizeBackgrounds()
{
    imageResizeBackgrounds(g_musicList,'#music_bg');
    return;

    for( var i = 0 ; i < g_musicList.length ; ++i )
    {
        var song = g_musicList[i];
        
        if( !song.loaded )
            continue;
        
        var bg_style = song.bg_style;
        if( bg_style == 'STRETCH' )
        {
            var win_height = $('#music_bg').height();
            var win_width = $('#music_bg').width();
            
            var div_holder = $('#music_bg  #image_holder_' + i + ' div');
            var image = $('#music_bg #image_holder_' + i + ' div img');
            
            div_holder.height(win_height);
            div_holder.width(win_width);
            
            var image_params = musicGetBackgroundParams(song);
            
            image.width(image_params.width);
            image.height(image_params.height);
            
            //div_holder.scrollLeft(-image_params.margin_left);
            //div_holder.scrollTop(-image_params.margin_top);
            div_holder.css("margin-left",image_params.margin_left + "px");
            div_holder.css("margin-top",image_params.margin_top + "px");
            div_holder.css("padding-right",-image_params.margin_left + "px");
            div_holder.css("padding-bottom",-image_params.margin_top + "px");
        }
    }
}

function musicGetBackgroundParams(song)
{
    var win_height = $('#music_bg').height();
    var win_width = $('#music_bg').width();
    var win_ratio = win_width / win_height;
    
    var img_width = song.image_data.width;
    var img_height = song.image_data.height;
    var img_ratio = img_width/img_height;
    
    
    var height = 0;
    var width = 0;
    var margin_left = 0;
    var margin_top = 0;
    
    if( win_ratio < img_ratio )
    {
        height = win_height;
        width = height * img_ratio;
        margin_left = -(width - win_width)/2;
    }
    else
    {
        width = win_width;
        height = width / img_ratio;
        margin_top = -(height - win_height)/2;
    }

    var ret = {
        'width': width,
        'height': height,
        'margin_top': margin_top,
        'margin_left': margin_left
    };
    return ret;
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


