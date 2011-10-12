<?php session_start();
include('includes/config.php');
include('includes/functions.php');
if ($_REQUEST["username"]!='' && $_REQUEST["password"]!='') {
	$user = addslashes($_REQUEST['username']);
	$pass = md5($_REQUEST['password']);	
	$result = mysql_query("select * from mydna_musicplayer where (email='$user' || url='$user' || username='$user') AND password='$pass' AND activeStatus='1'");
	$me_row = mysql_num_rows($result);	
	if ($me_row > 0) {	
			while($row = mf($result)){
				$myid = $row["id"];				
				//session_regenerate_id();
				$_SESSION['me'] = $row['id'];
				$_SESSION['sess_userId'] =	$row['id'];		
				$_SESSION['sess_userName'] = $row['artist'];
				$_SESSION['sess_userUsername'] = $row['username'];
				$_SESSION['sess_userEmail'] =  $row['email'];							
				
				// Set cookie to expire in two months
				$inTwoMonths = 60 * 60 * 24 * 60 + time();
				// Set the user cookie
				setcookie($cookievar, $myid, $inTwoMonths);
				echo "1";
			}
	}
	else {
	echo '0';
	}
}
else{
	echo '2';
}
?>