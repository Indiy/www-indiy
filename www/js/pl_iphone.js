

function iphoneGeneralReady()
{
    scrollToTop();
    $(window).resize(scrollToTop);
}
$(document).ready(iphoneGeneralReady);

function scrollToTop()
{
    window.scrollTo(0,1);
}

