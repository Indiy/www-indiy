<!--
This sample code is designed to connect to Authorize.net using the AIM method.
For API documentation or additional sample code, please visit:
http://developer.authorize.net
-->

<?PHP

$extendedwarrantyid = "1724";

// Post Variables 

$p_username = $_POST["xusernamex"];
$p_password = md5($_POST["xpasswordx"]);

$p_card_num = $_POST["x_card_num"];
$p_card_code = $_POST["x_card_code"];
$ex_month = $_POST["ex_month"];
$ex_year = $_POST["ex_year"];
$p_exp_date = $_POST["ex_month"]."".$_POST["ex_year"];
$p_amount = $_POST["x_amount"];
$p_description = $_POST["x_description"];
$p_email_customer = $_POST["x_email_customer"];
$p_invoice_num = $_POST["x_invoice_num"];
$p_first_name = $_POST["x_first_name"];
$p_last_name = $_POST["x_last_name"];
$p_last_name = $_POST["x_last_name"];
$p_address = $_POST["x_address"];
$p_city = $_POST["x_city"];
$p_state = $_POST["x_state"];
$p_zip = $_POST["x_zip"];
$p_zipp = $_POST["x_zip"];
$p_phone = $_POST["x_phone"];
$p_email = $_POST["x_email"];

