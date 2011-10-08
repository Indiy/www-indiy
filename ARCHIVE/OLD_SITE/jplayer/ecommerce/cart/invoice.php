<? 

//// Include Template Design ///////////////////////
	$variablePartNoTwo  = $currentPartNo + 2;					// add one to the current number of parts to bring us to the variable part
	$variablePartTwo	= currentPart($variablePartNoTwo);		// determine the variable part number
	$cartid 			= $GLOBALS[$variablePartTwo];			// grab the variable value if any
  
?>


<?

// SETUP //////////////////////////////////////////////////////////////////////////

	/* Check for any discount codes */
	$checkd = mq("select `id` from `[p]ecommerce_discount`");
	if (num($checkd) > 0) { $discount_code = "true"; }

	//// DISPLAY SHOPPING CART /////////////////////////////

	if ($_SESSION['cart'] > "1") {

		$mycart = mq("select * from `[p]ecommerce_cart` WHERE `userid`='{$cartid}' ORDER BY `id` ASC");

		if (num($mycart) > 0) {

			$cart_total = 0;
			$startingitem = 1;
			$shipping = 0;
			$shippingDiscount = shippingDiscount();
			$shippingRate = shippingRate();

			while($cart = mf($mycart)) {

				$cart_id = $cart['id'];
				$cart_product = $cart['productid'];
				$cart_itemamount = money(abs($cart['price']));
				$cart_name = stripslashes($cart['name']);
				$cart_product_mage = productImage($cart['productid']);
				$cart_total = $cart_total + $cart_itemamount;
				
				if ($shippingDiscount) {
					if ($cart_itemamount < $shippingDiscount) {
						$cart_shipping_load = $cart_itemamount * $shippingRate;
						$cart_shipping = money(round($cart_shipping_load, 2));
						$shipping = $shipping + $cart_shipping;
						$displayshipping = "<br /><small>Shipping: $ {$cart_shipping}</small>";
					} else {
						$cart_shipping = "";
						$displayshipping = "";
					}
				} else {
					$cart_shipping_load = $cart_itemamount * $shippingRate;
					$cart_shipping = money(round($cart_shipping_load, 2));
					$shipping = $shipping + $cart_shipping;
					$displayshipping = "<br /><small>Shipping: $ {$cart_shipping}</small>";
				}
				
				$statusMessage .= "<div class='one'>";
				$statusMessage .= "		<input type='hidden' name='item_name_{$startingitem}' value='{$cart_name}'>";
				$statusMessage .= "		<input type='hidden' name='amount_{$startingitem}' value='{$cart_itemamount}'>";
				$statusMessage .= "		<img src='".trueSiteUrl()."/system/images/products/{$cart_product_mage}' class='productimage' border='0' />";
				$statusMessage .= "</div>";
				$statusMessage .= "<div class='two'>";
				$statusMessage .= "		<p><a href='".productUrl($cart_product)."'>{$cart_name}</a></p>";
				$statusMessage .= "</div>";
				$statusMessage .= "<div class='three'>";
				$statusMessage .= "		<p>$ {$cart_itemamount}{$displayshipping}</p>";
				$statusMessage .= "</div>";
				$statusMessage .= "<div class='four'>";
				$statusMessage .= "</div>";
				$statusMessage .= "<div class='clear'></div>\n";
				
				$startingitem++;
			
			}

			$shipping = money($shipping);
			$final_load = $cart_total;
			$final = round($final_load, 2);

			if (isset($_POST["discount"]) || isset($_SESSION["discount"])) {
			
				if (isset($_POST["discount"])) {
					$check = mf(mq("select * from `[p]ecommerce_discount` where `code`='{$_POST["discount"]}' limit 1"));
					
					if ($check["id"] != "") { 
						if ($check["active"] == "1") {
							if ($check["uses"] != $check["max"]) {
								$_SESSION["discount"] = $check["code"];	
								if ($check["percent"] != "") {	
									$value = $check["percent"]."%";
									$discountType = "percent";
								} else {
									$value = "$ ".$check["amount"];
									$discountType = "dollars";
								}
								update("[p]ecommerce_cart","cart",$_SESSION["discount"],"userid",$_SESSION['cart']);
							} else {
								$notify = "<p class='center'>This code has reached it's maximum usage</p>";
							}
						} else {
							$notify = "<p class='center'>This code is no longer active</p>";
						}
					} else {
						$notify = "<p class='center'>The discount code you entered does not exist</p>";
					}
				} else {								
					$loadvalue = explode("|", discountvalue($_SESSION["discount"]));
					$value = $loadvalue[0];
					$discountType = $loadvalue[1];
				}
				
				if ($notify == "") {

					$discount = nohtml($_SESSION["discount"]);
					
					if ($discountType == "percent") {
						$percent = $value * 0.01;
						$percentvalue = 1.00 - $percent;
						$discountvalue = $final * $percent;
						$oldfinal = money($final);								
						$discountvalue = money(round($discountvalue, 2));
						$final = $final * $percentvalue;
						$final = round($final, 2);	
						$newSub = money($oldfinal - $discountvalue);
						$final = money($newSub + $shipping);
					} else {
						$oldfinal = money($final);								
						$discountvalue = money(str_replace("$ ", "", $value));
						$final = $final - $value;
						$final = round($final, 2);	
						$newSub = money($oldfinal - $discountvalue);
						$final = money($newSub + $shipping);
					}
					
					
					$statusMessage .= "<div class='full'>";
					$statusMessage .= "		<p class='center'>Discout CODE: <strong>".$discount."</strong> ".$value." Off</p>";
					$statusMessage .= "</div>\n";

					$statusMessage .= "<div class='half' totals>";
					$statusMessage .= "		<span class='right'>Sub Total:</span>";
					$statusMessage .= "		<span class='right'>Discount:</span>";
					$statusMessage .= "		<span class='right'><strong>Sub Total:</strong></span>";
					$statusMessage .= "		<span class='right'>Shipping:</span>";
					$statusMessage .= "		<span class='right total'>Total:</span>";
					$statusMessage .= "</div>\n";					
					$statusMessage .= "<div class='half' totals>";
					$statusMessage .= "		<span class='left>$ {$oldfinal}</span>";
					$statusMessage .= "		<span class='left>- $ {$discountvalue}</span>";
					$statusMessage .= "		<span class='left><strong>$ {$newSub}</strong></span>";
					$statusMessage .= "		<span class='left>$ {$shipping}</span>";
					$statusMessage .= "		<span class='left total'>$ {$final}</span>";
					$statusMessage .= "</div>\n";
					$statusMessage .= "<div class='clear'></div>\n";
					
				} else {
				
					$final = money($final + $shipping);
					$statusMessage .= "<div class='full warning'>{$notify}</div>\n";
					$statusMessage .= "<div class='full'>";
					$statusMessage .= "		<form method='post'>";
					$statusMessage .= "		<div class='center'>Discout CODE: <input type='text' name='discount' value='".$discount."' class='discount' /> <input type='submit' name='submitting' value='Submit' class='submit discount' /> </div>";
					$statusMessage .= "		</form>\n";
					$statusMessage .= "</div>\n";
					$statusMessage .= "<div class='one'></div>\n";
					$statusMessage .= "<div class='two totals'>";
					$statusMessage .= "		<span class='right'>Shipping:</span>";
					$statusMessage .= "		<span class='right total'>Total:</span>";
					$statusMessage .= "</div>\n";
					$statusMessage .= "<div class='three totals'>";
					$statusMessage .= "		<span class='left'>$ {$shipping}</span>";
					$statusMessage .= "		<span class='left total'>$ {$final}</span>";
					$statusMessage .= "</div>\n";
					$statusMessage .= "<div class='four'></div>\n";
					$statusMessage .= "<div class='clear'></div>\n";
					
				}
			
			} else {
			
				$final = money($final + $shipping);
				if ($discount_code) {
					$statusMessage .= "<div class='full'>\n";
					$statusMessage .= "   	<p><form method='post'>Discout CODE: <input type='text' name='discount' value='".$discount."' /> <input type='submit' name='submitting' value='Submit' class='submit discount' /></form></p>\n";
					$statusMessage .= "</div>\n";
				}
				$statusMessage .= "<div class='one'></div>\n";
				$statusMessage .= "<div class='two totals'>\n";
				$statusMessage .= "		<span class='right'>Shipping:</span>";
				$statusMessage .= " 	<span class='right total'>Total:</span>";
				$statusMessage .= "</div>\n";
				$statusMessage .= "<div class='three totals'>";
				$statusMessage .= "		<span class='left'>$ {$shipping}</span>";
				$statusMessage .= "		<span class='left total'>$ {$final}</span>\n";
				$statusMessage .= "</div>\n";
				$statusMessage .= "<div class='four'></div>\n";
				$statusMessage .= "<div class='clear'></div>\n";
			
			}
			
			$statusMessage .= "<div class='clear'></div>\n";

		} else {
			$statusMessage = "<p class='center'>You have not added products to your cart yet.</p>";
		}
	} else {
		$statusMessage = "<p class='center'>No shopping cart</p>";
	}

