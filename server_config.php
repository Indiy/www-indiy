<?php

/*
//Production 

error_reporting(0);
$dbhost		=	"localhost";
$dbusername	=	"myartist_user";
$dbpassword	=	"MyartistDNA!";
$dbname		=	"myartist_mysql";
*/

/*
// MADDEV.COM
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE); 
$dbhost		=	"localhost";
$dbusername	=	"madcom_user";
$dbpassword	=	"MyartistDNA!";
$dbname		=	"madcom_mysql";

*/

$connect 	= 	mysql_connect($dbhost, $dbusername, $dbpassword);
mysql_select_db($dbname,$connect) or die ("Could not select database");

?>
