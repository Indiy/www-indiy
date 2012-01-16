

$(document).ready(setupAudioPlayer);

function setupAudioPlayer()
{
 
    $("#jquery_jplayer").jPlayer({
        ready: function() {
            displayPlayList();
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

function displayPlayList() 
{
    $("#jplayer_playlist ul").empty();
    for( var i = 0 ; i < g_myPlayList.length ; ++i ) 
    {
        var song = g_myPlayList[i];
        var listItem = (i == g_myPlayList.length-1) ? "<li class='jplayer_playlist_item_last'>" : "<li>";
        listItem += song.plus;
        listItem += "<a href='#' id='jplayer_playlist_item_" + i + "' tabindex='1'>";
        listItem += "<span class='thisisthetrackname'>" + song.name + "</span>";
        listItem += "<span class='songimage' style='display: none;'>" + song.image + "</span>";
        listItem += "<span class='sellitunes' style='display: none;'>" + song.itunes + "</span>";
        listItem += "<span class='sellamazon' style='display: none;'>" + song.amazon + "</span>";
        listItem += "<div class='songbgcolor' style='display: none;'>" + song.bgcolor + "</div>";
        listItem += "<div class='songbgposition' style='display: none;'>" + song.bgposition + "</div>";
        listItem += "<div class='songbgrepeat' style='display: none;'>" + song.bgrepeat + "</div>";
        listItem += "</a>";
        if( song.download )
        {
            listItem += song.download;
        }
        else if( song.amazon || song.itunes )
        {
            listItem += "<span id='song_buy_icon_" + i + "' class='song_buy_icon' onclick='songBuyPopup(" + i + ");'>";
            listItem += "<img src='/images/buy_icon.png'/>";
            listItem += "</span>";
        }
        listItem += "<div class='clear'></div>";
        listItem += "<div class='metadata'>This is a test</div>";
        listItem += "<div class='clear'></div>";
        listItem += "</li>";
        $("#jplayer_playlist ul").append(listItem);
        $("#jplayer_playlist_item_"+i).data( "index", i ).click( function() {
                                                                var index = $(this).data("index");
                                                                if (playItem != index) {
                                                                playListChange( index );
                                                                } else {
                                                                //$("#jquery_jplayer").jPlayer("play");
                                                                }
                                                                $(this).blur();
                                                                return false;
                                                                });
    }
}

function playListInit(autoplay) 
{
    if(autoplay)
        playListChange( playItem );
    else
        playListConfig( playItem );
}

function playListConfig( index ) 
{
    $("#jplayer_playlist_item_"+playItem).removeClass("jplayer_playlist_current").parent().removeClass("jplayer_playlist_current");
    $("#jplayer_playlist_item_"+index).addClass("jplayer_playlist_current").parent().addClass("jplayer_playlist_current");
    
    playItem = index;
    var song = g_myPlayList[index];
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
    var sellamazon = $("#jplayer_playlist_item_"+index).children("span.sellamazon").text();
    var sellitunes = $("#jplayer_playlist_item_"+index).children("span.sellitunes").text();
    var mystore_product_id = song.product_id;
    
    var trackname = $("#jplayer_playlist_item_"+index).children("span.thisisthetrackname").text();
    var image = $("#jplayer_playlist_item_"+index).children("span.songimage").text();
    
    var color = song.bgcolor;
    var position = song.bgposition;
    var repeat = song.bgrepeat;
    
    $('#image').css("background-color", "#"+color);
    var src_arg = "/artists/images/" + image;
    if( repeat == 'stretch' )
    {
        var img_url = "/timthumb.php?src=" + src_arg + "&w=" + getWindowWidth() + "&h="+ getWindowHeight() + "&zc=0&q=100";
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
        $('#image').css("background-image","url(" + src_arg + ")");
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
    updateListens(image);
    
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
    var index = (playItem+1 < g_myPlayList.length) ? playItem+1 : 0;
    playListChange( index );
}

function playListPrev() 
{
    var index = (playItem-1 >= 0) ? playItem-1 : g_myPlayList.length-1;
    playListChange( index );
}

