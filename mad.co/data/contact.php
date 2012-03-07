<?

    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");

    $post_body = file_get_contents('php://input');
    $data = json_decode($post_body,TRUE);

    $name = $data['name'];
    $email = $data['email'];
    $subject = $data['subject'];

    $subject = 'Contact form from mad.co';

    $message = <<<END

Contact form from mad.co:

Name: $name 
Email: $email
Subject: $subject

Be Heard. Be Seen. Be Independent.

END;

    $to = "jim@blueskylabs.com";
    $from = "no-reply@myartistdna.com";
    $headers = "From:" . $from;

    mail($to,$subject,$message,$headers);
    
    echo "{ \"success\": 1 }\n";

?>
