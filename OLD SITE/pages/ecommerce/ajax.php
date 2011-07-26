<?

	include(dirname(__FILE__)."/../../../system/functions.php");
	include(dirname(__FILE__)."/../../../system/config.php");
	include(dirname(__FILE__)."/functions.php");
	
	
	if ($_REQUEST['origin'] != "") {
		$porigin = $_REQUEST['origin'];
		$content = mq("select `id`,`navtitle` from `[p]content` where `root`='{$porigin}' order by `order` asc");
		while ($row = mf($content)) {
			$pageid = $row["id"];
			$pagenavtitle = stripslashes($row["navtitle"]);
			if ($psubcat == $pageid) { $selected = " checked"; } else { $selected = ""; }
			$options .= "<input name='subcat[]' type='checkbox' class='radio' value='{$pageid}'{$selected}> {$pagenavtitle}<br />\n";
			
		}
		echo $options;
	}
	
	if ($_REQUEST['products'] == "products") {
		
		$array = $_REQUEST['arrayorder'];	
		
		$count = 1;
		foreach ($array as $idval) {
			update("[p]ecommerce_products","order",$count,"id",$idval);
			++$count;	
		}
		echo "SUCCESS!";
	}

	if ($_REQUEST['photos'] == "photos") {
		
		$array = $_REQUEST['arrayorder'];	
		
		$count = 1;
		foreach ($array as $idval) {
			update("[p]ecommerce_photos","order",$count,"id",$idval);
			++$count;	
		}
		echo "SUCCESS!";

	}	
	

	if ($_REQUEST['colors'] == "colors") {
		
		$array = $_REQUEST['arrayorder'];	
		
		$count = 1;
		foreach ($array as $idval) {
			update("[p]ecommerce_products_colors","order",$count,"id",$idval);
			++$count;	
		}
		echo "SUCCESS!";
	}	
	

?>