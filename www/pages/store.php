<?
	if (isAdmin()) {
		$me = $_SESSION["me"];
	} else {
		$me = $_SESSION["me"];
	}

	$database = "[p]musicplayer_ecommerce";
		
	if ($_POST["submit"] != "") {
	
		$s_paypalemail = $_POST["paypal"];
		$s_authorize = $_POST["authorize"];
		$s_customerservice = $_POST["customerservice"];
		
		$s_salestax = $_POST["salestax"];
		$s_shipping_rate = $_POST["shipping_rate"];
		$s_shipping_discount = $_POST["shipping_discount"];
		
			
		// Update Ecommerce Settings
		$tables = "customer_service|paypal|authorize|salestax|shipping_rate|shipping_discount";
		$values = "{$s_customerservice}|{$s_paypalemail}|{$s_authorize}|{$s_salestax}|{$s_shipping_rate}|{$s_shipping_discount}";
		update($database,$tables,$values,"userid",$me);
		
		$successMessage = '<div id="notify">Successfully updated!</div>';
	}
	
	$set = mf(mq("select * from `{$database}` where `id`='{$me}' limit 1"));
	$e_customerservice = $set["customer_service"];
	$e_paypalemail = $set["paypal"];
	$e_authorize = $set["authorize"];
	$e_root = $set["root"];
	$e_cart = $set["cart"];
	$e_categories = $set["categories"];
	$e_manufacturers = $set["manufacturers"];
	$e_salestax = $set["salestax"];
	$e_shippingrate = $set["shipping_rate"];
	$e_shippingdiscount = $set["shipping_discount"];

?>

		<div id="content">
			<?=$successMessage;?>
			<div class="post">
				<h2 class="title"><a href="#">Store Settings</a></h2>

				<form method="post">
				
					<label>Customer Service Email</label>
					<input type="text" name="customerservice" value="<?=$e_customerservice;?>" class="text" />
					<div class="clear"></div>
					
					<label>PayPal Email</label>
					<input type="text" name="paypal" value="<?=$e_paypalemail;?>" class="text" />
					<div class="clear"></div>
					
					<label>Sales Tax Rate</label>
					<input type="text" name="salestax" value="<?=$e_salestax;?>" class="text" />
					<div class="clear"></div>
					
					<label>Shipping Rate</label>
					<input type="text" name="shipping_rate" value="<?=$e_shippingrate;?>" class="text" />
					<div class="clear"></div>
					
					<label>Shipping Discount</label>
					<input type="text" name="shipping_discount" value="<?=$e_shippingdiscount;?>" class="text" />
					<div class="clear"></div>
					
					<input type="submit" name="submit" value="Submit" class="submit" />
					<div class="clear"></div>
					
				</form>
			</div>
		</div>
	