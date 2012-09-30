

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


function renderArtistStatement()
{
    $('#invoice_list').empty();
    var total_artist = 0.0;
    for( var i = 0 ; i < g_artistInvoiceList.length ; ++i )
    {
        var invoice = g_artistInvoiceList[i];
        
        var invoice_id = "{0}-{1}".format(invoice.artist_id,invoice.id.padZeros(8));
        
        var odd = "";
        if( i % 2 == 1 )
            odd = " odd";
        
        var html = "";
        html += "<div class='item{0}' onclick='showInvoice({1});'>".format(odd,i);
        html += " <div class='invoice_id'>{0}</div>".format(invoice_id);
        html += " <div class='invoice_date'>{0}</div>".format(invoice.invoice_date);
        html += " <div class='invoice_total'>${0}</div>".format(invoice.amount.toFixed(2));
        if( invoice.paid_amount > 0.0 )
        {
            html += " <div class='invoice_status'>Paid</div>";
        }
        else
        {
            html += " <div class='invoice_status'>Pending Payment</div>";
        }
        html += "</div>";
        $('#invoice_list').append(html);
        
        total_artist += invoice.amount;
    }
    
    var html = "";
    html += "<div class='number'>";
    html += " <div class='label'>Total Invoices:</div>";
    html += " <div class='amount'>{0}</div>".format(g_artistInvoiceList.length);
    html += "</div>";
    html += "<div class='number'>";
    html += " <div class='label'>Total Earned:</div>";
    html += " <div class='amount'>${0}</div>".format(total_artist.toFixed(2));
    html += "</div>";
    $('#invoice_summary').html(html);
}

function showInvoice(index)
{
    var invoice = g_artistInvoiceList[index];
    
    var invoice_id = "{0}-{1}".format(invoice.artist_id,invoice.id.padZeros(8));
    $('.set_invoice_id').html(invoice_id);
    $('.set_invoice_date').html(invoice.invoice_date);
    $('.set_invoice_amount').html("$" + invoice.amount);
    if( invoice.paid_amount > 0 )
    {
        var html = "PAID (${0} on {1})".format(invoice.paid_amount,invoice.paid_date);
        $('.set_invoice_status').html(html);
    }
    else
    {
        $('.set_invoice_status').html("Payment Pending");
    }
    $('#invoice_order_list').empty();
    var orders = invoice.orders;
    for( var i = 0 ; i < orders.length ; ++i )
    {
        var order = orders[i];
        var charge_amount = parseFloat(order.charge_amount);
        var to_artist_amount = parseFloat(order.to_artist_amount);
        
        var odd = "";
        if( i % 2 == 1 )
            odd = " odd";
    
        var html = "";
        html += "<div class='item{0}'>".format(odd,i);
        html += " <div class='order_id item_col'>{0}</div>".format(order.id);
        html += " <div class='order_date item_col'>{0}</div>".format(order.order_date);
        html += " <div class='order_total item_col'>${0}</div>".format(charge_amount.toFixed(2));
        html += " <div class='order_artist_payment item_col'>${0}</div>".format(to_artist_amount.toFixed(2));
        html += "</div>";
        $('#invoice_order_list').append(html);
    }
    
    $('#artist_statement').hide();
    $('#artist_invoice').show();
}
function showInvoiceList()
{
    $('#artist_invoice').hide();
    $('#artist_statement').show();
}

function renderArtistSettlementOrderArray(orders,tag,summary_tag)
{
    $(tag).empty();
    var total_charge = 0.0;
    var total_artist = 0.0;
    for( var i = 0 ; i < orders.length ; ++i )
    {
        var order = orders[i];
        
        var odd = "";
        if( i % 2 == 1 )
            odd = " odd";
        
        var html = "";
        html += "<div class='item{0}'>".format(odd);
        html += " <div class='order_id'>{0}</div>".format(order.id);
        html += " <div class='order_date'>{0}</div>".format(order.order_date);
        html += " <div class='charge_total'>${0}</div>".format(order.charge_amount.toFixed(2));
        html += " <div class='payout_total'>${0}</div>".format(order.to_artist_amount.toFixed(2));
        html += "</div>";
        $(tag).append(html);
        
        total_charge += order.charge_amount;
        total_artist += order.to_artist_amount;
    }
    
    var html = "";
    html += "<div class='number'>";
    html += " <div class='label'>Total Orders:</div>";
    html += " <div class='amount'>{0}</div>".format(orders.length);
    html += "</div>";
    html += "<div class='number'>";
    html += " <div class='label'>Total Earned:</div>";
    html += " <div class='amount'>${0}</div>".format(total_artist.toFixed(2));
    html += "</div>";
    $(summary_tag).html(html);
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



