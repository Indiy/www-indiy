<? 

// SETUP //////////////////////////////////////////////////////////////////////////

	/* Check for any discount codes */
	$checkd = mq("select `id` from `[p]ecommerce_discount`");
	if (num($checkd) > 0) { $discount_code = "true"; }

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
				$statusMessage .= "		<img src='".trueSiteUrl()."/system/images/products/{$cart_product_mage}' class='productimage' border='0' />";
				$statusMessage .= "</div>";
				$statusMessage .= "<div class='two'>";
				$statusMessage .= "		<p><a href='".productUrl($cart_product)."'>{$cart_name}</a></p>";
				$statusMessage .= "</div>";
				$statusMessage .= "<div class='three'>";
				$statusMessage .= "		<p>$ {$cart_itemamount}{$displayshipping}</p>";
				$statusMessage .= "</div>";
				$statusMessage .= "<div class='four'>";
				$statusMessage .= "		<input type='hidden' name='item_name_{$startingitem}' value='{$cart_name}'>";
				$statusMessage .= "		<input type='hidden' name='amount_{$startingitem}' value='{$cart_itemamount}'>";
				$statusMessage .= "</div>";
				$statusMessage .= "<div class='clear'></div>\n";
				
				$startingitem++;
			
			}

			$shipping = money($shipping);
			$final_load = $cart_total;
			$final = round($final_load, 2);

			if (isset($_SESSION["discount"])) {
			
				$loadvalue = explode("|", discountvalue($_SESSION["discount"]));
				$value = $loadvalue[0];
				$discountType = $loadvalue[1];
				
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
				$statusMessage .= "<input type='hidden' name='discount_amount_cart' value='{$dv}' />\n";
				$statusMessage .= "<input type='hidden' name='discount_amount2' value='0.00' />\n";
			
			} else {
			
				$final = money($final + $shipping);
				$statusMessage .= "<div class='one'></div>\n";
				$statusMessage .= "<div class='two totals'>\n";
				$statusMessage .= "		<span class='right'>Shipping:</span>";
				$statusMessage .= " 	<span class='right total'>Total:</span>";
				$statusMessage .= "</div>\n";
				$statusMessage .= "<div class='three totals'>";
				$statusMessage .= "		<span class='left'>$ {$shipping}</span><input type='hidden' name='shipping_1' value='{$shipping}' />";
				$statusMessage .= "		<span class='left total'>$ {$final}</span>\n";
				$statusMessage .= "</div>\n";
				$statusMessage .= "<div class='four'></div>\n";
				$statusMessage .= "<div class='clear'></div>\n";
			
			}
			
			if (paypalEmail()) {
				$statusMessage .= "   <div class='half paypal'>\n";
				$statusMessage .= "		<p class='center'>\n";
				$statusMessage .= "		<input type='submit' name='submit' class='submit' value='Continue to Pay' /></p>\n";
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

<style> h1.pageheading { display: none !important; } </style>
<h1>Confirm Shopping Cart</h1>

<form action="https://www.paypal.com/cgi-bin/webscr" method="post">  
<input type="hidden" name="cmd" value="_cart">  
<input type="hidden" name="upload" value="1">  
<input type="hidden" name="business" value="<?=paypalEmail();?>">
<div class="shoppingcartstyle">
    <div class="one heading">Image</div>
    <div class="two heading">Name</div>
    <div class="three heading">Price</div>
    <div class="four heading">&nbsp;</div>
	<div class="clear"></div>
	
	<?=$statusMessage;?>
	
</div>
</form>