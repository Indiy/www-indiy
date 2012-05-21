
var g_bottomOpen = false;

function toggleBottom()
{
    if( g_bottomOpen )
        closeBottom();
    else
        openBottom();
}

function openBottom()
{
    g_bottomOpen = true;
    $('#bottom_container').animate({ height: '275px' });
}

function closeBottom()
{
    g_bottomOpen = false;
    $('#bottom_container').animate({ height: '55px' });
}


