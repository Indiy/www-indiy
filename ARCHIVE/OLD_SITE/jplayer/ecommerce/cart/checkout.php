<?

$x_card_num = $_POST["x_card_num"];
$x_card_code = $_POST["x_card_code"];
$ex_month = $_POST["ex_month"];
$ex_year = $_POST["ex_year"];
$x_amount = $_POST["x_amount"];
$x_description = $_POST["x_description"];
$x_email_customer = $_POST["x_email_customer"];
$x_invoice_num = $_POST["x_invoice_num"];
$x_first_name = $_POST["x_first_name"];
$x_last_name = $_POST["x_last_name"];
$x_address = $_POST["x_address"];
$x_city = $_POST["x_city"];
$x_state = $_POST["x_state"];
$x_zip = $_POST["x_zipp"];
$x_phone = $_POST["x_phone"];
$x_email = $_POST["x_email"];
$x_ship_to_first_name = $_POST["x_ship_to_first_name"];
$x_ship_to_last_name = $_POST["x_ship_to_last_name"];
$x_ship_to_address = $_POST["x_ship_to_address"];
$x_ship_to_city = $_POST["x_ship_to_city"];
$x_ship_to_state = $_POST["x_ship_to_state"];
$x_ship_to_zip = $_POST["x_ship_to_zip"];	

?>


<h1>Check Out</h1>
<form name="myform" action="<?=$siteUrl.'/'.$partone.'/complete';?>" method="post">


