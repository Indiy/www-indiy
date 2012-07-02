

var g_productIndex = false;
var g_digitalDownloadRemoveList = "";

function showProductPopup(product_index)
{
    g_productIndex = product_index;
    g_removeSong = false;
    g_removeImage = false;
    g_digitalDownloadRemoveList = "";
    
    $('#edit_product #artist_id').val(g_artistId);
    clearFileElement('#edit_product #product_image');
    clearFileElement('#edit_product #digital_download1');

    if( product_index !== false )
    {
        var product = g_productList[product_index];
        
        $('#edit_product #product_id').val(product.id);
        $('#edit_product #name').val(product.name);
        if( product.image )
        {
            var html = "<img src='{0}' />".format(product.image);
            html += "<button onclick='return onImageRemove();'></button>";
            $('#edit_product #product_image_container').html(html);
        }
        else
        {
            $('#edit_product #product_image_container').empty();
        }
        
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
        
        if( product.digital_downloads && product.digital_downloads.length > 0 )
        {
            $('#edit_product #downloads').empty();
            for( var i = 0 ; i < product.digital_downloads.length ; ++i )
            {
                var file = product.digital_downloads[i];
                var html = "";
                html += "<div class='file' id='file_{0}'>".format(i);
                html += " <div class='name'>{0}</div>".format(file.upload_filename);
                html += " <button onclick='return removeDigitalFile({0});'></button>".format(i);
                html += "</div>";
                $('#edit_product #downloads').append(html);
            }
        }
        else
        {
            $('#edit_product #downloads').html("None");
        }
        
        clickProductType(product.type);
    }
    else
    {
        $('#edit_product #product_id').val("");
        $('#edit_product #name').val("");
        $('#edit_product #product_image_container').empty();
        $('#edit_product #description').val("");
        $('#edit_product #price').val("");
        $('#edit_product #shipping').val("0.00");
        $('#edit_product #size').val("");
        $('#edit_product #color').val("");
        $('#edit_product #downloads').html("None");
        
        $('#edit_product input[name=product_type]:eq(0)').attr('checked','checked');
        clickProductType("DIGITAL");
        
    }
    showPopup('#edit_product');
    return false;
}

function onAddProductSubmit()
{
    var needs_image = false;
    if( g_productIndex === false )
    {
        needs_image = true;
    }
    else
    {
        var product  = g_productList[g_productIndex];
        if( !product.image )
            needs_image = true;
    }

    var name = $('#edit_product #name').val();
    if( name.length == 0 )
    {
        window.alert("Please enter a name for your product.");
        return false;
    }
    var price = parseFloat($('#edit_product #price').val());
    if( ! ( price > 0.0 ) )
    {
        window.alert("Please enter a price for your product, must be greater than $0.00.");
        return false;
    }
    
    var product_image = $('#edit_product #product_image')[0];
    if( needs_image && ( !product_image || !product_image.value || product_image.value.length == 0 ) )
    {
        window.alert("Please upload an image for your product.");
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
        form_data.append('remove_digital_downloads',g_digitalDownloadRemoveList);
        form_data.append('ajax',true);
        
        var product_image = $('#edit_product #product_image')[0];
        if( product_image.files && product_image.files.length > 0 )
        {
            form_data.append('file',product_image.files[0]);
        }
        
        if( type == 'DIGITAL' )
        {
            var digital_download1 = $('#edit_product #digital_download1')[0];
            if( digital_download1.files && digital_download1.files.length > 0 )
            {
                form_data.append('digital_download1',digital_download1.files[0]);
            }
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
    var product = g_productList[g_productIndex];
    var file = product.digital_downloads[index];
    var id = file.id;
    $('#edit_product #downloads #file_' + index).hide();
    if( g_digitalDownloadRemoveList.length > 0 )
        g_digitalDownloadRemoveList += ",";
    g_digitalDownloadRemoveList += id;
    return false;
}



