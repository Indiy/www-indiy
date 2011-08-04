<?

	$database = "[p]ecommerce_photos";
	
	/* Get the Gallery ID */
	$galleryID = $_GET["gallery"];
	$gallery_id = $_GET["gallery"];


	/* Activate Item */
	if (isset($_GET["activate"]) && $_GET["activate"] != "") {
		$activate_id = $_GET["activate"];
		mq("UPDATE {$database} SET `active`='1' WHERE id={$activate_id}");
	}


	/* Dectivate Item */
	if (isset($_GET["deactivate"]) && $_GET["deactivate"] != "") {
		$deactivate_id = $_GET["deactivate"];
		mq("UPDATE {$database} SET `active`='0' WHERE id={$deactivate_id}");
	}


	/* Dectivate Item */
	if (isset($_GET["delete"]) && $_GET["delete"] != "") {
		$delete_id = $_GET["delete"];
		mq("DELETE FROM {$database} WHERE id={$delete_id}");
	}


//// Include Template Design ///////////////////////
  
?>


<script type="text/javascript">
	$(document).ready(function(){

		$(function() {
		$("ul.list").sortable({ opacity: 0.8, cursor: 'move', update: function() {
				
				var order = $(this).sortable("serialize") + '&photos=photos'; 
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
		<div class="box-header"><? echo $gallery_name; ?></div>
		<div class="box">
		<p><a href="?fuse=admin.ecommerce.manage">Return to Products</a> | <a href="?fuse=admin.ecommerce.add&id=<? echo $gallery_id; ?>">Edit Product Details</a><br /><a href="?fuse=admin.ecommerce.addphoto&gallery=<? echo $gallery_id; ?>">Add Photos</a></p>
		</div>
	</div>
	
	<div class="grid_8">
		<div class="box">
		<ul class="list">
		<?
		$load = mq("select * from `{$database}` where `productid`='{$gallery_id}' order by `order`");
		while ($row = mf($load)) {

			$photo_id = $row["id"];
			$photo_image = $row["image"];
			$photo_active = $row["active"];

			if ($photo_active == "0") {
				$active = '<a href="?fuse=admin.ecommerce.managephotos&gallery='.$galleryID.'&activate='.$photo_id.'" class="activate">Activate</a>';
			} else {
				$active = ' <a href="?fuse=admin.ecommerce.managephotos&gallery='.$galleryID.'&deactivate='.$photo_id.'">Deactivate</a>';
			}

			echo '<li id="arrayorder_'.$photo_id.'" class="order photolist">';
			echo '<p><img src="system/images/products/'.$photo_image.'" width="75" height="75" /><br /><br />';
			echo '<a href="?fuse=admin.ecommerce.addphoto&id='.$photo_id.'">Edit</a> | <a href="#" onclick="confirmDelete(\'?fuse=admin.ecommerce.managephotos&gallery='.$galleryID.'&delete='.$photo_id.'\')">Delete</a><br />'.$active.'</p>';
			echo '</li>';
		}
		?>
		</ul>
		<div class="clear"></div>
		</div>
	</div>
