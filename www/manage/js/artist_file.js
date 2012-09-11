
function artistFileReady()
{
    $('body').bind('dragenter',onDragEnter);
    $('#drop_file_overlay').bind('dragleave',onDragLeave);
    $('#drop_file_overlay').bind('dragover',onDragOver);
    $('#drop_file_overlay').bind('drop',onDrop);
}
$(document).ready(artistFileReady);

function updateFileList()
{
    $('#file_list').empty();
    
    for( var i = 0 ; i < g_fileList.length ; ++i )
    {
        var file = g_fileList[i];
        
        var filename = file.upload_filename;
        if( !filename )
            filename = file.filename;
        
        var odd = "";
        if( i % 2 == 0 )
            odd = " odd";
        
        var html = "";
        html += "<div class='item{0}'>".format(odd);
        
        if( file.is_uploading )
        {
            var sel = "upload_file_{0}".format(file.upload_index);
        
            
            if( file.upload_status == 'uploading'
               || file.upload_status == 'processing' )
            {
                var width = file.upload_progress.toFixed(4);
                
                var msg = "Uploading&hellip;";
                if( file.upload_status == 'processing' )
                    msg = "Processing&hellip;"
            
                html += "<div class='icon {0}'></div>".format(file.type);
                html += "<div id='{0}' class='file_status'>".format(sel);
                html += " <div class='file'>{0}</div>".format(filename);
                html += " <div class='status'>";
                html += "  <div class='upload_bar'>";
                html += "   <div id='upload_progress_bar' class='upload_progress_bar' style='width: {0}%'></div>".format(width);
                html += "   <div class='upload_label'>{0}</div>".format(msg);
                html += "   <div class='percent'><span id='upload_percent'>0</span>%</div>";
                html += "  </div>";
                html += " </div>";
                html += "</div>";
                html += "<div class='delete'>";
                html += " <div class='button' onclick='cancelUploadFile({0});'></div>".format(i);
                html += "</div>"
            }
            else if( file.upload_status == 'failed' )
            {
                html += "<div class='icon {0} error'></div>".format(file.type);
                html += "<div id='{0}' class='file_status'>".format(sel);
                html += " <div class='file'>{0}</div>".format(filename);
                html += " <div class='status'>";
                html += "  <div class='error'>Upload Failed!</div>";
                html += " </div>";
                html += "</div>";
                html += "<div class='delete'>";
                html += " <div class='button' onclick='removeUploadFile({0});'></div>".format(i);
                html += "</div>";
            }
        }
        else
        {
            if( file.error.length > 0 )
            {
                html += "<div class='icon {0} error'></div>".format(file.type);
                html += "<div id='{0}' class='file_status'>".format(sel);
                html += " <div class='file link' onclick='showFileDetail({0});'>{1}</div>".format(i,filename);
                html += " <div class='status'>";
                html += "  <div class='error link' onclick='showFileDetail({0});'>{1}</div>".format(i,file.error);
                html += " </div>";
                html += "</div>";
                html += "<div class='delete'>";
                html += " <div class='button' onclick='removeUploadFile({0});'></div>".format(i);
                html += "</div>";
            }
            else
            {
                html += "<div class='icon {0}'></div>".format(file.type);
                html += "<div class='filename'>{0}</div>".format(filename);
                html += "<div class='delete'>";
                html += " <div class='button' onclick='deleteFile({0});'></div>".format(i);
                html += "</div>";
            }
        }
        html += "</div>";
        
        $('#file_list').append(html);
    }
}
function showFileDetail(index)
{
    var file = g_fileList[index];
    
    $('#file_detail #filename').html(file.upload_filename);
    if( file.error.length > 0 )
    {
        $('#file_detail #error').html(file.error);
        $('#file_detail #error').show();
    }
    else
    {
        $('#file_detail #error').hide();
    }
    showPopup('#file_detail');
    return false;
}

