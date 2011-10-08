<?php

	$dbhost		=	"mysql50-28.wc2.dfw1.stabletransit.com";
	$dbusername	=	"537132_audiodna";
	$dbpassword	=	"Myartistdna2011";
	$dbname		=	"537132_myaudiodna";
	$connect 	= 	mysql_connect($dbhost, $dbusername, $dbpassword);
	mysql_select_db($dbname,$connect) or die ("Could not select database");

	// LOAD SYSTEM SETTINGS ////////////////////////////////////////////////////////////

	$prefix 			= "mydna_";
	$domainName 		= "http://www.myartistdna.com/sfo";
	$trueSiteUrl 		= "http://www.myartistdna.com/sfo";
	$siteUrl 			= "http://www.myartistdna.com/sfo";

?>