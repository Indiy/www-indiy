<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>MYARTISTDNA</title>

    <link href="css/styles.css" rel="stylesheet" type="text/css">

    <? if( $include_order ): ?>
        <link href="css/artist_invoice.css" rel="stylesheet" type="text/css">
        <link href="css/order.css" rel="stylesheet" type="text/css">
    <? endif; ?>
    
    <? if( $include_stats === TRUE ): ?>
        <link href="css/stats.css" rel="stylesheet" type="text/css">
    <? endif; ?>

    <!--[if lt IE 9]>
        <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <script src="color/jscolor.js" type="text/javascript"></script>
    <script src="/js/ZeroClipboard.js" type="text/javascript"></script>
    <script src="/js/string.utils.js" type="text/javascript"></script>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.js" type="text/javascript"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js" type="text/javascript"></script>

    <script src="js/popup.js" type="text/javascript"></script>
    <script src="js/manage.js" type="text/javascript"></script>
    <script src="js/manage_upload.js" type="text/javascript"></script>
    <script src="js/manage_tooltips.js" type="text/javascript"></script>
        
    <? if( $include_editor !== FALSE ): ?>

        <script src="js/edit_audio.js" type="text/javascript"></script>
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
        <script src="js/artist_file.js" type="text/javascript"></script>
        <script src="js/edit_template.js" type="text/javascript"></script>
        <script src="js/edit_playlist.js" type="text/javascript"></script>

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

<div id='body_wrap'><div id='main_content'>

<section id="bgtopbar">
	<section id="topbar">
		<p>Logged in as: <?=$_SESSION['sess_userName'];?> | <a href="/logout.php">Logout</a> | <a href="/faq.php">Help</a> </p>
	</section>
</section>
<section id="headerinner">
<header>
    <h1><a href="/home.php"><img src="images/MYARTISTDNA.png" alt="MYARTISTDNA"></a></h1>
	<nav>
    <ul>
        <?php
        
        if( $_SESSION['fan_id'] > 0 )
        {
            echo "<li>";
            echo "<a href='/fan/'>FAN HOME</a>";
            echo "</li>";
        }
        
        if( $_SESSION['sess_userType'] == 'SUPER_ADMIN' ) 
        {
            ?>
                <li><a class="active" href="dashboard.php">DASHBOARD</a></li>
                <li><a onclick="showAddArtist();">ADD ARTIST</a></li>
                <li class="nodivider"><a onclick="showAddLabel();">ADD BRAND</a></li>
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
            if( !$artist_url )
            {
                $host = parse_url(trueSiteUrl(),PHP_URL_HOST);
                $host_explode = explode(".", $host);
                $artist_home_host = $_SESSION['sess_userURL'] . '.' . implode('.',array_slice($host_explode,1));
                
                $artist_url = "http://$artist_home_host";
            }
            $artist_id = $_SESSION['sess_userId'];
            
            if( strstr($_SERVER['PHP_SELF'],'artist_management.php') !== FALSE )
            {
                echo "<li>";
                echo "<a onclick='showEditProfile();'>EDIT PROFILE</a>";
                echo "</li>";
            }
            else
            {
                echo "<li>";
                echo "<a href='/manage/artist_management.php?userId=$artist_id'>EDIT PROFILE</a>";
                echo "</li>";
            }
                
            echo "<li>";
            echo "<a href='/manage/stats.php?userId=$artist_id'>VIEW ANALYTICS</a>";
            echo "</li>";
            
            echo "<li class='nodivider'>";
            echo "<a href='/manage/artist_statement.php?artist_id=$artist_id'>ACCOUNT STATEMENT</a>";
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
        require_once 'templates/add_user.html';
        require_once 'templates/add_label.html';
    }
    else if( $_SESSION['sess_userType'] == 'LABEL' )
    {
        require_once 'templates/add_user.html';
    }

    require_once 'templates/popup_messages2.html';
?>