function updateFileListItem(file)
{
    var percent = file.upload_progress;
    var width = percent.toFixed(4);

    var sel = "#upload_file_{0}".format(file.upload_index);
    var style = "{0}%".format(width);
    
    $(sel).find('#upload_progress_bar').css("width",style);
    $(sel).find('#upload_percent').html(percent.toFixed());
}
function deleteFile(i)
{
    var ret = window.confirm("Are you sure you want to delete this file?");
    if( !ret )
        return false;

    var file = g_fileList[i];
    
    var args = {
        artist_id: g_artistId,
        method: "DELETE",
        file_id: file.id
    };
    
    jQuery.ajax(
    {
        type: 'POST',
        url: '/manage/data/artist_file.php',
        data: args,
        dataType: 'json',
        success: function(data) 
        {
            if( data['success'] )
            {
                g_fileList.splice(i,1);
                updateFileList();
            }
        },
        error: function()
        {
        }
    });
}
function cancelUploadFile(i)
{
    var ret = window.confirm("Are you sure you cancel this upload?");
    if( !ret )
        return false;

    var file = g_fileList[i];
    var xhr = file.xhr;

    if( xhr )
    {
        xhr.abort();
        g_uploadCount--;
        g_backgroundCount--;
    }
    g_fileList.splice(i,1);
    updateFileList();
}

function showAddArtistFilePopup()
{
    clearFileElement('#add_artist_file #file');
    $('#add_artist_file #artist_id').val(g_artistId);
    
    showPopup('#add_artist_file');
    return false;
}

function onAddArtistFile()
{
    var file_input = $('#add_artist_file #file')[0];
    if( !file_input || !file_input.value || file_input.value.length == 0 )
    {
        window.alert("Please select a file for upload.");
        return false;
    }
    
    return startFileUpload(file_input.files[0]);
}

var g_uploadIndex = 0;
function startFileUpload(file_obj)
{
    try
    {
        var xhr = new XMLHttpRequest();
        var file = {
            xhr: xhr,
            upload_filename: file_obj.name,
            upload_progress: 0.0,
            upload_status: 'uploading',
            is_uploading: true,
            type: 'MISC',
            upload_index: g_uploadIndex++
        };
    
        function makeCallback(callback)
        {
            return function(evt) { callback(evt,file); }
        }
        xhr.onreadystatechange = makeCallback(onArtistFileReadyStateChange)
        var upload = xhr.upload;
        if( upload )
        {
            upload.addEventListener('progress',makeCallback(onArtistFileUploadProgress),false);
            upload.addEventListener('load',makeCallback(onArtistFileUploadDone),false);
            upload.addEventListener('error',makeCallback(onArtistFileUploadFailed),false);
        }
        
        var form_data = new FormData();
        form_data.append('artist_id',g_artistId);
        form_data.append('file',file_obj);
        form_data.append('ajax','1');
        
        var url = "/manage/data/artist_file.php";
        
        xhr.open("POST",url);
        xhr.send(form_data);

        g_backgroundCount++;
        g_uploadCount++;
        
        closePopup();
        g_fileList.unshift(file);
        updateFileList();
        return false;
    }
    catch(e)
    {
        showProgress();
        return true;
    }
}

function onArtistFileUploadProgress(evt,file)
{
    if( evt.lengthComputable )
    {
        var percentage = evt.loaded / evt.total * 100.0;
        file.upload_progress = percentage;
        updateFileListItem(file);
    }
    else
    {
        console.log("progress event but can't calculate percent");
    }
}

function onArtistFileUploadDone(evt,file)
{
    g_uploadCount--;
    file.upload_status = 'processing';
    updateFileList();
}

function onArtistFileUploadFailed(evt,file)
{
    g_uploadCount--;
    file.upload_status = 'failed';
    updateFileList();
}

