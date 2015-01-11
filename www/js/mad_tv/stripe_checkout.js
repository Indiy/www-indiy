(function(){

window.authorizePlaylist = authorizePlaylist;

var g_handler = false;
var g_checkoutCallback = function(){};

function ready()
{
    var options = {
        key: g_stripePublicKey,
        image: g_templateParams.checkout_image.image,
        token: onCheckoutToken
    };

    g_handler = StripeCheckout.configure(options);
}
$(document).ready(ready);

function onCheckoutToken(token)
{
    console.log("onCheckoutToken: token:",token);
    var args = {
        artist_id: g_artistId,
        url: g_pageUrl,
        template_id: g_templateId,
        token: token.id,
        email: token.email
    };
    var url = "{0}/data/tv_buy.php".format(g_apiBaseUrl);
    jQuery.ajax({
        type: 'POST',
        url: url,
        data: args,
        success: function(data)
        {
            console.log("onCheckoutToken: success:",data);
        },
        error: function()
        {
            console.log("onCheckoutToken: error:",arguments);
        }
    });
}

function showCheckout()
{
    var checkout_amount = g_templateParams.checkout_amount;
    var amount = Math.floor(parseFloat(checkout_amount) * 100);

    var options = {
        name: g_templateParams.checkout_name,
        description: g_templateParams.checkout_description,
        amount: amount
    };

    g_handler.open(options);
}

function authorizePlaylist(playlist,callback)
{
    g_checkoutCallback = callback;

    if( playlist.name == g_templateParams.checkout_playlist )
    {
        showCheckout();
    }
    else
    {
        callback(null);
    }
}

})();
