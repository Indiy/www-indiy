

var TAB_LIMIT = 6;
var SONG_REGULAR_LIMIT  = 5;
var VIDEO_REGULAR_LIMIT = 5;
var PHOTO_REGULAR_LIMIT = 10;

function artistManagementReady()
{
    updatePageList();
    updatePhotoList();
    updateVideoList();
    updateStoreList();
    updateTabList();
    updateFileList();
    
    $('.heading').click(function(){
		if( $(this).next().is(':hidden') ) 
        {
			$('.heading').removeClass('active').next().slideUp();
			$(this).toggleClass('active').next().slideDown();
		}
        else
        {
            $('.heading').removeClass('active').next().slideUp();
        }
		return false;
	});
    $('.heading a').click(function(e) { e.stopPropagation(); });
    
    setupSortableList('ul.playlist_sortable',"/manage/data/page.php");
    setupSortableList('ul.videos_sortable',"/manage/data/video.php");
    setupSortableList('ul.products_sortable',"/manage/data/product.php");
    setupSortableList('ul.photos_sortable',"/manage/data/photo.php");
    setupSortableList('ul.pages_sortable',"/manage/data/tab.php");
    
    if( !g_artistData['email'] )
    {
        showEmailPopup();
    }
    else if( g_shouldShowFirstInstruction )
    {
        showFirstInstructions();
    }
    
}
$(document).ready(artistManagementReady);

function showList(list_selector)
{
    if( $(list_selector).next().is(':hidden') )
    {
        $('.heading').removeClass('active').next().slideUp();
        $(list_selector).toggleClass('active').next().slideDown();
    }
}

function updatePageList()
{
    $('#page_list_ul').empty();
    for( var i = 0 ; i < g_pageList.length ; ++i )
    {
        var song = g_pageList[i];
        
        var class_name = i % 2 == 0 ? 'odd' : ''; 

        var html = "";
        html += "<li id='arrayorder_{0}' class='playlist_sortable {1}'>".format(song.id,class_name);
        html += "<span class='title'>\n";
        html += "<a onclick='showPagePopup({0});'>".format(i);
        html += song.name;
        html += "</a>\n";
        html += "</span>\n";
        html += "<span class='share'>";
        html += "<a href='{0}' target='_blank'>Link</a>".format(song.short_link);
        html += "</span>\n";
        
        html += "<span class='socialize'>";
        if( g_facebook )
        {
            html += "<a onclick='showSocialPost({0});' title='Send a Facebook update for this page.'>".format(i);
            html += "<img class='social_icon' src='/images/fb_icon_color.png'/>";
            html += "</a>\n";
        }
        else
        {
            html += "<a onclick='showSocialConfigPopup();' title='Add a Facebook account.'>".format(g_artistId);
            html += "<img class='social_icon' src='/images/fb_icon_grey.png'/>";
            html += "</a>\n";
        }
        if( g_twitter )
        {
            html += "<a onclick='showSocialPost({0});' title='Send a tweet for this page.'>".format(i);
            html += "<img class='social_icon' src='/images/tw_icon_color.png'/>";
            html += "</a>\n";
        }
        else
        {
            html += "<a onclick='showSocialConfigPopup();' title='Add a Twitter account.'>".format(g_artistId);
            html += "<img class='social_icon' src='/images/tw_icon_grey.png'/>";
            html += "</a>\n";
        }
        html += "</span>";
        
        html += "<span class='delete'><a onclick='deletePage({0});' ></a></span>".format(song.id);
        html += "</li>\n";
        
        $('#page_list_ul').append(html);
    }
    if( g_pageList.length == 0 )
    {
        var html = "<div class='empty_list'>You have not uploaded any pages yet.</div>";
        $('#page_list_ul').append(html);
    }
}
function updatePhotoList()
{
    $('#photo_list_ul').empty();
    for( var i = 0 ; i < g_photoList.length ; ++i )
    {
        var photo = g_photoList[i];
        
        var class_name = i % 2 == 0 ? 'odd' : '';
        var error = false;
        
        var img_url = get_thumbnail(photo.image_url,photo.image_extra,210,132);
        
        var html = "";
        html += "<li id='arrayorder_{0}' class='photos_sortable'>".format(photo.id);
        html += "<figure>";
        html += "<span class='close'>";
        html += "<a href='#' onclick='deletePhoto({0});'></a>".format(photo.id);
        html += "</span>";
        html += "<a title='{0}' onclick='showPhotoPopup({1});'>".format("Edit Photo",i);
        html += "<img src='{0}' width='210' height='132'>".format(img_url);
        if( error )
            html += "<div class='error'>!</div>";
        html += "</a>";
        html += "</figure>";
        html += "<span>";
        html += "<a title='{0}' onclick='showPhotoPopup({1});'>".format("Edit Photo",i);
        html += photo.name;
        html += "</a>";
        html += "</span>";
        html += "<br>";
        html += "</li>";
        $('#photo_list_ul').append(html);
    }
    if( g_photoList.length == 0 )
    {
        var html = "<div class='empty_list'>You have not uploaded any photos yet.</div>";
        $('#photo_list_ul').append(html);
    }
}

