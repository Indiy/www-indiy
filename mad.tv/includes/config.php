<?php
    
    $new_include_path = get_include_path()
        . PATH_SEPARATOR . dirname(__FILE__) . "/../.."
        . PATH_SEPARATOR . dirname(__FILE__) . "/../../includes";
    set_include_path($new_include_path);

    $DB_HOST = "madprod.cghmds5s4dwn.us-east-1.rds.amazonaws.com";
    $DB_USERNAME = "madtv_user";
    $DB_PASSWORD = "GpLxHCPnAbLWQRFX";
    $DB_NAME = "madtv";
    
    $ROOT_URL = "http://tv.myartistdna.com";

    require_once 'functions.php';

    $cookie_domain = str_replace("http://www.","",$trueSiteUrl);
    session_set_cookie_params(30*24*60*60,"/",$cookie_domain);
    ini_set("session.gc_maxlifetime",2*24*60*60);
	//session_start();


?>