<?php include('../includes/config.php');
require_once("include/fgcontactform.php");
require_once("include/formvalidator.php");

$formproc = new FGContactForm();

//Initialize the contact form
$formproc->AddRecipient('vibratingdesigns@gmail.com'); //<<---Put your email address here
$formproc->SetFormRandomKey('CnRrspl1FyEylUj');

$validation_errors='';
if(isset($_POST['submitted']))
{// We need to validate only after the form is submitted

    //Setup Server side Validations
    //Please note that the element name is case sensitive 
    $validator = new FormValidator();
    $validator->addValidation("name","req","This is a required field.");
    $validator->addValidation("userPwd","req","This is a required field.");
    
    //Then validate the form
    if($validator->ValidateForm())
    {
		$query_findDetails = mysql_query("SELECT id,name,username,email FROM myartist_users WHERE username='".$_REQUEST['name']."' AND password='".md5($_REQUEST['userPwd'])."'");
		$find_records = mysql_fetch_array($query_findDetails);
		$no_records = mysql_num_rows($query_findDetails);
		if($no_records=='1')
		{
			session_start();
			$_SESSION['sess_userId'] =	$find_records['id'];		
			$_SESSION['sess_userName'] = $find_records['name'];
			$_SESSION['sess_userUsername'] = $find_records['username'];
			$_SESSION['sess_userEmail'] = $find_records['email'];
			$_SESSION['sess_userType'] = $find_records['usertype'];
			$_SESSION['sess_userURL'] = $find_records['username'];
			
			echo "<script type='text/javascript'>window.location='dashboard.php';  </script>'";			
			exit(); 
		}
		else
		{
			$validation_errors .="<p>Invalid Username or Password.</p>\n";		
			
		}
    }
    else
    {
        //Validations failed. Display Errors.
        $error_hash = $validator->GetErrors();
        foreach($error_hash as $inpname => $inp_err)
        { 
		   
		   $validation_errors .= "<p>$inpname : $inp_err</p>\n";
        }        
    }
}//if
$disp_name  = isset($_POST['name'])?$_POST['name']:'';
$disp_userPwd = isset($_POST['userPwd'])?$_POST['userPwd']:'';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head>
      <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
      <title>MYARTISTDNA -- Admin Login</title>
      <link rel="STYLESHEET" type="text/css" href="css/contact.css" />
	  <link rel="STYLESHEET" type="text/css" href="css/styles.css" />
      <script type='text/javascript' src='js/gen_validatorv31.js'></script>
</head>
<body>
<div align="center">
<!-- Form Code Start -->
<form id='contactus' action='<?php echo $formproc->GetSelfScript(); ?>' method='post' accept-charset='UTF-8'>
<fieldset>
<legend>Log in to Admin Panel</legend>

<input type='hidden' name='submitted' id='submitted' value='1'/>
<input type='hidden' name='<?php echo $formproc->GetFormIDInputName(); ?>' value='<?php echo $formproc->GetFormIDInputValue(); ?>'/>
<input type='text'  class='spmhidip' name='<?php echo $formproc->GetSpamTrapInputName(); ?>' />

<div class='short_explanation'>* required fields</div>

<div>
<span class='error'><?php echo $formproc->GetErrorMessage(); ?></span>
<span class='error'><?php echo $validation_errors; ?></span>
</div>
<div class='container' style="float:left;">
    <label for='name' >User Name*: </label> <br />
	<input type='text' name='name' id='name' value='<?php echo htmlentities($disp_name) ?>' maxlength="50" /><br />
	<span id='contactus_name_errorloc' class='error'></span>    
</div>

<div class='container' style="float:right;">   
	<label for='userPwd' >Password*:</label> <br /> 
	<input type='password' name='userPwd' id='userPwd' value='<?php echo htmlentities($disp_userPwd) ?>' maxlength="50" /><br/>
    <span id='contactus_userPwd_errorloc' class='error'></span>
</div>

<div class='container'>
    <input type='submit' name='Submit' value='Submit' class="submit"/>
</div>

</fieldset>
</form>
</div>
<!-- client-side Form Validations:
Uses the form validation script from JavaScript-coder.com
See: http://www.javascript-coder.com/html-form/javascript-form-validation.phtml
-->
<script type='text/javascript'>
// <![CDATA[

    var frmvalidator  = new Validator("contactus");
    frmvalidator.EnableOnPageErrorDisplay();
    frmvalidator.EnableMsgsTogether();
    frmvalidator.addValidation("name","req","This is a required field.");
    frmvalidator.addValidation("userPwd","req","This is a required field.");     
// ]]>
</script>
<!--
Form Code End
Visit html-form-guide.com for more info.
-->

</body>
</html>
