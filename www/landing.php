<?

$value_user_name = '';
if( strlen($_COOKIE['LOGIN_EMAIL']) > 0 )
{
    $email = $_COOKIE['LOGIN_EMAIL'];
    $value_user_name = " value='$email' ";
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>MyArtistDNA - BE HEARD, BE SEEN, BE INDEPENDENT</title>
        <link rel="icon" href="favicon.ico" />
        
        <style type="text/css">

@font-face {
    font-family: 'Teletex Light';
    src: url('css/Teletex_Light.eot');
    src: url('css/Teletex_Light.eot?$iefix') format('embedded-opentype'),
    url('css/Teletex_Light.otf') format('opentype');
    font-weight: normal;
    font-style: normal;
}
html, body 
{ 
    margin: 0; 
    border: 0 none; 
    padding: 0; 
    background-color: black;
    height: 100%; 
    min-height: 100%;
}

body
{
    background-color: black;
    background-size: 100% 100%;
}
            
.wrapper, .container 
{
    height: 250px;
    width: 800px;
}

.wrapper 
{
    bottom: 50%;
    right: 50%;
    position: absolute;
    max-height: 100%;
    max-width: 100%;
}

.container 
{
    left: 50%;
    position: relative;
    top: 50%;
}

.mad_header
{
    width: 100%;
    height: 10px;
    padding-bottom: 35px;
    margin-top: -75px;
    background: url('images/MYARTISTDNA.png') no-repeat;
}
.right
{
    float: right;
    width: 50%;
    height: 250px;
    background-color: #eee;
    margin: 0;
    padding: 0;
}

.left {
    float: left;
    width: 50%;
    height: 250px;
    background-color: white;
    margin: 0;
    padding: 0;
}
.login_header {
    width: 100%;
    height: 30px;
    font-size: 16px;
    padding-left: 20px;
    padding-top: 15px;
    background: url('images/bg_sign_up.gif') no-repeat;
    color: #ddd;
    font-family:"Teletex Light",'Habibi', Arial, Helvetica, sans-serif; 
}
#login_dialog {
    position: relative;
}
.email_header {
    position: absolute;
    top: 28px;
    left: 16px;
    font-size: 12px;
    font-family: sans-serif;
    color: #666;
}
input {
    width: 175px;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
}
input#username {
    position: absolute;
    top: 45px;
    left: 16px;
}
.password_header {
    position: absolute;
    top: 28px;
    left: 206px;
    font-size: 12px;
    font-family: sans-serif;
    color: #666;
}
input#password {
    position: absolute;
    top: 45px;
    left: 206px;
}
.forgot_login_box {
    position: absolute;
    top: 146px;
    left: 18px;
    height: 40px;
    width: 364px;
    background: url('images/bg_sign_up.gif') no-repeat;
}
.forgot_password {
    float: left;
    padding-top: 12px;
    padding-left: 8px;
    font-size: 12px;
    font-family: sans-serif;
}
.forgot_password a
{
    font-size: 12px;
    font-family: sans-serif;
    text-decoration: underline;
    color: #51C3C4;
}
.login {
    height: 28px;
    float: right;
    font-size: 16px;
    padding-left: 20px;
    padding-top: 12px;
    border: 0 none;
    margin: 0;
    padding-right: 12px;
    background:url('images/button_signup.gif') no-repeat;
    background-size: auto 100%;
}
.login button {
    font-size: 16px;
    border: 0 none;
    margin: 0;
    color: #ddd; 
    background-color: transparent;
    border:none; 
    cursor: pointer;
    font-family:"Teletex Light",'Habibi', Arial, Helvetica, sans-serif;
}
.login_fb {
    position: absolute;
    top: 85px;
    left: 20px;
}
.login_fb div {
    height: 25px;
    width: 155px;
    background: url('images/openid_signin_banner.png') -137px -86px no-repeat;
}
.login_tw {
    position: absolute;
    top: 85px;
    left: 233px;
}
.login_tw div {
    height: 25px;
    width: 155px;
    background: url('images/openid_signin_banner.png') -138px -35px no-repeat
}
.dash {
    position: absolute;
    top: 97px;
    left: 177px;
    width: 13px;
    height: 1px;
    background-color: #D2E0EB;
}
.dash_or {
    position: absolute;
    top: 91px;
    left: 194px;
    color: #458EC5;
    font-size: 12px;
    font-family: Helvetica, sans-serif;
}
.dash2 {
    position: absolute;
    top: 97px;
    left: 215px;
    width: 13px;
    height: 1px;
    background-color: #D2E0EB;
}
.right_header {
    width: 100%;
    height: 30px;
    font-size: 16px;
    padding-top: 15px;
    background: url('images/bg_sign_up.gif') no-repeat;
    color: #ccc;
    font-family:"Teletex Light",'Habibi',Arial, Helvetica, sans-serif; 
}

