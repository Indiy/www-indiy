
var PROGRESS_BAR_WIDTH = 534 - 2;
var PROGRESS_ROUND_LENGTH = PROGRESS_BAR_WIDTH - 6;

var g_genreInfo = false;
var g_streamInfo = false;
var g_scrollingRight = true;
var g_lastStreamLoad = 0;
var g_playing = false;
var g_intervalUpdateTrack = false;
var g_historyShown = false;
var g_loveMap = {};
var g_genre = 'rock';
var g_flash = false;
var g_buffering = false;

function ffmp3Callback(event,value)
{
    if( event == 'buffering' )
    {
        stopUpdateTrack();
        jplayerPlay();
    }
    else if( event == 'play' )
    {
        jplayerPlaying();
    }
}

function onReady()
{
    g_flash = swfobject.hasFlashPlayerVersion("9.0.0");
    if( typeof(g_genreList) == 'undefined' )
        g_genreList = [];

    if( !('ontouchstart' in document) )
        $('body').addClass('no_touch');

    var vars = {};
    window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi,function(m,k,v){vars[k] = v;});
    if( 'genre' in vars )
        g_genre = vars['genre'];

    if( g_flash )
    {
        jplayerReady();
    }
    else
    {
        var config = {
            solution: "html, flash",
            preload: "none",
            ready: jplayerReady,
            swfPath: "/swf/Jplayer.swf",
            supplied: "mp3",
            wmode: "window"
        };
        $("#jquery_jplayer_1").jPlayer(config)
        .bind($.jPlayer.event.play,jplayerPlay)
        .bind($.jPlayer.event.playing,jplayerPlaying)
        .bind($.jPlayer.event.pause,jplayerPause);
    }

    loadLoved();
    window.setInterval(scrollTrackTitle,50);
    
    update_genre_bg();
    
    $('.overlay_container').onclick(backgroundClick);
}

function backgroundClick()
{
    window.open("http://www.duracellpower.com/","_blank");
}

function get_genre_data()
{
    for( var i = 0 ; i < g_genreList.length ; i++ )
    {
        var genre_data = g_genreList[i];
        
        if( genre_data.stream_name == g_genre )
        {
            return genre_data;
        }
    }
    
    return false;
}

var g_backgroundFlipTimeout = false;

function update_genre_bg()
{
    var genre_data = get_genre_data();
    
    if( genre_data && genre_data.num_images > 1 )
    {
        var max = genre_data.num_images;
    
        var image_num = Math.floor(Math.random() * max) + 1;
    
        var img = "/images/" + g_genre + "_" + image_num + ".jpg";
        var bg_css = "black url(\"" + img + "\") center center no-repeat";
        $('.overlay_container').css('background',bg_css);
    }
    else
    {
        var img = "/images/" + g_genre + ".jpg";
        var bg_css = "black url(\"" + img + "\") center center no-repeat";
        $('.overlay_container').css('background',bg_css);
    }
    
    if( g_backgroundFlipTimeout !== false )
    {
        window.clearTimeout(g_backgroundFlipTimeout);
        g_backgroundFlipTimeout = false;
    }
    g_backgroundFlipTimeout = window.setTimeout(update_genre_bg,30*1000);
}

$(document).ready(onReady);

function jplayerReady()
{
    changeGenre(g_genre);
}
function jplayerStartMedia()
{
    var media = {
        mp3: "http://www.myartistdna.com:8000/stream_" + g_genre
    };
    $("#jquery_jplayer_1").jPlayer("setMedia",media).jPlayer("play");
}
function jplayerPlay()
{
    g_buffering = true;
    g_playing = true;
    $('#player .play').removeClass('paused');
    $('#track_title').text("BUFFERING STREAM");
}
function jplayerPlaying()
{
    g_buffering = false;
    g_intervalUpdateTrack = window.setInterval(updateTrackInfo,200);
    loadSteamInfo();
}
function stopUpdateTrack()
{
    if( g_intervalUpdateTrack !== false )
    {
        window.clearInterval(g_intervalUpdateTrack);
        g_intervalUpdateTrack = false;
    }
}
function jplayerPause()
{
    g_playing = false;
    stopUpdateTrack();
    $('#player .play').addClass('paused');
}

function msTime()
{
    return (new Date()).getTime();
}

function scrollTrackTitle()
{
    var old_pos = $('#title_scoller').scrollLeft();
    var new_pos = old_pos;
    if( g_scrollingRight )
        new_pos--;
    else
        new_pos++;
    $('#title_scoller').scrollLeft(new_pos);
    if( old_pos == $('#title_scoller').scrollLeft() )
        g_scrollingRight = !g_scrollingRight;
}

function mins_secs(secs)
{
    var mins = Math.floor(secs / 60);
    secs -= mins * 60;
    return sprintf("%02d:%02d",mins,secs); 
}
function titleFromTrack(track)
{
    return track.artist + " - " + track.song;
}

