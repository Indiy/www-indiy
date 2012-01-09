<?
    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");

    include('../includes/functions.php');   
    include('../includes/config.php');

    $form_contens = '';

    foreach( $_POST as $key => $value )
    {
        $form_contents .= "$key:\n$value\n";
    }

    $to = "jim@blueskylabs.com";
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

