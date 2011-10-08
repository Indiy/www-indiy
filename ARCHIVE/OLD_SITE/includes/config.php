<?php

	session_start();
	error_reporting(0);

	$dbhost		=	"localhost";
	$dbusername	=	"myartist_user";
	$dbpassword	=	"MyartistDNA!";
	$dbname		=	"myartist_mysql";
	$connect 	= 	mysql_connect($dbhost, $dbusername, $dbpassword);
	mysql_select_db($dbname,$connect) or die ("Could not select database");
	
	// LOAD SYSTEM SETTINGS ////////////////////////////////////////////////////////////

	$prefix 			= "mydna_";
	
	$jibya = mysql_fetch_array(mysql_query("select * from `{$prefix}musicplayer_config` where `id`='1' limit 1"));
	
	$domainName 		= $jibya["domain"];
	$trueSiteUrl 		= $jibya["url"];
	$siteUrl 			= $jibya["url"];
	$playerUrl 			= $jibya["playerUrl"];
	$siteTitle 			= $jibya["title"];

?>