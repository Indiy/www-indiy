<?php

    // This file should have db configuration
    include_once("../../server_config.php");

	session_start();

	$prefix 			= "mydna_";
	
	$jibya = mysql_fetch_array(mysql_query("select * from `{$prefix}musicplayer_config` where `id`='1' limit 1"));
	
	$domainName 		= $jibya["domain"];
	$trueSiteUrl 		= $jibya["url"];
	$siteUrl 			= $jibya["url"];
	$playerUrl 			= $jibya["playerUrl"];
	$siteTitle 			= $jibya["title"];

?>