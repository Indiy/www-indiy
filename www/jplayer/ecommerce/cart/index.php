<?
	
	include(serverPath()."/theme/plugins/ecommerce/functions.php");

//// Load Content //////////////////////////////////

	if($_SESSION['cart'] != "") {
		$cartid = $_SESSION['cart'];
	} else {	
		$_SESSION['cart'] = rand(1111111,9999999);
	}
	
	$getid 			= $GLOBALS["getid"];				// get the current page file names
	$currentPartNo	= currentPartNo();					// determine the current pages part number
	$variablePartNo = $currentPartNo + 1;				// add one to the current number of parts to bring us to the variable part
	$variablePart	= currentPart($variablePartNo);		// determine the variable part number
	$variable 		= $GLOBALS[$variablePart];			// grab the variable value if any

	if ($variable != "") {
		if ($variable == "checkout") {
			include("checkout.php");
		} else if ($variable == "invoice") {
			include("invoice.php");
		} else if ($variable == "complete") { 
			include("final.php");
		} else if ($variable == "paypal") {
			include("paypal.php");
		}
	} else {
		include("view.php");
	}

?>