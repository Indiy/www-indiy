

function iphoneGeneralReady()
{
    scrollToTop();
    $(window).resize(scrollToTop);
    
    photoChangeIndex(0);
}
$(document).ready(iphoneGeneralReady);

function scrollToTop()
{
    window.scrollTo(0,1);
}

