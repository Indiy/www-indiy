(function(){

window.navToggleTab = navToggleTab;
window.navToggleStore = navToggleStore;
window.navToggleFacebook = navToggleFacebook;
window.navToggleTwitter = navToggleTwitter;
window.navToggleInstagram = navToggleInstagram;
window.navHideTab = navHideTab;

function navToggleTab(ele,i)
{
    toggleItem(ele,'#user_tab_' + i);
}
function navToggleStore(ele)
{
    toggleItem(ele,'#store_tab');
}
function navToggleFacebook(ele)
{
    toggleItem(ele,'.social_item.facebook');
}
function navToggleTwitter(ele)
{
    toggleItem(ele,'.social_item.twitter');
}
function navToggleInstagram(ele)
{
    toggleItem(ele,'.social_item.instagram');
}

function toggleItem(ele,item_sel)
{
    var ele_jq = $(ele);
    if( ele_jq.hasClass('active') )
    {
        ele_jq.removeClass('active');
    }
    else
    {
        ele_jq.siblings().removeClass('active');
        ele_jq.addClass('active');
        hideAll();
        $(item_sel).show();
    }
}

function hideAll()
{
    $('.content_tab').hide();
}

function navHideTab(ele)
{
    $('#top_bar_nav .social.button').removeClass('active');
    hideAll();
}

})();
