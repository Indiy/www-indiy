
var g_photoListIndex = 0;
var g_currentPhotoIndex = 0;

var g_photoChangeToIndex = false;
var g_photoReady = false;

$(document).ready(photoOnReady);
function photoOnReady()
{
    if( g_photoList.length > 0 )
    {
        var opts = {
            panelCount: g_photoList.length,
            resizeCallback: photoResizeBackgrounds,
            onPanelChange: photoPanelChange,
            onPanelVisible: photoPanelVisible,
            onReady: photoSwipeReady
        };
        $('#photo_bg').swipe(opts);
    }
}

function photoHide()
{
    $('#photo_bg').hide();
}
function photoShow()
{
    $('#photo_bg').show();
}

function photoListScrollLeft()
{
    if( g_photoListIndex > 0 )
    {
        g_photoListIndex -= 3;
        if( g_photoListIndex < 0 )
            g_photoListIndex = 0;
        scrollPhotoListToIndex(true);
    }
}

function photoListScrollRight()
{
    var max_left = g_photoList.length - 3;
    
    if( g_photoListIndex < max_left )
    {
        g_photoListIndex += 3;
        if( g_photoListIndex > max_left )
            g_photoListIndex = max_left;
        scrollPhotoListToIndex(true);
    }
}

function photoPanelChange(index)
{
    console.log("photoPanelChange: " + index);
    photoUpdateToIndex(index);
}

function photoPanelVisible(index)
{
    var photo = g_photoList[index];
    photoLoadImage(photo,index);
}

function photoSwipeReady()
{
    g_photoReady = true;
    if( g_photoChangeToIndex !== false )
        photoChangeIndex(g_photoChangeToIndex);
    g_photoChangeToIndex = false;
}

function photoChangeId( photo_id )
{
    for( var i = 0 ; i < g_photoList.length ; ++i )
    {
        var photo = g_photoList[i];
        if( photo.id == photo_id )
        {
            photoChangeIndex(i);
            return;
        }
    }
}

function photoChangeIndex( index ) 
{
    if( !g_photoReady )
    {
        g_photoChangeToIndex = index;
        return;
    }

    setPlayerMode("photo");
    
    $('#photo_bg').swipe("scrollto",index);    
}
function photoUpdateToIndex(index)
{
    g_currentPhotoIndex = index;
    var photo = g_photoList[index];
    
    loveChangedPhoto(photo.id,photo.name);
    
    photoLoadImage(photo,index);
    
    g_currentPhotoId = photo.id;
    window.location.hash = '#photo_id=' + g_currentPhotoId; 
    
    playerPhotoInfo(photo.name,photo.location,photo.listens);    
}

function photoNext()
{
    var index = g_currentPhotoIndex + 1;
    if( index == g_photoList.length )
        index = 0;
    
    photoChangeIndex(index);
}
function photoPrevious()
{
    var index = g_currentPhotoIndex - 1;
    if( index < 0 )
        index = g_photoList.length - 1;
    
    photoChangeIndex(index);
}

function photoPreloadImages()
{
    for( var i = 0 ; i < g_photoList.length ; ++i  )
    {
        var photo = g_photoList[i];
        photoLoadImage(photo,i);
    }
}

function photoLoadImage(photo,index)
{
    imageLoadItem(photo,index,'#photo_bg');
}
function photoScrollToCurrentIndex()
{
    var win_width = $('#photo_bg').width();
    var left = win_width * g_currentPhotoIndex;
    $('#photo_bg').scrollLeft(left);
}

function photoResizeBackgrounds()
{
    imageResizeBackgrounds(g_photoList,'#photo_bg');
    photoScrollToCurrentIndex();
}
