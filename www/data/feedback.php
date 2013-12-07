<?
    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");

    require_once('../includes/functions.php');   
    require_once('../includes/config.php');

    $form_contents = '';

    foreach( $_GET as $key => $value )
    {
        $form_contents .= "$key:\n$value\n\n";
    }

    $to = "info@myartistdna.com";
    $subject = "User Feedback";
    $message = <<<END

User Feedback:

----------------------------------

$form_contents

----------------------------------

Be Heard. Be Seen. Be Independent.

END;
    $from = "no-reply@myartistdna.com";
    $headers = "From:" . $from;

    mail($to,$subject,$message,$headers);
    
    echo "{ \"success\": 1 }\n";
?>

