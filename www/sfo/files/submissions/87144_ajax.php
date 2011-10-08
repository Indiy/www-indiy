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


?>
