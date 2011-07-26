<? 

	include(dirname(__FILE__)."/functions.php");
	$e_type = $_GET["type"];

	if ($e_type == "categories") {
		$set = mf(mq("select `id`,`categories`,`plugin_categories_id` from `[p]ecommerce` where `id`='1' limit 1"));
		$rootid 	= $set["categories"];
		$usetitle 	= "Category";
		$plugin_id	= $set["plugin_categories_id"];
	} else {
		$set = mf(mq("select `id`,`manufacturers`,`plugin_manufacturers_id` from `[p]ecommerce` where `id`='1' limit 1"));
		$rootid 	= $set["manufacturers"];
		$usetitle 	= "Manufacturer";
		$plugin_id 	= $set["plugin_manufacturers_id"];
	}


	/* Activate Item */
	if (isset($_GET["activate"]) && $_GET["activate"] != "") {
		$activate_id = $_GET["activate"];
		mq("UPDATE `[p]content` SET `live`='1' WHERE `id`={$activate_id} and `user`='{$me}'");
	}

	/* Dectivate Item */
	if (isset($_GET["deactivate"]) && $_GET["deactivate"] != "") {
		$deactivate_id = $_GET["deactivate"];
		mq("UPDATE `[p]content` SET `live`='0' WHERE `id`={$deactivate_id} and `user`='{$me}'");
	}

	/* Delete Item */
	if (isset($_GET["delete"]) && $_GET["delete"] != "") {
		$getid = $_GET["id"];
		/* Remove from database */  
		mq("DELETE FROM `[p]content` WHERE `id`='$getid' and `user`='{$me}'");
	}	
	
	
	/* Add New Page */

	if (isset($_POST["submit"])) {
	
		// Convert that UNIX Timestamp into a string (GMT), safe for MySql
		$newreviseddate = $today;

		/* Basics */
		$newlive = $_POST["live"];
		$newnav = my($_POST["navtitle"]);
		$newmetadescription = my($_POST["metaDescription"]);
		$newmetakeywords = my($_POST["metaKeywords"]);
		  
		/* SEO */
		$newfilename = strtolower($newnav);
		$removethem = array(" ","/",".",",",":",";","?","!","@","#","$","%","^","&","*","(",")","{","}","|","'");
		foreach ($removethem as $symbol) {
			$newsymbol = "-";
			$newfilename = str_replace("$symbol", "$newsymbol", $newfilename);
		}
		$newheading = $newnav;
		$newmetatitle = $newnav;
		  
		/* Nesting */
		if ($_POST["root"] != "") {
			$newroot = $_POST["root"];
		} else {
			$newroot = $rootid;
		}
		$newmenu = $_POST["menu"];
		$newbody = my($_POST["body"]);
		$newplugin = $plugin_id;
		$user = $me;
		
		$tables = "user|type|reviseddate|live|filename|navtitle|body|pageHeading|metaTitle|metaDescription|metaKeywords|root|plugin|category";
		$values = "$user|page|$newreviseddate|$newlive|$newfilename|$newnav|$newbody|$newheading|$newmetatitle|$newmetadescription|$newmetakeywords|$newroot|$newplugin|1";

		if ($_GET["edit"] != "") {
			update("[p]content",$tables,$values,"id",$_GET["id"]);
		} else {
			insert("[p]content","publisheddate|".$tables,$today."|".$values);
			$newpageid = mysql_insert_id();
		}
		
		$status = "<div id='notify'>Success!</div>";
		$action = "?fuse=admin.ecommerce.pages&type={$e_type}";
	
	} else {
	
		if ($_GET["edit"] == "true") { 
			$edityo = mf(mq("select `id`,`navtitle`,`live`,`body`,`root`,`metaKeywords`,`metaDescription` from `[p]content` where `id`='{$_GET["id"]}' limit 1"));
			$e_root = $edityo["root"];
			$e_navtitle = nohtml($edityo["navtitle"]);
			$e_body = stripslashes($edityo["body"]);
			$e_live = nohtml($edityo["live"]);
			$e_description = nohtml($edityo["metaDescription"]);
			$e_keywords = nohtml($edityo["metaKeywords"]);
			if ($e_live == "1") { $liveChecked = " checked"; } else { $draftChecked = " checked"; }
			
			echo '
				<script type="text/javascript">
					$(document).ready(function(){
						$(".newpage").show();
					});
				</script>			
			';
			$action = "?fuse=admin.ecommerce.pages&type={$e_type}&edit=true&id={$_GET["id"]}";
		} else {
			$action = "?fuse=admin.ecommerce.pages&type={$e_type}";
		}
		
	}

		$allfourroot = mq("SELECT `id`,`navtitle`,`root`,`menu` FROM `[p]content` WHERE `type`='page' AND `root`='{$rootid}' ORDER BY `order` ASC");
		$countfourroot = num($allfourroot);
		if ($countfourroot > 0) {
			while ($fourrootrow = mf($allfourroot)) {
				$fournavid = $fourrootrow["id"];
				if ($e_root == $fournavid) { $usefourselect = " selected"; } else { $usefourselect = ""; }
			
				$fournavtitle = nohtml($fourrootrow["navtitle"]);
				$option .= "<option value=\"{$fournavid}\"{$usefourselect}>{$fournavtitle}</option>\n";
			}
		}

	
		$form = '
					<form method="post">
					
						<label>Name</label>
						<input type="text" name="navtitle" value="'.$e_navtitle.'" />
						<div class="clear"></div>
						
						<label>Status</label>
						<div class="group">
						<input type="radio" class="radio" name="live" id="live" value="0"'.$draftChecked.' /> Draft<br />
						<input type="radio" class="radio" name="live" id="live" value="1"'.$liveChecked.' /> Live
						</div>
						<div class="clear"></div>
						
						<label>Root</label>
						<select name="root" id="root">
						<option value=""></option>
						'.$option.'
						</select>
						<div class="clear"></div>
						
						<label>Description</label>
						<textarea name="body" class="smalltextarea">'.$e_body.'</textarea>
						<div class="clear"></div>
						
						<label>Keywords:</label>
						<input type="text" name="metaKeywords" value="'.$e_keywords.'" id="metaKeywords" class="input" />
					    <div class="clear"></div>
					
						<label>Meta Description: (SEO)</label>
						<input type="text" name="metaDescription" value="'.$e_description.'" id="metaDescription" class="input" />
					    <div class="clear"></div>					
						
						<div>
							<input type="submit" class="submit" name="submit" value="Save" />
						</div>
						
					</form>';




