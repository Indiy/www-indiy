<?php

    require_once '../includes/config.php';
	require_once '../includes/functions.php';	
	if( $_SESSION['sess_userId'] == '')
	{
		header("Location: index.php");
		exit();
	}
    
    echo "<html><body><pre>\n";

    function try_abbrev($abbrev,$id)
    {
        $ret = update('abbrev',$abbrev,'id',$id);
        if( $ret )
            echo "Saved abbrev $abbrev for $id\n";
        return $ret;
    }
    
    function try_split($name,$sep,$id)
    {
        $words = explode($sep,$name);
        if( count($words) > 1 )
        {
            $abbrev = $words[0][0];
            for( $i = 1 ; $i < count($words) ; ++$i)
            {
                $abbrev .= $words[$i][0];
                $abbrev = strtolower($abbrev);
                if( try_abbrev($abbrev,$id) )
                    return TRUE;
            }
        }
        return FALSE:
    }
    
    function try_caps($name,$id)
    {
        $abbrev = $name[0];
        $last_add = 0;
        for( $i = 1 ; $i < strlen($name) ; ++$i )
        {
            $c = $name[$i];
            if( is_upper($c) )
            {
                $last_add = $i;
                $abbrev .= $c;
                if( try_abbrev($abbrev,$id) )
                    return TRUE;
            }
        }
        for( $i = $last_add + 1; $i < strlen($name) ; ++$i )
        {
            $c = $name[$i];
            $abbrev .= $c;
            if( try_abbrev($abbrev,$id) )
                return TRUE;
        }
        return FALSE;
    }
    
    function is_upper($c)
    {
        if( $c >= 'A' && $c <= 'Z' )
            return TRUE;
        return FALSE;
    }
    
    function try_shortened($name,$id)
    {
        $abbrev = $name[0];
        
        for( $i = 1 ; $i < strlen($name) ; ++$i )
        {
            $c = $name[$i];
            $abbrev .= $c;
            if( try_abbrev($abbrev,$id) )
                return TRUE;
        }
        return FALSE;
    }

    $sql = "SELECT * FROM mydna_musicplayer WHERE abbrev IS NULL";
    $q = mq($sql);
    while( $artist = mf($q) )
    {
        $id = $artist['id'];
        $done = FALSE;
        $name = $artist['artist'];

        if( try_split($name,' ',$id) )
            continue;
        if( try_split($name,'-',$id) )
            continue;
        
        if( try_caps($name,$id) )
            continue;
        
        if( try_shortened($name,$id) )
            continue;
        
        echo "failed to find an abbrev for $name,$id\n";
    }


?>
