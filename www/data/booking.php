<?
    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");

    require_once('../includes/functions.php');   
    require_once('../includes/config.php');
    
    $artist_id = $_REQUEST['artist_id'];

    $name = $_REQUEST['name'];
    $email = $_REQUEST['email'];
    $date = $_REQUEST['date'];
    $location = $_REQUEST['location'];
    $budget = $_REQUEST['budget'];
    $comments = $_REQUEST['comments'];

    $artist = mf(mq("SELECT * FROM `[p]musicplayer` WHERE `id`='$artist_id' LIMIT 1"));
    $artist_name = $artist['name'];

    $to = "info@myartistdna.com";
    $subject = "Booking for $artist_name";
    $message = <<<END

Booking request:

----------------------------------

Artist: $artist_name
Name: $name
Email: $email
Date: $date
Location: $location
Budget: $budget
Comments: $comments

----------------------------------

Be Heard. Be Seen. Be Independent.

END;
    $from = "no-reply@myartistdna.com";
    $headers = "From:" . $from;

    mail($to,$subject,$message,$headers);
    
    echo "{ \"success\": 1 }\n";
?>