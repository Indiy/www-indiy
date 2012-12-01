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

?>

<html>
<head>
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
    Or keep your current browser and enhance it with <a href="http://google.com/chromeframe">Google Chrome Frame</a>
<?php endif; ?>


</body>

