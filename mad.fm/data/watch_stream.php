<?

$SERVER = 'http://myartistdna.fm:8000'; 
$STATS_FILE = '/status.xsl';

$FILE = "/tmp/mad_fm.json";

print "Starting run forever\n";

$history = array();
$track_info = array();

$json = file_get_contents($FILE);

if( $json )
{
    print "loading old data\n";
    $data = json_decode($json,TRUE);
    if( $data['history'] )
        $history = $data['history'];
    if( $data['track_info'] )
        $track_info = $data['track_info'];
}

$last_track = FALSE;
$last_start = 0;
while( TRUE )
{
    $radio_info = get_radio_info();

    $artist = $radio_info['now_playing']['artist'];
    $song = $radio_info['now_playing']['track'];

    $track = $artist . ' - ' . $song;

    if( $track != $last_track )
    {
        print "\n";
        $start = time();
        if( $last_start > 0 && $last_track )
            $track_info[$last_track] = $start - $last_start;
        $last_start = $start;
        
        $duration = 0;
        if( $track_info[$track] )
            $duration = $track_info[$track];

        print "new track: $track, duration: $duration\n";
        $data = array("artist" => $artist,
                      "song" => $song,
                      "start" => $time,
                      "duration" => $duration);
    
        array_unshift($history,$data);
        $history = array_slice($history,0,20);
        
        $last_track = $track;
        
        $data = array("history" => $history,
                      "track_info" => $track_info);
        $json = json_encode($data);
        file_put_contents($FILE,$json,LOCK_EX);
        print "updated file\n";
    }
    
    print ".";
    sleep(5);
}

print "Done done\n";

function get_radio_info()
{
    global $SERVER;
    global $STATS_FILE;

    $url = $SERVER.$STATS_FILE;
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
