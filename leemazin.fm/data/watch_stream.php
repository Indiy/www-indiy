<?php

ignore_user_abort(true);
set_time_limit(0);

$SERVER = 'http://myartistdna.fm:8000'; 
$STATS_FILE_PREFIX = '/status.xsl?mount=/stream_';

$fm_app_site = "leemazin";
$FILE = "/tmp/fm_app_genre_data_$fm_app_site.json";


print "Starting run forever\n";

while( TRUE )
{
    setup_genre_data();
    for( $i = 0 ; $i < 5*12 ; ++$i )
    {
        $changed = FALSE;
        foreach( $g_data as $k => $v )
            $changed |= get_stream_info($k);
        if( $changed )
            write_data();
        sleep(5);
    }
}
print "Done done\n";

function setup_genre_data()
{
    global $g_data;
    global $FILE;
    
    $g_data = array();

    $dbhost		=	"localhost";
    $dbusername	=	"fm_app_user";
    $dbpassword	=	"fm_app_password";
    $dbname		=	"fm_app";
    
    $connect 	= 	mysql_connect($dbhost, $dbusername, $dbpassword);
    mysql_select_db($dbname,$connect) or die ("Could not select database");
    
    $sql = "SELECT * FROM genres WHERE site=\"$fm_app_site\" ORDER BY `order` ASC";
    $q = mysql_query($sql);
    $genre_list = array();
    while( $row = mysql_fetch_array($q) )
    {
        $stream_name = $row['stream_name'];
        $g_data[$stream_name] = array();
    }    
    mysql_close();
    
    $json = file_get_contents($FILE);
    if( $json )
    {
        print "loading old data\n";
        $data = json_decode($json,TRUE);
        foreach( $g_data as $k => $v )
        {
            if( $data[$k] )
                $g_data[$k] = $data[$k];
        }
    }
    print "Found genres: " . implode(', ',$g_data) . "\n";
}

function get_stream_info($genre)
{
    global $g_data;

    $last_track = FALSE;
    $last_start = 0;
    $history = array();
    $track_info = array();

    $data = $g_data[$genre];
    if( $data )
    {
        $history = $data['history'];
        $track_info = $data['track_info'];
        $top = $history[0];
        if( $top )
        {
            $last_start = $top['start'];
            $artist = $top['artist'];
            $song = $top['song'];
            $last_track = $artist . ' - ' . $song;
        }
    }
    
    $radio_info = get_radio_info($genre);
    //var_dump($radio_info);

    $artist = $radio_info['now_playing']['artist'];
    $song = $radio_info['now_playing']['track'];

    $track = $artist . ' - ' . $song;
    //print "\ntrack: $track\n";

    if( $track != $last_track )
    {
    
        print "\n";
        $start = time();
        $calc_duration = $start - $last_start;
        //print "last_start: $last_start, start: $start, calc_duration: $calc_duration\n";
        
        if( $last_start > 0 && $last_track && $calc_duration < 20*60 )
        {
            print "Update track info for ($last_track): $calc_duration\n";
            $track_info[$last_track] = $calc_duration;
        }
        
        $duration = 2*60;
        if( $track_info[$track] )
            $duration = $track_info[$track];

        print "genre: $genre, new track: $track, duration: $duration\n";
        $data = array("artist" => $artist,
                      "song" => $song,
                      "start" => $start,
                      "duration" => $duration);
    
        array_unshift($history,$data);
        $history = array_slice($history,0,20);
        
        //var_dump($track_info);
        $data = array("history" => $history,
                      "track_info" => $track_info);
        
        $g_data[$genre] = $data;
        
        return TRUE;
    }
    print ".";
    return FALSE;
}
function write_data()
{
    global $g_data;
    global $FILE;
    
    $json = json_encode($g_data);
    file_put_contents($FILE,$json,LOCK_EX);
    print "updated file\n";
}

function get_radio_info($genre)
{
    global $SERVER;
    global $STATS_FILE_PREFIX;

    $url = $SERVER . $STATS_FILE_PREFIX . $genre;
    //var_dump($url);
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    $output = curl_exec($ch);
    curl_close($ch);
    
    $radio_info = array();
    $radio_info['server'] = $SERVER;
    $radio_info['title'] = '';
    $radio_info['description'] = '';
    $radio_info['content_type'] = '';
    $radio_info['mount_start'] = '';
    $radio_info['bit_rate'] = '';
    $radio_info['listeners'] = '';
    $radio_info['most_listeners'] = '';
    $radio_info['genre'] = '';
    $radio_info['url'] = '';
    $radio_info['now_playing'] = array();
    $radio_info['now_playing']['artist'] = '';
    $radio_info['now_playing']['track'] = '';
    
    //loop through $ouput and sort into our different arrays
    $temp_array = array();
    
    $search_for = "<td\s[^>]*class=\"streamdata\">(.*)<\/td>";
    $search_td = array('<td class="streamdata">','</td>');
    
    if(preg_match_all("/$search_for/siU",$output,$matches)) {
        foreach($matches[0] as $match) {
            $to_push = str_replace($search_td,'',$match);
            $to_push = trim($to_push);
            array_push($temp_array,$to_push);
        }
    }
    
    //sort our temp array into our ral array
    $radio_info['title'] = $temp_array[0];
    $radio_info['description'] = $temp_array[1];
    $radio_info['content_type'] = $temp_array[2];
    $radio_info['mount_start'] = $temp_array[3];
    $radio_info['bit_rate'] = $temp_array[4];
    $radio_info['listeners'] = $temp_array[5];
    $radio_info['most_listeners'] = $temp_array[6];
    $radio_info['genre'] = $temp_array[7];
    $radio_info['url'] = $temp_array[8];
    
    $x = explode(" - ",$temp_array[9]);
    $radio_info['now_playing']['artist'] = $x[0];
    $radio_info['now_playing']['track'] = $x[1];

    return $radio_info;
}

?>
