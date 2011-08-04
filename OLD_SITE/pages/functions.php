<?	
	
	function eHeader() {
		$eHeader = '
		<div class="grid_8">
			<div class="box-header">Ecommerce Management</div>
			<div class="box">

				<div class="buttons large">
				<a href="?fuse=admin.ecommerce.manage">Products</a> 
				<a href="?fuse=admin.ecommerce.pages&type=categories">Categories</a> 
				<a href="?fuse=admin.ecommerce.pages&type=manufacturers">Manufacturers</a> 
				<a href="?fuse=admin.ecommerce.orders">Orders</a> 
				<a href="?fuse=admin.ecommerce.discount">Discount Codes</a>
				</div>

			</div>
		</div>';
		return "";
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
	
	function paypalEmail() {
		$set = mf(mq("select `id`,`paypal` from `[p]ecommerce` where `id`='1' limit 1"));
		return $set["paypal"];
	}
	
	function customerService() {
		$set = mf(mq("select `id`,`customerservice` from `[p]ecommerce` where `id`='1' limit 1"));
		return $set["customerservice"];
	}
	

?>