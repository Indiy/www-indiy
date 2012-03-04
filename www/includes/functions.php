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
		return mysql_fetch_array($mycontent); 
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
    
    function mysql_insert($table,$inserts)
    {
        $values = array_map('mysql_real_escape_string', array_values($inserts));
        $keys = array_keys($inserts);
        $q = 'INSERT INTO `'.$table.'` (`'.implode('`,`', $keys).'`) VALUES (\''.implode('\',\'', $values).'\')';
        return mysql_query($q);
    }
    function mysql_update($table,$inserts,$insert_key,$insert_val)
    {
        $values = array_map('mysql_real_escape_string', array_values($inserts));
        $keys = array_keys($inserts);
        $pairs = array();
        foreach( $inserts as $key => $val )
        {
            $val = mysql_real_escape_string($val);
            $pairs[] = "`" . $key . "` = '" . $val . "'"; 
        }
        $q = "UPDATE `$table` SET " . implode(',', $pairs) . " WHERE `$insert_key` = '$insert_val'";
        return mysql_query($q);
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
?>