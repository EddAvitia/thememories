<?php 
	//http://stackoverflow.com/questions/18382740/cors-not-working-php
	if (isset($_SERVER['HTTP_ORIGIN'])) {
		header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
		header('Access-Control-Allow-Credentials: true');
		header('Access-Control-Max-Age: 86400');    // cache for 1 day
	}

	// Access-Control headers are received during OPTIONS requests
	if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

		if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
			header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

		if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
			header("Access-Control-Allow-Headers:        
			{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

		exit(0);
	}
	
	include "../common.php";
	include_once $config['SiteClassPath']."class.Content.php"; 
	include_once $config['SiteClassPath']."class.user.register.php";
	$ObjUser	= new User_Register();
	$objContent	= new Content();	
	
	//http://stackoverflow.com/questions/15485354/angular-http-post-to-php-and-undefined
	$postdata = file_get_contents("php://input");
	$response = array(); 
		
	if (isset($postdata)) {
		$request = json_decode($postdata);
		$email = $request->email;
		
		if( isset($email) ){

			if( count($ObjUser->emailexists($email)) > 0 ){
				$userdetails=$ObjUser->emailexists( $email );
				$ObjUser->SentPasswordEmail($userdetails[0]['email'],$userdetails[0]['password'],$userdetails[0]['firstname'],$userdetails[0]['lastname']);
			
				$response = array(
					'success' => true,
					'message' => "Your Password has been sent successfully. Please check your email."
				);
				
			}else{
				$response = array(
					'success' => false,
					'message' => "Given email is not available in our database. Please check the email you have entered."
				);
				
			}
		}else{
			$response = array(
				'success' => false,
				'message' => "Not called properly with email parameter!"
			);
		}
	}else {
		$response = array(
			'success' => false,
			'message' => "Not called properly with email parameter!"
		);
	}
	
	echo json_encode($response);
?>