function updateTrackInfo()
{
    if( !g_streamInfo )
        return;
 
    if( g_buffering )
        return;
       
    var track = g_streamInfo['history'][0];
    var title = titleFromTrack(track);
    
    if( $('#track_title').text() != title )
    {
        $('#track_title').text(title);
        g_scrollingRight = true;
        $('#track_title').scrollLeft(0);
        if( title in g_loveMap )
            $('#player .heart').addClass('love');
        else
            $('#player .heart').removeClass('love');
        updateHistory();
    }
    
    var duration = track.duration;
    var start = track.start;
    var curr_pos = msTime()/1000 - start;
    if( curr_pos > duration )
        curr_pos = duration;
    
    var s = mins_secs(curr_pos) + " - " + mins_secs(duration);
    if( $('#track_duration').text() != s )
    {
        $('#track_duration').text(s);
    }
    var percent = 0;
    if( duration > 0 )
        var percent = curr_pos/duration;
    var width = percent * PROGRESS_BAR_WIDTH;
    $('#player .progress .bar').width(width);
    if( width >= PROGRESS_ROUND_LENGTH )
        $('#player .progress .bar').css('border-radius','6px 6px 6px 6px');
    else
        $('#player .progress .bar').css('border-radius','6px 0px 0px 6px');

    if( curr_pos + 10 > duration )
    {
        var delta = msTime() - g_lastStreamLoad;
        if( delta > 500 )
        {
            g_lastStreamLoad = msTime();
            loadSteamInfo();
        }
    }
}
function emptyTrackInfo()
{
    $('#track_title').empty();
    $('#track_duration').empty();
    $('#player .progress .bar').width(0);
    $('#player .progress .bar').css('border-radius','6px 0px 0px 6px');
    $('#history .content').empty();
}

function updateHistory()
{
    $('#history .content').empty();
    for( var i = 1 ; i < Math.min(g_streamInfo['history'].length,4) ; ++i )
    {
        var track = g_streamInfo['history'][i];
        var title = titleFromTrack(track);
        var duration = mins_secs(track.duration);
        var love = "";
        if( title in g_loveMap )
            love = "love";
        
        var img_url = "/images/" + g_genre.replace(/ /g,"_") + "_icon.png";  
        var img = "<img onerror='$(this).hide();' src='" + img_url + "'>";
        var html = "";
        html += "<div class='row'>";
        html += " <div class='icon'>" + img + "</div>";
        html += " <div class='title'>" + title + "</div>";
        html += " <div class='length'>" + duration + "</div>";
        html += " <div id='history_loved_" + i + "' class='loved " + love + "'>";
        html += "  <div class='love_icon' onclick='toggleLoveHistory(this," + i + ");'>";
        html += "   <div class='tooltip love_tip'>";
        html += "    <div class='carrot'></div>";
        html += "    LOVE";
        html += "   </div>";
        html += "   <div class='tooltip unlove_tip'>";
        html += "    <div class='carrot'></div>";
        html += "    UNLOVE";
        html += "   </div>";
        html += "  </div>";
        html += " </div>";
        html += "</div>";
        $('#history .content').append(html);
    }
}
function loadSteamInfo()
{
    var url = "/data/stream_info.php";
    if( window.location.href.indexOf('localhost') >= 0 )
        url = "http://www.myartistdna.fm/data/stream_info.php";

    jQuery.ajax(
    {
        type: 'GET',
        url: url,
        dataType: 'json',
        success: function(data) 
        {
            g_genreInfo = data['genre_data'];
            g_genreList = data['genre_list'];
            g_streamInfo = g_genreInfo[g_genre];
            updateTrackInfo();
        },
        error: function()
        {
            //window.alert("Error!");
        }
    });
}

function playerToggle()
{
    if( g_playing )
        playerPause();
    else
        playerPlay();
}
function playerPlay()
{
    if( g_flash )
    {
        try 
        {
            var player = (document.ffmp3_player) ? document.ffmp3_player : document.getElementById('ffmp3_player');
            player.playSound();
        }
        catch(e) {}
    }
    else
    {
        $("#jquery_jplayer_1").jPlayer("play");
    }
}
function playerPause()
{
    if( g_flash )
    {
        try 
        {
            var player = (document.ffmp3_player) ? document.ffmp3_player : document.getElementById('ffmp3_player');
            player.stopSound();
        }
        catch(e) {}
        jplayerPause();
    }
    else
    {
        $("#jquery_jplayer_1").jPlayer("stop");
    }
}

function toggleHistory()
{
    if( g_historyShown )
        hideHistory();
    else
        showHistory();
}
function showHistory()
{
    hideGenrePicker();
    g_historyShown = true;
    $('#history').fadeIn();
    updateHistory();
}
function hideHistory()
{
    g_historyShown = false;
    $('#history').fadeOut();
}

function showAddMusic()
{
    hideGenrePicker();
    $('#add_music').fadeIn();
}
function hideAddMusic()
{
    $('#add_music').fadeOut();
}

