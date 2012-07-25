
$(document).ready(cartOnReady);

function cartOnReady()
{
    cartRender();
}


function myParseFloat(s,def)
{
    var f = parseFloat(s);
    if( isNaN(f) )
        return def;
    return f;
}

function cartRender()
{
    $('#cart_list').empty();

    if( g_cartList.length == 0 )
    {
        $('#cart').hide();
        $('#cart_empty').show();
        return;
    }
    $('#cart_empty').hide();

    for( var i = 0 ; i < g_cartList.length ; ++i )
    {
        var c = g_cartList[i];
        var id = c['id'];
        var name = c['name'];
        var description = c['description'];
        var image = c['image'];
        var price = myParseFloat(c['price'],0.0);
        var shipping = myParseFloat(c['shipping'],0.0);
        var quantity = c['quantity'];

        var odd = "";
        if( i % 2 == 0 )
            odd = " odd";
        
        var html = "";
        html += "<div class='cart_line{0}' id='cart_line_{1}'>".format(odd,i);
        html += " <div class='image_name_description'>";
        html += "  <div class='image_holder'><img src='{0}'></div>".format(image);
        html += "  <div class='name_description'>";
        html += "   <div class='name'>{0}</div>".format(name);
        html += "   <div class='description'>{0}</div>".format(description);
        html += "  </div>";
        html += " </div>";
        html += " <div class='delete'>";
        html += "  <div class='button' onclick='cartDeleteIndex({0});'>".format(i);
        html += "   <div class='icon'></div><div class='label'>Delete</div>";
        html += "  </div>";
        html += " </div>";
        html += " <div class='price'>${0}</div>".format(price);
        html += " <div class='quantity_update'>";
        html += "  <div class='quantity'><input value='{0}'/></div>".format(quantity);
        html += "  <div class='update' onclick='cartUpdateQuantity({0});'>Update</div>".format(i);
        html += "  <div class='saved'>Saved</div>";
        html += " </div>";
        html += "</div>";

        $('#cart_list').append(html);
    }
    
    cartRenderTotals();
    
    $('#cart .quantity input').unbind();
    $('#cart .quantity input').bind("propertychange keyup input paste",cartQuantityChange);

}

function cartRenderTotals()
{
    var shipping_total = 0.0;
    var sub_total = 0.0;
    for( var i = 0 ; i < g_cartList.length ; ++i )
    {
        var c = g_cartList[i];
        var price = myParseFloat(c['price'],0.0);
        var shipping = myParseFloat(c['shipping'],0.0);
        var quantity = c['quantity'];
        
        shipping_total += shipping * quantity;
        sub_total += price * quantity;
    }
    
    var total = shipping_total + sub_total;
    $('#cart #shipping_amount').html("$" + shipping_total.toFixed(2));
    $('#cart #total_amount').html("$" + total.toFixed(2));
    $('#cart .totals').show();
}

function paypalCheckout()
{
    var url = "/data/paypal.php?checkout=1&artist_id={0}".format(g_artistId);

    jQuery.ajax(
    {
        type: 'POST',
        url: url,
        dataType: 'json',
        success: function(data) 
        {
            console.log(data);
            if( data['success'] )
            {
                var url = data['url'];
                window.location.href = url;
            }
        },
        error: function()
        {
            window.alert("Error!");
        }
    });
}

function cartDeleteIndex(i)
{
    var c = g_cartList[i];
    
    var data = {
        'cart_item_id': c.id
    };
    var url = "/data/cart2.php?artist_id={0}".format(g_artistId); 
    jQuery.ajax(
    {
        type: 'DELETE',
        url: url,
        data: data,
        dataType: 'json',
        success: function(data) 
        {
            g_cartList = data;
            cartRender();
        },
        error: function()
        {
        }
    });
}

function cartQuantityChange(e)
{
    var parent = $(this).parents('.cart_line');
    var id = parent.attr('id');
    var index = id.split("_")[2];

    var c = g_cartList[index];

    var qty = parseInt($(this).val());
    
    
    if( qty > 0 && qty != c.quantity )
    {
        parent.find('.quantity_update .saved').hide();
        parent.find('.quantity_update .update').show();
    }
    else
    {
        parent.find('.quantity_update .update').hide();
        parent.find('.quantity_update .saved').hide();
    }
}

function cartUpdateQuantity(index)
{
    var c = g_cartList[index];
    var selector = "#cart_line_{0} .quantity_update".format(index);
    var parent = $(selector);
    var qty = parseInt(parent.find('input').val());
    
    if( qty > 0 && qty != c.quantity )
    {
        var data = {
            'cart_item_id': c.id,
            'quantity': qty
        };
        var url = "/data/cart2.php?artist_id={0}".format(g_artistId); 
        jQuery.ajax(
        {
            type: 'POST',
            url: url,
            data: data,
            dataType: 'json',
            success: function(data) 
            {
                g_cartList = data;
                $(parent).find('.update').hide();
                $(parent).find('.saved').show();
                cartRenderTotals();
            },
            error: function()
            {
            }
        });
    }
}


