

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
            $('#edit_order #order_status').html("Canceled");
            $('#edit_order #refund_button').hide();
            $('#edit_order #ship_button').hide();
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
    var total_charge = 0.0;
    var total_artist = 0.0;
    for( var i = 0 ; i < orders.length ; ++i )
    {
        var order = orders[i];
        
        var html = "";
        html += "<tr>";
        html += " <td class='order_id'>{0}</td>".format(order.id);
        html += " <td class='date'>{0}</td>".format(order.order_date);
        html += " <td class='amount'>${0}</td>".format(order.charge_amount.toFixed(2));
        html += " <td class='amount'>${0}</td>".format(order.to_artist_amount.toFixed(2));
        html += "</tr>";
        $(tag).append(html);
        
        total_charge += order.charge_amount;
        total_artist += order.to_artist_amount;
    }
    
    var html = "";
    html += "<tr class='total'>";
    html += " <td class='title' colspan='2'>Total</td>";
    html += " <td class='amount'>${0}</td>".format(total_charge.toFixed(2));
    html += " <td class='amount'>${0}</td>".format(total_artist.toFixed(2));
    html += "</tr>";
    $(tag).append(html);
}

function renderAllArtistSummary()
{
    $('#summary_list').empty();
    
    var total_charges = 0.0;
    var total_artist_payouts = 0.0;
    var total_count = 0;
    for( var i = 0 ; i < g_artistList.length ; ++i )
    {
        var artist = g_artistList[i];
        
        var odd = "";
        if( i % 2 == 0 )
            odd = " odd";
        
        var html = "";
        html += "<div class='item{0}'>".format(odd);
        html += " <div class='artist'>{0}</div>".format(artist.artist_name);
        html += " <div class='count'>{0}</div>".format(artist.order_count);
        html += " <div class='charge_total'>${0}</div>".format(artist.charge_total.toFixed(2));
        html += " <div class='payout_total'>${0}</div>".format(artist.artist_total.toFixed(2));
        html += "</div>";
        $('#summary_list').append(html);
        
        total_charges += artist.charge_total;
        total_artist_payouts += artist.artist_total;
        total_count += artist.order_count;
    }
    
    $('#total_charges').html("${0}".format(total_charges.toFixed(2)));
    $('#total_payouts').html("${0}".format(total_artist_payouts.toFixed(2)));
}

function showEditShippingPopup()
{
    var ship_date = (new Date()).strftime("%Y-%m-%d %T");

    if( g_shipDate && g_shipDate != "None" )
        ship_date = g_shipDate;

    $('#edit_shipping #ship_date').val(ship_date);
    $('#edit_shipping #tracking_number').val(g_trackingNumber);

    showPopup('#edit_shipping');
}

function onEditShippingSubmit()
{
    var ship_date = $('#edit_shipping #ship_date').val();
    var tracking_number = $('#edit_shipping #tracking_number').val();
    
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
            $('#edit_order #order_status').html("Shipped");
            if( tracking_number.length > 0 )
                $('#edit_order #tracking_number').html(tracking_number);
            $('#edit_order #ship_date').html(data.ship_date)
            closePopup();
        },
        error: function()
        {
            window.alert("Adding Shipping Data failed");
        }
    });
}



