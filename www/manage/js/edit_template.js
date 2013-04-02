
var g_templateIndex = false;

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
            html += " <input id='template_val_{0}' type='text' value='' class='right_text' />".format(i);
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

    $('#edit_template .template_name').val(template.name);
    for( var i = 0 ; i < schema.arg_list.length ; ++i )
    {
        var arg = schema.arg_list[i];
        var name = arg.name;
        
        if( template.params && name in template.params )
        {
            var val = template.params[name];
            if( arg.type == 'string' )
            {
                var sel = "#edit_template #template_val_{0}".format(i);
                $(sel).val(val);
            }
            else if( arg.type == 'image_spec' )
            {
                var sel = "#edit_template #template_val_drop_{0}".format(i);
                fillArtistFileSelect(sel,'IMAGE',val.file_id);
                var sel = "#edit_template #template_val_bg_style_{0}".format(i);
                $(sel).val(val.bg_style);
                var sel = "#edit_template #template_val_bg_color_{0}".format(i);
                $(sel).val(val.bg_color);
                new jscolor.color($(sel)[0]);
            }
            else if( arg.type == 'video' )
            {
                var sel = "#edit_template #template_val_drop_{0}".format(i);
                fillArtistFileSelect(sel,'VIDEO',val.file_id);
            }
        }
        else
        {
            if( arg.type == 'string' )
            {
                var sel = "#edit_template #template_val_{0}".format(i);
                $(sel).val('Sat, 06 Apr 2013 00:00:00 GMT');
            }
            else if( arg.type == 'image_spec' )
            {
                var sel = "#edit_template #template_val_drop_{0}".format(i);
                fillArtistFileSelect(sel,'IMAGE',false);
                var sel = "#edit_template #template_val_bg_style_{0}".format(i);
                $(sel).val('STRETCH');
                var sel = "#edit_template #template_val_bg_color_{0}".format(i);
                $(sel).val('000000');
                new jscolor.color($(sel)[0]);
            }
            else if( arg.type == 'video' )
            {
                var sel = "#edit_template #template_val_drop_{0}".format(i);
                fillArtistFileSelect(sel,'VIDEO',false);
            }
        }
    }
    showPopup('#edit_template');
    return false;
}

function onEditTemplateSubmit()
{
    
}

var TEMPLATE_SCHEMA =
{
    'PLAYER_DEFAULT': {
        type: 'PLAYER',
        arg_list: []
    },
    'PLAYER_PRINCE': {
        type: 'PLAYER',
        arg_list: []
    },
    'PLAYER_MEEK_SPLASH': {
        type: 'PLAYER',
        arg_list: []
    },
    'PLAYER_MEEK_VIDEO': {
        type: 'PLAYER',
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
    }
};

