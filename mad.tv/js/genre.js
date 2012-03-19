
var g_genrePickerShown = false;
var g_genre = 'rock';

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
        g_genrePickerShown = true;
        $('#genre_container div').show();
        $('#genre_container .' + g_genre).hide();
        $('#player .genre_picker').fadeIn();
        $('#player #tip_genre').addClass("inhibit");
    }
}
function hideGenrePicker()
{
    if( g_genrePickerShown )
    {
        g_genrePickerShown = false;
        $('#player .genre_picker').fadeOut();    
        $('#player #tip_genre').removeClass("inhibit");
    }
}

function changeGenre(new_genre)
{
    hideGenrePicker();
    g_genre = new_genre;
    if( g_videoPlayer )
        g_videoPlayer.pause();
    //$('#video_container').empty();
    
    //emptyTrackInfo();
    //loadSteamInfo();
}