.beta_line
{
    width: 100%;
    text-align: center;
    font-family: sans-serif;
    font-size: 20px;
    color: gray;
    padding-top: 40px;
}

.be_seen
{
    width: 360px;
    text-align: center;
    font-family:"Teletex Light",'Habibi',Arial, Helvetica, sans-serif; 
    font-size: 18px;
    color: #ccc;
    background: url('images/bg_sign_up.gif') repeat;
    padding-top: 22px;
    padding-bottom: 18px;
    margin-left: 20px;
    margin-top: 18px;
}
.validate_login
{
    height: 20px;
    font-family: sans-serif;
    font-size: 13px;
    padding-top: 5px;
    padding-left: 20px;
    color: red;
}
.copyright
{
    padding-top: 20px; 
    float: left;
    font-family: sans-serif;
    font-size: 11px;
    color: #aaa;
}
.social {
    padding-top: 12px; 
    float: right;
    font-family: sans-serif;
    font-size: 11px;
    color: #aaa;
}
.social a img {
    border-style: none;
}

.gray_cover
{
    position: absolute;
    left: 0;
    top: 0;
    z-index: 0;
    width: 100%;
    height: 100%;
    background: #080808;
    opacity: 0.65;
    -ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=65)";
}

.fb_icon, .tw_icon
{
    height: 20px;
    width: 20px;
    margin-bottom: -5px;
    padding-left: 5px;
}
            
.video_wrapper, .video_container {
    height: 510px;
    width: 853px;
}
.video_wrapper  {
    bottom: 50%;
    right: 50%;
    position: absolute;
    max-height: 100%;
    max-width: 100%;
}
.video_container {
    top: 50%;
    left: 50%;
    position: relative;
}
.top_bar {
    width: 100%;
    height: 40px;
    background: url('images/bg_sign_up.gif') no-repeat;
}

.close {
    height: 28px;
    float: right;
    font-size: 16px;
    padding-left: 20px;
    padding-top: 12px;
    border: 0 none;
    margin: 0;
    padding-right: 12px;
    background:url('images/button_signup.gif') no-repeat;
    background-size: auto 100%;
}
.close button {
    font-size: 16px;
    border: 0 none;
    margin: 0;
    color: #ddd; 
    background-color: transparent;
    border:none; 
    cursor: pointer;
    font-family:"Teletex Light",'Habibi',Arial, Helvetica, sans-serif;
}

input {
    border: 1px solid #ccc;
    padding-top: 3px;
    padding-bottom: 3px;
    padding-left: 3px;
    padding-right: 0px;
}

.watch_learn {
    height: 78px;
    padding-top: 30px;
}
.watch_video_box {
    float: left;
    width: 167px;
    height: 78px;
    margin-left: 20px;
    text-align: center;
    background: url('images/blue_hatch.png') no-repeat;
    background-size: 100% 100%;
    cursor: pointer;
}
.learn_more_box {
    float: right;
    width: 167px;
    height: 78px;
    margin-right: 20px;
    text-align: center;
    background: url('images/red_hatch.png') no-repeat;
    background-size: 100% 100%;
    cursor: pointer;
}
.watch_video, .learn_more
{
    font-family: "Teletex Light",'Habibi',Arial, Helvetica, sans-serif;
    font-size: 27px;
    color: white;
    margin-top: 16px;
    margin-top: 10px\9;
    text-decoration: none;
}

