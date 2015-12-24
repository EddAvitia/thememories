<?php
class User_Register extends MysqlFns
{
	function User_Register()
	{
		global $config;
        $this->MysqlFns();
		$this->Offset			= 0;
		$this->Limit			= 10;
		$this->page				= 0;
		$this->Keyword			= '';
		$this->Operator			= '';
		$this->PerPage			= '';
	}
	function changeDateformut($date) {
if($date!='' && $date!='0000-00-00')
{
$formuttedDate=explode("/",$date);
$formuttedDateRet=$formuttedDate[2]."-".$formuttedDate[0]."-".$formuttedDate[1];
return $formuttedDateRet;
}
}
	
	function user_registration()
	{		
		global $objSmarty,$config;
		$SelQuery="SELECT * from `member` where `email`='".trim(addslashes($_REQUEST['emailregister']))."'";
		$ResCart=$this->ExecuteQuery($SelQuery, "select");
		$NoOfRows=$this->ExecuteQuery($SelQuery, "norows");
		if($NoOfRows > 0)
		{
			   // print_r($_REQUEST);
			    $objSmarty->assign("ErrorMessage","Email address already exists");
				$objSmarty->assign("Useremail",$_REQUEST['emailregister']);
				$objSmarty->assign("Userfirstname",$_REQUEST['fname']);
				$objSmarty->assign("Userlastname",$_REQUEST['lname']);
				
				$objSmarty->assign("Usermonth",$_REQUEST['month']);
				$objSmarty->assign("Userday",$_REQUEST['day']);
				$objSmarty->assign("Useryear",$_REQUEST['year']);
				$objSmarty->assign("Usergender",$_REQUEST['gender']);		
					
		}
		else 
		{
			
		$activationKey =  mt_rand() . mt_rand() . mt_rand() . mt_rand() . mt_rand();	
		/*$prefix = 'I'; // a universal prefix prefix
  		$my_random_id = uniqid($prefix);
		$my_random_id .= chr(rand(65,90));	*/
		//$dat_exp 	= explode('/',trim(addslashes($_REQUEST['dob'])));
		$month=$_REQUEST['month']+1;
		$dob_dat 	=$_REQUEST['year'].'-'.$month.'-'.$_REQUEST['day'];
		//$dateb=$this->changeDateformut($_REQUEST['dob']);
		
		 $InsQuery = "INSERT INTO `member` (`email`,`password`,`firstname`,`lastname`,`dob`,`dbstatus`, `gender`, `address`,`city`, 
		 `state`,`country`, `postal`,`phoneno`,`Activation_Key`,`Facebook_First_Status`,`status`) 
		 VALUES ('".trim(addslashes($_REQUEST['emailregister']))."','".trim(addslashes($_REQUEST['pswdregister']))."',
		 '".trim(addslashes($_REQUEST['fname']))."','".trim(addslashes($_REQUEST['lname']))."','".$dob_dat."',1,
		 '".addslashes($_REQUEST['gender'])."','".trim(addslashes($_REQUEST['address']))."','".trim(addslashes($_REQUEST['city']))."',
		 '".trim(addslashes($_REQUEST['state']))."','".trim(addslashes($_REQUEST['country']))."','".trim(addslashes($_REQUEST['zipcode']))."',
		 '".trim(addslashes($_REQUEST['phone']))."','".addslashes($activationKey)."',2,1)";
		 $res	=$this->ExecuteQuery($InsQuery, "insert");
		 $_SESSION['currentReg_Id'] =	$res;
		 
		 if(isset($_REQUEST['Invite_Id']))
		 {
		 	$sql="INSERT INTO `tbl_friends` set 
			`MemberID`=".$_REQUEST['Invite_Id']." ,
			`FriendID`=".$res." ,
			`Status`=2 ,
			`CreatedDateTime`=NOW()";
			 mysql_query($sql);
			 
			 $Invite_Id="&Invite_Id=".$_REQUEST['Invite_Id'];
		 }
		 
		 
		$to=$_REQUEST['emailregister'];	
		$activation_link=$config['SiteGlobalPath']."verify_account.php?userid=".$res."&activationKey=".$activationKey.$Invite_Id;
		if($res){
			$imgurl=$config['SiteGlobalPath']."/images/logo.png";	
			$subject = "Memories-Registration Confirmation.";			
		   $message='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
			<title>Memories</title>
					<style>
			body{
				font-family:Arial, Helvetica, sans-serif;
				font-size:12px;
				color:#000000;
			}
			.left-header {
			float: left;
			width: 590px;
			}
			.left-header .logo {
			float: left;
			width: 220px;
			margin-top: 5px;
			}
			.logo-txt {
			float: left;
			width: 370px;
			text-align: center;
			margin-top: 55px;
		   }
			</style>
			</head>	
			<body>
			<table width="700" border="3" cellspacing="0" cellpadding="7">
			  <tr>
				<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
				  <tr>
					<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
					<td align="left"  class="normal_txt7"><div class="left-header">
          		 	<div class="logo"><img src="'.$imgurl.'" /></div>           			         
         			</div> </td>
         		 	</tr>
         		 						  
					  <tr>
						<td height="35" align="center" bgcolor="#FFFFFF" class="normal_txt7"><table width="97%"  cellspacing="0" cellpadding="0">
						  <tr>
							<td width="53%" height="8"></td>
							<td width="47%" height="8"></td>
							</tr>
							<tr>
							  <td height="25" colspan="2" align="left" valign="middle" class="title_1" >Hello '.ucwords(stripslashes($_REQUEST['fname'])).' '.ucwords(stripslashes($_REQUEST['lname'])).'  </td>
							</tr>
							<tr>
							<td colspan="2" align="left" valign="middle" class="title_1">&nbsp;</td>
							</tr>
						<tr>
					  <td height="25" colspan="2" align="left" valign="middle" class="title_1" >Your account has been added successfully. Your Login informations are:</td>
			    		</tr>	
								
							<tr>
							<td colspan="2" align="left" valign="middle" class="title_1">Email : '.stripslashes($_REQUEST['emailregister']).'</td>
							</tr>						
							<tr>
							  <td height="25" colspan="2" align="left" valign="middle" class="title_1" >Password  : '.stripslashes($_REQUEST['pswdregister']).'</td>
							</tr>		
							<tr>
							<td colspan="2" align="left" valign="middle" class="title_1">&nbsp;</td>
							</tr>										
							<tr>
							  <td height="25" colspan="2" align="left" valign="middle" class="title_1" ><a href="'.$activation_link.'" style="color:#000000;"><u>Click here to activate your account</u></a></td>
							</tr>					
							<tr>
							<td colspan="2" align="left" valign="middle" class="title_1">&nbsp;</td>
							</tr>							
											
							<tr>
							<td colspan="2" align="left" valign="middle" class="title_1">Thanks,</td>
				  			</tr>		
				 			<tr>
								<td colspan="2" align="left" valign="middle" class="title_1">Memories Team</td>
							</tr>											  
						</table></td>
					  </tr>
					</table></td>
				  </tr>
				</table></td>
			  </tr>
			</table>
			</body>
			</html>';	
			$headers = 'From: '.$config['AdminMail']."\r\n";
			$headers.= 'Reply-To: '.$_REQUEST['emailregister']."\r\n";
			$headers.= "MIME-Version: 1.0\r\n";			
			$headers.= "Content-type: text/html; charset=iso-8859-1\r\n";															
			@mail($to,$subject,$message,$headers); 
			Redirect("register_success.php?register_uid=".$res);
	 		//$objSmarty->assign("SuccessMessage","Your registration has been completed successfully.");
		
		}
	}
	}
	
	
	function sendReconfirmationMail($userid){		
		global $objSmarty,$config;
		$SelQuery_user		= "select * from  member where id=".$userid;
		$Resultset_user		= $this->ExecuteQuery($SelQuery_user, "select");
		$activation_link=$config['SiteGlobalPath']."verify_account.php?userid=".$userid."&activationKey=".$Resultset_user[0]['Activation_Key'];
		if($Resultset_user[0]['ActivationStatus']==0){
		$imgurl=$config['SiteGlobalPath']."/images/logo.png";		
			//$goodbyeimg=$config['SiteGlobalPath']."images/good-bye.png";
		$email=$Resultset_user[0]['email'];
		$subject = 'Memories - Registration Confirmation.';
		$message='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Memories</title>
	body{
		font-family:Arial, Helvetica, sans-serif;
		font-size:12px;
		color:#000000;
	}
	.left-header {
	float: left;
	width: 590px;
	}
	.left-header .logo {
	float: left;
	width: 220px;
	margin-top: 5px;
	}
	.logo-txt {
	float: left;
	width: 370px;
	text-align: center;
	margin-top: 55px;
   }
	</style>
	</head>	
	<body>
	<table width="700" border="3" cellspacing="0" cellpadding="7">
	  <tr>
		<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
			   <tr>
				<td align="left"  class="normal_txt7"><div class="left-header">
           		 <div class="logo"><img src="'.$imgurl.'" /></div>
           		        
          		</div>  </td>
			  </tr>
			  <tr><td> <div style="border-bottom:4px solid #D8D8D8;padding-top:15px;"></div></td></tr>
			  <tr>
			  <tr>
				<td height="35" align="center" bgcolor="#FFFFFF" class="normal_txt7"><table width="97%"  cellspacing="0" cellpadding="0">
				  <tr>
					<td width="53%" height="8"></td>
					<td width="47%" height="8"></td>
					</tr>
					<tr>
					  <td height="25" colspan="2" align="left" valign="middle" class="title_1" >Hello '.ucwords(stripslashes($Resultset_user[0]['firstname'])).' '.ucwords(stripslashes($Resultset_user[0]['lastname'])).',</td>
					</tr>
					<tr>
					  <td height="25" colspan="2" align="left" valign="middle" class="title_1" >Your account has been added successfully. Your Login informations are:</td>
					</tr>
					<tr>
							<td colspan="2" align="left" valign="middle" class="title_1">Email : '.stripslashes($Resultset_user[0]['email']).'</td>
							</tr>						
							<tr>
							  <td height="25" colspan="2" align="left" valign="middle" class="title_1" >Password  : '.stripslashes($Resultset_user[0]['password']).'</td>
							</tr>		
							<tr>
							<td colspan="2" align="left" valign="middle" class="title_1">&nbsp;</td>
							</tr>										
							<tr>
							  <td height="25" colspan="2" align="left" valign="middle" class="title_1" ><a href="'.$activation_link.'" style="color:#000000;"><u>Click here to activate your account</u></a></td>
							</tr>					
							<tr>
							<td colspan="2" align="left" valign="middle" class="title_1">&nbsp;</td>
							</tr>
									
				  <tr>
					<td colspan="2" align="left" valign="middle" class="title_1">Thanks,
 </td>
					</tr>				
				 <tr>
					<td colspan="2" align="left" valign="middle" class="title_1">Memories Team</td>
					</tr>				  
				</table></td>
			  </tr>
			</table></td>
		  </tr>
		</table></td>
	  </tr>
	</table>
	</body>
	</html>'; 
			$headers = 'From: '.$config['AdminMail']."\r\n";
			$headers.= 'Reply-To: '.$config['AdminMail']."\r\n";
			$headers.= "MIME-Version: 1.0\r\n";			
			$headers.= "Content-type: text/html; charset=iso-8859-1\r\n";									
			@mail($email,$subject,$message,$headers);			
			$objSmarty->assign("successmessagereg","Message sent successfully");
			
	}else{
		$objSmarty->assign("Errormessagereg","This account has been already activated!");
	}
}
	
