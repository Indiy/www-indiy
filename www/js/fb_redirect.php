<?php

//$uri= 'http://graph.facebook.com/'.$getArtistImage["username"].'/picture';
$uri= 'https://graph.facebook.com/680812941/picture?type=small';
  //$uri= $_GET["url"];
  $fh = fopen($uri, 'r');
  $details = stream_get_meta_data($fh);

  foreach ($details['wrapper_data'] as $line) {
   if (preg_match('/^Location: (.*?)$/i', $line, $m)) {
     // There was a redirect to $m[1]
     echo( substr( $m[0], 10 ) );
   }
  }
?>