
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

    var shipping_total = 0.0;
    var sub_total = 0.0;
    for( var i = 0 ; i < g_cartList.length ; ++i )
    {
        var c = g_cartList[i];
        var id = c['id'];
        var name = c['name'];
        var image = c['image'];
        var price = myParseFloat(c['price'],0.0);
        var shipping = myParseFloat(c['shipping'],0.0);
        var quantity = c['quantity'];

        shipping_total += shipping;
        sub_total += price; 
        
        var html = "";
        html += "<div class='cart_line' id='cart_line_{0}'>".format(i);
        html += " <div class='image_holder'><img src='{0}'></div>".format(image);
        html += " <div class='description'>";
        html += "  <div class='name_artist'>";
        html += "   <div class='name'>{0}</div>".format(name);
        html += "  </div>";
        html += "  <div class='delete' onclick='cartDeleteIndex({0});'>Delete</div>".format(i);
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
    
    var total = shipping_total + sub_total;
    $('#cart #shipping_amount').html("$" + shipping_total);
    $('#cart #total_amount').html("$" + total);
    $('#cart .total_lines').show();

}

function paypalCheckout()
{
    jQuery.ajax(
    {
        type: 'POST',
        url: "/data/paypal.php?checkout=1",
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


