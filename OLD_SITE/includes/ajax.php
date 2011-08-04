<?

	include("functions.php");
	include("config.php");
	
	
	if ($_REQUEST['order'] == "order") {
		
		$array = $_REQUEST['arrayorder'];	
		$database = $_REQUEST['type'];
		$count = 1;
		foreach ($array as $idval) {
			update("[p]".$database,"order",$count,"id",$idval);
			++$count;	
		}
		
		echo "SUCCESS!";
	}
	
	if ($_REQUEST['musiclist'] == "true") {
		
		$artist = $_REQUEST['fromartist'];
		$getid = mf(mq("select `id` from `[p]musicplayer` where `artist`='{$artist}' limit 1"));
		$id = $getid["id"];
		$load = mq("select `id`, `name`, `audio` from `[p]musicplayer_audio` where `artistid`='{$id}' and `type`='0' order by `order` asc, `id` desc");
		while ($row = mf($load)) {
			$list .= '<li class="order"><a href="#" class="addtrack">'.nohtml($row["name"]).' <span style="display: none;">'.$row["id"].'</span></a></li>';
		}
		
		echo '
		<script type="text/javascript">
		$(document).ready(function(){
			/* Add Tracks */
			$("a.addtrack").click(function() {
				var track = $(this).children("span").text();
				var post = "&addtrack=true&track="+track;
				$.post("includes/ajax.php", post, function(respond){
					$("ul.tracks").append(respond);
				});
				return false;
			});
		});
		</script>
		';
		
		echo $list;
	}	
	
	if ($_REQUEST['addtrack'] == "true") {
		
		$me = me();
		$trackid = $_REQUEST['track'];
		
		$get = mf(mq("select `id`,`artistid`,`name`,`image`,`audio`,`download` from `[p]musicplayer_audio` where `id`='{$trackid}' limit 1"));
		$artistid = $get["artistid"];
		$name = $get["name"];
		$image = $get["image"];
		$audio = $get["audio"];
		$download = $get["download"];
		
		insert("[p]musicplayer_audio","artistid|name|audio|image|download|type|user","{$artistid}|{$name}|{$audio}|{$image}|{$download}|1|{$me}");
		$newid = mysql_insert_id();
		//echo '<li id="arrayorder_'.$trackid.'" class="order"><a href="#" class="addtrack">'.nohtml($name).'</a></li>';
		echo '<li id="arrayorder_'.$trackid.'" class="order"><a href="#" onclick="confirmDelete(\'?p=home&delete=true&type=useraudio&a='.$me.'&id='.$newid.'\')">'.nohtml($name).'</a></li>'."\n";
	}	
	
?>