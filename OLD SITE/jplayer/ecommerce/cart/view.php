<? 

// REMOVE FROM CART ///////////////////////////////////////////////////////////////

	if (isset($_POST["removefromcart"])) {
		$remove_cartid = $_POST['cartid'];
		mq("DELETE FROM `[p]ecommerce_cart` WHERE `id`='{$remove_cartid}'");
	}

// SETUP //////////////////////////////////////////////////////////////////////////

	/* Check for any discount codes */
	$checkd = mq("select `id` from `[p]ecommerce_discount`");
	if (num($checkd) > 0) { $discount_code = "true"; }

// ADD TO CART ////////////////////////////////////////////////////////////////////

	if (isset($_POST["addtocart"])) {
		if ($_SESSION['cart'] != "") {
		
			$cart_productid = $_POST['productid'];
			$row = mf(mq("select * from `[p]ecommerce_products` where `id`='{$cart_productid}' limit 1"));

			$product_name = addslashes($row["name"]);
			if ($row["discount"] != "") {
				$product_price = $row["discount"];
			} else {
				$product_price = $row["price"];
			}
			$product_filename = $row["filename"];
			$product_manufacturer = $row["manufacturer"];
			$product_origin = $row["origin"];
			if ($_POST["size"] != "") { 
				$product_size = " - ".$_POST["size"]; 
			}
			if ($_POST["color"] != "") { 
				$product_color = " - ".$_POST["color"]; 
			}
			
			$database = "[p]ecommerce_cart";
			$tables = "userid|productid|price|name|code";
			$values = "{$_SESSION['cart']}|{$cart_productid}|{$product_price}|{$product_name}{$product_size}{$product_color}|".$_SESSION["discount"];

			insert($database,$tables,$values);
			
			
			$to = customerService();
			$subject = "New Invoice Created for ".siteName();
			$message = "Somebody added ".stripslashes($product_name)." to their shopping cart - view the whole cart online at ".cartUrl()."/invoice/{$_SESSION['cart']}";
			$from = $to;
			$headers = "From: $from";
			
			if ($to != "") {
				mail($to,$subject,$message,$headers);
			}

		} else {
			$statusMessage .= "<p class='center'>Error!</p>";
		}
	}

	//// DISPLAY SHOPPING CART /////////////////////////////

	if ($_SESSION['cart'] > "1") {

		$mycart = mq("select * from `[p]ecommerce_cart` WHERE `userid`='{$_SESSION['cart']}' ORDER BY `id` ASC");

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
				$statusMessage .= "		<p><form method='post'>";
				$statusMessage .= "		<input type='hidden' name='cartid' value='{$cart_id}' />";
				$statusMessage .= "		<input type='submit' name='removefromcart' value=' X ' class='submit remove' />";
				$statusMessage .= "		</form></p>";
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
			
			if ($_POST["productid"]) {			
				$statusMessage .= "<div class='full'>\n";
				$statusMessage .= "   <p><strong><a class='continueshopping' href='".productUrl($cart_productid)."'>Continue Shopping</a></strong></p>\n";
				$statusMessage .= "</div>\n";
			}
			
			if (authorize()) {
				$statusMessage .= "   <div class='half authorize'>\n";
				$statusMessage .= "		<p class='center'>\n";
				$statusMessage .= "		<form method=\"post\" action=\"".fullSiteUrl()."/checkout\">\n";
				$statusMessage .= "			<input type='hidden' value='{$final}' name='x_amount' />\n";
				$statusMessage .= "			<input type='hidden' value='{$_SESSION["cart"]}' name='id' />\n";
				$statusMessage .= "			<input type='submit' name='submit' class='submit' value='Check Out' />\n";
				$statusMessage .= "		</form>\n";
				$statusMessage .= "		</p>\n";
				$statusMessage .= "		\n";
				$statusMessage .= "  </div>\n";
			}
			
			if (paypalEmail()) {
				$statusMessage .= "   <div class='half paypal'>\n";
				$statusMessage .= "		<p class='center'><form method=\"post\" action=\"".fullSiteUrl()."/paypal\">\n";
				$statusMessage .= "		<input type='submit' name='submit' class='submit' value='Check Out' /></form></p>\n";
				$statusMessage .= "  </div>\n";
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
    <div class="four heading">Remove</div>
	<div class="clear"></div>
	
	<?=$statusMessage;?>
	
</div>