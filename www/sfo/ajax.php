<?

	include('includes/functions.php');	
	include('includes/config.php');

	
	if ($_REQUEST["type"] != "") {
		$type = $_REQUEST["type"];
		
		// Build Pages
		$pages = mq("select * from `[p]sfo_artists` where `type`='{$type}' order by `id` desc");
		
		while ($row = mf($pages)) {
			
			$id = $row["id"];
			$name = stripslashes($row["name"]);
			$filename = $row["filename"];
			$type = $row["type"];
			if ($_REQUEST["delete"] == "true") { $delete = ' <a href="?admin=true&delete='.$id.'"><small>Delete</small></a>'; }
			$list .= '
				<li><a href="download.php?file='.$filename.'&type=artists">'.$name.'</a>'.$delete.'</li>
				';
		}
		
		if ($list == "") { $list = '<p>No files availables</p>'; }
		
		$page_content = "<h1>{$type}</h1><ul>{$list}</ul><div class='clear'></div>";
		echo $page_content;
	}

	
	if ($_REQUEST["admin"] == "true") {
		$type = $_REQUEST["type"];
		
		// Build Pages
		$pages = mq("select * from `[p]sfo_submissions` order by `id` desc");
		
		while ($row = mf($pages)) {
			
			$id = nohtml($row["id"]);
			$name = nohtml($row["name"]);
			$title = nohtml($row["title"]);
			$email = nohtml($row["email"]);
			$filename = $row["filename"];
			
			$list .= '
				<li><a href="download.php?file='.$filename.'&type=submissions">&raquo; '.$title.' &laquo;</a> <a href="?admin=true&delete='.$id.'&artist=true"><small>Delete</small></a><br /><small>Submitted By: '.$name.' - '.$email.'</small></li>
				';
		}
		
		if ($list == "") { $list = '<li><p>No files availables</p></li>'; }
		
		$page_content = "<h1>Files Submitted</h1><ul>{$list}</ul><div class='clear'></div>";
		echo $page_content;
	}
		
?>