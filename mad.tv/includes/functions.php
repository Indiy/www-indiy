<?php

    function mad_mysql_connect()
    {
        if( !$GLOBALS['DB_CONNECT'] )
        {
            $connect = mysql_connect($GLOBALS['DB_HOST'], $GLOBALS['DB_USERNAME'], $GLOBALS['DB_PASSWORD']);
            mysql_select_db($GLOBALS['DB_NAME'],$connect) or die ("Could not select database");
            $GLOBALS['DB_CONNECT'] = $connect;
        }
    }

  	function mq($sql)
    {
        mad_mysql_connect();
		return mysql_query($sql);
	}

	function mf($mycontent)
    {
		return mysql_fetch_array($mycontent,MYSQL_ASSOC);
	}
  

?>