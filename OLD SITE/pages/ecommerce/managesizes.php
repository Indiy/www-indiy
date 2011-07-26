<? 
//////////////////////////////////////////////////
//// Copyright 2008 Hi-Fi Social. ////////////////
//// Developed by Todd Low of Hi-Fi Media ////////
//// For questions, contact info@hifisocial.com //
//////////////////////////////////////////////////

	$productid = $_GET["gallery"];
	$database = "[p]ecommerce_products_sizes";

	if ($_GET["delete"] == "true" && $_GET["id"] != "") {
		mq("DELETE FROM `{$database}` WHERE `id`='{$_GET["id"]}'");
		$successMessage = '<div id="notify">Successfully Deleted</div>';
	}
	
	
	// Upload
	
	if ($_POST["submit"] != "") {
		
		$name = my($_POST["name"]);
		$stock = my($_POST["stock"]);
		
		if ($stock == "") { $stock = "unlimited"; }
		
		insert($database,"productid|name|stock","{$productid}|{$name}|{$stock}");
		$successMessage = "<div id='notify'>Successfully added a new size</div>";
	}
	
	$form = '
				<form method="post" enctype="multipart/form-data">
				
					<label>Name</label>
					<input type="text" name="name" value="" />
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
					Add New Size
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
						$pdescription = $row["order"];
						$pstock = $row["stock"];
					  
						echo '<li id="arrayorder_'.$pid.'" class="order">'."\n";
						echo "<div class='three'><span class='orderTitle'><strong>$pname</strong></span></div>\n";
						echo "<div class='two'>{$pstock}</div>\n";
						echo "
								<div class='one buttons'>
									<a href=\"#\" class=\"xdelete\" onclick=\"confirmDelete('?fuse=admin.ecommerce.managesizes&gallery={$productid}&delete=true&id=$pid')\">Delete</a>
								</div> \n";
						echo "<div style='clear: both;'></div>\n";
						echo "</li>\n";
					
					}
			 	?>	

			    </ul>
			</div>
		</div>