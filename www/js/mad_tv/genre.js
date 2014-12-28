
var g_genrePickerShown = false;
var g_genre_id = false;

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
            if( g.genre_id != g_genre_id )
            {
                var html = "<div onclick=\"changeGenre('" + g.genre_id + "');\">";
                html += g.name;
                html += "</div>";
                $('#genre_container').append(html);
            }
        }
    
        g_genrePickerShown = true;
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
    $('#history .content').empty();
    if( g_touchDevice )
    {
        updateVideoElementInProgress();
    }
    else
    {
        loadSteamInfo(updateVideoElementInProgress);
    }
}


