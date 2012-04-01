
function artistManagementReady()
{
    updatePageList();
    updateStoreList();
    updateTabList();
    updateVideoList();
    
    $('a[rel*=facebox]').facebox();
    
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
    
    setupSortableLists();
}
$(document).ready(artistManagementReady);

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
        html += "<a href='addmusic.php?artist_id={0}&id={1}' rel='facebox[.bolder]'>".format(g_artistId,song.id);
        html += song.name;
        html += "</a>\n";
        html += "</span>\n";
        html += "<span class='share'>";
        html += "<a href='{0}' target='_blank'>Link</a>".format(song.short_link);
        html += "</span>\n";
        
        html += "<span class='socialize'>";
        if( g_facebook )
        {
            html += "<a href='socialize.php?artist_id={0}&song_id={1}' rel='facebox[.bolder]' title='Send a Facebook update for this page.'>"
                        .format(g_artistId,song.id);
            html += "<img class='social_icon' src='/images/fb_icon_color.png'/>";
            html += "</a>\n";
        }
        else
        {
            html += "<a href='social_config.php?artist_id={0}' rel='facebox[.bolder]' title='Add a Facebook account.'>".format(g_artistId);
            html += "<img class='social_icon' src='/images/fb_icon_grey.png'/>";
            html += "</a>\n";
        }
        if( g_twitter )
        {
            html += "<a href='socialize.php?artist_id={0}&song_id={1}' rel='facebox[.bolder]' title='Send a tweet for this page.'>"
                        .format(g_artistId,song.id);
            html += "<img class='social_icon' src='/images/tw_icon_color.png'/>";
            html += "</a>\n";
        }
        else
        {
            html += "<a href='social_config.php?artist_id={0}' rel='facebox[.bolder]' title='Add a Twitter account.'>".format(g_artistId);
            html += "<img class='social_icon' src='/images/tw_icon_grey.png'/>";
            html += "</a>\n";
        }
        html += "</span>";
        
        html += "<span class='delete'><a href='#' onclick='deletePage({0});' ></a></span>".format(song.id);
        html += "</li>\n";
        
        $('#page_list_ul').append(html);
    }
    
    if( g_pageList.length == 0 )
    {
        var html = "<div class='empty_list'>You have not uploaded any pages yet.</div>";
        $('#page_list_ul').append(html);
    }

}

function updateStoreList()
{
    
}

function updateVideoList()
{
    $('#video_list_ul').empty();
    
    for( var i = 0 ; i < g_videoList.length ; ++i )
    {
        var video = g_videoList[i];
    
        var html = "";
        html += "<li id='arrayorder_{0}' class='videos_sortable'>".format(video.id);
        html += "<figure>";
        html += "<span class='close'>";
        html += "<a href='#' onclick='deleteVideo({0});'></a>".format(video.id);
        html += "</span>";
        html += "<a href='addvideo.php?artist_id={0}&id={1}' rel='facebox[.bolder]'>".format(g_artistId,video.id);
        html += "<img src='{0}' width='210' height='132' alt=''>".format(video.image);
        html += "</a>";
        html += "</figure>";
        html += "<span>";
        html += "<a href='addvideo.php?artist_id={0}&id={1}' rel='facebox[.bolder]'>".format(g_artistId,video.id);
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

function setupSortableLists()
{
    $(function() {
        $("ul.playlist_sortable").sortable({opacity: 0.8, cursor: 'move', update: function() {
            //$("#response").html("Loading...");
                var order = $(this).sortable("serialize") + '&order=order&type=musicplayer_audio';
                $.post("/includes/ajax.php", order, function(theResponse){
                    //$("#response").html(theResponse);
                });
            }
        });
    });

    $(function() {
        $("ul.pages_sortable").sortable({opacity: 0.8, cursor: 'move', update: function() {
            //$("#response").html("Loading...");
                var order = $(this).sortable("serialize") + '&order=order&type=musicplayer_content';
                $.post("/includes/ajax.php", order, function(theResponse){
                    //$("#response").html(theResponse);
                });
            }
        });
    });

    $(function() {
        $("ul.videos_sortable").sortable({opacity: 0.8, cursor: 'move', update: function() {
            //$("#response").html("Loading...");
                var order = $(this).sortable("serialize") + '&order=order&type=musicplayer_video';
                $.post("/includes/ajax.php", order, function(theResponse){
                    //$("#response").html(theResponse);
                });
            }
        });
    });			

    $(function() {
        $("ul.products_sortable").sortable({opacity: 0.8, cursor: 'move', update: function() {
            //$("#response").html("Loading...");
                var order = $(this).sortable("serialize") + '&order=order&type=musicplayer_ecommerce_products';
                $.post("/includes/ajax.php", order, function(theResponse){
                    //$("#response").html(theResponse);
                });
            }
        });
    });
}