/* Load all products */
$customvideo = mq("select * from `[p]content` where `root`='{$rootid}' and `type`='page' and `user`='{$me}' ORDER BY `order` ASC");

  
	
//// Include Template Design ///////////////////////

?>             
<script type="text/javascript">
	$(document).ready(function(){

		$(".addnewpage").click(function(){
			$(".newpage").slideToggle(600);
		});

		$("#response").hide();
		$(function() {
		$("ul.list").sortable({ opacity: 0.8, cursor: 'move', update: function() {
				
				var order = $(this).sortable("serialize") + '&update=update'; 
				$.post("http://chez.hi-fimedia.com/system/ajax.php", order, function(theResponse){
					$("#response").html(theResponse);
					$("#response").slideDown('slow');
					slideout();
				}); 															 
			}								  
			});
		});

	});

</script>
 		<div class="grid_8">
			<div class="box-header"><?=ucfirst($e_type);?> Management</div>
			
				<div class="box">

				<div class="button addnewpage">
					Add <?=$usetitle;?>
				</div>
				<?=$status;?>
				<div class="newpage">
					<?=$form;?>
				</div>
				

				<div class='dark one'><strong>Name</strong></div>
				<div class='dark two'><strong>Views</strong></div>
				<div class='dark three'><strong>Tools</strong></div>
				<div style='clear: both;'></div>
				
				<ul class='list'>

				<?

					while ($row = mf($customvideo)) {

						$cvideo_id = $row["id"];
						$cvideo_title = $row["navtitle"];
						$cvideo_filename = $row["filename"];
						$cvideo_active = $row["live"];
						$cvideo_views = $row["views"];
						$cvideo_maintain = $row["maintain"];

						if ($cvideo_active == "0") {
						$active = " <a href='?fuse=admin.ecommerce.pages&type={$e_type}&activate={$cvideo_id}' style='background: #444444;'>Activate</a>";
						} else {
						$active = " <a href='?fuse=admin.ecommerce.pages&type={$e_type}&deactivate={$cvideo_id}'>Deactivate</a>";
						}
					  
						echo '<li id="arrayorder_'.$cvideo_id.'" class="order">'."\n";
						echo "<div class='one'><span class='orderTitle'><strong>{$cvideo_title}</strong></span></div>"."\n";
						echo "<div class='two'><small>{$cvideo_views}</small></div>"."\n";
						echo "<div class='three buttons'><a href='?fuse=admin.ecommerce.pages&edit=true&type={$e_type}&id={$cvideo_id}'>Edit</a> <a href=\"#\" class=\"xdelete\" onclick=\"confirmDelete('?fuse=admin.ecommerce.pages&type={$e_type}&delete=true&id={$cvideo_id}')\">Delete</a>{$active}</div>"."\n";
						echo "<div style='clear: both;'></div>"."\n";
						
						/* This is for the drop down menu */
						$subroot = mq("select * from `[p]content` WHERE `root`='{$cvideo_id}' and `type`='page' and `user`='{$me}' order by `order` ASC");

						/* Check to see if array returns any value */
						if (num($subroot) > 0) {
							
							/* Array returns value, now we beging the dropdown navigation */
							echo "<ul class='list'>"."\n";
							while ($subrootrow = mf($subroot)) {

								$subnavid = $subrootrow["id"];
								$subnavtitle = $subrootrow["navtitle"];
								$subnavfilename = $subrootrow["filename"];
								$subnavactive = $subrootrow["live"];
								$subnavviews = $subrootrow["views"];
								
								if ($subnavactive == "0") {
									$subactive = " <a href='?fuse=admin.ecommerce.pages&type={$e_type}&activate={$subnavid}' style='background: #444444;'>Activate</a>";
								} else {
									$subactive = " <a href='?fuse=admin.ecommerce.pages&type={$e_type}&deactivate={$subnavid}'>Deactivate</a>";
								}
								
								echo '<li id="arrayorder_'.$subnavid.'" class="order">'."\n";
								echo "<div class='one'> &nbsp;&nbsp;&nbsp;$subnavtitle</div>"."\n";
								echo "<div class='two'><small>{$subnavviews}</small></div>"."\n";
								echo "<div class='three buttons'>&nbsp;&nbsp;&nbsp;&nbsp;<a href='?fuse=admin.ecommerce.pages&edit=true&type={$e_type}&id=$subnavid'>Edit</a> <a href=\"#\" class=\"xdelete\" onclick=\"confirmDelete('?fuse=admin.ecommerce.pages&type={$e_type}&delete=true&id={$subnavid}')\">Delete</a>{$subactive}</div>"."\n";
								echo "<div style='clear: both;'></div>"."\n";
								
								$thirdroot = mq("select * from `[p]content` WHERE `root`='{$subnavid}' and `type`='page' and `user`='{$me}' order by `order` ASC");

								/* Check to see if array returns any value */
								if (num($thirdroot) > 0) {
									
									/* Array returns value, now we beging the dropdown navigation */
									echo "<ul class='list'>";
									while ($thirdrootrow = mf($thirdroot)) {

										$thirdnavid = $thirdrootrow["id"];
										$thirdnavtitle = $thirdrootrow["navtitle"];
										$thirdnavfilename = $thirdrootrow["filename"];
										$thirdnavactive = $thirdrootrow["live"];
										$thirdnavviews = $thirdrootrow["views"];
										
										if ($thirdnavactive == "0") {
											$thirdactive = " <a href='?fuse=admin.ecommerce.pages&type={$e_type}&activate={$thirdnavid}' style='background: #444444;'>Activate</a>";
										} else {
											$thirdactive = " <a href='?fuse=admin.ecommerce.pages&type={$e_type}&deactivate={$thirdnavid}'>Deactivate</a>";
										}
										
										echo '<li id="arrayorder_'.$thirdnavid.'" class="order">'."\n";
										echo "<div class='one'> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $thirdnavtitle</div>"."\n";
										echo "<div class='two'><small>{$thirdnavviews}</small></div>"."\n";
										echo "<div class='three buttons'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='?fuse=admin.ecommerce.pages&edit=true&type={$e_type}&id=$thirdnavid'>Edit</a> <a href=\"#\" class=\"xdelete\" onclick=\"confirmDelete('?fuse=admin.ecommerce.pages&type={$e_type}&delete=true&id={$thirdnavid}')\">Delete</a>{$thirdactive}</div>"."\n";
										echo "<div style='clear: both;'></div>"."\n";

									}
									echo "</ul>"."\n";
								}
							}
							echo "</ul>"."\n";
						}
						echo "</li>"."\n";
					}
				?>
			    
			</div>
			
		</div>