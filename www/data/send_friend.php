<?
    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");

    require_once('../includes/functions.php');   
    require_once('../includes/config.php');
    
    $post_body = file_get_contents('php://input');
    $data = json_decode($post_body,TRUE);

    $artist_id = $data['artist_id'];
    $to = $data['to'];
    $from = $data['from'];
    $message = $data['message'];

    $artist = mf(mq("SELECT * FROM `[p]musicplayer` WHERE `id`='{$artist_id}' LIMIT 1"));

    $artist_url = $artist['url'];
    $subject = 'Someone shared an Artist on MyArtistDNA with you';

    $message = <<<END
$from has shared a platform with you on MyArtistDNA.

$message

Check it out at: {$artist_url}.myartistdna.com

Be Heard. Be Seen. Be Independent.

END;
    $from = "no-reply@myartistdna.com";
    $headers = "From:" . $from;

    mail($to,$subject,$message,$headers);
    
    echo "{ \"success\": 1 }\n";
?>