.watch_learn a
{
    text-decoration: none;
}

        </style>
        
        <!--[if lt IE 9]>
            <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        
        <link href="https://vjs.zencdn.net/c/video-js.css" rel="stylesheet">
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.js" type="text/javascript"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js" type="text/javascript"></script>
        <script src="https://vjs.zencdn.net/c/video.js"></script>
        <script src="/js/login_signup.js" type="text/javascript"></script>
        
        <script type="text/javascript">

function showVideo()
{
    $('.wrapper').hide();
    $('.video_wrapper').show();
}

function closeVideo()
{
    $('.video_wrapper').hide();
    $('.wrapper').show();
    var myPlayer = _V_("my_video_1");
    myPlayer.pause();
}

var num_images = 3;

function rand(n)
{
    return Math.floor( Math.random() * n + 1 );
}

$(document).ready(function()
{
    var image = 'images/landing_bg' + rand(num_images) + '.jpg';
    $("body").css('background',"url('" + image + "')");
});

        </script>
    </head>
    <body>
        <div class="gray_cover"></div>
        <div class="wrapper">
            <div class="container">
                <div class='mad_header'>
                </div>
                <div class="left">
                    <div class="login_header">
                        LOG IN TO MYARTISTDNA
                    </div>
                    <div id='login_dialog'>
                        <div class='email_header'>
                            Email Address
                        </div>
                        <input id='username' name='username' type='text' <?=$value_user_name;?>/>
                        <div class='password_header'>
                            Password
                        </div>
                        <input id='password' name='password' type='password'/>
                        <div id='validate-login' class='validate_login'>
                        </div>
                        <div class='forgot_login_box'>
                            <div class='forgot_password'>
                                <a href="/forgot_password.html">Forgot your password?</a>
                            </div>
                            <div class='login'>
                                <button onclick='onLoginClick();'>LOG IN</button>
                            </div>
                        </div>
                        <div class='login_fb'>
                            <a href='/Login_Twitbook/login-facebook.php'>
                                <div></div>
                            </a>
                        </div>
                        <div class='dash'></div>
                        <div class='dash_or'>OR</div>
                        <div class='dash2'></div>
                        <div class='login_tw'>
                            <a href='/Login_Twitbook/login-twitter.php'>
                                <div></div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="right">
                    <div class='right_header'>
                    </div>
                    <div class='be_seen'>
                        BE SEEN. BE HEARD. BE INDEPENDENT.
                    </div>
                    <div class='watch_learn'>
                        <div class='watch_video_box' onclick='showVideo();'>
                            <div class='watch_video'>WATCH VIDEO</div>
                        </div>
                        <a href='/home.php'>
                            <div class='learn_more_box'>
                                <div class='learn_more'>LEARN MORE</div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="copyright_social">
                    <div class="copyright">
                        Copyright &copy; 2010-2012 MyArtistDNA, All Rights Reserved.
                    </div>
                    <div class="social">
                        Follow Us: 
                        <a href="http://facebook.com/pages/MyArtistDNA/106114012796731">
                            <img class="fb_icon" src="images/fb_icon_color.png"/>
                        </a>
                        <a href="http://twitter.com/myartistdna">
                            <img class="tw_icon" src="images/tw_icon_color.png"/>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="video_wrapper" style='display: none;'>
            <div class="video_container">
                <div class='top_bar'>
                    <div class='close'>
                        <button onclick='closeVideo();'>CLOSE</button>
                    </div>
                </div>
                <video id="my_video_1" class="video-js vjs-default-skin" width="853" height="480" controls="controls" preload="auto" poster="/images/mad_poster.png" data-setup="{}">
                    <source src="https://www.myartistdna.com/mad.webm" type="video/webm" />
                    <source src="https://www.myartistdna.com/mad.iphone.mp4" type="video/mp4" />
                    <source src="https://www.myartistdna.com/mad.ogv" type="video/ogg" />
                </video>
            </div>
        </div>
    </body>
</html>