<div id="productdetails">
	
	<div class="clear"></div>
	
	<div id="left">
		<div class="h3">Billing Address</div>
		<div class="spacer"></div>
		<label><a class="vtip" title="Name as shown on Credit Card">First Name</a></label>
		<INPUT size="30" name="x_first_name" value="<? echo $x_first_name; ?>">
		<label><a class="vtip" title="Name as shown on Credit Card">Last Name</a></label>
		<INPUT size="30" name="x_last_name" value="<? echo $x_last_name; ?>">
		<label>Phone Number</label>
		<INPUT size="30" name="x_phone" value="<? echo $x_phone; ?>">
		<label>Email Address </label>
		<INPUT size="30" name="x_email" value="<? echo $x_email; ?>">
		<label>Address </label>
		<INPUT size="30" name="x_address" value="<? echo $x_address; ?>">
		<label>City </label>
		<INPUT size="30" name="x_city" value="<? echo $x_city; ?>">
		<label>State </label>
		<SELECT name="x_state"> 
			<OPTION value="<? echo $x_state; ?>" selected><? echo $x_state; ?></OPTION> 
			<OPTION value=AL>Alabama</OPTION> 
			<OPTION value=AK>Alaska</OPTION> 
			<OPTION value=AZ>Arizona</OPTION> 
			<OPTION value=AR>Arkansas</OPTION> 
			<OPTION value=CA>California</OPTION> 
			<OPTION value=CO>Colorado</OPTION> 
			<OPTION value=CT>Connecticut</OPTION> 
			<OPTION value=DE>Delaware</OPTION> 
			<OPTION value=DC>District of Columbia</OPTION> 
			<OPTION value=FL>Florida</OPTION> 
			<OPTION value=GA>Georgia</OPTION> 
			<OPTION value=HI>Hawaii</OPTION> 
			<OPTION value=ID>Idaho</OPTION> 
			<OPTION value=IL>Illinois</OPTION> 
			<OPTION value=IN>Indiana</OPTION> 
			<OPTION value=IA>Iowa</OPTION> 
			<OPTION value=KS>Kansas</OPTION> 
			<OPTION value=KY>Kentucky</OPTION> 
			<OPTION value=LA>Louisiana</OPTION> 
			<OPTION value=ME>Maine</OPTION> 
			<OPTION value=MD>Maryland</OPTION> 
			<OPTION value=MA>Massachusetts</OPTION> 
			<OPTION value=MI>Michigan</OPTION> 
			<OPTION value=MN>Minnesota</OPTION> 
			<OPTION value=MS>Mississippi</OPTION> 
			<OPTION value=MO>Missouri</OPTION> 
			<OPTION value=MT>Montana</OPTION> 
			<OPTION value=NE>Nebraska</OPTION> 
			<OPTION value=NV>Nevada</OPTION> 
			<OPTION value=NH>New Hampshire</OPTION> 
			<OPTION value=NJ>New Jersey</OPTION> 
			<OPTION value=NM>New Mexico</OPTION> 
			<OPTION value=NY>New York</OPTION> 
			<OPTION value=NC>North Carolina</OPTION> 
			<OPTION value=ND>North Dakota</OPTION> 
			<OPTION value=OH>Ohio</OPTION> 
			<OPTION value=OK>Oklahoma</OPTION> 
			<OPTION value=ON>Ontario</OPTION> 
			<OPTION value=OR>Oregon</OPTION> 
			<OPTION value=PA>Pennsylvania</OPTION> 
			<OPTION value=PR>Puerto Rico</OPTION> 
			<OPTION value=RI>Rhode Island</OPTION>  
			<OPTION value=SC>South Carolina</OPTION> 
			<OPTION value=SD>South Dakota</OPTION> 
			<OPTION value=TN>Tennessee</OPTION> 
			<OPTION value=TX>Texas</OPTION> 
			<OPTION value=UT>Utah</OPTION> 
			<OPTION value=VT>Vermont</OPTION> 
			<OPTION value=VA>Virginia</OPTION> 
			<OPTION value=WA>Washington</OPTION> 
			<OPTION value=WV>West Virginia</OPTION> 
			<OPTION value=WI>Wisconsin</OPTION> 
			<OPTION value=WY>Wyoming</OPTION> 
			<OPTION value="">-----------------------------</OPTION> 
			</SELECT>

		<label>Zip Code </label>
		<INPUT name="x_zip" value="<? echo $x_zip; ?>">
	</div>
	<div id="right">

		<div class="h3">Shipping Address</div>
		<div class="spacer"></div>
		<INPUT type="radio" class="radio" value="same" name="shipto"> Ship to Above Billing Address<br />
		<INPUT type="radio" class="radio" value="separate" name="shipto"> Ship to a Different Address<br />
		<div class="spacer"><br /></div>


		<label>Recipient First Name </label>
		<INPUT size="30" name="x_ship_to_first_name" value="<? echo $x_ship_to_first_name; ?>">
		<label>Recipient Last Name </label>
		<INPUT size="30" name="x_ship_to_last_name" value="<? echo $x_ship_to_last_name; ?>">
		<label>Address </label>
		<INPUT size="30" name="x_ship_to_address" value="<? echo $x_ship_to_address; ?>">
		<label>City </label>
		<INPUT size="30" name="x_ship_to_city" value="<? echo $x_ship_to_city; ?>">
		<label>State </label>
		<SELECT name="x_ship_to_state"> 
			<OPTION value="<? echo $x_ship_to_state; ?>" selected><? echo $x_ship_to_state; ?></OPTION> 
			<OPTION value=AL>Alabama</OPTION> 
			<OPTION value=AK>Alaska</OPTION> 
			<OPTION value=AZ>Arizona</OPTION> 
			<OPTION value=AR>Arkansas</OPTION> 
			<OPTION value=CA>California</OPTION> 
			<OPTION value=CO>Colorado</OPTION> 
			<OPTION value=CT>Connecticut</OPTION> 
			<OPTION value=DE>Delaware</OPTION> 
			<OPTION value=DC>District of Columbia</OPTION> 
			<OPTION value=FL>Florida</OPTION> 
			<OPTION value=GA>Georgia</OPTION> 
			<OPTION value=HI>Hawaii</OPTION> 
			<OPTION value=ID>Idaho</OPTION> 
			<OPTION value=IL>Illinois</OPTION> 
			<OPTION value=IN>Indiana</OPTION> 
			<OPTION value=IA>Iowa</OPTION> 
			<OPTION value=KS>Kansas</OPTION> 
			<OPTION value=KY>Kentucky</OPTION> 
			<OPTION value=LA>Louisiana</OPTION> 
			<OPTION value=ME>Maine</OPTION> 
			<OPTION value=MD>Maryland</OPTION> 
			<OPTION value=MA>Massachusetts</OPTION> 
			<OPTION value=MI>Michigan</OPTION> 
			<OPTION value=MN>Minnesota</OPTION> 
			<OPTION value=MS>Mississippi</OPTION> 
			<OPTION value=MO>Missouri</OPTION> 
			<OPTION value=MT>Montana</OPTION> 
			<OPTION value=NE>Nebraska</OPTION> 
			<OPTION value=NV>Nevada</OPTION> 
			<OPTION value=NH>New Hampshire</OPTION> 
			<OPTION value=NJ>New Jersey</OPTION> 
			<OPTION value=NM>New Mexico</OPTION> 
			<OPTION value=NY>New York</OPTION> 
			<OPTION value=NC>North Carolina</OPTION> 
			<OPTION value=ND>North Dakota</OPTION> 
			<OPTION value=OH>Ohio</OPTION> 
			<OPTION value=OK>Oklahoma</OPTION> 
			<OPTION value=ON>Ontario</OPTION> 
			<OPTION value=OR>Oregon</OPTION> 
			<OPTION value=PA>Pennsylvania</OPTION> 
			<OPTION value=PR>Puerto Rico</OPTION> 
			<OPTION value=RI>Rhode Island</OPTION>  
			<OPTION value=SC>South Carolina</OPTION> 
			<OPTION value=SD>South Dakota</OPTION> 
			<OPTION value=TN>Tennessee</OPTION> 
			<OPTION value=TX>Texas</OPTION> 
			<OPTION value=UT>Utah</OPTION> 
			<OPTION value=VT>Vermont</OPTION> 
			<OPTION value=VA>Virginia</OPTION> 
			<OPTION value=WA>Washington</OPTION> 
			<OPTION value=WV>West Virginia</OPTION> 
			<OPTION value=WI>Wisconsin</OPTION> 
			<OPTION value=WY>Wyoming</OPTION> 
			<OPTION value="">-----------------------------</OPTION> 
			</SELECT>

		<label>Zip Code </label>
		<INPUT size="10" name="x_ship_to_zip" value="<? echo $x_ship_to_zip; ?>">
		
	</div>
	<div class="clear"></div>
	<div id="left">

		
		<div class="spacer"></div>
		<div class="h3">Payment Options</div>
		<div class="spacer"></div>
		
		<label>Payment Method</label>
			<div style="padding-top: 5px;"><INPUT type="radio" class="radio" value="Visa" name="PaymentMethod">&nbsp;Visa &nbsp;	<INPUT type="radio" class="radio" value="MC" name="PaymentMethod">&nbsp;MasterCard</div>
		
		<div class="spacer"></div>
		
		<label>Card Number </label>
		<INPUT type="text" maxLength="16" name="x_card_num" value="<? echo $x_card_num; ?>" />
		<div class="spacer"></div>
		
		<label><a class="vtip" title="3 or 4 digit on back of cart">CVV Code</a></label>
		<INPUT type="text" maxLength="16" name="x_card_code" value="<? echo $x_card_code; ?>" />

		<div class="spacer"></div>
		<label>Expiration Month</label>
		<SELECT name="ex_month"> 
			<option value="<? echo $ex_month; ?>"><? echo $ex_month; ?></option>
			<OPTION value="01">01 (January)</OPTION> 
			<OPTION value="02">02 (February)</OPTION> 
			<OPTION value="03">03 (March)</OPTION> 
			<OPTION value="04">04 (April)</OPTION> 
			<OPTION value="05">05 (May)</OPTION> 
			<OPTION value="06">06 (June)</OPTION> 
			<OPTION value="07">07 (July)</OPTION> 
			<OPTION value="08">08 (August)</OPTION> 
			<OPTION value="09">09 (September)</OPTION> 
			<OPTION value="10">10 (October)</OPTION> 
			<OPTION value="11">11 (November)</OPTION> 
			<OPTION value="12">12 (December)</OPTION>
		</SELECT> 
		<div class="spacer"></div>
		<label>Expiration Year</label>
		<SELECT name="ex_year"> 
			<option value="<? echo $ex_year; ?>"><? echo $ex_year; ?></option>
			<?
			$year = date("Y");
			$max = 6;
			$count = 0;
			
			while ($count < $max) {
				echo "<OPTION value={$year}>{$year}</OPTION>";
				
				++$year;
				++$count;
			}
			?>
		</SELECT>
		 
		 
		<div class="spacer"></div><div class="spacer"></div>
		<input type="hidden" name="x_email_customer" value="TRUE" />
		<input type="hidden" name="x_invoice_num" value="<? echo $_POST["id"]; ?>" />
		<input type="hidden" name="x_amount" value="<? echo $_POST["x_amount"]; ?>" />
		<input type="hidden" name="x_description" value="<?=$siteName;?> Purchase" />

	</div>
	
	<div id="right">
		<!--
		Create a Password
		<INPUT type="password" size="30" name="register_password" value="">
		Creating a password allows you to login during your next order if you choose, for even faster checkout!
		Retype your Password
		<INPUT type="password" size="30" name="confirm_password" value="">
		-->
	</div>
	
	<div class="clear" style="text-align: center;">
	<br /><br />
		<input type="submit" name="submit" class="submit vtip" value="Pay Now" title="Your card will be charded $<? echo $_POST["x_amount"]; ?>" /> <br /><small>Your card will be charded $<? echo $_POST["x_amount"]; ?>.</small>
	</div>

</div>