function toggleLoveTrack(track)
{
    var title = titleFromTrack(track)
    if( title in g_loveMap )
    {
        delete g_loveMap[title];
        return false;
    }
    else 
    {
        addLoved(title);
        showLoved(track);
        return true;
    }
}
function toggleLoveCurrentSong()
{
    var track = g_streamInfo['history'][0];
    if( toggleLoveTrack(track) )
        $('#player .heart').addClass('love');
    else
        $('#player .heart').removeClass('love');
}
function toggleLoveHistory(self,i)
{
    var track = g_streamInfo['history'][i];
    if( toggleLoveTrack(track) )
        $('#history_loved_' + i).addClass('love');
    else
        $('#history_loved_' + i).removeClass('love');
}

function addLoved(title)
{
    try
    {
        g_loveMap[title] = true;
        var json = JSON.stringify(g_loveMap);
        window.localStorage["love_map"] = json;
    }
    catch(e) {}
}
function loadLoved()
{
    try 
    {
        var json = window.localStorage["love_map"];
        var map = JSON.parse(json);
        if( map )
        {
            g_loveMap = map;
        }
    }
    catch(e) {}
}

function showLoved(track)
{
    hideGenrePicker();

    var artist = track.artist;
    var song = track.song;
    
    $('#song_love .dialog .header .title span').text('"' + song + '"');
    
    var link_url = "http://www.myartistdna.fm"
    var host = "www.myartistdna.fm"
    var msg = 'Check out ' + artist + '\'s song "' + song + '" on MyArtistDNA.FM';
    var name = 'MyArtistDNA.FM';
    
    $('#fb_link').attr('href','http://www.facebook.com/sharer/sharer.php?u=' + host);
    $('#tw_link').attr('href','http://twitter.com/?status=' + encodeURIComponent(msg));

    var url = "http://www.tumblr.com/share/link?url=" + encodeURIComponent(link_url);
    url += "&name=" + encodeURIComponent(name);
    url += "&description=" + encodeURIComponent(msg);
    $('#tumblr_link').attr('href',url);
    
    var url = "http://pinterest.com/pin/create/button/?url=" + encodeURIComponent(link_url);
    url += "&description=" + encodeURIComponent(msg);
    $('#pin_link').attr('href',url);
    
    var url = "https://plusone.google.com/_/+1/confirm?hl=en&url=" + encodeURIComponent(link_url);
    $('#google_link').attr('href',url);
    
    var url = "mailto:?&subject=" + encodeURIComponent(msg);
    $('#email_link').attr('href',url);
    
    $('#song_love').fadeIn();
}
function hideLoved()
{
    $('#song_love').fadeOut();
}

var g_genrePickerShown = false;
function toggleGenrePicker()
{
    if( g_genrePickerShown )
        hideGenrePicker();
    else
        showGenrePicker();
}
function showGenrePicker()
{
    if( !g_genrePickerShown )
    {
        $('#genre_container').empty();
        for( var i = 0 ; i < g_genreList.length ; ++i )
        {
            var g = g_genreList[i];
            if( g.stream_name != g_genre )
            {
                var html = "<div onclick=\"changeGenre('" + g.stream_name + "');\">";
                html += g.genre;
                html += "</div>";
                $('#genre_container').append(html);
            }
        }
    
        g_genrePickerShown = true;
        $('#genre_container div').show();
        $('#genre_container .' + g_genre).hide();
        $('#player .genre_picker').fadeIn();
    }
}
function hideGenrePicker()
{
    if( g_genrePickerShown )
    {
        g_genrePickerShown = false;
        $('#player .genre_picker').fadeOut();    
    }
}
function changeGenre(new_genre)
{
    hideGenrePicker();
    emptyTrackInfo();
    g_genre = new_genre;
    if( g_flash )
    {
        embedFlash();
        jplayerPlay();
    }
    else
    {
        jplayerStartMedia();
    }
    update_genre_bg();
}

function embedFlash()
{
    $('#player_container').empty();
    
    var host = window.location.hostname;
    var url = "http://" + host + ":8000/stream_" + g_genre;
    
    var html = "";
    html += '<object id="ffmp3_player" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="190" height="62">';
    html += ' <param name="movie" value="ffmp3-config.swf" />';
    html += ' <param name="flashvars" value="url=' + url + '&lang=en&codec=mp3&volume=100&autoplay=true&traking=true&jsevents=true&buffering=5&skin=ffmp3-darkconsole.xml&title=MyAritstDNA.fm&welcome=Welcome%20to%20MAD.fm" />';
    html += '<param name="wmode" value="transparent" />';
    html += '<param name="allowscriptaccess" value="always" />';
    html += '<param name="scale" value="noscale" />';
    html += '<embed name="ffmp3_player" src="ffmp3-config.swf" flashvars="url=' + url + '&lang=en&codec=mp3&volume=100&autoplay=true&traking=true&jsevents=true&buffering=5&skin=ffmp3-darkconsole.xml&title=MyAritstDNA.fm&welcome=Welcome%20to%20MAD.fm" width="190" scale="noscale" height="62" wmode="transparent" allowscriptaccess="always" type="application/x-shockwave-flash" />';
    html += '</object>';
    $('#player_container').html(html);
}

