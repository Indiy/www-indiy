<?php
    
    $new_include_path = get_include_path()
        . PATH_SEPARATOR . dirname(__FILE__) . "/../.."
        . PATH_SEPARATOR . dirname(__FILE__) . "/../../includes";
    set_include_path($new_include_path);

    $DB_HOST = "madprod.cghmds5s4dwn.us-east-1.rds.amazonaws.com";
    $DB_USERNAME = "madtv_user";
    $DB_PASSWORD = "GpLxHCPnAbLWQRFX";
    $DB_NAME = "madtv";
    $DB_CONNECT = false;
    
    $ROOT_URL = "http://tv.myartistdna.com";
    $BASE_PATH = "/var/www/mad.tv/html";

    require_once 'functions.php';

    ini_set("session.gc_maxlifetime",2*24*60*60);
	//session_start();


?>