<?

	/* Meta Refresh */
	function refresh($delay,$url) {
		echo '<meta http-equiv="refresh" content="'.$delay.';url='.$url.'">';
	}

	function me() {
		return $_SESSION["me"];
	}
	
	/* Is Administrator - returns "true" if is admin */
	function isAdmin() {
		$me = $_SESSION["me"];
		$check = mf(mq("select `id`,`url` from `[p]musicplayer` where `id`='{$me}' limit 1"));
		if ($check["url"] == "admin") {
			return true;
		}
	}
	
	function isLabel() {
		$me = $_SESSION["me"];
		$check = mf(mq("select `type` from `[p]musicplayer` where `id`='{$me}' limit 1"));
		if ($check["type"] == "3") {
			return true;
		}
	}
	
	function isLoggedIn() {
		$me = $_SESSION["me"];
		if ( $me > 0 || $_GET["artist"] == $me) { 
			return "true";
		}
	}
	
	function isFan() {
		$me = $_SESSION["me"];
		$check = mf(mq("select `type` from `[p]musicplayer` where `id`='{$me}' limit 1"));
		if ($check["type"] == "1") {
			return "true";
		}
	}	
	
	function username() {
		$me = $_SESSION["me"];
		$check = mf(mq("select `id`,`artist` from `[p]musicplayer` where `id`='{$me}' limit 1"));
		if ($check["artist"] != "") {
			return $check["artist"];
		} else if ($me != "" && $check["artist"] == "") {
			return "Admin";
		}
	}
	
	/* Player Views */
	function getViews($id) {
		$load = mf(mq("select `views` from `[p]musicplayer_content` where `id`='{$id}'"));
		$newviews = $load["views"];
		return $newviews;
	}
	/* Player Name */
	function getName($id) {
		$load = mf(mq("select `name` from `[p]musicplayer_content` where `id`='{$id}'"));
		$newviews = stripslashes($load["name"]);
		return $newviews;
	}
	
	/* Meta Refresh */
	function cleanup($value) {
		$value = str_replace(" ", "-", stripslashes($value));
		$value = str_replace("'", "", $value);
		$value = str_replace('"', "", $value);
		return $value;
	}

	/* Player Views */
	function playerViews($id) {
		$load = mf(mq("select `views` from `[p]musicplayer` where `id`='{$id}'"));
		$newviews = $load["views"] + 1;
		update("[p]musicplayer","views",$newviews,"id",$id);
		return $newviews;
	}
	
	/* Track Views */
	function trackViews($id) {
		$load = mf(mq("select `views` from `[p]musicplayer_audio` where `id`='{$id}'"));
		$newviews = $load["views"] + 1;
		update("[p]musicplayer_audio","views",$newviews,"id",$id);
		return $newviews;
	}
	
	/* Track Views */
	function trackDownloads($id) {
		$load = mf(mq("select `download` from `[p]musicplayer_audio` where `id`='{$id}'"));
		$newviews = $load["download"] + 1;
		update("[p]musicplayer_audio","download",$newviews,"id",$id);
		return $newviews;
	}
	
	/* Page Views */
	function pageViews($id) {
		$load = mf(mq("select `views` from `[p]musicplayer_content` where `id`='{$id}'"));
		$newviews = $load["views"] + 1;
		update("[p]musicplayer_content","views",$newviews,"id",$id);
		return $newviews;
	}
	
	/* Current Theme */
	function theme() {
		return $GLOBALS["theme"];
	}

	/* Name of Website */
	function siteName() {
		return $GLOBALS["siteName"];
	}
	
	/* Domain Name */
	function domainName() {
		return $GLOBALS["domainName"];
	}
	
	/* True Site Url */
	function trueSiteUrl() {
		return $GLOBALS["trueSiteUrl"];
	}
    
    function fan_site_url()
    {
        return trueSiteUrl() . "/fan";
    }
    
    function cart_base_url()
    {
        return $GLOBALS["cart_base_url"];
    }
    function fan_base_url()
    {
        return $GLOBALS["cart_base_url"];
    }
	
	/* Site Url */
	function siteUrl() {
		return $GLOBALS["siteUrl"];
	}
	
	/* Site Url */
	function playerUrl() {
		return $GLOBALS["playerUrl"];
	}
	
	/* Site Title */
	function siteTitle() {
		return $GLOBALS["siteTitle"];
	}
	
	/* mysql_query function */
	function mq($mycontent) {
		$pre = $GLOBALS["prefix"];
		return mysql_query(str_replace("[p]", "{$pre}", $mycontent)); 
	}

	/* mysql_fetch_aray function */
	function mf($mycontent) {
		return mysql_fetch_array($mycontent,MYSQL_ASSOC);
	}

	/* mysql_num_rows function */
	function num($mycontent) {
		return mysql_num_rows($mycontent); 
	}
		
	/* mysql_real_escape_string function */
	function my($mycontent) {
		return mysql_real_escape_string(str_replace("|", "&#124;", $mycontent));
	}	
	
	/* Display Formatted Date */
	function simpledate($date) {
		if ($date != "") {
			$chunkit = explode("-", $date);
			$year = $chunkit[0];
			$month = $chunkit[1];
			$day = $chunkit[2];
			$hour = $chunkit[3];
			$min = $chunkit[4];
			$smalldate = date("F j, Y", mktime($hour, $min, 0, $month, $day, $year)); // March 10, 2001, 5:16 pm
			return $smalldate;
		} else {
			return "";
		}
	}
	
	/* Display Formatted Date */
	function fulldate($date) {
		if ($date != "") {
			$chunkit = explode(" ", $date);
			$datechunk = explode("-", $chunkit[0]);
			$timechunk = explode(":", $chunkit[1]);
			$year = $datechunk[0];
			$month = $datechunk[1];
			$day = $datechunk[2];
			$hour = $timechunk[0];
			$min = $timechunk[1];
			$sec = $timechunk[2];
			$first = date("M j, Y h", mktime($hour, $min, 0, $month, $day, $year));
			$second = ":".$min." ";
			$third = date("a", mktime($hour, $min, 0, $month, $day, $year));
			return $first.$second.$third;
		} else {
			return ""; 
		}
	}	
	
	/* Update database */
	function update($database,$tables,$values,$locationtable,$locationvalue) {
		$pre = $GLOBALS["prefix"];
		/* Get Database */
		$intodatabase = str_replace("[p]", $pre, $database);
		
		/* Break Table into Chunks */
		$tablechunks = explode("|", $tables);
		/* Count the Tables */
		$totaltables = count($tablechunks);
		
		/* Break Values into Chunks */
		$valuechunks = explode("|", $values);
		/* Count the Values */
		$totalvalues = count($tablechunks);
		
		/* Check to see that the number of tables equals the number of values */
		if ($totaltables == $totalvalues) {
			$setcomma = $totaltables - 1;
			$i=0;
			while ($i < $totaltables) {
				/* Set the Table Name */
				$table = $tablechunks[$i];
				/* Set the Value of the Table */
				$value = $valuechunks[$i];
				
				/* If this is the last entry, remove the comma */
				if ($i == $setcomma) {
					$comma = " ";
				} else {
					$comma = ", ";
				}
				
				$updatestring .= "`{$table}`='{$value}'{$comma}";
				
				++$i;
			}

			return mysql_query("UPDATE `{$intodatabase}` SET {$updatestring}WHERE `{$locationtable}`='{$locationvalue}'");
		}
	}
	
	/* Insert into Database */
	function insert($database,$tables,$values) {
		$pre = $GLOBALS["prefix"];
		/* Get Database */
		$intodatabase = str_replace("[p]", $pre, $database);
		
		/* Break Table into Chunks */
		$tablechunks = explode("|", $tables);
		/* Count the Tables */
		$totaltables = count($tablechunks);
		
		/* Break Values into Chunks */
		$valuechunks = explode("|", $values);
		/* Count the Values */
		$totalvalues = count($tablechunks);
		
		/* Check to see that the number of tables equals the number of values */
		if ($totaltables == $totalvalues) {
			$setcomma = $totaltables - 1;
			$i=0;
			while ($i < $totaltables) {
				/* Set the Table Name */
				$table = $tablechunks[$i];
				/* Set the Value of the Table */
				$value = $valuechunks[$i];
				
				/* If this is the last entry, remove the comma */
				if ($i == $setcomma) {
					$comma = "";
				} else {
					$comma = ",";
				}
				
				$updatetables .= "`{$table}`{$comma}";
				$updatevalues .= "'{$value}'{$comma}";
				
				++$i;
			}
			return mysql_query("insert into `{$intodatabase}` ({$updatetables}) values ({$updatevalues})");
		}
	}
    
    function mysql_insert($table,$inserts,$debug=FALSE)
    {
        $keys_sql = "";
        $values_sql = "";
        foreach( $inserts  as $key => $value)
        {
            if( strlen($values_sql) > 0 )
            {
                $keys_sql .= ",";
                $values_sql .= ",";
            }
            $keys_sql .= "`$key`";
            
            if( $value === NULL )
            {
                $values_sql .= "NULL";
            }
            else
            {
                $values_sql .= "'" . mysql_real_escape_string($value) . "'";
            }
        }
        $sql = "INSERT INTO `$table` ($keys_sql) VALUES ($values_sql)";
        $ret = mysql_query($sql);

        if( $debug )
        {
            print "mysql_insert sql: $sql\n";
            if( !$ret )
            {
                print "mysql_error: ";
                print mysql_error();
                print "\n";
            }
        }
        return $ret;
    }
    function mysql_update($table,$inserts,$insert_key,$insert_val,$debug=FALSE)
    {
        $set_sql = "";
        foreach( $inserts as $key => $val )
        {
            if( strlen($set_sql) > 0 )
            {
                $set_sql .= ",";
            }
            if( $val === NULL )
            {
                $val = "NULL";
            }
            else
            {
                $val = "'" . mysql_real_escape_string($val) . "'";
            }
            $set_sql .= "`$key` = $val";
        }
        $sql = "UPDATE `$table` SET $set_sql WHERE `$insert_key` = '$insert_val'";
        $ret = mysql_query($sql);
        
        if( $debug )
        {
            print "mysql_update sql: $sql\n";
            if( !$ret )
            {
                print "mysql_error: ";
                print mysql_error();
                print "\n";
            }
        }
        return $ret;
    }
    function mysql_now()
    {
        return date('Y-m-d H:i:s');
    }

	/* No HTML AT ALL! */
	function nohtml($nohtml) {
		$nohtml = str_replace("&#124;", "|", $nohtml);
		return nl2br(stripslashes(strip_tags($nohtml)));
	}

	/* Basic HTML elements only */
	function somehtml($nohtml) {
		$nohtml = str_replace("&#124;", "|", $nohtml);
		return stripslashes(strip_tags($nohtml, '<p><a><font><ul><li><h1><h2><h3><h4><h5><h6><hr><b><u><i><strong><em><table><tr><td><tbody><tfoot><span><div><br><br /><blockquote><img><embed><object><form><input>'));
	}	

	// Include the contents of the file as a string
	function get_include_contents($filename) {
		if (is_file($filename)) {
			ob_start();
			include $filename;
			$contents = ob_get_contents();
			ob_end_clean();
			return $contents;
		}
		return false;
	}
	
	// header function
	function myHeader() {
		$useheader = $GLOBALS["header"];
		return $useheader;
	}
	
	// footer function
	function myFooter() {
		$usefooter = $GLOBALS["footer"]."\n\n".$GLOBALS["siteInsignia"];
		return $usefooter;
	}
	
	/* Get the users IP address */
	function VisitorIP() { 
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$TheIp=$_SERVER['HTTP_X_FORWARDED_FOR'];
		} else { $TheIp=$_SERVER['REMOTE_ADDR']; }		
		return trim($TheIp);
    }
	
	function getAddress() {
		/*** check for https ***/
		$protocol = $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
		/*** return the full address ***/
		return $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}
	
	// Send Email Function //
	function amail($to,$cc,$bcc,$from,$subject,$message) {
		
		// success, email the form
		$sendto = $to;
		$sendsubject = $subject;
		// Always set content-type when sending HTML email
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
		// More headers
		$headers .= 'From: '.$from. "\r\n";
		$headers .= 'Cc: '.$cc . "\r\n";
		$headers .= 'Bcc: '.$bcc . "\r\n";

		$sendmessage = "
						{$email_header}					
						$message
						{$email_footer}
					";
	
		mail($sendto,$sendsubject,$sendmessage,$headers);
		
	}

	// Send Email with Attachment //
	function mail_attachment($filename, $path, $mailto, $from_mail, $from_name, $replyto, $subject, $message, $cc, $bcc) {
		$file = $path.$filename;
		$file_size = filesize($file);
		$handle = fopen($file, "r");
		$content = fread($handle, $file_size);
		fclose($handle);
		$content = chunk_split(base64_encode($content));
		$uid = md5(uniqid(time()));
		$name = basename($file);
		$header = "From: ".$from_name." <".$from_mail.">\r\n";
		$header .= "Reply-To: ".$replyto."\r\n";
		$header .= 'Cc: '.$cc . "\r\n";
		$header .= 'Bcc: '.$bcc . "\r\n";
		$header .= "MIME-Version: 1.0\r\n";
		$header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
		$header .= "This is a multi-part message in MIME format.\r\n";
		$header .= "--".$uid."\r\n";
		$header .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
		$header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
		$header .= $message."\r\n\r\n";		
		$header .= "--".$uid."\r\n";
		$header .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n"; // use diff. tyoes here
		$header .= "Content-Transfer-Encoding: base64\r\n";
		$header .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
		$header .= $content."\r\n\r\n";
		$header .= "--".$uid."--";
		
		mail($mailto, $subject, "", $header);
	}

	// Validate Email Function //
	function validEmail($email) {
	   $isValid = true;
	   $atIndex = strrpos($email, "@");
		if (is_bool($atIndex) && !$atIndex) {
		  $isValid = false;   
		} else {
		  $domain = substr($email, $atIndex+1);
		  $local = substr($email, 0, $atIndex);
		  $localLen = strlen($local);
		  $domainLen = strlen($domain);
		  if ($localLen < 1 || $localLen > 64)
		  {
			 // local part length exceeded
			 $isValid = false;
		  }
		  else if ($domainLen < 1 || $domainLen > 255)
		  {
			 // domain part length exceeded
			 $isValid = false;
		  }
		  else if ($local[0] == '.' || $local[$localLen-1] == '.')
		  {
			 // local part starts or ends with '.'
			 $isValid = false;
		  }
		  else if (preg_match('/\\.\\./', $local))
		  {
			 // local part has two consecutive dots
			 $isValid = false;
		  }
		  else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
		  {
			 // character not valid in domain part
			 $isValid = false;
		  }
		  else if (preg_match('/\\.\\./', $domain))
		  {
			 // domain part has two consecutive dots
			 $isValid = false;
		  }
		  else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
					 str_replace("\\\\","",$local)))
		  {
			 // character not valid in local part unless 
			 // local part is quoted
			 if (!preg_match('/^"(\\\\"|[^"])+"$/',
				 str_replace("\\\\","",$local)))
			 {
				$isValid = false;
			 }
		  }
		  if ($isValid && !(checkdnsrr($domain,"MX"))) {
			 // domain not found in DNS
			 $isValid = false;
		  }
		}
	   return $isValid;
	}	
	
	// Create and Validate Form Function //
	function aform($setup,$subject,$to,$cc,$bcc,$from) {
		
		if (isset($_POST["Submit"])) {
			
			// Compile all POST info
			foreach ($_POST as $key => $value) {
				$$key = $value;								
				if ($key != "require" && $key != "Submit" && $key != "verify" && $key != "birthday") {
					$message .= "<span style='text-transform: capitalize; font-weight: 700;'>".$key."</span> ".$value."<br /><br />";
				}
			}
			
			//If "Verify" is set, then check to see if correct
			if ($birthday != "") {
				if ($birthday != $verify) {
					$error .= "Invalid Verification Code<br />";
				}
			}
			
			// Check for "required" fields
			if ($require != "") {
				$dcheck = explode(",", $require);
				
				while(list($check) = each($dcheck)) {
					if(!$$dcheck[$check]) {
						$error .= "The <b>$dcheck[$check]</b> field is required.<br />";
					}
				}
			}
			
			// Check the Email Address
			$checkEmail = validEmail($_POST["email"]);
			if ($checkEmail != "1") {
				$error .= "Your email is not valid. Please correct.<br />";
			}
			
		}	
	

		/* Let's start by building the form */
		$form .= '
		<form method="post" enctype="multipart/form-data" class="form">';
		$form .= "\n";
								
		// let's loop the PIPES "|" and breakdown the UNDERSCORES "_" then use this data to build the form
					
		$formchunk = explode("|", $setup);
					
		foreach ($formchunk as $key => $value) {
			$entrychunk = explode("_", $value);
			$formalname = $entrychunk[0];
			$name = $entrychunk[1];
			$type = $entrychunk[2];
			
			if ($entrychunk[3] == "required") {
				$required = $entrychunk[3]; // this is an optional field for making it REQUIRED and may not be set
				$values = $entrychunk[4]; // this is an optional field for the VALUE and may not be set (most commonly used for "select" and "radio"
			} else {
				$values = $entrychunk[3]; // this is an optional field for the VALUE and may not be set (most commonly used for "select" and "radio"
				$required = $entrychunk[4]; // this is an optional field for making it REQUIRED and may not be set
			}
						
			$form .= '						<div>
			<label>'.$formalname.'</label>';
			$form .= "\n";
			if ($_POST[$name]) {
				$valueDisplay = $_POST[$name];
			} else {
				$valueDisplay = $values;
			}
			
			switch ($type) {
				case "text":
					$form .= '							<input name="'.$name.'" type="text" class="text" id="'.$name.'" value="'.$valueDisplay.'">';
					$form .= "\n";
					break;
				case "password":
					$form .= '							<input name="'.$name.'" type="password" class="text" id="'.$name.'" value="'.$valueDisplay.'">';
					$form .= "\n";
					break; 
				case "radio":
					$form .= '<div class="radios">';
					$valuechunk = explode("-", $values);
					foreach ($valuechunk as $keey => $subvalue) {
						if ($subvalue == $valueDisplay) { $active = " checked"; }
						$form .= '							<input type="radio" name="'.$name.'" value="'.$subvalue.'" class="text radio"'.$active.'> '.$subvalue.'<br />';
						$form .= "\n";
						$active = "";
					}
					$form .= "</div>";
					break;
				case "checkbox":
					$form .= '<div class="radios">';
					$valuechunk = explode("-", $values);
					foreach ($valuechunk as $keey => $subvalue) {
						if ($subvalue == $valueDisplay) { $active = " checked"; }
						$form .= '							<input type="checkbox" name="'.$name.'" value="'.$subvalue.'" class="text radio"'.$active.'> '.$subvalue.'<br />';
						$form .= "\n";
						$active = "";
					}
					$form .= "</div>";
					break;					
				case "select":
					$form .= '							<select name="'.$name.'" class="text select">';
					$form .= "\n";
					$form .= '								<option value=""></option>';
					$form .= "\n";
					$valuechunk = explode("-", $values);
					foreach ($valuechunk as $keey => $subvalue) {
						if ($subvalue == $valueDisplay) { $active = " selected"; }
						$form .= '								<option value="'.$subvalue.'"'.$active.'>'.$subvalue.'</option>';
						$form .= "\n";
						$active = "";
					}
					$form .= '							</select>';
					$form .= "\n";
					break;
				case "textarea":
					$form .= '							<textarea name="'.$name.'" class="text textarea" id="'.$name.'">'.$valueDisplay.'</textarea>';
					$form .= "\n";
					break;
				case "file":
					$form .= '							<input name="file" class="text" id="'.$name.'" type="file" />';
					$form .= "\n";
					break;	
				case "verify":
					$verification = rand(1111, 9999);
					$form .= '							<input type="hidden" value="'.$verification.'" name="birthday" />
														<span class="verifytext">'.$verification.'</span>
														<input type="text" value="" name="verify" class="text verify" />';
					$form .= "\n";
					break;
			}
					
			$form .= '		 					<div class="clear"></div>';
			$form .= "\n";
			$form .= '						</div>';
			$form .= "\n";
			$form .= "\n";
				
			if ($required != "") {
				$requireding = $name;
				if ($req != "") {
					$req .= ','.$requireding;
				} else {
					$req .= $requireding;
				}											
			}
						
		}
					
		$form .= '
		<div>
			<label><input type="hidden" name="require" value="'.$req.'" />&nbsp;</label>
			<input type="submit" name="Submit" value="Submit" class="submit">
			<div class="clear"></div>
		</div>
		</form>';

		
		if (isset($_POST["Submit"])) {


			if ($_FILES['file']['tmp_name'] != "") {

				/* Attachment */
				$uploadto = $GLOBALS["uploadfolder"];
				$docroot = $GLOBALS["documentroot"];
				
				// Check Entension
				$extension = pathinfo($_FILES['file']['name']);
				$extension = $extension[extension];
				$allowed_paths = explode(", ", $GLOBALS["allowed_ext"]);						
				for($i = 0; $i < count($allowed_paths); $i++) {
					if ($allowed_paths[$i] == "$extension") {
						$ok = "1";
					}
				}
					
				// Check File Size
				if ($ok == "1") {
					if($_FILES['file']['size'] > $GLOBALS["max_size"]) {
						$error .= "File size is too big!<br />";
					} else {
						$ok = "2";
					}
				}

			}

		
			if($error != "") {
				$fullmessage .= "<p>".$error."</p>";
				$fullmessage .= $form;
				return $fullmessage;
			} else { 
				//success!
				
				if ($_FILES['file']['tmp_name'] == "") {
					/* No Attachment */
					$send_to = $to;
					$send_cc = $cc;
					$send_bcc = $bcc;
					$send_from = $from;
					$send_subject = $subject;
					$message .= "IP Address: ".$_SERVER['REMOTE_ADDR']; 
					amail($send_to,$send_cc,$send_bcc,$send_from,$send_subject,$message);	
					return "<p>".$GLOBALS["success"]."</p>";
				} else {
						
					if ($ok == "2") {
						@move_uploaded_file($_FILES['file']['tmp_name'], $uploadto.$_FILES['file']['name']);
						// how to use
						$my_file = $_FILES['file']['name'];
						$my_path = $doc.$uploadto;
						$my_name = $from;
						$my_mail = $from;
						$send_cc = $cc;
						$send_bcc = $bcc;
						$my_replyto = $from;
						$my_to = $to;
						$my_subject = $subject;
						$message .= " IP Address: ".$_SERVER['REMOTE_ADDR'];
						mail_attachment($my_file, $my_path, $my_to, $my_mail, $my_name, $my_replyto, $my_subject, $message, $cc, $bcc);							
						return "<p>".$GLOBALS["success"]."</p>";
					}

				}
				
			}
		
		} else {
			return $form;
		}
	}
	
	/* Main Navigation */
	function main_menu() {
		/* Start Nav Bar */
		$menu .= '<ul id="navbar">';
		
			$useOwnersID = $GLOBALS["useOwnersID"];
			
			/* Grab the root url */
			$fullid = $GLOBALS["loadfilename"];
			$fullurl = explode("/", $fullid);
			/* Everything before the slash */
			$rootname = $fullurl[0];
			
			/* Load Navigation Items */
			$allroot = mq("select `id`,`navtitle`,`root`,`menu`,`custom`,`filename`,`live`,`order` from [p]content WHERE menu=1 and root=0 and live=1 and user={$useOwnersID} order by `order` ASC");

			/* Loop Navigation Pages */
			while ($rootrow = mf($allroot)) {

				$navid = $rootrow["id"];
				$navnav = $rootrow["navtitle"];
				$navroot = $rootrow["root"];
				$navmenu = $rootrow["menu"];
				$navcustom = $rootrow["custom"];
				$navfilename = $rootrow["filename"];
				
				if ($GLOBALS["getid"] == $navfilename) {
						$mainActive = ' current';
				}
				
				/* Check for a custom URL */
				if ($navcustom != "") {
					$usethisurl = $navcustom;
				} else {
					// $usethisurl = $navurl; // I can only use this with htacces
					$usethisurl = $GLOBALS["siteUrl"]."/".$navfilename;
				}
				
				

				/* Display the results */
				$menu .= "\n					<li class=\"rollover{$mainActive}\"><a href=\"{$usethisurl}\" class=\"{$navfilename}\">{$navnav}</a></li>";
				$mainActive = "";

			}
			
		/* End Navigation Loop */
		$menu .= "\n\n				</ul>\n";
		$menu .= "<div style='clear: both;'></div>";
		
		return $menu;
	
	}
	
	// Pagination // Part 1
	function paginationLimit($perpage,$thisPage) {
		// Total entries per page
		$pagination = $perpage;
		
		// set absolute value of 0
		$zero = abs("0");

		// Get the current page
		$page = abs($thisPage);
		if ($page != "") {
			$startat = $page * $pagination;
		} else {
			$startat = 0;
		}
	
		return "{$startat}, {$pagination}";
	}
	
	// Pagination // Part 2
	function paginationLinks($perpage,$thisPage,$database,$searchThrough,$orderBy,$direction) {
	
		$useUrl = "{$GLOBALS["fullSiteUrl"]}?page=";
		
		// Total entries per page
		$pagination = $perpage;
		
		// set absolute value of 0
		$zero = abs("0");

		// Get the current page
		$page = abs($thisPage);
		if ($page != "") {
			$startat = $page * $pagination;
		} else {
			$startat = 0;
		}
		
		if ($page == "0") {
			$current_page = "1";
		} else {
			$current_page = $page;
		}

		// Initial count/setup
		$setupsql = "select * from `{$database}`{$searchThrough}";

		$load = mq($setupsql);
		$count = num($load);
		$addpages = $count / $pagination;
		$totalpages = round(floor($addpages));
		
		// Display the FIRST button
		if ($current_page != "1") {
			$next_current = $page;
			$nextpage = $next_current + 1;	
			$first = '<a href="'.$useUrl.'1">&lsaquo; First</a>';
		}
		// Display the PREVIOUS button
		if ($page > $zero && $current_page != "1") {
			$pre_current = $page;
			$previouspage = $pre_current - 1;
			$previous = '&nbsp;<a href="'.$useUrl.$previouspage.'">&lt;</a>&nbsp;';
		}
		// Display list of pages
		$c=1;
		$start = $current_page - 2;
		$end = 1;
		while ($c <= $totalpages) {
			if ($c >= $start && $end <= "5") {
				if ($c != $totalpages) { $space = "&nbsp;"; } else { $space = ""; }
				if ($c == $page) { $currentpage = ' class="active"'; } else { $currentpage = ""; }
				$listpages .= '&nbsp;<a href="'.$useUrl.$c.'"'.$currentpage.'>'.$c.'</a>'.$space;
				++$end;
			}
			++$c;
		}
		// TOTAL PAGES
		if ($c != "1") {
			$total_pages = $c - 1;
			$s = "s";
		} else {
			$total_pages = $c;
			$s = "";
		}
		// Display the LAST button
		if ($current_page != $total_pages) {
			$next_current = $page;
			$nextpage = $next_current + 1;	
			$last = "&nbsp;<a href='{$useUrl}{$totalpages}'> Last &rsaquo;</a>";
		}
		// Display the NEXT button
		if (($page < $totalpages) && ($totalpages > 0) && ($current_page != $total_pages)) {
			$next_current = $page;
			$nextpage = $next_current + 1;	
			$next = '&nbsp;<a href="'.$useUrl.$nextpage.'">&gt;</a>';
		}
		
		// Page '.$current_page.' of '.$total_pages.' pages '.$pagination_links.'
		return "<p>Page {$current_page} of {$total_pages} page{$s} &nbsp;&nbsp;{$first} {$previous} {$listpages} {$next} {$last}</p>";

	}
	


