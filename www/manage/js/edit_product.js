

var g_productIndex = false;

function showProductPopup(product_index)
{
    $('.status_container').hide();
    $('#ajax_form').show();
    
    g_productIndex = product_index;
    g_removeSong = false;
    g_removeImage = false;
    
    $('#artist_id').val(g_artistId);
    clearFileElement('#product_image');

    if( product_index !== false )
    {
        var product = g_productList[product_index];
        
        $('#product_id').val(product.id);
        $('#name').val(product.name);
        if( product.image )
        {
            var html = "<img src='{0}' />".format(product.image);
            html += "<button onclick='return onImageRemove();'></button>";
            $('#product_image_container').html(html);
        }
        else
        {
            $('#product_image_container').empty();
        }
        
        $('#description').val(product.description);
        $('#price').val(product.price);
        $('#size').val(product.size);
        $('#color').val(product.color);
    }
    else
    {
        $('#product_id').val("");
        $('#name').val("");
        $('#product_image_container').empty();
        $('#description').val("");
        $('#price').val("");
        $('#size').val("");
        $('#color').val("");
    }
    showPopup('edit_product_wrapper');
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

    var name = $('#name').val();
    if( name.length == 0 )
    {
        window.alert("Please enter a name for your product.");
        return false;
    }
    var price = parseFloat($('#price').val());
    if( ! ( price > 0.0 ) )
    {
        window.alert("Please enter a price for your product, must be greater than $0.00.");
        return false;
    }
    
    var product_image = document.getElementById('product_image');
    if( needs_image && ( !product_image || !product_image.value || product_image.value.length == 0 ) )
    {
        window.alert("Please upload an image for your product.");
        return false;
    }
    
    function fillProductForm(form_data)
    {
        var artist_id = $('#artist_id').val();
        var product_id = $('#product_id').val();
        var name = $('#name').val();
        var description = $('#description').val();
        var price = $('#price').val();
        var size = $('#size').val();
        var color = $('#color').val();
        
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
        
        var product_image = document.getElementById('product_image');
        if( product_image.files && product_image.files.length > 0 )
        {
            form_data.append('file',product_image.files[0]);
        }
    }
    
    var url = '/manage/addproduct.php';
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



