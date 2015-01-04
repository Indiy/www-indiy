(function(){

window.IS_IPAD = navigator.userAgent.match(/iPad/i) != null;
window.IS_IPHONE = navigator.userAgent.match(/iPhone/i) != null;
window.IS_IPOD = navigator.userAgent.match(/iPod/i) != null;
window.IS_IOS = IS_IPAD || IS_IPHONE || IS_IPOD;

window.IS_ANDROID = navigator.userAgent.match(/Android/i) != null;
window.IS_ANDROID_PHONE = navigator.userAgent.match(/Android.*Mobile/i) != null;
window.IS_ANDROID_TABLET = IS_ANDROID && !IS_ANDROID_PHONE;

window.IS_PHONE = IS_IPOD || IS_IPHONE || IS_ANDROID_PHONE;
window.IS_TABLET = IS_ANDROID_TABLET || IS_IPAD;

window.IS_WINDOWS = navigator.userAgent.match(/Windows/i) != null;
window.IS_CHROME = navigator.userAgent.match(/Chrome/i) != null;
window.IS_MOBILE = navigator.userAgent.match(/Mobile/i) != null;
window.IS_DESKTOP = !IS_MOBILE;

window.IS_RETINA = window.devicePixelRatio > 1;

window.IS_IE = false;
window.IS_OLD_IE = false;
(function() {
 window.ie_match = navigator.userAgent.match(/IE ([^;]*);/);
 if( ie_match != null && ie_match.length > 1 )
 {
 IS_IE = true;
 window.ie_version = parseFloat(ie_match[1]);
 if( ie_version < 9.0 )
 IS_OLD_IE = true;
 }
 })();

window.EMAIL_REGEX = new RegExp('[a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?');

window.IS_NARROW = false;

window.debugLog = function() {};
window.enableDebug = enableDebug;
window.disableDebug = disableDebug;
window.updateAnchor = updateAnchor;

if( window.localStorage.enable_debug )
{
    enableDebug();
}

function enableDebug()
{
    window.debugLog = console.log.bind(console);
    window.localStorage.enable_debug = "1";
}
function disableDebug()
{
    window.debugLog = function() {};
    delete window.localStorage.enable_debug;
}

var g_storedHash = "";

function getAnchorMap()
{
    var anchor_map = {};
    var anchor = self.document.location.hash.substring(1);
    var anchor_elements = anchor.split('&');
    for( var i = 0 ; i < anchor_elements.length ; i++ )
    {
        var e = anchor_elements[i];
        var k_v = e.split('=');
        
        k = unescape(k_v[0]);
        if( k_v.length > 1 )
            anchor_map[k] = unescape(k_v[1]);
        else
            anchor_map[k] = true;
    }
    return anchor_map;
}

function updateAnchor(map)
{
    var anchor_map = getAnchorMap();
    
    jQuery.extend(anchor_map,map);
    
    var anchor = "";
    for( var key in anchor_map )
    {
        var val = anchor_map[key];
        
        if( val.length > 0 )
        {
            if( anchor.length > 0 )
                anchor += "&";
            anchor += "{0}={1}".format(key,val);
        }
    }

    // inhibit hashChanged if we do it
    g_storedHash = "#" + anchor;
    window.location.hash = anchor;
}

})();