// Cart Functions

	function cartUrl() {
		$set = mf(mq("select `id`,`cart` from `[p]musicplayer_ecommerce` where `id`='1' limit 1"));
		return siteUrl()."/".filename($set["cart"]);
	}
	
	function money($money) {		
		$money = number_format($money, 2, '.', '');		
		return $money;	
	}	
	
	function productImage($id) {
		$row = mf(mq("select `id`,`image` from `[p]musicplayer_ecommerce_products` where `id`='{$id}' limit 1"));
		return $row["image"];
	}
	
	function productPrice($id) {
		$row = mf(mq("select `id`,`price` from `[p]musicplayer_ecommerce_products` where `id`='{$id}' limit 1"));
		return str_replace("$", "", $row["price"]);
	}
	
	function productName($id) {
		$row = mf(mq("select `id`,`name` from `[p]musicplayer_ecommerce_products` where `id`='{$id}' limit 1"));
		return nohtml($row["name"]);
	}
	
	function productUrl($id) {
	
		$product = mf(mq("select `id`,`page`,`origin`,`subcat`,`filename` from `[p]musicplayer_ecommerce_products` where `id`='{$id}' limit 1"));
		
		if ($product["page"] != "") { $getPage = "/".filename($product["page"]); }
		if ($product["origin"] != "") { $getOrigin = "/".filename($product["origin"]); }
		if ($product["subcat"] != "") { 
			$explode = explode(",", $product["subcat"]);
			$getSubcat = "/".filename($explode[1]);			
		}
		if ($product["filename"] != "") { $getFilename = "/".$product["filename"]; }
		
		return siteUrl().$getPage.$getOrigin.$getSubcat.$getFilename;
	
	}	

	function authorize() {
		$set = mf(mq("select `id`,`authorize` from `[p]musicplayer_ecommerce` where `id`='1' limit 1"));
		return $set["authorize"];
	}
	
	function paypalEmail() {
		$set = mf(mq("select `id`,`paypal` from `[p]musicplayer_ecommerce` where `id`='1' limit 1"));
		return $set["paypal"];
	}

	function customerService() {
		$set = mf(mq("select `id`,`customer_service` from `[p]musicplayer_ecommerce` where `id`='1' limit 1"));
		return $set["customer_service"];
	}
	
	function taxRate() {
		$set = mf(mq("select `id`,`salestax` from `[p]musicplayer_ecommerce` where `id`='1' limit 1"));
		return $set["salestax"];
	}
	
	function shippingRate() {
		$set = mf(mq("select `id`,`shipping_rate` from `[p]musicplayer_ecommerce` where `id`='1' limit 1"));
		return $set["shipping_rate"];
	}	
	
	function shippingDiscount() {
		$set = mf(mq("select `id`,`shipping_discount` from `[p]musicplayer_ecommerce` where `id`='1' limit 1"));
		return $set["shipping_discount"];
	}
    
    function make_short_link($abbrev)
    {
        return "http://madna.co/" . $abbrev;
    }
	
    function append_tags(&$tags,$s)
    {
        if( strlen(trim($s)) == 0 )
            return;
        if( strlen($tags) > 0 )
            $tags .= ",";
        $tags .= $s;
    }

    function get_image_data($image_path)
    {
        $data = getimagesize($image_path);
        if( count($data) > 3 )
        {
            $width = $data[0];
            $height = $data[1];
            if( $width > 0 && $height > 0 )
            {
                $image_data = array("width" => $width,
                                    "height" => $height);
                
                $json = json_encode($image_data);
                return $json;
            }
        }
        return NULL;
    }

    function cleanup_row_element(&$value,$key) 
    {
        $value = stripslashes($value);
    }
    
    function get_artist_data($id)
    {
        $row = mf(mq("SELECT * FROM mydna_musicplayer WHERE id='$id'"));
        
        $extra_json = $row['extra_json'];
        
        if( $row['oauth_token'] && $row['oauth_secret'] && $row['twitter'] )
            $twitter = 'true';
        else
            $row['twitter'] = FALSE;
        if( $row['fb_access_token'] && $row['facebook'] )
            $facebook = 'true';
        else
            $row['facebook'] = FALSE;
        
        $logo = $row['logo'];
        $logo_path = artist_file_url($logo);
        if( $row['logo'] )
            $row['logo_url'] = $logo_path;
        else
            $row['logo_url'] = '/manage/images/NoPhoto.jpg';
        
        $url = $row['url'];
        $row['player_url'] = str_replace("http://www.","http://$url.",trueSiteUrl());
        
        array_walk($row,cleanup_row_element);
        
        $extra = json_decode($extra_json,TRUE);
        if( $extra )
        {
            $row = array_merge($row,$extra);
        }
        return $row;
    }
    
    function artist_get_total_views($artist_id)
    {
        $music_sum = mf(mq("SELECT SUM(views) AS sum_views FROM mydna_musicplayer_audio WHERE `artistid`='$artist_id'"));
        $video_sum = mf(mq("SELECT SUM(views) AS sum_views FROM mydna_musicplayer_video WHERE `artistid`='$artist_id'"));
        $photo_sum = mf(mq("SELECT SUM(views) AS sum_views FROM photos WHERE `artist_id`='$artist_id'"));
        
        $sql = "SELECT SUM( playlist_items.views ) AS sum_views ";
        $sql .= " FROM  `playlists` ";
        $sql .= " JOIN playlist_items ON playlist_items.playlist_id = playlists.playlist_id ";
        $sql .= " WHERE artist_id = '$artist_id' ";
        $playlist_item_sum = mf(mq($sql));
        
        $total = intval($music_sum['sum_views']);
        $total += intval($video_sum['sum_views']);
        $total += intval($photo_sum['sum_views']);
        $totla += intval($playlist_item_sum['sum_views']);
        
        return $total;
    }
    function store_get_cart($artist_id,$cart_id)
    {
        $artist_cart_id = "$artist_id:$cart_id";
        if( !$artist_cart_id )
            return array();
        
        $sql = "";
        $sql .= "SELECT cart_items.*,";
        $sql .= "  mydna_musicplayer_ecommerce_products.name,";
        $sql .= "  mydna_musicplayer_ecommerce_products.description,";
        $sql .= "  mydna_musicplayer_ecommerce_products.image, ";
        $sql .= "  mydna_musicplayer_ecommerce_products.price, ";
        $sql .= "  mydna_musicplayer_ecommerce_products.shipping ";
        $sql .= " FROM cart_items";
        $sql .= " JOIN mydna_musicplayer_ecommerce_products ON cart_items.product_id = mydna_musicplayer_ecommerce_products.id";
        $sql .= " WHERE cart_id='$artist_cart_id'";
        $sql .= " ORDER BY `id` ASC";
        $q = mq($sql);
        
        $cart_list = array();
        while($cart = mf($q)) 
        {
            $id = $cart['id'];
            $product_id = $cart['product_id'];
            $price = floatval($cart['price']);
            $name = $cart['name'];
            if(  $cart['image'] )
            {
                $image = artist_file_url($cart['image']);
            }
            else
            {
                $image = static_file_url('/images/default_product_image.jpg');
            }
            
            $shipping = floatval($cart['shipping']);
            $quantity = intval($cart['quantity']);
            
            $item = array("id" => $id,
                          "product_id" => $product_id,
                          "price" => $price,
                          "name" => $name,
                          "description" => $cart['description'],
                          "image" => $image,
                          "shipping" => $shipping,
                          "size" => $cart['size'],
                          "color" => $cart['color'],
                          "quantity" => $quantity,
                          );
            $cart_list[] = $item;
        }
        return $cart_list;
    }
    
    function store_get_order($order_id)
    {
        $sql = "";
        $sql .= "SELECT order_items.*,";
        $sql .= "  mydna_musicplayer_ecommerce_products.name,";
        $sql .= "  mydna_musicplayer_ecommerce_products.description,";
        $sql .= "  mydna_musicplayer_ecommerce_products.image, ";
        $sql .= "  mydna_musicplayer_ecommerce_products.price, ";
        $sql .= "  mydna_musicplayer_ecommerce_products.shipping, ";
        $sql .= "  mydna_musicplayer_ecommerce_products.type ";
        $sql .= " FROM order_items ";
        $sql .= " JOIN mydna_musicplayer_ecommerce_products ON order_items.product_id = mydna_musicplayer_ecommerce_products.id";
        $sql .= " WHERE order_id='$order_id'";
        $sql .= " ORDER BY `id` ASC";
        $q = mq($sql);
        
        $cart_list = array();
        while($cart = mf($q)) 
        {
            $id = $cart['id'];
            $product_id = $cart['product_id'];
            $price = floatval($cart['price']);
            $name = $cart['name'];
            if(  $cart['image'] )
            {
                $image = artist_file_url($cart['image']);
            }
            else
            {
                $image = static_file_url('/images/default_product_image.jpg');
            }
            
            $shipping = floatval($cart['shipping']);
            $quantity = intval($cart['quantity']);
            
            $item = array("id" => $id,
                          "product_id" => $product_id,
                          "price" => $price,
                          "name" => $name,
                          "description" => $cart['description'],
                          "image" => $image,
                          "shipping" => $shipping,
                          "size" => $cart['size'],
                          "color" => $cart['color'],
                          "quantity" => $quantity,
                          "type" => $cart['type'],
                          );
            $cart_list[] = $item;
        }
        return $cart_list;
    }
    
    function get_product_data($product_id)
    {
        $row = mf(mq("SELECT * FROM mydna_musicplayer_ecommerce_products WHERE id='$product_id'"));
        
        array_walk($row,cleanup_row_element);
        if( !empty($row['image']) )
            $row['image_url'] = artist_file_url($row['image']);
        else
            $row['image_url'] = static_file_url("/images/photo_video_01.jpg");
        
        $digital_downloads = array();
        $q = mq("SELECT * FROM product_files WHERE product_id='$product_id' AND is_deleted=0");
        while( $file = mf($q) )
        {
            $digital_downloads[] = $file;
        }
        $row['digital_downloads'] = $digital_downloads;
        
        return $row;
    }

    function random_string($length)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";	

        $size = strlen( $chars );
        for( $i = 0; $i < $length; $i++ ) 
        {
            $str .= $chars[ mt_rand( 0, $size - 1 ) ];
        }

        return $str;
    }
    
    
    function build_scrollbar($style='')
    {
        
        $scrollbar_html = <<<END
        
<div class='scrollbar-handle-container'>
    <div class='scrollbar-handle $style'>
        <div class='inner'>
            <div class='fingers'>
                <div class='finger'></div>
                <div class='finger'></div>
                <div class='finger'></div>
                <div class='finger'></div>
            </div>
        </div>
    </div>
</div>    

END;
        
        return $scrollbar_html;
    }
    
    function artist_file_upload($artist_id,$file,$old_filename)
    {
        $ret['file'] = $old_filename;
    
        if(!empty($file["name"]))
        {
            $src_file = $file["tmp_name"];
			if( is_uploaded_file($src_file) )
            {
                $upload_filename = basename($file["name"]);
                
                $path_parts = pathinfo($upload_filename);
                $extension = strtolower($path_parts['extension']);

                $type = get_file_type($upload_filename);
                
                if( $type == 'IMAGE' )
                {
                    $image_data = get_image_data($src_file);
                    if( $image_data )
                        $ret['image_data'] = $image_data;
                }
                else if( $type == 'AUDIO' )
                {
                    if( $extension != 'mp3' )
                    {
                        $mp3_file = tempnam(sys_get_temp_dir(),"mad_") . ".mp3";
                        @system("/usr/local/bin/ffmpeg -i $src_file -acodec libmp3lame $mp3_file",$retval);
                        if( $retval == 0 )
                        {
                            $src_file = $mp3_file;
                            $extension = 'mp3';
                        }
                        else
                        {
                            $ret['upload_error'] = "Please upload audio files in mp3 format.";
                            return $ret;
                        }
                    }
                }
                else if( $type == 'VIDEO' )
                {
                    if( $extension != 'mp4' )
                    {
                        $args = "-i_qfactor 0.71 -qcomp 0.6 -qmin 10 -qmax 63 -qdiff 4 -trellis 0 -vcodec libx264 -s 640x360 -vb 300k -ab 64k -ar 44100 -threads 4";
                    
                        $mp4_file = tempnam(sys_get_temp_dir(),"mad_") . ".mp4";
                        @system("/usr/local/bin/ffmpeg -i $src_file $args $mp4_file");
                        if( $retval == 0 )
                        {
                            $src_file = $mp4_file;
                            $extension = 'mp4';
                        }
                        else
                        {
                            $ret['upload_error'] = "Please upload video files in MP4 or MOV format.";
                            return $ret;
                        }
                    }
                }

                $hash = hash_file("md5",$src_file);
                $save_filename = "{$artist_id}_$hash.$extension";
                
                if( PATH_TO_ROOT )
                    $dst_file = PATH_TO_ROOT . "artists/files/$save_filename";
                else
                    $dst_file = "../../artists/files/$save_filename";
                
				@move_uploaded_file($src_file, $dst_file);
                
                post_convert($dst_file);

                $values = array("artist_id" => $artist_id,
                                "filename" => $save_filename,
                                "upload_filename" => $upload_filename,
                                "type" => $type);
                
                mysql_insert("artist_files",$values);
                $ret['id'] = mysql_insert_id();
                $ret['file'] = $save_filename;
			}
		}
        return $ret;
    }
    function get_file_type($file)
    {
        $path_parts = pathinfo($file);
        $extension = strtolower($path_parts['extension']);
        switch( $extension )
        {
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
                return 'IMAGE';
            case 'mp4':
            case 'mov':
            case 'ogv':
            case 'm4v':
                return 'VIDEO';
            case 'wav':
            case 'ogg':
            case 'm4a':
            case 'mp3':
                return 'AUDIO';
        }
        return 'MISC';
    }

    function post_convert($file)
    {
        $path_parts = pathinfo($file);
        $extension = strtolower($path_parts['extension']);
        
        if( $extension == 'mp3')
        {
            $ogg_file = str_replace(".$extension",".ogg",$file);
            @system("/usr/local/bin/ffmpeg -i $file -acodec libvorbis $ogg_file");
        }
        else if( $extension == 'mp4')
        {
            $ogv_file = str_replace(".$extension",".ogv",$file);
            @system("/usr/local/bin/ffmpeg2theora --videoquality 8 --audioquality 6 -o $ogv_file $file");
        }
    }
    
    function get_fan_email()
    {
        $fan_email = FALSE;
        if( isset($_SESSION['fan_id']) )
        {
            $fan_id = $_SESSION['fan_id'];
            $fan = mf(mq("SELECT * FROM fans WHERE id='$fan_id'"));
            $fan_email = $fan['email'];
        }
        return $fan_email;
    }

    function ends_with($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }
        
        return (substr($haystack, -$length) === $needle);
    }

    function get_artist_url_for_page()
    {
        $artist_url = FALSE;
        $http_host = $_SERVER["HTTP_HOST"];
        if( "http://$http_host" == trueSiteUrl()
           || $http_host == staging_host() )
        {
            if( isset($_GET["url"]) )
            {
                $artist_url = $_GET["url"];
            }
        }
        else if( "http://www.$http_host" == trueSiteUrl() )
        {
            if( isset($_GET["url"]) )
            {
                $artist_url = $_GET["url"];
            }
            else
            {
                header("Location: " . trueSiteUrl());
                die();
            }
        }
        else if( ends_with($http_host,staging_host()) )
        {
            $host_parts = explode('.',$http_host);
            $leading_parts = array_slice($host_parts,0,-3);
            $leading = implode('.',$leading_parts);
            
            $artist_url = $leading;
        }
        else
        {
            $host_parts = explode('.',$http_host);
            $trailing_parts = array_slice($host_parts,-2);
            $trailing = implode('.',$trailing_parts);
            $leading_parts = array_slice($host_parts,0,-2);
            $leading = implode('.',$leading_parts);
            if( "http://www." . $trailing == trueSiteUrl() )
            {
                $artist_url = $leading;
            }
            else
            {
                $row = mf(mq("SELECT url FROM mydna_musicplayer WHERE custom_domain = '$http_host'"));
                if( $row )
                {
                    $artist_url = $row['url'];
                }
                if( !$artist_url )
                {
                    $row = mf(mq("SELECT url FROM mydna_musicplayer WHERE custom_domain LIKE '%$trailing'"));
                    if( $row )
                    {
                        $artist_url = $row['url'];
                    }
                }
            }
        }
        
        return $artist_url;
    }

    function get_artist_id_for_page()
    {
        $artist_host = FALSE;
        $http_host = $_SERVER["HTTP_HOST"];
        if( ends_with($http_host,staging_host()) )
        {
            $host_parts = explode('.',$http_host);
            $leading_parts = array_slice($host_parts,0,-3);
            $leading = implode('.',$leading_parts);
            
            $artist_host = $leading;
        }
        else
        {
            $host_parts = explode('.',$http_host);
            $trailing_parts = array_slice($host_parts,-2);
            $trailing = implode('.',$trailing_parts);
            $leading_parts = array_slice($host_parts,0,-2);
            $leading = implode('.',$leading_parts);
            if( "http://www." . $trailing == trueSiteUrl() )
            {
                $artist_host = $leading;
            }
            else
            {
                $row = mf(mq("SELECT id FROM mydna_musicplayer WHERE custom_domain = '$http_host'"));
                if( $row )
                {
                    return $row['id'];
                }
                if( !$artist_url )
                {
                    $row = mf(mq("SELECT id FROM mydna_musicplayer WHERE custom_domain LIKE '%$trailing'"));
                    if( $row )
                    {
                        return $row['id'];
                    }
                }
            }
        }
        
        if( $artist_host != FALSE )
        {
            $row = mf(mq("SELECT id FROM mydna_musicplayer WHERE url = '$artist_host'"));
            if( $row )
            {
                return $row['id'];
            }
        }
        
        return FALSE;
    }

    function add_free_product_to_fan($product_id)
    {
        $product = get_product_data($product_id);
        if( !$product )
        {
            return FALSE;
        }
        
        $price = floatval($product['price']);
        if( $price != 0.0 )
        {
            return FALSE;
        }
        
        $fan_id = $_SESSION['fan_id'];
        if( !$fan_id )
        {
            return FALSE;
        }
        
        $digital_downloads = $product['digital_downloads'];
        for( $j = 0 ; $j < count($digital_downloads) ; ++$j )
        {
            $download = $digital_downloads[$j];
            $product_file_id = $download['id'];
            $inserts = array("fan_id" => $fan_id,
                             "product_file_id" => $product_file_id,
                             );
            mysql_insert('fan_files',$inserts);
        }
        return TRUE;
    }

    function check_unsupported_browser()
    {
        $uas = $_SERVER['HTTP_USER_AGENT'];
        
        $matches = FALSE;
        
        if( strpos($_SERVER['HTTP_USER_AGENT'],"chromeframe") !== FALSE )
        {
            return;
        }
        
        if( preg_match('/MSIE ([0-9]*)/',$uas,$matches) === 1 )
        {
            $ie_major = intval($matches[1]);
            
            if( $ie_major < 9 )
            {
                include_once "unsupported_browser.php";
                die();
            }
        }
    }

    function get_audio_length($file)
    {
        $cmd = "/usr/local/bin/ffprobe $file 2>&1";
        $lines = array();
        
        exec($cmd,&$lines);
        
        $output = implode("\n",$lines);
        
        $matches = array();
        $ret = preg_match("/Duration: ([^::]*):([^:]*):([^,]*),/",$output,&$matches);
        
        if( $ret === 1 )
        {
            if( count($matches) > 3 )
            {
                $hours = floatval($matches[1]);
                $minutes = floatval($matches[2]);
                $seconds = floatval($matches[3]);
                
                $seconds += $hours * 60 * 60 + $minutes * 60;
                
                return $seconds;
            }
        }
        return 0;
    }

    function artist_file_base_url()
    {
        return $GLOBALS['g_artist_file_base_url'];
    }
    function artist_file_url($file)
    {
        return $GLOBALS['g_artist_file_base_url'] . '/artists/files/' . $file;
    }
    function get_image_thumbnail($image,$extra,$width,$height = 0)
    {
        $alts = $extra['alts'];
        
        $key = "w$width";
        if( $height > 0 )
        {
            $key .= "_h$height";
        }
        
        if( isset($alts[$key]) )
        {
            return artist_file_base_url() . $alts[$key];
        }
        return $image;
    }

    function get_s3_client()
    {
        require_once "aws.phar";
        
        $args = array(
                      'key' => $GLOBALS['g_access_key_id'],
                      'secret' => $GLOBALS['g_secret_access_key'],
                      );
        
        $client = Aws\S3\S3Client::factory($args);
        return $client;
    }
    function get_cf_client()
    {
        require_once "aws.phar";
        
        $args = array(
                      'key' => $GLOBALS['g_access_key_id'],
                      'secret' => $GLOBALS['g_secret_access_key'],
                      );
        
        $client = Aws\CloudFront\CloudFrontClient::factory($args);
        return $client;
    }
    function get_r53_client()
    {
        require_once "aws.phar";
        
        $args = array(
                      'key' => $GLOBALS['g_access_key_id'],
                      'secret' => $GLOBALS['g_secret_access_key'],
                      );
        
        $client = Aws\Route53\Route53Client::factory($args);
        return $client;
    }

    function upload_file_to_s3($key,$source_file)
    {
        $client = get_s3_client();
    
        $args = array(
                      'Bucket' => $GLOBALS['g_aws_static_bucket'],
                      'Key' => $key,
                      'SourceFile' => realpath($source_file),
                      'ACL' => 'public-read',
                      'CacheControl' => 'public, max-age=22896000'
                      );
        $client->putObject($args);
    }

    function image_maybe_convert_and_upload_file($client,$src_image,$prefix,$width,$height,&$extra)
    {
        global $ALT_IMAGE_REV_KEY;
        
        $src_imagex = imagesx($src_image);
        $src_imagey = imagesy($src_image);
        
        $dst_imagex = $width;
        if( $height )
        {
            $dst_imagey = $height;
        }
        else
        {
            $dst_imagey = round($src_imagey / $src_imagex * $dst_imagex);
        }
        
        $alt_key = "w{$width}";
        if( $height )
        {
            $alt_key .= "_h{$height}";
        }
        
        $file_path = "/artists/thumbs/{$prefix}_{$alt_key}_{$ALT_IMAGE_REV_KEY}.jpg";
        
        try
        {
            $ret = $client->headObject(array(
                                             'Bucket' => $GLOBALS['g_aws_static_bucket'],
                                             'Key' => $file_path,
                                             ));
            $extra['alts'][$alt_key] = $file_path;
            
            return;
        }
        catch( Exception $e )
        {
        }
        
        $dst_imagex = $width;
        if( $height )
        {
            $dst_imagey = $height;
        }
        else
        {
            $dst_imagey = round($src_imagey / $src_imagex * $dst_imagex);
        }
        
        $dst_image = imagecreatetruecolor($dst_imagex, $dst_imagey);
        
        imagecopyresampled($dst_image, $src_image, 0, 0, 0, 0, $dst_imagex,
                           $dst_imagey, $src_imagex, $src_imagey);
        
        
        ob_start();
        imagejpeg($dst_image,NULL,100);
        $img_data = ob_get_clean();
        
        imagedestroy($dst_image);
        
        $args = array(
                      'Bucket' => $GLOBALS['g_aws_static_bucket'],
                      'Key' => $file_path,
                      'Body' => $img_data,
                      'ACL' => 'public-read',
                      'CacheControl' => 'public, max-age=22896000',
                      'ContentType' => 'image/jpeg',
                      );
        $client->putObject($args);
        
        $extra['alts'][$alt_key] = $file_path;
    }

    function needed_image_sizes($width)
    {
        $needed_widths = array(320,480,640,768,800,960,1024,
                               1080,1280,1440,1536,1600,2048,
                               400,500,600,700,900,1000,1100,1200,1300,
                               1400,1500
                               );
        
        $ret = array();
        
        foreach( $needed_widths as $w )
        {
            if( $w < $width )
                $ret[] = array($w,FALSE);
        }
        
        $ret[] = array(200,FALSE);
        $ret[] = array(65,44);
        $ret[] = array(210,132);
        
        return $ret;
    }

    function process_upload_image($file)
    {
        $client = get_s3_client();
    
        $id = $file['id'];
        $filename = $file['filename'];
        $extra_json = $file['extra_json'];
        if( $extra_json && strlen($extra_json) > 0 )
        {
            $extra = json_decode($extra_json,TRUE);
        }
        else
        {
            $extra = array();
        }
        
        $url = artist_file_url($filename);
        
        $path_parts = pathinfo($filename);
        $extension = $path_parts['extension'];
        $prefix = str_replace(".$extension","",$filename);
        
        $src_data = file_get_contents($url);
        $src_image = imagecreatefromstring($src_data);
        
        $width = imagesx($src_image);
        $height = imagesy($src_image);
        
        if( !isset($extra['image_data']) )
        {
            $image_data = array("width" => $width,
                                "height" => $height,
                                );
            $extra['image_data'] = $image_data;
        }
        
        $needed_sizes = needed_image_sizes($width);
        
        foreach( $needed_sizes as $i => $size )
        {
            image_maybe_convert_and_upload_file($client,$src_image,$prefix,$size[0],$size[1],$extra);
        }
        
        $extra_json = json_encode($extra);
        mysql_update('artist_files',array("extra_json" => $extra_json),'id',$id);
    }

    function download_url_to_file($url,$dst_filename)
    {
        $src_file = fopen($url, "rb");
        $dst_file = fopen($dst_filename,"wb");
        
        if( $src_file && $dst_file )
        {
            while( !feof($src_file) )
            {
                fwrite($dst_file, fread($src_file, 1024 * 1024 ), 1024 * 1024 );
            }
        }
        
        if( $src_file )
        {
            fclose($src_file);
        }
        
        if( $dst_file )
        {
            fclose($dst_file);
        }
    }

    function static_file_base_url()
    {
        return $GLOBALS['g_static_base_url'];
    }
    function static_file_url($path)
    {
        $file_map = $GLOBALS['g_static_file_map'];
        if( isset($file_map[$path]) )
        {
            return $GLOBALS['g_static_base_url'] . $file_map[$path];
        }
        else
        {
            return $GLOBALS['g_static_base_url'] . $path;
        }
    }

    function api_base_url()
    {
        return $GLOBALS['g_api_base_url'];
    }

    function staging_host()
    {
        return $GLOBALS['g_staging_host'];
    }
    function root_redirect_ip()
    {
        return $GLOBALS['g_root_redirect_ip'];
    }
    function admin_publish_key()
    {
        return '297bhksdy8gr2bjkad792jbkr297';
    }

    function get_ga_code()
    {
        $ga_account = $GLOBALS['g_ga_account'];
        if( !ga_account )
        {
            $ga_account = "UA-15194524-1";
        }
    
        $ret = "";
        $ret .= "var _gaq = _gaq || [];\n";
        $ret .= "_gaq.push(['_setAccount', '$ga_account']);\n";
        $ret .= "_gaq.push(['_trackPageview']);\n";
        $ret .= "(function() {\n";
        $ret .= "var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;\n";
        $ret .= "ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';\n";
        $ret .= "var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);\n";
        $ret .= "})();\n";
        return $ret;
    }

    function url_get_content_length($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'HEAD');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        
        $data = curl_exec($ch);
        curl_close($ch);
        
        $content_length = 0;
        
        if( preg_match('/Content-Length: (\d+)/', $data, $matches) )
        {
            $content_length = (int)$matches[1];
        }
        return $content_length;
    }

    function make_comments_for_list($base_url,$type,$list)
    {
        $ret_html = "";
        foreach( $list as $index => $item )
        {
            $id = $item['id'];
            $url = "$base_url/#{$type}_id=$id";
            $id_tag = "{$type}_id_$id";
        
            $html = "";
            $html .= "<div id='$id_tag' class='fb_container'>";
            $html .= " <fb:comments href='$url' num_posts='10' width='470' colorscheme='dark'></fb:comments>";
            $html .= "</div>";
            
            $ret_html .= $html;
        }
        
        return $ret_html;
    }
?>