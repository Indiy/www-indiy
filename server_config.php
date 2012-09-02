<?php



    /*
    
    //Production 
    error_reporting(0);
    $dbhost		=	"localhost";
    $dbusername	=	"madcom_user";
    $dbpassword	=	"MyartistDNA!";
    $dbname		=	"madcom_mysql";
    
    */

    /*
    
    // MADDEV.COM
    error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE); 
    $dbhost		=	"localhost";
    $dbusername	=	"maddvcom_user";
    $dbpassword	=	"MyartistDNA!";
    $dbname		=	"maddvcom_mysql";

    */

    $connect 	= 	mysql_connect($dbhost, $dbusername, $dbpassword);
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
