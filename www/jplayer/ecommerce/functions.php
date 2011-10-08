<?	

	function cartUrl() {
		$set = mf(mq("select `id`,`cart` from `[p]ecommerce` where `id`='1' limit 1"));
		return siteUrl()."/".filename($set["cart"]);
	}
	
	function money($money) {		
		$money = number_format($money, 2, '.', '');		
		return $money;	
	}	
	
	function productImage($id) {
		$row = mf(mq("select `id`,`image` from `[p]ecommerce_products` where `id`='{$id}' limit 1"));
		return $row["image"];
	}
	
	function productPrice($id) {
		$row = mf(mq("select `id`,`price` from `[p]ecommerce_products` where `id`='{$id}' limit 1"));
		return str_replace("$", "", $row["price"]);
	}
	
	function productName($id) {
		$row = mf(mq("select `id`,`name` from `[p]ecommerce_products` where `id`='{$id}' limit 1"));
		return nohtml($row["name"]);
	}
	
	function productUrl($id) {
	
		$product = mf(mq("select `id`,`page`,`origin`,`subcat`,`filename` from `[p]ecommerce_products` where `id`='{$id}' limit 1"));
		
		if ($product["page"] != "") { $getPage = "/".filename($product["page"]); }
		if ($product["origin"] != "") { $getOrigin = "/".filename($product["origin"]); }
		if ($product["subcat"] != "") { 
			$explode = explode(",", $product["subcat"]);
			$getSubcat = "/".filename($explode[1]);			
		}
		if ($product["filename"] != "") { $getFilename = "/".$product["filename"]; }
		
		return siteUrl().$getPage.$getOrigin.$getSubcat.$getFilename;
	
	}	

	function authorize() {
		$set = mf(mq("select `id`,`authorize` from `[p]ecommerce` where `id`='1' limit 1"));
		return $set["authorize"];
	}
	
	function paypalEmail() {
		$set = mf(mq("select `id`,`paypal` from `[p]ecommerce` where `id`='1' limit 1"));
		return $set["paypal"];
	}

	function customerService() {
		$set = mf(mq("select `id`,`customer_service` from `[p]ecommerce` where `id`='1' limit 1"));
		return $set["customer_service"];
	}
	
	function taxRate() {
		$set = mf(mq("select `id`,`salestax` from `[p]ecommerce` where `id`='1' limit 1"));
		return $set["salestax"];
	}
	
	function shippingRate() {
		$set = mf(mq("select `id`,`shipping_rate` from `[p]ecommerce` where `id`='1' limit 1"));
		return $set["shipping_rate"];
	}	
	
	function shippingDiscount() {
		$set = mf(mq("select `id`,`shipping_discount` from `[p]ecommerce` where `id`='1' limit 1"));
		return $set["shipping_discount"];
	}
	
?>