if ($p_email != "") {

if ($_POST["shipto"] == "same") {
	$p_ship_to_first_name = $_POST["x_first_name"];
	$p_ship_to_last_name = $_POST["x_last_name"];
	$p_ship_to_address = $_POST["x_address"];
	$p_ship_to_city = $_POST["x_city"];
	$p_ship_to_state = $_POST["x_state"];
	$p_ship_to_zip = $_POST["x_zip"];		
	$p_ship_to_zipp = $_POST["x_zip"];			
} else {
	$p_ship_to_first_name = $_POST["x_ship_to_first_name"];
	$p_ship_to_last_name = $_POST["x_ship_to_last_name"];
	$p_ship_to_address = $_POST["x_ship_to_address"];
	$p_ship_to_city = $_POST["x_ship_to_city"];
	$p_ship_to_state = $_POST["x_ship_to_state"];
	$p_ship_to_zip = $_POST["x_ship_to_zip"];	
	$p_ship_to_zipp = $_POST["x_ship_to_zip"];		
}

// By default, this sample code is designed to post to our test server for
// developer accounts: https://test.authorize.net/gateway/transact.dll
// for real accounts (even in test mode), please make sure that you are
// posting to: https://secure.authorize.net/gateway/transact.dll

$post_url = "https://secure.authorize.net/gateway/transact.dll";

$post_values = array(
	
	// the API Login ID and Transaction Key must be replaced with valid values
	"x_login"					=> "7y6Ym8Ee",
	"x_tran_key"				=> "7L8s9Ey6w39Mdc3Y",

	"x_version"					=> "3.1",
	"x_delim_data"				=> "TRUE",
	"x_delim_char"				=> "|",
	"x_relay_response"			=> "FALSE",
	"x_email_customer"			=> $p_email_customer,
	"x_invoice_num"				=> $p_invoice_num,
	"x_type"					=> "AUTH_CAPTURE",
	"x_method"					=> "CC",
	"x_card_num"				=> $p_card_num,
	"x_card_code"				=> $p_card_code,
	"x_exp_date"				=> $p_exp_date,

	"x_amount"					=> $p_amount,
	"x_description"				=> $p_description,

	"x_first_name"				=> $p_first_name,
	"x_last_name"				=> $p_last_name,
	"x_address"					=> $p_address,
	"x_city"					=> $p_city,
	"x_state"					=> $p_state,
	"x_zip"						=> $p_zip,
	"x_phone"					=> $p_phone,
	"x_email"					=> $p_email,

	"x_ship_to_first_name"		=> $p_ship_to_first_name,
	"x_ship_to_last_name"		=> $p_ship_to_last_name,
	"x_ship_to_address"			=> $p_ship_to_address,
	"x_ship_to_city"			=> $p_ship_to_city,
	"x_ship_to_state"			=> $p_ship_to_state,
	"x_ship_to_zip"				=> $p_ship_to_zip

	
	// Additional fields can be added here as outlined in the AIM integration
	// guide at: http://developer.authorize.net
);

// This section takes the input fields and converts them to the proper format
// for an http post.  For example: "x_login=username&x_tran_key=a1B2c3D4"
$post_string = "";
foreach( $post_values as $key => $value )
	{ $post_string .= "$key=" . urlencode( $value ) . "&"; }
$post_string = rtrim( $post_string, "& " );

// This sample code uses the CURL library for php to establish a connection,
// submit the post, and record the response.
// If you receive an error, you may want to ensure that you have the curl
// library enabled in your php configuration
$request = curl_init($post_url); // initiate curl object
	curl_setopt($request, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
	curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
	curl_setopt($request, CURLOPT_POSTFIELDS, $post_string); // use HTTP POST to send form data
	curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment this line if you get no gateway response.
	$post_response = curl_exec($request); // execute curl post and store results in $post_response
	// additional options may be required depending upon your server configuration
	// you can find documentation on curl options at http://www.php.net/curl_setopt
//curl_close ($ch); // close curl object // this may need to be active

// This line takes the response and breaks it into an array using the specified delimiting character
$response_array = explode($post_values["x_delim_char"],$post_response);

// The results are output to the screen in the form of an html numbered list.
/*
 echo "<OL>\n";
 foreach ($response_array as $value)
{
	echo "<LI>" . $value . "&nbsp;</LI>\n";
	$i++;
}
echo "</OL>\n";

*/
if ($response_array[0] == "1") {
	echo "<h1>Transaction Complete</h1>";
	echo "<p>Your item will be shipped within 48 hours</p>";
	echo "<p><em>Thank you for your business and we look forward to working with you again!</em></p>";
	
	$fullname = $p_first_name." ".$p_last_name;
	if ($_SESSION['me'] != "") {
	
	} else {
	
		if ($p_password != "") {
			
			$database = "[p]users";
			$tables = "active|admin|first_name|last_name|password|email|address|state|city|zipcode|phone|shipaddress|shipcity|shipstate|shipzip|card";
			$fullname = $p_first_name." ".$p_last_name;
			$values = "1|0|{$p_first_name}|{$p_last_name}|{$p_password}|{$p_email}|{$p_address}|{$p_city}|{$p_state}|{$p_zip}|{$p_phone}|{$p_ship_to_address}|{$p_ship_to_city}|{$p_ship_to_state}|{$p_ship_to_zip}|{$p_card_num}";
			
			insert($database,$tables,$values);
			
			echo "<p><strong>Thank you for joining the site...this is the start of a beautiful relationship!</strong></p>";
			$registered = "And they are now a member of the site!";
		}
		
	}

	/* Update Cart Details */
	$database = "hifi_cart";
	$identifier = "userid";
	$identifiervalue = $p_invoice_num;
	$tables = "email|person|street|city|state|zipcode|country|status|code";
	$values = "{$p_email}|{$fullname}|{$p_ship_to_address}|{$p_ship_to_city}|{$p_ship_to_state}|{$p_ship_to_zip}|USA|1|".$_SESSION["discount"];
	
	update($database,$tables,$values,$identifier,$identifiervalue);
		
	if ($_SESSION["discount"] != "") {
		$dc = mf(mq("selet * from `[p]discount` where `code`='{$_SESSION["discount"]}'"));
		$newhits = ++$dc["uses"];
		update("[p]discount","uses",$newhits,"code",$_SESSION["discount"]);	
	}
	
	$notify1 = "billing@guitaritup.com";	
	$notify2 = "tara@guitaritupforgirls.com, ted@guitaritup.com";
	$notify4 = "todd@hi-fimedia.com";
	
	$subject = "New Order!";	
	$message = "
	<table width='100%'><tr><td align='left' width='100%'>
	<h1>Congratulations, you have just received an order!</h1>
	<p>
	ORDER DETAILS:<br />
	 &nbsp;<a href='{$siteUrl}/invoice/{$p_invoice_num}'>{$siteUrl}/invoice/{$p_invoice_num}</a>
	</p>

	<p>
	SHIPPING INFORMATION:<br />
	 &nbsp;$p_ship_to_first_name $p_ship_to_last_name<br />
	 &nbsp;$p_ship_to_address<br />
	 &nbsp;$p_ship_to_city, $p_ship_to_state $p_ship_to_zip
	</p>

	<p>
	CUSTOMER NAME:<br />
 	 &nbsp;$p_first_name $p_last_name
	</p>

	<p>
	CUSTOMER EMAIL:<br />
	 &nbsp;$p_email
	</p>

	<p>
	TOTAL:<br />
	 &nbsp;$ $p_amount
	</p>
	
	<p>$registered <br /><br /></p>
	</td></tr></table>
	";
	
	$Name = $siteName;
	$Email = "billing@guitaritup.com";
	$from = "$Name <$Email>";
	
	amail($notify1,$notify2,$notify4,$from,$subject,$message);
	
	// load 3 featured products
	$loadsql = mq("select * from ".$prefix."products where `sale`='Yes' ORDER BY rand() DESC LIMIT 3");	
	while($row = mf($loadsql)) {
		$product_id = $row["id"];
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
	  
		if ($gtrAcc == "true") {
			$product_manufacturer = $row["subcat"];
			$product_manufacturerName = manufacturer($row["manufacturer"]);
		} else {
			$product_manufacturer = $row["manufacturer"];
			$product_manufacturerName = manufacturer($row["manufacturer"]);
		}
						
		$productList .= '
					<div class="products"> 
					<table>
						<tr>
							<td align="center" valign="top" width="100">
								<a href="'.$siteUrl.'/'.$product_origin.'/'.$product_manufacturer.'/'.$product_filename.'"><img src="'.$siteUrl.'/system/image.php?file='.$product_image.'&width=200" width="70" border="0" alt="'.$use_product_name.'" /></a>
							</td> 
							<td align="left" valign="top">
								<a href="'.$siteUrl.'/'.$product_origin.'/'.$product_manufacturer.'/'.$product_filename.'">'.$use_product_name.'</a><br /><br />
								<small>
								<u>Manufacturer:</u><br />
								'.$product_manufacturerName.' <br />
								<u>SKU Number:</u><br />
								'.$product_sku.'<br />
								</small> 
								<br /><strong>Price: $ '.$product_discount.'</strong>
							</td> 
						</tr>
					</table>
					</div> 
		';						
	}
	
	// Load receipt
	$myshoppingcart2 = mq("select * from `[p]cart` WHERE `userid`='{$p_invoice_num}' ORDER BY `id` ASC");

	$myshoppingcart_total2 = 0;
	$startingitem2 = 1;
	$shipping = 0;
	while($myshoppingcartrow2 = mf($myshoppingcart2)) {

		$myshoppingcart_id2 = $myshoppingcartrow2['id'];
		$myshoppingcart_product2 = $myshoppingcartrow2['productid'];
		$myshoppingcart_itemamount2 = abs($myshoppingcartrow2['price']);
		$myshoppingcart_name2 = $myshoppingcartrow2['name'];
		$myshoppingcart_productid2 = productimage($myshoppingcartrow2['productid']);
		$myshoppingcart_total2 = $myshoppingcart_total2 + $myshoppingcart_itemamount2;
		if ($myshoppingcart_itemamount2 < "100") {
			if ($myshoppingcart_product2 != $extendedwarrantyid) {
				$myshoppingcart_shipping_load = $myshoppingcart_itemamount2 * 0.15;
				$myshoppingcart_shipping = money(round($myshoppingcart_shipping_load, 2));
				$shipping = $shipping + $myshoppingcart_shipping;
				$displayshipping = "<br /><small>Shipping: $ {$myshoppingcart_shipping}</small>";
			}
		} else {
			$myshoppingcart_shipping = "";
			$displayshipping = "";
		}
		$row = mf(mq("select * from `[p]products` where `id`='{$myshoppingcartrow2['productid']}'"));
			$product_origin = $row["origin"];
			$product_manufacturerlink = $row["manufacturer"];
			$product_filename = $row["filename"];
			
		$receipt .= "<tr>
						<td align='left' valign='middle' style='padding: 5px; border-top: 1px dashed #eee; width: 110px; text-align: center;'>
							<img src='{$siteUrl}/system/image.php?file={$myshoppingcart_productid2}&width=200' width='100' border='0' />
						</td>
						<td align='left' valign='middle' style='padding: 5px; border-top: 1px dashed #eee;'>";
						if ($myshoppingcart_product2 != $extendedwarrantyid) {									
							$receipt .= "<a href='$siteUrl/$product_origin/$product_manufacturerlink/$product_filename'>{$myshoppingcart_name2}</a>";
						} else {
							$receipt .= "<a href='$siteUrl/$product_origin/$product_filename'>{$myshoppingcart_name2}</a>";
						}
		$receipt .= "
						</td>
						<td align='right' valign='middle' style='padding: 5px; border-top: 1px dashed #eee;'>
							$ {$myshoppingcart_itemamount2}
							{$displayshipping}
						</td>
					</tr>";

		$startingitem2++;

	}
	$final_load = $myshoppingcart_total2;
	$final = round($final_load, 2);
	
	if (isset($_SESSION["discount"])) {
		$check = mf(mq("select * from `[p]discount` where `code`='{$_SESSION["discount"]}' limit 1"));
		if ($check["percent"] != "") {	
			$dinit = $check["percent"] * 0.01;
			$dv = money(round($final * $dinit, 2));
			$discountType = "percent";
		} else {
			$dv = money($check["amount"]);
			$discountType = "dollars";
		}
		$newSub = money($myshoppingcart_total2 - $dv);
		$final = money($newSub + $shipping);
			
		$discounth = "
			<div style='text-align: right; padding: 5px;'>Discount:</div>
			<div style='text-align: right; padding: 5px;'>Sub Total:</div>
			";
		$discountv = "
			<div style='text-align: left; padding: 5px;'>- $ {$dv}</div>
			<div style='text-align: left; padding: 5px;'>$ {$newSub}</div>
			";
		++$startingitem2;
	} else {
		$final = round($final_load, 2);
	}

	$receipt .= "
			<tr> 
				<td colspan='2' align='right' style='border-top: 1px #ccc solid;'>	
					<div style='text-align: right; padding: 5px;'>Sub Total:</div>
					{$discounth}
					<div style='text-align: right; padding: 5px;'>Shipping:</div>
					<div style='text-align: right; font-size: 18px; padding: 5px;'>Total:</div>
				</td> \n";
	$receipt .= "  <td align='left' style='border-top: 1px #ccc solid;'>
				<div style='text-align: left; padding: 5px;'>$ ".money($myshoppingcart_total2)."</div>
				{$discountv}
				<div style='text-align: left; padding: 5px;'>$ {$shipping}</div>
				<div style='text-align: left; font-size: 18px; padding: 5px;'>$ $final</div>
			</td>\n";
	$receipt .= "</tr>\n";
	
	// discount code
	$nextTimeDiscountCode = rand(11111,99999);
	// Create discount code in database
	insert("[p]discount","code|percent|active|max","{$nextTimeDiscountCode}|10|1|1");
	
	// subject of email sent to customer
	$customer_subject = "Order Confirmation from {$siteName}";
	$customer_message = '
		<h1>Thank you for your order!</h1>	 
		<p><strong>Order Number: '.$p_invoice_num.'</strong></p>
		<div style="background: #eee; border: 1px solid #ddd; padding: 25px; margin: 10px; text-align: center;">
			We know you have a choice in who you shop with and we appreciate you choosing us. <br />
			As our thank you, take 10% off your next purchase using this discount code: <br />
			<div style="color: #990000; font-size: 22px; font-weight: bold; text-align: center; padding-top: 8px;">'.$nextTimeDiscountCode.'</div>
		</div>
	
		<h1>Shipping Details</h1>
		<p>You will receive an email confirmation once your order has been shipped. Your order will be shipped to:</p>
		<p>
			'.$p_ship_to_first_name.' '.$p_ship_to_last_name.'<br />
			'.$p_ship_to_address.'<br />
			'.$p_ship_to_city.', '.$p_ship_to_state.' '.$p_ship_to_zip.'
		</p>
	
		<h1>Order Details</h1>
	
		<table width="615" cellpadding="0" cellspacing="0" style="padding: 10px 5px;">
			<tr>	    
				<td align="left" valign="top" width="75%" colspan="2" style="padding: 7px; background: #000; color: #fff;"><strong>Item Name</strong></td>
				<td align="right" valign="top" width="20%" style="padding: 7px; background: #000; color: #fff;"><strong>Price</strong></td>
			</tr>
			'.$receipt.'
		</table> 

		<h1>Other products you may like</h1>
		<table width="100%">
			<tr>
				<td align="center" valign="top" width="100">
					'.$productList.' 					
					<div style="clear: both;"><br /><br /></div>
				</td>
			</tr>
		</table>
	';
	amail($p_email,$cc,$notify4,$from,$customer_subject,$customer_message);
			
	
}

if ($response_array[0] == "2") {
	echo "<h1>Transaction INComplete</h1>";
	echo "<p>Your card has been denied, please try again using a different card.</p>";
	echo '<form name="returnform" action="'.$siteUrl.'/'.$partone.'/checkout" method=post>';
	echo "<input type='hidden' name='id' value='{$p_invoice_num}' />";
	echo "<input type='hidden' name='x_card_num' value='{$p_card_num}' />";
	echo "<input type='hidden' name='x_card_code' value='{$p_card_code}' />";
	echo "<input type='hidden' name='ex_month' value='{$ex_month}' />";
	echo "<input type='hidden' name='ex_year' value='{$ex_year}' />";
	echo "<input type='hidden' name='x_amount' value='{$p_amount}' />";
	echo "<input type='hidden' name='x_description' value='{$p_description}' />";
	echo "<input type='hidden' name='x_email_customer' value='{$p_email_customer}' />";
	echo "<input type='hidden' name='x_invoice_num' value='{$p_invoice_num}' />";
	echo "<input type='hidden' name='x_first_name' value='{$p_first_name}' />";
	echo "<input type='hidden' name='x_last_name' value='{$p_last_name}' />";
	echo "<input type='hidden' name='x_last_name' value='{$p_last_name}' />";
	echo "<input type='hidden' name='x_address' value='{$p_address}' />";
	echo "<input type='hidden' name='x_city' value='{$p_city}' />";
	echo "<input type='hidden' name='x_state' value='{$p_state}' />";
	echo "<input type='hidden' name='x_zipp' value='{$p_zip}' />";
	echo "<input type='hidden' name='x_phone' value='{$p_phone}' />";
	echo "<input type='hidden' name='x_email' value='{$p_email}' />";
	echo "<input type='hidden' name='x_ship_to_first_name' value='{$p_ship_to_first_name}' />";
	echo "<input type='hidden' name='x_ship_to_last_name' value='{$p_ship_to_last_name}' />";
	echo "<input type='hidden' name='x_ship_to_address' value='{$p_ship_to_address}' />";
	echo "<input type='hidden' name='x_ship_to_city' value='{$p_ship_to_city}' />";
	echo "<input type='hidden' name='x_ship_to_state' value='{$p_ship_to_state}' />";
	echo "<input type='hidden' name='x_ship_to_zip' value='{$p_ship_to_zip}' />";
	echo "<input type='submit' name='submit' value='Return' class='submit' />";
	echo "</form>";
	mail("todd@hi-fimedia.com","Failed Order","{$siteUrl}/invoice/{$p_invoice_num} did not complete - {$p_email}","From: {$siteName} <info@guitaritup.com>");
}

if ($response_array[0] == "3") {
	echo "<h1>Transaction INComplete</h1>";
	echo "<p>There has been an error processing this transaction, please go back and double check that you filled out all of your information correctly.</p>";
	echo '<form name="returnform" action="'.$siteUrl.'/'.$partone.'/checkout" method=post>';
	echo "<input type='hidden' name='id' value='{$p_invoice_num}' />";
	echo "<input type='hidden' name='x_card_num' value='{$p_card_num}' />";
	echo "<input type='hidden' name='x_card_code' value='{$p_card_code}' />";
	echo "<input type='hidden' name='ex_month' value='{$ex_month}' />";
	echo "<input type='hidden' name='ex_year' value='{$ex_year}' />";
	echo "<input type='hidden' name='x_amount' value='{$p_amount}' />";
	echo "<input type='hidden' name='x_description' value='{$p_description}' />";
	echo "<input type='hidden' name='x_email_customer' value='{$p_email_customer}' />";
	echo "<input type='hidden' name='x_invoice_num' value='{$p_invoice_num}' />";
	echo "<input type='hidden' name='x_first_name' value='{$p_first_name}' />";
	echo "<input type='hidden' name='x_last_name' value='{$p_last_name}' />";
	echo "<input type='hidden' name='x_last_name' value='{$p_last_name}' />";
	echo "<input type='hidden' name='x_address' value='{$p_address}' />";
	echo "<input type='hidden' name='x_city' value='{$p_city}' />";
	echo "<input type='hidden' name='x_state' value='{$p_state}' />";
	echo "<input type='hidden' name='x_zipp' value='{$p_zip}' />";
	echo "<input type='hidden' name='x_phone' value='{$p_phone}' />";
	echo "<input type='hidden' name='x_email' value='{$p_email}' />";
	echo "<input type='hidden' name='x_ship_to_first_name' value='{$p_ship_to_first_name}' />";
	echo "<input type='hidden' name='x_ship_to_last_name' value='{$p_ship_to_last_name}' />";
	echo "<input type='hidden' name='x_ship_to_address' value='{$p_ship_to_address}' />";
	echo "<input type='hidden' name='x_ship_to_city' value='{$p_ship_to_city}' />";
	echo "<input type='hidden' name='x_ship_to_state' value='{$p_ship_to_state}' />";
	echo "<input type='hidden' name='x_ship_to_zip' value='{$p_ship_to_zip}' />";
	echo "<input type='submit' name='submit' value='Return' class='submit' />";
	echo "</form>";
	mail("todd@hi-fimedia.com","Failed Order","{$siteUrl}/invoice/{$p_invoice_num} did not complete - {$p_email}","From: {$siteName} <info@guitaritup.com>");

}

if ($response_array[0] == "4") {
	echo "<h1>Transaction Being Reviewed</h1>";
	echo "<p>Your transaction is being reviewed.  Once approved you will be notified and your product will be shipped.</p>";
	echo '<form name="returnform" action="'.$siteUrl.'/'.$partone.'/checkout" method=post>';
	echo "<input type='hidden' name='id' value='{$p_invoice_num}' />";
	echo "<input type='hidden' name='x_card_num' value='{$p_card_num}' />";
	echo "<input type='hidden' name='x_card_code' value='{$p_card_code}' />";
	echo "<input type='hidden' name='ex_month' value='{$ex_month}' />";
	echo "<input type='hidden' name='ex_year' value='{$ex_year}' />";
	echo "<input type='hidden' name='x_amount' value='{$p_amount}' />";
	echo "<input type='hidden' name='x_description' value='{$p_description}' />";
	echo "<input type='hidden' name='x_email_customer' value='{$p_email_customer}' />";
	echo "<input type='hidden' name='x_invoice_num' value='{$p_invoice_num}' />";
	echo "<input type='hidden' name='x_first_name' value='{$p_first_name}' />";
	echo "<input type='hidden' name='x_last_name' value='{$p_last_name}' />";
	echo "<input type='hidden' name='x_last_name' value='{$p_last_name}' />";
	echo "<input type='hidden' name='x_address' value='{$p_address}' />";
	echo "<input type='hidden' name='x_city' value='{$p_city}' />";
	echo "<input type='hidden' name='x_state' value='{$p_state}' />";
	echo "<input type='hidden' name='x_zipp' value='{$p_zip}' />";
	echo "<input type='hidden' name='x_phone' value='{$p_phone}' />";
	echo "<input type='hidden' name='x_email' value='{$p_email}' />";
	echo "<input type='hidden' name='x_ship_to_first_name' value='{$p_ship_to_first_name}' />";
	echo "<input type='hidden' name='x_ship_to_last_name' value='{$p_ship_to_last_name}' />";
	echo "<input type='hidden' name='x_ship_to_address' value='{$p_ship_to_address}' />";
	echo "<input type='hidden' name='x_ship_to_city' value='{$p_ship_to_city}' />";
	echo "<input type='hidden' name='x_ship_to_state' value='{$p_ship_to_state}' />";
	echo "<input type='hidden' name='x_ship_to_zip' value='{$p_ship_to_zip}' />";
	echo "<input type='submit' name='submit' value='Return' class='submit' />";
	echo "</form>";

	mail("todd@hi-fimedia.com","Failed Order","{$siteUrl}/invoice/{$p_invoice_num} did not complete - {$p_email}","From: {$siteName} <info@guitaritup.com>");

}


// individual elements of the array could be accessed to read certain response
// fields.  For example, response_array[0] would return the Response Code,
// response_array[2] would return the Response Reason Code.
// for a list of response fields, please review the AIM Implementation Guide

} else {

	echo "<h1>Missing Email Address</h1>";
	echo "<p>You did not include a valid email address, please try again:";
	echo '<form name="returnform" action="'.$siteUrl.'/'.$partone.'/checkout" method=post>';
	echo "<input type='hidden' name='id' value='{$p_invoice_num}' />";
	echo "<input type='hidden' name='x_card_num' value='{$p_card_num}' />";
	echo "<input type='hidden' name='x_card_code' value='{$p_card_code}' />";
	echo "<input type='hidden' name='ex_month' value='{$ex_month}' />";
	echo "<input type='hidden' name='ex_year' value='{$ex_year}' />";
	echo "<input type='hidden' name='x_amount' value='{$p_amount}' />";
	echo "<input type='hidden' name='x_description' value='{$p_description}' />";
	echo "<input type='hidden' name='x_email_customer' value='{$p_email_customer}' />";
	echo "<input type='hidden' name='x_invoice_num' value='{$p_invoice_num}' />";
	echo "<input type='hidden' name='x_first_name' value='{$p_first_name}' />";
	echo "<input type='hidden' name='x_last_name' value='{$p_last_name}' />";
	echo "<input type='hidden' name='x_last_name' value='{$p_last_name}' />";
	echo "<input type='hidden' name='x_address' value='{$p_address}' />";
	echo "<input type='hidden' name='x_city' value='{$p_city}' />";
	echo "<input type='hidden' name='x_state' value='{$p_state}' />";
	echo "<input type='hidden' name='x_zipp' value='{$p_zip}' />";
	echo "<input type='hidden' name='x_phone' value='{$p_phone}' />";
	echo "<input type='hidden' name='x_email' value='{$p_email}' />";
	echo "<input type='hidden' name='x_ship_to_first_name' value='{$p_ship_to_first_name}' />";
	echo "<input type='hidden' name='x_ship_to_last_name' value='{$p_ship_to_last_name}' />";
	echo "<input type='hidden' name='x_ship_to_address' value='{$p_ship_to_address}' />";
	echo "<input type='hidden' name='x_ship_to_city' value='{$p_ship_to_city}' />";
	echo "<input type='hidden' name='x_ship_to_state' value='{$p_ship_to_state}' />";
	echo "<input type='hidden' name='x_ship_to_zip' value='{$p_ship_to_zip}' />";
	echo "<input type='submit' name='submit' value='Return' class='submit' />";
	echo "</form>";
	mail("todd@hi-fimedia.com","Failed Order","{$siteUrl}/invoice/{$p_invoice_num} did not complete - {$p_email}","From: {$siteName} <info@guitaritup.com>");
}

?>

