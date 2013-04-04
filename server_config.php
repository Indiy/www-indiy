<?php

    /*
    
    //Production 
    error_reporting(0);
    $dbhost = "madprod.cghmds5s4dwn.us-east-1.rds.amazonaws.com";
    $dbusername = "madcom_user";
    $dbpassword = "GpLxHCPnAbLWQRFX";
    $dbname = "madcom";
    
    $domainName = "http://www.myartistdna.com";
    $trueSiteUrl = "http://www.myartistdna.com";
    $siteUrl = "http://www.myartistdna.com";
    $playerUrl = "http://www.myartistdna.com/?url=";
    $siteTitle = "MyArtistDNA";
    $cart_base_url = "http://www.myartistdna.com";
    $g_artist_file_base_url = "http://static.myartistdna.com";
    $g_aws_static_bucket = "static.myartistdna.com";
    $g_static_base_url = "http://static.myartistdna.com";
    $g_api_base_url = "http://api.myartistdna.com";
    $g_manage_base_url = "http://manage.myartistdna.com";
    $g_staging_host = "staging.myartistdna.com";
    $g_font_base_url = "http://static.myartistdna.com.s3-website-us-east-1.amazonaws.com";

    $ALT_IMAGE_REV_KEY = "1";
    
    $stream_prefix = "stream_mad_";
    $fm_port = 8000;

    */

    /*
    
    // MADDEV.COM
    error_reporting(E_ERROR | E_WARNING | E_PARSE); 
    $dbhost = "localhost";
    $dbusername = "madd3v_user";
    $dbpassword = "Wp2T4erBEjREdwrS";
    $dbname = "madd3v.com";
    
    $domainName = "http://www.madd3v.com";
    $trueSiteUrl = "http://www.madd3v.com";
    $siteUrl = "http://www.madd3v.com";
    $playerUrl = "http://www.madd3v.com/?url=";
    $siteTitle = "MyArtistDNA";
    $cart_base_url = "http://www.madd3v.com";
    $g_artist_file_base_url = "http://static.madd3v.com";
    $g_aws_static_bucket = "static2.madd3v.com";
    $g_static_base_url = "http://static.madd3v.com";
    $g_api_base_url = "http://api.madd3v.com";
    $g_manage_base_url = "http://manage.madd3v.com";
    $g_staging_host = "staging.madd3v.com";
    $g_font_base_url = "http://static2.madd3v.com.s3-website-us-east-1.amazonaws.com";
    
    $ALT_IMAGE_REV_KEY = "1";
    
    $stream_prefix = "stream_maddev_";
    $fm_port = 8000;
    
    */
    
    $g_root_redirect_ip = "54.225.71.64";
    
    $g_access_key_id = "AKIAIP2VCXXJMBG4K75Q";
    $g_secret_access_key = "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";

    $connect = mysql_connect($dbhost, $dbusername, $dbpassword);
    mysql_select_db($dbname,$connect) or die ("Could not select database");

    $SandboxFlag = TRUE;
    if( $SandboxFlag == TRUE )
    {
        $API_UserName="mad_1346558535_biz_api1.myartistdna.com";
        $API_Password="1346558558";
        $API_Signature="Ab.Ua9MmJioLkDJWgEubbcrQ8dONA9x1bbDIhJetM9P6ktHGYZ6AK3D-";
    }
    else
    {
        $API_UserName="wtl_api1.lomaxco.com";
        $API_Password="B9K3CSH3AMLQSRRU";
        $API_Signature="AYPWYcsCU66yye7Ljup18V27fG8LAJE0BPSJQ5lI8-ogx1T9aLnD7sS3";
    }

?>