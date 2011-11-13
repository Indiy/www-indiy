<?
	//$white = "";
	
	$database = "[p]ecommerce_products";
	$database_colors = "[p]ecommerce_products_colors";

	$item = $variable;
	$product = mf(mq("select * from `$database` where `filename`='{$item}' limit 1"));
	
	$product_id 		= $product["id"];
	$product_name 		= nohtml($product["name"]);
	$product_filename 	= $product["filename"];
	$product_description= nohtml($product["description"]);
	$product_image		= $product["image"];
	$product_price		= $product["price"];
	$product_discount	= $product["discount"];
	$product_sku		= $product["sku"];
	// $product_sizes		= $product["size"];
	// $product_colors		= $product["color"];
	$product_shipping	= $product["shipping"];
	
	$loadcolors = mq("select * from `{$database_colors}` where `productid`='{$product_id}' order by `order` asc");
	
	while ($chunkcolor = mf($loadcolors)) {
			
		$color_name = stripslashes($chunkcolor["name"]);
		$color_image = $chunkcolor["image"];
		$color_thumb = $chunkcolor["thumb"];
		$color_stock = $chunkcolor["stock"];
			
		if ($color_stock != "0") {

			$product_colors = "true";
		
			$color_class = str_replace(" ", "-", $color_name);
			$color .= '<a href="#" class="swatches '.$color_class.'">'.$color_name.'</a>';
				
			$photosColor .= ",'".trueSiteUrl()."/system/images/products/colors/".$color_image."'";
			$extraColor = $extraColor.",''";
				
			$photogalleryswatches .= '<img src="'.trueSiteUrl().'/system/images/products/colors/'.$color_image.'" rel="prettyPhoto[gallery]" border="0" />';

		}
	}

	
?>

	<link href="<?=pluginUrl();?>/ecommerce/style.css" rel="stylesheet" type="text/css" media="screen" /> 
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.js" type="text/javascript"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js" type="text/javascript"></script>
	<script src="<?=pluginUrl();?>/ecommerce/js/jquery.prettyPhoto.js" type="text/javascript" charset="utf-8"></script>
	<script>

		function addToCart() {
			<? if ($product_colors) { ?>
			var color = document.getElementById('swatch').value;
			if (color == "Select Color") {
				alert("You must select a color");
				return false;
			} else {
				return true;
			}
			<? } ?>
			
			<? if ($product_sizes) { ?>
			var size = document.getElementById('size').value;
			if (size == "Select Size") {
				alert("You must select a size");
				return false;
			} else {
				return true;
			}
			<? } ?>
		}
		
		$(document).ready(function(){
			$("a.swatches").click(function(){
				var newswatch = $(this).text();
				$("#swatch").val(newswatch);
				return false;
			});
			
			$("a.swatches").mouseover(function() {
				$("a.imageHolder").hide();
				var newswatch = $(this).text();
				var imglocation = '<img src="<?=trueSiteUrl();?>/system/images/products/colors/<?=$product_sku;?> ' + newswatch + '.jpg" border="0" class="photos" />';
				$("a.imageHolder").html(imglocation).fadeIn();
			});
			
			$("a.photos").mouseover(function(){
				$("a.imageHolder").hide();
				var img = $(this).html();
				$("a.imageHolder").html(img).fadeIn();
			});

		});
	</script>

	<!-- CUSTOM CUSTOM -->
	<style>
	#page-content { width: 960px !important; }
	#page-sidebar { display: none !important; }
	#content h1 { display: none !important; }
	</style>
	<!-- CUSTOM CUSTOM -->

	<div id="productdetails">
		<h3><?=$_POST["color"];?></h3>
		<form method="post" action="<?=cartUrl();?>" onsubmit="return addToCart();">
		<input type="hidden" name="productid" value="<?=$product_id;?>" />
		
		<div id="left">			
			<?
				$more = mq("select * from `[p]ecommerce_photos` where `productid`='{$product_id}' order by `order` asc, `id` desc");
				if (num($more) > "0") {
					$morephotos = '<a href="'.trueSiteUrl().'/system/images/products/'.$product_image.'" rel="prettyPhoto[gallery]" class="photos"><img src="'.trueSiteUrl().'/system/images/products/'.$product_image.'" border="0" class="photos" /></a>';
					$photos = "'".trueSiteUrl()."/system/images/products/{$product_image}'";
					$extra = "''";
					while ($mp = mf($more)) {
						$mp_image = $mp["image"];
						$morephotos .= '<a href="'.trueSiteUrl().'/system/images/products/'.$mp_image.'" rel="prettyPhoto[gallery]" class="photos"><img src="'.trueSiteUrl().'/system/images/products/'.$mp_image.'" border="0" class="photos" /></a>';
						$photos .= ",'".trueSiteUrl()."/system/images/products/{$mp_image}'";
						$extra = $extra.",''";
					}
				} else {
					$photos = "'".trueSiteUrl()."/system/images/products/{$product_image}'";
					$extra = "''";
				}
				
			?>
			<div style="display: none;"><?=$photogalleryswatches;?></div>

			<div class="image"><a href="#" onclick="$.prettyPhoto.open([<?=$photos.$photosColor;?>],[<?=$extra.$extraColor;?>],[<?=$extra.$extraColor;?>]);" class="imageHolder"><img src="/system/images/products/<?=$product_image;?>" border="0" alt="" class="product" /></a></div> 
			<div class="spacer"></div>
			<div class="morephotos">
				<?=$morephotos;?>
			</div>
		</div>
		
		<div id="right">
			<h2 class="title"><?=stripslashes($product_name);?></h1>
			<div class="addtocart">
				<p>
				
				<? if ($product_discount) { ?>
					<strike>Price: $ <?=$product_price;?></strike><br />
					<strong class="em">Price: $ <?=$product_discount;?></strong>
				<? } else { ?>
					<strong class="em">Price: $ <?=$product_price;?></strong>
				<? } ?>
				
				
				<?if ($product_sku) { ?>
					<br />
					SKU Number: <?=$product_sku;?>
				<? } ?>
			
				</p>
				
				
				<? if ($product_colors) { ?>
					<p><strong class="em">COLORS:</strong> <input type="text" name="color" id="swatch" value="Select Color" class="" /><br />
					<?=$color;?>
					<div class='clear'></div>
					</p>
				<? } ?>
				
				
				<? if ($product_sizes) {
					$chunksize = explode(",", $product_sizes);

					foreach ($chunksize as $key => $value) {
					$size .= "<option value='$value'>$value</option>\n";
					}
					?>
					<p>
					<select name="size">
					<option value=""> - Select Size - </option>
					<?=$size;?>
					</select>
					</p>
				<? } ?>

				
				<? if ($product_description) { ?>
					<p><strong class="em">DESCRIPTION:</strong><br /><?=$product_description;?></p>
				<? } ?>
				
				
				<? if ($pstock == "unlimited") { ?>
					<h3 style="text-align: center;">** Out of stock, check back soon **</h3>
				<? } else { ?>
					<input type="submit" value="Add to Cart" class="submit" name="addtocart" />
				<? } ?>
				
			</div>
		</div>
		
		<div class="clear"></div>
		</form>
	</div>

	
<script type="text/javascript" charset="utf-8">
	$(document).ready(function(){
		$("a[rel^='prettyPhoto']").prettyPhoto({
			theme: 'light_rounded', /* light_rounded / dark_rounded / light_square / dark_square / facebook */
			overlay_gallery: false,
			keyboard_shortcuts: true
		});
	});
</script>