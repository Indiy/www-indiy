<?php
    
    define("ARTIST_PAYOUT_PERCENT",0.8); 
    
    
    $new_include_path = get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . "/../..";
    set_include_path($new_include_path);

    // This file should have db configuration
    require_once("server_config.php");
	session_start();

	$prefix = "mydna_";

	$jibya = mysql_fetch_array(mysql_query("select * from `{$prefix}musicplayer_config` where `id`='1' limit 1"));
	$domainName = $jibya["domain"];
	$trueSiteUrl = $jibya["url"];
	$siteUrl = $jibya["url"];
	$playerUrl = $jibya["playerUrl"];
	$siteTitle = $jibya["title"];
    
    $cart_base_url = $jibya["cart_base_url"];
?>