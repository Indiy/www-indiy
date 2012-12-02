<?php

    $is_windows = FALSE;
    
    if( strpos($_SERVER['HTTP_USER_AGENT'],"Windows") !== FALSE )
    {
        $is_windows = TRUE;
    }
    
    $is_msie = FALSE;
    if( strpos($_SERVER['HTTP_USER_AGENT'],"MSIE") !== FALSE )
    {
        $is_msie = TRUE;
    }

    $return_url = FALSE;
    if( isset($_GET['return_url']) )
    {
        $return_url = $_GET['return_url'];
    }
    else
    {
        if( __FILE__ != "unsupported_browser.php" )
        {
            if( $_SERVER["HTTPS"] == "on" )
            {
                $return_url .= "https://";
            }
            else
            {
                $return_url .= "http://";
            }
            $return_url .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
    }

?>

<html>
<head>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/chrome-frame/1/CFInstall.min.js"></script>

<script type="text/javascript">

var g_returnUrl = <?=json_encode($return_url);?>;

function popChromeFrame()
{
    var opts = {
        mode: "overlay"
    };
    
    if( g_returnUrl )
    {
        opts['destination'] = g_returnUrl;
    }

    CFInstall.check(opts);
}
</script>

</head>
<body>
<h3>Sorry!</h3>
This site does not support your browser.<br/>
<br/>
<a href="http://firefox.com">Firefox</a><br/>
<a href="http://www.google.com/chrome">Google Chrome</a><br/>
<?php  if( $is_windows ):  ?>
    <a href="http://windows.microsoft.com/en-US/internet-explorer/download-ie">Internet Explorer 9.0 or above</a><br/>
<?php endif; ?>
<?php  if( $is_msie ):  ?>
    Or keep your current browser and enhance it with <a href="javascript:popChromeFrame();">Google Chrome Frame</a>
<?php endif; ?>


</body>

