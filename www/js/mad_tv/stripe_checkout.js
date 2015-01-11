(function(){

window.authorizePlaylist = authorizePlaylist;

var g_handler = false;
var g_authorizeCallback = function(){};

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
            if( data && data.error )
            {
                var error = data.error;
                if( error == 'existing_sub' )
                {
                    window.alert("You already have a subscription. Please use the link in your email to access this content.");
                }
                else
                {
                    window.alert("Payment failed, please try again.");
                }
            }
            else
            {
                var secret_token = data.secret_token;
                window.localStorage.secret_token = secret_token;
                g_authorizeCallback(null);
            }
        },
        error: function()
        {
            window.alert("Payment failed, please try again.");
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
    g_authorizeCallback = callback;

    if( playlist.name == g_templateParams.checkout_playlist )
    {
        if( window.localStorage.secret_token )
        {
            checkToken();
        }
        else
        {
            showCheckout();
        }
    }
    else
    {
        g_authorizeCallback(null);
    }
}
function checkToken()
{
    var secret_token = window.localStorage.secret_token;
    var args = {
        artist_id: g_artistId,
        secret_token: secret_token
    };
    var url = "{0}/data/sub_check_token.php".format(g_apiBaseUrl);
    jQuery.ajax({
        type: 'POST',
        url: url,
        data: args,
        success: function(data)
        {
            if( data && data.error )
            {
                delete window.localStorage.secret_token;
                showCheckout();
            }
            else
            {
                g_authorizeCallback(null);
            }
        },
        error: function()
        {
            delete window.localStorage.secret_token;
            showCheckout();
        }
    });
}

})();
