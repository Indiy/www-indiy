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

if( window.localStorage.enable_debug )
{
    enableDebug();
}

function enableDebug()
{
    window.debug_log = console.log.bind(console);
    window.localStorage.enable_debug = "1";
}
function disableDebug()
{
    delete window.localStorage.enable_debug;
}

)();
