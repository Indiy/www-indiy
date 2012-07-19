<?php

	include(dirname(__FILE__).'/../includes/functions.php');	
	include(dirname(__FILE__).'/../includes/config.php');
	
	if ($_REQUEST["form"] != "") {
	
	/*
		
		$from = $email;
		$sendto = $_REQUEST["from"];
		$sendsubject = "{$siteName} Contact Form Submission";
		// Always set content-type when sending HTML email
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
		// More headers
		$headers .= 'From: '.$from. "\r\n";
		$sendmessage = "
		<b>Name:</b><br />{$name}
		<br /><br />
		<b>Email:</b><br />{$email}
		<br /><br />
		<b>Phone:</b><br />{$phone}
		<br /><br />
		<b>Comments:</b><br />{$comments}		
		";
	
		mail($sendto,$sendsubject,$sendmessage,$headers);
		
	*/
		$name = $_REQUEST["name"];
		$email = $_REQUEST["email"];
		$phone = $_REQUEST["phone"];
		$comments = $_REQUEST["comments"];	
	
		$to = $_REQUEST["from"];
		$subject = "{$siteName} Contact Form Submission";
		$message = "
NAME: {$name}

EMAIL: {$email}

PHONE: {$phone}

MESSAGE: {$comments}		
		
		";
		$from = $email;
		$headers = "From:" . $from;
		mail($to,$subject,$message,$headers);	
		
		$status = "Success! Your message was delivered.";
		echo $status;
		
	}
	
	if ($_REQUEST["newsletter"] != "") {

		$name = $_REQUEST["name"];
		$email = $_REQUEST["email"];
		$artist = $_REQUEST["artist"];
		
		insert("[p]musicplayer_subscribers","artistid|name|email","{$artist}|{$name}|{$email}");
		$status = "Success!";
		echo $status;
		
	}	

	if ($_REQUEST["imageid"] != "") {
		
		$track = $_REQUEST["imageid"];
		$artist = $_REQUEST["artist"];
		// Build Music
		
		$loadmusic = mq("select * from `[p]musicplayer_audio` where `artistid`='{$artist}' order by `order` asc, `id` desc");
		$m=0;
		while ($music = mf($loadmusic)) {
			$music_image = $music["image"];
			if ($m == $track) {
				$track_image = $music_image;
				$music_id = $music["id"];
			}
			++$m;
		}
		trackViews($music_id);
		
		//echo '<script>alert("'.$track_image.'");</script>';
		echo $track_image;
		//echo "1_73953_over.jpg";
		
		//echo "Track = {$track} & Artist = {$artist}";
	}
	
	if ($_REQUEST["vote"] != "") {
		$artist = $_REQUEST["vartist"];
		$audio = $_REQUEST["vtrack"];
		$result = $_REQUEST["vote"];
		insert("[p]musicplayer_votes","artistid|audioid|vote|ip","{$artist}|{$audio}|{$result}|{$ip}");
		echo "Thank you for your vote!";
	}
	
	if ($_REQUEST["delete"] != "") {
		$delete = $_REQUEST["delete"];
		mq("delete from `[p]musicplayer_ecommerce_cart` where `id`='{$delete}'");
		echo "Successfully Deleted!";
	}
	
	if ($_REQUEST["updatetotal"] != "") {
		$id = $_REQUEST["updatetotal"];
		$mycart = mq("select * from `[p]musicplayer_ecommerce_cart` WHERE `userid`='{$_SESSION['cart']}' ORDER BY `id` ASC");
		$newtotal = "0";
		while ($row = mf($mycart)) {
			$newtotal = money(abs($row['price']) + $newtotal);
		}
		echo "$ ".$newtotal;
	}
	
	if ($_REQUEST["shippingtotal"] != "") {
		$id = $_REQUEST["shippingtotal"];
		$mycart = mq("select * from `[p]musicplayer_ecommerce_cart` WHERE `userid`='{$_SESSION['cart']}' ORDER BY `id` ASC");
		$newtotal = "0";
		$shippingDiscount = shippingDiscount();
		$shippingRate = shippingRate();
		
		while ($row = mf($mycart)) {
			$cart_itemamount = $row['price'];
			$newtotal = money(abs($row['price']) + $newtotal);
			$cart_shipping_load = $cart_itemamount * $shippingRate;
			$cart_shipping = money(round($cart_shipping_load, 2));
			if ($shippingDiscount) {
				if ($cart_itemamount < $shippingDiscount) {
					$cart_shipping_load = $cart_itemamount * $shippingRate;
					$cart_shipping = money(round($cart_shipping_load, 2));
					$shipping = $shipping + $cart_shipping;
				} else {
					$cart_shipping = "";
					$displayshipping = "";
				}
			} else {
				$cart_shipping_load = $cart_itemamount * $shippingRate;
				$cart_shipping = money(round($cart_shipping_load, 2));
				$shipping = $shipping + $cart_shipping;
			}
			
		}
		echo "$ ".money($shipping);
	}
	
	if ($_REQUEST["cart"] == "true" || $_REQUEST["checkout"] == "true") {
	
		echo "
		<script>
		$(document).ready(function(){

			$('.remove').click(function() {
				var remove = $(this).text();
				$('div#product-'+remove).slideUp();
				var removeValue = '&delete='+remove;
				$.post('jplayer/ajax.php', removeValue, function(result) {
					//alert(remove);
					$('#results').html(result);
					$('#results').fadeIn();
					setTimeout(function(){ 
						$('#results').fadeOut();
					}, 500);
				});
				var removeValue = '&updatetotal='+remove;
				$.post('jplayer/ajax.php', removeValue, function(result) {
					//alert(remove);
					$('.totalvalue').text(result);
				});
				var shippingValue = '&shippingtotal='+remove;
				$.post('jplayer/ajax.php', shippingValue, function(result) {
					$('.shipping').text(result);
				});
			});
			
			$('#checkout').click(function(event){
				$('.cart').hide();
				$('ul.products').hide();
				var cart = '&paypal=".$_REQUEST["paypal"]."&artist=".$_REQUEST["artist"]."&checkout=true';
				$.post('jplayer/ajax.php', cart, function(items) {
					$('ul.products').hide();
					$('.cart').html(items);
					$('.cart').fadeIn();
				});
			});
			
		});	
		</script>
		
		";
		
		$paypal = $_REQUEST["paypal"];
		$product = $_REQUEST["product"];
		$artist = $_REQUEST["artist"];
		
		if ($_SESSION['cart'] != "") {
		
			$cart_productid = $product;
			$row = mf(mq("select * from `[p]musicplayer_ecommerce_products` where `id`='{$cart_productid}' limit 1"));

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
			
			$database = "[p]musicplayer_ecommerce_cart";
			$tables = "userid|productid|price|name|code";
			$values = "{$_SESSION['cart']}|{$cart_productid}|{$product_price}|{$product_name}{$product_size}{$product_color}|".$_SESSION["discount"];

			insert($database,$tables,$values);

		} else {
			if ($_REQUEST["checkout"] == "") {
				$statusMessage .= "<p class='center'>Error!</p>";
			}
		}

		//// DISPLAY SHOPPING CART /////////////////////////////

		if ($_SESSION['cart'] > "1") {

			$mycart = mq("select * from `[p]musicplayer_ecommerce_cart` WHERE `userid`='{$_SESSION['cart']}' ORDER BY `id` ASC");

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
					$cart_product_image = productImage($cart['productid']);
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
					
					$statusMessage .= "<div id='product-{$cart_id}'>";
					$statusMessage .= "<div class='one'>";
					$statusMessage .= "		<input type='hidden' name='item_name_{$startingitem}' value='{$cart_name}'>";
					$statusMessage .= "		<input type='hidden' name='amount_{$startingitem}' value='{$cart_itemamount}'>";
					if ($cart_product_image != "") {
					$statusMessage .= "		<img src='".trueSiteUrl()."/artists/products/{$cart_product_image}' class='productimage' border='0' />";
					}
					$statusMessage .= "</div>";
					$statusMessage .= "<div class='two'>";
					$statusMessage .= "		<p>{$cart_name}</p>";
					$statusMessage .= "</div>";
					$statusMessage .= "<div class='three'>";
					$statusMessage .= "		<p>$ {$cart_itemamount}{$displayshipping}</p>";
					$statusMessage .= "</div>";
					$statusMessage .= "<div class='four'>";
					$statusMessage .= "		<div class='remove'>{$cart_id}</div>";
					$statusMessage .= "		<input type='hidden' name='item_name_{$startingitem}' value='{$cart_name}'>";
					$statusMessage .= "		<input type='hidden' name='amount_{$startingitem}' value='{$cart_itemamount}'>";
					$statusMessage .= "</div>";
					$statusMessage .= "<div class='clear'></div>\n";
					$statusMessage .= "</div>\n\n";
					
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
						$statusMessage .= "		<span class='left shipping>$ {$shipping}</span>";
						$statusMessage .= "		<span class='left total totalvalue'>$ {$final}</span>";
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
						$statusMessage .= "		<span class='left shipping'>$ {$shipping}</span>";
						$statusMessage .= "		<span class='left total totalvalue'>$ {$final}</span>";
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
					$statusMessage .= "		<span class='left shipping'>$ {$shipping}</span>";
					$statusMessage .= "		<span class='left total totalvalue'>$ {$final}</span>\n";
					$statusMessage .= "</div>\n";
					$statusMessage .= "<div class='four'></div>\n";
					$statusMessage .= "<div class='clear'></div>\n";
					//$statusMessage .= "<input type='hidden' name='discount_amount_cart' value='{$final}' />\n";
					//$statusMessage .= "<input type='hidden' name='discount_amount2' value='0.00' />\n";
				
				}
				
				$statusMessage .= "<div class='full'>\n";
				if ($_REQUEST["checkout"] == "true") {
					$statusMessage .= "		<input type='submit' name='submit' class='submit' value='Check Out' />\n";
				} else {
					$statusMessage .= "		<div class='submit' id='checkout'>Confirm Cart</div>\n";
				}
				$statusMessage .= "</div>\n";
			
				$statusMessage .= "<div class='clear'></div>\n";

			} else {
				$statusMessage = "<p class='center'>You have not added products to your cart yet.</p>";
			}
		} else {
			$statusMessage = "<p class='center'>No shopping cart</p>";
		}
