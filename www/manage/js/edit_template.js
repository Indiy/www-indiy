
var g_templateIndex = false;

function updateTemplateList()
{
    $('#template_list_ul').empty();
    for( var i = 0 ; i < g_templateList.length ; ++i )
    {
        var template = g_templateList[i];
        var class_name = i % 2 == 0 ? 'odd' : '';
        var html = "";
        
        var desc = template.id + ": " + template.name;
        
        html += "<li id='arrayorder_{0}' class='{1}'>".format(template.id,class_name);
        html += "<span class='title'>";
        html += "<a onclick='showTemplatePopup({0});'>".format(i);
        html += desc;
        html += "</a>";
        html += "</span>";
        html += "<span class='delete'><a  href='#' onclick='deleteTemplate({0});'></a></span>".format(i);
        html += "</li>";
        $('#template_list_ul').append(html);
    }
}


function showTemplatePopup(template_index)
{
    g_templateIndex = template_index;
    
    var template = g_templateList[g_templateIndex];
    var type = template.type;
    
    var schema = TEMPLATE_SCHEMA[type];
    
    $('#edit_template .form_fields').empty();
    var html = "";
    for( var i = 0 ; i < schema.arg_list.length ; ++i )
    {
        var arg = schema.arg_list[i];
        
        if( arg.type == 'string' )
        {
            html += "<div class='input_container'>";
            html += " <div class='left_label'>{0}</div>".format(arg.description);
            html += " <input id='template_val_{0}' type='text' value='' class='right_text' style='width: 470px;' />".format(i);
            html += "</div>";
        }
        else if( arg.type == 'textbox' )
        {
            html += "<div class='flow_container'>";
            html += " <div class='line_label'>{0}</div>".format(arg.description);
            html += " <textarea id='template_val_{0}' class='textarea' style='height: 70px; width: 600px;'></textarea>".format(i);
            html += "</div>";
        }
        else if( arg.type == 'image_spec' )
        {
            html += "<div class='input_container' style='height: 50px;'>";
            html += " <div class='left_label'>{0}</div>".format(arg.description);
            html += " <select id='template_val_drop_{0}' class='right_drop' onchange='artistFileDropChange(this);'></select>".format(i);
            html += "</div>";
            html += "<div class='input_container'>";
            html += " <div class='left_label'>Image Style</div>";
            html += " <select id='template_val_bg_style_{0}' class='right_drop'>".format(i);
            html += "  <option value='LETTERBOX'>LETTERBOX</option>";
            html += "  <option value='STRETCH'>STRETCH</option>";
            html += "  <option value='CENTER'>CENTER</option>";
            html += "  <option value='TILE'>TILE</option>";
            html += " </select>";
            html += "</div>";
            html += "<div class='input_container'>";
            html += " <div class='left_label'>Image BG Color</div>";
            html += " <input id='template_val_bg_color_{0}' type='text' maxlength='6' size='6' class='color' value='' />".format(i);
            html += "</div>";
        }
        else if( arg.type == 'video' )
        {
            html += "<div class='input_container' style='height: 50px;'>";
            html += " <div class='left_label'>{0}</div>".format(arg.description);
            html += " <select id='template_val_drop_{0}' class='right_drop' onchange='artistFileDropChange(this);'></select>".format(i);
            html += "</div>";
        }
    }
    $('#edit_template .form_fields').html(html);

    $('#edit_template #template_name').val(template.name);
    for( var i = 0 ; i < schema.arg_list.length ; ++i )
    {
        var arg = schema.arg_list[i];
        var name = arg.name;
        
        if( template.params && name in template.params )
        {
            var val = template.params[name];
            if( arg.type == 'string' || arg.type == 'textbox' )
            {
                var sel = "#edit_template #template_val_{0}".format(i);
                $(sel).val(val);
            }
            else if( arg.type == 'image_spec' )
            {
                var sel = "#edit_template #template_val_drop_{0}".format(i);
                fillArtistFileIdSelect(sel,'IMAGE',val.file_id);
                var sel = "#edit_template #template_val_bg_style_{0}".format(i);
                $(sel).val(val.bg_style);
                var sel = "#edit_template #template_val_bg_color_{0}".format(i);
                $(sel).val(val.bg_color);
                new jscolor.color($(sel)[0]);
            }
            else if( arg.type == 'video' )
            {
                var sel = "#edit_template #template_val_drop_{0}".format(i);
                fillArtistFileIdSelect(sel,'VIDEO',val.file_id);
            }
        }
        else
        {
            if( arg.type == 'string' || arg.type == 'textbox' )
            {
                var sel = "#edit_template #template_val_{0}".format(i);
                $(sel).val("");
            }
            else if( arg.type == 'image_spec' )
            {
                var sel = "#edit_template #template_val_drop_{0}".format(i);
                fillArtistFileIdSelect(sel,'IMAGE',false);
                var sel = "#edit_template #template_val_bg_style_{0}".format(i);
                $(sel).val('STRETCH');
                var sel = "#edit_template #template_val_bg_color_{0}".format(i);
                $(sel).val('000000');
                new jscolor.color($(sel)[0]);
            }
            else if( arg.type == 'video' )
            {
                var sel = "#edit_template #template_val_drop_{0}".format(i);
                fillArtistFileIdSelect(sel,'VIDEO',false);
            }
        }
    }
    showPopup('#edit_template');
    return false;
}

