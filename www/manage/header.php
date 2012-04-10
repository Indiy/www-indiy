<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>MYARTISTDNA</title>

    <!-- Combo-handled YUI CSS files: --> 
    <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/combo?2.9.0/build/assets/skins/sam/skin.css"> 

    <link href="css/styles.css" rel="stylesheet" type="text/css">
    <link href="facefiles/facebox.css" media="screen" rel="stylesheet" type="text/css" />

    <!--[if lt IE 9]>
        <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.js" type="text/javascript"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js" type="text/javascript"></script>

    <!-- Combo-handled YUI JS files: --> 
    <script type="text/javascript" src="http://yui.yahooapis.com/combo?2.9.0/build/yahoo-dom-event/yahoo-dom-event.js&2.9.0/build/animation/animation-min.js&2.9.0/build/element/element-min.js&2.9.0/build/container/container-min.js&2.9.0/build/menu/menu-min.js&2.9.0/build/button/button-min.js&2.9.0/build/editor/editor-min.js"></script> 

    <script src="facefiles/facebox.js" type="text/javascript"></script>
    <script src="color/jscolor.js" type="text/javascript"></script>
    <script src="/js/ZeroClipboard.js" type="text/javascript"></script>
    <script src="/js/string.utils.js" type="text/javascript"></script>
    <script src="js/tooltip_text.js" type="text/javascript"></script>
    <script src="js/rich_text_editor.js" type="text/javascript"></script>
    
    <script src="js/manage.js" type="text/javascript"></script>
    <script src="js/manage_upload.js" type="text/javascript"></script>
    <script src="js/manage_tooltips.js" type="text/javascript"></script>
    
    <script src="js/edit_page.js" type="text/javascript"></script>
    <script src="js/edit_product.js" type="text/javascript"></script>
    <script src="js/edit_video.js" type="text/javascript"></script>
    <script src="js/edit_tab.js" type="text/javascript"></script>
    <script src="js/edit_social_config.js" type="text/javascript"></script>
    <script src="js/edit_profile.js" type="text/javascript"></script>
    <script src="js/invite_friends.js" type="text/javascript"></script>
    <script src="js/edit_store.js" type="text/javascript"></script>
    <script src="js/social_post.js" type="text/javascript"></script>

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
		<p>Logged in as <?php echo $_SESSION['sess_userName']; ?> | <a href="logout.php">Logout</a></p>
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
            echo '<li><a class="active" href="dashboard.php">DASHBOARD</a></li>';
            echo '<li><a href="add_user.php" rel="facebox[.bolder]">ADD ARTIST</a></li>';
            echo '<li class="nodivider"><a href="add_label.php" rel="facebox[.bolder]">ADD LABEL</a></li>';
        }
        else if( $_SESSION['sess_userType'] == 'LABEL' )
        {
            echo '<li><a class="active" href="dashboard.php">DASHBOARD</a></li>';
            echo '<li class="nodivider"><a href="add_user.php" rel="facebox[.bolder]">ADD ARTIST</a></li>';
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