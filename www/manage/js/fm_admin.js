
function fmReady()
{
    fmUpdateDisplay();
}
$(document).ready(fmReady);


function fmAddStream()
{
    var name = $('#fm_streams .add_stream input').val();
    
    if( !name || name.length == 0 )
    {
        window.alert("Please name your new stream.");
        return;
    }
    
    var args = {
        name: name,
        artist_id: g_artistId,
    };
    
    jQuery.ajax(
    {
        type: 'POST',
        url: '/manage/data/fm_stream.php',
        data: args,
        dataType: 'json',
        success: function(data) 
        {
            if( data['success'] )
            {
                var s = data['stream'];
                g_streams.push(s);
                fmUpdateDisplay();
            }
            else
            {
                window.alert("Failed to add stream.");
            }
        },
        error: function()
        {
            window.alert("Failed to add stream.");
        }
    });
}

function fmOptionList(type)
{
    var possible_files = getArtistFiles(type);
    
    var html = "";
    
    for( var i = 0 ; i < possible_files.length ; ++i )
    {
        var file = possible_files[i];
        
        var val = file.id;
        var vis = file.upload_filename;
        
        html += "<option value='{0}'>{1}</option>".format(val,vis);
    }
    return html;
}

function fmUpdateDisplay()
{
    $('#fm_streams .stream_list').empty();

    for( var i = 0 ; i < g_streams.length ; ++i )
    {
        var stream = g_streams[i];
        var songs = stream.songs;
        
        var html = "";
        
        html += "<div class='stream' id='stream_{0}'>".format(i);
        html += " <div class='title'>{0}</div>".format(stream.name);
        html += " <div class='song_list'>";
        
        for( var j = 0 ; j < songs.length ; ++j )
        {
            var song = songs[j];
            
            html += "<div class='song'>";
            html += " <div class='scrubber'>{0}</div>".format(song.scrubber_text);
            html += " <div class='bottom'>{0}</div>".format(song.bottom_text);
            html += " <div class='audio'>{0}</div>".format(song.audio_file_id);
            html += " <div class='image'>{0}</div>".format(song.image_file_id);
            html += " <div class='remove'></div>";
            html += "</div>";
        }
        
        html += " </div>";
        html += " <div class='add_song'>";
        html += "  <div class='title'>Add Stream</div>";
        html += "  <div class='add_line'>";
        html += "   <div class='scrubber_container'>";
        html += "    <input class='scrubber' type='text'>";
        html += "   </div>";
        html += "   <div class='bottom_container'>";
        html += "    <input class='bottom' type='text'>";
        html += "   </div>";
        html += "   <div class='audio_container'>";
        html += "    <select class='audio_drop' >";
        html += fmOptionList('AUDIO');
        html += "    </select>";
        html += "   </div>";
        html += "   <div class='image_container'>";
        html += "    <select class='image_drop'>";
        html += fmOptionList('IMAGE');
        html += "    </select>";
        html += "   </div>";
        html += "  </div>";
        html += "  <div class='button_container'>";
        html += "   <div class='button' onclick='fmAddSong({0});'>Add Song</div>".format(i);
        html += "  </div>";
        html += " </div>";
        html += "</div>";
    }
}


