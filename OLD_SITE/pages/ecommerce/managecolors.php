<? 
//////////////////////////////////////////////////
//// Copyright 2008 Hi-Fi Social. ////////////////
//// Developed by Todd Low of Hi-Fi Media ////////
//// For questions, contact info@hifisocial.com //
//////////////////////////////////////////////////

	$productid = $_GET["gallery"];
	$database = "[p]ecommerce_products_colors";

	if ($_GET["delete"] == "true" && $_GET["id"] != "") {
		mq("DELETE FROM `{$database}` WHERE `id`='{$_GET["id"]}'");
		$successMessage = '<div id="notify">Successfully Deleted</div>';
	}
	
	
	// Upload
	
	if ($_POST["submit"] != "") {
		$getsku = mf(mq("select `sku` from `[p]ecommerce_products` where `id`='{$productid}' limit 1"));
		$sku = $getsku["sku"];
		
		$name = my($_POST["name"]);
		$stock = my($_POST["stock"]);
		
		if (is_uploaded_file($_FILES["image"]["tmp_name"])) {
			if (($_FILES["image"]["type"] == "image/jpeg") || ($_FILES["image"]["type"] == "image/pjpeg")) {
				$write_image = "{$sku} {$name}.jpg";
				@move_uploaded_file($_FILES['image']['tmp_name'], 'system/images/products/colors/'.$write_image);
			} else {
				$error .= "Invalid File Type, only jpeg files are allowed.";
			}
		}
		
		if (is_uploaded_file($_FILES["thumb"]["tmp_name"])) {
			if (($_FILES["thumb"]["type"] == "image/jpeg") || ($_FILES["thumb"]["type"] == "image/pjpeg")) {
				$write_thumb = $sku."_".rand(1111111,9999999)."_thumb.jpg";
				@move_uploaded_file($_FILES['thumb']['tmp_name'], 'system/images/products/colors/'.$write_thumb);
			} else {
				$error .= "Invalid File Type, only jpeg files are allowed.";
			}
		}
		
		if ($stock == "") { $stock = "unlimited"; }
		
		insert($database,"productid|name|image|thumb|stock","{$productid}|{$name}|{$write_image}|{$write_thumb}|{$stock}");
		$successMessage = "<div id='notify'>Successfully added a new color</div>";
	}
	
	$form = '
				<form method="post" enctype="multipart/form-data">
				
					<label>Name</label>
					<input type="text" name="name" value="" />
					<div class="clear"></div>
					
					<label>Image</label>
					<input type="file" name="image" value="" />
					<div class="clear"></div>
					
					<label>Thumbnail</label>
					<input type="file" name="thumb" value="" />
					<div class="clear"></div>
					
					<label>Stock</label>
					<input type="text" name="stock" value="" />
					<div class="clear"></div>
					
					<div>
						<input type="submit" class="submit" name="submit" value="Save" />
					</div>
					
				</form>';	
	
	
	
	
	/* Load all products */
	$allproducts = mq("select * from `{$database}` where `productid`='{$productid}' ORDER BY `order` ASC, `id` desc");

	
//// Include Template Design ///////////////////////

?>

<style>
.three { border-right: 0; }
.one { border-right: 1px solid #ccc; }
</style>

<script type="text/javascript">
	$(document).ready(function(){
		
		$(".addnewpage").click(function(){
			$(".newpage").slideToggle(600);
		});
		
		$("#response").hide();
		$(function() {
		$("ul.list").sortable({ opacity: 0.8, cursor: 'move', update: function() {
				
				var order = $(this).sortable("serialize") + '&colors=colors'; 
				$.post("modules/admin/ecommerce/ajax.php", order, function(theResponse){
					$("#response").html(theResponse);
					$("#response").slideDown('slow');
				}); 															 
			}								  
			});
		});

	});
</script>
	
		
		<div class="grid_8">
			<div class="box-header">Manage Products</div>
			<div class="box">
				
	
				<div class="button addnewpage">
					Add New Color
				</div>
				
				<?=$successMessage;?>
				
				<div class="newpage">
					<?=$form;?>
				</div>
				
				
				
				<div class='dark three'><strong>Name</strong></div> 
				<div class='dark two'><strong>Stock</strong></div>
				<div class='dark one'><strong>Tools</strong></div> 
				<div style='clear: both;'></div> 
				
				<ul class='list'>

				<?				
					while ($row = mysql_fetch_array($allproducts)) {
					  $pid = $row["id"];
					  $pproductid = $row["productid"];
					  $pname = stripslashes($row["name"]);
					  $pimage = stripslashes($row["image"]);
					  $pthumb = stripslashes($row["thumb"]);
					  $pdescription = $row["order"];
					  $pstock = $row["stock"];
					  
					echo '<li id="arrayorder_'.$pid.'" class="order">'."\n";
					echo "<div class='three'><img src='".trueSiteUrl()."/system/images/products/colors/{$pthumb}' height='15' /> &nbsp;<span class='orderTitle'><strong>$pname</strong></span></div>\n";
					echo "<div class='two'>{$pstock}</div>\n";
					echo "
							<div class='one buttons'>
								<a href=\"#\" class=\"xdelete\" onclick=\"confirmDelete('?fuse=admin.ecommerce.managecolors&gallery={$productid}&delete=true&id=$pid')\">Delete</a>
							</div> \n";
					echo "<div style='clear: both;'></div>\n";
					echo "</li>\n";
					
					}
			 	?>	

			    </ul>
			</div>
		</div>