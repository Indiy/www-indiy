<?

	$loadUsername = explode(".", $_SERVER["HTTP_HOST"]);
	if ($loadUsername[0] == "www" || $loadUsername[0] == "myartistdna") {
		$artist_url = $_GET["url"];
	} else {
		$artist_url = $loadUsername[0];
	}

	if (is_file("install.php")) {
	
		include('includes/config.php');
		include('includes/functions.php');
		include("install.php");
		unlink("install.php");
		refresh("2","");
		echo "<p>Installing...</p>";
	
	} else {

		if ($_GET["p"] != "") {

			include('includes/config.php');
			include('includes/functions.php');
		
			// call the function to include the "frame.tpl" file as a string
			$string = get_include_contents('includes/frame.php');
			// break apart the "frame.tpl" to pull the header and footer out
			$displayframe = explode("{inject}", $string);
			// header variable
			$header = $displayframe[0];
			// footer variable
			$footer = $displayframe[1];		

			//// LOAD FRAME /////////////////////////////////////////////////////////
			if ($_GET["p"] == "index") {
				include("pages/index.php");
			} else {
				echo $header; // Header
				include('pages/'.$_GET["p"].'.php'); // Body
				echo $footer; // Footer
			}
			
		} else if ($_GET["url"] != "" || $artist_url != "") {
			
			include('includes/config.php');
			include('includes/functions.php');
			include('jplayer/index.php');
		
		} else {

			include('includes/config.php');
			include('includes/functions.php');		
			include('home.php');

		}
	}
?>