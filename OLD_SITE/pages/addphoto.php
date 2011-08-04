<?			
//////////////////////////////////////////////////
//// Copyright 2008 Hi-Fi Social. ////////////////
//// Developed by Todd Low of Hi-Fi Media ////////
//// For questions, contact info@hifisocial.com //
//////////////////////////////////////////////////

//// LOAD EXISTING DATA IF AVAILABLE /////////////

	$database = "[p]ecommerce_photos";

	if ($_GET["id"] != "") {

		$getid = $_GET["id"];
		$content = mq("select * from $database where id='$getid'");
		$row = mf($content);

		/* Basics */

		$photo_active = $row["active"];
		$photo_image = $row["image"];

		if ($photo_image != "") {
			$show_image01 = "<img src='system/images/gallery/{$photo_image}' class='image imagefloat' width='75' />";
		}

		if ($photo_active == "1") {
			$livecheckmark = " checked";
		} else {
			$photo_checkmark = " checked";
		}

		$situation = "Edit";

	} else {

		$situation = "Add";

		if ($_GET["gallery"] != "") {

			$galleryid = $_GET["gallery"];
			$usegalleryid = $galleryid;

		}		

	}
	

//// ADD TO DATABASE /////////////////////////////

   if (isset($_POST["submit"])) {

	/* Basics */

	$newactive = $_POST["active"];

	/* Upload Image */

	if (is_uploaded_file($_FILES["image01"]["tmp_name"])) {
	 	$write_image01 = strtolower(rand(11111,99999)."_".basename($_FILES["image01"]["name"]));
	 	@move_uploaded_file($_FILES['image01']['tmp_name'], 'system/images/products/' . $write_image01);
	 	$post_image01 = $write_image01;
	} else {
	    if ($_POST["remove01"] == "remove") {
            $post_image01 = NULL;
        } else {
            $post_image01 = $photo_image;
        }
	}

	if ($situation == "Edit") {		
		update("$database","userid|productid|order|image|active","$me|$galleryid|0|$post_image01|$newactive","id",$getid);
	} else {
		insert("$database","userid|productid|order|image|active","$me|$galleryid|0|$post_image01|$newactive");
		$newid = mysql_insert_id();
	}

	$status = "<div id='notify'>Success!</div>";

   }

//// Include Template Design ///////////////////////
  
  
?>

<? echo $redirect; ?>
	<div class="grid_8">
		<div class="box-header"><? echo $situation; ?> Photo</div>
			<div class="box">
			<p><a href="?fuse=admin.ecommerce.manage">Return to Products</a> | <a href="?fuse=admin.ecommerce.managephotos&gallery=<?=$galleryid;?>">Return to Photos</a> | <a href="?fuse=admin.ecommerce.add&id=<?=$galleryid;?>">Edit Product Details</a></p>

			<? echo $status; ?>
			
			<form method="post" method="post" enctype="multipart/form-data">
					<div id="table">


					<label>Image</label>
					<div class="group">
					<? echo $show_image01; ?><input type="file" name="image01" /><br /><input type="checkbox" class="radio" value="remove" name="remove01" /> <small>Check this to delete picture</small>
					</div>
					<div class="clear"></div>

					<label><strong>Active:</strong></label>
					<input type="radio" class="radio" name="active" value="0"<? echo $draftcheckmark; ?> /> Draft<br />
					<input type="radio" class="radio" name="active" value="1"<? echo $livecheckmark; ?> /> Live
					<div class="clear"></div>					

					<input type="submit" name="submit" value="<? echo $situation; ?> Photo" class="submit" />
				    <div class="clear"></div>
					
				   
					</div>


			</form>
		</div>
	</div>				

			


