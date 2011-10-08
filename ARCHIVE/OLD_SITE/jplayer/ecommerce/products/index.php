<?

	include(serverPath()."/theme/plugins/ecommerce/functions.php");
	$database 		= "[p]ecommerce_products";
	
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

	// $pageid 		= pageID();
	// Get current page plugin id and root id
	$currentPageID				= pageID();
	$currentPagePluginID 		= pagePluginID();
	$currentPageRootID			= pageRootID();	
	
	// Build URL 
	if ($variable != "search" && $variable != "tags" && $variable != "") {
		
		include("view.php");
		
	} else {
	
		if ($variable != "search" && $variable != "tags") {
			// Get products plugin ID
			$set = mf(mq("select * from `[p]ecommerce` where `id`='1' limit 1"));
			$main_categories 			= $set["categories"];
			$main_manufacturers 		= $set["manufacturers"];
			$plugin_products_id 		= $set["plugin_products_id"];
			$plugin_categories_id 		= $set["plugin_categories_id"];
			$plugin_manufacturers_id 	= $set["plugin_manufacturers_id"];

			if (($currentPagePluginID == $plugin_categories_id) && ($currentPageRootID != $main_categories) && ($currentPageRootID != $main_manufacturers)) { 
				/* Check for SUB Categories */
				$queryQ = "(`subcat` LIKE '%,{$currentPageID},%')";
			} else if ($currentPagePluginID == $plugin_categories_id) {	
				/* Check for Categories */
				$queryQ = "`origin`='{$currentPageID}'";
			} else if ($currentPagePluginID == $plugin_manufacturers_id) { 
				/* Check for Manufacturers */
				$queryQ = "`manufacturer`='{$currentPageID}'";
			} else { 
				/* Nothing left, load page */
				$queryQ = "`page`='{$currentPageID}'";
			}
		} else {

			$variablePartNoTwo  = $currentPartNo + 2;					// add one to the current number of parts to bring us to the variable part
			$variablePartTwo	= currentPart($variablePartNoTwo);		// determine the variable part number
			$variableTwo 		= $GLOBALS[$variablePartTwo];			// grab the variable value if any		
			$variableTwo		= str_replace("-", " ", $variableTwo);  // Cleans the URL
			
			if ($variable == "tags") {
				$tagsQ = " and (`tags` LIKE '%{$variableTwo}%')";
			} else {
				$searchQ = " and (`name` LIKE '%{$variableTwo}%') and (`description` LIKE '%{$variableTwo}%') and (`sku` LIKE '%{$variableTwo}%')";
			}
			$queryQ = "`page`='{$currentPageID}'";
		}
		
		
		/* Let's count the total products */
		$counting = mq("select * from `$database` where {$queryQ}{$tagsQ}{$searchQ} ORDER BY `order` ASC, `id` desc");  
		$productcount = num($counting);

		echo "<ul class='products'>\n";
		while($row = mf($counting)) {
		
			$product_id = $row["id"];
			$product_origin = $row["origin"];
			$product_subcat = $row["subcat"];
			$use_product_name = nohtml($row["name"]);
			$product_price = "$".$row["price"];
			$product_description = $row["description"];
			$product_image = $row["image"];			  
			$product_sku = $row["sku"];
			$product_filename = $row["filename"];
			  
			echo "<li class='products'>\n";
			echo "	<a href='".productUrl($product_id)."'><img src='".trueSiteUrl()."/system/images/products/$product_image' border='0' alt='$use_product_name' /></a><br />$use_product_name\n";
			echo "	<ul>\n";
			echo "		<li class='tip'>$product_sku $use_product_name $product_price</li>\n";
			echo "		<li>$product_description</li>\n";
			echo "		<li><a href='".productUrl($product_id)."'>&raquo; View Details</a></li>\n";
			echo "	</ul>\n";
			echo "</li>";
		}
		echo "</ul>\n";
	}
?>