function updateStoreList()
{
    $('#product_list_ul').empty();
    for( var i = 0 ; i < g_productList.length ; ++i )
    {
        var product = g_productList[i];
        
        var img_url = get_thumbnail(product.image_url,product.image_extra,210,132);
        
        var html = "";
        html += "<li id='arrayorder_{0}' class='products_sortable'>".format(product.id);
        html += "<figure>";
        html += "<span class='close'><a href='#' onclick='deleteProduct({0});'></a></span>".format(product.id);
        html += "<a onclick='showProductPopup({0});'>".format(i);
        html += "<img src='{0}' width='207' height='130' alt=''>".format(img_url);
        html += "</a>";
        html += "</figure>";
        html += "<span>";
        html += "<a onclick='showProductPopup({0});'>".format(i);
        html += product.name;
        html += "</a>";
        html += "</span>";
        html += "<br>${0}".format(product.price);
        html += "</li>";
        $('#product_list_ul').append(html);
    }
    if( g_productList.length == 0 )
    {
        var html = "<div class='empty_list'>You have not added any products yet.</div>";
        $('#product_list_ul').append(html);
    }
}

function updateVideoList()
{
    $('#video_list_ul').empty();
    for( var i = 0 ; i < g_videoList.length ; ++i )
    {
        var video = g_videoList[i];
        
        var tip = "Edit Video";
        var error = false;
        if( video.error && video.error.length > 0 )
        {
            error = true;
            tip = video.error
        }
        else if( video.video == null || video.video.length == 0 )
        {
            error = true;
            tip = "Item needs video!"
        }
        
        var img_url = get_thumbnail(video.image_url,video.image_extra,210,132);
        
        var html = "";
        html += "<li id='arrayorder_{0}' class='videos_sortable'>".format(video.id);
        html += "<figure>";
        html += "<span class='close'>";
        html += "<a href='#' onclick='deleteVideo({0});'></a>".format(video.id);
        html += "</span>";
        html += "<a title='{0}' onclick='showVideoPopup({1});'>".format(tip,i);
        html += "<img src='{0}' width='210' height='132'>".format(img_url);
        if( error )
            html += "<div class='error'>!</div>";
        html += "</a>";
        html += "</figure>";
        html += "<span>";
        html += "<a title='{0}' onclick='showVideoPopup({1});'>".format(tip,i);
        html += video.name;
        html += "</a>";
        html += "</span>";
        html += "<br>";
        html += "</li>";
        $('#video_list_ul').append(html);
    }
    if( g_videoList.length == 0 )
    {
        var html = "<div class='empty_list'>You have not uploaded any videos yet.</div>";
        $('#video_list_ul').append(html);
    }
}

function updateTabList()
{
    $('#tab_list_ul').empty();
    for( var i = 0 ; i < g_tabList.length ; ++i )
    {
        var tab = g_tabList[i];
        var class_name = i % 2 == 0 ? 'odd' : '';
        var html = "";

        html += "<li id='arrayorder_{0}' class='pages_sortable {1}'>".format(tab.id,class_name);
        html += "<span class='title'>";
        html += "<a onclick='showTabPopup({0});'>".format(i);
        html += tab.name;
        html += "</a>";
        html += "</span>";
        html += "<span class='delete'><a  href='#' onclick='deleteTab({0});'></a></span>".format(tab.id);
        html += "</li>";
        $('#tab_list_ul').append(html);
    }
    if( g_tabList.length == 0 )
    {
        var html = "<div class='empty_list'>You have not uploaded any tab content yet.</div>";
        $('#tab_list_ul').append(html);
    }
}

function updateProfile()
{
    $('#profile_figure img').attr('src',g_artistData.logo_url);
    $('#profile_name_anchor').text(g_artistData.artist);
    updatePageList();
    updatePublishState();
}

function deletePage(song_id)
{
    var ret = window.confirm("Are you sure you want delete this item?");
    if(ret)
    {
        window.location.href = "artist_management.php?userId={0}&action=1&song_id={1}".format(g_artistId,song_id);
    }
}
function deleteVideo(video_id)
{
    var ret = window.confirm("Are you sure you want delete this item?");
    if(ret)
    {
        window.location.href = "artist_management.php?userId={0}&action=1&video_id={1}".format(g_artistId,video_id);
    }
}
function deleteTab(tab_id)
{
    var ret = window.confirm("Are you sure you want delete this item?");
    if(ret)
    {
        window.location.href = "artist_management.php?userId={0}&action=1&content_id={1}".format(g_artistId,tab_id);
    }
}
function deletePhoto(photo_id)
{
    var ret = window.confirm("Are you sure you want delete this item?");
    if(ret)
    {
        window.location.href = "artist_management.php?userId={0}&action=1&photo_id={1}".format(g_artistId,photo_id);
    }
}
function deleteProduct(product_id)
{
    var ret = window.confirm("Are you sure you want delete this item?");
    if(ret)
    {
        window.location.href = "artist_management.php?userId={0}&action=1&prod_id={1}".format(g_artistId,product_id);
    }
}

function setupSortableList(el,url)
{
    var args = {
        opacity: 0.8,
        cursor: 'move',
        update: function()
        {
            var order = $(this).sortable("serialize") + "&method=order";
            $.post(url, order, function(data){});
        }
    };
    $(el).sortable(args);
}

function clearFileElement(selector)
{
    var html = $(selector).parent().html();
    $(selector).parent().html(html);
}

function get_thumbnail(image,image_extra,width,height)
{
    var key = "w" + width;
    if( height && height > 0 )
    {
        key += "_h" + height;
    }
    
    if( image_extra && key in image_extra.alts )
    {
        return g_artistFileBaseUrl + image_extra.alts[key];
    }
    return image;    
}

