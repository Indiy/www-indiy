<?php

    require_once '../includes/config.php';
    require_once '../includes/functions.php';
    
    $register_token = $_REQUEST['token'];
    
    $fan = mf(mq("SELECT * FROM fans WHERE register_token='$register_token'"));
    if( !$fan )
    {
        echo <<<END

<html>
        Unknown Token<br/>
        <form>
            Enter Token: <input name='token'/><br/>
            <br/>
            <input type='submit' value='Submit'/><br/>
        </form>
</html>

END;
        die();
    }
    
    $fan_email = $fan['email'];
    
    include_once 'templates/register.html';
    
?>