function onArtistFileReadyStateChange(evt,file)
{
    var xhr = file.xhr;
    if( xhr.readyState == 4 )
    {
        g_backgroundCount--;
        var status_code = xhr.status;
        var text = xhr.responseText;
        try
        {
            if( status_code == 200 && text.length > 0 )
            {
                var data = JSON.parse(text);
                if( 'upload_error' in data )
                {
                    file.upload_status = 'failed';
                    updateFileList();
                }
                else
                {
                    var new_file = data.file;
                    
                    file.filename = new_file.filename;
                    file.is_uploading = false;
                    file.upload_filename = new_file.upload_filename;
                    file.id = new_file.id;
                    file.type = new_file.type;
                    updateFileList();
                }
            }
            else
            {
                file.upload_status = 'failed';
                updateFileList();
            }
        }
        catch(e)
        {
            file.upload_status = 'failed';
            updateFileList();
        }
    }
}

function iterableContains(array,val)
{
    for( var i = 0 ; i < array.length ; ++i )
        if( array[i] == val )
            return true;
    return false;
}

function onDragEnter(je)
{
    var evt = je.originalEvent;

    evt.stopPropagation();
    evt.preventDefault();
    var dt = evt.dataTransfer;
    if( dt && dt.types && iterableContains(dt.types,'Files') )
    {
        $('#drop_file_overlay').fadeIn('fast');
    }
}
function onDragLeave(je)
{
    var evt = je.originalEvent;
    evt.stopPropagation();
    evt.preventDefault();
    if( evt.pageX < 10 || evt.pageY < 10 || $(window).width() - evt.pageX < 10  || $(window).height - evt.pageY < 10 )
    {
        $('#drop_file_overlay').fadeOut('fast');
    }
}
function onDragOver(je)
{
    var evt = je.originalEvent;
    evt.stopPropagation();
    evt.preventDefault();
}
function onDrop(je)
{
    var evt = je.originalEvent;
    evt.stopPropagation();
    evt.preventDefault();
    $('#drop_file_overlay').fadeOut('fast');
    
    var files = evt.dataTransfer.files;
    for( var i = 0 ; i < files.length ; ++i )
    {
        var file = files[i];
        startFileUpload(file);
    }
    if( files.length > 0 )
    {
        if( $('#adminblock .filelist .heading').next().is(':hidden') )
        {
			$('.heading').removeClass('active').next().slideUp();
			$('#adminblock .filelist .heading').toggleClass('active').next().slideDown();
		}
    }
}

function getArtistFiles(type)
{
    var ret = [];

    for( var i = 0 ; i < g_fileList.length ; ++i )
    {
        var file = g_fileList[i];
        
        if( file.is_uploading )
            continue;
        
        if( file.error.length > 0 )
            continue;
        
        if( type == 'ALL' || file.type == type )
            ret.push(file);
    }
    return ret;
}

function fillArtistFileSelect(sel,type,current_val)
{
    var possible_files = getArtistFiles(type);
    
    $(sel).empty();
    
    var html = "<option value=''>None</option>";
    $(sel).append(html);
    
    var found_current = false;
    
    for( var i = 0 ; i < possible_files.length ; ++i )
    {
        var file = possible_files[i];
        
        var val = file.filename;
        var vis = file.upload_filename;
        
        var selected = "";
        if( val == current_val )
        {
            selected = "selected='selected'";
            found_current = true;
        }
        
        var html = "<option value='{0}' {1}>{2}</option>".format(val,selected,vis);
        $(sel).append(html);
    }
    
    if( !found_current && current_val != false )
    {
        var html = "<option value='{0}' selected='selected'>{0}</option>".format(current_val);
        $(sel).append(html);
    }
    
    var html = "<option value='upload_new_file'>Upload New File</option>";
    $(sel).append(html);
}

function artistFileDropChange(el)
{
    var val = $(el).val();
    
    if( val == 'upload_new_file' )
    {
        showAddArtistFilePopup();
    }
}

function artistFilenameToId(filename)
{
    for( var i = 0 ; i < g_fileList.length ; ++i )
    {
        var file = g_fileList[i];
        
        if( file.filename == filename )
            return file.id;
    }
    return false;
}
