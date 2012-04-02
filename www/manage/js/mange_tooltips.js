

var g_hideTooltipTimer = false;
var g_clip = false;

function hideTooltip()
{
    $('#link_tooltip').hide();
}
function startTooltipTimer()
{
    clearTooltipTimer();
    g_hideTooltipTimer = setTimeout(hideTooltip,1000);
}
function clearTooltipTimer()
{
    if( g_hideTooltipTimer )
    {
        clearTimeout(g_hideTooltipTimer);
        g_hideTooltipTimer = false;
    }
}

function mouseenterLink(self)
{
    $('.link_copy').text('Copy');
    clearTooltipTimer();
    var url = self.href;
    g_clip.setText(url);
    var short_url = url.substring(7);
    $('#link_url').text(short_url);
    
    $('#link_tooltip').show();
    var new_offset = $(self).offset();
    new_offset.left -= $('#link_tooltip').width()/2;
    new_offset.top -= $('#link_tooltip').height() + 5; 
    $('#link_tooltip').offset(new_offset)
    
    var link_tooltip = $('#link_tooltip').get(0);
    if( g_clip.div ) 
    {
        g_clip.reposition(link_tooltip);
    }
    else
    {
        g_clip.glue(link_tooltip);
    }
}
function mouseleaveLink()
{
    startTooltipTimer();
}

function clipMouseOver()
{
    clearTooltipTimer();   
}
function clipMouseOut()
{
    startTooltipTimer();
}
function clipComplete()
{
    $('.link_copy').text('Copied');
}
function mouseenterToolTip()
{
    clearTooltipTimer();
}
function mouseleaveToolTip()
{
    startTooltipTimer();
}

function setupClipboard()
{
    ZeroClipboard.setMoviePath('/flash/ZeroClipboard.swf');
    g_clip = new ZeroClipboard.Client();
    g_clip.setHandCursor(true);
    g_clip.addEventListener('onMouseOver',clipMouseOver);
    g_clip.addEventListener('onMouseOut',clipMouseOut);
    g_clip.addEventListener('onComplete',clipComplete);
    $('.share a').mouseenter(function() { mouseenterLink(this); });
    $('.share a').mouseleave(mouseleaveLink);
    $('#link_tooltip').mouseenter(mouseenterToolTip);
    $('#link_tooltip').mouseleave(mouseleaveToolTip);
}

$(document).ready(setupClipboard);

function hoverInQuestion(event)
{
    $('#question_tooltip').show();
    var id = $(event.target).attr('id');
    $('#question_tooltip').text(g_tooltipText[id]);
    
    var new_offset = $(event.target).offset();
    new_offset.left -= $('#question_tooltip').width()/2 - 40;
    new_offset.top -= $('#question_tooltip').height() + 20;
    $('#question_tooltip').offset(new_offset);
    
}
function hoverOutQuestion(event)
{
    $('#question_tooltip').hide();
}
function setupQuestionTolltips()
{
    $('.tooltip').hover(hoverInQuestion,hoverOutQuestion);
}



