

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
            $('#order .status').html("Canceled");
            $('#order .refund').hide();
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