function getprofile($memid){
	global $objSmarty,$config;
	$SelQuery="SELECT *,DATE_FORMAT(dob,'%m/%d/%Y') as dob,DATE_FORMAT(dod,'%m/%d/%Y') as dod from `member` where id=".$memid;
	$ResCart=$this->ExecuteQuery($SelQuery, "select");
	//print_r($ResCart);
	$objSmarty->assign("updatevals",$ResCart);	
	$datetext=$ResCart[0]['dob'];
	$datetext1=explode("/",$datetext);
	$Usermonth=$datetext1[0]-1;
	$Userday=$datetext1[1];
	$Useryear=$datetext1[2];
	
	$datetext2=$ResCart[0]['dod'];
	$datetext3=explode("/",$datetext2);
	
	$Usermonthd=$datetext3[0]-1;
	$Userdayd=$datetext3[1];
	$Useryeard=$datetext3[2];
	$objSmarty->assign("Usermonth",$Usermonth);	
	$objSmarty->assign("Userday",$Userday);	
	$objSmarty->assign("Useryear",$Useryear);	
	$objSmarty->assign("Usermonthd",$Usermonthd);	
	$objSmarty->assign("Userdayd",$Userdayd);	
	$objSmarty->assign("Useryeard",$Useryeard);	
}

	function updateprofile($memid)
	{		
		global $objSmarty,$config;
			
		/*$prefix = 'I'; // a universal prefix prefix
  		$my_random_id = uniqid($prefix);
		$my_random_id .= chr(rand(65,90));	*/
		
		$dat_exp 	= explode('/',trim(addslashes($_REQUEST['dob'])));
		$dob_dat 	= $dat_exp[2].'-'.$dat_exp[0].'-'.$dat_exp[1];
		$month=$_REQUEST['month']+1;
		$dob_birth 	=$_REQUEST['year'].'-'.$month.'-'.$_REQUEST['day'];
		//$dateb=$this->changeDateformut($_REQUEST['dob']);
		$monthd=$_REQUEST['monthd']+1;
		$dob_death 	=$_REQUEST['yeard'].'-'.$monthd.'-'.$_REQUEST['dayd'];
		
		$SelQuery_user		= "select * from  member where id=".$memid;
		$ExecuteQuery=$this->ExecuteQuery($SelQuery_user, "select");
		$user_password=$ExecuteQuery[0]['password'];
		$current_password=$_REQUEST['cpswd'];
		$new_password_c=$_REQUEST['pswdregister'];
		
		if($current_password=='')
		{
			$current_password=$user_password;
			$new_password=$current_password;
		}
		else 
		{
			$current_password=$current_password;
			$new_password=$new_password_c;
		}
		
		if($current_password==$user_password)
		{
			if($_FILES['profilephotoright12']['name']!="")
			{
				$name=time().$_FILES['profilephotoright12']['name'];
				@move_uploaded_file($_FILES['profilephotoright12']['tmp_name'], "profilepic/".$name);
				
				$UpQuery="Update `member` set `ProfilePic`= '".$name."' where ID=".$memid;
				$this->ExecuteQuery($UpQuery, "update");
			}
			
			$InsQuery = "UPDATE `member` 
			 set `firstname`='".trim(addslashes($_REQUEST['fname']))."',
			 `lastname`='".trim(addslashes($_REQUEST['lname']))."',
			 `password`='".$new_password."',
			 `dob`='".$dob_birth."',
			 `dod`='".$dob_death."',
			 `gender`='".addslashes($_REQUEST['gender'])."',
			 `ProfileView`='".addslashes($_REQUEST['sharewith'])."'
			 where ID=".$memid;
			 $update=$this->ExecuteQuery($InsQuery, "update");
			 if($update)
			 {
			 	//$_SESSION['firstname']=$ResCart[0]['firstname'];
			 	unset($_SESSION['firstname']);
			 	$_SESSION['firstname']=trim(addslashes($_REQUEST['fname']));
			 }
			 //$_SESSION['successmessagereg']="Profile has been updated successfully";
			 $objSmarty->assign("successmessagereg","Profile has been updated successfully");
		//	 redirect("editprofile.php?id=".$memid);
		}
		else 
		{
			 $objSmarty->assign("ErrorMessage","Invalid current password");
		}
		// $objSmarty->assign("successmessagereg","Profile updated successfully");
		//	$_SESSION['currentReg_Id'] =	$res;
	}

	
	
	
