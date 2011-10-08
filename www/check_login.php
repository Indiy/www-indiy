<?php session_start();
include('includes/config.php');
include('includes/functions.php');
if ($_REQUEST["username"]!='' && $_REQUEST["password"]!='') {
	$user = addslashes($_REQUEST['username']);
	$pass = md5($_REQUEST['password']);	
	$result = mysql_query("select id,url,username,password from mydna_musicplayer where (url='$user' || username='$user') AND password='$pass'");
	$me_row = mysql_num_rows($result);	
	if ($me_row > 0) {	
			while($row = mf($result)){
				$myid = $row["id"];				
				//session_regenerate_id();
				$_SESSION['me'] = $row['id'];
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

