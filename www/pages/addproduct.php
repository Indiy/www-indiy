<? 
//////////////////////////////////////////////////
//// Copyright 2008 Hi-Fi Social. ////////////////
//// Developed by Todd Low of Hi-Fi Media ////////
//// For questions, contact info@hifisocial.com //
//////////////////////////////////////////////////

	$database = "[p]musicplayer_ecommerce_products";
	if (isAdmin() || isLabel()) {
		$me = $_SESSION["me"];
	} else {
		$me = $_SESSION["me"];
	}	

//// ADD TO DATABASE /////////////////////////////

    if (isset($_POST["submit"])) {
		
		$getid = $_GET["id"];
		
		if (is_uploaded_file($_FILES["file"]["tmp_name"])) {
			$productimage = strtolower(rand(111,999)."_".basename($_FILES["file"]["name"]));
			@move_uploaded_file($_FILES['file']['tmp_name'], 'artists/products/' . $productimage);
			$image = $productimage;
		} else {
			if ($_GET["id"] != "") {
				$productss = mf(mq("select `image` from $database where id='$getid'"));
				$image = $productss["image"];	
			} else {
				$image = "";
			}
		}
		

		$origin = $_POST["origin"];
		$s=0;
		foreach ($_POST["subcat"] as $value) {
			if ($s == "0") {
				$subcat .= ",".$value.",";
			} else {
				$subcat .= $value.",";
			}
			++$s;
		}
				
		$sale = $_POST["sale"];
		$page = $_POST["page"];
		// Give page the ecommerce plugin // update("[p]content","plugin",$plugin_products_id,"id",$page);
		
		$name = my($_POST["name"]);
		$newvideo = $_POST["video"];		
		$productdescription = my($_POST["description"]);
		$lprice = $_POST["price"];
		$price = str_replace("$", "", $lprice);
		$ldiscount = $_POST["discount"];
		$discount = str_replace("$", "", $ldiscount);
		$sku = $_POST["sku"];
		$newsize = $_POST["size"];
		$newcolor = $_POST["color"];
		if ($_POST["stock"] != "") {
			$newstock = $_POST["stock"];
		} else {
			$newstock = "unlimited";
		}
		$manufacturer = $_POST["manufacturer"];
		$newtags = my($_POST["tags"]);
		if ($_POST["filename"] != "") {
			$fname = strtolower($_POST["filename"]);
		} else {
			$fname = strtolower(my($_POST["name"]));
		}
			/* convert " - " to "-" */
			$no1 = str_replace(" - ", "-", $fname);
			$no2 = str_replace(" ", "-", $no1);
			$no3 = str_replace(".", "", $no2);
			$no4 = str_replace("&", "and", $no3);
			$no5 = str_replace('"', '', $no4);
			$no6 = str_replace("'", "", $no5);
			$no7 = str_replace("(", "-", $no6);
			$no8 = str_replace("/", "-", $no7);
			$nospaces = str_replace(")", "-", $no8);
			$newfilename = $nospaces;

		$tables = "artistid|page|stock|origin|subcat|name|filename|description|image|price|discount|sku|sale|manufacturer|tags|size|color";
		$values = "$me|$page|$newstock|$origin|$subcat|$name|$newfilename|$productdescription|$image|$price|$discount|$sku|$sale|$manufacturer|$newtags|$newsize|$newcolor";

		if ($_POST["situation"] == "Update") {
			update($database,$tables,$values,"id",$getid);
		} else {
			insert($database,$tables,$values);
			$newuserid = mysql_insert_id();
		}
		
		$successMessage = "<div id='notify'>Success!</div>";
		refresh("2","?p=home");	
	
    }
	
	
