<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>MYARTISTDNA</title>

    <!-- Combo-handled YUI CSS files: --> 
    <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/combo?2.9.0/build/assets/skins/sam/skin.css"> 

    <link href="css/styles.css" rel="stylesheet" type="text/css">

    <? if( $include_order ): ?>
        <link href="css/order.css" rel="stylesheet" type="text/css">
    <? endif; ?>

    <!--[if lt IE 9]>
        <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <script src="color/jscolor.js" type="text/javascript"></script>
    <script src="/js/ZeroClipboard.js" type="text/javascript"></script>
    <script src="/js/string.utils.js" type="text/javascript"></script>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.js" type="text/javascript"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js" type="text/javascript"></script>

    <? if( $include_editor !== FALSE ): ?>

        <!-- Combo-handled YUI JS files: --> 
        <script type="text/javascript" src="http://yui.yahooapis.com/combo?2.9.0/build/yahoo-dom-event/yahoo-dom-event.js&2.9.0/build/animation/animation-min.js&2.9.0/build/element/element-min.js&2.9.0/build/container/container-min.js&2.9.0/build/menu/menu-min.js&2.9.0/build/button/button-min.js&2.9.0/build/editor/editor-min.js"></script> 

        <script src="js/tooltip_text.js" type="text/javascript"></script>
        <script src="js/rich_text_editor.js" type="text/javascript"></script>
    
    <? endif; ?>
        
        <script src="js/popup.js" type="text/javascript"></script>
        <script src="js/manage.js" type="text/javascript"></script>
        <script src="js/manage_upload.js" type="text/javascript"></script>
        <script src="js/manage_tooltips.js" type="text/javascript"></script>
        
    <? if( $include_editor !== FALSE ): ?>

        <script src="js/edit_page.js" type="text/javascript"></script>
        <script src="js/edit_photo.js" type="text/javascript"></script>
        <script src="js/edit_product.js" type="text/javascript"></script>
        <script src="js/edit_video.js" type="text/javascript"></script>
        <script src="js/edit_tab.js" type="text/javascript"></script>
        <script src="js/edit_social_config.js" type="text/javascript"></script>
        <script src="js/edit_profile.js" type="text/javascript"></script>
        <script src="js/invite_friends.js" type="text/javascript"></script>
        <script src="js/edit_store.js" type="text/javascript"></script>
        <script src="js/social_post.js" type="text/javascript"></script>
        <script src="js/account_limit.js" type="text/javascript"></script>

        <script src="js/user_admin.js" type="text/javascript"></script>

    <? endif; ?>
    
    <? if( $include_order ): ?>
        <script src="js/order.js" type="text/javascript"></script>
    <? endif; ?>

</head>
<body>
<div id='mask' style='display: none;'></div>
<div id='link_tooltip'>
    <div class='link_url' id='link_url'>madna.co/aa_bb</div>
    <div class='link_copy_sep'>
        <div class='link_sep_bar'> </div>
        <div class='link_copy'>Copy</div>
    </div>
</div>
<div id='question_tooltip' style='display: none;'>
    Tooltip Text
</div>
<section id="bgtopbar">
	<section id="topbar">
		<p>Logged in as <?php echo $_SESSION['sess_userName']; ?> | <a href="/logout.php">Logout</a></p>
	</section>
</section>
<section id="headerinner">
<header>
    <h1><a href="/home.php"><img src="images/MYARTISTDNA.png" alt="MYARTISTDNA"></a></h1>
	<nav>
    <ul>
        <?php 
        if( $_SESSION['sess_userType'] == 'SUPER_ADMIN' ) 
        {
            ?>
                <li><a class="active" href="dashboard.php">DASHBOARD</a></li>
                <li><a onclick="showAddArtist();">ADD ARTIST</a></li>
                <li class="nodivider"><a onclick="showAddLabel();">ADD LABEL</a></li>
            <?php
        }
        else if( $_SESSION['sess_userType'] == 'LABEL' )
        {
            ?>
                <li><a class="active" href="dashboard.php">DASHBOARD</a></li>
                <li class="nodivider"><a onclick="showAddArtist();">ADD ARTIST</a></li>
            <?php
        }
         else
         {
             $host = parse_url(trueSiteUrl(),PHP_URL_HOST);
             $host_explode = explode(".", $host);
             $artist_home_host = $_SESSION['sess_userURL'] . '.' . implode('.',array_slice($host_explode,1));
             
             $artist_id = $_SESSION['sess_userId'];
             
             echo "<li>";
             echo "<a href='http://$artist_home_host'>VIEW SITE</a>";
             echo "</li>";
             echo "<li>";
             echo "<a onclick='showEditProfile();'>EDIT PROFILE</a>";
             echo "</li>";
             echo "<li class='nodivider'>";
             echo "<a onclick='showInvitePopup();'>INVITE FRIENDS</a>";
             echo "</li>";
         }
         ?>
    </ul>
    </nav>
</header>
</section><!-- header -->

<?php

    if( $_SESSION['sess_userType'] == 'SUPER_ADMIN' ) 
    {
        include_once 'include/add_user.html';
        include_once 'include/add_label.html';
    }
    else if( $_SESSION['sess_userType'] == 'LABEL' )
    {
        include_once 'include/add_user.html';
    }

    include_once 'include/popup_messages2.html';
?>
