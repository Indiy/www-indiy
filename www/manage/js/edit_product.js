

var g_productIndex = false;
var g_digitalDownloadRemoveList = "";

var g_digitalDownloads = [];

function jsonClone(obj)
{
    return JSON.parse(JSON.stringify(obj));
}

function showProductPopup(product_index)
{
    g_productIndex = product_index;
    g_removeSong = false;
    g_removeImage = false;
    g_digitalDownloadRemoveList = "";
    
    $('#edit_product #artist_id').val(g_artistId);

    if( product_index !== false )
    {
        var product = g_productList[product_index];
        
        $('#edit_product #product_id').val(product.id);
        $('#edit_product #name').val(product.name);
        
        fillArtistFileSelect('#edit_product #image_drop','IMAGE',product.image);
        
        $('#edit_product #description').val(product.description);
        $('#edit_product #price').val(product.price);
        $('#edit_product #shipping').val(product.shipping);
        $('#edit_product #size').val(product.size);
        $('#edit_product #color').val(product.color);
        
        if( product.type == 'DIGITAL' )
        {
            $('#edit_product input[name=product_type]:eq(0)').attr('checked','checked');
        }
        else
        {
            $('#edit_product input[name=product_type]:eq(1)').attr('checked','checked');
        }
        
        g_digitalDownloads = jsonClone(product.digital_downloads);
        
        renderDigitalDownloads();
        fillArtistFileSelect('#edit_product #dd_drop','ALL',false);
        
        clickProductType(product.type);
    }
    else
    {
        $('#edit_product #product_id').val("");
        $('#edit_product #name').val("");
        $('#edit_product #description').val("");
        $('#edit_product #price').val("");
        $('#edit_product #shipping').val("0.00");
        $('#edit_product #size').val("");
        $('#edit_product #color').val("");
        $('#edit_product #downloads').html("None");
        
        $('#edit_product input[name=product_type]:eq(0)').attr('checked','checked');
        clickProductType("DIGITAL");

        fillArtistFileSelect('#edit_product #image_drop','IMAGE',false);
        fillArtistFileSelect('#edit_product #dd_drop','ALL',false);
        
        g_digitalDownloads = [];
        
        renderDigitalDownloads();
    }
    showPopup('#edit_product');
    return false;
}

function onAddProductSubmit()
{
    var image_drop = $('#edit_product #image_drop').val();
    if( image_drop.length == 0 )
    {
        window.alert("Please select an image for your product.");
        return false;
    }

    var name = $('#edit_product #name').val();
    if( name.length == 0 )
    {
        window.alert("Please enter a name for your product.");
        return false;
    }
    var price = parseFloat($('#edit_product #price').val());
    if( ! ( price >= 0.0 ) )
    {
        window.alert("Please enter a price for your product.");
        return false;
    }
    
    function fillProductForm(form_data)
    {
        var artist_id = $('#edit_product #artist_id').val();
        var product_id = $('#edit_product #product_id').val();
        var name = $('#edit_product #name').val();
        var description = $('#edit_product #description').val();
        var price = $('#edit_product #price').val();
        var size = $('#edit_product #size').val();
        var color = $('#edit_product #color').val();
        var type = $('#edit_product input[@name=product_type]:checked').val();
        var shipping = $('#edit_product #shipping').val();
        var image_drop = $('#edit_product #image_drop').val();
        
        form_data.append('artistid',artist_id);
        form_data.append('id',product_id);
        form_data.append('filename','');
        form_data.append('name',name);
        form_data.append('origin','');
        form_data.append('description',description);
        form_data.append('price',price);
        form_data.append('sku','');
        form_data.append('size',size);
        form_data.append('color',color);
        form_data.append('type',type);
        form_data.append('shipping',shipping);
        
        form_data.append('ajax',true);

        form_data.append('image_drop',image_drop);
        
        if( type == 'DIGITAL' )
        {
            var ret = ddFormData(form_data);
        }
    }
    
    var url = '/manage/data/product.php';
    return startAjaxUpload(url,fillProductForm,onProductSuccess);
}
function onProductSuccess(data)
{
    if( g_productIndex !== false )
    {
        g_productList[g_productIndex] = data.product_data;
    }
    else
    {
        g_productList.unshift(data.product_data);
    }
    updateStoreList();
}

function clickProductType(type)
{
    if( type == 'DIGITAL' )
    {
        $('#edit_product #type_physical').hide();
        $('#edit_product #type_digital').show();
    }
    else
    {
        $('#edit_product #type_digital').hide();
        $('#edit_product #type_physical').show();
    }
}

function removeDigitalFile(index)
{
    var file = g_digitalDownloads[index];
    file.edit_deleted = true;
    renderDigitalDownloads();
    return false;
}

function renderDigitalDownloads()
{
    $('#edit_product #downloads').empty();
    
    var num_shown = 0;

    for( var i = 0 ; i < g_digitalDownloads.length ; ++i )
    {
        var file = g_digitalDownloads[i];
        
        if( file.edit_deleted )
            continue;
            
        num_shown++;
        
        var html = "";
        html += "<div class='file' id='file_{0}'>".format(i);
        html += " <div class='name'>{0}</div>".format(file.upload_filename);
        html += " <button onclick='return removeDigitalFile({0});'></button>".format(i);
        html += "</div>";
        $('#edit_product #downloads').append(html);
    }
    
    if( num_shown == 0 )
    {
        $('#edit_product #downloads').html("None");
    }
}

function ddDropChange(el)
{
    var filename = $(el).val();
    
    if( filename == 'upload_new_file' )
    {
        showAddArtistFilePopup();
        return;
    }

    var sel = "#edit_product #dd_drop option[value='{0}']".format(filename);
    var upload_filename = $(sel).text();
    
    var file = {
        edit_new: true,
        upload_filename: upload_filename,
        filename: filename
    };
    g_digitalDownloads.push(file);
    $(el).val("None");
    renderDigitalDownloads();
}

function ddFormData(form_data)
{
    var remove_list = [];
    var add_list = [];

    for( var i = 0 ; i < g_digitalDownloads.length ; i++ )
    {
        var file = g_digitalDownloads[i];
        
        if( file.edit_deleted && !file.edit_new)
        {
            remove_list.push(file.id);
        }
        if( file.edit_new && !file.edit_deleted )
        {
            var id = artistFilenameToId(file.filename);
            if( id !== false )
                add_list.push(id);
        }
    }

    if( remove_list.length > 0 )
        form_data.append('remove_digital_downloads',remove_list.join(','));
        
    if( add_list.length > 0 )
        form_data.append('add_digital_downloads',add_list.join(','));
}
