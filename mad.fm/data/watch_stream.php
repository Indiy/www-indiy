<?

$SERVER = 'http://myartistdna.fm:8000'; 
$STATS_FILE_PREFIX = '/status.xsl?mount=/stream_';

$FILE = "/tmp/mad_fm_genre_data.json";

print "Starting run forever\n";

$g_data = array("rock" => array(),
                "dance" => array(),
                "chill" => array(),
                "bounce" => array(),
                );


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

while( TRUE )
{
    $changed = FALSE;
    foreach( $g_data as $k => $v )
        $changed |= get_stream_info($k);
    if( $changed )
        write_data();
    sleep(5);
}
print "Done done\n";

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
        if( $last_start > 0 && $last_track && $calc_duration < 20*60 )
            $track_info[$last_track] = $calc_duration;
        
        $duration = 0;
        if( $track_info[$track] )
            $duration = $track_info[$track];

        print "genre: $genre, new track: $track, duration: $duration\n";
        $data = array("artist" => $artist,
                      "song" => $song,
                      "start" => $start,
                      "duration" => $duration);
    
        array_unshift($history,$data);
        $history = array_slice($history,0,20);
        
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
