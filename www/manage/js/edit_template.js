
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
        else if( arg.type == 'video' || arg.type == 'image' || arg.type == 'misc_file' )
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
                fillArtistFileIdSelect(sel,'IMAGE',val.image_file_id);
                var sel = "#edit_template #template_val_bg_style_{0}".format(i);
                $(sel).val(val.bg_style);
                var sel = "#edit_template #template_val_bg_color_{0}".format(i);
                $(sel).val(val.bg_color);
                new jscolor.color($(sel)[0]);
            }
            else if( arg.type == 'video' )
            {
                var sel = "#edit_template #template_val_drop_{0}".format(i);
                fillArtistFileIdSelect(sel,'VIDEO',val.video_file_id);
            }
            else if( arg.type == 'image' )
            {
                var sel = "#edit_template #template_val_drop_{0}".format(i);
                fillArtistFileIdSelect(sel,'IMAGE',val.image_file_id);
            }
            else if( arg.type == 'misc_file' )
            {
                var sel = "#edit_template #template_val_drop_{0}".format(i);
                fillArtistFileIdSelect(sel,'ALL',val.misc_file_id);
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
            else if( arg.type == 'image' )
            {
                var sel = "#edit_template #template_val_drop_{0}".format(i);
                fillArtistFileIdSelect(sel,'IMAGE',false);
            }
            else if( arg.type == 'misc_file' )
            {
                var sel = "#edit_template #template_val_drop_{0}".format(i);
                fillArtistFileIdSelect(sel,'ALL',false);
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
                image_file_id: file_id,
                bg_style: bg_style,
                bg_color: bg_color
            };
        }
        else if( arg.type == 'video' )
        {
            var sel = "#edit_template #template_val_drop_{0}".format(i);
            var file_id = $(sel).val();
            params[name] = {
                video_file_id: file_id
            };
        }
        else if( arg.type == 'image' )
        {
            var sel = "#edit_template #template_val_drop_{0}".format(i);
            var file_id = $(sel).val();
            params[name] = {
                image_file_id: file_id
            };
        }
        else if( arg.type == 'misc_file' )
        {
            var sel = "#edit_template #template_val_drop_{0}".format(i);
            var file_id = $(sel).val();
            params[name] = {
                misc_file_id: file_id
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
    var template = g_templateList[index];

    var args = {
        method: 'DELETE',
        id: template.id
    };
    
    jQuery.ajax(
    {
        type: 'POST',
        url:  '/manage/data/template.php',
        data: args,
        dataType: 'json',
        success: function(data) 
        {
            g_templateList.splice(index,1);
            updateTemplateList();
        },
        error: function()
        {
        }
    });
    return false;
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
        description: 'Countdown Splash Form w/ Download',
        default_params:
        {
            page_title: "#DC3",
            title: "TITLE",
            subtitle1: "SUBTITLE",
            subtitle2: "SUBTITLE2",
            countdown_date: "Web, 01 May 2013 04:00:00 GMT"
        },
        arg_list:
        [
            {
                name: 'page_title',
                description: 'Page Title',
                type: 'string'
            },
            {
                name: 'title',
                description: 'Title',
                type: 'string'
            },
            {
                name: 'subtitle1',
                description: 'Subtitle 1',
                type: 'string'
            },
            {
                name: 'subtitle2',
                description: 'Subtitle 2',
                type: 'string'
            },
            {
                name: 'button_text',
                description: 'Button Text',
                type: 'string'
            },
            {
                name: 'success_text',
                description: 'Success Text',
                type: 'string'
            },
            {
                name: 'copyright',
                description: 'Copyright Text',
                type: 'string'
            },
            {
                name: 'banner_left',
                description: 'Banner Left',
                type: 'string'
            },
            {
                name: 'banner_right1',
                description: 'Banner Right1',
                type: 'string'
            },
            {
                name: 'banner_right2',
                description: 'Banner Right2',
                type: 'string'
            },
            {
                name: 'logo_image',
                description: 'Logo Image',
                type: 'image'
            },
            {
                name: 'banner_image',
                description: 'Banner Image',
                type: 'image'
            },
            {
                name: 'form_tag',
                description: 'Form Tag',
                type: 'string'
            },
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
                name: 'file_download',
                description: 'File Download',
                type: 'misc_file'
            }

        ]
    },
    'PLAYER_SPLASH_FORM_DOWNLOAD': {
        type: 'PLAYER',
        description: 'Splash Form w/ Download',
        default_params:
        {
            page_title: "#DC3",
            top_title: "TOP TITLE",
            title: "TITLE",
            subtitle1: "SUBTITLE",
            subtitle2: "SUBTITLE2",
            ga_account_id: 'UA-15194524-1'
        },
        arg_list:
        [
            {
                name: 'page_title',
                description: 'Page Title',
                type: 'string'
            },
            {
                name: 'top_title',
                description: 'Top Title',
                type: 'string'
            },
            {
                name: 'title',
                description: 'Title',
                type: 'string'
            },
            {
                name: 'subtitle1',
                description: 'Subtitle 1',
                type: 'string'
            },
            {
                name: 'subtitle2',
                description: 'Subtitle 2',
                type: 'string'
            },
            {
                name: 'button_text',
                description: 'Button Text',
                type: 'string'
            },
            {
                name: 'success_text',
                description: 'Success Text',
                type: 'string'
            },
            {
                name: 'copyright',
                description: 'Copyright Text',
                type: 'string'
            },
            {
                name: 'banner_left',
                description: 'Banner Left',
                type: 'string'
            },
            {
                name: 'banner_right1',
                description: 'Banner Right1',
                type: 'string'
            },
            {
                name: 'banner_right2',
                description: 'Banner Right2',
                type: 'string'
            },
            {
                name: 'banner_image',
                description: 'Banner Image',
                type: 'image'
            },
            {
                name: 'form_tag',
                description: 'Form Tag',
                type: 'string'
            },
            {
                name: 'bg_file',
                description: 'Background Image',
                type: 'image_spec'
            },
            {
                name: 'file_download',
                description: 'File Download',
                type: 'misc_file'
            },
            {
                name: 'ga_account_id',
                description: 'Google Analytics Id',
                type: 'string'
            }
        ]
    },
    'PLAYER_MEEK_VIDEO': {
        type: 'PLAYER',
        description: 'Countdown Video Splash',
        default_params:
        {
            page_title: "meekmill.com",
            top_title1: "MEEK MILL LIVE FROM PHILADELPHIA",
            top_title2: "FRIDAY, APRIL 5TH @8PM EST",
            top_subtitle: "ONLY ON MEEKMILL.COM",
            bottom_subtitle: "COUNTDOWN TO LIVE STREAM FEED",
            bottom_title: "#MEEKLIVE",
            countdown_date: "Web, 01 May 2013 04:00:00 GMT"
        },
        arg_list:
        [
            {
                name: 'page_title',
                description: 'Page Title',
                type: 'string'
            },
            {
                name: 'top_title1',
                description: 'Top Title 1',
                type: 'string'
            },
            {
                name: 'top_title2',
                description: 'Top Title 2',
                type: 'string'
            },
            {
                name: 'top_subtitle',
                description: 'Top Subtitle',
                type: 'string'
            },
            {
                name: 'bottom_subtitle',
                description: 'Bottom Subtittle',
                type: 'string'
            },
            {
                name: 'bottom_title',
                description: 'Bottom Title',
                type: 'string'
            },
            {
                name: 'copyright',
                description: 'Copyright Text',
                type: 'string'
            },
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
            page_title: '#MEEKLIVE',
            top_bar_title: 'WATCH MEEK LIVE @ TOWER THEATER',
            iphone_title1: 'WATCH MEEK MILL LIVE @ TOWER THEATER',
            iphone_title2: 'CLICK BUTTON BELOW TO START REPLAY',
            ga_account_id: 'UA-15194524-1'
        },
        arg_list:
        [
            {
                name: 'page_title',
                description: 'Page Title',
                type: 'string'
            },
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
                name: 'desktop_bg_top_line',
                description: 'Desktop BG Top Line',
                type: 'string'
            },
            {
                name: 'desktop_bg_blue_line',
                description: 'Desktop BG Blue Line',
                type: 'string'
            },
            {
                name: 'desktop_bg_bottom_line',
                description: 'Desktop BG Bottom Line',
                type: 'string'
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
            },
            {
                name: 'iphone_title3',
                description: 'iPhone Title 3',
                type: 'string'
            },
            {
                name: 'iphone_title4',
                description: 'iPhone Title 4',
                type: 'string'
            },
            {
                name: 'twitter_handle',
                description: 'Twitter Handle',
                type: 'string'
            },
            {
                name: 'twitter_widget',
                description: 'Twitter Widget',
                type: 'textbox'
            },
            {
                name: 'bg_file',
                description: 'Background Image',
                type: 'image_spec'
            },
            {
                name: 'ga_account_id',
                description: 'Google Analytics Id',
                type: 'string'
            }
        ]
    },
    'PLAYER_MAD_STREAM': {
        type: 'PLAYER',
        description: 'MAD Live Stream',
        default_params: {
            page_title: 'MAD Steam',
            top_bar_title: 'MAD STREAM!',
            iphone_title1: 'MAD STREAM!',
            iphone_title2: 'MAD STREAM ROCKS!',
            ga_account_id: 'UA-15194524-1'
        },
        arg_list:
        [
            {
                name: 'page_title',
                description: 'Page Title',
                type: 'string'
            },
            {
                name: 'top_bar_title',
                description: 'Top Bar Title',
                type: 'string'
            },
            {
                name: 'hds_url',
                description: 'HDS URL',
                type: 'string'
            },
            {
                name: 'hls_url',
                description: 'HLS URL',
                type: 'string'
            },
            {
                name: 'desktop_bg_top_line',
                description: 'Desktop BG Top Line',
                type: 'string'
            },
            {
                name: 'desktop_bg_blue_line',
                description: 'Desktop BG Blue Line',
                type: 'string'
            },
            {
                name: 'desktop_bg_bottom_line',
                description: 'Desktop BG Bottom Line',
                type: 'string'
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
            },
            {
                name: 'iphone_title3',
                description: 'iPhone Title 3',
                type: 'string'
            },
            {
                name: 'iphone_title4',
                description: 'iPhone Title 4',
                type: 'string'
            },
            {
                name: 'twitter_handle',
                description: 'Twitter Handle',
                type: 'string'
            },
            {
                name: 'twitter_widget',
                description: 'Twitter Widget',
                type: 'textbox'
            },
            {
                name: 'bg_file',
                description: 'Background Image',
                type: 'image_spec'
            },
            {
                name: 'ga_account_id',
                description: 'Google Analytics Id',
                type: 'string'
            }
        ]
    },
    'PLAYER_SPLASH_AUDIO': {
        type: 'PLAYER',
        description: 'Splash with Audio Player',
        default_params:
        {
            page_title: "MyArtistDNA. Be Seen, Be Heard, Be Independant.",
            top_title1: "TOP TITLE 1 HERE",
            top_title2: "TOP TITLE 2 HERE",
            bottom_title1: "BOTTOM TITLE 1",
        },
        arg_list:
        [
            {
                name: 'page_title',
                description: 'Page Title',
                type: 'string'
            },
            {
                name: 'top_title1',
                description: 'Top Title 1',
                type: 'string'
            },
            {
                name: 'top_title2',
                description: 'Top Title 2',
                type: 'string'
            },
            {
                name: 'bottom_title1',
                description: 'Bottom Title 1',
                type: 'string'
            },
            {
                name: 'copyright',
                description: 'Copyright Text',
                type: 'string'
            },
            {
                name: 'album_image',
                description: 'Album Image',
                type: 'image'
            },

        ]
    },
    'PLAYER_COUNTDOWN_AUDIO': {
        type: 'PLAYER',
        description: 'Countdown with Audio Player',
        default_params:
        {
            page_title: "MyArtistDNA. Be Seen, Be Heard, Be Independant.",
            top_title1: "TOP TITLE 1 HERE",
            top_title2: "TOP TITLE 2 HERE",
            top_subtitle: "TOP SUB TITLE",
            bottom_subtitle: "BOTTOM SUB TITLE",
            bottom_title: "BOTTOM TITLE",
            countdown_date: "Web, 01 May 2014 04:00:00 GMT"
        },
        arg_list:
        [
            {
                name: 'page_title',
                description: 'Page Title',
                type: 'string'
            },
            {
                name: 'top_title1',
                description: 'Top Title 1',
                type: 'string'
            },
            {
                name: 'top_title2',
                description: 'Top Title 2',
                type: 'string'
            },
            {
                name: 'top_subtitle',
                description: 'Top Subtitle',
                type: 'string'
            },
            {
                name: 'bottom_subtitle',
                description: 'Bottom Subtittle',
                type: 'string'
            },
            {
                name: 'bottom_title',
                description: 'Bottom Title',
                type: 'string'
            },
            {
                name: 'copyright',
                description: 'Copyright Text',
                type: 'string'
            },
            {
                name: 'countdown_date',
                description: 'Countdown Date',
                type: 'string'
            },
            {
                name: 'album_image',
                description: 'Album Image',
                type: 'image'
            }
        ]
    },
    'PLAYER_SPLASH_VIDEO': {
        type: 'PLAYER',
        description: 'Splash with Video',
        default_params:
        {
            page_title: "meekmill.com",
            top_title1: "TOP TITLE 1",
            top_title2: "TOP TITLE 2",
            top_subtitle: "TOP SUBTITLE",
            bottom_subtitle1: "BOTTOM SUBTITLE 1",
            bottom_subtitle2: "BOTTOM SUBTITLE 2",
            bottom_title1: "BOTTOM TITLE 1",
            bottom_title2: "BOTTOM TITLE 2",
            ga_account_id: 'UA-15194524-1'
        },
        arg_list:
        [
            {
                name: 'page_title',
                description: 'Page Title',
                type: 'string'
            },
            {
                name: 'top_title1',
                description: 'Top Title 1',
                type: 'string'
            },
            {
                name: 'top_title2',
                description: 'Top Title 2',
                type: 'string'
            },
            {
                name: 'top_subtitle',
                description: 'Top Subtitle',
                type: 'string'
            },
            {
                name: 'bottom_subtitle1',
                description: 'Bottom Subtittle 1',
                type: 'string'
            },
            {
                name: 'bottom_subtitle2',
                description: 'Bottom Subtittle 2',
                type: 'string'
            },
            {
                name: 'bottom_title1',
                description: 'Bottom Title 1',
                type: 'string'
            },
            {
                name: 'bottom_title2',
                description: 'Bottom Title 2',
                type: 'string'
            },
            {
                name: 'copyright',
                description: 'Copyright Text',
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
            },
            {
                name: 'ga_account_id',
                description: 'Google Analytics Id',
                type: 'string'
            }
        ]
    },
    'PLAYER_DEFAULT_V2': {
        type: 'PLAYER',
        description: 'Default v2',
        default_params: {
            page_title: 'MyArtistDNA | Be Heard. Be Seen. Be Independent.',
            footer_left: 'BE HEARD. BE SEEN. BE INDEPENDENT.',
            footer_right: '&copy;2012 Powered by <a href="http://myartistdna.com">MyArtistDNA</a>',
            iphone_top_text: '#MAYBACHMUSICTV',
            iphone_bottom_text: '#SELFMADE3',
            ga_account_id: ''
        },
        arg_list:
        [
            {
                name: 'page_title',
                description: 'Page Title',
                type: 'string'
            },
            {
                name: 'top_bar_image',
                description: 'Top Bar Image',
                type: 'image'
            },
            {
                name: 'top_bar_title',
                description: 'Top Bar Title',
                type: 'string'
            },
            {
                name: 'bottom_left_text',
                description: 'Bottom Left Text',
                type: 'textbox'
            },
            {
                name: 'media_button_text',
                description: 'Media Button Text',
                type: 'string'
            },
            {
                name: 'footer_left',
                description: 'Footer Left Text',
                type: 'string'
            },
            {
                name: 'footer_right',
                description: 'Footer Right Text',
                type: 'string'
            },
            {
                name: 'iphone_top_text',
                description: 'iPhone Top Text',
                type: 'string'
            },
            {
                name: 'iphone_bottom_text',
                description: 'iPhone Bottom Text',
                type: 'string'
            },
            {
                name: 'twitter_widget',
                description: 'Twitter Widget',
                type: 'textbox'
            },
            {
                name: 'facebook_widget',
                description: 'Facebook Widget',
                type: 'textbox'
            },
            {
                name: 'tracker_bar_texture',
                description: 'Tracker Bar Texture',
                type: 'image'
            },
            {
                name: 'ga_account_id',
                description: 'Google Analytics Id',
                type: 'string'
            }
        ]
    },
    'PLAYER_DEFAULT_V3': {
        type: 'PLAYER',
        description: 'Default v3',
        default_params: {
            page_title: 'MyArtistDNA | Be Heard. Be Seen. Be Independent.',
            footer_left: 'BE HEARD. BE SEEN. BE INDEPENDENT.',
            footer_right: '&copy;2012 Powered by <a href="http://myartistdna.com">MyArtistDNA</a>',
            iphone_top_text: '#MAYBACHMUSICTV',
            iphone_bottom_text: '#SELFMADE3',
            show_social_feed_text: '+ SHOW SOCIAL FEED',
            hide_social_feed_text: '- HIDE SOCIAL FEED',
            show_store_text: '+ SHOW STORE',
            hide_store_text: '- HIDE STORE',
            playlist_artist_text: 'ARTISTS',
            playlist_playlists_text: 'PLAYLISTS',
            playlist_media_text: 'MEDIA',
            ga_account_id: ''
        },
        arg_list:
        [
            {
                name: 'page_title',
                description: 'Page Title',
                type: 'string'
            },
            {
                name: 'top_bar_image',
                description: 'Top Bar Image',
                type: 'image'
            },
            {
                name: 'top_bar_title',
                description: 'Top Bar Title',
                type: 'string'
            },
            {
                name: 'bottom_left_text',
                description: 'Bottom Left Text',
                type: 'textbox'
            },
            {
                name: 'media_button_text',
                description: 'Media Button Text',
                type: 'string'
            },
            {
                name: 'footer_left',
                description: 'Footer Left Text',
                type: 'string'
            },
            {
                name: 'footer_right',
                description: 'Footer Right Text',
                type: 'string'
            },
            {
                name: 'iphone_top_text',
                description: 'iPhone Top Text',
                type: 'string'
            },
            {
                name: 'iphone_bottom_text',
                description: 'iPhone Bottom Text',
                type: 'string'
            },
            {
                name: 'twitter_widget',
                description: 'Twitter Widget',
                type: 'textbox'
            },
            {
                name: 'facebook_widget',
                description: 'Facebook Widget',
                type: 'textbox'
            },
            {
                name: 'tracker_bar_texture',
                description: 'Tracker Bar Texture',
                type: 'image'
            },
            {
                name: 'show_social_feed_text',
                description: 'Show Social Feed',
                type: 'string'
            },
            {
                name: 'hide_social_feed_text',
                description: 'Hide Social Feed',
                type: 'string'
            },
            {
                name: 'show_store_text',
                description: 'Show Store',
                type: 'string'
            },
            {
                name: 'hide_store_text',
                description: 'Hide Store',
                type: 'string'
            },
            {
                name: 'playlist_artist_text',
                description: 'Playlist Artist Label',
                type: 'string'
            },
            {
                name: 'playlist_playlists_text',
                description: 'Playlist Playlists Label',
                type: 'string'
            },
            {
                name: 'playlist_media_text',
                description: 'Playlist Media Label',
                type: 'string'
            },
            {
                name: 'ga_account_id',
                description: 'Google Analytics Id',
                type: 'string'
            }
        ]
    },
    'PLAYER_DEFAULT_V4': {
        type: 'PLAYER',
        description: 'Default v4',
        default_params: {
            page_title: 'MyArtistDNA | Be Heard. Be Seen. Be Independent.',
            top_text: 'KID CUDI',
            bottom_text: '#thekingofpop',
            twitter_widget: '',
            footer_text: 'Powered by myartistdna',
            ga_account_id: ''
        },
        arg_list:
        [
            {
                name: 'page_title',
                description: 'Page Title',
                type: 'string'
            },
            {
                name: 'top_text',
                description: 'Top Text',
                type: 'string'
            },
            {
                name: 'bottom_text',
                description: 'Bottom Text',
                type: 'string'
            },
            {
                name: 'twitter_widget',
                description: 'Twitter Widget',
                type: 'textbox'
            },
            {
                name: 'instagram_username',
                description: 'Instagram Username',
                type: 'string'
            },
            {
                name: 'footer_text',
                description: 'Footer Text',
                type: 'string'
            },
            {
                name: 'ga_account_id',
                description: 'Google Analytics Id',
                type: 'string'
            }
        ]
    },
    'PLAYER_DEFAULT_V5': {
        type: 'PLAYER',
        description: 'Default v5',
        default_params: {
            page_title: 'MyArtistDNA | Be Heard. Be Seen. Be Independent.',
            ga_account_id: ''
        },
        arg_list:
        [
            {
                name: 'page_title',
                description: 'Page Title',
                type: 'string'
            },
            {
                name: 'ga_account_id',
                description: 'Google Analytics Id',
                type: 'string'
            }
        ]
    },
    'PLAYER_MAD_TV': {
        type: 'PLAYER',
        description: 'TV Template',
        default_params: {
            page_title: 'MyArtistDNA | Be Heard. Be Seen. Be Independent.',
            footer_left_text: 'BE HEARD. BE SEEN. BE INDEPENDENT.',
            footer_right_text: '&copy;2012 Powered by <a href="http://myartistdna.com">MyArtistDNA</a>',
            ga_account_id: ''
        },
        arg_list:
        [
            {
                name: 'page_title',
                description: 'Page Title',
                type: 'string'
            },
            {
                name: 'splash_top_text',
                description: 'Splash Top Text',
                type: 'string'
            },
            {
                name: 'splash_bottom_text',
                description: 'Splash Bottom Text',
                type: 'string'
            },
            {
                name: 'splash_bg',
                description: 'Splash Background',
                type: 'image_spec'
            },
            {
                name: 'upper_left_logo',
                description: 'Upper Left Logo',
                type: 'image'
            },
            {
                name: 'footer_left_text',
                description: 'Footer Left Text',
                type: 'string'
            },
            {
                name: 'footer_right_text',
                description: 'Footer Right Text',
                type: 'string'
            },
            {
                name: 'ga_account_id',
                description: 'Google Analytics Id',
                type: 'string'
            }
        ]
    }
};

