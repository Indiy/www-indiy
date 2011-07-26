<script> 
	  
	$(document).ready(function(){
		$(".addnewpage").click(function(){
			$(".newpage").slideToggle(600);
		});
	});
	
</script>

<?
	include("functions.php");
	$database = "[p]discount";
	
	if ($_GET["edit"] == "true") { 

		$useID = $_GET["id"];
		
		$editrow = mf(mq("select * from `$database` where `id`='$useID'"));
		$e_code 		 = $editrow["code"];
		if ($editrow["percent"] != "") {
		$e_percent		 = $editrow["percent"]."%";
		}
		if ($editrow["amount"] != "") {
		$e_amount		 = "$".$editrow["amount"];
		}
		$e_max		 	 = $editrow["max"];
		$e_expiration	 = $editrow["expiration"];
		$e_active		 = $editrow["active"];
		$e_storewide	 = $editrow["storewide"];

	}	
	
	if (isset($_POST["code"])) {
	
		$p_code 		 = $_POST["code"];
		$p_percent		 = str_replace("%", "", $_POST["percent"]);
		$p_amount		 = str_replace("$", "", $_POST["amount"]);
		if ($_POST["max"] != "") {
		$p_max			 = $_POST["max"];
		} else {
		$p_max			 = "unlimited";
		}
		$p_expiration	 = $_POST["expiration"];
		$p_active		 = $_POST["active"];
		$p_storewide	 = $_POST["storewide"];
	
		$tables 		 = "code|percent|amount|max|expiration|active|storewide";
		$values 		 = "{$p_code}|{$p_percent}|{$p_amount}|{$p_max}|{$p_expiration}|{$p_active}|{$p_storewide}";
		
		if ($_GET["add"] == "true") {
			insert($database,$tables,$values);
		} else {
			update($database,$tables,$values,"id",$_GET["update"]);
		}
	
	}
?>

		<div class="grid_8">
			<div class="box-header">Manage Discount Codes</div>
			<div class="box">

				<? if ($_GET["edit"] != "true") { $addUrl = "&add=true"; ?>
				<div class="button addnewpage">
				Add Discount
				</div>
				<div class="newpage">
				<? } else { 
						$addUrl = "&update=".$_GET["id"];
				   }
				?>
					<form method="post" action="?fuse=admin.discount.manage<?=$addUrl;?>">
						<label>Discount Code</label> 
						<input type="text" name="code" value="<?=$e_code;?>" class="smallinput" />
						<div class="clear"></div>
						
						<label>Percent Off</label> 
						<input type="text" name="percent" value="<?=$e_percent;?>" class="smallinput" /> <small>(00% or leave blank)</small>
						<div class="clear"></div>
						
						<label>Amount Off</label> 
						<input type="text" name="amount" value="<?=$e_amount;?>" class="smallinput" /> <small>($00.00 or leave blank)</small>
						<div class="clear"></div>
						
						<label>Max Uses</label> 
						<input type="text" name="max" value="<?=$e_max;?>" class="smallinput" /> <small>(if no maximum leave blank)</small>
						<div class="clear"></div>
						
						<label>Expiration Date</label> 
						<input type="text" name="expiration" value="<?=$e_expiration;?>" class="smallinput" /> <small>(yyyy-mm-dd or leave blank)</small>
						<div class="clear"></div>
						
						<label>Active</label> 
						<input type="text" name="active" value="<?=$e_active;?>" class="smallinput" /> <small>(1=active 0=NonActive)</small>
						<div class="clear"></div>
						
						<label>Store Wide Sale?</label> 
						<input type="text" name="storewide" value="<?=$e_storewide;?>" class="smallinput" /> <small>(1=yes 0=no)</small>
						<div class="clear"></div>
						
						<input type="submit" name="submit" value="Submit" class="submit" /><div class="clear"></div>
					</form>
				<? if ($_GET["edit"] != "true") { ?>
				</div>
				<? } ?>

				<table width="100%" cellpadding="8" cellspacing="0" style="margin-bottom: 50px;">
				  <tr>
				    <td width="30%" style="background: #bbbbbb;"><strong>Code</strong></td>					
					<td width="10%" style="background: #bbbbbb;" align="center"><strong>Value</strong></td>
					<td width="10%" style="background: #bbbbbb;" align="center"><strong>Uses</strong></td>
					<td width="10%" style="background: #bbbbbb;" align="center"><strong>Expiration</strong></td>
				    <td width="40%" style="background: #bbbbbb;"><strong>Tools</strong></td>
				  </tr>

				<?
				
					/* Activate Item */
					if (isset($_GET["activate"]) && $_GET["activate"] != "") {
						$activate_id = $_GET["activate"];
						mq("UPDATE $database SET `active`='1' WHERE id={$activate_id}");
					}

					/* Dectivate Item */
					if (isset($_GET["deactivate"]) && $_GET["deactivate"] != "") {
						$deactivate_id = $_GET["deactivate"];
						mq("UPDATE $database SET `active`='0' WHERE id={$deactivate_id}");
					}

					/* Delete Item */
					if (isset($_GET["id"]) && $_GET["id"] != "" && $_GET["delete"] == "true") {
						$getid = $_GET["id"];
						/* Remove from database */  
						mq("DELETE FROM `$database` WHERE `id`='$getid'");
					}				

					$checkinthecodes = mq("select * from `$database` order by `id` desc");
					while ($row = mf($checkinthecodes)) {

						$c_id = $row["id"];
						$c_name = stripslashes($row["code"]);
						$c_uses = $row["uses"];
						
						if ($row["percent"] != "") {	
							$c_value = $row["percent"]."%";
						} else {
							$c_value = "$ ".$row["amount"];
						}		

						if ($row["expire"] != "") {
							$c_expire = $row["expire"];
						} else {
							$c_expire = $row["max"];
						}

						if ($row["active"] == "0") {
							$active = " <a href='?fuse=admin.discount.manage&activate={$c_id}' style='background: #444444;'>Activate</a>";
						} else {
							$active = " <a href='?fuse=admin.discount.manage&deactivate={$c_id}'>Deactivate</a>";
						}
					  
						echo "<tr onMouseOver=\"this.style.backgroundColor='#dddddd';\" onMouseOut=\"this.style.backgroundColor='#FFFFFF';\">";
						echo "<td align='left' valign='top' class='root'>";
						echo "$c_name</td>";
						echo "<td align='center' valign='top'>{$c_value}</td>";
						echo "<td align='center' valign='top'>{$c_uses}</td>";
						echo "<td align='center' valign='top'>{$c_expire}</td>";
						echo "<td align='left' valign='top'>";
						echo "<div id='buttons'><a href='?fuse=admin.discount.manage&edit=true&id=$c_id'>Edit</a> <a href=\"#\" class=\"xdelete\" onclick=\"confirmDelete('?fuse=admin.discount.manage&delete=true&id=$c_id')\">Delete</a>{$active}</div>";
						echo "</td>";
						echo "</tr>";
					}
				?>
	
				</table>				 			 
				

			    </p>
			    
			</div>
			
		</div>					    