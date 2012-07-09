

function confirmRefund()
{
    var ret = window.confirm("Are you sure you want to refund this order?");
    if( ret )
    {
        refundOrder();
    }
}

function refundOrder()
{
    var args = {
        order_id: g_orderId,
        method: "refund"
    };

    jQuery.ajax(
    {
        type: 'POST',
        url: '/manage/data/order.php',
        data: args,
        dataType: 'json',
        success: function(data) 
        {
            $('#order #order_status').html("Canceled");
            $('#order .refund').hide();
            $('#order .edit_shipping').hide();
            window.alert("Order refunded.");
        },
        error: function()
        {
            window.alert("Refund failed");
        }
    });
}

function markShipped()
{
    var ship_date = $('#tracking_ship_date').val();
    var tracking_number = $('#tracking_number').val();
    
    if( ship_date.length == 0 )
    {
        window.alert("Please enter a ship date.");
        return;
    }
    
    var args = {
        order_id: g_orderId,
        method: "ship",
        ship_date: ship_date
    };
    
    if( tracking_number.length > 0 )
        args.tracking_number = tracking_number;
    
    jQuery.ajax(
    {
        type: 'POST',
        url: '/manage/data/order.php',
        data: args,
        dataType: 'json',
        success: function(data) 
        {
            $('#order .status').html("Shipped");
        },
        error: function()
        {
            window.alert("Adding Shipping Data failed");
        }
    });
    
}


function renderArtistSettlementOrders()
{
    renderArtistSettlementOrderArray(g_pendingShipmentOrders,'#pending_orders tbody');
    renderArtistSettlementOrderArray(g_shippedOrders,'#shipped_orders tbody');
}

function renderArtistSettlementOrderArray(orders,tag)
{
    $(tag).empty();
    for( var i = 0 ; i < orders.length ; ++i )
    {
        var order = orders[i];
        
        var html = "";
        html += "<tr>";
        html += " <td class='order_id'>{0}</td>".format(order.id);
        html += " <td class='date'>{0}</td>".format(order.id);
        html += " <td class='amount'>{0}</td>".format(order.charge_amount);
        html += " <td class='amount'>{0}</td>".format(order.to_artist_amount);
        html += "</tr>";
        $(tag).append(html);
    }
}
