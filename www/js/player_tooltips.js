
$(document).ready(tooltipsOnReady);

function tooltipsOnReady()
{
    $('.photos.tooltiped').hover(showTooltip,hideTooltip);
}

function showTooltip(arg1,arg2)
{
    console.log(arg1);
    console.log(arg2);
    console.log($(this));
}

function hideTooltip(arg1,arg2)
{
    console.log(arg1);
    console.log(arg2);
    console.log($(this));    
}
