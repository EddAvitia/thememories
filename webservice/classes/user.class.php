<?php
 include_once('conexion.class.php');
 include_once('utils.class.php');
 
 
 class User{
 	var $con;
	var $utils;

 	//Constructor
	function User(){
		$this->con=new DBManager;
		$this->utils=new Utils;
	}
	
	function obtener_user($email, $pswd){
		if($this->con->Conectar()== true){
			$SelQuery="SELECT id from `member` where `email`='".trim(addslashes($email))."' and `password`='".trim(addslashes($pswd))."'";
			return mysql_query($SelQuery);			
		}
	}
	
	
	function check_facebook_user($facebook_id){
		if($this->con->Conectar()== true){
			return mysql_query("select * from `member` where `facebook_id`='$facebook_id'");
		}
	}
	
	
	function is_user_activated($email, $pswd){
		if($this->con->Conectar()== true){	
			$SelQuerystatus="SELECT * from `member` where `email`='".trim(addslashes($email))."' and `password`='".trim(addslashes($pswd))."'and `ActivationStatus`='1'";
			return mysql_query($SelQuerystatus);			
		}
	}
	
	//Not used
	function emailexists($email){
		if($this->con->Conectar()== true){
			$sql="select * from member where email = '".$email."'";
			return mysql_query($sql);			
		}
	}
	
	//Not used
	function SentPasswordEmail($email,$password,$firstname,$lastname) {
		global $config;
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
	
	function createUser($first_name, $last_name, $username, $birthday, $email, $facebook_id, $gender) {
		if($this->con->Conectar()== true){

			$temp_pass	=$this->utils->gen_md5_password();
			$dat_exp 	= explode('/',trim(addslashes($birthday)));
			$dob_dat 	= $dat_exp[2].'-'.$dat_exp[0].'-'.$dat_exp[1];
			$join_date  = date('Y-m-d h:i:s');
			$activationKey =  mt_rand() . mt_rand() . mt_rand() . mt_rand() . mt_rand();	
			$response = array();

			if($this->con->Conectar()== true){
				$result = mysql_query("INSERT INTO `member` (firstname, lastname, username, password, dob, dbstatus, email, facebook_id, gender, join_date, ActivationStatus,Facebook_First_Status,status, Activation_Key) 
											VALUES ('$first_name','$last_name','$username','$temp_pass','$dob_dat', 1,'$email', '$facebook_id', '$gender', '$join_date', 1,2,1, '$activationKey')");
				$id = mysqli_insert_id();
				return array(
					'result' => $result,
					'temp_pass' => $temp_pass,
					'id' => $id						
				);
			}
		}
	}
}
 
?>