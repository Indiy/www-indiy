

g_playListShown = false;

$(document).ready(setupAudioPlayer);

function setupAudioPlayer()
{
    $('#playlist .song_list').lionbars();

    $("#jquery_jplayer").jPlayer({
        ready: function() {
            playListInit(true); // Parameter is a boolean for autoplay.
        },
        solution: "html, flash",
        supplied: "mp3, oga",
        swfPath: "/js/Jplayer.swf",
        verticalVolume: true,
        wmode: "window"
    })
    .bind($.jPlayer.event.ended, function() {
        playListNext();
    });
 
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
}

function playListInit(autoplay) 
{
    if(autoplay)
        playListChange( playItem );
    else
        playListConfig( playItem );
}

function updateListens(song_id)
{
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
            //$('#total_listens').text(g_totalListens);
            $('#current_track_listens').text(track_listens);
        },
        error: function()
        {
            //alert('Failed to get listens!');
        }
    });
}


function playListConfig( index ) 
{
    $("#playlist .song_list_item").removeClass("current");
    $("#playlist .song_list_item_" + index).addClass("current");
    
    playItem = index;
    var song = g_songPlayList[index];
    var media = {
        mp3: song.mp3,
        oga: song.mp3.replace(".mp3",".ogg")
    };
    if( song.mp3.endsWith("mp3") )
    {
        $("#jquery_jplayer").jPlayer("setMedia", media);
        $("#jquery_jplayer").jPlayer("play");
        $('#jplayer_stop').show();
        $('#jplayer_pause').show();
        $('#jplayer_play').show();
        $('#jplayer_volume_bar').show();
        $('.current-track').show();
        $('#jplayer_play_time').show();
        $('.slash').show();
        $('#jplayer_total_time').show();
        $('.jp-progress').show();
        $('#volumebg').show();
        $('#progressbg').show();
        $('.jp-play-fake').show();
        $('.jp-pause-fake').show();
        $('.playlist-main').show();
        $('.playlist-bottom').show();
        $('.jp-controls-to-hide').show();
    }
    else
    {
        $("#jquery_jplayer").jPlayer("stop");
        $('#jplayer_stop').hide();
        $('#jplayer_pause').hide();
        $('#jplayer_play').hide();
        $('#jplayer_volume_bar').hide();
        $('.current-track').hide();
        $('#jplayer_play_time').hide();
        $('.slash').hide();
        $('#jplayer_total_time').hide();
        $('.jp-progress').hide();
        $('#volumebg').hide();
        $('#progressbg').hide();
        $('.jp-play-fake').hide();
        $('.jp-pause-fake').hide();
        $('.playlist-main').hide();
        $('.playlist-bottom').hide();
        $('.jp-controls-to-hide').hide();
    }
    
    g_currentSongId = song.id;
    window.location.hash = '#song_id=' + g_currentSongId; 
    $('span.showamazon').hide();
    $('span.showitunes').hide();
    $('span.show_mystore').hide();
    
    // Display Image            
    $('#loader').show();
    $('#image').hide();
    
    // Get Current Image
    var sellamazon = song.amazon;
    var sellitunes = song.itunes;
    var mystore_product_id = song.product_id;
    
    var trackname = song.name;
    var image = song.image;
    
    var color = song.bgcolor;
    var position = song.bgposition;
    var repeat = song.bgrepeat;
    
    $('#image').css("background-color", "#"+color);
    if( repeat == 'stretch' )
    {
        var img_url = "/timthumb.php?src=" + image + "&w=" + getWindowWidth() + "&h="+ getWindowHeight() + "&zc=0&q=100";
        var style = "width: 100%; height: 100%;";
        var html = "<img src='" + img_url + "' style='" + style + "'/>";
        $('#image').html(html);
        $('#image').css("background-image","none");
        $('#image').css("background-repeat","no-repeat");
        $('#image').css("background-position","center center");
    }
    else
    {
        $('#image').html("");
        $('#image').css("background-image","url(" + image + ")");
        $('#image').css("background-repeat",repeat);
        $('#image').css("background-position",position);
    }
    $('#image').fadeIn();
    
    if (sellamazon == "" && sellitunes == "" && !mystore_product_id) {
        $('div.mighthide').fadeOut();
    } else {
        $('div.mighthide').fadeIn();
    }
    
    if (sellamazon != "") {
        $('span.showamazon').html("<a href='" + sellamazon + "' class='amazon' target='_blank'></a>");
        $('span.showamazon').show();
    }
    
    if (sellitunes != "") {
        $('span.showitunes').html("<a href='" + sellitunes + "' class='itunes' target='_blank'></a>");
        $('span.showitunes').show();
    }
    if (mystore_product_id)
    {
        $('span.show_mystore').html("<a href='javascript:buySong(" + mystore_product_id + ");' class='mystore' target='_blank'></a>");
        $('span.show_mystore').show();
    }
    
    $('#current_track_name').text(trackname);
    $(".vote").click(function(event) 
                     {
                     var voteBody = $(this).text();
                     var voteData = "&vartist=" + g_artistId;
                     voteData += "&vtrack=" + g_currentSongId;
                     voteData += "&vote=" + voteBody;
                     
                     $.post("jplayer/ajax.php", voteData, function(voteResultsNow) {
                            $("#results").html(voteResultsNow);
                            $("#results").fadeIn();
                            setTimeout(function(){ 
                                       $("#results").fadeOut();
                                       }, 2000);
                            });
                     });
    g_totalListens++;
    updateListens(song.id);
    
    setTimeout(function(){ 
               $('#loader').hide();
               }, 1500);
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

function playListChange( index ) 
{
    playListConfig( index );
    //$("#jquery_jplayer").jPlayer("play");
}

function playListNext() 
{
    var index = (playItem+1 < g_songPlayList.length) ? playItem+1 : 0;
    playListChange( index );
}

function playListPrev() 
{
    var index = (playItem-1 >= 0) ? playItem-1 : g_songPlayList.length-1;
    playListChange( index );
}

function hidePlaylist()
{
    $('#playlist').animate({"left": "-230px"}, "fast");
    g_playListShown = false;
}
function showPlaylist()
{
    $('#playlist').animate({"left": "0px"}, "fast");
    g_playListShown = true;
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
    var top = $('#playlist .song_list').scrollTop();
    $('#playlist .song_list').scrollTop(top - 25);
}
function playlistScrollDown()
{
    var top = $('#playlist .song_list').scrollTop();
    $('#playlist .song_list').scrollTop(top + 25);
}

function songBuyPopup(i)
{
    var id = '#song_buy_icon_' + i;
    var pos = $(id).offset();
    var top = pos.top - 38;
    var left = pos.left;
    
    var song = g_songPlayList[i];
    if( song.product_id )
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
    $('#song_buy_popup').show();
}

