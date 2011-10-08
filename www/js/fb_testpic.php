<?php
/**url*of*website**/
$fbg_url=file_get_contents('http://graph.facebook.com/kumar.sekhar?fields=picture');
$data=json_decode($fbg_url,true);
$fb_picture=$data['picture'];
echo $fb_picture;
?>