?>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">  
<input type="hidden" name="cmd" value="_cart">  
<input type="hidden" name="upload" value="1">  
<input type="hidden" name="business" value="<?=$paypal;?>">
<div class="shoppingcartstyle">
    <div class="one heading">Image</div>
    <div class="two heading">Name</div>
    <div class="three heading">Price</div>
    <div class="four heading">Remove</div>
	<div class="clear"></div>
	
	<?=$statusMessage;?>
	
</div>
</form>
<?

	}
	
	
	if ($_REQUEST["track"] != "") {
		$track = $_REQUEST["track"];

		// Build Music
		$music = mf(mq("select * from `[p]musicplayer_audio` where `image`='{$track}' limit 1"));
		$artist = $music["artistid"];
		$art = mf(mq("select `artist`,`listens` from `[p]musicplayer` where id='{$artist}' limit 1"));
		$music_artist = nohtml($art["artist"]);
		$music_listens = $art["listens"];
		
		$music_id = $music["id"];
		$music_name = stripslashes($music["name"]);
		$track_name = $music_name;
		$track_id = $music_id;
		$listens = $music["views"] + 1;
		update("[p]musicplayer_audio","views","{$listens}","id",$music["id"]);
		
		
		// trackViews($artist_id);
		
		echo '
		<script>
		$(document).ready(function(){
			$(".vote").click(function(event) {
				var voteBody = $(this).text();
				var voteData = "&vartist='.$artist.'&vtrack='.$track_id.'&vote=" + voteBody;
				$.post("jplayer/ajax.php", voteData, function(voteResultsNow) {
					$("#results").html(voteResultsNow);
					$("#results").fadeIn();
					setTimeout(function(){ 
						$("#results").fadeOut();
					}, 2000);
				});
			});
		});
		</script>
		'."
		<div style='float: left; padding-right: 10px;'><span style='color: #555 !important;'>Artist:</span> {$music_artist} <span style='color: #555 !important;'>// Track:</span> {$track_name}";
		echo "</div> <div class='vote'>1</div><div class='vote nay'>0</div><div class='clear'></div>";
	}
	
	if ($_REQUEST["page"] != "") {
		$page = $_REQUEST["page"];
		$artist = $_REQUEST["artist"];
		
		// Build Pages
		$pages = mf(mq("select * from `[p]musicplayer_content` where `id`='{$page}' and `artistid`='{$artist}' limit 1"));

		$page_name = stripslashes($pages["name"]);
		if ($pages["body"] != "") { 
			$page_body = '<p>'.nohtml($pages["body"]).'</p>';
		} else { 
			$page_body = '';
		}
		if ($pages["video"] != "") {
			$breakvideo = explode(".com/", $pages["video"]);
			if ($breakvideo[0] == "http://www.youtube") {
				$breakmore = explode("&", str_replace("watch?v=", "", $breakvideo[1]));
				$videourl = "http://www.youtube.com/embed/".$breakmore[0];
			} else if ($breakvideo[0] == "http://vimeo") {
				$videourl = "http://player.vimeo.com/video/".$breakvideo[1];
			} else {
				
			}
			if ($videourl != "") {
				$page_video = '<div class="video jp-pause"><iframe width="600" height="368" src="'.$videourl.'" frameborder="0" allowfullscreen></iframe></div>';
			}
		} else { 
			$page_video = '';
		}
		if ($pages["image"] != "") { 
			$page_image = '<img class="image" src="'.trueSiteUrl().'/artists/images/'.$pages["image"].'" border="0" />';
		} else { 
			$page_image = '';
		}

		$page_content = "<h1>{$page_name}</h1>{$page_image}{$page_video}{$page_body}<div class='clear'></div>";
		pageViews($page);
		echo $page_content;
	}

?>