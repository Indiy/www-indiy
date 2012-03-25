
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

function onReady()
{
    if( typeof(g_genreList) == 'undefined' )
        g_genreList = [];

    if( !('ontouchstart' in document) )
        $('body').addClass('no_touch');

    var vars = {};
    window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi,function(m,k,v){vars[k] = v;});

    var config = {
        solution: "html, flash",
        ready: jPlayerReady,
        swfPath: "/swf/",
        supplied: "mp3",
        wmode: "window"
    };
    $("#jquery_jplayer_1").jPlayer(config)
    .bind($.jPlayer.event.play,jplayerPlay)
    .bind($.jPlayer.event.pause,jplayerPause)

    if( 'genre' in vars )
        g_genre = vars['genre'];

    loadLoved();
    window.setInterval(scrollTrackTitle,50);
    
    $("img").error(function() { $(this).hide(); });
}
$(document).ready(onReady);

function jPlayerReady()
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
    g_playing = true;
    g_intervalUpdateTrack = window.setInterval(updateTrackInfo,200);
    $('#player .play').removeClass('paused');
}
function jplayerPause()
{
    g_playing = false;
    if( g_intervalUpdateTrack !== false )
    {
        window.clearInterval(g_intervalUpdateTrack);
        g_intervalUpdateTrack = false;
    }
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
        
        var img_url = "/media/" + title.replace(/ /g,"_"); 
        var img = "<img src='" + img_url + "'>";
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
    $("#jquery_jplayer_1").jPlayer("play");
}
function playerPause()
{
    $("#jquery_jplayer_1").jPlayer("stop");
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
    g_genre = new_genre;
    jplayerStartMedia();
    emptyTrackInfo();
    loadSteamInfo();
}