?>
<link href="<?=pluginUrl();?>/ecommerce/style.css" rel="stylesheet" type="text/css" media="screen" /> 
<div class="shoppingcartstyle">
    <div class="one heading">Image</div>
    <div class="two heading">Name</div>
    <div class="three heading">Price</div>
    <div class="four heading">&nbsp;</div>
	<div class="clear"></div>
	
	<?=$statusMessage;?>
	
</div>





	<? 		
/*
	
	  $getid = $_GET["value"];
 	
	  if ($getid > "1") {
		   
		    $getid = $_GET["value"];

			$myshoppingcart2 = mysql_query("select * from ".$prefix."cart WHERE `userid`='$getid' ORDER BY `id` DESC", $connect);
			$loaddetails = mysql_fetch_array(mysql_query("select * from {$prefix}cart where `userid`='$getid' limit 1"));
			
			$load_name = $loaddetails["person"];
			$load_street = $loaddetails["street"];
			$load_city = $loaddetails["city"];
			$load_state = $loaddetails["state"];
			$load_zipcode = $loaddetailsl["zipcode"];
			
				echo "<p><br /><strong>Shipping Information</strong><br />";
				echo $load_name;
				echo "<br />";
				echo $load_street;
				echo "<br />";				
				echo $load_city.", ".$load_state." ".$load_zipcode;
				echo "</p>";
		  
				echo "	 <table width='100%'>";
				echo "	  <tr>";
				echo "	    <td align='left' valign='top' width='10'>&nbsp;</td>";				
				echo "	    <td align='left' valign='top' width='300'><strong>Item Name</strong></td>";
				echo "      <td align='right' valign='top' width='90'><strong>Price</strong></td>";
				echo "	  </tr>";

					$myshoppingcart_total2 = 0;
					$brush_price2 = 5; 
					$startingitem2 = 1;
					$shipping = 0;
					while($myshoppingcartrow2 = mysql_fetch_array($myshoppingcart2)) {

						$myshoppingcart_id2 = $myshoppingcartrow2['id'];
						$productid = $myshoppingcartrow2['productid'];						
						$myshoppingcart_itemamount2 = $myshoppingcartrow2['price'];
						$myshoppingcart_name2 = $myshoppingcartrow2['name'];
						$myshoppingcart_total2 = $myshoppingcart_total2 + $myshoppingcart_itemamount2;
						$row = mf(mq("select * from `[p]products` where `id`='{$productid}'"));
						  $product_origin = $row["origin"];
						  $product_subcat = $row["subcat"];
						  $use_product_name = $row["name"];
						  $product_description = $row["description"];
						  $product_image = $row["image"];			  
						  $product_price = $row["price"];
						  $product_sku = $row["sku"];
						  $product_discount = $row["discount"];
						  $product_manufacturerlink = $row["manufacturer"];
						  $product_manufacturer = manufacturer($row["manufacturer"]);
						  $product_filename = $row["filename"];			

									if ($myshoppingcart_itemamount2 < "100") {
										$myshoppingcart_shipping_load = $myshoppingcart_itemamount2 * 0.15;
										$myshoppingcart_shipping = round($myshoppingcart_shipping_load, 2);
										$shipping = $shipping + $myshoppingcart_shipping;
										$displayshipping = "<br /><small>Shipping: $ {$myshoppingcart_shipping}";
									} else {
										$myshoppingcart_shipping = "";
										$displayshipping = "";
									}						  

						echo "<tr>";						
						echo "   <td align='left' valign='top'></td>";
						echo "   <td align='left' valign='top'><a href='../$product_origin/$product_manufacturerlink/$product_filename'>$myshoppingcart_name2</a></td>";
						echo "   <td align='right' valign='top'>$ $myshoppingcart_itemamount2{$displayshipping}</td>";
						echo "</tr>";
						
						$startingitem2++;

					}
				
				
				$final_load = $myshoppingcart_total2 + $shipping;
				$final = round($final_load, 2);
				
				echo "<tr>";
				echo "   <td colspan='2' align='right'><p style=\"text-align: right;\"><strong>Sub Total:<br />Shipping:<br />Total:</strong></p></td>";
				echo "   <td><p><strong>$ $myshoppingcart_total2<br />$ $shipping<br />$ $final</strong></p></td>";
				echo "</tr>";

				echo "<tr>";
				echo "   <td colspan='2' align='right'><p style=\"text-align: right;\"><strong>Paid:</strong></p></td>";
				echo "   <td><p><strong>$ $final</strong></p></td>";
				echo "</tr>";
				
				echo "<tr>";
				echo "   <td colspan='2' align='right'><p style=\"text-align: right;\"><strong>Remaining Balance:</strong></p></td>";
				echo "   <td><p><strong>$ 0.00</strong></p></td>";
				echo "</tr>";
				


				echo "</table>";

		 
		   } else { 
		    echo "<form method='get'>";
		    echo "<input type='hidden' value='cart.invoice' name='fuse' />";		    
		    echo "Insert Invoice Number: <input type='text' value='' name='id' /> <input type='submit' value='Submit' name='submit' />";
		    echo "</form>";
		   }

*/		   
?>

