<?
    header("Content-Type: application/json");
    header("Cache-Control: no-cache");
    header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");


    $post_body = file_get_contents('php://input');
    $data = json_decode($post_body,TRUE);
    $subject = 'Contact Form Request: ' . $data['subject'];
    $reason = $data['reason'];
    $name = $data['name'];
    $email = $data['email'];
    $body = $data['body'];
    
    $to = 'info@myartistdna.com';

    $message = "NAME: $name\n\nEMAIL: $email\n\nREASON: $reason\n\nMESSAGE: $body\n";
    $from = "no-reply@myartistdna.com";
    $headers = "From:" . $from;
    
    mail($to,$subject,$message,$headers);
    
    print "{\"success\": 1}\n";
?>