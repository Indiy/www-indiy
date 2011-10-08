<?
	include("functions.php");
	$database = "[p]ecommerce_cart";
?>

<script> 
    
	$(document).ready(function(){
		$('a.details').click(function() {
			$this("div.viewdetails").slideToggle('slow', function() {});});
	});
	
</script>


		<div class="grid_8">
			<div class="box-header">Manage Orders</div>
			<div class="box">

				

				<table width="100%" cellpadding="8" cellspacing="0" style="margin-bottom: 50px;">
				  <tr>
				    <td width="15%" style="background: #bbbbbb;"><strong>Order #</strong></td>					
					<td width="25%" style="background: #bbbbbb;"><strong>Date</strong></td>
					<td width="25%" style="background: #bbbbbb;"><strong>Name</strong></td>
					<td width="15%" style="background: #bbbbbb;"><strong>Total</strong></td>
				    <td width="20%" style="background: #bbbbbb;"><strong>Tools</strong></td>
				  </tr>

				<?
				
					/* Ship Item */
					if (isset($_GET["ship"]) && $_GET["ship"] == "true") {
						$userID = $_GET["userid"];
						$shippingNumber = $_POST["number"];
						$notifyEmail = $_POST["email"];
						update($database,"status|shipping","2|{$shippingNumber}","userid","$userID");
						// subject of email sent to customer
						$customer_subject = "Shipping Confirmation from {$siteName}";
						$customer_message = '
							<h1>Your order has been shipped</h1>							
							<table width="100%">
								<tr>
									<td align="center" valign="top" width="100">
										<p><strong>Order Number: '.$userID.'</strong></p>
										<div style="background: #eee; border: 1px solid #ddd; padding: 25px; margin: 10px; text-align: center;">
											Your can track the status of your order on-line at ups.com. <br />
											<div style="color: #990000; font-size: 22px; font-weight: bold; text-align: center; padding-top: 8px;">'.$shippingNumber.'</div>
										</div>					
										<div style="clear: both;"><br /><br /></div>
									</td>
								</tr>
							</table>
						';
						$from = customerService();
						amail($notifyEmail,$cc,$from,$from,$customer_subject,$customer_message);						
						
					}

					/* Delete Item */
					if ($_GET["delete"] == "true") {
						$getid = $_GET["id"];
						/* Remove from database */  
						update($database,"status","3","id",$_GET["id"]);
					}
	
					$checkinthecart = mq("select * from `{$database}` where (`status`='1' OR `status`='2') group by `userid` order by `date` desc");
					$inv = "";
					$grandTotal = "0.00";
					while ($row = mf($checkinthecart)) {
					
						if ($inv != $row["userid"]) {

							$i_id = $row["id"];
							$i_invoice = $row["userid"];
							$i_date = $row["date"];
							$i_person = $row["person"];
							$i_code = $row["code"];
							$i_email = $row["email"];
							
							if ($row["status"] == "1") {
								// has not yet shipped
								$status = "#f4fea0";
							} else {
								$status = "#ffffff";
							}
						  
							echo "<tr style='background: {$status};' onMouseOver=\"this.style.backgroundColor='#dddddd';\" onMouseOut=\"this.style.backgroundColor='{$status}';\">";
							echo "<td align='left' valign='top' class='root'>";
							echo "<div class='details'><a href='{$siteUrl}/invoice/{$i_invoice}'>$i_invoice</a>";
							
							// Load receipt
							$myshoppingcart2 = mq("select * from `{$database}` WHERE `userid`='{$i_invoice}' ORDER BY `id` DESC");
							$myshoppingcart_total2 = 0;
							$startingitem2 = 1;
							$shipping = 0;
							while($myshoppingcartrow2 = mf($myshoppingcart2)) {

								$myshoppingcart_id2 = $myshoppingcartrow2['id'];
								$myshoppingcart_itemamount2 = abs($myshoppingcartrow2['price']);
								$myshoppingcart_name2 = $myshoppingcartrow2['name'];
								$myshoppingcart_productid2 = productimage($myshoppingcartrow2['productid']);
								$myshoppingcart_total2 = $myshoppingcart_total2 + $myshoppingcart_itemamount2;
								if ($myshoppingcart_itemamount2 < "100") {
									$myshoppingcart_shipping_load = $myshoppingcart_itemamount2 * 0.15;
									$myshoppingcart_shipping = money(round($myshoppingcart_shipping_load, 2));
									$shipping = money($shipping + $myshoppingcart_shipping);
									$displayshipping = "<br /><small>Shipping: $ {$myshoppingcart_shipping}</small>";
								} else {
									$myshoppingcart_shipping = "";
									$displayshipping = "";
								}
								$row = mf(mq("select * from `[p]products` where `id`='{$myshoppingcartrow2['productid']}'"));
									$product_origin = $row["origin"];
									$product_manufacturerlink = $row["manufacturer"];
									$product_filename = $row["filename"];
									
								$receipt .= "<tr>
												<td align='left' valign='middle' style='padding: 5px; border-top: 1px dashed #eee; width: 60px; text-align: center;'>
													<img src='{$siteUrl}/system/image.php?file={$myshoppingcart_productid2}&width=100' width='50' border='0' />
												</td>
												<td align='left' valign='middle' style='padding: 5px; border-top: 1px dashed #eee;'>
													<a href='$siteUrl/$product_origin/$product_manufacturerlink/$product_filename'>{$myshoppingcart_name2}</a>
												</td>
												<td align='right' valign='middle' style='padding: 5px; border-top: 1px dashed #eee;'>
													$ {$myshoppingcart_itemamount2}
													{$displayshipping}
												</td>
											</tr>";

								$startingitem2++;

							}
							$final_load = $myshoppingcart_total2;
							$final = round($final_load, 2);
							
							if (isset($i_code)) {
								$check = mf(mq("select * from `[p]discount` where `code`='{$i_code}' limit 1"));
								if ($check["percent"] != "") {	
									$dinit = $check["percent"] * 0.01;
									$dv = money(round($final * $dinit, 2));
									$discountType = "percent";
								} else {
									$dv = money($check["amount"]);
									$discountType = "dollars";
								}
								$newSub = money($myshoppingcart_total2 - $dv);
								$final = money($newSub + $shipping);
									
								$discounth = "
									<div style='text-align: right; padding: 5px;'>Discount:</div>
									<div style='text-align: right; padding: 5px;'>Sub Total:</div>
									";
								$discountv = "
									<div style='text-align: left; padding: 5px;'>- $ {$dv}</div>
									<div style='text-align: left; padding: 5px;'>$ {$newSub}</div>
									";
								++$startingitem2;
							} else {
								$final = round($final_load, 2);
							}

							$receipt .= "
									<tr> 
										<td colspan='2' align='right' style='border-top: 1px #ccc solid;'>	
											<div style='text-align: right; padding: 5px;'>Sub Total:</div>
											{$discounth}
											<div style='text-align: right; padding: 5px;'>Shipping:</div>
											<div style='text-align: right; font-size: 18px; padding: 5px;'>Total:</div>
										</td> \n";
							$receipt .= "  <td align='left' style='border-top: 1px #ccc solid;'>
										<div style='text-align: left; padding: 5px;'>$ ".money($myshoppingcart_total2)."</div>
										{$discountv}
										<div style='text-align: left; padding: 5px;'>$ {$shipping}</div>
										<div style='text-align: left; font-size: 18px; padding: 5px;'>$ $final</div>
									</td>\n";
							$receipt .= "</tr>\n";

								echo '<div class="viewdetails shadow">
									<p>View Invoice: <a href="'.$siteUrl.'/invoice/'.$i_invoice.'">'.$siteUrl.'/invoice/'.$i_invoice.'</a></p>
									';
									if ($status == "#f4fea0") { 
									echo '<form method="post" action="?fuse=admin.cart.manage&ship=true&userid='.$i_invoice.'">
									<input type="hidden" name="email" value="'.$i_email.'" />
									<label style="width: 130px; padding-top: 6px;">Shipping Number</label><input type="text" name="number" value="" /> <input type="submit" name="submit" class="submit" value="Send Shipment Notification" style="font-size: 12px; padding: 4px 5px;" />
									<div class="clear"></div>
									<br /><br />
									</form>';
									}
								echo '	<table width="500" cellpadding="0" cellspacing="0" style="padding: 10px 5px;">
											<tr>	    
												<td align="left" valign="top" width="65%" colspan="2" style="padding: 7px; background: #000; color: #fff;"><strong>Item Name</strong></td>
												<td align="right" valign="top" width="35%" style="padding: 7px; background: #000; color: #fff;"><strong>Price</strong></td>
											</tr>
											'.$receipt.'
										</table> 
									</div>';
							
							$i_total = "$ ".$final;
							echo "</div></td>";
							echo "<td align='left' valign='top'><small>{$i_date}</small></td>";
							echo "<td align='left' valign='top'>{$i_person}</td>";
							echo "<td align='left' valign='top'>{$i_total}</td>";
							echo "<td align='left' valign='top'>";
							echo "<div id='buttons'><a class=\"xdelete\" onclick=\"confirmDelete('?fuse=admin.cart.manage&delete=true&id=$i_id')\">Delete</a></div>";
							echo "</td>";
							echo "</tr>";
							
						} else {
						
							$inv = $row["userid"];
						
						}
						
						$inv = $row["userid"];
						$receipt = "";
						$grandTotal = $final + $grandTotal;
					}
				?>
				
	
				</table>				 			 
				
				<h1>GRAND TOTAL: $ <?=money_format('%(#10n', $grandTotal);?></h1>
				
			    </p>
			    
			</div>
			
		</div>					    