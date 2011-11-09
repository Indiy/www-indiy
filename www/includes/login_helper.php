<?php


function loginArtistFromRow($row)
{
    $inTwoMonths = 60 * 60 * 24 * 60 + time();
    setcookie($cookievar, $row['id'], $inTwoMonths);

    $myid = $row['id'];
    $_SESSION['sess_userId'] =	$myid;		
    $_SESSION['sess_userName'] = $row['artist'];
    $_SESSION['sess_userUsername'] = $row['username'];
    $_SESSION['sess_userEmail'] =  $row['email'];
    $_SESSION['sess_userType'] = 'ARTIST';
    $_SESSION['sess_userURL'] = $userdata['url'];
    
    $url = trueSiteUrl() . "/manage/artist_management.php?userId=$myid&session_id=". session_id());
    
    return $url;
}

?>