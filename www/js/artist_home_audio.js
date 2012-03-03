

g_playListShown = false;

var IS_IPAD = navigator.userAgent.match(/iPad/i) != null;

var IS_IE = false;
var IS_OLD_IE = false;
(function() {
    var ie_match = navigator.userAgent.match(/IE ([^;]*);/);
    if( ie_match != null && ie_match.length > 1 )
    {
        IS_IE = true;
        var ie_version = parseFloat(ie_match[1]);
        if( ie_version < 9.0 )
            IS_OLD_IE = true;
    }
})();

$(document).ready(setupAudioPlayer);

function setupAudioPlayer()
{
    $('#playlist .song_list').lionbars(/*{ autohide: true }*/);
    $('#playlist .lb-wrap').css('height','200px');

    $("#jquery_jplayer").jPlayer({
        ready: function() {
            jplayerReady(); // Parameter is a boolean for autoplay.
        },
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
     
    $("#jplayer_previous").click( function() {
        playListPrev();
        $(this).blur();
        return false;
    });
 
    $("#jplayer_next").click( function() {
        playListNext();
        $(this).blur();
        return false;
    });
    
    $('#image_holder_' + g_currentSongIndex).show();
    
    if( IS_IPAD )
    {
        $('#prev_track').hide();
        $('#next_track').hide();
    }
    else
    {
        $('#playlist').mouseover(mouseoverPlaylist);
        $('#playlist').mouseout(mouseoutPlaylist);

        $('#player').mouseover(mouseoverPlayer);
        $('#player').mouseout(mouseoutPlayer);
        
        $('#prev_track').mouseover(function(){ $(this).animate({width: "200px"}, 250);});
        $('#prev_track').mouseout(function(){ $(this).animate({width: "31px"}, 250);});
        
        $('#next_track').mouseover(function(){ $(this).animate({width: "200px"}, 250);});
        $('#next_track').mouseout(function(){ $(this).animate({width: "31px"}, 250);});
    }
    
    $('#player .seek_bar').click(playerSeek);
    $('#player .volume').click(playerVolume);
    
    window.setTimeout(preloadImages,1000);
    $(document).resize(resizeBackground);
}

function formatMinSeconds(seconds)
{
    seconds = Math.floor(seconds);
    var mins = Math.floor(seconds / 60);
    var seconds = seconds % 60;
    var seconds_string = '';
    if( seconds < 10 )
        seconds_string += "0";
    seconds_string += seconds;
    return mins + ":" + seconds_string;
}
function jplayerTimeUpdate(event)
{
    var percent = event.jPlayer.status.currentPercentAbsolute;
    var total_time = event.jPlayer.status.duration;
    var curr_time = event.jPlayer.status.currentTime;
    
    var time = formatMinSeconds(curr_time) + ' / ' + formatMinSeconds(total_time);
    $('#player .time').text(time);
    $('#player .seek_bar .current').css('width',percent + "%");
}
var g_playerIsPlaying = false;
function jplayerPlay()
{
    g_playerIsPlaying = true;
    $('#player .play_pause').addClass('playing');
}
function jplayerPause()
{
    g_playerIsPlaying = false;    
    $('#player .play_pause').removeClass('playing');
}
function jplayerEnded()
{
    g_playerIsPlaying = false;
    $('#player .play_pause').removeClass('playing');
    playListNext();
}
function jplayerVolume(event)
{
    var vol = event.jPlayer.options.volume;
    $('#player .volume .current').css('height',vol * 100 + "%");
}

function playerPlayPause()
{
    if( g_playerIsPlaying )
        $("#jquery_jplayer").jPlayer("pause");
    else
        $("#jquery_jplayer").jPlayer("play");
}
var g_isPlayerOpen = false;
function playerToggle()
{
    if( g_isPlayerOpen )
        playerClose();
    else
        playerOpen();
}
function playerOpen()
{
    if( !g_isPlayerHidden && !g_isPlayerOpen )
    {
        g_isPlayerOpen = true;
        $('#player').animate({"left": "0px"}, 300);
    }
}
function playerClose()
{
    if( !g_isPlayerHidden && g_isPlayerOpen )
    {
        g_isPlayerOpen = false;
        $('#player').animate({"left": "-462px"}, 300);
    }
}
var g_playerCloseTimer = false;
function mouseoverPlayer()
{
    if( g_playerCloseTimer !== false )
    {
        window.clearTimeout(g_playerCloseTimer);
        g_playerCloseTimer = false;
    }
    playerOpen();
}
function mouseoutPlayer()
{
    g_playerCloseTimer = window.setTimeout(playerClose,800);
}
var g_isPlayerHidden = false;
function playerHide()
{
    if( !g_isPlayerHidden )
    {
        g_isPlayerHidden = true;
        if( g_playerCloseTimer !== false )
        {
            window.clearTimeout(g_playerCloseTimer);
            g_playerCloseTimer = false;
        }
        $('#player').animate({"left": "-495px"}, 300);
    }
}
function playerUnhide()
{
    if( g_isPlayerHidden )
    {
        g_isPlayerHidden = false;
        g_isPlayerOpen = true;
        playerClose();
    }
}
function playerSeek(event)
{
    var x = event.offsetX;
    var width = $('#player .seek_bar').width();
    var progress = x / width;
    var duration = $("#jquery_jplayer").data("jPlayer").status.duration;
    var seek_secs = progress * duration;
    $("#jquery_jplayer").jPlayer("play",seek_secs);
}
function playerVolume(event)
{
    var y = event.pageY - $('#player .volume').offset().top;
    var height = $('#player .volume').height();
    var vol_ratio = 1 - y / height;
    $("#jquery_jplayer").jPlayer("volume",vol_ratio);
}

var g_songSwipe = false;
function setupSwipe()
{
    var element = document.getElementById('image_slider');
    var settings = {
        startSlide: g_currentSongIndex,
        callback: imageChange
    }
    g_songSwipe = new Swipe(element,settings);
}
function imageChange(event, index, elem)
{
    if( g_currentSongIndex != index )
    {
        playListChange( index );
    }
}

function jplayerReady() 
{
    playListChange(g_currentSongIndex);
    setupSwipe();
}

function preloadImages()
{
    for( var k in g_songPlayList )
    {
        var song = g_songPlayList[k];
        loadSongImage(song,k);
    }
}

function loadSongImage(song,index)
{
    var image = song.image;
    var color = song.bgcolor;
    var bg_style = song.bg_style;
    
    if( !song.loaded )
    {
        //$('#loader').show();
        
        song.loaded = true;
        var holder = $('#image_holder_' + index);
        
        holder.css("background-color", "#" + color);
        if( bg_style == 'STRETCH' )
        {
            var img_url = "/timthumb.php?src=" + image + "&w=" + getWindowWidth() + "&zc=0&q=100";
            var html = "<img src='" + img_url + "' />";
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
        
        //window.setTimeout(function() { $('#loader').hide(); }, 1500);
    }
}
function resizeBackground()
{
    var song = g_songPlayList[g_currentSongIndex];
    var bg_style = song.bg_style;
    if( bg_style == 'STRETCH' )
    {
        var win_height = getWindowHeight();
        var win_width = getWindowWidth();
        var win_ratio = win_width / win_height;

        var image = $('#image #image_holder_' + g_currentSongIndex + ' img');
        var img_width = image.width();
        var img_height = image.height();
        var img_ratio = img_width/img_height;
        
        if( win_ratio < img_ratio )
        {
            var height = win_height;
            var width = height * img_ratio;
            image.width(width);
            image.height(height);
        }
        else
        {
            var width = win_width;
            var height = width / img_ratio;
            image.width(width);
            image.height(height);
        }
    }
}

var g_listenUpdated = {};
function updateListens(song_id,index)
{
    if( song_id in g_listenUpdated )
        return false;

    g_listenUpdated[song_id] = true;

    var url = "/data/listens.php?song_id=" + song_id;
    jQuery.ajax(
    {
        type: 'POST',
        url: url,
        dataType: 'json',
        success: function(data) 
        {
            g_totalListens = data['total_listens'];
            var track_listens = data['track_listens'];
            g_songPlayList[index].listens = track_listens;
            $('#total_listens_val').text(g_totalListens);
            $('#current_track_listens').text(track_listens);
        },
        error: function()
        {
            //alert('Failed to get listens!');
        }
    });
    return true;
}


function playListChange( index ) 
{
    g_currentSongIndex = index;
    var song = g_songPlayList[index];
    $("#playlist .song_list_item").removeClass("current");
    $("#playlist #song_list_item_" + song.id).addClass("current");

    var media = {
        mp3: song.mp3,
        oga: song.mp3.replace(".mp3",".ogg")
    };
    if( song.mp3.endsWith("mp3") )
    {
        $("#jquery_jplayer").jPlayer("setMedia", media);
        $("#jquery_jplayer").jPlayer("play");
        playerUnhide();
    }
    else
    {
        $("#jquery_jplayer").jPlayer("stop");
        playerHide();
    }
    
    g_currentSongId = song.id;
    window.location.hash = '#song_id=' + g_currentSongId; 

    loadSongImage(song,index);

    var sellamazon = song.amazon;
    var sellitunes = song.itunes;
    var mystore_product_id = song.product_id;
    if( song.download )
    {
        $('#buynow_free a').attr("href",'/download.php?artist=' + g_artistId + '&id=' + song.id);
        $('#buynow_free').show();
        $('#buynow_itunes').hide();
        $('#buynow_amazon').hide();
        $('#buynow_mad_store').hide();
    }
    else
    {
        $('#buynow_free').hide();
        if( sellamazon != "" ) 
        {
            $('#buynow_amazon a').attr("href",sellamazon);
            $('#buynow_amazon').show();
        }
        else
        {
            $('#buynow_amazon').hide();
        }
        if( sellitunes != "" ) 
        {
            $('#buynow_itunes a').attr("href",sellitunes);
            $('#buynow_itunes').show();
        }
        else
        {
            $('#buynow_itunes').hide();
        }
        if( mystore_product_id && g_paypalEmail.length > 0 )
        {
            $('#buynow_mad_store a').attr("href",'javascript:buySong(' + mystore_product_id + ');');
            $('#buynow_mad_store').show();
        }
        else
        {
            $('#buynow_mad_store').hide();
        }
    }
    var trackname = song.name;
    $('#player .artist_song').text(g_artistName + ' - ' + trackname);
    $('#current_track_name').text(trackname);
 
    var listens = song.listens;
    $('#current_track_listens').text(listens);
 
    if( updateListens(song.id,index) )
    {
        g_totalListens++;
        $('#total_listens_val').text(g_totalListens);
    }
}
function songVote(vote)
{
    var voteData = "&vartist=" + g_artistId;
    voteData += "&vtrack=" + g_currentSongId;
    voteData += "&vote=" + vote;
    
    $.post("/jplayer/ajax.php", voteData, function(voteResultsNow) 
    {
        $("#vote_results").fadeIn();
        window.setTimeout(function() { $("#vote_results").fadeOut(); }, 2000);
    });
}

// Function that gets window width
function getWindowWidth() 
{
    screenMinWidth = 1024; // Minimum screen width
    var windowWidth = 0;
    if (typeof(window.innerWidth) == 'number') 
    {
        windowWidth = window.innerWidth;
    }
    else 
    {
        if (document.documentElement && document.documentElement.clientWidth) 
        {
            windowWidth = document.documentElement.clientWidth;
        }
        else if (document.body && document.body.clientWidth) 
        {
            windowWidth = document.body.clientWidth;
        }
    }
    if( windowWidth < screenMinWidth ) 
        windowWidth = screenMinWidth;
    return windowWidth;
}

// Function that gets window height
function getWindowHeight() 
{
    screenMinHeight =  768; // Minimum screen height
    var windowHeight = 0;
    if (typeof(window.innerHeight) == 'number')
    {
        windowHeight = window.innerHeight;
    }
    else 
    {
        if (document.documentElement && document.documentElement.clientHeight) 
        {
            windowHeight = document.documentElement.clientHeight;
        }
        else if (document.body && document.body.clientHeight) 
        {
            windowHeight = document.body.clientHeight;
        }
    }
    if( windowHeight < screenMinHeight ) 
        windowHeight = screenMinHeight;
    return windowHeight;
}

function playListNext() 
{
    if( g_currentSongIndex == g_songPlayList.length - 1 )
        g_songSwipe.slide(0,3000);
    else
        g_songSwipe.next();
}
function playListPrev() 
{
    if( g_currentSongIndex == 0 )
        g_songSwipe.slide(g_songPlayList.length - 1,3000);
    else
        g_songSwipe.prev();
}
function hidePlaylist()
{
    if( g_playListShown )
    {
        g_playListShown = false;
        $('#playlist').animate({"left": "-280px"}, 300);
        $('#song_buy_popup').hide();
    }
}
function showPlaylist()
{
    if( !g_playListShown )
    {
        g_playListShown = true;
        $('#playlist').animate({"left": "0px"}, 300);
    }
}
function togglePlaylistVisibility()
{
    if( g_playListShown )
        hidePlaylist();
    else
        showPlaylist();
}
function playlistScrollUp()
{
    var top = $('#playlist .lb-wrap').scrollTop();
    $('#playlist .lb-wrap').scrollTop(top - 25);
}
function playlistScrollDown()
{
    var top = $('#playlist .lb-wrap').scrollTop();
    $('#playlist .lb-wrap').scrollTop(top + 25);
}
var g_playlistHideTimer = false;
function mouseoverPlaylist()
{
    if( g_playlistHideTimer !== false )
    {
        window.clearTimeout(g_playlistHideTimer);
        g_playlistHideTimer = false;
    }
    showPlaylist();
}
function mouseoutPlaylist()
{
    g_playlistHideTimer = window.setTimeout(hidePlaylist,500);
}

function changeSong(i)
{
    $('#song_buy_popup').hide();
    g_songSwipe.slide(i,500)
}

function songBuyPopup(i)
{
    var id = '#song_buy_icon_' + i;
    var pos = $(id).offset();
    var top = pos.top - 38;
    var left = pos.left;
    
    var song = g_songPlayList[i];
    if( song.product_id && g_paypalEmail.length > 0 )
    {
        $('#song_buy_popup_mystore').show();
        $('#song_buy_popup_mystore').attr('href','javascript:buySong(' + song.product_id + ');');
    }
    else
    {
        $('#song_buy_popup_mystore').hide();
    }
    if( song.itunes )
    {
        $('#song_buy_popup_itunes').show();
        $('#song_buy_popup_itunes').attr('href',song.itunes);
    }
    else
    {
        $('#song_buy_popup_itunes').hide();
    }
    if( song.amazon )
    {
        $('#song_buy_popup_amazon').show();
        $('#song_buy_popup_amazon').attr('href',song.amazon);
    }
    else
    {
        $('#song_buy_popup_amazon').hide();
    }
    
    $('#song_buy_popup').css('top',top);
    $('#song_buy_popup').css('left',left);
    $('#song_buy_popup').toggle();
}