function is_Activated_User($ObjArray){
	global $objSmarty;
	 $check_key_exist="select * from `member` where Activation_Key='".$ObjArray['activationKey']."' AND id='".$ObjArray['userid']."'";
	
	$SelQuery=$this->ExecuteQuery($check_key_exist, "select");
	$count=$this->ExecuteQuery($check_key_exist, "norows");
	if($count>0){
		if($SelQuery[0]['ActivationStatus']==0){
			$UpQuery		= "Update `member` SET			
		 	`ActivationStatus` = '1'		
		 	where id=".$ObjArray['userid'];
			$updateResult		= $this->ExecuteQuery($UpQuery, "update");
			$objSmarty->assign("successmessage","Your account has been activated successfully");

			$sql="UPDATE `tbl_friends` set `Status`=1, 
			`ModifiedDateTime`=NOW() where `MemberID`=".$ObjArray['Invite_Id']." and `FriendID`=".$ObjArray['userid']."";
			mysql_query($sql);
		}else{
			$objSmarty->assign("Errormessage","Your account has been already activated!");
		}
	}else{
		//Redirect("logout.php");exit;
	}
	
}
	function emailexists($email)
	{
		 $sql="select * from member where email = '".$email."'";
		$exec=$this->ExecuteQuery($sql, "select");
		return $exec;
	}
	function SentPasswordEmail($email,$password,$firstname,$lastname)
	{
		global $objSmarty,$config;
		$date = date("Y-m-d h:i:s");
		
		$toemail=$email;	
		$imgurl=$config['SiteGlobalPath']."images/logo.png";	
		$subject = 'Memories - Forgot Password';
	    $message='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Memories</title>
	<style>
	body{
		font-family:Arial, Helvetica, sans-serif;
		font-size:12px;
		color:#000000;
	}
	.left-header {
	float: left;
	width: 590px;
	}
	.left-header .logo {
	float: left;
	width: 220px;
	margin-top: 5px;
	}
	.logo-txt {
	float: left;
	width: 370px;
	text-align: center;
	margin-top: 55px;
   }
	</style>
	</head>
	
	<body>
	<table width="700" border="3" cellspacing="0" cellpadding="7">
	  <tr>
		<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
			   <tr>
				<td align="left"  class="normal_txt7"><div class="left-header">
          		  <div class="logo"><img src="'.$imgurl.'" /></div>           		  
         		 </div> </td>
			  </tr>
			  <tr><td> <div style="border-bottom:4px solid #D8D8D8;padding-top:15px;"></div></td></tr>
			  <tr>
			  <tr>
				<td height="35" align="center" bgcolor="#FFFFFF" class="normal_txt7"><table width="97%"  cellspacing="0" cellpadding="0">
				  <tr>
					<td width="53%" height="8"></td>
					<td width="47%" height="8"></td>
					</tr>
					<tr>
					  <td height="25" colspan="2" align="left" valign="middle" class="title_1" >Hello '.ucwords(stripslashes($firstname)).' '.ucwords(stripslashes($lastname)).',</td>
					</tr>
					<tr>
					<td height="25" colspan="2" align="left" valign="middle" class="title_1" >Your login informations are
																	</td>
					</tr>
					<tr>
					  <td height="25" colspan="2" align="left" valign="middle" class="title_1" >Email : '.stripslashes($email).'</td>
					</tr><tr>
					  <td height="25" colspan="2" align="left" valign="middle" class="title_1" >Password : '.stripslashes($password).'</td>
					</tr>
					<tr>
					  <td height="25" colspan="2" align="left" valign="middle" class="title_1" >Thanks,</td>
					</tr>
					<tr>
					<td colspan="2" align="left" valign="middle" class="title_1">Memories Team.</td>
					</tr>
					<tr>
					  <td height="25" colspan="2" align="left" valign="middle" class="title_1" >&nbsp;</td>
					</tr>											  
				</table></td>
			  </tr>
			</table></td>
		  </tr>
		</table></td>
	  </tr>
	</table>
	</body>
	</html>';
		$headers = 'From: '.$config['AdminMail']."\r\n";
		$headers.= 'Reply-To: '.$config['AdminMail']."\r\n";
		$headers.= "MIME-Version: 1.0\r\n";			
		$headers.= "Content-type: text/html; charset=iso-8859-1\r\n";		
		@mail($toemail,$subject,$message,$headers);
				
	}	
	
	
	function SentFacebookPasswordEmail($email,$temp_pass,$firstname,$lastname)
	{
		global $objSmarty,$config;
		$date = date("Y-m-d h:i:s");
		$imgurl=$config['SiteGlobalPath']."/images/logo.png";				
		$to=$email;	
		$message='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
			<title>Memories</title>
					<style>
			body{
				font-family:Arial, Helvetica, sans-serif;
				font-size:12px;
				color:#000000;
			}
			.left-header {
			float: left;
			width: 590px;
			}
			.left-header .logo {
			float: left;
			width: 220px;
			margin-top: 5px;
			}
			.logo-txt {
			float: left;
			width: 370px;
			text-align: center;
			margin-top: 55px;
		   }
			</style>
			</head>	
			<body>
			<table width="700" border="3" cellspacing="0" cellpadding="7">
			  <tr>
				<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
				  <tr>
					<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
					<td align="left"  class="normal_txt7"><div class="left-header">
          		 	<div class="logo"><img src="'.$imgurl.'" /></div>           			         
         			</div> </td>
         		 	</tr>
         		 						  
					  <tr>
						<td height="35" align="center" bgcolor="#FFFFFF" class="normal_txt7"><table width="97%"  cellspacing="0" cellpadding="0">
						  <tr>
							<td width="53%" height="8"></td>
							<td width="47%" height="8"></td>
							</tr>
							<tr>
							  <td height="25" colspan="2" align="left" valign="middle" class="title_1" >Hello '.ucwords(stripslashes($first_name)).' '.ucwords(stripslashes($last_name)).'  </td>
							</tr>
							<tr>
							<td colspan="2" align="left" valign="middle" class="title_1">&nbsp;</td>
							</tr>
						<tr>
					  <td height="25" colspan="2" align="left" valign="middle" class="title_1" >Your account has been added successfully. Your Login informations are:</td>
			    		</tr>	
								
							<tr>
							<td colspan="2" align="left" valign="middle" class="title_1">Email : '.stripslashes($email).'</td>
							</tr>						
							<tr>
							  <td height="25" colspan="2" align="left" valign="middle" class="title_1" >Password  : '.stripslashes($temp_pass).'</td>
							</tr>		
							<tr>
							<td colspan="2" align="left" valign="middle" class="title_1">&nbsp;</td>
							</tr>					
							<tr>
							<td colspan="2" align="left" valign="middle" class="title_1">Thanks,</td>
				  			</tr>		
				 			<tr>
								<td colspan="2" align="left" valign="middle" class="title_1">Memories Team</td>
							</tr>											  
						</table></td>
					  </tr>
					</table></td>
				  </tr>
				</table></td>
			  </tr>
			</table>
			</body>
			</html>';	
		  	$subject = "Our Times-Thank you for your registration using facebook .";
			$headers = 'From: '.$config['AdminMail']."\r\n";
			$headers.= 'Reply-To: '.$email."\r\n";
			$headers.= "MIME-Version: 1.0\r\n";			
			$headers.= "Content-type: text/html; charset=iso-8859-1\r\n";															
			@mail($to,$subject,$message,$headers);
	}
	
	
	function LoginCheck()
	{
		
			global $objSmarty;
			
			
			
			$SelQuery="SELECT * from `member` where (`email`='".trim(addslashes($_REQUEST['email']))."' and `password`='".trim(addslashes($_REQUEST['pswd']))."' and status ='1'";
			$ResCart=$this->ExecuteQuery($SelQuery, "select");
		
			if(count($ResCart)==0)
			{
			$objSmarty->assign("ErrorMessage","Email or Password is incorrect");
			//$objSmarty->assign("SEmail",$_REQUEST['email']);
			}
		}
		
		
     function LoginUser()
     {     
     	global $objSmarty;
     	$SelQuery="SELECT * from `member` where `email`='".trim(addslashes($_REQUEST['email']))."' and `password`='".trim(addslashes($_REQUEST['pswd']))."'";
     	$ResCart=$this->ExecuteQuery($SelQuery, "select");
     		if(count($ResCart)==1)
			{
				if($ResCart[0]['status']!=0){
					$SelQuerystatus="SELECT * from `member` where `email`='".trim(addslashes($_REQUEST['email']))."' and `password`='".trim(addslashes($_REQUEST['pswd']))."'and `ActivationStatus`='1'";
	              	$ResCartstatus=$this->ExecuteQuery($SelQuerystatus, "select");
	              	
						if(count($ResCartstatus)==1)
						{
							$_SESSION['Original_User']=$ResCart[0]['id'];
							$_SESSION['Original_Firstname']=$ResCart[0]['firstname'];
							$_SESSION['userid']=$ResCart[0]['id'];
							$_SESSION['firstname']=$ResCart[0]['firstname'];
							$_SESSION['lastname']=$ResCart[0]['lastname'];
							$_SESSION['user_email']=$ResCart[0]['email'];
							$dob = $ResCart[0]['dob'];
							if($dob!='0000-00-00')
							{
								include_once $config['SiteClassPath']."class.TagNotifications.php";
								$objTagNotifications = new TagNotifications();
								
								$objTagNotifications->Check_for_Notification();
								if ($_SESSION['fbredirect']=='yes' && isset($_SESSION['memoryId']))
								{
								Redirect("viewMemory.php?id=".$_SESSION['memoryId']);
								}
			
								if(!isset($_SESSION['View_Shared_Image']))
								{
									if($ResCart[0]['Facebook_First_Status']=='1')
									{
										redirect('home.php');
									}
									if($ResCart[0]['Facebook_First_Status']=='2')
									{
										redirect('user_tutorial.php');
									}
									if($ResCart[0]['Facebook_First_Status']=='0')
									{
										redirect('invite_first.php');
									}
								}
								if(isset($_SESSION['View_Shared_Image']))
								{
									redirect('mymemories.php');
								}
							}
						  
							else 
							{
								redirect('editprofile.php?dob=1');
							}
						}
						else 
						{
							redirect("activationerr.php?register_uid=".$ResCart[0]['id']);
						}
				}else{
						redirect('blockuser.php');	
				}
			}
		
			else{
		       $objSmarty->assign("ErrorLoginMessage","Email or Password is incorrect");		
			}
     }
		
	function GetPrduct($cid)
	{
		global $objSmarty;
		
		if($cid!='')
		{  	$SqlCats="SELECT * FROM `categories` WHERE `Desc` like '%".$cid."%'";
		     $Res_Cats= $this->ExecuteQuery($SqlCats, "select"); 
		
			$SqlCat="SELECT * FROM `categories` WHERE `CatRelRecID` = '".$Res_Cats[0]['CatRelRecID']."'";
			$Res_Cat= $this->ExecuteQuery($SqlCat, "select"); 
			
			$sql="SELECT * FROM `products` WHERE `CategoryID` = ".$Res_Cat[0]['RecID']." AND `Status` = '1' order by `title` asc";
			$Res_Pro= $this->ExecuteQuery($sql, "select"); 
			$objSmarty->assign("Res_Pro",$Res_Pro);
			$objSmarty->assign("title1",$Res_Cat[0]['Desc']);
		}		
	}
	function GetSinglePrduct($pid)
	{
             
		global $objSmarty;
		
		$sql="SELECT * FROM `products` WHERE `title` = '".$pid."' and `pid`='0' ";
		$Res_Spro= $this->ExecuteQuery($sql, "select"); 
		// $sql1="SELECT * FROM `products` WHERE `title`='".$Res_Spro[0]['title']."' and `pid`='1'";
      
		//$Res_Spros= $this->ExecuteQuery($sql1, "select");
		//$objSmarty->assign("Res_SPros",$Res_Spros[0]['ProductNo']);
		//$objSmarty->assign("Res_SProsrp",$Res_Spros[0]['Price']);
		//$objSmarty->assign("Res_SProsyp",$Res_Spros[0]['yourprice1']);
		
		$objSmarty->assign("Res_SPro",$Res_Spro);
		$objSmarty->assign("title",$Res_Spro[0]['PageTitle']);
		$objSmarty->assign("QtyUnitRecID",$Res_Spro[0]['QtyUnitRecID']);
	}
	
	function AddCart()
	{
		global $objSmarty;
		
		$SelProduct	= "SELECT * FROM products where id=".$_REQUEST['product_id']."";
		$ResProduct	= $this->ExecuteQuery($SelProduct, "select");
		
		if($_SESSION['user_id']!='')
		{
			$SelCart	= "SELECT * FROM tbl_cart where product_id=".$_REQUEST['product_id']." and user_id='".$_SESSION['user_id'].
			"' and cart_status=0";
			$ResCart	= $this->ExecuteQuery($SelCart, "select");
		}
		else
		{
			$SelCart	= "SELECT * FROM tbl_cart where product_id=".$_REQUEST['product_id']." and session_id='".session_id()."' 
			and cart_status=0";
			$ResCart	= $this->ExecuteQuery($SelCart, "select");
			
			
		}
		if(count($ResCart)==0)
		{
			if($_REQUEST['cart_qty']!='')
				$qty=$_REQUEST['cart_qty'];
			else
				$qty=1;
			
			$sql = "INSERT INTO tbl_cart (product_id, session_id, user_id, added_on,quantity) VALUES
			(".$_REQUEST['product_id'].",'".session_id()."','".$_SESSION['user_id']."', now(),'".$qty."')";
			$this->ExecuteQuery($sql, "insert");
			
			
		}
		else
		{
			if($_REQUEST['cart_qty']!='')
				$qty=$ResCart[0]['quantity']+$_REQUEST['cart_qty'];
			else
				$qty=$ResCart[0]['quantity']+1;
			
			$SelQuery	= "UPDATE tbl_cart SET quantity = '".$qty."' WHERE cart_id=".$ResCart[0]['cart_id']."";
			$res	= $this->ExecuteQuery($SelQuery, "update");
			
		}
		header("Location:cart.php");
	}
	
	function ListCart()
	{
		global $objSmarty;
		if($_SESSION['user_id']!='')
		{
			$SelCart	= "SELECT * FROM tbl_cart TC left join products TP on TC.product_id=TP.id  where TC.cart_status=0 and TC.user_id='".$_SESSION['user_id']."' order by cart_id desc";
			$ResCart	= $this->ExecuteQuery($SelCart, "select");
		}
		else
		{
			$SelCart	= "SELECT * FROM tbl_cart TC left join products TP on TC.product_id=TP.id  where TC.cart_status=0 and TC.session_id='".session_id()."' and TC.user_id='' order by cart_id desc";
			$ResCart	= $this->ExecuteQuery($SelCart, "select");
		}
		$objSmarty->assign("CartCount",count($ResCart));
		$objSmarty->assign("ResCart",$ResCart);
	}
	
	function UpdateCart($objArray)
	{
		global $objSmarty;
		if($_SESSION['user_id']!='')
		{
			$SelCart	= "SELECT * FROM tbl_cart TC left join products TP on TC.product_id=TP.id  where TC.cart_status=0 and TC.user_id='".$_SESSION['user_id']."' order by cart_id desc";
			$ResCart	= $this->ExecuteQuery($SelCart, "select");
		}
		else
		{
			$SelCart	= "SELECT * FROM tbl_cart TC left join products TP on TC.product_id=TP.id  where TC.cart_status=0 and TC.session_id='".session_id()."' and TC.user_id='' order by cart_id desc";
			$ResCart	= $this->ExecuteQuery($SelCart, "select");
		}
		for($i=0;$i<count($ResCart);$i++)
		{
			if($objArray['qty'][$i]>0)
			{
				
				$SelQuery= "UPDATE tbl_cart SET quantity = '".$objArray['qty'][$i]."' 
				WHERE cart_id=".$ResCart[$i]['cart_id']."";
				$res	= $this->ExecuteQuery($SelQuery, "update");
				
			}
			else
			{
				$SelQuery	= "DELETE FROM tbl_cart WHERE cart_id=".$ResCart[$i]['cart_id']."";
				$res		= $this->ExecuteQuery($SelQuery, "delete");
			}
		}
		header("Location:cart.php?up=scc");
		$objSmarty->assign("SuccessMessage","Cart details updated successfully");
	}
	function DeleteCartProducts($ids)
	{
		global $objSmarty;
		if(is_array($ids))
		{
			$selected=implode(",",$ids);
			$SelQuery	= "delete from tbl_cart  Where cart_id in ($selected)";
			$res	= $this->ExecuteQuery($SelQuery, "delete");
		}
		$objSmarty->assign("SuccessMessage","Selected product(s) deleted successfully");
	}
	
	function SearchResult($vals)
	{
		global $objSmarty;
		$mode = $_REQUEST['mode'];
		//echo $_REQUEST['mode'];
		//exit;
		$searchterm = $_REQUEST['searchterm'];
		$category = $_REQUEST['category'];
		$maxprice = $_REQUEST['maxprice'];
		if($mode==3)
		{
			header("location:doctors.php");
		}
		else
		{
			$sql = "SELECT * FROM `products` WHERE";
			$c=0;
			if($category !="")
			{
				$sql.= " `CategoryID` = '$category'";
				$c++;
			}
			if($maxprice !="")
			{	
				if($c!=0){ $sql.= " AND"; }
				$sql.= " `Price` <= '$maxprice'";
				$c++;
			}
			if($mode==2)
			{
				if($c!=0){ $sql.= " AND"; }
				$sql.= " `ProductNo` = '$searchterm'";
			}
			if($mode==1)
			{
				if($c!=0){ $sql.= " AND"; }
				$sql.= " (`title` like '%$searchterm%' || `desc` like '%$searchterm%')";
			}
			if($mode==0)
			{
				if($c!=0){ $sql.= " AND"; }
				$sql.= " (`title` like '%$searchterm%' || `desc` like '%$searchterm%' || `ProductNo` like '%$searchterm%')";
			}
			//echo $sql;
			//exit;
			$res	= $this->ExecuteQuery($sql, "select");
			$objSmarty->assign("Result",$res);
			$objSmarty->assign("cnt",count($res));
			//print_r($res);
			
		}
	}
	
}
?>
