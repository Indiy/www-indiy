

var g_productIndex = false;

function showProductPopup(product_index)
{
    g_productIndex = product_index;
    g_removeSong = false;
    g_removeImage = false;
    
    $('#edit_product #artist_id').val(g_artistId);
    clearFileElement('#edit_product #product_image');

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
        $('#edit_product #size').val(product.size);
        $('#edit_product #color').val(product.color);
    }
    else
    {
        $('#edit_product #product_id').val("");
        $('#edit_product #name').val("");
        $('#edit_product #product_image_container').empty();
        $('#edit_product #description').val("");
        $('#edit_product #price').val("");
        $('#edit_product #size').val("");
        $('#edit_product #color').val("");
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
        form_data.append('ajax',true);
        
        var product_image = $('#edit_product #product_image')[0];
        if( product_image.files && product_image.files.length > 0 )
        {
            form_data.append('file',product_image.files[0]);
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