function onEditTemplateSubmit()
{
    var template_name = $('#edit_template #template_name').val();
    
    if( !template_name || template_name.length == 0 )
    {
        window.alert("Please enter a name for your template.");
        return;
    }
    
    var params = {};
    
    var template = g_templateList[g_templateIndex];
    var schema = TEMPLATE_SCHEMA[template.type];
    
    for( var i = 0 ; i < schema.arg_list.length ; ++i )
    {
        var arg = schema.arg_list[i];
        var name = arg.name;
        
        if( arg.type == 'string' || arg.type == 'textbox' )
        {
            var sel = "#edit_template #template_val_{0}".format(i);
            var val = $(sel).val();
            
            params[name] = val;
        }
        else if( arg.type == 'image_spec' )
        {
            var sel = "#edit_template #template_val_drop_{0}".format(i);
            var file_id = $(sel).val();
            
            var sel = "#edit_template #template_val_bg_style_{0}".format(i);
            var bg_style = $(sel).val();

            var sel = "#edit_template #template_val_bg_color_{0}".format(i);
            var bg_color = $(sel).val();
            
            params[name] = {
                file_id: file_id,
                bg_style: bg_style,
                bg_color: bg_color
            };
        }
        else if( arg.type == 'video' )
        {
            var sel = "#edit_template #template_val_drop_{0}".format(i);
            var file_id = $(sel).val();
            params[name] = {
                file_id: file_id
            };
        }
    }
    
    showProgress("Updating template.");
    
    var args = {
        'artist_id': g_artistId,
        'id': template.id,
        'name': template_name,
        'params_json': JSON.stringify(params)
    };
    
    jQuery.ajax(
    {
        type: 'POST',
        url:  '/manage/data/template.php',
        data: args,
        dataType: 'json',
        success: function(data) 
        {
            g_templateList[g_templateIndex] = data.template;
            updateTemplateList();
            showSuccess("Update Success");
        },
        error: function()
        {
            showFailure("Update Failed");
        }
    });
    return false;
}

function deleteTemplate(index)
{
    
}

var TEMPLATE_SCHEMA =
{
    'PLAYER_PRINCE': {
        type: 'PLAYER',
        description: 'Prince',
        default_params: {},
        arg_list: []
    },
    'PLAYER_MEEK_SPLASH': {
        type: 'PLAYER',
        description: 'Meek Splash',
        default_params: {},
        arg_list: []
    },
    'PLAYER_MEEK_VIDEO': {
        type: 'PLAYER',
        description: 'Meek Video',
        default_params:
        {
            countdown_date: "Sat, 06 Apr 2013 00:00:00 GMT"
        },
        arg_list:
        [
            {
                name: 'countdown_date',
                description: 'Countdown Date',
                type: 'string'
            },
            {
                name: 'bg_file',
                description: 'Background Image',
                type: 'image_spec'
            },
            {
                name: 'video_file',
                description: 'Video File',
                type: 'video'
            }
        ]
    },
    'PLAYER_MEEK_STREAM': {
        type: 'PLAYER',
        description: 'Meek Live Stream',
        default_params: {
            top_bar_title: 'WATCH MEEK LIVE @ TOWER THEATER',
            iphone_title1: 'WATCH MEEK MILL LIVE @ TOWER THEATER',
            iphone_title2: 'CLICK BUTTON BELOW TO START REPLAY'
        },
        arg_list:
        [
            {
                name: 'top_bar_title',
                description: 'Top Bar Title',
                type: 'string'
            },
            {
                name: 'iframe_html',
                description: 'IFrame HTML',
                type: 'textbox'
            },
            {
                name: 'bg_file',
                description: 'Background Image',
                type: 'image_spec'
            },
            {
                name: 'iphone_title1',
                description: 'iPhone Title 1',
                type: 'string'
            },
            {
                name: 'iphone_title2',
                description: 'iPhone Title 2',
                type: 'string'
            }
        ]
    }
    
};

