
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
        artist_id: g_artistId
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

function fmAddSong(i)
{
    var stream = g_streams[i];

    var sel = "#fm_streams #stream_{0}".format(i);

    var scrubber_text = $(sel + " .add_line .scrubber input").val();
    var bottom_text = $(sel + " .add_line .bottom input").val();
    var audio_file_id = $(sel + " .add_line .audio select").val();
    var image_file_id = $(sel + " .add_line .image select").val();
    
    if( scrubber_text.length == 0
       || bottom_text.length == 0
       )
    {
        window.alert("Please enter a scrubber and bottom text for your new song.");
        return;
    }
    
    var args = {
        fm_stream_id: stream.id,
        scrubber_text: scrubber_text,
        bottom_text: bottom_text,
        audio_file_id: audio_file_id,
        image_file_id: image_file_id
    };
    
    jQuery.ajax(
    {
        type: 'POST',
        url: '/manage/data/fm_song.php',
        data: args,
        dataType: 'json',
        success: function(data) 
        {
            if( data['success'] )
            {
                var s = data['song'];
                g_streams[i]['songs'].push(s);
                fmUpdateDisplay();
            }
            else
            {
                window.alert("Failed to add song.");
            }
        },
        error: function()
        {
            window.alert("Failed to add song.");
        }
    });
}



function fmUpdateDisplay()
{
    $('#fm_streams .stream_list').empty();

    var html = "";
    for( var i = 0 ; i < g_streams.length ; ++i )
    {
        var stream = g_streams[i];
        var songs = stream.songs;
        
        html += "<div class='stream' id='stream_{0}'>".format(i);
        html += " <div class='title'>{0}</div>".format(stream.name);
        html += " <div class='column_titles'>";
        html += "  <div class='scrubber'>Scrubber Text</div>";
        html += "  <div class='bottom'>Bottom Text</div>";
        html += "  <div class='audio'>Audio File</div>";
        html += "  <div class='image'>Image File</div>";
        html += "  <div class='remove'></div>";
        html += " </div>";
        html += " <div class='song_list'>";
        
        for( var j = 0 ; j < songs.length ; ++j )
        {
            var song = songs[j];
            
            html += "<div class='song'>";
            html += " <div class='scrubber'>{0}</div>".format(song.scrubber_text);
            html += " <div class='bottom'>{0}</div>".format(song.bottom_text);
            html += " <div class='audio'>{0}</div>".format(song.audio_upload_filename);
            html += " <div class='image'>{0}</div>".format(song.image_upload_filename);
            html += " <div class='remove'></div>";
            html += "</div>";
        }
        
        html += " </div>";
        html += " <div class='add_song'>";
        html += "  <div class='add_line'>";
        html += "   <div class='scrubber'>";
        html += "    <input  type='text'>";
        html += "   </div>";
        html += "   <div class='bottom'>";
        html += "    <input type='text'>";
        html += "   </div>";
        html += "   <div class='audio'>";
        html += "    <select class='audio_drop' >";
        html += fmOptionList('AUDIO');
        html += "    </select>";
        html += "   </div>";
        html += "   <div class='image'>";
        html += "    <select class='image_drop'>";
        html += fmOptionList('IMAGE');
        html += "    </select>";
        html += "   </div>";
        html += "  </div>";
        html += "  <div class='button_container'>";
        html += "   <button onclick='fmAddSong({0});'>Add Song</button>".format(i);
        html += "  </div>";
        html += " </div>";
        html += "</div>";
    }
    $('#fm_streams .stream_list').html(html);
}