//// LOAD PRE EXISTING DATA //////////////////////

	if ($_GET["id"] != "") {

		$getid = $_GET["id"];	 
		$productss = mf(mq("select * from $database where id='$getid'"));

		$porigin = $productss["origin"];
		$psubcat = explode(",", $productss["subcat"]);
		foreach ($psubcat as $value) {
			$varname = "subcat".$value;
			$$varname = "true";
		}		
		
		$psale = $productss["sale"];  	  
		$page = $productss["page"];
		$pname = stripslashes(htmlentities($productss["name"]));
		$pvideo = $productss["op2"];	  
		$pproductdescription = stripslashes(htmlentities($productss["description"]));
		$pimage = $productss["image"];	
		$pprice = $productss["price"];
		$ptags = nohtml($productss["tags"]);
		$psize = $productss["size"];
		$pcolor = $productss["color"];
		$pstock = $productss["stock"];
		$pdiscount = $productss["discount"];
		$pfilename = htmlentities($productss["filename"]);
		$psku = $productss["sku"];
		$pmanufacturer = $productss["manufacturer"];

		$situation = "Update";

	} else {
		$situation = "Add New";
	}	
	
	
	$set = mf(mq("select * from `[p]ecommerce` where `id`='1' limit 1"));
	$rootcat = $set["categories"];
	$rootman = $set["manufacturers"];


//// Include Template Design ///////////////////////

?>
<script>
	$(document).ready(function(){
		$('#origin').change(function() {
			var neworigin = $("select#origin").val();
			if (neworigin != "new") {
				$("#subcat").html("Loading...");
				var order = '&origin=' + neworigin; 
				$.post("modules/admin/ecommerce/ajax.php", order, function(theResponse){
					$("#subcat").html(theResponse);
				});				
			}
		});
	});

</script>

		<div id="content">
			<?=$successMessage;?>
			<div class="post">
				<h2 class="title"><a href="#"><?=$situation;?> Product</a></h2>

				<form method="post" enctype="multipart/form-data">
				
					
					<label>Name:</label>
					<input type="text" name="name" value="<? echo $pname; ?>" id="input" class="input" />
					<div class="clear"></div>	   

					<? if ($_GET["id"] != "") { ?>
					
					<input type="hidden" name="filename" value="<? echo $pfilename; ?>" id="input" class="input" />

					<? } ?>

					<label>Category:</label>
					<select name="origin" id="origin" class="input">
						<option value=""> -- Select -- </option>
						<?
							$cont = mq("select * from `[p]musicplayer_ecommerce_categories` order by `name` asc");
							while ($rw = mf($cont)) {
								$man_id = $rw["id"];
								$man_name = stripslashes($rw["name"]);
								if ($porigin == $man_id) { $selected = " selected"; } else { $selected = ""; }
								echo "<option value='{$man_id}'{$selected}>{$man_name}</option>\n";
							}
						?>						
						</select>
					<div class="clear"></div>		    

					<label>Description:</label>
					<textarea name="description" id="input" class="input smalltextarea"><? echo $pproductdescription; ?></textarea>
					<div class="clear"></div>	   
		   
					<label>Image:</label>
					<input type="file" name="file" value="" id="input" class="input" /> <? if ($pimage != "") {?> <img src="artists/products/<?=$pimage;?>" height="20" /><? } ?>
					<div class="clear"></div>	   
		   
					<label>Price:</label>
					<input type="text" name="price" value="<? echo $pprice; ?>" id="input" class="input" />
					<div class="clear"></div>	   
		   
					<label>Sku Number:</label>
					<input type="text" name="sku" value="<? echo $psku; ?>" id="input" class="input" />
					<div class="clear"></div>
					
					<label>Size:<br /><small><small>(Separated by commas)</small></small></label>
					<input type="text" name="size" value="<? echo $psize; ?>" id="input" class="input" />
					<div class="clear"></div>
					
					<label>Colors:<br /><small><small>(Separated by commas)</small></small></label>
					<input type="text" name="color" value="<? echo $pcolor; ?>" id="input" class="input" />
					<div class="clear"></div>

					<label></label>
					<input type="hidden" value="<? echo $situation; ?>" name="situation" /><input type="submit" name="submit" class="submit" value="<? echo $situation; ?>" id="submitr" />
					<div class="clear"></div>		

					
					<div class="buttons block">
						<? if ($_GET["id"] != "") { ?><a href="#" onclick="confirmDelete('?p=home&type=products&a=<?=$me;?>&delete=true&id=<?=$getid;?>')">Delete Product</a> <? } ?>
					</div>					
					
				</form>

			</div>
		</div>			
