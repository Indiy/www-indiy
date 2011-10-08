<? 
//////////////////////////////////////////////////
//// Copyright 2008 Hi-Fi Social. ////////////////
//// Developed by Todd Low of Hi-Fi Media ////////
//// For questions, contact info@hifisocial.com //
//////////////////////////////////////////////////

	$database = "[p]ecommerce_products";

	if ($_GET["delete"] == "true" && $_GET["id"] != "") {
		mq("DELETE FROM `{$database}` WHERE `id`='{$_GET["id"]}'");
		$successMessage = '<div id="notify">Successfully Deleted</div>';
	}

	if (isset($_GET["stock"]) && $_GET["stock"] == "true") {
		$stockid = $_GET["id"];
		update($database,"stock","0","id",$stockid);
	}
	
	if (isset($_GET["instock"]) && $_GET["instock"] == "true") {
		$stockid = $_GET["id"];
		update($database,"stock","10","id",$stockid);
	}
	
	
  /* Order By */
  if ($_GET["orderby"] != "") {
	switch ($_GET["orderby"]) {
		case "name":
			$orderBy = "name";
			break;
		case "subcat":
			$orderBy = "subcat";
			break;
		case "sku":
			$orderBy = "sku";
			break;
		case "manufacturer":
			$orderBy = "manufacturer";
			break;	
		case "origin":
			$orderBy = "origin";
			break;
		case "price":
			$orderBy = "ABS(discount)";
			break;
	}
  } else {
	$orderBy = "name";
  }
  
	if (isset($_POST["query"])) {
		$query = $_POST["query"];
		$searchQ = " WHERE (`name` LIKE '%$query%' OR `description` LIKE '%$search_entry%')";	
	}
	if (isset($_GET["inventory"])) {
		$searchQ = " WHERE `stock`='0'";	
	}  
	
  /* Load all products */
  $allproducts = mq("select * from `{$database}` ORDER BY `order` ASC, `id` desc");

  /* Let's count the total products */
  $countproducts = num($allproducts);
	
//// Include Template Design ///////////////////////

?>

<style>
.three { border-right: 0; }
.one { border-right: 1px solid #ccc; }
</style>

<script type="text/javascript">
	$(document).ready(function(){

		$("#response").hide();
		$(function() {
		$("ul.list").sortable({ opacity: 0.8, cursor: 'move', update: function() {
				
				var order = $(this).sortable("serialize") + '&products=products'; 
				$.post("modules/admin/ecommerce/ajax.php", order, function(theResponse){
					$("#response").html(theResponse);
					$("#response").slideDown('slow');
				}); 															 
			}								  
			});
		});

	});
</script>
	
		
		<div class="post">
			<div class="entry">
				<h1 class="title">Manage Products</h1>
				<?=$successMessage;?>
				<p>There are <? echo $countproducts; ?> products | <a href="?fuse=admin.ecommerce.add">Add Product</a></p>
				<!--
				<p><a href="?fuse=admin.ecommerce.manage&inventory=true">Show out of stock products only</a></p>
				
				Sort by: <a href="?fuse=admin.ecommerce.manage&orderby=name">Name</a> | <a href="?fuse=admin.ecommerce.manage&orderby=sku">Sku</a> | 
				<a href="?fuse=admin.ecommerce.manage&orderby=price">Price</a> | <a href="?fuse=admin.ecommerce.manage&orderby=origin">Origin</a> | 
				<a href="?fuse=admin.ecommerce.manage&orderby=manufacturer">Manufacturer</a> | <a href="?fuse=admin.ecommerce.manage&orderby=subcat">Sub Category</a>
				</p>
				-->
				
				<div class='dark three'><strong>Name</strong></div> 
				<div class='dark two'><strong>Sku</strong></div>
				<div class='dark one'><strong>Tools</strong></div> 
				<div style='clear: both;'></div> 
				
				<ul class='list'>

				<?				
					while ($row = mysql_fetch_array($allproducts)) {
					  $pid = $row["id"];
					  $porigin = $row["origin"];
					  $psubcat = $row["subcat"];
					  $pname = stripslashes($row["name"]);
					  $pdescription = $row["description"];
					  $pimage = $row["image"];			  
					  $pviews = $row["views"];
					  $pdiscount = $row["discount"];
					  $pshipping = $row["shipping"];
					  $psku = $row["sku"];
					  $pstock = ABS($row["stock"]);
					  $pmanufacturer = $row["manufacturer"];
					  $pfilename = $row["filename"];

					  if ($pstock < 1) {
						$instock = '<a href="?fuse=admin.ecommerce.manage&instock=true&id='.$pid.'" class="out">Back In Stock</a>';
					  } else {
						$instock = '<a href="?fuse=admin.ecommerce.manage&stock=true&id='.$pid.'">Out of Inventory</a>';
					  }
					echo '<li id="arrayorder_'.$pid.'" class="order">'."\n";
					echo "<div class='three'><span class='orderTitle'><strong>$pname</strong></span></div>\n";
					echo "<div class='two'><small>{$psku}</small></div>\n";
					echo "
							<div class='one buttons'>
								<a href='?fuse=admin.ecommerce.add&id=$pid'>Edit</a> 
								<a href='?fuse=admin.ecommerce.managephotos&gallery=$pid'>Photos</a> 
								<a href='?fuse=admin.ecommerce.managecolors&gallery=$pid'>Colors</a> 
								<a href='?fuse=admin.ecommerce.managesizes&gallery=$pid'>Sizes</a> 
								<a href=\"#\" class=\"xdelete\" onclick=\"confirmDelete('?fuse=admin.ecommerce.manage&delete=true&id=$pid')\">Delete</a>
							</div> \n";
					echo "<div style='clear: both;'></div>\n";
					echo "</li>\n";
					
					}
			 	?>	

			    </ul>
			</div>
		</div>