<?php

    $table = '';
    $artist_abbrev = '';
    function create_abbrevs()
    {
        function try_abbrev($abbrev,$id)
        {
            global $artist_abbrev;
            global $table;
            if( strlen( $artist_abbrev ) )
            {
                $abbrev = $artist_abbrev . '_' . $abbrev;
            }
            $abbrev = strtolower($abbrev);
            $ret = update($table,'abbrev',$abbrev,'id',$id);
            if( $ret )
                ;//echo "Saved abbrev $abbrev for $id\n";
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
                    $word = $words[$i];
                    if( strlen($word) > 0 )
                    {
                        $abbrev .= $word[0];
                        if( try_abbrev($abbrev,$id) )
                            return TRUE;
                    }
                }
            }
            return FALSE;
        }
        
        function try_caps($name,$id)
        {
            $abbrev = $name[0];
            $last_add = 0;
            for( $i = 1 ; $i < strlen($name) ; ++$i )
            {
                $c = $name[$i];
                if( ctype_upper($c) )
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
                if( ctype_alnum($c) )
                {
                    $abbrev .= $c;
                    if( try_abbrev($abbrev,$id) )
                        return TRUE;
                }
            }
            return FALSE;
        }
        
        function try_shortened($name,$id)
        {
            $abbrev = $name[0];
            
            for( $i = 1 ; $i < strlen($name) ; ++$i )
            {
                $c = $name[$i];
                if( ctype_alnum($c) )
                {
                    $abbrev .= $c;
                    if( try_abbrev($abbrev,$id) )
                        return TRUE;
                }
            }
            return FALSE;
        }
        function cleanup_name($name)
        {
            for( $i = 0 ; $i < strlen($name) ; ++$i )
            {
                $c = $name[$i];
                if( !ctype_alnum($c) )
                    $name[$i] = ' ';
            }
            return $name;
        }
        global $artist_abbrev;
        global $table;
        
        $table = 'mydna_musicplayer';
        $sql = "SELECT * FROM mydna_musicplayer WHERE abbrev IS NULL";
        //echo "sql='$sql'\n";
        $q = mq($sql);
        while( $artist = mf($q) )
        {
            $id = $artist['id'];
            $name = $artist['artist'];
            $name = cleanup_name($name);
            
            //echo "artist: $name($id)\n";
            
            if( try_split($name,' ',$id) )
                continue;
            
            if( try_caps($name,$id) )
                continue;
            
            if( try_shortened($name,$id) )
                continue;
            
            //echo "failed to find an abbrev for $name,$id\n";
        }
        
        
        $table = 'mydna_musicplayer_audio';
        $sql = "SELECT mydna_musicplayer_audio.*,mydna_musicplayer.abbrev AS artist_abbrev FROM mydna_musicplayer_audio "
        . "JOIN mydna_musicplayer ON mydna_musicplayer_audio.artistid = mydna_musicplayer.id "
        . "WHERE mydna_musicplayer_audio.abbrev IS NULL";
        
        //echo "sql='$sql'\n";
        $q = mq($sql);
        while( $song = mf($q) )
        {
            $id = $song['id'];
            $artist_abbrev = $song['artist_abbrev'];
            $name = $song['name'];
            $name = cleanup_name($name);
            
            //echo "artist: $name($id)\n";
            
            if( try_split($name,' ',$id) )
                continue;
            
            if( try_caps($name,$id) )
                continue;
            
            if( try_shortened($name,$id) )
                continue;
            
            //echo "failed to find an song abbrev for $name,$id\n";
        }
    }


?>

